<?php
use Component\Storage\Storage;

$boardPath = UserFilePath::data('board');

?>
<link rel="stylesheet" href="<?= PATH_ADMIN_GD_SHARE ?>ncds/css/board/board-register.css">
<link type="text/css" href="<?=gd_set_browser_cache(PATH_ADMIN_GD_SHARE . 'ncds/css/ncds-editor/'. NCDS_EDITOR_VERSION .'/ncds-editor.css')?>" rel="stylesheet"/>
<script defer type="text/javascript" src="<?=gd_set_browser_cache(PATH_ADMIN_GD_SHARE . 'ncds/script/ncds-editor/'. NCDS_EDITOR_VERSION .'/ncds-editor.js')?>"></script>
<script defer src="<?=gd_set_browser_cache(PATH_ADMIN_GD_SHARE . 'ncds/script/common/switch.js')?>"></script>
<script defer src="<?=gd_set_browser_cache(PATH_ADMIN_GD_SHARE . 'ncds/script/common/select-member-combo-box.js')?>"></script>
<script defer src="<?=gd_set_browser_cache(PATH_ADMIN_GD_SHARE . 'ncds/script/common/ncds-jquery-validator.js')?>"></script>
<article class="ncua-content board-register-wrapper">
    <form id="frmBoard" action="board_ps.php" method="post" enctype="multipart/form-data">
        <input type="hidden" name="mode" id="mode" value="<?= gd_isset($mode) ?>"/>
        <input type="hidden" name="sno" id="sno" value="<?= gd_isset($data['sno']) ?>"/>
        <?php if ($mode == 'modify') { ?>
            <input type="hidden" name="bdId" id="bdId" value="<?= gd_isset($data['bdId']) ?>"/>
        <?php } ?>

        <header class="page-header ncua-page-header js-affix">
            <h3 class="ncua-help-manual">
                <?php if ($mode == 'modify') { ?>
                <button type="button" class="ncua-btn ncua-btn--md only-icon ncua-btn--secondary-gray ncua-history-back js-btn-back">뒤로가기</button>
                <?php } ?>
                <?= end($naviMenu->location); ?>
            </h3>
            <span class="ncua-page-header__actions">
                <button type="submit" class="ncua-btn ncua-btn--md ncua-btn--primary js-btn-write"><?= gd_isset($modeTxt, '등록') ?></button>
            </span>
        </header>

        <?php include $defaultSetting; ?>
        <?php include $functionSetting; ?>
        <?php include $spamSetting; ?>
        <?php include $listSetting; ?>
        <?php include $writerSetting; ?>
        <?php include $contentSetting; ?>
        <?php include $decorationSetting; ?>
        <?php include $seoSetting; ?>

    </form>
</article>
<script type="text/javascript">

    // IP 노출 스위치 이벤트
    $(document).on('change', '.js-bdIpFl-switch input[type="radio"]', function() {
        const isEnabled = $(this).val() === 'y' && $(this).is(':checked');
        $('#bdIpFilterFl').prop('disabled', !isEnabled);

        // 비활성화 시 체크 해제
        if (!isEnabled) {
            $('#bdIpFilterFl').prop('checked', false);
        }
    });

    // 초기 상태 설정
    $(document).ready(function() {
        const bdIpFlChecked = $('.js-bdIpFl-switch input[name="bdIpFl"]:checked');
        if (bdIpFlChecked.length > 0) {
            const isEnabled = bdIpFlChecked.val() === 'y';
            $('#bdIpFilterFl').prop('disabled', !isEnabled);
        }
    });

</script>

<script type="text/javascript">
    // 테마별 리스트형태
    var themes = <?=json_encode(gd_isset($themes, array()));?>;

    // 테마수정으로 이동
    function addSkin() {
        window.open("board_theme_register.php");
    }

    function layer_register(parentLayerFormID, dataInputNm, dataFormID) {
        var addParam = {
            "parentFormID": parentLayerFormID,
            "dataInputNm": dataInputNm,
            "dataFormID": dataFormID,
        };

        if (dataInputNm == 'bdAuthReplyGroup') {
            if ($('input[name=bdReplyFl]:checked').val() == 'n') {
                return;
            }
        }
        else if (dataInputNm == 'bdAuthMemoGroup') {
            if ($('input[name=bdMemoFl]:checked').val() == 'n') {
                return;
            }
        }
        else if (dataInputNm == 'bdAuthWriteGroup') {
            if ($('input[name=bdAuthWrite][value=group]').is(':disabled')) {
                return;
            }
        }

        layer_add_info('member_group', addParam);
    }

    /**
     * 구매 상품 범위 등록 / 예외 등록 Ajax layer
     *
     * @param string typeStr 타입
     * @param string modeStr 예외 여부
     */
    function layer_except_register(typeStr, modeStr, isDisabled) {
        var layerFormID = 'addPresentForm';

        typeStrId = typeStr.substr(0, 1).toUpperCase() + typeStr.substr(1);

        if (typeof modeStr == 'undefined') {
            var parentFormID = 'present' + typeStrId;
            var dataFormID = 'id' + typeStrId;
            var dataInputNm = 'present' + typeStrId;
            var layerTitle = '조건 - ';
        } else {
            var parentFormID = 'except' + typeStrId;
            var dataFormID = 'idExcept' + typeStrId;
            var dataInputNm = 'except' + typeStrId;
            var layerTitle = '예외 조건 - ';
        }

        // 레이어 창
        if (typeStr == 'goods') {
            var layerTitle = layerTitle + '상품';
            var mode = 'simple';

            $("#" + parentFormID + "Table thead").show();
            $("#" + parentFormID + "Table tfoot").show();
        }
        if (typeStr == 'category') {
            var layerTitle = layerTitle + '카테고리';
            var mode = 'simple';

            $("#" + parentFormID + "Table thead").show();
            $("#" + parentFormID + "Table tfoot").show();
        }
        if (typeStr == 'brand') {
            var layerTitle = layerTitle + '브랜드';
            var mode = 'simple';

            $("#" + parentFormID + "Table thead").show();
            $("#" + parentFormID + "Table tfoot").show();
        }
        if (typeStr == 'scm') {
            var layerTitle = '공급사';
            var dataInputNm = typeStr + "No";
            var parentFormID = typeStr + 'Layer';
            var dataFormID = 'info_scm_';

            $('input:radio[name=scmFl]:input[value=y]').prop("checked", true);

            var mode = 'radio';
        }

        if (typeStr == 'member_group') {
            var mode = 'search';
            var layerTitle = '회원등급 선택';
            var dataInputNm = "memberGroupNo";
            var parentFormID = "member_groupLayer";
            var dataFormID = "info_member_group";
        }

        var addParam = {
            "mode": mode,
            "layerFormID": layerFormID,
            "parentFormID": parentFormID,
            "dataFormID": dataFormID,
            "dataInputNm": dataInputNm,
            "layerTitle": layerTitle,
        };

        if (typeStr == 'goods') {
            addParam['scmFl'] = $('input[name="scmFl"]:checked').val();
            addParam['scmNo'] = $('input[name="scmNo"]').val();
        }


        if (!_.isUndefined(isDisabled) && isDisabled == true) {
            addParam.disabled = 'disabled';
        }

        layer_add_info(typeStr, addParam);
    }

    const templateBdAllowDomain = `
    <li class="ncua-gap-8 ncua-flex">
        <div class="ncua-input ncua-input--xs ncua-input-width-320">
            <div class="ncua-input__content">
                <div class="ncua-input__field ncua-input__field--xs">
                    <input type="text" name="bdAllowDomain[]" placeholder="youtube.com" />
                </div>
            </div>
        </div>
        <div class="ncua-input ncua-input--xs ncua-input-width-320">
            <div class="ncua-input__content">
                <div class="ncua-input__field ncua-input__field--xs">
                    <input type="text" name="bdAllowDomain[]" placeholder="naver.com" />
                </div>
            </div>
        </div>
        <div class="ncua-remove-icon-button js-allow-domain-minus"></div>
    </li>
    `;

    $(document).ready(function () {
        var mode = $('#frmBoard>input[name=mode]').val();
        var bdId = $('#frmBoard>input[name=bdId]').val();
        var arrOnlyGoodsBdId = [];
        <?php foreach ($data['onlyGoodsBdId'] as $val) {?>
        arrOnlyGoodsBdId.push('<?=$val?>');
        <?php }?>

        $('[name=bdReviewAuthWrite]').bind('click',function(){
            $('[name=bdGoodsType][value=order]').prop('disabled',false);
            $('[name=bdGoodsType][value=goods]').prop('disabled',false);
            $('[name=bdReviewPeriodFl]').prop('disabled',false);
            if($(this).val() == 'all') {
                $('[name=bdGoodsType][value=goods]').prop('checked',true);
                $('[name=bdGoodsType][value=order]').prop('disabled',true);
                $('[name=bdReviewPeriodFl]').prop('disabled',true);
            }
            else {
                $('[name=bdGoodsType][value=order]').prop('checked',true);
                $('[name=bdGoodsType][value=goods]').prop('disabled',true);
            }
        })

        function list_image_target_notice_toggle() {
            var is_show = true;
            $('label[class*="js-bdListImageTarget-"]').each(function (idx, item) {
                if (item.style.display != 'none') {
                    is_show = false;
                    return false;
                }
            });
            $('.js-bdListImageTarget-notice').hide();
            if (is_show) {
                $('.js-bdListImageTarget-notice').show();
            }
        }

        $('input[name=bdUploadFl]').bind('click', function () {
            var $target = $('.js-bdListImageTarget-upload');
            $target.hide();
            if ($(this).val() == 'y') {
                $target.show();
            } else {
                $target.find(':radio').prop('checked', false);
            }
            list_image_target_notice_toggle();
        });

        $('input[name=bdEditorFl]').bind('click', function () {
            var $target = $('.js-bdListImageTarget-editor');
            $target.hide();
            if ($(this).val() == 'y') {
                $target.show();
            } else {
                $target.find(':radio').prop('checked', false);
            }
            list_image_target_notice_toggle();
        });

        $('input[name=bdGoodsFl]').bind('click', function () {
            $('input[name=bdGoodsType]').prop('disabled', $(this).val() == 'n');    //상품연동 사용안함이면 상품연동타입 비활성화
            $('input[name=bdGoodsTypeOrderDuplication]').prop('disabled', $(this).val() == 'n');
            var $target = $('.js-bdListImageTarget-goods');
            $target.hide();
            if ($(this).val() == 'y') {
                $target.show();
                $('input[name=bdGoodsType]:checked').trigger('click');
            } else {
                $target.find(':radio').prop('checked', false);
                // 상품 연동 사용안함일 때 주문내역 중복 허용 숨김
                $('.js-bdGoodsTypeOrderDuplication').hide();
            }
            list_image_target_notice_toggle();
        });



        // 말머리 기능 > 말머리 입력 > 말머리 명에서 엔터키 입력 시
        $(document).on('keypress', '.js-add-field-category', function (e) {
            if(e.which == 13) {
                add_category();
            }
        })

        $('input[name=bdGoodsType]').bind('click', function () {
            $('.js-bdGoodsTypeOrderDuplication').hide();
            // 상품 연동이 사용일 때만 주문내역 중복 허용 표시
            if ($(this).val() == 'order' && $('input[name=bdGoodsFl]:checked').val() == 'y') {
                $('.js-bdGoodsTypeOrderDuplication').show();
            }
        })
        $('input[name=bdGoodsType]:checked').trigger('click')
        $('input[name=bdGoodsFl]:checked').trigger('click');
        $('[name=bdReviewAuthWrite]:checked').trigger('click');

        if (arrOnlyGoodsBdId.indexOf(bdId) != -1) {
            // 상품 게시판이 아닌 경우: switch 전체 disabled, "사용"에 체크
            $('input[name=bdGoodsFl][value="y"]').prop('checked', true);
            $('input[name=bdGoodsFl][value="y"]').closest('label').addClass('ncua-switch__option--active').removeClass('ncua-switch__option--inactive');
            $('input[name=bdGoodsFl][value="n"]').closest('label').addClass('ncua-switch__option--inactive').removeClass('ncua-switch__option--active');
            $('input[name=bdGoodsFl]').prop('disabled', true);
            $('input[name=bdGoodsFl]').closest('.ncua-switch').addClass('ncua-switch--disabled');
//            $('input[name=bdGoodsType][value=order]').prop('disabled', true);
            $('input[name=bdGoodsType][value=bdGoodsTypeOrderDuplication]').prop('disabled', true);
        }

        $('input[name=bdAttachImageDisplayFl]').bind('click', function () {
            if ($(this).val() == 'y') {
                $('.bdAttachImageRow').show();
            }
            else {
                $('.bdAttachImageRow').hide();
            }
        })
        $('input[name=bdMemoFl]').bind('click', function () {
            if ($(this).val() == 'y') {
                $('.bdSecretReply').show();
                $('.bdSecretReplyTitle').show();
            }
            else {
                $('.bdSecretReply').hide();
                $('.bdSecretReplyTitle').hide();
            }
        })

        $('input[name=bdAttachImageDisplayFl]:checked').trigger('click');

        // 삭제 버튼 활성/비활성 상태 업데이트 함수
        const updateDomainDeleteButtons = () => {
            const $deleteButtons = $('#domain-allow-box .js-allow-domain-minus');
            const liCount = $('#domain-allow-box>li').length;
            $deleteButtons.prop('disabled', liCount <= 1);
        };

        $('.js-allow-domain-add').bind('click', function () {
            if ($('#domain-allow-box>li').length == 10) {
                NCDSAlert({message: '허용도메인은 최대 20개까지 등록가능합니다.', iconType: 'error'});
                return;
            }
            var $lastLi = $('#domain-allow-box>li:last');
            if ($lastLi.length > 0) {
                $lastLi.after(templateBdAllowDomain);
            } else {
                $('#domain-allow-box').append(templateBdAllowDomain);
            }
            // 추가 후 삭제 버튼 상태 업데이트
            updateDomainDeleteButtons();
        })

        $('body').on('click', '.js-allow-domain-minus', function () {
            $(this).closest('li').remove();
            // 삭제 후 삭제 버튼 상태 업데이트
            updateDomainDeleteButtons();
        })

        // 초기 로드 시 삭제 버튼 상태 확인
        updateDomainDeleteButtons();


        $('input[name=bdReplyFl]').bind('click', function () {
            $('input[name=bdAuthReply]').prop('disabled', $(this).val() == 'n');

            // 답변기능 사용안함일때
            if($(this).val() == 'n'){
                $(':checkbox[name="bdAnswerStatusFl"]').closest('label').addClass('display-none');
                $('.js-answer-status').addClass('display-none');
                $("input:checkbox[name='bdAnswerStatusFl']").prop("checked", false);
                $('.bdReplyFl-area').addClass('display-none');
                $('.bdReplyMileageFl-area').addClass('display-none');
                $('input:radio[name=bdReplyDelFl][value=applicable]').prop('checked',true);
                $("input:checkbox[name='bdReplyMileageFl']").prop("checked", false);
            }else if($(this).val() == 'y'){
                var checkedKind = $("input:radio[name=bdKind]:checked").val();

                if(checkedKind == 'default' || checkedKind == 'gallery') {
                    $(':checkbox[name="bdAnswerStatusFl"]').closest('label').removeClass('display-none');
                    $('.js-answer-status').removeClass('display-none');
                    $('.bdReplyFl-area').removeClass('display-none');
                    $('.bdReplyMileageFl-area').removeClass('display-none');
                }
            }
        })
        $('input[name=bdReplyFl]:checked').trigger('click');


        $('input[name=bdMemoFl]').bind('click', function () {
            $('input[name=bdAuthMemo]').prop('disabled', $(this).val() == 'n');
        })
        $('input[name=bdMemoFl]:checked').trigger('click');

        $('input[type=radio][name^=bdAuth]').bind('click', function () {
            if ($(this).val() != 'group') {
                $(this).closest('td').find('.selected-btn-group').html('');
            }
        })


        $('.js-group-select').bind('click', function () {
            $(this).closest('td').find('input[type="radio"][value="group"]').trigger('click');
        })

        //게시판유형
        var oldCheckedKind = $("input:radio[name=bdKind]:checked").val();
        $("input:radio[name=bdKind]").bind('change', function (event, isLoad) {
            var val = $(this).val();
            if( oldCheckedKind == 'event' && val!= 'event' ){  //유형을 이벤트에서 다른유형으로 수정하는경우 쓰기권한 전체로 변경
                $('input:radio[name=bdAuthWrite][value=member]').prop('checked',true);
            }
            if (val === 'default' || val === 'qa') {
                $(':radio[name="bdListImageFl"]').attr('disabled', false);
                $('input[name=bdListImageFl]').closest('.ncua-switch').removeClass('ncua-switch--disabled');
            } else if (val === 'gallery' || val === 'event') {
                $('input[name=bdListImageFl][value="y"]').prop('checked', true);
                $(':radio[name="bdListImageFl"][value="y"]').closest('label').addClass('ncua-switch__option--active').removeClass('ncua-switch__option--inactive');
                $(':radio[name="bdListImageFl"][value="n"]').closest('label').addClass('ncua-switch__option--inactive').removeClass('ncua-switch__option--active');
                $(':radio[name="bdListImageFl"]').attr('disabled', true);
                $('input[name=bdListImageFl]').closest('.ncua-switch').addClass('ncua-switch--disabled');
            }

            // 게시판 유형에 따른 답변관리 기능 사용 노출 처리
            if(val === 'default' || val === 'gallery' || val === 'qa'){
                if(val == 'default' || val == 'gallery') {
                    $(':checkbox[name="bdAnswerStatusFl"]').closest('label').removeClass('display-none');
                    $('.js-answer-status').removeClass('display-none');
                    if ($('input[name=bdReplyFl]:checked').val() == 'y') {
                        $('.bdReplyMileageFl-area').removeClass('display-none');
                        $('.bdReplyFl-area').removeClass('display-none');
                    }
                }else{
                    $(':checkbox[name="bdAnswerStatusFl"]').closest('label').addClass('display-none');
                    $('.js-answer-status').addClass('display-none');
                    $('.bdReplyMileageFl-area').addClass('display-none');
                    $("input:checkbox[name='bdReplyMileageFl']").prop("checked", false);
                    $('.bdReplyFl-area').addClass('display-none');
                }
            }else{
                $(':checkbox[name="bdAnswerStatusFl"]').closest('label').addClass('display-none');
                $('.js-answer-status').addClass('display-none');
                $('.bdReplyMileageFl-area').addClass('display-none');
                $("input:checkbox[name='bdReplyMileageFl']").prop("checked", false);
            }

            oldCheckedKind = val;

            if (isLoad !== true) {
                <?php if ($gGlobal['isUse']) {  //글로벌 사용중   ?>
                <?php foreach ($gGlobal['useMallList'] as $key => $val) {?>
                loadSkinSelectBox(val, 'n', '<?=$val['skin']['frontLive']?>', '<?=$val['domainFl']?>');
                loadSkinSelectBox(val, 'y', '<?=$val['skin']['mobileLive']?>', '<?=$val['domainFl']?>');
                <?php }?>
                <?php }
                else {?>
                loadSkinSelectBox(val, 'n');
                loadSkinSelectBox(val, 'y');
                <?php  }?>
            }


            var isCondition = function (data, val, flag) {
                var _tmp = data.split('_');

                if (flag == 'is') { //or
                    for (var i = 0; i < _tmp.length; i++) {
                        if (_tmp[i] == val) {
                            return true;
                        }
                    }
                    return false;
                }
                else {
                    for (var i = 0; i < _tmp.length; i++) {
                        if (_tmp[i] != val) {
                            return true;
                        }
                    }
                    return false;
                }
            }

            $('[class^="if-"]').each(function () {
                className = $(this).attr('class');
                _className = className.split(' ')[0].split('-');
                condition = _className[1];
                kind = _className[2];
                actionName = _className[3];
                arrayAction = actionName.split('_');
                thisClassEl = $('.' + className);
                if (thisClassEl.length === 0) {
                    thisClassEl = $(this);
                }

                for (var i = 0; i < arrayAction.length; i++) {
                    action = arrayAction[i];
                    if (isCondition(kind, val, condition) == true) {
                        if (action == 'show') {
                            thisClassEl.show();
                        }
                        else if (action == 'hide') {
                            thisClassEl.hide();
                        }
                        else if (action == 'disabled') {
                            thisClassEl.attr('disabled', true);
                        }
                        else if (action == 'checked') {
                            //thisClassEl.prop('checked', true);
                            thisClassEl.trigger('click');
                        }
                    }
                    else {
                        if (action == 'show') {
                            thisClassEl.hide();
                        }
                        else if (action == 'hide') {
                            thisClassEl.show();
                        }
                        else if (action == 'disabled') {
                            thisClassEl.removeAttr('disabled');
                        }
                        else if (action == 'checked') {
                        }
                    }
                }


            });

            updateWriterTableTopShape($('.writer-setting-table tr.if-is-event-show'));
            updateListTableBottomShape($('.list-setting-table tr.if-is-event-show'));

            // 게시판 등록시 유형 변경되었을경우 디폴트값 변동 없도록
            if(val === 'default' || val === 'gallery' || val === 'qa'){
                if(mode === 'regist'){
                    let replyY = $('input:radio[name=bdReplyFl][value=y]');
                    replyY.prop('checked', true);
                    replyY[0].dispatchEvent(new Event('change', { bubbles: true }));
                    replyY.trigger('click');
                }
            }

            var defaultListImageSizeWidth = '<?=$data['bdListImageSizeWidth']?>';
            var defaultListImageSizeHeight = '<?=$data['bdListImageSizeHeight']?>';
            var defaultEventListImageSizeWidth = '<?=$data['bdEventListImageSizeWidth']?>';
            var defaultEventListImageSizeHeight = '<?=$data['bdEventListImageSizeHeight']?>';

            if (mode == 'regist') {
                switch (val) {
                    case 'default' :
                    case 'gallery' :
                        $('input[name="bdListImageSize[width]"]').val(defaultListImageSizeWidth);
                        $('input[name="bdListImageSize[height]"]').val(defaultListImageSizeHeight);
                        break;
                    case 'qa' :
                        break;
                    case 'event' :
                        $('input[name="bdListImageSize[width]"]').val(defaultEventListImageSizeWidth);
                        $('input[name="bdListImageSize[height]"]').val(defaultEventListImageSizeHeight);
                        break;
                }
            }
        });

        $("input:radio[name=bdKind]:checked").trigger('change', [true]);

        //파일 업로드
        $("input:radio[name=bdUploadFl]").bind('change', function () {
            const isEnabled = $(this).val() == 'y';
            const $uploadMaxSizeInput = $('input[name=bdUploadMaxSize]');
            const $uploadMaxSizeContainer = $uploadMaxSizeInput.closest('.ncua-input');
            if (isEnabled) {
                $uploadMaxSizeContainer.removeClass('is-disabled');
                $uploadMaxSizeInput.prop('disabled', false);
            } else {
                $uploadMaxSizeContainer.addClass('is-disabled');
                $uploadMaxSizeInput.prop('disabled', true);
            }
        });
        $("input:radio[name=bdUploadFl]:checked").trigger('change');

        //아이디중복체크
        $('#overlap_bdId').bind('click', function () {
            var bdId = $('input[name=bdId]').val();
            if (bdId.length < 2 || bdId.length > 30) {
                NCDSAlert({message: '2~30자리까지 입력가능합니다.', iconType: 'error'});
                return false;
            }

            if (!validId(bdId)) {
                NCDSAlert({message: '영문으로 시작해야하며 특수문자와 한글은 사용하실 수 없습니다.(2~30자)', iconType: 'error'});
                return false;
            }
            $.post('board_ps.php', {'bdId': bdId, 'mode': 'overlapBdId'},
                function (data) {
                    if (data['result'] == 'ok') {
                        NCDSToast({message: '사용 가능합니다.', color: 'success'});
                        $('#chkbdId').val(bdId);
                    } else {
                        NCDSAlert({message: data['msg'], iconType: 'error'});
                    }
                })
        })

        //스토리지 경로 변경
        $("input[name=bdId]").bind('keyup', function () {
            $(this).val($(this).val().replace(/[^a-z0-9]*/gi, ''));
            var val = valThumb = $(this).val();
            if (val != "") {
                val += "/";
                valThumb = val + "t/";
            }
            $("#bdUploadPath").val('upload/' + val);
            $("#bdUploadThumbPath").val('upload/' + valThumb);
        });

        //말머리
        $('input[name=bdCategoryFl]').bind('change', function () {
            $('.category-write').hide();
            if ($(this).val() == 'y') {
                $('.category-write').show();
            }
        });

        if($('input[name=bdCategoryFl]:checked').val() == 'y') {
            $('.category-write').show();
        } else {
            $('.category-write').hide();
        }

        //저장소
        $("#bdUploadStorage").bind('change', function () {
            var storageName = $(this).val();
            $.get("board_ps.php", {mode: "getStorage", storage: storageName, pathCode: '<?=Storage::PATH_CODE_BOARD?>'})
                .done(function (data) {
                    $("#spanFileStorage").html(data);
                    $("#spanFileThumbStorage").html(data);
                    if (storageName == 'local') {
                        $(".save-location-text").css('display', 'none');
                    } else {
                        $(".save-location-text").css('display', 'flex');
                    }
                });
        });
        $('#bdUploadStorage').trigger('change');

        //마일리지
        $('input[name=bdMileageFl]').bind('click', function () {
            $('.bdMileageFl-area').hide();
            if ($(this).val() == 'y') {
                $('.bdMileageFl-area').show();
            }
        });
        $('input[name=bdMileageFl]:checked').trigger('click');


        $('input[name=bdMileageFl]').bind('click', function () {
            $('input[name=bdMileageAmount]').attr('disabled', $(this).val() == 'n');
        });
        $("input:radio[name=bdMileageFl]:checked").trigger('click');


        // 비밀글 제목설정: 제목 노출(0)일 때 비활성화, 제목 지정(1)일 때 활성화
        const updateSecretTitleInput = () => {
            const secretTitleInput = document.querySelector('input[name="bdSecretTitleTxt"]');
            const secretTitleRadio = document.querySelector('input[name="bdSecretTitleFl"]:checked');
            if (secretTitleInput && secretTitleRadio) {
                const inputWrapper = secretTitleInput.closest('.ncua-input');
                if (inputWrapper) {
                    if (secretTitleRadio.value === '1') {
                        inputWrapper.classList.remove('is-disabled');
                        secretTitleInput.disabled = false;
                    } else {
                        inputWrapper.classList.add('is-disabled');
                        secretTitleInput.disabled = true;
                    }
                }
            }
        };

        // 비밀댓글 제목설정: 제목 노출(0)일 때 비활성화, 제목 지정(1)일 때 활성화
        const updateSecretReplyTitleInput = () => {
            const secretReplyTitleInput = document.querySelector('input[name="bdSecretReplyTitle"]');
            const secretReplyTitleRadio = document.querySelector('input[name="bdSecretReplyTitleFl"]:checked');
            if (secretReplyTitleInput && secretReplyTitleRadio) {
                const inputWrapper = secretReplyTitleInput.closest('.ncua-input');
                if (inputWrapper) {
                    if (secretReplyTitleRadio.value === '1') {
                        inputWrapper.classList.remove('is-disabled');
                        secretReplyTitleInput.disabled = false;
                    } else {
                        inputWrapper.classList.add('is-disabled');
                        secretReplyTitleInput.disabled = true;
                    }
                }
            }
        };

        // 라디오 버튼 변경 이벤트 리스너
        const form = document.getElementById('frmBoard');
        if (form) {
            form.addEventListener('change', (e) => {
                if (e.target.matches('input[name="bdSecretTitleFl"]')) {
                    updateSecretTitleInput();
                }
                if (e.target.matches('input[name="bdSecretReplyTitleFl"]')) {
                    updateSecretReplyTitleInput();
                }
            });
        }

        // 초기 상태 설정
        updateSecretTitleInput();
        updateSecretReplyTitleInput();

        var validId = function (id) {
            var regExp = /^[A-za-z][A-za-z0-9]{2,29}$/g;
            return regExp.test(id);
        }
        //이벤트
        $("input:radio[name=bdEndEventType]").bind('click', function () {
            const target = document.querySelector('input[name="bdEndEventMsg"]')
            const targetWrapper = target.closest('.ncua-input');
            target.disabled = false;
            targetWrapper.classList.remove('is-disabled');
            if ($("input:radio[name=bdEndEventType]:checked").val() == 'read') {
                target.disabled = true;
                targetWrapper.classList.add('is-disabled');
            }
        });
        $("input:radio[name=bdEndEventType]:checked").trigger('click');

        $(':radio[name="bdListImageFl"]').click(function () {
            var row = $('#trListImageSize');
            var noticeImage = $("#trListNoticeImage");

            var bdKind = $("input:radio[name=bdKind]:checked").val();

            if ($(this).val() == 'y') {
                row.show();
                if(bdKind === 'default' || bdKind === 'qa'){
                    noticeImage.show();
                }
            } else {
                row.hide();
                if(bdKind === 'default' || bdKind === 'qa'){
                    noticeImage.hide();
                }
            }
        });
        $(':radio[name="bdListImageFl"]:checked').trigger('click');

        $('input[name=bdIncludeReplayInSearchFl]').bind('click',function () {
            $('.js-bdIncludeReplayInSearchFl-show').hide()
            if($(this).val() == 'y'){
                $('.js-bdIncludeReplayInSearchFl-show').show();
            }
        })

        $(':radio[name=bdIncludeReplayInSearchFl]:checked').trigger('click')

        $.validator.addMethod("regx", function (value, element, fl) {
            return validId(value);
        }, "영문으로 시작해야하며 특수문자와 한글은 사용하실 수 없습니다.(2~30자)");

        // 폼검증
        $("#frmBoard").validate({
            ignore: ':hidden',
            invalidHandler: function(event, validator) {
                if (validator.errorList.length > 0) {
                    NCDSAlert({
                        message: validator.errorList[0].message,
                        iconType: 'error'
                    });
                }
            },
            errorPlacement: function(error, element) {
                return false;
            },
            submitHandler: function (form) {
                if ($(':radio[name="bdListImageFl"][value="y"]').prop('checked') && $('.js-bdListImageTarget-notice').css('display') != 'none') {
                    NCDSAlert({message: this.settings.messages.bdListImageTarget.required, iconType: 'error'});
                    return false;
                }
                // 에디터 내용은 자동 반영되므로 별도의 처리 필요 없음

                // disabled 필드중 일부도 submit에 포함되도록 hidden input으로 값 복사
                // 기존 hidden input 제거 후 새로 생성 (중복 방지)

                // 상품연동 관련 필드
                const goodsField = form.querySelector('input[name="bdGoodsFl"]:checked');
                if (goodsField && goodsField.disabled) {
                    // 기존 hidden input 제거 (이전 제출 실패 시 남아있을 수 있음)
                    const existingHidden = form.querySelector('input[type="hidden"][name="bdGoodsFl"][data-disabled-submit]');
                    if (existingHidden) {
                        existingHidden.remove();
                    }
                    // 새 hidden input 생성
                    const hiddenInput = document.createElement('input');
                    hiddenInput.type = 'hidden';
                    hiddenInput.name = 'bdGoodsFl';
                    hiddenInput.value = goodsField.value;
                    hiddenInput.setAttribute('data-disabled-submit', 'true');
                    form.appendChild(hiddenInput);
                } else {
                    // disabled가 아니면 기존 hidden input 제거
                    const existingHidden = form.querySelector('input[type="hidden"][name="bdGoodsFl"][data-disabled-submit]');
                    if (existingHidden) {
                        existingHidden.remove();
                    }
                }

                // 대표 이미지 설정 관련 필드
                const listImageField = form.querySelector('input[name="bdListImageFl"]:checked');
                if (listImageField && listImageField.disabled) {
                    // 기존 hidden input 제거 (이전 제출 실패 시 남아있을 수 있음)
                    const existingHidden = form.querySelector('input[type="hidden"][name="bdListImageFl"][data-disabled-submit]');
                    if (existingHidden) {
                        existingHidden.remove();
                    }
                    // 새 hidden input 생성
                    const hiddenInput = document.createElement('input');
                    hiddenInput.type = 'hidden';
                    hiddenInput.name = 'bdListImageFl';
                    hiddenInput.value = listImageField.value;
                    hiddenInput.setAttribute('data-disabled-submit', 'true');
                    form.appendChild(hiddenInput);
                } else {
                    // disabled가 아니면 기존 hidden input 제거
                    const existingHidden = form.querySelector('input[type="hidden"][name="bdListImageFl"][data-disabled-submit]');
                    if (existingHidden) {
                        existingHidden.remove();
                    }
                }

                form.target = 'ifrmProcess';
                form.submit();
            },
            showErrors: function (errorMap, errorList) {
                NCDSValidator.showErrors(errorMap, errorList);
                this.defaultShowErrors();
            },
            // onclick: false, // <-- add this option
            rules: {
                bdId: {
                    required: true,
                    minlength: 2,
                    maxlength: 30,
                    regx: true,
                    equalTo: '#chkbdId',
                },
                bdUsePcFl: "required",
                bdUseMobileFl: "required",
                bdKind: "required",
                bdNm: 'required',
                themeSno: 'required',
                mobileThemeSno: 'required',
                bdAuthMemo: {
                    required: function () {
                        return $('input[name=bdMemoFl][value=y]').is(':checked');
                    }
                },
                bdAuthReply: {
                    required: function () {
                        return $('input[name=bdReplyFl][value=y]').is(':checked');
                    }
                },
                bdUploadMaxSize: {
                    required: "input:radio[name=bdUploadFl][value=y]:checked"
                },
                bdCategoryTitle: {
                    required: "input:radio[name=bdCategoryFl][value=y]:checked"
                },
                'bdCategory[]': {
                    required: "input:radio[name=bdCategoryFl][value=y]:checked"
                },
                bdSubjectLength: {
                    max: 255,
                    number: true,
                },
                bdGoodsFl: 'required',
                bdGoodsType: {
                    required: "input:radio[name=bdGoodsFl][value=y]:checked"
                },
                bdMileageAmount: {
                    required: "input:checkbox[name=bdMileageFl][value=y]:checked",
                },
                bdAttachImageMaxSize: {
                    required: "input[name=bdAttachImageDisplayFl][value=y]:checked",
                    number: "input[name=bdAttachImageDisplayFl][value=y]:checked",
                },
                bdListCount: {
                    required: true,
                    number: true,
                },
                bdListImageTarget: {
                    required: ":radio[name=bdListImageFl][value=y]:checked"
                },
                bdGoodsPageCountPc: {
                    required: true,
                    number: true,
                    min: 1,
                },
                bdGoodsPageCountMobile: {
                    required: true,
                    number: true,
                    min: 1,
                }
            },
            messages: {
                themeSno: {
                    required: "PC 쇼핑몰 스킨을 선택해주세요.",
                },
                mobileThemeSno: {
                    required: "모바일 쇼핑몰 스킨을 선택해주세요.",
                },
                bdAuthMemo: {
                    required: "댓글권한설정을 선택해주세요.",
                },
                bdAuthReply: {
                    required: "답변권한설정을 선택해주세요.",
                },
                bdKind: {
                    required: "유형을 선택하세요",
                },
                bdId: {
                    required: "아이디를 입력하세요.",
                    equalTo: '아이디 중복체크를 해주세요.',
                    minlength: '{0}자 이상입력해주세요.',
                    maxlength: '{0}자 이하로 입력해주세요.',
                },
                bdGoodsFl: {
                    required: "상품연동을 사용여부를 체크해주세요",
                },
                bdGoodsType: {
                    required: "상품/주문연동을 체크해주세요",
                },
                bdUsePcFl: {
                    required: "PC쇼핑몰 사용여부를 체크해주세요."
                },
                bdUseMobileFl: {
                    required: "모바일쇼핑몰 사용여부를 체크해주세요."
                },
                bdNm: {
                    required: "이름을 입력해주세요."
                },
                bdUploadMaxSize: {
                    required: "업로드 사이즈를 입력해주세요."
                },
                bdCategoryTitle: {
                    required: "말머리 타이틀을 입력해주세요."
                },
                'bdCategory[]': {
                    required: "말머리명을 입력해주세요."
                },
                bdSubjectLength: {
                    max: '{0} 까지 제한가능'
                },
                bdAttachImageMaxSize: {
                    required: '이미지 리사이즈값을 입력해주세요.',
                    number: '숫자를 입력해주세요.'
                },
                bdListImageTarget: {
                    required: "대표 이미지 설정이 상품연동/업로드파일사용/에디터 사용 중 1개 이상을 사용함으로 설정해야합니다."
                },
                bdGoodsPageCountPc: {
                    required: '상품상세 페이지 내 페이지별 게시물 수(PC)를 입력해 주세요.',
                    number: '상품상세 페이지 내 페이지별 게시물 수(PC)는 숫자로 입력해 주세요.',
                    min: '상품상세 페이지 내 페이지별 게시물 수(PC)는 0 이상 입력해 주세요.',
                },
                bdGoodsPageCountMobile: {
                    required: '상품상세 페이지 내 페이지별 게시물 수(모바일)를 입력해 주세요.',
                    number: '상품상세 페이지 내 페이지별 게시물 수(모바일)는 숫자로 입력해 주세요.',
                    min: '상품상세 페이지 내 페이지별 게시물 수(모바일)는 0 이상 입력해 주세요.',
                }
            },
        });

        function showTemplateRegisterLayer(layerForm) {
            BootstrapDialog.show({
                title: '게시글 양식 등록',
                size: 'size-wide',
                message: $(layerForm),
                closable: true,
                cssClass: 'ncds-modal',
                onshown: function(dialog) {
                    const modal = dialog.getModal().get(0);
                    const target = modal.querySelector('.bootstrap-dialog-message');
                    const observer = new MutationObserver((mutations) => {
                        const modal = mutations[0].target.closest('.bootstrap-dialog');
                        if (modal && modal.classList.contains('ncds-modal')) {
                            modal.classList.remove('ncds-modal');
                        }
                        observer.disconnect();
                    });

                    observer.observe(target, {
                        childList: true,
                        subtree: false,
                    });
                }
            });
        }

        $('.js-template-register').bind('click', function () {
            $.ajax({
                url: 'template_write.php',
                success: function (data) {
                    const layerForm = data;
                    showTemplateRegisterLayer(layerForm);
                }
            });
        })

        // 답변관리 기능 사용 디폴트값은 체크해제(등록일경우)
        if(mode == 'regist'){
            $('input[name="bdKind"]').click(function () {
                $("input:checkbox[name='bdAnswerStatusFl']").prop("checked", false);
            });
        }
    });

    // 말머리 기능 > 말머리 입력 > 삭제 버튼 클릭 시
    function remove_category(button) {
        const item = $(button).closest('.heading-function-category-item');
        item.remove();
    }

    // 말머리 기능 > 말머리 입력 > 추가 버튼 클릭 시
    function add_category() {
        var select = $("#bdTemplateSno").html();
        var html = `
            <div class="ncua-gap-8 ncua-flex heading-function-category-item">
                <div class="ncua-input ncua-input--xs heading-function-select-flex-1 ncua-input-width-320">
                    <div class="ncua-input__content">
                        <div class="ncua-input__field ncua-input__field--xs">
                            <input type="text" name="bdCategory[]" class="js-add-field-category" value="" placeholder="말머리를 입력하세요."/>
                        </div>
                    </div>
                </div>
                <span class="ncua-select ncua-select--xs board-register-select-box-width-240">
                    <span class="ncua-select__content">
                        <?= gd_select_box('bdCategoryTemplateSno[]', 'bdCategoryTemplateSno[]', $templateList, null, $bdCategoryTemplateSno[$i], null, null, 'ncua-select__tag'); ?>
                    </span>
                </span>
                <div onclick="remove_category(this);" class="ncua-remove-icon-button"></div>
            </div>
        `;
        $('.heading-function-category-input').children().last().before(html);
        $("input[name='bdCategory[]']:last").focus();
    }

    function loadSkinSelectBox(bdKind, isMobile, liveSkin, domainFl) {
        $.ajax({
            method: 'get',
            url: 'board_ps.php',
            data: {'mode': 'selectListTheme', 'bdKind': bdKind, 'mobileFl': isMobile, 'liveSkin': liveSkin},
            dataType: 'json'
        }).success(function (data) {
            if (typeof domainFl != 'undefined') {
                var domainPostfix = (domainFl == 'kr') ? '' : domainFl;
                if (domainFl != '') {
                    domainPostfix = domainPostfix.substring(0, 1).toUpperCase() + domainPostfix.substring(1, domainPostfix.length).toLowerCase()
                }
            }
            else {
                domainPostfix = '';
            }

            var $themeSnoSelector = isMobile == 'y' ? $('select[name=mobileTheme' + domainPostfix + 'Sno]') : $('select[name=theme' + domainPostfix + 'Sno]');

            $themeSnoSelector.empty();
            $themeSnoSelector.append($('<option>', {value: 0, text: '스킨을 선택하세요.'}));
            for (var i = 0; i < data.list.length; i++) {
                $themeSnoSelector.append($('<option>', {value: data.list[i].sno, text: data.list[i].themeNm}));
            }
            if (data.selected == null) {
                $themeSnoSelector.index(0);
            }
            else {
                $themeSnoSelector.val(data.selected);
            }
        }).error(function (e) {
            NCDSAlert({message: '스킨 정보를 불러오는데 실패했습니다.', iconType: 'error'});
        });

        /*$.ajax({
         method: 'get',
         url: 'board_ps.php',
         data: {'mode': 'selectListTheme', 'bdKind': val, 'mobileFl': 'n'},
         dataType: 'json'
         }).success(function (data) {
         $('select[name=themeSno]').empty();
         $('select[name=themeSno]').append($('<option>', {value: 0, text: '스킨을 선택하세요.'}));
         for (var i = 0; i < data.list.length; i++) {
         $('select[name=themeSno]').append($('<option>', {value: data.list[i].sno, text: data.list[i].themeNm}));
         }
         $('select[name=themeSno]').val(data.selected);
         }).error(function (e) {
         console.log(e);
         alert(e);
         });

         $.ajax({
         method: 'get',
         url: 'board_ps.php',
         data: {'mode': 'selectListTheme', 'bdKind': val, 'mobileFl': 'y'},
         dataType: 'json'
         }).success(function (data) {
         $('select[name=mobileThemeSno]').empty();
         $('select[name=mobileThemeSno]').append($('<option>', {value: 0, text: '스킨을 선택하세요.'}));
         for (var i = 0; i < data.list.length; i++) {
         $('select[name=mobileThemeSno]').append($('<option>', {value: data.list[i].sno, text: data.list[i].themeNm}));
         }
         $('select[name=mobileThemeSno]').val(data.selected);
         }).error(function (e) {
         console.log(e);
         alert(e);
         });*/
    }

    /**
     * 예외상품 스위치 토글 처리
     * @param {string} exceptType - 예외 타입 (goods 등)
     * @param {boolean} isEnabled - 사용함 여부
     */
    const presentExcept_conf = (exceptType, isEnabled) => {
        const table = document.getElementById(`presentFlExcept_${exceptType}_tbl`);
        if (table) {
            table.style.display = isEnabled ? 'table-row' : 'none';
        }
    };

    // 예외상품 스위치 이벤트 바인딩
    document.querySelector('input[name="presentExceptFl"]')?.addEventListener('change', (e) => {
        const target = e.target;
        if (!target.hasAttribute('data-except-type')) return;

        const exceptType = target.getAttribute('data-except-type');
        const isEnabled = target.value === 'goods';

        presentExcept_conf(exceptType, isEnabled);
    });

    // 예외상품 선택 삭제
    document.querySelector('.js-delete-exceptGoods')?.addEventListener('click', function(e) {
        // e.currentTarget 사용 (버튼 내부 span 클릭 시에도 버튼 참조)
        const button = e.currentTarget;
        const parent = button.closest('.ncua-search-result__content');
        if (!parent) return;

        const tbody = parent.querySelector('tbody');
        if (!tbody) return;
        const tbodyId = tbody.id;

        // 체크된 체크박스 찾기 (tr 내부의 체크박스만 찾기)
        const checkedCheckboxes = Array.from(tbody.querySelectorAll('tr input[type="checkbox"][name="exceptGoodsChk"]'))
            .filter(cb => cb.checked && cb.closest('tr')?.id?.startsWith('idExceptGoods_'));

        if (checkedCheckboxes.length === 0) {
            NCDSAlert({ message: '선택한 상품이 없습니다', iconType: 'error' });
            return;
        }

        // 선택된 항목 삭제 (field_remove와 동일한 방식)
        checkedCheckboxes.forEach((checkbox) => {
            const tr = checkbox.closest('tr');
            if (tr && tr.id) {
                tr.remove();
            }
        });

        // 번호 재정렬 (field_remove와 동일한 방식)
        const remainingRows = tbody.querySelectorAll('tr:not(.tr-no-data)');
        remainingRows?.forEach((row, index) => {
            const numberCell = row.querySelector('td:nth-child(2) div');
            if (numberCell) {
                // hidden input은 유지하고 텍스트만 변경
                const hiddenInput = numberCell.querySelector('input[type="hidden"]');
                const hiddenInputValue = hiddenInput ? hiddenInput.outerHTML : '';
                // 번호와 hidden input만 남기기
                numberCell.innerHTML = (index + 1) + hiddenInputValue;
            }
        });

        // 남은 데이터 확인 및 처리
        if (remainingRows?.length === 0) {
            // 데이터 없음 메시지 추가
            tbody.innerHTML = '<tr class="tr-no-data"><td colspan="4" class="no-data"><div>추가된 예외상품이 없습니다.</div></td></tr>';
        }

        // 전체 선택 체크박스 해제
        const checkAllBox = parent.querySelector('.js-checkall');
        if (checkAllBox) {
            checkAllBox.checked = false;
        }
    });

    // selectMemberShipComboBox.js에서 MemberGroupManager를 제공
    // 스크립트가 로드된 후 초기화
    function initMemberGroupComboBoxes() {
        if (typeof initMemberGroupComboBox === 'undefined') {
            // 스크립트가 아직 로드되지 않았으면 재시도
            setTimeout(initMemberGroupComboBoxes, 100);
            return;
        }

        // ComboBox 인스턴스 저장 객체
        const memberGroupComboBoxes = {};

        // 각 권한별 ComboBox 초기화
        const comboBoxList = initMemberGroupComboBox({
            comboboxId: 'layer_member_group_list_combobox',
            parentLayerId: 'member_groupLayer_list',
            dataInputNm: 'bdAuthListGroup',
            dataFormID: 'info_member_list_group',
            pageCountVar: 'comboBoxMemberGroupListPageCount',
            keywordVar: 'comboBoxMemberGroupListKeyword'
        });
        if (comboBoxList) {
            memberGroupComboBoxes['layer_member_group_list_combobox'] = comboBoxList;
        }

        const comboBoxRead = initMemberGroupComboBox({
            comboboxId: 'layer_member_group_read_combobox',
            parentLayerId: 'member_groupLayer_read',
            dataInputNm: 'bdAuthReadGroup',
            dataFormID: 'info_member_read_group',
            pageCountVar: 'comboBoxMemberGroupReadPageCount',
            keywordVar: 'comboBoxMemberGroupReadKeyword'
        });
        if (comboBoxRead) {
            memberGroupComboBoxes['layer_member_group_read_combobox'] = comboBoxRead;
        }

        const comboBoxWrite = initMemberGroupComboBox({
            comboboxId: 'layer_member_group_write_combobox',
            parentLayerId: 'member_groupLayer_write',
            dataInputNm: 'bdAuthWriteGroup',
            dataFormID: 'info_member_write_group',
            pageCountVar: 'comboBoxMemberGroupWritePageCount',
            keywordVar: 'comboBoxMemberGroupWriteKeyword'
        });
        if (comboBoxWrite) {
            memberGroupComboBoxes['layer_member_group_write_combobox'] = comboBoxWrite;
        }

        const comboBoxReply = initMemberGroupComboBox({
            comboboxId: 'layer_member_group_reply_combobox',
            parentLayerId: 'member_groupLayer_reply',
            dataInputNm: 'bdAuthReplyGroup',
            dataFormID: 'info_member_reply_group',
            pageCountVar: 'comboBoxMemberGroupReplyPageCount',
            keywordVar: 'comboBoxMemberGroupReplyKeyword'
        });
        if (comboBoxReply) {
            memberGroupComboBoxes['layer_member_group_reply_combobox'] = comboBoxReply;
        }

        const comboBoxMemo = initMemberGroupComboBox({
            comboboxId: 'layer_member_group_memo_combobox',
            parentLayerId: 'member_groupLayer_memo',
            dataInputNm: 'bdAuthMemoGroup',
            dataFormID: 'info_member_memo_group',
            pageCountVar: 'comboBoxMemberGroupMemoPageCount',
            keywordVar: 'comboBoxMemberGroupMemoKeyword'
        });
        if (comboBoxMemo) {
            memberGroupComboBoxes['layer_member_group_memo_combobox'] = comboBoxMemo;
        }
    }

    // DOM이 준비되면 초기화
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initMemberGroupComboBoxes);
    } else {
        // 이미 로드된 경우 즉시 실행
        initMemberGroupComboBoxes();
    }

    const btnBack = document.querySelector('.js-btn-back');
    if (btnBack) {
        btnBack.addEventListener('click', function() {
            history.back();
        });
    }

    function updateWriterTableTopShape(target) {
        const $target = (typeof jQuery !== 'undefined' && target instanceof jQuery)
            ? target
            : $(target);

        if (!$target || $target.length === 0) {
            return;
        }

        const tableTopShape = $target.data('table-top-shape');
        if (tableTopShape === undefined) {
            return;
        }

        const $nextRow = $target.next();
        if ($nextRow.length === 0) {
            return;
        }

        const isHidden = window.getComputedStyle($target[0]).display === 'none';

        if (isHidden) {
            $target.removeAttr('data-table-top-shape');
            $nextRow.attr('data-table-top-shape', '');
        } else {
            $target.attr('data-table-top-shape', '');
            $nextRow.removeAttr('data-table-top-shape');
        }
    }

    function updateListTableBottomShape(target) {
        const $target = (typeof jQuery !== 'undefined' && target instanceof jQuery)
            ? target
            : $(target);

        if (!$target || $target.length === 0) {
            return;
        }
        const tableBottomShape = $target.data('table-bottom-shape');
        if (tableBottomShape === undefined) {
            return;
        }

        const $prevRow = $target.prev();
        if ($prevRow.length === 0) {
            return;
        }

        const isHidden = window.getComputedStyle($target[0]).display === 'none';

        if (isHidden) {
            $target.removeAttr('data-table-bottom-shape');
            $prevRow.attr('data-table-bottom-shape', '');
        } else {
            $target.attr('data-table-bottom-shape', '');
            $prevRow.removeAttr('data-table-bottom-shape');
        }
    }

    updateListTableBottomShape($('.list-setting-table tr.if-is-event-show'));
    updateWriterTableTopShape($('.writer-setting-table tr.if-is-event-show'));
</script>

<script type="text/javascript">
    const code = '251023001';

    function initTooltipTrigger() {
        const tooltipTriggers = document.querySelectorAll('.js-tooltip-trigger');
        if (tooltipTriggers?.length === 0) return;
        tooltipTriggers.forEach(trigger => {
            trigger.addEventListener('mouseenter', (e) => {
                // 버튼 내부의 ncua-tooltip-icon 요소에 mouseenter 이벤트 전파
                const tooltipElement = trigger.querySelector('.ncua-tooltip__icon');
                if (!tooltipElement) return;

                tooltipElement.dispatchEvent(new MouseEvent('mouseenter', {
                    bubbles: false,
                    cancelable: true,
                    view: window,
                    relatedTarget: e.relatedTarget || null
                }));
            });
        });
    }


    if (window.GodoCosGuide) {
        window.GodoCosGuide.init({
            apiUrl: <?= json_encode($cosGuideApiUrl) ?>,
            guideCode: code,
            tooltipOptions: {
                zIndex: 2000,
            },
        }).then(() => {
            setTimeout(() => {
                if(typeof window.initClipboard === 'function') {
                    window.initClipboard(); // 치환코드 선택에서 클립보드 기능이 필요하여 별도 초기화
                }
            }, 100);
            initTooltipTrigger();
        });
    }
</script>
