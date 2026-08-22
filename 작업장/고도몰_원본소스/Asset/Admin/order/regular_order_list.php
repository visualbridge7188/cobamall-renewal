<div class="page-header js-affix">
    <h3><?php echo end($naviMenu->location); ?></h3>
</div>

<?php include $layoutRegularOrderSearchForm;// 검색 폼 ?>

<form id="frmSearchRegularDelivery" action="./regular_order_ps.php" method="post">
    <input type="hidden" name="mode" value="combine_status_change"/>
    <input type="hidden" name="detailSearch" value="<?= $search['detailSearch']; ?>"/>

    <div class="table-action-dropdown">
        <div class="table-action mgt0 mgb0">
            <div class="pull-left form-inline">
                <span class="action-title">선택한 신청정보를</span>
                <?php echo gd_select_box('orderStatusTop', 'changeStatusTop', $selectBoxApplyStatus, null, null, '=이용상태='); ?>
                <button type="button" class="btn btn-white status-update-process">일괄처리</button>
            </div>
        </div>
    </div>

    <div class="table-responsive">
        <table class="table table-rows order-list">
            <thead>
            <tr>
                <?php
                if (!empty($orderGridConfigList)) {
                    $headerCells = [];
                    foreach ($orderGridConfigList as $gridKey => $gridName) {
                        // 주문상세창 열기 옵션 설정
                        if ($gridKey === 'openLinkOption') {
                            continue; // 테이블에서 노출 금지
                        }

                        $addClass = ($gridKey === 'orderGoodsNm') ? " class='orderGoodsNm'" : '';

                        if ($gridKey === 'check') {
                            $headerCells[] = "<th><input type='checkbox' value='y' class='js-checkall' data-target-name='applyNo'/></th>";
                        } else {
                            $headerCells[] = "<th{$addClass}>{$gridName}</th>";
                        }
                    }

                    echo implode('', $headerCells);
                }
                ?>
            </tr>
            </thead>
            <tbody>
                <?php if (!empty($regularOrderList)) : ?>
                    <?php foreach ($regularOrderList as $data) : ?>
                        <tr class="text-center">
                            <?php foreach ($orderGridConfigList as $gridKey => $gridName) : ?>
                                <?php if ($gridKey === 'check') : // 선택 ?>
                                    <td>
                                        <input type="checkbox" name="applyNo[]" value="<?= $data['applyNo']; ?>"/>
                                    </td>
                                <?php endif; ?>
                                <?php if ($gridKey === 'no') : // 번호 ?>
                                    <td class="font-num">
                                        <small><?= $page->idx--; ?></small>
                                    </td>
                                <?php endif; ?>
                                <?php if ($gridKey === 'applyDt') : // 신청일시 ?>
                                    <td class="font-date nowrap">
                                        <?= str_replace(' ', '<br>', gd_date_format('Y-m-d H:i', $data['regDt'])); ?>
                                    </td>
                                <?php endif; ?>
                                <?php if ($gridKey === 'applyNo') : // 신청번호 ?>
                                    <td class="order-no">
                                        <a href="#;" onclick="javascript:open_regular_order_link('<?=$data["applyNo"]?>', '<?=$isProvider?>')" title="신청번호" data-order-no="<?=$data["applyNo"]?>" data-is-provider="<?= $isProvider ? 'true' : 'false' ?>"><?= $data["applyNo"]; ?></a>
                                        <img src="/admin/gd_share/img/icon_grid_open.png" alt="팝업창열기" class="hand mgl5" border="0" onclick="javascript:regular_order_view_popup('<?=$data["applyNo"]?>', '<?=$isProvider?>');">
                                    </td>
                                <?php endif; ?>
                                <?php if ($gridKey === 'applyGroupNo') : // 신청그룹번호 ?>
                                    <td class="order-no">
                                        <?= $data['applyGroupNo'] ?>
                                    </td>
                                <?php endif; ?>
                                <?php if ($gridKey === 'applierName') : // 신청자 ?>
                                    <td class="js-member-info" data-member-no="<?= $data['memNo'] ?>" data-member-name="<?= $data['applierName']; ?>" data-cell-phone="<?= $data['applierCellPhone']; ?>">
                                        <?= $data['applierName']; ?>
                                        <p class="mgb0">
                                            <?php if (!$isProvider) : ?>
                                                <?php if (!empty($data['memId'])) : ?>
                                                    <button type="button" class="btn btn-link font-eng js-layer-crm" data-member-no="<?= $data['memNo'] ?>">(<?= $data['memId']; ?>/<?=$data['groupNm']?>)
                                                <?php else : ?>
                                                    (탈퇴회원)
                                                <?php endif; ?>
                                            <?php else: ?>
                                                (<?= $data['memId']; ?>/<?=$data['groupNm']?>)
                                            <?php endif; ?>
                                        </p>
                                    </td>
                                <?php endif; ?>
                                <?php if ($gridKey === 'applierCellPhone') : // 신청자 휴대폰 ?>
                                    <td>
                                        <?= $data['applierCellPhone'] ?>
                                    </td>
                                <?php endif; ?>
                                <?php if ($gridKey === 'goodsCd') : // 상품코드 ?>
                                    <td>
                                        <?= $data['goodsNo'] ?>
                                    </td>
                                <?php endif; ?>
                                <?php if ($gridKey === 'regularOrderGoodsNm') : // 상품명 ?>
                                    <td class="text-left">
                                        <?php if ($data['addGoodsTypeCount'] > 0 ) : ?>
                                            <?= $data['goodsNm'] ?> 외 <?= $data['addGoodsTypeCount'] ?>건
                                        <?php else : ?>
                                            <?= $data['goodsNm'] ?>
                                        <?php endif; ?>
                                    </td>
                                <?php endif; ?>
                                <?php if ($gridKey === 'goodsCnt') : // 수량 ?>
                                    <td>
                                        <?= $data['regularGoodsCnt'] + $data['addGoodsCount'] ?>
                                    </td>
                                <?php endif; ?>
                                <?php if ($gridKey === 'totalGoodsPrice') : // 총 상품금액(원본 상품 가격 + 원본 상품 옵션 가 + 원본 상품 텍스트옵션 가) * 정기배송 상품 수 ?>
                                    <td>
                                        <?= gd_currency_display($data['totalGoodsPrice'], true, true); ?>
                                    </td>
                                <?php endif; ?>
                                <?php if ($gridKey === 'totalDcPrice') : // 총 할인금액 ?>
                                    <td>
                                        <?= gd_currency_display($data['totalDcPrice'], true, true); ?>
                                    </td>
                                <?php endif; ?>
                                <?php if ($gridKey === 'totalRegularOrderPrice') : // 총 정기결제 금액 ?>
                                    <td>
                                        <?= gd_currency_display($data['regularOrderTotalPrice'], true, true); ?>
                                    </td>
                                <?php endif; ?>
                                <?php if ($gridKey === 'deliveryCycle') : // 배송 주기 ?>
                                    <td>
                                        <?php if ($data['deliveryCycleType'] === 'month') : ?>
                                            <?= $data['deliveryCycle'] ?>개월
                                        <?php elseif ($data['deliveryCycleType'] === 'week') : ?>
                                            <?= $data['deliveryCycle'] ?>주 /
                                            <?php if ($data['deliveryCycleDay'] === 1) : ?>월요일
                                            <?php elseif ($data['deliveryCycleDay'] === 2) : ?>화요일
                                            <?php elseif ($data['deliveryCycleDay'] === 3) : ?>수요일
                                            <?php elseif ($data['deliveryCycleDay'] === 4) : ?>목요일
                                            <?php elseif ($data['deliveryCycleDay'] === 5) : ?>금요일
                                            <?php endif; ?>
                                        <?php endif; ?>
                                    </td>
                                <?php endif; ?>
                                <?php if ($gridKey === 'deliveryRound') : // 배송 회차 ?>
                                    <td>
                                        <?php if ($data['maxDeliveryRound'] === 0) : ?>
                                            무제한
                                        <?php else : ?>
                                            <?= $data['maxDeliveryRound']; ?>회차
                                        <?php endif; ?>
                                    </td>
                                <?php endif; ?>
                                <?php if ($gridKey === 'deliveryDueDate') : // 배송 예정일(회차) ?>
                                    <td>
                                        <?php if (in_array($data['applyStatus'], $inactiveStatusList) || in_array($data['applyStatus'], $pausedStatusList)) :?>
                                            0000-00-00<br/>
                                        <?php else : ?>
                                            <?=$data['deliveryDueDate']?><br/>
                                        <?php endif; ?>
                                       (<?= $data['deliveryRound']; ?>회차)
                                    </td>
                                <?php endif; ?>
                                <?php if ($gridKey === 'applyStatus') : // 이용 상태 ?>
                                    <td>
                                        <?= $totalApplyStatus[$data['applyStatus']]; ?>
                                    </td>
                                <?php endif; ?>
                                <?php if ($gridKey === 'gift') : // 사은품 ?>
                                    <td>
                                        <?php foreach ($data['giftInfoList'] as $gift) : ?>
                                            <?= $gift['conditionTitle']; ?> | <?= $gift['giftNm']; ?> | <?= $gift['giveCnt']; ?> 개 <br/>
                                        <?php endforeach; ?>
                                    </td>
                                <?php endif; ?>
                                <?php if ($gridKey === 'receiverName') : // 수령자 ?>
                                    <td>
                                        <?= $data['shippingName']; ?>
                                    </td>
                                <?php endif; ?>
                                <?php if ($gridKey === 'inactiveDt') : // 해지일 ?>
                                    <td>
                                        <?php if (in_array($data['applyStatus'], $inactiveStatusList)) : ?>
                                            <?= $data['inactiveDt']; ?>
                                        <?php else : ?>
                                        -
                                        <?php endif; ?>
                                    </td>
                                <?php endif; ?>
                                <?php if ($gridKey === 'adminMemo') : // 관리자 메모 ?>
                                    <td class="text-center" data-apply-no="<?= $data['applyNo'] ?>" data-reg-date="<?= $data['regDt'] ?>">
                                        <button type="button" class="btn btn-sm btn-<?php if (!empty($data['adminMemo'])){ echo 'gray'; } else { echo 'white';} ?> js-regular-order-admin-memo" data-apply-no="<?= $data['applyNo']; ?>" data-memo="<?=$data['adminMemo'];?>">보기</button>
                                    </td>
                                <?php endif; ?>
                            <?php endforeach; ?>
                        </tr>
                    <?php endforeach; ?>
                <?php else : ?>
                    <tr>
                        <td colspan="<?=count($orderGridConfigList)?>" class="no-data">
                            검색된 정보가 없습니다.
                        </td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

    <div class="table-action">
        <div class="pull-left form-inline">
            <div class="pull-left form-inline">
                <span class="action-title">선택한 신청정보를</span>
                <?php echo gd_select_box('orderStatusTop', 'changeStatusTop', $selectBoxApplyStatus, null, null, '=이용상태='); ?>
                <button type="button" class="btn btn-white status-update-process">일괄처리</button>
            </div>
        </div>
        <div class="pull-right">
            <button type="button" class="btn btn-white btn-icon-excel js-excel-download" data-target-form="frmSearchOrder" data-search-count="<?=$page->recode['total']?>" data-total-count="<?=$page->recode['amount']?>" data-state-code ="<?=$currentStatusCode?>" data-target-list-form="frmSearchRegularDelivery" data-target-list-sno="applyNo" >엑셀다운로드</button>
        </div>
    </div>
</form>

<div class="text-center"><?= $page->getPage(); ?></div>
<script type="text/javascript" src="<?=PATH_ADMIN_GD_SHARE?>script/orderList.js?ts=<?=time();?>"></script>
<script>
    $('.status-update-process').off('click').on('click', function () {
        // 클릭된 버튼이 속한 상위 div에서 해당하는 select 박스를 찾아 가져오기
        const actionDiv = $(this).closest('.table-action');
        const selectElement = actionDiv.find('select[name="changeStatusTop"]')[0];

        // select의 라벨 값 가져오기
        const selectedLabel = selectElement.options[selectElement.selectedIndex].text;

        // 선택된 체크박스 수 계산
        const checkedCount = $('input[name="applyNo[]"]:checked').length;

        if (checkedCount === 0) {
            dialog_alert('선택된 정기결제(배송) 신청 내역이 없습니다.');
            return;
        }

        if (!selectedLabel || selectedLabel === '=이용상태=') {
            dialog_alert('이용 상태를 선택해주세요.');
            return;
        }

            if (selectedLabel === '해지') {
                // confirm 창 표시
                dialog_confirm(`해지 시 이미 결제된 주문 외 해당 상품의 정기배송이 모두 중단됩니다.<br> 정기결제(배송) ${checkedCount}건을 해지 상태로 변경하시겠습니까?`, function (result) {
                    if (result) {
                        submitForm();
                    }
                }, '안내');
            } else {
                // confirm 창 표시
                dialog_confirm(`정기결제(배송) ${checkedCount}건을 ${selectedLabel} 상태로 변경하시겠습니까?`, function (result) {
                    if (result) {
                        submitForm();
                    }
                }, '안내');
            }

        function submitForm() {
            const form = document.getElementById('frmSearchRegularDelivery');
            const hiddenInput = document.createElement('input');
            hiddenInput.type = 'hidden';
            hiddenInput.name = 'selectedStatus';
            hiddenInput.value = selectElement.value;
            form.appendChild(hiddenInput);

            const formData = new FormData(form);
            $.ajax({
                url: form.action,
                type: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                dataType: 'json',
                success: function(data) {
                    if (data.result === 'success') {
                        dialog_alert('처리가 완료되었습니다.', '정보');
                        setTimeout(function () {
                            location.reload();
                        }, 3000);
                    } else {
                        dialog_alert(data.message, '경고');
                    }
                },
                error: function() {
                    dialog_alert('처리 중 오류가 발생했습니다.');
                }
            });
        }
    });


    // 관리자메모 노출
    $('.js-regular-order-admin-memo').on({
        'click': function(e){
            let applyNo = $(this).closest('td').data('apply-no');
            let regDt = $(this).closest('td').data('reg-date');

            window.open('../order/popup_regular_order_admin_memo.php?popupMode=yes&applyNo=' + applyNo + '&regDt=' +regDt, 'popup_super_admin_memo', 'width=1200,height=850,scrollbars=yes');
            return false;
        },
        'mouseover' :function (e) { // 메모보기 클릭 시
            let memoEmptyFl = $(this).data('memo');
            console.log(memoEmptyFl);
            if (memoEmptyFl) {
                let selectApplyNo = $(this).data('apply-no');
                let top = ($(this).position().top) - 50;  //보기 버튼 top
                let left = ($(this).position().left) - 900; //보기 버튼의 left
                $.each($('.js-regular-order-admin-memo').closest('td'), function (key, val) {
                    if ($(val).data('apply-no') === selectApplyNo) {
                        $.post("../order/layer_regular_order_admin_memo", {applyNo: selectApplyNo}, function (result) {
                            $('.js-regular-order-admin-memo').after('<div class="memo_layer"></div>');
                            $('.memo_layer').html(result);
                            $('.memo_layer').css({
                                "top": top
                                , "left": left
                                , "right": "300px"
                                , "position": "absolute"
                                , "width": "850px"
                                , "overflow": "hidden"
                                , "height": "auto"
                                , "z-index": "999"
                                , "border": "1px solid #cccccc"
                                , "background": "#ffffff"

                            }).show();
                        }, "html");
                    }
                });
            }
        },
        'mouseout'  :function (e) {
            $('.memo_layer').remove();
        }
    });
</script>

<script type="text/javascript" src="<?= PATH_ADMIN_GD_SHARE ?>script/modal/CoachModalHandler.js"></script>
<script>
const coachModal = new window.GodoCoach.CoachModalHandler({
    coachName: 'REGULAR_ORDER_LIST',
    imageUrl: '<?= PATH_ADMIN_GD_SHARE ?>img/modal/regular/coach_regular_order_list.png',
    modalTitle: '정기결제(배송) 신청 리스트 미리보기'
});
</script>
