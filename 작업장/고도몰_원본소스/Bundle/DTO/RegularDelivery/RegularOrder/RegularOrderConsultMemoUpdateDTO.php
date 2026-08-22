<?php
/*
 * Copyright (C) 2025 NHN COMMERCE. - All Rights Reserved
 *
 * Unauthorized copying or redistribution of this file in source and binary forms via any medium
 * is strictly prohibited.
 */

namespace Bundle\DTO\RegularDelivery\RegularOrder;

use Origin\DTO\AbstractDTO;

class RegularOrderConsultMemoUpdateDTO extends AbstractDTO
{
    private $sno;
    private $managerSno;
    private $requestMemo;
    private $consultMemo;

    public function __construct(int $sno, int $managerSno, array $data)
    {
        $this->sno = $sno;
        $this->managerSno = $managerSno;
        $this->requestMemo = $data['requestMemo'];
        $this->consultMemo = $data['consultMemo'];
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
