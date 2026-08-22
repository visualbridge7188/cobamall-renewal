<link type="text/css" href="<?=gd_set_browser_cache(PATH_ADMIN_GD_SHARE . 'ncds/css/board/template-list.css')?>" rel="stylesheet"/>


<article class="ncua-content">

    <header class="page-header ncua-page-header js-affix">
        <h3 class="ncua-help-manual"><?= end($naviMenu->location); ?>
            <small></small>
        </h3>
        <span class="ncua-page-header__actions">
            <button type="button" class="ncua-btn ncua-btn--md ncua-btn--primary js-btn-write">등록</button>
        </span>
    </header>
    <section class="ncua-card">
        <header class="ncua-card__header">
            <h4 class="ncua-card__title">게시글 양식 검색</h4>
        </header>
        
        <section class="ncua-card__body board-template-list">
            <!-- 검색 -->
            <?php include $templateListSearch ?>
            <!-- // 검색 -->
            <!-- 검색 결과 -->
             <?php include $templateListResult ?>
            <!-- // 검색 결과 -->
            <div class="ncua-pagination"><?= $pager->getPage(); ?></div>
        </section>

    </section>
</article>

<script type="text/javascript">
    $(document).ready(function () {
        const form = document.getElementById('frmSearch');
        const setSearchForm = () => {
            if (!form) return;

            const sortValue = document.querySelector('select[name=\'sort\']').value;
            const pageNumValue = document.querySelector('select[name=\'pageNum\']').value;

            const createHiddenInput = (name, value) => {
                const hiddenInput = document.createElement('input');
                hiddenInput.type = 'hidden';
                hiddenInput.name = name;
                hiddenInput.value = value;
                form.appendChild(hiddenInput);
            };

            if (sortValue) {
                createHiddenInput('sort', sortValue);
            }

            if (pageNumValue) {
                createHiddenInput('pageNum', pageNumValue);
            }

            $('#frmSearch').submit();
        };

        $('select[name=\'pageNum\']').change(setSearchForm);
        $('select[name=\'sort\']').change(setSearchForm);

        $(".js-btn-write").bind('click', function () {
            var $btn = $(this);
            
            // 버튼 비활성화 (중복 클릭 방지)
            if ($btn.prop('disabled')) {
                return;
            }
            $btn.prop('disabled', true);
            
            $.ajax({
                url: 'template_write.php',
                success: function (data) {
                    var layerForm = data;
                    ncds_layer_popup({
                        message: $(layerForm), 
                        title: '게시글 양식 등록', 
                        size: 'wide',
                        callback: function(dialog) {
                            // 레이어가 닫히면 버튼 다시 활성화
                            $btn.prop('disabled', false);
                        }
                    });
                },
                error: function() {
                    // 에러 발생 시에도 버튼 다시 활성화
                    $btn.prop('disabled', false);
                }
            });
        });

        $('.js-btn-modify').click(function () {
            var sno = $(this).data('sno');
            $.ajax({
                url: 'template_write.php',
                data: 'sno=' + sno,
                type: 'get',
                success: function (data) {
                    var layerForm = data;
                    ncds_layer_popup({message: $(layerForm), title: '게시글 양식 등록', size: 'wide'});
                }
            });
        });

        $("#frmList").validate({
            dialog: false,
            submitHandler: function (form) {
                NCDSConfirm({message: '선택한 게시글 양식을 삭제하시겠습니까?\n\r영구 삭제되어 복원 불가능합니다.',
                    callback: function (result) {
                    if (result) {
                        form.target = 'ifrmProcess';
                        form.submit();
                    }
                }});
            },
            rules: {
                "sno[]": 'required'
            },
            messages: {
                "sno[]": '선택된 게시글 양식이 없습니다.'
            }
        });

        // 삭제
        $('.js-btn-delete').click(function () {
            $(this).closest('tr').find('input[name="sno[]"]').prop('checked', true);
            $('.table-action button[type=submit]').trigger('click');
        });

        form.addEventListener('click', (e) => {
            const targetType = e.target.type;

            if (targetType !== 'reset') {
                return;
            }

            const url = window.location.pathname;

            window.location.href = url;
        });
    });
</script>
