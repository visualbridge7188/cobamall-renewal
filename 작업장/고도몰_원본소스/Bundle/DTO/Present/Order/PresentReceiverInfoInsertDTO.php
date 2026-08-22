<?php
/*
 * Copyright (C) 2025 NHN COMMERCE. - All Rights Reserved
 *
 * Unauthorized copying or redistribution of this file in source and binary forms via any medium
 * is strictly prohibited.
 */

namespace Bundle\DTO\Present\Order;

use Origin\DTO\AbstractDTO;
use Component\Present\Confirm\PresentConfirm;

/**
 * 선물하기 수령자 정보 데이터 저장용 DTO
 */
class PresentReceiverInfoInsertDTO extends AbstractDTO
{
    /**
     * @param string $orderNo 주문번호
     * @param int $orderGoodsNo 주문상품번호
     * @param string $receiverName 수령자 이름
     * @param string|null $phone 전화번호
     * @param string $cellPhone 휴대폰번호
     * @param string|null $address 주소
     * @param string|null $addressSub 상세주소
     * @param string|null $zipcode 우편번호
     * @param string|null $orderMemo 배송메시지
     * @param string $acceptFl 수락여부
     * @param string|null $confirmToken 확인토큰
     * @param string $sendSmsFl SMS 전송 여부
     * @param string|null $sendDt SMS 전송일시
     * @param string|null $regDt 등록일
     * @param string|null $modDt 수정일
     */
    public function __construct(
        public readonly string $orderNo,
        public readonly int $orderGoodsNo,
        public readonly string $receiverName = '',
        public readonly ?string $phone = null,
        public readonly string $cellPhone = '',
        public readonly ?string $address = null,
        public readonly ?string $addressSub = null,
        public readonly ?string $zipcode = null,
        public readonly ?string $orderMemo = null,
        public readonly string $acceptFl = PresentConfirm::PRESENT_ACCEPT_READY,
        public readonly ?string $confirmToken = null,
        public readonly string $sendSmsFl = 'n',
        public readonly ?string $sendDt = null,
        public readonly ?string $regDt = null,
        public readonly ?string $modDt = null
    ) {
    }

}

