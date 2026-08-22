<?php
/*
 * Copyright (C) 2025 NHN COMMERCE. - All Rights Reserved
 *
 * Unauthorized copying or redistribution of this file in source and binary forms via any medium
 * is strictly prohibited.
 */

namespace Bundle\DTO\RegularDelivery\RegularOrder;

use Origin\DTO\AbstractDTO;
use Origin\Enum\RegularDelivery\RegularOrder\RegularOrderLogActionType;

/**
 * @property-read string $applyNo
 * @property-read string $modifier
 * @property-read int $modifierNo
 * @property-read string $modifierIP
 * @property-read string $actionType
 * @property-read string|null $actionDesc
 * @property-read string $regDt
 */
class RegularOrderLogDTO extends AbstractDTO
{
    private $applyNo;
    private $modifier;
    private $modifierNo;
    private $modifierIP;
    private $actionType;
    private $actionDesc;
    private $regDt;

    /**
     * @param string $applyNo
     * @param string $modifier
     * @param int $modifierNo
     * @param string $actionType
     * @param string|null $prevAction
     * @param string|null $changeAction
     */
    public function __construct(
        string $applyNo,
        string $modifier,
        int $modifierNo,
        string $actionType,
        string $prevAction = '',
        string $changeAction = ''
    ) {
        $this->applyNo = $applyNo;
        $this->modifier = $modifier;
        $this->modifierNo = $modifierNo;
        $this->modifierIP = \Request::getRemoteAddress();
        $this->actionType = $actionType;
        $this->actionDesc = !empty(RegularOrderLogActionType::combineDescription($prevAction, $changeAction))
            ? RegularOrderLogActionType::combineDescription($prevAction, $changeAction)
            : null;
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
    public function getActionType(): string
    {
        return $this->actionType;
    }

    /**
     * @return string|null
     */
    public function getActionDesc()
    {
        return $this->actionDesc;
    }

    /**
     * @return string
     */
    public function getRegDt(): string
    {
        return $this->regDt;
    }
}
