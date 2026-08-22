<?php

namespace Bundle\DTO\Present\Cart;

use Carbon\Carbon;

/**
 * 선물하기 Insert 전용 DTO
 * es_presentCart 테이블의 실제 구조를 정확히 반영
 */
class PresentCartInsertDTO
{
    /**
     * @param int $cartSno 장바구니 번호
     * @param string $receiverName 수령자 이름
     * @param string $cellPhone 수령자 전화번호
     * @param int $cardSno 카드 일련번호
     * @param string $cardMessage 카드 메시지
     * @param string $regDt 등록일시
     */
    public function __construct(
        public readonly int $cartSno,
        public readonly string $receiverName,
        public readonly string $cellPhone,
        public readonly int $cardSno,
        public readonly string $cardMessage,
        public readonly string $regDt
    ) {
    }

    /**
     * DTO를 DB 저장용 배열로 변환
     * 
     * @return array DB 저장용 배열
     */
    public function toDbArray(): array
    {
        return [
            'cartSno' => $this->cartSno,
            'receiverName' => $this->receiverName,
            'cellPhone' => $this->cellPhone,
            'cardSno' => $this->cardSno,
            'cardMessage' => $this->cardMessage,
            'regDt' => $this->regDt
        ];
    }

    /**
     * Insert DTO 생성
     */
    public static function createForInsert(
        int $cartSno,
        string $receiverName,
        string $cellPhone,
        int $cardSno,
        string $cardMessage
    ): static {
        return new static(
            cartSno: $cartSno,
            receiverName: $receiverName,
            cellPhone: $cellPhone,
            cardSno: $cardSno,
            cardMessage: $cardMessage,
            regDt: Carbon::now()
        );
    }
}
