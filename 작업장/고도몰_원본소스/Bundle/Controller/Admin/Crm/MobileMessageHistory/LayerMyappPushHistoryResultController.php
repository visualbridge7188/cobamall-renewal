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
use Origin\DTO\ApiClient\Commerce\Apps\MyappPushHistoryDTO;
use Origin\DTO\ApiClient\Commerce\Apps\MyappPushHistoryListRequestDTO;
use Origin\DTO\ApiClient\Commerce\Apps\MyappPushHistoryListResponseDTO;
use Origin\DTO\ApiClient\Commerce\Apps\MyappPushHistorySearchFormDTO;
use Origin\Enum\ApiClient\Apps\MyappPushResultDirection;
use Origin\Enum\ApiClient\Apps\MyappPushResultSortType;
use Origin\Enum\ApiClient\Apps\MyappPushScheduleType;
use Origin\Enum\ApiClient\Apps\MyappPushSendStatus;
use Origin\Service\Crm\Message\MyappPushHistoryService;
use Origin\Service\Crm\Message\MyappPushSettingService;

class LayerMyappPushHistoryResultController extends \Controller\Admin\Controller
{
    public function index()
    {
        try {
            $this->callMenu('crm', 'messageHistory', 'mobileHistoryList');
            $this->setMenuCode('crm', 'messageHistory', 'mobileHistoryList');

            /** @var Request $request */
            $request = \App::getInstance('request');
            $postData = $request->post()->toArray();

            $searchFormDTO = new MyappPushHistorySearchFormDTO($postData);
            $searchFormArray = $searchFormDTO->toRecursiveArray();
            $preparedData = $this->prepareRequestDataForDto($searchFormArray);
            $requestDTO = new MyappPushHistoryListRequestDTO($preparedData);

            /** @var MyappPushSettingService $myappPushSettingService */
            $myappPushSettingService = \App::getInstance(MyappPushSettingService::class);
            $myappPushConfig = $myappPushSettingService->getMyappPushConfig();
            $isEnabled = $myappPushConfig->getUseFlag() === 'y';

            try {
                /** @var MyappPushHistoryService $myappPushHistoryService */
                $myappPushHistoryService = \App::getInstance(MyappPushHistoryService::class);
                $myappPushHistoryListResponseDTO = $myappPushHistoryService->getMyappPushHistoryList($requestDTO);
            } catch (\Throwable $e) {
                \Logger::channel('mobileMessage')->error($e->getMessage(), ['requestDTO' => $requestDTO->toArray()]);
                $myappPushHistoryListResponseDTO = new MyappPushHistoryListResponseDTO(['totalCount' => 0, 'contents' => []], $requestDTO->getPage(), $requestDTO->getPageSize());
            }
            $page = $myappPushHistoryListResponseDTO->getPage();

            $this->setData('searchFormData', $searchFormArray);
            $this->setData('sort', MyappPushHistoryService::generateSortList());
            $this->setData('myappPushHistoryList', $this->sanitizeHistoryList($myappPushHistoryListResponseDTO->getMyappPushHistoryList()));
            $this->setData('isEnabled', $isEnabled);
            $this->setData('page', $page);
            $this->getView()->setDefine('layout', 'layout_layer.php');
        } catch (\Throwable $e) {
            $this->json(['success' => false, 'message' => $e->getMessage()], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * DTO 리스트의 문자열 필드에 htmlspecialchars 처리
     *
     * @param MyappPushHistoryDTO[] $historyList
     * @return MyappPushHistoryDTO[]
     */
    private function sanitizeHistoryList(array $historyList): array
    {
        foreach ($historyList as $dto) {
            $title = $dto->getTitle();
            if (!empty($title)) {
                $dto->setTitle(htmlspecialchars($title, ENT_QUOTES, 'UTF-8'));
            }

            $content = $dto->getContent();
            if (!empty($content)) {
                $dto->setContent(htmlspecialchars($content, ENT_QUOTES, 'UTF-8'));
            }

            $failReason = $dto->getFailReason();
            if (!empty($failReason)) {
                $dto->setFailReason(htmlspecialchars($failReason, ENT_QUOTES, 'UTF-8'));
            }
        }

        return $historyList;
    }

    /**
     * DTO 생성을 위한 요청 데이터 전처리
     *
     * @param array $postData
     * @return array
     */
    private function prepareRequestDataForDto(array $postData): array
    {
        $preparedData = $postData;

        $sort = explode(' ', $preparedData['sort'] ?? (MyappPushResultSortType::SEND_DATE_TIME->name . ' ' . MyappPushResultDirection::DESC->name));
        $preparedData['sortType'] = (MyappPushResultSortType::tryFromName($sort[0]) ?? MyappPushResultSortType::SEND_DATE_TIME)->name;
        $preparedData['direction'] = (MyappPushResultDirection::tryFromName(strtoupper($sort[1])) ?? MyappPushResultDirection::DESC)->name;

        $allScheduleTypes = array_map(fn($e) => $e->name, MyappPushScheduleType::cases());
        if (!empty($preparedData['scheduleTypes']) && !array_diff($preparedData['scheduleTypes'], $allScheduleTypes) && !array_diff($allScheduleTypes, $preparedData['scheduleTypes'])) {
            unset($preparedData['scheduleTypes']);
        }

        $allSendStatuses = array_map(fn($e) => $e->name, MyappPushSendStatus::availableForSearch());
        if (!empty($preparedData['sendStatuses']) && !array_diff($preparedData['sendStatuses'], $allSendStatuses) && !array_diff($allSendStatuses, $preparedData['sendStatuses'])) {
            unset($preparedData['sendStatuses']);
        }

        if (empty($preparedData['keyword'])) {
            unset($preparedData['keyword']);
        }

        if (empty($preparedData['searchType'])) {
            unset($preparedData['searchType']);
        }

        return $preparedData;
    }
}
