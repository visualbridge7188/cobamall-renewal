<div class="page-header js-affix">
    <h3><?php echo end($naviMenu->location); ?></h3>
    <div class="btn-group">
        <input type="button" value="정기결제(배송) 상품 등록" class="btn btn-red-line js-register"/>
    </div>
</div>

<?php include($regularGoodsSearchFrm); ?>

<form id="frmRegularGoodsList" method="get">
    <input type="hidden" name="mode" value="">
    <input type="hidden" name="regularGoodsDetailSearch" value="">
    <div class="table-action" style="margin:0;">
        <div class="pull-left">
            <button type="button" class="btn btn-white js-modify-apply-status">신청 상태 수정</button>
            <button type="button" class="btn btn-white js-delete-regular-goods">선택 삭제</button>
        </div>
        <div class="pull-right">
            <button type="button" class="js-layer-register btn btn-black" data-type="goods_grid_config"
                    data-goods-grid-mode="regular_goods_list">조회항목설정
            </button>
        </div>
    </div>
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
                            $addClass = " style='min-width: 300px !important;' ";
                        }

                        if ($gridKey === 'goodsDisplayFl' || $gridKey === 'goodsSellFl') {
                            $addClass = " style='min-width: 120px !important;' ";
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
                        <?php if ($gridKey === 'check') : ?>
                            <td class="center">
                                <input type="checkbox" name="sno[<?= $regularGoods['sno']; ?>]"
                                       class="regularGoodsCheck"
                                       onclick="updateSelectAll('regularGoodsCheckAll','regularGoodsCheck');"
                                       value="<?= $regularGoods['sno']; ?>"/>
                            </td>
                        <?php endif; ?>
                        <?php if ($gridKey === 'no') : ?>
                            <td class="center number"><?= number_format($page->idx--); ?></td>
                        <?php endif; ?>
                        <?php if ($gridKey === 'goodsNo') : ?>
                            <td class="center number">
                                <?= $regularGoods['goodsNo']; ?>
                            </td>
                        <?php endif; ?>
                        <?php if ($gridKey === 'goodsCd') : ?>
                            <td class="center number">
                                <?= $regularGoods['goodsCd']; ?>
                            </td>
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
                        <?php if ($gridKey === 'goodsPrice') : ?>
                            <td class="center text-nowrap">
                                <div>
                                    <span class="font-num"><?= gd_currency_display($regularGoods['goodsPrice']); ?></span>
                                </div>
                            </td>
                        <?php endif; ?>
                        <?php if ($gridKey === 'regularPrice') : ?>
                            <td class="center text-nowrap">
                                <div>
                                    <span class="font-num"><?= gd_currency_display($regularGoods['regularPrice']); ?></span>
                                </div>
                            </td>
                        <?php endif; ?>
                        <?php if ($gridKey === 'regularDiscountUseFl') : ?>
                            <td class="center text-nowrap">
                                <?php if ($regularGoods['discountUseFl'] === 'y') : ?>
                                    사용함
                                <?php elseif ($regularGoods['discountUseFl'] === 'n') : ?>
                                    사용 안함
                                <?php endif; ?>
                            </td>
                        <?php endif; ?>
                        <?php if ($gridKey === 'scmNo') : ?>
                            <td class="center text-nowrap">
                                <?= $regularGoods['companyNm']; ?>
                            </td>
                        <?php endif; ?>
                        <?php if ($gridKey === 'regularDeliveryType') : ?>
                            <td class="center text-nowrap">
                                <?php if ($regularGoods['deliveryType'] === 'all') : ?>
                                    일반배송, 정기배송
                                <?php elseif ($regularGoods['deliveryType'] === 'regular') : ?>
                                    정기배송
                                <?php endif; ?>
                            </td>
                        <?php endif; ?>
                        <?php if ($gridKey === 'regularDeliveryCycleType') : ?>
                            <td class="center text-nowrap">
                                <?php if ($regularGoods['deliveryCycleType'] === 'all') : ?>
                                    전체
                                <?php elseif ($regularGoods['deliveryCycleType'] === 'month') : ?>
                                    월 단위
                                    <div class="btn btn-white btn-sm"
                                         onclick="layerDeliveryInfoView('<?= $regularGoods['sno']; ?>', '<?= $regularGoods['deliveryCycleType']; ?>')">보기
                                    </div>
                                <?php elseif ($regularGoods['deliveryCycleType'] === 'week') : ?>
                                    주 단위
                                    <div class="btn btn-white btn-sm"
                                         onclick="layerDeliveryInfoView('<?= $regularGoods['sno']; ?>', '<?= $regularGoods['deliveryCycleType']; ?>')">보기
                                    </div>
                                <?php endif; ?>
                            </td>
                        <?php endif; ?>
                        <?php if ($gridKey === 'regularDeliveryRoundsDisplayType') : ?>
                            <td class="center text-nowrap">
                                <?php if ($regularGoods['deliveryRoundsDisplayType'] === 'all') : ?>
                                    전체
                                <?php elseif ($regularGoods['deliveryRoundsDisplayType'] === 'abled') : ?>
                                    <?= $regularGoods['maxDeliveryRounds'] ?>회
                                <?php elseif ($regularGoods['deliveryRoundsDisplayType'] === 'disabled') : ?>
                                    미설정
                                <?php endif; ?>
                            </td>
                        <?php endif; ?>
                        <?php if ($gridKey === 'regularGiftPresentUseFl') : ?>
                            <td class="center text-nowrap">
                                <?php if ($regularGoods['giftPresentUseFl'] === 'y') : ?>
                                    사용함
                                <?php elseif ($regularGoods['giftPresentUseFl'] === 'n') : ?>
                                    사용 안함
                                <?php endif; ?>
                            </td>
                        <?php endif; ?>
                        <?php if ($gridKey === 'applyStatus') : ?>
                            <td class="center text-nowrap">
                                <?php if ($regularGoods['applyStatus'] === 'abled') : ?>
                                    신청 가능
                                <?php elseif ($regularGoods['applyStatus'] === 'disabled') : ?>
                                    신청 중지
                                <?php endif; ?>
                            </td>
                        <?php endif; ?>
                        <?php if ($gridKey === 'regDt') : ?>
                            <td class="center date">
                                <?= gd_date_format('Y-m-d', $regularGoods['regDt']); ?>
                                <?php if ($regularGoods['modDt']) {
                                    echo "<br/>" . gd_date_format('Y-m-d', $regularGoods['modDt']);
                                } ?>
                            </td>
                        <?php endif; ?>
                        <?php if ($gridKey === 'memo') : ?>
                            <!--메모-->
                            <td class="center">
                                <button type="button"
                                        class="js-layer-regular-goods-memo btn btn-sm btn-<?= $regularGoods['adminMemo'] != '' ? 'gray js-html-popover' : 'white' ?>"
                                        style="height: 27px !important;" title="관리자메모" data-placement="left"
                                        data-content="<?= gd_htmlspecialchars(nl2br($regularGoods['adminMemo'])) ?>"
                                        data-regular-goods-memo="<?= $regularGoods['sno'] ?>">보기
                                </button>
                            </td>
                        <?php endif; ?>
                        <?php if ($gridKey === 'btn') : ?>
                            <td class="center padlr10">
                                <a href="./regular_goods_register.php?sno=<?= $regularGoods['sno']; ?>&page=<?= $page->getCurrentPage() ?>"
                                   class="btn btn-white btn-sm">수정</a>
                            </td>
                        <?php endif; ?>
                    <?php endforeach; ?>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </div>
    <div class="table-action" style="margin:0;">
        <div class="pull-left">
            <button type="button" class="btn btn-white js-modify-apply-status">신청 상태 수정</button>
            <button type="button" class="btn btn-white js-delete-regular-goods">선택 삭제</button>
        </div>
        <div class="pull-right">
            <button type="button" class="btn btn-white btn-icon-excel js-excel-download"
                    data-target-form="frmSearchRegularGoods" data-target-list-form="frmRegularGoodsList" data-target-list-sno="sno"
                    data-search-count="<?= $page->recode['total'] ?>" data-total-count="<?= $page->recode['amount'] ?>">엑셀다운로드</button>
        </div>
    </div>
</form>
<br><br>
<div class="text-center"><?= $page->getPage(); ?></div>
<script>

    /**
     * 배송 주기 상세 정보 모달을 보여주는 함수
     *
     * @param sno : 정기결제(배송) 상품의 sno
     * @param deliveryCycleType : 정기결제(배송)상품의 정기 상품 배송 주기
     */
    function layerDeliveryInfoView(sno, deliveryCycleType) {
        let title = '배송주기 보기';

        $.post('../share/layer_regular_goods_delivery_cycle_view.php', {sno : sno, deliveryCycleType : deliveryCycleType}, function (data) {
            let layerForm = data;  // 데이터를 JSON 객체로 변환

            let configure = {
                title: title,
                message: $(layerForm),
                closable: true
            };

            BootstrapDialog.show(configure);
        });
    }

    // 정기결제(배송) 상품리스트 - 그리드 항목 관리자메모보기 버튼
    $('button.js-layer-regular-goods-memo').on({
        'click': function (e) { // 메모보기 클릭 시
            var regularGoodsSno = $(this).attr('data-regular-goods-memo');
            var params = {
                regularGoodsSno: regularGoodsSno
            };
            $.post('layer_regular_goods_list_memo.php', params, function (data) {
                layer_popup(data, '관리자 메모');
            });
        }
    });

    /**
     * 신청 상태 수정
     */
    $('button.js-modify-apply-status').click(function () {

        // 체크된 정기결제(배송) 상품의 갯수
        let chkCnt = $('input[name*="sno"]:checked').length;

        // 체크된 정기결제(배송) 상품이 없을 경우
        if (chkCnt == 0) {
            alert('선택된 상품이 없습니다.');
            return;
        }

        // 신청 상태 변경 모달창 활성화
        let childNm = 'regular_goods_update_form';
        let addParam = {
            mode: 'updateApplyStatus',
            layerTitle: '정기결제(배송) 상품 신청 상태 설정',
            dataForm: $('input[name*="sno"]:checked').map(function () {
                return $(this).val(); // 선택된 input의 값을 배열로 변환
            }).get()
        };
        layer_add_info(childNm, addParam);
    });

    /**
     * 등록
     */
    $('.js-register').click(function () {
        location.href = './regular_goods_register.php';
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
     * 선택 삭제 기능
     */
    $('button.js-delete-regular-goods').click(function () {

        // 체크된 정기결제(배송) 상품의 갯수
        let chkCnt = $('input[name*="sno"]:checked').length;

        // 체크된 정기결제(배송) 상품이 없을 경우
        if (chkCnt == 0) {
            alert('선택된 상품이 없습니다.');
            return;
        }

        // 삭제
        dialog_confirm('선택한 ' + chkCnt + '개 상품을 정말로 삭제하시겠습니까?<br>삭제 시 복원 불가합니다.', function (result) {
            if (result) {
                $('#frmRegularGoodsList input[name=\'mode\']').val('delete_state');
                $('#frmRegularGoodsList').attr('method', 'post');
                $('#frmRegularGoodsList').attr('action', './regular_goods_ps.php');
                $('#frmRegularGoodsList').attr('target', 'ifrmProcess');
                $('#frmRegularGoodsList').submit();
            }
        });

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
