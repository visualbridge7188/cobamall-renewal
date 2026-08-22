$(document).ready(function(){
    /* 상단으로 */
	$('.top-btn').click(function(e){
		$('html, body').animate({ scrollTop : 0 }, 400);
		e.preventDefault();
	});
	/* 하단으로 */
	$('.down-btn').click(function(e){
		var position = $('#footer').position();
		$('html, body').animate({ scrollTop: position.top }, 600);		
		e.preventDefault();
	});

	/* 하단 고정 */
	var last_scrollTop = 0;
	$(window).scroll(function () {
		var tmp = $(this).scrollTop();

		if ( tmp > 100 ) {
			if (tmp > last_scrollTop) {
				$('.bottom-nav').addClass('active');
				$('.side-floating').removeClass('on');
			} else {
				$('.bottom-nav').removeClass('active');
				$('.side-floating').addClass('on');
			}
			last_scrollTop = tmp;
		}
	});

	/* 하단 : 공지사항 */
	if ( $('.vertical-slide').length > 0 ) {
		$(".footer-board").each(function(){
			var swiper = new Swiper('.footer-board .swiper-container', {
			  spaceBetween: 0,
			  slidesPerView: 1,
			  loop: true,
			  grabCursor: true,
			  autoplay: {
				  delay: 6000,
				  disableOnInteraction: false
			  },
			  direction: 'vertical',
			  wrapperClass: 'swiper-vertical-wrapper',
			  slideClass: 'vertical-slide',
			});
		});
	}

	/* 상단 카테고리 */
	$('.cate-main').each(function() {
		// 상단 카테고리 슬라이드 
		var mySwiper = new Swiper(this, {
		  spaceBetween: 0,
		  slidesPerView: 'auto',
		  loop: false,
		  grabCursor: true,
		  freeMode: true,
		  watchSlidesProgress: true,
		});

		// 현재 위치 on 표시 
		const currentUrl = new URL(window.location.href);
		const currentPath = currentUrl.pathname + currentUrl.search;

		$('.cate-main li').each(function() {
			const $a = $(this).find('a');
			const aUrl = new URL($a[0].href, location.origin);
			const aPath = aUrl.pathname + aUrl.search;

			if (currentPath === aPath) {
				$(this).addClass('on');
			}
		});
	});	

	/* 목록 - 중분류 슬라이드 */
	$('.goods_list .goods_list_category').each(function() {
		var mySwiper = new Swiper(this, {
		  spaceBetween: 8,
		  slidesPerView: 'auto',
		  loop: false,
		  grabCursor: true,
		  freeMode: true,
		  watchSlidesProgress: true,
		});
	});	

	/* 상세 - 구매안내 토글 */
	$('.detail_info_box .delivery-cont dt').click(function(){
		$(this).toggleClass('selected').next().slideToggle();
	});

});