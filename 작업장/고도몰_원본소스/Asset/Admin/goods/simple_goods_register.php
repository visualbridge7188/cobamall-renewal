<link rel="stylesheet" href="<?= PATH_ADMIN_GD_SHARE ?>css/goods/simple_goods.css">
<link type="text/css" href="<?=gd_set_browser_cache(PATH_ADMIN_GD_SHARE . 'ncds/css/ncds-editor/' . NCDS_EDITOR_VERSION . '/ncds-editor.css')?>" rel="stylesheet"/>
<script type="text/javascript" src="<?=gd_set_browser_cache(PATH_ADMIN_GD_SHARE . 'ncds/script/ncds-editor/' . NCDS_EDITOR_VERSION . '/ncds-editor.js')?>"></script>
<script defer src="<?=gd_set_browser_cache(PATH_ADMIN_GD_SHARE . 'ncds/script/ncds-multi-select/ncds-multi-select.js')?>"></script>
<script defer src="<?=gd_set_browser_cache(PATH_ADMIN_GD_SHARE . 'ncds/script/common/supply-combo-box.js')?>"></script>
<script defer src="<?=gd_set_browser_cache(PATH_ADMIN_GD_SHARE . 'ncds/script/common/ncds-jquery-validator.js')?>"></script>
<script defer src="<?=gd_set_browser_cache(PATH_ADMIN_GD_SHARE . 'ncds/script/common/number-only.js')?>"></script>
<script defer src="<?=gd_set_browser_cache(PATH_ADMIN_GD_SHARE . 'ncds/script/common/charcount.js')?>"></script>
<script type="text/javascript" src="/admin/gd_share/script/jquery/jquery.multi_select_box.js"></script>

<article class="ncua-content simple-goods-register">
    <form id="frmSimpleGoods" name="frmSimpleGoods" action="./goods_ps.php" method="post" enctype="multipart/form-data">
        <input type="hidden" name="mode" value="simple_register" />
        <input type="hidden" name="simpleGoodsToken" value="<?=$simpleGoodsToken?>" />
        <input type="hidden" name="imageStorage" value="<?=$defaultImageStorage?>" />
        <input type="hidden" name="deliverySno" value="" />
        <input type="hidden" name="optionReged" value="y" />
        <?php if (gd_is_provider()): ?>
            <input type="hidden" name="scmFl" value="y" />
            <input type="hidden" name="scmNo" value="<?= gd_htmlspecialchars($scmNo) ?>" />
        <?php else: ?>
            <input type="hidden" name="scmNo" id="scmNoInput" value="<?= DEFAULT_CODE_SCMNO ?>" />
        <?php endif; ?>

        <header class="page-header js-affix ncua-page-header">
            <h3 class="ncua-help-manual"><?= end($naviMenu->location); ?></h3>
            <div class="ncua-page-header__actions">
                <button type="button" class="ncua-btn ncua-btn--md ncua-btn--secondary-gray js-sgr-list-btn" onclick="location.href='./goods_list.php'"><span class="ncua-btn__label">목록</span></button>
                <button type="submit" class="ncua-btn ncua-btn--md ncua-btn--primary js-sgr-save-btn" id="saveBtn"><span class="ncua-btn__label">저장</span></button>
            </div>
        </header>

        <div class="sgr-notification-banner">
            <div class="ncua-full-width-notification ncua-full-width-notification--info" role="alert" style="min-height: 52px; display: flex; align-items: center;">
                <div class="ncua-full-width-notification__container" style="padding: 15px 40px; max-width: none; width: 100%;">
                    <div class="ncua-full-width-notification__content">
                        <div class="ncua-full-width-notification__content-wrapper">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="none" color="#5720B7" class="ncua-full-width-notification__icon"><path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 16v-4m0-4h.01M22 12c0 5.523-4.477 10-10 10S2 17.523 2 12 6.477 2 12 2s10 4.477 10 10"></path></svg>
                            <div class="ncua-full-width-notification__text-container">
                                <span class="ncua-full-width-notification__title">상품 간편 등록에서는 상품 판매에 필요한 핵심 정보만 입력이 가능하며, 세부적인 설정이 필요한 경우 <a href="./goods_register.php" class="sgr-inline-link">상품등록</a>을 이용해주세요.</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Content Area -->
        <!-- Block 1: 기본정보 -->
        <section class="ncua-card" id="sgr-block-basic">
            <header class="ncua-card__header">
                <h3 class="ncua-card__title">기본정보</h3>
                <span class="ncua-card__sub-info"><strong>*</strong> 는 필수 입력 항목입니다.</span>
            </header>
            <section class="ncua-card__body">
                <div class="ncua-table ncua-table--vertical"><table>
                        <tbody>
                        <?php if (!gd_is_provider()): ?>
                            <tr>
                                <th><div>공급사구분</div></th>
                                <td>
                                    <div class="sgr-scm-area">
                                        <div class="sgr-scm-area__radio sgr-scm-radio-group">
                                            <label class="ncua-radio-field ncua-radio-field--xs has-text">
                                            <span class="ncua-radio-input ncua-radio-input--xs ncua-radio-field__input">
                                                <input type="radio" name="scmFl" value="n" checked/>
                                            </span>
                                                <span><span class="ncua-radio-field__text">본사</span></span>
                                            </label>
                                            <label class="ncua-radio-field ncua-radio-field--xs has-text">
                                            <span class="ncua-radio-input ncua-radio-input--xs ncua-radio-field__input">
                                                <input type="radio" name="scmFl" value="y"/>
                                            </span>
                                                <span><span class="ncua-radio-field__text">공급사</span></span>
                                            </label>
                                        </div>
                                        <div class="sgr-scm-area__main" id="scmSelectBox" style="display: none;">
                                            <div id="scmComboBox"></div>
                                        </div>
                                    </div>
                                    <div id="scmTagArea" class="sgr-scm-area__tags" style="display: none;" aria-live="polite"></div>
                                </td>
                            </tr>
                        <?php endif; ?>
                        <tr>
                            <th><div>상품코드</div></th>
                            <td><div><span class="sgr-readonly-text">저장 시 자동 생성</span></div></td>
                        </tr>
                        <tr>
                            <th>
                                <div data-tooltip-seq="001">
                                    자체상품코드
                                </div>
                            </th>
                            <td>
                                <div class="sgr-input-field" style="width: 400px;">
                                    <div class="ncua-input ncua-input--xs ncua-input--full-width-with-count js-sgr-goodsCd-wrap" style="width: 100%;">
                                        <div class="ncua-input__content-wrap" style="width: 100%;">
                                            <div class="ncua-input__content" style="width: 100%;">
                                                <div class="ncua-input__field ncua-input__field--xs" style="width: 100%;">
                                                    <input type="text" name="goodsCd" id="goodsCd" class="js-sgr-charcount" data-charcount-key="goodsCd" maxlength="30" style="width: 100%;" />
                                                </div>
                                            </div>
                                            <div class="ncua-input__field-text-count" data-charcount-text="goodsCd">
                                                <output class="ncua-input__field-text-count-current">0</output><span>/30</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <th class="ncua-required">
                                <div data-tooltip-seq="002">
                                    상품명
                                </div>
                            </th>
                            <td>
                                <div class="sgr-input-field" style="width: 520px;">
                                    <div class="ncua-input ncua-input--xs ncua-input--full-width-with-count js-sgr-goodsNm-wrap" style="width: 100%;">
                                        <div class="ncua-input__content-wrap" style="width: 100%;">
                                            <div class="ncua-input__content" style="width: 100%;">
                                                <div class="ncua-input__field ncua-input__field--xs" style="width: 100%;">
                                                    <input type="text" name="goodsNm" id="goodsNm" class="js-sgr-charcount" data-charcount-key="goodsNm" placeholder="상품명을 입력하세요." maxlength="250" style="width: 100%;" />
                                                </div>
                                            </div>
                                            <div class="ncua-input__field-text-count" data-charcount-text="goodsNm">
                                                <output class="ncua-input__field-text-count-current">0</output><span>/250</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </td>
                        </tr>
                        <tr class="is-tall-row is-visual-last" id="cateInputRow">
                            <th>
                                <div data-tooltip-seq="003">
                                    카테고리 선택
                                </div>
                            </th>
                            <td>
                                <div class="sgr-cate-wrap">
                                    <div class="sgr-cate-toolbar">
                                        <button type="button" class="ncua-btn ncua-btn--xs ncua-btn--secondary-gray js-sgr-cate-bulk-btn"><span class="ncua-btn__label">카테고리 일괄선택</span></button>
                                        <?php if (!$isProvider): ?>
                                            <button type="button" class="ncua-btn ncua-btn--xs ncua-btn--secondary-gray" onclick="window.open('/goods/category_tree.php', '_blank')"><span class="ncua-btn__label">새 카테고리 등록</span></button>
                                        <?php endif; ?>
                                    </div>
                                    <div class="sgr-cate-container">
                                        <div class="sgr-cate-breadcrumb" id="cateBreadcrumb">
                                            <span style="color:#A4A5A8">카테고리를 선택하세요</span>
                                        </div>
                                        <div class="sgr-cate-browser" id="categoryBrowser">
                                            <div class="sgr-cate-column" id="cateCol0"></div>
                                            <div class="sgr-cate-column" id="cateCol1"></div>
                                            <div class="sgr-cate-column" id="cateCol2"></div>
                                            <div class="sgr-cate-column" id="cateCol3"></div>
                                            <div class="sgr-cate-select-btn">
                                                <button type="button" class="ncua-btn ncua-btn--xs ncua-btn--secondary js-sgr-cate-select-btn"><span class="ncua-btn__label">선택</span></button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </td>
                        </tr>
                        <tr class="is-tall-row" id="selectedCateRow" style="display: none;">
                            <th>
                                <div data-tooltip-seq="012">
                                    선택된 카테고리
                                </div>
                            </th>
                            <td>
                                <div class="sgr-cate-wrap">
                                    <div class="ncua-accordion ncua-accordion--blue">
                                        <div class="ncua-accordion__summary-style">카테고리 등록 안내</div>
                                        <div class="ncua-accordion__content">
                                            <div class="ncua-notice-info">카테고리 등록 시 상위카테고리는 자동 등록되며, 등록된 카테고리에 상품이 노출됩니다.</div>
                                            <div class="ncua-notice-info">상품 노출을 원하지 않는 카테고리는 '삭제'버튼을 이용하여 삭제할 수 있습니다.</div>
                                            <div class="ncua-notice-info">등록하신 카테고리들 중 체크된 카테고리가 대표 카테고리로 설정됩니다.</div>
                                        </div>
                                    </div>
                                    <div class="ncua-table ncua-table--horizontal">
                                        <table>
                                            <colgroup>
                                                <col style="width: 80px;">
                                                <col style="width: 120px;">
                                                <col>
                                                <col style="width: 160px;">
                                                <col style="width: 100px;">
                                            </colgroup>
                                            <thead>
                                            <tr>
                                                <th><div>대표설정</div></th>
                                                <th><div>노출상점</div></th>
                                                <th><div>카테고리</div></th>
                                                <th><div>카테고리 코드</div></th>
                                                <th><div>연결해제</div></th>
                                            </tr>
                                            </thead>
                                            <tbody id="selectedCateBody">
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </td>
                        </tr>
                        <tr id="cateTopFixRow" style="display: none;">
                            <th>
                                <div data-tooltip-seq="004">
                                    카테고리 페이지 상단 고정
                                </div>
                            </th>
                            <td>
                                <div>
                                    <div class="ncua-switch ncua-switch--xs">
                                        <label class="ncua-switch__option ncua-switch__option--left ncua-switch__option--inactive">
                                            <input class="ncua-switch__radio ncua-switch__radio--left" type="radio" name="cateTopFix" value="y" />
                                            <span class="ncua-switch__label">사용함</span>
                                        </label>
                                        <label class="ncua-switch__option ncua-switch__option--right ncua-switch__option--active">
                                            <input class="ncua-switch__radio ncua-switch__radio--right" type="radio" name="cateTopFix" value="n" checked="checked" />
                                            <span class="ncua-switch__label">사용안함</span>
                                        </label>
                                    </div>
                                    <input type="hidden" name="goodsSortTop" value="n" />
                                </div>
                            </td>
                        </tr>
                        </tbody>
                    </table></div>
            </section>
        </section>

        <!-- Block 2: 노출/판매 설정 -->
        <section class="ncua-card" id="sgr-block-display-sale">
            <header class="ncua-card__header">
                <h3 class="ncua-card__title">노출/판매 설정</h3>
            </header>
            <section class="ncua-card__body">
                <div class="ncua-table ncua-table--vertical"><table>
                        <tbody>
                        <tr>
                            <th>
                                <div data-tooltip-seq="005">
                                    노출 상태
                                </div>
                            </th>
                            <td>
                                <div class="sgr-display-sale-switches">
                                    <div class="sgr-display-sale-switches__item">
                                        <span class="sgr-display-sale-switches__label">PC 쇼핑몰</span>
                                        <div class="ncua-switch ncua-switch--xs">
                                            <label class="ncua-switch__option ncua-switch__option--left ncua-switch__option--active">
                                                <input class="ncua-switch__radio ncua-switch__radio--left" type="radio" name="goodsDisplayFl" value="y" checked="checked" />
                                                <span class="ncua-switch__label">노출함</span>
                                            </label>
                                            <label class="ncua-switch__option ncua-switch__option--right ncua-switch__option--inactive">
                                                <input class="ncua-switch__radio ncua-switch__radio--right" type="radio" name="goodsDisplayFl" value="n" />
                                                <span class="ncua-switch__label">노출안함</span>
                                            </label>
                                        </div>
                                    </div>
                                    <?php if ($mobileShopFl === 'y'): ?>
                                    <div class="sgr-display-sale-switches__item">
                                        <span class="sgr-display-sale-switches__label">모바일 쇼핑몰</span>
                                        <div class="ncua-switch ncua-switch--xs">
                                            <label class="ncua-switch__option ncua-switch__option--left ncua-switch__option--active">
                                                <input class="ncua-switch__radio ncua-switch__radio--left" type="radio" name="goodsDisplayMobileFl" value="y" checked="checked" />
                                                <span class="ncua-switch__label">노출함</span>
                                            </label>
                                            <label class="ncua-switch__option ncua-switch__option--right ncua-switch__option--inactive">
                                                <input class="ncua-switch__radio ncua-switch__radio--right" type="radio" name="goodsDisplayMobileFl" value="n" />
                                                <span class="ncua-switch__label">노출안함</span>
                                            </label>
                                        </div>
                                    </div>
                                    <?php else: ?>
                                        <input type="hidden" name="goodsDisplayMobileFl" value="y" />
                                    <?php endif; ?>
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <th>
                                <div data-tooltip-seq="006">
                                    판매 상태
                                </div>
                            </th>
                            <td>
                                <div class="sgr-display-sale-switches">
                                    <div class="sgr-display-sale-switches__item">
                                        <span class="sgr-display-sale-switches__label">PC 쇼핑몰</span>
                                        <div class="ncua-switch ncua-switch--xs">
                                            <label class="ncua-switch__option ncua-switch__option--left ncua-switch__option--active">
                                                <input class="ncua-switch__radio ncua-switch__radio--left" type="radio" name="goodsSellFl" value="y" checked="checked" />
                                                <span class="ncua-switch__label">판매함</span>
                                            </label>
                                            <label class="ncua-switch__option ncua-switch__option--right ncua-switch__option--inactive">
                                                <input class="ncua-switch__radio ncua-switch__radio--right" type="radio" name="goodsSellFl" value="n" />
                                                <span class="ncua-switch__label">판매안함</span>
                                            </label>
                                        </div>
                                    </div>
                                    <?php if ($mobileShopFl === 'y'): ?>
                                    <div class="sgr-display-sale-switches__item">
                                        <span class="sgr-display-sale-switches__label">모바일 쇼핑몰</span>
                                        <div class="ncua-switch ncua-switch--xs">
                                            <label class="ncua-switch__option ncua-switch__option--left ncua-switch__option--active">
                                                <input class="ncua-switch__radio ncua-switch__radio--left" type="radio" name="goodsSellMobileFl" value="y" checked="checked" />
                                                <span class="ncua-switch__label">판매함</span>
                                            </label>
                                            <label class="ncua-switch__option ncua-switch__option--right ncua-switch__option--inactive">
                                                <input class="ncua-switch__radio ncua-switch__radio--right" type="radio" name="goodsSellMobileFl" value="n" />
                                                <span class="ncua-switch__label">판매안함</span>
                                            </label>
                                        </div>
                                    </div>
                                    <?php else: ?>
                                        <input type="hidden" name="goodsSellMobileFl" value="y" />
                                    <?php endif; ?>
                                </div>
                            </td>
                        </tr>
                        </tbody>
                    </table></div>
            </section>
        </section>

        <!-- Block 3: 추가정보 -->
        <section class="ncua-card" id="sgr-block-additional">
            <header class="ncua-card__header">
                <h3 class="ncua-card__title">추가정보</h3>
            </header>
            <section class="ncua-card__body">
                <div class="ncua-table ncua-table--vertical"><table>
                        <tbody>
                        <tr>
                            <th><div>제조사</div></th>
                            <td>
                                <div class="sgr-input-field" style="width: 400px;">
                                    <div class="ncua-input ncua-input--xs ncua-input--full-width-with-count js-sgr-makerNm-wrap" style="width: 100%;">
                                        <div class="ncua-input__content-wrap" style="width: 100%;">
                                            <div class="ncua-input__content" style="width: 100%;">
                                                <div class="ncua-input__field ncua-input__field--xs" style="width: 100%;">
                                                    <input type="text" name="makerNm" id="makerNm" class="js-sgr-charcount" data-charcount-key="makerNm" placeholder="제조사를 입력하세요." maxlength="30" style="width: 100%;" />
                                                </div>
                                            </div>
                                            <div class="ncua-input__field-text-count" data-charcount-text="makerNm">
                                                <output class="ncua-input__field-text-count-current">0</output><span>/30</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <th><div>원산지</div></th>
                            <td>
                                <div class="sgr-input-field" style="width: 400px;">
                                    <div class="ncua-input ncua-input--xs ncua-input--full-width-with-count js-sgr-originNm-wrap" style="width: 100%;">
                                        <div class="ncua-input__content-wrap" style="width: 100%;">
                                            <div class="ncua-input__content" style="width: 100%;">
                                                <div class="ncua-input__field ncua-input__field--xs" style="width: 100%;">
                                                    <input type="text" name="originNm" id="originNm" class="js-sgr-charcount" data-charcount-key="originNm" placeholder="원산지를 입력하세요." maxlength="30" style="width: 100%;" />
                                                </div>
                                            </div>
                                            <div class="ncua-input__field-text-count" data-charcount-text="originNm">
                                                <output class="ncua-input__field-text-count-current">0</output><span>/30</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <th><div>브랜드</div></th>
                            <td>
                                <div class="sgr-brand-field">
                                    <div class="sgr-input-field">
                                        <div class="ncua-input ncua-input--xs is-disabled js-sgr-brandCdNm-wrap" style="width: 100%;">
                                            <div class="ncua-input__content-wrap" style="width: 100%;">
                                                <div class="ncua-input__content" style="width: 100%;">
                                                    <div class="ncua-input__field ncua-input__field--xs" style="width: 100%;">
                                                        <input type="text" name="brandCdNm" id="brandCdNm" placeholder="브랜드를 선택해주세요." readonly style="width: 100%;" />
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <button type="button" class="ncua-btn ncua-btn--xs ncua-btn--secondary-gray js-sgr-brand-select-btn">
                                        <span class="ncua-btn__label">선택</span>
                                    </button>
                                    <div id="brandLayer" class="sgr-brand-layer">
                                        <input type="hidden" name="brandCd" value="" />
                                    </div>
                                </div>
                            </td>
                        </tr>
                        </tbody>
                    </table></div>
            </section>
        </section>

        <!-- Block 4: 판매정보 -->
        <section class="ncua-card" id="sgr-block-sale-info">
            <header class="ncua-card__header">
                <h3 class="ncua-card__title">판매정보</h3>
            </header>
            <section class="ncua-card__body">
                <div class="ncua-table ncua-table--vertical"><table>
                        <tbody>
                        <tr>
                            <th><div>판매가</div></th>
                            <td>
                                <div class="sgr-price-field">
                                    <div class="ncua-input ncua-input--xs" style="width: 120px;">
                                        <div class="ncua-input__content-wrap">
                                            <div class="ncua-input__content">
                                                <div class="ncua-input__field ncua-input__field--xs">
                                                    <input type="text" name="goodsPrice"
                                                           inputmode="numeric" placeholder="0"
                                                           value="" maxlength="9" />
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <span class="sgr-unit-text">원</span>
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <th><div>정가</div></th>
                            <td>
                                <div class="sgr-price-field">
                                    <div class="ncua-input ncua-input--xs" style="width: 120px;">
                                        <div class="ncua-input__content-wrap">
                                            <div class="ncua-input__content">
                                                <div class="ncua-input__field ncua-input__field--xs">
                                                    <input type="text" name="fixedPrice"
                                                           inputmode="numeric" placeholder="0"
                                                           value="" maxlength="9" />
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <span class="sgr-unit-text">원</span>
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <th><div>판매 재고</div></th>
                            <td>
                                <div class="sgr-stock-row">
                                    <div class="sgr-stock-radio-group">
                                        <label class="ncua-radio-field ncua-radio-field--xs has-text">
                                            <span class="ncua-radio-input ncua-radio-input--xs ncua-radio-field__input">
                                                <input type="radio" name="stockFl" value="n" checked/>
                                            </span>
                                            <span><span class="ncua-radio-field__text">무한정판매</span></span>
                                        </label>
                                        <label class="ncua-radio-field ncua-radio-field--xs has-text">
                                            <span class="ncua-radio-input ncua-radio-input--xs ncua-radio-field__input">
                                                <input type="radio" name="stockFl" value="y"/>
                                            </span>
                                            <span><span class="ncua-radio-field__text">재고량에 따름</span></span>
                                        </label>
                                    </div>
                                    <div id="sgr-stock-input-area" class="sgr-stock-input-area" style="display: none;">
                                        <span class="sgr-stock-detail__label">재고수</span>
                                        <div class="ncua-input ncua-input--xs" style="width: 120px;">
                                            <div class="ncua-input__content-wrap">
                                                <div class="ncua-input__content">
                                                    <div class="ncua-input__field ncua-input__field--xs">
                                                        <input type="text" name="stockCnt" class="js-number"
                                                               inputmode="numeric" pattern="[0-9]*" placeholder="0"
                                                               value="" />
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <span class="sgr-unit-text">개</span>
                                    </div>
                                </div>
                            </td>
                        </tr>
                        </tbody>
                    </table></div>
            </section>
        </section>

        <!-- Block 5: 옵션/재고 -->
        <section class="ncua-card" id="sgr-block-option-stock">
            <header class="ncua-card__header">
                <h3 class="ncua-card__title" data-tooltip-seq="007">옵션/재고</h3>
            </header>
            <section class="ncua-card__body">
                <div class="ncua-table ncua-table--vertical"><table>
                        <tbody>
                        <!-- Row 1: 옵션 사용여부 Switch + 보조 버튼 -->
                        <tr>
                            <th><div>옵션 사용여부</div></th>
                            <td>
                                <div class="sgr-option-switch-row">
                                    <div class="ncua-switch ncua-switch--xs">
                                        <label class="ncua-switch__option ncua-switch__option--left ncua-switch__option--inactive">
                                            <input class="ncua-switch__radio ncua-switch__radio--left" type="radio" name="optionFl" value="y" />
                                            <span class="ncua-switch__label">사용함</span>
                                        </label>
                                        <label class="ncua-switch__option ncua-switch__option--right ncua-switch__option--active">
                                            <input class="ncua-switch__radio ncua-switch__radio--right" type="radio" name="optionFl" value="n" checked="checked" />
                                            <span class="ncua-switch__label">사용안함</span>
                                        </label>
                                    </div>
                                    <div class="sgr-option-helper-buttons" style="display: none;">
                                        <button type="button" class="ncua-btn ncua-btn--xs ncua-btn--secondary-gray js-sgr-option-load-existing"><span class="ncua-btn__label">기존상품 옵션 불러오기</span></button>
                                        <button type="button" class="ncua-btn ncua-btn--xs ncua-btn--secondary-gray js-sgr-option-load-frequent"><span class="ncua-btn__label">자주쓰는 옵션 불러오기</span></button>
                                    </div>
                                </div>
                            </td>
                        </tr>
                        <!-- Row 2: 옵션 노출 방식 -->
                        <tr class="sgr-option-sub-row" style="display: none;">
                            <th><div>옵션 노출 방식</div></th>
                            <td>
                                <div class="sgr-option-radio-group">
                                    <label class="ncua-radio-field ncua-radio-field--xs has-text">
                                        <span class="ncua-radio-input ncua-radio-input--xs ncua-radio-field__input">
                                            <input type="radio" name="optionY[optionDisplayFl]" value="s" checked />
                                        </span>
                                        <span><span class="ncua-radio-field__text">일체형</span></span>
                                    </label>
                                    <label class="ncua-radio-field ncua-radio-field--xs has-text">
                                        <span class="ncua-radio-input ncua-radio-input--xs ncua-radio-field__input">
                                            <input type="radio" name="optionY[optionDisplayFl]" value="d" />
                                        </span>
                                        <span><span class="ncua-radio-field__text">분리형</span></span>
                                    </label>
                                </div>
                            </td>
                        </tr>
                        <!-- Row 3: 옵션 이미지 노출 설정 -->
                        <tr class="sgr-option-sub-row" style="display: none;">
                            <th>
                                <div data-tooltip-seq="008">
                                    옵션 이미지 노출 설정
                                </div>
                            </th>
                            <td>
                                <div class="sgr-option-checkbox-group">
                                    <label class="ncua-checkbox-field ncua-checkbox-field--xs has-text">
                                        <span class="ncua-checkbox-input ncua-checkbox-input--xs ncua-checkbox-field__input">
                                            <input type="checkbox" name="optionImagePreviewFl" value="y" />
                                        </span>
                                        <span class="ncua-checkbox-field__text">미리보기 사용</span>
                                    </label>
                                    <label class="ncua-checkbox-field ncua-checkbox-field--xs has-text">
                                        <span class="ncua-checkbox-input ncua-checkbox-input--xs ncua-checkbox-field__input">
                                            <input type="checkbox" name="optionImageDisplayFl" value="y" />
                                        </span>
                                        <span class="ncua-checkbox-field__text">상세 이미지에 추가</span>
                                    </label>
                                </div>
                            </td>
                        </tr>
                        <!-- Row 4: 옵션 등록 -->
                        <tr class="sgr-option-sub-row" style="display: none;">
                            <th><div>옵션 등록</div></th>
                            <td>
                                <div class="ncua-accordion ncua-accordion--blue" style="margin-top:8px; margin-bottom:12px;">
                                    <div class="ncua-accordion__summary-style">옵션 등록 안내</div>
                                    <div class="ncua-accordion__content">
                                        <div class="ncua-notice-info">옵션명, 옵션값은 한글, 영문 대/소문자, 숫자, 특수문자 등록 가능합니다. (단, - ` ' ' " " 입력 불가)</div>
                                        <div class="ncua-notice-info ncua-notice-info--red">옵션값에 &amp; 특수문자 사용 시, 옵션 이미지가 출력되지 않습니다.</div>
                                        <div class="ncua-notice-info">"직접 업로드와 URL 직접입력" 방식 모두 사용하여 이미지를 등록한 경우 "직접 업로드"된 이미지만 적용됩니다.</div>
                                        <div class="ncua-notice-info">옵션명/옵션값 입력 후에 [옵션 적용] 버튼 클릭 시 옵션의 가격/재고/상태 설정부분이 출력 됩니다.</div>
                                        <div class="ncua-notice-info">옵션 정보 적용 후 옵션을 수정한 경우 [옵션 적용] 버튼을 다시 클릭해야 수정된 정보가 옵션 정보 항목에 적용됩니다.</div>
                                    </div>
                                </div>
                                <div id="sgr-option-register-area" class="sgr-option-register-area">
                                    <div class="sgr-opt-register">
                                        <div class="sgr-opt-register__header">
                                            <div class="sgr-opt-register__col-name">옵션명</div>
                                            <div class="sgr-opt-register__col-val">
                                                <span>옵션값</span>
                                                <label class="ncua-checkbox-field ncua-checkbox-field--xs has-text" style="margin-left: 8px;">
                                                    <span class="ncua-checkbox-input ncua-checkbox-input--xs ncua-checkbox-field__input"><input type="checkbox" id="optImageUrlCheck" /></span>
                                                    <span class="ncua-checkbox-field__text">옵션 이미지 URL 직접입력</span>
                                                </label>
                                            </div>
                                            <div class="sgr-opt-register__col-del">삭제</div>
                                        </div>
                                        <div class="sgr-opt-register__row js-opt-name-row">
                                            <div class="sgr-opt-register__col-name">
                                                <div class="ncua-input ncua-input--xs ncua-input--full-width-with-count"><div class="ncua-input__content-wrap"><div class="ncua-input__content"><div class="ncua-input__field ncua-input__field--xs"><input type="text" placeholder="예시) 사이즈" maxlength="30" class="sgr-text-count-input js-opt-name-input" data-charcount-key="sgr-opt-0" /></div></div><div class="ncua-input__field-text-count" data-charcount-text="sgr-opt-0"><output class="ncua-input__field-text-count-current">0</output><span>/30</span></div></div></div>
                                            </div>
                                            <div class="sgr-opt-register__col-val">
                                                <div class="sgr-opt-vname-card">
                                                    <div class="sgr-opt-vname">
                                                        <div class="sgr-opt-vname-header">
                                                            <div class="sgr-opt-vname-header__col--fill">옵션값</div>
                                                            <div class="sgr-opt-vname-header__col--price">옵션가</div>
                                                            <div class="sgr-opt-vname-header__col--image">옵션 이미지</div>
                                                            <div class="sgr-opt-vname-header__col--url">옵션 이미지 URL</div>
                                                        </div>
                                                        <div class="sgr-opt-vname-row">
                                                            <div class="sgr-opt-vname-row__val"><div class="ncua-input ncua-input--xs ncua-input--full-width-with-count" style="width:100%;"><div class="ncua-input__content-wrap"><div class="ncua-input__content"><div class="ncua-input__field ncua-input__field--xs"><input type="text" placeholder="Enter키로 연속 입력할 수 있습니다. 예시) XL" maxlength="255" class="sgr-text-count-input js-opt-val-input" data-charcount-key="sgr-opt-1" /></div></div><div class="ncua-input__field-text-count" data-charcount-text="sgr-opt-1"><output class="ncua-input__field-text-count-current">0</output><span>/255</span></div></div></div></div>
                                                            <div class="sgr-opt-vname-row__price"><div style="display:flex;align-items:center;gap:8px;"><div class="ncua-input ncua-input--xs"><div class="ncua-input__content-wrap"><div class="ncua-input__content"><div class="ncua-input__field ncua-input__field--xs"><input type="text" inputmode="numeric" placeholder="0" class="js-opt-price-input" /></div></div></div></div><span class="sgr-unit-text">원</span></div></div>
                                                            <div class="sgr-opt-vname-row__image"><button type="button" class="ncua-btn ncua-btn--xs ncua-btn--secondary-gray js-opt-image-btn"><svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" fill="none" viewBox="0 0 24 24" stroke="none"><path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 15v1.2c0 1.68 0 2.52-.327 3.162a3 3 0 0 1-1.311 1.311C18.72 21 17.88 21 16.2 21H7.8c-1.68 0-2.52 0-3.162-.327a3 3 0 0 1-1.311-1.311C3 18.72 3 17.88 3 16.2V15m14-7-5-5m0 0L7 8m5-5v12"/></svg><span class="ncua-btn__label">파일 찾기</span></button><input type="file" class="no-filestyle js-opt-image-file" accept="image/png,image/jpeg,image/gif,image/bmp,image/svg+xml,image/tiff,image/x-icon,application/postscript" style="display:none" /></div>
                                                            <div class="sgr-opt-vname-row__url"><div class="ncua-input ncua-input--xs"><div class="ncua-input__content-wrap"><div class="ncua-input__content"><div class="ncua-input__field ncua-input__field--xs"><input type="text" placeholder="이미지 URL을 입력해주세요." class="js-opt-url-input" /></div></div></div></div></div>
                                                            <div class="sgr-opt-vname-row__del"><button type="button" class="ncua-btn ncua-btn--xxs only-icon ncua-btn--tertiary-gray js-opt-val-del" disabled><svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" fill="none" viewBox="0 0 24 24" stroke="none"><path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 3h6M3 6h18m-2 0-.701 10.52c-.105 1.578-.158 2.367-.499 2.965a3 3 0 0 1-1.298 1.215c-.62.3-1.41.3-2.993.3h-3.018c-1.582 0-2.373 0-2.993-.3A3 3 0 0 1 6.2 19.485c-.34-.598-.394-1.387-.499-2.966L5 6m5 4.5v5m4-5v5"/></svg></button></div>
                                                        </div>
                                                    </div>
                                                    <button type="button" class="ncua-btn ncua-btn--xs ncua-btn--secondary-gray js-opt-val-add"><svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" fill="none" viewBox="0 0 24 24"><path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 5v14m-7-7h14"/></svg><span class="ncua-btn__label">옵션값 추가</span></button>
                                                </div>
                                            </div>
                                            <div class="sgr-opt-register__col-del">
                                                <button type="button" class="ncua-btn ncua-btn--xxs ncua-btn--secondary-gray js-opt-name-del" disabled><div class="ncua-minus-icon"></div><span class="ncua-btn__label">삭제</span></button>
                                            </div>
                                        </div>
                                        <div class="sgr-opt-register__footer">
                                            <button type="button" class="ncua-btn ncua-btn--xs ncua-btn--secondary-gray" id="optNameAddBtn"><span class="ncua-btn__label">추가</span></button>
                                            <button type="button" class="ncua-btn ncua-btn--xs ncua-btn--secondary" id="optionApplyBtn"><span class="ncua-btn__label">옵션 적용</span></button>
                                        </div>
                                    </div>
                                    <div style="display:flex;justify-content:flex-start;margin-top:8px;">
                                        <button type="button" class="ncua-btn ncua-btn--xs ncua-btn--secondary-gray" data-modal-open="optSaveFreqModal"><span class="ncua-btn__label">자주쓰는 옵션으로 등록하기</span></button>
                                    </div>
                                </div>
                            </td>
                        </tr>
                        </tbody>
                    </table>
                    <div id="sgr-option-info-row" style="display:none;">
                        <div class="sgr-option-info-section">
                            <div class="sgr-option-info-section__label">옵션 정보</div>
                            <div class="sgr-option-info-section__content">
                                <div class="ncua-accordion ncua-accordion--blue" style="margin-bottom:12px;">
                                    <div class="ncua-accordion__summary-style">옵션 정보 안내</div>
                                    <div class="ncua-accordion__content">
                                        <div class="ncua-notice-info">옵션 매입가는 상품 매입가 기준, 옵션가는 상품의 판매가 기준 추가 또는 차감될 옵션별 금액이 있는 경우에만 입력합니다.</div>
                                        <div class="ncua-notice-info ncua-notice-info--red">상품 매입가 및 판매가에 추가될 금액은 양수, 차감될 금액은 음수(마이너스)로 입력 합니다.</div>
                                        <div class="ncua-notice-info">순서조정 버튼을 이용하여 옵션의 순서를 변경할 수 있으며, 설정된 순서대로 쇼핑몰에 노출됩니다.</div>
                                        <div class="ncua-notice-info">옵션추가 버튼을 이용하여 옵션 정보를 초기화하지 않고 추가 생성할 수 있습니다.</div>
                                        <div class="ncua-notice-info">옵션 정보에 출력되는 항목을 [조회항목설정] 버튼을 이용하여 설정할 수 있습니다.</div>
                                        <div class="ncua-notice-info">옵션품절상태의 "정상/품절" 제외 상태와 옵션배송상태는 쇼핑몰에만 적용되며, 네이버 ep/네이버 쇼핑/다음 쇼핑하우 ep 등의 외부연동 시 적용되지 않습니다.</div>
                                    </div>
                                </div>
                                <div class="ncua-data-grid sgr-option-grid">
                                    <div class="ncua-data-grid__action-bar ncua-data-grid__action-bar--top ncua-data-grid__action-bar--space-between sgr-option-action-bar--top">
                                        <div class="sgr-option-action-bar__left">
                                            <div class="ncua-button-group ncua-button-group--xs has-border sgr-option-sort-btns" id="sgr-sort-btn-group">
                                                <button type="button" class="ncua-button-group__item js-sgr-sort-btn" data-direction="top" title="맨 위로" disabled><svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 16 16" fill="none"><path fill-rule="evenodd" clip-rule="evenodd" d="M7.5286 8.19518C7.78894 7.93483 8.21106 7.93483 8.4714 8.19518L11.8047 11.5285C12.0651 11.7889 12.0651 12.211 11.8047 12.4713C11.5444 12.7317 11.1223 12.7317 10.8619 12.4713L8 9.60939L5.13807 12.4713C4.87772 12.7317 4.45561 12.7317 4.19526 12.4713C3.93491 12.211 3.93491 11.7889 4.19526 11.5285L7.5286 8.19518Z" fill="currentColor"/><path fill-rule="evenodd" clip-rule="evenodd" d="M7.5286 3.52851C7.78894 3.26816 8.21106 3.26816 8.4714 3.52851L11.8047 6.86185C12.0651 7.1222 12.0651 7.54431 11.8047 7.80466C11.5444 8.06501 11.1223 8.06501 10.8619 7.80466L8 4.94273L5.13807 7.80466C4.87772 8.06501 4.45561 8.06501 4.19526 7.80466C3.93491 7.54431 3.93491 7.1222 4.19526 6.86185L7.5286 3.52851Z" fill="currentColor"/></svg></button>
                                                <button type="button" class="ncua-button-group__item js-sgr-sort-btn" data-direction="up" title="위로" disabled><svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 16 16" fill="none"><path d="M12 10L8 6L4 10" stroke="currentColor" stroke-width="1.33" stroke-linecap="round" stroke-linejoin="round"/></svg></button>
                                                <button type="button" class="ncua-button-group__item js-sgr-sort-btn" data-direction="down" title="아래로" disabled><svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 16 16" fill="none"><path d="M4 6L8 10L12 6" stroke="currentColor" stroke-width="1.33" stroke-linecap="round" stroke-linejoin="round"/></svg></button>
                                                <button type="button" class="ncua-button-group__item js-sgr-sort-btn" data-direction="bottom" title="맨 아래로" disabled><svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 16 16" fill="none"><path d="M4.66699 8.66667L8.00033 12L11.3337 8.66667M4.66699 4L8.00033 7.33333L11.3337 4" stroke="currentColor" stroke-width="1.33" stroke-linecap="round" stroke-linejoin="round"/></svg></button>
                                            </div>
                                        </div>
                                        <div class="sgr-option-action-bar__right">
                                            <button type="button" class="ncua-btn ncua-btn--xs ncua-btn--secondary-gray js-sgr-grid-config-btn"><span class="ncua-btn__label">조회항목설정</span></button>
                                        </div>
                                    </div>
                                    <div class="ncua-data-grid__table">
                                        <div class="ncua-table ncua-table--in-data-grid ncua-table--horizontal ncua-table--draggable sgr-option-table-container">
                                            <table class="ncua-table__table sgr-option-table">
                                                <thead class="ncua-table__header">
                                                <tr class="ncua-table__row">
                                                    <th class="ncua-table__header-cell ncua-table__checkbox-cell ncua-table__checkbox-cell--header sgr-opt-col-dragcheck"><div><label class="ncua-checkbox-field ncua-checkbox-field--xs" style="margin:0;"><span class="ncua-checkbox-input ncua-checkbox-input--xs ncua-checkbox-field__input"><input type="checkbox" id="sgr-option-select-all" /></span></label></div></th>
                                                    <th class="ncua-table__header-cell sgr-opt-col-name js-sgr-opt-col-name1"><div>옵션명1</div></th>
                                                    <th class="ncua-table__header-cell sgr-opt-col-name js-sgr-opt-col-name2" style="display:none;"><div>옵션명2</div></th>
                                                    <th class="ncua-table__header-cell sgr-opt-col-name js-sgr-opt-col-name3" style="display:none;"><div>옵션명3</div></th>
                                                    <th class="ncua-table__header-cell sgr-opt-col-name js-sgr-opt-col-name4" style="display:none;"><div>옵션명4</div></th>
                                                    <th class="ncua-table__header-cell sgr-opt-col-name js-sgr-opt-col-name5" style="display:none;"><div>옵션명5</div></th>
                                                    <th class="ncua-table__header-cell" data-grid-key="optionCostPrice"><div>옵션매입가</div></th>
                                                    <th class="ncua-table__header-cell" data-grid-key="optionPrice"><div>옵션가</div></th>
                                                    <th class="ncua-table__header-cell" data-grid-key="stockCnt"><div>재고량</div></th>
                                                    <th class="ncua-table__header-cell" data-grid-key="optionViewFl"><div>옵션노출상태</div></th>
                                                    <th class="ncua-table__header-cell" data-grid-key="optionSellFl"><div>옵션품절상태</div></th>
                                                    <th class="ncua-table__header-cell" data-grid-key="optionDeliveryFl"><div>옵션배송상태</div></th>
                                                    <th class="ncua-table__header-cell" data-grid-key="optionCode"><div>자체 옵션코드</div></th>
                                                    <th class="ncua-table__header-cell" data-grid-key="optionMemo"><div>메모</div></th>
                                                </tr>
                                                <tr class="ncua-table__row sgr-option-table__bulk-row">
                                                    <td colspan="2" class="ncua-table__cell js-sgr-bulk-first-cell"><div class="sgr-option-table__bulk-left"><span class="sgr-option-table__bulk-label">옵션 정보 일괄 적용하기</span><button type="button" class="ncua-btn ncua-btn--xs ncua-btn--secondary-gray js-sgr-bulk-apply-btn"><span class="ncua-btn__label">적용</span></button></div></td>
                                                    <td class="ncua-table__cell js-sgr-bulk-col-name2" style="display:none;"></td>
                                                    <td class="ncua-table__cell js-sgr-bulk-col-name3" style="display:none;"></td>
                                                    <td class="ncua-table__cell js-sgr-bulk-col-name4" style="display:none;"></td>
                                                    <td class="ncua-table__cell js-sgr-bulk-col-name5" style="display:none;"></td>
                                                    <td class="ncua-table__cell" data-grid-key="optionCostPrice"><div style="display:flex;align-items:center;gap:8px;"><div class="ncua-input ncua-input--xs" style="width:68px;"><div class="ncua-input__content-wrap"><div class="ncua-input__content"><div class="ncua-input__field ncua-input__field--xs"><input type="text" inputmode="numeric" data-field="optionBuyPrice" class="js-sgr-bulk-field" placeholder="0" /></div></div></div></div><span class="sgr-unit-text">원</span></div></td>
                                                    <td class="ncua-table__cell" data-grid-key="optionPrice"><div style="display:flex;align-items:center;gap:8px;"><div class="ncua-input ncua-input--xs" style="width:68px;"><div class="ncua-input__content-wrap"><div class="ncua-input__content"><div class="ncua-input__field ncua-input__field--xs"><input type="text" inputmode="numeric" data-field="optionPrice" class="js-sgr-bulk-field" placeholder="0" /></div></div></div></div><span class="sgr-unit-text">원</span></div></td>
                                                    <td class="ncua-table__cell" data-grid-key="stockCnt"><div style="display:flex;align-items:center;gap:8px;"><div class="ncua-input ncua-input--xs" style="width:60px;"><div class="ncua-input__content-wrap"><div class="ncua-input__content"><div class="ncua-input__field ncua-input__field--xs"><input type="text" inputmode="numeric" data-field="stockCnt" class="js-number js-sgr-bulk-field" placeholder="0" /></div></div></div></div><span class="sgr-unit-text">개</span></div></td>
                                                    <td class="ncua-table__cell" data-grid-key="optionViewFl"><div><select class="js-sgr-bulk-field" data-field="optionDisplayFl"><option value="">선택</option><option value="y">노출함</option><option value="n">노출안함</option></select></div></td>
                                                    <td class="ncua-table__cell" data-grid-key="optionSellFl"><div><select class="js-sgr-bulk-field" data-field="optionSoldoutFl"><option value="">선택</option><?php foreach($stockReason as $k => $v) { ?><option value="<?=$k?>"><?=$v?></option><?php } ?></select></div></td>
                                                    <td class="ncua-table__cell" data-grid-key="optionDeliveryFl"><div><select class="js-sgr-bulk-field" data-field="optionDeliveryFl"><option value="">선택</option><?php foreach($deliveryReason as $k => $v) { ?><option value="<?=$k?>"><?=$v?></option><?php } ?></select></div></td>
                                                    <td class="ncua-table__cell" data-grid-key="optionCode"><div style="display:flex;align-items:center;gap:8px;"><div class="ncua-input ncua-input--xs" style="width:85px;"><div class="ncua-input__content-wrap"><div class="ncua-input__content"><div class="ncua-input__field ncua-input__field--xs"><input type="text" data-field="optionCode" class="js-sgr-bulk-field" /></div></div></div></div></div></td>
                                                    <td class="ncua-table__cell" data-grid-key="optionMemo"><div style="display:flex;align-items:center;gap:8px;"><div class="ncua-input ncua-input--xs" style="width:85px;"><div class="ncua-input__content-wrap"><div class="ncua-input__content"><div class="ncua-input__field ncua-input__field--xs"><input type="text" data-field="optionMemo" class="js-sgr-bulk-field" /></div></div></div></div></div></td>
                                                </tr>
                                                </thead>
                                                <tbody class="ncua-table__body" id="sgr-option-table-body">
                                                <tr class="ncua-table__row sgr-option-table__empty" id="sgr-option-table-empty"><td class="ncua-table__cell" colspan="14"><div>옵션 등록 시 자동 생성됩니다.</div></td></tr>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                    <div class="ncua-data-grid__action-bar ncua-data-grid__action-bar--bottom sgr-option-action-bar--bottom">
                                        <button type="button" class="ncua-btn ncua-btn--xs ncua-btn--secondary-gray js-sgr-add-option-btn"><span class="ncua-btn__label">옵션 추가</span></button>
                                        <button type="button" class="ncua-btn ncua-btn--xs ncua-btn--destructive js-sgr-delete-selected-btn" disabled><span class="ncua-btn__label">선택 삭제</span></button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </section>
        </section>

        <!-- Block 6: 상품이미지 -->
        <section class="ncua-card" id="sgr-block-image">
            <header class="ncua-card__header">
                <h3 class="ncua-card__title">상품이미지</h3>
            </header>
            <section class="ncua-card__body">
                <div class="ncua-table ncua-table--vertical"><table>
                        <tbody>
                        <tr>
                            <th>
                                <div data-tooltip-seq="009">
                                    원본이미지
                                </div>
                            </th>
                            <td>
                                <div class="sgr-image-upload-area">
                                    <div id="sgr-image-file-input"></div>
                                    <input type="file" name="image[imageOriginal][]" id="sgrOriginalImageInput" class="no-filestyle" tabindex="-1" aria-hidden="true" />
                                </div>
                                <div class="sgr-auto-resize-wrap">
                                    <span class="ncua-checkbox-field__support-text">원본이미지 등록 시 상품이미지 사이즈 설정값으로 개별이미지(상세/썸네일/리스트/확대)가 자동 생성됩니다</span>
                                    <span class="ncua-checkbox-field__support-text">최초 상품 이미지 등록 시 <?php if (!$isProvider): ?><a href="../policy/goods_images.php" target="_blank" class="sgr-inline-link">상품 이미지 사이즈 설정</a><?php else: ?>상품 이미지 사이즈 설정<?php endif; ?>에서 이미지 사이즈를 먼저 설정해야 합니다</span>
                                </div>
                            </td>
                        </tr>
                        </tbody>
                    </table></div>
            </section>
        </section>

        <!-- Block 7: 상세정보 -->
        <section class="ncua-card" id="sgr-block-detail">
            <header class="ncua-card__header">
                <h3 class="ncua-card__title">상세정보</h3>
            </header>
            <section class="ncua-card__body">
                <div class="ncua-table ncua-table--vertical"><table>
                        <tbody>
                        <tr>
                            <th>
                                <div data-tooltip-seq="010">
                                    짧은설명
                                </div>
                            </th>
                            <td>
                                <div class="ncua-input ncua-input--xs" style="width: 100%;">
                                    <div class="ncua-input__content-wrap" style="width: 100%;">
                                        <div class="ncua-input__content" style="width: 100%;">
                                            <div class="ncua-input__field ncua-input__field--xs" style="width: 100%;">
                                                <input type="text" name="shortDescription" placeholder="짧은설명을 입력하세요." maxlength="250" style="width: 100%;" />
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <th>
                                <div>상세설명</div>
                            </th>
                            <td>
                                <div class="sgr-detail-desc">
                                    <?php if ($mobileShopFl === 'y'): ?>
                                    <div class="sgr-detail-desc__header">
                                        <div class="ncua-horizontal-tab ncua-horizontal-tab--button-white ncua-horizontal-tab--sm">
                                            <div class="swiper">
                                                <div class="swiper-wrapper">
                                                    <div class="swiper-slide ncua-horizontal-tab__item">
                                                        <button type="button" class="ncua-tab-button is-active js-sgr-desc-tab" data-target="pc">PC 쇼핑몰 상세설명</button>
                                                    </div>
                                                    <div class="swiper-slide ncua-horizontal-tab__item">
                                                        <button type="button" class="ncua-tab-button js-sgr-desc-tab" data-target="mobile">모바일 쇼핑몰 상세설명</button>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <label class="ncua-checkbox-field ncua-checkbox-field--xs has-text" style="margin-left: 12px; white-space: nowrap;">
                                            <span class="ncua-checkbox-input ncua-checkbox-input--xs ncua-checkbox-field__input">
                                                <input type="checkbox" name="goodsDescriptionSameFl" value="y" class="js-sgr-desc-same-checkbox" checked />
                                            </span>
                                            <span class="ncua-checkbox-field__text">PC/모바일 상세설명 동일사용</span>
                                        </label>
                                    </div>
                                    <?php endif; ?>
                                    <div class="sgr-editor-wrap js-sgr-editor-pc">
                                        <textarea name="goodsDescription" id="editor" data-godo-editor="basic-editor" data-height-min="300" imageUploadCallback="handleGodoImageUpload(images, editor)"></textarea>
                                    </div>
                                    <?php if ($mobileShopFl === 'y'): ?>
                                    <div class="sgr-editor-wrap js-sgr-editor-mobile" style="display: none;">
                                        <div class="sgr-editor-overlay js-sgr-editor-overlay" style="display: none;"></div>
                                        <div class="sgr-editor-same-notice js-sgr-editor-same-notice" style="display: none;">저장 시 PC 상세설명이 모바일에 동일 적용됩니다</div>
                                        <textarea name="goodsDescriptionMobile" id="editor2" data-height-min="300" imageUploadCallback="handleGodoImageUpload(images, editor2)"></textarea>
                                    </div>
                                    <?php endif; ?>
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <th>
                                <div data-tooltip-seq="011">
                                    검색 키워드
                                </div>
                            </th>
                            <td>
                                <div class="sgr-search-keyword-row">
                                    <div class="sgr-search-keyword-input">
                                        <div class="ncua-input ncua-input--xs" style="width: 100%;">
                                            <div class="ncua-input__content-wrap" style="width: 100%;">
                                                <div class="ncua-input__content" style="width: 100%;">
                                                    <div class="ncua-input__field ncua-input__field--xs" style="width: 100%;">
                                                        <input type="text" name="goodsSearchWord" placeholder="콤마(,)로 구분하여 입력" maxlength="250" style="width: 100%;" />
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <label class="ncua-checkbox-field ncua-checkbox-field--xs has-text" style="white-space: nowrap;">
                                        <span class="ncua-checkbox-input ncua-checkbox-input--xs ncua-checkbox-field__input">
                                            <input type="checkbox" name="goodsSearchWordAddGoodsNmFl" value="y" />
                                        </span>
                                        <span class="ncua-checkbox-field__text">상품명을 검색 키워드에 추가</span>
                                    </label>
                                </div>
                            </td>
                        </tr>
                        </tbody>
                    </table></div>
            </section>
        </section>
    </form>
</article>

<script>
    $(document).ready(function () {

        // 판매가/정가: 일반상품등록과 동일하게 마이너스 입력 허용 (number_only)
        $('input[name="goodsPrice"], input[name="fixedPrice"]').number_only();

        <?php if (!gd_is_provider()): ?>
        var sgrScmManager = null;
        (function initSgrScmComboBox() {
            if (typeof initSupplyComboBox === 'undefined') {
                setTimeout(initSgrScmComboBox, 100);
                return;
            }
            sgrScmManager = initSupplyComboBox({
                scmLayerSelector: 'scmTagArea',
                dataInputNm: 'scmNo',
                comboBoxLayerId: 'scmComboBox',
                radioGroupClass: 'sgr-scm-radio-group',
                apiUrl: '/share/ncds/layer_scm.php',
                multiple: false,
                tagIdPrefix: 'scm',
                tagHiddenName: 'scmCompanyNm',
                tagCloseButton: true,
                autoHideTagArea: true,
                supplyButtonNamesTypes: ['y']
            });

            sgrScmManager.scmDatas.subscribe(function (data) {
                if (data.length > 0) {
                    sgrLoadDefaultDelivery(data[0].id);
                }
            });
        })();

        $('input[name="scmFl"]').on('change', function () {
            var $combo = $('#scmSelectBox');
            var isSupplier = this.value === 'y';
            if (isSupplier) {
                $combo.show();
            } else {
                $combo.hide();
                if (sgrScmManager) {
                    sgrScmManager.scmDatas([]);
                    sgrScmManager.comboBox.setValues([]);
                }
                $('#scmNoInput').val('<?= DEFAULT_CODE_SCMNO ?>');
                sgrLoadDefaultDelivery('<?= DEFAULT_CODE_SCMNO ?>');
            }
        });
        <?php endif; ?>

        var sgrDeliveryConfigUrl = '<?= gd_is_provider() === true ? "/provider" : "" ?>/policy/delivery_config.php';

        // 기본 배송비조건이 없을 때 경고 (확인 클릭 시 배송비조건 관리 새 탭 이동)
        function sgrShowNoDeliveryWarning() {
            NCDSAlert({
                message: '기본 배송비조건이 없어 상품을 등록할 수 없습니다.',
                subMessage: '배송비조건 관리에서 먼저 설정해 주세요.',
                iconType: 'warning',
                callback: function () {
                    window.open(sgrDeliveryConfigUrl, '_blank');
                }
            });
        }

        function sgrLoadDefaultDelivery(scmNo) {
            if (!scmNo) {
                $('input[name="deliverySno"]').val('');
                return;
            }
            $.post('../policy/delivery_ps.php', {mode: 'search_scm', scmNo: scmNo}, function (data) {
                try {
                    var deliveryData = $.parseJSON(data);
                    if (deliveryData && deliveryData.sno) {
                        $('input[name="deliverySno"]').val(deliveryData.sno);
                    } else {
                        $('input[name="deliverySno"]').val('');
                    }
                } catch (e) {
                    console.error('배송비 정보 파싱 오류:', e);
                    $('input[name="deliverySno"]').val('');
                }
            }).fail(function() {
                console.error('배송비 정보를 가져오는데 실패했습니다.');
                $('input[name="deliverySno"]').val('');
            });
        }

        <?php if (gd_is_provider()): ?>
        sgrLoadDefaultDelivery('<?= gd_htmlspecialchars($scmNo) ?>');
        <?php else: ?>
        sgrLoadDefaultDelivery('<?= DEFAULT_CODE_SCMNO ?>');
        <?php endif; ?>

        var sgrCharCountManager = null;
        (function initSgrCharCount() {
            if (typeof createCharCountManager === 'undefined') {
                setTimeout(initSgrCharCount, 100);
                return;
            }
            sgrCharCountManager = createCharCountManager({
                targetClasses: ['js-sgr-charcount', 'sgr-text-count-input'],
            });
            sgrCharCountManager.init();
        })();

        // 카테고리 Column Browser
        (function () {
            var CHEVRON_SVG = '<span class="sgr-cate-node__arrow"><svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="none" viewBox="0 0 24 24"><path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m9 18 6-6-6-6"/></svg></span>';
            var SEP_SVG = '<svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" fill="none" viewBox="0 0 24 24"><path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m9 18 6-6-6-6"/></svg>';
            var API_URL = '/share/category_select_json.php';
            var MAX_DEPTH = <?= DEFAULT_DEPTH_CATE ?>;
            var MAX_CATE_LEN = <?= DEFAULT_DEPTH_CATE * DEFAULT_LENGTH_CATE ?>;
            var SPINNER_DELAY_MS = 300;
            var FADE_DURATION_MS = 200;
            var ROOT_CATE_DATA = <?= $rootCateList ?>;
            var SGR_MALL_DATA = <?= $mallData ?>;

            var columns = [];
            for (var ci = 0; ci < MAX_DEPTH; ci++) {
                columns.push(document.getElementById('cateCol' + ci));
            }
            var $breadcrumb = $('#cateBreadcrumb');
            var selectedCateBody = document.getElementById('selectedCateBody');
            var $selectedCateRow = $('#selectedCateRow');
            var $cateInputRow = $('#cateInputRow');

            var selectedNodes = [];
            var selectedCategories = [];
            var isCurrentLeaf = false;
            var leafCache = {};

            function escapeHtml(str) {
                var div = document.createElement('div');
                div.appendChild(document.createTextNode(str));
                return div.innerHTML;
            }

            function isMaxDepth(cateCd) {
                return cateCd && cateCd.length >= MAX_CATE_LEN;
            }

            function hasPotentialChildren(cateCd) {
                return !isMaxDepth(cateCd) && !leafCache[cateCd];
            }

            function fetchCategories(parentCateCd, callback) {
                $.post(API_URL, { mode: 'next_select', value: parentCateCd || '' }, function (data) {
                    try {
                        var parsed = data ? (typeof data === 'string' ? JSON.parse(data) : data) : [];
                        callback(null, parsed);
                    } catch (e) {
                        console.error('[category] response parse failed', e);
                        callback(e, null);
                    }
                }).fail(function (xhr, textStatus, errorThrown) {
                    console.error('[category] api request failed', textStatus, errorThrown);
                    callback(errorThrown || textStatus || 'network_error', null);
                });
            }

            function renderColumn(depth, items) {
                var col = columns[depth];
                if (!col) return;
                col.innerHTML = '';
                if (!items || items.length === 0) return;

                items.forEach(function (item) {
                    if (item.hasChildren === false) {
                        leafCache[String(item.optionValue)] = true;
                    }
                });

                items.forEach(function (item) {
                    var cateCd = String(item.optionValue);
                    var name = item.optionText;
                    var flag = item.flag || '';
                    var mallName = item.mallName || '';
                    var hasChildren = hasPotentialChildren(cateCd);
                    var isSelected = selectedNodes[depth] && selectedNodes[depth].cateCd === cateCd;

                    var btn = document.createElement('button');
                    btn.type = 'button';
                    btn.className = 'sgr-cate-node' + (isSelected ? ' is-selected' : '');
                    btn.innerHTML = '<span class="sgr-cate-node__text">' + escapeHtml(name) + '</span>' + (hasChildren ? CHEVRON_SVG : '');
                    btn.setAttribute('data-cate-cd', cateCd);
                    btn.setAttribute('data-flag', flag);
                    btn.setAttribute('data-mall-name', mallName);
                    btn.addEventListener('click', function () {
                        selectNode(depth, cateCd, name, flag, mallName, hasPotentialChildren(cateCd));
                    });
                    col.appendChild(btn);
                });
            }

            function markParentAsLeaf(parentDepth, parentCateCd) {
                if (!parentCateCd || parentDepth < 0) return;
                leafCache[parentCateCd] = true;
                var $parentBtn = $(columns[parentDepth]).find('[data-cate-cd="' + parentCateCd + '"]');
                $parentBtn.find('.sgr-cate-node__arrow').remove();
            }

            function selectNode(depth, cateCd, name, flag, mallName, hasChildren) {
                selectedNodes[depth] = { cateCd: cateCd, name: name, flag: flag, mallName: mallName };
                for (var j = depth + 1; j < MAX_DEPTH; j++) {
                    selectedNodes[j] = null;
                    columns[j].innerHTML = '';
                }

                $(columns[depth]).find('.sgr-cate-node').removeClass('is-selected');
                $(columns[depth]).find('[data-cate-cd="' + cateCd + '"]').addClass('is-selected');
                updateBreadcrumb();

                if (hasChildren) {
                    isCurrentLeaf = false;
                    loadColumn(depth + 1, cateCd);
                } else {
                    isCurrentLeaf = true;
                }
            }

            function loadColumn(depth, parentCateCd) {
                if (depth >= MAX_DEPTH) {
                    isCurrentLeaf = true;
                    return;
                }

                var col = columns[depth];
                var spinnerTimer = setTimeout(function () {
                    col.innerHTML = '<div class="sgr-cate-spinner"><span class="ncua-spinner ncua-spinner--xs" aria-hidden="true"><span class="ncua-spinner__content"></span></span></div>';
                }, SPINNER_DELAY_MS);

                fetchCategories(parentCateCd, function (err, items) {
                    clearTimeout(spinnerTimer);

                    if (err) {
                        col.innerHTML = '<div class="sgr-cate-error">' +
                            '<span>카테고리를 불러올 수 없습니다</span>' +
                            '<button type="button" class="ncua-btn ncua-btn--xxs ncua-btn--tertiary-gray js-sgr-cate-retry" data-depth="' + depth + '" data-parent="' + escapeHtml(parentCateCd) + '"><span class="ncua-btn__label">재시도</span></button>' +
                            '</div>';
                        return;
                    }

                    if (items.length === 0) {
                        isCurrentLeaf = true;
                        col.innerHTML = '';
                        markParentAsLeaf(depth - 1, parentCateCd);
                        return;
                    }

                    renderColumn(depth, items);
                });
            }

            function updateBreadcrumb() {
                var html = '';
                for (var bi = 0; bi < MAX_DEPTH; bi++) {
                    if (!selectedNodes[bi]) break;
                    if (bi > 0) html += '<span class="sgr-cate-breadcrumb__sep">' + SEP_SVG + '</span>';
                    html += '<span>' + escapeHtml(selectedNodes[bi].name) + '</span>';
                }
                $breadcrumb.html(html || '<span style="color:#A4A5A8">카테고리를 선택하세요</span>');
            }

            function getLastSelectedNode() {
                for (var li = MAX_DEPTH - 1; li >= 0; li--) {
                    if (selectedNodes[li]) return selectedNodes[li];
                }
                return null;
            }

            $('.js-sgr-cate-select-btn').on('click', function () {
                var lastNode = getLastSelectedNode();
                if (!lastNode) return;
                var cateCd = lastNode.cateCd;

                for (var di = 0; di < selectedCategories.length; di++) {
                    if (selectedCategories[di].cateCd === cateCd) {
                        NCDSAlert({ message: '이미 선택된 카테고리입니다.', iconType: 'warning' });
                        return;
                    }
                }

                var wasEmpty = selectedCategories.length === 0;

                for (var pi = 0; pi < selectedNodes.length; pi++) {
                    if (!selectedNodes[pi]) continue;
                    var nodeCd = selectedNodes[pi].cateCd;
                    var alreadyExists = false;
                    for (var ei = 0; ei < selectedCategories.length; ei++) {
                        if (selectedCategories[ei].cateCd === nodeCd) { alreadyExists = true; break; }
                    }
                    if (alreadyExists) continue;

                    var pathParts = [];
                    for (var pp = 0; pp <= pi; pp++) {
                        if (selectedNodes[pp]) pathParts.push(selectedNodes[pp].name);
                    }
                    selectedCategories.push({
                        cateCd: nodeCd,
                        pathText: pathParts.join(' > '),
                        isRep: wasEmpty && selectedCategories.length === 0,
                        flag: selectedNodes[pi].flag || '',
                        mallName: selectedNodes[pi].mallName || '',
                        parentCateCds: []
                    });
                }

                renderSelectedTable();

                if (wasEmpty) {
                    $cateInputRow.removeClass('is-visual-last');
                    $selectedCateRow.hide().fadeIn(FADE_DURATION_MS);
                    $('#cateTopFixRow').hide().fadeIn(FADE_DURATION_MS);
                }
            });

            function parseCateMalls(cat) {
                var flags = cat.flag ? cat.flag.split(',') : [];
                var names = cat.mallName ? cat.mallName.split(',') : [];
                var malls = [];
                for (var mi = 0; mi < flags.length; mi++) {
                    if (flags[mi]) malls.push({ domainFl: flags[mi], mallName: names[mi] || '' });
                }
                return malls.length > 0 ? malls : SGR_MALL_DATA;
            }

            function renderSelectedTable() {
                var $body = $(selectedCateBody);
                $body.empty();

                if (selectedCategories.length === 0) return;

                selectedCategories.forEach(function (cat) {
                    var safeCateCd = escapeHtml(cat.cateCd);
                    var malls = parseCateMalls(cat);

                    var flagsHtml = '';
                    malls.forEach(function (mall) {
                        flagsHtml += '<span class="js-popover flag flag-16 flag-' + escapeHtml(mall.domainFl) + '" data-content="' + escapeHtml(mall.mallName) + '"></span>';
                    });
                    var hiddenHtml =
                        '<input type="hidden" name="link[cateCd][]" value="' + safeCateCd + '" />' +
                        '<input type="hidden" name="link[cateLinkFl][]" value="y" />';

                    var tr = document.createElement('tr');
                    tr.id = 'info_category_' + cat.cateCd;
                    tr.setAttribute('data-cate-cd', cat.cateCd);
                    tr.innerHTML =
                        '<td><div><label class="ncua-radio-field ncua-radio-field--xs"><span class="ncua-radio-input ncua-radio-input--xs ncua-radio-field__input"><input type="radio" name="cateCd" value="' + safeCateCd + '"' + (cat.isRep ? ' checked' : '') + '/></span><span></span></label></div></td>' +
                        '<td><div>' + flagsHtml + '</div></td>' +
                        '<td><div class="sgr-cate-path"><span>' + escapeHtml(cat.pathText) + '</span></div></td>' +
                        '<td><div>' + safeCateCd + '</div></td>' +
                        '<td><div><button type="button" class="ncua-btn ncua-btn--xxs ncua-btn--secondary-gray js-sgr-cate-delete"><div class="ncua-minus-icon"></div><span class="ncua-btn__label">삭제</span></button></div>' +
                        hiddenHtml + '</td>';

                    $body.append(tr);
                });

                $body.find('.js-popover').popover({trigger: 'hover', container: '#content'});
            }

            $(selectedCateBody).on('click', '.js-sgr-cate-delete', function () {
                var cateCd = $(this).closest('tr').attr('data-cate-cd');
                var idx = -1;
                for (var si = 0; si < selectedCategories.length; si++) {
                    if (selectedCategories[si].cateCd === cateCd) { idx = si; break; }
                }
                if (idx === -1) return;

                var wasRep = selectedCategories[idx].isRep;
                selectedCategories.splice(idx, 1);

                if (wasRep && selectedCategories.length > 0) {
                    selectedCategories[0].isRep = true;
                }

                var $rows = $(selectedCateBody).find('tr[data-cate-cd="' + cateCd + '"]');
                var rowCount = $rows.length;
                var removed = 0;

                $rows.fadeOut(FADE_DURATION_MS, function () {
                    $(this).remove();
                    removed++;
                    if (removed < rowCount) return;

                    if (wasRep && selectedCategories.length > 0) {
                        $(selectedCateBody).find('input[name="cateCd"]').first().prop('checked', true);
                    }

                    if (selectedCategories.length === 0) {
                        $selectedCateRow.fadeOut(FADE_DURATION_MS, function () {
                            $cateInputRow.addClass('is-visual-last');
                        });
                        $('#cateTopFixRow').fadeOut(FADE_DURATION_MS);
                        var $nRadio = $('input[name="cateTopFix"][value="n"]');
                        if (!$nRadio.is(':checked')) {
                            $nRadio.prop('checked', true).trigger('change');
                        }
                    }
                });
            });

            $(selectedCateBody).on('change', 'input[name="cateCd"]', function () {
                var val = $(this).val();
                selectedCategories.forEach(function (cat) {
                    cat.isRep = (cat.cateCd === val);
                });
            });

            $('input[name="cateTopFix"]').on('change', function () {
                var $switch = $(this).closest('.ncua-switch');
                $('input[name="goodsSortTop"]').val($(this).val());

                $switch.find('.ncua-switch__option')
                    .removeClass('ncua-switch__option--active')
                    .addClass('ncua-switch__option--inactive');
                $switch.find('.ncua-switch__radio:checked').closest('.ncua-switch__option')
                    .removeClass('ncua-switch__option--inactive')
                    .addClass('ncua-switch__option--active');
            });

            $('#sgr-block-display-sale .ncua-switch__radio').on('change', function () {
                var $switch = $(this).closest('.ncua-switch');
                $switch.find('.ncua-switch__option')
                    .removeClass('ncua-switch__option--active')
                    .addClass('ncua-switch__option--inactive');
                $switch.find('.ncua-switch__radio:checked').closest('.ncua-switch__option')
                    .removeClass('ncua-switch__option--inactive')
                    .addClass('ncua-switch__option--active');
            });

            window.sgrCategoryBulkCallback = function (resultJson) {
                var addedCount = 0;
                var dupCount = 0;

                $.each(resultJson.info, function (idx, item) {
                    for (var i = 0; i < selectedCategories.length; i++) {
                        if (selectedCategories[i].cateCd === item.cateCd) {
                            dupCount++;
                            return true;
                        }
                    }

                    var isFirst = (selectedCategories.length === 0);
                    var pathText = item.cateNm.replace(/&gt;/g, '>');
                    selectedCategories.push({
                        cateCd: item.cateCd,
                        pathText: pathText,
                        isRep: isFirst
                    });
                    addedCount++;

                    if (isFirst) {
                        $cateInputRow.removeClass('is-visual-last');
                        $selectedCateRow.hide().fadeIn(FADE_DURATION_MS);
                        $('#cateTopFixRow').hide().fadeIn(FADE_DURATION_MS);
                    }
                });

                renderSelectedTable();

                if (dupCount > 0 && addedCount > 0) {
                    NCDSAlert({ message: addedCount + '개 카테고리가 추가되었습니다. (중복 ' + dupCount + '개 제외)', iconType: 'warning' });
                } else if (dupCount > 0 && addedCount === 0) {
                    NCDSAlert({ message: '이미 선택된 카테고리입니다.', iconType: 'warning' });
                }
            };

            $('.js-sgr-cate-bulk-btn').on('click', function () {
                layer_add_info('category', {
                    mode: 'search',
                    layerTitle: '카테고리 일괄선택',
                    callFunc: 'sgrCategoryBulkCallback'
                });
            });

            $('#categoryBrowser').on('click', '.js-sgr-cate-retry', function () {
                var depth = parseInt($(this).attr('data-depth'));
                var parentCateCd = $(this).attr('data-parent');
                loadColumn(depth, parentCateCd);
            });

            if (ROOT_CATE_DATA && ROOT_CATE_DATA.length > 0) {
                renderColumn(0, ROOT_CATE_DATA);
            } else {
                loadColumn(0, '');
            }
        })();

        $('#frmSimpleGoods').on('input', '.js-sgr-charcount, .sgr-text-count-input', function () {
            var max = parseInt($(this).attr('maxlength'));
            $(this).closest('.ncua-input').toggleClass('sgr-count-over', this.value.length >= max);
        });

        // 상품명: onBlur 유효성 (highlight만, 얼럿은 submit에서)
        $('#goodsNm').on('blur', function () {
            var val = this.value.trim();
            if (val.length === 0) {
                NCDSValidator.highlight(this);
            } else {
                clearGoodsNmError();
            }
        });

        // 상품명: onInput 시 에러 해제
        $('#goodsNm').on('input', function () {
            if (this.value.trim().length > 0) {
                clearGoodsNmError();
            }
        });

        function validateGoodsNm() {
            var val = $('#goodsNm').val().trim();
            if (val.length === 0) {
                NCDSValidator.highlight($('#goodsNm')[0]);
                NCDSAlert({ message: '상품명을 입력해주세요', iconType: 'error', callback: function () { $('#goodsNm').focus(); } });
                return false;
            }
            clearGoodsNmError();
            return true;
        }

        function clearGoodsNmError() {
            NCDSValidator.unhighlight($('#goodsNm')[0]);
        }

        // 상품명 검색 키워드 추가
        $('input[name="goodsSearchWordAddGoodsNmFl"]').on('click', function () {
            var goodsNm = $.trim($('input[name="goodsNm"]').val());
            var target = $('input[name="goodsSearchWord"]');
            if (goodsNm.length > 0 && $(this).prop('checked')) {
                var maxLength = parseInt(target.attr('maxlength'));
                var oldKeyword = $.trim(target.val());
                var newKeyword = oldKeyword ? goodsNm + ',' + oldKeyword : goodsNm;
                if (newKeyword.length > maxLength) {
                    newKeyword = newKeyword.substr(0, maxLength);
                }
                target.val(newKeyword);
                target.trigger('input');
            }
        });

        $('.js-sgr-brand-select-btn').on('click', function () {
            layer_add_info('brand', {mode: 'radio'});
        });

        // Block 6: 상품이미지
        var SGR_IMAGE_ACCEPT_TYPES = [
            'image/png', 'image/jpeg', 'image/gif', 'image/bmp',
            'image/svg+xml', 'image/tiff', 'image/x-icon', 'application/postscript'
        ];
        var SGR_IMAGE_MAX_SIZE = 10 * 1024 * 1024;

        function sgrValidateImageFile(file) {
            if (!file) {
                return { valid: false, message: '' };
            }
            if (SGR_IMAGE_ACCEPT_TYPES.indexOf(file.type) === -1) {
                return { valid: false, message: '지원하지 않는 파일 형식입니다' };
            }
            if (file.size > SGR_IMAGE_MAX_SIZE) {
                return { valid: false, message: '파일 크기는 10MB 이하만 가능합니다' };
            }
            return { valid: true, message: '' };
        }

        function sgrSetOriginalImageFile(files) {
            var fileInput = document.getElementById('sgrOriginalImageInput');
            if (!fileInput) {
                return false;
            }
            try {
                var dataTransfer = new DataTransfer();
                if (files && files.length > 0) {
                    dataTransfer.items.add(files[0]);
                }
                fileInput.files = dataTransfer.files;
                return true;
            } catch (e) {
                return false;
            }
        }

        function sgrShowImageHint(message) {
            if (message) {
                NCDSAlert({ message: message, iconType: 'error' });
            }
        }

        var sgrImageOnChangeInProgress = false;

        if (typeof ncua !== 'undefined' && ncua.ImageFileInput) {
            window.sgrImageFileInput = new ncua.ImageFileInput({
                container: 'sgr-image-file-input',
                size: 'sm',
                buttonLabel: '파일 찾기',
                maxFileCount: 1,
                accept: SGR_IMAGE_ACCEPT_TYPES.join(', '),
                hintItems: ['용량 : 최대 10MB', '권장 파일 형식 : png, jpg, jpeg, gif, bmp, svg'],
                disabled: false,
                onChange: function (newFiles) {
                    if (sgrImageOnChangeInProgress) {
                        return;
                    }
                    sgrImageOnChangeInProgress = true;
                    try {
                        sgrShowImageHint('');
                        if (!newFiles || newFiles.length === 0) {
                            sgrSetOriginalImageFile([]);
                            return;
                        }
                        var file = newFiles[0];
                        var check = sgrValidateImageFile(file);
                        if (!check.valid) {
                            sgrShowImageHint(check.message);
                            if (window.sgrImageFileInput && typeof window.sgrImageFileInput.clearFiles === 'function') {
                                window.sgrImageFileInput.clearFiles();
                            }
                            sgrSetOriginalImageFile([]);
                            if (window.sgrImageFileInput && typeof window.sgrImageFileInput.renderImagePreviews === 'function') {
                                window.sgrImageFileInput.renderImagePreviews();
                            }
                            return;
                        }
                        var ok = sgrSetOriginalImageFile([file]);
                        if (!ok) {
                            sgrShowImageHint('이미지 업로드에 실패했습니다. 다시 시도해주세요');
                            if (window.sgrImageFileInput && typeof window.sgrImageFileInput.clearFiles === 'function') {
                                window.sgrImageFileInput.clearFiles();
                            }
                        }
                        if (window.sgrImageFileInput && typeof window.sgrImageFileInput.renderImagePreviews === 'function') {
                            window.sgrImageFileInput.renderImagePreviews();
                        }
                    } catch (e) {
                        sgrShowImageHint('이미지 업로드에 실패했습니다. 다시 시도해주세요');
                        if (window.sgrImageFileInput && typeof window.sgrImageFileInput.clearFiles === 'function') {
                            window.sgrImageFileInput.clearFiles();
                        }
                        sgrSetOriginalImageFile([]);
                        if (window.sgrImageFileInput && typeof window.sgrImageFileInput.renderImagePreviews === 'function') {
                            window.sgrImageFileInput.renderImagePreviews();
                        }
                    } finally {
                        sgrImageOnChangeInProgress = false;
                    }
                }
            });

            var $protectedFiles = $('#sgr-image-file-input :file, #sgrOriginalImageInput').addClass('no-filestyle');
            if ($protectedFiles.filestyle) {
                try { $protectedFiles.filestyle('destroy'); } catch (e) { /* filestyle 미적용 상태면 무시 */ }
            }
        }

        // ─── 상세설명 에디터 (PC/모바일 탭 + Froala) ─────────────────
        (function () {
            var swiperEl = document.querySelector('.ncua-horizontal-tab--button-white .swiper');
            if (swiperEl && typeof ncua !== 'undefined' && ncua.Tab) {
                new ncua.Tab(swiperEl);
            }

            var mobileEditorInitialized = false;
            var $pcWrap = $('.js-sgr-editor-pc');
            var $mobileWrap = $('.js-sgr-editor-mobile');
            var $overlay = $('.js-sgr-editor-overlay');
            var $sameNotice = $('.js-sgr-editor-same-notice');
            var $sameCheckbox = $('.js-sgr-desc-same-checkbox');
            var mobileEditor2 = document.getElementById('editor2');

            // 탭 전환
            $(document).on('click', '.js-sgr-desc-tab', function () {
                var target = $(this).data('target');
                $('.js-sgr-desc-tab').removeClass('is-active');
                $(this).addClass('is-active');

                if (target === 'pc') {
                    $pcWrap.show();
                    $mobileWrap.hide();
                } else {
                    $mobileWrap.show();
                    $pcWrap.hide();
                    initMobileEditor();
                }
            });

            // 모바일 에디터 lazy 초기화
            function initMobileEditor() {
                if (mobileEditorInitialized || !mobileEditor2) return;
                if (typeof NcdsEditor !== 'undefined') {
                    var editor = new NcdsEditor(mobileEditor2);
                    mobileEditor2.ncdsEditor = editor;
                    mobileEditor2.froalaInstance = editor.froalaInstance || undefined;
                    mobileEditorInitialized = true;
                }
            }

            // 동일사용 체크박스
            $sameCheckbox.on('change', function () {
                if ($(this).is(':checked')) {
                    $overlay.show();
                    $sameNotice.show();
                } else {
                    $overlay.hide();
                    $sameNotice.hide();
                }
            });

            // 초기 로드 시 체크 상태 반영
            if ($sameCheckbox.is(':checked')) {
                $overlay.show();
                $sameNotice.show();
            }

            window.sgrSyncEditors = function () {
                var pcEditor = document.getElementById('editor');
                if (pcEditor && pcEditor.ncdsEditor) {
                    pcEditor.value = pcEditor.ncdsEditor.getHTML();
                }
                if (mobileEditorInitialized && mobileEditor2 && mobileEditor2.ncdsEditor) {
                    mobileEditor2.value = mobileEditor2.ncdsEditor.getHTML();
                }
            };

        })();

        var $saveBtn = $('#saveBtn');

        function setSaveBtnLoading() {
            if ($saveBtn.find('.ncua-btn__spinner').length === 0) {
                $saveBtn.addClass('is-loading is-disable').attr({'disabled': '', 'aria-busy': 'true'});
                $saveBtn.find('.ncua-btn__label').before('<span class="ncua-btn__spinner"></span>');
            }
        }

        // ─── 옵션/재고 블록 JS ───
            // 옵션 데이터 유무 판단
            function hasOptionData() {
                return window.sgrOption && window.sgrOption.hasData();
            }

            function showOptionSubRows() {
                $('#sgr-block-option-stock .sgr-option-sub-row').show();
                $('#sgr-block-option-stock .sgr-option-helper-buttons').show();
            }

            function hideOptionSubRows() {
                $('#sgr-block-option-stock .sgr-option-sub-row').hide();
                $('#sgr-block-option-stock .sgr-option-helper-buttons').hide();
            }

            function setOptionFlSwitchActive(value) {
                var $switch = $('#sgr-block-option-stock input[name="optionFl"]').first().closest('.ncua-switch');
                $switch.find('.ncua-switch__option')
                    .removeClass('ncua-switch__option--active')
                    .addClass('ncua-switch__option--inactive');
                var $target = (value === 'y')
                    ? $switch.find('.ncua-switch__option--left')
                    : $switch.find('.ncua-switch__option--right');
                $target.removeClass('ncua-switch__option--inactive')
                    .addClass('ncua-switch__option--active');
                $switch.find('.ncua-switch__radio[value="' + value + '"]').prop('checked', true);
            }


            $('#sgr-block-option-stock .ncua-switch__radio').on('change', function () {
                var value = $(this).val();
                var $switch = $(this).closest('.ncua-switch');
                $switch.find('.ncua-switch__option')
                    .removeClass('ncua-switch__option--active')
                    .addClass('ncua-switch__option--inactive');
                $switch.find('.ncua-switch__radio:checked').closest('.ncua-switch__option')
                    .removeClass('ncua-switch__option--inactive')
                    .addClass('ncua-switch__option--active');

                if (value === 'y') {
                    showOptionSubRows();
                } else {
                    if (hasOptionData()) {
                        NCDSConfirm({
                            message: '옵션 설정 변경',
                            subMessage: '옵션 설정을 사용안함으로 변경하면 입력한 옵션 정보가 초기화됩니다. 변경하시겠습니까?',
                            btnText: { confirmLabel: '변경', cancelLabel: '취소' },
                            callback: function (result) {
                                if (result) {
                                    hideOptionSubRows();
                                    if (window.sgrOption) window.sgrOption.reset();
                                    $('input[name="stockCnt"]').prop('disabled', false).closest('.ncua-input').removeClass('is-disabled');
                                } else {
                                    setOptionFlSwitchActive('y');
                                }
                            }
                        });
                    } else {
                        hideOptionSubRows();
                        $('input[name="stockCnt"]').prop('disabled', false).closest('.ncua-input').removeClass('is-disabled');
                    }
                }
            });

        $('#sgr-block-sale-info input[name="stockFl"]').on('change', function () {
            if ($(this).val() === 'y') {
                $('#sgr-stock-input-area').show();
            } else {
                $('#sgr-stock-input-area').hide();
            }
        });

        // ─── Block 5: 옵션 등록 ──────────────────────────────────────────────
        (function () {
            var MAX_OPT_NAMES = 5;
            var $optRegister = $('.sgr-opt-register');

            window.sgrOption = {
                combinations: [],
                loadedStockMap: {},

                hasData: function () {
                    var found = false;
                    $optRegister.find('.js-opt-name-row').each(function () {
                        if ($(this).find('.js-opt-name-input').val().trim()) { found = true; return false; }
                        $(this).find('.js-opt-val-input').each(function () {
                            if ($(this).val().trim()) { found = true; return false; }
                        });
                        if (found) return false;
                    });
                    return found;
                },

                reset: function () {
                    var $rows = $optRegister.find('.js-opt-name-row');
                    $rows.slice(1).remove();
                    var $first = $rows.first();
                    $first.find('.js-opt-name-input').val('');
                    var $vname = $first.find('.sgr-opt-vname');
                    $vname.find('.sgr-opt-vname-row').slice(1).remove();
                    $vname.find('.js-opt-val-input').val('');
                    $vname.find('.js-opt-price-input').val('');
                    $vname.find('.js-opt-url-input').val('');
                    $vname.find('.js-opt-image-file').val('');
                    $vname.find('.js-opt-image-tag-wrap').remove();
                    $first.find('.js-opt-name-del').prop('disabled', true);
                    updateTextCounters($first);
                    updateOptNameButtons();
                    window.sgrOption.combinations = [];
                    window.sgrOption.loadedStockMap = {};
                    $('#sgr-option-info-row').hide();
                    $('.sgr-option-table__bulk-row .js-sgr-bulk-field').val('');
                }
            };

            function updateTextCounters($scope) {
                ($scope || $optRegister).find('.sgr-text-count-input').each(function () {
                    if (sgrCharCountManager) sgrCharCountManager.update(this);
                });
            }

            $optRegister.find('.js-opt-price-input').number_only();
            $('.sgr-option-table__bulk-row [data-field="optionBuyPrice"], .sgr-option-table__bulk-row [data-field="optionPrice"]').number_only();

            var sgrCharcountSeq = 2;

            // 옵션값 row HTML 생성
            function createValRowHtml(isFirst) {
                var seq = ++sgrCharcountSeq;
                var urlActive = $('#optImageUrlCheck').is(':checked');
                return '<div class="sgr-opt-vname-row">' +
                    '<div class="sgr-opt-vname-row__val"><div class="ncua-input ncua-input--xs ncua-input--full-width-with-count" style="width:100%;"><div class="ncua-input__content-wrap"><div class="ncua-input__content"><div class="ncua-input__field ncua-input__field--xs"><input type="text" placeholder="Enter키로 연속 입력할 수 있습니다. 예시) XL" maxlength="255" class="sgr-text-count-input js-opt-val-input" data-charcount-key="sgr-opt-' + seq + '" /></div></div><div class="ncua-input__field-text-count" data-charcount-text="sgr-opt-' + seq + '"><output class="ncua-input__field-text-count-current">0</output><span>/255</span></div></div></div></div>' +
                    '<div class="sgr-opt-vname-row__price"><div style="display:flex;align-items:center;gap:8px;"><div class="ncua-input ncua-input--xs"><div class="ncua-input__content-wrap"><div class="ncua-input__content"><div class="ncua-input__field ncua-input__field--xs"><input type="text" inputmode="numeric" placeholder="0" class="js-opt-price-input" /></div></div></div></div><span class="sgr-unit-text">원</span></div></div>' +
                    '<div class="sgr-opt-vname-row__image"><button type="button" class="ncua-btn ncua-btn--xs ncua-btn--secondary-gray js-opt-image-btn"><svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" fill="none" viewBox="0 0 24 24" stroke="none"><path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 15v1.2c0 1.68 0 2.52-.327 3.162a3 3 0 0 1-1.311 1.311C18.72 21 17.88 21 16.2 21H7.8c-1.68 0-2.52 0-3.162-.327a3 3 0 0 1-1.311-1.311C3 18.72 3 17.88 3 16.2V15m14-7-5-5m0 0L7 8m5-5v12"/></svg><span class="ncua-btn__label">파일 찾기</span></button><input type="file" class="no-filestyle js-opt-image-file" accept="image/png,image/jpeg,image/gif,image/bmp,image/svg+xml,image/tiff,image/x-icon,application/postscript" style="display:none" /></div>' +
                    '<div class="sgr-opt-vname-row__url"><div class="ncua-input ncua-input--xs"><div class="ncua-input__content-wrap"><div class="ncua-input__content"><div class="ncua-input__field ncua-input__field--xs"><input type="text" placeholder="이미지 URL을 입력해주세요." class="js-opt-url-input" /></div></div></div></div></div>' +
                    '<div class="sgr-opt-vname-row__del"><button type="button" class="ncua-btn ncua-btn--xxs only-icon ncua-btn--tertiary-gray js-opt-val-del"' + (isFirst ? ' disabled' : '') + '><svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" fill="none" viewBox="0 0 24 24" stroke="none"><path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 3h6M3 6h18m-2 0-.701 10.52c-.105 1.578-.158 2.367-.499 2.965a3 3 0 0 1-1.298 1.215c-.62.3-1.41.3-2.993.3h-3.018c-1.582 0-2.373 0-2.993-.3A3 3 0 0 1 6.2 19.485c-.34-.598-.394-1.387-.499-2.966L5 6m5 4.5v5m4-5v5"/></svg></button></div>' +
                    '</div>';
            }

            // 옵션명 카드 row HTML 생성
            function createNameRowHtml() {
                var nameSeq = ++sgrCharcountSeq;
                var urlActive = $('#optImageUrlCheck').is(':checked');
                return '<div class="sgr-opt-register__row js-opt-name-row">' +
                    '<div class="sgr-opt-register__col-name">' +
                    '<div class="ncua-input ncua-input--xs ncua-input--full-width-with-count"><div class="ncua-input__content-wrap"><div class="ncua-input__content"><div class="ncua-input__field ncua-input__field--xs"><input type="text" placeholder="예시) 사이즈" maxlength="30" class="sgr-text-count-input js-opt-name-input" data-charcount-key="sgr-opt-' + nameSeq + '" /></div></div><div class="ncua-input__field-text-count" data-charcount-text="sgr-opt-' + nameSeq + '"><output class="ncua-input__field-text-count-current">0</output><span>/30</span></div></div></div>' +
                    '</div>' +
                    '<div class="sgr-opt-register__col-val">' +
                    '<div class="sgr-opt-vname-card' + (urlActive ? ' is-url-active' : '') + '">' +
                    '<div class="sgr-opt-vname">' +
                    '<div class="sgr-opt-vname-header">' +
                    '<div class="sgr-opt-vname-header__col--fill">옵션값</div>' +
                    '<div class="sgr-opt-vname-header__col--price">옵션가</div>' +
                    '<div class="sgr-opt-vname-header__col--image">옵션 이미지</div>' +
                    '<div class="sgr-opt-vname-header__col--url">옵션 이미지 URL</div>' +
                    '</div>' +
                    createValRowHtml(true) +
                    '</div>' +
                    '<button type="button" class="ncua-btn ncua-btn--xs ncua-btn--secondary-gray js-opt-val-add"><svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" fill="none" viewBox="0 0 24 24"><path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 5v14m-7-7h14"/></svg><span class="ncua-btn__label">옵션값 추가</span></button>' +
                    '</div>' +
                    '</div>' +
                    '<div class="sgr-opt-register__col-del">' +
                    '<button type="button" class="ncua-btn ncua-btn--xxs ncua-btn--secondary-gray js-opt-name-del"><div class="ncua-minus-icon"></div><span class="ncua-btn__label">삭제</span></button>' +
                    '</div>' +
                    '</div>';
            }

            function updateOptNameButtons() {
                var $rows = $optRegister.find('.js-opt-name-row');
                var count = $rows.length;
                $('#optNameAddBtn').prop('disabled', count >= MAX_OPT_NAMES);
                $rows.find('.js-opt-name-del').prop('disabled', count <= 1);
            }

            function updateOptImageVisibility() {
                $optRegister.find('.js-opt-name-row').each(function (idx) {
                    $(this).find('.sgr-opt-vname-card').toggleClass('is-image-hidden', idx > 0);
                });
            }

            // 옵션값/옵션명 Enter
            var lastOptEnter = 0;
            $optRegister.on('keydown', '.js-opt-val-input, .js-opt-name-input', function (e) {
                if (e.key === 'Enter' || e.keyCode === 13) e.preventDefault();
            });
            $optRegister.on('keyup', '.js-opt-val-input', function (e) {
                if (e.key !== 'Enter') return;
                var now = Date.now();
                if (now - lastOptEnter < 100) return;
                lastOptEnter = now;
                var $card = $(this).closest('.sgr-opt-vname-card');
                var $vname = $card.find('.sgr-opt-vname');
                var $newRow = $(createValRowHtml(false));
                $vname.append($newRow);
                $newRow.find('.js-opt-price-input').number_only();
                $newRow.find('.js-opt-val-input').focus();
            });

            // 옵션명 Enter → 옵션값 첫 행 포커스
            $optRegister.on('keyup', '.js-opt-name-input', function (e) {
                if (e.key !== 'Enter') return;
                var now = Date.now();
                if (now - lastOptEnter < 100) return;
                lastOptEnter = now;
                $(this).closest('.js-opt-name-row').find('.js-opt-val-input').first().focus();
            });

            // 정규 상품등록(option_value_check)과 동일하게 동일 옵션명 내 옵션값 중복 차단
            $optRegister.on('blur', '.js-opt-val-input', function () {
                var $input = $(this);
                var val = ($input.val() || '').trim();
                if (val === '') return;
                var isDup = false;
                $input.closest('.js-opt-name-row').find('.js-opt-val-input').each(function () {
                    if (this === $input[0]) return true;
                    if (($(this).val() || '').trim() === val) {
                        isDup = true;
                        return false;
                    }
                });
                if (isDup) {
                    NCDSAlert({ message: '현재 입력한 옵션값과 동일한 옵션값이 존재합니다.', iconType: 'error', callback: function () { $input.val('').focus(); } });
                }
            });

            // + 옵션값 추가 버튼
            $optRegister.on('click', '.js-opt-val-add', function () {
                var $vname = $(this).closest('.sgr-opt-vname-card').find('.sgr-opt-vname');
                var $newRow = $(createValRowHtml(false));
                $vname.append($newRow);
                $newRow.find('.js-opt-price-input').number_only();
                $newRow.find('.js-opt-val-input').focus();
            });

            // 옵션값 행 삭제
            $optRegister.on('click', '.js-opt-val-del', function () {
                $(this).closest('.sgr-opt-vname-row').remove();
            });

            // Footer 추가 → 옵션명 카드 추가
            $('#optNameAddBtn').on('click', function () {
                var $newRow = $(createNameRowHtml());
                $optRegister.find('.sgr-opt-register__footer').before($newRow);
                $newRow.find('.js-opt-price-input').number_only();
                updateOptNameButtons();
                updateOptImageVisibility();
                $newRow.find('.js-opt-name-input').focus();
            });

            // 옵션명 카드 삭제
            $optRegister.on('click', '.js-opt-name-del', function () {
                $(this).closest('.js-opt-name-row').remove();
                updateOptNameButtons();
                updateOptImageVisibility();
            });

            // 옵션 이미지 URL 체크박스 토글
            $('#optImageUrlCheck').on('change', function () {
                var active = this.checked;
                $optRegister.find('.sgr-opt-vname-card').toggleClass('is-url-active', active);
            });

            // 옵션 이미지 파일 찾기 버튼 → hidden file input 트리거
            $optRegister.on('click', '.js-opt-image-btn', function () {
                $(this).closest('.sgr-opt-vname-row__image').find('.js-opt-image-file').trigger('click');
            });

            // 옵션 이미지 파일 선택 시 → 버튼 유지 + 버튼 아래에 파일명 Tag 노출
            $optRegister.on('change', '.js-opt-image-file', function () {
                var $imageDiv = $(this).closest('.sgr-opt-vname-row__image');
                if (this.files && this.files[0]) {
                    var file = this.files[0];
                    var check = sgrValidateImageFile(file);
                    if (!check.valid) {
                        NCDSAlert({ message: check.message, iconType: 'error' });
                        $(this).val('');
                        return;
                    }
                    var fileName = file.name;
                    // 기존 Tag 제거 후 버튼 아래에 새 Tag 추가 (버튼은 유지)
                    $imageDiv.find('.js-opt-image-tag-wrap').remove();
                    $imageDiv.append(
                        '<div class="js-opt-image-tag-wrap" style="margin-top:4px;">' +
                        '<span class="ncua-tag ncua-tag--sm" style="max-width:100%;display:inline-flex;align-items:center;">' +
                        '<span class="ncua-tag__text" style="overflow:hidden;text-overflow:ellipsis;white-space:nowrap;">' + escHtml(fileName) + '</span>' +
                        '<button type="button" class="ncua-tag__close js-opt-image-remove"><svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" fill="none" viewBox="0 0 24 24"><path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 6 6 18M6 6l12 12"/></svg></button>' +
                        '</span></div>'
                    );
                }
            });

            // 옵션 이미지 Tag × 버튼 → 이미지 제거
            $optRegister.on('click', '.js-opt-image-remove', function (e) {
                e.preventDefault();
                e.stopPropagation();
                var $imageDiv = $(this).closest('.sgr-opt-vname-row__image');
                $imageDiv.find('.js-opt-image-file').val('');
                $imageDiv.find('.js-opt-image-tag-wrap').remove();
            });

            // 옵션 적용 버튼
            function collectOptionGroups() {
                var groups = [];
                $optRegister.find('.js-opt-name-row').each(function () {
                    var name = $(this).find('.js-opt-name-input').val().trim();
                    var values = [];
                    $(this).find('.sgr-opt-vname-row').each(function () {
                        var val = $(this).find('.js-opt-val-input').val().trim();
                        var price = $(this).find('.js-opt-price-input').val().trim();
                        var url = $(this).find('.js-opt-url-input').val().trim();
                        if (val) {
                            var fileInput = $(this).find('.js-opt-image-file')[0];
                            var hasImage = fileInput && fileInput.files && fileInput.files.length > 0;
                            var existingImage = $(this).find('.js-opt-existing-image').val() || '';
                            values.push({ val: val, price: price || '0', image: hasImage ? fileInput.files[0].name : existingImage, url: url });
                        }
                    });
                    if (values.length > 0) {
                        groups.push({ name: name, values: values });
                    }
                });
                return groups;
            }

            function applyOptionCombinations(groups, fresh) {
                var newCombinations = computeCombinations(groups);
                window.sgrOption.combinations = newCombinations;

                $('#sgr-option-info-row').show();
                document.dispatchEvent(new CustomEvent('sgr:option-combinations-updated', { detail: { fresh: !!fresh } }));
                NCDSAlert({ message: '옵션 조합이 갱신되었습니다.', iconType: 'success' });
            }

            $('#optionApplyBtn').on('click', function () {
                var forbiddenOptChars = ['`', '‘', '’', '“', '”', "'", '"'];
                var hasForbiddenOptChar = false;
                $optRegister.find('.js-opt-name-input, .js-opt-val-input').each(function () {
                    var val = $(this).val() || '';
                    for (var ci = 0; ci < forbiddenOptChars.length; ci++) {
                        if (val.indexOf(forbiddenOptChars[ci]) !== -1) {
                            hasForbiddenOptChar = true;
                            return false;
                        }
                    }
                });
                if (hasForbiddenOptChar) {
                    NCDSAlert({ message: '옵션명/옵션값에 사용할 수 없는 문자가 있습니다.', iconType: 'error' });
                    return;
                }

                var groups = collectOptionGroups();

                // 빈 옵션값
                if (groups.length === 0) {
                    NCDSAlert({ message: '옵션값을 1개 이상 입력해주세요.', iconType: 'error' });
                    return;
                }

                // 옵션값은 입력됐으나 옵션명이 비면 옵션명 공란 옵션이 저장되므로 차단
                if (groups.some(function (g) { return !g.name; })) {
                    NCDSAlert({ message: '옵션명을 입력해 주세요!', iconType: 'error' });
                    return;
                }

                var comboCount = countOptionCombinations(groups);
                if (comboCount > <?=DEFAULT_LIMIT_OPTION_COMBINATION;?>) {
                    NCDSAlert({ message: '옵션의 조합은 <?=DEFAULT_LIMIT_OPTION_COMBINATION;?>개 이하로 가능합니다.', iconType: 'error' });
                    return;
                }

                function proceedApply() {
                    // 기존 조합이 있으면 Confirm — 확인 시 기존 옵션 정보는 전부 초기화(fresh)
                    if (window.sgrOption.combinations.length > 0) {
                        NCDSConfirm({
                            message: '옵션 재적용',
                            subMessage: '옵션 정보 재적용 시 기존 옵션 정보는 삭제됩니다. 현재 등록된 옵션 정보로 조합하여 재적용 하시겠습니까?',
                            callback: function (result) {
                                if (result) {
                                    applyOptionCombinations(groups, true);
                                }
                            }
                        });
                        return;
                    }

                    applyOptionCombinations(groups, true);
                }

                // 옵션 조합 개수 경고 후 계속 진행 (일반등록 동일 정책)
                if (comboCount > <?=DEFAULT_LIMIT_OPTION_VALUE;?>) {
                    NCDSConfirm({
                        message: '옵션 조합 확인',
                        subMessage: '옵션값 개수가 ' + comboCount + '개 입니다. 옵션이 <?=DEFAULT_LIMIT_OPTION_VALUE;?>개 이상이 되면 너무 많아 작성이 힘들어 지거나 느려질 수 있습니다. 계속 옵션 작성 하시겠습니까?',
                        callback: function (result) {
                            if (result) {
                                proceedApply();
                            }
                        }
                    });
                    return;
                }

                proceedApply();
            });

            function countOptionCombinations(groups) {
                var nonEmpty = groups.map(function (g) {
                    return g.values.filter(function (v) { return v.val && v.val.trim().length > 0; });
                }).filter(function (vals) { return vals.length > 0; });

                if (nonEmpty.length === 0) return 0;

                return nonEmpty.reduce(function (acc, vals) { return acc * vals.length; }, 1);
            }

            function computeCombinations(groups) {
                var nonEmpty = groups.map(function (g) {
                    return g.values.filter(function (v) { return v.val && v.val.trim().length > 0; });
                }).filter(function (vals) { return vals.length > 0; });

                if (nonEmpty.length === 0) return [];

                return nonEmpty.reduce(function (acc, vals) {
                    var next = [];
                    acc.forEach(function (prefix) {
                        vals.forEach(function (v) {
                            next.push(prefix.concat([{ val: v.val, price: parseInt(v.price, 10) || 0 }]));
                        });
                    });
                    return next;
                }, [[]]).map(function (combo) {
                    var totalPrice = 0;
                    var values = [];
                    combo.forEach(function (item) {
                        values.push(item.val);
                        totalPrice += item.price;
                    });
                    return {
                        key: values.join('|'),
                        values: values,
                        optionPurchasePrice: 0,
                        optionPrice: totalPrice,
                        stockCnt: 0,
                        displayFl: 'y',
                        soldOutFl: 'y',
                        deliveryFl: 'normal',
                        selfOptionCd: '',
                        memo: ''
                    };
                });
            }

            updateOptNameButtons();

            var SGR_STOCK_REASON = <?= json_encode($stockReason, JSON_UNESCAPED_UNICODE) ?>;
            var SGR_DELIVERY_REASON = <?= json_encode($deliveryReason, JSON_UNESCAPED_UNICODE) ?>;

            var $optTableBody = $('#sgr-option-table-body');
            var $optTableEmpty = $('#sgr-option-table-empty');

            function escHtml(s) {
                return String(s).replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;').replace(/"/g, '&quot;');
            }

            function getDragHandleSvg() {
                return '<svg width="14" height="14" viewBox="0 0 14 14" fill="none" xmlns="http://www.w3.org/2000/svg"><circle cx="4.5" cy="2.5" r="1" fill="currentColor"/><circle cx="9.5" cy="2.5" r="1" fill="currentColor"/><circle cx="4.5" cy="7" r="1" fill="currentColor"/><circle cx="9.5" cy="7" r="1" fill="currentColor"/><circle cx="4.5" cy="11.5" r="1" fill="currentColor"/><circle cx="9.5" cy="11.5" r="1" fill="currentColor"/></svg>';
            }

            function normalizeDisplayFl(val) {
                if (val === 'y' || val === 'n') return val;
                return val === '노출안함' ? 'n' : 'y';
            }

            function normalizeSoldoutFl(val) {
                if (SGR_STOCK_REASON[val] !== undefined) return val;
                return 'y';
            }

            function normalizeDeliveryFl(val) {
                if (SGR_DELIVERY_REASON[val] !== undefined) return val;
                return 'normal';
            }

            function buildSelectHtml(field, value, options) {
                var html = '<select data-field="' + field + '">';
                options.forEach(function(opt) {
                    html += '<option value="' + opt.val + '"' + (value === opt.val ? ' selected' : '') + '>' + opt.label + '</option>';
                });
                html += '</select>';
                return html;
            }

            function buildReasonSelectHtml(field, value, reasonMap) {
                var html = '<select data-field="' + field + '">';
                Object.keys(reasonMap).forEach(function(key) {
                    html += '<option value="' + escHtml(key) + '"' + (value === key ? ' selected' : '') + '>' + escHtml(reasonMap[key]) + '</option>';
                });
                html += '</select>';
                return html;
            }

            function buildInputHtml(field, value) {
                return '<div class="ncua-input ncua-input--xs" style="width:100%;"><div class="ncua-input__content-wrap"><div class="ncua-input__content"><div class="ncua-input__field ncua-input__field--xs"><input type="text" data-field="' + field + '" value="' + escHtml(String(value)) + '" /></div></div></div></div>';
            }

            function buildNumericInputHtml(field, value, width, extraClass) {
                var classAttr = extraClass ? ' class="' + extraClass + '"' : '';
                return '<div class="ncua-input ncua-input--xs" style="width:' + width + ';"><div class="ncua-input__content-wrap"><div class="ncua-input__content"><div class="ncua-input__field ncua-input__field--xs"><input type="text" inputmode="numeric" data-field="' + field + '" value="' + escHtml(String(value)) + '"' + classAttr + ' /></div></div></div></div>';
            }

            function getActiveGroups() {
                var groups = [];
                $optRegister.find('.js-opt-name-row').each(function() {
                    var name = $(this).find('.js-opt-name-input').val().trim();
                    var hasVals = false;
                    $(this).find('.js-opt-val-input').each(function() {
                        if ($(this).val().trim()) { hasVals = true; return false; }
                    });
                    if (hasVals) groups.push({ name: name });
                });
                return groups;
            }

            function updateTableHeader() {
                var groups = getActiveGroups();
                var count = groups.length;
                for (var i = 1; i <= 5; i++) {
                    if (i <= count) {
                        $('th.js-sgr-opt-col-name' + i).find('div').text(groups[i - 1].name || ('옵션명' + i));
                        $('th.js-sgr-opt-col-name' + i + ', td.js-sgr-data-col-name' + i).show();
                    } else {
                        $('th.js-sgr-opt-col-name' + i + ', td.js-sgr-data-col-name' + i).hide();
                    }
                }
                $('.js-sgr-bulk-first-cell').attr('colspan', 1 + count);
            }

            function buildOptionNameCells(values, groupCount) {
                var cells = '';
                for (var i = 0; i < 5; i++) {
                    var isVisible = i < groupCount;
                    cells += '<td class="ncua-table__cell sgr-opt-col-name js-sgr-data-col-name' + (i + 1) + '"' +
                        (!isVisible ? ' style="display:none;"' : '') +
                        '><div>' + escHtml(values[i] || '') + '</div></td>';
                }
                return cells;
            }

            function buildCombinationRow(combo, saved) {
                var s = saved || {};
                var displayFlVal = normalizeDisplayFl(s.optionDisplayFl !== undefined ? s.optionDisplayFl : combo.displayFl);
                var soldoutFlVal = normalizeSoldoutFl(s.optionSoldoutFl !== undefined ? s.optionSoldoutFl : combo.soldOutFl);
                var deliveryFlVal = normalizeDeliveryFl(s.optionDeliveryFl !== undefined ? s.optionDeliveryFl : combo.deliveryFl);
                var groupCount = getActiveGroups().length;

                var html = '<tr class="ncua-table__row sgr-option-table__row" data-combination-key="' + escHtml(combo.key) + '">' +
                    '<td class="ncua-table__cell ncua-table__checkbox-cell sgr-opt-col-dragcheck"><div><span class="ncua-table__drag-handle sgr-option-drag-handle js-sgr-drag-handle" aria-hidden="true">' + getDragHandleSvg() + '</span>' +
                    '<label class="ncua-checkbox-field ncua-checkbox-field--xs" style="margin:0;">' +
                    '<span class="ncua-checkbox-input ncua-checkbox-input--xs ncua-checkbox-field__input">' +
                    '<input type="checkbox" class="js-sgr-row-check" />' +
                    '</span></label></div></td>' +
                    buildOptionNameCells(combo.values, groupCount) +
                    '<td class="ncua-table__cell" data-grid-key="optionCostPrice"><div style="display:flex;align-items:center;gap:8px;">' +
                    buildNumericInputHtml('optionBuyPrice', s.optionBuyPrice !== undefined ? s.optionBuyPrice : (combo.optionPurchasePrice || ''), '68px', '') +
                    '<span class="sgr-unit-text">원</span></div></td>' +
                    '<td class="ncua-table__cell" data-grid-key="optionPrice"><div style="display:flex;align-items:center;gap:8px;">' +
                    buildNumericInputHtml('optionPrice', s.optionPrice !== undefined ? s.optionPrice : (combo.optionPrice || ''), '68px', '') +
                    '<span class="sgr-unit-text">원</span></div></td>' +
                    '<td class="ncua-table__cell" data-grid-key="stockCnt"><div style="display:flex;align-items:center;gap:8px;">' +
                    buildNumericInputHtml('stockCnt', s.stockCnt !== undefined ? s.stockCnt : (combo.stockCnt || ''), '60px', 'js-number js-sgr-stock-input') +
                    '<span class="sgr-unit-text">개</span></div></td>' +
                    '<td class="ncua-table__cell" data-grid-key="optionViewFl"><div>' +
                    buildSelectHtml('optionDisplayFl', displayFlVal, [{ val: 'y', label: '노출함' }, { val: 'n', label: '노출안함' }]) +
                    '</div></td>' +
                    '<td class="ncua-table__cell" data-grid-key="optionSellFl"><div>' +
                    buildReasonSelectHtml('optionSoldoutFl', soldoutFlVal, SGR_STOCK_REASON) +
                    '</div></td>' +
                    '<td class="ncua-table__cell" data-grid-key="optionDeliveryFl"><div>' +
                    buildReasonSelectHtml('optionDeliveryFl', deliveryFlVal, SGR_DELIVERY_REASON) +
                    '</div></td>' +
                    '<td class="ncua-table__cell" data-grid-key="optionCode"><div>' +
                    buildInputHtml('optionCode', s.optionCode !== undefined ? s.optionCode : (combo.selfOptionCd || '')) +
                    '</div></td>' +
                    '<td class="ncua-table__cell" data-grid-key="optionMemo"><div>' +
                    buildInputHtml('optionMemo', s.optionMemo !== undefined ? s.optionMemo : (combo.memo || '')) +
                    '</div></td>' +
                    '</tr>';
                return html;
            }

            function renderOptionTable(combinations, fresh) {
                // 재렌더링 전 기존 입력값 보존 (조합 키 기준) — fresh 모드면 보존하지 않음
                var existing = {};
                if (!fresh) {
                    $optTableBody.find('.sgr-option-table__row').each(function() {
                        var key = $(this).data('combination-key');
                        if (key) {
                            existing[key] = {
                                optionBuyPrice: $(this).find('[data-field="optionBuyPrice"]').val(),
                                optionPrice: $(this).find('[data-field="optionPrice"]').val(),
                                stockCnt: $(this).find('[data-field="stockCnt"]').val(),
                                optionDisplayFl: $(this).find('[data-field="optionDisplayFl"]').val(),
                                optionSoldoutFl: $(this).find('[data-field="optionSoldoutFl"]').val(),
                                optionDeliveryFl: $(this).find('[data-field="optionDeliveryFl"]').val(),
                                optionCode: $(this).find('[data-field="optionCode"]').val(),
                                optionMemo: $(this).find('[data-field="optionMemo"]').val()
                            };
                        }
                    });
                }

                $optTableBody.find('.sgr-option-table__row').remove();

                if (!combinations || combinations.length === 0) {
                    $optTableEmpty.show();
                    $('#sgr-option-select-all').prop('checked', false).prop('indeterminate', false).closest('.ncua-checkbox-input').removeClass('has-indeterminate');
                    return;
                }

                $optTableEmpty.hide();
                var loadedMap = (window.sgrOption && window.sgrOption.loadedStockMap) || {};
                combinations.forEach(function(combo) {
                    var saved = existing[combo.key];
                    // 옵션가: 신규 조합·옵션등록 옵션가 수정분은 설정값 반영, 불러온 조합 미수정분(합산 0)만 기존 값 유지
                    if (saved) {
                        var keepLoadedPrice = loadedMap.hasOwnProperty(combo.key) && (parseInt(combo.optionPrice, 10) || 0) === 0;
                        if (!keepLoadedPrice) {
                            saved = $.extend({}, saved);
                            delete saved.optionPrice;
                        }
                    }
                    $optTableBody.append(buildCombinationRow(combo, saved));
                });
                $optTableBody.find('[data-field="optionBuyPrice"], [data-field="optionPrice"]').number_only();
                updateSelectAllState();
                sgrApplyGridColumns();
            }

            // sgr:option-combinations-updated 이벤트 구독
            document.addEventListener('sgr:option-combinations-updated', function(e) {
                var fresh = e.detail && e.detail.fresh;
                updateTableHeader();
                renderOptionTable(window.sgrOption.combinations || [], fresh);
                // 옵션 (재)적용 시 조합 테이블이 재생성되므로 일괄 적용 행의 잔여 입력값 초기화
                $('.sgr-option-table__bulk-row .js-sgr-bulk-field').val('');
            });

            // 전체 선택 Checkbox
            $(document).on('change', '#sgr-option-select-all', function() {
                var checked = $(this).prop('checked');
                $optTableBody.find('.js-sgr-row-check').prop('checked', checked);
                $optTableBody.find('.sgr-option-table__row').toggleClass('is-selected', checked);
                $(this).prop('indeterminate', false).closest('.ncua-checkbox-input').removeClass('has-indeterminate');
                $('.js-sgr-delete-selected-btn').prop('disabled', !checked || $optTableBody.find('.js-sgr-row-check').length === 0);
            });

            $(document).on('change', '#sgr-option-table-body .js-sgr-row-check', function() {
                $(this).closest('.sgr-option-table__row').toggleClass('is-selected', $(this).prop('checked'));
                updateSelectAllState();
            });

            function updateSelectAllState() {
                var total = $optTableBody.find('.js-sgr-row-check').length;
                var checked = $optTableBody.find('.js-sgr-row-check:checked').length;
                var allChecked = total > 0 && total === checked;
                var indeterminate = checked > 0 && !allChecked;
                $('#sgr-option-select-all').prop('checked', allChecked).prop('indeterminate', indeterminate)
                    .closest('.ncua-checkbox-input').toggleClass('has-indeterminate', indeterminate);
                $('.js-sgr-delete-selected-btn').prop('disabled', checked === 0);
            }

            // 일괄 적용 버튼
            $(document).on('click', '.js-sgr-bulk-apply-btn', function() {
                var $checked = $optTableBody.find('.js-sgr-row-check:checked');
                if ($checked.length === 0) {
                    NCDSAlert({ message: '선택된 옵션이 없습니다.', iconType: 'error' });
                    return;
                }

                var bulkValues = {};
                var hasAnyValue = false;
                $('.js-sgr-bulk-field').each(function() {
                    var field = $(this).data('field');
                    var val = $(this).val();
                    if (val !== '') {
                        bulkValues[field] = val;
                        hasAnyValue = true;
                    }
                });

                if (!hasAnyValue) {
                    NCDSAlert({ message: '적용할 값을 입력해주세요.', iconType: 'error' });
                    return;
                }

                $checked.each(function() {
                    applyBulkValuesToRow($(this).closest('.sgr-option-table__row'), bulkValues);
                });
                syncCombinationsFromTable();
                if (bulkValues.stockCnt !== undefined) {
                    document.dispatchEvent(new CustomEvent('sgr:option-stock-changed'));
                }
            });

            function applyBulkValuesToRow($row, bulkValues) {
                Object.keys(bulkValues).forEach(function(field) {
                    $row.find('[data-field="' + field + '"]').val(bulkValues[field]);
                });
            }

            function syncCombinationsFromTable() {
                $optTableBody.find('.sgr-option-table__row').each(function() {
                    var key = $(this).data('combination-key');
                    var combo = (window.sgrOption.combinations || []).find(function(c) { return c.key === key; });
                    if (combo) {
                        combo.optionPurchasePrice = $(this).find('[data-field="optionBuyPrice"]').val();
                        combo.optionPrice = $(this).find('[data-field="optionPrice"]').val();
                        combo.stockCnt = $(this).find('[data-field="stockCnt"]').val();
                        combo.displayFl = $(this).find('[data-field="optionDisplayFl"]').val();
                        combo.soldOutFl = $(this).find('[data-field="optionSoldoutFl"]').val();
                        combo.deliveryFl = $(this).find('[data-field="optionDeliveryFl"]').val();
                        combo.selfOptionCd = $(this).find('[data-field="optionCode"]').val();
                        combo.memo = $(this).find('[data-field="optionMemo"]').val();
                    }
                });
            }


            // 재고량 직접 변경 시
            var stockDebounceTimer = null;
            $(document).on('input', '#sgr-option-table-body .js-sgr-stock-input', function() {
                var key = $(this).closest('.sgr-option-table__row').data('combination-key');
                var combo = (window.sgrOption.combinations || []).find(function(c) { return c.key === key; });
                if (combo) { combo.stockCnt = $(this).val(); }
                clearTimeout(stockDebounceTimer);
                stockDebounceTimer = setTimeout(function() {
                    document.dispatchEvent(new CustomEvent('sgr:option-stock-changed'));
                }, 200);
            });

            // 정렬 버튼 enabled/disabled 토글
            function updateSortBtnsState() {
                var hasChecked = $optTableBody.find('.js-sgr-row-check:checked').length > 0;
                $('#sgr-sort-btn-group .js-sgr-sort-btn').prop('disabled', !hasChecked);
            }

            // DOM 행 순서 기반 combinations 배열 동기화
            function syncCombinationsOrderFromDOM() {
                var ordered = [];
                $optTableBody.find('.sgr-option-table__row').each(function() {
                    var key = $(this).data('combination-key');
                    var combo = (window.sgrOption.combinations || []).find(function(c) { return c.key === key; });
                    if (combo) ordered.push(combo);
                });
                window.sgrOption.combinations = ordered;
            }

            // 체크 변경 시 정렬 버튼 상태 갱신 (sgr-option-select-all 변경도 포함)
            $(document).on('change', '#sgr-option-table-body .js-sgr-row-check', function() {
                updateSortBtnsState();
            });
            $(document).on('change', '#sgr-option-select-all', function() {
                setTimeout(updateSortBtnsState, 0);
            });

            // 정렬 버튼 클릭
            $(document).on('click', '#sgr-sort-btn-group .js-sgr-sort-btn', function() {
                if ($(this).prop('disabled')) return;
                var direction = $(this).data('direction');
                var $rows = $optTableBody.find('.sgr-option-table__row');
                var $checked = $rows.filter(function() {
                    return $(this).find('.js-sgr-row-check:checked').length > 0;
                });
                if ($checked.length === 0) return;

                if (direction === 'top') {
                    $checked.get().reverse().forEach(function(row) { $optTableBody.prepend(row); });
                } else if (direction === 'up') {
                    $checked.each(function() {
                        var $prev = $(this).prev('.sgr-option-table__row');
                        if ($prev.length) { $prev.before(this); }
                    });
                } else if (direction === 'down') {
                    $checked.get().reverse().forEach(function(row) {
                        var $next = $(row).next('.sgr-option-table__row');
                        if ($next.length) { $next.after(row); }
                    });
                } else if (direction === 'bottom') {
                    $checked.each(function() { $optTableBody.append(this); });
                }

                syncCombinationsOrderFromDOM();
            });

            // HTML5 Drag & Drop
            var $sgrDragSrc = null;

            // mousedown on drag handle → draggable 활성화, 그 외에서는 비활성
            $(document).on('mousedown', '#sgr-option-table-body .js-sgr-drag-handle', function() {
                $(this).closest('.sgr-option-table__row').attr('draggable', 'true');
            });
            $(document).on('mouseup', '#sgr-option-table-body .sgr-option-table__row', function() {
                $(this).removeAttr('draggable');
            });

            $(document).on('dragstart', '#sgr-option-table-body .sgr-option-table__row', function(e) {
                $sgrDragSrc = $(this);
                $(this).addClass('is-dragging');
                e.originalEvent.dataTransfer.effectAllowed = 'move';
                e.originalEvent.dataTransfer.setData('text/plain', '');
            });

            $(document).on('dragover', '#sgr-option-table-body .sgr-option-table__row', function(e) {
                if (!$sgrDragSrc || $sgrDragSrc[0] === this) return;
                e.preventDefault();
                e.originalEvent.dataTransfer.dropEffect = 'move';
                $optTableBody.find('.sgr-option-table__row').not(this).removeClass('ncua-table__row--drag-over-top');
                $(this).addClass('ncua-table__row--drag-over-top');
            });

            $(document).on('dragleave', '#sgr-option-table-body .sgr-option-table__row', function(e) {
                if (!$(e.relatedTarget).closest('.sgr-option-table__row').is(this)) {
                    $(this).removeClass('ncua-table__row--drag-over-top');
                }
            });

            $(document).on('drop', '#sgr-option-table-body .sgr-option-table__row', function(e) {
                e.preventDefault();
                if (!$sgrDragSrc || $sgrDragSrc[0] === this) return;
                $(this).before($sgrDragSrc);
                syncCombinationsOrderFromDOM();
            });

            $(document).on('dragend', '#sgr-option-table-body .sgr-option-table__row', function() {
                $optTableBody.find('.sgr-option-table__row').removeClass('is-dragging ncua-table__row--drag-over-top').removeAttr('draggable');
                $sgrDragSrc = null;
            });

            window.sgrGridActiveKeys = null;

            window.sgrApplyGridColumns = function(activeKeys) {
                if (activeKeys) {
                    window.sgrGridActiveKeys = activeKeys;
                }
                var keys = window.sgrGridActiveKeys;
                if (!keys) return;
                var $table = $('.sgr-option-table');
                $table.find('tr').each(function() {
                    var $row = $(this);
                    var $gridCells = $row.children('[data-grid-key]');
                    if ($gridCells.length === 0) return;
                    var $anchor = $gridCells.first().prev();
                    var cellMap = {};
                    $gridCells.each(function() {
                        cellMap[$(this).data('grid-key')] = $(this);
                    });
                    $gridCells.detach();
                    var $insertAfter = $anchor;
                    for (var i = 0; i < keys.length; i++) {
                        var $cell = cellMap[keys[i]];
                        if ($cell) {
                            $cell.show();
                            $cell.insertAfter($insertAfter);
                            $insertAfter = $cell;
                            delete cellMap[keys[i]];
                        }
                    }
                    $.each(cellMap, function(k, $cell) {
                        $cell.hide();
                        $cell.insertAfter($insertAfter);
                        $insertAfter = $cell;
                    });
                });
            };

            $.post('/goods/goods_ps.php', {mode: 'get_goods_option_admin_grid_list', goodsOptionGridMode: 'goods_option_list'}, function(data) {
                if (data) {
                    var result = $.parseJSON(data);
                    if (result && result.select) {
                        var keys = [];
                        $.each(result.select, function(key) { keys.push(key); });
                        sgrApplyGridColumns(keys);
                    }
                }
            });

            // 조회항목설정 Confirm Alert
            $(document).on('click', '.js-sgr-grid-config-btn', function() {
                NCDSConfirm({
                    message: '조회항목 설정을 계속 진행하시겠습니까?',
                    subMessage: '조회항목 설정 시 화면이 새로고침되어 입력된 내용은 저장되지 않습니다.',
                    callback: function(result) {
                        if (!result) return;
                        window.sgrOption.combinations = [];
                        $optTableBody.find('.sgr-option-table__row').remove();
                        $optTableEmpty.show();
                        $('.sgr-option-table__bulk-row .js-sgr-bulk-field').val('');
                        $('#sgr-option-select-all').prop('checked', false).prop('indeterminate', false).closest('.ncua-checkbox-input').removeClass('has-indeterminate');
                        updateSortBtnsState();
                        $('#sgr-option-info-row').hide();
                        $('#sgr-stock-input-area input[name="stockCnt"]').prop('disabled', false).val('0').closest('.ncua-input').removeClass('is-disabled');
                        var colSettingModal = new window.ncua.Modal({
                            size: 'xl',
                            closeOnEsc: true,
                            onClose: function() { $(document).off('keydown.gridConfig'); }
                        });
                        window._colSettingModal = colSettingModal;
                        var modalEl = colSettingModal.getModalElement();
                        var colSettingHeader = new window.ncua.Modal.Header({
                            title: '조회항목 설정',
                            showDivider: true
                        });
                        colSettingHeader.setCloseHandler(function() { colSettingModal.close(); });
                        colSettingHeader.appendTo(modalEl);
                        var headerEl = modalEl.querySelector('.ncua-modal__header');
                        if (headerEl) headerEl.style.borderBottom = '1px solid var(--gray-100, #e4e5e7)';

                        var colSettingActions = new window.ncua.Modal.Actions('', {
                            layout: 'horizontal',
                            align: 'right'
                        });
                        var cancelBtn = document.createElement('button');
                        cancelBtn.className = 'ncua-btn ncua-btn--sm ncua-btn--secondary-gray';
                        cancelBtn.innerHTML = '<span class="ncua-btn__label">취소</span>';
                        cancelBtn.addEventListener('click', function() { colSettingModal.close(); });
                        var saveBtn = document.createElement('button');
                        saveBtn.className = 'ncua-btn ncua-btn--sm ncua-btn--primary';
                        saveBtn.innerHTML = '<span class="ncua-btn__label">설정</span>';
                        saveBtn.addEventListener('click', function() {
                            var activeKeys = $('.js-list-active .ncua-col-setting__item').map(function() {
                                return $(this).data('field-key');
                            }).get();
                            $('#goodsGridOptionForm input[name="mode"]').val('save_goods_option_admin_grid_list');
                            $.post('goods_ps.php', $('#goodsGridOptionForm').serialize(), function() {
                                if (typeof window.sgrApplyGridColumns === 'function') {
                                    window.sgrApplyGridColumns(activeKeys);
                                }
                                colSettingModal.close();
                            });
                        });
                        colSettingActions.setContent([cancelBtn, saveBtn]);

                        $.get('/share/ncds/layer_goods_option_grid_config.php', {goodsOptionGridMode: 'goods_option_list'}, function(html) {
                            var scripts = [];
                            var cleanHtml = html.replace(/<script[^>]*>([\s\S]*?)<\/script>/gi, function(m, code) {
                                scripts.push(code.replace(/^<!--/, '').replace(/\/\/-->$/, ''));
                                return '';
                            });
                            colSettingModal.open();
                            var $wrapper = $('<div/>').appendTo(modalEl);
                            $wrapper[0].innerHTML = cleanHtml;
                            colSettingActions.appendTo(modalEl);
                            var actionsEl = modalEl.querySelector('.ncua-modal__actions-wrapper');
                            if (actionsEl) actionsEl.style.borderTop = '1px solid var(--gray-100, #e4e5e7)';
                            scripts.forEach(function(s) { $.globalEval(s); });
                        });
                    }
                });
            });

            // 옵션 추가 (인라인 빈 행)
            function buildManualOptionNameCells(groupCount) {
                var cells = '';
                for (var i = 0; i < 5; i++) {
                    var isVisible = i < groupCount;
                    var placeholder = i === 0 ? '옵션명 입력' : ('옵션명' + (i + 1) + ' 입력');
                    cells += '<td class="ncua-table__cell sgr-opt-col-name js-sgr-data-col-name' + (i + 1) + '"' +
                        (!isVisible ? ' style="display:none;"' : '') +
                        '><div><div class="ncua-input ncua-input--xs" style="width:100%;"><div class="ncua-input__content-wrap"><div class="ncua-input__content"><div class="ncua-input__field ncua-input__field--xs"><input type="text" placeholder="' + placeholder + '" /></div></div></div></div></div></td>';
                }
                return cells;
            }

            function buildManualEntryRow(combo) {
                var groupCount = getActiveGroups().length;
                var html = '<tr class="ncua-table__row sgr-option-table__row" data-combination-key="' + escHtml(combo.key) + '">' +
                    '<td class="ncua-table__cell ncua-table__checkbox-cell sgr-opt-col-dragcheck"><div><span class="ncua-table__drag-handle sgr-option-drag-handle js-sgr-drag-handle" aria-hidden="true">' + getDragHandleSvg() + '</span>' +
                    '<label class="ncua-checkbox-field ncua-checkbox-field--xs" style="margin:0;">' +
                    '<span class="ncua-checkbox-input ncua-checkbox-input--xs ncua-checkbox-field__input">' +
                    '<input type="checkbox" class="js-sgr-row-check" />' +
                    '</span></label></div></td>' +
                    buildManualOptionNameCells(groupCount) +
                    '<td class="ncua-table__cell" data-grid-key="optionCostPrice"><div style="display:flex;align-items:center;gap:8px;">' +
                    buildNumericInputHtml('optionBuyPrice', '', '68px', '') +
                    '<span class="sgr-unit-text">원</span></div></td>' +
                    '<td class="ncua-table__cell" data-grid-key="optionPrice"><div style="display:flex;align-items:center;gap:8px;">' +
                    buildNumericInputHtml('optionPrice', '', '68px', '') +
                    '<span class="sgr-unit-text">원</span></div></td>' +
                    '<td class="ncua-table__cell" data-grid-key="stockCnt"><div style="display:flex;align-items:center;gap:8px;">' +
                    buildNumericInputHtml('stockCnt', '', '60px', 'js-number js-sgr-stock-input') +
                    '<span class="sgr-unit-text">개</span></div></td>' +
                    '<td class="ncua-table__cell" data-grid-key="optionViewFl"><div>' +
                    buildSelectHtml('optionDisplayFl', 'y', [{ val: 'y', label: '노출함' }, { val: 'n', label: '노출안함' }]) +
                    '</div></td>' +
                    '<td class="ncua-table__cell" data-grid-key="optionSellFl"><div>' +
                    buildReasonSelectHtml('optionSoldoutFl', 'y', SGR_STOCK_REASON) +
                    '</div></td>' +
                    '<td class="ncua-table__cell" data-grid-key="optionDeliveryFl"><div>' +
                    buildReasonSelectHtml('optionDeliveryFl', 'normal', SGR_DELIVERY_REASON) +
                    '</div></td>' +
                    '<td class="ncua-table__cell" data-grid-key="optionCode"><div>' + buildInputHtml('optionCode', '') + '</div></td>' +
                    '<td class="ncua-table__cell" data-grid-key="optionMemo"><div>' + buildInputHtml('optionMemo', '') + '</div></td>' +
                    '</tr>';
                return html;
            }

            $(document).on('click', '.js-sgr-add-option-btn', function() {
                var newCombo = {
                    key: 'manual_' + Date.now(),
                    values: [''],
                    manualEntry: true,
                    optionPurchasePrice: '',
                    optionPrice: '',
                    stockCnt: '',
                    displayFl: 'y',
                    soldOutFl: 'y',
                    deliveryFl: 'normal',
                    selfOptionCd: '',
                    memo: ''
                };
                window.sgrOption.combinations = window.sgrOption.combinations || [];
                window.sgrOption.combinations.push(newCombo);
                $optTableEmpty.hide();
                var $newRow = $(buildManualEntryRow(newCombo));
                $optTableBody.append($newRow);
                $newRow.find('[data-field="optionBuyPrice"], [data-field="optionPrice"]').number_only();
                updateSelectAllState();
                sgrApplyGridColumns();
            });

            // 선택 삭제
            $(document).on('click', '.js-sgr-delete-selected-btn', function() {
                var $checked = $optTableBody.find('.js-sgr-row-check:checked');
                if ($checked.length === 0) return;

                $checked.each(function() {
                    var key = $(this).closest('.sgr-option-table__row').data('combination-key');
                    window.sgrOption.combinations = (window.sgrOption.combinations || []).filter(function(c) { return c.key !== key; });
                    $(this).closest('.sgr-option-table__row').remove();
                });

                $('#sgr-option-select-all').prop('checked', false);
                updateSelectAllState();
                updateSortBtnsState();
                document.dispatchEvent(new CustomEvent('sgr:option-stock-changed'));

                if ($optTableBody.find('.sgr-option-table__row').length === 0) {
                    $optTableEmpty.show();
                    $('#sgr-option-info-row').hide();
                    $('#sgr-stock-input-area input[name="stockCnt"]').prop('disabled', false).val('0').closest('.ncua-input').removeClass('is-disabled');
                }
            });

            /**
             * 옵션 카드 영역을 groups 배열로 채운다.
             * @param {Array}  groups     [{name, values:[{val,price,image,url}]}]
             * @param {string} displayFl  's'|'d'
             */
            function fillOptCards(groups, displayFl, serverCombinations) {
                window.sgrOption.reset();
                $('[name="optionY[optionDisplayFl]"]').filter('[value="' + (displayFl === 'd' ? 'd' : 's') + '"]').prop('checked', true).trigger('change');

                $optRegister.find('.js-opt-name-row').remove();
                var $footer = $optRegister.find('.sgr-opt-register__footer');
                $.each(groups, function(i, grp) {
                    var $row = $(createNameRowHtml());
                    $row.find('.js-opt-name-input').val(grp.name);
                    $footer.before($row);
                    $.each(grp.values, function(j, v) {
                        var $valRow;
                        if (j === 0) {
                            $valRow = $row.find('.sgr-opt-vname-row').first();
                        } else {
                            $valRow = $(createValRowHtml(false));
                            $row.find('.sgr-opt-vname').append($valRow);
                        }
                        $valRow.find('.js-opt-val-input').val(v.val);
                        $valRow.find('.js-opt-price-input').val(v.price || 0);
                        if (v.url) { $valRow.find('.js-opt-url-input').val(v.url); }
                        if (v.image) {
                            var $imageDiv = $valRow.find('.sgr-opt-vname-row__image');
                            $imageDiv.find('.js-opt-image-tag-wrap').remove();
                            $imageDiv.append(
                                '<div class="js-opt-image-tag-wrap" style="margin-top:4px;">' +
                                '<span class="ncua-tag ncua-tag--sm" style="max-width:100%;display:inline-flex;align-items:center;">' +
                                '<span class="ncua-tag__text" style="overflow:hidden;text-overflow:ellipsis;white-space:nowrap;">' + escHtml(v.image) + '</span>' +
                                '<button type="button" class="ncua-tag__close js-opt-image-remove"><svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" fill="none" viewBox="0 0 24 24"><path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 6 6 18M6 6l12 12"/></svg></button>' +
                                '</span>' +
                                '<input type="hidden" class="js-opt-existing-image" value="' + escHtml(v.image) + '" />' +
                                '</div>'
                            );
                        }
                    });
                });
                var hasUrl = groups.some(function(g) { return g.values.some(function(v) { return !!v.url; }); });
                if (hasUrl && !$('#optImageUrlCheck').is(':checked')) {
                    $('#optImageUrlCheck').prop('checked', true).trigger('change');
                }
                $optRegister.find('.js-opt-price-input').number_only();
                $('[name="optionY[optionDisplayFl]"]:checked').trigger('change');
                updateOptNameButtons();
                updateOptImageVisibility();
                updateSaveFreqBtnState();

                // 조합별 옵션 정보(옵션가·재고량 등)가 있으면 자동 옵션 적용 + stock 데이터 반영
                if (serverCombinations && serverCombinations.length > 0) {
                    var newGroups = collectOptionGroups();
                    var newCombinations = computeCombinations(newGroups);

                    // 서버 조합 데이터를 key 기준으로 lookup
                    var stockMap = {};
                    serverCombinations.forEach(function(s) {
                        stockMap[s.key] = s;
                    });

                    // 생성된 조합에 서버 stock 데이터 병합
                    newCombinations.forEach(function(combo) {
                        var stock = stockMap[combo.key];
                        if (stock) {
                            combo.optionPurchasePrice = parseInt(stock.optionCostPrice, 10) || 0;
                            combo.optionPrice = parseInt(stock.optionPrice, 10) || 0;
                            combo.stockCnt = parseInt(stock.stockCnt, 10) || 0;
                            combo.displayFl = stock.optionViewFl || 'y';
                            combo.soldOutFl = stock.optionSellFl || 'y';
                            combo.deliveryFl = stock.optionDeliveryFl || 'normal';
                            combo.selfOptionCd = stock.optionCode || '';
                            combo.memo = stock.optionMemo || '';
                        }
                    });

                    window.sgrOption.combinations = newCombinations;
                    window.sgrOption.loadedStockMap = stockMap;
                    $('#sgr-option-info-row').show();
                    document.dispatchEvent(new CustomEvent('sgr:option-combinations-updated', { detail: { fresh: true } }));
                }
            }

            function updateSaveFreqBtnState() {
                var hasName = false;
                $('.js-opt-name-input').each(function() { if ($.trim($(this).val()) !== '') { hasName = true; return false; } });
                $('[data-modal-open="optSaveFreqModal"]').prop('disabled', !hasName);
            }

            // ── Task 5: 기존상품 옵션 불러오기 ──────────────────────────────────────
            // 옵션 대체 성공 시에만 옵션 모달을 닫고, Confirm 취소·조회 실패 시에는 모달을 유지한다.
            // 'deferClose' 반환 시 레이어 공통 로직이 모달을 닫지 않고 콜백에 닫기를 위임한다.
            function sgrCloseOptionLayer() {
                $('div.bootstrap-dialog-close-button').click();
            }

            window.sgrApplyGoodsOption = function(resultJson) {
                if (!resultJson.info || !resultJson.info.length) return true;
                var goodsNo = resultJson.info[0].goodsNo;
                $.ajax({
                    url: './goods_ps.php', type: 'POST',
                    data: { mode: 'sgr_get_goods_option', goodsNo: goodsNo },
                    dataType: 'json',
                    success: function(res) {
                        if (res.error) {
                            NCDSAlert({ message: res.message, iconType: 'error' });
                            return;
                        }
                        var groups = res.groups || [];
                        var displayFl = res.optionDisplayFl || 's';
                        var combinations = res.combinations || [];
                        if (window.sgrOption.hasData()) {
                            NCDSConfirm({
                                message: '현재 입력된 옵션 정보가 불러온 옵션으로 대체됩니다. 진행하시겠습니까?',
                                callback: function(result) {
                                    if (result) {
                                        fillOptCards(groups, displayFl, combinations);
                                        sgrCloseOptionLayer();
                                    }
                                }
                            });
                        } else {
                            fillOptCards(groups, displayFl, combinations);
                            sgrCloseOptionLayer();
                        }
                    }
                });
                return 'deferClose';
            };

            $(document).on('click', '.js-sgr-option-load-existing', function() {
                layer_add_info('goods', {optionRegister: 'y', callFunc: 'sgrApplyGoodsOption', layerTitle: '상품 선택'});
            });

            // ── Task 6: 자주쓰는 옵션 불러오기 (ncds 레이어 팝업) ────────────────
            window.sgrApplyFreqOption = function(getData) {
                if (!getData || !getData.optionName) return;
                var names     = getData.optionName || [];
                var displayFl = getData.displayFl  || 's';
                var groups = [];
                $.each(names, function(i, nm) {
                    var rawVals = (getData.optionValue && getData.optionValue[i]) ? getData.optionValue[i] : [];
                    var vals = [];
                    $.each(rawVals, function(j, v) {
                        var parts = String(v).split('|');
                        vals.push({ val: parts[0] || '', price: parseInt(parts[1], 10) || 0, image: parts[2] || '', url: parts[3] || '' });
                    });
                    groups.push({ name: nm, values: vals });
                });
                if (window.sgrOption.hasData()) {
                    NCDSConfirm({
                        message: '현재 입력된 옵션 정보가 선택한 자주쓰는 옵션으로 대체됩니다. 진행하시겠습니까?',
                        callback: function(result) {
                            if (result) {
                                fillOptCards(groups, displayFl);
                                sgrCloseOptionLayer();
                            }
                        }
                    });
                } else {
                    fillOptCards(groups, displayFl);
                    sgrCloseOptionLayer();
                }
                return 'deferClose';
            };

            $(document).on('click', '.js-sgr-option-load-frequent', function() {
                var loadChk = $('#layerOptionListForm').length;
                $.get('ncds/layer_goods_option_list.php', { callFunc: 'sgrApplyFreqOption' }, function(data) {
                    if (loadChk == 0) {
                        data = '<div id="layerOptionListForm">' + data + '</div>';
                    }
                    ncds_layer_popup({ message: data, title: '자주쓰는 옵션 선택', size: 'wide' });
                });
            });

            // ── Task 7: 자주쓰는 옵션 등록 (ncds 레이어 팝업) ─────────────────────
            $(document).on('click', '[data-modal-open="optSaveFreqModal"]', function() {
                if ($(this).prop('disabled')) return;
                var loadChk = $('#layerOptionRegisterForm').length;
                $.get('ncds/layer_goods_option_register.php', function(data) {
                    if (loadChk == 0) {
                        data = '<div id="layerOptionRegisterForm">' + data + '</div>';
                    }
                    ncds_layer_popup({ message: data, title: '자주쓰는 옵션 등록', onshown: function(dialog) { dialog.getModalDialog().css('width', '560px'); } });
                });
            });

            // 초기 상태
            updateSaveFreqBtnState();
            $(document).on('input change', '.js-opt-name-input', updateSaveFreqBtnState);

            // 옵션 combinations 합계를 판매정보 재고수 필드에 반영하고 disabled 상태를 제어한다.
            // stockFl='n'(무한정판매) 상태는 재고수 영역 자체가 숨겨지므로 처리하지 않는다.
            function updateStockFromOptions() {
                var combinations = window.sgrOption ? window.sgrOption.combinations : [];
                var $stockCnt = $('input[name="stockCnt"]');
                var stockFl = $('input[name="stockFl"]:checked').val();

                var $stockCntWrap = $stockCnt.closest('.ncua-input');

                if (combinations.length > 0 && stockFl === 'y') {
                    var sum = combinations.reduce(function(acc, c) {
                        return acc + (parseInt(c.stockCnt) || 0);
                    }, 0);
                    if (sum > 9999999999) sum = 9999999999;
                    $stockCnt.val(sum).prop('disabled', true);
                    $stockCntWrap.addClass('is-disabled');
                } else if (combinations.length === 0) {
                    $stockCnt.prop('disabled', false).val(0);
                    $stockCntWrap.removeClass('is-disabled');
                }
            }

            document.addEventListener('sgr:option-stock-changed', function() {
                updateStockFromOptions();
            });

            // "옵션 적용" 버튼은 sgr:option-combinations-updated 를 발행하므로 추가 구독
            document.addEventListener('sgr:option-combinations-updated', function() {
                updateStockFromOptions();
            });

            // stockFl "재고량에 따름"(y) 전환 시 옵션 있으면 즉시 합계 재계산
            $('input[name="stockFl"]').on('change.sgrStockSync', function() {
                updateStockFromOptions();
            });

        })();

        $('#frmSimpleGoods').on('submit', function (e) {
            var nmValid = validateGoodsNm();
            if (!nmValid) {
                e.preventDefault();
                $('#goodsNm').focus();
                return false;
            }

            // 기본 배송비조건이 없으면 등록 불가
            if (!$('input[name="deliverySno"]').val()) {
                e.preventDefault();
                sgrShowNoDeliveryWarning();
                return false;
            }

            if (window.sgrSyncEditors) window.sgrSyncEditors();

            // ── 옵션 이미지 name 할당 + 옵션 조합 직렬화 (간편상품등록 옵션/재고) ──
            // 옵션 이미지 file input에 name 속성 할당 (옵션명 카드 인덱스 기준)
            $('#frmSimpleGoods .js-opt-name-row').each(function (nameIdx) {
                $(this).find('.js-opt-image-file').attr('name', 'optionYIcon[goodsImage][' + nameIdx + '][]');
                $(this).find('.js-opt-existing-image').attr('name', 'sgrExistingImage[' + nameIdx + '][]');
                $(this).find('.js-opt-url-input').attr('name', 'optionYIcon[goodsImageText][' + nameIdx + '][]');
            });
            $('#frmSimpleGoods .js-sgr-opt-addurl-hidden').remove();
            if ($('#optImageUrlCheck').is(':checked')) {
                $('#frmSimpleGoods').append('<input type="hidden" class="js-sgr-opt-addurl-hidden" name="optionImageAddUrl" value="y" />');
            }

            // 옵션 사용 시 조합 데이터를 optionY 히든 필드로 직렬화
            $('#frmSimpleGoods .js-sgr-option-hidden').remove();
            // 옵션 사용함인데 적용된 옵션 조합이 없으면 사용안함으로 전환 후 저장 (일반등록 동일 동작)
            if ($('input[name="optionFl"]:checked').val() === 'y'
                && !(window.sgrOption && window.sgrOption.combinations && window.sgrOption.combinations.length > 0)) {
                $('input[name="optionFl"][value="n"]').prop('checked', true);
                $('input[name="stockCnt"]').prop('disabled', false);
            }
            var optFl = $('input[name="optionFl"]:checked').val();
            var combinations = window.sgrOption ? window.sgrOption.combinations : [];
            if (optFl === 'y' && combinations.length > 0) {
                var STR_DIV = '<?=STR_DIVISION?>';
                var $form = $('#frmSimpleGoods');
                var groups = [];
                $('.sgr-opt-register .js-opt-name-row').each(function () {
                    var name = $(this).find('.js-opt-name-input').val().trim();
                    var vals = [];
                    $(this).find('.js-opt-val-input').each(function () {
                        var v = $(this).val().trim();
                        if (v) vals.push(v);
                    });
                    if (vals.length > 0) groups.push({ name: name, values: vals });
                });

                var h = function(name, value) {
                    $form.append($('<input type="hidden" class="js-sgr-option-hidden">').attr('name', name).val(value));
                };

                groups.forEach(function (g, gi) {
                    h('optionY[optionName][]', g.name);
                    h('optionY[optionCnt][]', g.values.length);
                    g.values.forEach(function (v) {
                        h('optionY[optionValue][' + gi + '][]', v);
                    });
                });

                var currentValues = {};
                $('#sgr-option-table-body .sgr-option-table__row').each(function () {
                    var key = $(this).data('combination-key');
                    currentValues[key] = {
                        optionBuyPrice: $(this).find('[data-field="optionBuyPrice"]').val() || '0',
                        optionPrice: $(this).find('[data-field="optionPrice"]').val() || '0',
                        stockCnt: $(this).find('[data-field="stockCnt"]').val() || '0',
                        optionDisplayFl: $(this).find('[data-field="optionDisplayFl"]').val() || 'y',
                        optionSoldoutFl: $(this).find('[data-field="optionSoldoutFl"]').val() || 'y',
                        optionDeliveryFl: $(this).find('[data-field="optionDeliveryFl"]').val() || 'normal',
                        optionCode: $(this).find('[data-field="optionCode"]').val() || '',
                        optionMemo: $(this).find('[data-field="optionMemo"]').val() || ''
                    };
                });

                combinations.forEach(function (combo) {
                    var cv = currentValues[combo.key] || {};
                    var soldoutFl = cv.optionSoldoutFl || 'y';
                    var deliveryFl = cv.optionDeliveryFl || 'normal';
                    h('optionY[sno][]', '');
                    h('optionY[optionValueText][]', combo.values.join(STR_DIV));
                    h('optionY[optionCostPrice][]', cv.optionBuyPrice || '0');
                    h('optionY[optionPrice][]', cv.optionPrice || combo.optionPrice || '0');
                    h('optionY[stockCnt][]', cv.stockCnt || combo.stockCnt || '0');
                    h('optionY[optionViewFl][]', cv.optionDisplayFl === 'n' ? 'n' : 'y');
                    h('optionY[optionSellFl][]', soldoutFl);
                    h('optionY[optionDeliveryFl][]', deliveryFl);
                    h('optionY[optionCode][]', cv.optionCode || '');
                    h('optionY[optionMemo][]', cv.optionMemo || '');
                });
            }

            // ── 판매재고/옵션재고 불일치 확인 ──
            var form = this;
            var stockFl = $('input[name="stockFl"]:checked').val();
            var hasOptionStock = optFl === 'y' && combinations.length > 0 && combinations.some(function (combo) {
                var cv = currentValues ? currentValues[combo.key] : null;
                return parseInt((cv && cv.stockCnt) || combo.stockCnt || '0') > 0;
            });

            if (stockFl === 'n' && hasOptionStock) {
                e.preventDefault();
                NCDSConfirm({
                    message: '판매재고 변경 확인',
                    subMessage: '상품재고가 등록되었습니다.\n판매재고를 "재고량에 따름"으로 변경 후 상품 정보를 저장하시겠습니까?',
                    btnText: { confirmLabel: '예', cancelLabel: '아니오' },
                    callback: function (result) {
                        if (result) {
                            $('input[name="stockFl"][value="y"]').prop('checked', true);
                            var $stockCnt = $('input[name="stockCnt"]');
                            var sum = combinations.reduce(function (acc, c) {
                                var cv = currentValues ? currentValues[c.key] : null;
                                return acc + (parseInt((cv && cv.stockCnt) || c.stockCnt || '0') || 0);
                            }, 0);
                            $stockCnt.val(sum);
                        }
                        setSaveBtnLoading();
                        form.target = 'ifrmProcess';
                        form.submit();
                    }
                });
                return false;
            }

            setSaveBtnLoading();
            this.target = 'ifrmProcess';
        });

    });
</script>

<script type="text/javascript">
    if (window.GodoCosGuide) {
        window.GodoCosGuide.init({
            apiUrl: <?= json_encode($cosGuideApiUrl) ?>,
            guideCode: '260512001',
        });
    }
</script>
