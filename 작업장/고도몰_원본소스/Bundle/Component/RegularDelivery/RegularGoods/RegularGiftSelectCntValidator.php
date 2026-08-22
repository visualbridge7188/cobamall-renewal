<?php
/*
 * Copyright (C) 2026 NHN COMMERCE. - All Rights Reserved
 *
 * Unauthorized copying or redistribution of this file in source and binary forms via any medium
 * is strictly prohibited.
 */

namespace Bundle\Component\RegularDelivery\RegularGoods;

use Component\Gift\GiftSelectCntValidator;
use Framework\Debug\Exception\AlertBackException;
use Repository\RegularDelivery\RegularGoods\RegularGiftPresentInfoRepository;

/**
 * 정기결제(신청/변경) 사은품 선택수량 검증 서비스
 *
 * 제출된 사은품을 지급조건(regularGiftPresentInfoSno)별로 묶어, 서버 권위 selectCnt 와
 * 실제 선택 개수/제출 선택수량을 비교한다. 위반 시 AlertBackException 을 던진다
 */
class RegularGiftSelectCntValidator
{
    public function __construct(
        private readonly RegularGiftPresentInfoRepository $presentInfoRepository
    ) {
    }

    /**
     * 정기결제 사은품 선택수량 검증 (변조 방지)
     *
     * @param array $giftItems 제출된 사은품 데이터 [['giftNo','regularGiftPresentInfoSno','selectCnt'?...], ...]
     * @return void
     * @throws AlertBackException 선택수량 위반 시
     */
    public function assertValid(array $giftItems): void
    {
        if (empty($giftItems)) {
            return;
        }

        // 지급조건(sno)별 선택 개수 / 제출 선택수량 집계
        $snoList = [];
        $selectedCntBySno = [];
        $submittedSelectCntBySno = [];
        foreach ($giftItems as $gift) {
            if (!isset($gift['regularGiftPresentInfoSno'])) {
                continue;
            }
            $sno = (int) $gift['regularGiftPresentInfoSno'];
            $snoList[$sno] = $sno;
            if (isset($gift['selectCnt'])) {
                $submittedSelectCntBySno[$sno] = (int) $gift['selectCnt'];
            }
            if (isset($gift['giftNo']) && $gift['giftNo'] !== '') {
                $selectedCntBySno[$sno] = ($selectedCntBySno[$sno] ?? 0) + 1;
            }
        }

        if (empty($snoList)) {
            return;
        }

        $authInfo = $this->presentInfoRepository->findSelectInfoBySnoList(array_values($snoList));

        foreach ($snoList as $sno) {
            if (!isset($authInfo[$sno])) {
                continue;
            }
            $authSelectCnt = (int) ($authInfo[$sno]['selectCnt'] ?? 0);
            $selectedCnt = (int) ($selectedCntBySno[$sno] ?? 0);
            $submittedSelectCnt = $submittedSelectCntBySno[$sno] ?? null;

            $message = GiftSelectCntValidator::resolveViolationMessage($authSelectCnt, $selectedCnt, $submittedSelectCnt);
            if ($message !== null) {
                throw new AlertBackException($message);
            }
        }
    }
}
