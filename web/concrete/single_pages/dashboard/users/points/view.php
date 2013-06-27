<?php
print
Loader::helper('concrete/dashboard')->getDashboardPaneHeaderWrapper(t('Community Points'), false, false, false, array(
	Page::getByPath('/dashboard/users/points/actions'),
	Page::getByPath('/dashboard/users/points/assign')

))?>
<form class="form-inline" action="<?php echo $this->action('view')?>" method="get">
<div class="ccm-pane-options">
<div class="ccm-pane-options-permanent-search">

<label class="control-label"><?=t('User')?></label>
<?php echo $form_user_selector->quickSelect('uName',$_GET['uName']);?>

	
<input type="submit" value="<?php echo t('Search');?>" class="btn" />
<a href="<?=View::url('/dashboard/users/points/assign')?>" class="btn btn-primary"><?=t('Add')?></a>

</div>
</div>

</form>
<div class="ccm-pane-body ccm-pane-body-footer">

	<?
		if (!$mode) {
			$mode = $_REQUEST['mode'];
		}
		$txt = Loader::helper('text');
		$keywords = $_REQUEST['keywords'];
		
		if (count($entries) > 0) { ?>	
			<table border="0" cellspacing="0" cellpadding="0" id="ccm-product-list" class="table table-condensed ccm-results-list">
			<tr>
				<th class="<?=$upEntryList->getSearchResultsClass('uName')?>"><a href="<?=$upEntryList->getSortByURL('uName', 'asc')?>"><?=t('User')?></a></th>
				<th class="<?=$upEntryList->getSearchResultsClass('upaName')?>"><a href="<?=$upEntryList->getSortByURL('upaName', 'asc')?>"><?=t('Action')?></a></th>
				<th class="<?=$upEntryList->getSearchResultsClass('upPoints')?>"><a href="<?=$upEntryList->getSortByURL('upPoints', 'asc')?>"><?=t('Points')?></a></th>
				<th class="<?=$upEntryList->getSearchResultsClass('timestamp')?>"><a href="<?=$upEntryList->getSortByURL('timestamp', 'asc')?>"><?=t('Date Assigned')?></a></th>
				<th><?=t("Details")?></th>
				<th></th>
			</tr>
		<?php 
		foreach($entries as $up) { ?>
			<tr class="ccm-list-record <?=$striped?>">
				<?
				$ui = $up->getUserPointEntryUserObject();
				$action = $up->getUserPointEntryActionObject();
				?>
				<td><? if (is_object($ui)) { ?><?php echo $ui->getUserName()?><? } ?></td>
				<td><? if (is_object($action)) { ?><?=$action->getUserPointActionName()?><? } ?></td>
				<td><?php echo number_format($up->getUserPointEntryValue())?></td>
				<td><?php echo date(DATE_APP_GENERIC_MDYT, strtotime($up->getUserPointEntryTimestamp()));?></td>
				<td><?=$up->getUserPointEntryDescription()?></td>
				<td style="Text-align: right">
					<?php echo $concrete_interface->button(t('Delete'),View::url('/dashboard/users/points/','deleteEntry',$up->getUserPointEntryID()),
						'', 'btn btn-small', array(),"return confirm('<?=t('Are you sure?')?>')"); ?>
				</td>
			</tr>
		<?php } ?>
		</table>
		<? } else { ?>
			<div id="ccm-list-none"><?=t('No Entries found.')?></div>
		<? } 
		$upEntryList->displayPaging(); ?>
</div>
<? print Loader::helper('concrete/dashboard')->getDashboardPaneFooterWrapper(false)?>