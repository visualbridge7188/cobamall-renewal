<?php
/*
 * Copyright (C) 2025 NHN COMMERCE. - All Rights Reserved
 *
 * Unauthorized copying or redistribution of this file in source and binary forms via any medium
 * is strictly prohibited.
 */

namespace Bundle\DTO\RegularDelivery\RegularOrder;

use Origin\DTO\AbstractDTO;

class RegularOrderGiftConditionDTO extends AbstractDTO
{
    /**
     * @var int
     */
    private $page;
    /**
     * @var string
     */
    private $changeGoodsFl;
    /**
     * @var int
     */
    private $applyNo;
    /**
     * @var int
     */
    private $regularGoodsNo;
    /**
     * @var int
     */
    private $regularGoodsCnt;
    /**
     * @var int
     */
    private $regularAddGoodsCnt;

    public function __construct(array $data)
    {
        $this->page = $data['page'] ?? 1;
        $this->changeGoodsFl = $data['changeGoodsFl'] ?? 'n';
        $this->applyNo = (int)$data['applyNo'];
        $this->regularGoodsNo = (int)$data['regularGoodsNo'];
        $this->regularGoodsCnt = (int)$data['regularGoodsCnt'];
        $this->regularAddGoodsCnt = (int)$data['regularAddGoodsCnt'];
    }

    /**
     * @return int
     */
    public function getPage(): int
    {
        return $this->page;
    }

    /**
     * @return string
     */
    public function getChangeGoodsFl(): string
    {
        return $this->changeGoodsFl;
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
    public function getRegularGoodsCnt(): int
    {
        return $this->regularGoodsCnt;
    }

    /**
     * @return int
     */
    public function getRegularAddGoodsCnt(): int
    {
        return $this->regularAddGoodsCnt;
    }
}
