<?php
/*
 * Copyright (C) 2025 NHN COMMERCE. - All Rights Reserved
 *
 * Unauthorized copying or redistribution of this file in source and binary forms via any medium
 * is strictly prohibited.
 */

namespace Bundle\Repository\Goods;

use Origin\Model\Goods\GoodsOption;

class GoodsOptionRepository
{
    /**
     * 상품 옵션 조회
     *
     * SELECT
     * es_goodsOption.*, es_goods.optionName, es_goods.stockFl
     * FROM es_goodsOption
     * JOIN es_goods ON es_goods.goodsNo = es_goodsOption.goodsNo
     * WHERE es_goodsOption.sno = ?
     * LIMIT 1;
     *
     * @param int $optionSno
     * @return array
     */
    public function findGoodsOptionByOptionSno(int $optionSno): array
    {
        $goodsOption = GoodsOption::query()
            ->select('es_goodsOption.*', 'es_goods.optionName', 'es_goods.stockFl')
            ->join('es_goods', 'es_goods.goodsNo', '=', 'es_goodsOption.goodsNo')
            ->where('es_goodsOption.sno', $optionSno)
            ->first();

        if ($goodsOption) {
            return $goodsOption->toArray();
        }

        return [];
    }

    /**
     * 상품 번호를 기준으로 상품 옵션 조회
     *
     * SELECT es_goodsOption.*
     * FROM es_goodsOption
     * WHERE es_goodsOption.goodsNo = ?;
     *
     * @param int $goodsNo : 상품 번호
     * @return array
     */
    public function findGoodsOptionByGoodsNo(int $goodsNo): array
    {
        return GoodsOption::query()
            ->select('es_goodsOption.*')
            ->where('es_goodsOption.goodsNo', $goodsNo)
            ->get()
            ->toArray();
    }

    /**
     * 상품 옵션 재고 정보 조회
     *
     * @param int $goodsNo 상품 번호
     * @param array $optionSnoArray 옵션 번호 배열
     * @return array ['stockCnt', 'stockFl', 'soldOutFl', 'optionSellFl']
     */
    public function findStockInfoByGoodsNoAndOptionSnoArray(int $goodsNo, array $optionSnoArray): array
    {
        $results = GoodsOption::query()
            ->select('es_goodsOption.sno', 'es_goodsOption.stockCnt', 'g.stockFl', 'g.soldOutFl', 'es_goodsOption.optionSellFl')
            ->join('es_goods as g', 'es_goodsOption.goodsNo', '=', 'g.goodsNo')
            ->where('es_goodsOption.goodsNo', $goodsNo)
            ->whereIn('es_goodsOption.sno', $optionSnoArray)
            ->get()
            ->keyBy('sno')
            ->toArray();

        return $results ? $results : [];
    }

    /**
     * 옵션 번호 리스트로 옵션 가격 정보를 배치 조회
     *
     * SELECT sno, optionPrice
     * FROM es_goodsOption
     * WHERE sno IN (...)
     *
     * @param array $optionSnoList 옵션 번호 리스트
     * @return array
     */
    public function findOptionPricesBySnoList(array $optionSnoList): array
    {
        return GoodsOption::query()
            ->whereIn('sno', $optionSnoList)
            ->select('sno', 'optionPrice')
            ->get()
            ->pluck('optionPrice', 'sno')
            ->toArray();
    }
}
