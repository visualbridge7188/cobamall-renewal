<?php
/*
 * Copyright (C) 2026 NHN COMMERCE. - All Rights Reserved
 *
 * Unauthorized copying or redistribution of this file in source and binary forms via any medium
 * is strictly prohibited.
 */

namespace Bundle\Component\Gift;

/**
 * 사은품 선택수량(selectCnt) 검증
 *
 * DB/세션/로거 등 부수효과 없이 정수 입력만으로 위반 여부를 판정
 * 일반주문/정기결제 등 각 호출부는 이 메시지를 받아 흐름에 맞는 예외를 던진다
 */
class GiftSelectCntValidator
{
    /**
     * 선택수량 위반 메시지를 반환한다. 위반이 없으면 null.
     *
     *  - 지급조건 선택수량(전체=0)은 검증 제외
     *  - 미선택(0개)은 검증 제외 (사은품 거절 허용)
     *  - 실제 선택 개수 또는 제출된 선택수량(변조)이 지급조건과 다르면 위반
     *
     * @param int      $authSelectCnt      서버(지급조건)의 선택수량. 0이면 전체 지급
     * @param int      $selectedCnt        실제 선택(체크)된 사은품 개수
     * @param int|null $submittedSelectCnt 주문서에서 제출된 선택수량 값(변조 탐지용). null이면 미사용
     * @return string|null 위반 메시지 또는 null
     */
    public static function resolveViolationMessage(int $authSelectCnt, int $selectedCnt, ?int $submittedSelectCnt = null): ?string
    {
        // 전체(0) 또는 미선택은 검증 제외 (전체 지급 / 사은품 거절 허용)
        if ($authSelectCnt <= 0 || $selectedCnt <= 0) {
            return null;
        }

        // 제출된 선택수량이 없으면 실제 선택 개수로 비교
        $submittedSelectCnt ??= $selectedCnt;

        return match (true) {
            // 최대 초과 : 실제 선택 개수 또는 제출된 선택수량(변조)이 지급조건보다 큰 경우
            $selectedCnt > $authSelectCnt || $submittedSelectCnt > $authSelectCnt
                => sprintf(__('사은품은 최대 %s개만 선택하실 수 있습니다.'), $authSelectCnt),
            // 최소 미달 : 실제 선택 개수 또는 제출된 선택수량(변조)이 지급조건보다 작은 경우
            $selectedCnt < $authSelectCnt || $submittedSelectCnt < $authSelectCnt
                => sprintf(__('사은품은 최소 %s개 이상 선택하셔야 합니다.'), $authSelectCnt),
            default => null,
        };
    }
}
