<?php
/*
 * Copyright (C) 2025 NHN COMMERCE. - All Rights Reserved
 *
 * Unauthorized copying or redistribution of this file in source and binary forms via any medium
 * is strictly prohibited.
 */

namespace Bundle\DTO\RegularDelivery\RegularOrder;

use Origin\DTO\AbstractDTO;

class RegularOrderGoodsSkipRoundDTO extends AbstractDTO
{
    /**
     * @var int
     */
    private $applyNo;
    /**
     * @var string
     */
    private $deliveryDueDate;
    /**
     * @var string
     */
    private $orderCreateDate;

    public function __construct(int $applyNo, string $deliveryDueDate, string $orderCreateDate)
    {
        $this->applyNo = $applyNo;
        $this->deliveryDueDate = $deliveryDueDate;
        $this->orderCreateDate = $orderCreateDate;
    }

    /**
     * @return int
     */
    public function getApplyNo(): int
    {
        return $this->applyNo;
    }

    /**
     * @return string
     */
    public function getDeliveryDueDate(): string
    {
        return $this->deliveryDueDate;
    }

    /**
     * @return string
     */
    public function getOrderCreateDate(): string
    {
        return $this->orderCreateDate;
    }
}
