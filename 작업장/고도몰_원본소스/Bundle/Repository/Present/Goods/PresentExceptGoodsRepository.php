<?php
/*
 * Copyright (C) 2025 NHN COMMERCE. - All Rights Reserved
 *
 * Unauthorized copying or redistribution of this file in source and binary forms via any medium is strictly prohibited.
 */

namespace Bundle\Repository\Present\Goods;

use Origin\Model\Present\Goods\PresentExceptGoods;
use Repository\Present\Goods\BasePresentGoodsRepository;

class PresentExceptGoodsRepository extends BasePresentGoodsRepository
{
    protected string $model = PresentExceptGoods::class;

    /**
     * 상품 번호로 제외 상품 존재 여부 조회
     *
     * SELECT EXISTS(
     * SELECT 1
     * FROM es_presentExceptGoods
     * WHERE goodsNo = ?
     * ) AS exists_result;
     *
     * @param int $goodsNo
     * @return bool
     */
    public function hasExceptGoodsByGoodsNo(int $goodsNo): bool
    {
        return ($this->model)::query()
            ->where('goodsNo', $goodsNo)
            ->exists();
    }
}
