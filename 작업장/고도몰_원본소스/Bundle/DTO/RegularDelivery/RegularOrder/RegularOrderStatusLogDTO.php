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
 * @property-read string $applyNo
 * @property-read string $modifier
 * @property-read int $modifierNo
 * @property-read string $modifierIP
 * @property-read string $prevOrderStatus
 * @property-read string $changeOrderStatus
 * @property-read string $description
 * @property-read string $regDt
 */
class RegularOrderStatusLogDTO extends AbstractDTO
{
    private $applyNo;
    private $modifier;
    private $modifierNo;
    private $modifierIP;
    private $prevOrderStatus;
    private $changeOrderStatus;
    private $description;
    private $regDt;

    public function __construct(
        string $applyNo,
        string $modifier,
        int $modifierNo,
        string $prevOrderStatus,
        string $changeOrderStatus = '',
        string $description = ''
    )
    {
        $this->applyNo = $applyNo;
        $this->modifier = $modifier;
        $this->modifierNo = $modifierNo;
        $this->modifierIP = (in_array($changeOrderStatus, RegularOrderStatus::getStatusFromSystem())) ? '' : \Request::getRemoteAddress();
        $this->prevOrderStatus = $prevOrderStatus;
        $this->changeOrderStatus = $changeOrderStatus;
        $this->description = $description;
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
     * @return string
     */
    public function getModifier(): string
    {
        return $this->modifier;
    }

    /**
     * @return int
     */
    public function getModifierNo(): int
    {
        return $this->modifierNo;
    }

    /**
     * @return string
     */
    public function getModifierIP(): string
    {
        return $this->modifierIP;
    }

    /**
     * @return string
     */
    public function getPrevOrderStatus(): string
    {
        return $this->prevOrderStatus;
    }

    /**
     * @return string
     */
    public function getChangeOrderStatus(): string
    {
        return $this->changeOrderStatus;
    }

    /**
     * @return string
     */
    public function getDescription(): string
    {
        return $this->description;
    }

    /**
     * @return string
     */
    public function getRegDt(): string
    {
        return $this->regDt;
    }
}
