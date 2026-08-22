function showFloating() {

	var $this = $(".side-floating"),
		scrollTop = $(window).scrollTop(),
		winH = $(window).height(),
		_bottom = $("footer").offset().top - winH;

	if ( $(".body-main").length ) {
		if ( scrollTop > 10 ) {
			$this.fadeIn();
		} else {
			$this.fadeOut();
		}
	}

	if ( scrollTop > _bottom ) { // 하단도달
		$this.addClass("end-stop");
	} else {
		$this.removeClass("end-stop");
	}

	/* 쇼핑카트탭이 보이면 그 위로 배치 */
	var $cart = $('#shop_cart_wrap');
	if ( $cart.length && $cart.is(':visible') ) {
		$this.css('bottom', $cart.outerHeight() + 15 + 'px');
	} else {
		$this.css('bottom', '');
	}

}
$(document).ready(function(){
	$(window).scroll(showFloating);
	showFloating();
});
