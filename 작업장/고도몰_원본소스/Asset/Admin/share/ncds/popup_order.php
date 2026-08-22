<link type="text/css" href="<?=gd_set_browser_cache(PATH_ADMIN_GD_SHARE . 'ncds/css/share/popup-order.css')?>" rel="stylesheet"/>
<script defer src="<?=gd_set_browser_cache(PATH_ADMIN_GD_SHARE . 'ncds/script/common/supply-combo-box.js')?>"></script>

<article class="ncua-content popup-order">
    <?php include $layoutOrderSearchForm;// 검색 및 프린트 폼
    use Component\Goods\AddGoodsAdmin;
    use Component\Goods\Goods;
    use Component\Naver\NaverPay;
    use Component\Order\OrderAdmin;

    $order = new OrderAdmin();
    ?>

    <div class="ncua-search-result">
        <div class="ncua-search-result__summary">
            <p class="ncua-search-result__summary-count">검색 <strong><?= number_format(gd_isset($page->recode['total'], 0)); ?></strong>개 / 전체 <strong class="text-danger"><?= number_format(gd_isset($page->recode['amount'], 0)); ?></strong>개</p>
        </div>
        <div class="ncua-search-result__content">
            <div class="ncua-search-result__actions">
                <div class="ncua-search-result__actions-select">
                    <span class="ncua-select ncua-select--xs">
                        <span class="ncua-select__content">
                            <?= gd_select_box('sort', 'sort', $search['sortList'], null, $search['sort'], null, null, 'ncua-select__tag'); ?>
                        </span>
                    </span>
                    <span class="ncua-select ncua-select--xs">
                        <span class="ncua-select__content">
                            <?= gd_select_box('pageNum', 'pageNum', gd_array_change_key_value([10,20,30,40,50,60,70,80,90,100,200,300,500,]), '개 보기', $page->page['list'], null, null, 'ncua-select__tag'); ?>
                        </span>
                    </span>
                </div>
            </div>
            <input type="hidden" name="searchFl" value="y">
            <input type="hidden" name="applyPath" value="<?=gd_php_self()?>">
            <div class="ncua-table ncua-table--horizontal">
                <table>
                    <colgroup>
                        <col width="56px"/>
                        <col width="60px"/>
                        <col width="110px"/>
                        <col width="140px"/>
                        <col/>
                        <col width="65px"/>
                        <col width="130px"/>
                        <col width="80px"/>
                        <col width="140px"/>
                        <col width="130px"/>
                    </colgroup>
                    <thead>
                        <tr>
                            <th><div>선택</div></th>
                            <th><div>번호</div></th>
                            <th><div>주문일시</div></th>
                            <th><div>주문번호</div></th>
                            <th><div>주문상품</div></th>
                            <th><div>수량</div></th>
                            <th><div>판매가</div></th>
                            <th><div>처리상태</div></th>
                            <th><div>송장번호</div></th>
                            <th><div>공급사</div></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $naverPay = new NaverPay();
                        if (empty($data) === false && is_array($data)) {
                            $sortNo = 1; // 번호 설정
                            $goods = new Goods();
                            $addGoods = new AddGoodsAdmin();
                            foreach ($data as $key => $val) {
                                $goodsType = $val['goodsType'];
                                $orderNo = $val['orderNo'];
                                if($goodsType == 'addGoods') {
                                    $addGoodsData = $addGoods->getDataAddGoods($val['goodsNo'])['data'];
                                    $goodsPrice = $val['goodsCnt'] * ($addGoodsData['goodsPrice'] + $val['optionPrice'] + $val['optionTextPrice']); // 상품 주문 금액
                                }
                                else {
                                    $goodsData = $goods->getGoodsInfo($val['goodsNo']);
                                    $goodsPrice = $val['goodsCnt'] * ($goodsData['goodsPrice'] + $val['optionPrice'] + $val['optionTextPrice']); // 상품 주문 금액
                                }

                                $settlePrice = $val['settlePrice'];

                                if ($val['orderChannelFl'] == 'naverpay') {
                                    $checkoutData = json_decode($val['checkoutData'], true);
                                    if ($naverPay->getStatusText($checkoutData)) {
                                        $naverImg = sprintf("<img src='%s' > ", \UserFilePath::adminSkin('gd_share', 'img', 'channel_icon', 'naverpay.gif')->www());
                                        $val['orderStatusStr'] .= '<br>(' . $naverImg . $naverPay->getStatusText($checkoutData) . ')';
                                    }
                                }
                                ?>
                                <tr id="tbl_add_order_<?= $val['sno'] ?>">
                                    <td>
                                        <div>
                                            <?php if ($checkType == 'radio') { ?>
                                                <label class="ncua-radio-field ncua-radio-field--xs">
                                                    <span class="ncua-radio-input ncua-radio-input--xs ncua-radio-field__input">
                                                        <input type="radio" name="orderGoodsSno[]" value="<?= $val['sno']; ?>"/>
                                                    </span>
                                                </label>
                                            <?php } else { ?>
                                                <label class="ncua-checkbox-field ncua-checkbox-field--xs">
                                                    <span class="ncua-checkbox-input ncua-checkbox-input--xs ncua-checkbox-field__input">
                                                        <input type="checkbox" name="orderGoodsSno[]" value="<?= $val['sno']; ?>"/>
                                                    </span>
                                                </label>
                                            <?php } ?>

                                            <input type="hidden" name="goodsType" value="<?=$goodsType?>">
                                        </div>
                                    </td>
                                    <td>
                                        <div><?= $page->idx--; ?></div>
                                    </td>
                                    <td>
                                        <div>
                                            <?= str_replace(' ', '<br>', gd_date_format('Y-m-d', $val['regDt'])); ?>
                                            <span style="display:none" class="itemRegDt"><?= gd_date_format('Y-m-d', $val['regDt']) ?></span>
                                        </div>
                                    </td>
                                    <td>
                                        <div>
                                            <a href="<?php echo URI_ADMIN; ?>order/order_view.php?orderNo=<?= $orderNo; ?>" title="주문번호" target="_blank" class="itemOrderNo"><?= $orderNo; ?></a>
                                            <?php if ($val['orderChannelFl'] == 'naverpay') { ?>
                                                <a href="<?php echo URI_ADMIN; ?>order/order_view.php?orderNo=<?= $orderNo; ?>" title="주문번호" target="_blank" class="itemOrderNo">
                                                    <img src="<?= UserFilePath::adminSkin('gd_share', 'img', 'channel_icon', 'naverpay.gif')->www() ?>"/> <?= $val['apiOrderNo']; ?>
                                                </a>
                                            <?php } else if ($val['orderChannelFl'] == 'payco') { ?>
                                                <img src="<?= UserFilePath::adminSkin('gd_share', 'img', 'channel_icon', 'payco.gif')->www() ?>"/>
                                            <?php } ?>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="order-product-name">
                                            <input type="hidden" name="goodsNo" value="<?= $val['goodsNo'] ?>">
                                            <?php if($val['goodsType'] == 'addGoods') {?>
                                                <span class="label label-default">추가</span>
                                            <?php }?>
                                                <button type="button" class="ncua-btn ncua-btn--md ncua-btn--text has-underline" title="주문 상품명"
                                                onclick="goods_register_popup('<?= $val['goodsNo']; ?>', <?= $isProvider ? 'true' : 'false' ?>);">
                                                <span class="itemGoodsName"><?= gd_html_cut($val['goodsNm'], 46, '..'); ?></span></button>
                                            <?php
                                            // 옵션 처리
                                            if (empty($val['optionInfo']) === false) {
                                                echo '<div class="itemOptionInfo" title="상품 옵션">';
                                                $optionInfo = json_decode(gd_htmlspecialchars_stripslashes($val['optionInfo'], true));
                                                foreach ($optionInfo as $option) {
                                                    $tmpOption[] = $option[0] . ':' . $option[1];
                                                }
                                                echo gd_implode(', ', $tmpOption);
                                                echo '</div>';
                                                unset($tmpOption);
                                            }

                                            // 텍스트 옵션 처리
                                            if (empty($val['optionTextInfo']) === false) {
                                                echo '<div class="itemOptionInfo2" title="텍스트 옵션">';
                                                $optionTextInfo = json_decode(gd_htmlspecialchars_stripslashes($val['optionTextInfo'], true));
                                                foreach ($optionTextInfo as $option) {
                                                    $tmpOption[] = $option[0] . ':' . $option[1];
                                                }
                                                echo gd_implode(', ', $tmpOption);
                                                echo '</div>';
                                                unset($tmpOption);
                                            }
                                            ?>
                                            <span class="itemImage" style="display:none">
                                                <?php if($val['goodsType'] == 'addGoods') {?>
                                                    <?= gd_html_add_goods_image($val['goodsNo'], $val['addImageName'], $val['addImagePath'], $val['imageStorage'], 30, $val['goodsNm']); ?>
                                                <?php } else {?>
                                                    <?= gd_html_goods_image($val['goodsNo'], $val['imageName'], $val['imagePath'], $val['imageStorage'], 30, $val['goodsNm'], '_blank'); ?>
                                                <?php }?>
                                            </span>
                                        </div>
                                    </td>
                                    <td class="itemGoodsCnt">
                                        <div><?= number_format($val['goodsCnt']); ?></div>
                                    </td>
                                    <td class="itemGoodsPrice">
                                        <div><?= gd_currency_display($goodsPrice); ?></div>
                                    </td>
                                    <td class="itemOrderStatus">
                                        <div><?= $order->getOrderStatusAdmin($val['orderStatus']) ?></div>
                                    </td>
                                    <td class="itemInvoiceNo">
                                        <div><?= $val['invoiceNo'] ?></div>
                                    </td>
                                    <td class="itemCompanyNm">
                                        <div><?= $val['companyNm'] ?></div>
                                    </td>

                                </tr>
                                <?php
                            }

                        } else {
                            ?>
                            <tr>
                                <td colspan="10" class="no-data">
                                    <div>검색된 정보가 없습니다.</div>
                                </td>
                            </tr>
                        <?php } ?>
                    </tbody>
                </table>
            </div>
            <div class="ncua-table-bottom"></div>
        </div>
    </div>
    <?php if (empty($data) === false && is_array($data)) { ?>
        <div class="ncua-pagination"><?= $page->getPage(); ?></div>
    <?php } ?>
    <footer class="ncua-popup-order__footer">
        <button type="button" id="goodsChoiceCancel" class="ncua-btn ncua-btn--sm ncua-btn--secondary-gray" onclick="self.close();">취소</button>
        <?php if ($checkCheckboxType) { ?>
            <button type="button" id="orderChoiceConfirm" class="orderChoiceConfirm ncua-btn ncua-btn--sm ncua-btn--secondary">선택 완료</button>
        <?php } else { ?>
            <button type="button" id="orderRadioChoiceConfirm" class="orderRadioChoiceConfirm ncua-btn ncua-btn--sm ncua-btn--primary">선택 완료</button>
        <?php } ?>
    </footer>
</article>
<script type="text/javascript">
    $(document).ready(function () {
        $('.orderRadioChoiceConfirm').bind('click', function () {
            if ($('input[type=radio][name="orderGoodsSno[]"]:checked').length < 1) {
                NCDSAlert({message: '주문을 선택해주세요.', iconType: 'error'});
                return;
            }

            var resultJson = {
                "info": []
            };

            var checkedNo = $('input[type=radio][name="orderGoodsSno[]"]:checked').val();
            var orderNo = $('#tbl_add_order_' + checkedNo).find('.itemOrderNo').text();
            var goodsNo = $('#tbl_add_order_' + checkedNo).find('input[name=goodsNo]').val();
            var goodsImgageSrc = $('#tbl_add_order_' + checkedNo).find('.itemImage img').attr('src');
            var orderStatus = $('#tbl_add_order_' + checkedNo).find('.itemOrderStatus').text();
            var goodsName = $('#tbl_add_order_' + checkedNo).find('.itemGoodsName').text();
            var _arrOptionName = new Array();
            if($('#tbl_add_order_' + checkedNo).find('.itemOptionInfo').length>0) {
                _arrOptionName.push($('#tbl_add_order_' + checkedNo).find('.itemOptionInfo').text());
            }
            if($('#tbl_add_order_' + checkedNo).find('.itemOptionInfo2').length>0) {
                _arrOptionName.push($('#tbl_add_order_' + checkedNo).find('.itemOptionInfo2').text());
            }
            var optionName = _arrOptionName.join('<br>');
            var regDt = $('#tbl_add_order_' + checkedNo).find('.itemRegDt').html();
            var goodsPrice = $('#tbl_add_order_' + checkedNo).find('.itemGoodsPrice').text();
            var goodsType = $('#tbl_add_order_' + checkedNo).find('input[name=goodsType]').val();

            resultJson.info.push({
                "orderGoodsNo": checkedNo,
                "goodsNo": goodsNo,
                "orderNo": orderNo,
                "regDt": regDt,
                "goodsPrice": goodsPrice,
                "goodsImgageSrc": goodsImgageSrc,
                "goodsName": goodsName,
                "optionName": optionName,
                "orderStatus": orderStatus,
                "goodsType": goodsType,
            });
            opener.setAddOrder(resultJson);
            self.close();
        })

        $('input').keydown(function (e) {
            if (e.keyCode == 13) {
                $("#frmSearchBase").submit();
                return false
            }
        });

        $('.pagination li a').click(function () {

            $("input[name='page']").val($(this).data('page'));
            $('.search-goods-btn').click();
        });

    });

    $('select[name=\'pageNum\']').change(function () {
        $('.search-goods-btn').click();
    });

    $('select[name=\'sort\']').change(function () {
        $('.search-goods-btn').click();
    })
</script>
<script type="text/javascript" src="<?= PATH_ADMIN_GD_SHARE ?>script/orderList.js"></script>
<script type="text/javascript">
    let searchDateDatePicker = null;

    datePicker = new ncua.DatePicker(document.querySelector('#datepicker-container'), {
    size: 'xs', 
    buttons: [
                {
                    text: '오늘',
                    period: 0,
                    unit: 'days',
                    isCurrent: false,
                },
                {
                    text: '7일',
                    period: 6,
                    unit: 'days',
                    isCurrent: true,
                },
                {
                    text: '15일',
                    period: 14,
                    unit: 'days',
                    isCurrent: false,
                },
                {
                    text: '1개월',
                    period: 29,
                    unit: 'days',
                    isCurrent: false,
                },
                {
                    text: '3개월',
                    period: 89,
                    unit: 'days',
                    isCurrent: false,
                },
                {
                    text: '1년',
                    period: 364,
                    unit: 'days',
                    isCurrent: false,
                },
    ],
    datePickerOptions: [
        {
        element: 'start-date',
        attrName: 'treatDate[]',
        options: {
            mode: 'single',
            static: true,
            dateFormat: 'Y-m-d',
            clickOpens: true,
            allowInvalidPreload: true,
            allowInput: true,
            locale: 'ko',
        },
        },
        {
        element: 'end-date',
        attrName: 'treatDate[]',
        options: {
            mode: 'single',
            static: true,
            dateFormat: 'Y-m-d',
            clickOpens: true,
            allowInvalidPreload: true,
            allowInput: true,
            locale: 'ko',
        },
        },
    ],
});
datePicker.setDate(["<?php echo $search['treatDate'][0]; ?>", "<?php echo $search['treatDate'][1]; ?>"]);
    
    try {
        /**
         * 공급사 선택 ComboBox 초기화
         */
        const initSupplyComboBoxHandler = () => {
            if (typeof window.initSupplyComboBox === 'undefined') {
                setTimeout(initSupplyComboBoxHandler, 100);
                return;
            }

            // 초기 데이터 준비
            const initData = [];
            <?php if (($search['scmFl'] == 'y') || ($search['scmFl'] == '1') && !empty($search['scmNo'])) { ?>
                <?php foreach ($search['scmNo'] as $k => $v) { ?>
                    initData.push({
                        id: '<?= $v ?>',
                        label: '<?= addslashes($search['scmNoNm'][$k]) ?>'
                    });
                <?php } ?>
            <?php } ?>

            const applySupplyComboBox = window.initSupplyComboBox({
                scmLayerSelector: 'scmLayer',
                dataInputNm: 'scmNo',
                comboBoxLayerId: 'ncua-combo-box-layer',
                radioGroupClass: 'supply-radio-group',
                initData: initData,
                tagIdPrefix: 'info_scm',
                tagHiddenName: 'scmNoNm',
                apiUrl: '../ncds/layer_scm.php',
                supplyButtonNamesTypes: ['1', 'y'],
            });
        }

        initSupplyComboBoxHandler();
    } catch (error) {
        console.error('공급사 ComboBox 초기화 오류:', error);
    }
</script>
<script type="text/javascript">
    const searchForm = document.querySelector('#frmSearchOrder');
    const btnReset = document.querySelector('.js-btn-reset');
    
    const triggerChangeEvent = (element) => {
        if (element) {
            element.dispatchEvent(new Event('change', { bubbles: true }));
        }
    };
    
    if(btnReset && searchForm)  {
        btnReset.addEventListener('click', function (e) {
        e.preventDefault();

        const scmFlAllRadio = searchForm.querySelector('input[name="scmFl"][value="all"]');
        if (scmFlAllRadio) {
            scmFlAllRadio.checked = true;
            triggerChangeEvent(scmFlAllRadio);
        }
        
        const scmLayer = document.getElementById('scmLayer');
        if (scmLayer) {
            const scmTags = scmLayer.querySelectorAll('.ncua-tag');
            scmTags.forEach(tag => tag.remove());
        }
        
        const scmNoInputs = searchForm.querySelectorAll('input[name="scmNo[]"], input[name="scmNoNm[]"]');
        scmNoInputs.forEach(input => input.remove());
        
        window.applySupplyComboBox?.comboBox?.setDisabled(true);
        window.applySupplyComboBox?.comboBox?.clear();
        
        const keySelect = searchForm.querySelector('select[name="key"]');
        if (keySelect && keySelect.options.length > 0) {
            keySelect.selectedIndex = 0;
            triggerChangeEvent(keySelect);
        }
        
        const keywordInput = searchForm.querySelector('input[name="keyword"]');
        if (keywordInput) {
            keywordInput.value = '';
        }
        
        const treatDateFlSelect = searchForm.querySelector('select[name="treatDateFl"]');
        if (treatDateFlSelect && treatDateFlSelect.options.length > 0) {
            treatDateFlSelect.selectedIndex = 0;
            triggerChangeEvent(treatDateFlSelect);
        }
        
        if (datePicker && typeof datePicker.setDate === 'function') {
            datePicker.setDate(['', '']);
        }
        
        searchForm.submit();
    });
    }
</script>
<script type="text/javascript">
    const setSearchForm = () => {
        const form = document.getElementById('frmSearchOrder');
        if (!form) return;

        const sortValue = document.querySelector("select[name='sort']").value;
        const pageNumValue = document.querySelector("select[name='pageNum']").value;

        const sortInput = document.getElementById('sort');
        const pageNumInput = document.getElementById('pageNum');

        if (sortInput) {
            sortInput.value = sortValue;
        }

        if (pageNumInput) {
            pageNumInput.value = pageNumValue;
        }
    };

    $('select[name="sort"]').on('change', setSearchForm);
    $('select[name="pageNum"]').on('change', setSearchForm);
</script>
