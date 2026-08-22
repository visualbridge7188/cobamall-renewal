<?php
/*
 * Copyright (C) 2025 NHN COMMERCE. - All Rights Reserved
 *
 * Unauthorized copying or redistribution of this file in source and binary forms via any medium
 * is strictly prohibited.
 */

namespace Bundle\DTO\RegularDelivery\RegularOrder;

/**
 * 신청서 skip 용 DTO
 */
class RegularOrderSkipDTO
{
    /**
     * @var int
     */
    private $applyNo;
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
