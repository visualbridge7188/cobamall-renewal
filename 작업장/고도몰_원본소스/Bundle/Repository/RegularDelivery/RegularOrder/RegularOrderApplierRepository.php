<?php
/*
 * Copyright (C) 2025 NHN COMMERCE. - All Rights Reserved
 *
 * Unauthorized copying or redistribution of this file in source and binary forms via any medium
 * is strictly prohibited.
 */

namespace Bundle\Repository\RegularDelivery\RegularOrder;

use DTO\RegularDelivery\RegularOrder\RegularOrderApplierCreateDTO;
use DTO\RegularDelivery\RegularOrder\RegularOrderApplierUpdateDTO;
use Origin\Model\RegularDelivery\RegularOrder\RegularOrderApplier;

class RegularOrderApplierRepository
{
    /**
     * 신청자 정보 저장
     *
     * INSERT INTO es_regularOrderApplier(applyGroupNo, applierName, applierPhone, applierCellPhone, applierEmail, applierIp, regDt)
     *  VALUES (?, ?, ?, ?, ?, ?, ?)
     *
     * @param RegularOrderApplierCreateDTO $regularOrderApplierCreateDTO
     * @return void
     */
    public function insertRegularOrderApplier(RegularOrderApplierCreateDTO $regularOrderApplierCreateDTO)
    {
        RegularOrderApplier::query()
            ->insert([
                'applyGroupNo' => $regularOrderApplierCreateDTO->getApplyGroupNo(),
                'applierName' => $regularOrderApplierCreateDTO->getApplierName(),
                'applierPhone' => $regularOrderApplierCreateDTO->getApplierPhone(),
                'applierCellPhone' => $regularOrderApplierCreateDTO->getApplierCellPhone(),
                'applierEmail' => $regularOrderApplierCreateDTO->getApplierEmail(),
                'applierIp' => $regularOrderApplierCreateDTO->getApplierIp(),
                'regDt' => $regularOrderApplierCreateDTO->getRegDt(),
            ]);

    }

    /**
     * 신청서 신청자 정보 수정
     *
     * UPDATE es_regularOrderApplier
     * JOIN es_regularOrder ON es_regularOrder.applyGroupNo = es_regularOrderGoods.applyGroupNo
     * JOIN es_regularOrderGoods ON es_regularOrderGoods.applyGroupNo = es_regularOrder.applyGroupNo
     * SET applierName = ?, applierPhone = ?, applierCellPhone = ?, applierEmail = ?
     * WHERE es_regularOrderGoods.applyNo = ?
     *
     * @param RegularOrderApplierUpdateDTO $dto
     * @return void
     */
    public function updateRegularOrderApplierByApplyNo(RegularOrderApplierUpdateDTO $dto)
    {
        RegularOrderApplier::query()
            ->join('es_regularOrder', 'es_regularOrder.applyGroupNo', '=', 'es_regularOrderApplier.applyGroupNo')
            ->join('es_regularOrderGoods', 'es_regularOrderGoods.applyGroupNo', '=', 'es_regularOrder.applyGroupNo')
            ->where('es_regularOrderGoods.applyNo', $dto->getApplyNo())
            ->update([
                'applierName' => $dto->getApplierName(),
                'applierPhone' => $dto->getApplierPhone(),
                'applierCellPhone' => $dto->getApplierCellPhone(),
                'applierEmail' => $dto->getApplierEmail(),
            ]);
    }

    /**
     * 신청자 정보 조회
     *
     * SELECT * from es_regularOrderApplier WHERE applyGroupNo = ?
     *
     * @param string $applyGroupNo
     * @return array
     */
    public function findApplierInfoByApplyGroupNo(string $applyGroupNo): array
    {
        $applierInfo = RegularOrderApplier::query()
            ->where('applyGroupNo', $applyGroupNo)
            ->first();

        return $applierInfo ? $applierInfo->toArray() : [];
    }

    /**
     * 신청번호로 신청자 정보 조회
     *
     * SELECT es_regularOrderApplier.*, es_member.memId, es_member.memNo, es_member.smsFl, es_memberGroup.groupNm
     * FROM es_regularOrderApplier
     * JOIN es_regularOrderGoods ON es_regularOrderGoods.applyGroupNo = es_regularOrderApplier.applyGroupNo
     * JOIN es_regularOrder ON es_regularOrder.applyGroupNo = es_regularOrderGoods.applyGroupNo
     * JOIN es_member ON es_member.memNo = es_regularOrder.memNo
     * JOIN es_memberGroup ON es_member.groupSno = es_memberGroup.sno
     * WHERE es_regularOrderGoods.applyNo = ?
     *
     * @param int $applyNo
     * @return array
     */
    public function findApplierInfoByApplyNo(int $applyNo): array
    {
        $applierInfo = RegularOrderApplier::query()
            ->select([
                'es_regularOrderApplier.*',
                'es_member.memId',
                'es_member.memNo',
                'es_member.smsFl',
                'es_memberGroup.groupNm'
            ])
            ->join('es_regularOrderGoods', 'es_regularOrderGoods.applyGroupNo', '=', 'es_regularOrderApplier.applyGroupNo')
            ->join('es_regularOrder', 'es_regularOrder.applyGroupNo', '=', 'es_regularOrderGoods.applyGroupNo')
            ->leftJoin('es_member', 'es_member.memNo', '=', 'es_regularOrder.memNo')
            ->leftJoin('es_memberGroup', 'es_member.groupSno', '=', 'es_memberGroup.sno')
            ->where('es_regularOrderGoods.applyNo', $applyNo)
            ->first();

        return $applierInfo ? $applierInfo->toArray() : [];
    }

    /**
     * 정기결제용 사은품 조건 정보 조회
     *
     * SELECT * from es_regularGiftPresentInfo
     * JOIN es_regularGiftPresent ON es_regularGiftPresentInfo.regularGiftPresentSno = es_regularGiftPresent.sno
     * WHERE es_regularGiftPresentInfo.sno = ?
     *
     * @param int $regularGiftPresentInfoSno
     * @return array
     */
    public function findRegularGiftPresentInfoByPresentInfoSno(int $regularGiftPresentInfoSno): array
    {
        $regularGiftPresentInfo = RegularGiftPresentInfo::query()
            ->join('es_regularGiftPresent', 'es_regularGiftPresentInfo.regularGiftPresentSno', '=', 'es_regularGiftPresent.sno')
            ->where('es_regularGiftPresentInfo.sno', $regularGiftPresentInfoSno)
            ->first();

        return $regularGiftPresentInfo ? $regularGiftPresentInfo->toArray() : [];
    }
}
