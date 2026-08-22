<?php
/*
 * Copyright (C) 2026 NHN COMMERCE. - All Rights Reserved
 *
 * Unauthorized copying or redistribution of this file in source and binary forms via any medium
 * is strictly prohibited.
 */

namespace Bundle\Repository\Order;

use Origin\Model\Order\OrderDeliveryOriginal;

class OrderDeliveryOriginalRepository
{
    /**
     * sno 목록으로 orderDeliveryOriginal 정보를 일괄 조회해 [sno => row] 맵으로 반환 (주문상세 N+1 제거용)
     *
     * @param array $snos orderDeliveryOriginal.sno 목록
     * @return array sno 를 키로 하는 orderDeliveryOriginal row 맵
     */
    public function findMapBySnos(array $snos): array
    {
        if (empty($snos)) {
            return [];
        }

        return OrderDeliveryOriginal::query()
            ->whereIn('sno', $snos)
            ->get()
            ->keyBy('sno')
            ->map(fn ($model) => $model->toArray())
            ->all();
    }
}
