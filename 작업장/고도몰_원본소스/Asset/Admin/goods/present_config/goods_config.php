<section class="ncua-card present-config__goods-config">
    <header class="ncua-card__header">
        <h3 class="ncua-card__title" data-tooltip-seq="011" data-tooltip-icon-type="fill">상품 설정</h3>
    </header>
    <section class="ncua-card__body">
        <div>
            <div class="ncua-notice-info">판매/노출 상태와 상관없이 선물하기 기능을 적용할 수 있습니다. (단, 판매/노출 제외 상품에 적용 후 상태 변경 시 선물하기가 자동 적용됩니다.)</div>
            <div class="ncua-notice-info">성인인증 상품, 지역별 추가 배송비 발생 상품, 배송 불가 상품, 방문 수령 상품은 선물하기 기능을 사용할 수 없습니다.</div>
            <div class="ncua-notice-info">선물하기 적용 상품은 상세 페이지에 ‘선물하기’ 버튼이 노출됩니다.</div>
            <div class="ncua-notice-info">직접 선택 옵션은 최대 <?=number_format($goodsLimit)?>개까지 설정할 수 있습니다.</div>
        </div>
        <div class="ncua-table ncua-table--vertical">
            <table>
                <tbody>
                    <tr>
                        <th class="ncua-required"><div data-tooltip-seq="012">상품 설정</div></th>
                        <td class="present-config__goods-config-td">
                            <div class="present-config__goods-config-radio-group js-track-change">
                                <label class="ncua-radio-field ncua-radio-field--xs has-text">
                                    <span class="ncua-radio-input ncua-radio-input--xs ncua-radio-field__input">
                                        <input type="radio" name="applyType" value="all"<?= $checked['applyType']['all'] ?: '' ?>/>
                                    </span>
                                    <span class="ncua-radio-field__text">모든 상품</span>
                                </label>
                                <label class="ncua-radio-field ncua-radio-field--xs has-text">
                                    <span class="ncua-radio-input ncua-radio-input--xs ncua-radio-field__input">
                                        <input type="radio" name="applyType" value="category"<?= $checked['applyType']['category'] ?: '' ?>/>
                                    </span>
                                    <span class="ncua-radio-field__text">카테고리별</span>
                                </label>
                                <label class="ncua-radio-field ncua-radio-field--xs has-text">
                                    <span class="ncua-radio-input ncua-radio-input--xs ncua-radio-field__input">
                                        <input type="radio" name="applyType" value="applyGoods"<?= $checked['applyType']['applyGoods'] ?: '' ?>/>
                                    </span>
                                    <span class="ncua-radio-field__text">직접선택</span>
                                </label>
                            </div>

                            <!-- 상품 설정: 카테고리별 -->
                            <div class="ncua-search-result present-config__goods-config-category">
                                <div class="ncua-search-result__content">
                                    <div class="ncua-table ncua-table--horizontal js-track-change">
                                        <table>
                                            <colgroup>
                                                <col width="56px">
                                                <col width="*">
                                            </colgroup>
                                            <thead>
                                            <tr>
                                                <th>
                                                    <div>
                                                        <label class="ncua-checkbox-field ncua-checkbox-field--xs has-text">
                                                            <span class="ncua-checkbox-input ncua-checkbox-input--xs ncua-checkbox-field__input">
                                                                <input type="checkbox" class="js-checkall" data-target-name="cateCd">
                                                            </span>
                                                        </label>
                                                    </div>
                                                </th>
                                                <th><div data-tooltip-seq="014">카테고리</div></th>
                                            </tr>
                                            </thead>
                                            <tbody>
                                            <?php if (empty($categories)): ?>
                                                <tr class="tr-no-data">
                                                    <td colspan="2" class="no-data">
                                                        <div>선물하기 기능을 사용할 카테고리가 없습니다.</div>
                                                    </td>
                                                </tr>
                                            <?php else: ?>
                                                <?php foreach ($categories as $category): ?>
                                                    <tr>
                                                        <td>
                                                            <div>
                                                                <label class="ncua-checkbox-field ncua-checkbox-field--xs has-text">
                                                                    <span class="ncua-checkbox-input ncua-checkbox-input--xs ncua-checkbox-field__input">
                                                                        <input type="checkbox" name="cateCd[<?= $category['cateCd'] ?>]"
                                                                               value="<?= $category['cateCd'] ?>" <?= $category['checked'] ?>>
                                                                    </span>
                                                                </label>
                                                            </div>
                                                        </td>
                                                        <td>
                                                            <div class="ncua-left-align"><?= $category['cateNm'] ?></div>
                                                        </td>
                                                    </tr>
                                                <?php endforeach; ?>
                                            <?php endif;?>
                                            </tbody>
                                        </table>
                                    </div>
                                    <div class="ncua-table-bottom"></div>
                                </div>
                            </div>
                            <!-- 상품 설정: 직접선택 -->
                            <div class="present-config__goods-config-selected">
                                <div class="ncua-search-result">
                                    <div class="ncua-search-result__summary">
                                        <p class="ncua-search-result__summary-count applyGoods-goods-count">
                                            전체 <strong>00</strong>개
                                        </p>
                                    </div>
                                    <div class="ncua-search-result__content">
                                        <div class="ncua-table ncua-table--horizontal ncua-table--border-bottom-radius-none">
                                            <table>
                                                <colgroup>
                                                    <col width="56px">
                                                    <col>
                                                    <col>
                                                    <col>
                                                    <col>
                                                    <col>
                                                    <col>
                                                </colgroup>
                                                <thead>
                                                <tr>
                                                    <th>
                                                        <div>
                                                            <label class="ncua-checkbox-field ncua-checkbox-field--xs has-text">
                                                                <span class="ncua-checkbox-input ncua-checkbox-input--xs ncua-checkbox-field__input">
                                                                    <input type="checkbox" class="js-checkall" data-target-name="applyGoods-goodsNo">
                                                                </span>
                                                            </label>
                                                        </div>
                                                    </th>
                                                    <th><div data-tooltip-seq="017">상품코드</div></th>
                                                    <th><div data-tooltip-seq="018">이미지</div></th>
                                                    <th><div data-tooltip-seq="019">상품명</div></th>
                                                    <th><div data-tooltip-seq="020">판매가</div></th>
                                                    <th><div data-tooltip-seq="021">공급사</div></th>
                                                    <th><div data-tooltip-seq="022">품절상태</div></th>
                                                </tr>
                                                </thead>
                                                <tbody class="applyGoods-goods-table-body"></tbody>
                                            </table>
                                        </div>
                                        <div class="ncua-search-result__actions ncua-search-result__actions--bottom">
                                            <div class="ncua-search-result__button-group">
                                                <button type="button" class="ncua-btn ncua-btn--xs ncua-btn--secondary select-goods-popup" data-title="상품 선택" data-type="applyGoods">
                                                    상품 추가
                                                </button>
                                                <button type="button" class="ncua-btn ncua-btn--xs ncua-btn--destructive ncua-select-delete delete-check-goods" disabled data-type="applyGoods">
                                                    선택 삭제
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                    <!-- pagination -->
                                    <div class="ncua-pagination applyGoods-pagination"></div>
                                </div>
                            </div>
                        </td>
                    </tr>
                    <!-- 예외상품 설정 -->
                    <tr class="present-config__goods-exception-config">
                        <th><div data-tooltip-seq="013">예외상품 설정</div></th>
                        <td>
                            <div class="ncua-flex-column">
                            <div class="ncua-search-result">
                                <div class="ncua-search-result__summary">
                                    <p class="ncua-search-result__summary-count exceptGoods-goods-count">
                                        전체 <strong>00</strong>개
                                    </p>
                                </div>
                                <div class="ncua-search-result__content">
                                    <div class="ncua-table ncua-table--horizontal ncua-table--border-bottom-radius-none">
                                        <table>
                                            <colgroup>
                                                <col width="56px">
                                                <col>
                                                <col>
                                                <col>
                                            </colgroup>
                                            <thead>
                                                <tr>
                                                    <th>
                                                        <div>
                                                            <label class="ncua-checkbox-field ncua-checkbox-field--xs has-text">
                                                                <span class="ncua-checkbox-input ncua-checkbox-input--xs ncua-checkbox-field__input">
                                                                    <input type="checkbox" class="js-checkall" data-target-name="exceptGoods-goodsNo">
                                                                </span>
                                                            </label>
                                                        </div>
                                                    </th>
                                                    <th><div data-tooltip-seq="014">상품코드</div></th>
                                                    <th><div data-tooltip-seq="015">이미지</div></th>
                                                    <th><div data-tooltip-seq="016">상품명</div></th>
                                                </tr>
                                            </thead>
                                            <tbody class="exceptGoods-goods-table-body"></tbody>
                                        </table>
                                    </div>
                                    <div class="ncua-search-result__actions ncua-search-result__actions--bottom">
                                        <div class="ncua-search-result__button-group">
                                            <button type="button" class="ncua-btn ncua-btn--xs ncua-btn--secondary select-goods-popup" data-title="예외 상품 선택" data-type="exceptGoods">
                                                예외상품 설정하기
                                            </button>
                                            <button type="button" class="ncua-btn ncua-btn--xs ncua-btn--destructive ncua-select-delete delete-check-goods" disabled data-type="exceptGoods">
                                                선택 삭제
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <!-- pagination -->
                            <div class="ncua-pagination exceptGoods-pagination"></div>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </section>
</section>
