<?php
/*
 * Copyright (C) 2025 NHN COMMERCE. - All Rights Reserved
 *
 * Unauthorized copying or redistribution of this file in source and binary forms via any medium
 * is strictly prohibited.
 */

namespace Bundle\Repository\RegularDelivery\RegularGoods;

use Origin\Model\RegularDelivery\RegularGoods\RegularGiftPresent;

class RegularGiftPresentRepository
{
    /**
     * regularGiftPresent insert
     *
     * INSERT INTO es_regularGiftPresent (....) VALUES (....),(....),....
     *
     * @param array $regularGiftPresentList : es_regularGiftPresent에 insert할 데이터 모음
     * @return void
     */
    public function insertRegularGiftPresentList(array $regularGiftPresentList)
    {
        RegularGiftPresent::query()
            ->insert($regularGiftPresentList);
    }

    /**
     * regularGoodsSno를 기준으로 Sno를 조회
     *
     * SELECT sno
     * FROM es_regularGiftPresent
     * WHERE regularGoodsSno IN (?, ?, ?, ?...)
     * AND delFl = 'n'
     * ORDER BY sno ASC;
     *
     * @param array $regularGoodsSnoList : fk인 regularGoodsSno의 리스트
     * @return array : 조회된  sno의 리스트
     */
    public function findSnoByRegularGoodsSnoList(array $regularGoodsSnoList): array
    {
        return RegularGiftPresent::query()
            ->whereIn('regularGoodsSno', $regularGoodsSnoList)
            ->where('delFl', 'n')
            ->orderBy('sno', 'asc')
            ->pluck('sno')
            ->toArray();
    }

    /**
     * UPDATE es_regularGiftPresent
     *  SET delFl = 'T'
     *  WHERE regularGoodsSno = ?
     *  AND delFl = 'n';
     *
     * @param int $regularGoodsSno : 소프트 딜리트를 할 데이터의 regularGoodsSno
     */
    public function updateDelFlByRegularGoodsSno(int $regularGoodsSno)
    {
        return RegularGiftPresent::where('regularGoodsSno', $regularGoodsSno)
            ->where('delFl', 'n')
            ->update(['delFl' => 'y',
                'modDt' => date("Y-m-d H:i:s")]);
    }

    /**
     * 상품에 해당하는 사은품 지급 조건 조회
     *
     * SELECT *
     * FROM es_regularGiftPresent
     * WHERE regularGoodsSno = ? AND delFl = 'n'
     * AND (if (periodUseFl = 'y', (startDate <= ? AND endDate >= ?), periodUseFl = 'n'))
     * ORDER BY sno DESC;
     *
     * @param int $regularGoodsNo
     * @return array
     */
    public function findGiftPresentInfoByRegularGoodsNo(int $regularGoodsNo): array
    {
        $regularGift = RegularGiftPresent::query()
            ->where('regularGoodsSno', '=', $regularGoodsNo)
            ->where('delFl', 'n')
            ->whereRaw("if (periodUseFl = 'y', (startDate <= ? AND endDate >= ?), periodUseFl = 'n')", [date('Y-m-d H:i:s'), date('Y-m-d H:i:s')])
            ->orderBy('sno', 'DESC')
            ->first();

        if ($regularGift) {
            return $regularGift->toArray();
        }

        return [];
    }

    /**
     * SELECT
     * es_regularGiftPresent.conditionType, es_regularGiftPresent.addGoodsFl,
     * es_regularGiftPresentInfo.conditionStart, es_regularGiftPresentInfo.conditionEnd, es_regularGiftPresentInfo.multiGiftNo,
     * es_regularGiftPresentInfo.selectCnt, es_regularGiftPresentInfo.giveCnt
     * FROM es_regularGiftPresent
     * INNER JOIN es_regularGiftPresentInfo ON es_regularGiftPresent.sno = es_regularGiftPresentInfo.regularGiftPresentSno
     * WHERE regularGiftPresentSno = ?
     *
     * @param int $sno : es_regularGiftPresentSno
     * @return array
     */
    public function findRegularGiftPresentConditionTypeAndInfo(int $sno) : array
    {
        return RegularGiftPresent::query()
            ->select('es_regularGiftPresent.conditionTitle', 'es_regularGiftPresent.conditionType', 'es_regularGiftPresent.addGoodsFl',
                'es_regularGiftPresentInfo.sno', 'es_regularGiftPresentInfo.conditionStart', 'es_regularGiftPresentInfo.conditionEnd',
                'es_regularGiftPresentInfo.multiGiftNo', 'es_regularGiftPresentInfo.selectCnt', 'es_regularGiftPresentInfo.giveCnt'
            )
            ->where('regularGiftPresentSno', '=', $sno)
            ->join('es_regularGiftPresentInfo', 'es_regularGiftPresent.sno', '=', 'es_regularGiftPresentInfo.regularGiftPresentSno')
            ->get()
            ->toArray();
    }

    /**
     * 정기결제용 사은품 조건 정보 조회
     *
     * SELECT *
     *  FROM es_regularGiftPresent
     *  JOIN es_regularGiftPresentInfo on es_regularGiftPresentInfo.regularGiftPresentSno = es_regularGiftPresent.sno
     *  WHERE es_regularGiftPresent.sno = ?
     *
     * @param int $regularGiftPresentSno
     * @return array
     */
    public function findRegularGiftPresentInfoByPresentSno(int $regularGiftPresentSno): array
    {
        $regularGiftPresentInfo = RegularGiftPresent::query()
            ->join('es_regularGiftPresentInfo', 'es_regularGiftPresentInfo.regularGiftPresentSno', '=', 'es_regularGiftPresent.sno')
            ->where('es_regularGiftPresent.sno', $regularGiftPresentSno)
            ->first();

        return $regularGiftPresentInfo ? $regularGiftPresentInfo->toArray() : [];
    }
}
