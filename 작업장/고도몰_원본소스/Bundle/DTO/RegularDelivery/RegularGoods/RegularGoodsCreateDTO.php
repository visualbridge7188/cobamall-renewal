<?php
/*
 * Copyright (C) 2025 NHN COMMERCE. - All Rights Reserved
 *
 * Unauthorized copying or redistribution of this file in source and binary forms via any medium
 * is strictly prohibited.
 */

namespace Bundle\DTO\RegularDelivery\RegularGoods;

use Origin\Enum\RegularDelivery\RegularGoods\RegularGoodsAttribute;


class RegularGoodsCreateDTO extends RegularGoodsDTO
{
    /**
     * @var array
     */
    private $regularGoodsCreateData;

    public function __construct(array $regularGoodsData)
    {
        $this->regularGoodsCreateData = [];
        foreach($regularGoodsData as $regularGoods){
            $this->regularGoodsCreateData[] = [
                'goodsNo' => $regularGoods['goodsNo'],
                'applyStatus' => $regularGoods['applyStatus'] ?? RegularGoodsAttribute::ABLED,
                'discountUseFl' => $regularGoods['discountUseFl'],
                'discountType' => $regularGoods['discountType'],
                'discountRate' => $regularGoods['discountRate'],
                'discountPrice' => $regularGoods['discountPrice'] ,
                'regularPrice' => $regularGoods['regularPrice'] ,
                'deliveryType' => $regularGoods['deliveryType'] ?? RegularGoodsAttribute::DELIVERY_ALL,
                'deliveryCycleType' => $regularGoods['deliveryCycleType'] ?? RegularGoodsAttribute::DELIVERY_CYCLE_ALL,
                'deliveryRoundsDisplayType' => $regularGoods['deliveryRoundsDisplayType'] ?? RegularGoodsAttribute::DELIVERY_ROUNDS_DISPLAY_ALL,
                'maxDeliveryRounds' => $regularGoods['maxDeliveryRounds'] ,
                'giftPresentUseFl' => $regularGoods['giftPresentUseFl'],
                'adminMemo' => $regularGoods['adminMemo'],
                'delFl' => $regularGoods['delFl'],
                'regDt' => date("Y-m-d H:i:s"), // 현재 시간 저장
            ];
        }
    }

    /**
     * @return array
     */
    public function getRegularGoodsCreateData(): array
    {
        return $this->regularGoodsCreateData;
    }
}
