<?
defined('C5_EXECUTE') or die("Access Denied.");

class ViewUserAttributesUserPermissionKey extends UserPermissionKey  {

	protected function getAllowedAttributeKeyIDs($list = false) {
		if (!$list) {
			$u = new User();
			$accessEntities = $u->getUserAccessEntityObjects();
			$list = $this->getAccessListItems(UserPermissionKey::ACCESS_TYPE_ALL, $accessEntities);
			$list = PermissionDuration::filterByActive($list);
		}

		$db = Loader::db();
		$allakIDs = $db->GetCol('select akID from UserAttributeKeys');
		$akIDs = array();
		foreach($list as $l) {
			if ($l->getAttributesAllowedPermission() == 'N') {
				$akIDs = array();
			}
			if ($l->getAttributesAllowedPermission() == 'C') {
				if ($l->getAccessType() == PermissionKey::ACCESS_TYPE_EXCLUDE) {
					$akIDs = array_values(array_diff($akIDs, $l->getAttributesAllowedArray()));
				} else {
					$akIDs = array_unique(array_merge($akIDs, $l->getAttributesAllowedArray()));
				}
			}
			if ($l->getAttributesAllowedPermission() == 'A') {
				$akIDs = $allakIDs;
			}
		}

		return $akIDs;
	}


	public function getMyAssignment() {
		$u = new User();
		$asl = new ViewUserAttributesUserPermissionAssignment();
		if ($u->isSuperUser()) {
			$asl->setAttributesAllowedPermission('A');
			return $asl;
		}

		$pae = $this->getPermissionAccessObject();
		if (!is_object($pae)) {
			return $asl;
		}

		$accessEntities = $u->getUserAccessEntityObjects();
		$accessEntities = $pae->validateAndFilterAccessEntities($accessEntities);
		$list = $this->getAccessListItems(UserPermissionKey::ACCESS_TYPE_ALL, $accessEntities);
		$list = PermissionDuration::filterByActive($list);

		foreach($list as $l) {
			if ($l->getAttributesAllowedPermission() == 'N') {
				$asl->setAttributesAllowedPermission('N');
			}

			if ($l->getAttributesAllowedPermission() == 'C') {
				$asl->setAttributesAllowedPermission('C');
			}

			if ($l->getAttributesAllowedPermission() == 'A') {
				$asl->setAttributesAllowedPermission('A');
			}
		}

		$asl->setAttributesAllowedArray($this->getAllowedAttributeKeyIDs($list));
		return $asl;
	}

	public function validate($obj = false) {
		$u = new User();
		if ($u->isSuperUser()) {
			return true;
		}

		$types = $this->getAllowedAttributeKeyIDs();
		if ($obj != false) {
			if (is_object($obj)) {
				$akID = $obj->getAttributeKeyID();
			} else {
				$akID = $obj;
			}
			return in_array($akID, $types);
		} else {
			return count($types) > 0;
		}
	}


}

class ViewUserAttributesUserPermissionAccess extends UserPermissionAccess {

	public function save($args) {
		parent::save();
		$db = Loader::db();
		$db->Execute('delete from UserPermissionViewAttributeAccessList where paID = ?', array($this->getPermissionAccessID()));
		$db->Execute('delete from UserPermissionViewAttributeAccessListCustom where paID = ?', array($this->getPermissionAccessID()));
		if (is_array($args['viewAttributesIncluded'])) {
			foreach($args['viewAttributesIncluded'] as $peID => $permission) {
				$v = array($this->getPermissionAccessID(), $peID, $permission);
				$db->Execute('insert into UserPermissionViewAttributeAccessList (paID, peID, permission) values (?, ?, ?)', $v);
			}
		}

		if (is_array($args['viewAttributesExcluded'])) {
			foreach($args['viewAttributesExcluded'] as $peID => $permission) {
				$v = array($this->getPermissionAccessID(), $peID, $permission);
				$db->Execute('insert into UserPermissionViewAttributeAccessList (paID, peID, permission) values (?, ?, ?)', $v);
			}
		}

		if (is_array($args['akIDInclude'])) {
			foreach($args['akIDInclude'] as $peID => $akIDs) {
				foreach($akIDs as $akID) {
					$v = array($this->getPermissionAccessID(), $peID, $akID);
					$db->Execute('insert into UserPermissionViewAttributeAccessListCustom (paID, peID, akID) values (?, ?, ?)', $v);
				}
			}
		}

		if (is_array($args['akIDExclude'])) {
			foreach($args['akIDExclude'] as $peID => $akIDs) {
				foreach($akIDs as $akID) {
					$v = array($this->getPermissionAccessID(), $peID, $akID);
					$db->Execute('insert into UserPermissionViewAttributeAccessListCustom (paID, peID, akID) values (?, ?, ?)', $v);
				}
			}
		}
	}

	public function duplicate($newPA = false) {
		$newPA = parent::duplicate($newPA);
		$db = Loader::db();
		$r = $db->Execute('select * from UserPermissionViewAttributeAccessList where paID = ?', array($this->getPermissionAccessID()));
		while ($row = $r->FetchRow()) {
			$v = array($row['peID'], $newPA->getPermissionAccessID(), $row['permission']);
			$db->Execute('insert into UserPermissionViewAttributeAccessList (peID, paID, permission) values (?, ?, ?)', $v);
		}
		$r = $db->Execute('select * from UserPermissionViewAttributeAccessListCustom where paID = ?', array($this->getPermissionAccessID()));
		while ($row = $r->FetchRow()) {
			$v = array($row['peID'], $newPA->getPermissionAccessID(), $row['akID']);
			$db->Execute('insert into UserPermissionViewAttributeAccessListCustom  (peID, paID, akID) values (?, ?, ?)', $v);
		}
		return $newPA;
	}

	public function getAccessListItems($accessType = PermissionKey::ACCESS_TYPE_INCLUDE, $filterEntities = array()) {
		$db = Loader::db();
		$list = parent::getAccessListItems($accessType, $filterEntities);
		foreach($list as $l) {
			$pe = $l->getAccessEntityObject();
			if ($this->permissionObjectToCheck instanceof Page && $l->getAccessType() == PermissionKey::ACCESS_TYPE_INCLUDE) {
				$permission = 'A';
			} else {
				$permission = $db->GetOne('select permission from UserPermissionViewAttributeAccessList where paID = ? and peID = ?', array($l->getPermissionAccessID(), $pe->getAccessEntityID()));
				if ($permission != 'N' && $permission != 'C') {
					$permission = 'A';
				}

			}
			$l->setAttributesAllowedPermission($permission);
			if ($permission == 'C') {
				$akIDs = $db->GetCol('select akID from UserPermissionViewAttributeAccessListCustom where paID = ? and peID = ?', array($l->getPermissionAccessID(), $pe->getAccessEntityID()));
				$l->setAttributesAllowedArray($akIDs);
			}
		}
		return $list;
	}

}

class ViewUserAttributesUserPermissionAccessListItem extends UserPermissionAccessListItem {

	protected $customAttributeArray = array();
	protected $attributesAllowedPermission = 'N';

	public function setAttributesAllowedPermission($permission) {
		$this->attributesAllowedPermission = $permission;
	}
	public function getAttributesAllowedPermission() {
		return $this->attributesAllowedPermission;
	}
	public function setAttributesAllowedArray($akIDs) {
		$this->customAttributeArray = $akIDs;
	}
	public function getAttributesAllowedArray() {
		return $this->customAttributeArray;
	}


}