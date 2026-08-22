<?php
/*
 * Copyright (C) 2025 NHN COMMERCE. - All Rights Reserved
 *
 * Unauthorized copying or redistribution of this file in source and binary forms via any medium
 * is strictly prohibited.
 */

namespace Bundle\DTO\RegularDelivery\RegularOrder;

use Origin\DTO\AbstractDTO;

class RegularOrderApplierUpdateDTO extends AbstractDTO
{
    private $applyNo;
    private $applierName;
    private $applierPhone;
    private $applierCellPhone;
    private $applierEmail;

    public function __construct(int $applyNo, array $data)
    {
        $this->applyNo = $applyNo;
        $this->applierName = $data['applierName'];
        $this->applierPhone = !empty($data['applierPhone']) ? $data['applierPhone'] : null;
        $this->applierCellPhone = $data['applierCellPhone'];
        $this->applierEmail = $data['applierEmail'];
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
}
