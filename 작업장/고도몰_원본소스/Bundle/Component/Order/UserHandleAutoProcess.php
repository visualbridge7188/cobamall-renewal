<?php
/*
 * Copyright (C) 2026 NHN COMMERCE. - All Rights Reserved
 *
 * Unauthorized copying or redistribution of this file in source and binary forms via any medium
 * is strictly prohibited.
 */

namespace Bundle\Component\Order;

use Component\Order\OrderAdmin;
use Origin\Enum\Order\UserHandleFl;
use Origin\Enum\Order\UserHandleMode;
use Repository\Order\OrderUserHandleRepository;

/**
 * 관리자 클레임 접수 시 고객 클레임 신청 건의 자동 승인/거절 처리 클래스
 *
 * 관리자가 주문상세에서 교환/반품/환불을 접수하면, 해당 상품주문번호에
 * 승인/거절 처리되지 않은(userHandleFl='r') 고객 클레임 신청 건이 있는지 조회하여
 *  - 고객 신청 클레임 종류 = 관리자 접수 클레임 종류 → 자동 승인 (userHandleFl='y')
 *  - 고객 신청 클레임 종류 ≠ 관리자 접수 클레임 종류 → 자동 거절 (userHandleFl='n')
 * 처리 후 운영자 메모를 시스템 사유로 저장한다.
 *
 * 이 클래스는 OrderAdmin / OrderAdminNew 양쪽 흐름에서 공통으로 사용된다.
 *
 * @package Bundle\Component\Order
 */
class UserHandleAutoProcess
{
    /** 시스템 자동 처리 매니저 번호 */
    public const SYSTEM_MANAGER_NO = -1;

    /** 결과 배열 키 */
    private const RESULT_APPROVE = 'approve';
    private const RESULT_REJECT  = 'reject';

    public function __construct(
        private readonly OrderUserHandleRepository $orderUserHandleRepository,
        private readonly OrderAdmin $orderAdmin
    ) {}

    /**
     * 관리자 클레임 접수 시 고객 클레임 신청 건의 자동 승인/거절 처리
     *
     * 부작용 최소화를 위해 es_orderUserHandle UPDATE 만 수행하며,
     * 주문상태 변경/SMS 발송/카운팅 동기화 등 부가 처리는 호출하지 않는다.
     * (주문상태 변경은 호출처에서 이미 완료된 상태로 가정)
     *
     * 업데이트는 OrderAdmin::updateUserHandle() 에 위임하여
     * managerNo === -1 시스템 처리 분기 등 기존 정책을 그대로 사용한다.
     *
     * @param string $orderNo 주문번호
     * @param array $orderGoodsSnoList 관리자 클레임 처리된 상품주문번호(es_orderGoods.sno) 배열
     * @param UserHandleMode $adminClaimMode 관리자 접수 클레임 종류
     *
     * @return array 처리 결과 ['approve' => [['userHandleSno'=>X, 'orderGoodsSno'=>Y], ...], 'reject' => [...]]
     */
    public function autoApproveOrReject(string $orderNo, array $orderGoodsSnoList, UserHandleMode $adminClaimMode): array
    {
        $result = [self::RESULT_APPROVE => [], self::RESULT_REJECT => []];

        if (empty($orderNo) || empty($orderGoodsSnoList)) {
            return $result;
        }

        // --- 대상 조회: 승인/거절 처리되지 않은(userHandleFl='r') 고객 클레임 신청 건
        $targetList = $this->orderUserHandleRepository->findPendingUserHandlesForAutoProcess($orderNo, $orderGoodsSnoList);

        if (empty($targetList)) {
            return $result;
        }

        $adminLabel = __($adminClaimMode->label());

        // --- 각 건에 대해 자동 승인/거절 분기 후 업데이트
        foreach ($targetList as $row) {
            $userHandleSno = $row['sno'];
            $isApprove = ($row['userHandleMode'] === $adminClaimMode->value);

            $newUserHandleFl = $isApprove ? UserHandleFl::APPROVE->value : UserHandleFl::REJECT->value;
            $resultKey       = $isApprove ? self::RESULT_APPROVE         : self::RESULT_REJECT;
            $reason = sprintf(
                __('%s 접수에 따른 자동 %s'),
                $adminLabel,
                $isApprove ? __('승인') : __('거절')
            );

            // 업데이트
            $bundleData = [
                'sno'               => $userHandleSno,
                'userHandleFl'      => $newUserHandleFl,
                'adminHandleReason' => $reason,
                'managerNo'         => self::SYSTEM_MANAGER_NO,
            ];
            $updated = $this->orderAdmin->updateUserHandle($bundleData);
            if ($updated !== false) {
                $result[$resultKey][] = [
                    'userHandleSno' => $userHandleSno,
                    'orderGoodsSno' => $row['userHandleGoodsNo'],
                ];
            }
        }

        return $result;
    }
}
