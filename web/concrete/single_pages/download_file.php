<?php 

defined('C5_EXECUTE') or die("Access Denied.");

// File ID = $fID
// get the file and 
// Find out where to take the user once they're done.
// We check for a posted value, to see if this is the users first page load or after submitting a password, etc.
$returnURL = ($_POST['returnURL']) ? $_POST['returnURL'] : $_SERVER['HTTP_REFERER'];

?>

<h1><?=t('Download File')?></h1>

<?php if (!isset($filename)) { ?>

	<p><?=t("Invalid File.");?></p>

<?php } else { ?>
	
	<p><?=t('This file requires a password to download.')?></p>
	
	<?php if (isset($error)) {  ?>
		<div class="ccm-error-response"><?=$error?></div>
	<?php } ?>
	
	<form action="<?= View::url('/download_file', 'submit_password', $fID) ?>" method="post">
		<?php if(isset($force)) { ?>
			<input type="hidden" value="<?=force?>" name="force" />
		<?php } ?>
		<input type="hidden" value="<?= $returnURL ?>" name="returnURL" />
		<input type="hidden" value="<?= $rcID ?>" name="rcID"/>
		<label for="password"><?=t('Password')?>: <input type="text" name="password" /></label>
		<br /><br />
		<button type="submit"><?=t('Download')?></button>
	</form>

<?php } ?>

<?php if ($returnURL) { ?>
<p><a href="<?=$returnURL?>">&lt; <?=t('Back')?></a></p>
<?php } ?>
