<?php
/*
 * Copyright (C) 2025 NHN COMMERCE. - All Rights Reserved
 *
 * Unauthorized copying or redistribution of this file in source and binary forms via any medium is strictly prohibited.
 */

namespace Bundle\Factory\Present;

use Component\Present\PresentApplyTypeInterface;
use Component\Present\Category\PresentCategory;
use Component\Present\Goods\PresentApplyGoods;

class PresentApplyTypeFactory
{
    public static function create(string $type): PresentApplyTypeInterface
    {
        return match ($type) {
            'category' => \App::getInstance(PresentCategory::class),
            'applyGoods' => \App::getInstance(PresentApplyGoods::class),
            default => throw new \Exception("Unknown type: {$type}")
        };
    }
}
