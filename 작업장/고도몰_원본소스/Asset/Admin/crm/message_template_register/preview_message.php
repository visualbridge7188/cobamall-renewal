<!-- INFO: 
 - hidden attribute로 visible 처리하시면 됩니다. 
 -->

 <section class="ncua-card">
    <header class="ncua-card__header">
        <h4 class="ncua-card__title">템플릿 미리보기</h4>
    </header>

    <section class="ncua-card__body">
        <div id="template-preview-container"></div>
    </section>
</section>

<script type="text/javascript">
    $(document).on('shown.bs.modal', '.modal', function() {
        const $slider = $('.js-mobile-preview');

        if (!$slider) return;
        
        // 이미 초기화되어 있으면 setPosition만 호출
        if ($slider.hasClass('slick-initialized')) {
            $slider.slick('setPosition');
        } else {
            // 아직 초기화되지 않았으면 Slick 초기화
            $slider.slick({
                // 슬라이더 옵션 설정
                dots: true,
                arrows: false,
                infinite: false,
                slidesToShow: 1,
                slidesToScroll: 1,
                variableWidth: true,
                // 필요한 옵션 추가
            });
        }
    });
</script>
