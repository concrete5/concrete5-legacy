<? 
defined('C5_EXECUTE') or die("Access Denied."); 
$vt = Loader::helper('validation/token');
?>
<h1><span><?=t('Customize Theme')?></span></h1>
<div class="ccm-dashboard-inner">

<? $h = Loader::helper('concrete/interface'); ?>
<? if (count($styles) > 0) { ?>


<form action="<?=REL_DIR_FILES_TOOLS_REQUIRED?>/themes/preview_internal?themeID=<?=$themeID?>&previewCID=1" method="post" target="preview-theme" id="customize-form">
<?=$vt->output()?>
<?=$form->hidden('saveAction', $this->action('save')); ?>
<?=$form->hidden('resetAction', $this->action('reset')); ?>

<? 
$useSlots = false;
// we use the slots if we have more than one style type for any given style
foreach($styles as $tempStyles) {
	if (count($tempStyles) > 1) {
		$useSlots = true;
		break;
	}
}
?>

<table border="0" cellspacing="0" cellpadding="0" width="100%">
<tr>
	<td valign="top">
<?	
	$customSt = false;
	
	foreach($styles as $sto) { 
		$st = $sto[0];
		if ($st->getType() == PageThemeEditableStyle::TSTYPE_CUSTOM) {
			$customST = $st;
			continue;
		}
		
		?>
	
		<div class="ccm-theme-style-attribute <? if ($useSlots) { ?>ccm-theme-style-slots<? } ?>">
		<?=$st->getName()?>

		<? 
		for ($i = 0; $i < count($sto); $i++) { 
			$slot = $i + 1;
			$st = $sto[$i];
			switch($st->getType()) {
				case PageThemeEditableStyle::TSTYPE_COLOR: ?>
					<?=$form->hidden('input_theme_style_' . $st->getHandle() . '_' . $st->getType(), $st->getValue())?>
					<div class="ccm-theme-style-color <? if ($useSlots) { ?>ccm-theme-style-slot-<?=$slot?><? } ?>" id="theme_style_<?=$st->getHandle()?>_<?=$st->getType()?>"><div hex-color="<?=$st->getValue()?>" style="background-color: <?=$st->getValue()?>"></div></div>
				<? 
					break;
				case PageThemeEditableStyle::TSTYPE_FONT: ?>
					<?=$form->hidden('input_theme_style_' . $st->getHandle() . '_' . $st->getType(), $st->getShortValue())?>
					<div class="ccm-theme-style-font <? if ($useSlots) { ?>ccm-theme-style-slot-<?=$slot?><? } ?>" font-panel-font="<?=$st->getFamily()?>" font-panel-weight="<?=$st->getWeight()?>" font-panel-style="<?=$st->getStyle()?>" font-panel-size="<?=$st->getSize()?>" id="theme_style_<?=$st->getHandle()?>_<?=$st->getType()?>"><div></div></div>
					
				<? 
					break;
			}
		} ?>
		</div>
		
	<? 
	} 
	
	if (isset($customST)) { ?>
	<div class="ccm-theme-style-attribute <? if ($useSlots) { ?>ccm-theme-style-slots<? } ?>">
		<?=t('Add Your CSS')?>
		<?=$form->hidden('input_theme_style_' . $customST->getHandle() . '_' . $customST->getType(), $customST->getOriginalValue())?>
		<div class="ccm-theme-style-custom <? if ($useSlots) { ?>ccm-theme-style-slot-1<? } ?>" id="theme_style_<?=$customST->getHandle()?>_<?=$customST->getType()?>"><div></div></div>
	</div>
	
	<? }

	?>
	
	<? 
		$b1 = $h->button_js(t('Reset'), 'resetCustomizedTheme()', 'left');
		$b2 = $h->button_js(t('Save'), 'saveCustomizedTheme()');
	?>
	<?=$h->buttons($b1, $b2); ?>
	
	<?=$form->hidden('themeID', $themeID)?>
	<?=$form->hidden('ttask', 'preview_theme_customization')?>
	
	</td>
	<td valign="top" width="100%">
	<div style="padding: 8px; border: 2px solid #eee; margin-left: 10px">
	<iframe name="preview-theme" height="500px" width="100%" src="<?=REL_DIR_FILES_TOOLS_REQUIRED?>/themes/preview_internal?themeID=<?=$themeID?>&previewCID=1" border="0" frameborder="0"></iframe>
	</div>

	
	</td>
	</tr>
	</table>
<? 		
} else {
	print t('This theme contains no styles that can be customized.');
}
?>

</form>
<? $ok = t('Ok')?>
<? $resetMsg = t('This will remove any theme customizations you have made.'); ?>
<script type="text/javascript">

var lblOk = '<?=$ok?>';

jQuery.CustomPanel = {
	activePanel: false,
	init: function() {
		var html = '<div id="jquery-custom-panel"><textarea><\/textarea><div id="jquery-custom-panel-save"><input type="button" name="save" value="' + lblOk + '" /><\/<div><\/div>';
		
		if (jQuery('#jquery-custom-panel').length == 0) {
			jQuery(document.body).append(html);
		}

		this.setupSubmit();

	},
	
	showPanel: function(parent) {
		var content = jQuery("#input_" + jQuery(parent).attr('id')).val();
		jQuery("#jquery-custom-panel textarea").val(content);
		this.activePanel = parent;
		var jcp = jQuery('#jquery-custom-panel');
		var dim = jQuery(parent).offset();
		jcp.css('top', dim.top + 36);
		jcp.css('left', dim.left + 5);
		jcp.bind('mousedown', function(e) {
			e.stopPropagation();
		});

		jQuery(document).bind('mousedown', function() {
			jQuery.CustomPanel.hidePanel()
		});
		jcp.show();		

	},

	hidePanel: function() {
		var jcp = jQuery('#jquery-custom-panel');
		jQuery(document).unbind('mousedown');
		jcp.hide();
	},
	
	setupSubmit: function() {
		var jcp = this;
		jQuery('div#jquery-custom-panel-save input').click(function() {
			var content = jQuery('div#jquery-custom-panel textarea').get(0).value;
			var afp = jQuery(jQuery.CustomPanel.activePanel);		
			jQuery("#input_" + afp.attr('id')).val(content);
			jQuery("#customize-form").get(0).submit()
			jcp.hidePanel();
		});
	}
	
}

jQuery.FontPanel = {
	fonts: new Array('Arial','Helvetica', 'Georgia', 'Verdana', 'Trebuchet MS', 'Book Antiqua', 'Tahoma', 'Times New Roman', 'Courier New', 'Arial Black', 'Comic Sans MS'),
	sizes: new Array(8, 10, 11, 12, 13, 14, 16, 18, 21, 24, 28, 36, 48, 64),
	styles: new Array('normal', 'italic'),
	weights: new Array('normal', 'bold'),
	activePanel: false,
	init: function(font, size, weight, style) {
		var html = '<div id="jquery-font-panel"><div id="jquery-font-panel-list-fonts" class="jquery-font-panel-list">';	
		for (i = 0; i < this.fonts.length; i++) {
			html += '<div font-panel-font="' + this.fonts[i] + '" style="font-family:' + this.fonts[i] + '">' + this.fonts[i] + '<\/div>';
		}
		html += '<\/div><div id="jquery-font-panel-list-sizes" class="jquery-font-panel-list">';
		for (i = 0; i < this.sizes.length; i++) {
			html += '<div font-panel-size="' + this.sizes[i] + '">' + this.sizes[i] + '<\/div>';
		}
		html += '<\/div><div id="jquery-font-panel-list-styles" class="jquery-font-panel-list">';
		for (i = 0; i < this.styles.length; i++) {
			html += '<div font-panel-style="' + this.styles[i] + '" style="font-style:' + this.styles[i] + '">' + this.styles[i] + '<\/div>';
		}
		html += '<\/div><div id="jquery-font-panel-list-weights" class="jquery-font-panel-list">';
		for (i = 0; i < this.weights.length; i++) {
			html += '<div font-panel-weight="' + this.weights[i] + '" style="font-weight:' + this.weights[i] + '">' + this.weights[i] + '<\/div>';
		}
		html +='<\/div><div id="jquery-font-panel-save"><input type="button" name="save" value="' + lblOk + '" /><\/<div><\/div>';
		
		if (jQuery('#jquery-font-panel').length == 0) {
			jQuery(document.body).append(html);
		}
		this.setupSubmit();

	},
	
	showPanel: function(parent) {

		this.setupFonts(jQuery(parent).attr('font-panel-font'));
		this.setupSizes(jQuery(parent).attr('font-panel-size'));
		this.setupWeights(jQuery(parent).attr('font-panel-weight'));
		this.setupStyles(jQuery(parent).attr('font-panel-style'));
		
		this.activePanel = parent;
		var jfp = jQuery('#jquery-font-panel');
		jfp.bind('mousedown', function(e) {
			e.stopPropagation();
		});
		var dim = jQuery(parent).offset();
		jfp.css('top', dim.top + 36);
		jfp.css('left', dim.left + 5);
		jQuery(document).bind('mousedown', function() {
			jQuery.FontPanel.hidePanel()
		});
		jfp.show();		
	},

	hidePanel: function() {
		var jfp = jQuery('#jquery-font-panel');
		jQuery(document).unbind('mousedown');
		jfp.hide();
	},
	
	setupSubmit: function() {
		var jfp = this;
		jQuery('div#jquery-font-panel-save input').click(function() {
			var font = jQuery('div#jquery-font-panel-list-fonts div.font-panel-list-selected').attr('font-panel-font');
			var size = jQuery('div#jquery-font-panel-list-sizes div.font-panel-list-selected').attr('font-panel-size');
			var weight = jQuery('div#jquery-font-panel-list-weights div.font-panel-list-selected').attr('font-panel-weight');
			var style = jQuery('div#jquery-font-panel-list-styles div.font-panel-list-selected').attr('font-panel-style');
			var afp = jQuery(jQuery.FontPanel.activePanel);			
			afp.attr('font-panel-weight', weight);
			afp.attr('font-panel-size', size);
			afp.attr('font-panel-style', style);
			afp.attr('font-panel-font', font);
			var selectedString = style + '|' + weight + '|' + size + '|' + font;
			jQuery("#input_" + afp.attr('id')).val(selectedString);
			jQuery("#customize-form").get(0).submit()
			jfp.hidePanel();
		});
	},
	
	setupFonts: function(font) {
		jQuery('div#jquery-font-panel-list-fonts div').removeClass('font-panel-list-selected');
		jQuery('div#jquery-font-panel-list-fonts div').click(function() {
			jQuery('div#jquery-font-panel-list-fonts div').removeClass('font-panel-list-selected');
			jQuery(this).addClass("font-panel-list-selected");
		});
		jQuery('div#jquery-font-panel-list-fonts div[font-panel-font=' + font + ']').addClass('font-panel-list-selected');
	},

	setupSizes: function(size) {
		jQuery('div#jquery-font-panel-list-sizes div').removeClass('font-panel-list-selected');
		jQuery('div#jquery-font-panel-list-sizes div').click(function() {
			jQuery('div#jquery-font-panel-list-sizes div').removeClass('font-panel-list-selected');
			jQuery(this).addClass("font-panel-list-selected");
		});
		jQuery('div#jquery-font-panel-list-sizes div[font-panel-size=' + size + ']').addClass('font-panel-list-selected');
	},

	setupWeights: function(weight) {
		jQuery('div#jquery-font-panel-list-weights div').removeClass('font-panel-list-selected');
		jQuery('div#jquery-font-panel-list-weights div').click(function() {
			jQuery('div#jquery-font-panel-list-weights div').removeClass('font-panel-list-selected');
			jQuery(this).addClass("font-panel-list-selected");
		});
		jQuery('div#jquery-font-panel-list-weights div[font-panel-weight=' + weight + ']').addClass('font-panel-list-selected');
	},

	setupStyles: function(style) {
		jQuery('div#jquery-font-panel-list-styles div').removeClass('font-panel-list-selected');
		jQuery('div#jquery-font-panel-list-styles div').click(function() {
			jQuery('div#jquery-font-panel-list-styles div').removeClass('font-panel-list-selected');
			jQuery(this).addClass("font-panel-list-selected");
		});
		jQuery('div#jquery-font-panel-list-styles div[font-panel-style=' + style + ']').addClass('font-panel-list-selected');
	}

}

jQuery.fn.CustomPanel = function() {
	jQuery.CustomPanel.init();
	jQuery(this).click(function() {
		jQuery.CustomPanel.showPanel(this);
	});
}

jQuery.fn.FontPanel = function() {
	jQuery.FontPanel.init();
	jQuery(this).click(function() {
		jQuery.FontPanel.showPanel(this);
	});
}


saveCustomizedTheme = function() {
	jQuery("#customize-form").attr('target', '_self');
	jQuery("#customize-form").get(0).action = jQuery('#saveAction').val();
	jQuery("#customize-form").get(0).submit();
}

resetCustomizedTheme = function() {
	if (confirm('<?=$resetMsg?>')) { 
		jQuery("#customize-form").attr('target', '_self');
		jQuery("#customize-form").get(0).action = jQuery('#resetAction').val();
		jQuery("#customize-form").get(0).submit();
	}
}

jQuery(function() {
	jQuery('div.ccm-theme-style-font').FontPanel();
	jQuery('div.ccm-theme-style-custom').CustomPanel();
	jQuery('div.ccm-theme-style-color').each(function() {
		var thisID = jQuery(this).attr('id');
		var col = jQuery(this).children(0).attr('hex-color');
		jQuery(this).ColorPicker({
			color: col,
			onSubmit: function(hsb, hex, rgb, cal) {
				jQuery('input#input_' + thisID).val('#' + hex);
				jQuery('div#' + thisID + ' div').css('backgroundColor', '#' + hex);
				jQuery("#customize-form").get(0).submit()
				cal.hide();
			}
		});
	});
});


</script>
</div>