<?php
/*
 * Copyright (C) 2025 NHN COMMERCE. - All Rights Reserved
 *
 * Unauthorized copying or redistribution of this file in source and binary forms via any medium
 * is strictly prohibited.
 */

namespace Bundle\Repository\RegularDelivery\RegularOrder;

use DTO\RegularDelivery\RegularOrder\RegularOrderStatusLogDTO;
use Origin\Model\RegularDelivery\RegularOrder\RegularOrderStatusLog;

class RegularOrderStatusLogRepository
{
    /**
     * 이용상태 변경 로그 저장
     *
     * INSERT INTO es_regularOrderStatusLog
     * (applyNo, modifier, modifierNo, modifierIP, prevOrderStatus, changeOrderStatus, description, regDt)
     * VALUES
     * (?, ?, ?, ?, ?, ?, ?, ?);
     *
     * @param RegularOrderStatusLogDTO $dto
     * @return void
     */
    public function insertRegularOrderStatusLog(RegularOrderStatusLogDTO $dto)
    {
        RegularOrderStatusLog::query()
            ->insert([
                'applyNo' => $dto->getApplyNo(),
                'modifier' => $dto->getModifier(),
                'modifierNo' => $dto->getModifierNo(),
                'modifierIP' => $dto->getModifierIP(),
                'prevOrderStatus' => $dto->getPrevOrderStatus(),
                'changeOrderStatus' => $dto->getChangeOrderStatus(),
                'description' => $dto->getDescription(),
                'regDt' => $dto->getRegDt(),
            ]);
    }

    /**
     * 이용상태 변경 로그 벌크 저장
     *
     *  INSERT INTO es_regularOrderStatusLog
     *  (applyGroupNo, modifier, modifierNo, modifierIP, prevOrderStatus, changeOrderStatus, description, regDt)
     *  VALUES
     *  (?, ?, ?, ?, ?, ?, ?, ?),
     *  (?, ?, ?, ?, ?, ?, ?, ?),
     *  ...
     *
     * @param RegularOrderStatusLogDTO[] $statusLogDtoList
     * @return void
     */
    public function bulkInsertRegularOrderStatusLog(array $statusLogDtoList)
    {
        $insertStatusLog = [];
        foreach ($statusLogDtoList as $dto) {
            $insertStatusLog[] = [
                'applyNo' => $dto->getApplyNo(),
                'modifier' => $dto->getModifier(),
                'modifierNo' => $dto->getModifierNo(),
                'modifierIP' => $dto->getModifierIP(),
                'prevOrderStatus' => $dto->getPrevOrderStatus(),
                'changeOrderStatus' => $dto->getChangeOrderStatus(),
                'description' => $dto->getDescription(),
                'regDt' => $dto->getRegDt(),
            ];
        }

        RegularOrderStatusLog::query()
            ->insert($insertStatusLog);
    }

    /**
     * 이용상태 변경 로그
     *
     * SELECT es_regularOrderStatusLog.*, es_manager.managerId
     * FROM es_regularOrderStatusLog
     * LEFT JOIN es_manager ON es_regularOrderStatusLog.modifierNo = es_manager.sno AND es_regularOrderStatusLog.modifier = 'admin'
     * WHERE es_regularOrderStatusLog.applyNo = ?
     * ORDER BY es_regularOrderStatusLog.regDt DESC;
     *
     * @param int $applyNo
     * @return array
     */
    public function findRegularOrderStatusLogByApplyNo(int $applyNo): array
    {
        return RegularOrderStatusLog::query()
            ->select('es_regularOrderStatusLog.*', 'es_manager.managerId')
            ->leftJoin('es_manager', function ($join) {
                $join->on('es_regularOrderStatusLog.modifierNo', '=', 'es_manager.sno')
                    ->where('es_regularOrderStatusLog.modifier', '=', 'admin');
            })
            ->where('es_regularOrderStatusLog.applyNo', $applyNo)
            ->orderBy('es_regularOrderStatusLog.regDt', 'desc')
            ->get()
            ->toArray();
    }

    /**
     * 해지 사유 조회
     *
     * SELECT description
     * FROM es_regularOrderStatusLog
     * WHERE applyNo = ?
     * ORDER BY regDt DESC
     * LIMIT 1;
     *
     * @param int $applyNo
     * @return string
     */
    public function findCancelReasonByApplyNo(int $applyNo): string
    {
        $cancelReason = RegularOrderStatusLog::query()
            ->where('applyNo', $applyNo)
            ->orderBy('regDt', 'desc')
            ->value('description');

        return $cancelReason ?? '';
    }
}
