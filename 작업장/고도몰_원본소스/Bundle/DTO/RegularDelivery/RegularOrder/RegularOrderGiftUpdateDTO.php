<?php
/*
 * Copyright (C) 2025 NHN COMMERCE. - All Rights Reserved
 *
 * Unauthorized copying or redistribution of this file in source and binary forms via any medium
 * is strictly prohibited.
 */

namespace Bundle\DTO\RegularDelivery\RegularOrder;

use Origin\DTO\AbstractDTO;

/**
 * 정기결제 신청서 사은품 변경 DTO
 */
class RegularOrderGiftUpdateDTO extends AbstractDTO
{
    /**
     * @var int
     */
    private $applyNo;
    /**
     * @var int
     */
    private $selectCount;
    /**
     * @var int
     */
    private $regularGiftPresentInfoSno;
    /**
     * @var array
     */
    private $giftNoList;
    /**
     * @var string
     */
    private $sessionType;
    /**
     * @var int
     */
    private $sessionSno;

    public function __construct(array $data)
    {
        $this->applyNo = (int)$data['applyNo'];
        $this->selectCount = (int)$data['selectCount'];
        $this->regularGiftPresentInfoSno = (int)$data['regularGiftPresentInfoSno'];
        $this->giftNoList = $data['giftNoList'];
        $this->sessionType = $data['sessionType'];
        $this->sessionSno = (int) $data['sessionSno'];
    }

    /**
     * @return int
     */
    public function getApplyNo()
    {
        return $this->applyNo;
    }

    /**
     * @return int
     */
    public function getSelectCount()
    {
        return $this->selectCount;
    }

    /**
     * @return int
     */
    public function getRegularGiftPresentInfoSno()
    {
        return $this->regularGiftPresentInfoSno;
    }

    /**
     * @return array
     */
    public function getGiftNoList()
    {
        return $this->giftNoList;
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
