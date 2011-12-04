ccm_closeDashboardPane = function(r) {
	var accepter = $('#ccm-recent-page-' + CCM_CID);
	var l = $(r);
	ccm_showQuickNav(function() { 
		var title = l.parent().parent().parent().find('h3');
		title.css('display','inline');
		$(r).closest('div.ccm-pane').fadeOut(120, 'easeOutExpo');
		title.effect("transfer", { to: accepter, 'easing': 'easeOutExpo'}, 600, function() {
			accepter.hide().css('visibility','visible').fadeIn(240, 'easeInExpo');			
			title.css('display','block');
			ccm_quickNavTimer = setTimeout(function() {
				ccm_hideQuickNav();
			}, 1000);
		});
	});
}


ccm_getDashboardBackgroundImageData = function(image) {
	$.getJSON(CCM_TOOLS_PATH + '/dashboard/get_image_data', {
		'image': image
	}, function(r) {
		if (r) {
			var html = '<div>';
			html += '<strong>' + r.title + '</strong> / ';
			if (r.link) {
				html += '<a target="_blank" href="' + r.link + '">' + r.author + '</a>';
			} else {
				html += r.author;
			}
			if (r.description) {
				html += ' / ' + r.description;
			}
			$('<div id="ccm-dashboard-image-caption" class="ccm-ui"/>').html(html).appendTo(document.body).show();
		}
	});
}