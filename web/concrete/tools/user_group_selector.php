<?
defined('C5_EXECUTE') or die("Access Denied.");
$displayGroups = true;
$displayUsers = true;

if ($_REQUEST['mode'] == 'users') {
	$displayGroups = false;
} else if ($_REQUEST['mode'] == 'groups') {
	$displayUsers = false;
}

$tp = new TaskPermission();
if (!$tp->canAccessUserSearch() && !$tp->canAccessGroupSearch()) { 
	die(t("Access Denied."));
}

?>

<script type="text/javascript">
var ccm_ugActiveTab = "ccm-select-group";

jQuery("#ccm-ug-tabs a").click(function() {
	jQuery("li.ccm-nav-active").removeClass('ccm-nav-active');
	jQuery("#" + ccm_ugActiveTab + "-tab").hide();
	ccm_ugActiveTab = jQuery(this).attr('id');
	jQuery(this).parent().addClass("ccm-nav-active");
	jQuery("#" + ccm_ugActiveTab + "-tab").show();
});

</script>

<? if ($displayGroups && $displayUsers) { ?>

<ul class="ccm-dialog-tabs" id="ccm-ug-tabs">
<li class="ccm-nav-active"><a href="javascript:void(0)" id="ccm-select-group"><?=t('Groups')?></a></li>
<li><a href="javascript:void(0)" id="ccm-select-user"><?=t('Users')?></a></li>
</ul>

<? } ?>

<? if ($displayGroups) { ?>

<div id="ccm-select-group-tab">

<h1><?=t('Select Group')?></h1>
<? include(DIR_FILES_TOOLS_REQUIRED . '/select_group.php'); ?>

</div>

<? } ?>

<? if ($displayUsers) { ?>

<div id="ccm-select-user-tab" style="display: none">
<h1><?=t('Select User')?></h1>

<? 
$mode = 'choose_multiple';
include(DIR_FILES_TOOLS_REQUIRED . '/users/search_dialog.php'); ?>

</div>

<? } ?>