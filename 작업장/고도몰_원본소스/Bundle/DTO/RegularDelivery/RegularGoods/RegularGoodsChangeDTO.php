<?php
/*
 * Copyright (C) 2025 NHN COMMERCE. - All Rights Reserved
 *
 * Unauthorized copying or redistribution of this file in source and binary forms via any medium
 * is strictly prohibited.
 */

namespace Bundle\DTO\RegularDelivery\RegularGoods;

use Origin\DTO\AbstractDTO;
use DTO\RegularDelivery\RegularOrder\RegularOrderGiftCreateDTO;

/**
 * @property-read int $applyNo
 * @property-read int $regularGoodsNo
 * @property-read int $goodsCnt
 * @property-read int $optionSno
 * @property-read array $optionTextSno
 * @property-read array $optionTextValue
 * @property-read array $addGoodsNo
 * @property-read array $addGoodsCnt
 * @property-read array giftInfo
 * @property-read string $sessionType
 * @property-read int $sessionSno
 */
class RegularGoodsChangeDTO extends AbstractDTO
{
    /** @var int */
    private $applyNo;

    /** @var int */
    private $regularGoodsNo;

    /** @var int */
    private $goodsCnt;

    /** @var int */
    private $optionSno;

    /** @var array */
    private $optionTextSno;

    /** @var array */
    private $optionTextValue;

    /** @var array */
    private $addGoodsNo;

    /** @var array */
    private $addGoodsCnt;

    /** @var RegularOrderGiftCreateDTO */
    private $giftInfo;

    /** @var string */
    private $sessionType;

    /** @var int */
    private $sessionSno;

    public function __construct(array $data)
    {
        $this->applyNo = (int) $data['applyNo'];
        $this->regularGoodsNo = (int) $data['regularGoodsNo'];
        $this->goodsCnt = (int) $data['goodsCnt'];
        $this->optionSno = (int) $data['optionSno'];
        $this->optionTextSno = $data['optionTextSno'] ?? [];
        $this->optionTextValue = $data['optionTextValue'] ?? [];
        $this->addGoodsNo = $data['addGoodsNo'] ?? [];
        $this->addGoodsCnt = $data['addGoodsCnt'] ?? [];
        $this->giftInfo = new RegularOrderGiftCreateDTO($data['giftInfo'] ?? []);
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
     * @return int
     */
    public function getRegularGoodsNo(): int
    {
        return $this->regularGoodsNo;
    }

    /**
     * @return int
     */
    public function getGoodsCnt(): int
    {
        return $this->goodsCnt;
    }

    /**
     * @return int
     */
    public function getOptionSno(): int
    {
        return $this->optionSno;
    }

    /**
     * @return array
     */
    public function getOptionTextSno(): array
    {
        return $this->optionTextSno;
    }

    /**
     * @return array
     */
    public function getOptionTextValue(): array
    {
        return $this->optionTextValue;
    }

    /**
     * @return array
     */
    public function getAddGoodsNo(): array
    {
        return $this->addGoodsNo;
    }

    /**
     * @return array
     */
    public function getAddGoodsCnt(): array
    {
        return $this->addGoodsCnt;
    }

    /**
     * @return RegularOrderGiftCreateDTO
     */
    public function getGiftInfo(): RegularOrderGiftCreateDTO
    {
        return $this->giftInfo;
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
