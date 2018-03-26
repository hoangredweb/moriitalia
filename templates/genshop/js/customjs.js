jQuery(document).ready(function($){
	jQuery('.alert .close').click(function (e) {
		console.log('close');
	});
	jQuery('.mod_cart_main').each(function(){
		var cart = jQuery(this);
		cart.find('.mod_cart_top').click(function (e) {
			cart.find('.mod_cart_total').toggle();
		});
	});
	jQuery('#redshopcomponent .product .product-left .product_image .productImageWrap').each(function(){
		//fix this issue https://redweb.atlassian.net/browse/MIT-497
		if ( jQuery(this).length ) {
			var flag = true;
			jQuery(window).resize(function(event) {
				if (flag) {
					flag = false;
					setTimeout(function(){
						preloadSlimbox( {isenable: false, mainImage: false} );
						flag = true;
					}, 1000);
				}
			});
		}
	});
	jQuery('#redshopcomponent .compare-page').each(function(){
		jQuery(this).find('.compare_readmore').click(function (e) {
			e.preventDefault();
			jQuery(this).closest('.desc_wrapper').addClass('active');
		});
		jQuery(this).find('.compare_readless').click(function (e) {
			e.preventDefault();
			jQuery(this).closest('.desc_wrapper').removeClass('active');
		});
	});
	jQuery('a[href*="#wul_"]').click(function (e) {
		e.preventDefault();
		var idLink = jQuery(this).attr('href');
		jQuery(idLink).toggleClass('in');
	});
	jQuery('#toolbar-top').each(function(){
		var objtoolbar = jQuery(this);
		var btnremove = jQuery('<div>', {
			class: 'toolbar-remove',
			html: '<i class="icon-remove"></i>'}).appendTo( objtoolbar );
		btnremove.click(function(){
			objtoolbar.hide();
		});
	});
	// trick for this issue https://redweb.atlassian.net/browse/MIT-611
	jQuery('.inner-form.dm-cua-hang').each(function(){
		jQuery('.inner-form.dm-cua-hang .reditemItem .reditem_image img').responsiveEqualHeightGrid();
		var reditemItem = jQuery(this).find('.reditemItem').size();
		var curentitem = reditemItem;
		if ( (reditemItem%3) != 0 ) {
			while( (reditemItem%3) != 0 ){
				reditemItem++;
			}
		}
		var itemcount = reditemItem - curentitem;
		if ( itemcount > 0 ) {
			for (var i = itemcount - 1; i >= 0; i--) {
				jQuery(this).find('#reditemsItems').append('<div class="reditemItem"></div>');
			}
			jQuery('.reditemItem').responsiveEqualHeightGrid();
		}
	});
});