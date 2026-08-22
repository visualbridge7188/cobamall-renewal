<?php
/*
 * Copyright (C) 2025 NHN COMMERCE. - All Rights Reserved
 *
 * Unauthorized copying or redistribution of this file in source and binary forms via any medium
 * is strictly prohibited.
 */

namespace Bundle\DTO\RegularDelivery\RegularGoods;

use Origin\DTO\AbstractDTO;

/**
 * @property-read int sno
 * @property-read array targetGoodsNoList
 * @property-read array regularGoods
 * @property-read array regularGiftPresent
 * @property-read array regularGoodsDeliveryCycle
 * @property-read array regularGiftPresentInfo
 * @property-read string|null prevData
 */
class RegularGoodsAdminDTO extends AbstractDTO
{
    /** @var int */
    private $sno;

    /** @var array */
    private $targetGoodsNoList;

    /** @var array */
    private $regularGoods;

    /** @var array */
    private $regularGoodsDeliveryCycle;

    /** @var array */
    private $regularGiftPresent;

    /** @var array */
    private $regularGiftPresentInfo;

    /** @var string|null */
    private $prevData;

    public function __construct(array $data)
    {
        $this->sno = $data['sno'];
        $this->targetGoodsNoList = $data['targetGoodsNoList'] ?? [];
        $this->regularGoods = $data['regularGoods'] ?? [];
        $this->regularGoodsDeliveryCycle = $data['regularGoodsDeliveryCycle'] ?? [];
        $this->regularGiftPresent = $data['regularGiftPresent'] ?? [];
        $this->regularGiftPresentInfo = $data['gift'] ?? [];
        $this->prevData = $data['prevData'] ?? null;
    }

    /**
     * @return int
     */
    public function getSno(): int
    {
        return $this->sno;
    }

    /**
     * @return array
     */
    public function getTargetGoodsNoList(): array
    {
        return $this->targetGoodsNoList;
    }

    /**
     * @return array
     */
    public function getRegularGoods(): array
    {
        return $this->regularGoods;
    }

    /**
     * @return array
     */
    public function getRegularGoodsDeliveryCycle(): array
    {
        return $this->regularGoodsDeliveryCycle;
    }

    /**
     * @return array
     */
    public function getRegularGiftPresent(): array
    {
        return $this->regularGiftPresent;
    }

    /**
     * @return array
     */
    public function getRegularGiftPresentInfo(): array
    {
        return $this->regularGiftPresentInfo;
    }

    /**
     * @return string|null
     */
    public function getPrevData()
    {
        return $this->prevData;
    }
}
