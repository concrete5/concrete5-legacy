<?php defined('C5_EXECUTE') or die("Access Denied.");?>

<?=Loader::helper('concrete/dashboard')->getDashboardPaneHeaderWrapper(t('Multilingual Setup'), false, 'span12 offset2', false)?>
<?php

if (count($languages) == 0) { ?>
<div class="ccm-pane-body ccm-pane-body-footer">
    <?=t("You don't have any interface languages installed. You must run concrete5 in English.");?>
</div>
<?php } else { ?>

<form method="post" action="<?=$this->action('save_interface_language')?>">
<div class="ccm-pane-body">

    <div class="clearfix">
    <?=$form->label('LANGUAGE_CHOOSE_ON_LOGIN', t('Login'))?>
    <div class="input">
    <ul class="inputs-list">
    <li>
        <label><?=$form->checkbox('LANGUAGE_CHOOSE_ON_LOGIN', 1, $LANGUAGE_CHOOSE_ON_LOGIN)?> <span><?=t('Offer choice of language on login.')?></span></label>
    </li>
    </ul>
    </div>
    </div>

    <?php
    $args = array();
    if (defined("LOCALE")) {
        $args['disabled'] = 'disabled';
    }
    ?>

    <div class="clearfix">
    <?=$form->label('SITE_LOCALE', t('Default Language'))?>
    <div class="input">
    <?=$form->select('SITE_LOCALE', $interfacelocales, SITE_LOCALE, $args);?>
    </div>
    </div>

    <br/>
    <?=Loader::helper('validation/token')->output('save_interface_language')?>
</div>
<div class="ccm-pane-footer">
    <?= Loader::helper('concrete/interface')->submit(t('Save'), 'save', 'left', 'primary')?>
</div>
</form>

<?php } ?>

<?=Loader::helper('concrete/dashboard')->getDashboardPaneFooterWrapper(false)?>
