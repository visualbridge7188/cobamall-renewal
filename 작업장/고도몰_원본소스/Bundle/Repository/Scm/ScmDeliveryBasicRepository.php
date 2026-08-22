<?php
/*
 * Copyright (C) 2025 NHN COMMERCE. - All Rights Reserved
 *
 * Unauthorized copying or redistribution of this file in source and binary forms via any medium
 * is strictly prohibited.
 */

namespace Bundle\Repository\Scm;

use Origin\Model\Scm\ScmDeliveryBasic;

class ScmDeliveryBasicRepository
{
    /**
     * 배송 정책 조회
     *
     * SELECT es_scmDeliveryBasic.*, es_scmManage.scmCommissionDelivery, es_scmManage.scmCommission
     * JOIN es_scmManage ON es_scmManage.scmNo = es_scmDeliveryBasic.scmNo
     * FROM es_scmDeliveryBasic
     * WHERE sno = ?
     *
     * @param int $sno
     * @return array
     */
    public function findScmDeliveryBasicInfoBySno(int $sno): array
    {
        $deliveryBasic = ScmDeliveryBasic::query()
            ->select([
                'es_scmDeliveryBasic.*',
                'es_scmManage.scmCommissionDelivery',
                'es_scmManage.scmCommission',
            ])
            ->join('es_scmManage', 'es_scmManage.scmNo', '=', 'es_scmDeliveryBasic.scmNo')
            ->where('es_scmDeliveryBasic.sno', $sno)
            ->first();

        return $deliveryBasic ? $deliveryBasic->toArray() : [];
    }

    /**
     * 배송 정책 별 가격 조회
     *
     * SELECT es_scmDeliveryCharge.method, es_scmDeliveryCharge.unitStart, es_scmDeliveryCharge.unitEnd, es_scmDeliveryCharge.price, es_scmDeliveryCharge.message
     *  FROM es_scmDeliveryBasic
     *  JOIN es_scmDeliveryCharge ON es_scmDeliveryCharge.basicKey = es_scmDeliveryBasic.sno
     *  WHERE es_scmDeliveryBasic.sno = ?
     *
     * @param int $sno
     * @return array
     */
    public function findScmDeliveryChargeBySno(int $sno): array
    {
        return ScmDeliveryBasic::query()
            ->select([
                'es_scmDeliveryCharge.method', 'es_scmDeliveryCharge.unitStart', 'es_scmDeliveryCharge.unitEnd', 'es_scmDeliveryCharge.price', 'es_scmDeliveryCharge.message',
            ])
            ->join('es_scmDeliveryCharge', 'es_scmDeliveryCharge.basicKey', '=', 'es_scmDeliveryBasic.sno')
            ->where('es_scmDeliveryBasic.sno', $sno)
            ->get()
            ->toArray();
    }

    /**
     * 지역별 배송비 리스트 조회
     *
     * SELECT
     * es_scmDeliveryArea.sno, es_scmDeliveryArea.regDt, es_scmDeliveryArea.scmNo, es_scmDeliveryArea.basicKey,
     * es_scmDeliveryArea.addPrice, es_scmDeliveryArea.addArea, es_scmDeliveryArea.addAreaCode
     * FROM es_scmDeliveryBasic
     * JOIN es_scmDeliveryArea ON es_scmDeliveryArea.basicKey = es_scmDeliveryBasic.areaGroupNo
     * WHERE es_scmDeliveryBasic.sno = ?
     *
     * @param int $sno
     * @return array
     */
    public function findScmDeliveryAreaBySno(int $sno): array
    {
        return ScmDeliveryBasic::query()
            ->select([
                'es_scmDeliveryArea.sno', 'es_scmDeliveryArea.regDt', 'es_scmDeliveryArea.scmNo', 'es_scmDeliveryArea.basicKey', 'es_scmDeliveryArea.addPrice',
                'es_scmDeliveryArea.addArea', 'es_scmDeliveryArea.addAreaCode'
            ])
            ->join('es_scmDeliveryArea', 'es_scmDeliveryArea.basicKey', '=', 'es_scmDeliveryBasic.areaGroupNo')
            ->where('es_scmDeliveryBasic.sno', $sno)
            ->get()
            ->toArray();
    }

    /**
     * snoList에 속한 배송 정책 중 전체 무료 배송 항목이 있는지 확인
     *
     * SELECT EXISTS (
     * SELECT 1
     * FROM es_scmDeliveryBasic
     * WHERE fixFl = 'free'
     * AND freeFl = 'y'
     * AND sno IN (101, 102, 103)
     * ) AS exists;
     *
     * @param array $snoList
     * @return array
     */
    public function findFreeDeliveryBySnoList(array $snoList): bool
    {
        return ScmDeliveryBasic::query()
            ->where('fixFl', 'free')
            ->where('freeFl', 'y')
            ->whereIn('sno', $snoList)
            ->exists();
    }

    /**
     * snoList에 속하는 배송 정책 조회
     *
     * SELECT es_scmDeliveryBasic.*
     * FROM es_scmDeliveryBasic
     * WHERE es_scmDeliveryBasic.sno IN ( ... );
     *
     * @param array $snoList
     * @return array
     */
    public function findScmDeliveryInfoBySnoList(array $snoList): array
    {
        return ScmDeliveryBasic::query()
            ->select(['es_scmDeliveryBasic.*'])
            ->whereIn('es_scmDeliveryBasic.sno', $snoList)
            ->get()
            ->toArray();
    }

}
