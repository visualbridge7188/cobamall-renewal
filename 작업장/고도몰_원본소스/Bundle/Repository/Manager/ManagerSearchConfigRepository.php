<?php
/*
 * Copyright (C) 2025 NHN COMMERCE. - All Rights Reserved
 *
 * Unauthorized copying or redistribution of this file in source and binary forms via any medium
 * is strictly prohibited.
 */

namespace Bundle\Repository\Manager;

use Origin\Model\Manager\ManagerSearchConfig;

class ManagerSearchConfigRepository
{

    /**
     * applyNo를 바탕으로 전체 데이터 조회
     *
     * SELECT * FROM es_managerSearchConfig
     * WHERE applyPath = ?;
     *
     * @param string $applyPath
     * @return array
     */
    public function findManagerSearchConfigByApplyPath(string $applyPath): array
    {
        $managerSearchConfig = ManagerSearchConfig::where('applyPath', '=', $applyPath)
            ->first();
        return $managerSearchConfig ? $managerSearchConfig->toArray() : [];
    }
}
