function animateM($item){
	var scrollTop = $(window).scrollTop() + ($(window).height()/7*6);
	if (scrollTop > $item.offset().top) {
		$item.addClass('motion');
	} 
}
function showDiv(){
	var mainDivEl = $('.main-wrap > div');
	for (var i = 0; i < mainDivEl.length; i++) {
		animateM( mainDivEl.eq(i) );
	}
}
$(window).scroll(function () {
	showDiv(); 
});
$(document).ready(function(){
	showDiv();

	/* 메인 비주얼 */
	$(".main-visual").each(function() {
		$(".main-visual .slider-wrap > div").each(function(i) {
			var title = $(".slider-nav > li");
			$(".main-visual .slider-wrap > div").eq(i).append(title.eq(i).html());
		});
		// 인덱싱
		var num = function(str) {
			return parseInt(str);
		}
		var $mainSlide = $(this).find('.visual-slider');

		$(this).find('.current').text('1');
		$mainSlide
		.on('init', function(event, slick) {	
			$(this).find('.total').text(num(slick.slideCount));
		})
		.on('beforeChange', function(event, slick, currentSlide, nextSlide) {	
			$(this).find('.current').text(num(nextSlide + 1));
		}); 

		$mainSlide
		.on('init', function(event, slick) {

			/* 일시정지 & 재생 버튼 */
			$mainSlide.find(".slick-player-btn").click(function() {
				if ( $(this).hasClass('active') ) {
					$(this).removeClass('active');
					$mainSlide.find('.slider-wrap').slick('slickPlay');
				} else {
					$(this).addClass('active');
					$mainSlide.find('.slider-wrap').slick('slickPause');
				}
			});
		});
	});

	/* 메인 퀵 메뉴 */
	$('.main-quick-menu.swiper-container').each(function() {
		var mySwiper = new Swiper(this, {
		  spaceBetween:14,
		  slidesPerView: 'auto',
		  loop: false,
		  grabCursor: true,
		  freeMode: true,
		  watchSlidesProgress: true,
		});
	});	

	/* 유튜브 영상 */
    var $iframe = $('#myVideo');
    var videoId = $iframe.data('video-id'); 
    var embedUrl = 'https://www.youtube.com/embed/' + videoId + '?autoplay=1&mute=1&controls=0&showinfo=0&modestbranding=1&rel=0&loop=1&playlist=' + videoId;

    $iframe.attr('src', embedUrl);

	/* 후기 롤링 */
	$('.best-review .swiper-container').each(function() {
		var mySwiper = new Swiper(this, {
		  spaceBetween:8,
		  slidesPerView: 1.4,
		  loop: false,
		  grabCursor: true,
		  freeMode: true,
		  watchSlidesProgress: true,
		  slidesOffsetBefore: 18,
		  slidesOffsetAfter: 27,
		});
	});
	
	/* 매거진 */
	$('.main-magazine .swiper-container').each(function() {
		var swiper = new Swiper('.main-magazine .swiper-container', {		
		  spaceBetween: 20,		
		  slidesPerView: 1,
		  loop: true,	
	      allowTouchMove:false,
		  autoplay: {		
			  delay: 4000,	
			  disableOnInteraction: false	
		  },
		  navigation: {
			nextEl: '.main-magazine .swiper-button-next',
			prevEl: '.main-magazine .swiper-button-prev',
		  },
			pagination: {
				el: ".main-magazine .swiper-pagination",
				clickable: true,
			},
		});		
	});	

	/* 추천상품 */
	$('.new-prd').each(function() {
		// 첫번째 탭 상품
		var swiper = new Swiper('.new-prd .tab-contents', {
		  loop: false,
		  spaceBetween: 8,
		  slidesPerView: 2.2,
		  grabCursor: true,
		});
	});

});