<?php
/*
 * Copyright (C) 2025 NHN COMMERCE. - All Rights Reserved
 *
 * Unauthorized copying or redistribution of this file in source and binary forms via any medium is strictly prohibited.
 */

namespace Bundle\Factory\Present;

use Component\Present\PresentGoodsInterface;
use Component\Present\Goods\PresentApplyGoods;
use Component\Present\Goods\PresentExceptGoods;

class PresentGoodsFactory
{
    public static function create(string $type): PresentGoodsInterface
    {
        return match ($type) {
            'exceptGoods' => \App::getInstance(PresentExceptGoods::class),
            'applyGoods' => \App::getInstance(PresentApplyGoods::class),
            default => throw new \Exception("Unknown type: {$type}")
        };
    }
}
