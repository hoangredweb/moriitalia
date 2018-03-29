jQuery(document).ready(function($){

	$('#menubar ul.nav > li > a').each(function(index, el) {
		var title = '';

		if ($(el).attr('title') !== '' && typeof $(el).attr('title') !== 'undefined') {
			title = '<span class="gift">' + $(el).attr('title') + '</span>';
			$(this).parents('#menubar ul.nav > li').addClass('gift')
		}

		$(title).appendTo($(el));

	});
	$('.list_slider').owlCarousel({
	    loop:true,
	    margin:10,
	    nav:true,
	    dots: false,
	    responsive:{
	        0:{
	            items:1
	        },
	        600:{
	            items:1
	        },
	        1000:{
	            items:1
	        }
	    }
	})



	if ($('.old_price_and_stock').find('.product_old_price').text().length > 0){

		$('#product_price').find('.product_r_price').addClass('red');
	}

	$('.oldprice-and-percentage').each(function(index, el)
	{
		if ($(this).find('.category_product_oldprice').text().length > 0){			
				$(this).parent().find('.product_real').addClass('red');
			}
	});


	$('.category_box_inside .wishlist').click(function() {
		window.location = jQuery(this).find('a').attr("href");
		return false;
	});

	$(".size-guide").prependTo($(".attributes_box.size")).insertBefore('.attributes_box.size .attribute_wrapper');

	var showChar = 174;
	var ellipsestext = "...";
	var moretext = "See More";
	var lesstext = " ";

	$('.content_s_desc p').each(function() {
		var content = $(this).html();
		console.log(content);

		if(content.length > showChar) {
			var c = content.substr(0, showChar);
			var h = content.substr(showChar, content.length - showChar);
			var html = c + '<span class="moreellipses">' + ellipsestext+ '&nbsp;</span><span class="morecontent"><span>' + h + '</span>&nbsp;&nbsp;<a href="" class="morelink">' + moretext + '</a></span>';

			$(this).html(html);
		}
	});

	$(".morelink").click(function(){
		if($(this).hasClass("less")) {
			$(this).removeClass("less");
			$(this).html(moretext);
		} else {
			$(this).addClass("less");
			$(this).html(lesstext);
		}
			$(this).parent().prev().toggle();
			$(this).prev().toggle();
		return false;
	});

	$('.redSHOPSiteViewProduct .product-cart-link span.pdaddtocart_link, .wrapper-quickview .cart-link span.pdaddtocart_link ').text('Add to Bag');
	$('.quickview-quickadd .cart-link span.pdaddtocart_link').text('+ Quick Add');

});