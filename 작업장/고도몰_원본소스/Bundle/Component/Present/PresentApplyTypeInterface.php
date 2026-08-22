<?php
/*
 * Copyright (C) 2025 NHN COMMERCE. - All Rights Reserved
 *
 * Unauthorized copying or redistribution of this file in source and binary forms via any medium is strictly prohibited.
 */

namespace Bundle\Component\Present;

interface PresentApplyTypeInterface
{
    public function save(array $items): bool;
    public function remove(array $items);
    public function clearExclusiveConfig();
}
