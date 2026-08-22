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
 * @property-read string applyNo
 * @property-read int giftNo
 * @property-read int regularGiftPresentInfoSno
 */
class RegularOrderGiftCreateDTO extends AbstractDTO
{
    private $giftData;

    /**
     * @param array $giftInfo
     */
    public function __construct(array $giftInfo)
    {
        $this->giftData = [];
        foreach ($giftInfo as $gift) {
            $this->giftData[] = [
                'applyNo' => $gift['applyNo'],
                'giftNo' => (int) $gift['giftNo'],
                'regularGiftPresentInfoSno' => (int) $gift['regularGiftPresentInfoSno'],
            ];
        }
    }

    /**
     * @return array
     */
    public function getGiftData(): array
    {
        return $this->giftData;
    }
}
