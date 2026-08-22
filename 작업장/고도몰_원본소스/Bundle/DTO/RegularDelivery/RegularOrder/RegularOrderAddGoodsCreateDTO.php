<?php
/*
 * Copyright (C) 2025 NHN COMMERCE. - All Rights Reserved
 *
 * Unauthorized copying or redistribution of this file in source and binary forms via any medium
 * is strictly prohibited.
 */

namespace Bundle\DTO\RegularDelivery\RegularOrder;

use Origin\DTO\AbstractDTO;

/**
 * @property-read array addGoodsData
 */
class RegularOrderAddGoodsCreateDTO extends AbstractDTO
{
    private $addGoodsData;

    /**
     * @param array $addGoodsInfo
     * @param string $applyNo
     */
    public function __construct(array $addGoodsInfo, string $applyNo)
    {
        $this->addGoodsData = [];
        foreach ($addGoodsInfo as $addGoods) {
            $this->addGoodsData[] = [
                'applyNo' => $applyNo,
                'regularAddGoodsNo' => $addGoods['addGoodsNo'],
                'regularAddGoodsCnt' => $addGoods['addGoodsCnt'],
                'regularAddGoodsPrice' => $addGoods['addGoodsPrice'],
                'addGoodsOptionName' => $addGoods['addGoodsOptionName'],
            ];
        }
    }

    /**
     * @return array
     */
    public function getAddGoodsData(): array
    {
        return $this->addGoodsData;
    }
}
