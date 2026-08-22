<?php
/*
 * Copyright (C) 2025 NHN COMMERCE. - All Rights Reserved
 *
 * Unauthorized copying or redistribution of this file in source and binary forms via any medium
 * is strictly prohibited.
 */

namespace Bundle\Component\RegularDelivery\RegularOrderAdmin;

use Util\Order\RegularOrderUtil;
use DTO\RegularDelivery\RegularOrder\RegularOrderSearchCondition;
use Framework\Log\Logger;
use Origin\Enum\RegularDelivery\RegularOrder\RegularOrderStatus;
use Repository\RegularDelivery\RegularOrder\RegularOrderGiftRepository;
use Repository\RegularDelivery\RegularOrder\RegularOrderRepository;
use Repository\RegularDelivery\RegularOrder\RegularOrderAddGoodsRepository;
use Origin\Enum\RegularDelivery\RegularGoods\DeliveryCycle;

/**
 * 정기결제 신청서 어드민 리스트 조회
 */
class RegularOrderAdminListExcel
{
    /**
     * @var RegularOrderRepository
     */
    private $regularOrderRepository;
    /**
     * @var RegularOrderGiftRepository
     */
    private $regularOrderGiftRepository;
    /**
     * @var RegularOrderAddGoodsRepository
     */
    private $regularOrderAddGoodsRepository;
    /**
     * @var Logger
     */
    private $logger;

    public function __construct(
        RegularOrderRepository      $regularOrderRepository,
        RegularOrderGiftRepository  $regularOrderGiftRepository,
        RegularOrderAddGoodsRepository  $regularOrderAddGoodsRepository,
        Logger                      $logger
    )
    {
        $this->regularOrderRepository = $regularOrderRepository;
        $this->regularOrderGiftRepository = $regularOrderGiftRepository;
        $this->regularOrderAddGoodsRepository = $regularOrderAddGoodsRepository;
        $this->logger = $logger;
    }

    /**
     * 정기결제 엑셀 다운로드 폼 구성
     *
     * @param array $conditions
     * @param string $excelRequireRows
     * @param array $excelFieldName
     * @return array
     */
    public function generateExcelForm(array $conditions, string $excelRequireRows, array $excelFieldName): array
    {
        $conditions = new RegularOrderSearchCondition($conditions);
        $excelRequireColumn = explode(STR_DIVISION, $excelRequireRows);

        // 조회
        $regularOrderList = $this->regularOrderRepository->findRegularOrderList($conditions);

        // 추가 조회나 가공 필요한 정보를 구성해서 추가
        $regularOrderExcelList = [];
        foreach ($regularOrderList as $index => $regularOrder) {
            // 사은품 정보
            $giftInfo = $this->regularOrderGiftRepository->findGiftInfoByApplyNo($regularOrder['applyNo']);
            $giftInfoList = [];
            foreach ($giftInfo as $gift) {
                $regularOrderList[$index]['giftConditionTitle'] = $gift['conditionTitle'];
                $giftInfoList[] = $gift['giftNm'] . ' | ' . $gift['giveCnt'] . '개';
            }
            $regularOrderList[$index]['giftInfo'] = implode('<br>', $giftInfoList);

            // 총 상품 금액
            $regularOrderList[$index]['totalGoodsPrice'] = RegularOrderUtil::calculateTotalRegularOrderOriginGoodsPrice($regularOrder);

            // 총 할인 금액
            $regularOrderList[$index]['totalDcPrice'] = RegularOrderUtil::calculateRegularOrderOriginGoodsDiscountPrice($regularOrder);

            // 배송 주기 관련
            if ($regularOrder['deliveryCycleType'] === 'month') {
                $regularOrderList[$index]['deliveryCycle'] = $regularOrder['deliveryCycle'] . '개월';
            } else {
                $regularOrderList[$index]['deliveryCycle'] = $regularOrder['deliveryCycle'] . '주 / ' . DeliveryCycle::DELIVERY_CYCLE_WEEK_DAY[$regularOrder['deliveryCycleDay']];
            }

            // 배송 회차 관련
            if ($regularOrder['maxDeliveryRound'] === 0) {
                $regularOrderList[$index]['maxDeliveryRound'] = '무제한';
            } else {
                $regularOrderList[$index]['maxDeliveryRound'] = $regularOrder['maxDeliveryRound'] . '회차';
            }

            // 배송 예정일(해지 상태면 0000-00-00)
            if (in_array($regularOrder['applyStatus'], RegularOrderStatus::getInactiveStatus())) {
                $regularOrder['deliveryDueDate'] = '0000-00-00';
            }
            $regularOrderList[$index]['deliveryDueDate'] = $regularOrder['deliveryDueDate'].'('.$regularOrder['deliveryRound'].'회차)';

            // 해지일
            $regularOrderList[$index]['inactiveDt'] = $regularOrder['inactiveDt'] ? date('Y-m-d', strtotime($regularOrder['inactiveDt'])) : '';

            // 전체 주소
            $regularOrderList[$index]['shippingAddressTotal'] = $regularOrder['shippingAddress'] .' '.$regularOrder['shippingAddressSub'];

            // 이용상태
            $regularOrderList[$index]['applyStatus'] = RegularOrderStatus::getStatusLabel($regularOrder['applyStatus']);
            
            // 총 상품 금액
            $addGoodsInfo = $this->regularOrderAddGoodsRepository->findRegularOrderAddGoodsByApplyNo($regularOrder['applyNo']);
            if (!empty($addGoodsInfo)) {
                // 총 상품금액에 추가 상품 금액 합산
                foreach ($addGoodsInfo as $addGoods) {
                    $regularOrderList[$index]['totalGoodsPrice'] += $addGoods['regularAddGoodsPrice'] * $addGoods['regularAddGoodsCnt'];
                }
            }
            // 총 신청 금액
            $regularOrderList[$index]['regularOrderTotalPrice'] = $regularOrderList[$index]['totalGoodsPrice'] - $regularOrderList[$index]['totalDcPrice'];

            // 주문 내역을 배열에 추가
            $regularOrderExcelList[] = $regularOrderList[$index];

            // 추가 상품 별도 쿼리 조회
            if (!empty($addGoodsInfo)) {
                // 추가 상품을 주문 내역 바로 다음 인덱스에 추가 하기 위함
                foreach ($addGoodsInfo as $addGoods) {
                    // 기존 regularOrderList 값을 복사 후 goodsNm만 덮어씀
                    $addGoodsRow = $regularOrderList[$index];
                    $addGoodsRow['goodsNm'] = '[추가]' . $addGoods['goodsNm'];
                    $addGoodsRow['goodsNo'] = $addGoods['addGoodsNo'];
                    $addGoodsRow['regularGoodsCnt'] = $addGoods['regularAddGoodsCnt'];
                    $regularOrderExcelList[] = $addGoodsRow;
                }
            }

        }
        $this->logger->channel('regularOrderAdmin')->info('정기결제 엑셀 폼 데이터 : ', $regularOrderExcelList);

        // 엑셀 헤더 설정
        $headers = [];
        $headers[] = '<tr>';
        $headerKeys = [];
        foreach ($excelFieldName as $fieldKey => $fieldName) {
            foreach ($excelRequireColumn as $row) {
                if ($row === $fieldKey) {
                    $headers[] = '<td class="title">' . $fieldName['name'] . '</td>';
                    $headerKeys[] = $fieldKey; // header 순서 저장
                }
            }
        }
        $headers[] = '</tr>';

        // 엑셀 데이터 삽입
        $rows = [];
        foreach ($regularOrderExcelList as $orderList) {
            $rows[] = '<tr>';
            foreach ($headerKeys as $key) { // header 순서에 맞게 값 삽입
                $value = $orderList[$key] ?? ''; // 값이 없으면 빈 문자열
                $rows[] = "<td class=\"xl24\">" . $value . "</td>"; // 엑셀 cell 서식을 문자열로 강제
            }
            $rows[] = '</tr>';
        }
        $this->logger->channel('regularOrderAdmin')->info('정기결제 엑셀 헤더와 맵핑된 데이터 : ', $rows);

        return array_merge($headers, $rows);
    }
}
