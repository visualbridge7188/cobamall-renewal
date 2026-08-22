function showFloating() {

	var $this = $(".side-floating"),
		scrollTop = $(window).scrollTop(),
		winH = $(window).height(),
		_bottom = $("#footer").offset().top - winH;

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

}
$(document).ready(function(){
	$(window).scroll(showFloating);
	showFloating();
});