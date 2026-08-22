<?php
/*
 * Copyright (C) 2025 NHN COMMERCE. - All Rights Reserved
 *
 * Unauthorized copying or redistribution of this file in source and binary forms via any medium
 * is strictly prohibited.
 */

namespace Bundle\DTO\RegularDelivery\RegularGoods;

use Origin\DTO\AbstractDTO;
use Origin\Enum\RegularDelivery\RegularGoods\RegularGoodsAttribute;

/**
 * @property-read int goodsNo
 * @property-read string applyStatus
 * @property-read string discountUseFl
 * @property-read string discountType
 * @property-read float|null discountRate
 * @property-read float|null discountPrice
 * @property-read float regularPrice
 * @property-read string deliveryType
 * @property-read string deliveryCycleType
 * @property-read string deliveryRoundsDisplayType
 * @property-read int|null maxDeliveryRounds
 */
class RegularGoodsDTO extends AbstractDTO
{
    /** @var int */
    private $goodsNo;

    /** @var string */
    private $applyStatus;

    /** @var string */
    private $discountUseFl;

    /** @var string */
    private $discountType;

    /** @var float|null */
    private $discountRate;

    /** @var float|null */
    private $discountPrice;

    /** @var float */
    private $regularPrice;

    /** @var string */
    private $deliveryType;

    /** @var string */
    private $deliveryCycleType;

    /** @var string */
    private $deliveryRoundsDisplayType;

    /** @var int|null */
    private $maxDeliveryRounds;

    public function __construct(array $regularGoodsData)
    {
        $this->goodsNo = $regularGoodsData['goodsNo'];
        $this->applyStatus = $regularGoodsData['applyStatus'] ?? RegularGoodsAttribute::ABLED;
        $this->discountUseFl = $regularGoodsData['discountUseFl'];
        $this->discountType = $regularGoodsData['discountType'] ?? RegularGoodsAttribute::DISCOUNT_PERCENT;
        $this->discountRate = $regularGoodsData['discountRate'] ?? null;
        $this->discountPrice = $regularGoodsData['discountPrice'] ?? null;
        $this->regularPrice = $regularGoodsData['regularPrice'] ?? 0.0;
        $this->deliveryType = $regularGoodsData['deliveryType'] ?? RegularGoodsAttribute::DELIVERY_ALL;
        $this->deliveryCycleType = $regularGoodsData['deliveryCycleType'] ?? RegularGoodsAttribute::DELIVERY_CYCLE_ALL;
        $this->deliveryRoundsDisplayType = $regularGoodsData['deliveryRoundsDisplayType'] ?? RegularGoodsAttribute::DELIVERY_ROUNDS_DISPLAY_ALL;
        $this->maxDeliveryRounds = $regularGoodsData['maxDeliveryRounds'] ?? null;
    }

    /**
     * @return int
     */
    public function getGoodsNo(): int
    {
        return $this->goodsNo;
    }

    /**
     * @return string
     */
    public function getApplyStatus(): string
    {
        return $this->applyStatus;
    }

    /**
     * @return string
     */
    public function getDiscountUseFl(): string
    {
        return $this->discountUseFl;
    }

    /**
     * @return string
     */
    public function getDiscountType(): string
    {
        return $this->discountType;
    }

    /**
     * @return float|null
     */
    public function getDiscountRate()
    {
        return $this->discountRate;
    }

    /**
     * @return float|null
     */
    public function getDiscountPrice()
    {
        return $this->discountPrice;
    }

    /**
     * @return float
     */
    public function getRegularPrice(): float
    {
        return $this->regularPrice;
    }

    /**
     * @return string
     */
    public function getDeliveryType(): string
    {
        return $this->deliveryType;
    }

    /**
     * @return string
     */
    public function getDeliveryCycleType(): string
    {
        return $this->deliveryCycleType;
    }

    /**
     * @return string
     */
    public function getDeliveryRoundsDisplayType(): string
    {
        return $this->deliveryRoundsDisplayType;
    }

    /**
     * @return int|null
     */
    public function getMaxDeliveryRounds()
    {
        return $this->maxDeliveryRounds;
    }
}
