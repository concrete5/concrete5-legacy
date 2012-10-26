<?

defined('C5_EXECUTE') or die("Access Denied.");
class ConcreteUpgradeVersion561Helper {
	
	public $dbRefreshTables = array(
		'atSocialLinks',
		'QueuePageDuplicationRelations',
		'Queues',
		'QueueMessages'

	);
	
	
	public function run() {
		$tt = AttributeType::getByHandle('social_links');
		if (!is_object($tt) || $tt->getAttributeTypeID() == 0) {
			$tt = AttributeType::add('social_links', t('Social Link'));
		}
		$akc = AttributeKeyCategory::getByHandle('user');
		if (is_object($akc)) {
			$akc->associateAttributeKeyType($tt);
		}
		
		$db = Loader::db();
		$columns = $db->MetaColumns('Pages');
		if (isset($columns['PTID'])) {
			$db->Execute('alter table Pages drop column ptID');
		}
	}
		
}
