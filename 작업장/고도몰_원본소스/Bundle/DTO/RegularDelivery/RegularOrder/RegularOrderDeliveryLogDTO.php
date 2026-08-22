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
 * @property-read string $applyNo
 * @property-read int $deliveryRound
 * @property-read string $deliveryDate
 * @property-read string|null $orderNo
 * @property-read string $regDt
 */
class RegularOrderDeliveryLogDTO extends AbstractDTO
{
    private $applyNo;
    private $deliveryRound;
    private $deliveryDate;
    private $orderNo;
    private $regDt;

    /**
     * @param string $applyNo
     * @param string $deliveryRound
     * @param string $deliveryDate
     * @param string $orderNo
     */
    public function __construct(
        string $applyNo,
        string $deliveryRound,
        string $deliveryDate,
        string $orderNo = ''
    ) {
        $this->applyNo = $applyNo;
        $this->deliveryRound = (int) $deliveryRound;
        $this->deliveryDate = $deliveryDate;
        $this->orderNo = !empty($orderNo) ? $orderNo : null;
        $this->regDt = date('Y-m-d H:i:s');
    }

    /**
     * @return string
     */
    public function getApplyNo(): string
    {
        return $this->applyNo;
    }

    /**
     * @return int
     */
    public function getDeliveryRound(): int
    {
        return $this->deliveryRound;
    }

    /**
     * @return string
     */
    public function getDeliveryDate(): string
    {
        return $this->deliveryDate;
    }

    /**
     * @return string|null
     */
    public function getOrderNo()
    {
        return $this->orderNo;
    }

    /**
     * @return string
     */
    public function getRegDt(): string
    {
        return $this->regDt;
    }
}
