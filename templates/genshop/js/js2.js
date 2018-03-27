jQuery(document).ready(function($){

	$('#menubar ul.nav > li > a').each(function(index, el) {
		var title = '';

		if ($(el).attr('title') !== '' && typeof $(el).attr('title') !== 'undefined') {
			title = '<span class="gift">' + $(el).attr('title') + '</span>';
		}

		$(title).appendTo($(el));

	});

});