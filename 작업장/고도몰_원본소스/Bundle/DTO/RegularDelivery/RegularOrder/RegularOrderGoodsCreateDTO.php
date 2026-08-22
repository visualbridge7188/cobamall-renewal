<?php
/*
 * Copyright (C) 2025 NHN COMMERCE. - All Rights Reserved
 *
 * Unauthorized copying or redistribution of this file in source and binary forms via any medium
 * is strictly prohibited.
 */

namespace Bundle\DTO\RegularDelivery\RegularOrder;

use Origin\DTO\AbstractDTO;
use Origin\Enum\RegularDelivery\RegularOrder\RegularOrderStatus;

/**
 *
 * @property-read string $applyGroupNo
 * @property-read string $applyStatus
 * @property-read int $regularGoodsNo
 * @property-read string $regularGoodsNm
 * @property-read float $regularGoodsPrice
 * @property-read int $regularGoodsCnt
 * @property-read float $originGoodsPrice
 * @property-read string $mileageInfo
 * @property-read int $shippingAddressSno
 * @property-read string $cardNo
 * @property-read string $deliveryCycleType
 * @property-read int $deliveryCycle
 * @property-read int $deliveryCycleDay
 * @property-read int $deliveryRound
 * @property-read string $deliveryDueDate
 * @property-read string $orderCreateDate
 * @property-read string $regularGoodsPolicy
 * @property-read string $regDt
 */
class RegularOrderGoodsCreateDTO extends AbstractDTO
{
    const FIRST_DELIVERY_ROUND = 1;

    private $applyGroupNo;
    private $applyStatus;
    private $regularGoodsNo;
    private $regularGoodsNm;
    private $regularGoodsPrice;
    private $regularGoodsCnt;
    private $originGoodsPrice;
    private $discountPrice;
    private $mileageInfo;
    private $shippingAddressSno;
    private $cardNo;
    private $deliveryCycleType;
    private $deliveryCycle;
    private $deliveryCycleDay;
    private $deliveryRound;
    private $maxDeliveryRound;
    private $deliveryDueDate;
    private $orderCreateDate;
    private $regularGoodsPolicy;
    private $regDt;

    public function __construct(string $applyGroupNo, array $regularOrderGoodsInfo)
    {
        $this->applyGroupNo = $applyGroupNo;
        $this->regularGoodsNo = $regularOrderGoodsInfo['regularGoodsNo'];
        $this->applyStatus = RegularOrderStatus::ACTIVE; // 고정
        $this->regularGoodsNm = $regularOrderGoodsInfo['goodsNm'];
        $this->regularGoodsPrice = $regularOrderGoodsInfo['regularGoodsPrice'];
        $this->regularGoodsCnt = (int) $regularOrderGoodsInfo['goodsCnt'];
        $this->originGoodsPrice = $regularOrderGoodsInfo['originGoodsPrice'];
        $this->discountPrice = $regularOrderGoodsInfo['discountPrice'];
        $this->mileageInfo = json_encode($regularOrderGoodsInfo['mileageInfo']);
        $this->shippingAddressSno = $regularOrderGoodsInfo['shippingAddressSno'];
        $this->cardNo = $regularOrderGoodsInfo['cardNo'];
        $this->deliveryCycleType = $regularOrderGoodsInfo['deliveryCycleType'];
        $this->deliveryCycle = $regularOrderGoodsInfo['deliveryCycle'];
        $this->deliveryCycleDay = $regularOrderGoodsInfo['deliveryCycleDay'];
        $this->deliveryRound = self::FIRST_DELIVERY_ROUND;
        $this->maxDeliveryRound = $regularOrderGoodsInfo['maxDeliveryRound'];
        $this->deliveryDueDate = $regularOrderGoodsInfo['deliveryDueDate'];
        $this->orderCreateDate = $regularOrderGoodsInfo['orderCreateDate'];
        $this->regularGoodsPolicy = $regularOrderGoodsInfo['regularGoodsPolicy'];
        $this->regDt = $regularOrderGoodsInfo['regDt'];
    }

    /**
     * @return string
     */
    public function getApplyGroupNo(): string
    {
        return $this->applyGroupNo;
    }

    /**
     * @return string
     */
    public function getApplyStatus(): string
    {
        return $this->applyStatus;
    }

    /**
     * @return int
     */
    public function getRegularGoodsNo(): int
    {
        return $this->regularGoodsNo;
    }

    /**
     * @return string
     */
    public function getRegularGoodsNm(): string
    {
        return $this->regularGoodsNm;
    }

    /**
     * @return float
     */
    public function getRegularGoodsPrice(): float
    {
        return $this->regularGoodsPrice;
    }

    /**
     * @return int
     */
    public function getDiscountPrice(): int
    {
        return $this->discountPrice;
    }

    /**
     * @return int
     */
    public function getRegularGoodsCnt(): int
    {
        return $this->regularGoodsCnt;
    }

    /**
     * @return float
     */
    public function getOriginGoodsPrice(): float
    {
        return $this->originGoodsPrice;
    }

    /**
     * @return string
     */
    public function getMileageInfo(): string
    {
        return $this->mileageInfo;
    }

    /**
     * @return int
     */
    public function getShippingAddressSno(): int
    {
        return $this->shippingAddressSno;
    }

    /**
     * @return string
     */
    public function getCardNo(): string
    {
        return $this->cardNo;
    }

    /**
     * @return string
     */
    public function getDeliveryCycleType(): string
    {
        return $this->deliveryCycleType;
    }

    /**
     * @return int
     */
    public function getDeliveryCycle(): int
    {
        return $this->deliveryCycle;
    }

    /**
     * @return int
     */
    public function getDeliveryCycleDay(): int
    {
        return $this->deliveryCycleDay;
    }

    /**
     * @return int
     */
    public function getDeliveryRound(): int
    {
        return $this->deliveryRound;
    }

    /**
     * @return int
     */
    public function getMaxDeliveryRound(): int
    {
        return $this->maxDeliveryRound;
    }

    /**
     * @return string
     */
    public function getDeliveryDueDate(): string
    {
        return $this->deliveryDueDate;
    }

    /**
     * @return string
     */
    public function getOrderCreateDate(): string
    {
        return $this->orderCreateDate;
    }

    /**
     * @return string
     */
    public function getRegularGoodsPolicy(): string
    {
        return $this->regularGoodsPolicy;
    }

    /**
     * @return string
     */
    public function getRegDt(): string
    {
        return $this->regDt;
    }
}
