<?php
/*
 * Copyright (C) 2025 NHN COMMERCE. - All Rights Reserved
 *
 * Unauthorized copying or redistribution of this file in source and binary forms via any medium
 * is strictly prohibited.
 */

namespace Bundle\DTO\RegularDelivery\RegularOrder;

use Origin\DTO\AbstractDTO;

class RegularOrderGoodsOptionTextDTO extends AbstractDTO
{
    /**
     * @var int
     */
    private $applyNo;
    /**
     * @var int
     */
    private $regularGoodsNo;
    /**
     * @var float
     */
    private $regularGoodsOptionTextPrice;
    /**
     * @var string
     */
    private $regularGoodsOptionTextInfo;
    /**
     * @var float
     */
    private $originGoodsOptionTextPrice;
    /**
     * @var string
     */
    private $regDt;

    public function __construct(
        int $applyNo,
        int $regularGoodsNo,
        float $optionTextPrice,
        string $optionTextInfo,
        float $originGoodsOptionTextPrice
    )
    {
        $this->applyNo = $applyNo;
        $this->regularGoodsNo = $regularGoodsNo;
        $this->regularGoodsOptionTextPrice = $optionTextPrice;
        $this->regularGoodsOptionTextInfo = $optionTextInfo;
        $this->originGoodsOptionTextPrice = $originGoodsOptionTextPrice;
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
    public function getRegularGoodsNo(): int
    {
        return $this->regularGoodsNo;
    }

    /**
     * @return float
     */
    public function getRegularGoodsOptionTextPrice(): float
    {
        return $this->regularGoodsOptionTextPrice;
    }

    /**
     * @return string
     */
    public function getRegularGoodsOptionTextInfo(): string
    {
        return $this->regularGoodsOptionTextInfo;
    }

    /**
     * @return float
     */
    public function getOriginGoodsOptionTextPrice(): float
    {
        return $this->originGoodsOptionTextPrice;
    }

    /**
     * @return string
     */
    public function getRegDt(): string
    {
        return $this->regDt;
    }
}
