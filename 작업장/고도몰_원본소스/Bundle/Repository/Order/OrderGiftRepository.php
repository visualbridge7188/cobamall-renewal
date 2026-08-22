<?php
/*
 * Copyright (C) 2025 NHN COMMERCE. - All Rights Reserved
 *
 * Unauthorized copying or redistribution of this file in source and binary forms via any medium
 * is strictly prohibited.
 */

namespace Bundle\Repository\Order;

use Origin\Model\Order\OrderGift;

class OrderGiftRepository
{
    /**
     * 주문 사은품 정보 조회
     * 
     * SELECT es_gift.giftNm, es_orderGift.giveCnt
     * FROM es_orderGift
     * JOIN es_gift ON es_orderGift.giftNo = es_gift.giftNo
     * WHERE es_orderGift.orderNo = ?
     * GROUP BY es_orderGift.orderNo
     * LIMIT 1
     * 
     * @param string $orderNo
     * @return array
     */
    public function findOrderGiftInfoByOrderNo(string $orderNo): array
    {
        $orderGiftInfo = OrderGift::query()
            ->select('es_gift.giftNm', 'es_orderGift.giveCnt')
            ->join('es_gift', 'es_orderGift.giftNo', '=', 'es_gift.giftNo')
            ->where('es_orderGift.orderNo', $orderNo)
            ->groupBy('es_orderGift.orderNo')
            ->first();

        return $orderGiftInfo ? $orderGiftInfo->toArray() : [];
    }
}
