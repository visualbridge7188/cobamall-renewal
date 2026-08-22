<?php
/*
 * Copyright (C) 2025 NHN COMMERCE. - All Rights Reserved
 *
 * Unauthorized copying or redistribution of this file in source and binary forms via any medium
 * is strictly prohibited.
 */

namespace Bundle\Repository\Cart;

use Origin\Model\Cart\Cart;
use Carbon\Carbon;

class CartRepository
{
    /**
     * 특정 장바구니 항목에 대해 directCart 플래그 변경
     *
     * Update es_cart SET directCart = ? WHERE sno IN (cartSnoList)
     *
     * @param array $cartSnoList 장바구니 항목들
     * @param string $directCartFlag directCart 플래그
     * @return bool 성공 여부
     */
    public function updateDirectCart(array $cartSnoList, string $directCartFlag): bool
    {
        return Cart::whereIn('sno', $cartSnoList)
            ->update([
                'directCart' => $directCartFlag,
                'modDt' => Carbon::now()
            ]);
    }

    /**
     * 선물하기 상품 여부 체크
     *
     * SELECT 1 FROM es_cart WHERE sno = ? AND directCart = 'present'
     *
     * @param int $cartSno 장바구니 항목
     * @return bool 선물하기 상품 여부
     */
    public function existsPresentCartByCartSno(int $cartSno): bool
    {
        return Cart::query()
            ->where('sno', $cartSno)
            ->where('directCart', 'present')
            ->exists();
    }

    public function updateDirectCartBySnoList(array $snoList): void
    {
        Cart::query()
            ->whereIn('sno', $snoList)
            ->update(['directCart' => 'n']);
    }
}
