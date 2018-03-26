function reponSwiper(class_container, nexbtn, prebtn) {
	if (!jQuery(class_container).length) {
		return false;
	}
	var swipername = new Swiper (class_container, {
		// Optional parameters Swiper
		speed: 600,
		nextButton: nexbtn,
		prevButton: prebtn,
		preventClicks: false,
		preventClicksPropagation: false
	});

	function resizeswp(obj) {
		if (jQuery(window).width() > 1024) {
			obj.params.slidesPerView = 4;
			obj.params.spaceBetween=30;
		}
		if (jQuery(window).width() <= 1024) {
			obj.params.slidesPerView = 3;
			obj.params.spaceBetween=15;
		}
		if (jQuery(window).width() <= 768) {
			obj.params.slidesPerView = 2;
			obj.params.spaceBetween=15;
		}
		if (jQuery(window).width() <= 480) {
			obj.params.slidesPerView = 1;
			obj.params.spaceBetween=20;
		}
		obj.update();
	}

	resizeswp(swipername);
	jQuery(window).resize(function(){
		resizeswp(swipername);
	});
}
function reponSwiper_mostproduct(class_container, nexbtn, prebtn) {
	if (!jQuery(class_container).length) {
		return false;
	}
	var swipername = new Swiper (class_container, {
		// Optional parameters Swiper
		speed: 600,
		nextButton: nexbtn,
		prevButton: prebtn,
		preventClicks: false,
		preventClicksPropagation: false
	});
	function resizeswp(obj) {
		if (jQuery(window).width() > 1024) {
			obj.params.slidesPerView = 4;
			obj.params.slidesPerGroup = 4;
			obj.params.spaceBetween=30;
		}
		if (jQuery(window).width() <= 1024) {
			obj.params.slidesPerView = 3;
			obj.params.slidesPerGroup = 3;
			obj.params.spaceBetween=15;
		}
		if (jQuery(window).width() <= 768) {
			obj.params.slidesPerView = 2;
			obj.params.slidesPerGroup = 2;
			obj.params.spaceBetween=15;
		}
		if (jQuery(window).width() <= 480) {
			obj.params.slidesPerView = 1;
			obj.params.slidesPerGroup = 1;
			obj.params.spaceBetween=20;
		}
		obj.update();
	}

	resizeswp(swipername);
	jQuery(window).resize(function(){
		resizeswp(swipername);
	});
}

function makeswiper(swipercontainer, swiperslide){
	jQuery(swipercontainer).find(swiperslide).addClass('swiper-slide').wrapAll('<div class="swiper-wrapper"></div>')
}