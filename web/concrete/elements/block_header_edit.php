<?
defined('C5_EXECUTE') or die("Access Denied.");
global $c; ?>

<a name="_edit<?=$b->getBlockID()?>"></a>

<? $bt = $b->getBlockTypeObject(); ?>

<script type="text/javascript">

<? $ci = Loader::helper("concrete/urls"); ?>
<? $url = $ci->getBlockTypeJavaScriptURL($bt); 
if ($url != '') { ?>
	ccm_addHeaderItem("<?=$url?>", 'JAVASCRIPT');
<? } 

$identifier = strtoupper('BLOCK_CONTROLLER_' . $btHandle);
if (is_array($headerItems[$identifier])) {
	foreach($headerItems[$identifier] as $item) { 
		if ($item instanceof CSSOutputObject) {
			$type = 'CSS';
		} else {
			$type = 'JAVASCRIPT';
		}
		?>
		ccm_addHeaderItem("<?=$item->file?>", '<?=$type?>');
	<?
	}
}
?>
jQuery(function() {
	jQuery('#ccm-block-form').each(function() {
		ccm_setupBlockForm(jQuery(this), '<?=$b->getBlockID()?>', 'edit');
	});
});
</script>

<form method="post" id="ccm-block-form" class="validate" action="<?=$b->getBlockEditAction()?>&rcID=<?=intval($rcID)?>" enctype="multipart/form-data">

<input type="hidden" name="ccm-block-form-method" value="REGULAR" />

<? foreach($this->controller->getJavaScriptStrings() as $key => $val) { ?>
	<input type="hidden" name="ccm-string-<?=$key?>" value="<?=$val?>" />
<? } ?>


<div id="ccm-block-fields">
