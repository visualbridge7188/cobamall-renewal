<?php
/*
 * Copyright (C) 2025 NHN COMMERCE. - All Rights Reserved
 *
 * Unauthorized copying or redistribution of this file in source and binary forms via any medium is strictly prohibited.
 */

namespace Bundle\Repository\Present\Goods;

use Origin\Model\Present\Goods\PresentApplyGoods;
use Repository\Present\Goods\BasePresentGoodsRepository;

class PresentApplyGoodsRepository extends BasePresentGoodsRepository
{
    protected string $model = PresentApplyGoods::class;

    /**
     * 상품 번호로 적용 상품 존재 여부 조회
     *
     * SELECT EXISTS(
     * SELECT 1
     * FROM es_presentApplyGoods
     * WHERE goodsNo = ?
     * ) AS exists_result;
     *
     * @param int $goodsNo
     * @return bool
     */
    public function hasApplyGoodsByGoodsNo(int $goodsNo): bool
    {
        return ($this->model)::query()
            ->where('goodsNo', $goodsNo)
            ->exists();
    }
}
