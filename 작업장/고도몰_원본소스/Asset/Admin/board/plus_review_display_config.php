
<link type="text/css" href="<?=gd_set_browser_cache(PATH_ADMIN_GD_SHARE . 'ncds/css/board/plus-review-display-config.css')?>" rel="stylesheet"/>
<script defer src="<?=gd_set_browser_cache(PATH_ADMIN_GD_SHARE . 'ncds/script/common/switch.js')?>"></script>
<script defer src="<?=gd_set_browser_cache(PATH_ADMIN_GD_SHARE . 'ncds/script/common/ncds-jquery-validator.js')?>"></script>

<article class="ncua-content">
    <div class="plus_review_config">
        <form id="frm" action="/board/plus_review_ps.php" method="post" enctype="multipart/form-data" target="ifrmProcess">
            <input type="hidden" name="mode" value="save">
            <input type="hidden" name="type" value="view">

            <header class="page-header js-affix ncua-page-header">
                <h3><?php echo end($naviMenu->location); ?></h3>
                <div class="ncua-page-header__actions">
                    <button type="submit" class="ncua-btn ncua-btn--md ncua-btn--primary">저장</button>
                </div>
            </header>


            <!-- 상품상세 페이지 설정 -->
            <?php include $plusReviewGoodsDetailConfig ?>
        

            <!-- 메인 플러스리뷰 작성 팝업 설정 -->
            <?php include $plusReviewMainPopupConfig ?>

        </form>
    </div>
</article>


<script>
    $(document).ready(function () {
        // 미리보기 버튼 클릭 이벤트 처리
        $('.js-btn-preview-template').mouseover(function () {
            var targetClass = $(this).data('target');
            $('.' + targetClass).show();
        })

        $('.js-btn-preview-template').mouseout(function () {
            var targetClass = $(this).data('target');
            $('.' + targetClass).hide();
        })

       
        // 메인팝업노출시점 text 처리
        $('#authWriteStatusText').text($('input[name="popupStatus"]:checked').closest('label').find('.ncua-radio-field__text').text().replace(' 이후',''));

        $('input[name="popupStatus"]').change(function() {
            $('#authWriteStatusText').text($(this).closest('label').find('.ncua-radio-field__text').text().replace(' 이후',''));
        });  

        $("#frm").validate({
            invalidHandler: function(event, validator) {
                if (validator.errorList.length > 0) {
                    NCDSAlert({
                        message: validator.errorList[0].message,
                        iconType: 'error'
                    });
                }
            },
            submitHandler: function (form) {
                form.submit();
            },
            rules: {
                'goodsViewPageNum[front]': {
                    required: function () {
                        return $('[name=goodsPageReviewFl][value=y]').is(':checked');
                    },
                    min: 1
                },
                'goodsViewPageNum[mobile]': {
                    required: function () {
                        return $('[name=goodsPageReviewFl][value=y]').is(':checked');
                    },
                    min: 1
                }
            },
            messages: {
                'goodsViewPageNum[front]': {
                    required: '플러스리뷰 노출개수 설정(PC)를 입력해주세요.',
                    min: '플러스리뷰 노출 개수 설정의 게시물 수(PC)는 1이상 입력해주세요.'
                },
                'goodsViewPageNum[mobile]': {
                    required: '플러스리뷰 노출개수 설정(모바일)를 입력해주세요.',
                    min: '플러스리뷰 노출 개수 설정의 게시물 수(모바일)는 1이상 입력해주세요.'
                }
            }
        });
    })
</script>
<script type="text/javascript">
    const code = '251028001';
    if (window.GodoCosGuide) {
        window.GodoCosGuide.init({
            apiUrl: <?= json_encode($cosGuideApiUrl) ?>,
            guideCode: code,
        });
    }
</script>
