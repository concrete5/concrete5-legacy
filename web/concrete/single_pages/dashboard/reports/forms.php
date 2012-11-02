<?php defined('C5_EXECUTE') or die("Access Denied.");
/* @var $h ConcreteDashboardHelper */
$h = Loader::helper('concrete/dashboard');
/* @var $ih ConcreteInterfaceHelper */
$ih = Loader::helper('concrete/interface');
/* @var $nh NavigationHelper */
$nh = Loader::helper('navigation');
/* @var $text TextHelper */
$text = Loader::helper('text');
/* @var $dh DateHelper*/
$dh = Loader::helper('date');
/* @var $urlhelper UrlHelper */
$urlhelper = Loader::helper('url');
/* @var $json JsonHelper */
$json = Loader::helper('json');
/* @var $db DataBase */
$db = Loader::db();
?>
<script>
jQuery(function($) {
	var deleteResponse = (<?=$json->encode(t('Are you sure you want to delete this form submission?'))?>),
		deleteForm = (<?=$json->encode(t('Are you sure you want to delete this form and its form submissions?'))?>);
	$('.delete-response').live('click', function(e) {
		if (!confirm(deleteResponse)) {
			e.preventDefault();
		}
	});
	$('.delete-form').live('click', function(e) {
		if (!confirm(deleteForm)) {
			e.preventDefault();
		}
	});
});
</script>
<?if(!isset($questionSet)):?>
<?=$h->getDashboardPaneHeaderWrapper(t('Form Results'));?>
<?
$showTable = false;
foreach ($surveys as $qsid => $survey) {
	$block = Block::getByID((int) $survey['bID']);
	if (is_object($block)) {
		$showTable = true;
		break;
	}
}

if ($showTable) { ?>

<table class="table table-striped">
	<thead>
		<tr>
			<th><?php echo t('Form')?></th>
			<th><?php echo t('Submissions')?></th>
			<th><?php echo t('Options')?></th>
		</tr>
	</thead>
	<tbody>
		<? foreach ($surveys as $qsid => $survey):
		$block = Block::getByID((int) $survey['bID']);
		if (!is_object($block)) {
			continue;
		}
		$in_use = (int) $db->getOne(
			'SELECT count(*)
			FROM CollectionVersionBlocks
			INNER JOIN Pages
			ON CollectionVersionBlocks.cID = Pages.cID
			INNER JOIN CollectionVersions
			ON CollectionVersions.cID = Pages.cID
			WHERE CollectionVersions.cvIsApproved = 1
			AND CollectionVersionBlocks.cvID = CollectionVersions.cvID
			AND CollectionVersionBlocks.bID = ?',
			array($block->bID)
		);
		$url = $nh->getLinkToCollection($block->getBlockCollectionObject());
?>
		<tr>
			<td><?=$text->entities($survey['surveyName'])?></td>
			<td><?=$text->entities($survey['answerSetCount'])?></td>
			<td>
				<?=$ih->button(t('View Responses'), DIR_REL . '/index.php?cID=' . $c->getCollectionID().'&qsid='.$qsid, 'left', 'small')?>
				<?=$ih->button(t('Open Page'), $url, 'left', 'small')?>
				<?if(!$in_use):?>
				<?=$ih->button(t('Delete'), $this->action('').'?bID='.$survey['bID'].'&qsID='.$qsid.'&action=deleteForm', 'left', 'small error delete-form')?>
				<?endif?>
			</td>
		</tr>
		<?endforeach?>
	</tbody>
</table>
<? } else { ?>
	<p><?=t('There are no available forms in your site.')?></p>
<? } ?>
<?=$h->getDashboardPaneFooterWrapper();?>
<?else:?>
<?=$h->getDashboardPaneHeaderWrapper(t('Responses to %s', $surveys[$questionSet]['surveyName']), false, false, false);?>
<div class="ccm-pane-body <? if(!$paginator || !strlen($paginator->getPages())>0){ ?> ccm-pane-body-footer <? } ?>">
<?if(count($answerSets) == 0):?>
<div><?=t('No one has yet submitted this form.')?></div>
<?else:?>

<div class="ccm-list-action-row">
	<a id="ccm-export-results" href="<?=$this->action('excel', '?qsid=' . $questionSet)?>"><span></span><?=t('Export to Excel')?></a>
</div>

<table class="table table-striped">
	<thead>
		<tr>
			<? if($_REQUEST['sortBy']=='chrono') { ?>
			<th class="header headerSortDown">
				<a href="<?=$text->entities($urlhelper->unsetVariable('sortBy'))?>">
			<? } else { ?>
			<th class="header headerSortUp">
				<a href="<?=$text->entities($urlhelper->setVariable('sortBy', 'chrono'))?>">
			<? } ?>		
				<?=t('Date')?>
				</a>
			</th>
			<th><?=t('User')?></th>
<?foreach($questions as $question):?>
			<th><?=$question['question']?></th>
<?endforeach?>
			<th><?=t('Actions')?></th>
		</tr>	
	</thead>
	<tbody>
<?foreach($answerSets as $answerSetId => $answerSet):?>
		<tr>
			<td>
<?=$dh->getSystemDateTime($answerSet['created'])?></td>
			<td><?
			if ($answerSet['uID'] > 0) { 
				$ui = UserInfo::getByID($answerSet['uID']);
				if (is_object($ui)) {
					print $ui->getUserName().' ';
				}
				print t('(User ID: %s)', $answerSet['uID']);
			}
			?></td>
<?
$countries = null;
foreach($questions as $questionId => $question):
			switch($question['inputType']) {
				case 'fileupload':
					$fID = (int) $answerSet['answers'][$questionId]['answer'];
					$file = File::getByID($fID);
					if ($fID && $file) {
						$fileVersion = $file->getApprovedVersion();
						echo '<td><a href="' . $fileVersion->getRelativePath() .'">'.$text->entities($fileVersion->getFileName()).'</a></td>';
					} else {
						echo '<td>'.t('File not found').'</td>';
					}
					break;
				case 'text':
					echo '<td>'.$text->entities($answerSet['answers'][$questionId]['answerLong']).'</td>';
					break;
				case 'country':
					echo '<td>';
					if(!empty($answerSet['answers'][$questionId]['answer'])) {
						if(!$countries) {
							$countries = Loader::helper('lists/countries')->getCountries();
						}
						if(array_key_exists($answerSet['answers'][$questionId]['answer'], $countries)) {
							echo $text->entities($countries[$answerSet['answers'][$questionId]['answer']]);
						}
						else {
							echo $text->entities($answerSet['answers'][$questionId]['answer']);
						}
						}
					echo '</td>';
					break;
				default:
					echo '<td>'.$text->entities($answerSet['answers'][$questionId]['answer']).'</td>';
					break;
			}
			
endforeach?>
			<td>
				<?=$ih->button(
					t("Delete"),
					$this->action('').'?qsid='.$answerSet['questionSetId'].'&asid='.$answerSet['asID'].'&action=deleteResponse',
					'left',
					'danger delete-response small'
				)?>
			</td>
		</tr>
<?endforeach?>
	</tbody>
</table>
</div>
<? if($paginator && strlen($paginator->getPages())>0){ ?>	 
<div class="ccm-pane-footer">
	<div class="pagination">
	  <ul>
		  <li class="prev"><?=$paginator->getPrevious()?></li>
		  
		  <? // Call to pagination helper's 'getPages' method with new $wrapper var ?>
		  <?=$paginator->getPages('li')?>
		  
		  <li class="next"><?=$paginator->getNext()?></li>
	  </ul>
	</div>
</div>
<? } ?>		
<?endif?>
<?=$h->getDashboardPaneFooterWrapper(false);?>
<?endif?>