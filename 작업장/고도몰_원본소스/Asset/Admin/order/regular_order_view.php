<!-- 주문상태 일괄 변경을 위한 form -->
<form id="frmSearchRegularDelivery" method="post" action="../order/regular_order_ps.php">
    <input type="hidden" name="mode" value="single_status_change"/>
    <input type="hidden" name="applyNo" value="<?= $applyNo ?>"/>
</form>
<!-- //주문상태 일괄 변경을 위한 form -->

<form id="frmOrder" name="frmOrder" action="../order/regular_order_ps.php" method="post">
    <input type="hidden" name="mode" value=""/>
    <input type="hidden" name="applyNo" value="<?= $applyNo ?>"/>
    <input type="hidden" name="adminMemoSno" value="">

    <div class="page-header js-affix">
        <h3><?= end($naviMenu->location); ?></h3>
        </div>
    <div class="panel panel-default">
        <div class="panel-heading">
            <span class="flag flag-16 flag-kr"></span>
            기준몰
            <?= str_repeat('&nbsp', 6); ?>

            신청번호 : <span><?= $applyNo; ?></span>

            <?= str_repeat('&nbsp', 2); ?>

            <?= str_repeat('&nbsp', 6); ?>
            신청일시 : <span><?= gd_date_format('Y년 m월 d일 H시 i분', $data[0]['regDt']); ?></span>
            <?= str_repeat('&nbsp', 6); ?>
            주문유형 : <span>정기주문</span>
        </div>
    </div>
    <div class="table-title">
        <span class="gd-help-manual mgt30">정기결제(배송) 상품정보</span>
    </div>
    <div id="tabOrderStatus" clear>
        <ul class="nav nav-tabs mgb30" role="tablist">
        </ul>
        <div class="tab-content loading">
            <div class="table-action" style="margin-bottom: 0px !important;">
                <div class="pull-right form-inline" style="height: 26px;">
                    <div class="display-inline-block">
                        <button type="button" class="js-layer-register btn btn-sm btn-black" id="orderGridConfigBtn" data-type="order_grid_config" data-order-grid-mode="view_regularOrderDetail" style="margin-bottom: 25px;">조회항목설정</button>
                    </div>
                </div>
            </div>
        </div>
        <div id="inc_order_view" class="table-responsive">
            <table class="table table-rows">
                <thead>
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
                            $headerCells[] = "<th><input type='checkbox' value='y' class='js-checkall' data-target-name='statusCheck'/></th>";
                        } else {
                            $headerCells[] = "<th{$addClass}>{$gridName}</th>";
                        }
                    }

                    echo implode('', $headerCells);
                }
                ?>
                </thead>
                <tbody>

                    <?php
                        $sort = count($data);
                        foreach ($data as $key => $value) : ?>
                        <tr class="text-center">
                            <?php foreach ($orderGridConfigList as $gridKey => $gridName) : ?>

                                <?php if ($gridKey === 'check' && $key === 0) : // 선택 ?>
                                    <td class="center" rowspan="<?=count($data)?>">
                                        <div class="display-block">
                                            <input type="checkbox" name="bundle[statusCheck][<?= $applyNo?>]" value="<?= $applyNo ?>"/>
                                        </div>
                                    </td>
                                <?php endif; ?>

                                <?php if($gridKey === 'no') : // 번호 ?>
                                    <td><?= $sort ?></td>
                                <?php endif; ?>

                                <?php if ($gridKey === 'applyNo' && $key === 0) : // 신청번호 ?>
                                    <td rowspan="<?=count($data)?>">
                                        <p class="mgt5">
                                            <?= $applyNo?>
                                        </p>
                                    </td>
                                <?php endif; ?>

                                <?php if ($gridKey === 'goodsImage') : // 이미지 ?>
                                    <td>
                                        <?php if (isset($value['addGoodsName'])) : // 추가상품이면 ?>
                                            <?= $value['addGoodsImage'] ?>
                                        <?php else : ?>
                                            <?= $value['imageUrl']; ?>
                                        <?php endif; ?>
                                    </td>
                                <?php endif; ?>

                                <?php if ($gridKey === 'regularOrderGoodsNm') : // 신청상품 ?>
                                    <td class="text-left">
                                        <?php if (isset($value['addGoodsName'])) : // 추가상품이면 ?>
                                            <span class="label label-default">추가</span>
                                            <a href="javascript:void();" class="one-line bold mgb5" title="추가상품명"
                                            onclick="addgoods_register_popup('<?= $value['addGoodsNo']; ?>', <?= $isProvider ? 'true' : 'false' ?>);">
                                                <?= $value['addGoodsName'] ?>
                                            </a>
                                            <?php if (!empty($value['addGoodsOptionName'])) : ?>
                                                <br/>
                                                : <?=$value['addGoodsOptionName']?>
                                            <?php endif; ?>
                                        <?php else : ?>
                                            <a href="javascript:void()" class="one-line" title="상품명" onclick="goods_register_popup('<?= $value['goodsNo']; ?>', <?= $isProvider ? 'true' : 'false' ?>);">
                                                <?= $value['regularGoodsNm']; ?>
                                            </a>
                                            <div class="info">
                                            <?php if (!empty($value['optionName'])) : // 옵션 존재?>
                                                <?php foreach ($value['optionName'] as $optionValues) {
                                                    echo '<dl class="dl-horizontal" title="옵션명">';
                                                    echo '<dt>' . $optionValues[0] . ' :</dt>';
                                                    echo '<dd>' . $optionValues[1] . '</dd>';
                                                    echo '</dl>';
                                                } ?>
                                            <?php endif; ?>

                                            <?php if (!empty($value['optionTextInfo'])) {
                                                foreach ($value['optionTextInfo'] as $textInfo) {
                                                    echo '<ul class="list-unstyled" title="텍스트 옵션명">';
                                                    echo '<li>' . $textInfo[0] . ' :</li>';
                                                    echo '<li>' . $textInfo[1] . ' ';
                                                    if ($textInfo[2] > 0) {
                                                        echo '<span>(추가금 ';
                                                        echo gd_currency_display($textInfo[3]);
                                                        echo ')</span>';
                                                    }
                                                    echo '</li>';
                                                    echo '</ul>';
                                                }
                                            } ?>
                                            </div>
                                        <?php endif; ?>
                                    </td>
                                <?php endif; ?>

                                <?php if ($gridKey === 'goodsCnt') : // 수량 ?>
                                    <td>
                                        <?php if (isset($value['addGoodsCnt'])) : // 추가상품이면 ?>
                                            <?= $value['addGoodsCnt']?>
                                        <?php else : ?>
                                            <?= $value['regularGoodsCnt']?>
                                        <?php endif; ?>
                                    </td>
                                <?php endif; ?>

                                <?php if ($gridKey === 'deliveryCycle' && $key === 0) : // 배송 주기 ?>
                                    <td rowspan="<?=count($data)?>">
                                        <?php if ($value['deliveryCycleType'] === 'month') : ?>
                                            <?= $value['deliveryCycle'] ?>개월
                                        <?php else : ?>
                                            <?= $value['deliveryCycle'] ?>주 /
                                            <?php if ($value['deliveryCycleDay'] === 1) : ?>
                                            월요일
                                            <?php elseif ($value['deliveryCycleDay'] === 2) : ?>
                                            화요일
                                            <?php elseif ($value['deliveryCycleDay'] === 3) : ?>
                                            수요일
                                            <?php elseif ($value['deliveryCycleDay'] === 4) : ?>
                                            목요일
                                            <?php elseif ($value['deliveryCycleDay'] === 5) : ?>
                                            금요일
                                            <?php endif;?>
                                        <?php endif;?>
                                    </td>
                                <?php endif; ?>

                                <?php if ($gridKey === 'deliveryRound' && $key === 0) : // 배송 회차 ?>
                                    <td rowspan="<?=count($data)?>">
                                        <?php if ($value['maxDeliveryRound'] === 0) : ?>
                                            무제한
                                        <?php else : ?>
                                            <?= $value['maxDeliveryRound']?>회차
                                        <?php endif; ?>
                                    </td>
                                <?php endif; ?>

                                <?php if ($gridKey === 'deliveryDueDate' && $key === 0) : // 배송 예정일 ?>
                                    <td rowspan="<?=count($data)?>">
                                        <?php if (in_array($value['applyStatus'], $inactiveStatusList) || in_array($value['applyStatus'], $pausedStatusList)) :?>
                                            0000-00-00<br/>
                                        <?php else : ?>
                                            <?=$value['deliveryDueDate']?><br/>
                                        <?php endif; ?>
                                        (<?=$value['deliveryRound']?>회차)
                                        <input type="button" data-apply-no="<?= $applyNo ?>" value="로그" class="btn btn-sm btn-white js-delivery-log"/>
                                    </td>
                                <?php endif; ?>
                                <?php if ($gridKey === 'applyStatus' && $key === 0) : // 이용상태 ?>
                                    <td rowspan="<?=count($data)?>">
                                        <?=$value['applyStatusLabel']?>
                                        <input type="button" data-apply-no="<?= $applyNo ?>" value="로그" class="btn btn-sm btn-white js-status-log"/>
                                    </td>
                                <?php endif; ?>

                                <?php if ($gridKey === 'regularOrderLog' && $key === 0) : // 변경이력 ?>
                                    <td rowspan="<?=count($data)?>">
                                        <input type="button" data-apply-no="<?= $applyNo ?>" value="로그" class="btn btn-sm btn-white js-change-log"/>
                                    </td>
                                <?php endif; ?>

                                <?php if ($gridKey === 'goodsPrice') : // 상품금액 ?>
                                    <td>
                                        <?php if (isset($value['addGoodsPrice'])) : // 추가상품이면 ?>
                                            <?= gd_currency_display($value['addGoodsPrice'] * $value['addGoodsCnt'])?>
                                        <?php else : ?>
                                            <?= gd_currency_display($originOrderPrice)?>
                                        <?php endif; ?>
                                    </td>
                                <?php endif; ?>

                                <?php if ($gridKey === 'totalRegularOrderDcPrice' && $key === 0) : // 총 정기결제 할인금액 ?>
                                    <td rowspan="<?=count($data)?>">
                                        <?= gd_currency_display($totalDcPrice) ?>
                                    </td>
                                <?php endif; ?>

                                <?php if ($gridKey === 'regularOrderPrice') : // 정기결제 금액 ?>
                                    <td>
                                        <?php if (isset($value['addGoodsPrice'])) : // 추가상품이면 ?>
                                            <?= gd_currency_display($value['addGoodsPrice'] * $value['addGoodsCnt'])?>
                                        <?php else : ?>
                                            <?= gd_currency_display($regularOrderPrice )?>
                                        <?php endif; ?>
                                    </td>
                                <?php endif; ?>

                                <?php if ($gridKey === 'totalRegularOrderPrice' && $key === 0) : // 총 정기결제 금액 ?>
                                    <td rowspan="<?=count($data)?>">
                                        <?= gd_currency_display($regularOrderTotalPrice )?>
                                    </td>
                                <?php endif; ?>

                                <?php if ($gridKey === 'brandNm') : // 브랜드 ?>
                                    <td>
                                        <?php if (!isset($value['addGoodsName'])) : // 추가상품이 아닌 경우만?>
                                            <?= $value['brandNm'] ?>
                                        <?php endif; ?>
                                    </td>
                                <?php endif; ?>

                                <?php if ($gridKey === 'scmNm') : // 공급사 ?>
                                    <td>
                                        <?php if (isset($value['addGoodsScmCompanyName'])) : // 추가상품이면 ?>
                                            <?= $value['addGoodsScmCompanyName'] ?>
                                        <?php else : ?>
                                            <?= $value['companyNm'] ?>
                                        <?php endif; ?>
                                    </td>
                                <?php endif; ?>

                                <?php if ($gridKey === 'adminMemo' && $key === 0) : // 관리자 메모 ?>
                                    <td rowspan="<?=count($data)?>" data-apply-no="<?=$applyNo?>" data-reg-date="<?= $data[0]['regDt'] ?>">
                                        <button type="button" class="btn btn-sm btn-<?php if (!empty($memoData)){ echo 'gray'; } else { echo 'white';} ?> js-regular-order-admin-memo" data-apply-no="<?=$applyNo?>" data-memo="<?=$memoData;?>">보기</button>
                                    </td>
                                <?php endif; ?>

                            <?php endforeach; ?>
                        </tr>
                    <?php $sort--; endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
    <div class="table-action">
        <div class="pull-left form-inline">
            <?php if ($selectBoxApplyStatus) : ?>
                <span class="action-title">선택한 신청정보를</span>
                <?= gd_select_box('bundleOrderStatus', 'bundle[orderStatus]', $selectBoxApplyStatus, null, null, '==이용상태==', null, 'form-control js-status-change') ?>
                <button type="button" class="btn btn-red js-order-status-delivery">일괄적용</button>
            <?php endif; ?>
        </div>
        <div class="pull-right form-inline">
            <?php if (!$isProvider && $applyStatus === 'active') : ?>
                <button type="button" class="btn btn-sm btn-black mgr5 change-regular-goods" data-apply-no="<?=$applyNo?>" data-delivery-round="<?=$value['deliveryRound']?>">상품변경</button>
                <button type="button" class="btn btn-sm btn-black mgr5 change-delivery-round" data-apply-no="<?=$applyNo?>">배송주기변경</button>
                <?php if ($canSkipFl) : ?>
                <button type="button" class="btn btn-sm btn-black mgr5 skip-delivery-round" data-apply-no="<?=$applyNo?>">회차 건너뛰기</button>
                <?php endif; ?>
            <?php endif; ?>
        </div>
    </div>

    <!-- 사은품 정보 -->
    <?php
    if (!empty($gift) && $giftEnable['giftFl'] === 'y') {
        ?>
        <table class="table table-cols">
            <colgroup>
                <col class="width-md"/>
                <col class="width-md"/>
                <col/>
                <col class="width-xl"/>
                <col class="width-md"/>
            </colgroup>
            <tbody>
            <tr>
                <th class="text-left" rowspan="<?= count($gift) + 2; ?>">
                    <span class="gd-help-manual">사은품 정보</span>
                    <?php if ($value['applyStatusLabel'] === '이용중' &&  !$isProvider ) : ?>
                        <input type="button" data-apply-no="<?= $applyNo ?>" value="변경" class="btn btn-sm btn-white js-regular-gift-change"/>
                    <?php endif; ?>
                </th>
                <th class="text-center">이미지</th>
                <th class="text-center">사은품명</th>
                <th class="text-center">사은품 지급조건명</th>
                <th class="text-center">수량</th>
                <th class="text-center">비고</th>
            </tr>
            <?php
            $total = 0;
            foreach ($gift as $val) {
                $total += $val['giveCnt'];
                ?>
                <tr class="text-center">
                    <td><?= html_entity_decode($val['imageUrl']); ?></td>
                    <td><?= $val['giftNm']; ?></td>
                    <td><?= $val['conditionTitle']; ?></td>
                    <td><?= number_format($val['giveCnt']); ?></td>
                    <td><?php if ($val['isGiftSoldOut']) :?> 재고없음으로 지급 불가 <?php else: ?> - <?php endif; ?></td>
                </tr>
                <?php
            }
            ?>
            <tr>
                <th colspan="3" class="text-right">합계 수량</th>
                <th class="text-center"><?=number_format($total)?></th>
                <th></th>
            </tr>
            </tbody>
        </table>
        <?php
    }
    ?>
    <!-- 신청정보 및 신청 -->
    <div class="row">
        <?php if (!$isProvider) : ?>
        <div class="col-xs-6">
            <div class="table-title">신청정보</div>
            <div>
                <table class="table table-cols">
                    <colgroup>
                        <col style="width: 150px"/>
                        <col/>
                    </colgroup>
                    <tr>
                        <th>상품 판매금액</th>
                        <td class="text-right">
                            <strong><?= gd_currency_display($originOrderTotalPrice); ?></strong>
                        </td>
                    </tr>
                    <tr>
                        <th>
                            <button type="button" class="btn btn-xs btn-link js-pay-toggle">보기</button>
                            총 할인금액
                        </th>
                        <th class="th">
                            <div class="text-danger">
                                (-) <?= gd_currency_display($totalDcPrice); ?>
                            </div>
                        </th>
                    </tr>
                    <tr class="js-detail-display" style="display:none;">
                        <th></th>
                        <td class="th">
                            <ul class="list-unstyled">
                                <li>
                                    <strong>정기결제 할인</strong>
                                    <span>
                                    <?= gd_currency_display($totalDcPrice); ?>
                                    </span>
                                </li>
                            </ul>
                        </td>
                    </tr>
                    <tr>
                        <th>실 결제금액</th>
                        <td class="text-right">
                            <strong><?= gd_currency_display($originOrderTotalPrice - $totalDcPrice); ?></strong>
                        </td>
                    </tr>
                </table>
            </div>
        </div>

        <div class="col-xs-6">
            <div class="table-title">신청수단</div>
            <div>
                <table class="table table-cols">
                    <colgroup>
                        <col style="width: 150px"/>
                        <col/>
                    </colgroup>
                    <tr>
                        <th>결제방법</th>
                        <td class="text-left">
                            <span>신용카드</span>
                        </td>
                    </tr>
                    <tr>
                        <th>카드사</th>
                        <td class="text-left">
                            <span><?= $cardCompany ?></span>
                        </td>
                    </tr>
                    <tr>
                        <th>정기결제 카드번호</th>
                        <td class="text-left">
                            <span><?= $cardNo ?></span>
                        </td>
                    </tr>
                </table>
            </div>
        </div>
        <?php endif; ?>
    </div>

    <!-- 신청자정보 및 수령자 정보 -->
    <div class="row">
        <div class="col-xs-6">
            <div class="table-title">
                신청자 정보
                <div class="flo-right">
                    <button type="button" class="btn btn-red btn-sm js-orderInfoBtn">정보수정</button>
                    <button type="button" class="btn btn-red-box btn-sm js-orderInfoBtnSave js-orderViewInfoSave display-none" data-submit-mode="modify_applier_info">저장</button>
                </div>
            </div>
            <!-- 신청자정보 기본 폼 -->
            <div id="layoutOrderViewOrderInfo">
                <table class="table table-cols">
                    <colgroup>
                        <col style="width: 150px"/>
                        <col/>
                    </colgroup>
                    <tr>
                        <th>신청자 IP</th>
                        <td class="text-left">
                            <span><?= $applierInfo['applierIp'] ?></span>
                        </td>
                    </tr>
                    <tr>
                        <th>신청자명</th>
                        <td class="text-left">
                            <span class="text-primary"><?= $applierInfo['applierName'] ?></span> /
                            <?php if (!empty($applierInfo['memId'])): ?>
                                <span class="text-primary"> <?= $applierInfo['memId'] ?> </span> / <span class="text-primary"> <?= $applierInfo['groupNm'] ?> </span>
                            <?php else: ?>
                                <span class="text-primary">탈퇴회원</span>
                            <?php endif; ?>
                            <?php if (!$isProvider && !empty($applierInfo['memId'])) { ?>
                                <button type="button" class="btn btn-sm btn-gray js-layer-crm" data-member-no="<?= $applierInfo['memNo'] ?>">CRM 보기</button>
                            <?php } ?>
                        </td>
                    </tr>
                    <tr>
                        <th>전화번호</th>
                        <td class="text-left">
                            <span><?= $applierInfo['applierPhone'] ?></span>
                        </td>
                    </tr>
                    <tr>
                        <th>휴대폰번호</th>
                        <td class="text-left">
                            <span><?= $applierInfo['applierCellPhone'] ?></span>
                            <a class="btn btn-sm btn-gray" onclick="member_sms('<?=gd_isset($applierInfo['memNo'])?>','<?= urlencode($applierInfo['applierName']); ?>','<?= $applierInfo['applierCellPhone']; ?>', '<?=$applierInfo['smsFl']?>')">SMS 보내기</a>
                        </td>
                    </tr>
                    <tr>
                        <th>이메일</th>
                        <td class="text-left">
                            <span><?= $applierInfo['applierEmail'] ?></span>
                        </td>
                    </tr>
                </table>
            </div>
            <!-- 신청자정보 수정 폼 -->
            <div id="layoutOrderViewOrderInfoModify" class="display-none">
                <table class="table table-cols">
                    <colgroup>
                        <col style="width: 150px"/>
                        <col/>
                    </colgroup>
                    <tr>
                        <th>신청자명</th>
                        <td class="text-left">
                            <input type="text" name="info[applierName]" value="<?= $applierInfo['applierName'] ?>" class="form-control width-sm"/>
                        </td>
                    </tr>
                    <tr>
                        <th>전화번호</th>
                        <td class="text-left">
                            <input type="text" name="info[applierPhone]" value="<?= $applierInfo['applierPhone'] ?>" maxlength="20" class="form-control js-number-only width-md"/>
                        </td>
                    </tr>
                    <tr>
                        <th>휴대폰번호</th>
                        <td class="text-left">
                            <input type="text" name="info[applierCellPhone]" value="<?= $applierInfo['applierCellPhone'] ?>" maxlength="20" class="form-control js-number-only width-md"/>
                        </td>
                    </tr>
                    <tr>
                        <th>이메일</th>
                        <td class="text-left">
                            <div class="form-inline">
                                <input type="text" name="info[applierEmail]" value="<?= $applierInfo['applierEmail'] ?>" class="form-control width-md" />
                                <select id="emailDomain" class="form-control" style="width: 120px;">
                                    <?php foreach($emailDomain as $key => $value){ ?>
                                        <option value="<?=$key?>"><?=$value?></option>
                                    <?php } ?>
                                </select>
                            </div>
                        </td>
                    </tr>
                </table>
            </div>
        </div>
        <div class="col-xs-6">
            <div class="table-title">
                수령자 정보
                <?php if (!$isProvider) : ?>
                <div class="flo-right">
                    <button type="button" class="btn btn-red btn-sm js-receiverInfoBtn">정보수정</button>
                    <button type="button" class="btn btn-red-box btn-sm js-receiverInfoBtnSave js-orderViewInfoSave display-none" data-submit-mode="modifyReceiverInfo" data-order-info-sno="<?=$infoSno?>" data-use-safenumber-fl="<?=$data['receiverUseSafeNumberFl'];?>">저장</button>
                </div>
                <?php endif; ?>
            </div>
            <!-- 수령자 정보 기본 폼 -->
            <div id="layoutOrderViewReceiverInfo">
                <table class="table table-cols">
                    <colgroup>
                        <col style="width: 150px"/>
                        <col/>
                    </colgroup>
                    <tr>
                        <th>배송지 확인</th>
                        <td class="text-left">
                            <span><?= $shippingInfo['shippingTitle'] ?></span>
                        </td>
                    </tr>
                    <tr>
                        <th>수령자명</th>
                        <td class="text-left">
                            <span><?= $shippingInfo['shippingName'] ?></span>
                        </td>
                    </tr>
                    <tr>
                        <th>전화번호</th>
                        <td class="text-left">
                            <span><?= $shippingInfo['shippingPhone'] ?></span>
                        </td>
                    </tr>
                    <tr>
                        <th>휴대폰번호</th>
                        <td class="text-left">
                            <span><?= $shippingInfo['shippingCellPhone'] ?></span>
                            <a class="btn btn-sm btn-gray" onclick="member_sms('<?=gd_isset($shippingInfo['memNo'])?>','<?= urlencode($shippingInfo['shippingName']); ?>','<?= $shippingInfo['shippingCellPhone']; ?>', '<?=$shippingInfo['smsFl']?>')">SMS 보내기</a>
                        </td>
                    </tr>
                    <tr>
                        <th>주소</th>
                        <td class="text-left">
                            <span>[<?= $shippingInfo['shippingZonecode']?>]<br/><?= $shippingInfo['shippingAddress']?><br/><?= $shippingInfo['shippingAddressSub']?></span>
                        </td>
                    </tr>
                    <tr>
                        <th>배송 메세지</th>
                        <td class="text-left">
                            <span><?= $shippingInfo['shippingMessage'] ?></span>
                        </td>
                    </tr>
                </table>
            </div>
            <!-- 수령자 정보 수정 폼 -->
            <div id="layoutOrderViewReceiverInfoModify" class="display-none">
                <table class="table table-cols">
                    <colgroup>
                        <col style="width: 150px"/>
                        <col/>
                    </colgroup>
                    <tr>
                        <th>배송지 확인</th>
                        <td class="text-left">
                            <span><?= $shippingInfo['shippingTitle'] ?></span>
                            <a class="btn btn-sm btn-gray change-shipping-address" data-apply-no="<?=$applyNo?>" data-member-no="<?=$shippingInfo['memNo']?>">배송지 목록</a>
                        </td>
                    </tr>
                    <tr>
                        <th>수령자명</th>
                        <td class="text-left">
                            <span><?= $shippingInfo['shippingName'] ?></span>
                        </td>
                    </tr>
                    <tr>
                        <th>전화번호</th>
                        <td class="text-left">
                            <span><?= $shippingInfo['shippingPhone'] ?></span>
                        </td>
                    </tr>
                    <tr>
                        <th>휴대폰번호</th>
                        <td class="text-left">
                            <span><?= $shippingInfo['shippingCellPhone'] ?></span>
                        </td>
                    </tr>
                    <tr>
                        <th>주소</th>
                        <td class="text-left">
                            <span>[<?= $shippingInfo['shippingZonecode']?>]<br/><?= $shippingInfo['shippingAddress']?><br/><?= $shippingInfo['shippingAddressSub']?></span>
                        </td>
                    </tr>
                    <tr>
                        <th>배송 메세지</th>
                        <td class="text-left">
                            <span><?= $shippingInfo['shippingMessage'] ?></span>
                        </td>
                    </tr>
                </table>
            </div>
        </div>
    </div>

    <!-- 요청사항 / 상담메모 -->
    <?php if (!$isProvider) { ?>
        <div class="row">
            <div class="col-xs-12">
                <div class="table-title gd-help-manual">요청사항 / 상담메모</div>
                <div class="pull-left notice-info">
                    요청사항/상담메모의 내용이 수정 또는 삭제된 경우 "저장" 버튼을 클릭해야 적용됩니다.
                </div>
                <table class="table table-rows mgb5">
                    <colgroup>
                        <col class="width-sm" />
                        <col class="width-md" />
                        <col class="width50p" />
                        <col class="width50p" />
                        <col class="width-sm" />
                    </colgroup>
                    <thead>
                    <tr>
                        <th>작성일</th>
                        <th>작성자</th>
                        <th>요청사항</th>
                        <th>상담메모</th>
                        <th>관리</th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php if (!empty($consultMemo)) { ?>
                        <?php foreach ($consultMemo as $memo) { ?>
                            <tr class="text-center">
                                <td class="nowrap"><?=$memo['regDt']?></td>
                                <td class="nowrap"><?=$memo['managerId']?> / <?=$memo['managerNm']?></td>
                                <td class="text-left js-request-memo"><?=$memo['requestMemo']?></td>
                                <td class="text-left js-consult-memo"><?=$memo['consultMemo']?></td>
                                <td class="nowrap">
                                    <button type="button" class="btn btn-sm btn-gray js-consult-modify" data-sno="<?=$memo['sno']?>">수정</button>
                                    <button type="button" class="btn btn-sm btn-gray js-consult-delete" data-sno="<?=$memo['sno']?>">삭제</button>
                                </td>
                            </tr>
                        <?php } ?>
                    <?php } else { ?>
                        <tr>
                            <td colspan="5" class="no-data">
                                등록된 내용이 없습니다.
                            </td>
                        </tr>
                    <?php } ?>
                    </tbody>
                </table>
            </div>
            <div class="col-xs-12">
                <table class="table table-cols">
                    <colgroup>
                        <col class="width-md"/>
                        <col/>
                        <col class="width-md"/>
                        <col/>
                    </colgroup>
                    <tbody>
                    <tr>
                        <th>고객요청사항</th>
                        <td>
                            <textarea maxlength="1000" name="consult[requestMemo]" class="form-control js-maxlength"></textarea>
                        </td>
                        <th>고객상담메모</th>
                        <td>
                            <table class="width100p">
                                <tr>
                                    <td>
                                        <input type="hidden" name="consult[sno]" value="">
                                        <textarea maxlength="1000" name="consult[consultMemo]" class="form-control js-maxlength"></textarea>
                                    </td>
                                    <td class="width3p pdb20 pdl5">
                                        <button type="button" class="btn btn-black btn-sm js-custom-memo-reset mgb5" >초기화</button>
                                        <button type="button" class="btn btn-red btn-sm js-custom-memo-save mgl5 mgt3" data-submit-mode="modify_consult_memo">저장</button>
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <div class="row">
            <div class="col-xs-12">
                <div class="table-title gd-help-manual">관리자메모</div>

                <table class="table table-rows mgb5">
                    <colgroup>
                        <col class="width-md" />
                        <col class="width-md" />
                        <col class="width-sm" />
                        <col class="width-sm" />
                        <col class="width50p" />
                        <col class="width15p" />
                    </colgroup>
                    <thead>
                    <tr>
                        <th>작성일</th>
                        <th>작성자</th>
                        <th>메모 구분</th>
                        <th>신청번호</th>
                        <th>메모 내용</th>
                        <th>관리</th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php if (empty($memoData) === false) : ?>
                        <?php foreach ($memoData as $mKey => $mVal) : ?>
                            <tbody id="adminMemo<?= $mKey; ?>">
                                <tr>
                                    <td class="text-center">
                                        <span>
                                            <?php if ($mVal['modDt']) { echo $mVal['modDt']; } else { echo $mVal['regDt']; } ?></span></td>
                                    <td class="text-center">
                                        <span class="managerId"><?= $mVal['managerId']; ?></span><br/>
                                        <?php if($mVal['managerNm']){?><span class="managerNm">(<?= $mVal['managerNm']; ?>)</span><?php }?>
                                    </td>
                                    <td class="text-center">
                                        <span class="itemNm"><?= $mVal['itemNm']; ?></span>
                                    </td>
                                    <td class="text-center">
                                        <?= $mVal['applyNo']; ?>
                                    </td>
                                    <td>
                                        <span class="content-memo"><?=str_replace('\"','"', str_replace(['\r\n', '\n', chr(10)], '<br>', $mVal['content']));?></span>
                                    </td>
                                    <td class="text-center">
                                        <span class="mod-button" style="padding-bottom: 5px;">
                                           <button type="button" class="btn btn-sm btn-gray js-admin-memo-modify" data-memocd="<?=$mVal['memoCd'];?>" data-manager-sno="<?=$mVal['managerSno'];?>" data-m-sno="<?=$managerSno;?>" data-content="<?=gd_htmlspecialchars($mVal['content']);?>" data-no="<?=$mVal['sno'];?>">수정</button>
                                        </span>
                                        <span class="del-button">
                                            <button type="button" class="btn btn-sm btn-gray js-admin-memo-delete" data-no="<?=$mVal['sno'];?>">삭제</button>
                                        </span>
                                    </td>
                                </tr>
                            </tbody>
                        <?php endforeach; ?>
                    <?php else : ?>
                        <tr>
                            <td colspan="6" class="no-data">
                                등록된 내용이 없습니다.
                            </td>
                        </tr>
                    <?php endif; ?>
                    </tbody>
                </table>
            </div>
            <div class="col-xs-12">
                <table class="table table-cols">
                    <colgroup>
                        <col class="width-sm">
                        <col>
                        <col class="width-sm">
                        <col>
                    </colgroup>
                    <tbody>
                    <tr>
                        <th>메모 유형</th>
                        <td>
                            <label class="radio-inline">
                                <input type="radio" name="memoType" value="order" checked="checked"/>신청번호별
                            </label>
                        </td>
                        <th>메모 구분</th>
                        <td>
                            <?= gd_select_box('orderMemoCd', 'orderMemoCd', $memoCd, null, null, '=메모 구분='); ?>
                        </td>
                    </tr>
                    <tr>
                        <th>메모 내용</th>
                        <td colspan="3">
                            <textarea name="adminMemo" class="form-control" rows="6"><?=$data['adminMemo']?></textarea>
                        </td>
                        <td class="width3p">
                            <button type="button" class="btn btn-black btn-sm js-memo-reset mgb5" >초기화</button>
                            <button type="button" class="btn btn-red btn-sm mgl5 js-admin-memo-save" data-apply-no="<?=$applyNo?>" data-submit-mode="admin_memo_save">저장</button>
                        </td>
                    </tr>
                    </tbody>
                </table>
            </div>
        </div>
    <?php } ?>
</form>
<script>
    $(document).ready(function () {
        // 상태 일괄 변경
        $(document).on('click', '.js-order-status-delivery', function (e) {

            // 클릭된 버튼이 속한 상위 div에서 해당하는 select 박스를 찾아 가져오기
            const actionDiv = this.closest('.table-action');
            const selectElement = actionDiv.querySelector('select[name="bundle[orderStatus]"]');

            // select의 라벨 값 가져오기
            const selectedLabel = selectElement.options[selectElement.selectedIndex].text;

            if (!$('input[name*=\'bundle[statusCheck]\']:checkbox:checked').length) {
                dialog_alert('선택된 정기결제(배송) 신청 내역이 없습니다.');
                return false;
            }

            if (!selectedLabel || selectedLabel === '==이용상태==') {
                dialog_alert('이용 상태를 선택해주세요.');
                return;
            }

            if (selectedLabel === '해지') {
                // confirm 창 표시
                dialog_confirm(`해지 시 이미 결제된 주문 외 해당 상품의 정기배송이 모두 중단됩니다.<br> 선택한 정기결제(배송) 신청 내역을 해지 상태로 변경하시겠습니까?`, function (result) {
                    if (result) {
                        submitForm();
                    }
                });
            } else {
                // confirm 창 표시
                dialog_confirm(`선택한 정기결제(배송) 신청 내역을 ${selectedLabel} 상태로 변경하시겠습니까?`, function (result) {
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

        $('.js-pay-toggle').click(function () {
            var detailRow = $(this).closest('tr').next('.js-detail-display');
            var isVisible = detailRow.is(':visible');

            if (isVisible) {
                // 보이고 있으면 숨기기
                detailRow.hide();
                $(this).removeClass('active');
            } else {
                // 숨겨져 있으면 보이기
                detailRow.show();
                $(this).addClass('active');
            }
        });

        $('.change-delivery-round').click(function () {
            $.get('layer_regular_order.php', {mode : 'change-delivery-round', applyNo : $(this).data('apply-no')}, function (data) {
                layer_popup(data, '배송주기 변경', 'normal');
            });
        });

        $('.change-regular-goods').click(function () {
            const queryString = "?applyNo=" + $(this).data('apply-no') + "&deliveryRound=" + $(this).data('delivery-round');
            window.open('../share/popup_regular_goods_list.php' + queryString, 'change_regular_goods', 'width=1200, height=800, scrollbars=no');
        });

        $('.skip-delivery-round').click(function () {
            const applyNo = $(this).data('apply-no'); // 버튼에서 apply-no 가져오기
            dialog_confirm('정기결제 건너뛰기를 신청하시겠습니까?<br/>건너뛰기는 신청 후 철회가 불가합니다.', function (result) {
                if (result) {
                    $.post('../order/layer_regular_order_ps.php', {mode : 'skip-delivery-round', applyNo: applyNo}, function (data) {
                        if (data.result === 'success') {
                            dialog_alert(data.message, '회차 건너뛰기', setTimeout(function(){
                                location.reload(true);
                            }, 1000));
                        } else {
                            alert(data.message)
                        }
                    });
                }
            }, '회차 건너뛰기')
        });

        // 신청자 정보 - 수정 토글링
        $('.js-orderInfoBtn').click(function(){
            $("#layoutOrderViewOrderInfo, #layoutOrderViewOrderInfoModify, .js-orderInfoBtn, .js-orderInfoBtnSave").toggleClass('display-none');
        });

        // 수령자 정보 - 수정 토글링
        $('.js-receiverInfoBtn').click(function(){
            $("#layoutOrderViewReceiverInfo, #layoutOrderViewReceiverInfoModify, .js-receiverInfoBtn, .js-receiverInfoBtnSave").toggleClass('display-none');
        });

        // 신청자 정보 - 정보수정 저장
        $('.js-orderViewInfoSave').click(function(){
            $("#frmOrder>input[name='mode']").val($(this).data('submit-mode'));
            $("#frmOrder").attr('target', 'ifrmProcess');
            $("#frmOrder").submit();
        });

        // 배송지 목록 변경
        $('.change-shipping-address').click(function () {
            $.get('layer_regular_order.php', {mode : 'change-shipping-address', applyNo : $(this).data('apply-no'), memNo : $(this).data('member-no')}, function (data) {
                layer_popup(data, '배송지 목록', 'wide');
            });
        });

        // 고객요청사항 및 고객상담메모 초기화
        $('.js-custom-memo-reset').click(function () {
            $("textarea[name='consult[requestMemo]'], textarea[name='consult[consultMemo]").val('');
        });

        // 고객 요청사항 및 고객상담메모 저장
        $('.js-custom-memo-save').click(function () {
            if($.trim($("textarea[name='consult[requestMemo]']").val()) === '' && $.trim($("textarea[name='consult[consultMemo]']").val()) === ''){
                dialog_alert('고객요청사항 혹은 고객상담메모를 입력해 주세요.');
                return;
            }
            $("#frmOrder>input[name='mode']").val($(this).data('submit-mode'));
            $("#frmOrder").attr('target', 'ifrmProcess');
            $("#frmOrder").submit();
        });

        // 요청사항/상담메모 수정
        $('.js-consult-modify').click(function(e){
            $('input[name="consult[sno]"]').val($(this).data('sno'));
            $('textarea[name="consult[requestMemo]"]').val($(this).closest('tr').find('.js-request-memo').text());
            $('textarea[name="consult[consultMemo]"]').val($(this).closest('tr').find('.js-consult-memo').text());
        });

        // 요청사항/상담메모 삭제
        $('.js-consult-delete').click(function(e){
            let element = $(this).closest('tr');
            let sno = $(this).data('sno');
            BootstrapDialog.confirm({
                type: BootstrapDialog.TYPE_WARNING,
                title: '요청사항 및 상담메모 삭제',
                message: '선택한 상담메모를 삭제하시겠습니까? 삭제하시면 복구하실 수 없습니다.',
                callback: function (result) {
                    // 확인 버튼 클릭시
                    if (result) {
                        // 다른 폼에 데이터를 추가해서 일괄변경 처리를 한다.
                        $.post('../order/regular_order_ps.php', {
                            mode: 'delete_consult_memo',
                            sno: sno
                        }, function (data) {
                            if (data.result === 'success') {
                                alert(data.message);
                                element.remove();
                            }
                        });
                    }
                }
            });
        });

        // 베송 예정일 로그
        $('.js-delivery-log').click(function () {
            $.get('layer_regular_order.php', {mode : 'regular-delivery-log', applyNo : $(this).data('apply-no')}, function (data) {
                layer_popup(data, '배송회차 로그 보기', 'normal');
            });
        });

        // 이용상태 로그
        $('.js-status-log').click(function () {
            $.get('layer_regular_order.php', {mode : 'regular-status-log', applyNo : $(this).data('apply-no')}, function (data) {
                layer_popup(data, '이용상태 로그 보기', 'wide');
            });
        });

        // 변경이력 로그
        $('.js-change-log').click(function () {
            $.get('layer_regular_order.php', {mode : 'regular-change-log', applyNo : $(this).data('apply-no')}, function (data) {
                layer_popup(data, '변경이력 로그 보기', 'wide');
            });
        });

        // 메모 초기화
        $('.js-memo-reset').click(function () {
            $("input[name='memoType'][value='order']").prop("checked",true);
            $("#orderMemoCd").val($(this).data('memocd')).prop("selected", false);
            $("textarea[name='adminMemo']").val('');
            $('input[name="mode"]').attr('value', '');
            $('input[name="no"]').attr('value','');
        });

        // 메모 저장
        $('.js-admin-memo-save').click(function () {
            let memoContent = $('textarea[name="adminMemo"]').val();
            if (!memoContent || !memoContent.trim()) {
                dialog_alert('관리자 메모를 등록해주세요.');
                return false;
            }

            let adminMemoMode = $(this).data('submit-mode');
            if (adminMemoMode === 'admin_memo_modify') {
                $.post('../order/regular_order_ps.php', {
                    mode: 'admin_memo_modify',
                    adminMemoSno : $('input[name="adminMemoSno"]').val(),
                    orderMemoCd : $("select[name='orderMemoCd']").val(),
                    adminMemo : memoContent
                }, function (data) {
                    if (data.result === 'success') {
                        dialog_alert(data.message, '안내');
                        setTimeout(function () {
                            location.reload();
                        }, 2000);
                    } else {
                        dialog_alert(data.message);
                    }
                });
            } else {
                $.post('../order/regular_order_ps.php', {
                    mode: 'admin_memo_save',
                    applyNo : $(this).data('apply-no'),
                    orderMemoCd : $("select[name='orderMemoCd']").val(),
                    adminMemo : memoContent
                }, function (data) {
                    if (data.result === 'success') {
                        dialog_alert(data.message, '안내');
                        setTimeout(function () {
                            location.reload();
                        }, 2000);
                    } else {
                        dialog_alert(data.message);
                    }
                });
            }

        });

        // 메모 수정
        $('.js-admin-memo-modify').click(function () {
            if (($(this).data('manager-sno') == $(this).data('m-sno')) || ($(this).data('manager-sno') == 0)) {
                let contentStr = $(this).data('content').toString().replace(/\\r\\n/gi, "\n").replace(/\\"/gi,'"');

                // 수정 모드로 변경
                // 저장 버튼의 data-submit-mode도 같이 변경
                $('.js-admin-memo-save').data('submit-mode', 'admin_memo_modify');
                $('input[name="adminMemoSno"]').attr('value',$(this).data('no'));
                $("#orderMemoCd").val($(this).data('memocd')).prop("selected", true);
                $("textarea[name='adminMemo']").val(contentStr);
            } else {
                dialog_alert('메모를 등록한 관리자만 수정가능합니다.');
                return false;
            }
        });

        // 메모 삭제
        $('.js-admin-memo-delete').click(function () {
            if (($(this).data('manager-sno') == $(this).data('m-sno')) || ($(this).data('manager-sno') == 0)) {
                let sno = $(this).data('no');
                dialog_confirm('선택한 관리자메모를 삭제하시겠습니까? 삭제하시면 복구 하실 수 없습니다.', function (result) {
                    if (result) {
                        $.post('../order/regular_order_ps.php', {
                            mode: 'admin_memo_delete',
                            adminMemoSno : sno,
                        }, function (data) {
                            if (data.result === 'success') {
                                dialog_alert(data.message, '안내');
                                setTimeout(function () {
                                    location.reload();
                                }, 2000);
                            } else {
                                dialog_alert(data.message);
                            }
                        });
                    }
                });
            } else {
                dialog_alert('메모를 등록한 관리자만 삭제가능합니다.');
                return false;
            }
        });

        // 사은품 정보 변경
        $('.js-regular-gift-change').click(function () {
            $.get('layer_regular_order.php', {
                mode: 'regular-gift-update-list',
                applyNo: $(this).data('apply-no')
            }, function (data) {
                layer_popup(data, '사은품 정보 변경', 'normal');
            });
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

        select_email_domain('info[applierEmail]');

        function select_email_domain(name,select) {
            if (typeof select === 'undefined') {
                select = 'emailDomain';
            }
            var $email = $(':text[name="' + name + '"]');
            var $emailDomain = $('select[id='+select+']');
            $emailDomain.on('change', function (e) {
                var emailValue = $email.val();
                var indexOf = emailValue.indexOf('@');
                if (indexOf == -1) {
                    if ($emailDomain.val() === 'self') {
                        $email.val(emailValue + '@');
                    } else {
                        $email.val(emailValue + '@' + $emailDomain.val());
                    }
                    $email.trigger('focusout');
                } else {
                    if ($emailDomain.val() === 'self') {
                        $email.val(emailValue.substring(0, indexOf + 1));
                        $email.focus();
                    } else {
                        $email.val(emailValue.substring(0, indexOf + 1) + $emailDomain.val());
                        $email.trigger('focusout');
                    }
                }
            });
        }
    });
</script>

<script type="text/javascript" src="<?= PATH_ADMIN_GD_SHARE ?>script/modal/CoachModalHandler.js"></script>
<script>
const coachModal = new window.GodoCoach.CoachModalHandler({
    coachName: 'REGULAR_ORDER_VIEW',
    imageUrl: '<?= PATH_ADMIN_GD_SHARE ?>img/modal/regular/coach_regular_order_view.png',
    modalTitle: '정기결제(배송) 신청 상세 정보 미리보기'
});
</script>
