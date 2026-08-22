<?php
/*
 * Copyright (C) 2025 NHN COMMERCE. - All Rights Reserved
 *
 * Unauthorized copying or redistribution of this file in source and binary forms via any medium
 * is strictly prohibited.
 */

namespace Bundle\Repository\Present\Order;

use Origin\Model\Present\Order\PresentReceiverInfo;

class PresentReceiverInfoRepository
{
    /**
     * 주문번호로 기존 레코드 존재 여부 확인
     *
     * SELECT 1
     * FROM es_presentReceiverInfo
     * WHERE orderNo = ?
     * LIMIT 1
     *
     * @param string $orderNo 주문번호
     * @return bool 존재 여부
     */
    public function existsByOrderNo(string $orderNo): bool
    {
        return PresentReceiverInfo::query()
            ->where('orderNo', $orderNo)
            ->exists();
    }

    /**
     * 주문상품번호로 기존 레코드 존재 여부 확인
     * 
     * SELECT 1
     * FROM es_presentReceiverInfo
     * WHERE orderGoodsNo = ?
     * LIMIT 1
     *
     * @param int $orderGoodsNo 주문상품번호
     * @return bool 존재 여부
     */
    public function existsByOrderGoodsNo(int $orderGoodsNo): bool
    {
        return PresentReceiverInfo::query()
            ->where('orderGoodsNo', $orderGoodsNo)
            ->exists();
    }

    /**
     * 확인토큰으로 주문정보 조회
     * 
     * SELECT orderNo, orderGoodsNo
     * FROM es_presentReceiverInfo
     * WHERE confirmToken = ?
     * LIMIT 1
     * 
     * @param string $confirmToken 확인토큰
     * @return array ['orderNo' => string, 'orderGoodsNo' => int] 또는 []
     */
    public function findOrderInfoByConfirmToken(string $confirmToken): array
    {
        $orderInfo = PresentReceiverInfo::query()
            ->where('confirmToken', $confirmToken)
            ->first(['orderNo', 'orderGoodsNo']);

        return $orderInfo ? $orderInfo->toArray() : [];
    }

    /**
     * 주문번호와 주문상품번호로 확인토큰 조회
     * 
     * @param string $orderNo 주문번호
     * @param int $orderGoodsNo 주문상품번호
     * @return string|null 확인토큰 또는 null (값이 없을 경우)
     */
    public function findConfirmTokenByOrderInfo(string $orderNo, int $orderGoodsNo): ?string
    {
        return PresentReceiverInfo::query()
            ->where('orderNo', $orderNo)
            ->where('orderGoodsNo', $orderGoodsNo)
            ->value('confirmToken');
    }

    /**
     * 주문상품번호로 선물 정보 조회
     * 
     * SELECT *
     * FROM es_presentReceiverInfo
     * JOIN es_presentOrderCard ON es_presentOrderCard.orderNo = es_presentReceiverInfo.orderNo
     * JOIN es_presentCard ON es_presentCard.sno = es_presentOrderCard.cardSno
     * WHERE es_presentReceiverInfo.orderNo = ?
     * 
     * @param int $orderGoodsNo 주문상품번호
     * @return array 선물 정보
     */
    public function findPresentInfoByOrderGoodsNo(int $orderGoodsNo): array
    {
        $presentInfo = PresentReceiverInfo::query()
            ->join('es_presentOrderCard', 'es_presentOrderCard.orderNo', '=', 'es_presentReceiverInfo.orderNo')
            ->join('es_presentCard', 'es_presentCard.sno', '=', 'es_presentOrderCard.cardSno')
            ->where('es_presentReceiverInfo.orderGoodsNo', $orderGoodsNo)
            ->first(['es_presentReceiverInfo.*', 'es_presentOrderCard.*', 'es_presentCard.*']);

        return $presentInfo ? $presentInfo->toArray() : [];
    }

    /**
     * 주문번호로 수령자 정보 리스트 조회 (알림용)
     *
     * SELECT es_presentReceiverInfo.*, es_orderGoods.scmNo, es_orderGoods.invoiceNo, es_manageDeliveryCompany.companyName
     * FROM es_presentReceiverInfo
     * LEFT JOIN es_orderGoods ON es_presentReceiverInfo.orderGoodsNo = es_orderGoods.sno
     * LEFT JOIN es_manageDeliveryCompany ON es_orderGoods.invoiceCompanySno = es_manageDeliveryCompany.sno
     * WHERE es_presentReceiverInfo.orderNo = ?
     *
     * @param string $orderNo 주문번호
     * @return array 수령자 정보
     */
    public function findByOrderNoForNotification(string $orderNo): array
    {
        $receiverInfo = PresentReceiverInfo::query()
            ->leftJoin(DB_ORDER_GOODS, DB_PRESENT_RECEIVER_INFO.'.orderGoodsNo', '=', DB_ORDER_GOODS.'.sno')
            ->leftJoin(DB_MANAGE_DELIVERY_COMPANY, DB_ORDER_GOODS.'.invoiceCompanySno', '=', DB_MANAGE_DELIVERY_COMPANY.'.sno')
            ->where(DB_PRESENT_RECEIVER_INFO.'.orderNo', $orderNo)
            ->select(DB_PRESENT_RECEIVER_INFO.'.*', DB_ORDER_GOODS.'.scmNo', DB_ORDER_GOODS.'.invoiceNo', DB_MANAGE_DELIVERY_COMPANY.'.companyName')
            ->first();

        return $receiverInfo ? $receiverInfo->toArray() : [];
    }

    /**
     * 주문번호와 주문상품번호로 수령자 정보 조회
     * 
     * SELECT *
     * FROM es_presentReceiverInfo
     * WHERE orderNo = ? AND orderGoodsNo = ?
     * LIMIT 1
     * 
     * @param string $orderNo 주문번호
     * @param int $orderGoodsNo 주문상품번호
     * @return array 수령자 정보
     */
    public function findReceiverInfoByOrderInfo(string $orderNo, int $orderGoodsNo): array
    {
        $receiverInfo = PresentReceiverInfo::query()
            ->where('orderNo', $orderNo)
            ->where('orderGoodsNo', $orderGoodsNo)
            ->first();

        return $receiverInfo ? $receiverInfo->toArray() : [];
    }

    /**
     * 수령자 정보 업데이트
     * 
     * UPDATE es_presentReceiverInfo
     * SET ...
     * WHERE orderNo = ? AND orderGoodsNo = ?
     * 
     * @param string $orderNo 주문번호
     * @param int $orderGoodsNo 주문상품번호
     * @param array $receiverData 수령자 정보
     * @return array 업데이트된 수령자 정보 배열
     */
    public function updateReceiverInfo(string $orderNo, array $receiverData): array
    {
        $receiverInfo = PresentReceiverInfo::query()
            ->where('orderNo', $orderNo)
            ->first();
        
        if ($receiverInfo) {
            $receiverInfo->update($receiverData);
            $freshReceiverInfo = $receiverInfo->fresh();
            return $freshReceiverInfo ? $freshReceiverInfo->toArray() : [];
        }
        
        return [];
    }

    /**
     * 선물하기 수령자 정보 리스트 조회
     * 
     * SELECT *
     * FROM es_presentReceiverInfo
     * WHERE orderNo = ?
     *
     * @param string $orderNo
     * @return array
     */
    public function findPresentReceiverListByOrderNo(string $orderNo): array
    {
        return PresentReceiverInfo::query()
            ->where('orderNo', $orderNo)
            ->get()
            ->toArray();
    }
}
