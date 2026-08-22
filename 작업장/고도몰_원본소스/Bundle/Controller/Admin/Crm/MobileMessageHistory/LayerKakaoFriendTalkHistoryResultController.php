<?php
/*
 * Copyright (C) 2025 NHN COMMERCE. - All Rights Reserved
 *
 * Unauthorized copying or redistribution of this file in source and binary forms via any medium
 * is strictly prohibited.
 */

namespace Bundle\Controller\Admin\Crm\MobileMessageHistory;

use Framework\Http\Request;
use Origin\DTO\ApiClient\Commerce\Apps\KakaoFriendTalkHistoryDTO;
use Origin\DTO\ApiClient\Commerce\Apps\KakaoFriendTalkHistoryListRequestDTO;
use Origin\DTO\ApiClient\Commerce\Apps\KakaoFriendTalkHistoryListResponseDTO;
use Origin\DTO\ApiClient\Commerce\Apps\KakaoFriendTalkHistorySearchFormDTO;
use Origin\Enum\ApiClient\Apps\KakaoFriendTalkResultDirection;
use Origin\Enum\ApiClient\Apps\KakaoFriendTalkResultSortType;
use Origin\Enum\ApiClient\Apps\KakaoFriendTalkScheduleType;
use Origin\Enum\ApiClient\Apps\KakaoFriendTalkSendStatus;
use Origin\Enum\ApiClient\Excel\ExcelGenerateMenu;
use Origin\Service\Crm\Message\KakaoFriendTalkHistoryService;
use Origin\Service\Crm\Message\KakaoFriendTalkSettingService;

class LayerKakaoFriendTalkHistoryResultController extends \Controller\Admin\Controller
{
    public function index()
    {
        $this->callMenu('crm', 'messageHistory', 'mobileHistoryList');
        $this->setMenuCode('crm', 'messageHistory', 'mobileHistoryList');

        /** @var Request $request */
        $request = \App::getInstance('request');
        $postData = $request->post()->toArray();

        $searchFormDTO = new KakaoFriendTalkHistorySearchFormDTO($postData);
        $searchFormArray = $searchFormDTO->toRecursiveArray();
        $preparedData = $this->prepareRequestDataForDto($searchFormArray);
        $requestDTO = new KakaoFriendTalkHistoryListRequestDTO($preparedData);

        /** @var KakaoFriendTalkSettingService $kakaoFriendTalkSettingService */
        $kakaoFriendTalkSettingService = \App::getInstance(KakaoFriendTalkSettingService::class);
        $kakaoFriendTalkConfig = $kakaoFriendTalkSettingService->getKakaoFriendTalkConfig();
        $isEnabled = $kakaoFriendTalkConfig->getUseFlag() === 'y';

        try {
            /** @var KakaoFriendTalkHistoryService $kakaoFriendTalkHistoryService */
            $kakaoFriendTalkHistoryService = \App::getInstance(KakaoFriendTalkHistoryService::class);
            $kakaoFriendTalkHistoryListResponseDTO = $kakaoFriendTalkHistoryService->getKakaoFriendTalkHistoryList($requestDTO);
        } catch (\Throwable $e) {
            \Logger::channel('mobileMessage')->error($e->getMessage(), ['requestDTO' => $requestDTO->toArray()]);
            $kakaoFriendTalkHistoryListResponseDTO = new KakaoFriendTalkHistoryListResponseDTO(['totalCount' => 0, 'contents' => []], $requestDTO->getPage(), $requestDTO->getPageSize());
        }
        $page = $kakaoFriendTalkHistoryListResponseDTO->getPage();

        $this->setData('searchFormData', $searchFormArray);
        $this->setData('excelSearchQuery', $requestDTO->toRecursiveArray());
        $this->setData('excelMenu', ExcelGenerateMenu::ADMIN_FRIENDTALK_SEND_RESULT);
        $this->setData('sort', KakaoFriendTalkHistoryService::generateSortList());
        $this->setData('kakaoFriendTalkHistoryList', $this->sanitizeHistoryList($kakaoFriendTalkHistoryListResponseDTO->getKakaoFriendTalkHistoryList()));
        $this->setData('isEnabled', $isEnabled);
        $this->setData('page', $page);
        $this->getView()->setDefine('layout', 'layout_layer.php');
    }

    /**
     * DTO 리스트의 문자열 필드에 htmlspecialchars 처리
     *
     * @param KakaoFriendTalkHistoryDTO[] $historyList
     * @return KakaoFriendTalkHistoryDTO[]
     */
    private function sanitizeHistoryList(array $historyList): array
    {
        foreach ($historyList as $dto) {
            $sendGroupKey = $dto->getSendGroupKey();
            if (!empty($sendGroupKey)) {
                $dto->setSendGroupKey(htmlspecialchars($sendGroupKey, ENT_QUOTES, 'UTF-8'));
            }

            $campaignName = $dto->getCampaignName();
            if (!empty($campaignName)) {
                $dto->setCampaignName(htmlspecialchars($campaignName, ENT_QUOTES, 'UTF-8'));
            }

            $snapshot = $dto->getSnapshot();
            if (!empty($snapshot)) {
                $dto->setSnapshot(htmlspecialchars($snapshot, ENT_QUOTES, 'UTF-8'));
            }
        }

        return $historyList;
    }

    private function prepareRequestDataForDto(array $postData): array
    {
        $preparedData = $postData;

        $sort = explode(' ', $preparedData['sort'] ?? (KakaoFriendTalkResultSortType::CREATED_DATE_TIME->name . ' ' . KakaoFriendTalkResultDirection::DESC->name));
        $preparedData['sortType'] = (KakaoFriendTalkResultSortType::tryFromName($sort[0]) ?? KakaoFriendTalkResultSortType::CREATED_DATE_TIME)->name;
        $preparedData['direction'] = (KakaoFriendTalkResultDirection::tryFromName(strtoupper($sort[1])) ?? KakaoFriendTalkResultDirection::DESC)->name;

        $allScheduleTypes = array_map(fn($e) => $e->name, KakaoFriendTalkScheduleType::cases());
        if (!empty($preparedData['scheduleTypes']) && !array_diff($preparedData['scheduleTypes'], $allScheduleTypes) && !array_diff($allScheduleTypes, $preparedData['scheduleTypes'])) {
            unset($preparedData['scheduleTypes']);
        }

        $allSendStatuses = array_map(fn($e) => $e->name, KakaoFriendTalkSendStatus::availableForSearch());
        if (!empty($preparedData['sendResultStatuses']) && !array_diff($preparedData['sendResultStatuses'], $allSendStatuses) && !array_diff($allSendStatuses, $preparedData['sendResultStatuses'])) {
            unset($preparedData['sendResultStatuses']);
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
