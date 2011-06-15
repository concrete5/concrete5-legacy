<? 
defined('C5_EXECUTE') or die("Access Denied.");
?>

<script type="text/javascript">
ccmPrepareScrapbookItemsDelete=function(){
	jQuery("a.ccm-scrapbook-delete").click(function() {
		var id=jQuery(this).attr('id');
		var arHandle=jQuery(this).attr('arHandle');
		var qStr='&ptask=delete_content&arHandle='+encodeURIComponent(arHandle)+'<?=$token?>'; 
		
		if( id.indexOf('bID')>-1 ){
			if(!confirm('<?=t('Are you sure you want to delete this block?').'\n'.t('(All page instances will also be removed)') ?>'))
				return false;
			var bID = id.substring(13);
			qStr='bID=' + bID + qStr;
		}else{
			var pcID = id.substring(2);
			qStr='pcID=' + pcID + qStr;
		}  
		$.ajax({
			type: 'POST',
			url: CCM_DISPATCHER_FILENAME,
			data: qStr,
			success: function(msg) {
				if(pcID) jQuery("#ccm-pc-" + pcID).fadeOut();
				if(bID)  jQuery("#ccm-scrapbook-list-item-" + bID).fadeOut(); 
			}
		}); 
	});
}

jQuery(function(){ ccmPrepareScrapbookItemsDelete(); });
	
ccmChangeDisplayedScrapbook = function(sel){  
	var scrapbook=jQuery(sel).val(); 
	if(!scrapbook) return false;
	jQuery('#ccm-scrapbookListsWrap').html("<div style='padding:16px;'><?=t('Loading...')?></div>");
	$.ajax({
	type: 'POST',
	url: CCM_TOOLS_PATH+"/edit_area_scrapbook_refresh.php",
	data: 'cID=<?=intval($_REQUEST['cID'])?>&arHandle=<?=urlencode($_REQUEST['arHandle'])?>&scrapbookName='+scrapbook+'&<?=$token?>',
	success: function(resp) { 
		jQuery('#ccm-scrapbookListsWrap').html(resp);
		ccmPrepareScrapbookItemsDelete(); 
	}});		
	return false;
}
</script>


<? 
$u = new User();
$scrapbookHelper=Loader::helper('concrete/scrapbook'); 
$scrapBookAreasData = $scrapbookHelper->getAvailableScrapbooks(); 
$scrapbookName=$_SESSION['ccmLastViewedScrapbook']; 
?>	
<select name="scrapbookName" onchange="ccmChangeDisplayedScrapbook(this)" style=" float:right; margin-top:6px;"> 
	<option value="userScrapbook">
		<?=ucfirst($u->getUserName()) ?><?=t("'s Scrapbook") ?> 
	</option>
	<? foreach($scrapBookAreasData as $scrapBookAreaData){ ?>
		<option value="<?=addslashes($scrapBookAreaData['arHandle'])?>" <?=($scrapbookName==$scrapBookAreaData['arHandle'])?'selected':''?>>
			<?=$scrapBookAreaData['arHandle'] ?>
		</option>
	<? } ?>
</select> 	

<h1><?=t('Add From Scrapbook')?></h1>		
	
<div id="ccm-scrapbookListsWrap">
<? Loader::element('scrapbook_lists', array( 'c'=>$c, 'a'=>$a, 'scrapbookName'=>$scrapbookName, 'token'=>$token ) );  ?>
</div>