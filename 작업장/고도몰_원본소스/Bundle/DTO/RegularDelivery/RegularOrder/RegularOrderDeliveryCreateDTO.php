<?php
/*
 * Copyright (C) 2025 NHN COMMERCE. - All Rights Reserved
 *
 * Unauthorized copying or redistribution of this file in source and binary forms via any medium
 * is strictly prohibited.
 */

namespace Bundle\DTO\RegularDelivery\RegularOrder;

use Origin\DTO\AbstractDTO;

class RegularOrderDeliveryCreateDTO extends AbstractDTO
{
    /**
     * @var int
     */
    private $applyNo;
    /** @var int  */
    private $deliverySno;
    /**
     * @var int
     */
    private $scmNo;
    /**
     * @var array
     */
    private $deliveryPolicy;

    public function __construct(int $applyNo, int $deliverySno, array $deliveryPolicy, array $deliveryCharge, array $deliveryArea)
    {
        $this->applyNo = $applyNo;
        $this->scmNo = $deliveryPolicy['scmNo'];
        $this->deliverySno = $deliverySno;
        $this->deliveryPolicy = json_encode([
            'scmNo' => $deliveryPolicy['scmNo'],
            'method' => $deliveryPolicy['method'],
            'collectFl' => $deliveryPolicy['collectFl'],
            'fixFl' => $deliveryPolicy['fixFl'],
            'freeFl' => $deliveryPolicy['freeFl'],
            'pricePlusStandard' => explode('^|^', $deliveryPolicy['pricePlusStandard']),
            'priceMinusStandard' => explode('^|^', $deliveryPolicy['priceMinusStandard']),
            'goodsDeliveryFl' => $deliveryPolicy['goodsDeliveryFl'],
            'areaFl' => $deliveryPolicy['areaFl'],
            'areaGroupNo' => $deliveryPolicy['areaGroupNo'],
            'scmCommissionDelivery' => $deliveryPolicy['scmCommissionDelivery'],
            'scmCommission' => $deliveryPolicy['scmCommission'],
            'taxFreeFl' => $deliveryPolicy['taxFreeFl'],
            'taxPercent' => $deliveryPolicy['taxPercent'],
            'rangeLimitFl' => $deliveryPolicy['rangeLimitFl'],
            'rangeLimitWeight' => $deliveryPolicy['rangeLimitWeight'],
            'rangeRepeat' => $deliveryPolicy['rangeRepeat'],
            'addGoodsCountInclude' => $deliveryPolicy['addGoodsCountInclude'],
            'deliveryMethodFl' => $deliveryPolicy['deliveryMethodFl'],
            'deliveryVisitPayFl' => $deliveryPolicy['deliveryVisitPayFl'],
            'deliveryConfigType' => $deliveryPolicy['deliveryConfigType'],
            'dmVisitAddressUseFl' => $deliveryPolicy['dmVisitAddressUseFl'],
            'sameGoodsDeliveryFl' => $deliveryPolicy['sameGoodsDeliveryFl'],
            'charge' => $deliveryCharge,
            'areaGroupList' => $deliveryArea,
        ]);
    }

    /**
     * @return int
     */
    public function getApplyNo(): int
    {
        return $this->applyNo;
    }

    /**
     * @return int
     */
    public function getDeliverySno(): int
    {
        return $this->deliverySno;
    }

    /**
     * @return int
     */
    public function getScmNo(): int
    {
        return $this->scmNo;
    }

    /**
     * @return string
     */
    public function getDeliveryPolicy(): string
    {
        return $this->deliveryPolicy;
    }
}
