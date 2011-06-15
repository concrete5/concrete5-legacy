<?
defined('C5_EXECUTE') or die("Access Denied.");
if ($cp->canAdminPage()) {
$gArray = array();
$gl = new GroupList($c, false, true);
$gArray = $gl->getGroupList();
?>

<div class="ccm-pane-controls">
<form method="post" id="ccmPermissionsForm" name="ccmPermissionsForm" action="<?=$c->getCollectionAction()?>">
<input type="hidden" name="rel" value="<?=$_REQUEST['rel']?>" />

<h1><?=t('Page Access')?></h1>

<div class="ccm-form-area">

<div class="ccm-field">

<h2><?=t('Who can view this page?')?></h2>

<?

foreach ($gArray as $g) {
?>

<input type="checkbox" name="readGID[]" value="<?=$g->getGroupID()?>" <? if ($g->canRead()) { ?> checked <? } ?> /> <?=$g->getGroupName()?><br/>

<? } ?>

</div>

<div class="ccm-field">

<h2><?=t('Who can edit this page?')?></h2>

<?

foreach ($gArray as $g) {
?>

<input type="checkbox" name="editGID[]" value="<?=$g->getGroupID()?>" <? if ($g->canWrite()) { ?> checked <? } ?> /> <?=$g->getGroupName()?><br/>

<? } ?>


</div>
</div>

<div class="ccm-buttons">
<!--	<a href="javascript:void(0)" onclick="ccm_hidePane()" class="ccm-button-left cancel"><span><em class="ccm-button-close">Cancel</em></span></a>//-->
	<a href="javascript:void(0)" onclick="jQuery('form[name=ccmPermissionsForm]').submit()" class="ccm-button-right accept"><span><?=t('Save')?></span></a>
</div>	
<input type="hidden" name="update_permissions" value="1" class="accept">
<input type="hidden" name="processCollection" value="1">

<script type="text/javascript">
jQuery(function() {
	jQuery("#ccmPermissionsForm").ajaxForm({
		type: 'POST',
		iframe: true,
		beforeSubmit: function() {
			jQuery.fn.dialog.showLoader();
		},
		success: function(r) {
			var r = eval('(' + r + ')');
			if (r != null && r.rel == 'SITEMAP') {
				jQuery.fn.dialog.hideLoader();
				jQuery.fn.dialog.closeTop();
				ccmSitemapHighlightPageLabel(r.cID);
			} else {
				ccm_hidePane(function() {
					jQuery.fn.dialog.hideLoader();						
				});
			}
			ccmAlert.hud(ccmi18n_sitemap.setPagePermissionsMsg, 2000, 'success', ccmi18n_sitemap.setPagePermissions);
		}
	});
});
</script>

<div class="ccm-spacer">&nbsp;</div>
</form>
</div>
<? } ?>