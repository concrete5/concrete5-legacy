<?php
defined('C5_EXECUTE') or die(_("Access Denied."));
class DashboardBricksController extends Controller {
	
	public function on_start() {
		/* Core Commerce Settings */

		// Products
		AttributeKeyCategory::registerSetting('core_commerce_product', 'list_model_path', 'product/list');
		AttributeKeyCategory::registerSetting('core_commerce_product', 'url_browse', 'dashboard/core_commerce/products/search');
		AttributeKeyCategory::registerSetting('core_commerce_product', 'url_insert', 'dashboard/core_commerce/products/add');
		AttributeKeyCategory::registerSetting('core_commerce_product', 'url_structure', 'dashboard/core_commerce/products/attributes');
		AttributeKeyCategory::registerSetting('core_commerce_product', 'url_access_disabled', TRUE);
		AttributeKeyCategory::registerSetting('core_commerce_product', 'url_drop_disabled', TRUE);

		// Orders
		AttributeKeyCategory::registerSetting('core_commerce_order', 'list_model_path', 'order/list');
		AttributeKeyCategory::registerSetting('core_commerce_order', 'url_browse', 'dashboard/core_commerce/orders/search');
		AttributeKeyCategory::registerSetting('core_commerce_order', 'url_insert_disabled', TRUE);
		AttributeKeyCategory::registerSetting('core_commerce_order', 'url_structure', 'dashboard/core_commerce/orders/attributes');
		AttributeKeyCategory::registerSetting('core_commerce_order', 'url_access_disabled', TRUE);
		AttributeKeyCategory::registerSetting('core_commerce_order', 'url_drop_disabled', TRUE);
		
		// Product Options
		AttributeKeyCategory::registerSetting('core_commerce_product_option', 'hidden', TRUE);
	}
	
	public function view() {
		foreach(AttributeKeyCategory::getList() as $akc) {
			if($akc->pkgID == '0') $pkgName = 'Custom Additions';
			if($akc->pkgID) $pkgName = Package::getByID($akc->pkgID)->getPackageName();
			if(!$pkgName) $pkgName = 'Built-In';
			$piles[$pkgName][] = $akc;
			unset($pkgName);
		}
		if(empty($piles['Custom Additions'])) $piles['Custom Additions'] = NULL;
		$this->set('piles', $piles);
	}
	
}
