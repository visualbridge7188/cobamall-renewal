<?php
/*
 * Copyright (C) 2025 NHN COMMERCE. - All Rights Reserved
 *
 * Unauthorized copying or redistribution of this file in source and binary forms via any medium
 * is strictly prohibited.
 */

namespace Bundle\Repository\RegularDelivery\RegularOrder;

use DTO\RegularDelivery\RegularOrder\RegularOrderAddGoodsCreateDTO;
use Origin\Model\RegularDelivery\RegularOrder\RegularOrderAddGoods;

class RegularOrderAddGoodsRepository
{
    /**
     * 추가상품 정보 저장 bulk insert
     *
     * INSERT INTO es_regularOrderAddGoods(applyNo, regularAddGoodsNo, regularAddGoodsPrice, regularAddGoodsCnt, addGoodsOptionName)
     *  VALUES (?, ?, ?, ?, ?)
     *  VALUES (?, ?, ?, ?, ?)
     *  VALUES (?, ?, ?, ?, ?)
     *  ....
     *
     * @param RegularOrderAddGoodsCreateDTO $dto
     * @return void
     */
    public function insertRegularOrderAddGoods(RegularOrderAddGoodsCreateDTO $dto)
    {
        RegularOrderAddGoods::query()
            ->insert($dto->getAddGoodsData());
    }

    /**
     * 정기결제 추가상품 조회
     *
     * SELECT es_addGoods.*, es_regularOrderAddGoods.regularAddGoodsCnt, es_regularOrderAddGoods.regularAddGoodsPrice,
     * es_regularOrderAddGoods.addGoodsOptionName, es_scmManage.companyNm
     * FROM es_regularOrderAddGoods
     * JOIN es_addGoods on es_addGoods.addGoodsNo = es_regularOrderAddGoods.regularAddGoodsNo
     * JOIN es_scmManage on es_scmManage.scmNo = es_addGoods.scmNo
     * WHERE es_regularOrderAddGoods.applyNo = ?
     *
     * @param int $applyNo
     * @return array
     */
    public function findRegularOrderAddGoodsByApplyNo(int $applyNo): array
    {
        return RegularOrderAddGoods::query()
            ->select([
                'es_addGoods.*',
                'es_regularOrderAddGoods.*',
                'es_scmManage.companyNm'
            ])
            ->join('es_addGoods', 'es_addGoods.addGoodsNo', '=', 'es_regularOrderAddGoods.regularAddGoodsNo')
            ->join('es_scmManage', 'es_scmManage.scmNo', '=', 'es_addGoods.scmNo')
            ->where('es_regularOrderAddGoods.applyNo', $applyNo)
            ->get()
            ->toArray();
    }

    /**
     * 추가상품 번호 조회
     *
     * SELECT regularAddGoodsNo
     * FROM es_regularOrderAddGoods
     * WHERE applyNo = ?
     *
     * @param int $applyNo
     * @return int|null
     */
    public function findRegularAddGoodsNoByApplyNo(int $applyNo)
    {
        return RegularOrderAddGoods::query()
        ->where('applyNo', '=', $applyNo)
        ->value('regularAddGoodsNo');
    }


    /**
     * 신청번호로 추가상품 정보 조회
     *
     * @param int $applyNo
     * @return array
     */
    public function findAddGoodsInfoByApplyNo(int $applyNo): array
    {
        return RegularOrderAddGoods::query()
            ->where('applyNo', '=', $applyNo)
            ->get()
            ->toArray();
    }

    /**
     * 신청서 번호로 추가상품 삭제
     *
     * DELETE FROM es_regularOrderAddGoods WHERE applyNo = ?
     *
     * @param int $applyNo
     * @return void
     */
    public function deleteRegularOrderAddGoodsByApplyNo(int $applyNo)
    {
        RegularOrderAddGoods::query()
            ->where('applyNo', '=', $applyNo)
            ->delete();
    }

    /**
     * 신청서 번호에 해당하는 추가상품 수량 조회
     *
     * SELECT COUNT(*) AS cnt FROM es_regularOrderAddGoods WHERE applyNo = ?
     *
     * @param int $applyNo
     * @return int
     */
    public function countRegularOrderAddGoodsByApplyNo(int $applyNo): int
    {
        return RegularOrderAddGoods::query()
            ->where('applyNo', '=', $applyNo)
            ->count();
    }

    /**
     * 신청번호 리스트로 추가상품 수량 조회
     *
     * SELECT COUNT(*) AS cnt FROM es_regularOrderAddGoods WHERE applyNo IN (?)
     *
     * @param array $applyNoList
     * @return int
     */
    public function countRegularOrderAddGoodsByApplyNoList(array $applyNoList): int
    {
        return RegularOrderAddGoods::query()
            ->whereIn('applyNo', $applyNoList)
            ->count();
    }

    /**
     * regularAddGoodsNo를 기준으로 applyStatus 조회
     *
     * SELECT applyNo
     * FROM es_regularOrderAddGoods
     * WHERE regularAddGoodsNo = ?
     * AND es_regularOrderAddGoods.inactiveDt IS NULL;
     *
     * @param int $regularAddGoodsNo : 조회할 데이터의 regularAddGoodsNo
     * @return array
     */
    public function findApplyNoApplyStatusListByRegularAddGoodsNo(int $regularAddGoodsNo): array
    {
        return RegularOrderAddGoods::query()
            ->select('es_regularOrderAddGoods.applyNo', 'es_regularOrderGoods.applyStatus')
            ->join('es_regularOrderGoods', 'es_regularOrderAddGoods.applyNo', '=', 'es_regularOrderGoods.applyNo')
            ->where('regularAddGoodsNo', $regularAddGoodsNo)
            ->whereNull('inactiveDt')
            ->get()
            ->toArray();
    }
}
