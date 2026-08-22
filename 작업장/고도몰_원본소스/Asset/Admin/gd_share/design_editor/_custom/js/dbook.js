var frmPath = location.pathname;
var frmSearch = location.search;
var frmPathSearch = location.pathname + location.search;

/* 상단 고정 */
function showHeader() {
	var headH = $('header').height();
	if ($(window).scrollTop() > headH) {
		$('header').addClass('fixed');
	} else {
		$('header').removeClass('fixed');
	}
}

function sizeListener() {

	/* sns 위치 */
	// var footerMenuH = $('#footer .footer-menu').height();
	// document.documentElement.style.setProperty('--footer-menu-height', footerMenuH + 'px');
	// document.documentElement.style.setProperty('--footer-menu-height2', -footerMenuH + 'px');

	/* 탑배너 높이 */
	if ( $('header #topLine.mo-view .top-bn img').length )	{
		var topImgH = $('header #topLine.mo-view').height();
	} else {
		var topImgH = 0;
	}
	document.documentElement.style.setProperty('--top-line-height', topImgH + 'px');
	document.documentElement.style.setProperty('--top-line-height2', -topImgH + 'px');

	/* class 변경 */
	var bodyW = window.innerWidth;
	var deviceW = 768;
	if (bodyW <= deviceW) {
		$('body').removeClass('pc').addClass('mobile');
	} else if (bodyW >= deviceW) {
		$('body').removeClass('mobile').addClass('pc');
		$('.gnb_allmenu_wrap,.btn_all_menu_open').removeClass('on');
		$('.layerDim').addClass('dn');
		$('body').css({'overflow':''});
	}
	if ($('body').hasClass('.mobile')) {
		// 모바일 
	}

	/* 검색 레이어 */
	if (window.matchMedia('(max-width: 912px)').matches) {
		$('.search-layer').addClass('off');
	} else {
		$('.search-layer').removeClass('off');
	}

}

$(window).resize(function(){
	sizeListener();	
});
$(document).ready(function(){
	sizeListener();

	/* 상단 고정 */
	$(window).scroll(showHeader);
	showHeader();

	/* 회원 하위메뉴 드롭다운 */
	$('header .top-account > ul > li').each(function(){
		$(this).mouseenter(function(){
			$(this).find('.mypage-sub').show();
		}).mouseleave(function(){
			$(this).find('.mypage-sub').hide();
		});
	});

    /* 상단으로 */
	$('.top-btn').click(function(e){
		$('html, body').animate({ scrollTop : 0 }, 600);
		e.preventDefault();
	});
	/* 하단으로 */
	$('.down-btn').click(function(e){
		var position = $('footer').offset();
		$('html, body').animate({ scrollTop: position.top }, 600);		
		e.preventDefault();
	});

	/* 검색 창 : 열림 & 닫힘 */
	$('.top-account .search > button').click(function(e){
		$('.search-layer').addClass('on');
		$('.header-wrap').addClass('on');
		$('body').css('overflow','hidden');
		e.preventDefault();
	});
	$('.search-layer .search-close-btn').click(function(e){
		$('.search-layer').removeClass('on');
		$('.header-wrap').removeClass('on');	
		$('body').css('overflow','');
		e.preventDefault();
	});

	/* 상품상세 탭 앵커 이동 — 고정 헤더 높이 보정 */
	$('.item_goods_tab a[href^="#"]').on('click', function(e) {
		var target = $(this.getAttribute('href'));
		if (target.length) {
			e.preventDefault();
			var headerH = 0;
			if ($('header .nde-o-section-header--fixed').length) {
				headerH = $('header .nde-o-section-header').outerHeight() || 0;
			}
			$('html, body').animate({ scrollTop: target.offset().top - headerH + 20 }, 400);
		}
	});

	/* 상품 목록 */
	if ( $('.body-goods-list, .body-goods-search, .body-goods-main, .body-time-sale').length )	{

		// 상품 정렬
		$('.sort-select em').click(function(){			
			$(this).toggleClass('on');
			$(this).next().slideToggle(200);
		});
		if(frmSearch.indexOf('sort=') > -1){
			setTimeout(function(){
				$('.sort-select em').text($('.sort-select label.on').text());
			}, 1);

		}
	}
});
