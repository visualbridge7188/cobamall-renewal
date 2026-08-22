<section class="ncua-card">
    <header class="ncua-card__header">
        <h3 class="ncua-card__title">발급 정보</h3>
    </header>
    <section class="ncua-card__body">
        <div class="ncua-table ncua-table--vertical">
            <table>
                <tbody>
                    <tr>
                        <th>
                            <div>쿠폰유형</div>
                        </th>
                        <td>
                            <div class="ncua-flex-gap">
                                <label class="ncua-radio-field ncua-radio-field--xs has-text">
                                    <span class="ncua-radio-input ncua-radio-input--xs ncua-radio-field__input">
                                        <input type="radio" name="couponUseType" value="product" <?= $checked['couponUseType']['product'] ?> />
                                    </span>
                                    <span><span class="ncua-radio-field__text">상품적용쿠폰</span></span>
                                </label>
                                <label class="ncua-radio-field ncua-radio-field--xs has-text">
                                    <span class="ncua-radio-input ncua-radio-input--xs ncua-radio-field__input">
                                        <input type="radio" name="couponUseType" value="order" <?= $checked['couponUseType']['order'] ?> />
                                    </span>
                                    <span><span class="ncua-radio-field__text">주문적용쿠폰</span></span>
                                </label>
                                <label class="ncua-radio-field ncua-radio-field--xs has-text">
                                    <span class="ncua-radio-input ncua-radio-input--xs ncua-radio-field__input">
                                        <input type="radio" name="couponUseType" value="delivery" <?= $checked['couponUseType']['delivery'] ?> />
                                    </span>
                                    <span><span class="ncua-radio-field__text">배송비 할인 쿠폰</span></span>
                                </label>
                                <label class="ncua-radio-field ncua-radio-field--xs has-text">
                                    <span class="ncua-radio-input ncua-radio-input--xs ncua-radio-field__input">
                                        <input type="radio" name="couponUseType" value="gift" <?= $checked['couponUseType']['gift'] ?> />
                                    </span>
                                    <span><span class="ncua-radio-field__text">기프트쿠폰</span></span>
                                </label>
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <th>
                            <div data-tooltip-seq="001">발급방식</div>
                        </th>
                        <td>
                            <div class="ncua-flex-gap">
                                <label id="labelCouponSaveTypeManual" class="ncua-radio-field ncua-radio-field--xs has-text">
                                    <span class="ncua-radio-input ncua-radio-input--xs ncua-radio-field__input">
                                        <input type="radio" name="couponSaveType" value="manual" <?= $checked['couponSaveType']['manual']; ?> />
                                    </span>
                                    <span><span class="ncua-radio-field__text">수동발급</span></span>
                                </label>
                                <label id="labelCouponSaveTypeAuto" class="ncua-radio-field ncua-radio-field--xs has-text">
                                    <span class="ncua-radio-input ncua-radio-input--xs ncua-radio-field__input">
                                        <input type="radio" name="couponSaveType" value="auto" <?= $checked['couponSaveType']['auto']; ?> />
                                    </span>
                                    <span><span class="ncua-radio-field__text">자동발급</span></span>
                                </label>
                                <label id="labelCouponSaveTypeDown" class="ncua-radio-field ncua-radio-field--xs has-text">
                                    <span class="ncua-radio-input ncua-radio-input--xs ncua-radio-field__input">
                                        <input type="radio" name="couponSaveType" value="down" <?= $checked['couponSaveType']['down']; ?> />
                                    </span>
                                    <span><span class="ncua-radio-field__text">회원다운로드</span></span>
                                </label>
                            </div>
                        </td>
                    </tr>
                    <tr class="tr_auto">
                        <th class="ncua-required">
                            <div data-tooltip-seq="008">자동 발급 이벤트</div>
                        </th>
                        <td>
                            <div>
                                <span class="ncua-select ncua-select--xs">
                                    <span class="ncua-select__content">
                                        <select class="ncua-select__tag" name="couponEventType">
                                            <option value="">이벤트 선택</option>
                                            <option value="first" <?= $couponData['couponEventType'] == 'first' ? 'selected' : '' ?>>첫구매 축하 쿠폰</option>
                                            <option value="order"  <?= $couponData['couponEventType'] == 'order' ? 'selected' : '' ?>>구매 감사 쿠폰</option>
                                            <option value="birth" <?= $couponData['couponEventType'] == 'birth' ? 'selected' : '' ?>>생일 축하 쿠폰</option>
                                            <option value="join" <?= $couponData['couponEventType'] == 'join' ? 'selected' : '' ?>>회원가입 축하 쿠폰</option>
                                            <?php if (gd_is_plus_shop(PLUSSHOP_CODE_SIMPLEJOIN)) {?><option value="joinEvent" <?= $couponData['couponEventType'] == 'joinEvent' ? 'selected' : '' ?>>주문 간단 가입 쿠폰</option><?php } ?>
                                            <?php if (gd_is_plus_shop(PLUSSHOP_CODE_ATTENDANCE)) {?><option value="attend" <?= $couponData['couponEventType'] == 'attend' ? 'selected' : '' ?>>출석체크 감사 쿠폰</option><?php } ?>
                                            <?php if (gd_is_plus_shop(PLUSSHOP_CODE_CARTREMIND)) {?><option value="cartRemind" <?= $couponData['couponEventType'] == 'cartRemind' ? 'selected' : '' ?>>장바구니 알림 쿠폰</option><?php } ?>
                                            <?php if (gd_is_plus_shop(PLUSSHOP_CODE_REVIEW)) {?><option value="plusReview" <?= $couponData['couponEventType'] == 'plusReview' ? 'selected' : '' ?>>플러스리뷰 전용 쿠폰</option><?php } ?>
                                            <option value="memberModifyEvent" <?= $couponData['couponEventType'] == 'memberModifyEvent' ? 'selected' : '' ?>>회원정보 이벤트 쿠폰</option>
                                            <option value="wake" <?= $couponData['couponEventType'] == 'wake' ? 'selected' : '' ?>>휴면해제 감사 쿠폰</option>
                                        </select>
                                    </span>
                                </span>
                            </div>
                        </td>
                    </tr>
                    <tr class="tr_auto event-group" data-event-type="first,order,birth,join,attend,memberModifyEvent,wake">
                        <th>
                            <div data-tooltip-seq="009">메시지 발송</div>
                        </th>
                        <td>
                            <div class="ncua-gap-8">
                                <div class="event-group" data-event-type="first">
                                    <div class="ncua-switch ncua-switch--xs">
                                        <label class="ncua-switch__option ncua-switch__option--left ncua-switch__option--<?= $checked['couponEventFirstSmsType']['y'] ? 'active' : 'inactive' ?>">
                                            <input class="ncua-switch__radio ncua-switch__radio--left" type="radio" name="couponEventFirstSmsType" value="y" <?= $checked['couponEventFirstSmsType']['y'] ?> />
                                            <span class="ncua-switch__label">발송함</span>
                                        </label>
                                        <label class="ncua-switch__option ncua-switch__option--right ncua-switch__option--<?= $checked['couponEventFirstSmsType']['n'] ? 'active' : 'inactive' ?>">
                                            <input class="ncua-switch__radio ncua-switch__radio--right" type="radio" name="couponEventFirstSmsType" value="n"
                                                <?= $checked['couponEventFirstSmsType']['n']
                                                    || !empty($checked['couponEventFirstSmsType'][''])
                                                    || empty($checked['couponEventFirstSmsType']) ? 'checked="checked"' : '' ?> />
                                            <span class="ncua-switch__label">발송안함</span>
                                        </label>
                                    </div>
                                </div>
                                <div class="ncua-flex-gap event-group" data-event-type="order">
                                    <div class="ncua-switch ncua-switch--xs">
                                        <label class="ncua-switch__option ncua-switch__option--left ncua-switch__option--<?= $checked['couponEventOrderSmsType']['y'] ? 'active' : 'inactive' ?>">
                                            <input class="ncua-switch__radio ncua-switch__radio--left" type="radio" name="couponEventOrderSmsType" value="y" <?= $checked['couponEventOrderSmsType']['y'] ?> />
                                            <span class="ncua-switch__label">발송함</span>
                                        </label>
                                        <label class="ncua-switch__option ncua-switch__option--right ncua-switch__option--<?= $checked['couponEventOrderSmsType']['n'] ? 'active' : 'inactive' ?>">
                                            <input class="ncua-switch__radio ncua-switch__radio--right" type="radio" name="couponEventOrderSmsType" value="n"
                                                <?= $checked['couponEventOrderSmsType']['n']
                                                    || !empty($checked['couponEventOrderSmsType'][''])
                                                    || empty($checked['couponEventOrderSmsType']) ? 'checked="checked"' : '' ?> />
                                            <span class="ncua-switch__label">발송안함</span>
                                        </label>
                                    </div>
                                </div>
                                <div class="event-group" data-event-type="birth">
                                    <div class="ncua-switch ncua-switch--xs">
                                        <label class="ncua-switch__option ncua-switch__option--left ncua-switch__option--<?= $checked['couponEventBirthSmsType']['y'] ? 'active' : 'inactive' ?>">
                                            <input class="ncua-switch__radio ncua-switch__radio--left" type="radio" name="couponEventBirthSmsType" value="y" <?= $checked['couponEventBirthSmsType']['y'] ?> />
                                            <span class="ncua-switch__label">발송함</span>
                                        </label>
                                        <label class="ncua-switch__option ncua-switch__option--right ncua-switch__option--<?= $checked['couponEventBirthSmsType']['n'] ? 'active' : 'inactive' ?>">
                                            <input class="ncua-switch__radio ncua-switch__radio--right" type="radio" name="couponEventBirthSmsType" value="n"
                                                <?= $checked['couponEventBirthSmsType']['n']
                                                    || !empty($checked['couponEventBirthSmsType'][''])
                                                    || empty($checked['couponEventBirthSmsType']) ? 'checked="checked"' : '' ?> />
                                            <span class="ncua-switch__label">발송안함</span>
                                        </label>
                                    </div>
                                </div>
                                <div class="event-group" data-event-type="join">
                                    <div class="ncua-switch ncua-switch--xs">
                                        <label class="ncua-switch__option ncua-switch__option--left ncua-switch__option--<?= $checked['couponEventMemberSmsType']['y'] ? 'active' : 'inactive' ?>">
                                            <input class="ncua-switch__radio ncua-switch__radio--left" type="radio" name="couponEventMemberSmsType" value="y" <?= $checked['couponEventMemberSmsType']['y'] ?> />
                                            <span class="ncua-switch__label">발송함</span>
                                        </label>
                                        <label class="ncua-switch__option ncua-switch__option--right ncua-switch__option--<?= $checked['couponEventMemberSmsType']['n'] ? 'active' : 'inactive' ?>">
                                            <input class="ncua-switch__radio ncua-switch__radio--right" type="radio" name="couponEventMemberSmsType" value="n"
                                                <?= $checked['couponEventMemberSmsType']['n']
                                                    || !empty($checked['couponEventMemberSmsType'][''])
                                                    || empty($checked['couponEventMemberSmsType']) ? 'checked="checked"' : '' ?> />
                                            <span class="ncua-switch__label">발송안함</span>
                                        </label>
                                    </div>
                                </div>
                                <div class="event-group" data-event-type="attend">
                                    <div class="ncua-switch ncua-switch--xs">
                                        <label class="ncua-switch__option ncua-switch__option--left ncua-switch__option--<?= $checked['couponEventAttendanceSmsType']['y'] ? 'active' : 'inactive' ?>">
                                            <input class="ncua-switch__radio ncua-switch__radio--left" type="radio" name="couponEventAttendanceSmsType" value="y" <?= $checked['couponEventAttendanceSmsType']['y'] ?> />
                                            <span class="ncua-switch__label">발송함</span>
                                        </label>
                                        <label class="ncua-switch__option ncua-switch__option--right ncua-switch__option--<?= $checked['couponEventAttendanceSmsType']['n'] ? 'active' : 'inactive' ?>">
                                            <input class="ncua-switch__radio ncua-switch__radio--right" type="radio" name="couponEventAttendanceSmsType" value="n"
                                                <?= $checked['couponEventAttendanceSmsType']['n']
                                                    || !empty($checked['couponEventAttendanceSmsType'][''])
                                                    || empty($checked['couponEventAttendanceSmsType']) ? 'checked="checked"' : '' ?> />
                                            <span class="ncua-switch__label">발송안함</span>
                                        </label>
                                    </div>
                                </div>
                                <div class="event-group" data-event-type="memberModifyEvent">
                                    <div class="ncua-switch ncua-switch--xs">
                                        <label class="ncua-switch__option ncua-switch__option--left ncua-switch__option--<?= $checked['couponEventMemberModifySmsType']['y'] ? 'active' : 'inactive' ?>">
                                            <input class="ncua-switch__radio ncua-switch__radio--left" type="radio" name="couponEventMemberModifySmsType" value="y" <?= $checked['couponEventMemberModifySmsType']['y'] ?> />
                                            <span class="ncua-switch__label">발송함</span>
                                        </label>
                                        <label class="ncua-switch__option ncua-switch__option--right ncua-switch__option--<?= $checked['couponEventMemberModifySmsType']['n'] ? 'active' : 'inactive' ?>">
                                            <input class="ncua-switch__radio ncua-switch__radio--right" type="radio" name="couponEventMemberModifySmsType" value="n"
                                                <?= $checked['couponEventMemberModifySmsType']['n']
                                                    || !empty($checked['couponEventMemberModifySmsType'][''])
                                                    || empty($checked['couponEventMemberModifySmsType']) ? 'checked="checked"' : '' ?> />
                                            <span class="ncua-switch__label">발송안함</span>
                                        </label>
                                    </div>
                                </div>
                                <div class="event-group" data-event-type="wake">
                                    <div class="ncua-switch ncua-switch--xs">
                                        <label class="ncua-switch__option ncua-switch__option--left ncua-switch__option--<?= $checked['couponEventWakeSmsType']['y'] ? 'active' : 'inactive' ?>">
                                            <input class="ncua-switch__radio ncua-switch__radio--left" type="radio" name="couponEventWakeSmsType" value="y" <?= $checked['couponEventWakeSmsType']['y'] ?> />
                                            <span class="ncua-switch__label">발송함</span>
                                        </label>
                                        <label class="ncua-switch__option ncua-switch__option--right ncua-switch__option--<?= $checked['couponEventWakeSmsType']['n'] ? 'active' : 'inactive' ?>">
                                            <input class="ncua-switch__radio ncua-switch__radio--right" type="radio" name="couponEventWakeSmsType" value="n"
                                                <?= $checked['couponEventWakeSmsType']['n']
                                                    || !empty($checked['couponEventWakeSmsType'][''])
                                                    || empty($checked['couponEventWakeSmsType']) ? 'checked="checked"' : '' ?> />
                                            <span class="ncua-switch__label">발송안함</span>
                                        </label>
                                    </div>
                                </div>
                                <label class="event-group ncua-checkbox-field ncua-checkbox-field--xs has-text" data-event-type="order">
                                    <span class="ncua-checkbox-input ncua-checkbox-input--xs ncua-checkbox-field__input">
                                        <input type="checkbox" name="couponEventOrderFirstType" value="y" <?= $checked['couponEventOrderFirstType']['y'] ?> />
                                    </span>
                                    <span class="ncua-checkbox-field__text">첫구매 축하 쿠폰과 중복 지급</span>
                                </label>
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <th>
                            <div data-tooltip-seq="002">지급 혜택 구분</div>
                        </th>
                        <td>
                            <div class="ncua-flex-gap">
                                <label id="labelCouponKindTypeSale" class="ncua-radio-field ncua-radio-field--xs has-text">
                                    <span class="ncua-radio-input ncua-radio-input--xs ncua-radio-field__input">
                                        <input type="radio" name="couponKindType" value="sale" <?= $checked['couponKindType']['sale'] ?> />
                                    </span>
                                    <span><span class="ncua-radio-field__text">상품할인</span></span>
                                </label>
                                <label id="labelCouponKindTypeAdd" class="ncua-radio-field ncua-radio-field--xs has-text">
                                    <span class="ncua-radio-input ncua-radio-input--xs ncua-radio-field__input">
                                        <input type="radio" name="couponKindType" value="add" <?= $checked['couponKindType']['add'] ?> />
                                    </span>
                                    <span><span class="ncua-radio-field__text">마일리지 적립</span></span>
                                </label>
                                <label id="labelCouponKindTypeDelivery" class="ncua-radio-field ncua-radio-field--xs has-text">
                                    <span class="ncua-radio-input ncua-radio-input--xs ncua-radio-field__input">
                                        <input type="radio" name="couponKindType" value="delivery" <?= $checked['couponKindType']['delivery'] ?> />
                                    </span>
                                    <span><span class="ncua-radio-field__text">배송비 할인</span></span>
                                </label>
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <th class="ncua-required"><div data-tooltip-seq="003">혜택 금액 설정</div></th>
                        <td>
                            <div class="ncua-flex-column ncua-flex-gap">
                                <div class="ncua-flex-gap ncua-coupon-benefit-type-group" id="couponBenefitTypeGroup">
                                    <label class="ncua-radio-field ncua-radio-field--xs has-text">
                                        <span class="ncua-radio-input ncua-radio-input--xs ncua-radio-field__input">
                                            <input type="radio" name="couponBenefitType" value="percent" <?= ($couponData['couponBenefitType'] === 'percent' || $couponData['couponBenefitType'] !== 'fix') ? 'checked="checked"' : '' ?>>
                                        </span>
                                        <span><span class="ncua-radio-field__text">정률<span class="benefit-text">할인</span></span></span>
                                    </label>
                                    <label class="ncua-radio-field ncua-radio-field--xs has-text">
                                        <span class="ncua-radio-input ncua-radio-input--xs ncua-radio-field__input">
                                            <input type="radio" name="couponBenefitType" value="fix" <?= $couponData['couponBenefitType'] === 'fix' ? 'checked="checked"' : '' ?>>
                                        </span>
                                        <span><span class="ncua-radio-field__text">정액<span class="benefit-text">할인</span></span></span>
                                    </label>
                                </div>
                                <div class="ncua-benefit-config ncua-flex-gap">
                                    <div class="ncua-input ncua-input--xs ncua-text-input-unit">
                                        <div class="ncua-input-text" id="couponBenefitText">구매금액 기준 :</div>
                                        <div class="ncua-input__content">
                                            <div class="ncua-input__field ncua-input__field--xs ncua-input-width-120">
                                                <input type="text" name="couponBenefit" value="<?= $couponData['couponBenefit'] ? gd_money_format($couponData['couponBenefit'], false) : ''; ?>"  class="js-number" placeholder="할인율 입력" maxlength="3"> 
                                            </div>
                                        </div>
                                        <div class="ncua-input__unit">
                                            <span id="couponBenefitUnitPercent">%</span>
                                            <span id="couponBenefitUnitFix"><?= gd_currency_string(); ?><span id="couponBenefitUnit"> 할인</span>
                                        </div>
                                    </div>
                                    <div class="ncua-input ncua-input--xs ncua-text-input-unit" id="couponMaxBenefitType">
                                        <label class="ncua-checkbox-field ncua-checkbox-field--xs has-text">
                                            <span class="ncua-checkbox-input ncua-checkbox-input--xs ncua-checkbox-field__input">
                                                <input type="checkbox" name="couponMaxBenefitType" value="y" <?= $checked['couponMaxBenefitType']['y'] ?> />
                                            </span>
                                            <span class="ncua-checkbox-field__text">최대<span class="benefit-text">할인</span>금액</span>
                                        </label>
                                        <div class="ncua-input__content">
                                            <div class="ncua-input__field ncua-input__field--xs ncua-input-width-120">
                                                <input type="text" name="couponMaxBenefit" value="<?= gd_money_format($couponData['couponMaxBenefit'], false); ?>" class="js-number" placeholder="금액 입력" maxlength="8"> 
                                            </div>
                                        </div>
                                        <div class="ncua-input__unit"><?= gd_currency_string(); ?> 까지 가능</div>
                                    </div>
                                    <div class="ncua-input ncua-input--xs ncua-text-input-unit" id="benefitFixApply">
                                        <label class="ncua-checkbox-field ncua-checkbox-field--xs has-text">
                                            <span class="ncua-checkbox-input ncua-checkbox-input--xs ncua-checkbox-field__input">
                                                <input type="checkbox"  name="couponBenefitFixApply" value="all" <?= $checked['couponBenefitFixApply']['all'] ?> />
                                            </span>
                                            <span class="ncua-checkbox-field__text">상품 수량별 혜택 적용 (추가 상품 제외)</span>
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </td>
                    </tr>
                    <tr class="tr_down tr_auto" id="issueMemberGroupTr">
                        <th><div id="couponApplyMemberGroupTitle">발급 가능 회원 등급</div></th>
                        <td class="couponEventType">
                            <div class="ncua-flex-column ncua-gap-8" data-combobox-id="layer_member_group_apply_member_combobox">
                                <div class="ncua-coupon-apply-member-group ncua-flex-gap">
                                    <label class="ncua-radio-field ncua-radio-field--xs has-text">
                                        <span class="ncua-radio-input ncua-radio-input--xs ncua-radio-field__input">
                                            <input type="radio" name="applyMemberGroup" id="applyMemberGroupAll" value="all"<?php if (!$couponData['couponApplyMemberGroup']) {?> checked<?php }?> />
                                        </span>
                                        <span><span class="ncua-radio-field__text">전체회원</span></span>
                                    </label>
                                    <label class="ncua-radio-field ncua-radio-field--xs has-text">
                                        <span class="ncua-radio-input ncua-radio-input--xs ncua-radio-field__input">
                                            <input type="radio" name="applyMemberGroup" id="applyMemberGroupSelect" value="group"<?php if ($couponData['couponApplyMemberGroup']) {?> checked<?php }?> />
                                        </span>
                                        <span><span class="ncua-radio-field__text">특정회원등급</span></span>
                                    </label>
                                    <div id="layer_member_group_apply_member_combobox"></div>
                                    <label class="ncua-checkbox-field ncua-checkbox-field--xs has-text label_memberdown">
                                        <span class="ncua-checkbox-input ncua-checkbox-input--xs ncua-checkbox-field__input">
                                            <input type="checkbox" name="couponApplyMemberGroupDisplayType" value="y" <?= $checked['couponApplyMemberGroupDisplayType']['y'] ?> />
                                        </span>
                                        <span class="ncua-checkbox-field__text">선택한 회원등급만 쿠폰 노출</span>
                                    </label>
                                </div>
                                <div id="member_groupLayer_apply_member" class="ncua-flex ncua-gap-4 ncua-align-items-center member-group-display-none <?=$couponData['couponApplyMemberGroup'] ? 'active' : ''?>">
                                    <?php
                                    if ($couponData['couponApplyMemberGroup']) {
                                        foreach ($couponData['couponApplyMemberGroup'] as $k => $v) {
                                        ?>
                                            <div id="couponApplyMemberGroup_<?= $couponData['couponApplyMemberGroup'][$k]['no'] ?>">
                                                <input type="hidden" name="couponApplyMemberGroup[]" value="<?= $couponData['couponApplyMemberGroup'][$k]['no'] ?>"/>
                                                <span class="ncua-tag ncua-tag--sm">
                                                    <span class="ncua-tag__text"><?= $couponData['couponApplyMemberGroup'][$k]['name'] ?></span>
                                                    <button type="button" class="ncua-tag__close" onclick="removeMemberGroup('couponApplyMemberGroup', <?= $couponData['couponApplyMemberGroup'][$k]['no'] ?>)">
                                                        <span class="coupon-apply-member-tag ncua-select-group-tag"></span>
                                                    </button>
                                                </span>
                                            </div>
                                        <?php
                                        }
                                    }
                                    ?>
                                </div>
                            </div>
                        </td>
                    </tr>
                    <tr class="tr_down">
                        <th>
                            <div>상품리스트/상품상세 쿠폰 발급 설정</div>
                        </th>
                        <td>
                            <div class="ncua-flex-gap">
                                <span class="ncua-select ncua-select--xs">
                                    <span class="ncua-select__content">
                                        <select class="ncua-select__tag" name="couponDisplayType" id="couponDisplayType">
                                            <option value="n" <?= $couponData['couponDisplayType'] === 'n' ? 'selected' : '' ?>>등록 즉시 발급</option>
                                            <option value="y" <?= $couponData['couponDisplayType'] === 'y' ? 'selected' : '' ?>>발급기간 설정</option>
                                        </select>
                                    </span>
                                </span>
                                <div id="display-type-datepicker"></div>
                            </div>
                        </td>
                    </tr>
                    <tr class="tr_down tr_auto">
                        <th><div>전체 발급 수량</div></th>
                        <td>
                            <div class="ncua-gap-8">
                                <div class="ncua-coupon-amount-type-group ncua-flex-gap">
                                    <label class="ncua-radio-field ncua-radio-field--xs has-text">
                                        <span class="ncua-radio-input ncua-radio-input--xs ncua-radio-field__input">
                                            <input type="radio" name="couponAmountType" value="n" <?= $checked['couponAmountType']['n'] ?> />
                                        </span>
                                        <span><span class="ncua-radio-field__text">무제한</span></span>
                                    </label>
                                    <label class="ncua-radio-field ncua-radio-field--xs has-text">
                                        <span class="ncua-radio-input ncua-radio-input--xs ncua-radio-field__input">
                                            <input type="radio" name="couponAmountType" value="y" <?= $checked['couponAmountType']['y'] ?> />
                                        </span>
                                        <span><span class="ncua-radio-field__text">최대</span></span>
                                    </label>
                                </div>
                                <div class="ncua-input ncua-input--xs ncua-text-input-unit <?= empty(($checked['couponAmountType']['y'])) ? 'is-disabled' : '' ?>">
                                    <div class="ncua-input__content">
                                        <div class="ncua-input__field ncua-input__field--xs ncua-input-width-120">
                                            <input type="text" name="couponAmount" value="<?= $couponData['couponAmount'] ?>" placeholder="수량 입력" class="js-number" maxlength="8" />
                                        </div>
                                    </div>
                                    <div>장</div>
                                </div>
                            </div>
                        </td>
                    </tr>
                    <tr class="tr_down tr_auto couponEventType">
                        <th>
                            <div>동일 아이디 재발급 제한</div>
                        </th>
                        <td class="ncua-save-duplicate-limit-config">
                            <div class="ncua-gap-8">
                                <div class="ncua-switch ncua-switch--xs">
                                    <label class="ncua-switch__option ncua-switch__option--left ncua-switch__option--<?= $checked['couponSaveDuplicateType']['y'] ? 'active' : 'inactive' ?>">
                                        <input class="ncua-switch__radio ncua-switch__radio--left" type="radio" name="couponSaveDuplicateType" value="y" <?= $checked['couponSaveDuplicateType']['y'] ?> />
                                        <span class="ncua-switch__label">설정함</span>
                                    </label>
                                    <label class="ncua-switch__option ncua-switch__option--right ncua-switch__option--<?= $checked['couponSaveDuplicateType']['n'] ? 'active' : 'inactive' ?>">
                                        <input class="ncua-switch__radio ncua-switch__radio--right" type="radio" name="couponSaveDuplicateType" value="n" <?= $checked['couponSaveDuplicateType']['n'] ?> />
                                        <span class="ncua-switch__label">설정안함</span>
                                    </label>
                                </div>
                                <div id="couponSaveDuplicateLimitType" class="ncua-input ncua-input--xs ncua-text-input-unit">
                                    <label class="ncua-checkbox-field ncua-checkbox-field--xs has-text">
                                        <span class="ncua-checkbox-input ncua-checkbox-input--xs ncua-checkbox-field__input">
                                            <input type="checkbox" name="couponSaveDuplicateLimitType" value="y" <?= $checked['couponSaveDuplicateLimitType']['y'] ?> />
                                        </span>
                                        <span class="ncua-checkbox-field__text">최대</span>
                                    </label>
                                    <div id="couponSaveDuplicateLimitContainer" class="ncua-input ncua-input--xs ncua-text-input-unit <?= empty(($checked['couponSaveDuplicateLimitType']['y'])) ? 'is-disabled' : '' ?>">
                                        <div class="ncua-input__content">
                                            <div class="ncua-input__field ncua-input__field--xs ncua-input-width-80">
                                                <input type="text" name="couponSaveDuplicateLimit" value="<?= $couponData['couponSaveDuplicateLimit'] ?>" placeholder="숫자 입력" class="js-number" maxlength="8" />
                                            </div>
                                        </div>
                                        <div>장</div>
                                    </div>
                                </div>
                            </div>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </section>
</section>
