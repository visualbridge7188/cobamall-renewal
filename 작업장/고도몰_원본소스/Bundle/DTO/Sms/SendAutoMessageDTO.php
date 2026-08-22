<?php
/*
 * Copyright (C) 2025 NHN COMMERCE. - All Rights Reserved
 *
 * Unauthorized copying or redistribution of this file in source and binary forms via any medium
 * is strictly prohibited.
 */

namespace Bundle\DTO\Sms;

use DTO\Sms\SendAutoMessageReceiverDTO;
use Origin\DTO\AbstractDTO;

class SendAutoMessageDTO extends AbstractDTO
{
    /**
     * SmsAutoCode.php 참조 (ORDER, MEMBER, PROMOTION, ADMIN, BOARD, PRESENT ...)
     * @var string $type 자동 알림 발송 타입
     * @Required
     */
    protected string $type;

    /**
     * Code.php 참조 (ORDER, INCASH, ACCOUNT ...)
     * @var string $code 자동 알림 발송 코드
     * @Required
     */
    protected string $code;

    /**
     * @var SendAutoMessageReceiverDTO[] $receivers 알림 수신자 데이터 배열
     * @Required
     */
    protected array $receivers;

    /**
     * @param string $type
     * @param string $code
     * @param SendAutoMessageReceiverDTO[] $receivers
     */
    public function __construct(
        string $type,
        string $code,
        array $receivers,
    )
    {
        $this->type = $type;
        $this->code = $code;
        $this->receivers = $receivers;
    }

    /**
     * @return string
     */
    public function getType(): string
    {
        return $this->type;
    }

    /**
     * @return string
     */
    public function getCode(): string
    {
        return $this->code;
    }

    /**
     * @return SendAutoMessageReceiverDTO[]
     */
    public function getReceivers(): array
    {
        return $this->receivers;
    }

}



