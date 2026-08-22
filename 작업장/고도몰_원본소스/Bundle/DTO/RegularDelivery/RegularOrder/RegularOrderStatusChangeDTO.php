<?php
/*
 * Copyright (C) 2025 NHN COMMERCE. - All Rights Reserved
 *
 * Unauthorized copying or redistribution of this file in source and binary forms via any medium
 * is strictly prohibited.
 */

namespace Bundle\DTO\RegularDelivery\RegularOrder;

use Origin\DTO\AbstractDTO;

class RegularOrderStatusChangeDTO extends AbstractDTO
{
    /**
     * @var array
     */
    private $applyNoList;
    /**
     * @var string
     */
    private $updateStatus;
    /**
     * @var string
     */
    private $sessionType;
    /**
     * @var int
     */
    private $sessionSno;

    /**
     * @var string
     */
    private $reason;

    public function __construct(array $data)
    {
        $this->applyNoList = $data['applyNoList'];
        $this->updateStatus = $data['updateStatus'];
        $this->sessionType = $data['sessionType'];
        $this->sessionSno = (int) $data['sessionSno'];
        $this->reason = $data['reason'] ?? '';
    }

    /**
     * @return array
     */
    public function getApplyNoList(): array
    {
        return $this->applyNoList;
    }

    /**
     * @return string
     */
    public function getUpdateStatus(): string
    {
        return $this->updateStatus;
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

    /**
     * @return string
     */
    public function getReason(): string
    {
        return $this->reason;
    }
}
