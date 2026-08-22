<?php
/*
 * Copyright (C) 2025 NHN COMMERCE. - All Rights Reserved
 *
 * Unauthorized copying or redistribution of this file in source and binary forms via any medium
 * is strictly prohibited.
 */

namespace Bundle\Repository\Agreement;

use Origin\Model\Agreement\BuyerInform;

class BuyerInformRepository
{
    /**
     * 안내문 정보 조회
     * 
     * @param string $informCd 안내문코드
     * @return array 안내문 정보 배열
     */
    public function findByInformCd(string $informCd): array
    {
        $buyerInform = BuyerInform::query()
            ->where('informCd', $informCd)
            ->first();
        
        return $buyerInform ? $buyerInform->toArray() : [];
    }
}
