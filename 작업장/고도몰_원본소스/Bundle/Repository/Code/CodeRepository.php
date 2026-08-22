<?php
/*
 * Copyright (C) 2025 NHN COMMERCE. - All Rights Reserved
 *
 * Unauthorized copying or redistribution of this file in source and binary forms via any medium
 * is strictly prohibited.
 */

namespace Bundle\Repository\Code;

use Origin\Model\Code\Code;

class CodeRepository
{
    const ADMIN_MEMO_GROUP_CODE = '04004';

    /**
     * 정기배송 해지 사유 코드 조회
     * 
     * SELECT itemCd, itemNm 
     * FROM code 
     * WHERE groupCd = 04005 AND useFl = 'y'
     * ORDER BY sort
     *
     * @param int $groupCd
     * @return array
     */
    public function findRegularDeliveryCancelReasonNames(int $groupCd): array
    {
        return Code::query()
            ->select('itemCd', 'itemNm')
            ->where('groupCd', $groupCd)
            ->where('useFl', 'y')
            ->orderBy('sort')
            ->get()
            ->toArray();
    }

    /**
     * 관리자 메모 코드 조회
     *
     * SELECT itemCd, itemNm FROM es_code WHERE groupCd = '04004' AND useFl = 'y'
     *
     * @return array
     */
    public function findAdminMemoCode(): array
    {
        return Code::query()
            ->select('itemCd', 'itemNm')
            ->where('groupCd', self::ADMIN_MEMO_GROUP_CODE)
            ->where('useFl', 'y')
            ->get()
            ->toArray();
    }
}
