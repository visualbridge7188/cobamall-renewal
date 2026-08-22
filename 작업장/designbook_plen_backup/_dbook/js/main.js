/* 메인 애니메이션 */
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
	$(".main-visual .slider-wrap > div").each(function(i) {
		var title = $(".slider-nav > li");
		$(".main-visual .slider-wrap > div").eq(i).append(title.eq(i).html());
	});

	/* 매거진 모션 */
	$('.main-magazine ul > li ').each(function(i){

		var num = 6;

		for (j=0;j<=i;j++) {
			num += 2;
		}
		$('.main-magazine ul > li').eq(i).addClass('ani'+num);
	});

	/* 유튜브 영상 */
    var $iframe = $('#myVideo');
    var videoId = $iframe.data('video-id'); 
    var embedUrl = 'https://www.youtube.com/embed/' + videoId + '?autoplay=1&mute=1&controls=0&showinfo=0&modestbranding=1&rel=0&loop=1&playlist=' + videoId;

    $iframe.attr('src', embedUrl);

});