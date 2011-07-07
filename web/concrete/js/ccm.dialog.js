$.widget.bridge( "jqdialog", $.ui.dialog );

// wrap our old dialog function in the new dialog() function.
jQuery.fn.dialog = function() {
	// Pass this over to jQuery UI Dialog in a few circumstances
	if (arguments.length > 0) {
		$(this).jqdialog(arguments[0], arguments[1], arguments[2]);
		return;
	} else if ($(this).is('div')) {
		$(this).jqdialog();
		return;
	}
	// LEGACY SUPPORT
	return $(this).each(function() {
		$(this).click(function(e) {
			var href = $(this).attr('href');
			var width = $(this).attr('dialog-width');
			var height =$(this).attr('dialog-height');
			var title = $(this).attr('dialog-title');
			var onOpen = $(this).attr('dialog-on-open');
			var onDestroy = $(this).attr('dialog-on-destroy');
			var onClose = $(this).attr('dialog-on-close');
			obj = {
				modal: true,
				href: href,
				width: width,
				height: height,
				title: title,
				onOpen: onOpen,
				onDestroy: onDestroy,
				onClose: onClose
			}
			jQuery.fn.dialog.open(obj);
			return false;
		});	
	});
}

jQuery.fn.dialog.open = function(obj) {
	jQuery.fn.dialog.showLoader();
	if (ccm_uiLoaded) {
		ccm_hideMenus();
	}
	var nd = $(".ui-dialog").length;
	nd++;
	$('body').append('<div id="ccm-dialog-content' + nd + '" style="display: none"></div>');
	
	if (typeof(obj.width) == 'string') {
		if (obj.width.indexOf('%', 0) > 0) {
			w = obj.width.replace('%', '');
			h = obj.height.replace('%', '');
			h = $(window).height() * (h / 100);
			w = $(window).width() * (w / 100);
			h = h + 100;
			w = w + 50;
		} else {
			w = parseInt(obj.width) + 50;
			h = parseInt(obj.height) + 100;
		}
	} else {
		w = parseInt(obj.width) + 50;
		h = parseInt(obj.height) + 100;
	}

	$("#ccm-dialog-content" + nd).jqdialog({
		'modal': true,
		'height': h,
		'width': w,
		'escapeClose': true,
		'title': obj.title,
		'close': function(ev, u) {							
			$(this).jqdialog('destroy').remove();
			$("#ccm-dialog-content" + nd).remove();
			if (typeof obj.onClose != "undefined") {
				if ((typeof obj.onClose) == 'function') {
					obj.onClose();
				} else {
					eval(obj.onClose);
				}
			}
			if (typeof obj.onDestroy != "undefined") {
				if ((typeof obj.onDestroy) == 'function') {
					obj.onDestroy();
				} else {
					eval(obj.onDestroy);
				}
			}
			nd--;
		}
	});		
	
	if (!obj.element) {
		$.ajax({
			type: 'GET',
			url: obj.href,
			success: function(r) {
				jQuery.fn.dialog.hideLoader();
				$("#ccm-dialog-content" + nd).html(r);
				$("#ccm-dialog-content" + nd + " .dialog-launch").dialog();
				$("#ccm-dialog-content" + nd + " .ccm-dialog-close").click(function() {
					jQuery.fn.dialog.closeTop();
				});
				if (typeof obj.onOpen != "undefined") {
					if ((typeof obj.onOpen) == 'function') {
						obj.onOpen();
					} else {
						eval(obj.onOpen);
					}
				}
				
			}
		});			
	} else {
		jQuery.fn.dialog.hideLoader();
		$("#ccm-dialog-content" + nd).append($(obj.element));
		$("#ccm-dialog-content" + nd + " .dialog-launch").dialog();
		$("#ccm-dialog-content" + nd + " .ccm-dialog-close").click(function() {
			jQuery.fn.dialog.closeTop();
		});
		if (typeof obj.onOpen != "undefined") {
			if ((typeof obj.onOpen) == 'function') {
				obj.onOpen();
			} else {
				eval(obj.onOpen);
			}
		}
	}
		
}

jQuery.fn.dialog.replaceTop = function(h) {
	var nd = $(".ui-dialog").length;
	$("#ccm-dialog-content" + nd).html(h);
}

jQuery.fn.dialog.showLoader = function(fnd) {
	if (typeof(imgLoader)=='undefined' || !imgLoader || !imgLoader.src) return false; 
	if ($('#ccm-dialog-loader').length < 1) {
		$("body").append("<div id='ccm-dialog-loader-wrapper'><img id='ccm-dialog-loader' src='"+imgLoader.src+"' /></div>");//add loader to the page
	}
	$('#ccm-dialog-loader-wrapper').css('opacity', 0.8);
	$('#ccm-dialog-loader-wrapper').show();//show loader
	//$('#ccm-dialog-loader-wrapper').fadeTo('slow', 0.2);
}

jQuery.fn.dialog.hideLoader = function() {
	$("#ccm-dialog-loader-wrapper").hide();
}

jQuery.fn.dialog.closeTop = function() {
	var nd = $(".ui-dialog").length;
	$("#ccm-dialog-content" + nd).jqdialog('close');
}

var imgLoader;
var ccm_dialogSkinMode = 'v2';
var ccm_dialogOpen = 0;
jQuery.fn.dialog.loaderImage = CCM_IMAGE_PATH + "/throbber_white_32.gif";


if (jQuery.browser.msie) {
	var ccm_dialogSkinMode = 'transparent';
	if (jQuery.browser.version.substring(0, 1) == 6) {
		var ccm_dialogSkinMode = 'basic';
	}
}

var ccmAlert = {  
    notice : function(title, message, onCloseFn) {
        $.fn.dialog.open({
            href: CCM_TOOLS_PATH + '/alert',
            title: title,
            width: 320,
            height: 160,
            modal: false, 
			onOpen: function () {
        		$("#ccm-popup-alert-message").html(message);
			},
			onDestroy: onCloseFn
        }); 
    },
    
    hud: function(message, time, icon, title) {
    	if ($('#ccm-notification-inner').length == 0) { 
    		$(document.body).append('<div id="ccm-notification"><div id="ccm-notification-inner"></div></div>');
    	}
    	
    	if (icon == null) {
    		icon = 'edit_small';
    	}
    	
    	if (title == null) {	
	    	var messageText = message;
	    } else {
	    	var messageText = '<h3>' + title + '</h3>' + message;
	    }
    	$('#ccm-notification-inner').html('<table border="0" cellspacing="0" cellpadding="0"><tr><td valign="top"><img id="ccm-notification-icon" src="' + CCM_IMAGE_PATH + '/icons/' + icon + '.png" width="16" height="16" /></td><td valign="top">' + messageText + '</td></tr></table>');
		
		$('#ccm-notification').fadeIn({easing: 'easeInQuart', duration: 100});
    	if (time > 0) {
    		setTimeout(function() {
    			$('#ccm-notification').fadeOut({easing: 'easeOutExpo', duration: 800});
    		}, time);
    	}
    	
    }
}      

$(document).ready(function(){   
	imgLoader = new Image();// preload image
	imgLoader.src = jQuery.fn.dialog.loaderImage;

});
