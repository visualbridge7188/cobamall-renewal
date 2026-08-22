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
 * 배송주기 변경 용 DTO
 */
class DeliveryCycleChangeDTO extends AbstractDTO
{
    /**
     * @var int
     */
    private $applyNo;
    /**
     * @var int
     */
    private $maxDeliveryRound;
    /**
     * @var string
     */
    private $deliveryCycleType;
    /**
     * @var int
     */
    private $deliveryCycle;
    /**
     * @var int
     */
    private $deliveryCycleDay;
    /**
     * @var string
     */
    private $sessionType;
    /**
     * @var int
     */
    private $sessionSno;

    public function __construct(array $data)
    {
        $this->applyNo = (int) $data['applyNo'];
        $this->maxDeliveryRound = (int) $data['maxDeliveryRound'];
        $this->deliveryCycleType = $data['deliveryCycleType'];
        $this->deliveryCycle = (int) $data['deliveryCycle'];
        $this->deliveryCycleDay = (int) $data['deliveryCycleDay'];
        $this->sessionType = $data['sessionType'];
        $this->sessionSno = (int) $data['sessionSno'];
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
    public function getMaxDeliveryRound(): int
    {
        return $this->maxDeliveryRound;
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
     * @return string
     */
    public function getSessionType(): string
    {
        return $this->sessionType;
    }

    /**
     * @return int
     */
    public function getSessionSno(): int
    {
        return $this->sessionSno;
    }
}
