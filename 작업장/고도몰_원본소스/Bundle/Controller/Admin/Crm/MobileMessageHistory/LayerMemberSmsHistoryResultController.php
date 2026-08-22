<?php
/*
 * Copyright (C) 2026 NHN COMMERCE. - All Rights Reserved
 *
 * Unauthorized copying or redistribution of this file in source and binary forms via any medium
 * is strictly prohibited.
 */

namespace Bundle\Controller\Admin\Crm\MobileMessageHistory;

use Framework\Http\Request;
use Framework\Http\Response;
use Origin\DTO\ApiClient\Commerce\Notification\MemberSmsHistoryDTO;
use Origin\DTO\ApiClient\Commerce\Notification\MemberSmsHistoryListRequestDTO;
use Origin\DTO\ApiClient\Commerce\Notification\MemberSmsHistoryListResponseDTO;
use Origin\DTO\ApiClient\Commerce\Notification\MemberSmsHistorySearchFormDTO;
use Origin\Enum\ApiClient\Notification\SmsSendStatus;
use Origin\Enum\ApiClient\Notification\SmsTriggerType;
use Origin\Service\Crm\Message\SmsHistoryService;

class LayerMemberSmsHistoryResultController extends \Controller\Admin\Controller
{
    public function index()
    {
        try {
            $this->callMenu('crm', 'messageHistory', 'mobileHistoryList');
            $this->setMenuCode('crm', 'messageHistory', 'mobileHistoryList');

            /** @var Request $request */
            $request = \App::getInstance('request');
            $postData = $request->post()->toArray();

            $searchFormDTO = new MemberSmsHistorySearchFormDTO($postData);
            $searchFormArray = $searchFormDTO->toRecursiveArray();
            $preparedData = $this->prepareRequestDataForDto($searchFormArray);
            $requestDTO = new MemberSmsHistoryListRequestDTO($preparedData);

            try {
                /** @var SmsHistoryService $smsHistoryService */
                $smsHistoryService = \App::getInstance(SmsHistoryService::class);
                $smsHistoryListResponseDTO = $smsHistoryService->getMemberSmsHistoryList($requestDTO);
            } catch (\Throwable $e) {
                \Logger::channel('mobileMessage')->error($e->getMessage(), ['requestDTO' => $requestDTO]);
                $smsHistoryListResponseDTO = new MemberSmsHistoryListResponseDTO(['totalCount' => 0, 'contents' => []], $requestDTO->getPage(), $requestDTO->getPageSize());
            }
            $page = $smsHistoryListResponseDTO->getPage();

            $this->setData('searchFormData', $searchFormArray);
            $this->setData('sortType', SmsHistoryService::SORT_LIST);
            $this->setData('smsHistoryList', $this->sanitizeHistoryList($smsHistoryListResponseDTO->getMemberSmsHistoryList()));
            $this->setData('page', $page);
            $this->getView()->setDefine('layout', 'layout_layer.php');
        } catch (\Throwable $throwable) {
            $this->json(['success' => false, 'message' => $throwable->getMessage()], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * DTO 리스트의 문자열 필드에 htmlspecialchars 처리
     *
     * @param MemberSmsHistoryDTO[] $historyList
     * @return MemberSmsHistoryDTO[]
     */
    private function sanitizeHistoryList(array $historyList): array
    {
        foreach ($historyList as $dto) {
            $sendKey = $dto->getSendKey();
            if (!empty($sendKey)) {
                $dto->setSendKey(htmlspecialchars($sendKey, ENT_QUOTES, 'UTF-8'));
            }

            $senderName = $dto->getSenderName();
            if (!empty($senderName)) {
                $dto->setSenderName(htmlspecialchars($senderName, ENT_QUOTES, 'UTF-8'));
            }

            $from = $dto->getFrom();
            if (!empty($from)) {
                $dto->setFrom(htmlspecialchars($from, ENT_QUOTES, 'UTF-8'));
            }

            $content = $dto->getContent();
            if (!empty($content)) {
                $dto->setContent(htmlspecialchars($content, ENT_QUOTES, 'UTF-8'));
            }
        }

        return $historyList;
    }

    private function prepareRequestDataForDto(array $postData): array
    {
        $preparedData = $postData;
        // TODO 정렬 기준 추가를 대비하여 분리
        $sortType = explode(' ', $preparedData['sort'] ?? 'reservedAt desc');
        $preparedData['sortDescending'] = ($sortType[1] === 'desc') ? 'true' : 'false';

        $allTriggerTypes = array_map(fn($e) => $e->name, SmsTriggerType::cases());
        if (!empty($preparedData['triggerTypes']) && !array_diff($preparedData['triggerTypes'], $allTriggerTypes) && !array_diff($allTriggerTypes, $preparedData['triggerTypes'])) {
            unset($preparedData['triggerTypes']);
        }

        $allSendStatuses = array_map(fn($e) => $e->name, SmsSendStatus::availableForMemberHistorySearch());
        if (
            !empty($preparedData['sendStatuses']) &&
            !array_diff($preparedData['sendStatuses'], $allSendStatuses) &&
            !array_diff($allSendStatuses, $preparedData['sendStatuses'])
        ) {
            unset($preparedData['sendStatuses']);
        }

        if (empty($preparedData['keyword'])) {
            unset($preparedData['keyword']);
        }

        if (empty($preparedData['smsType'])) {
            unset($preparedData['smsType']);
        }

        return $preparedData;
    }
}
