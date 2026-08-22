<?php
/*
 * Copyright (C) 2025 NHN COMMERCE. - All Rights Reserved
 *
 * Unauthorized copying or redistribution of this file in source and binary forms via any medium
 * is strictly prohibited.
 */

namespace Bundle\Repository\RegularDelivery\RegularOrder;

use DTO\RegularDelivery\RegularOrder\RegularOrderGiftCreateDTO;
use Origin\Model\RegularDelivery\RegularOrder\RegularOrderGift;

class RegularOrderGiftRepository
{
    /**
     * 사은품 정보 bulk insert
     *
     * INSERT INTO es_regularOrderGift(applyNo, giftNo, regularGiftPresentInfoSno)
     *  VALUES (?,?,?)
     *  VALUES (?,?,?)
     *  VALUES (?,?,?)
     *  ...
     *
     * @param RegularOrderGiftCreateDTO $dto
     * @return bool
     */
    public function insertRegularOrderGift(RegularOrderGiftCreateDTO $dto): bool
    {
        return RegularOrderGift::query()
            ->insert($dto->getGiftData());
    }

    /**
     * 정기결제 신청 관련 사은품 정보 조회
     *
     * SELECT *
     * FROM es_regularOrderGift
     * JOIN es_regularGiftPresentInfo ON es_regularOrderGift.regularGiftPresentInfoSno = es_regularGiftPresentInfo.sno
     * JOIN es_regularGiftPresent ON es_regularGiftPresentInfo.regularGiftPresentSno = es_regularGiftPresent.sno
     * JOIN es_gift ON es_gift.giftNo = es_regularOrderGift.giftNo
     * WHERE es_regularOrderGift.applyNo = ?
     *
     * @param int $applyNo
     * @return array
     */
    public function findGiftInfoByApplyNo(int $applyNo): array
    {
        return RegularOrderGift::query()
            ->join('es_regularGiftPresentInfo', 'es_regularOrderGift.regularGiftPresentInfoSno', '=', 'es_regularGiftPresentInfo.sno')
            ->join('es_regularGiftPresent', 'es_regularGiftPresentInfo.regularGiftPresentSno', '=', 'es_regularGiftPresent.sno')
            ->join('es_gift', 'es_gift.giftNo', '=', 'es_regularOrderGift.giftNo')
            ->where('es_regularOrderGift.applyNo', $applyNo)
            ->get()
            ->toArray();
    }

    /**
     * 정기결제 신청 관련 사은품 정보 조회
     *
     * SELECT *
     * FROM es_regularOrderGift
     * JOIN es_regularGiftPresentInfo ON es_regularOrderGift.regularGiftPresentInfoSno = es_regularGiftPresentInfo.sno
     * JOIN es_regularGiftPresent ON es_regularGiftPresentInfo.regularGiftPresentSno = es_regularGiftPresent.sno
     * JOIN es_gift ON es_gift.giftNo = es_regularOrderGift.giftNo
     * WHERE es_regularOrderGift.applyNo IN (?)
     *
     * @param array $applyNoList
     * @return array
     */
    public function findGiftInfoByApplyNoList(array $applyNoList): array
    {
        return RegularOrderGift::query()
            ->join('es_regularGiftPresentInfo', 'es_regularOrderGift.regularGiftPresentInfoSno', '=', 'es_regularGiftPresentInfo.sno')
            ->join('es_regularGiftPresent', 'es_regularGiftPresentInfo.regularGiftPresentSno', '=', 'es_regularGiftPresent.sno')
            ->join('es_gift', 'es_gift.giftNo', '=', 'es_regularOrderGift.giftNo')
            ->whereIn('es_regularOrderGift.applyNo', $applyNoList)
            ->get()
            ->toArray();
    }

    /**
     * 신청서 번호로 정기결제 신청 사은품 삭제
     *
     * DELETE FROM es_regularOrderGift WHERE applyNo = ?
     *
     * @param int $applyNo
     * @return void
     */
    public function deleteRegularOrderGiftByApplyNo(int $applyNo)
    {
        RegularOrderGift::query()
            ->where('applyNo', $applyNo)
            ->delete();
    }

    /**
     * 신청서 번호를 통해 관련 사은품 번호 및 사은품명 조회
     *
     * SELECT es_regularOrderGift.giftNo, es_gift.giftNm
     * FROM es_regularOrderGift
     * INNER JOIN es_gift ON es_gift.giftNo = es_regularOrderGift.giftNo
     * WHERE es_regularOrderGift.applyNo = ?;
     *
     * @param int $applyNo : 신청서 번호
     * @return array
     */
    public function findGiftNameAndGiftNoByApplyNo(int $applyNo): array
    {
        return RegularOrderGift::query()
            ->select('es_regularOrderGift.giftNo', 'es_gift.giftNm')
            ->join('es_gift', 'es_gift.giftNo', '=', 'es_regularOrderGift.giftNo')
            ->where('es_regularOrderGift.applyNo', $applyNo)
            ->get()
            ->toArray();
    }

    /**
     * 신청서와 관련된 사은품 설정 및 사은품 정보 조회
     *
     * SELECT
     * es_regularOrderGift.regularGiftPresentSno,
     * es_regularOrderGoods.regularGoodsCnt,
     * es_regularOrderAddGoods.regularAddGoodsCnt,
     * es_regularOrderAddGoods.regularAddGoodsNo
     * FROM es_regularOrderGift
     * INNER JOIN es_regularOrderGoods ON es_regularOrderGift.applyNo = es_regularOrderGoods.applyNo
     * LEFT JOIN es_regularOrderAddGoods ON es_regularOrderGift.applyNo = es_regularOrderAddGoods.applyNo
     * WHERE es_regularOrderGift.applyNo = ?;
     *
     * @param int $applyNo : 신청서 번호
     * @return array
     */
    public function findRegularGiftPresentSnoByApplyNo(int $applyNo): array
    {
        $regularGiftPresentSno = RegularOrderGift::query()
            ->select([
                'es_regularGiftPresentInfo.regularGiftPresentSno',
                'es_regularOrderGoods.regularGoodsCnt',
                'es_regularOrderAddGoods.regularAddGoodsCnt',
                'es_regularOrderAddGoods.regularAddGoodsNo'
            ])
            ->join('es_regularGiftPresentInfo', 'es_regularOrderGift.regularGiftPresentInfoSno', '=', 'es_regularGiftPresentInfo.sno')
            ->join('es_regularOrderGoods', 'es_regularOrderGift.applyNo', '=', 'es_regularOrderGoods.applyNo')
            ->leftJoin('es_regularOrderAddGoods', 'es_regularOrderGift.applyNo', '=', 'es_regularOrderAddGoods.applyNo') // 추가상품 조인
            ->where('es_regularOrderGift.applyNo', $applyNo)
            ->get();

        return $regularGiftPresentSno ? $regularGiftPresentSno->toArray() : [];
    }
}
