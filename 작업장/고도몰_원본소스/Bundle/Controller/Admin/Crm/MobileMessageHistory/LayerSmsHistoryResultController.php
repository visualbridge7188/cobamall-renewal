<?php
/*
 * Copyright (C) 2025 NHN COMMERCE. - All Rights Reserved
 *
 * Unauthorized copying or redistribution of this file in source and binary forms via any medium
 * is strictly prohibited.
 */

namespace Bundle\Controller\Admin\Crm\MobileMessageHistory;

use Framework\Http\Request;
use Framework\Http\Response;
use Origin\DTO\ApiClient\Commerce\Notification\SmsHistoryDTO;
use Origin\DTO\ApiClient\Commerce\Notification\SmsHistoryListRequestDTO;
use Origin\DTO\ApiClient\Commerce\Notification\SmsHistoryListResponseDTO;
use Origin\DTO\ApiClient\Commerce\Notification\SmsHistorySearchFormDTO;
use Origin\Enum\ApiClient\Excel\ExcelGenerateMenu;
use Origin\Enum\ApiClient\Notification\SmsSendStatus;
use Origin\Enum\ApiClient\Notification\SmsTriggerType;
use Origin\Service\Crm\Message\SmsHistoryService;

class LayerSmsHistoryResultController extends \Controller\Admin\Controller
{
    public function index()
    {
        try {
            $this->callMenu('crm', 'messageHistory', 'mobileHistoryList');
            $this->setMenuCode('crm', 'messageHistory', 'mobileHistoryList');

            /** @var Request $request */
            $request = \App::getInstance('request');
            $postData = $request->post()->toArray();

            $searchFormDTO = new SmsHistorySearchFormDTO($postData);
            $searchFormArray = $searchFormDTO->toRecursiveArray();
            $preparedData = $this->prepareRequestDataForDto($searchFormArray);
            $requestDTO = new SmsHistoryListRequestDTO($preparedData);

            try {
                /** @var SmsHistoryService $smsHistoryService */
                $smsHistoryService = \App::getInstance(SmsHistoryService::class);
                $smsHistoryListResponseDTO = $smsHistoryService->getSmsHistoryList($requestDTO);
            } catch (\Throwable $e) {
                \Logger::channel('mobileMessage')->error($e->getMessage(), ['requestDTO' => $requestDTO]);
                $smsHistoryListResponseDTO = new SmsHistoryListResponseDTO(['totalCount' => 0, 'contents' => []], $requestDTO->getPage(), $requestDTO->getPageSize());
            }
            $page = $smsHistoryListResponseDTO->getPage();

            $this->setData('searchFormData', $searchFormArray);
            $this->setData('excelSearchQuery', $requestDTO->toRecursiveArray());
            $this->setData('excelMenu', ExcelGenerateMenu::ADMIN_SMS_SEND_RESULT);
            $this->setData('sortType', SmsHistoryService::SORT_LIST);
            $this->setData('smsHistoryList', $this->sanitizeHistoryList($smsHistoryListResponseDTO->getSmsHistoryList()));
            $this->setData('page', $page);
            $this->getView()->setDefine('layout', 'layout_layer.php');
        } catch (\Throwable $e) {
            $this->json(['success' => false, 'message' => $e->getMessage()], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * DTO 리스트의 문자열 필드에 htmlspecialchars 처리
     *
     * @param SmsHistoryDTO[] $historyList
     * @return SmsHistoryDTO[]
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

        $allSendStatuses = array_map(fn($e) => $e->name, SmsSendStatus::availableForSearch());
        if (!empty($preparedData['sendStatuses']) && !array_diff($preparedData['sendStatuses'], $allSendStatuses) && !array_diff($allSendStatuses, $preparedData['sendStatuses'])) {
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
