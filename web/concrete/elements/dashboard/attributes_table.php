<? 
//Used on both page and file attributes
$c = Page::getCurrentPage();

$sets = array();
if (is_object($category) && $category->allowAttributeSets()) {
	$sets = $category->getAttributeSets();
}

if (count($attribs) > 0) { 

	$ih = Loader::helper('concrete/interface');
	$valt = Loader::helper('validation/token');
	
	if (count($sets) > 0) {  ?>
	
		<h3 style="position: absolute; top: 6px; right: 8px"><?=t('View Attributes: ')?><select style="font-size: 10px" onchange="window.location.href='<?=Loader::helper('navigation')->getLinkToCollection($c)?>?asGroupAttributes=' + this.value" name="asGroupAttributes">
			<option value="1" <? if ($_REQUEST['asGroupAttributes'] !== '0') { ?> selected <? } ?>><?=t('Grouped by set')?></option>
			<option value="0" <? if ($_REQUEST['asGroupAttributes'] === '0') { ?> selected <? } ?>><?=t('In one list')?></option>
		</select></h3>
		<div class="ccm-spacer">&nbsp;</div>

	<? }
	
	if (count($sets) > 0 && ($_REQUEST['asGroupAttributes'] !== '0')) { ?>
	
	
		<?
	
		foreach($sets as $as) { ?>
	
		
		<h2><?=$as->getAttributeSetName()?></h2>
	
		<?
		
		$setattribs = $as->getAttributeKeys();
		if (count($setattribs) == 0) { ?>
		
			<?=t('No attributes defined.')?><br/><br/>
		
		<? } else { ?>
			
			<div class="ccm-attribute-sortable-set-list" attribute-set-id="<?=$as->getAttributeSetID()?>" id="asID_<?=$as->getAttributeSetID()?>">			
			
			<?
			
			foreach($setattribs as $ak) { ?>
			
			<div class="ccm-attribute" id="akID_<?=$as->getAttributeSetID()?>_<?=$ak->getAttributeKeyID()?>">
				<img class="ccm-attribute-icon" src="<?=$ak->getAttributeKeyIconSRC()?>" width="16" height="16" /><a href="<?=$this->url($editURL, 'edit', $ak->getAttributeKeyID())?>"><?=$ak->getAttributeKeyName()?></a>
			</div>
	

			<? } ?>
			
			</div>
			
			<? } ?>
			
			<br/>
			
		<? } 
		
		$unsetattribs = $category->getUnassignedAttributeKeys();
		if (count($unsetattribs) > 0) { ?>
		
			<h2><?=t('Other')?></h2>
		
			<?
			foreach($unsetattribs as $ak) { ?>
	
			<div class="ccm-attribute" id="akID_<?=$as->getAttributeSetID()?>_<?=$ak->getAttributeKeyID()?>">
				<img class="ccm-attribute-icon" src="<?=$ak->getAttributeKeyIconSRC()?>" width="16" height="16" /><a href="<?=$this->url($editURL, 'edit', $ak->getAttributeKeyID())?>"><?=$ak->getAttributeKeyName()?></a>
			</div>
	

			<? } ?>
		
		<?
		
		}
	
	} else { ?>
		
		<div class="ccm-attributes-list">
		
		<?
		foreach($attribs as $ak) { ?>
		<div class="ccm-attribute" id="akID_<?=$ak->getAttributeKeyID()?>">
			<img class="ccm-attribute-icon" src="<?=$ak->getAttributeKeyIconSRC()?>" width="16" height="16" /><a href="<?=$this->url($editURL, 'edit', $ak->getAttributeKeyID())?>"><?=$ak->getAttributeKeyName()?></a>
		</div>
		
		<? } ?>
	
		</div>
	
	<? } ?>
	
<? } else { ?>
	
	<br/>
	
	<strong>
		<?
	 echo t('No attributes defined.');
		?>
	</strong>
	
	<br/><br/>
	
<? } ?>

<script type="text/javascript">
	jQuery("div.ccm-attribute-sortable-set-list").sortable({
		handle: 'img.ccm-attribute-icon',
		cursor: 'move',
		opacity: 0.5,
		stop: function() {
			var ualist = jQuery(this).sortable('serialize');
			ualist += '&cID=<?=$c->getCollectionID()?>';
			ualist += '&asID=' + jQuery(this).attr('attribute-set-id');
			jQuery.post('<?=REL_DIR_FILES_TOOLS_REQUIRED?>/dashboard/attribute_sets_update', ualist, function(r) {

			});
		}
	});
</script>

<style type="text/css">
div.ccm-attribute-sortable-set-list img.ccm-attribute-icon:hover {cursor: move}
</style>
