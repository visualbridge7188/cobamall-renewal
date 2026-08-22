<link rel="stylesheet" href="<?=gd_set_browser_cache(PATH_ADMIN_GD_SHARE . 'ncds/css/share/layer-goods.css')?>">
<div class="modal-dialog__content layer_goods">
    <article class="ncua-content">
        <form id="layer_search_goods_frm">
            <div class="ncua-table ncua-table--vertical">
                <table>
                    <tbody>
                        <tr>
                            <th><div>검색어</div></th>
                            <td class="ncds-table__cell ncds-table__cell--search-keyword">
                                <div class="ncua-gap-4">
                                    <span class="ncua-select ncua-select--xs">
                                        <span class="ncua-select__content">
                                            <?php echo gd_select_box('key','key',$search['combineSearch'],null,$search['key'], null, null, 'ncua-select__tag');?>
                                        </span>
                                    </span>
                                    <div class="ncua-input ncua-input--xs ncua-input-width-320">
                                        <div class="ncua-input__content">
                                            <div class="ncua-input__field ncua-input__field--xs">
                                            <input type="text" name="keyword" value="<?= $search['keyword'] ?>"  placeholder="검색어를 입력하세요." />
                                            </div>
                                        </div>
                                    </div> 
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <th><div>기간 설정</div></th>
                            <td>
                                <div class="ncua-period-search ncua-flex-gap">
                                    <div class="ncua-radio-group ncua-flex ncua-flex-gap">
                                        <label class="ncua-radio-field ncua-radio-field--xs has-text">
                                            <span class="ncua-radio-input ncua-radio-input--xs ncua-radio-field__input">
                                                <input type="radio" name="searchDateFl" value="regDt" <?= (gd_isset($search['searchDateFl'], 'regDt') == 'regDt') ? 'checked' : '' ?> />
                                            </span>
                                            <span><span class="ncua-radio-field__text">등록일</span></span>
                                        </label>
                                        <label class="ncua-radio-field ncua-radio-field--xs has-text">
                                            <span class="ncua-radio-input ncua-radio-input--xs ncua-radio-field__input">
                                                <input type="radio" name="searchDateFl" value="modDt" <?= (gd_isset($search['searchDateFl'], 'regDt') == 'modDt') ? 'checked' : '' ?> />
                                            </span>
                                            <span><span class="ncua-radio-field__text">수정일</span></span>
                                        </label>
                                    </div>
                                    <div id="product-period-datepicker"></div>
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <th><div>카테고리 선택</div></th>
                            <td>
                                <div class="ncua-select-group ncua-gap-4 ncua-flex-wrap">
                                    <?php echo $cate->getMultiCategoryBox('layerCateGoods', gd_isset($search['cateGoods']), 'class="ncua-select__tag"'); ?>
                                    <label class="ncua-checkbox-field ncua-checkbox-field--xs has-text">
                                        <span class="ncua-checkbox-input ncua-checkbox-input--xs ncua-checkbox-field__input">
                                            <input type="checkbox" name="categoryNoneFl" value="y" <?= gd_isset($checked['categoryNoneFl']['y']) ?>/>
                                        </span>
                                        <span><span class="ncua-checkbox-field__text">미지정 상품</span></span>
                                    </label>
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <th><div>브랜드</div></th>
                            <td>
                                <div class="ncua-select-group ncua-gap-4 ncua-flex-wrap">
                                    <?php echo $brand->getMultiCategoryBox(null, gd_isset($search['brand']), 'class="ncua-select__tag"'); ?>
                                    <label class="ncua-checkbox-field ncua-checkbox-field--xs has-text">
                                        <span class="ncua-checkbox-input ncua-checkbox-input--xs ncua-checkbox-field__input">
                                            <input type="checkbox" name="brandNoneFl" value="y" <?= gd_isset($checked['brandNoneFl']['y']) ?>/>
                                        </span>
                                        <span><span class="ncua-checkbox-field__text">미지정 상품</span></span>
                                    </label>
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <th><div>판매가</div></th>
                            <td class="ncua-price-search">
                                <div class="ncua-gap-8">
                                    <div class="ncua-input ncua-input--xs ncua-text-input-unit">
                                        <div class="ncua-input__content">
                                            <div class="ncua-input__field ncua-input__field--xs ncua-input-width-80">
                                                <input type="text" name="goodsPrice[0]" value="<?=$search['goodsPrice'][0]; ?>" class="ncua-number js-number" placeholder="최소 가격"> 
                                            </div>
                                        </div>
                                        <div class="ncua-input-text">이상 ~</div>
                                    </div>
                                    <div class="ncua-input ncua-input--xs ncua-text-input-unit">
                                        <div class="ncua-input__content">
                                            <div class="ncua-input__field ncua-input__field--xs ncua-input-width-80">
                                                <input type="text" name="goodsPrice[1]" value="<?=$search['goodsPrice'][1]; ?>" class="ncua-number js-number" placeholder="최대 가격"> 
                                            </div>
                                        </div>
                                        <div class="ncua-input-text">이하</div>
                                    </div>
                                </div>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <p class="ncua-btn-group ncua-align-right">
                <button type="button" class="ncua-btn ncua-btn--xs has-underline ncua-btn--text" id="btn_reset_search">초기화</button>
                <button type="button" class="ncua-btn ncua-btn--xs ncua-btn--secondary" onclick="layer_list_search();">검색</button>
            </p>
        </form>

        <div class="ncua-search-result">
            <div class="ncua-search-result__summary">
                <p class="ncua-search-result__summary-count">검색 <strong><?= number_format(gd_isset($page->recode['total'], 0)); ?></strong>개 / 전체 <strong class="text-danger"><?= number_format(gd_isset($page->recode['amount'], 0)); ?></strong>개</p>
            </div>

            <div class="ncua-search-result__content">
                <div class="ncua-table ncua-table--horizontal ncua-table--rounded">
                <table>
                    <colgroup>
                        <col width="56px">
                        <col width="70px">
                        <col width="70px">
                        <col width="*">
                        <col width="100px">
                        <col width="80px">
                        <col width="70px">
                        <col width="80px">
                        <col width="100px">
                        <col width="100px">
                        <?php
                        if($optionRegister === 'y'){
                        ?><col width="90px"><?php
                        }
                        ?>
                    </colgroup>
                    <thead>
                        <tr>
                            <th><?php
                                if($optionRegister !== 'y' && $mode != 'multiSearch'){
                                ?><div>
                                    <label class="ncua-checkbox-field ncua-checkbox-field--xs">
                                        <span class="ncua-checkbox-input ncua-checkbox-input--xs ncua-checkbox-field__input">
                                            <input type="checkbox" id="allCheck" value="y" onclick="check_toggle(this.id,'layer_goods_');"/>
                                        </span>
                                    </label>
                                </div></th><?php
                                }
                            ?>
                            <th><div>번호</div></th>
                            <th><div>이미지</div></th>
                            <th><div>상품명</div></th>
                            <th><div>판매가</div></th>
                            <th><div>공급사</div></th>
                            <th><div>재고</div></th>
                            <th><div>품절상태</div></th>
                            <th><div>PC쇼핑몰<br />노출상태</div></th>
                            <th><div>모바일쇼핑몰<br />노출상태</div></th>
                            <?php
                            if($optionRegister === 'y'){
                            ?><th><div>옵션</div></th><?php
                            }
                            ?>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                            if (gd_isset($data) && is_array($data)) {
                                $i = 0;
                                foreach ($data as $key => $val) {

                                    list($totalStock,$stockText) = gd_is_goods_state($val['stockFl'],$val['totalStock'],$val['soldOutFl']);
                                    $goodsDisplay = $goodsDisplayMobile = '노출함';
                                    if ($val['goodsDisplayFl'] != 'y') $goodsDisplay = '노출안함';
                                    if ($val['goodsDisplayMobileFl'] != 'y') $goodsDisplayMobile = '노출안함';

                                    $checkboxType = 'checkbox';
                                    if($optionRegister === 'y' || $mode == 'multiSearch') $checkboxType = 'radio';

                                    $optionViewButton = '<button type="button" class="ncua-btn ncua-btn--xxs ncua-btn--secondary-gray" onclick="show_goods_option_stock('.$val['goodsNo'].');"><span class="ncua-btn__label">옵션보기</span></button>';
                                    if($val['optionFl'] === 'n') $optionViewButton = '사용안함';
                        ?>
                        <tr id="tbl_goods_<?= $val['goodsNo'] ?>">
                            <td>
                                <div>
                                    <?php if ($checkboxType === 'radio') { ?>
                                    <label class="ncua-radio-field ncua-radio-field--xs">
                                        <span class="ncua-radio-input ncua-radio-input--xs ncua-radio-field__input">
                                            <input type="radio" id="layer_goods_<?= $val['goodsNo'] ?>" name="layer_goods" value="<?= $val['goodsNo'] ?>" />
                                        </span>
                                    </label>
                                    <?php } else { ?>
                                    <label class="ncua-checkbox-field ncua-checkbox-field--xs">
                                        <span class="ncua-checkbox-input ncua-checkbox-input--xs ncua-checkbox-field__input">
                                            <input type="checkbox" id="layer_goods_<?= $val['goodsNo'] ?>" name="layer_goods_<?= $i ?>" value="<?= $val['goodsNo'] ?>" />
                                        </span>
                                    </label>
                                    <?php } ?>
                                </div>
                            </td>
                            <td><div><?= number_format($page->idx--) ?></div></td>
                            <td>
                                <div>
                                    <?php echo gd_html_goods_image($val['goodsNo'], $val['imageName'], $val['imagePath'], $val['imageStorage'], 30, $val['goodsNm'], '_blank', 'id="goodsImage_'.$val['goodsNo'].'"');?>
                                </div>
                            </td>
                            <td>
                                <div class="ncua-left-align">
                                    <a class="text-blue hand" style="word-break: break-all;" onclick="goods_register_popup('<?=$val['goodsNo']; ?>' <?php if(gd_is_provider() === true) { echo ",'1'"; } ?>);"><?= gd_remove_tag($val['goodsNm']) ?></a>
                                    <input type="hidden" id="goodsNm_<?= $val['goodsNo'] ?>" value="<?= gd_remove_tag($val['goodsNm']) ?>" />
                                    <input type="hidden" id="regDt_<?= $val['goodsNo'] ?>" value="<?= gd_date_format('Y-m-d', $val['regDt']) ?>" />
                                    <div>
                                        <?php

                                        // 상품 아이콘
                                        if (empty($val['goodsIconCd']) === false && is_array($val['goodsIconCd']) === true) {
                                            foreach ($val['goodsIconCd'] as $iKey => $iVal) {
                                                echo gd_html_image(UserFilePath::icon('goods_icon', $iVal['iconImage'])->www(), $iVal['iconNm']) . ' ';
                                            }
                                        }

                                        // 기간 제한용 아이콘
                                        if (empty($val['goodsIconStartYmd']) === false && empty($val['goodsIconEndYmd']) === false && empty($val['goodsIconCdPeriod']) === false && strtotime($val['goodsIconStartYmd']) <= time() && strtotime($val['goodsIconEndYmd']) >= time()) {
                                            foreach ($val['goodsIconCdPeriod'] as $iKey => $iVal) {
                                                echo gd_html_image(UserFilePath::icon('goods_icon', $iVal['iconImage'])->www(), $iVal['iconNm']) . ' ';
                                            }
                                        }

                                        // 품절 체크
                                        if ($val['soldOutFl'] == 'y' || ($val['stockFl'] == 'y' && $val['totalStock'] <= 0)) {
                                            echo gd_html_image(UserFilePath::icon('goods_icon')->www() . '/' . 'icon_soldout.gif', '품절상품') . ' ';
                                        }

                                        if($val['timeSaleSno']) {
                                            echo "<img src='" . PATH_ADMIN_GD_SHARE . "img/time-sale.png' alt='타임세일' /> ";
                                        }

                                        ?>
                                    </div>
                                </div>
                            </td>
                            <td id="goodsPrice_<?= $val['goodsNo'] ?>"><div><?= number_format($val['goodsPrice']) ?> 원</div></td>
                            <td id="scmNm_<?= $val['goodsNo'] ?>"><div><?= $val['scmNm'] ?></div></td>
                            <td id="totalStock_<?= $val['goodsNo'] ?>"><div><?= $totalStock ?></div></td>
                            <td id="stockTxt_<?= $val['goodsNo'] ?>"><div><?=$stockText?></div></td>
                            <td id="displayPc_<?= $val['goodsNo'] ?>"><div><?= $goodsDisplay ?></div></td>
                            <td id="displayMobile_<?= $val['goodsNo'] ?>"><div><?= $goodsDisplayMobile ?></div></td>
                            <?php
                            if($optionRegister === 'y') {
                            ?>
                                <td><div><?=$optionViewButton?></div></td>
                            <?php
                            }
                            ?>
                        </tr>
                    <?php
                        $i++;
                        }
                    } else {
                    ?>
                        <tr>
                            <td colspan="10"><div>검색된 정보가 없습니다.</div></td>
                        </tr>
                    <?php
                    }
                    ?>
                    </tbody>
                </table>
                <div class="ncua-layer-goods__footer-bar"></div>
                </div>
            </div>

            <div class="ncua-search-result-pagination">
                <div class="ncua-pagination">
                    <?php echo $page->getPage('layer_list_search(\'PAGELINK\')'); ?>
                </div>
            </div>
        </div>
    </article>
</div>

<div class="board-template-write-footer <?php if($req['mode'] != 'popup') {?>modal-dialog__footer<?php }?>">
    <button type="button" class="ncua-btn ncua-btn--sm ncua-btn--secondary-gray" onclick="layer_close();">취소</button>
    <button type="submit" class="ncua-btn ncua-btn--sm ncua-btn--primary" onclick="select_code();">선택 완료</button>
</div>

<script type="text/javascript">
    <!--
    $(document).ready(function(){
        $('input').keydown(function(e) {
            if (e.keyCode == 13) {
                layer_list_search();
                return false;
            }
        });

        // 초기화 버튼 클릭 이벤트
        $('#btn_reset_search').on('click', function(e) {
            e.preventDefault();
            reset_search_form();
            layer_list_search();
        });
    });

    let periodDatePicker;
    try {
        // 기간 검색
        periodDatePicker = new ncua.DatePicker(document.querySelector('#product-period-datepicker'), {
            size: 'xs', 
            datePickerOptions: [
                {
                element: 'start-date',
                attrName: 'searchDate[0]',
                options: {
                    mode: 'single',
                    static: true,
                    defaultDate: null,
                    dateFormat: 'Y-m-d',
                    time_24hr: true,
                    clickOpens: true,
                    allowInvalidPreload: true,
                    locale: 'ko',
                    },
                },
                {
                element: 'end-date',
                attrName: 'searchDate[1]',
                options: {
                    mode: 'single',
                    static: true,
                    defaultDate: null,
                    dateFormat: 'Y-m-d',
                    time_24hr: true,
                    clickOpens: true,
                    allowInvalidPreload: true,
                    locale: 'ko',
                    },
                },
            ],
        });

        periodDatePicker.setDate(["<?= $search['searchDate'][0] ?>", "<?= $search['searchDate'][1] ?>"]);
    } catch {
        // ignore
    }

    // 검색 폼 초기화 함수
    const reset_search_form = () => {
        const form = document.querySelector('#layer_search_goods_frm');
        if (!form) return;
        
        // 검색어 선택 초기화 (첫 번째 옵션으로)
        const keySelect = form.querySelector('select[name="key"]');
        if (keySelect) {
            const options = keySelect.querySelectorAll('option');
            if (options.length > 0) {
                keySelect.value = options[0].value;
            }
        }
        
        // 검색어 입력 초기화
        const keywordInput = form.querySelector('input[name="keyword"]');
        if (keywordInput) {
            keywordInput.value = '';
        }
        
        // 기간검색 라디오 버튼 초기화 (등록일로)
        const regDtRadio = form.querySelector('input[name="searchDateFl"][value="regDt"]');
        if (regDtRadio) {
            regDtRadio.checked = true;
        }
        
        // 기간 날짜 선택기 초기화
        if (typeof periodDatePicker !== 'undefined' && periodDatePicker?.setDate) {
            periodDatePicker.setDate([null, null]);
        }
        
        // 카테고리 선택 초기화
        for (let i = <?= DEFAULT_DEPTH_CATE;?>; i > 0; i--) {
            const cateSelect = document.querySelector(`#layerCateGoods${i}`);
            if (cateSelect) {
                cateSelect.value = '';
                cateSelect.dispatchEvent(new Event('change', { bubbles: true }));
            }
        }
        
        // 카테고리 미지정 체크박스 초기화
        const categoryNoneCheckbox = form.querySelector('input[name="categoryNoneFl"]');
        if (categoryNoneCheckbox) {
            categoryNoneCheckbox.checked = false;
        }
        
        // 브랜드 선택 초기화
        for (let i = <?= DEFAULT_DEPTH_BRAND;?>; i > 0; i--) {
            const brandSelect = document.querySelector(`#brand${i}`);
            if (brandSelect) {
                brandSelect.value = '';
                brandSelect.dispatchEvent(new Event('change', { bubbles: true }));
            }
        }
        
        // 브랜드 미지정 체크박스 초기화
        const brandNoneCheckbox = form.querySelector('input[name="brandNoneFl"]');
        if (brandNoneCheckbox) {
            brandNoneCheckbox.checked = false;
        }
        
        // 판매가 입력 초기화
        const priceInput0 = form.querySelector('input[name="goodsPrice[0]"]');
        const priceInput1 = form.querySelector('input[name="goodsPrice[1]"]');
        if (priceInput0) priceInput0.value = '';
        if (priceInput1) priceInput1.value = '';
    };

    function layer_list_search(pagelink)
    {
        pagelink = pagelink || '';
        var frm = $("#layer_search_goods_frm").serializeArray();
        var cateGoods = '';
        var brandGoods = '';
        var parameters = {
            'layerFormID': '<?= $layerFormID ?>',
            'parentFormID': '<?= $parentFormID ?>',
            'dataFormID': '<?= $dataFormID ?>',
            'dataInputNm': '<?= $dataInputNm ?>',
            'scmFl': '<?= $scmFl ?>',
            'scmNo': '<?= $scmNo ?>',
            'mode': '<?= $mode ?>',
            'callFunc': '<?= $callFunc ?>',
            'childRow': '<?= $childRow ?>',
            'pagelink': pagelink
        };

        $.each(frm, function(i, field){
            if(field.name) parameters[field.name] = field.value;
        });

        for (var i = <?= DEFAULT_DEPTH_CATE;?>; i > 0; i--) {
            if ($('#layerCateGoods'+i).val()) {
                cateGoods = $('#layerCateGoods'+i).val();
                break;
            }
        }

        for (var i = <?= DEFAULT_DEPTH_BRAND;?>; i > 0; i--) {
            if ($('#brand'+i).val()) {
                brandGoods = $('#brand'+i).val();
                break;
            }
        }

        parameters['cateGoods[]'] = cateGoods;
        parameters['brand[]'] = brandGoods;
        parameters['optionRegister'] = '<?=$optionRegister?>';

        $.get('/share/ncds/layer_goods.php', parameters, function(data){
            const $layerContainer = $('#<?= $layerFormID ?>');
            $layerContainer.html(data);
            
            // HTML 교체 직후 스크롤 위치 설정 (pagelink가 변경될 때만)
            if (pagelink && pagelink !== '' && typeof scrollToElementAfterHtmlReplace === 'function') {
                scrollToElementAfterHtmlReplace($layerContainer, '.ncua-search-result', {
                    offset: 20,
                    useAnimation: false
                });
            }
        });
    }

    function select_code()
    {
        if ($('#<?= $layerFormID ?> input[id*=\'layer_goods_\']:checked').length == 0) {
            NCDSAlert({message: '상품을 선택해 주세요.', iconType: 'error'});
            return false;
        }

        var checkboxCnt = $('#<?= $layerFormID ?> input[id*=\'layer_goods_\']').length;
        var applyGoodsCnt = 0;
        var chkGoodsCnt = 0;
        var resultJson = {
            "mode": "<?= $mode ?>",
            "parentFormID": "<?= $parentFormID ?>",
            "dataFormID": "<?= $dataFormID ?>",
            "dataInputNm": "<?= $dataInputNm ?>",
            "childRow": "<?= $childRow ?>",
            "info": []
        };

        $('#<?= $layerFormID ?> input[id*=\'layer_goods_\']:checked').each(function() {
            if ('<?=$optionRegister?>' == 'y' && '<?=$callFunc?>' === '') {
                NCDSConfirm({message: '선택된 상품의 옵션정보를 적용하시겠습니까? 적용 시 기존 등록된 정보는 삭제되며 상품에 적용중인 옵션이 아닌 경우 복구가 불가능합니다.', callback: (result)=>{
                    if (result) {
                        window.location.href='goods_register_option.php?copy=1&goodsNo=' + $(this).val();
                    }
                }});
                applyGoodsCnt = 0;
            }
            var goodsNo = $(this).val();
            var goodsNm = $('#goodsNm_'+goodsNo).val();
            var goodsImg = $('#goodsImage_'+goodsNo).get(0).src;
            var goodsInfo = $('#tbl_goods_'+goodsNo).html();
            var goodsPrice = $('#goodsPrice_'+goodsNo).html();
            var scmNm = $('#scmNm_'+goodsNo).html();
            var regDt = $('#regDt_'+goodsNo).val();
            var totalStock = $('#totalStock_'+goodsNo).html();
            var stockTxt = $('#stockTxt_'+goodsNo).html();
            var displayPc = $('#displayPc_'+goodsNo).html();
            var displayMobile = $('#displayMobile_'+goodsNo).html();

            if ($('#<?= $dataFormID ?>_'+goodsNo).length == 0) {

                resultJson.info.push({"goodsNo": goodsNo, "goodsNm": goodsNm, "scmNm": scmNm, "goodsImg": goodsImg, "goodsInfo": goodsInfo, "goodsPrice": goodsPrice, "regDt": regDt, "totalStock": totalStock, "stockTxt": stockTxt,"displayPc": displayPc,"displayMobile": displayMobile});

                applyGoodsCnt++;
            }
            chkGoodsCnt++;
        });

        if (applyGoodsCnt > 0) {
            let shouldDisplayDuplicateAlert = true;
            const callbackResult = <?php if($callFunc) { ?>
            <?=$callFunc?>(resultJson);
            <?php } else { ?>
            displayTemplate(resultJson);
            <?php } ?>

            if (callbackResult === true || callbackResult === 'deferClose') {
                shouldDisplayDuplicateAlert = false;
            }

            if (applyGoodsCnt !== chkGoodsCnt && shouldDisplayDuplicateAlert) {
                NCDSAlert({
                    message: `중복된 상품 ${chkGoodsCnt - applyGoodsCnt}개를 제외하고 총 ${applyGoodsCnt}개 상품이 추가되었습니다.`,
                    iconType: 'error',
                });
            }
            // 선택된 버튼 div 토글
            if (chkGoodsCnt > 0) {
                $('#' + resultJson.parentFormID).addClass('active');
            } else {
                $('#' + resultJson.parentFormID).removeClass('active');
            }
            // 'deferClose' 반환 콜백은 적용 성공 시점에 스스로 모달을 닫는다 (실패·취소 시 모달 유지)
            if (callbackResult !== 'deferClose') {
                $('div.bootstrap-dialog-close-button').click();
            }
        } else {
            NCDSAlert({message: '동일한 상품이 이미 존재합니다.', iconType: 'error'});
        }
    }
    /**
     * 상품 기본 출력
     * @param data
     */
    function displayTemplate(data) {
        if (data.dataInputNm == '') {
            data.dataInputNm = 'goodsNo';
        }

        var exceptTitle = ['simple', 'recom', 'plusReview'];
        if (data.info.length > 0 && !$('#' + data.parentFormID).children().is('h5') && $.inArray(data.mode, exceptTitle) === -1) {
            $('#' + data.parentFormID).prepend('<h5>선택된 상품</h5>');
        }

        var parentFormCount = $('#' + data.parentFormID+' tr').length;

        const hasNoData = $("#" + data.parentFormID + " .tr-no-data").length > 0;
        if (data.info.length > 0 && hasNoData) {
            $("#" + data.parentFormID + " .tr-no-data").remove();
            parentFormCount -= 1;
        }

        if(data.mode == 'search'){
            $.each(data.info, function (key, val) {
                var addHtml = "";
                addHtml += '<div id="' + data.dataFormID + '_' + val.goodsNo + '"  class="btn-group btn-group-xs">';
                addHtml += '<input type="hidden" name="' + data.dataInputNm + '[]" value="' + val.goodsNo + '" />';
                addHtml += '<input type="hidden" name="' + data.dataInputNm + 'Nm[]" value="' + val.goodsNm + '" />';
                addHtml += '<button type="button" class="btn btn-gray">' + val.goodsNm + '</button>';
                addHtml += '<button type="button" class="btn btn-red" data-toggle="delete" data-target="#'+data.dataFormID+'_'+ val.goodsNo+'">삭제</button>';
                addHtml += '</div>';
                $("#" + data.parentFormID).append(addHtml);
            });
        } else if(data.mode == 'simple'){
            $.each(data.info, function (key, val) {
                var addHtml = "";
                addHtml += '<tr id="' + data.dataFormID + '_' + val.goodsNo + '">';
                addHtml += `
                    <td>
                        <div>
                            <input type="hidden" name="${data.dataInputNm}[]" value="${val.goodsNo}">
                            <label class="ncua-checkbox-field ncua-checkbox-field--xs has-text">
                                <span class="ncua-checkbox-input ncua-checkbox-input--xs ncua-checkbox-field__input">
                                    <input type="checkbox" data-target="#${data.dataFormID}_${val.goodsNo}">
                                </span>
                            </label>
                        </div>
                    </td>
                `;
                addHtml += '<td><div><span class="number">' + (key + 1 + Number(data.childRow) + parentFormCount) + '</span></div></td>';
                addHtml += '<td><div><a href="<?php echo URI_HOME; ?>goods/goods_view.php?goodsNo=' + val.goodsNo + '" target="_blank"><img src="' + val.goodsImg + '" align="absmiddle" width="50" alt="' + val.goodsNm + '" title="' + val.goodsNm + '" /></a></div></td>';
                addHtml += '<td><div class="ncua-left-align"><a class="ncua-link" href="<?php echo URI_ADMIN; ?>goods/goods_register.php?goodsNo=' + val.goodsNo + '" target="_blank">' + val.goodsNm + '</a></div></td>';
                addHtml += '</tr>';
                $("#" + data.parentFormID).append(addHtml);
            });
        } else if (data.mode == 'recom') {
            if ($("#" + data.parentFormID + " #tbl_recom_goods_tr_none").length > 0) $("#" + data.parentFormID + " #tbl_recom_goods_tr_none").remove();
            $.each(data.info, function (key, val) {
                var addHtml = "";
                addHtml += '<tr id="' + data.dataFormID + '_' + val.goodsNo + '" class="recom_tr">';
                addHtml += '<td class="center"><input type="checkbox" name="del[]" value="' + val.goodsNo + '"></td>';
                addHtml += '<td class="center"><span class="number"><span>' + (key + 1 + Number(data.childRow)) + '</span><input type="hidden" name="' + data.dataInputNm + '[]" value="' + val.goodsNo + '" /></td>';
                addHtml += '<td class="center"><a href="<?php echo URI_HOME; ?>goods/goods_view.php?goodsNo=' + val.goodsNo + '" target="_blank"><img src="' + val.goodsImg + '" align="absmiddle" width="50" alt="' + val.goodsNm + '" title="' + val.goodsNm + '" /></a></td>';
                addHtml += '<td><a href="../goods/goods_register.php?goodsNo=' + val.goodsNo + '" target="_blank">' + val.goodsNm + '</a></td>';
                addHtml += '<td class="center">' + val.goodsPrice + '</td>';
                addHtml += '<td class="center">' + val.scmNm + '</td>';
                addHtml += '<td class="center">' + val.totalStock + '</td>';
                addHtml += '<td class="center">' + val.stockTxt + '</td>';
                addHtml += '<td class="center">' + val.displayPc + '</td>';
                addHtml += '<td class="center">' + val.displayMobile + '</td>';
                addHtml += '</tr>';
                $("#" + data.parentFormID).append(addHtml);
            });
        } else if(data.mode == 'plusReview') {
            $.each(data.info, function (key, val) {
                var addHtml = "";
                
                addHtml += '<tr id="' + data.dataFormID + '_' + val.goodsNo + '">';
                addHtml += '<td><div><label class="ncua-checkbox-field ncua-checkbox-field--xs has-text">';
                addHtml += '<span class="ncua-checkbox-input ncua-checkbox-input--xs ncua-checkbox-field__input">';
                addHtml += '<input type="checkbox" name="exceptGoodsChk"/>';
                addHtml += '<input type="hidden" name="' + data.dataInputNm + '[]" value="' + val.goodsNo + '" />';
                addHtml += '</span></label></div></td>';
                addHtml += '<td><div><span class="number">' + (key + 1 + Number(data.childRow) + parentFormCount) + '</span></div></td>';
                addHtml += '<td><div><a href="<?php echo URI_HOME; ?>goods/goods_view.php?goodsNo=' + val.goodsNo + '" target="_blank"><img src="' + val.goodsImg + '" align="absmiddle" width="50" alt="' + val.goodsNm + '" title="' + val.goodsNm + '" /></a></div></td>';
                addHtml += '<td><div class="ncua-left-align"><a class="ncua-link" href="<?php echo URI_ADMIN; ?>goods/goods_register.php?goodsNo=' + val.goodsNo + '" target="_blank">' + val.goodsNm + '</a></div></td>';
                addHtml += '</tr>';
                $("#" + data.parentFormID).append(addHtml);

                
            });

            
        } else if(data.mode == 'multiSearch') {
            $.each(data.info, function (key, val) {
                $("#" + data.parentFormID + ' input[name='+data.dataInputNm+']').val(val.goodsNo);
                $("#" + data.parentFormID + ' input[name=goodsText]').val(val.goodsNm);
                $("#" + data.parentFormID + ' select[name=goodsKey]').val('og.goodsNm');
            });
        } else {
            $.each(data.info, function (key, val) {
                var addHtml = "";
                addHtml += '<tr id="' + data.dataFormID + '_' + val.goodsNo + '" >';
                addHtml += '<input type="hidden" name="' + data.dataInputNm + '[]" value="' + val.goodsNo + '" />';
                addHtml += val.goodsInfo;
                addHtml += '</tr>';
                $("#" + data.parentFormID).append(addHtml);
            });
        }
    }

    /**
     * 상품 옵션 보기 Ajax layer
     */
    function show_goods_option_stock(goodsNo) {
        var loadChk = $('#layerShowGoodsOptionStock').length;

        var parameters = {
            'optionRegister' : 'y',
            'layerFormID' : 'layerShowGoodsOptionStock',
            'goodsNo' : goodsNo
        };
        $.get('../share/ncds/layer_goods_option_stock.php', parameters, function (data) {
            if (loadChk == 0) {
                data = '<div id="layerShowGoodsOptionStock">' + data + '</div>';
            }
            var layerForm = data;
            ncds_layer_popup({message: layerForm, title: '옵션 상세보기', size: 'wide'});
        });
    }


    try {
        const categoryMultiSelectManager = createNcuaMultiSelectManager({ 
            targetGroups: document.querySelectorAll('.ncua-select-group'),
            sizeClass: 'ncua-select--xs' 
        });
        categoryMultiSelectManager.init();
    } catch {
        // ignore
    }

    //-->
</script>
