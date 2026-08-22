<article class="ncua-goods-select" id="iframe_goodsChoiceList">
    <h2 class="ncua-popup-goods__title">상품 선택
        <?php if($checkCheckboxType) {?>
        <span class="goodsChoice_title_sub">최대 등록 가능한 상품수는 <span class="text-orange-red">500</span>개 입니다. 500개 초과시 기존 등록된 상품은 <span class="text-orange-red">자동 삭제</span> 됩니다</span>
        <?php }?>
    </h2>
    <form id="frmSearchBase" name="frmSearchBase" method="post">
        <input type="hidden" name="detailSearch" value="<?php echo $search['detailSearch']; ?>"/>
        <input type="hidden" name="sort"/>
        <input type="hidden" name="page"/>
        <input type="hidden" name="pageNum"/>
        <input type="hidden" name="setGoodsList">
        <input type="hidden" id="selectedGoodsList" name="selectedGoodsList" value="<?=$selectedGoodsList?>" />
        <div class="ncua-table ncua-table--vertical">
            <table>
                <tbody>
                    <?php if (gd_use_provider() === true) { ?>
                    <tr>
                        <th><div>공급사 구분</div></th>
                        <td>
                            <div class="ncua-goods-select-supplies-container ncua-flex-column">
                                <div class="ncua-goods-select-supplies-choice ncua-flex-gap">
                                    <p class="supply-radio-group ncua-goods-select-supplies ncua-flex-gap">
                                        <label class="ncua-radio-field ncua-radio-field--xs has-text">
                                            <span class="ncua-radio-input ncua-radio-input--xs ncua-radio-field__input">
                                                <input type="radio" name="scmFl" value="all" <?php echo gd_isset($checked['scmFl']['all']); ?> />
                                            </span>
                                            <span class="ncua-radio-field__text">전체</span>
                                        </label>
                                        <label class="ncua-radio-field ncua-radio-field--xs has-text">
                                            <span class="ncua-radio-input ncua-radio-input--xs ncua-radio-field__input">
                                                <input type="radio" name="scmFl" value="n" <?php echo gd_isset($checked['scmFl']['n']); ?> />
                                            </span>
                                            <span class="ncua-radio-field__text">본사</span>
                                        </label>
                                        <label class="ncua-radio-field ncua-radio-field--xs has-text">
                                            <span class="ncua-radio-input ncua-radio-input--xs ncua-radio-field__input">
                                                <input type="radio" name="scmFl" value="y" <?php echo gd_isset($checked['scmFl']['y']); ?> />
                                            </span>
                                            <span class="ncua-radio-field__text">공급사</span>
                                        </label>
                                    </p>

                                    <div id="ncua-combo-box-layer"></div>
                                </div>
                                <div id="scmLayer" class="ncua-flex ncua-gap-4 ncua-flex-wrap"></div>

                            </div>
                            </td>
                        </tr>
                    <?php } ?>
                    <tr>
                        <th><div>검색어</div></th>
                        <td><div class="ncua-goods-select-keyword">
                                <span class="ncua-select ncua-select--xs">
                                    <span class="ncua-select__content">
                                <?php echo gd_select_box('key', 'key', $search['combineSearch'], null, $search['key'], null, null, 'ncua-select__tag'); ?>
                                    </span>
                                </span> 
                                <div class="ncua-input ncua-input--xs">
                                    <div class="ncua-input__content">
                                        <div class="ncua-input__field ncua-input__field--xs">
                                            <input type="text" name="keyword" value="<?php echo $search['keyword']; ?>" placeholder="검색어를 입력하세요." />
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <th><div>기간설정</div></th>
                        <td>
                            <div class="ncua-goods-select-date">
                                <label class="ncua-radio-field ncua-radio-field--xs has-text">
                                    <span class="ncua-radio-input ncua-radio-input--xs ncua-radio-field__input">
                                        <input type="radio" name="searchDateFl" value="regDt" <?php echo gd_isset($checked['searchDateFl']['regDt']); ?> />
                                    </span>
                                    <span class="ncua-radio-field__text">등록일</span>
                                </label>
                                <label class="ncua-radio-field ncua-radio-field--xs has-text">
                                    <span class="ncua-radio-input ncua-radio-input--xs ncua-radio-field__input">
                                        <input type="radio" name="searchDateFl" value="modDt" <?php echo gd_isset($checked['searchDateFl']['modDt']); ?> />
                                    </span>
                                    <span class="ncua-radio-field__text">수정일</span>
                                </label>
                                <div id="datepicker-container"></div>
                            </div>
                        </td>
                    </tr>
                </tbody>

                <tbody class="js-search-detail" class="display-none">
                    <tr>
                        <th><div>카테고리</div></th>
                        <td>
                            <div class="ncua-goods-select-category">
                                <div class="ncua-goods-select-groups js-category-box"><?php echo $category->getMultiCategoryBox(null, gd_isset($search['cateGoods']), 'class="ncua-select__tag"'); ?></div>
                                
                                <label class="ncua-checkbox-field ncua-checkbox-field--xs has-text">
                                    <span class="ncua-checkbox-input ncua-checkbox-input--xs ncua-checkbox-field__input">
                                        <input type="checkbox" name="categoryNoneFl" value="y" <?php echo gd_isset($checked['categoryNoneFl']['y']); ?>>
                                    </span>
                                    <span class="ncua-checkbox-field__text">카테고리 미지정 상품</span>
                                </label>
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <th><div>브랜드</div></th>
                        <td>
                            <div class="ncua-goods-select-brand">
                                <div class="js-brand-box ncua-goods-select-groups"><?php echo $brand->getMultiCategoryBox(null, gd_isset($search['brand']), 'class="ncua-select__tag"'); ?></div>

                                <label class="ncua-checkbox-field ncua-checkbox-field--xs has-text">
                                    <span class="ncua-checkbox-input ncua-checkbox-input--xs ncua-checkbox-field__input">
                                        <input type="checkbox" name="brandNoneFl" value="y" <?php echo gd_isset($checked['brandNoneFl']['y']); ?>>
                                    </span>
                                    <span class="ncua-checkbox-field__text">브랜드 미지정 상품</span>
                                </label>
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <th><div>PC쇼핑몰<br />상품노출 상태</div></th>
                        <td>
                            <div class="ncua-flex-gap">    
                                <label class="ncua-radio-field ncua-radio-field--xs has-text">
                                    <span class="ncua-radio-input ncua-radio-input--xs ncua-radio-field__input">
                                        <input type="radio" name="goodsDisplayFl" value="" <?=gd_isset($checked['goodsDisplayFl']['']); ?> />
                                    </span>
                                    <span class="ncua-radio-field__text">전체</span>
                                </label>
                                <label class="ncua-radio-field ncua-radio-field--xs has-text">
                                    <span class="ncua-radio-input ncua-radio-input--xs ncua-radio-field__input">
                                        <input type="radio" name="goodsDisplayFl" value="y" <?=gd_isset($checked['goodsDisplayFl']['y']); ?> />
                                    </span>
                                    <span class="ncua-radio-field__text">노출함</span>
                                </label>
                                <label class="ncua-radio-field ncua-radio-field--xs has-text">
                                    <span class="ncua-radio-input ncua-radio-input--xs ncua-radio-field__input">
                                        <input type="radio" name="goodsDisplayFl" value="n" <?=gd_isset($checked['goodsDisplayFl']['n']); ?> />
                                    </span>
                                    <span class="ncua-radio-field__text">노출안함</span>
                                </label>
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <th><div>모바일쇼핑몰<br />상품노출 상태</div></th>
                        <td>
                            <div class="ncua-flex-gap">    
                                <label class="ncua-radio-field ncua-radio-field--xs has-text">
                                    <span class="ncua-radio-input ncua-radio-input--xs ncua-radio-field__input">
                                        <input type="radio" name="goodsDisplayMobileFl" value="" <?=gd_isset($checked['goodsDisplayMobileFl']['']); ?> />
                                    </span>
                                    <span class="ncua-radio-field__text">전체</span>
                                </label>
                                <label class="ncua-radio-field ncua-radio-field--xs has-text">
                                    <span class="ncua-radio-input ncua-radio-input--xs ncua-radio-field__input">
                                        <input type="radio" name="goodsDisplayMobileFl" value="y" <?=gd_isset($checked['goodsDisplayMobileFl']['y']); ?> />
                                    </span>
                                    <span class="ncua-radio-field__text">노출함</span>
                                </label>
                                <label class="ncua-radio-field ncua-radio-field--xs has-text">
                                    <span class="ncua-radio-input ncua-radio-input--xs ncua-radio-field__input">
                                        <input type="radio" name="goodsDisplayMobileFl" value="n" <?=gd_isset($checked['goodsDisplayMobileFl']['n']); ?> />
                                    </span>
                                    <span class="ncua-radio-field__text">노출안함</span>
                                </label>
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <th><div>PC쇼핑몰<br />상품판매 상태</div></th>
                        <td>
                            <div class="ncua-flex-gap">    
                                <label class="ncua-radio-field ncua-radio-field--xs has-text">
                                    <span class="ncua-radio-input ncua-radio-input--xs ncua-radio-field__input">
                                        <input type="radio" name="goodsSellFl" value="" <?=gd_isset($checked['goodsSellFl']['']); ?> />
                                    </span>
                                    <span class="ncua-radio-field__text">전체</span>
                                </label>
                                <label class="ncua-radio-field ncua-radio-field--xs has-text">
                                    <span class="ncua-radio-input ncua-radio-input--xs ncua-radio-field__input">
                                        <input type="radio" name="goodsSellFl" value="y" <?=gd_isset($checked['goodsSellFl']['y']); ?> />
                                    </span>
                                    <span class="ncua-radio-field__text">판매함</span>
                                </label>
                                <label class="ncua-radio-field ncua-radio-field--xs has-text">
                                    <span class="ncua-radio-input ncua-radio-input--xs ncua-radio-field__input">
                                        <input type="radio" name="goodsSellFl" value="n" <?=gd_isset($checked['goodsSellFl']['n']); ?> />
                                    </span>
                                    <span class="ncua-radio-field__text">판매안함</span>
                                </label>
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <th><div>모바일쇼핑몰<br />상품판매 상태</div></th>
                        <td>
                            <div class="ncua-flex-gap">    
                                <label class="ncua-radio-field ncua-radio-field--xs has-text">
                                    <span class="ncua-radio-input ncua-radio-input--xs ncua-radio-field__input">
                                        <input type="radio" name="goodsSellMobileFl" value="" <?=gd_isset($checked['goodsSellMobileFl']['']); ?> />
                                    </span>
                                    <span class="ncua-radio-field__text">전체</span>
                                </label>
                                <label class="ncua-radio-field ncua-radio-field--xs has-text">
                                    <span class="ncua-radio-input ncua-radio-input--xs ncua-radio-field__input">
                                        <input type="radio" name="goodsSellMobileFl" value="y" <?=gd_isset($checked['goodsSellMobileFl']['y']); ?> />
                                    </span>
                                    <span class="ncua-radio-field__text">판매함</span>
                                </label>
                                <label class="ncua-radio-field ncua-radio-field--xs has-text">
                                    <span class="ncua-radio-input ncua-radio-input--xs ncua-radio-field__input">
                                        <input type="radio" name="goodsSellMobileFl" value="n" <?=gd_isset($checked['goodsSellMobileFl']['n']); ?> />
                                    </span>
                                    <span class="ncua-radio-field__text">판매안함</span>
                                </label>
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <th><div>상품재고 상태</div></th>
                        <td>
                            <div class="ncua-flex-gap">
                                <label class="ncua-radio-field ncua-radio-field--xs has-text">
                                    <span class="ncua-radio-input ncua-radio-input--xs ncua-radio-field__input">
                                        <input type="radio" name="stockStateFl" value="all" <?php echo gd_isset($checked['stockStateFl']['all']); ?>/>
                                    </span>
                                    <span class="ncua-radio-field__text">전체</span>
                                </label>
                                <label class="ncua-radio-field ncua-radio-field--xs has-text">
                                    <span class="ncua-radio-input ncua-radio-input--xs ncua-radio-field__input">
                                        <input type="radio" name="stockStateFl" value="n" <?php echo gd_isset($checked['stockStateFl']['n']); ?>/>
                                    </span>
                                    <span class="ncua-radio-field__text">무한정 판매</span>
                                </label>
                                <label class="ncua-radio-field ncua-radio-field--xs has-text">
                                    <span class="ncua-radio-input ncua-radio-input--xs ncua-radio-field__input">
                                        <input type="radio" name="stockStateFl" value="u" <?php echo gd_isset($checked['stockStateFl']['u']); ?>/>
                                    </span>
                                    <span class="ncua-radio-field__text">재고있음</span>
                                </label>
                                <label class="ncua-radio-field ncua-radio-field--xs has-text">
                                    <span class="ncua-radio-input ncua-radio-input--xs ncua-radio-field__input">
                                        <input type="radio" name="stockStateFl" value="z" <?php echo gd_isset($checked['stockStateFl']['z']); ?>/>
                                    </span>
                                    <span class="ncua-radio-field__text">재고없음</span>
                                </label>
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <th><div>품절 상태</div></th>
                        <td>
                            <div class="ncua-flex-gap">    
                                <label class="ncua-radio-field ncua-radio-field--xs has-text">
                                    <span class="ncua-radio-input ncua-radio-input--xs ncua-radio-field__input">
                                        <input type="radio" name="soldOut" value="" <?=gd_isset($checked['soldOut']['']); ?> />
                                    </span>
                                    <span class="ncua-radio-field__text">전체</span>
                                </label>
                                <label class="ncua-radio-field ncua-radio-field--xs has-text">
                                    <span class="ncua-radio-input ncua-radio-input--xs ncua-radio-field__input">
                                        <input type="radio" name="soldOut" value="y" <?=gd_isset($checked['soldOut']['y']); ?> />
                                    </span>
                                    <span class="ncua-radio-field__text">품절</span>
                                </label>
                                <label class="ncua-radio-field ncua-radio-field--xs has-text">
                                    <span class="ncua-radio-input ncua-radio-input--xs ncua-radio-field__input">
                                        <input type="radio" name="soldOut" value="n" <?=gd_isset($checked['soldOut']['n']); ?> />
                                    </span>
                                    <span class="ncua-radio-field__text">정상</span>
                                </label>
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <th><div>판매가</div></th>
                        <td>
                            <div class="ncua-goods-select-price">
                                <div class="ncua-input ncua-input--xs">
                                    <div class="ncua-input__content">
                                        <div class="ncua-input__field ncua-input__field--xs">
                                            <input placeholder="최소 가격" type="text" name="goodsPrice[0]" value="<?php echo $search['goodsPrice'][0]; ?>" />
                                        </div>
                                        이상
                                    </div>
                                </div>
                                ~ 
                                <div class="ncua-input ncua-input--xs">
                                    <div class="ncua-input__content">
                                        <div class="ncua-input__field ncua-input__field--xs">
                                            <input placeholder="최대 가격" type="text" name="goodsPrice[1]" value="<?php echo $search['goodsPrice'][1]; ?>" />
                                        </div>
                                        이하
                                    </div>
                                </div>
                                
                            </div>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
        <p class="ncua-btn-group ncua-align-right">
            <button type="button" class="ncua-btn ncua-btn--xs ncua-btn--text ncua-btn--toggle js-search-toggle">상세검색 <span>펼침</span></button>
            <button type="reset" class="ncua-btn ncua-btn--xs ncua-btn--text has-underline">초기화</button>
            <button type="button" class="ncua-btn ncua-btn--xs ncua-btn--secondary search-goods-btn">검색</button>
        </p>
    </form>

    <!-- 검색 결과 -->
    <div class="ncua-search-result">
        <?php 
        /* INFO: 임시 주석 처리
        <div class="ncua-search-result__summary">
            <p class="ncua-search-result__summary-count">
                검색 <strong><?=number_format($page->recode['total'])?></strong>개 / 
                전체 <strong><?=number_format($page->recode['amount'])?></strong>개
            </p>
        </div>
        */
        ?>

            <form id="frmList" action="" method="get" target="ifrmProcess">
                <input type="hidden" name="mode" value="">
                <input type="hidden" name="relationFl" value="<?=$relationFl?>">
                <div class="ncua-search-result__content">
                    <?php if (($displayMode ?? '') !== 'noneSort') {?>
                    <!-- 검색 결과 액션 -->
                    <div class="ncua-search-result__actions">
                        <span class="ncua-select ncua-select--xs">
                            <span class="ncua-select__content">
                            <?php echo gd_select_box('sort', 'sort', $search['sortList'], null, $search['sort'], null, null, 'ncua-select__tag'); ?>
                            </span>
                        </span>
                        <span class="ncua-select ncua-select--xs">
                            <span class="ncua-select__content">
                            <?php echo gd_select_box('pageNum', 'pageNum', gd_array_change_key_value([10, 20, 30, 40, 50, 60, 70, 80, 90, 100, 200, 300, 500]), '개 보기', Request::get()->get('pageNum'), null, null, null, 'ncua-select__tag'); ?>
                            </span>
                        </span>
                    </div>
                    <?php } ?>
                    <div class="ncua-table ncua-table--horizontal">
                        <table id="tbl_add_goods">
                            <colgroup>
                                <col width="56px" />
                                <col width="80px" />
                                <col width="80px" />
                                <col />
                                <col />
                                <col />
                                <col width="130px" />
                                <col width="130px" />
                            </colgroup>
                            <thead>
                                <tr id="goodsRegisteredTrArea">
                                    <?php if($checkCheckboxType) {?>
                                    <th>
                                        <div>
                                            <label class="ncua-checkbox-field ncua-checkbox-field--xs">
                                                <span class="ncua-checkbox-input ncua-checkbox-input--xs ncua-checkbox-field__input">
                                                    <input type="checkbox" id="allCheck" value="y" onclick="all_checkbox(this,'tbl_add_goods')"/>
                                                </span>
                                            </label>
                                        </div>
                                    </th>
                                    <?php } else {?>
                                    <th><div>선택</div></th>
                                    <?php }?>
                                    <th><div>번호</div></th>
                                    <th><div>이미지</div></th>
                                    <th><div>상품명</div></th>
                                    <th><div>판매가</div></th>
                                    <th><div>공급사</div></th>
                                    <th><div>재고</div></th>
                                    <th><div>품절여부</div></th>
                                </tr>
                            </thead>

                            <tbody>
                            <?php
                            if (is_array(gd_isset($data))) {

                                foreach ($data as $key => $val) {

                                    list($totalStock,$stockText) = gd_is_goods_state($val['stockFl'],$val['totalStock'],$val['soldOutFl']);

                                    // 상품 아이콘
                                    if (empty($val['goodsIconCd']) === false && is_array($val['goodsIconCd']) === true) {
                                        foreach ($val['goodsIconCd'] as $iKey => $iVal) {
                                            $val['goodsIcon'] .= gd_html_image(UserFilePath::icon('goods_icon', $iVal['iconImage'])->www(), $iVal['iconNm']) . ' ';
                                        }
                                    }
                                    // 기간 제한용 아이콘
                                    if (empty($val['goodsIconStartYmd']) === false && empty($val['goodsIconEndYmd']) === false && empty($val['goodsIconCdPeriod']) === false && strtotime($val['goodsIconStartYmd']) <= time() && strtotime($val['goodsIconEndYmd']) >= time()) {
                                        foreach ($val['goodsIconCdPeriod'] as $iKey => $iVal) {
                                            $val['goodsIcon'] .=  gd_html_image(UserFilePath::icon('goods_icon', $iVal['iconImage'])->www(), $iVal['iconNm']) . ' ';
                                        }
                                    }

                                    // 품절 체크
                                    if ($val['soldOutFl'] == 'y' || ($val['stockFl'] == 'y' && $val['totalStock'] <= 0)) {
                                        $val['goodsIcon'] .= gd_html_image(UserFilePath::icon('goods_icon')->www() . '/' . 'icon_soldout.gif', '품절상품') . ' ';
                                    }

                                    if($val['timeSaleSno']) {
                                        $val['goodsIcon'] .= "<img src='" . PATH_ADMIN_GD_SHARE . "img/time-sale.png' alt='타임세일' /> ";
                                    }

                                    ?>

                                    <tr id="tbl_add_goods_<?php echo $val['goodsNo'];?>">
                                        <td>
                                            <div>
                                                <input type="hidden" name="itemGoodsNm[]" value="<?=gd_remove_only_tag($val['goodsNm'])?>" />
                                                <input type="hidden" name="itemGoodsPrice[]" value="<?=gd_currency_display($val['goodsPrice'])?>" />
                                                <input type="hidden" name="itemScmNm[]" value="<?=$val['scmNm']?>" />
                                                <input type="hidden" name="itemTotalStock[]" value="<?=$val['totalStock']?>" />
                                                <input type="hidden" name="itemBrandNm[]" value="<?=gd_isset($val['brandNm'])?>" />
                                                <input type="hidden" name="itemMakerNm[]" value="<?=gd_isset($val['makerNm'])?>" />
                                                <input type="hidden" name="itemSoldOutFl[]" value="<?=gd_isset($val['soldOutFl'])?>" />
                                                <input type="hidden" name="itemStockFl[]" value="<?=gd_isset($val['stockFl'])?>" />
                                                <input type="hidden" name="itemImage[]" value="<?=rawurlencode(gd_html_goods_image($val['goodsNo'], $val['imageName'], $val['imagePath'], $val['imageStorage'], 30, $val['goodsNm'], '_blank')); ?>" />
                                                <label class="ncua-radio-field ncua-radio-field--xs">
                                                    <span class="ncua-<?=$checkType?>-input ncua-<?=$checkType?>-input--xs ncua-<?=$checkType?>-field__input">
                                                        <input type="<?=$checkType?>" name="itemGoodsNo[]" id="layer_goods_<?php echo $val['goodsNo'];?>"  value="<?php echo $val['goodsNo']; ?>" <?php if($timeSaleFl && $val['timeSaleSno']) { echo "disabled='disabled'"; } ?>/>
                                                    </span>
                                                </label>
                                                <input type="hidden" name="itemGoodsDisplayFl[]" value="<?=gd_isset($val['goodsDisplayFl'])?>" />
                                                <input type="hidden" name="itemGoodsDisplayMobileFl[]" value="<?=gd_isset($val['goodsDisplayMobileFl'])?>" />
                                                <input type="hidden" name="itemGoodsSellFl[]" value="<?=gd_isset($val['goodsSellFl'])?>" />
                                                <input type="hidden" name="itemGoodsSellMobileFl[]" value="<?=gd_isset($val['goodsSellMobileFl'])?>" />
                                                <input type="hidden" name="itemIcon[]" value="<?=rawurlencode(gd_isset($val['goodsIcon'])); ?>" />
                                                <input type="hidden" name="regDt[]" value="<?=gd_date_format('Y-m-d', gd_isset($val['regDt']))?>" />
                                            </div>
                                        </td>
                                        <td class="addGoodsNumber_<?php echo $val['goodsNo'];?>"><div><?php echo number_format($page->idx--); ?></div></td>
                                        <td>
                                            <div>
                                                <span class="itemImage"><?php echo gd_html_goods_image($val['goodsNo'], $val['imageName'], $val['imagePath'], $val['imageStorage'], 30, $val['goodsNm'], '_blank'); ?></span>
                                            </div>
                                        </td>
                                        <td>
                                            <div class="ncua-goods-select-product-name">
                                                <span class="itemName"><a class="text-blue hand js-goods-popup" data-goodsno="<?=$val['goodsNo']; ?>"><?php echo gd_remove_only_tag(stripslashes($val['goodsNm'])); ?></a></span> <input type="hidden" name="goodsNoData[]" value="<?=$val['goodsNo']?>" />
                                                <input type="checkbox" name="sortFix[]" class="layer_sort_fix_<?php echo $val['goodsNo'];?>"  value="<?php echo $val['goodsNo']; ?>" style="display:none" >
                                                <div>
                                                    <?php echo $val['goodsIcon']; ?>
                                                </div>
                                            </div>
                                        </td>
                                        <td><div><span class="itemPrice"><?php echo gd_currency_display($val['goodsPrice']); ?></span></div></td>
                                        <td><div><?php echo $val['scmNm']; ?></div></td>
                                        <td><div><?php echo $totalStock ?></div></td>
                                        <td><div><?=$stockText ?></div></td>
                                    </tr>
                                    <?php }
                            } else {
                                ?>
                                <tr>
                                    <td class="no-data" colspan="8"><div>검색된 정보가 없습니다.</div></td>
                                </tr>
                                <?php
                            }
                            ?>
                            </tbody>
                        </table>
                    </div>
                    <div class="ncua-table-bottom"></div>
                </div>
                <div class="ncua-pagination"><?php echo $page->getPage("#"); ?></div>
            </form>
    </div>
</article>
