<?php
/*
 * Copyright (C) 2025 NHN COMMERCE. - All Rights Reserved
 *
 * Unauthorized copying or redistribution of this file in source and binary forms via any medium
 * is strictly prohibited.
 */

namespace Bundle\DTO\RegularDelivery\RegularOrder;

use Origin\DTO\AbstractDTO;

class RegularOrderGoodsDeliveryInfoUpdateDTO extends AbstractDTO
{
    private $applyNo;
    private $deliveryCycleType;
    private $deliveryCycle;
    private $deliveryCycleDay;
    private $maxDeliveryRound;
    private $deliveryDueDate;
    private $orderCreateDate;

    public function __construct(array $data, string $deliveryDueDate, string $orderCreateDate)
    {
        $this->applyNo = (int) $data['applyNo'];
        $this->deliveryCycleType = $data['deliveryCycleType'];
        $this->deliveryCycle = (int) $data['deliveryCycle'];
        $this->deliveryCycleDay = (int) $data['deliveryCycleDay'];
        $this->maxDeliveryRound = (int) $data['maxDeliveryRound'];
        $this->deliveryDueDate = $deliveryDueDate;
        $this->orderCreateDate = $orderCreateDate;
    }

    /**
     * @return int
     */
    public function getApplyNo(): int
    {
        return $this->applyNo;
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
}
