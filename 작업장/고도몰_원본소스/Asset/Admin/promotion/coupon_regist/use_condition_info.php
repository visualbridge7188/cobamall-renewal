<section class="ncua-card">
    <header class="ncua-card__header">
        <h3 class="ncua-card__title">사용 조건</h3>
    </header>
    <section class="ncua-card__body">
        <div class="ncua-table ncua-table--vertical">
            <table>
                <tbody>
                    <tr>
                        <th class="ncua-required"><div>사용기간</div></th>
                        <td>
                            <div class="ncua-flex-column ncua-flex-gap">
                                <div class="ncua-flex-gap ncua-coupon-use-period-type-group">
                                    <label class="ncua-radio-field ncua-radio-field--xs has-text">
                                        <span class="ncua-radio-input ncua-radio-input--xs ncua-radio-field__input">
                                            <input type="radio" name="couponUsePeriodType" value="period" <?= $checked['couponUsePeriodType']['period'] ?>   />
                                        </span>
                                        <span class="ncua-radio-field__text">설정 기간 기준</span></span>
                                    </label>
                                    <label class="ncua-radio-field ncua-radio-field--xs has-text">
                                        <span class="ncua-radio-input ncua-radio-input--xs ncua-radio-field__input">
                                            <input type="radio" name="couponUsePeriodType" value="day" <?= $checked['couponUsePeriodType']['day'] ?> />
                                        </span>
                                        <span class="ncua-radio-field__text">쿠폰 발급일 기준</span></span>
                                    </label>
                                </div>
                                <div id="divCouponUsePeriodTypePeriod">
                                    <div id="config-period-datepicker"></div>
                                </div>
                                <div id="divCouponUsePeriodTypeDay" class="ncua-benefit-config ncua-flex-gap">
                                    <div class="ncua-input ncua-input--xs ncua-text-input-unit">
                                        <div class="ncua-input__content">
                                            <div class="ncua-input__field ncua-input__field--xs ncua-input-width-120">
                                                <input type="text" name="couponUsePeriodDay" value="<?= $couponData['couponUsePeriodDay'] ?>" class="js-number" placeholder="일수 입력" maxlength="3"> 
                                            </div>
                                        </div>
                                        <div class="ncua-input__unit">일까지 사용 가능</div>
                                    </div>
                                    <div class="ncua-input ncua-input--xs ncua-text-input-unit">
                                        <label class="ncua-checkbox-field ncua-checkbox-field--xs has-text">
                                            <span class="ncua-checkbox-input ncua-checkbox-input--xs ncua-checkbox-field__input">
                                                <input type="checkbox" name="couponUsePeriodDayLimit" value="on" <?= $checked['couponUsePeriodDayLimit']['on'] ?>>
                                            </span>
                                            <span class="ncua-checkbox-field__text">종료일 제한</span>
                                        </label>
                                        <div id="end-period-datepicker"></div>
                                    </div>
                                </div>
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <th>
                            <div data-tooltip-seq="004">사용범위</div>
                        </th>
                        <td>
                            <div class="ncua-flex-gap">
                                <label class="ncua-radio-field ncua-radio-field--xs has-text">
                                    <span class="ncua-radio-input ncua-radio-input--xs ncua-radio-field__input">
                                        <input type="radio" name="couponDeviceType" value="all" <?= $checked['couponDeviceType']['all'] ?> />
                                    </span>
                                    <span class="ncua-radio-field__text">전체</span>
                                </label>
                                <label class="ncua-radio-field ncua-radio-field--xs has-text">
                                    <span class="ncua-radio-input ncua-radio-input--xs ncua-radio-field__input">
                                        <input type="radio" name="couponDeviceType" value="pc" <?= $checked['couponDeviceType']['pc'] ?> />
                                    </span>
                                    <span class="ncua-radio-field__text">PC</span>
                                </label>
                                <label class="ncua-radio-field ncua-radio-field--xs has-text">
                                    <span class="ncua-radio-input ncua-radio-input--xs ncua-radio-field__input">
                                        <input type="radio" name="couponDeviceType" value="mobile" <?= $checked['couponDeviceType']['mobile'] ?> />
                                    </span>
                                    <span class="ncua-radio-field__text">모바일</span>
                                </label>
                            </div>
                        </td>
                    </tr>
                    <tr class="tr_gift">
                        <th>
                            <div data-tooltip-seq="005">결제수단 사용 제한</div>
                        </th>
                        <td>
                            <div class="ncua-flex-gap">
                                <label class="ncua-radio-field ncua-radio-field--xs has-text">
                                    <span class="ncua-radio-input ncua-radio-input--xs ncua-radio-field__input">
                                        <input type="radio" name="couponUseAblePaymentType" value="all" <?= $checked['couponUseAblePaymentType']['all'] ?> />
                                    </span>
                                    <span class="ncua-radio-field__text">제한없음</span>
                                </label>
                                <label class="ncua-radio-field ncua-radio-field--xs has-text">
                                    <span class="ncua-radio-input ncua-radio-input--xs ncua-radio-field__input">
                                        <input type="radio" name="couponUseAblePaymentType" value="bank" <?= $checked['couponUseAblePaymentType']['bank'] ?> />
                                    </span>
                                    <span class="ncua-radio-field__text">무통장입금만 사용가능</span>
                                </label>
                            </div>
                        </td>
                    </tr>
                    <tr class="tr_down tr_auto" id="useMemberGroupTr">
                        <th><div>사용 가능 회원등급 선택</div></th>
                        <td class="couponEventType">
                            <div class="ncua-flex-column ncua-gap-8" data-combobox-id="layer_member_group_use_member_combobox">
                                <div class="ncua-coupon-use-member-group ncua-flex-gap">
                                    <label class="ncua-radio-field ncua-radio-field--xs has-text">
                                        <span class="ncua-radio-input ncua-radio-input--xs ncua-radio-field__input">
                                            <input type="radio" name="useMemberGroup" id="useMemberGroupAll" value="all"<?php if (!$couponData['couponUseMemberGroup']) {?> checked<?php }?> />
                                        </span>
                                        <span class="ncua-radio-field__text">전체회원</span>
                                    </label>
                                    <label class="ncua-radio-field ncua-radio-field--xs has-text">
                                        <span class="ncua-radio-input ncua-radio-input--xs ncua-radio-field__input">
                                            <input type="radio"name="useMemberGroup" id="useMemberGroupSelect" value="group"<?php if ($couponData['couponUseMemberGroup']) {?> checked<?php }?> />
                                        </span>
                                        <span class="ncua-radio-field__text">특정회원등급</span>
                                    </label>
                                    <div id="layer_member_group_use_member_combobox"></div>
                                </div>
                                <div id="member_groupLayer_use_member" class="ncua-flex ncua-gap-4 ncua-align-items-center member-group-display-none <?=$couponData['couponUseMemberGroup'] ? 'active' : ''?>">
                                    <?php
                                    if ($couponData['couponUseMemberGroup']) {
                                        foreach ($couponData['couponUseMemberGroup'] as $k => $v) {
                                        ?>
                                            <div id="couponUseMemberGroup_<?= $couponData['couponUseMemberGroup'][$k]['no'] ?>">
                                                <input type="hidden" name="couponUseMemberGroup[]" value="<?= $couponData['couponUseMemberGroup'][$k]['no'] ?>"/>
                                                <span class="ncua-tag ncua-tag--sm">
                                                    <span class="ncua-tag__text"><?= $couponData['couponUseMemberGroup'][$k]['name'] ?></span>
                                                    <button type="button" class="ncua-tag__close" onclick="removeMemberGroup('couponUseMemberGroup', <?= $couponData['couponUseMemberGroup'][$k]['no'] ?>)">
                                                        <span class="coupon-use-member-tag ncua-select-group-tag"></span>
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
                    <tr class="tr-apply-use">
                        <th>
                            <div id="couponApplyProductTypeTitle">쿠폰 사용 가능 상품 범위 설정</div>
                        </th>
                        <td>
                            <div class="ncua-flex-gap apply-supply-radio-group">
                                <label class="ncua-radio-field ncua-radio-field--xs has-text">
                                    <span class="ncua-radio-input ncua-radio-input--xs ncua-radio-field__input">
                                        <input type="radio" name="couponApplyProductType" value="all" <?= $checked['couponApplyProductType']['all'] ?> />
                                    </span>
                                    <span class="ncua-radio-field__text">전체상품</span>
                                </label>
                                <?php if (gd_use_provider()) { ?>
                                <label class="ncua-radio-field ncua-radio-field--xs has-text">
                                    <span class="ncua-radio-input ncua-radio-input--xs ncua-radio-field__input">
                                        <input type="radio" name="couponApplyProductType" value="provider" <?= $checked['couponApplyProductType']['provider'] ?> />
                                    </span>
                                    <span class="ncua-radio-field__text">특정 공급사</span>
                                </label>
                                <?php } ?>
                                <label class="ncua-radio-field ncua-radio-field--xs has-text">
                                    <span class="ncua-radio-input ncua-radio-input--xs ncua-radio-field__input">
                                        <input type="radio" name="couponApplyProductType" value="category" <?= $checked['couponApplyProductType']['category'] ?> />
                                    </span>
                                    <span class="ncua-radio-field__text">특정 카테고리</span>
                                </label>
                                <label class="ncua-radio-field ncua-radio-field--xs has-text">
                                    <span class="ncua-radio-input ncua-radio-input--xs ncua-radio-field__input">
                                        <input type="radio" name="couponApplyProductType" value="brand" <?= $checked['couponApplyProductType']['brand'] ?> />
                                    </span>
                                    <span class="ncua-radio-field__text">특정 브랜드</span>
                                </label>
                                <label class="ncua-radio-field ncua-radio-field--xs has-text">
                                    <span class="ncua-radio-input ncua-radio-input--xs ncua-radio-field__input">
                                        <input type="radio" name="couponApplyProductType" value="goods" <?= $checked['couponApplyProductType']['goods'] ?> />
                                    </span>
                                    <span class="ncua-radio-field__text">특정 상품</span>
                                </label>
                            </div>
                        </td>
                    </tr>
                    <?php if (gd_use_provider()) { ?>
                    <tr class="tr-apply-provider tr-apply-use">
                        <th><div id="couponApplyProductTypeProviderTitle">발급/사용 가능 특정 공급사 선택</div></th>
                        <td>
                            <div class="ncua-flex-column ncua-gap-8">
                                <div id="ncua-apply-combo-box-layer"></div>
                                <div id="couponApplyProvider" class="ncua-flex ncua-gap-4 ncua-flex-wrap"></div>
                            </div>
                        </td>
                    </tr>
                    <?php } ?>
                    <tr class="tr-apply-category tr-apply-use">
                        <th>
                            <div id="couponApplyProductTypeCategoryTitle">발급/사용 가능 특정 카테고리 선택</div>
                        </th>
                        <td>
                            <div class="ncua-flex-column">
                                <span>
                                    <span class="ncua-radio-field__text">
                                    <button type="button" id="selectApplyCategory" class="ncua-btn ncua-btn--xs ncua-btn--secondary">
                                        <span class="ncua-btn__label">카테고리 선택</span>
                                    </button>
                                    </span>
                                </span>

                                <div class="ncua-search-result">
                                    <div class="ncua-search-result__content">
                                        <div class="ncua-table ncua-table--horizontal">
                                            <table>
                                                <colgroup>
                                                    <col width="56px">
                                                    <col width="80px">
                                                    <col width="*">
                                                </colgroup>
                                                <thead>
                                                    <tr>
                                                        <th>
                                                            <div>
                                                                <label class="ncua-checkbox-field ncua-checkbox-field--xs has-text">
                                                                    <span class="ncua-checkbox-input ncua-checkbox-input--xs ncua-checkbox-field__input">
                                                                        <input type="checkbox" id="categoryAllCheck">
                                                                    </span>
                                                                </label>
                                                            </div>
                                                        </th>
                                                        <th><div>번호</div></th>
                                                        <th><div class="ncua-left-align">카테고리명</div></th>
                                                    </tr>
                                                </thead>
                                                <tbody id="couponApplyCategory">
                                                    <?php
                                                    if ($couponData['couponApplyProductType'] == 'category' && $couponData['couponApplyCategory']) {
                                                        foreach ($couponData['couponApplyCategory'] as $k => $v) {
                                                    ?>
                                                    <tr id="idCategory_<?= $couponData['couponApplyCategory'][$k]['no'] ?>">
                                                        <td>
                                                            <div>
                                                                <input type="hidden" name="couponApplyCategory[]" value="<?= $couponData['couponApplyCategory'][$k]['no'] ?>">
                                                                <input type="hidden" name="couponApplyCategoryName[]" value="<?= $couponData['couponApplyCategory'][$k]['name'] ?>">
                                                                <label class="ncua-checkbox-field ncua-checkbox-field--xs has-text">
                                                                    <span class="ncua-checkbox-input ncua-checkbox-input--xs ncua-checkbox-field__input">
                                                                        <input type="checkbox" data-target="#idCategory_<?= $couponData['couponApplyCategory'][$k]['no'] ?>">
                                                                    </span>
                                                                </label>
                                                            </div>
                                                        </td>
                                                        <td><div><?= ($k + 1) ?></div></td>
                                                        <td><div class="ncua-left-align"><?= $couponData['couponApplyCategory'][$k]['name'] ?></div></td>
                                                    </tr>
                                                    <?php
                                                        }
                                                    } else {
                                                    ?>
                                                    <tr class="tr-no-data"><td colspan="3" class="no-data"><div>선택된 카테고리가 없습니다.</div></td></tr>
                                                    <?php
                                                    }
                                                    ?>
                                                </tbody>
                                            </table>
                                        </div>
                                        <div class="ncua-search-result__actions ncua-search-result__actions--bottom">
                                            <button type="button" id="deleteSelectedCategorys" class="ncua-btn ncua-btn--xs ncua-btn--destructive ncua-select-delete js-delete-category">
                                                선택 삭제
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </td>
                    </tr>
                    <tr class="tr-apply-brand tr-apply-use">
                        <th>
                            <div id="couponApplyProductTypeBrandTitle">발급/사용 가능 특정 브랜드 선택</div>
                        </th>
                        <td>
                            <div class="ncua-flex-column">
                                <span>
                                    <span class="ncua-radio-field__text">
                                    <button type="button" id="selectApplyBrand" class="ncua-btn ncua-btn--xs ncua-btn--secondary">
                                        <span class="ncua-btn__label">브랜드 선택</span>
                                    </button>
                                    </span>
                                </span>

                                <div class="ncua-search-result">
                                    <div class="ncua-search-result__content">
                                        <div class="ncua-table ncua-table--horizontal">
                                            <table>
                                                <colgroup>
                                                    <col width="56px">
                                                    <col width="80px">
                                                    <col width="*">
                                                </colgroup>
                                                <thead>
                                                    <tr>
                                                        <th>
                                                            <div>
                                                                <label class="ncua-checkbox-field ncua-checkbox-field--xs has-text">
                                                                    <span class="ncua-checkbox-input ncua-checkbox-input--xs ncua-checkbox-field__input">
                                                                        <input type="checkbox" id="brandAllCheck">
                                                                    </span>
                                                                </label>
                                                            </div>
                                                        </th>
                                                        <th><div>번호</div></th>
                                                        <th><div class="ncua-left-align">브랜드명</div></th>
                                                    </tr>
                                                </thead>
                                                <tbody id="couponApplyBrand">
                                                    <?php
                                                    if ($couponData['couponApplyProductType'] == 'brand' && $couponData['couponApplyBrand']) {
                                                        foreach ($couponData['couponApplyBrand'] as $k => $v) {
                                                    ?>
                                                        <tr id="idBrand_<?= $couponData['couponApplyBrand'][$k]['no'] ?>">
                                                            <td>
                                                                <div>
                                                                <input type="hidden" name="couponApplyBrand[]" value="<?= $couponData['couponApplyBrand'][$k]['no'] ?>"/>
                                                                <input type="hidden" name="couponApplyBrandName[]" value="<?= $couponData['couponApplyBrand'][$k]['name'] ?>">
                                                                    <label class="ncua-checkbox-field ncua-checkbox-field--xs has-text">
                                                                        <span class="ncua-checkbox-input ncua-checkbox-input--xs ncua-checkbox-field__input">
                                                                            <input type="checkbox" data-target="#idBrand_<?= $couponData['couponApplyBrand'][$k]['no'] ?>">
                                                                        </span>
                                                                    </label>
                                                                </div>
                                                            </td>
                                                            <td><div><?= ($k + 1) ?></div></td>
                                                            <td><div class="ncua-left-align"><?= $couponData['couponApplyBrand'][$k]['name'] ?></div></td>
                                                        </tr>
                                                    <?php
                                                        }
                                                    } else {
                                                    ?>
                                                    <tr class="tr-no-data"><td colspan="3" class="no-data"><div>선택된 브랜드가 없습니다.</div></td></tr>
                                                    <?php
                                                    }
                                                    ?>
                                                </tbody>
                                            </table>
                                        </div>
                                        <div class="ncua-search-result__actions ncua-search-result__actions--bottom">
                                            <button type="button" id="deleteSelectedBrands" class="ncua-btn ncua-btn--xs ncua-btn--destructive ncua-select-delete js-delete-brand">
                                                선택 삭제
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </td>
                    </tr>
                    <tr class="tr-apply-goods tr-apply-use">
                        <th>
                            <div id="couponApplyProductTypeGoodsTitle">발급/사용 가능 특정 상품 선택</div>
                        </th>
                        <td>
                            <div class="ncua-flex-column">
                                <span>
                                    <span class="ncua-radio-field__text">
                                    <button type="button" id="selectApplyGoods" class="ncua-btn ncua-btn--xs ncua-btn--secondary">
                                        <span class="ncua-btn__label">상품 선택</span>
                                    </button>
                                    </span>
                                </span>

                                <div class="ncua-search-result">
                                    <div class="ncua-search-result__content">
                                        <div class="ncua-table ncua-table--horizontal">
                                            <table>
                                                <colgroup>
                                                    <col width="56px">
                                                    <col width="80px">
                                                    <col width="160px">
                                                    <col width="*">
                                                </colgroup>
                                                <thead>
                                                    <tr>
                                                        <th>
                                                            <div>
                                                                <label class="ncua-checkbox-field ncua-checkbox-field--xs has-text">
                                                                    <span class="ncua-checkbox-input ncua-checkbox-input--xs ncua-checkbox-field__input">
                                                                        <input type="checkbox" id="goodsAllCheck">
                                                                    </span>
                                                                </label>
                                                            </div>
                                                        </th>
                                                        <th><div>번호</div></th>
                                                        <th><div>이미지</div></th>
                                                        <th><div class="ncua-left-align">상품명</div></th>
                                                    </tr>
                                                </thead>
                                                <tbody id="couponApplyGoods">
                                                    <?php
                                                    if ($couponData['couponApplyProductType'] == 'goods' && $couponData['couponApplyGoods']) {
                                                        foreach ($couponData['couponApplyGoods'] as $key => $val) {
                                                    ?>
                                                    <tr id="idGoods_<?= $val['goodsNo'] ?>">
                                                        <td>
                                                            <div>
                                                                <input type="hidden" name="couponApplyGoods[]" value="<?= $val['goodsNo'] ?>" />
                                                                <label class="ncua-checkbox-field ncua-checkbox-field--xs has-text">
                                                                    <span class="ncua-checkbox-input ncua-checkbox-input--xs ncua-checkbox-field__input">
                                                                        <input type="checkbox" data-target="#idGoods_<?= $val['goodsNo'] ?>">
                                                                    </span>
                                                                </label>
                                                            </div>
                                                        </td>
                                                        <td><div><?= ($key + 1) ?></div></td>
                                                        <td><div><?= gd_html_goods_image($val['goodsNo'], $val['imageName'], $val['imagePath'], $val['imageStorage'], 50, $val['goodsNm'], '_blank') ?></div></td>
                                                        <td><div class="ncua-left-align"><?= gd_remove_tag($val['goodsNm']) ?></div></td>
                                                    </tr>
                                                    <?php
                                                        }
                                                    } else {
                                                    ?>
                                                    <tr class="tr-no-data"><td colspan="4" class="no-data"><div>선택된 상품이 없습니다.</div></td></tr>
                                                    <?php
                                                    }
                                                    ?>
                                                </tbody>
                                            </table>
                                        </div>
                                        <div class="ncua-search-result__actions ncua-search-result__actions--bottom">
                                            <button type="button" id="deleteSelectedGoods" class="ncua-btn ncua-btn--xs ncua-btn--destructive ncua-select-delete js-delete-goods">
                                                선택 삭제
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </td>
                    </tr>
                    <tr class="tr-except-use">
                        <th><div id="couponExceptTypeTitle">쿠폰 사용 제외 설정</div></th>
                        <td>
                            <div class="ncua-flex-gap except-supply-radio-group">
                                <?php if (gd_use_provider()) { ?>
                                <label class="ncua-checkbox-field ncua-checkbox-field--xs has-text">
                                    <span class="ncua-checkbox-input ncua-checkbox-input--xs ncua-checkbox-field__input">
                                        <input type="checkbox"  name="couponExceptProviderType" value="y" <?= $checked['couponExceptProviderType']['y'] ?> />
                                    </span>
                                    <span class="ncua-checkbox-field__text">특정 공급사</span>
                                </label>
                                <?php } ?>
                                <label class="ncua-checkbox-field ncua-checkbox-field--xs has-text">
                                    <span class="ncua-checkbox-input ncua-checkbox-input--xs ncua-checkbox-field__input">
                                        <input type="checkbox" name="couponExceptCategoryType" value="y" <?= $checked['couponExceptCategoryType']['y'] ?> />
                                    </span>
                                    <span class="ncua-checkbox-field__text">특정 카테고리</span>
                                </label>
                                <label class="ncua-checkbox-field ncua-checkbox-field--xs has-text">
                                    <span class="ncua-checkbox-input ncua-checkbox-input--xs ncua-checkbox-field__input">
                                        <input type="checkbox" name="couponExceptBrandType" value="y" <?= $checked['couponExceptBrandType']['y'] ?> />
                                    </span>
                                    <span class="ncua-checkbox-field__text">특정 브랜드</span>
                                </label>
                                <label class="ncua-checkbox-field ncua-checkbox-field--xs has-text">
                                    <span class="ncua-checkbox-input ncua-checkbox-input--xs ncua-checkbox-field__input">
                                        <input type="checkbox" name="couponExceptGoodsType" value="y" <?= $checked['couponExceptGoodsType']['y'] ?> />
                                    </span>
                                    <span class="ncua-checkbox-field__text">특정 상품</span>
                                </label>
                            </div>
                        </td>
                    </tr>
                    <?php if (gd_use_provider()) { ?>
                    <tr class="tr-except-provider tr-except-use">
                        <th><div id="couponExceptTypeProviderTitle">발급/사용 제외 특정 공급사 선택</div></th>
                        <td>
                            <div class="ncua-flex-column ncua-flex-gap">
                                <div>쿠폰 사용을 제외할 공급사를 선택해주세요.</div>
                                <div id="ncua-except-combo-box-layer"></div>
                                <div id="couponExceptProvider" class="except-supply-tag-group ncua-flex ncua-flex-wrap ncua-gap-4">
                                </div>
                            </div>
                        </td>
                    </tr>
                    <?php } ?>
                    <tr class="tr-except-category tr-except-use">
                        <th>
                            <div id="couponExceptTypeCategoryTitle">발급/사용 제외 특정 카테고리 선택</div>
                        </th>
                        <td>
                            <div class="ncua-flex-column">
                                <span>
                                    <span class="ncua-radio-field__text">
                                    <button type="button" id="selectExceptCategory" class="ncua-btn ncua-btn--xs ncua-btn--secondary">
                                        <span class="ncua-btn__label">카테고리 선택</span>
                                    </button>
                                    </span>
                                </span>

                                <div class="ncua-search-result">
                                    <div class="ncua-search-result__content">
                                        <div class="ncua-table ncua-table--horizontal">
                                            <table>
                                                <colgroup>
                                                    <col width="56px">
                                                    <col width="80px">
                                                    <col width="*">
                                                </colgroup>
                                                <thead>
                                                    <tr>
                                                        <th>
                                                            <div>
                                                                <label class="ncua-checkbox-field ncua-checkbox-field--xs has-text">
                                                                    <span class="ncua-checkbox-input ncua-checkbox-input--xs ncua-checkbox-field__input">
                                                                        <input type="checkbox" id="exceptCategoryAllCheck">
                                                                    </span>
                                                                </label>
                                                            </div>
                                                        </th>
                                                        <th><div>번호</div></th>
                                                        <th><div class="ncua-left-align">카테고리명</div></th>
                                                    </tr>
                                                </thead>
                                                <tbody id="couponExceptCategory">
                                                    <?php
                                                    if ($couponData['couponExceptCategoryType'] == 'y' && $couponData['couponExceptCategory']) {
                                                        foreach ($couponData['couponExceptCategory'] as $k => $v) {
                                                    ?>
                                                        <tr id="idExceptCategory_<?= $couponData['couponExceptCategory'][$k]['no'] ?>">
                                                            <td>
                                                                <div>
                                                                    <input type="hidden" name="couponExceptCategory[]" value="<?= $couponData['couponExceptCategory'][$k]['no'] ?>">
                                                                    <input type="hidden" name="couponExceptCategoryName[]" value="<?= $couponData['couponExceptCategory'][$k]['name'] ?>">
                                                                    <label class="ncua-checkbox-field ncua-checkbox-field--xs has-text">
                                                                        <span class="ncua-checkbox-input ncua-checkbox-input--xs ncua-checkbox-field__input">
                                                                            <input type="checkbox" data-target="#idExceptCategory_<?= $couponData['couponExceptCategory'][$k]['no'] ?>">
                                                                        </span>
                                                                    </label>
                                                                </div>
                                                            </td>
                                                            <td><div><?= ($k + 1) ?></div></td>
                                                            <td><div class="ncua-left-align"><?= $couponData['couponExceptCategory'][$k]['name'] ?></div></td>
                                                        </tr>
                                                    <?php
                                                        }
                                                    } else {
                                                    ?>
                                                    <tr class="tr-no-data"><td colspan="3" class="no-data"><div>선택된 카테고리가 없습니다.</div></td></tr>
                                                    <?php
                                                    }
                                                    ?>
                                                </tbody>
                                            </table>
                                        </div>
                                        <div class="ncua-search-result__actions ncua-search-result__actions--bottom">
                                            <button type="button" id="deleteSelectExceptCategorys" class="ncua-btn ncua-btn--xs ncua-btn--destructive ncua-select-delete js-delete-except-category">
                                                선택 삭제
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </td>
                    </tr>
                    <tr class="tr-except-brand tr-except-use">
                        <th>
                            <div id="couponExceptTypeBrandTitle">발급/사용 제외 특정 브랜드 선택</div>
                        </th>
                        <td>
                            <div class="ncua-flex-column">
                                <span>
                                    <span class="ncua-radio-field__text">
                                    <button type="button" id="selectExceptBrand" class="ncua-btn ncua-btn--xs ncua-btn--secondary">
                                        <span class="ncua-btn__label">브랜드 선택</span>
                                    </button>
                                    </span>
                                </span>

                                <div class="ncua-search-result">
                                    <div class="ncua-search-result__content">
                                        <div class="ncua-table ncua-table--horizontal">
                                            <table>
                                                <colgroup>
                                                    <col width="56px">
                                                    <col width="80px">
                                                    <col width="*">
                                                </colgroup>
                                                <thead>
                                                    <tr>
                                                        <th>
                                                            <div>
                                                                <label class="ncua-checkbox-field ncua-checkbox-field--xs has-text">
                                                                    <span class="ncua-checkbox-input ncua-checkbox-input--xs ncua-checkbox-field__input">
                                                                        <input type="checkbox" id="exceptBrandAllCheck">
                                                                    </span>
                                                                </label>
                                                            </div>
                                                        </th>
                                                        <th><div>번호</div></th>
                                                        <th><div class="ncua-left-align">브랜드명</div></th>
                                                    </tr>
                                                </thead>
                                                <tbody id="couponExceptBrand">
                                                <?php
                                                if ($couponData['couponExceptBrandType'] == 'y' && $couponData['couponExceptBrand']) {
                                                    foreach ($couponData['couponExceptBrand'] as $k => $v) {
                                                ?>
                                                    <tr id="idExceptBrand_<?= $couponData['couponExceptBrand'][$k]['no'] ?>">
                                                        <td>
                                                            <div>
                                                            <input type="hidden" name="couponExceptBrand[]" value="<?= $couponData['couponExceptBrand'][$k]['no'] ?>"/>
                                                            <input type="hidden" name="couponExceptBrandName[]" value="<?= $couponData['couponExceptBrand'][$k]['name'] ?>">
                                                                <label class="ncua-checkbox-field ncua-checkbox-field--xs has-text">
                                                                    <span class="ncua-checkbox-input ncua-checkbox-input--xs ncua-checkbox-field__input">
                                                                        <input type="checkbox" data-target="#idExceptBrand_<?= $couponData['couponExceptBrand'][$k]['no'] ?>">
                                                                    </span>
                                                                </label>
                                                            </div>
                                                        </td>
                                                        <td><div><?= ($k + 1) ?></div></td>
                                                        <td><div class="ncua-left-align"><?= $couponData['couponExceptBrand'][$k]['name'] ?></div></td>
                                                    </tr>
                                                <?php
                                                    }
                                                } else {
                                                ?>
                                                <tr class="tr-no-data"><td colspan="3" class="no-data"><div>선택된 브랜드가 없습니다.</div></td></tr>
                                                <?php
                                                }
                                                ?>
                                                </tbody>
                                            </table>
                                        </div>
                                        <div class="ncua-search-result__actions ncua-search-result__actions--bottom">
                                            <button type="button" id="deleteSelectExceptBrands" class="ncua-btn ncua-btn--xs ncua-btn--destructive ncua-select-delete js-delete-except-brand">
                                                선택 삭제
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </td>
                    </tr>
                    <tr class="tr-except-goods tr-except-use">
                        <th>
                            <div id="couponExceptTypeGoodsTitle">발급/사용 제외 특정 상품 선택</div>
                        </th>
                        <td>
                            <div class="ncua-flex-column">
                                <span>
                                    <span class="ncua-radio-field__text">
                                    <button type="button" id="selectExceptGoods" class="ncua-btn ncua-btn--xs ncua-btn--secondary">
                                        <span class="ncua-btn__label">상품 선택</span>
                                    </button>
                                    </span>
                                </span>

                                <div class="ncua-search-result">
                                    <div class="ncua-search-result__content">
                                        <div class="ncua-table ncua-table--horizontal">
                                            <table>
                                                <colgroup>
                                                    <col width="56px">
                                                    <col width="80px">
                                                    <col width="160px">
                                                    <col width="*">
                                                </colgroup>
                                                <thead>
                                                    <tr>
                                                        <th>
                                                            <div>
                                                                <label class="ncua-checkbox-field ncua-checkbox-field--xs has-text">
                                                                    <span class="ncua-checkbox-input ncua-checkbox-input--xs ncua-checkbox-field__input">
                                                                        <input type="checkbox" id="exceptGoodsAllCheck">
                                                                    </span>
                                                                </label>
                                                            </div>
                                                        </th>
                                                        <th><div>번호</div></th>
                                                        <th><div>이미지</div></th>
                                                        <th><div class="ncua-left-align">상품명</div></th>
                                                    </tr>
                                                </thead>
                                                <tbody id="couponExceptGoods">
                                                <?php
                                                if ($couponData['couponExceptGoodsType'] == 'y' && $couponData['couponExceptGoods']) {
                                                    foreach ($couponData['couponExceptGoods'] as $key => $val) {
                                                ?>
                                                    <tr id="idExceptGoods_<?= $val['goodsNo'] ?>">
                                                        <td>
                                                            <div>
                                                                <input type="hidden" name="couponExceptGoods[]" value="<?= $val['goodsNo'] ?>" />
                                                                <label class="ncua-checkbox-field ncua-checkbox-field--xs has-text">
                                                                    <span class="ncua-checkbox-input ncua-checkbox-input--xs ncua-checkbox-field__input">
                                                                        <input type="checkbox" data-target="#idExceptGoods_<?= $val['goodsNo'] ?>">
                                                                    </span>
                                                                </label>
                                                            </div>
                                                        </td>
                                                        <td><div><span class="number"><?= $key + 1 ?></span></div></td>
                                                        <td><div><?= gd_html_goods_image($val['goodsNo'], $val['imageName'], $val['imagePath'], $val['imageStorage'], 50, $val['goodsNm'], '_blank') ?></div></td>
                                                        <td><div class="ncua-left-align"><a class="ncua-link" href="<?php echo URI_ADMIN; ?>goods/goods_register.php?goodsNo=<?= $val['goodsNo'] ?>" target="_blank"><?= gd_remove_tag($val['goodsNm']) ?></a></div></td>
                                                    </tr>
                                                <?php
                                                    }
                                                } else {
                                                ?>
                                                <tr class="tr-no-data"><td colspan="4" class="no-data"><div>선택된 상품이 없습니다.</div></td></tr>
                                                <?php
                                                }
                                                ?>
                                                </tbody>
                                            </table>
                                        </div>
                                        <div class="ncua-search-result__actions ncua-search-result__actions--bottom">
                                            <button type="button" id="deleteSelectExceptGoods" class="ncua-btn ncua-btn--xs ncua-btn--destructive ncua-select-delete js-delete-except-goods">
                                                선택 삭제
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </td>
                    </tr>
                    <tr class="tr_gift">
                        <th>
                            <div data-tooltip-seq="006">최소 상품 구매 금액 제한</div>
                        </th>
                        <td>
                            <div class="ncua-gap-4">
                                <span class="ncua-select ncua-select--xs">
                                    <span class="ncua-select__content">
                                        <select class="ncua-select__tag" name="couponProductMinOrderType">
                                            <option value="none" <?= $checked['couponProductMinOrderType']['none'] ? 'selected' : '' ?>>제한없음</option>
                                            <option value="product" <?= $checked['couponProductMinOrderType']['product'] ? 'selected' : '' ?>>상품 금액 기준</option>
                                            <option value="order" <?= $checked['couponProductMinOrderType']['order'] ? 'selected' : '' ?>>주문금액</option>
                                        </select>
                                    </span>
                                </span>
                                <span id="couponProductMinOrderTypeText" class="ncua-input__unit">
                                    <?php if ($checked['couponProductMinOrderType']['product']) {
                                        echo '구매금액이';
                                    } else if ($checked['couponProductMinOrderType']['order']) {
                                        echo '기준이';
                                    } ?></span>
                                <div id="couponProductMinOrderPriceInput" class="ncua-input ncua-input--xs ncua-text-input-unit">
                                    <div class="ncua-input__content">
                                        <div class="ncua-input__field ncua-input__field--xs ncua-input-width-120">
                                            <input type="text" name="couponMinOrderPrice" value="<?= gd_money_format($couponData['couponMinOrderPrice'], false); ?>" class="js-number" placeholder="금액 입력" maxlength="8"> 
                                        </div>
                                    </div>
                                    <div class="ncua-input__unit">원 이상인 경우 결제 시 사용 가능</div>
                                </div>
                            </div>
                        </td>
                    </tr>
                    <tr class="tr_gift">
                        <th>
                            <div>같은 유형 쿠폰 중복 사용</div>
                        </th>
                        <td>
                            <div class="ncua-flex-column">
                                <div class="ncua-switch ncua-switch--xs">
                                    <label class="ncua-switch__option ncua-switch__option--left ncua-switch__option--<?= $checked['couponApplyDuplicateType']['y'] ? 'active' : 'inactive' ?>">
                                        <input class="ncua-switch__radio ncua-switch__radio--left" type="radio" name="couponApplyDuplicateType" value="y" <?= $checked['couponApplyDuplicateType']['y'] ?> />
                                        <span class="ncua-switch__label">허용함</span>
                                    </label>
                                    <label class="ncua-switch__option ncua-switch__option--right ncua-switch__option--<?= $checked['couponApplyDuplicateType']['n'] ? 'active' : 'inactive' ?>">
                                        <input class="ncua-switch__radio ncua-switch__radio--right" type="radio" name="couponApplyDuplicateType" value="n" <?= $checked['couponApplyDuplicateType']['n'] ?>  />
                                        <span class="ncua-switch__label">허용안함</span>
                                    </label>
                                </div>
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <th>
                            <div data-tooltip-seq="007">사용기간 만료안내 메시지 발송</div>
                        </th>
                        <td>
                            <div class="ncua-gap-8">
                                <div class="ncua-switch ncua-switch--xs">
                                    <label class="ncua-switch__option ncua-switch__option--left ncua-switch__option--<?= $checked['couponLimitSmsFl']['y'] ? 'active' : 'inactive' ?>">
                                        <input class="ncua-switch__radio ncua-switch__radio--left" type="radio" name="couponLimitSmsFl" value="y" <?= $checked['couponLimitSmsFl']['y'] ?> />
                                        <span class="ncua-switch__label">발송함</span>
                                    </label>
                                    <label class="ncua-switch__option ncua-switch__option--right ncua-switch__option--<?= $checked['couponLimitSmsFl']['n'] ? 'active' : 'inactive' ?>">
                                        <input class="ncua-switch__radio ncua-switch__radio--right" type="radio" name="couponLimitSmsFl" value="n" <?= $checked['couponLimitSmsFl']['n'] ?>  />
                                        <span class="ncua-switch__label">발송안함</span>
                                    </label>
                                </div>
                            </div>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </section>
</section>