<?php
/*
 * Copyright (C) 2026 NHN COMMERCE. - All Rights Reserved
 *
 * Unauthorized copying or redistribution of this file in source and binary forms via any medium
 * is strictly prohibited.
 */

namespace Bundle\Repository\Member;

use Origin\Model\Member\MemberOrderGoodsCount;

class MemberOrderGoodsCountRepository
{
    /**
     * 회원의 상품별 누적 구매수량 조회 (ID 기준 구매수량 제한 체크용)
     *
     * SELECT goodsNo, orderCount
     * FROM es_memberOrderGoodsCount
     * WHERE memNo = ?
     * AND goodsNo IN (...)
     *
     * @param int $memNo 회원번호
     * @param array $goodsNoList 상품번호 리스트
     * @return array goodsNo를 키로 하는 누적 구매수량 배열
     */
    public function findOrderCountByGoodsNoList(int $memNo, array $goodsNoList): array
    {
        return MemberOrderGoodsCount::query()
            ->where('memNo', $memNo)
            ->whereIn('goodsNo', $goodsNoList)
            ->pluck('orderCount', 'goodsNo')
            ->toArray();
    }
}
