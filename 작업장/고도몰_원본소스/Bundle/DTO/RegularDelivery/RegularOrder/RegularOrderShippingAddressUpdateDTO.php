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
 * 정기결제 신청서 배송지 변경 DTO
 */
class RegularOrderShippingAddressUpdateDTO extends AbstractDTO
{
    /**
     * @var int
     */
    private $applyNo;
    /**
     * @var int
     */
    private $shippingSno;
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
        $this->applyNo = $data['applyNo'];
        $this->shippingSno = $data['shippingSno'];
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
    public function getShippingSno(): int
    {
        return $this->shippingSno;
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
