<section class="ncua-card">
    <header class="ncua-card__header">
        <h3 class="ncua-card__title">기본 정보</h3>
    </header>
    <section class="ncua-card__body">
        <div class="ncua-table ncua-table--vertical">
            <table>
                <tbody>
                    <tr>
                        <th class="ncua-required"><div>쿠폰명</div></th>
                        <td>
                            <div>
                                <div class="ncua-input ncua-input--xs">
                                    <div class="ncua-input__content-wrap">
                                        <div class="ncua-input__content">
                                            <div class="ncua-input__field ncua-input__field--xs ncua-input-width-520">
                                                <input placeholder="쿠폰명을 입력하세요." name="couponNm" data-charcount-key="couponNm" value="<?= $couponData['couponNm'] ?>" class="ncua-coupon__name" type="text" maxlength="30" />
                                            </div>
                                        </div>
                                        <div class="ncua-input__field-text-count" data-charcount-text="couponNm">
                                            <output class="ncua-input__field-text-count-current">0</output>
                                            <span>/30</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <th><div>쿠폰설명</div></th>
                        <td>
                            <div>
                                <div class="ncua-input ncua-input--xs">
                                    <div class="ncua-input__content-wrap">
                                        <div class="ncua-input__content">
                                            <div class="ncua-input__field ncua-input__field--xs ncua-input-width-520">
                                                <input placeholder="쿠폰 설명을 입력하세요." name="couponDescribed" data-charcount-key="couponDescribed" value="<?= $couponData['couponDescribed'] ?>" class="ncua-coupon__description" type="text" maxlength="50" />
                                            </div>
                                        </div>
                                        <div class="ncua-input__field-text-count" data-charcount-text="couponDescribed">
                                            <output class="ncua-input__field-text-count-current">0</output>
                                            <span>/50</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <th>
                            <div>쿠폰 노출 이미지</div>
                        </th>
                        <td>
                            <div class="ncua-flex-column ncua-flex-gap ncua-coupon-image__group">
                                <div class="ncua-flex-column ncua-gap-8 ncua-coupon-image__group-default">
                                    <label class="ncua-radio-field ncua-radio-field--xs has-text">
                                        <span class="ncua-radio-input ncua-radio-input--xs ncua-radio-field__input">
                                            <input type="radio" name="couponImageType" value="basic" <?= $checked['couponImageType']['basic'] ?> />
                                        </span>
                                        <span><span class="ncua-radio-field__text">기본이미지</span></span>
                                    </label>
                                    <img src="<?= PATH_ADMIN_GD_SHARE ?>ncds/image/coupon.svg" alt="기본쿠폰이미지" />
                                </div>
                                <div class="ncua-flex-column ncua-gap-8">
                                    <label class="ncua-radio-field ncua-radio-field--xs has-text">
                                        <span class="ncua-radio-input ncua-radio-input--xs ncua-radio-field__input">
                                            <input type="radio" name="couponImageType" value="self" <?= $checked['couponImageType']['self'] ?> />
                                        </span>
                                        <span><span class="ncua-radio-field__text">직접등록</span></span>
                                    </label>
                                    <div class="ncua-coupon-image__preview ncua-gap-8">
                                    <input name="couponImage" id="couponImageInput" tabindex="-1" aria-hidden="true" type="file" />
                                    <?php if ($couponData['mode'] === 'insertCouponRegist' && !empty($couponData['couponImage'])) { ?>
                                    <input type="hidden" name="couponCopyImageUrl" value="<?= $couponData['couponImage'] ?>" />
                                    <?php } ?>
                                        <div id="image-file-input-container"></div>
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