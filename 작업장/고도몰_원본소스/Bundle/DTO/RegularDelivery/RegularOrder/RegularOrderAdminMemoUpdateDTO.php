<?php
/*
 * Copyright (C) 2025 NHN COMMERCE. - All Rights Reserved
 *
 * Unauthorized copying or redistribution of this file in source and binary forms via any medium
 * is strictly prohibited.
 */

namespace Bundle\DTO\RegularDelivery\RegularOrder;

use Origin\DTO\AbstractDTO;

class RegularOrderAdminMemoUpdateDTO extends AbstractDTO
{
    private $sno;
    private $memoCode;
    private $memoContent;
    private $managerSno;

    public function __construct(int $sno, string $memoCode, string $memoContent, int $managerSno)
    {
        $this->sno = $sno;
        $this->memoCode = $memoCode;
        $this->memoContent = $memoContent;
        $this->managerSno = $managerSno;
    }

    /**
     * @return int
     */
    public function getSno(): int
    {
        return $this->sno;
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
