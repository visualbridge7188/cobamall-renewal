<?php
/*
 * Copyright (C) 2026 NHN COMMERCE. - All Rights Reserved
 *
 * Unauthorized copying or redistribution of this file in source and binary forms via any medium
 * is strictly prohibited.
 */

namespace Bundle\Component\Order;

use Repository\Order\OrderUserHandleRepository;

/**
 * 고객 클레임(교환/반품/환불) 신청 건의 승인/거절 처리 전 상태 검증 가드
 *
 * 이미 승인(userHandleFl='y') 또는 거절(userHandleFl='n') 처리된 신청 건에 대해
 * 중복 승인/거절이 발생하지 않도록, 처리 직전 현재 상태를 재확인한다.
 *
 * 목록 화면 로드 이후 다른 관리자/자동환불 등으로 상태가 바뀐 stale 상황에서
 * 승인/거절을 시도하면, 환불 완료 등 이미 종결된 건이 다시 신청 상태로 되돌아가
 * 환불 재시도가 불가해지는 문제(고객센터 문의 인입)를 방지한다.
 *
 * @package Bundle\Component\Order
 */
class UserHandleProcessGuard
{
    public function __construct(
        private readonly OrderUserHandleRepository $orderUserHandleRepository
    ) {}

    /**
     * statusCheck 항목들 중 이미 승인/거절 처리된(userHandleFl != 'r') 클레임이 있는지 여부
     *
     * @param array $statusCheckList 체크박스 value 목록
     *                               (orderNo||orderGoodsSno||userHandleSno||cnt||originCnt)
     * @return bool 하나라도 이미 처리된 건이 있으면 true
     */
    public function hasAlreadyProcessed(array $statusCheckList): bool
    {
        $userHandleSnoList = $this->extractUserHandleSnoList($statusCheckList);
        if (empty($userHandleSnoList)) {
            return false;
        }

        return $this->orderUserHandleRepository->existsAlreadyProcessed($userHandleSnoList);
    }

    /**
     * statusCheck value 목록에서 클레임 신청 sno(es_orderUserHandle.sno) 목록을 추출한다.
     *
     * statusCheck value 포맷: orderNo || orderGoodsSno || userHandleSno || cnt || originCnt
     * (INT_DIVISION 구분, layout_order_goods_list.php 의 checkBoxCd 생성 규칙과 동일)
     *
     * @param array $statusCheckList
     * @return int[]
     */
    public function extractUserHandleSnoList(array $statusCheckList): array
    {
        $snoList = [];
        foreach ($statusCheckList as $statusCheckValue) {
            // 포맷: orderNo || orderGoodsSno || userHandleSno || cnt || originCnt (index 2 = userHandleSno)
            $columns = explode(INT_DIVISION, (string) $statusCheckValue);
            if (empty($columns[2])) {
                continue;
            }
            $snoList[] = (int) $columns[2];
        }

        return array_values(array_unique(array_filter($snoList)));
    }
}
