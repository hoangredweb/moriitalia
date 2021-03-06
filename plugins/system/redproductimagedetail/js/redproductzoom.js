function preloadSlimbox(parameters)
{
	jQuery(document).ready(function($){

		$('div[id*=additional_images]').find('a').click(function(){
			$('div[id*=productImageWrapID_]').find('a').attr('href', $(this).attr('data-zoom-image'));
		});

		getImagename = function (link) {
	    	var re = new RegExp("images\/(.*?)\/thumb\/(.*?)_w([0-9]*?)_h([0-9]*?)(_.*?|)([.].*?)$");
			var m = link.match(re);
			return m;
	    };

	    redproductzoom = function () {
			var mainimg = $('div[id*=productImageWrapID_]').find('img');
			var m = getImagename(mainimg.attr('src'));
			var newxsize = m[3];
			var newysize = m[4];
			var urlfull = redSHOP.RSConfig._('SITE_URL') + 'components/com_redshop/assets/images/' + m[1] + '/' + m[2] + m[6];

			mainimg.attr('data-zoom-image', urlfull);

			//more image
		   	$('div[id*=additional_images]').find('.additional_image').each(function(){
		   		$(this).attr('onmouseout', '');
				$(this).attr('onmouseover', '');

				gl = $(this).attr('id');

				var urlimg = $(this).find('img').attr('data-src');
				if (typeof urlimg === 'undefined' || urlimg === false) {
				   urlimg = $(this).find('img').attr('src');
				}

				var m = getImagename(urlimg);

				var urlthumb = redSHOP.RSConfig._('SITE_URL') + 'components/com_redshop/assets/images/' + m[1] + '/thumb/' + m[2] + '_w' + newxsize + '_h' + newysize + m[5] + m[6];
				var urlfull = redSHOP.RSConfig._('SITE_URL') + 'components/com_redshop/assets/images/' + m[1] + '/' + m[2] + m[6];

				$(this).find('a').attr('data-image', urlthumb);
				$(this).find('a').attr('data-zoom-image', urlfull);

				$(this).find('a').attr('class', 'elevatezoom-gallery');
			});

		   	if(mainimg.data('elevateZoom'))
		   	{
				var ez = mainimg.data('elevateZoom');
				ez.currentImage = urlfull;
				ez.imageSrc = urlfull;
				ez.zoomImage = urlfull;
				ez.closeAll();
				ez.refresh();

				$('.zoomContainer').remove();

				//Create the image swap from the gallery
				$('#'+ez.options.gallery + ' a').click( function(e) {

					//Set a class on the currently active gallery image
					if(ez.options.galleryActiveClass){
						$('#'+ez.options.gallery + ' a').removeClass(ez.options.galleryActiveClass);
						$(this).addClass(ez.options.galleryActiveClass);
					}
					//stop any link on the a tag from working
					e.preventDefault();

					//call the swap image function
					if($(this).data("zoom-image")){ez.zoomImagePre = $(this).data("zoom-image")}
					else{ez.zoomImagePre = $(this).data("image");}
					ez.swaptheimage($(this).data("image"), ez.zoomImagePre);
					return false;
				});

		   	}
		   	else
		   	{
		   		var gl = $('.redhoverImagebox').attr('id');
				mainimg.elevateZoom({
					cursor: "crosshair",
			   		gallery : gl,
			   		loadingIcon: 'plugins/system/redproductzoom/js/zoomloader.gif'
		   		});
		   	}
	    };

	    redproductzoom();
	});

	console.log(parameters)

	/*if (parameters.isenable)
	{

        (function(window, $, PhotoSwipe){

	        $(document).ready(function(){
	            $("a[rel^=\'myallimg\']").attr("rel","lightbox[gallery]");
	            if (!/android|iphone|ipod|series60|symbian|windows ce|blackberry/i.test(navigator.userAgent))
	            {
	                $("a[rel^=\'lightbox\']").slimbox(isenable, null, function(el) {
	                    return (this == el) || ((this.rel.length > 8) && (this.rel == el.rel));
	                });
	            }
	            else
	            {
	                $("a[rel^=\'lightbox\']").photoSwipe({ enableMouseWheel: false , enableKeyboard: false, captionAndToolbarAutoHideDelay: 0});
	            }
	        });
	    }(window, window.jQuery, window.Code.PhotoSwipe));
    }*/
}
