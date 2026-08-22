<link type="text/css" href="<?=gd_set_browser_cache(PATH_ADMIN_GD_SHARE . 'ncds/css/board/board-theme.css')?>" rel="stylesheet"/>

<article class="ncua-content board-theme">
    <header class="page-header ncua-page-header js-affix">
        <h3 class="ncua-help-manual"><?= end($naviMenu->location); ?>
            <small></small>
        </h3>
        <span class="ncua-page-header__actions">
            <a href="./board_theme_register.php" class="ncua-btn ncua-btn--md ncua-btn--primary js-btn-write">게시판 스킨 등록</a>
        </span>
    </header>
    <section class="ncua-card">
        <header class="ncua-card__header">
            <h4 class="ncua-card__title">게시판 스킨 검색</h4>
        </header>
        <section class="ncua-card__body">

            <?php include $boardThemeSearch ?>
            <?php include $boardThemeResult ?>
        </section>
    </section>
</article>
<script>
    $(document).ready(function () {
        $('input[name=deviceType]').bind('click',function(event,isLoad){
            if(isLoad !== true) {
                changeApplySkinList();
                $('select[name=liveSkin]').find("option:eq(0)").prop("selected", true);
            }
        })

        $('input[name=domainFl]').bind('click',function(event,isLoad){
            if(isLoad !== true) {
                changeApplySkinList();
            }
        });

        const changeApplySkinList = function(){
            var domainFl = $('input[name=domainFl]:checked').val();
            var deviceType = $('input[name=deviceType]:checked').val();
            if (domainFl != '') {
                $.ajax({
                    method: 'post',
                    url: 'board_theme_ps.php',
                    data: {'mode': 'getApplySkinList', 'domainFl' : domainFl , 'deviceType' : deviceType },
                    dataType: 'json'
                }).success(function (data) {
                    $('select[name=liveSkin').empty().append($('<option>', {value: '', text: '=디자인 스킨 검색='}));
                    for (var i = 0; i < data.list.length; i++) {
                        $('select[name=liveSkin').append($('<option>', {value: data.list[i].skinValue, text: data.list[i].skinTitle}));
                    }
                }).error(function (e) {
                    console.log(e);
                    NCDSAlert({message: e});
                });
            }
        }

        $('input[name=deviceType]:checked').trigger('click',[true]);

        $('#frmList').validate({
            dialog: false,
            submitHandler: function (form) {
                form.target = 'ifrmProcess';

                NCDSConfirm({message: '선택된 스킨을 삭제하시겠습니까?\n\r영구 삭제되어 복원 불가능합니다.',
                    callback: function (result) {
                    if (result) {
                        form.submit();
                    }
                }});
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
                'sno[]': {
                    required: true
                }
            },
            messages: {
                'sno[]': {
                    required: '선택한 스킨이 없습니다.'
                },

            },
        });

        $('.js-row-delete').bind('click', function () {
            $(this).closest('tr').find('input[name="sno[]"]').prop('checked', true);
            $('#frmList').submit();
        })

        const form = document.querySelector('.js-form-enter-submit');

        form.addEventListener('click', (e) => {
            const targetType = e.target.type;

            if (targetType !== 'reset') {
                return;
            }

            const url = window.location.pathname;
            window.location.href = url;
        });

    })
</script>
