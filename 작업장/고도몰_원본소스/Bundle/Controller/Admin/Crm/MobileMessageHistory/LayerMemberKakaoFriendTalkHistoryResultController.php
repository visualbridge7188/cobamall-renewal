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
use Origin\DTO\ApiClient\Commerce\Apps\MemberKakaoFriendTalkHistoryDTO;
use Origin\DTO\ApiClient\Commerce\Apps\MemberKakaoFriendTalkHistoryListRequestDTO;
use Origin\DTO\ApiClient\Commerce\Apps\MemberKakaoFriendTalkHistoryListResponseDTO;
use Origin\DTO\ApiClient\Commerce\Apps\MemberKakaoFriendTalkHistorySearchFormDTO;
use Origin\Enum\ApiClient\Apps\KakaoFriendTalkResultDirection;
use Origin\Enum\ApiClient\Apps\KakaoFriendTalkResultSortType;
use Origin\Enum\ApiClient\Apps\KakaoFriendTalkScheduleType;
use Origin\Enum\ApiClient\Apps\KakaoFriendTalkSendStatus;
use Origin\Service\Crm\Message\KakaoFriendTalkHistoryService;
use Origin\Service\Crm\Message\KakaoFriendTalkSettingService;

class LayerMemberKakaoFriendTalkHistoryResultController extends \Controller\Admin\Controller
{
    public function index()
    {
        try {
            $this->callMenu('crm', 'messageHistory', 'mobileHistoryList');
            $this->setMenuCode('crm', 'messageHistory', 'mobileHistoryList');

            /** @var Request $request */
            $request = \App::getInstance('request');
            $postData = $request->post()->toArray();

            $searchFormDTO = new MemberKakaoFriendTalkHistorySearchFormDTO($postData);
            $searchFormArray = $searchFormDTO->toRecursiveArray();
            $preparedData = $this->prepareRequestDataForDto($searchFormArray);
            $requestDTO = new MemberKakaoFriendTalkHistoryListRequestDTO($preparedData);

            /** @var KakaoFriendTalkSettingService $kakaoFriendTalkSettingService */
            $kakaoFriendTalkSettingService = \App::getInstance(KakaoFriendTalkSettingService::class);
            $kakaoFriendTalkConfig = $kakaoFriendTalkSettingService->getKakaoFriendTalkConfig();
            $isEnabled = $kakaoFriendTalkConfig->getUseFlag() === 'y';

            try {
                /** @var KakaoFriendTalkHistoryService $kakaoFriendTalkHistoryService */
                $kakaoFriendTalkHistoryService = \App::getInstance(KakaoFriendTalkHistoryService::class);
                $kakaoFriendTalkHistoryListResponseDTO = $kakaoFriendTalkHistoryService->getMemberKakaoFriendTalkHistoryList($requestDTO);
            } catch (\Throwable $e) {
                \Logger::channel('mobileMessage')->error($e->getMessage(), ['requestDTO' => $requestDTO->toArray()]);
                $kakaoFriendTalkHistoryListResponseDTO = new MemberKakaoFriendTalkHistoryListResponseDTO(['totalCount' => 0, 'contents' => []], $requestDTO->getPage(), $requestDTO->getPageSize());
            }
            $page = $kakaoFriendTalkHistoryListResponseDTO->getPage();

            $this->setData('searchFormData', $searchFormArray);
            $this->setData('sort', KakaoFriendTalkHistoryService::generateSortList());
            $this->setData('kakaoFriendTalkHistoryList', $this->sanitizeHistoryList($kakaoFriendTalkHistoryListResponseDTO->getKakaoFriendTalkHistoryList()));
            $this->setData('isEnabled', $isEnabled);
            $this->setData('page', $page);
            $this->getView()->setDefine('layout', 'layout_layer.php');
        } catch (\Throwable $throwable) {
            $this->json(['success' => false, 'message' => $throwable->getMessage()], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * DTO 리스트의 문자열 필드에 htmlspecialchars 처리
     *
     * @param MemberKakaoFriendTalkHistoryDTO[] $historyList
     * @return MemberKakaoFriendTalkHistoryDTO[]
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
        }

        return $historyList;
    }

    private function prepareRequestDataForDto(array $postData): array
    {
        $preparedData = $postData;

        // 정렬 기준 추가를 대비하여 분리
        $sortType = explode(' ', $preparedData['sort'] ?? (KakaoFriendTalkResultSortType::CREATED_DATE_TIME->name . ' ' . KakaoFriendTalkResultDirection::DESC->name));
        $preparedData['sortDescending'] = ($sortType[1] === KakaoFriendTalkResultDirection::DESC->name) ? 'true' : 'false';

        $allScheduleTypes = array_map(fn($e) => $e->name, KakaoFriendTalkScheduleType::cases());
        if (
            !empty($preparedData['triggerTypes']) &&
            !array_diff($preparedData['triggerTypes'], $allScheduleTypes) &&
            !array_diff($allScheduleTypes, $preparedData['triggerTypes'])
        ) {
            unset($preparedData['triggerTypes']);
        }

        $allSendStatuses = array_map(fn($e) => $e->name, KakaoFriendTalkSendStatus::availableForMemberHistorySearch());
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

        if (empty($preparedData['searchType'])) {
            unset($preparedData['searchType']);
        }

        return $preparedData;
    }
}
