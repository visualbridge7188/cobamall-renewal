<div class="ncua-search-result">
    <div class="ncua-search-result__summary">
        <p class="ncua-search-result__summary-count">
            총 <strong><?= $couponSearchCount ?></strong>건
        </p>
    </div>
    <div class="ncua-search-result__content">
        <div class="ncua-table ncua-table--horizontal">
            <table>
                <colgroup>
                    <col width="56px"/>
                    <col width="80px"/>
                    <col width="170px"/>
                    <col/>
                    <col/>
                    <col width="140px"/>
                </colgroup>
                <thead>
                    <tr>
                        <th><div>선택</div></th>
                        <th><div>쿠폰번호</div></th>
                        <th><div>쿠폰 유형</div></th>
                        <th><div>쿠폰명</div></th>
                        <th><div>사용기간</div></th>
                        <th><div>사용범위</div></th>
                    </tr>
                </thead>
                <tbody>
                <?php if (!empty($couponData)): ?>
                    <?php foreach ($couponData as $coupon): ?>
                        <?php
                            $couponUseTypeText = match($coupon['couponUseType']) {
                                'product' => '상품 적용 쿠폰',
                                'order' => '주문 적용 쿠폰',
                                'delivery' => '배송비 할인 쿠폰',
                                'gift' => '기프트 쿠폰',
                                default => ''
                            };
                            $couponDeviceTypeText = match($coupon['couponDeviceType']) {
                                'all' => 'PC+모바일',
                                'pc' => 'PC',
                                'mobile' => '모바일',
                                default => ''
                            };
                            if ($coupon['couponUsePeriodType'] === 'period') {
                                $couponPeriodText = date('Y-m-d H:i:s', strtotime($coupon['couponUsePeriodStartDate'])) . ' ~ ' . date('Y-m-d H:i:s', strtotime($coupon['couponUsePeriodEndDate']));
                            } else if ($coupon['couponUsePeriodType'] === 'day') {
                                $couponPeriodText = '발급일로부터 ' . $coupon['couponUsePeriodDay'] . '일까지';
                                if (strtotime($coupon['couponUseDateLimit']) > 0) {
                                    $couponPeriodText .= ' (종료일 : ' . $coupon['couponUseDateLimit'] . ')';
                                } else {
                                    $couponPeriodText .= ' (종료일 : 없음)';
                                }
                            } else {
                                $couponPeriodText = '';
                            }
                        ?>
                        <tr>
                            <td>
                                <div>
                                    <label class="ncua-radio-field ncua-radio-field--xs has-text">
                                        <span class="ncua-radio-input ncua-radio-input--xs ncua-radio-field__input">
                                            <input name="couponNo" type="radio" value="<?= $coupon['couponNo'] ?>"
                                                   data-coupon-name="<?= htmlspecialchars($coupon['couponNm'], ENT_QUOTES) ?>"
                                                   data-coupon-use-type="<?= htmlspecialchars($couponUseTypeText, ENT_QUOTES) ?>"
                                                   data-coupon-period="<?= htmlspecialchars($couponPeriodText, ENT_QUOTES) ?>"
                                                   data-coupon-device-type="<?= htmlspecialchars($couponDeviceTypeText, ENT_QUOTES) ?>"
                                            />
                                        </span>
                                    </label>
                                </div>
                            </td>
                            <td>
                                <div><a class="ncua-btn ncua-btn--xs ncua-btn--text has-underline" href="../../promotion/coupon_regist.php?couponNo=<?= $coupon['couponNo'] ?>" target="_blank"><?= $coupon['couponNo'] ?></a></div>
                            </td>
                            <td>
                                <div class="coupon-detail-use-type">
                                    <?= $couponUseTypeText ?>
                                </div>
                            </td>
                            <td>
                                <div class="coupon-detail-name">
                                    <?= $coupon['couponNm'] ?>
                                </div>
                            </td>
                            <td>
                                <div class="coupon-detail-period">
                                    <?php if ($coupon['couponUsePeriodType'] === 'period') { ?>
                                        시작일: <?= date('Y-m-d H:i:s', strtotime($coupon['couponUsePeriodStartDate'])) ?><br />
                                        종료일: <?= date('Y-m-d H:i:s', strtotime($coupon['couponUsePeriodEndDate'])) ?>
                                    <?php } else if ($coupon['couponUsePeriodType'] === 'day') { ?>
                                        발급일로부터 <?= $coupon['couponUsePeriodDay'] ?>일까지<br />종료일 : <?= strtotime($coupon['couponUseDateLimit']) > 0 ? $coupon['couponUseDateLimit'] : '없음' ?>
                                    <?php } ?>
                                </div>
                            </td>
                            <td>
                                <div class="coupon-detail-device-type">
                                    <?= $couponDeviceTypeText ?>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="6">
                            <div class="no-data">검색 내역이 없습니다.</div>
                        </td>
                    </tr>
                <?php endif; ?>
                </tbody>
            </table>
        </div>
        <div class="ncua-table-bottom"></div>
    </div>
    <div class="ncua-pagination"><?= $page->getPage('loadCouponsByPage(\'PAGELINK\')'); ?></div>
</div>
<script type="text/javascript">
    const requestData = JSON.parse('<?= json_encode($requestData, JSON_UNESCAPED_UNICODE); ?>');

    async function loadCouponsByPage(pageLink = null) {
        try {
            let page = 1;
            if (pageLink) {
                const pageLinkParams = new URLSearchParams(pageLink);
                page = pageLinkParams.get('page');
            }

            const formData = new FormData();
            Object.entries(requestData).forEach(([key, value]) => {
                if (Array.isArray(value)) {
                    value.forEach(v => formData.append(`${key}[]`, v));
                } else {
                    formData.append(key, value);
                }
            });
            formData.append('pagelink', `page=${page}`);

            await loadCoupons(formData);
        } catch (e) {
            logger.error(e);
        }
    }
</script>
