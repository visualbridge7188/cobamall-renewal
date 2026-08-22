<?php
/*
 * Copyright (C) 2025 NHN COMMERCE. - All Rights Reserved
 *
 * Unauthorized copying or redistribution of this file in source and binary forms via any medium
 * is strictly prohibited.
 */

namespace Bundle\DTO\RegularDelivery\RegularOrder;

use Framework\Utility\StringUtils;
use Origin\DTO\AbstractDTO;

/**
 * @property-read string applyGroupNo
 * @property-read string applierName
 * @property-read string|null applierPhone
 * @property-read string applierCellPhone
 * @property-read string applierEmail
 * @property-read string applierIp
 * @property-read string regDt
 */
class RegularOrderApplierCreateDTO extends AbstractDTO
{
    private $applyGroupNo;
    private $applierName;
    private $applierPhone;
    private $applierCellPhone;
    private $applierEmail;
    private $applierIp;
    private $regDt;

    /**
     * @param RegularOrderDTO $regularOrderDTO
     * @param string $applyGroupNo
     */
    public function __construct(RegularOrderDTO $regularOrderDTO, string $applyGroupNo)
    {
        $applierInfo = $regularOrderDTO->getApplierInfo();
        $this->applyGroupNo = $applyGroupNo;
        $this->applierName = $applierInfo['applierName'];
        $this->applierPhone = !empty($applierInfo['applierPhone']) ? StringUtils::numberToPhone(str_replace('-', '', $applierInfo['applierPhone']), true) : null;
        $this->applierCellPhone = StringUtils::numberToPhone(str_replace('-', '', $applierInfo['applierCellPhone']), true);
        $this->applierEmail = $applierInfo['applierEmail'];
        $this->applierIp = \Request::getRemoteAddress();
        $this->regDt = date('Y-m-d H:i:s');
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
    public function getApplierName(): string
    {
        return $this->applierName;
    }

    /**
     * @return string|null
     */
    public function getApplierPhone()
    {
        return $this->applierPhone;
    }

    /**
     * @return string
     */
    public function getApplierCellPhone(): string
    {
        return $this->applierCellPhone;
    }

    /**
     * @return string
     */
    public function getApplierEmail(): string
    {
        return $this->applierEmail;
    }

    /**
     * @return string
     */
    public function getApplierIp(): string
    {
        return $this->applierIp;
    }

    /**
     * @return string
     */
    public function getRegDt(): string
    {
        return $this->regDt;
    }
}
