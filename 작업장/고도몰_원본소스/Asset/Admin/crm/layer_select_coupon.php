<div class="modal-dialog__content">
    <article class="ncua-content select-coupon">
        <form id="formCouponSearch" method="get">
            <div class="ncua-table ncua-table--vertical">
                <table>
                    <tbody>
                        <tr>
                            <th><div>쿠폰유형</div></th>
                            <td>
                                <div class="ncua-flex-gap js-coupon-use-type">
                                    <label class="ncua-checkbox-field ncua-checkbox-field--xs has-text">
                                        <span class="ncua-checkbox-input ncua-checkbox-input--xs ncua-checkbox-field__input">
                                            <input type="checkbox" value="all" checked>
                                        </span>
                                        <span class="ncua-checkbox-field__text">전체</span>
                                    </label>
                                    <label class="ncua-checkbox-field ncua-checkbox-field--xs has-text">
                                        <span class="ncua-checkbox-input ncua-checkbox-input--xs ncua-checkbox-field__input">
                                            <input type="checkbox" name="couponUseType" value="product" checked>
                                        </span>
                                        <span class="ncua-checkbox-field__text">상품 적용 쿠폰</span>
                                    </label>
                                    <label class="ncua-checkbox-field ncua-checkbox-field--xs has-text">
                                        <span class="ncua-checkbox-input ncua-checkbox-input--xs ncua-checkbox-field__input">
                                            <input type="checkbox" name="couponUseType" value="order" checked>
                                        </span>
                                        <span class="ncua-checkbox-field__text">주문 적용 쿠폰</span>
                                    </label>
                                    <label class="ncua-checkbox-field ncua-checkbox-field--xs has-text">
                                        <span class="ncua-checkbox-input ncua-checkbox-input--xs ncua-checkbox-field__input">
                                            <input type="checkbox" name="couponUseType" value="delivery" checked>
                                        </span>
                                        <span class="ncua-checkbox-field__text">배송비 할인 쿠폰</span>
                                    </label>
                                    <label class="ncua-checkbox-field ncua-checkbox-field--xs has-text">
                                        <span class="ncua-checkbox-input ncua-checkbox-input--xs ncua-checkbox-field__input">
                                            <input type="checkbox" name="couponUseType" value="gift" checked>
                                        </span>
                                        <span class="ncua-checkbox-field__text">기프트 쿠폰</span>
                                    </label>
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <th><div>사용범위</div></th>
                            <td>
                                <div class="ncua-gap-8 js-coupon-device-type">
                                    <label class="ncua-checkbox-field ncua-checkbox-field--xs has-text">
                                        <span class="ncua-checkbox-input ncua-checkbox-input--xs ncua-checkbox-field__input">
                                            <input type="checkbox" value="all" checked>
                                        </span>
                                        <span class="ncua-checkbox-field__text">전체</span>
                                    </label>
                                    <label class="ncua-checkbox-field ncua-checkbox-field--xs has-text">
                                        <span class="ncua-checkbox-input ncua-checkbox-input--xs ncua-checkbox-field__input">
                                            <input type="checkbox" name="couponDeviceType" value="pc" checked>
                                        </span>
                                        <span class="ncua-checkbox-field__text">PC</span>
                                    </label>
                                    <label class="ncua-checkbox-field ncua-checkbox-field--xs has-text">
                                        <span class="ncua-checkbox-input ncua-checkbox-input--xs ncua-checkbox-field__input">
                                            <input type="checkbox" name="couponDeviceType" value="mobile" checked>
                                        </span>
                                        <span class="ncua-checkbox-field__text">모바일</span>
                                    </label>
                                    <label class="ncua-checkbox-field ncua-checkbox-field--xs has-text">
                                        <span class="ncua-checkbox-input ncua-checkbox-input--xs ncua-checkbox-field__input">
                                            <input type="checkbox" name="couponDeviceType" value="All" checked>
                                        </span>
                                        <span class="ncua-checkbox-field__text">PC+모바일</span>
                                    </label>
                                </div>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
            <div class="ncua-btn-group ncua-align-right">
                <button type="reset" class="ncua-btn ncua-btn--xs ncua-btn--text has-underline">초기화</button>
                <button type="button" class="ncua-btn ncua-btn--xs ncua-btn--secondary search-goods-btn" onclick="loadCoupons()">검색</button>
            </div>
        </form>

        <div id="layerSelectCouponResult"></div>
    </article>
</div>
<div class="modal-dialog__footer">
    <button type="button" class="ncua-btn ncua-btn--sm ncua-btn--secondary-gray" onclick="layer_close();">취소</button>
    <button type="button" class="ncua-btn ncua-btn--sm ncua-btn--primary" onclick="addSelectedCoupon();">추가</button>
</div>
<script type="text/javascript">
    const formGroupSearch = document.querySelector('#formCouponSearch');

    $(document).ready(function () {
        initializeSelectCouponEvents();
        initializeSelectCouponElements();

        loadCoupons();
    });

    function initializeSelectCouponEvents() {
        formGroupSearch.querySelector('button[type="reset"]')?.addEventListener('click', () => {
            setTimeout(() => {
                loadCoupons();
            }, 100);
        });
    }

    function initializeSelectCouponElements() {
        ['.js-coupon-use-type', '.js-coupon-device-type'].forEach(selector => new CheckboxGroup(selector));
    }

    function addSelectedCoupon() {
        const selectedCoupon = document.querySelector('#layerSelectCouponResult input[name="couponNo"]:checked');
        if (!selectedCoupon) {
            NCDSAlert({message: '쿠폰을 선택해주세요.', iconType: 'error'});
            return;
        }

        const couponNo = selectedCoupon.value;
        const couponName = selectedCoupon.dataset.couponName || '';
        const couponUseType = selectedCoupon.dataset.couponUseType || '';
        const couponPeriod = selectedCoupon.dataset.couponPeriod || '';
        const couponDeviceType = selectedCoupon.dataset.couponDeviceType || '';

        const couponDetailContainer = document.querySelector('#kakaoFriendTalkcouponTable .coupon-detail-container');
        if(!couponDetailContainer) {
            return;
        }
        const couponData = {couponName: couponName, couponUseType: couponUseType, couponPeriod: couponPeriod, couponDeviceType: couponDeviceType};

        // 쿠폰 상세 Element 생성 및 추가
        document.querySelector('#selectCoupon').remove();
        couponDetailContainer.innerHTML = '';
        couponDetailContainer.appendChild(generateCouponColumn(couponData));

        // 쿠폰 번호 추가
        const couponNoInput = document.querySelector('input[name="friendtalkCouponNo"]');
        couponNoInput.value = couponNo;
        couponNoInput.dataset.couponName = couponName;
        couponNoInput.dataset.couponPeriod = couponPeriod;
        couponNoInput.dataset.couponUseType = couponUseType;
        couponNoInput.dataset.couponDeviceType = couponDeviceType;
        window.preview.setCoupon({text: 'couponName', date: 'couponPeriod'})

        // 툴팁 적용
        if (window.GodoCosGuide && window.cosData) {
                window.GodoCosGuide.apply(cosData);
            }

        layer_close();
    }

    async function loadCoupons(formData = null) {
        try {
            if (!formData) {
                formData = new FormData(formGroupSearch);
            }
            const couponUseTypeCheckboxes = formGroupSearch.querySelectorAll('input[name="couponUseType"]:checked');
            const couponUseTypeValues = Array.from(couponUseTypeCheckboxes).map(cb => cb.value).filter(v => v !== '');
            formData.delete('couponUseType');
            if (couponUseTypeValues.length > 0 && !Array.from(couponUseTypeCheckboxes).some(cb => cb.value === '')) {
                formData.append('couponUseTypeList', couponUseTypeValues.join(","));
            }

            const couponDeviceTypeCheckboxes = formGroupSearch.querySelectorAll('input[name="couponDeviceType"]:checked');
            const couponDeviceTypeValues = Array.from(couponDeviceTypeCheckboxes).map(cb => cb.value).filter(v => v !== '');
            formData.delete('couponDeviceType');
            if (couponDeviceTypeValues.length > 0 && !Array.from(couponDeviceTypeCheckboxes).some(cb => cb.value === '')) {
                formData.append('couponDeviceTypeList', couponDeviceTypeValues.join(","));
            }

            const params = new URLSearchParams(formData).toString();
            const response = await fetch(`./layer_select_coupon_result.php?${params}`, {
                method: 'GET',
                headers: { 'X-Requested-With': 'XMLHttpRequest' }
            });

            if (!response.ok) {
                const errorText = await response.text();
                throw new Error(`HTTP ${response.status}: ${errorText}`);
            }

            $('#layerSelectCouponResult').html(await response.text());
        } catch (e) {
            logger.error(e);
        }
    }
</script>
