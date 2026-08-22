/* 메인 애니메이션 */
function animateM($item){
	var scrollTop = $(window).scrollTop() + $(window).height();
	if ($item.length){
		if (scrollTop > $item.offset().top) {
			$item.addClass('motion');
		}
	}
}
function showDiv(){
	var mainDivEl = $('.main-wrap > div');
	for (var i = 0; i < mainDivEl.length; i++) {
		animateM( mainDivEl.eq(i) );
	}
}

/* 이 상품 어때요? */
let prdSwiper;
function initSwiper(bool, between, view) {
  if (typeof(prdSwiper) == 'object') prdSwiper.destroy();
  
  return prdSwiper = new Swiper('.prd-slide1 .swiper-container', {
    spaceBetween: between,		
    slidesPerView: view,
    loop: bool,
	centeredSlides: bool,					
	grabCursor: true,
	scrollbar: {
		el: '.prd-slide1 .swiper-scrollbar',
		hide: false,
		draggable: true,
	},
	navigation: {	
		nextEl: '.prd-slide1 .swiper-button-next',
		prevEl: '.prd-slide1 .swiper-button-prev',
	},
	on: {
		slideChange: function(){
			prdSlider();
		}
	},
  });
} 

function prdSlider() {
	setTimeout(function(){
		var prdSlideH = $('.prd-slide1 > div').height();
		document.documentElement.style.setProperty('--main-prd1-slide-height', prdSlideH + 'px');
	}, 100);
}
function mainSizeListener() {

	if ( $(window).outerWidth() <= 768) {
		initSwiper(false, 10, 2.3);
	} else {
		initSwiper(true, 0, 'auto');
	}

}

$(window).resize(function(){
	mainSizeListener();
	prdSlider();
});

$(window).scroll(function () {
	showDiv(); 
});

$(document).ready(function(){
	showDiv(); 
	mainSizeListener();
	prdSlider();

	$(".main-visual").each(function() {
		$(this).css("opacity",1);

		$('.main-visual .visual-slider .swiper-wrapper .swiper-slide').each(function(i) {
			var title = $('.main-visual .slider-nav > li');
			var idx = $(this).data('swiper-slide-index');
			$(this).append(title.eq(idx).html());
		});
	});

	/* 퀵 메뉴 */
	$('.main-quick-menu > ul > li').each(function(i){
		var num = 0;
		for (j=0;j<=i;j++) {
			num += 2;
		}
		$('.main-quick-menu > ul > li').eq(i).addClass('ani'+num);
	});

	/* WEEKLY SPECIAL */ 
	$('.main-special-cont').each(function() {
		$(this).find('.prdList > li').addClass('swiper-slide');
		var swiper = new Swiper($(this).find('.swiper-container'), {	
		  spaceBetween: 0,	
		  slidesPerView: 3,
		  allowTouchMove: false,
		  breakpoints: {
			912: {		
			  spaceBetween: 10,		
			  slidesPerView: 2.5,
			  allowTouchMove: true,
			},
			640: {			
			  spaceBetween: 10,		
			  slidesPerView: 1.6,	
			  allowTouchMove: true,
			},
		    observer: true,
		    observeParents: true,
		  },	
		});	
	});


	/* 인기키워드 스크롤 */
	$('.prd-tab-slide2 .item_hl_tab_type .goods_tab_cont .goods_tab_box').each(function() { 
		$(this).find('> ul').addClass('swiper-wrapper');
		$(this).find('> ul > li').addClass('swiper-slide');
		$(this).append('<div class="swiper-scrollbar"></div>');

		var swiper = new Swiper($(this), {
		  spaceBetween: 30,		
		  slidesPerView: 3,
		  scrollbar: {
		    el: $(this).find('.swiper-scrollbar'),
		    hide: false,
		    draggable: true,
		  },
		  breakpoints: {
			912: {		
			  spaceBetween: 20,		
			  slidesPerView: 2,	
			},
			640: {		
			  spaceBetween: 10,		
			  slidesPerView: 2.5,	
			},
		  },	
		  observer: true,
		  observeParents: true,
		});		
	});	

	/* 브랜드 */
	$('.main-brand').each(function() {		
		var swiper = new Swiper($(this).find('.swiper-container'), {	
		  spaceBetween: 30,	
		  slidesPerView: 3,	
		  allowTouchMove: false,
		  breakpoints: {
			912: {		
			  spaceBetween: 24,		
			  slidesPerView: 2.4,		
			  allowTouchMove: true,
			},
			640: {		
			  spaceBetween: 10,		
			  slidesPerView: 1.6,		
			  allowTouchMove: true,
			},
		  },	
		});	
	}); 

});
