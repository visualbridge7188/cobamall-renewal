<?php
/*
 * Copyright (C) 2025 NHN COMMERCE. - All Rights Reserved
 *
 * Unauthorized copying or redistribution of this file in source and binary forms via any medium
 * is strictly prohibited.
 */

namespace Bundle\DTO\RegularDelivery\RegularOrder;

use Origin\DTO\AbstractDTO;

class RegularOrderAdminMemoDeleteDTO extends AbstractDTO
{
    private $sno;
    private $managerSno;
    private $delFl;

    public function __construct(int $sno, int $managerSno)
    {
        $this->sno = $sno;
        $this->managerSno = $managerSno;
        $this->delFl = 'y';
    }

    /**
     * @return int
     */
    public function getSno(): int
    {
        return $this->sno;
    }

    /**
     * @return int
     */
    public function getManagerSno(): int
    {
        return $this->managerSno;
    }

    /**
     * @return string
     */
    public function getDelFl(): string
    {
        return $this->delFl;
    }
}
