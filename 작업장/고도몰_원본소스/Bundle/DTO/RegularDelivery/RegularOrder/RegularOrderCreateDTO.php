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
 * @property-read string applyGroupNo
 * @property-read int memNo
 * @property-read string regDt
 */
class RegularOrderCreateDTO extends AbstractDTO
{
    private $applyGroupNo;
    private $memNo;
    private $regDt;

    /**
     * @param string $applyGroupNo
     * @param RegularOrderDTO $regularOrderDTO
     */
    public function __construct(string $applyGroupNo, RegularOrderDTO $regularOrderDTO)
    {
        $this->applyGroupNo = $applyGroupNo;
        $this->memNo = $regularOrderDTO->getMemNo();
        $this->regDt = $regularOrderDTO->getRegDt();
    }

    /**
     * @return string
     */
    public function getApplyGroupNo(): string
    {
        return $this->applyGroupNo;
    }

    /**
     * @return int
     */
    public function getMemNo(): int
    {
        return $this->memNo;
    }

    /**
     * @return string
     */
    public function getRegDt(): string
    {
        return $this->regDt;
    }
}
