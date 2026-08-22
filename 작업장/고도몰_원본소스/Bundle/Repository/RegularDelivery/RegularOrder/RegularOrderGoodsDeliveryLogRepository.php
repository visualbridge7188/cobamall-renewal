<?php
/*
 * Copyright (C) 2025 NHN COMMERCE. - All Rights Reserved
 *
 * Unauthorized copying or redistribution of this file in source and binary forms via any medium
 * is strictly prohibited.
 */

namespace Bundle\Repository\RegularDelivery\RegularOrder;

use Origin\Model\RegularDelivery\RegularOrder\RegularOrderGoodsDeliveryLog;

class RegularOrderGoodsDeliveryLogRepository
{
    /**
     * 주문상품 번호로 신청번호 리스트 조회
     *
     * SELECT applyNo FROM es_regularOrderGoodsDeliveryLog WHERE orderGoodsSno IN (?)
     *
     * @param array $orderGoodsSno
     * @return array
     */
    public function findApplyNoByOrderGoodsSnoList(array $orderGoodsSno): array
    {
        return RegularOrderGoodsDeliveryLog::query()
            ->whereIn('orderGoodsSno', $orderGoodsSno)
            ->pluck('applyNo')
            ->unique()
            ->toArray();
    }
}
