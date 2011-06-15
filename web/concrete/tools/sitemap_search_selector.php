<?
defined('C5_EXECUTE') or die("Access Denied.");
$sh = Loader::helper('concrete/dashboard/sitemap');
if (!$sh->canRead()) {
	die(t('Access Denied. You do not have access to sitemap permissions.'));
}

?>
<div>

<?=Loader::helper('html')->css('ccm.sitemap.css')?>
<?=Loader::helper('html')->javascript('ccm.sitemap.js')?>


<script type="text/javascript">
var ccm_ssActiveTab = "ccm-show-sitemap";

jQuery("#ccm-ss-tabs a").click(function() {
	jQuery("li.ccm-nav-active").removeClass('ccm-nav-active');
	jQuery("#" + ccm_ssActiveTab + "-tab").hide();
	ccm_ssActiveTab = jQuery(this).attr('id');
	jQuery(this).parent().addClass("ccm-nav-active");
	jQuery("#" + ccm_ssActiveTab + "-tab").show();
});

</script>

<ul class="ccm-dialog-tabs" id="ccm-ss-tabs">
<li class="ccm-nav-active"><a href="javascript:void(0)" id="ccm-show-sitemap"><?=t('Sitemap')?></a></li>
<li><a href="javascript:void(0)" id="ccm-show-search"><?=t('Search')?></a></li>
</ul>

<br/>

<div id="ccm-show-sitemap-tab">
<? $sitemapCombinedMode = true; ?>
<? include(DIR_FILES_TOOLS_REQUIRED . '/sitemap_overlay.php'); ?>

</div>

<div id="ccm-show-search-tab" style="display: none">

<? 
$sitemap_select_mode = $select_mode;
include(DIR_FILES_TOOLS_REQUIRED . '/pages/search_dialog.php'); ?>


</div>
</div>