<? defined('C5_EXECUTE') or die("Access Denied."); ?>

<ul class="ccm-pane-header-icons">
	<li><a href="javascript:void(0)" onclick="ccm_closeNewsflow(this)" class="ccm-icon-close"><?=t('Close')?></a></li>
</ul>

<?
$_c = Page::getCurrentPage();
$valt = Loader::Helper('validation/token');
$token = '&amp;' . $valt->getParameter();
if ($_c->getCollectionPath() != '/dashboard/news' && $_c->getCollectionPath() != '/dashboard/welcome' && !isset($_GET['_ccm_dashboard_external'])) { ?>
<div class="well" style="margin-bottom: 0px">
	<? if ($_c->isCheckedOut()) { ?>
	<a href="#" id="ccm-nav-save-arrange" class="btn ccm-main-nav-arrange-option" style="display: none"><?=t('Save Positioning')?></a>
	<a href="<?=DIR_REL?>/<?=DISPATCHER_FILENAME?>?cID=<?=$_c->getCollectionID()?>&amp;approve=APPROVE&amp;ctask=check-in&amp;<?=$token?>" id="ccm-nav-exit-edit-direct" class="btn success ccm-main-nav-edit-option"><?=t('Save Changes')?></a>
	<? } ?>
	<? if (!$_c->isCheckedOut()) { ?><a href="<?=DIR_REL?>/<?=DISPATCHER_FILENAME?>?cID=<?=$c->getCollectionID()?>&amp;ctask=check-out<?=$token?>" id="ccm-nav-check-out" class="btn"><?=t('Edit Page')?></a><? } ?>
</div>
<? } ?>

<?

$u = new User();
$u->saveConfig('NEWSFLOW_LAST_VIEWED', time());
