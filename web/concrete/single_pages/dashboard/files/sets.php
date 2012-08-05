<?php defined('C5_EXECUTE') or die("Access Denied."); ?>
<?php $ih = Loader::helper('concrete/interface'); ?>
<?php if ($this->controller->getTask() == 'view_detail') { ?>

    <?=Loader::helper('concrete/dashboard')->getDashboardPaneHeaderWrapper(t('File Set'), false, 'span12 offset2', false)?>
    <form method="post" id="file_sets_edit" action="<?=$this->url('/dashboard/files/sets', 'file_sets_edit')?>" onsubmit="return ccm_saveFileSetDisplayOrder()">
        <?=$form->hidden('fsDisplayOrder', '')?>
        <?=$validation_token->output('file_sets_edit');?>

    <div class="ccm-pane-body">

    <div class="clearfix">
    <ul class="tabs">
        <li class="active"><a href="javascript:void(0)" onclick="$('.tabs').find('li.active').removeClass('active');$(this).parent().addClass('active');$('.ccm-tab').hide();$('#ccm-tab-details').show()" ><?=t('Details')?></a></li>
        <li><a href="javascript:void(0)" onclick="$('.tabs').find('li.active').removeClass('active');$(this).parent().addClass('active');$('.ccm-tab').hide();$('#ccm-tab-files').show()"><?=t("Files in Set")?></a></li>
    </ul>
    </div>

    <div id="ccm-tab-details" class="ccm-tab">

        <?php
        $u=new User();

        $delConfirmJS = t('Are you sure you want to permanently remove this file set?');
        ?>

        <script type="text/javascript">
        deleteFileSet = function() {
            if (confirm('<?=$delConfirmJS?>')) {
                location.href = "<?=$this->url('/dashboard/files/sets', 'delete', $fs->getFileSetID(), Loader::helper('validation/token')->generate('delete_file_set'))?>";
            }
        }
        </script>

        <div class="clearfix">
        <?=$form->label('file_set_name', t('Name'))?>
        <div class="input">
            <?=$form->text('file_set_name',$fs->fsName, array('class' => 'span5'));?>
        </div>
        </div>

        <?php if (PERMISSIONS_MODEL != 'simple') { ?>
        <div class="clearfix">
        <?=$form->label('fsOverrideGlobalPermissions', t('Custom Permissions'))?>
        <div class="input">
        <ul class="inputs-list">
            <li><label><?=$form->checkbox('fsOverrideGlobalPermissions', 1, $fs->overrideGlobalPermissions())?> <span><?=t('Enable custom permissions for this file set.')?></span></label></li>
        </ul>
        </div>
        </div>

        <div id="ccm-file-set-permissions-wrapper" <?php if (!$fs->overrideGlobalPermissions()) { ?> style="display: none" <?php } ?>>

        <a class="btn ccm-button-right dialog-launch ug-selector" href="<?=REL_DIR_FILES_TOOLS_REQUIRED?>/users/search_dialog?mode=choose_multiple" dialog-modal="false" dialog-width="90%" dialog-title="<?=t('Add User')?>"  dialog-height="70%"><?=t('Add User')?></a>
        <a class="btn ccm-button-right dialog-launch ug-selector" style="margin-right: 5px" href="<?=REL_DIR_FILES_TOOLS_REQUIRED?>/select_group" dialog-modal="false" dialog-title="<?=t('Add Group')?>"><?=t('Add Group')?></a>

        <div class="ccm-spacer">&nbsp;</div><br/>

        <div id="ccm-file-permissions-entities-wrapper" class="ccm-permissions-entities-wrapper">
        <div id="ccm-file-permissions-entity-base" class="ccm-permissions-entity-base">

            <?php print $ph->getFileAccessRow('SET'); ?>

        </div>

        <?php
        if ($fs->overrideGlobalPermissions()) {
            $gl = new GroupList($fs);
            $ul = new UserInfoList($fs);
        } else {
            $gfs = FileSet::getGlobal();
            $gl = new GroupList($gfs);
            $ul = new UserInfoList($gfs);

        }

        $gArray = $gl->getGroupList();
        $uArray = $ul->getUserInfoList();
        foreach ($gArray as $g) { ?>

            <?php print $ph->getFileAccessRow('SET','gID_' . $g->getGroupID(), $g->getGroupName(), $g->getFileSearchLevel(), $g->getFileReadLevel(), $g->getFileWriteLevel(), $g->getFileAdminLevel(), $g->getFileAddLevel(), $g->getAllowedFileExtensions()); ?>

        <?php } ?>
        <?php foreach ($uArray as $ui) { ?>

            <?php print $ph->getFileAccessRow('SET','uID_' . $ui->getUserID(), $ui->getUserName(), $ui->getFileSearchLevel(), $ui->getFileReadLevel(), $ui->getFileWriteLevel(), $ui->getFileAdminLevel(), $ui->getFileAddLevel(), $ui->getAllowedFileExtensions()); ?>

        <?php } ?>
        </div>

        <div class="ccm-spacer">&nbsp;</div>

        </div>
        <?php } ?>

        <?php
            echo $form->hidden('fsID',$fs->getFileSetID());
        ?>

        </div>

    <div style="display: none" class="ccm-tab" id="ccm-tab-files">
        <?php
        Loader::model("file_list");
        $fl = new FileList();
        $fl->filterBySet($fs);
        $fl->sortByFileSetDisplayOrder();
        $files = $fl->get();
        if (count($files) > 0) { ?>

        <p><?=t('Click and drag to reorder the files in this set. New files added to this set will automatically be appended to the end.')?></p>
        <div class="ccm-spacer">&nbsp;</div>

        <ul class="ccm-file-set-file-list">

        <?php

        foreach ($files as $f) { ?>

        <li id="fID_<?=$f->getFileID()?>">
            <div>
                <?=$f->getThumbnail(1)?>
                <span style="word-wrap: break-word"><?=$f->getTitle()?></span>
            </div>
        </li>

        <?php } ?>

        </ul>
        <?php } else { ?>
            <p><?=t('There are no files in this set.')?></p>
        <?php } ?>
    </div>
    </div>
    <div class="ccm-pane-footer">
        <input type="submit" value="<?=t('Save')?>" class="btn primary ccm-button-v2-right" />
        <?php print $ih->button_js(t('Delete'), "deleteFileSet()", 'right','error');?>
    </div>

    <?=Loader::helper('concrete/dashboard')->getDashboardPaneFooterWrapper(false)?>

    </form>

    <script type="text/javascript">

    ccm_saveFileSetDisplayOrder = function() {
        var fslist = $('.ccm-file-set-file-list').sortable('serialize');
        $('input[name=fsDisplayOrder]').val(fslist);

        return true;
    }

    $(function() {
        $(".ccm-file-set-file-list").sortable({
            cursor: 'move',
            opacity: 0.5
        });

    });

    </script>

    <style type="text/css">
    .ccm-file-set-file-list:hover {cursor: move}
    </style>

    <script type="text/javascript">

        $(function() {
            ccm_triggerSelectUser = function(uID, uName) {
                ccm_alSelectPermissionsEntity('uID', uID, uName);
            }

            ccm_triggerSelectGroup = function (gID, gName) {
                ccm_alSelectPermissionsEntity('gID', gID, gName);
            }

            $(".ug-selector").dialog();
            ccm_alActivateFilePermissionsSelector();

            $("#fsOverrideGlobalPermissions").click(function() {
                if ($(this).prop('checked')) {
                    $('#ccm-file-set-permissions-wrapper').show();
                } else {
                    $('#ccm-file-set-permissions-wrapper').hide();
                }
            });
        });
</script>
<?php } else { ?>

    <?=Loader::helper('concrete/dashboard')->getDashboardPaneHeaderWrapper(t('File Sets'), false, 'span12 offset2', false)?>
    <div class="ccm-pane-options">
    <div class="ccm-pane-options-permanent-search">

        <form id="ccm-file-set-search" method="get" action="<?=$this->url('/dashboard/files/sets')?>">

        <div class="span5">
        <?=$form->label('fsKeywords', t('Keywords'))?>
        <div class="input">
        <input type="text" id="fsKeywords" name="fsKeywords" value="<?=Loader::helper('text')->entities($_REQUEST['fsKeywords'])?>" class="span3" />
        </div>
        </div>

        <div class="span4">
        <?=$form->label('fsType', t('Type'))?>
        <div class="input">
        <select id="fsType" name="fsType" class="span3">
        <option value="<?=FileSet::TYPE_PUBLIC?>" <?php if ($fsType != FileSet::TYPE_PRIVATE) { ?> selected <?php } ?>><?=t('Public Sets')?></option>
        <option value="<?=FileSet::TYPE_PRIVATE?>" <?php if ($fsType == FileSet::TYPE_PRIVATE) { ?> selected <?php } ?>><?=t('My Sets')?></option>
        </select>
        </div>
        </div>

        <input type="submit" class="btn" value="<?=t('Search')?>" />
        <input type="hidden" name="group_submit_search" value="1" />
        </form>

    </div>
    </div>
    <div class="ccm-pane-body <?php if (!$fsl->requiresPaging()) { ?> ccm-pane-body-footer <?php } ?> ">

        <a href="<?=View::url('/dashboard/files/add_set')?>" style="float: right;z-index:999;position:relative;top:-5px" class="btn primary"><?=t("Add File Set")?></a>

        <?=$fsl->displaySummary()?>

        <?php if (count($fileSets) > 0) { ?>

            <style type="text/css">
                div.ccm-paging-top {padding-bottom:10px;}
            </style>

        <?php foreach ($fileSets as $fs) { ?>

            <div class="ccm-group">
                <a class="ccm-group-inner" href="<?=$this->url('/dashboard/files/sets/', 'view_detail', $fs->getFileSetID())?>" style="background-image: url(<?=ASSETS_URL_IMAGES?>/icons/group.png)"><?=$fs->getFileSetName()?></a>
            </div>

        <?php }

        } else { ?>

            <p><?=t('No file sets found.')?></p>

        <?php } ?>

    </div>
    <?php if ($fsl->requiresPaging()) { ?>
        <div class="ccm-pane-footer">
        <?php $fsl->displayPagingV2(); ?>
        </div>
    <?php } ?>
    <?=Loader::helper('concrete/dashboard')->getDashboardPaneFooterWrapper($fsl->requiresPaging())?>

    <script type="text/javascript">
        var editFileSet = function(fsID){
            //set id
            $('#fsID').attr('value',fsID);
            $('#file-sets-edit-or-delete-action').attr('value','edit-form');
            //submit form
            $("#file-sets-edit-or-delete").get(0).submit();
        }

        var deleteFileSet = function(fsID){
            //set id
            $('#fsID').attr('value',fsID);
            $('#file-sets-edit-or-delete-action').attr('value','delete');
            if (confirm("<?=t('Are you sure you want to delete this file set?')?>")) {
                $("#file-sets-edit-or-delete").get(0).submit();
            }
        }

    </script>
<?php }
