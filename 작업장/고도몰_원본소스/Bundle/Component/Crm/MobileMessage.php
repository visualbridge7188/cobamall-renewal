<?php
/*
 * Copyright (C) 2025 NHN COMMERCE. - All Rights Reserved
 *
 * Unauthorized copying or redistribution of this file in source and binary forms via any medium
 * is strictly prohibited.
 */

namespace Bundle\Component\Crm;

use Component\Sms\SmsExcelLog;
use Repository\Member\MemberRepository;

class MobileMessage
{
    public function __construct(
        private readonly MemberRepository $memberRepository,
        private readonly SmsExcelLog $smsExcelLog,
    ) {
    }

    /**
     * 알림 대상자 카운트 조회
     * @param ?array $memberNos 회원 번호 배열
     * @param ?array $groupSnos 회원 등급 번호 배열
     * @return array 알림 대상자 카운트 배열 { 'totalCount': 전체 대상자 수, 'rejectCount': 광고성 수신 거부자 수  }
     */
    public function getReceiverCounts(?array $memberNos = [], ?array $groupSnos = []): array
    {
        return $this->memberRepository->findNotificationTargetCount($memberNos ?? [], $groupSnos ?? []);
    }

    /**
     * 엑셀 업로드 대상자 설정
     * @param array $requestData 요청 데이터
     * @return array 엑셀 대상자가 설정된 요청 데이터
     */
    public function setExcelTargets(array $requestData): array
    {
        $uploadKey = $requestData['targetInfo']['uploadedExcelKey'] ?? null;
        if (!$uploadKey) {
            return $requestData;
        }

        $excelLogs = $this->smsExcelLog->getValidationLogByUploadKey($uploadKey);

        $excelTargets = [];
        foreach ($excelLogs as $log) {
            $excelTargets[] = [
                'name' => $log['name'] ?? '',
                'phoneNo' => $log['cellPhone'] ?? ''
            ];
        }
        $requestData['targetInfo']['targetCondition']['excel'] = $excelTargets;

        return $requestData;
    }
}
