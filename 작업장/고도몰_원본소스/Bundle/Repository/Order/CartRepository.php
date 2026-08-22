<?php
/*
 * Copyright (C) 2025 NHN COMMERCE. - All Rights Reserved
 *
 * Unauthorized copying or redistribution of this file in source and binary forms via any medium
 * is strictly prohibited.
 */

namespace Bundle\Repository\Order;

use Origin\Model\Order\Cart;

class CartRepository
{
    /**
     * 마지막 장바구니 날짜 조회 (modDt 없으면 regDt)
     *
     * @param int $memNo
     * @return string|null
     */
    public function findLastCartDate(int $memNo): ?string
    {
        $cart = Cart::query()
            ->selectRaw('COALESCE(modDt, regDt) as lastDt')
            ->where('memNo', $memNo)
            ->where('directCart', 'n')
            ->orderByRaw('COALESCE(modDt, regDt) DESC, regDt DESC')
            ->first();

        return $cart?->lastDt;
    }
}
