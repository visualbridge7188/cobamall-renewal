<?php
/*
 * Copyright (C) 2025 NHN COMMERCE. - All Rights Reserved
 *
 * Unauthorized copying or redistribution of this file in source and binary forms via any medium
 * is strictly prohibited.
 */

namespace Bundle\Repository\Delivery;

use Origin\Model\Delivery\ManageDeliveryCompany;

class ManageDeliveryCompanyRepository
{
    /**
     * 배송업체 정보 조회
     *
     * SELECT *
     *  FROM es_manageDeliveryCompany
     *  WHERE sno = ?
     *
     * @param int $sno
     * @return array
     */
    public function findManageDeliveryCompanyBySno(int $sno): array
    {
        $manageDeliveryCompany = ManageDeliveryCompany::query()
            ->where('sno', $sno)
            ->first();

        if ($manageDeliveryCompany) {
            return $manageDeliveryCompany->toArray();
        }

        return [];
    }
}
