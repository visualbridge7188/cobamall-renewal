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
 * @property-read int $applyNo
 * @property-read string $currentCardNo
 * @property-read string $updatedCardNo
 * @property-read string $sessionType
 * @property-read int $sessionSno
 */
class RegularOrderPaymentCardUpdateDTO extends AbstractDTO
{
    /** @var int */
    private $applyNo;

    /** @var string */
    private $currentCardNo;

    /** @var string */
    private $updatedCardNo;

    /** @var string */
    private $sessionType;

    /** @var int */
    private $sessionSno;

    public function __construct(array $data)
    {
        $this->applyNo = $data['applyNo'];
        $this->currentCardNo = $data['currentCardNo'];
        $this->updatedCardNo = $data['updatedCardNo'];
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
     * @return string
     */
    public function getCurrentCardNo(): string
    {
        return $this->currentCardNo;
    }

    /**
     * @return string
     */
    public function getUpdatedCardNo(): string
    {
        return $this->updatedCardNo;
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
