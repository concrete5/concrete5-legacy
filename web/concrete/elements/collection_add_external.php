<? defined('C5_EXECUTE') or die("Access Denied."); ?>
<div class="ccm-pane-controls">
<? 

Loader::model('collection_attributes');
Loader::model('collection_types');
$dh = Loader::helper('date');

	
?>

<h1><?=t('Add External Link')?></h1>

	<form method="post" action="<?=$c->getCollectionAction()?>" id="ccmAddPage">		
	
	<div class="ccm-form-area">
	<div class="ccm-field">
	
	<label><?=t('Name')?></label> <input type="text" name="cName" value="" class="text" style="width: 100%">
	
	</div>
	<div class="ccm-field">

	<label><?=t('URL')?></label> <input type="text" name="cExternalLink" style="width: 100%" value="http://">

	</div>

	<div class="ccm-field">

	<label for="cExternalLinkNewWindow"><input type="checkbox" value="1" name="cExternalLinkNewWindow" id="cExternalLinkNewWindow" style="vertical-align: middle" /> <?=t('Open Link in New Window')?></label>

	</div>
	
	
	<div class="ccm-spacer">&nbsp;</div>
	</div>

	<div class="ccm-buttons">
	<a href="javascript:void(0)" onclick="jQuery('#ccmAddPage').get(0).submit()" class="ccm-button-right accept"><span><?=t('Add')?></span></a>
	</div>	
	<input type="hidden" name="add_external" value="1" />
	<input type="hidden" name="processCollection" value="1">
</form>
</div>
