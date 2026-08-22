function showFloating() {

	var $this = $('.side-floating'),
		scrollTop = $(window).scrollTop(),
		winH = $(window).height(),
		_bottom = $('#footer').offset().top - winH;

	if (scrollTop > (winH / 2)) {
		$this.fadeIn('fast');
	} else {
		$this.fadeOut('fast');
	}

	if ( scrollTop > _bottom ) { // 하단도달
		$this.addClass('end-stop');
	} else {
		$this.removeClass('end-stop');
	}

}

$(document).ready(function(){
	$(window).scroll(showFloating);
	showFloating();
});