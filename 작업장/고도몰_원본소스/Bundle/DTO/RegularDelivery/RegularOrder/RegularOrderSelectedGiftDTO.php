<?php
/*
 * Copyright (C) 2025 NHN COMMERCE. - All Rights Reserved
 *
 * Unauthorized copying or redistribution of this file in source and binary forms via any medium
 * is strictly prohibited.
 */

namespace Bundle\DTO\RegularDelivery\RegularOrder;

use Origin\DTO\AbstractDTO;

class RegularOrderSelectedGiftDTO extends AbstractDTO
{
    /**
     * @var int
     */
    private $regularGiftPresentInfoSno;
    /**
     * @var string
     */
    private $conditionTitle;
    /**
     * @var array
     */
    private $selectGiftNum;
    /**
     * @var int
     */
    private $selectCount;
    /**
     * @var int
     */
    private $giveCount;

    public function __construct(array $data)
    {
        $this->regularGiftPresentInfoSno = (int)$data['regularGiftPresentInfoSno'];
        $this->conditionTitle = $data['conditionTitle'];
        $this->selectGiftNum = $data['selectGiftNum'];
        $this->selectCount = (int)$data['selectCount'];
        $this->giveCount = (int)$data['giveCount'];
    }

    /**
     * @return int
     */
    public function getRegularGiftPresentInfoSno(): int
    {
        return $this->regularGiftPresentInfoSno;
    }

    /**
     * @return string
     */
    public function getConditionTitle()
    {
        return $this->conditionTitle;
    }

    /**
     * @return array
     */
    public function getSelectGiftNum()
    {
        return $this->selectGiftNum;
    }

    /**
     * @return int
     */
    public function getSelectCount(): int
    {
        return $this->selectCount;
    }

    /**
     * @return int
     */
    public function getGiveCount(): int
    {
        return $this->giveCount;
    }

}
