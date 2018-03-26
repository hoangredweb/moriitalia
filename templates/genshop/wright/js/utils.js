if (typeof jQuery != 'undefined' && typeof MooTools != 'undefined' ) {
	(function($) {
		$(document).ready(function(){
			$('.carousel').each(function(index, element) {
				$(this)[index].slide = null;
			});
		});
	})(jQuery);
}

(function($) {
	function wToolbar() {
		if (typeof wrightWrapperToolbar === 'undefined')
			wrightWrapperToolbar = '.wrapper-toolbar';

		$(wrightWrapperToolbar).each(function() {
			$(this).css('min-height',$(this).find('.navbar:first').height() + 'px');
		});

		if ($(document).width() <= 767)
		{
			$('li.dropdown').each(function() {
				var a =  $(this).find('a').eq(0);
				$(a.find('.caret')).click(function(event){
					event.preventDefault();
					var lipa = $(this).parent().parent();

					if (lipa.hasClass('open'))
					{
						lipa.removeClass('open');
					}
					else
					{
						lipa.addClass('open');
					}
				});

				if ($(this).hasClass('active'))
				{
					$(this).addClass('open');
				}
			});
		}
		else
		{
			$('li.dropdown').removeClass('open');
			$('.navbar-collapse').removeClass('in');
			$('.navbar-collapse').addClass('collapse');
		}
	}

	function fixImagesIE() {
		$('img').each(function() {
			if ($(this).attr('width') != undefined)
				$(this).width($(this).attr('width'));
		});
	}

	wToolbar();
	fixImagesIE();

	$(window).resize(function() {
		wToolbar();
	});
})(jQuery);