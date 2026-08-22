<?php
/*
 * Copyright (C) 2025 NHN COMMERCE. - All Rights Reserved
 *
 * Unauthorized copying or redistribution of this file in source and binary forms via any medium
 * is strictly prohibited.
 */

namespace Bundle\Repository\RegularDelivery\RegularOrder;

use DTO\RegularDelivery\RegularOrder\RegularOrderLogDTO;
use Origin\Model\RegularDelivery\RegularOrder\RegularOrderLog;

class RegularOrderLogRepository
{
    /**
     * 변경이력 로그 저장
     *
     * INSERT INTO es_regularOrderLog
     * (applyNo, modifier, modifierNo, modifierIP, actionType, actionDesc, regDt)
     *  VALUES
     *  (?, ?, ?, ?, ?, ?, ?);
     *
     * @param RegularOrderLogDTO $dto
     * @return void
     */
    public function insertRegularOrderLog(RegularOrderLogDTO $dto)
    {
        RegularOrderLog::query()
            ->insert([
                'applyNo' => $dto->getApplyNo(),
                'modifier' => $dto->getModifier(),
                'modifierNo' => $dto->getModifierNo(),
                'modifierIP' => $dto->getModifierIP(),
                'actionType' => $dto->getActionType(),
                'actionDesc' => $dto->getActionDesc(),
                'regDt' => $dto->getRegDt(),
            ]);
    }

    /**
     * 변경이력 로그
     *
     * SELECT es_regularOrderLog.*, es_manager.managerId
     * FROM es_regularOrderLog
     * LEFT JOIN es_manager ON es_regularOrderLog.modifierNo = es_manager.sno AND es_regularOrderLog.modifier = 'admin'
     * WHERE es_regularOrderLog.applyNo = ?
     * ORDER BY es_regularOrderLog.regDt DESC;
     *
     * @param int $applyNo
     * @return array|bool
     */
    public function findRegularOrderLogByApplyNo(int $applyNo): array
    {
        return RegularOrderLog::query()
            ->select('es_regularOrderLog.*', 'es_manager.managerId')
                ->leftJoin('es_manager', function ($join) {
                    $join->on('es_regularOrderLog.modifierNo', '=', 'es_manager.sno')
                        ->where('es_regularOrderLog.modifier', '=', 'admin');
                })
            ->where('es_regularOrderLog.applyNo', $applyNo)
            ->orderBy('es_regularOrderLog.regDt', 'desc')
            ->get()
            ->toArray();
    }
}
