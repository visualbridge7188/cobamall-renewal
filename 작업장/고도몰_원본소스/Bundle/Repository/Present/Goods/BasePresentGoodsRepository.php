<?php
/*
 * Copyright (C) 2025 NHN COMMERCE. - All Rights Reserved
 *
 * Unauthorized copying or redistribution of this file in source and binary forms via any medium is strictly prohibited.
 */

namespace Bundle\Repository\Present\Goods;

use Repository\BaseRepository;
use Repository\Present\Goods\PresentGoodsRepositoryInterface;

class BasePresentGoodsRepository extends BaseRepository implements PresentGoodsRepositoryInterface
{
    protected string $model;
    
    /**
     * @param array $insertData
     * @return void
     */
    public function insert(array $insertData): void
    {
        ($this->model)::query()->insert($insertData);
    }

    /**
     * DELETE FROM ($this->model) WHERE goodsNo IN (?, ?, ...)
     *
     * @param array $goodsNos
     * @return void
     */
    public function deleteByGoodsNo(array $goodsNos): void
    {
        ($this->model)::query()
            ->whereIn('goodsNo', $goodsNos)
            ->delete();
    }

    /**
     * DELETE FROM ($this->model)
     *
     * @return void
     */
    public function deleteAll(): void
    {
        parent::delete();
    }

    /**
     * SELECT goodsNo FROM ($this->model)
     *
     * @return array
     */
    public function findAllGoodsNo(): array
    {
        return ($this->model)::query()
            ->pluck('goodsNo')
            ->toArray();
    }
}
