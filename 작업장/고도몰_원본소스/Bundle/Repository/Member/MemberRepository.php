<?php
/*
 * Copyright (C) 2025 NHN COMMERCE. - All Rights Reserved
 *
 * Unauthorized copying or redistribution of this file in source and binary forms via any medium
 * is strictly prohibited.
 */

namespace Bundle\Repository\Member;

use Origin\Model\Member\Member;

/**
 * MemberRepository Class
 * @package Bundle\Repository\Member
 */
class MemberRepository
{
    /**
     * 회원 정보 조회
     *
     * SELECT * FROM es_member WHERE memNo = ?
     *
     * @param int $memNo
     * @return array
     */
    public function findMemberInfoByMemNo(int $memNo): array
    {
        $member = Member::query()
            ->where('memNo', $memNo)
            ->first();

        if ($member) {
            return $member->toArray();
        }

        return [];
    }

    /**
     * 알림 대상자 전체 카운트 조회 (전체, 거부)
     * @param array $memberNos 회원 번호 배열
     * @param array $groupSnos 회원 등급 번호 배열
     * @return array
     */
    public function findNotificationTargetCount(array $memberNos = [], array $groupSnos = []): array
    {
        $query = Member::query()
            ->selectRaw("
                COUNT(*) AS totalCount,
                COUNT(IF(smsFl = 'n', smsFl, NULL)) AS rejectCount
            ")
            ->whereNotNull('cellPhone')
            ->where('cellPhone', '!=', '')
            ->where('sleepFl', '!=', 'y');

        if (!empty($memberNos)) {
            $query->whereIn('memNo', $memberNos);
        }

        if (!empty($groupSnos)) {
            $query->whereIn('groupSno', $groupSnos);
        }

        $counts = $query->first();

        return $counts ? $counts->toArray() : [];
    }
}
