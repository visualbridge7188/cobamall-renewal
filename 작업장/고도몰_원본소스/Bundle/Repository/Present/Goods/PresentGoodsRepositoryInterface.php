<?php
/*
 * Copyright (C) 2025 NHN COMMERCE. - All Rights Reserved
 *
 * Unauthorized copying or redistribution of this file in source and binary forms via any medium is strictly prohibited.
 */

namespace Bundle\Repository\Present\Goods;

interface PresentGoodsRepositoryInterface
{
    public function insert(array $insertData);
    public function deleteByGoodsNo(array $goodsNos);
    public function findAllGoodsNo();
    public function deleteAll();
}
