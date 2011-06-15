<?
/**
 * @package Helpers
 * @category Concrete
 * @subpackage Forms
 * @author Andrew Embler <andrew@concrete5.org>
 * @copyright  Copyright (c) 2003-2008 Concrete5. (http://www.concrete5.org)
 * @license    http://www.concrete5.org/license/     MIT License
 */

/**
 * Special form elements for rating an item.
 * @package Helpers
 * @category Concrete
 * @subpackage Forms
 * @author Andrew Embler <andrew@concrete5.org>
 * @copyright  Copyright (c) 2003-2008 Concrete5. (http://www.concrete5.org)
 * @license    http://www.concrete5.org/license/     MIT License
 */

defined('C5_EXECUTE') or die("Access Denied.");
class FormColorHelper {

	
	/** 
	 * Creates form fields and JavaScript includes to add a color picker widget.
	 * <code>
	 *     $dh->datetime('yourStartDate', '2008-07-12 3:00:00');
	 * </code>
	 * @param string $fieldFormName
	 * @param string $fieldLabel
	 * @param string $value
	 * @param bool $includeJavaScript
	 */
	
	public function output($fieldFormName, $fieldLabel, $value = null, $includeJavaScript = true) {
		$html = '';
		$form = Loader::helper('form');

		$html .= '<div class="ccm-color-swatch-wrapper"><div class="ccm-color-swatch"><div id="f' . $fieldFormName . '" hex-color="' . $value . '" style="background-color:' . $value . '"></div></div></div>';
		$html .= $form->hidden($fieldFormName, $value);
		$html .= $form->label($fieldFormName, $fieldLabel);

		if ($includeJavaScript) { 
			$html .= "<script type=\"text/javascript\">
				jQuery(function() {
					var f" .$fieldFormName. "Div =jQuery('div#f" .$fieldFormName. "');
					var c" .$fieldFormName. " = f" .$fieldFormName. "Div.attr('hex-color'); 
					f" .$fieldFormName. "Div.ColorPicker({
						color: c" .$fieldFormName. ",  
						onSubmit: function(hsb, hex, rgb, cal) { 
							jQuery('input[name=" . $fieldFormName . "]').val('#' + hex);				
							jQuery('div#f" . $fieldFormName. "').css('backgroundColor', '#' + hex); 
							cal.hide();
						},  
						onNone: function(cal) {  
							jQuery('input[name=" . $fieldFormName . "]').val('');		
							jQuery('div#f" . $fieldFormName. "').css('backgroundColor',''); 
						}
					});
				
				});
				</script>";
		}
		return $html;		
		
	}
	
	
}