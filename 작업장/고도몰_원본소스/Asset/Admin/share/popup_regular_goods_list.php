<div style="padding-top:7.5px; padding-bttom:7.5px; margin: 10px 0 17px 0px;" >
    <span style="font-size: 18px; display: inline; font-weight: bold;">상품변경</span><span style="font-size: 11px; margin-left: 10px;">상품/옵션은 하나만 선택할 수 있습니다.</span>
</div>

<?php include($regularGoodsSearchFrm); ?>

<form id="frmRegularGoodsList" method="get">
    <div class="table-responsive" style="margin:0;">
        <table class="table table-rows">
            <thead>
            <tr>
                <!-- 상품리스트 그리드 항목 시작-->
                <?php
                if (count($regularGoodsGridConfigList) > 0) {
                    foreach ($regularGoodsGridConfigList as $goodInfo) {
                        $addClass = '';
                        $gridKey = $goodInfo['gridKey'];
                        $gridName = $goodInfo['gridName'];

                        if ($gridKey === 'display') continue;

                        if ($gridKey === 'goodsNm') {
                            $addClass = " class='min-width-300'";
                        }

                        if ($gridKey === 'goodsDisplayFl' || $gridKey === 'goodsSellFl') {
                            $addClass = " class='min-width-120'";
                        }

                        if ($gridKey === 'check') {
                            echo "<th><input id='regularGoodsCheckAll' type='checkbox' value='y' class='js-checkall' data-target-name='goodsNo' onclick='toggleCheckboxes(\"regularGoodsCheckAll\",\"regularGoodsCheck\");'/></th>";
                        } else {
                            echo "<th {$addClass}>{$gridName}</th>";
                        }
                    }
                }
                ?>
            </tr>
            </thead>
            <tbody>
            <?php if (empty($regularGoodsList)) : ?>
                <tr>
                    <td class="center" colspan="<?= count($regularGoodsGridConfigList) + 1 ?>">검색된 정보가 없습니다.</td>
                </tr>
            <?php endif; ?>
            <?php foreach ($regularGoodsList as $regularGoods) : ?>
                <tr>
                    <?php foreach ($regularGoodsGridKey as $gridKey) : ?>
                        <?php if ($gridKey === 'no') : ?>
                            <td class="center number"><?= number_format($page->idx--); ?></td>
                        <?php endif; ?>
                        <?php if ($gridKey === 'goodsImage') : ?>
                            <td class="width-2xs center">
                                <?= gd_html_goods_image(
                                    $regularGoods['goodsNo'],
                                    $regularGoods['goodsImageStorage'] === 'obs' ? $regularGoods['imageUrl'] : $regularGoods['imageName'],
                                    $regularGoods['imagePath'],
                                    $regularGoods['imageStorage'],
                                    40,
                                    $regularGoods['goodsNm'],
                                    '_blank'
                                ); ?>
                            </td>
                        <?php endif; ?>
                        <?php if ($gridKey === 'goodsNm') : ?>
                            <td class="center text-nowrap">
                                <a class="text-blue hand"
                                   onclick="goods_register_popup('<?= $regularGoods['goodsNo']; ?>' <?php if (gd_is_provider() === true) {
                                       echo ",'1'";
                                   } ?>);">
                                    <?= $regularGoods['goodsNm']; ?>
                                </a>
                            </td>
                        <?php endif; ?>
                        <?php if ($gridKey === 'regularPrice') : ?>
                            <td class="center text-nowrap">
                                <div>
                                    <span class="font-num"><?= gd_currency_display($regularGoods['regularPrice']); ?></span>
                                </div>
                            </td>
                        <?php endif; ?>
                        <?php if ($gridKey === 'scmNo') : ?>
                            <td class="center text-nowrap">
                                <?= $regularGoods['companyNm']; ?>
                            </td>
                        <?php endif; ?>
                        <?php if ($gridKey === 'stock') : ?>
                            <td class="center text-nowrap">
                                <?= $regularGoods['stockFl'] === 'n' ? '∞' : $regularGoods['totalStock']; ?>
                            </td>
                        <?php endif; ?>
                        <?php if ($gridKey === 'btn') : ?>
                            <td class="center padlr10">
                                <a href="#" class="btn btn-sm btn-gray btn-regular-goods" data-goods-no="<?php echo $regularGoods['goodsNo']; ?>" data-apply-no="<?php echo $search['applyNo']; ?>">선택</a>
                            </td>
                        <?php endif; ?>
                    <?php endforeach; ?>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</form>
<br><br>
<div class="text-center"><?= $page->getPage(); ?></div>
<script>
    // 상품 레이어 옵션 호출
    $('.btn-regular-goods').click(function(e) {
        e.preventDefault();

        //ajax로 정기결제(배송) 유효성 여부 확인
        const goodsNo = $(this).data('goods-no');
        const applyNo = $(this).data('apply-no');

        let params = {
            mode: 'validateRegularDeliveryOption',
            goodsNo: goodsNo,
            applyNo : {[goodsNo] : applyNo},
        };
        $.ajax({
            method: "POST",
            async: false,
            cache: false,
            url: '../goods/regular_goods_ps.php',
            data: params,
            success: function (data) {
                // error 메시지 예외 처리용
                if (data !== '' && data.status === 'error') {
                    dialog_alert(data.message, '경고', { isReload: true });
                } else {
                    let addParam = {
                        "goodsNo": goodsNo,
                        "applyNo": applyNo,
                        "loadPageType" : 'regularOrderGoodsExchange'
                    };
                    layer_add_info('regular_goods_option', addParam);
                }
            },
            error: function (data) {
                dialog_alert(data.message, '경고', { isReload: true });
            }
        });
    });

    /**
     * 정렬
     */
    $('select[name=\'pageSizeNum\']').change(function () {
        $('#frmSearchRegularGoods').submit();
    });

    /**
     * 페이지 사이즈 변경
     */
    $('select[name=\'sort\']').change(function () {
        $('#frmSearchRegularGoods').submit();
    });

    /**
     * 전체 체크박스 선택/해제 기능
     *
     * @param string checkBoxIdUseAll '전체' 값을 가지고 있는 체크박스 Id
     * @param string checkBoxClass '전체'값을 가진 체크박스에 영향을 받는 체크박스들의 class
     */
    function toggleCheckboxes(checkBoxIdUseAll, checkBoxClass) {
        const isChecked = $('#' + checkBoxIdUseAll).prop('checked');
        $('.' + checkBoxClass).prop('checked', isChecked);
    }

    /**
     * 개별 체크박스 선택/해제 시 전체 체크박스 상태 변경
     *
     * @param string checkBoxIdUseAll '전체' 값을 가지고 있는 체크박스 Id
     * @param string checkBoxClass '전체'값을 가진 체크박스에 영향을 받는 체크박스들의 class
     */
    function updateSelectAll(checkBoxIdUseAll, checkBoxClass) {
        const checkboxes = $('.' + checkBoxClass);
        const selectAll = $('#' + checkBoxIdUseAll);

        // 모든 체크박스가 선택되면 '전체 선택' 체크박스를 체크
        selectAll.prop('checked', checkboxes.length === checkboxes.filter(':checked').length);

        // 일부라도 체크가 해제되면 '전체 선택' 체크박스 체크 해제
        selectAll.prop('indeterminate', checkboxes.filter(':checked').length > 0 && checkboxes.filter(':checked').length < checkboxes.length);
    }

</script>
<style>
    .min-width-300 {
        min-width: 300px !important;
    }
    .min-width-120 {
        min-width: 120px !important;
    }
</style>
