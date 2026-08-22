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
 * @property-read array $regularGoodsSnoList
 * @property-read string $modifier
 * @property-read int $modifierNo
 */
class RegularGoodsDeleteDTO extends AbstractDTO
{
    /** @var array */
    private $regularGoodsSnoList;

    /** @var string */
    private $modifier;

    /** @var int */
    private $modifierNo;

    public function __construct(array $data)
    {
        $this->regularGoodsSnoList = $data['regularGoodsSnoList'];
        $this->modifier = $data['modifier'];
        $this->modifierNo = $data['modifierNo'];
    }

    /**
     * @return array
     */
    public function getRegularGoodsSnoList(): array
    {
        return $this->regularGoodsSnoList;
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
}
