<link rel="stylesheet" href="<?=gd_set_browser_cache(PATH_ADMIN_GD_SHARE . 'ncds/css/share/layer-excel.css')?>">
<script defer src="<?=gd_set_browser_cache(PATH_ADMIN_GD_SHARE . 'ncds/script/common/switch.js')?>"></script>
<script defer src="<?=gd_set_browser_cache(PATH_ADMIN_GD_SHARE . 'ncds/script/common/charcount.js')?>"></script>
<script defer src="<?=gd_set_browser_cache(PATH_ADMIN_GD_SHARE . 'ncds/script/common/password-input.js')?>"></script>
<div class="modal-dialog__content layer-excel ncua-content">
    <form id="frmExcelForm" name="frmExcelForm" action="../share/layer_excel_ps.php" method="post">
        <input type="hidden" name="mode" value="excel" >
        <input type="hidden" name="searchCount" value="<?=$searchCount?>" >
        <input type="hidden" name="totalCount" value="<?=$totalCount?>" >
        <input type="hidden" name="layerExcelToken" value="<?=$layerExcelToken?>" >
        <input type="hidden" name="menu" value="<?=$menu?>" >
        <input type="hidden" name="whereDetail" value="" >

        <div id="whereDetail" style="display:none"></div>

        <!-- <div class="table-title gd-help-manual">
            <?php if($menu == 'adminLog') { ?> 엑셀 다운로드 설정 <?php } else { ?> 다운로드 양식 검색 <?php } ?>
        </div> -->
        <div class="ncua-table ncua-table--vertical">
            <table>
                <colgroup>
                    <col width="144px"/>
                    <col/>
                </colgroup>
                <?php if ($menu == 'adminLog') { ?>
                    <input type="hidden" name="whereFl" value="search">
                    <input type="hidden" name="menu" value="<?= $menu ?>">
                    <input type="hidden" name="location" value="<?= $location ?>">
                    <input type="hidden" name="formSno" value="<?= $formList[0]['sno'] ?>">
                <?php } else { ?>
                    <tr>
                        <th><div>다운로드 범위</div></th>
                        <td>
                            <div>
                                <div class="radio-group ncua-flex-gap">
                                    <?php if($menu != 'promotion'){ ?>
                                        <label class="ncua-radio-field ncua-radio-field--xs has-text">
                                            <span class="ncua-radio-input ncua-radio-input--xs ncua-radio-field__input">
                                                <input type="radio" name="whereFl" value="select" />
                                            </span>
                                            <span>
                                                <span class="ncua-radio-field__text">선택내역</span>
                                            </span>
                                        </label>
                                    <?php } ?>
                                    <label class="ncua-radio-field ncua-radio-field--xs has-text">
                                        <span class="ncua-radio-input ncua-radio-input--xs ncua-radio-field__input">
                                            <input type="radio" name="whereFl" value="search" />
                                        </span>
                                        <span>
                                            <span class="ncua-radio-field__text">검색내역</span>
                                        </span>
                                    </label>
                                    <?php if($menu !='order') { ?>
                                        <label class="ncua-radio-field ncua-radio-field--xs has-text">
                                            <span class="ncua-radio-input ncua-radio-input--xs ncua-radio-field__input">
                                                <input type="radio" name="whereFl" value="total" />
                                            </span>
                                            <span>
                                                <span class="ncua-radio-field__text">전체내역</span>
                                            </span>
                                        </label>
                                    <?php } ?>
                                </div>
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <th><div>양식 선택</div></th>
                        <td>
                            <div>
                                <div class="select-group ncua-gap-8">
                                    <span class="ncua-select ncua-select--xs">
                                        <span class="ncua-select__content">
                                            <select name="menu" class="ncua-select__tag" disabled="disabled">
                                                <option value="">선택</option>
                                                <?php foreach ($menuList as $k => $v) { ?>
                                                    <option value="<?= $k ?>" <?php if ($menu == $k) {
                                                        echo "selected='selected'";
                                                    } ?>><?= $v ?></option>
                                                <?php } ?>
                                            </select>
                                        </span>
                                    </span>

                                    <span class="ncua-select ncua-select--xs">
                                        <span class="ncua-select__content">
                                            <select name="location" class="ncua-select__tag" <?php if ($menu == 'board' || $menu == 'plusreview') { ?>onchange="select_form(this.value)" <?php } else { ?>disabled="disabled"<?php } ?>>
                                                <option value="">상세 항목 선택</option>
                                                <?php foreach ($locationList as $k => $v) { ?>
                                                    <option value="<?= $k ?>" <?php if ($location == $k) {
                                                        echo "selected='selected'";
                                                    } ?>><?= $v ?></option>
                                                <?php } ?>
                                            </select>
                                        </span>
                                    </span>

                                    <span class="ncua-select ncua-select--xs">
                                        <span class="ncua-select__content">
                                            <select name="formSno" class="ncua-select__tag">
                                                <option value="">양식 선택</option>
                                                <?php if ($menu != 'board' && $menu != 'plusreview') {
                                                    foreach ($formList as $k => $v) { ?>
                                                        <option value="<?= $v['sno'] ?>" data-count-personal-field="<?= $v['countPersonalField'] ?>"><?= $v['title'] ?></option>
                                                    <?php }
                                                } ?>
                                            </select>
                                        </span>
                                    </span>
                                    <?php if($menu !='promotion' ) { ?><a href="../../../policy/excel_form_register.php" class='ncua-btn ncua-btn--xs ncua-btn--secondary-gray' target="_blank">다운로드 양식 추가</a><?php } ?>
                                </div>
                                <?php if($menu =='goods' && ($location =='goods_list' || $location=='goods_list_delete')) {?>
                                    <label class="ncua-checkbox-field ncua-checkbox-field--xs has-text">
                                        <span class="ncua-checkbox-input ncua-checkbox-input--xs ncua-checkbox-field__input">
                                            <input type="checkbox" name="goodsNameTagFl" value="y"/>
                                        </span>
                                        <span>
                                            <span class="ncua-checkbox-field__text">상품명 HTML태그 제외</span>
                                        </span>
                                    </label>
                                <?php } ?>
                            </div>
                        </td>
                    </tr>
                <?php } ?>
                <tr>
                    <th><div>파일명</div></th>
                    <td>
                        <div>
                            <div class="ncua-input ncua-input--xs ncua-input-full-width">
                                <div class="ncua-input__content">
                                    <div class="ncua-input__field ncua-input__field--xs">
                                        <input name="downloadFileName" type="text" data-charcount-key="fileName" class="js-type-korea ncua-file__name" maxlength="50" />
                                    </div>
                                    <div class="ncua-input__field-text-count" data-charcount-text="fileName">
                                        <output class="ncua-input__field-text-count-current">0</output>
                                        <span>/50</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </td>
                </tr>
                <tr>
                    <th><div>비밀번호 사용여부</div></th>
                    <td>
                        <div class="password-use-wrap">
                            <div class="ncua-switch ncua-switch--xs">
                                <label class="ncua-switch__option ncua-switch__option--left ncua-switch__option--<?= $checked['passwordFl']['y'] ? 'active' : 'inactive' ?>">
                                    <input class="ncua-switch__radio ncua-switch__radio--left" type="radio" name="passwordFl" value="y" <?= $checked['passwordFl']['y'] ?> />
                                    <span class="ncua-switch__label">사용</span>
                                </label>
                                <label class="ncua-switch__option ncua-switch__option--right ncua-switch__option--<?= $checked['passwordFl']['n'] ? 'active' : 'inactive' ?>">
                                    <input class="ncua-switch__radio ncua-switch__radio--right" type="radio" name="passwordFl" value="n" <?= $checked['passwordFl']['n'] ?> />
                                    <span class="ncua-switch__label">사용안함</span>
                                </label>
                            </div>
                            <ul class="notice-list">
                                <li class="notice-required-password display-none caution">다운로드 항목 중 개인정보가 3개 이상 포함되는 경우, 비밀번호 설정이 필수입니다.</li>
                            </ul>
                        </div>
                    </td>
                </tr>
                <tr>
                    <th>
                        <div>엑셀파일 당<br/>데이터 최대개수</div>
                    </th>
                    <td>
                        <div class="excel-file-count-wrap">
                            <div class="ncua-input ncua-input--xs ncua-text-input-unit">
                                <div class="ncua-input__content">
                                    <div class="ncua-input__field ncua-input__field--xs ncua-input-width-80">
                                        <input name="excelPageNum" type="text" value="10000" />
                                    </div>
                                </div>
                                <div class="ncua-input-text">개</div>
                            </div>
                            <ul class="notice-list">
                                <li class="caution">다운로드 시간이 오래 걸리는 경우 데이터 최대개수 숫자를 조정하세요</li>
                            </ul>
                        </div>
                    </td>
                </tr>
                <tr id="js-excel-form-password">
                    <th><div data-tooltip-seq="001">비밀번호 설정</div></th>
                    <td>
                        <div>
                            <div class="ncua-input ncua-input--xs ncua-input-password ncua-input-width-320">
                                <div class="ncua-input__content">
                                    <div class="ncua-input__field ncua-input__field--xs">
                                        <div class="ncua-input__icon-wrap">
                                            <img class="ncua-input__left-icon" src="<?= PATH_ADMIN_GD_SHARE ?>ncds/image/password-lock-01.svg" width="14" height="14" />
                                        </div>
                                        <input type="password" name="password" data-charcount-key="passwordSetting" class="ncua-password__setting" maxlength="16" placeholder="영문/숫자/특수문자 2개 포함, 10~16자" />
                                        <button type="button" class="ncua-input__icon-wrap ncua-input__right-icon ncua-input__password-icon">
                                            <img src="<?= PATH_ADMIN_GD_SHARE ?>ncds/image/password-eye-off.svg" width="14" height="14" />
                                        </button>
                                    </div>
                                    <div class="ncua-input__field-text-count" data-charcount-text="passwordSetting">
                                        <span class="ncua-input__field-text-count-current">0</span>
                                        <span>/16</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </td>
                </tr>
                <tr id="js-excel-form-repassword">
                    <th><div>비밀번호 확인</div></th>
                    <td>
                        <div>
                            <div class="ncua-input ncua-input--xs ncua-input-password ncua-input-width-320">
                                <div class="ncua-input__content">
                                    <div class="ncua-input__field ncua-input__field--xs">
                                        <div class="ncua-input__icon-wrap">
                                            <img class="ncua-input__left-icon" src="<?= PATH_ADMIN_GD_SHARE ?>ncds/image/password-lock-01.svg" width="14" height="14" />
                                        </div>
                                        <input type="password" name="rePassword" data-charcount-key="passwordConfirm" class="ncua-password__confirm" maxlength="16" />
                                        <button type="button" class="ncua-input__icon-wrap ncua-input__right-icon ncua-input__password-icon">
                                            <img src="<?= PATH_ADMIN_GD_SHARE ?>ncds/image/password-eye-off.svg" width="14" height="14" />
                                        </button>
                                    </div>
                                    <div class="ncua-input__field-text-count" data-charcount-text="passwordConfirm">
                                        <span class="ncua-input__field-text-count-current">0</span>
                                        <span>/16</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </td>
                </tr>
            </table>
        </div>
        <div class="table-bottom-wrap">
            <ul class="notice-list">
                <?php if ($menu != 'goods') { ?>
                    <li class="caution">
                        개인정보를 개인용PC에 저장할 시 암호화가 의무이므로 비밀번호 '사용'을 권장합니다.
                        <button type="button" class="js-excel-guide">[자세히보기]</button>
                    </li>
                <?php } ?>
                <?php if ($menu == 'member') { ?>
                    <li class="caution">
                        개인정보 보호를 위해 '다운로드 보안 설정'을 사용하시길 권장합니다.
                        <a href="/policy/manage_security.php" target="_blank" class="btn-link">운영 보안 설정></a>
                    </li>
                <?php } ?>
            </ul>

            <button type="submit" class="ncua-btn ncua-btn--xs ncua-btn--primary">요청</button>
        </div>
    </form>
    <div>
        <form id="frmExcelRequest" name="frmExcelRequest" action="../share/layer_excel_ps.php" method="post" target="ifrmProcess">
            <input type="hidden"  name="mode" value="download">
            <input type="hidden"  name="excelFileName" value="<?=$excelFileName?>">
            <input type="hidden"  name="sno" value="">
            <input type="hidden"  name="excelKey" value="">
            <input type="hidden"  name="excelTitle" value="">
            <input type="hidden"  name="excelDownloadReason" value="">
            <div class="ncua-table ncua-table--horizontal">
                <table id="tblExcelRequest">
                    <colgroup>
                        <col width="7%" />
                        <col width="15%"/>
                        <col />
                        <col width="80px" />
                        <col width="80px" />
                        <col width="120px"/>
                        <col width="120px"/>
                        <col width="120px"/>
                    </colgroup>
                    <thead>
                        <tr>
                            <th><div class="ncua-align-center">번호</div></th>
                            <?php if ($menu == 'adminLog') { ?>
                                <th><div class="ncua-align-center">파일명</div></th>
                            <?php } else { ?>
                                <th><div class="ncua-align-center">다운로드 양식명</div></th>
                                <th><div class="ncua-align-center">파일명</div></th>
                            <?php } ?>
                            <th><div class="ncua-align-center">파일구분</div></th>
                            <th><div class="ncua-align-center">파일상태</div></th>
                            <th><div class="ncua-align-center">요청자</div></th>
                            <th><div class="ncua-align-center">다운로드 기간</div></th>
                            <th><div class="ncua-align-center">다운로드</div></th>
                        </tr>
                    </thead>
                    <tbody>
                    </tbody>
                </table>
            </div>
        </form>
    </div>
</div>
<script type="text/javascript">
    <?php if (empty($tooltipData) === false) {?>
    $(document).ready(function () {
        // 툴팁 데이터
        var tooltipData = <?php echo $tooltipData;?>;
        var sectionEle = null;
        $('#frmExcelForm .table.table-cols th').each(function(idx){
            if ($(this).closest('table').siblings('.table-title').length) {
                sectionEle = $(this).closest('table').prevAll('.table-title:first');
            } else if ($(this).closest('table').parent('div').siblings('.table-title').length) {
                sectionEle = $(this).closest('table').parent('div').prevAll('.table-title:first');
            } else {
                sectionEle = $(this).closest('table').parent('div').parent('div').prevAll('.table-title:first');
            }
            if (typeof sectionEle[0] !== "undefined") {
                var sectionTitle = $(sectionEle[0]).html().replace(/\(?<\/?[^*]+>/gi, '').trim().replace(/ /gi, '').replace(/\n/gi, '');
                var titleName = $(this).text().trim().replace(/ /gi, '').replace(/\n/gi, '');
                for (var i in tooltipData) {
                    if (tooltipData[i].title == sectionTitle) {
                        if (tooltipData[i].attribute == titleName) {
                            $(this).append('<button type="button" onclick="tooltip(this)" class="btn btn-xs js-layer-tooltip" title="' + tooltipData[i].content + '" data-placement="right" data-width="' + tooltipData[i].cntWidth + '"><span title="" class="icon-tooltip"></span></button>');
                        }
                    }
                }
            }
        });
        $(document).on('click', '.tooltip.in .tooltip-close', function () {
            $('.js-layer-tooltip[aria-describedby=' + $(this).parent().attr('id') + ']').trigger('click');
        });
        $('button.close').click(function(){
            $('.tooltip.in .tooltip-close').trigger('click');
        });
    });

    function tooltip(e) {
        if ($(e).attr('aria-describedby')) {
            $(e).tooltip('destroy');
        } else {
            var option = {
                trigger: 'click',
                container: '#content',
                html: true,
                template: '<div class="tooltip" role="tooltip"><div class="tooltip-arrow"></div><div class="tooltip-inner"></div><button class="tooltip-close">close</button></div>',
            };
            $(e).on('shown.bs.tooltip', function () {
                $(".tooltip.in").css({
                    width: 270,
                    maxWidth: "none",
                });
            });
            $(e).tooltip(option).tooltip('show');
        }
    }
    <?php }?>
</script>
<!-- //@formatter:on -->
<script type="text/javascript">
    const PROGRESS_CIRCLE_RADIUS = 45;
    const PROGRESS_CIRCLE_CIRCUMFERENCE = 2 * Math.PI * PROGRESS_CIRCLE_RADIUS;

    const progressExcelTemplate = `
    <div class="ncua-progress-container-dimmed js-progress-excel" style="display: none;"></div>
    <div class="ncua-progress-container js-progress-excel" style="display: none;">
        <p>데이터 생성 중입니다. 잠시만 기다려주세요.</p>
        <div class="ncua-progress-circle ncua-progress-circle-xxs ncua-progress-circle-circle">
            <div class="ncua-progress-circle__content">
                <svg class="ncua-progress-circle__svg" viewBox="0 0 100 100">
                    <circle class="ncua-progress-circle__background" cx="50" cy="50" r="45" stroke-width="10" fill="none"></circle>
                    <circle
                        class="ncua-progress-circle__progress"
                        cx="50"
                        cy="50"
                        r="45"
                        stroke-width="10"
                        fill="none"
                        stroke-dasharray="${PROGRESS_CIRCLE_CIRCUMFERENCE}"
                        stroke-dashoffset="${PROGRESS_CIRCLE_CIRCUMFERENCE}"
                        transform="rotate(-90 50 50)"
                        stroke-linecap="round">
                    </circle>
                </svg>
                <div class="ncua-progress-circle__label-container">
                    <div id="progressView" class="ncua-progress-circle__label">0%</div>
                </div>
            </div>
        </div>
        <button type="button" class="ncua-btn ncua-btn--xs ncua-btn--secondary-gray" onclick="cancelProgressExcel()">요청 취소</button>
    </div>
    `;

    $(document).ready(function () {
        $('select[name=formSno]').change(function (e) {
            var $pass = $(':radio[name=passwordFl]'), $notice = $('.notice-required-password');
            $pass[1].disabled = false;
            $notice.addClass('display-none');
            if ($(e.target).find(':selected').data('count-personal-field') > 2) {
                $pass[0].checked = true;
                $pass.eq(0).closest('label').removeClass('ncua-switch__option--inactive').addClass('ncua-switch__option--active');
                $pass.eq(1).closest('label').removeClass('ncua-switch__option--active').addClass('ncua-switch__option--inactive');
                $pass[1].disabled = true;
                $notice.removeClass('display-none');
            }
            $pass.filter(':checked').trigger('click');
        });
        $(".modal-content").append(progressExcelTemplate);

        $("#frmExcelForm").validate({
            dialog: false,
            invalidHandler: function(event, validator) {
                if (validator.errorList.length > 0) {
                    NCDSAlert({
                        message: validator.errorList[0].message,
                        iconType: 'error'
                    });
                }
            },
            submitHandler: function (form) {

                var checkPersonalField = true;
                $.ajax({
                    url: '../share/layer_excel_ps.php',
                    type: 'post',
                    async: false,
                    data: {'mode': 'countPersonalField', 'formSno': $("select[name='formSno']").val()},
                    success: function (cnt) {
                        if(cnt > 2 && $("input[name='passwordFl']:checked").val() == 'n') {
                            NCDSAlert({message: "비밀번호를 입력해주세요.", iconType: 'error'});
                            var $pass = $(':radio[name=passwordFl]'), $notice = $('.notice-required-password');
                            $pass[0].checked = true;
                            $pass[1].disabled = true;
                            $notice.removeClass('display-none');
                            $pass.filter(':checked').trigger('click');
                            checkPersonalField = false;
                        }
                    }
                });
                if(!checkPersonalField) {
                    return false;
                }

                var whereDetail = $('#<?=$targetForm?>').serialize();

                if ($("input[name='whereFl']:checked").val() == 'select') {

                    if ($("#<?=$targetListForm?> input[name*='<?=$targetListSno?>']:checked").length) {
                        whereDetail += "&" + $("#<?=$targetListForm?> input[name*='<?=$targetListSno?>']").serialize();
                    } else {
                        NCDSAlert({message: "리스트에서 다운받을 데이터를 먼저 선택해주세요.", iconType: 'error'});
                        return false;
                    }
                }


                if ($("input[name='whereFl']:checked").val() == 'search' && $("input[name='searchCount']").val() == 0) {
                    NCDSAlert({message: "검색된 데이터가 없습니다.", iconType: 'error'});
                    return false;
                }

                <?php if($orderStateMode) { ?>
                whereDetail += "&statusMode=<?=$orderStateMode?>";
                <?php } ?>

                <?php if($menu == 'board') { ?>
                <?php if (gd_is_provider()) { ?>
                whereDetail += "&bdId=" + $("input[name='bdId']").val();
                <?php } else { ?>
                whereDetail += "&bdId=" + $("select[name='bdId']").val();
                <?php } ?>
                <?php } ?>

                if ($("input[name='downloadFileName']").val().trim() == '') {
                    <?php if($menu == 'adminLog') { ?>
                    $("input[name='downloadFileName']").val('<?= gd_code_item('06004001') ?>');
                    <?php } else { ?>
                    $("input[name='downloadFileName']").val($("select[name='formSno'] option:selected").text());
                    <?php } ?>
                }

                $("input[name='whereDetail']").val(whereDetail);

                if ($("input[name='excelPageNum']").val().trim() == '' || parseInt($("input[name='excelPageNum']").val().trim()) == '0') {
                    NCDSConfirm({message: '엑셀파일당 데이터 최대개수 제한이 없어 대용량 다운로드 시 시간이 오래 걸릴 수 있습니다. 다운로드 요청을 진행하시겠습니까?', callback: function (result) {
                        if (result) {
                            form.target = 'ifrmProcess';
                            form.submit();
                            $(".js-progress-excel").show();
                        } else {
                            return false;
                        }

                    }, });
                } else {
                    form.target = 'ifrmProcess';
                    form.submit();
                    $(".js-progress-excel").show();
                    return false;
                }
            },
            // onclick: false, // <-- add this option
            rules: {
                location: {
                    required: true
                },
                formSno: {
                    required: true
                },
                password: {
                    required: function () {
                        var required = false;
                        if ($("input[name='passwordFl']:checked").val() == 'y') {
                            required = true;
                        }
                        return required;
                    },
                    minlength: 10,
                    maxlength: 16,
                    equalTo: "input[name=rePassword]"
                }
            },
            messages: {
                location: {
                    required: "상세 항목을 선택해주세요."
                },
                formSno: {
                    required: "다운로드 양식을 선택해주세요."
                },
                password: {
                    required: "비밀번호를 입력해주세요",
                    minlength: '비밀번호는 영문대문자/영문소문자/숫자/특수문자 중 2가지 이상 조합, 10~16자리 이하로 설정할 수 있습니다.',
                    maxlength: '비밀번호는 영문대문자/영문소문자/숫자/특수문자 중 2가지 이상 조합, 10~16자리 이하로 설정할 수 있습니다.',
                    equalTo: "동일한 비밀번호를 입력해주세요."
                }
            }
        });


        set_excel_list();

        $('input[name="passwordFl"]').click(function (e) {
            if ($(this).val() == 'y') {
                $("#js-excel-form-password").show();
                $("#js-excel-form-repassword").show();
            } else {
                $("#js-excel-form-password").hide();
                $("#js-excel-form-repassword").hide();
                $('input[name="password"]').val('');
                $('input[name="rePassword"]').val('');
            }
        });

        $("input.js-type-korea").bind('keyup', function () { //익스 11 한글 초중성분리 그래서 test후 replace
            var tmp = $(this).val();
            var pattern = /[^a-zA-Zㄱ-ㅎㅏ-ㅣ가-힣\u119E\u11A20-9!@#$%^_{}~,.]/;
            if (pattern.test(tmp)) {
                $(this).val(tmp.replace(/[^a-zA-Zㄱ-ㅎㅏ-ㅣ가-힣\u119E\u11A20-9!@#$%^_{}~,.]/g, ''));
            }
        });

        //영어랑숫자만입력
        $("input.js-type-normal").bind('keyup', function () {
            $(this).val($(this).val().replace(/[^a-z0-9!@#$%^_{}~,.]*/gi, ''));
        });


        $("input[name='excelPageNum']").bind('keyup', function () {
            $(this).val($(this).val().replace(/[^0-9]*/gi, ''));
        });


        $('input[maxlength]').maxlength({
            showOnReady: true,
            alwaysShow: true
        });

        if ($("#<?=$targetListForm?> input[name*='<?=$targetListSno?>']:checked").length) {
            $("input[name='whereFl']").eq(0).prop("checked", true);
        } else {
            // 쿠폰발급내역 및 페이커쿠폰발급내역 다운로드 범위 디폴트 설정
            if("<?=$menu;?>" == 'promotion'){
                $("input[name='whereFl']").eq(0).prop("checked", true);
            }else{
                $("input[name='whereFl']").eq(1).prop("checked", true);
            }

        }

        // maxlength의 경우 display none으로 되어있으면 정상작동 하지 않는다 따라서 페이지 로딩 후 maxlength가 적용된 후 display none으로 강제 처리 (임시방편 처리)
        setTimeout(function () {
            $('#frmExcelForm').find('input[maxlength]').next('span.bootstrap-maxlength').css({top: '1px', left: '255px'});
        }, 1000);

        // 엑셀 다운로드 가이드 팝업
        $('.js-excel-guide').on('click', function () {
            BootstrapDialog.show({
                title: '관련 법적 고지 안내',
                message: '<span class="text-blue bold">방송통신위원회고시 \'개인정보의 기술적·관리적 보호조치 기준\'</span><br/><br/><span class="bold">제4조(접근통제)</span><br/>⑧ 정보통신서비스 제공자등은 개인정보취급자를 대상으로 다음 각 호의 사항을 포함하는 비밀번호 작성규칙을 수립하고, 이를 적용․운용하여야 한다.<br/>1. 영문, 숫자, 특수문자 중 2종류 이상을 조합하여 최소 10자리 이상 또는 3종류 이상을 조합하여 최소 8자리 이상의 길이로 구성<br/><br/><span class="bold">제6조(개인정보의 암호화)</span><br/>④ 정보통신서비스 제공자등은 이용자의 개인정보를 컴퓨터, 모바일 기기 및 보조저장매체 등에 저장할 때에는 이를 암호화해야 한다.',
                buttons: [{
                    label: '확인', cssClass: 'ncua-btn ncua-btn--sm ncua-btn--secondary-gray', hotkey: 13, size: BootstrapDialog.SIZE_LARGE,
                    action: function (dialog) {
                        dialog.close();
                    }
                }],
                cssClass: 'excel-download-guide'
            });
        });
    });

    function set_excel_list(msg) {
        if (msg) {
            NCDSAlert({message: msg, iconType: 'error'});
        }

        //필수정보 세팅
        var menu = '<?=$menu?>';
        var reasonUseFl = '<?=$reasonUseFl;?>';
        var layerExcelToken = '<?=$layerExcelToken?>';
        $.post('../share/layer_excel_ps.php', {'mode': 'searchList', 'menu': menu, 'location': '<?=$location?>', 'layerExcelToken': layerExcelToken}, function (data) {

            var getData = $.parseJSON(data);
            var addHtml = "";

            // CSRF 토큰 체크
            if (getData === '잘못된 접근입니다.') {
                NCDSAlert({message: "잘못된 접근입니다.", iconType: 'error'});
                $('div.bootstrap-dialog-close-button').click();
                return false;
            }

            if (getData != '') {
                $("#tblExcelRequest tbody").css("height", "200px");
                $("#tblExcelRequest tbody").css("overflow-y", "auto");
                $("#tblExcelRequest tbody").css("overflow-x", "hidden");

                var pageNum = 1;

                $.each(getData, function (key, val) {

                    if (val.state == 'n') var cnt = 1;
                    else var cnt = val.fileName.split('<?=STR_DIVISION?>').length;

                    for (var i = 0; i < cnt; i++) {
                        addHtml += "<tr>";
                        addHtml += "<td><div class='ncua-align-center'>" + pageNum + "</div></td>";
                        if (menu == 'adminLog') {
                            addHtml += "<td><div class='ncua-align-center'>" + val.downloadFileName + "</div></td>";
                        } else {
                            addHtml += "<td><div class='ncua-align-center'>" + val.title + "</div></td>";
                            addHtml += "<td><div class='ncua-align-center'>" + val.downloadFileName + "</div></td>";
                        }
                        addHtml += "<td><div class='ncua-align-center'>" + (i + 1) + "/" + cnt + "</div></td>";
                        if (val.state = 'y') {
                            addHtml += "<td><div class='ncua-align-center'>생성완료</div></td>";
                            if (val.managerNm === null) {
                                addHtml += "<td><div class='ncua-align-center'>고도CS담당</div></td>";
                            } else {
                                addHtml += "<td><div class='ncua-align-center'>" + val.managerNm + "(" + val.managerId + ")</div></td>";
                            }
                            addHtml += "<td><div class='ncua-align-center'>~" + val.expiryDate + "</div></td>";
                            addHtml += "<td><div class='ncua-align-center'><button type='button' class='ncua-btn ncua-btn--xxs ncua-btn--secondary js-excel-request-download' data-sno='" + val.sno + "' data-key='" + i + "' data-title='" + val.title +"'><svg xmlns=\"http://www.w3.org/2000/svg\" width=\"12\" height=\"12\" viewBox=\"0 0 12 12\" fill=\"none\"><path d=\"M10.5 7.5V8.1C10.5 8.94008 10.5 9.36012 10.3365 9.68099C10.1927 9.96323 9.96323 10.1927 9.68099 10.3365C9.36012 10.5 8.94008 10.5 8.1 10.5H3.9C3.05992 10.5 2.63988 10.5 2.31901 10.3365C2.03677 10.1927 1.8073 9.96323 1.66349 9.68099C1.5 9.36012 1.5 8.94008 1.5 8.1V7.5M8.5 5L6 7.5M6 7.5L3.5 5M6 7.5V1.5\" stroke=\"white\" stroke-linecap=\"round\" stroke-linejoin=\"round\"/></svg>다운로드</button></div></td>";
                        } else {
                            addHtml += "<td><div>생성중</div></td>";
                            addHtml += "<td><div>" + val.managerNm + "(" + val.managerId + ")</div></td>";
                            addHtml += "<td><div></div></td>";
                            addHtml += "<td><div class='ncua-align-center'><button type='button' class='ncua-btn ncua-btn--xxs ncua-btn--secondary js-excel-request-download'><svg xmlns=\"http://www.w3.org/2000/svg\" width=\"12\" height=\"12\" viewBox=\"0 0 12 12\" fill=\"none\"><path d=\"M10.5 7.5V8.1C10.5 8.94008 10.5 9.36012 10.3365 9.68099C10.1927 9.96323 9.96323 10.1927 9.68099 10.3365C9.36012 10.5 8.94008 10.5 8.1 10.5H3.9C3.05992 10.5 2.63988 10.5 2.31901 10.3365C2.03677 10.1927 1.8073 9.96323 1.66349 9.68099C1.5 9.36012 1.5 8.94008 1.5 8.1V7.5M8.5 5L6 7.5M6 7.5L3.5 5M6 7.5V1.5\" stroke=\"white\" stroke-linecap=\"round\" stroke-linejoin=\"round\"/></svg>다운로드</button></div></td>";
                        }
                        addHtml += '</tr>';
                        pageNum++;
                    }
                });
            } else {
                $("#tblExcelRequest tbody").css("height", "");
                $("#tblExcelRequest tbody").css("overflow-y", "");
                $("#tblExcelRequest tbody").css("overflow-x", "");

                const colspanCount = (menu == 'adminLog') ? '6' : '7';
                addHtml += "<tr><td colspan='" + colspanCount + "' class='no-data'><div>다운로드할 엑셀 양식을 선택 후 요청 버튼을 눌러주세요.</div></td></tr>";
            }

            $("#tblExcelRequest tbody").html(addHtml);
            if (pageNum > 5) {
                if (menu == 'adminLog') {
                    $("#tblExcelRequest thead tr th:last").show().text('');
                } else {
                    $("#tblExcelRequest thead tr th:last").show();
                }
            }

            $('.js-excel-request-download').on('click', function () {

                if (reasonUseFl === 'y') {
                    // 다운로드 보안 설정 확인
                    var authUseFl = false;
                    $.ajax({
                        url: '../share/layer_excel_ps.php',
                        type: 'post',
                        async: false,
                        data: {'mode':'checkAuthUseFl', 'sno': $(this).data('sno')},
                        success: function (useFl) {
                            if (useFl == 'true') {
                                authUseFl = true
                            }
                        }
                    });
                }

                if ($(this).data('sno')) {
                    if (authUseFl || reasonUseFl !== 'y') {
                        $("input[name='sno']").val($(this).data('sno'));
                        $("input[name='excelKey']").val($(this).data('key'));
                        $("input[name='excelTitle']").val($(this).data('title'));

                        $("#frmExcelRequest").submit();
                    } else {
                        var complied = _.template($('#downloadReason').html());
                        var message = complied();
                        var target = $(this);
                        BootstrapDialog.show({
                            title: '엑셀 다운로드 사유',
                            cssClass: 'download-reason',
                            size: BootstrapDialog.SIZE_WIDE,
                            message: message,
                            buttons: [{
                                label: '확인',
                                cssClass: 'btn-black',
                                hotkey: 32,
                                size: BootstrapDialog.SIZE_LARGE,
                                action: function (dialog) {
                                    if ($('#excelDownloadReason').val() == '') {
                                        $('#reasonError').removeClass('display-none');
                                        return false;
                                    }
                                    dialog.close();
                                    $("input[name='sno']").val(target.data('sno'));
                                    $("input[name='excelKey']").val(target.data('key'));
                                    $("input[name='excelTitle']").val(target.data('title'));
                                    $("input[name='excelDownloadReason']").val($('#excelDownloadReason').val());
                                    $("#frmExcelRequest").submit();
                                }
                            }]
                        });
                    }
                    return false;
                } else {
                    NCDSAlert({message: "파일이 생성되지 않았습니다.", iconType: 'error'});
                    return false;
                }

            });


            $(".js-progress-excel").hide();
            $("#progressView").text("0%");
            $("#progressViewBg").css("width", "0%");
        });

    }

    function select_form(location) {

        $.post('../share/layer_excel_ps.php', {'mode': 'search_form', 'menu': '<?=$menu?>', 'location': location}, function (data) {

            var formList = $.parseJSON(data);
            var addHtml = "<option value>선택</option>";
            if (formList) {
                $.each(formList, function (key, val) {
                    addHtml += "<option value='" + val.sno + "' data-count-personal-field='" + val.countPersonalField + "'>" + val.title + "</option>";
                });
            }
            $('select[name="formSno"]').html(addHtml);

        });

    }

    function progressExcel(size) {

        if ($.isNumeric(size) == false) {
            size = "100";
        }

        $(".ncua-progress-circle__label").text(size + "%");

        const progressPercentage = parseFloat(size) || 0;
        const strokeDashoffset = PROGRESS_CIRCLE_CIRCUMFERENCE * (1 - progressPercentage / 100);

        const progressCircle = $('.ncua-progress-circle__progress');

        if (progressCircle.length > 0) {
            progressCircle.attr('stroke-dasharray', PROGRESS_CIRCLE_CIRCUMFERENCE);
            progressCircle.attr('stroke-dashoffset', strokeDashoffset);
        }
    }

    function cancelProgressExcel() {

        NCDSConfirm({message: '요청 취소 시 생성 중인 엑셀 다운로드 파일이 모두 삭제됩니다.<br/>진행중인 내용을 취소하고 페이지를 이동하시겠습니까?', callback: function (result) {
            if (result) {

                if ($.browser.msie) {
                    document.execCommand("Stop");
                } else {
                    window.stop(); //works in all browsers but IE
                }

                setTimeout(function () {
                    $(".js-progress-excel").hide();
                    $(".ncua-progress-circle__label").text("0%");

                    const progressCircle = $('.ncua-progress-circle__progress');

                    if (progressCircle.length > 0) {
                        progressCircle.attr('stroke-dasharray', PROGRESS_CIRCLE_CIRCUMFERENCE);
                        progressCircle.attr('stroke-dashoffset', PROGRESS_CIRCLE_CIRCUMFERENCE); // 0% 상태
                    }
                }, 10);

            } else {
                return false;
            }

        }});

    }

    function hide_process() {
        $(".js-progress-excel").hide();
    }

    function open_excel_auth() {
        var params = {
            mode: 'download',
            url: '../share/layer_excel_ps.php',
            data: $("#frmExcelRequest").serializeArray(),
            managerId: '<?=$managerId?>'
        };
        $.get('../share/layer_excel_auth.php', params, function (data) {
            console.log('layer_excel_auth', data);
            BootstrapDialog.show({
                title: '엑셀 다운로드 보안 인증',
                message: $(data),
                closable: false,
                onshow: function (dialog) {
                    var $modal = dialog.$modal;
                    BootstrapDialog.currentId = $modal.attr('id');
                }
            });
        });
    }
</script>
<script type="text/html" id="downloadReason">
    <div class="ncua-table ncua-table--vertical">
        <table>
            <colgroup>
                <col width="144px">
                <col>
            </colgroup>
            <tbody>
            <tr>
                <th><div>사유 선택</div></th>
                <td>
                    <div>
                        <span class="ncua-select ncua-select--xs">
                            <span class="ncua-select__content">
                                <?= gd_select_box('excelDownloadReason', 'excelDownloadReason', $reasonList, null, null, '=사유 선택=', null, 'ncua-select__tag'); ?>
                            </span>
                        </span>
                        <div id="reasonError" class="text-red display-none">사유 선택은 필수입니다.</div>
                    </div>
                </td>
            </tr>
            </tbody>
        </table>
    </div>
    <ul class="notice-list">
        <li>개인정보의 안전성 확보조치 기준(고시)에 의거하여 개인정보를 다운로드한 경우 사유 확인이 필요합니다.</li>
        <li>엑셀 다운로드 사유는 설정 > 기본정책 > 코드 관리에서 추가 가능합니다.</li>
    </ul>
</script>
<script type="text/javascript">
    // ncds-modal 클래스 자동 제거 로직
    // #addSearchForm이 DOM에서 제거될 때만 실행
    const modalObserver = new MutationObserver((mutations) => {
        mutations.forEach((mutation) => {
            // childList가 아니거나 제거된 노드가 없으면 skip
            if (mutation.type !== 'childList' || mutation.removedNodes.length === 0) return;

            mutation.removedNodes.forEach((node) => {
                if (node.nodeType !== 1) return;
                
                const isAddSearchForm = node.id === 'addSearchForm' || node.querySelector('#addSearchForm');
                if (!isAddSearchForm) return;

                const modal = document.querySelector('.bootstrap-dialog.ncds-modal');
                const hasAlertContent = modal.querySelector('.bootstrap-dialog-message');
                    if (!hasAlertContent) return;
                modal.classList.remove('ncds-modal');
                modalObserver.disconnect();
            });
        });
    });

    modalObserver.observe(document.body, {
        childList: true,
        subtree: true
    });

    const charCountManager = createCharCountManager({
        targetClasses: ['ncua-file__name', 'ncua-password__setting', 'ncua-password__confirm'],
    });
    charCountManager.init();

    const passwordInputManager = createPasswordInputManager();
    passwordInputManager.init();
</script>
<script defer type="text/javascript">
    const code = 251112003;
    if (window.GodoCosGuide) {
        window.GodoCosGuide.init({
            apiUrl: <?= json_encode($cosGuideApiUrl) ?>,
            guideCode: code,
        });
    }
</script>
