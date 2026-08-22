<?php
/*
 * Copyright (C) 2025 NHN COMMERCE. - All Rights Reserved
 *
 * Unauthorized copying or redistribution of this file in source and binary forms via any medium
 * is strictly prohibited.
 */

namespace Bundle\Repository\RegularDelivery\RegularOrder;

use DTO\RegularDelivery\RegularOrder\RegularOrderAdminMemoCreateDTO;
use DTO\RegularDelivery\RegularOrder\RegularOrderAdminMemoDeleteDTO;
use DTO\RegularDelivery\RegularOrder\RegularOrderAdminMemoUpdateDTO;
use Origin\Model\RegularDelivery\RegularOrder\RegularOrderAdminMemo;

class RegularOrderAdminMemoRepository
{
    /**
     * 관리자 메모 저장
     *
     * INSERT INTO es_regularOrderAdminMemo (`applyNo`, `memoCd`, `content`, `managerSno`)
     * VALUES (?, ?, ?, ?)
     *
     * @param RegularOrderAdminMemoCreateDTO $dto
     * @return void
     */
    public function insertAdminMemo(RegularOrderAdminMemoCreateDTO $dto)
    {
        RegularOrderAdminMemo::query()
            ->insert([
                'applyNo' => $dto->getApplyNo(),
                'memoCd' => $dto->getMemoCode(),
                'content' => $dto->getMemoContent(),
                'managerSno' => $dto->getManagerSno()
            ]);
    }

    /**
     * 관리자 메모 수정
     *
     * UPDATE es_regularOrderAdminMemo SET `memoCd` = ?, `content` = ?, `managerSno` = ? WHERE `sno` = ?
     *
     * @param RegularOrderAdminMemoUpdateDTO $dto
     * @return void
     */
    public function updateAdminMemo(RegularOrderAdminMemoUpdateDTO $dto)
    {
        RegularOrderAdminMemo::query()
            ->where('sno', $dto->getSno())
            ->update([
                'memoCd' => $dto->getMemoCode(),
                'content' => $dto->getMemoContent(),
                'managerSno' => $dto->getManagerSno()
            ]);
    }

    /**
     * 관리자 메모 삭제
     *
     * UPDATE es_regularOrderAdminMemo SET `delFl` = ?, `deleterManagerSno` = ? = ? WHERE `sno` = ?
     * @param RegularOrderAdminMemoDeleteDTO $dto
     * @return void
     */
    public function deleteAdminMemo(RegularOrderAdminMemoDeleteDTO $dto)
    {
        RegularOrderAdminMemo::query()
            ->where('sno', $dto->getSno())
            ->update([
                'delFl' => $dto->getDelFl(),
                'deleterManagerSno' => $dto->getManagerSno()
            ]);
    }

    /**
     * 관리자 메모 조회
     *
     * SELECT es_regularOrderAdminMemo.*, es_manager.managerId, es_manager.managerNm, es_code.itemNm
     * FROM es_regularOrderAdminMemo
     * JOIN es_manager ON es_manager.sno = es_regularOrderAdminMemo.managerSno
     * LEFT JOIN es_code ON es_code.itemCd = es_regularOrderAdminMemo.memoCd
     * WHERE es_regularOrderAdminMemo.applyNo = ? AND es_regularOrderAdminMemo.delFl = 'n'
     * ORDER BY es_regularOrderAdminMemo.regDt DESC
     *
     * @param int $applyNo
     * @param int $page
     * @param int $pageNum
     * @return array
     */
    public function findAdminMemoByApplyNo(int $applyNo, int $page = 0, int $pageNum = 0): array
    {
        $query = RegularOrderAdminMemo::query()
            ->select('es_regularOrderAdminMemo.*', 'es_manager.managerId', 'es_manager.managerNm', 'es_code.itemNm')
            ->join('es_manager', 'es_manager.sno', '=', 'es_regularOrderAdminMemo.managerSno')
            ->leftjoin('es_code', 'es_code.itemCd', '=', 'es_regularOrderAdminMemo.memoCd')
            ->where('es_regularOrderAdminMemo.applyNo', $applyNo)
            ->where('es_regularOrderAdminMemo.delFl', 'n')
            ->orderBy('es_regularOrderAdminMemo.regDt', 'desc');

        if ($page > 0 && $pageNum > 0) {
            $query->skip(($page - 1) * $pageNum)
                ->take($pageNum);
        }
        return $query->get()->toArray();
    }
}
