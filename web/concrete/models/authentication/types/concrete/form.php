<?php defined('C5_EXECUTE') or die('Access denied.');
$form = Loader::helper('form');
?>
<div class='row'>
	<div class='span5'>
		<div class='control-group'>
			<label class='control-label' for="uName">
				<?=USER_REGISTRATION_WITH_EMAIL_ADDRESS?t('Email Address'):t('Username')?>
			</label>
			<div class='controls'>
				<?=$form->text('uName')?>
			</div>
		</div>
		<div class='control-group'>
			<label class='control-label' for="uPassword">
				<?=t('Password')?>
			</label>
			<div class='controls'>
				<?=$form->password('uPassword')?>
			</div>
		</div>
	</div>
	<div class='span3 offset1'>
		<label class='checkbox'><?=$form->checkbox('uMaintainLogin',1)?> <?php echo t('Remain logged in to website.');?></label>
	</div>
</div>
<div class='actions'>
	<button class='btn primary'><?php echo t('Sign In');?></button>
</div>