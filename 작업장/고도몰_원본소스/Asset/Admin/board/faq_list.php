<link type="text/css" href="<?=gd_set_browser_cache(PATH_ADMIN_GD_SHARE . 'ncds/css/board/faq-list.css')?>" rel="stylesheet"/>


<article class="ncua-content faq-list">

    <header class="page-header ncua-page-header js-affix">
        <h3 class="ncua-help-manual"><?=end($naviMenu->location); ?></h3>
        <span class="ncua-page-header__actions">
            <button type="button" class="ncua-btn ncua-btn--md ncua-btn--primary js-btn-register">FAQ 등록</button>
        </span>
    </header>

    <section class="ncua-card">
        <header class="ncua-card__header">
            <h4 class="ncua-card__title">FAQ 검색</h4>
        </header>

        <section class="ncua-card__body">
            <!-- 검색 -->
            <?php include $faqListSearch ?>
            <!-- // 검색 -->


            <!-- 검색 결과 -->
            <?php include $faqListResult ?>
            <!-- // 검색 결과 -->
        </section>
        
    </section>

</article>


<script type="text/javascript">
    <!--
    $(document).ready(function () {
        $("#selectedAll").bind('click', function () {
            $("input[name='chk[]']").prop("checked", $("#selectedAll").prop("checked"));
        });

        $('input[name=mallSno]').bind('click',function(){
            var mallSno = $(this).val();
            location.href="faq_list.php?mallSno="+mallSno;
        })

        // 등록
        $('.js-btn-register').click(function () {
            location.href = 'faq_register.php?mallSno=<?=$search['mallSno']?>';
        });

        $("#frmList").validate({
            dialog: false,
            submitHandler: function (form) {
                form.target = 'ifrmProcess';
                NCDSConfirm({ 
                    message:'선택한 글을 삭제하시겠습니까?\n\r영구 삭제되어 복원 불가능합니다.', 
                    callback: function (result) {
                        if (result) {
                            form.submit();
                        }
                    }
                });
            },
            rules: {
                "chk[]": 'required'
            },
            messages: {
                "chk[]": '선택된 FAQ 글이 없습니다.'
            },
            invalidHandler: function(form, validator) {
                  if (validator.errorList.length > 0) {
                    NCDSAlert({message: validator.errorList[0].message.toString(), iconType: 'error' });
                }
            }
        });

        $('.js-faq-search-reset').click(function(e) {
            const url = window.location.pathname;
            window.location.href = url;
        });

         const datePicker = new ncua.DatePicker(document.querySelector('#datepicker-container'), {
            size: 'xs', 
            buttons: [
                {
                    text: '오늘',
                    period: 0,
                    unit: 'days',
                    isCurrent: false,
                },
                {
                    text: '7일',
                    period: 6,
                    unit: 'days',
                    isCurrent: true,
                },
                {
                    text: '15일',
                    period: 14,
                    unit: 'days',
                    isCurrent: false,
                },
                {
                    text: '1개월',
                    period: 29,
                    unit: 'days',
                    isCurrent: false,
                },
                {
                    text: '3개월',
                    period: 89,
                    unit: 'days',
                    isCurrent: false,
                },
                {
                    text: '1년',
                    period: 364,
                    unit: 'days',
                    isCurrent: false,
                },
            ],
            datePickerOptions: [
                {
                element: 'start-date',
                attrName: 'regDt[]',
                options: {
                    mode: 'single',
                    static: true,
                    dateFormat: 'Y-m-d',
                    clickOpens: true,
                    allowInvalidPreload: true,
                    allowInput: true,
                    locale: 'ko',
                },
                },
                {
                element: 'end-date',
                attrName: 'regDt[]',
                options: {
                    mode: 'single',
                    static: true,
                    dateFormat: 'Y-m-d',
                    clickOpens: true,
                    allowInvalidPreload: true,
                    allowInput: true,
                    locale: 'ko',
                },
                },
            ],
            });

        <?php if (!empty($search['regDt'][0]) && !empty($search['regDt'][1])) { ?>
            datePicker.setDate(["<?= htmlspecialchars($search['regDt'][0]) ?>", "<?= htmlspecialchars($search['regDt'][1]) ?>"]);
        <?php } ?>

    });
    //-->
</script>
<script type="text/javascript">
    const code = '251126002';
    if (window.GodoCosGuide) {
        window.GodoCosGuide.init({
            apiUrl: <?= json_encode($cosGuideApiUrl) ?>,
            guideCode: code,
        });
    }
</script>
