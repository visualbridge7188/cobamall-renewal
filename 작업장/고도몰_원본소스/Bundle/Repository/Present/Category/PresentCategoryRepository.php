<?php
/*
 * Copyright (C) 2025 NHN COMMERCE. - All Rights Reserved
 *
 * Unauthorized copying or redistribution of this file in source and binary forms via any medium is strictly prohibited.
 */

namespace Bundle\Repository\Present\Category;

use Origin\Model\Category\CategoryGoods;
use Origin\Model\Present\Category\PresentCategory;
use Repository\BaseRepository;

class PresentCategoryRepository extends BaseRepository
{
    protected string $model = PresentCategory::class;

    /**
     * SELECT * FROM es_presentCategory
     *
     * @return array
     */
    public function findAll(): array
    {
        return ($this->model)::all()
            ->toArray();
    }

    /**
     * SELECT * FROM es_presentCategory WHERE cateCd = ?
     *
     * @param string $cateCd
     * @return array
     */
    public function findByCateCd(string $cateCd): array
    {
        $presentCategory = ($this->model)::query()
            ->where('cateCd', $cateCd)
            ->first()
            ?->toArray();

        return $presentCategory ?: [];
    }

    /**
     * SELECT cateCd FROM es_categoryGoods WHERE (cateCd LIKE ?% or cateCd LIKE ?% ...)
     *
     * @param array $presentCateCds
     * @return array
     */
    public function findChildCateCdsByParentCateCd(array $presentCateCds): array
    {
        return CategoryGoods::query()
            ->where(function ($q) use ($presentCateCds) {
                foreach ($presentCateCds as $cateCd) {
                    $q->orWhere('cateCd', 'like', $cateCd . '%');
                }
            })
            ->pluck('cateCd')
            ->toArray();
    }

    /**
     * DELETE FROM es_presentCategory WHERE cateCd IN (?, ?, ?, ...)
     *
     * @param array $cateCds
     * @return void
     */
    public function deleteByCateCd(array $cateCds): void
    {
        ($this->model)::query()
            ->whereIn('cateCd', $cateCds)
            ->delete();
    }

    /**
     * DELETE FROM es_presentCategory
     *
     * @return void
     */
    public function deleteAll(): void
    {
        parent::delete();
    }

    /**
     * 해당 상품이 선물하기 카테고리에 속하는지 여부 조회
     *
     * SELECT EXISTS(
     * SELECT 1
     * FROM es_presentCategory
     * JOIN es_goodsLinkCategory ON es_presentCategory.cateCd = es_goodsLinkCategory.cateCd
     * WHERE es_goodsLinkCategory.goodsNo = ?
     * ) AS exists_result;
     *
     * @param int $goodsNo
     * @return bool
     */
    public function hasPresentCategoryByGoodsNo(int $goodsNo): bool
    {
        return ($this->model)::query()
            ->join('es_goodsLinkCategory', 'es_presentCategory.cateCd', '=', 'es_goodsLinkCategory.cateCd')
            ->where('es_goodsLinkCategory.goodsNo', $goodsNo)
            ->exists();
    }
}
