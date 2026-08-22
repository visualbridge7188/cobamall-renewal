<link type="text/css" href="<?=gd_set_browser_cache(PATH_ADMIN_GD_SHARE . 'ncds/css/ncds-editor/'. NCDS_EDITOR_VERSION .'/ncds-editor.css')?>" rel="stylesheet"/>
<link type="text/css" href="<?=gd_set_browser_cache(PATH_ADMIN_GD_SHARE . 'ncds/css/board/faq-register.css')?>" rel="stylesheet"/>
<script defer type="text/javascript" src="<?=gd_set_browser_cache(PATH_ADMIN_GD_SHARE . 'ncds/script/ncds-editor/'. NCDS_EDITOR_VERSION .'/ncds-editor.js')?>"></script>

<article class="ncua-content faq-register">
    <header class="page-header ncua-page-header js-affix">
		<h3 class="ncua-help-manual"><button type="button" class="ncua-btn ncua-btn--md only-icon ncua-btn--secondary-gray ncua-history-back js-btn-back">뒤로가기</button><?= end($naviMenu->location); ?></h3>
		<span class="ncua-page-header__actions">
            <a href="faq_list.php" class="ncua-btn ncua-btn--md ncua-btn--secondary-gray">목록</a>
            <button type="button" class="ncua-btn ncua-btn--md ncua-btn--primary js-btn-write"><?=$mode === 'modify' ? '수정' : '등록'?></button>
        </span>
	</header>

    <?php if ($gGlobal['isUse'] == 'y') { ?>
        <div class="swiper ncua-horizontal-tab ncua-horizontal-tab--md ncua-horizontal-tab--underline-fill">
            <div class="swiper-wrapper ncua-gap-8">
                <?php foreach ($gGlobal['useMallList'] as $key => $mall) { ?>
                    <div class="swiper-slide ncua-horizontal-tab__item">
                        <a href="faq_register.php?mallSno=<?=$mall['sno']; ?>" class="ncua-tab-button <?=$mallSno == $mall['sno'] ? 'is-active' : ''; ?>" data-html="true" data-content="<?=$mall['mallName']; ?>" data-placement="top">
                            <?=$mall['mallName']; ?>
                        </a>
                    </div>
                <?php } ?>
            </div>
        </div>
    <?php } ?>

    <section class="ncua-card">
        <header class="ncua-card__header">
            <h4 class="ncua-card__title"><?= end($naviMenu->location); ?></h4>
            <p class="ncua-card__sub-info"><strong>*는 필수 입력</strong> 항목입니다.</p>
        </header>


        <section class="ncua-card__body">
            <?php include $faqRegisterForm ?>
        </section>
    </section>
</article>

<script type="text/javascript">
<!--
$(document).ready(function(){
    $('.js-btn-write').click(function() {
        $('#frm').submit();
    });

    $("#frm").validate({
        submitHandler: function (form) {
            form.target = 'ifrmProcess';
            form.submit();
        },
        invalidHandler: function(event, validator) {
            if (validator.errorList.length > 0) {
                NCDSAlert({
                    message: validator.errorList[0].message,
                    iconType: 'error'
                });
            }
        },
        rules: {
            subject: {maxlength:[30], required:true},
            contents: {
                required: function (textarea) {
                    const editorcontent = textarea.value.replace(/<[^>]*>/gi, '').replace('&nbsp;', '');
                    return editorcontent.length === 0;
                }
            },
            answer: {
                required: function (textarea) {
                    const isEditMode = <?= !empty($data['sno']) ? 'true' : 'false' ?>;
                    if (isEditMode) {
                        return false; // 수정 모드에서는 필수가 아님
                    }

                    const editorcontent = textarea.value.replace(/<[^>]*>/gi, '').replace('&nbsp;', '');
                    return editorcontent.length === 0;
                }
            },
        },
        messages: {
            subject: {
                required: '제목을 입력해 주세요.',
                maxlength: '제목은 최대 30자까지 입력할 수 있습니다.'
            },
            contents: {
                required: '내용을 입력해 주세요.'
            },
            answer: {
                required: '답변을 입력해 주세요.'
            },
        }
    });
});

document.querySelector('.js-btn-back').addEventListener('click', function() {
    history.back();
});
//-->
</script>


