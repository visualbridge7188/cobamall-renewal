<link type="text/css" href="<?=gd_set_browser_cache(PATH_ADMIN_GD_SHARE . 'ncds/css/promotion/coupon-regist.css')?>" rel="stylesheet" />
<script defer src="<?=gd_set_browser_cache(PATH_ADMIN_GD_SHARE . 'ncds/script/ncds-multi-select/ncds-multi-select.js')?>"></script>
<script defer src="<?=gd_set_browser_cache(PATH_ADMIN_GD_SHARE . 'ncds/script/common/charcount.js')?>"></script>
<script defer src="<?=gd_set_browser_cache(PATH_ADMIN_GD_SHARE . 'ncds/script/common/checkbox-selection.js')?>"></script>
<script defer src="<?=gd_set_browser_cache(PATH_ADMIN_GD_SHARE . 'ncds/script/common/switch.js')?>"></script>
<script defer src="<?=gd_set_browser_cache(PATH_ADMIN_GD_SHARE . 'ncds/script/common/select-member-combo-box.js')?>"></script>
<script defer src="<?=gd_set_browser_cache(PATH_ADMIN_GD_SHARE . 'ncds/script/common/supply-combo-box.js')?>"></script>
<script defer src="<?=gd_set_browser_cache(PATH_ADMIN_GD_SHARE . 'ncds/script/common/number-only.js')?>"></script>

<article class="ncua-content promotion-coupon-regist">
    <form id="frmCoupon" name="frmCoupon" action="<?= URI_ADMIN; ?>/promotion/coupon_ps.php" method="post" class="content_form" enctype="multipart/form-data">
        <input type="hidden" name="mode" value="<?= $couponData['mode']; ?>"/>
        <input type="hidden" name="ypage" value="<?= $ypage; ?>"/>
        <input type="hidden" name="couponNo" value="<?= $couponData['couponNo']; ?>"/>
        <input type="hidden" name="couponKind" value="<?= $couponData['couponKind']; ?>"/>
        <input type="hidden" name="couponType" value="<?= $couponData['couponType']; ?>"/>

        <header class="page-header js-affix ncua-page-header">
            <h3 class="ncua-help-manual"><?php echo end($naviMenu->location); ?></h3>
            <div class="ncua-page-header__actions">
                <button type="button" class="ncua-btn ncua-btn--md ncua-btn--secondary-gray" onclick="goList('<?= URI_ADMIN; ?>promotion/coupon_list.php');"><span class="ncua-btn__label">목록</span></button>
                <button type="submit" class="ncua-btn ncua-btn--md ncua-btn--primary">저장</button>
            </div>
        </header>

        <?php if ($couponData['mode'] === 'modifyCouponRegist' && !empty($couponData['couponSaveCount']) && $couponData['couponSaveCount'] > 0) { ?>
        <section class="ncua-info">
            <span class="ncua-info__ico"></span>
            <p class="ncua-info__content">
                이미 발급된 쿠폰은 일부 항목만 수정 가능하나, 수정 시 쿠폰 이용고객과 분쟁의 소지가 있을 수 있습니다.<br />
                <span class="text-red">쿠폰의 적용 상품 범위를 변경해도 문제 없는 쿠폰인지 반드시 확인한 후 설정을 수정하시기 바랍니다.</span><br />
                이를 준수하지 않는 경우 발생하는 문제에 대한 책임은 쇼핑몰 운영자에게 있으며, 수정 가능 항목에 대한 상세 내용은 가이드를 참고해 주세요.
            </p>
        </section>
        <?php } ?>

        <!-- 기본 정보 -->
        <?php include $couponRegistBasicInfo ?>

        <!-- 발급 정보 -->
        <?php include $couponRegistUseTypeInfo ?>

        <!-- 사용 조건 -->
        <?php include $couponRegistUseConditionInfo ?>
    </form>
</div>

<script type="text/javascript">
    $(document).ready(function () {
        $("#frmCoupon").validate({
            ignore: ":hidden, :disabled",
            submitHandler: function (form) {
                const validator = $(form).validate();
                // 검증 실패 시 invalidHandler 호출하는 헬퍼
                const triggerValidationError = ({ message }, fieldName) => {
                    let targetElement = form.elements[fieldName];
        
                    if (targetElement && targetElement.length > 1) {
                        targetElement = targetElement[0];
                    }

                    validator.errorList = [{
                        message: message,
                        element: targetElement || form
                    }];
                    validator.settings.invalidHandler(form, validator);
                };

                // 회원등급 검증 헬퍼 함수
                const validateMemberGroup = (radioName, containerId, errorMessage) => {
                    const selectedValue = document.querySelector(`input[name="${radioName}"]:checked`)?.value;
                    const hasSelectedGroups = document.getElementById(containerId)?.children.length > 0;

                    if (selectedValue === 'group' && !hasSelectedGroups) {
                        return {
                            isValid: false,
                            message: errorMessage,
                            fieldName: radioName
                        };
                    }
                    
                    return { isValid: true };
                };
                
                // 쿠폰 사용 가능 상품범위 설정 검증
                const applyValidation = validateCouponApplyTypeValue();
                if (!applyValidation.result) {
                    triggerValidationError(applyValidation, 'couponApplyProductType');
                    return false;
                }

                // 쿠폰 사용 제외 설정 검증
                const exceptValidation = validateCouponExceptTypeValue();
                if (!exceptValidation.result) {
                    triggerValidationError(exceptValidation, 'couponExceptProviderType');
                    return false;
                }

                // 발급 가능 회원등급 검증
                const issueMemberGroupTr = document.getElementById('issueMemberGroupTr');
                if (issueMemberGroupTr && getComputedStyle(issueMemberGroupTr).display !== 'none') {
                    const applyMemberGroupValidation = validateMemberGroup(
                        'applyMemberGroup',
                        'member_groupLayer_apply_member',
                        '회원 등급을 선택해주세요.'
                    );

                    if (!applyMemberGroupValidation.isValid) {
                        triggerValidationError(applyMemberGroupValidation, 'applyMemberGroup');
                        return false;
                    }
                }

                // 사용 가능 회원등급 검증
                const useMemberGroupTr = document.getElementById('useMemberGroupTr');
                if (useMemberGroupTr && getComputedStyle(useMemberGroupTr).display !== 'none') {
                    const useMemberGroupValidation = validateMemberGroup(
                        'useMemberGroup',
                        'member_groupLayer_use_member',
                        '회원 등급을 선택해주세요.'
                    );

                    if (!useMemberGroupValidation.isValid) {
                        triggerValidationError(useMemberGroupValidation, 'useMemberGroup');
                        return false;
                    }
                }

                form.target = 'ifrmProcess';
                form.submit();
            },
            invalidHandler: function(form, validator) {
                if (validator.errorList.length === 0) return;

                const [firstError] = validator.errorList;
                const { element: target, message } = firstError;

                NCDSAlert({
                    message,
                    iconType: 'error',
                    callback: () => {
                        if (target && typeof target.focus === 'function') {
                            target.focus();

                            // 특정 필드에 대한 레이어 내 input 포커스 맵핑
                            const layerInputFocusMap = {
                                applyMemberGroup: '#layer_member_group_apply_member_combobox input',
                                useMemberGroup: '#layer_member_group_use_member_combobox input',
                                couponApplyProductType: '#ncua-apply-combo-box-layer input',
                                couponExceptProviderType: '#ncua-except-combo-box-layer input'
                            };

                            const layerInputSelector = layerInputFocusMap[target.name];
                            if (layerInputSelector) {
                                document.querySelector(layerInputSelector)?.focus();
                            }
                        }
                    }
                });
            },
            rules: {
                mode: {
                    required: true,
                },
                couponKind: {
                    required: true,
                },
                couponType: {
                    required: true,
                },
                couponUseType: {
                    required: true,
                },
                couponSaveType: {
                    required: true,
                },
                couponNm: {
                    required: true,
                    maxlength: 30,
                },
                couponUsePeriodStartDate: {
                    required: function (input) {
                        var required = false;
                        if ($('[name=couponUsePeriodType]:checked').val() == 'period') {
                            required = true;
                        }
                        return required;
                    }
                },
                couponUsePeriodEndDate: {
                    required: function (input) {
                        var required = false;
                        if ($('[name=couponUsePeriodType]:checked').val() == 'period') {
                            required = true;
                        }
                        return required;
                    }
                },
                couponUsePeriodDay: {
                    required: function (input) {
                        var required = false;
                        if ($('[name=couponUsePeriodType]:checked').val() == 'day') {
                            required = true;
                        }
                        return required;
                    }
                },
                couponBenefit: {
                    required: true,
                    min: 1,
                    maxlength: function () {
                        const selectedValue = $('input:radio[name="couponBenefitType"]:checked').val();
                        return selectedValue === 'fix' ? 8 : 999;
                    },
                    max: function () {
                        const selectedValue = $('input:radio[name="couponBenefitType"]:checked').val();
                        return selectedValue === 'percent' ? 100 : 99999999;
                    },
                },
                couponUseDateLimit: {
                    required: function (input) {
                        let required = false;
                        if ($('[name=couponUsePeriodDayLimit]').is(':checked')) {
                            required = true;
                        }
                        return required;
                    }
                },
                couponEventType: {
                    required: function (input) {
                        let required = false;
                        if ($('[name=couponSaveType]:checked').val() == 'auto' && $('[name=couponEventType]').val() == '') {
                            required = true;
                        }
                        return required;
                    }
                }
            },
            messages: {
                mode: {
                    required: '정상 접속이 아닙니다.(mode)',
                },
                couponKind: {
                    required: '정상 접속이 아닙니다.(kind)',
                },
                couponType: {
                    required: '정상 접속이 아닙니다.(type)',
                },
                couponUseType: {
                    required: '쿠폰유형이 선택하세요.',
                },
                couponSaveType: {
                    required: '발급방식이 선택하세요.',
                },
                couponNm: {
                    required: '쿠폰명을 입력하세요.',
                    maxlength: '쿠폰명의 길이는 최대 30자 입니다.',
                },
                couponUsePeriodStartDate: {
                    required: '사용기간 시작일을 입력하세요.',
                },
                couponUsePeriodEndDate: {
                    required: '사용기간 종료일을 입력하세요.',
                },
                couponUsePeriodDay: {
                    required: '사용기간 일자를 입력하세요.',
                },
                couponBenefit: {
                    required: '제공할 할인/적립 혜택금액을 입력해주세요.',
                    min: '제공할 할인/적립 혜택금액을 입력해주세요.',
                    maxlength: function () {
                        const selectedValue = $('input:radio[name="couponBenefitType"]:checked').val();
                        if (selectedValue === 'fix') {
                            return '정액은 8자를 넘을 수 없습니다.';
                        }
                        return '';
                    },
                    max: function () {
                        const selectedValue = $('input:radio[name="couponBenefitType"]:checked').val();
                        if (selectedValue === 'percent') {
                            return '정률(%)의 경우 숫자 100까지 입력하실 수 있습니다.';
                        }
                        return '';
                    },
                },
                couponUseDateLimit: {
                    required: '종료일 제한 일자를 입력하세요.',
                },
                couponEventType: {
                    required: '자동 발급 이벤트를 선택하세요.',
                }
            }
        });
        // 사용구분 선택 시
        $('input:radio[name="couponUseType"]').click(function (e) {
            changeCouponUseType();
            changeCouponSaveType();
            changeCouponKindType();
            changeCouponBenefitType();
            changeCouponApplyMemberGroup();
            changeCouponUseMemberGroup();
            changeCouponApplyProductType();
            changeCouponExceptProductType();
            changeCouponEventType();
            changeCouponProductMinOrderTypeText();
        });
        // 발급방식 선택 시
        $('input:radio[name="couponSaveType"]').click(function (e) {
            changeCouponSaveType();
            changeCouponKindType();
            changeCouponBenefitType();
            changeCouponApplyMemberGroup();
            changeCouponUseMemberGroup();
            changeCouponApplyProductType();
            changeCouponExceptProductType();
            changeCouponProductMinOrderTypeText();
        });
        // 해당구분 선택 시
        $('input:radio[name="couponKindType"]').click(function (e) {
            changeCouponKindType();
            changeCouponBenefitType();
            changeCouponApplyMemberGroup();
            changeCouponUseMemberGroup();
            changeCouponApplyProductType();
            changeCouponExceptProductType();
        });
        // 혜택금액설정에서 정액,정율 선택 시
        $('input[name="couponBenefitType"]').change(function (e) {
            changeCouponBenefitType(true);
            changeCouponApplyMemberGroup();
            changeCouponApplyProductType();
            changeCouponExceptProductType();
        });
        // 쿠폰 적용 범위 설정 선택 시
        $('input:radio[name="applyMemberGroup"]').click(function (e) {
            changeCouponApplyMemberGroup();
        });
        // 쿠폰 사용 범위 설정 선택 시
        $('input:radio[name="useMemberGroup"]').click(function (e) {
            changeCouponUseMemberGroup();
        });
        // 쿠폰 적용 범위 설정 선택 시
        $('input:radio[name="couponApplyProductType"]').click(function (e) {
            changeCouponApplyProductType();
        });
        // 쿠폰 제외 범위 설정 선택 시
        $('input:checkbox[name^="couponExcept"]').click(function (e) {
            changeCouponExceptProductType();
        });
        // 쿠폰 적용 해당 버튼 선택 시
        $('[id^=selectApply]').click(function (e) {
            var code = (this.id).split('selectApply');
            code = code[1];
            layer_register(code);
        });
        // 쿠폰 제외 해당 버튼 선택 시
        $('[id^=selectExcept]').click(function (e) {
            var code = (this.id).split('selectExcept');
            code = code[1];
            layer_register(code, 'except');
        });
        // 쿠폰 사용 가능 버튼 선택시 
        $('#selectUseMemberGroup').click(function (e) {
            layer_register('UseMemberGroup', 'use');
        });
        // 동일 아이디 재발급 제한 설정안함 시 하위 필드 초기화
        $('input:radio[name="couponSaveDuplicateType"]').change(function () {
            if ($(this).val() == 'n') {
                $('input:checkbox[name="couponSaveDuplicateLimitType"]').prop('checked', false);
                $('input:text[name="couponSaveDuplicateLimit"]').val('');
            }
        });
        // 자동발급이벤트 선택으로 인한 체크박스 옵션 해제
        $('body').on('unchecked', '[name="couponEventType"]', function(e){
            $(e.target).parents('div.radio').find(':checkbox').prop('checked', false);
        });
        // 자동발급이벤트 선택 시
        $('[name="couponEventType"]').click(function (e) {
            changeCouponEventType();
            $('[name="' + e.target.name + '"]').not($(this)).trigger('unchecked');
        });
        // 사용기간 타입 변경 시
        $('input[name="couponUsePeriodType"]').change(function (e) {
            if($('input:radio[name="couponUsePeriodType"]:checked').val() == 'period') {
                $('#divCouponUsePeriodTypePeriod').show();
                $('#divCouponUsePeriodTypeDay').hide();
                // 발급일 기준 필드 초기화
                $('input[name="couponUsePeriodDay"]').val('');
                $('input[name="couponUsePeriodDayLimit"]').prop('checked', false);
                $('input[name="couponUseDateLimit"]').val('');
            } else {
                $('#divCouponUsePeriodTypePeriod').hide();
                $('#divCouponUsePeriodTypeDay').show();
                // 설정기간 필드 초기화
                $('input[name="couponUsePeriodStartDate"]').val('');
                $('input[name="couponUsePeriodEndDate"]').val('');
            }
        });
        // 상품리스트, 상품상세 쿠폰발급설정 타입 변경 시
        $('[name="couponDisplayType"]').change(function (e) {
            if($('[name="couponDisplayType"]').val() == 'n') {
                $('#display-type-datepicker').css('display', 'none');
            } else {
                $('#display-type-datepicker').css('display', 'flex');
            }
        });

        changeCouponUseType();
        changeCouponSaveType();
        changeCouponKindType();
        changeCouponBenefitType();
        changeCouponApplyMemberGroup();
        changeCouponUseMemberGroup();
        changeCouponApplyProductType();
        changeCouponExceptProductType();
        changeCouponEventType();
        changeCouponProductMinOrderTypeText();

        <!--{ / }-->
    });

    function changeCouponEventType() {
        // 쿠폰 이벤트 타입에 따른 폼 변경
        const couponEventType = $('[name="couponEventType"]').val();
        const couponSaveType = $('input:radio[name="couponSaveType"]:checked').val();

        if (couponSaveType !== 'manual') {
            $('.couponEventType').toggle(couponEventType != 'joinEvent');
            $('.couponEventTypeJoinEvent').toggle(couponEventType == 'joinEvent');
        }

        if ($('input:radio[name="couponSaveType"]:checked').val() == 'auto') {
            $('#useMemberGroupTr').toggle(couponEventType != 'join' && couponEventType != 'joinEvent');
            $('#issueMemberGroupTr').toggle(couponEventType != 'join' && couponEventType != 'joinEvent');
        }

        const noticeArea = $('#couponSaveDuplicateNotice');
        const couponNotices = {
            birth: `'생일 축하 쿠폰'은 '재발급함'으로 설정 시 매년 회원의 생일마다 발급되며, 회원 아이디 당 1년에 1회만 발급됩니다.`
        };
        $('input[name="couponEventType"]').on('change', function () {
            const selectedValue = $(this).val();
            const notice = couponNotices[selectedValue];
            noticeArea.html(notice ? `<span class="notice-info">${notice}</span>` : '');
        });
    }

    // 사용구분에 따른 폼 변경
    function changeCouponUseType() {
        if ($('input:radio[name="couponUseType"]:checked').val() == 'product') {
            // 발급방식
            $('input:radio[name="couponSaveType"]:eq(0)').prop("disabled", false);
            $('input:radio[name="couponSaveType"]:eq(1)').prop("disabled", false);
            $('input:radio[name="couponSaveType"]:eq(2)').prop("disabled", false);
            $('#labelCouponSaveTypeManual').show();
            $('#labelCouponSaveTypeAuto').show();
            $('#labelCouponSaveTypeDown').show();
            // 혜택구분
            $('input:radio[name="couponKindType"]:eq(0)').prop("disabled", false);
            if ($('input:radio[name="couponKindType"]:checked').val() == 'delivery') {
                $('input:radio[name="couponKindType"]:eq(0)').prop("checked", true);
            }
            $('input:radio[name="couponKindType"]:eq(1)').prop("disabled", false);
            $('input:radio[name="couponKindType"]:eq(2)').prop("disabled", true);
            $('#labelCouponKindTypeSale').show();
            if ($('#labelCouponKindTypeSale').hasClass('radio-inline') == false) {
                $('#labelCouponKindTypeSale').addClass('radio-inline');
            }
            $('#labelCouponKindTypeAdd').show();
            if ($('#labelCouponKindTypeAdd').hasClass('radio-inline') == false) {
                $('#labelCouponKindTypeAdd').addClass('radio-inline');
            }
            $('#labelCouponKindTypeDelivery').hide();
            // 혜택금액종류
            $('select[name="couponBenefitType"] option:eq(0)').prop("disabled", false);
            $('select[name="couponBenefitType"] option:eq(1)').prop("disabled", false);
            $('select[name="couponBenefitType"]').show();
            // 제한조건 설정 - 구매관련보이기
            $('.tr_gift').show();
            // 쿠폰 적용 범위 설정
            $('.tr-apply-use').show();
            $('.tr-apply-use input').prop("disabled", false);
            // 쿠폰 제한 범위 설정
            $('.tr-except-use').show();
            $('.tr-except-use input').prop("disabled", false);
            // 혜택금액설정 문구 변경
            $('#couponBenefitTypeGroup').show();
            $('#couponMaxBenefitType').show();
            $('#benefitFixApply').show();
            $('#couponBenefitText').show();
            $('#couponBenefitUnit').hide();
            // 구매금액 기준 옵션 상품금액 OR 주문전체상품금액
            $('#couponProductMinOrderType').show();
            $('select[name="couponProductMinOrderType"]').removeClass('readonly').removeAttr('readonly');
            <!--{ ? productCouponChangeLimitVersionFl == true}-->
            if($('input:radio[name="couponProductMinOrderType"]:checked').val() == 'order') {
                $('.changeLimitVersion').show();
            } else {
                $('.changeLimitVersion').hide();
            }

        } else if ($('input:radio[name="couponUseType"]:checked').val() == 'order') {
            // 발급방식
            $('input:radio[name="couponSaveType"]:eq(0)').prop("disabled", false);
            $('input:radio[name="couponSaveType"]:eq(1)').prop("disabled", false);
            $('input:radio[name="couponSaveType"]:eq(2)').prop("disabled", true);
            if ($('input:radio[name="couponSaveType"]:checked').val() == 'down') {
                $('input:radio[name="couponSaveType"]:eq(2)').prop("checked", false);
                $('input:radio[name="couponSaveType"]:eq(0)').prop("checked", true);
            }
            $('#labelCouponSaveTypeManual').show();
            $('#labelCouponSaveTypeAuto').show();
            $('#labelCouponSaveTypeDown').hide();
            // 혜택구분
            $('input:radio[name="couponKindType"]:eq(0)').prop("disabled", false);
            if ($('input:radio[name="couponKindType"]:checked').val() == 'delivery') {
                $('input:radio[name="couponKindType"]:eq(0)').prop("checked", true);
            }
            $('input:radio[name="couponKindType"]:eq(1)').prop("disabled", false);
            $('input:radio[name="couponKindType"]:eq(2)').prop("disabled", true);
            $('#labelCouponKindTypeSale').show();
            if ($('#labelCouponKindTypeSale').hasClass('radio-inline') == false) {
                $('#labelCouponKindTypeSale').addClass('radio-inline');
            }
            $('#labelCouponKindTypeAdd').show();
            if ($('#labelCouponKindTypeAdd').hasClass('radio-inline') == false) {
                $('#labelCouponKindTypeAdd').addClass('radio-inline');
            }
            $('#labelCouponKindTypeDelivery').hide();
            // 혜택금액종류
            $('select[name="couponBenefitType"] option:eq(0)').prop("disabled", false);
            $('select[name="couponBenefitType"] option:eq(0)').prop("selected", true);
            $('select[name="couponBenefitType"] option:eq(1)').prop("disabled", true);
            $('select[name="couponBenefitType"]').hide();

            // 쿠폰 적용 / 제한 범위 초기화
            $('input:radio[name="couponApplyProductType"]:eq(0)').prop("checked", true);
            $('input:checkbox[name="couponExceptProviderType"]').prop("checked", false);
            $('input:checkbox[name="couponExceptCategoryType"]').prop("checked", false);
            $('input:checkbox[name="couponExceptBrandType"]').prop("checked", false);
            $('input:checkbox[name="couponExceptGoodsType"]').prop("checked", false);
            // 제한조건 설정 - 구매관련보이기
            $('.tr_gift').show();
            // 쿠폰 적용 범위 설정
            $('.tr-apply-use').hide();
            $('.tr-apply-use input').prop("disabled", true);
            // 쿠폰 제한 범위 설정
            $('.tr-except-use').hide();
            $('.tr-except-use input').prop("disabled", true);
            // 구매금액 기준 옵션 상품금액 OR 주문전체상품금액
            $('#couponProductMinOrderType').hide();
            // 주문적용쿠폰의 자동발급인 경우만 최소 상품 구매 금액 제한의 주문 금액을 고정
            $('select[name="couponProductMinOrderType"] option[value="order"]').prop("selected", true);
            $('select[name="couponProductMinOrderType"]').addClass('readonly').prop('readonly', true);
            // 혜택금액설정 문구 변경
            $('#couponBenefitTypeGroup').show();
            $('#benefitFixApply').hide();
            $('#couponBenefitText').show();
            $('#couponBenefitUnit').hide();

        } else if ($('input:radio[name="couponUseType"]:checked').val() == 'delivery') {
            // 발급방식
            $('input:radio[name="couponSaveType"]:eq(0)').prop("disabled", false);
            $('input:radio[name="couponSaveType"]:eq(1)').prop("disabled", false);
            $('input:radio[name="couponSaveType"]:eq(2)').prop("disabled", true);
            if ($('input:radio[name="couponSaveType"]:checked').val() == 'down') {
                $('input:radio[name="couponSaveType"]:eq(2)').prop("checked", false);
                $('input:radio[name="couponSaveType"]:eq(0)').prop("checked", true);
            }
            $('#labelCouponSaveTypeManual').show();
            $('#labelCouponSaveTypeAuto').show();
            $('#labelCouponSaveTypeDown').hide();
            // 혜택구분
            $('input:radio[name="couponKindType"]:eq(0)').prop("disabled", true);
            $('input:radio[name="couponKindType"]:eq(1)').prop("disabled", true);
            $('input:radio[name="couponKindType"]:eq(2)').prop("disabled", false);
            $('input:radio[name="couponKindType"]:eq(2)').prop("checked", true);
            $('#labelCouponKindTypeSale').hide();
            $('#labelCouponKindTypeSale').removeClass('radio-inline');
            $('#labelCouponKindTypeAdd').hide();
            $('#labelCouponKindTypeAdd').removeClass('radio-inline');
            $('#labelCouponKindTypeDelivery').show();
            if ($('#labelCouponKindTypeDelivery').hasClass('radio-inline') == false) {
                $('#labelCouponKindTypeDelivery').addClass('radio-inline');
            }
            // 지급 혜택 구분
            $('select[name="couponBenefitType"] option:eq(0)').prop("disabled", true);
            $('select[name="couponBenefitType"] option:eq(1)').prop("disabled", false);
            $('select[name="couponBenefitType"] option:eq(1)').prop("selected", true);
            $('select[name="couponBenefitType"]').hide();
            $('input:radio[name="couponBenefitType"][value="fix"]').prop('checked', true);

            // 쿠폰 적용 / 제한 범위 초기화
            $('input:radio[name="couponApplyProductType"]:eq(0)').prop("checked", true);
            $('input:checkbox[name="couponExceptProviderType"]').prop("checked", false);
            $('input:checkbox[name="couponExceptCategoryType"]').prop("checked", false);
            $('input:checkbox[name="couponExceptBrandType"]').prop("checked", false);
            $('input:checkbox[name="couponExceptGoodsType"]').prop("checked", false);
            // 제한조건 설정 - 구매관련보이기
            $('.tr_gift').show();
            // 쿠폰 적용 범위 설정
            $('.tr-apply-use').hide();
            $('.tr-apply-use input').prop("disabled", true);
            // 쿠폰 제한 범위 설정
            $('.tr-except-use').hide();
            $('.tr-except-use input').prop("disabled", true);
            // 혜택금액설정 문구 변경
            $('#couponBenefitTypeGroup').hide();
            $('#couponMaxBenefitType').hide();
            $('#benefitFixApply').hide();
            $('#couponBenefitUnitPercent').hide();
            $('#couponBenefitUnitFix').show();
            $('#couponBenefitText').hide();
            $('#couponBenefitUnit').show();
            // 구매금액 기준 옵션 상품금액 OR 주문전체상품금액
            $('#couponProductMinOrderType').hide();
            $('select[name="couponProductMinOrderType"] option[value="order"]').prop("selected", true);
            $('select[name="couponProductMinOrderType"]').addClass('readonly').prop('readonly', true);
        } else if ($('input:radio[name="couponUseType"]:checked').val() == 'gift') {
            // 발급방식
            $('input:radio[name="couponSaveType"]:eq(0)').prop("disabled", false);
            $('input:radio[name="couponSaveType"]:eq(1)').prop("disabled", false);
            $('input:radio[name="couponSaveType"]:eq(2)').prop("disabled", true);
            if ($('input:radio[name="couponSaveType"]:checked').val() == 'down') {
                $('input:radio[name="couponSaveType"]:eq(2)').prop("checked", false);
                $('input:radio[name="couponSaveType"]:eq(0)').prop("checked", true);
            }
            $('#labelCouponSaveTypeManual').show();
            $('#labelCouponSaveTypeAuto').show();
            $('#labelCouponSaveTypeDown').hide();
            // 혜택구분
            $('input:radio[name="couponKindType"]:eq(0)').prop("disabled", true);
            $('input:radio[name="couponKindType"]:eq(1)').prop("disabled", false);
            $('input:radio[name="couponKindType"]:eq(2)').prop("disabled", true);
            $('input:radio[name="couponKindType"]:eq(1)').prop("checked", true);
            $('#labelCouponKindTypeSale').hide();
            $('#labelCouponKindTypeSale').removeClass('radio-inline');
            $('#labelCouponKindTypeAdd').show();
            if ($('#labelCouponKindTypeAdd').hasClass('radio-inline') == false) {
                $('#labelCouponKindTypeAdd').addClass('radio-inline');
            }
            $('#labelCouponKindTypeDelivery').hide();
            // 지급 혜택 구분
            $('select[name="couponBenefitType"] option:eq(0)').prop("disabled", true);
            $('select[name="couponBenefitType"] option:eq(1)').prop("disabled", false);
            $('select[name="couponBenefitType"] option:eq(1)').prop("selected", true);
            $('select[name="couponBenefitType"]').hide();
            $('input:radio[name="couponBenefitType"][value="fix"]').prop('checked', true);

            // 쿠폰 적용 / 제한 범위 초기화
            $('input:radio[name="couponApplyProductType"]:eq(0)').prop("checked", true);
            $('input:checkbox[name="couponExceptProviderType"]').prop("checked", false);
            $('input:checkbox[name="couponExceptCategoryType"]').prop("checked", false);
            $('input:checkbox[name="couponExceptBrandType"]').prop("checked", false);
            $('input:checkbox[name="couponExceptGoodsType"]').prop("checked", false);
            // 제한조건 설정 - 구매관련숨기기
            $('.tr_gift').hide();
            // 쿠폰 적용 범위 설정
            $('.tr-apply-use').hide();
            $('.tr-apply-use input').prop("disabled", true);
            // 쿠폰 제한 범위 설정
            $('.tr-except-use').hide();
            $('.tr-except-use input').prop("disabled", true);
            // 혜택금액설정 문구 변경
            $('#couponBenefitTypeGroup').hide();
            $('#couponMaxBenefitType').hide();
            $('#benefitFixApply').hide();
            $('#couponBenefitUnitPercent').hide();
            $('#couponBenefitUnitFix').show();
            $('#couponBenefitText').hide();
            $('#couponBenefitUnit').show();
            // 구매금액 기준 옵션 상품금액 OR 주문전체상품금액
            $('#couponProductMinOrderType').hide();
        }
    }

    // 발급방식에 따른 폼 변경
    function changeCouponSaveType() {
        $('.tr_down').hide();
        $('.tr_down').find('input, select').prop("disabled", true);
        $('.tr_auto').hide();
        $('.tr_auto input').prop("disabled", true);
        $('.tr_manual').hide();
        $('.tr_manual input').prop("disabled", true);
        $('.label_memberdown').hide();
        $('.label_memberdown input').prop("disabled", true);
        if ($('input:radio[name="couponSaveType"]:checked').val() == 'down') {
            // 사용기간
            $('input:radio[name="couponUsePeriodType"]:eq(0)').prop("disabled", false);
            // 선택된 값이 없다면 기본값 설정
            if ($('input:radio[name="couponUsePeriodType"]:checked').length === 0) {
                $('input:radio[name="couponUsePeriodType"]:eq(0)').prop("checked", true);
            }
            $('.tr_down').show();
            $('.tr_down').find('input, select').prop("disabled", false);
            $('.label_memberdown').show();
            $('.label_memberdown input').prop("disabled", false);
            if ($('input:radio[name="couponUsePeriodType"]:checked').val() == 'period') {
                $('#divCouponUsePeriodTypePeriod').show();
                $('#divCouponUsePeriodTypeDay').hide();
            } else {
                $('#divCouponUsePeriodTypePeriod').hide();
                $('#divCouponUsePeriodTypeDay').show();
            }
            $('#titleLimitOption').show();
            $('#couponApplyMemberGroupTitle').html('발급/사용 가능 회원 등급');
            $('#couponApplyProductTypeTitle').html('쿠폰 발급/사용 가능 상품 범위 설정');
            $('#couponApplyProductTypeProviderTitle').html('발급/사용 가능 특정 공급사 선택');
            $('#couponApplyProductTypeCategoryTitle').html('발급/사용 가능 특정 카테고리 선택');
            $('#couponApplyProductTypeBrandTitle').html('발급/사용 가능 특정 브랜드 선택');
            $('#couponApplyProductTypeGoodsTitle').html('발급/사용 가능 특정 상품 선택');
            $('#couponExceptTypeTitle').html('쿠폰 발급/사용 제외 상품 범위 설정');
            $('#couponExceptTypeProviderTitle').html('발급/사용 제외 특정 공급사 선택');
            $('#couponExceptTypeCategoryTitle').html('발급/사용 제외 특정 카테고리 선택');
            $('#couponExceptTypeBrandTitle').html('발급/사용 제외 특정 브랜드 선택');
            $('#couponExceptTypeGoodsTitle').html('발급/사용 제외 특정 상품 선택');
            $('#useMemberGroupTr').hide();
            $('#couponUseMemberGroup').empty();
            $('#couponUseMemberGroup').removeClass('active');
            $('#selectUseMemberGroup').prop('disabled', true);
            if($('[name="couponDisplayType"]').val() == 'n') {
                $('#display-type-datepicker').hide();
            } else {
                $('#display-type-datepicker').show();
            }
        } else if ($('input:radio[name="couponSaveType"]:checked').val() == 'auto') {
            // 사용기간
            $('input:radio[name="couponUsePeriodType"]:eq(0)').prop("disabled", true);
            $('input:radio[name="couponUsePeriodType"]:eq(1)').prop("checked", true);
            $('.tr_auto').show();
            $('.tr_auto input').prop("disabled", false);
            $('#divCouponUsePeriodTypePeriod').hide();
            $('#divCouponUsePeriodTypeDay').show();
            $('#titleLimitOption').show();
            $('#couponApplyMemberGroupTitle').html('발급 가능 회원 등급');
            $('#couponApplyProductTypeTitle').html('쿠폰 사용 가능 상품 범위 설정');
            $('#couponApplyProductTypeProviderTitle').html('사용 가능 특정 공급사 선택');
            $('#couponApplyProductTypeCategoryTitle').html('사용 가능 특정 카테고리 선택');
            $('#couponApplyProductTypeBrandTitle').html('사용 가능 특정 브랜드 선택');
            $('#couponApplyProductTypeGoodsTitle').html('사용 가능 특정 상품 선택');
            $('#couponExceptTypeTitle').html('쿠폰 사용 제외 설정');
            $('#couponExceptTypeProviderTitle').html('사용 제외 특정 공급사 선택');
            $('#couponExceptTypeCategoryTitle').html('사용 제외 특정 카테고리 선택');
            $('#couponExceptTypeBrandTitle').html('사용 제외 특정 브랜드 선택');
            $('#couponExceptTypeGoodsTitle').html('사용 제외 특정 상품 선택');
            $('#useMemberGroupTr').show();
            
            // 자동발급이벤트 선택 시 이벤트 그룹 표시/숨기기
            if (typeof window.updateCouponEventGroupUI === 'function') {
                window.updateCouponEventGroupUI();
            }
        } else if ($('input:radio[name="couponSaveType"]:checked').val() == 'manual') {
            // 사용기간
            $('input:radio[name="couponUsePeriodType"]:eq(0)').prop("disabled", false);
            // 선택된 값이 없다면 기본값 설정
            if ($('input:radio[name="couponUsePeriodType"]:checked').length === 0) {
                $('input:radio[name="couponUsePeriodType"]:eq(0)').prop("checked", true);
            }
            $('.tr_manual').show();
            $('.tr_manual input').prop("disabled", false);
            if ($('input:radio[name="couponUsePeriodType"]:checked').val() == 'period') {
                $('#divCouponUsePeriodTypePeriod').show();
                $('#divCouponUsePeriodTypeDay').hide();
            } else {
                $('#divCouponUsePeriodTypePeriod').hide();
                $('#divCouponUsePeriodTypeDay').show();
            }
            if ($('input:radio[name="couponUseType"]:checked').val() == 'gift') {
                $('#titleLimitOption').hide();
            } else {
                $('#titleLimitOption').show();
            }
            $('#couponApplyMemberGroupTitle').html('발급 가능 회원 등급');
            $('#couponApplyProductTypeTitle').html('쿠폰 사용 가능 상품 범위 설정');
            $('#couponApplyProductTypeProviderTitle').html('사용 가능 특정 공급사 선택');
            $('#couponApplyProductTypeCategoryTitle').html('사용 가능 특정 카테고리 선택');
            $('#couponApplyProductTypeBrandTitle').html('사용 가능 특정 브랜드 선택');
            $('#couponApplyProductTypeGoodsTitle').html('사용 가능 특정 상품 선택');
            $('#couponExceptTypeTitle').html('쿠폰 사용 제외 설정');
            $('#couponExceptTypeProviderTitle').html('사용 제외 특정 공급사 선택');
            $('#couponExceptTypeCategoryTitle').html('사용 제외 특정 카테고리 선택');
            $('#couponExceptTypeBrandTitle').html('사용 제외 특정 브랜드 선택');
            $('#couponExceptTypeGoodsTitle').html('사용 제외 특정 상품 선택');
        }

        // 자동발급이 아닌 경우 이벤트/SMS 필드 초기화
        if ($('input:radio[name="couponSaveType"]:checked').val() != 'auto') {
            $('[name="couponEventType"]').val('');
            $('[name="couponEventFirstSmsType"][value="n"]').prop('checked', true);
            $('[name="couponEventOrderSmsType"][value="n"]').prop('checked', true);
            $('[name="couponEventBirthSmsType"][value="n"]').prop('checked', true);
            $('[name="couponEventMemberSmsType"][value="n"]').prop('checked', true);
            $('[name="couponEventAttendanceSmsType"][value="n"]').prop('checked', true);
            $('[name="couponEventMemberModifySmsType"][value="n"]').prop('checked', true);
            $('[name="couponEventWakeSmsType"][value="n"]').prop('checked', true);
            $('[name="couponEventOrderFirstType"]').prop('checked', false);
        }

        setCouponAmountEnabledByType();
        bindCouponAmountTypeEvents();
    }

    // 지급 혜택 구분에 따른 폼 변경
    function changeCouponKindType() {
        if ($('input:radio[name="couponKindType"]:checked').val() == 'add') {
            if ($('input:radio[name="couponUseType"]:checked').val() == 'product') {
                $('.benefit-text').text('적립');
            } else {
                if ($('select[name="couponBenefitType"]').val() == 'percent') {
                    $('.benefit-text').text('적립');
                } else {
                    $('.benefit-text').text('적립');
                }
            }

            if ($('input:radio[name="couponUseType"]:checked').val() == 'gift') {
                $('#couponBenefitUnit').text(' 적립');
            }
        } else {
            if ($('input:radio[name="couponUseType"]:checked').val() == 'product') {
                $('.benefit-text').text('할인');
            } else {
                if ($('select[name="couponBenefitType"]').val() == 'percent') {
                    $('.benefit-text').text('할인');
                } else {
                    $('.benefit-text').text('할인');
                }
            }
            
            if ($('input:radio[name="couponUseType"]:checked').val() == 'delivery') {
                $('#couponBenefitUnit').text(' 할인');
            }
        }
    }

    // 혜택금액종류에 따른 폼 변경
    function changeCouponBenefitType(shouldResetValue = false) {
        if ($('input:radio[name="couponBenefitType"]:checked').val() == 'percent') {
            $('input:checkbox[name="couponMaxBenefitType"]').prop("disabled", false);
            $('input:text[name="couponMaxBenefit"]').prop("disabled", false);
            $('input:text[name="couponBenefit"]').removeAttr('maxlength');
            $('input:text[name="couponBenefit"]').attr('maxlength', '3');
            if (shouldResetValue) {
                $('input:text[name="couponBenefit"]').val('');
            }
            if ($('input:radio[name="couponUseType"]:checked').val() == 'gift' 
            || $('input:radio[name="couponUseType"]:checked').val() == 'delivery') {
                $('input:text[name="couponBenefit"]').attr('placeholder', '금액 입력');
            } else {
                $('input:text[name="couponBenefit"]').attr('placeholder', '할인율 입력');
            }
            $('#benefittype_text').show();
            $('.div-benefit').show();
            if ($('input:radio[name="couponUseType"]:checked').val() == 'product' 
            || $('input:radio[name="couponUseType"]:checked').val() == 'order') {
                $('#couponBenefitUnitPercent').show();
                $('#couponBenefitUnitFix').hide();
                $('#couponMaxBenefitType').show();
            }
            $('#benefitFixApply').hide();
            $('input:checkbox[name="couponBenefitFixApply"]').prop("checked", false);
        } else {
            $('input:checkbox[name="couponMaxBenefitType"]').prop("disabled", true).prop("checked", false);
            $('input:text[name="couponMaxBenefit"]').prop("disabled", true).val('');
            $('input:text[name="couponBenefit"]').removeAttr('maxlength');
            $('input:text[name="couponBenefit"]').attr('maxlength', '8');
            if (shouldResetValue) {
                $('input:text[name="couponBenefit"]').val('');
            }
            $('input:text[name="couponBenefit"]').attr('placeholder', '금액 입력');
            $('#benefittype_text').hide();
            $('.div-benefit').hide();
            $('#couponMaxBenefitType').hide();
            $('#couponBenefitUnitPercent').hide();
            $('#couponBenefitUnitFix').show();
            if ($('input:radio[name="couponUseType"]:checked').val() == 'product') {
                $('#benefitFixApply').show();
            }
        }
    }

    // 쿠폰 발급/사용 가능, 제외 설정시 상세항목이 없으면 default 처리
    function checkCouponApplyExceptType() {
        var applyType = $('input:radio[name="couponApplyProductType"]:checked').val();

        $('input:checkbox[name^="couponExcept"]:checked').each(function () {
            if ($(this).attr('name') === 'couponExceptGoodsType') {
                if ($('#' + $(this).attr('name').replace('Type', '') + ' tbody').children().size() === 0) {
                    $(this).prop('checked', false);
                }
            } else {
                if ($('#' + $(this).attr('name').replace('Type', '')).children().size() === 0) {
                    $(this).prop('checked', false);
                }
            }
        });

        if (applyType != 'all') {
            if (applyType === 'goods') {
                if ($('.tr-apply-' + applyType + ' td table tbody').children().size() === 0) {
                    $('input:radio[name="couponApplyProductType"][value="all"]').prop('checked', true);
                }
            } else {
                if ($('.tr-apply-' + applyType + ' td div').children().size() === 0) {
                    $('input:radio[name="couponApplyProductType"][value="all"]').prop('checked', true);
                }
            }
        }
    }

    // 쿠폰 발급 가능 회원등급 선택
    function changeCouponApplyMemberGroup() {
        if ($('input:radio[name="applyMemberGroup"]:checked').val() == 'all') {
            $('#couponApplyMemberGroup, #member_groupLayer_apply_member').empty();
            $('#couponApplyMemberGroup').removeClass('active');
            $('#selectApplyMemberGroup').prop('disabled', true);
            $('input:checkbox[name="couponApplyMemberGroupDisplayType"]').prop('disabled', true);
        } else {
            $('#selectApplyMemberGroup').prop('disabled', false);
            $('input:checkbox[name="couponApplyMemberGroupDisplayType"]').prop('disabled', false);
        }
    }

    // 쿠폰 발급 가능 회원등급 선택
    function changeCouponUseMemberGroup() {
        if ($('input:radio[name="useMemberGroup"]:checked').val() == 'all') {
            $('#couponUseMemberGroup, #member_groupLayer_use_member').empty();
            $('#couponUseMemberGroup').removeClass('active');
            $('#selectUseMemberGroup').prop('disabled', true);
        } else {
            $('#selectUseMemberGroup').prop('disabled', false);
        }
    }

    // 쿠폰 발급/사용 가능 범위 설정에 따른 폼 변경
    function changeCouponApplyProductType() {
        if ($('input:radio[name="couponApplyProductType"]:checked').val() == 'all') {
            $('.tr-apply-provider').hide();
            $('.tr-apply-category').hide();
            $('.tr-apply-brand').hide();
            $('.tr-apply-goods').hide();
            $('input:checkbox[name="couponExceptProviderType"]').prop("disabled", false);
            $('input:checkbox[name="couponExceptCategoryType"]').prop("disabled", false);
            $('input:checkbox[name="couponExceptBrandType"]').prop("disabled", false);
            $('input:checkbox[name="couponExceptGoodsType"]').prop("disabled", false);
        } else if ($('input:radio[name="couponApplyProductType"]:checked').val() == 'provider') {
            $('.tr-apply-provider').show();
            $('.tr-apply-category').hide();
            $('.tr-apply-brand').hide();
            $('.tr-apply-goods').hide();
            $('input:checkbox[name="couponExceptProviderType"]').prop("disabled", true);
            $('input:checkbox[name="couponExceptProviderType"]').prop("checked", false);
            $('input:checkbox[name="couponExceptCategoryType"]').prop("disabled", false);
            $('input:checkbox[name="couponExceptBrandType"]').prop("disabled", false);
            $('input:checkbox[name="couponExceptGoodsType"]').prop("disabled", false);
        } else if ($('input:radio[name="couponApplyProductType"]:checked').val() == 'category') {
            $('.tr-apply-provider').hide();
            $('.tr-apply-category').show();
            $('.tr-apply-brand').hide();
            $('.tr-apply-goods').hide();
            $('input:checkbox[name="couponExceptProviderType"]').prop("disabled", false);
            $('input:checkbox[name="couponExceptCategoryType"]').prop("disabled", true);
            $('input:checkbox[name="couponExceptCategoryType"]').prop("checked", false);
            $('input:checkbox[name="couponExceptBrandType"]').prop("disabled", false);
            $('input:checkbox[name="couponExceptGoodsType"]').prop("disabled", false);
        } else if ($('input:radio[name="couponApplyProductType"]:checked').val() == 'brand') {
            $('.tr-apply-provider').hide();
            $('.tr-apply-category').hide();
            $('.tr-apply-brand').show();
            $('.tr-apply-goods').hide();
            $('input:checkbox[name="couponExceptProviderType"]').prop("disabled", false);
            $('input:checkbox[name="couponExceptCategoryType"]').prop("disabled", false);
            $('input:checkbox[name="couponExceptBrandType"]').prop("disabled", true);
            $('input:checkbox[name="couponExceptBrandType"]').prop("checked", false);
            $('input:checkbox[name="couponExceptGoodsType"]').prop("disabled", false);
        } else if ($('input:radio[name="couponApplyProductType"]:checked').val() == 'goods') {
            // 쿠폰적용제한범위설정에 따른 폼 변경
            $('.tr-apply-provider').hide();
            $('.tr-apply-category').hide();
            $('.tr-apply-brand').hide();
            $('.tr-apply-goods').show();
            $('input:checkbox[name="couponExceptProviderType"]').prop("disabled", false);
            $('input:checkbox[name="couponExceptCategoryType"]').prop("disabled", false);
            $('input:checkbox[name="couponExceptBrandType"]').prop("disabled", false);
            $('input:checkbox[name="couponExceptGoodsType"]').prop("disabled", true);
            $('input:checkbox[name="couponExceptGoodsType"]').prop("checked", false);
        }
        changeCouponExceptProductType();
    }

    // 쿠폰 발급/사용 제외 설정에 따른 폼 변경
    function changeCouponExceptProductType() {
        if ($('input:checkbox[name="couponExceptGoodsType"]').prop("checked") == true) {
            $('.tr-except-goods').show();
        } else {
            $('.tr-except-goods').hide();
        }
        if ($('input:checkbox[name="couponExceptBrandType"]').prop("checked") == true) {
            $('.tr-except-brand').show();
        } else {
            $('.tr-except-brand').hide();
        }
        if ($('input:checkbox[name="couponExceptCategoryType"]').prop("checked") == true) {
            $('.tr-except-category').show();
        } else {
            $('.tr-except-category').hide();
        }
        if ($('input:checkbox[name="couponExceptProviderType"]').prop("checked") == true) {
            $('.tr-except-provider').show();
        } else {
            $('.tr-except-provider').hide();
        }
    }

    /**
     * 구매 상품 범위 등록 / 예외 등록 Ajax layer
     *
     * @param string codeStr 타입
     * @param string modeStr 예외 여부
     */
    function layer_register(codeStr, modeStr, isDisabled) {
        var layerFormID = 'couponRangeForm';
        var addParam = '';
        var fileStr = '';
        if (typeof modeStr == 'undefined') {
            // 레이어 창
            var parentFormID = 'couponApply' + codeStr;
            var dataFormID = 'id' + codeStr;
            var dataInputNm = 'couponApply' + codeStr;
            var layerTitle = '쿠폰 적용 ';
        } else if (modeStr == 'use') {
            var parentFormID = 'coupon' + codeStr;
            var dataFormID = 'id' + codeStr;
            var dataInputNm = 'coupon' + codeStr;
            var layerTitle = '쿠폰 사용 ';
        } else {
            var parentFormID = 'couponExcept' + codeStr;
            var dataFormID = 'idExcept' + codeStr;
            var dataInputNm = 'couponExcept' + codeStr;
            var layerTitle = '쿠폰 제외 ';
        }

        if (codeStr == 'MemberGroup') {
            layerTitle = layerTitle + '회원등급';

            fileStr = 'member_group';
            mode = 'search';
            $("#" + parentFormID + " thead").show();
            $("#" + parentFormID + " tfoot").show();
        }
        if (codeStr == 'UseMemberGroup') {
            layerTitle = layerTitle + '회원등급';

            fileStr = 'member_group';
            mode = 'search';
            $("#" + parentFormID + " thead").show();
            $("#" + parentFormID + " tfoot").show();
        }
        if (codeStr == 'Goods') {
            layerTitle = layerTitle + '상품';
            fileStr = 'goods';
            mode = 'simple';
            $("#" + parentFormID + " thead").show();
            $("#" + parentFormID + " tfoot").show();
        }
        if (codeStr == 'Category') {
            layerTitle = layerTitle + '카테고리';
            fileStr = 'category';
            mode = 'search';
            $("#" + parentFormID + " thead").show();
            $("#" + parentFormID + " tfoot").show();
        }
        if (codeStr == 'Brand') {
            layerTitle = layerTitle + '브랜드';
            fileStr = 'brand';
            mode = 'search';
            $("#" + parentFormID + " thead").show();
            $("#" + parentFormID + " tfoot").show();
        }
        if (codeStr == 'Provider') {
            layerTitle = layerTitle + '공급사';
            fileStr = 'scm';
            isDisabled = 'disabled';
            mode = 'search';
            $("#" + parentFormID + " thead").show();
            $("#" + parentFormID + " tfoot").show();
        }

        var addParam = {
            "mode": mode,
            "layerFormID": layerFormID,
            "parentFormID": parentFormID,
            "dataFormID": dataFormID,
            "dataInputNm": dataInputNm,
            "layerTitle": layerTitle,
            "disabled": isDisabled,
            //            "callFunc": "",
        };

        layer_add_info(fileStr, addParam);
    }

    /**
     * 출석체크, 회원정보수정 이벤트 신규쿠폰 등록 시 등록 후 호출되는 함수
     *
     * @param string couponEventType 자동발급쿠폰 종류
     */
    function unload_callback(couponEventType) {
        <?php
        if(gd_isset($callback, '') != ''){?>
        var callback = window.opener.<?=$callback?>;
        if ($.isFunction(callback)) {
            callback(couponEventType);
        }
        <?php }
        ?>
    }

     // 여신전문금융업법 안내
     function lawAlert() {
        var message = '';
        message += '<b style="color: #0070c0;">제19조(가맹점의 준수사항)</b><br/>';
        message += '① 신용카드가맹점은 신용카드로 거래한다는 이유로 신용카드 결제를 거절하거나 신용카드회원을 불리하게 대우하지 못한다.<br/>';
        message += '④ 신용카드가맹점은 가맹점수수료를 신용카드회원이 부담하게 하여서는 아니 된다.<br/><br/>';
        message += '<b style="color: #0070c0;">제70조(벌칙)</b><br/>';
        message += '④ 다음 각 호의 어느 하나에 해당하는 자는 1년 이하의 징역 또는 1천만원 이하의 벌금에 처한다.<br/>';
        message += '4. 제19조제1항을 위반하여 신용카드로 거래한다는 이유로 물품의 판매 또는 용역의 제공 등을 거절하거나 신용카드회원을 불리하게 대우한 자<br/>';
        message += '5. 제19조제4항을 위반하여 가맹점수수료를 신용카드회원이 부담하게 한 자<br/>';

        NCDSAlert({ message: '여신전문금융업법 안내', subMessage: message });
    }

    /**
     * "최소 상품 구매 금액 제한" 변경 시 텍스트 및 입력 필드 표시 토글 기능 초기화
     * @function changeCouponProductMinOrderTypeText
     */
    function changeCouponProductMinOrderTypeText() {
        const minOrderSelect = document.querySelector('select[name="couponProductMinOrderType"]');
        const minOrderTypeText = document.getElementById('couponProductMinOrderTypeText');
        const minOrderPriceInput = document.getElementById('couponProductMinOrderPriceInput');

        const getOrderTypeText = (value) => {
            if (value === 'product') {
                return '구매금액이';
            } else if (value === 'order') {
                return '기준이';
            }
            
            return '';
        };

        const updateInputVisibility = (value) => {
            if (minOrderPriceInput) {
                if (value === 'product' || value === 'order') {
                    minOrderPriceInput.style.display = '';
                } else {
                    minOrderPriceInput.style.display = 'none';
                    // 제한없음 선택 시 금액 초기화
                    const priceInput = minOrderPriceInput.querySelector('input[name="couponMinOrderPrice"]');
                    if (priceInput) priceInput.value = '';
                }
            }
        };

        if (minOrderSelect) {
            const value = minOrderSelect.value;
            if (minOrderTypeText) {
                minOrderTypeText.textContent = getOrderTypeText(value);
            }
            updateInputVisibility(value);
        }
    };

    $('input[name="couponAmount"]').change(function (e) {
        if ($('input[name="couponSaveDuplicateLimit"]').val() != '' && parseInt($('input[name="couponAmount"]').val(), 0) < parseInt($('input[name="couponSaveDuplicateLimit"]').val(), 0)) {
            $('input[name="couponSaveDuplicateLimit"]').val($('input[name="couponAmount"]').val());
        }
    });
    $('input[name="couponSaveDuplicateLimit"]').change(function (e) {
        // 무제한일 경우 정상 저장 처리
        if ($('input[name="couponAmountType"]:checked').val() == 'n') {
            return;
        }

        if ($('input[name="couponAmount"]').val() == '') {
            $('input[name="couponSaveDuplicateLimit"]').val('');
            NCDSAlert({ message: '먼저 전체 발급수량 항목에서 최대 장수를 입력해주셔야합니다.', iconType: 'error' });
        } else {
            if (parseInt($('input[name="couponAmount"]').val(), 0) < parseInt($('input[name="couponSaveDuplicateLimit"]').val(), 0)) {
                $('input[name="couponSaveDuplicateLimit"]').val($('input[name="couponAmount"]').val());
            }
        }
    });

    /**
     * 전체 발급 수량 타입에 따른 초기화
     */
    function setCouponAmountEnabledByType() {
        const radios = document.querySelectorAll('input[name="couponAmountType"]');
        const amountInput = document.querySelector('input[name="couponAmount"]');
        const container = amountInput?.closest('.ncua-input.ncua-input--xs.ncua-text-input-unit');
        const selected = Array.from(radios).find(radio => radio.checked)?.value;
        const isManual = document.querySelector('input[name="couponSaveType"]:checked')?.value === 'manual';

        if (amountInput) {
            amountInput.disabled = selected !== 'y' || isManual;
            // 무제한 선택 시 수량 초기화
            if (selected !== 'y') {
                amountInput.value = '';
            }
        }

        if (container) {
            container.classList.toggle('is-disabled', selected !== 'y');
        }
    }

    function bindCouponAmountTypeEvents() {
        const radios = document.querySelectorAll('input[name="couponAmountType"]');
        radios.forEach(radio => {
            radio.removeEventListener('click', setCouponAmountEnabledByType);
            radio.addEventListener('click', setCouponAmountEnabledByType);
        });
    }

    function isValidDate(dateString) {
        return dateString && !dateString.startsWith('0000-00-00');
    }
</script>

<script>
document.addEventListener('DOMContentLoaded', () => {
    try {
        const MAX_UPLOAD_COUNT = <?=$maxUploadCount?>;
        const MAX_UPLOAD_SIZE = <?=$maxUploadSize?>;

        /**
         * 파일 업로드 검증 함수
         * @param {File[]} newFiles - 새로 추가할 파일 배열
         * @param {number} maxSize - 최대 파일 크기 (MB)
         * @returns {{valid: boolean, message: string}} 검증 결과
         */
        const validateFiles = (newFiles, maxSize) => {
            // 파일 크기 검증
            const maxSizeMB = maxSize * 1024 * 1024;
            const oversizedFile = newFiles.find(file => file?.size > maxSizeMB);

            if (oversizedFile) {
                return {
                    valid: false,
                    message: `파일 "${oversizedFile.name}"의 크기가 최대 허용 크기(${maxSize}MB)를 초과합니다.`
                };
            }

            return {
                valid: true,
            };
        }

        // 파일 선택 시 FileInput 설정
        const setInputFile = files => {
            const fileInput = document.querySelector('input[name="couponImage"]');
            if (!fileInput) return;

            const dataTransfer = new DataTransfer();

            if (files && files.length > 0) {
                dataTransfer.items.add(files[0]);
            }
            
            fileInput.files = dataTransfer.files;
        };

        // 쿠폰 노출 이미지 ImageFileInput 초기화
        const couponImageType = document.querySelector('input[name="couponImageType"]:checked')?.value ?? '';
        const imageFileInput = new ncua.ImageFileInput({
            container: 'image-file-input-container',
            buttonLabel : '파일 찾기',
            maxFileCount: MAX_UPLOAD_COUNT,
            accept : 'image/*',
            hintItems: ['권장 사이즈 : 90x42 pixel', '권장 파일 형식 : png, jpg, jpeg, gif, bmp, svg'],
            disabled: couponImageType === 'basic' ? true : false,
            onChange: (newFiles) => {
                // 파일 검증
                const validation = validateFiles(newFiles, MAX_UPLOAD_SIZE);
                if (!validation.valid) {
                    NCDSAlert({message: validation.message, iconType: 'error'});
                    return;
                }

                // 이미지 미리보기 렌더링
                imageFileInput.renderImagePreviews();

                setInputFile(newFiles);
            },
        });

        // 초기 이미지가 있으면 설정
        <?php if ($couponData['couponImage']) { ?>
            imageFileInput.setFiles([
                {
                    fileName: '',
                    fileImageUrl: '<?php echo $couponData['couponImage']; ?>'
                }
            ]);
        <?php } ?>

        // couponImageType 클릭 시 FileInput 비활성화/활성화
        const couponImageGroup = document.querySelector('.ncua-coupon-image__group');
        if (!couponImageGroup) return;

        couponImageGroup.addEventListener('change', (e) => {
            if (!e.target.matches('input[name="couponImageType"]')) return;

            const couponImageType = e.target.value;
            const couponImageInput = document.querySelector('input[name="couponImage"]');
            const isBasic = couponImageType === 'basic';
            
            imageFileInput.setDisabled(isBasic);
            if (couponImageInput) {
                couponImageInput.disabled = isBasic;
            }
        });
    } catch {
        // ignore
    }

    try {
        // 사용 기간: 설정 기간 기준으로 쿠폰 발급 기간을 설정하는 DatePicker
        const configPeriodDatePicker = new ncua.DatePicker(document.querySelector('#config-period-datepicker'), {
            size: 'xs', 
            datePickerOptions: [
                {
                element: 'start-date',
                attrName: 'couponUsePeriodStartDate',
                options: {
                    mode: 'single',
                    static: true,
                    defaultDate: null,
                    dateFormat: 'Y-m-d H:i:S',
                    enableTime: true,
                    enableSeconds: true,
                    time_24hr: true,
                    clickOpens: true,
                    allowInvalidPreload: true,
                    locale: 'ko',
                    disableMobile: true
                    },
                },
                {
                element: 'end-date',
                attrName: 'couponUsePeriodEndDate',
                options: {
                    mode: 'single',
                    static: true,
                    defaultDate: null,
                    dateFormat: 'Y-m-d H:i:S',
                    enableTime: true,
                    enableSeconds: true,
                    time_24hr: true,
                    clickOpens: true,
                    allowInvalidPreload: true,
                    locale: 'ko',
                    disableMobile: true
                    },
                },
            ],
        });

        const configStartDate = "<?= $couponData['couponUsePeriodStartDate'] ?>";
        const configEndDate = "<?= $couponData['couponUsePeriodEndDate'] ?>";

        if (isValidDate(configStartDate) && isValidDate(configEndDate)) {
            configPeriodDatePicker.setDate([configStartDate, configEndDate]);
        }
    } catch {
        // ignore
    }

    try {
        // 사용 기간: 쿠폰발급일 기준으로으 쿠폰 발급 기간을 설정하는 DatePicker
        const endPeriodDatePickerContainer = document.querySelector('#end-period-datepicker');
        const endPeriodDatePicker = new ncua.DatePicker(endPeriodDatePickerContainer, {
            size: 'xs', 
            datePickerOptions: [
                {
                element: 'date',
                attrName: 'couponUseDateLimit',
                options: {
                    mode: 'single',
                    static: true,
                    defaultDate: null,
                    dateFormat: 'Y-m-d H:i:S',
                    enableTime: true,
                    enableSeconds: true,
                    time_24hr: true,
                    clickOpens: true,
                    allowInvalidPreload: true,
                    locale: 'ko',
                    disableMobile: true
                    },
                }
            ],
        });

        const useDateLimit = "<?= $couponData['couponUseDateLimit'] ?>";
        if (isValidDate(useDateLimit)) {
            endPeriodDatePicker.setDate([useDateLimit]);
        }

        // 종료일 제한 설정에 따른 couponUseDateLimit submit 포함/미포함
        const syncCouponUsePeriodDayLimitDisabled = () => {
            const checkbox = document.querySelector('input[name="couponUsePeriodDayLimit"]');
            const ncuaDatePicker = endPeriodDatePickerContainer.querySelector('.ncua-date-picker');
            const isChecked = !!(checkbox && checkbox.checked);

            document.querySelectorAll('[name="couponUseDateLimit"]').forEach((el) => {
                el.disabled = !isChecked;
                ncuaDatePicker.classList.toggle('ncua-date-picker--disabled', !isChecked);
            });
        };

        syncCouponUsePeriodDayLimitDisabled();
        
        document.querySelectorAll('input[name="couponUsePeriodDayLimit"]').forEach((el) => {
            el.addEventListener('change', syncCouponUsePeriodDayLimitDisabled);
        });
    } catch {
        // ignore
    }

    try {
        // 상품리스트, 상품상세 쿠폰발급설정 DatePicker
        const couponDisplayDatePicker = new ncua.DatePicker(document.querySelector('#display-type-datepicker'), {
            size: 'xs', 
            datePickerOptions: [
                {
                element: 'start-date',
                attrName: 'couponDisplayStartDate',
                options: {
                    mode: 'single',
                    static: true,
                    defaultDate: null,
                    dateFormat: 'Y-m-d H:i:S',
                    enableTime: true,
                    enableSeconds: true,
                    time_24hr: true,
                    clickOpens: true,
                    allowInvalidPreload: true,
                    locale: 'ko',
                    disableMobile: true
                    },
                },
                {
                element: 'end-date',
                attrName: 'couponDisplayEndDate',
                options: {
                    mode: 'single',
                    static: true,
                    defaultDate: null,
                    dateFormat: 'Y-m-d H:i:S',
                    enableTime: true,
                    enableSeconds: true,
                    time_24hr: true,
                    clickOpens: true,
                    allowInvalidPreload: true,
                    locale: 'ko',
                    },
                },
            ],
        });

        const couponDisplayStartDate = "<?= $couponData['couponDisplayStartDate'] ?>";
        const couponDisplayEndDate = "<?= $couponData['couponDisplayEndDate'] ?>";

        if (isValidDate(couponDisplayStartDate) && isValidDate(couponDisplayEndDate)) {
            couponDisplayDatePicker.setDate([couponDisplayStartDate, couponDisplayEndDate]);
        }
        
        // DatePicker 초기화 후, 상품리스트, 상품상세 쿠폰발급설정 submit 포함/미포함
        const syncCouponDisplayDisabled = () => {
            const checked = document.querySelector('input[name="couponSaveType"]:checked');
            const isDown = checked && checked.value === 'down';

            document.querySelectorAll('[name="couponDisplayStartDate"], [name="couponDisplayEndDate"]').forEach((el) => {
                el.disabled = !isDown;
            });
        };

        syncCouponDisplayDisabled();


        document.querySelectorAll('input[name="couponSaveType"]').forEach((el) => {
            el.addEventListener('change', syncCouponDisplayDisabled);
        });
    } catch {
        // ignore
    }

    try {
        // 쿠폰명, 쿠폰설명 글자수 카운트 기능 초기화
        const charCountManager = createCharCountManager({
            targetClasses: ['ncua-coupon__name', 'ncua-coupon__description'],
        });
        charCountManager.init();
    } catch {
        // ignore
    }

    try {
        // 카테고리 선택/삭제/체크 기능 초기화
        const categoryManager = createCheckboxManager({
            tableBodyId: 'couponApplyCategory',
            deleteBtnId: 'deleteSelectedCategorys',
            allCheckId: 'categoryAllCheck',
            noDataMessage: '선택된 카테고리가 없습니다.',
            noDataColspan: 3
        });
        categoryManager.init();
    
        // 브랜드 선택/삭제/체크 기능 초기화
        const brandManager = createCheckboxManager({
            tableBodyId: 'couponApplyBrand',
            deleteBtnId: 'deleteSelectedBrands',
            allCheckId: 'brandAllCheck',
            noDataMessage: '선택된 브랜드가 없습니다.',
            noDataColspan: 3
        });
        brandManager.init();
    
        // 상품 선택/삭제/체크 기능 초기화
        const goodsManager = createCheckboxManager({
            tableBodyId: 'couponApplyGoods',
            deleteBtnId: 'deleteSelectedGoods',
            allCheckId: 'goodsAllCheck',
            noDataMessage: '선택된 상품이 없습니다.',
            noDataColspan: 4
        });
        goodsManager.init();
    
        // 제외 카테고리 선택/삭제 기능 초기화
        const exceptCategoryManager = createCheckboxManager({
            tableBodyId: 'couponExceptCategory',
            deleteBtnId: 'deleteSelectExceptCategorys',
            allCheckId: 'exceptCategoryAllCheck',
            noDataMessage: '선택된 카테고리가 없습니다.',
            noDataColspan: 3
        });
        exceptCategoryManager.init();
    
        // 제외 브랜드 선택/삭제 기능 초기화
        const exceptBrandManager = createCheckboxManager({
            tableBodyId: 'couponExceptBrand',
            deleteBtnId: 'deleteSelectExceptBrands',
            allCheckId: 'exceptBrandAllCheck',
            noDataMessage: '선택된 브랜드가 없습니다.',
            noDataColspan: 3
        });
        exceptBrandManager.init();
    
        // 제외 상품 선택/삭제 기능 초기화
        const exceptGoodsManager = createCheckboxManager({
            tableBodyId: 'couponExceptGoods',
            deleteBtnId: 'deleteSelectExceptGoods',
            allCheckId: 'exceptGoodsAllCheck',
            noDataMessage: '선택된 상품이 없습니다.',
            noDataColspan: 4
        });
        exceptGoodsManager.init();
    } catch {
        // ignore
    }

    try {
        /**
         * 회원등급 ComboBox 초기화
         */
        const initMemberGroupComboBoxes = () => {
            if (typeof initMemberGroupComboBox === 'undefined') {
                setTimeout(initMemberGroupComboBoxes, 100);
                return;
            }
    
            const memberGroupComboBoxes = {};
    
            const comboBoxApplyMember = initMemberGroupComboBox({
                comboboxId: 'layer_member_group_apply_member_combobox',
                parentLayerId: 'member_groupLayer_apply_member',
                dataInputNm: 'couponApplyMemberGroup',
                dataFormID: 'info_member_apply_member',
                pageCountVar: 'comboBoxMemberGroupApplyMemberPageCount',
                keywordVar: 'comboBoxMemberGroupApplyMemberKeyword',
                labelText: '',
                tagClass: 'coupon-apply-member-tag ncua-select-group-tag',
                apiUrl: '/share/ncds/layer_member_group.php'
            });

            if (comboBoxApplyMember) {
                memberGroupComboBoxes['layer_member_group_apply_member_combobox'] = comboBoxApplyMember;
            }

            const comboBoxUseMember = initMemberGroupComboBox({
                comboboxId: 'layer_member_group_use_member_combobox',
                parentLayerId: 'member_groupLayer_use_member',
                dataInputNm: 'couponUseMemberGroup',
                dataFormID: 'info_member_use_member',
                pageCountVar: 'comboBoxMemberGroupUseMemberPageCount',
                keywordVar: 'comboBoxMemberGroupUseMemberKeyword',
                labelText: '',
                tagClass: 'coupon-use-member-tag ncua-select-group-tag',
                apiUrl: '/share/ncds/layer_member_group.php'
            });

            if (comboBoxUseMember) {
                memberGroupComboBoxes['layer_member_group_use_member_combobox'] = comboBoxUseMember;
            }
        }
        
        initMemberGroupComboBoxes();
    } catch {
        // ignore
    }

    try {
        /**
         * 공급사 선택 ComboBox 초기화
         */
        const initSupplyComboBoxes = () => {
            if (typeof initSupplyComboBox === 'undefined') {
                setTimeout(initSupplyComboBoxes, 100);
                return;
            }

            const supplyComboBoxes = {};

            // // 초기 데이터 준비
            const applyInitData = [];
            <?php if (($couponData['couponApplyProductType'] === 'provider') && !empty($couponData['couponApplyProvider'])) { ?>
                <?php foreach ($couponData['couponApplyProvider'] as $k => $v) { ?>
                    applyInitData.push({
                        id: '<?= $v['no'] ?>',
                        label: '<?= addslashes($v['name']) ?>'
                    });
                <?php } ?>
            <?php } ?>

            const applyProviderSupplyComboBox = initSupplyComboBox({
                scmLayerSelector: 'couponApplyProvider',
                dataInputNm: 'couponApplyProvider',
                comboBoxLayerId: 'ncua-apply-combo-box-layer',
                scmFlValue: "<?= $checked['couponApplyProvider']['y'] ? 'y' : 'n'; ?>",
                radioGroupClass: 'apply-supply-radio-group',
                apiUrl: '/share/ncds/layer_scm.php',
                initData: applyInitData
            });

            if (applyProviderSupplyComboBox) {
                supplyComboBoxes['applyProviderSupplyComboBox'] = applyProviderSupplyComboBox;
            }

            // 초기 데이터 준비
            const exceptInitData = [];
            <?php if (($couponData['couponExceptProviderType'] === 'y') && !empty($couponData['couponExceptProvider'])) { ?>
                <?php foreach ($couponData['couponExceptProvider'] as $k => $v) { ?>
                    exceptInitData.push({
                        id: '<?= $v['no'] ?>',
                        label: '<?= addslashes($v['name']) ?>'
                    });
                <?php } ?>
            <?php } ?>

            const exceptProviderSupplyComboBox = initSupplyComboBox({
                scmLayerSelector: 'couponExceptProvider',
                dataInputNm: 'couponExceptProvider',
                comboBoxLayerId: 'ncua-except-combo-box-layer',
                scmFlValue: "<?= $checked['couponExceptProviderType']['y'] ? 'y' : 'n'; ?>",
                radioGroupClass: 'except-supply-radio-group',
                apiUrl: '/share/ncds/layer_scm.php',
                initData: exceptInitData
            });

            if (exceptProviderSupplyComboBox) {
                supplyComboBoxes['exceptProviderSupplyComboBox'] = exceptProviderSupplyComboBox;
            }
        }
        
        initSupplyComboBoxes();
    } catch {
        // ignore
    }

    // 구매금액 기준 옵션 상품금액 OR 주문전체상품금액 선택 시 텍스트 변경
    const minOrderSelect = document.querySelector('select[name="couponProductMinOrderType"]');
    if (minOrderSelect) {
        minOrderSelect.addEventListener('change', (e) => {
            changeCouponProductMinOrderTypeText();
        });
    }

    /**
     * 동일 아이디 재발급 제한 타입/체크 UI 상태 갱신 (라디오·체크박스 동작 연동)
     * name="couponSaveDuplicateType" 클릭 시 영역 노출/비노출,
     * 노출 상태에서 name="couponSaveDuplicateLimitType" 체크박스 값에 따라 input/컨테이너 활성 토글
     */
    const updateCouponSaveDuplicateLimitUI = () => {
        const radioChecked = document.querySelector('input[name="couponSaveDuplicateType"]:checked');
        const limitTypeSection = document.getElementById('couponSaveDuplicateLimitType');
        if (!radioChecked || !limitTypeSection) return;

        const radioVal = radioChecked.value;
        limitTypeSection.style.display = radioVal === 'y' ? '' : 'none';

        if (radioVal !== 'y') return;

        const checkbox = document.querySelector('input[name="couponSaveDuplicateLimitType"]');
        const input = document.querySelector('input[name="couponSaveDuplicateLimit"]');
        const container = document.getElementById('couponSaveDuplicateLimitContainer');
        if (!checkbox || !input || !container) return;

        input.disabled = !checkbox.checked;
        container.classList.toggle('is-disabled', !checkbox.checked);
    };

    /**
     * 동일 아이디 재발급 제한 설정 UI 라디오/체크박스 이벤트 바인딩 및 최초 상태 동기화
     */
    const initCouponSaveDuplicateLimitUI = () => {
        // 라디오: 설정함/설정안함
        document.querySelectorAll('input[name="couponSaveDuplicateType"]').forEach((el) => {
            el.removeEventListener('change', updateCouponSaveDuplicateLimitUI);
            el.addEventListener('change', updateCouponSaveDuplicateLimitUI);
        });

        // 체크박스: 최대
        const saveDuplicateLimitCheckbox = document.querySelector('input[name="couponSaveDuplicateLimitType"]');
        if (saveDuplicateLimitCheckbox) {
            saveDuplicateLimitCheckbox.removeEventListener('change', updateCouponSaveDuplicateLimitUI);
            saveDuplicateLimitCheckbox.addEventListener('change', updateCouponSaveDuplicateLimitUI);
        }

        // 최초 상태 동기화
        updateCouponSaveDuplicateLimitUI();
    };
    initCouponSaveDuplicateLimitUI();

    /**
     * 쿠폰 이벤트 타입에 따른 메시지 그룹 노출/숨김 UI 초기화
     */
    const initCouponEventGroupUI = () => {
        const saveTypeSelect = document.querySelector('input[name="couponSaveType"]:checked');
        const eventTypeSelect = document.querySelector('select[name="couponEventType"]');
        const groups = document.querySelectorAll('.event-group');
        if (!eventTypeSelect || !groups.length) {
            return;
        }

        const updateCouponEventGroupUI = () => {
            const val = eventTypeSelect.value;
            groups.forEach(group => {
                if (!group.dataset.eventType) {
                    return; 
                }
                const types = group.dataset.eventType.split(',');
                const isActive = types.includes(val);

                group.style.display = isActive ? '' : 'none';
                group.querySelectorAll('input[type="radio"]').forEach((el) => {
                    el.disabled = !isActive;
                });
            });
        };

        window.updateCouponEventGroupUI = updateCouponEventGroupUI;

        eventTypeSelect.removeEventListener('change', updateCouponEventGroupUI);
        eventTypeSelect.addEventListener('change', updateCouponEventGroupUI);

        updateCouponEventGroupUI();

        // saveTypeSelect 값이 manual 또는 down 인 경우 메시지 그룹 비노출
        if (saveTypeSelect && saveTypeSelect.value === 'manual' || saveTypeSelect.value === 'down') {
            groups.forEach(group => {
                (group.closest('tr') || group).style.display = 'none';
            });
        }
    };
    initCouponEventGroupUI();

    // 숫자만 입력 가능하도록 개선
    document.addEventListener('input', (event) => {
        const target = event.target;
        if (target.matches('input.js-number')) {
            target.value = target.value.replace(/[^0-9]/g, '');
        }
    });

    document.body.addEventListener('click', (e) => {
        const lawAlertLink = e.target.closest('.ncua-tooltip-panel a[title="lawAlert"]');
        if (!lawAlertLink) return;

        e.preventDefault();
        lawAlert();
    });
});
</script>

<script type="text/javascript">
    const code = '251113001';
    if (window.GodoCosGuide) {
        window.GodoCosGuide.init({
            apiUrl: <?= json_encode($cosGuideApiUrl) ?>,
            guideCode: code,
        }).then(() => {
            // 혜택 금액 설정 치환 코드 주입
            const couponCurrentUnitInfo = document.getElementById('couponCurrentUnitInfo');
            if (couponCurrentUnitInfo) {
                couponCurrentUnitInfo.innerHTML = `<?= gd_trunc_display('coupon'); ?>`;
            }
        });
    }
</script>
