<?php
/*
 * Copyright (C) 2025 NHN COMMERCE. - All Rights Reserved
 *
 * Unauthorized copying or redistribution of this file in source and binary forms via any medium
 * is strictly prohibited.
 */

namespace Bundle\Repository\Goods;


use Origin\Model\Goods\GoodsOptionText;

class GoodsOptionTextRepository
{
    /**
     * 상품 텍스트 옵션 조회
     *
     * SELECT *
     *  FROM es_goodsOptionText
     *  WHERE sno = ?
     *
     * @param int $optionTextSno
     * @return array
     */
    public function findGoodsOptionTextByOptionTextSno(int $optionTextSno): array
    {
        $optionText = GoodsOptionText::query()
            ->where('sno', $optionTextSno)
            ->first();

        return $optionText ? $optionText->toArray() : [];
    }

    /**
     * 상품번호를 기준으로 필스 텍스트 옵션 조회
     *
     * SELECT es_goodsOptionText.*
     * FROM es_goodsOptionText
     * WHERE goodsNo = ? AND mustFl = 'y';
     *
     * @param int $goodsNo
     * @return array
     */
    public function findMustGoodsOptionText(int $goodsNo): array
    {
        return GoodsOptionText::query()
            ->where('goodsNo', $goodsNo)
            ->where('mustFl', 'y')
            ->get()
            ->toArray();
    }

    /**
     * 텍스트옵션 sno 리스트로 장바구니용 텍스트옵션 정보 조회
     *
     * SELECT sno, goodsNo, optionName, addPrice, mustFl
     * FROM es_goodsOptionText
     * WHERE sno IN (?, ?, ?);
     *
     * @param array $optionTextSnoList 텍스트옵션 sno 배열
     * @return array 텍스트옵션 정보 배열
     */
    public function findOptionTextInfoForCartBySnoList(array $optionTextSnoList): array
    {
        if (empty($optionTextSnoList)) {
            return [];
        }

        return GoodsOptionText::query()
            ->whereIn('sno', $optionTextSnoList)
            ->select(['sno', 'goodsNo', 'optionName', 'addPrice', 'mustFl'])
            ->get()
            ->toArray();
    }
    
    /**
     * 텍스트 옵션 번호 리스트로 텍스트 옵션 가격 정보를 배치 조회
     *
     * SELECT sno, addPrice
     * FROM es_goodsOptionText
     * WHERE sno IN (...)
     *
     * @param array $optionTextSnoList 텍스트 옵션 번호 리스트
     * @return array
     */
    public function findOptionTextPricesBySnoList(array $optionTextSnoList): array
    {
        return GoodsOptionText::query()
            ->whereIn('sno', $optionTextSnoList)
            ->select('sno', 'addPrice')
            ->get()
            ->pluck('addPrice', 'sno')
            ->toArray();
    }
}
