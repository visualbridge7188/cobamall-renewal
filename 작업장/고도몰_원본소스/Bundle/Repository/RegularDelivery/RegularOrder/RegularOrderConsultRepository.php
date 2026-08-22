<?php
/*
 * Copyright (C) 2025 NHN COMMERCE. - All Rights Reserved
 *
 * Unauthorized copying or redistribution of this file in source and binary forms via any medium
 * is strictly prohibited.
 */

namespace Bundle\Repository\RegularDelivery\RegularOrder;

use DTO\RegularDelivery\RegularOrder\RegularOrderConsultMemoCreateDTO;
use DTO\RegularDelivery\RegularOrder\RegularOrderConsultMemoUpdateDTO;
use Origin\Model\RegularDelivery\RegularOrder\RegularOrderConsult;

class RegularOrderConsultRepository
{
    /**
     * 요청/상담 메모 조회
     *
     * SELECT es_regularOrderConsult.*, es_manager.managerId, es_manager.managerNm
     * FROM es_regularOrderConsult
     * JOIN es_manager on es_manager.sno = es_regularOrderConsult.managerNo
     * WHERE es_regularOrderConsult.applyNo = ?
     * ORDER BY es_regularOrderConsult.regDt DESC
     *
     * @param int $applyNo
     * @return array
     */
    public function findRegularOrderConsultByApplyNo(int $applyNo): array
    {
        return RegularOrderConsult::query()
            ->select([
                'es_regularOrderConsult.*', 'es_manager.managerId', 'es_manager.managerNm'
            ])
            ->join('es_manager', 'es_manager.sno', '=', 'es_regularOrderConsult.managerNo')
            ->where('es_regularOrderConsult.applyNo', $applyNo)
            ->orderBy('es_regularOrderConsult.regDt', 'desc')
            ->get()
            ->toArray();
    }

    /**
     * 상담메모 저장
     *
     * INSERT INTO es_regularOrderConsult(applyNo, managerNo, requestMemo, consultMemo, regDt)
     * VALUES (?, ?, ?, ?, ?)
     *
     * @param RegularOrderConsultMemoCreateDTO $dto
     * @return void
     */
    public function insertRegularOrderConsultMemo(RegularOrderConsultMemoCreateDTO $dto)
    {
        RegularOrderConsult::query()
            ->insert([
                'applyNo' => $dto->getApplyNo(),
                'managerNo' => $dto->getManagerSno(),
                'requestMemo' => $dto->getRequestMemo(),
                'consultMemo' => $dto->getConsultMemo(),
                'regDt' => date('Y-m-d H:i:s'),
            ]);
    }

    /**
     * 상담메모 업데이트
     *
     * UPDATE es_regularOrderConsult SET managerNo = ?, requestMemo = ?, consultMemo = ?, modDt = ? WHERE sno = ?
     * @param RegularOrderConsultMemoUpdateDTO $dto
     * @return void
     */
    public function updateRegularOrderConsultMemoBySno(RegularOrderConsultMemoUpdateDTO $dto)
    {
        RegularOrderConsult::query()
            ->where('sno', $dto->getSno())
            ->update([
                'managerNo' => $dto->getManagerSno(),
                'requestMemo' => $dto->getRequestMemo(),
                'consultMemo' => $dto->getConsultMemo(),
                'modDt' => date('Y-m-d H:i:s'),
            ]);
    }

    /**
     * 상담메모 삭제
     * DELETE FROM es_regularOrderConsult WHERE sno = ?
     *
     * @param int $sno
     * @return void
     */
    public function deleteRegularOrderConsultBySno(int $sno)
    {
        RegularOrderConsult::query()
            ->where('sno', $sno)
            ->delete();
    }
}
