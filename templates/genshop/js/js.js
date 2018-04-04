jQuery(document).ready(function($){
	var window_w = $(window).innerWidth();
	var window_h = $(window).innerHeight();
	var submenu_with = $('ul.shopbMegaMenu-menu').innerWidth();

	if(window_h>600 && window_h<800){
		$('body').addClass('small-height');
	}

	if(window_w<768){


		$('#menubar #menu .menu li.parent .indicator').toggle(function(event) {
			event.preventDefault();
			$(this).addClass('clicked');
		}, function() {
			event.preventDefault();
			$(this).removeClass('clicked');
		});

		$('#footer .moduletable .col-sm-12 p, #footer .module .col-sm-12 p').insertAfter('.newletter-footer form');
	}
	else{
		var container = $('body .container').first().innerWidth();
	}
	if(window_w>768 && window_w<=1024){
		$('.product .attribute_wrapper select').change(function(event) {
			$('.product .product-right .product_addtocart').css('padding-top', '0');
		});
	}

	function hiderefine(){
		$('.moduletable.filter-product h3 .dropdown').each(function(){
			$(this).parent().nextAll().removeClass('show').addClass('hidden');
			$(this).removeClass('show');
		});
	}

	if( $(window).innerWidth() < 768 ){
		hiderefine();
	}

	$(window).resize(function(event) {
		var window_w_2 = $(window).innerWidth();

		if(window_w_2<768){
			hiderefine();
		}
		else{
			$('.moduletable.filter-product h3 .dropdown').each(function(){
				$(this).addClass('show');
				$(this).parent().nextAll().addClass('show').removeClass('hidden');
			});

			var container = $('body .container').first().innerWidth()
		}
	});

		$('#menubar #menu .menu li.parent').each(function(index, el) {
			$(this).find('.indicator').insertAfter($(this).find('.menuLinkTitle').parent());
			$(this).find('.lv1').removeClass('dropdown');
		});
		$('#menubar #menu .menu li.parent .indicator').click(function(event) {
				event.preventDefault();
				$('#menubar #menu .menu li.parent div.lv1').removeClass('slidedown');
				$(this).next('.lv1').addClass('slidedown');
			    $(this).addClass('clicked');
		});

		$('#menubar #menu .menu li.parent .indicator').toggle(function(event) {
				event.preventDefault();
		 		$(this).next('.lv1').addClass('slidedown');
		 		$(this).addClass('clicked');

		}, function(event) {
			event.preventDefault();
			$(this).next('.lv1').removeClass('slidedown');
		 	$(this).removeClass('clicked');
		});



	$('.showhide').click(function(event) {
		$('#nav-menu').removeClass('in');
	});

	var offset = 300;
	var duration = 500;
	$(window).scroll(function() {
		if ($(this).scrollTop() > offset) {
			$('#nav-menu').removeClass('in');
		}
		if ($(this).scrollTop() > offset) {
			$('.back-to-top').css({
				visibility: 'visible',
				opacity: '1'
			});
		} else {
			$('.back-to-top').css({
				visibility: 'hidden',
				opacity: '0'
			});
		}
	});



	$('.back-to-top').click(function(event) {
		event.preventDefault();
		$('html, body').animate({scrollTop: 0}, duration);
		return false;
	});


	$('.home #system-message-container').insertAfter('#menubar');

	$('.id_137 .container h1').html($('.redSHOPSiteViewOrders .componentheading').text());
	$('#kunena select').select2();

	$('#manufacture ul li').each(function(index, el) {
		var img = $(this).find('img').attr('src');
		if(img=="http://moriitalia.redweb.vn/components/com_redshop/assets/images/manufacturer/thumb/Untitled_1_w200_h80.jpg"){
			$(this).find('img').attr('src', 'http://moriitalia.redweb.vn/components/com_redshop/assets/images/manufacturer/thumb/logo-Moriitalia(3)_w200_h80.jpg');
		}
	});

	//$('#banner-menu-sale .custom').insertAfter('ul.shopbMegaMenu-menu li.item-15527 ul.lv1 .left-image-relative');


	$('.last-cate').parents('.col-sm-9').prev().addClass('hidden-manu');
	$('.category_wrapper.parent').parents('.col-sm-9').prev().prev().css('display', 'none');

	$('.redSHOPSiteViewCategory .mod_redshop_products_wrapper .mod_redshop_products').each(function(index, el) {
		$(this).find('.rating').html($(this).find('.rating').html().replace($(this).find('.rating').text(),''));
	});

	$('.blog .reditemItem .item-image a:empty').parent().css('display', 'none');
	//$('.redcore h1').first().insertBefore('#sidebar1').addClass('title');
	//$('#redshopcomponent.redshop h1').first().insertBefore('#main-content').addClass('title');

	$('#grid-top-slider .module h3').addClass('container');
	$('#grid-top-slider .col-sm-6, .product-list').each(function(index, el) {
		var h3 = $(this).find('h3').first().html();
		$(this).find('h3').first().html('<span class="title">'+h3+'</span>');
	});

	$('.mod_redshop_products').each(function(index, el) {

		//$(this).find('.mod_redshop_products_addtocart,.wishlist').appendTo($(this).find('.mod_product_price_wrapper'));
		$(this).find('.on_sale_wrap').appendTo($(this).find('.mod_redshop_products_image'));
		// $(this).find('.mod_redshop_products_image a').click(function(event) {
		// 	console.log('123');
		// });
	});

	$('.sclogin-social-login .login .facebook a').addClass('icon-facebook').html('Facebook');
	$('.sclogin-social-login .login .google a').addClass('icon-google-plus').html('Google');

	$('.category_product_list.wishlist .cate_redshop_products_wrapper').each(function(index, el) {
		$(this).find('.cate_products_title').insertBefore($(this).find('.category_product_price span:first-child'));
	});


	$('.redform-form.friend input[type="email"]').attr('value', '');

    $('.pagination-start span').addClass('icon-backward icon-first').html("");
    $('.pagination-prev span').addClass('icon-step-backward icon-previous').html("");

    $('.pagination-end a').addClass('icon-forward icon-last').html("");

    $('.newarr-product .mod_redshop_header_wrapper a.see-all').attr('href', 'index.php?option=com_redshop&view=search&layout=newproduct&template_id=8&categorytemplate=3310&productlimit=15&newproduct=365');
    $('.sale-product .mod_redshop_header_wrapper a.see-all').attr('href', 'index.php?option=com_redshop&view=search&layout=productonsale&Itemid=924');

    /* END radio button*/
    /* register page */
    $('#member-registration').html(function(){
    	$(this).find('#jform_email2,#jform_email1').each(function(index, el) {
    		$(this).closest('.control-group').addClass('emailbox');
    	});
    	$(this).find('#jform_password2,#jform_password1').each(function(index, el) {
    		$(this).closest('.control-group').addClass('passwordbox');
    	});
    	$(this).find('.passwordbox').wrapAll('<div class="password-wrapper"></div>');
    	$(this).find('.emailbox').wrapAll('<div class="email-wrapper"></div>');
    	$(this).find('.email-wrapper').insertBefore('.password-wrapper')
    });
    /* end register page */

    $('#top_primary .moduletable.counter h3').toggle(function() {
    	$('#top_primary .moduletable.counter .visitorcounter').slideDown(50);
    }, function() {
    	$('#top_primary .moduletable.counter .visitorcounter').slideUp(50);
    });
    $('.home .newarr-product .mod_redshop_header_wrapper a.btn-sm').attr('href', '/index.php?option=com_redshop&view=search&layout=newproduct&template_id=8&categorytemplate=7&productlimit=6&newproduct=365&Itemid=143');

	$('#grid-bottom2').html(function(){
		$(this).find('.module,.moduletable').each(function(index, el) {
			$(this).find('>*').wrapAll('<div class="module-wrapper"></div>');
		});
	});

	$('[name*="update_cart"]').find('input[name="quantity"]').html(function(){

		$(this).before('<i class="ico-qtyarrow" data-list="'+$(this).attr('id')+'" ></i>');

		var cartQbox = $('#cartQbox');
		var dtoggle = $('[datatoggle="'+$(this).attr('id')+'"]');

		if ( cartQbox.length == 0 ) {
			cartQbox = jQuery('<div/>',{
				id: 'cartQbox'
			});
			cartQbox.appendTo('body');
		}

		dtoggle.appendTo(cartQbox);

	});
	function showcompare(){
		setTimeout(function(){
			var compare = $.trim($('.div_compare ul').html());
				if(compare == ""){
					$('.div_compare .btn-default').css('display', 'none');
				}
				else{
					$('.div_compare .btn-default').css('display', 'inline-block');
				}
		}, 150);
	}

	showcompare();

	$('.product_details fieldset.checkbox label.checkbox, .wishlist fieldset.checkbox label.checkbox').click(function(event) {
		$('.div_compare .btn-default').css('display', 'inline-block');
	});

	$('.remove-link-compare').on("click", function(event) {
		event.preventDefault();
		showcompare();
	});


	
	$('.category_wrapper.parent').parent().parent().prev().addClass('sidebar-parent');


	$('.moduletable.filter-product h3 .dropdown').toggle(function() {
		if($(this).hasClass('show')){
			$(this).parent().nextAll().removeClass('show').addClass('hidden');
			$(this).removeClass('show');
		}
		else{
			$(this).addClass('show');
			$(this).parent().nextAll().addClass('show').removeClass('hidden');
		}
	}, function() {
		if($(this).hasClass('show')){
			$(this).parent().nextAll().removeClass('show').addClass('hidden');
			$(this).removeClass('show');
		}
		else{
			$(this).addClass('show');
			$(this).parent().nextAll().addClass('show').removeClass('hidden');
		}
	});


	$('.compare-page dl').each(function(index, el) {

		var count_div = $(this).find('dd div[name]').length;

		$(this).find('dd div[name]').css('width', 100/count_div+'%');
	});

	//$('.redSHOPSiteViewProduct .product .product-right .brand h3 .manufacture a:first-child img').appendTo('.redSHOPSiteViewProduct .product .product-right .brand h3 .manufacture a:last-child');

	$('.product .product-right .product_addtocart fieldset.checkbox').insertBefore('.product .product-right .product_addtocart form .addcart_group');
	$('.Low-in-stokc').each(function(){
		var Lowinstokc = $(this);
		if ( $(this).find('>span').html().length == 0 ) {
			$(this).addClass('hide');
		}
	});

	$('.modal-body .label2:eq(1)').insertBefore('.modal-body form .redform-form');
	$('.modal-body .label3').insertAfter('.modal-body .submitform ');

	if(window_w >= 600){
		$('.manufacturer_box_wrapper .row-manufacture').responsiveEqualHeightGrid();
	}
	if(window_w>767){
		$('.dm-cua-hang #reditemsItems .reditemItem').responsiveEqualHeightGrid();

		$('#grid-bottom2 .module .mod_reditem_items_wrapper .col-sm-3 h3').responsiveEqualHeightGrid();
	}

	var src_img = $('.homeblog .reditem_image img').attr('src');
	$('.homeblog .reditem_image img').attr('visibility', 'hidden').css('visibility', 'hidden');
	$('.homeblog .link-image').parent().css('background', 'url("'+src_img+'") top center');

	var quick = $('.quick-view').innerWidth();
	//$('.quick-view').css('margin-left', -(quick/2));
	$('.quick-view').click(function(event) {
		$('body').addClass('modal-open');
	});


	$('.modal-backdrop,.modal-header .close').click(function(event) {
		$('.modal').removeClass('in');
		$('body').removeClass('modal-open');
	});

	if(window_w>767){
		$('.cate_redshop_products_wrapper').responsiveEqualHeightGrid();
		$('.manufacturer_box_wrapper .manufacturer_box_outside').responsiveEqualHeightGrid();
	}
	if(window_w<=1024){
		$('.cate_redshop_products_wrapper').each(function(index, el) {
			var item_h = $(this).height();
			$(this).find('.inner_redshop_products').css('min-height', item_h+30);
		});
	}

	$('.div-link-compare a').first().addClass('link-compare');

	$('#tdUsernamePassword').insertAfter($('input#createaccount').parent().parent());
	$('.redshop input.button').addClass('btn btn-primary');
	$('.redSHOPSiteViewSearch h3').prev('br').remove();

	$('.product-list').each(function(index, el) {
    	if($(this).find('.chevron-box').next('.swiper-wrapper').html="" || $(this).find('.chevron-box').next('.swiper-wrapper').length<=0){
	    	$(this).parent().remove();
		}
    });
    var title_cart = $('.compare-page .pdaddtocart_img_link img').attr('alt');
    $('.compare-page .pdaddtocart_img_link img').remove();
    $('.compare-page .pdaddtocart_img_link').html(title_cart);
    $('form[name="newwishlistForm"] a input[type="button"]').addClass('btn btn-cancel');
    $('.adminform div').each(function(index, el) {
    	var span = $(this).find('span:first-child');
    	var span_html = $.trim(span.html());
    	if(span_html==":"){
    		span.html("");
    	}
    });

	$('.redSHOPSiteViewAccount .accounttable .col-sm-6 .control-group').each(function(index, el) {
		var label = $.trim($(this).find('label').html());
		if(label==""){
			$(this).hide();
		}
	});

	$('.category_header').parent().prevAll('#above-content,#above-content-1').remove();

	
  // var textlabel = $('.attributes_box .attribute_wrapper >label').text();
  // 	console.log(textlabel);
  	$('.attributes_box').each(function(i,e){
  		 var textlabel = $(this).find('.attribute_wrapper >label').text().toLowerCase();
  		$(this).addClass(textlabel)
  	});

  	 // /* radio button*/

    $('.attributes_box [type="radio"]').change(function(){

        $('.attributes_box [type="radio"]').each(function(){
            if( $(this).is(':checked')){
                $(this).next().addClass('radio-checked');
            }else{
                $(this).next().removeClass('radio-checked');
            }
        });
    });

 //    $( '.moduletable.menu-on-top >ul >li .sign-in' ).click(function () {
	//   if ( $( ".moduletable.menu-on-top >ul >li >ul" ).is( ":hidden" ) ) {
	//     $( ".moduletable.menu-on-top >ul >li >ul" ).slideDown( "slow" );
	//   } else {
	//     $( ".moduletable.menu-on-top >ul >li >ult" ).slideUp("slow");
	//   }
	// });

	// $(".product_detail div.tab-content").prependTo($(".product-right")).insertAfter('.wrap-tab');
	
	if(window_w <= 979)
	{
		$("#header #logo ").insertBefore('#header #top');
	}
	if(window_w <=767)
	{
	   	$( '#menu-toggle' ).click(function () {
	  		if ( $( "#nav-menu" ).is( ":hidden" ) ) {
			    $( "#nav-menu" ).slideDown( "slow" );
		  	} else {
		    	$( "#nav-menu" ).slideUp("slow");
		  	}
		});
		$( '#header #top_primary .searchbox .icon_search' ).click(function () {
			console.log('123');
	  		if ( $( "#header #top_primary .searchbox form" ).is( ":hidden" ) ) {
			    $( "#header #top_primary .searchbox form" ).slideDown( "slow" );
		  	} else {
		    	$( "#header #top_primary .searchbox form" ).slideUp("slow");
		  	}
		});

	$( '#footer .module.colum-shop >h3' ).click(function () {
  		if ( $( "#footer .module.colum-shop ul.menu" ).is( ":hidden" ) ) {
  			$( '#footer .module.colum-shop >h3' ).addClass('open');
		    $( "#footer .module.colum-shop ul.menu" ).slideDown( "slow" );
	  	} else {
	    	$( "#footer .module.colum-shop ul.menu" ).slideUp("slow");
	    	$( '#footer .module.colum-shop >h3' ).removeClass('open');
	  	}
	});

	$( '#footer .module.colum-company >h3' ).click(function () {
  		if ( $( "#footer .module.colum-company ul.menu" ).is( ":hidden" ) ) {
  			$( '#footer .module.colum-company >h3' ).addClass('open');
		    $( "#footer .module.colum-company ul.menu" ).slideDown( "slow" );
	  	} else {
	    	$( "#footer .module.colum-company ul.menu" ).slideUp("slow");
	    	$( '#footer .module.colum-company >h3' ).removeClass('open');
	  	}
	});

	$( '#footer .module.my-account >h3' ).click(function () {
  		if ( $( "#footer .module.my-account ul.menu" ).is( ":hidden" ) ) {
  			$( '#footer .module.my-account >h3' ).addClass('open');
		    $( "#footer .module.my-account ul.menu" ).slideDown( "slow" );
	  	} else {
	    	$( "#footer .module.my-account ul.menu" ).slideUp("slow");
	    	$( '#footer .module.my-account >h3' ).removeClass('open');
	  	}
	});

	$( '#footer .module.colum-support >h3' ).click(function () {
  		if ( $( "#footer .module.colum-support ul.menu" ).is( ":hidden" ) ) {
  			$( '#footer .module.colum-support >h3' ).addClass('open');
		    $( "#footer .module.colum-support ul.menu" ).slideDown( "slow" );
	  	} else {
	    	$( "#footer .module.colum-support ul.menu" ).slideUp("slow");
	    	$( '#footer .module.colum-support >h3' ).removeClass('open');
	  	}
	});

		
	}

	/*menu responsive*/
	$('.navbar-header .navbar-toggle').on('click', function() {
		$( ".navbar-header .navbar-toggle span:nth-of-type(2)" ).toggleClass( "icon-bar--1");
	  	$( ".navbar-header .navbar-toggle span:nth-of-type(3)" ).toggleClass( "icon-bar--2");
	  	$( ".navbar-header .navbar-toggle span:nth-of-type(4)" ).toggleClass( "icon-bar--3");

	  	$('#nav-menu-phone').toggleClass('nav-menu-phone--open');
	  	return false;
	});
	
});