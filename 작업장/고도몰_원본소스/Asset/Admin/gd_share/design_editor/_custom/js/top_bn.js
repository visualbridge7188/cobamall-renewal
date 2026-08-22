$(document).ready(function(){
	/* 상단 배너 */
	var topBnH = $('.top-bn').find('img').height();
	function topBanner(){
		if ($('#topLine').css('height')=='0px') {
			$('#topLine').stop(true,true).animate({height:topBnH});
		}
	}
	//topBanner();

	$('#topLine .top-btn-close').click(function() {
		$('#topLine').stop(true,true).animate({height:0});
	});
});