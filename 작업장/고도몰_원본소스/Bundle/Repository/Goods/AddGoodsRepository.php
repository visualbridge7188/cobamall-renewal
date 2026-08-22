<?php
/*
 * Copyright (C) 2025 NHN COMMERCE. - All Rights Reserved
 *
 * Unauthorized copying or redistribution of this file in source and binary forms via any medium
 * is strictly prohibited.
 */

namespace Bundle\Repository\Goods;

use Origin\Model\Goods\AddGoods;

class AddGoodsRepository
{
    /**
     *
     * 추가상품 존재 확인
     *
     * SELECT 1
     *  FROM es_addGoods
     *  WHERE addGoodsNo = ?
     *
     * @param int $addGoodsNo
     * @return bool
     */
    public function existsByAddGoodsNo(int $addGoodsNo): bool
    {
        return AddGoods::query()
            ->where('addGoodsNo', $addGoodsNo)
            ->exists();
    }

    /**
     * 추가상품 정보 조회
     *
     * SELECT *
     *  FROM es_addGoods
     *  WHERE addGoodsNo = ?
     *
     * @param int $addGoodsNo
     * @return array
     */
    public function findAddGoodsInfoByAddGoodsNo(int $addGoodsNo): array
    {
        $addGoods = AddGoods::query()
            ->where('addGoodsNo', $addGoodsNo)
            ->first();

        return $addGoods ? $addGoods->toArray() : [];
    }

    /**
     * 추가상품 번호 리스트를 통해 조회
     *
     * SELECT *
     * FROM es_addGoods
     * WHERE addGoodsNo IN (?, ?, ?);
     *
     * @param array $addGoodsNoList
     * @return array
     */
    public function findAddGoodsByAddGoodsNoList(array $addGoodsNoList): array
    {
        return AddGoods::query()
            ->whereIn('addGoodsNo', $addGoodsNoList)
            ->get()
            ->toArray();
    }

    /**
     * 추가상품 번호 리스트로 장바구니용 추가상품 정보 조회
     *
     * SELECT addGoodsNo, goodsNm as addGoodsNm, goodsPrice, optionNm, stockCnt, viewFl, soldOutFl, imageStorage, imagePath, imageNm
     * FROM es_addGoods
     * WHERE addGoodsNo IN (?, ?, ?);
     *
     * @param array $addGoodsNoList 추가상품 번호 배열
     * @return array 추가상품 정보 배열
     */
    public function findAddGoodsInfoForCartByAddGoodsNoList(array $addGoodsNoList): array
    {
        if (empty($addGoodsNoList)) {
            return [];
        }

        return AddGoods::query()
            ->whereIn('addGoodsNo', $addGoodsNoList)
            ->select([
                'addGoodsNo',
                'goodsNm as addGoodsNm',
                'goodsPrice',
                'optionNm',
                'stockCnt',
                'viewFl',
                'soldOutFl',
                'imageStorage',
                'imagePath',
                'imageNm',
                'stockUseFl'
            ])
            ->get()
            ->toArray();
    }

    /**
     * 추가상품 재고 정보 조회
     *
     * @param int $addGoodsNo 추가상품 번호
     * @return array ['stockCnt', 'stockUseFl', 'soldOutFl']
     */
    public function findStockInfoByAddGoodsNo(int $addGoodsNo): array
    {
        $addGoods = AddGoods::query()
            ->where('addGoodsNo', $addGoodsNo)
            ->first(['stockCnt', 'stockUseFl', 'soldOutFl']);

        return $addGoods ? $addGoods->toArray() : [];
    }

}

