<?php
/*
 * Copyright (C) 2025 NHN COMMERCE. - All Rights Reserved
 *
 * Unauthorized copying or redistribution of this file in source and binary forms via any medium
 * is strictly prohibited.
 */

namespace Bundle\DTO\RegularDelivery\RegularGoods;

use Origin\DTO\AbstractDTO;

/**
 * @property-read array $goodsNoList
 * @property-read int|null $applyNo
 * @property-read array|null $deliveryCycleTypes
 * @property-read array|null $deliveryCycles
 * @property-read array|null $deliveryCycleDays
 * @property-read array|null $maxDeliveryRounds
 */
class RegularGoodsValidateDTO extends AbstractDTO
{
    /** @var array */
    private $regularGoodsValidateData;

    public function __construct(array $data)
    {
        $this->regularGoodsValidateData = [];

        // goodsNo가 배열이든 단일 값이든 일관되게 배열로 처리
        $goodsNos = is_array($data['goodsNo']) ? $data['goodsNo'] : [$data['goodsNo']];

        foreach ($goodsNos as $goodsNo) {
            $this->regularGoodsValidateData[$goodsNo] = [
                'applyNo'            => isset($data['applyNo'][$goodsNo]) ? $data['applyNo'][$goodsNo] : null,
                'deliveryCycleTypes' => isset($data['deliveryCycleTypes'][$goodsNo]) ? $data['deliveryCycleTypes'][$goodsNo] : null,
                'deliveryCycles'     => isset($data['deliveryCycles'][$goodsNo]) ? $data['deliveryCycles'][$goodsNo] : null,
                'deliveryCycleDays'  => isset($data['deliveryCycleDays'][$goodsNo]) ? $data['deliveryCycleDays'][$goodsNo] : null,
                'maxDeliveryRounds'  => isset($data['maxDeliveryRounds'][$goodsNo]) ? $data['maxDeliveryRounds'][$goodsNo] : null,
            ];
        }
    }


    /**
     * @return array
     */
    public function getRegularGoodsValidateData(): array
    {
        return $this->regularGoodsValidateData;
    }
}
