<?php
/*
 * Copyright (C) 2025 NHN COMMERCE. - All Rights Reserved
 *
 * Unauthorized copying or redistribution of this file in source and binary forms via any medium
 * is strictly prohibited.
 */

namespace Bundle\DTO\RegularDelivery\RegularOrder;

use Origin\DTO\AbstractDTO;

class RegularOrderGoodsOptionDTO extends AbstractDTO
{
    /**
     * @var int
     */
    private $applyNo;
    /**
     * @var int
     */
    private $optionSno;
    /**
     * @var int
     */
    private $regularGoodsNo;
    /**
     * @var float
     */
    private $regularGoodsOptionPrice;
    /**
     * @var string
     */
    private $regularGoodsOptionInfo;
    /**
     * @var float
     */
    private $originGoodsOptionPrice;
    /**
     * @var string
     */
    private $regDt;

    public function __construct(
        int $applyNo,
        int $optionSno,
        int $regularGoodsNo,
        float $optionPrice,
        string $optionInfo,
        float $originGoodsOptionPrice
    )
    {
        $this->applyNo = $applyNo;
        $this->optionSno = $optionSno;
        $this->regularGoodsNo = $regularGoodsNo;
        $this->regularGoodsOptionPrice = $optionPrice;
        $this->regularGoodsOptionInfo = $optionInfo;
        $this->originGoodsOptionPrice = $originGoodsOptionPrice;
        $this->regDt = date('Y-m-d H:i:s');
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
    public function getOptionSno(): int
    {
        return $this->optionSno;
    }

    /**
     * @return int
     */
    public function getRegularGoodsNo(): int
    {
        return $this->regularGoodsNo;
    }

    /**
     * @return float
     */
    public function getRegularGoodsOptionPrice(): float
    {
        return $this->regularGoodsOptionPrice;
    }

    /**
     * @return string
     */
    public function getRegularGoodsOptionInfo(): string
    {
        return $this->regularGoodsOptionInfo;
    }

    /**
     * @return float
     */
    public function getOriginGoodsOptionPrice(): float
    {
        return $this->originGoodsOptionPrice;
    }

    /**
     * @return string
     */
    public function getRegDt(): string
    {
        return $this->regDt;
    }
}
