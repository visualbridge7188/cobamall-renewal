<?php
/*
 * Copyright (C) 2025 NHN COMMERCE. - All Rights Reserved
 *
 * Unauthorized copying or redistribution of this file in source and binary forms via any medium
 * is strictly prohibited.
 */

namespace Bundle\DTO\RegularDelivery\RegularOrder;

use Origin\DTO\AbstractDTO;

class RegularOrderConsultMemoCreateDTO extends AbstractDTO
{
    private $applyNo;
    private $managerSno;
    private $requestMemo;
    private $consultMemo;

    public function __construct(int $applyNo, int $managerSno, array $data)
    {
        $this->applyNo = $applyNo;
        $this->managerSno = $managerSno;
        $this->requestMemo = $data['requestMemo'];
        $this->consultMemo = $data['consultMemo'];
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
    public function getManagerSno(): int
    {
        return $this->managerSno;
    }

    /**
     * @return string
     */
    public function getRequestMemo(): string
    {
        return $this->requestMemo;
    }

    /**
     * @return string
     */
    public function getConsultMemo(): string
    {
        return $this->consultMemo;
    }
}
