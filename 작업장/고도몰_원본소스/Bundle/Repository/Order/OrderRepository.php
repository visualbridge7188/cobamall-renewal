<?php
/*
 * Copyright (C) 2025 NHN COMMERCE. - All Rights Reserved
 *
 * Unauthorized copying or redistribution of this file in source and binary forms via any medium
 * is strictly prohibited.
 */

namespace Bundle\Repository\Order;

use Origin\Model\Order\Order;

class OrderRepository
{
    /**
     * 주문번호로 총 배송비 조회
     *
     * SELECT totalDeliveryCharge FROM es_order
     *  WHERE orderNo = ?
     *
     * @param string $orderNo
     * @return int
     */
    public function findTotalDeliveryChargeByOrderNo(string $orderNo): int
    {
        return Order::query()
            ->select('totalDeliveryCharge')
            ->where('orderNo', $orderNo)
            ->value('totalDeliveryCharge');
    }
}
