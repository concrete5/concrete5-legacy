<? defined('C5_EXECUTE') or die("Access Denied."); ?>
<div class="ccm-pane-controls">
<? 

Loader::model('collection_attributes');
Loader::model('collection_types');
$dh = Loader::helper('date');

if ($c->isAlias() && $c->getCollectionPointerExternalLink() != '') {
?>

<h1><?=t('Edit External Link')?></h1>

	<form method="post" action="<?=$c->getCollectionAction()?>" id="ccmEditLink">		
	
	<div class="ccm-form-area">
	<div class="ccm-field">
	
	<label><?=t('Name')?></label> <input type="text" name="cName" value="<?=$c->getCollectionName()?>" class="text" style="width: 100%" />
	
	</div>
	<div class="ccm-field">

	<label><?=t('URL')?></label> <input type="text" name="cExternalLink" style="width: 100%" value="<?=$c->getCollectionPointerExternalLink()?>" />

	</div>

	<div class="ccm-field">

	<label for="cExternalLinkNewWindow"><input type="checkbox" value="1" <? if ($c->openCollectionPointerExternalLinkInNewWindow()) { ?> checked <? } ?> name="cExternalLinkNewWindow" id="cExternalLinkNewWindow" style="vertical-align: middle" /> <?=t('Open Link in New Window')?></label>

	</div>
	
	<div class="ccm-spacer">&nbsp;</div>
	</div>


	<div class="ccm-buttons">
	<a href="javascript:void(0)" onclick="jQuery('#ccmEditLink').get(0).submit()" class="ccm-button-right accept"><span><?=('Save')?></span></a>
	</div>	
	<input type="hidden" name="update_external" value="1" />
	<input type="hidden" name="processCollection" value="1">

</form>
<? } ?>
</div>
