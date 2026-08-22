<?php
/*
 * Copyright (C) 2025 NHN COMMERCE. - All Rights Reserved
 *
 * Unauthorized copying or redistribution of this file in source and binary forms via any medium
 * is strictly prohibited.
 */

namespace Bundle\DTO\RegularDelivery\RegularOrder;

use Origin\DTO\AbstractDTO;

class RegularOrderAdminMemoCreateDTO extends AbstractDTO
{
    private $applyNo;
    private $memoCode;
    private $memoContent;
    private $managerSno;

    public function __construct(int $applyNo, string $memoCode, string $memoContent, int $managerSno)
    {
        $this->applyNo = $applyNo;
        $this->memoCode = $memoCode;
        $this->memoContent = $memoContent;
        $this->managerSno = $managerSno;
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
    public function getMemoCode(): string
    {
        return $this->memoCode;
    }

    /**
     * @return string
     */
    public function getMemoContent(): string
    {
        return $this->memoContent;
    }

    /**
     * @return int
     */
    public function getManagerSno(): int
    {
        return $this->managerSno;
    }
}
