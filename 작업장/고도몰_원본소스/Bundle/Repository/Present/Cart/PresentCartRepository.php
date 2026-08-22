<?php

namespace Bundle\Repository\Present\Cart;

use Origin\Model\Present\PresentCart;

class PresentCartRepository
{
    /**
     * 선물하기 정보를 es_presentCart 테이블에 일괄 저장
     * 
     * @param array $insertRows 저장할 데이터
     */
    public function insertPresentCartRows(array $insertRows): void
    {
        PresentCart::query()
            ->insert($insertRows);
    }

    /**
     * 선물하기 수령자 정보 조회
     * 
     * SELECT *
     * FROM es_presentCart
     * WHERE cartSno = ?
     *
     * @param int $cartSno 장바구니 항목
     * @return array 선물하기 수령자 정보
     */
    public function findPresentCartReceiverInfoByCartSno(int $cartSno): array
    {
        return PresentCart::query()
            ->where('cartSno', $cartSno)
            ->get()
            ->toArray();
    }
}
