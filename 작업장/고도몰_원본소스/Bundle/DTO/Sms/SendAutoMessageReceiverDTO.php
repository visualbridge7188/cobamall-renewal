<?php
/*
 * Copyright (C) 2025 NHN COMMERCE. - All Rights Reserved
 *
 * Unauthorized copying or redistribution of this file in source and binary forms via any medium
 * is strictly prohibited.
 */

namespace Bundle\DTO\Sms;

use Origin\DTO\AbstractDTO;

class SendAutoMessageReceiverDTO extends AbstractDTO
{
    /**
     * @var array|null $receiverInfo 수신자 정보
     */
    protected array|null $receiverInfo = null;

    /**
     * @var array|null $recipientInfo 수령자 정보
     */
    protected array|null $recipientInfo = null;

    /**
     * @var array $replaceArguments 치환값 배열
     */
    protected array $replaceArguments = [];

    /**
     * @param array|null $receiverInfo
     * @param array|null $recipientInfo
     * @param array $replaceArguments 치환값 배열
     */
    public function __construct(
        array|null $receiverInfo,
        array|null $recipientInfo,
        array      $replaceArguments = []
    )
    {
        if($receiverInfo != null) {
            $this->receiverInfo = [
                'cellPhone' => $receiverInfo['cellPhone'],
                'memNo'     => $receiverInfo['memberNo'] ?? 0,
                'memNm'     => $receiverInfo['memberName'] ?? "",
                'smsFl'     => $receiverInfo['smsFl'] ?? "y",
                'scmNo'     => $receiverInfo['scmNo'] ?? DEFAULT_CODE_SCMNO,
            ];
        }
        if($recipientInfo != null) {
            $this->recipientInfo = [
                'cellPhone' => $recipientInfo['cellPhone'],
                'memNo'     => $recipientInfo['memberNo'] ?? 0,
                'memNm'     => $recipientInfo['memberName'] ?? "",
                'smsFl'     => $recipientInfo['smsFl'] ?? "y",
                'scmNo'     => $recipientInfo['scmNo'] ?? DEFAULT_CODE_SCMNO,
            ];
        }
        $this->replaceArguments = $replaceArguments;
    }

    /**
     * @return array|null
     */
    public function getReceiverInfo(): array|null
    {
        return $this->receiverInfo;
    }

    /**
     * @param array $infos 추가할 수신자 정보 배열
     * @return array
     */
    public function appendReceiverInfo(array $infos): array
    {
        foreach ($infos as $key => $value) {
            $this->receiverInfo[$key] = $value;
        }
        return $this->receiverInfo;
    }

    /**
     * @return array|null
     */
    public function getRecipientInfo(): array|null
    {
        return $this->recipientInfo;
    }

    /**
     * @param array $infos 추가할 수령자 정보 배열
     * @return array
     */
    public function appendRecipientInfo(array $infos): array
    {
        foreach ($infos as $key => $value) {
            $this->recipientInfo[$key] = $value;
        }
        return $this->recipientInfo;
    }


    /**
     * @return array
     */
    public function getReplaceArguments(): array
    {
        return $this->replaceArguments;
    }

    /**
     * @param array $arguments 추가할 치환값 배열
     * @return array
     */
    public function appendReplaceArguments(array $arguments): array
    {
        foreach ($arguments as $key => $value) {
            $this->replaceArguments[$key] = $value;
        }

        return $this->replaceArguments;
    }
}



