<?php
defined('C5_EXECUTE') or die(_("Access Denied."));
class DashboardBricksSearchController extends Controller {
	
	public function view($akCategoryHandle = NULL) {
		if(!$akCategoryHandle) $this->redirect('dashboard/bricks');
		$akcsh = Loader::helper('attribute_key_category_settings');
		$rs = $akcsh->getRegisteredSettings($akCategoryHandle);
		$this->set('rs', $rs);
		
			$this->addHeaderItem(Loader::helper('html')->javascript('attribute_key_category.ui.js'));
			$searchInstance = $akCategoryHandle.time();
			if (isset($_REQUEST['searchInstance'])) {
				$searchInstance = $_REQUEST['searchInstance'];
			}
			$this->addHeaderItem('<script type="text/javascript">$(function(){ccm_setupAdvancedSearch(\''.$searchInstance.'\');});</script>');
			$this->set('akCategoryHandle', $akCategoryHandle);
			$this->set('txt', Loader::helper('text'));
			$this->set('form', Loader::helper('form'));
			Loader::model('attribute_key_category_item_permission');
			$akcip = AttributeKeyCategoryItemPermission::getByID($akCategoryHandle);
			$this->set('permission', $akcip->canSearch());
			
			$objectList = $this->getRequestedSearchResults($akCategoryHandle);
			$objects = $objectList->getPage();
			
			$this->set('newObjectList', $objectList);		
			$this->set('newObjects', $objects);		
			$this->set('pagination', $objectList->getPagination());
			
		$subnav = array(array(View::url('dashboard/bricks'), t('Categories')));
		foreach($akcsh->getActions() as $action) {
			if(!$rs['url_'.$action.'_hidden']) {
				$url = View::url('dashboard/bricks/', $action, $akCategoryHandle);
				if($rs['url_'.$action]) $url = View::url($rs['url_'.$action]);
				$subnav[] = array(
					$url,
					t(ucwords($action)),
					($this->getCollectionObject()->getCollectionHandle() == $action)
				);
			}
		}
		$this->set('subnav', $subnav);
	}
	
	public function getRequestedSearchResults($akCategoryHandle) {
		$akc = AttributeKeyCategory::getByHandle($akCategoryHandle);
		$objectList = $akc->getItemList();
		
		if ($_GET['keywords'] != '') {
			$objectList->filterByKeywords($_GET['keywords']);
		}	
		
		if ($_REQUEST['numResults']) {
			$objectList->setItemsPerPage($_REQUEST['numResults']);
		} else {
			$objectList->setItemsPerPage(10);
		}
		
		if (is_array($_REQUEST['selectedSearchField'])) {
			foreach($_REQUEST['selectedSearchField'] as $i => $akID) {
				// due to the way the form is setup, index will always be one more than the arrays
				if ($akID != '') {
					$ak = AttributeKey::getByID($akID);
					$type = $ak->getAttributeType();
					$cnt = $type->getController();
					$cnt->setRequestArray($req);
					$cnt->setAttributeKey($ak);
					$cnt->searchForm($objectList);
				}
			}
		}
		$req = $objectList->getSearchRequest();
		$this->set('searchRequest', $req);
		return $objectList;
	}
} ?>
