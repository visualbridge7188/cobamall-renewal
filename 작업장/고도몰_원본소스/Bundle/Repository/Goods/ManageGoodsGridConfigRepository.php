<?php
/*
 * Copyright (C) 2025 NHN COMMERCE. - All Rights Reserved
 *
 * Unauthorized copying or redistribution of this file in source and binary forms via any medium
 * is strictly prohibited.
 */

namespace Bundle\Repository\Goods;

use Origin\Model\Goods\ManagerGoodsGridConfig;

class ManageGoodsGridConfigRepository
{

    /**
     * 운영자 상품리스트 노출조건 설정 중 goodsGroupApplyMode를 기준으로 goodsGroupData 조회
     *
     * SELECT ggData FROM es_managerGoodsGridConfig WHERE ggApplyMode = ?
     *
     * @param string $goodsGroupApplyMode : 조회 기준이 될 goodsGroupApplyMode
     * @return array
     */
    public function findGoodsGroupDataByGoodsGroupApplyMode(string $goodsGroupApplyMode): array
    {
        $gridConfig = ManagerGoodsGridConfig::query()
            ->select('ggData')
            ->where('ggApplyMode', '=', $goodsGroupApplyMode)
            ->first();

        return $gridConfig ? $gridConfig->toArray() : [];
    }
}
