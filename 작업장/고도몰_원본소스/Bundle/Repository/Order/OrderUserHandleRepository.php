<?php
/*
 * Copyright (C) 2026 NHN COMMERCE. - All Rights Reserved
 *
 * Unauthorized copying or redistribution of this file in source and binary forms via any medium
 * is strictly prohibited.
 */

namespace Bundle\Repository\Order;

use Origin\Enum\Order\UserHandleFl;
use Origin\Enum\Order\UserHandleMode;
use Origin\Model\Order\OrderUserHandle;

class OrderUserHandleRepository
{
    /**
     * 자동 승인/거절 대상이 되는 고객 클레임 신청 건 조회
     *
     * SELECT sno, userHandleMode, userHandleGoodsNo
     * FROM es_orderUserHandle
     * WHERE orderNo = ?
     *   AND userHandleGoodsNo IN (...)
     *   AND userHandleFl = 'r'
     *   AND userHandleMode IN (...)
     *
     * @param string $orderNo 주문번호
     * @param int[] $orderGoodsSnoList userHandleGoodsNo (es_orderGoods.sno) 목록
     * @return array<int, array{sno:int, userHandleMode:string, userHandleGoodsNo:int}>
     */
    public function findPendingUserHandlesForAutoProcess(string $orderNo, array $orderGoodsSnoList): array
    {
        if (empty($orderNo) || empty($orderGoodsSnoList)) {
            return [];
        }

        $allowedModes = array_map(
            static fn(UserHandleMode $m) => $m->value,
            UserHandleMode::cases()
        );

        return OrderUserHandle::query()
            ->select(['sno', 'userHandleMode', 'userHandleGoodsNo'])
            ->where('orderNo', $orderNo)
            ->whereIn('userHandleGoodsNo', array_map('intval', $orderGoodsSnoList))
            ->where('userHandleFl', UserHandleFl::REQUEST->value)
            ->whereIn('userHandleMode', $allowedModes)
            ->get()
            ->toArray();
    }

    /**
     * 주어진 클레임 신청 sno 목록 중 이미 승인/거절 처리된(userHandleFl != 'r') 건이 존재하는지 확인
     *
     * 승인/거절 처리 직전 현재 상태를 재확인하여, stale 화면(목록 로드 이후 다른 관리자/
     * 자동환불 등으로 상태가 변경된 경우)에서의 중복 처리를 차단하기 위해 사용한다.
     *
     * @param int[] $userHandleSnoList es_orderUserHandle.sno 목록
     * @return bool 하나라도 신청(미처리, 'r') 이 아닌 건이 있으면 true
     */
    public function existsAlreadyProcessed(array $userHandleSnoList): bool
    {
        return OrderUserHandle::query()
            ->whereIn('sno', array_map('intval', $userHandleSnoList))
            ->where('userHandleFl', '!=', UserHandleFl::REQUEST->value)
            ->exists();
    }
}
