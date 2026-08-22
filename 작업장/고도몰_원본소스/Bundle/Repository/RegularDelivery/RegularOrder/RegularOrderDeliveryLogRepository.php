<?php
/*
 * Copyright (C) 2025 NHN COMMERCE. - All Rights Reserved
 *
 * Unauthorized copying or redistribution of this file in source and binary forms via any medium
 * is strictly prohibited.
 */

namespace Bundle\Repository\RegularDelivery\RegularOrder;

use DTO\RegularDelivery\RegularOrder\RegularOrderDeliveryLogDTO;
use Origin\Model\RegularDelivery\RegularOrder\RegularOrderDeliveryLog;

class RegularOrderDeliveryLogRepository
{

    /**
     * 배송예정일(회차) 로그 저장
     *
     * INSERT INTO es_regularOrderDeliveryLog
     * (applyNo, deliveryRound, deliveryDate, regDt)
     *  VALUES
     *  (?, ?, ?, ?);
     *
     * @param RegularOrderDeliveryLogDTO $dto
     * @return void
     */
    public function insertRegularOrderDeliveryLog(RegularOrderDeliveryLogDTO $dto)
    {
        RegularOrderDeliveryLog::query()
            ->insert([
                'applyNo' => $dto->getApplyNo(),
                'deliveryRound' => $dto->getDeliveryRound(),
                'deliveryDate' => $dto->getDeliveryDate(),
                'regDt' => $dto->getRegDt()
            ]);
    }


    /**
     * 정기결제 신청서 로그 정보
     *
     * SELECT * FROM es_regularOrderDelivery WHERE applyNo = ? ORDER BY regDt DESC
     *
     * @param int $applyNo
     * @return array
     */
    public function findRegularOrderDeliveryLogByApplyNo(int $applyNo): array
    {
        return RegularOrderDeliveryLog::query()
            ->where('applyNo', $applyNo)
            ->orderBy('regDt', 'desc')
            ->get()
            ->toArray();
    }

    /**
     * 배송회차 정보 조회
     *
     * SELECT *
     *  FROM es_regularOrderDeliveryLog
     *  WHERE orderNo = ?
     *
     * @param string $orderNo
     * @return int
     */
    public function findMaxDeliveryRoundByOrderNo(string $orderNo): int
    {
        return RegularOrderDeliveryLog::query()
            ->where('orderNo', $orderNo)
            ->max('deliveryRound');
    }

    /**
     * 주문번호의 배송 예정일 조회
     *
     * SELECT deliveryDate FROM es_regularOrderDeliveryLog WHERE orderNo = ?
     *
     * @param string $orderNo
     * @return string
     */
    public function findDeliveryDueDateByOrderNo(string $orderNo): string
    {
        $deliveryDate = RegularOrderDeliveryLog::query()
            ->where('orderNo', $orderNo)
            ->value('deliveryDate');

        return $deliveryDate ?? '';
    }

    /**
     * 정기배송 상품 정보 조회
     *
     * SELECT es_regularOrderGoods.*
     * FROM es_regularOrderDeliveryLog
     * JOIN es_regularOrderGoods ON es_regularOrderDeliveryLog.applyNo = es_regularOrderGoods.applyNo
     * JOIN es_order ON es_regularOrderDeliveryLog.orderNo = es_order.orderNo
     * WHERE es_regularOrderDeliveryLog.orderNo = ?
     * AND es_regularOrderDeliveryLog.deliveryRound = ?
     * ORDER BY es_regularOrderGoods.applyNo DESC
     * LIMIT 1
     * 
     * @param string $orderNo
     * @param int $deliveryRound
     * @return array
     */
    public function findRegularOrderGoodsInfoByOrderNo(string $orderNo, int $deliveryRound): array
    {
        $regularOrderGoodsInfo = RegularOrderDeliveryLog::query()
            ->select(
                'es_regularOrderGoods.*'
            )
            ->join('es_regularOrderGoods', 'es_regularOrderGoods.applyNo', '=', 'es_regularOrderDeliveryLog.applyNo')
            ->join('es_order', 'es_order.orderNo', '=', 'es_regularOrderDeliveryLog.orderNo')
            ->where('es_regularOrderDeliveryLog.orderNo', $orderNo)
            ->where('es_regularOrderDeliveryLog.deliveryRound', $deliveryRound)
            ->orderBy('es_regularOrderGoods.applyNo', 'desc')
            ->first();

        if ($regularOrderGoodsInfo) {
            return $regularOrderGoodsInfo->toArray();
        }

        return [];
    }


    /**
     * 정기배송 상품 정보 조회
     *
     * SELECT es_regularOrderGoods.*
     * FROM es_regularOrderDeliveryLog
     * JOIN es_regularOrderGoods ON es_regularOrderDeliveryLog.applyNo = es_regularOrderGoods.applyNo
     * JOIN es_order ON es_regularOrderDeliveryLog.orderNo = es_order.orderNo
     * WHERE es_regularOrderDeliveryLog.orderNo = ?
     * ORDER BY es_regularOrderGoods.applyNo
     * 
     * @param string $orderNo
     * @return array
     */
    public function findRegularOrderGoodsInfoListByOrderNo(string $orderNo): array
    {
        return RegularOrderDeliveryLog::query()
            ->select(
                'es_regularOrderGoods.*'
            )
            ->join('es_regularOrderGoods', 'es_regularOrderGoods.applyNo', '=', 'es_regularOrderDeliveryLog.applyNo')
            ->join('es_order', 'es_order.orderNo', '=', 'es_regularOrderDeliveryLog.orderNo')
            ->where('es_regularOrderDeliveryLog.orderNo', $orderNo)
            ->orderBy('es_regularOrderGoods.applyNo', 'asc')
            ->get()
            ->toArray();
    }

    /**
     * 배송회차 로그의 배송예정일 업데이트
     *
     * UPDATE es_regularOrderDeliveryLog SET deliveryDate = ? WHERE applyNo = ? AND deliveryRound = ?
     *
     * @param string $applyNo
     * @param int $deliveryRound
     * @param string $deliveryDueDate
     * @return void
     */
    public function updateDeliveryDueDateByApplyNo(string $applyNo, int $deliveryRound, string $deliveryDueDate)
    {
        RegularOrderDeliveryLog::query()
            ->where('applyNo', $applyNo)
            ->where('deliveryRound', $deliveryRound)
            ->update(['deliveryDate' => $deliveryDueDate]);
    }

    /**
     * 배송회차 조회
     *
     * SELECT deliveryRound FROM es_regularOrderDeliveryLog WHERE orderNo = ? AND applyNo = ?
     *
     * @param string $orderNo
     * @param string $applyNo
     * @return int
     */
    public function findDeliveryRoundByOrderNoAndApplyNo(string $orderNo, string $applyNo): int
    {
        return RegularOrderDeliveryLog::query()
            ->where('applyNo', $applyNo)
            ->where('orderNo', $orderNo)
            ->value('deliveryRound');
    }

    /**
     * SELECT deliveryDate FROM es_regularOrderDeliveryLog WHERE applyNo = ? AND deliveryRound = ?
     *
     * @param int $applyNo
     * @param int $deliveryRound
     * @return string
     */
    public function findDeliveryDueDateByApplyNoAndDeliveryRound(int $applyNo, int $deliveryRound): string
    {
        return RegularOrderDeliveryLog::query()
            ->where('applyNo', $applyNo)
            ->where('deliveryRound', $deliveryRound)
            ->value('deliveryDate');
    }

    /**
     * 주문번호로 정기배송 신청 번호 리스트 조회
     *
     * SELECT applyNo
     * FROM es_regularOrderDeliveryLog
     * WHERE orderNo = ?
     * 
     * @param string $orderNo
     * @return array
     */
    public function findRegularOrderApplyNoListByOrderNo(string $orderNo): array
    {
        return RegularOrderDeliveryLog::query()
            ->select('applyNo')
            ->where('orderNo', $orderNo)
            ->get()
            ->toArray();
    }


    /**
     * 주문상품번호로 배송 회차가 가장 높은 배송 정보 조회
     * 
     * SELECT es_regularOrderGoodsDeliveryLog.orderGoodsSno, es_regularOrderDeliveryLog.applyNo, es_regularOrderDeliveryLog.deliveryDate, es_regularOrderDeliveryLog.deliveryRound
     * FROM es_regularOrderDeliveryLog
     * JOIN es_regularOrderGoodsDeliveryLog ON es_regularOrderDeliveryLog.applyNo = es_regularOrderGoodsDeliveryLog.applyNo
     * WHERE es_regularOrderGoodsDeliveryLog.orderGoodsSno IN (?) AND es_regularOrderDeliveryLog.orderNo = ?
     * ORDER BY es_regularOrderDeliveryLog.deliveryRound DESC
     * ORDER BY es_regularOrderDeliveryLog.applyNo ASC
     * LIMIT 1
     *
     * @param array $snoList
     * @param string $orderNo
     * @return array
     */
    public function findHighestDeliveryRoundInfoByOrderGoodsSnoList(array $snoList, string $orderNo): array
    {
        $goodsInfo = RegularOrderDeliveryLog::query()
            ->select([
                'es_regularOrderGoodsDeliveryLog.orderGoodsSno', 
                'es_regularOrderDeliveryLog.applyNo', 
                'es_regularOrderDeliveryLog.deliveryDate', 
                'es_regularOrderDeliveryLog.deliveryRound'
            ])
            ->join('es_regularOrderGoodsDeliveryLog', 'es_regularOrderDeliveryLog.applyNo', '=', 'es_regularOrderGoodsDeliveryLog.applyNo')
            ->whereIn('es_regularOrderGoodsDeliveryLog.orderGoodsSno', $snoList)
            ->where('es_regularOrderDeliveryLog.orderNo', $orderNo)
            ->orderBy('es_regularOrderDeliveryLog.deliveryRound', 'desc')
            ->orderBy('es_regularOrderDeliveryLog.applyNo', 'asc')
            ->first();

        if (!empty($goodsInfo)) {
            return $goodsInfo->toArray();
        }

        return [];
    }
}
