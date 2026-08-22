<?php
/*
 * Copyright (C) 2025 NHN COMMERCE. - All Rights Reserved
 *
 * Unauthorized copying or redistribution of this file in source and binary forms via any medium
 * is strictly prohibited.
 */

namespace Bundle\DTO\Present\Order;

use Origin\DTO\AbstractDTO;
use Carbon\Carbon;

/**
 * 선물하기 주문 카드 정보 데이터 저장용 DTO
 */
class PresentOrderCardInsertDTO extends AbstractDTO
{
    /**
     * @param string $orderNo 주문번호
     * @param int $cardSno 카드 일련번호
     * @param string|null $cardMessage 카드 메시지
     * @param string $regDt 등록일
     * @param string|null $modDt 수정일
     */
    public function __construct(
        public readonly string $orderNo,
        public readonly int $cardSno,
        public readonly ?string $cardMessage = null,
        public readonly string $regDt,
        public readonly ?string $modDt = null
    ) {
    }

    /**
     * Insert DTO 생성
     */
    public static function createForInsert(
        string $orderNo,
        int $cardSno,
        ?string $cardMessage = null
    ): static {
        return new static(
            orderNo: $orderNo,
            cardSno: $cardSno,
            cardMessage: $cardMessage,
            regDt: Carbon::now(),
            modDt: null
        );
    }
}

