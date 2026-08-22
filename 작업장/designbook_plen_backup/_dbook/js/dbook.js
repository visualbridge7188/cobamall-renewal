var frmPath = location.pathname;
var frmSearch = location.search;
var frmPathSearch = location.pathname + location.search;

/* 상단 고정 */
function showHeader() {
	var headH = $("#header").height();
	if ($(window).scrollTop() > headH) {
		$("#header").addClass("fixed");
	} else {
		$("#header").removeClass("fixed");
	}
}
$(document).ready(function(){

	/* 상단 고정 */
	$(window).scroll(showHeader);
	showHeader();

	/* 회원 하위메뉴 드롭다운 */
	$("#header .top-account > ul > li").each(function(){
		$(this).mouseenter(function(){
			$(this).find(".mypage-sub").show();
		}).mouseleave(function(){
			$(this).find(".mypage-sub").hide();
		});
	});

    /* 상단으로 */
	$(".top-btn").click(function(e){
		$("html, body").animate({ scrollTop : 0 }, 600);
		e.preventDefault();
	});
	/* 하단으로 */
	$(".down-btn").click(function(e){
		var position = $("#footer").offset();
		$("html, body").animate({ scrollTop: position.top }, 600);		
		e.preventDefault();
	});

	/* 검색 창 : 열림 & 닫힘 */
	$('.top-account .search > a').click(function(e){
		if ( $('.search-layer').css('display') == 'none' ) {
			$('.search-layer').slideDown();
			$('.header-wrap').addClass('on');				
			$('#layerDim').show();
		} else {
			$('.search-layer').slideUp({ complete : function(){
				$('.header-wrap').removeClass('on');				
			}});
			$('#layerDim').hide();
		}
		e.preventDefault();
	});
	$('.search-layer .search-close-btn, #layerDim').click(function(e){
		$('.search-layer').slideUp({ complete : function(){
			$('.header-wrap').removeClass('on');	
			$('#layerDim').hide();			
		}});
		e.preventDefault();
	});

	/* 상품 목록 */
	if ( $('.body-goods-list').length )	{

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