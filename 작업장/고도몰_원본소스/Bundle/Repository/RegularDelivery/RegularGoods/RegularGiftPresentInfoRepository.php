<?php
/*
 * Copyright (C) 2025 NHN COMMERCE. - All Rights Reserved
 *
 * Unauthorized copying or redistribution of this file in source and binary forms via any medium
 * is strictly prohibited.
 */

namespace Bundle\Repository\RegularDelivery\RegularGoods;

use Origin\Model\RegularDelivery\RegularGoods\RegularGiftPresentInfo;

class RegularGiftPresentInfoRepository
{
    /**
     * regularGiftPresentInfo insert
     *
     * INSERT INTO es_regularGiftPresentInfo (....) VALUES (....),(....),....
     *
     * @param array $regularGiftPresentInfoList : es_regularGiftPresentInfo에 insert 할 데이터 리스트
     * @return void
     */
    public function insertRegularGiftPresentInfoList(array $regularGiftPresentInfoList)
    {
        RegularGiftPresentInfo::query()
            ->insert($regularGiftPresentInfoList);
    }

    /**
     * regularGiftPresentSno에 해당하는 특정 필드 값 반환
     *
     * SELECT regularGiftPresentSno, conditionStart, conditionEnd, multiGiftNo, selectCnt, giveCnt
     * FROM es_regularGiftPresentInfo
     * WHERE regularGiftPresentSno = ?
     * ORDER BY sno ASC
     *
     * @param int $regularGiftPresentSno 조회할 regularGiftPresentSno 값
     * @return array 조건에 맞는 필드 값 리스트 (없으면 빈 배열 반환)
     */
    public function findRegularGiftPresentToSetFormByRegularGiftPresentSno(int $regularGiftPresentSno): array
    {
        return RegularGiftPresentInfo::query()
            ->where('regularGiftPresentSno', $regularGiftPresentSno)
            ->orderBy('sno', 'asc')
            ->select([
                'regularGiftPresentSno',
                'conditionStart',
                'conditionEnd',
                'multiGiftNo',
                'selectCnt',
                'giveCnt'
            ])
            ->get()
            ->toArray();
    }

    /**
     * regularGiftPresentSno를 기준으로 es_regularGiftPresentInfo 조회
     *
     * SELECT *
     * FROM es_regularGiftPresentInfo
     * WHERE regularGiftPresentSno IN (...)
     * ORDER BY regularGiftPresentSno;
     *
     * @param array $regularGiftPresentSnoList
     * @return array
     */
    public function findRegularGiftPresentInfoByRegularGiftPresentSno(array $regularGiftPresentSnoList): array
    {
        return RegularGiftPresentInfo::whereIn('regularGiftPresentSno', $regularGiftPresentSnoList)
            ->select('regularGiftPresentSno', 'conditionStart', 'conditionEnd', 'multiGiftNo', 'selectCnt', 'giveCnt')
            ->get()
            ->toArray();
    }

    /**
     * sno값을 기준으로 multiGiftNo 조회
     *
     * SELECT multiGiftNo
     * FROM RegularGiftPresentInfo
     * WHERE sno = ?
     *
     * @param int $sno
     * @return mixed
     */
    public function findMultiGiftNoBySno(int $sno){
        return RegularGiftPresentInfo::select('multiGiftNo')
            ->where('sno','=',$sno)
            ->get()
            ->toArray();
    }

    /**
     * sno 리스트로 선택수량(selectCnt)과 사은품 목록(multiGiftNo) 조회 (주문 검증용)
     *
     * SELECT sno, selectCnt, multiGiftNo
     * FROM es_regularGiftPresentInfo
     * WHERE sno IN (...)
     *
     * @param array $snoList 지급조건 sno 리스트
     * @return array sno => ['sno','selectCnt','multiGiftNo'] 형태 맵 (없으면 빈 배열)
     */
    public function findSelectInfoBySnoList(array $snoList): array
    {
        if (empty($snoList)) {
            return [];
        }

        $rows = RegularGiftPresentInfo::query()
            ->whereIn('sno', $snoList)
            ->select(['sno', 'selectCnt', 'multiGiftNo'])
            ->get()
            ->toArray();

        // sno 를 키로 하는 맵으로 변환
        return array_column($rows, null, 'sno');
    }

    /**
     * 지급 조건 번호로 받을 수 있는 사은품 관련 정보 조회
     * SELECT *
     * FROM es_regularGiftPresentInfo
     * WHERE regularGiftPresentSno = ?
     * AND (conditionStart <= ? AND conditionEnd >= ? AND giveCnt > 0) -> $cnt가 0보다 크면 적용
     * LIMIT 1;
     *
     * @param int $regularGiftPresentSno
     * @param int $cnt
     * @return array
     */
    public function findGiftPresentInfoByRegularGiftPresentSno(int $regularGiftPresentSno, int $cnt = 0): array
    {
        $regularGiftPresentInfo = RegularGiftPresentInfo::query()
            ->where('regularGiftPresentSno', $regularGiftPresentSno)
            ->where('conditionStart', '<=', $cnt)
            ->where('conditionEnd', '>=', $cnt)
            ->where('giveCnt', '>', 0)
            ->first();

        if ($regularGiftPresentInfo) {
            return $regularGiftPresentInfo->toArray();
        }

        return [];
    }
}
