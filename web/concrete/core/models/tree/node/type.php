<?php defined('C5_EXECUTE') or die('Access Denied.');

class Concrete5_Model_TreeNodeType extends Object {

	public function getTreeNodeTypeID() {
		return $this->treeNodeTypeID;
	}
	public function getTreeNodeTypeHandle() {
		return $this->treeNodeTypeHandle;
	}
	public function getPackageID() { return $this->pkgID;}
	public function getPackageHandle() {
		return PackageList::getHandle($this->pkgID);
	}

	public function add($treeNodeTypeHandle, $pkg = false) {

		$pkgID = 0;
		$db = Loader::db();
		if (is_object($pkg)) {
			$pkgID = $pkg->getPackageID();
		}
		
		$r = $db->query("insert into TreeNodeTypes (treeNodeTypeHandle, pkgID) values (?, ?)", array(
			$treeNodeTypeHandle, $pkgID
		));
		
		$treeNodeTypeID = $db->Insert_ID();
		return TreeNodeType::getByID($treeNodeTypeID);
	}

	public function delete() {
		$db = Loader::db();
		$db->Execute('delete from TreeNodeTypes where treeNodeTypeID = ?', array($this->treeNodeTypeID));
	}

	public static function getByID($treeNodeTypeID) {
		$db = Loader::db();
		$row = $db->GetRow('select * from TreeNodeTypes where treeNodeTypeID = ?', array($treeNodeTypeID));
		if (is_array($row) && $row['treeNodeTypeID']) {
			$type = new TreeNodeType();
			$type->setPropertiesFromArray($row);
			return $type;
		}
	}

	public static function getByHandle($treeNodeTypeHandle) {
		$db = Loader::db();
		$row = $db->GetRow('select * from TreeNodeTypes where treeNodeTypeHandle = ?', array($treeNodeTypeHandle));
		if (is_array($row) && $row['treeNodeTypeHandle']) {
			$type = new TreeNodeType();
			$type->setPropertiesFromArray($row);
			return $type;
		}
	}

	public function getTreeNodeTypeClass() {
		return Loader::helper('text')->camelcase($this->treeNodeTypeHandle) . 'TreeNode';
	}

	public static function getListByPackage($pkg) {
		$db = Loader::db();
		$list = array();
		$r = $db->Execute('select treeNodeTypeID from TreeNodeTypes where pkgID = ? order by treeNodeTypeID asc', array($pkg->getPackageID()));
		while ($row = $r->FetchRow()) {
			$list[] = TreeNodeType::getByID($row['treeNodeTypeID']);
		}
		$r->Close();
		return $list;
	}	
	
}