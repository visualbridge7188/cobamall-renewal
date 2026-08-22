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
use Origin\DTO\ApiClient\Commerce\Notification\MemberBizmKakaoAlimTalkHistorySearchFormDTO;
use Origin\DTO\ApiClient\Commerce\Notification\MemberBizmKakaoAlimTalkHistoryDTO;
use Origin\DTO\ApiClient\Commerce\Notification\MemberBizmKakaoAlimTalkHistoryListRequestDTO;
use Origin\DTO\ApiClient\Commerce\Notification\MemberBizmKakaoAlimTalkHistoryListResponseDTO;
use Origin\Enum\ApiClient\Notification\KakaoAlimTalkSendStatus;
use Origin\Service\Crm\Message\KakaoAlimTalkHistoryService;
use Origin\Service\Crm\Message\KakaoAlimTalkSettingService;

class LayerMemberBizmKakaoAlimTalkHistoryResultController extends \Controller\Admin\Controller
{
    public function index()
    {
        try {
            $this->callMenu('crm', 'messageHistory', 'mobileHistoryList');
            $this->setMenuCode('crm', 'messageHistory', 'mobileHistoryList');

            /** @var Request $request */
            $request = \App::getInstance('request');
            $postData = $request->post()->toArray();

            $searchFormDTO = new MemberBizmKakaoAlimTalkHistorySearchFormDTO($postData);
            $searchFromArray = $searchFormDTO->toRecursiveArray();
            $preparedData = $this->prepareRequestDataForDto($searchFromArray);
            $requestDTO = new MemberBizmKakaoAlimTalkHistoryListRequestDTO($preparedData);

            /** @var KakaoAlimTalkSettingService $kakaoAlimTalkSettingService */
            $kakaoAlimTalkSettingService = \App::getInstance(KakaoAlimTalkSettingService::class);
            $kakaoAlimTalkConfig = $kakaoAlimTalkSettingService->getKakaoAlimTalkConfig();
            $isEnabled = $kakaoAlimTalkConfig->getUseFlag() === 'y';

            try {
                /** @var KakaoAlimTalkHistoryService $kakaoAlimTalkHistoryService */
                $kakaoAlimTalkHistoryService = \App::getInstance(KakaoAlimTalkHistoryService::class);
                $bizmKakaoAlimTalkHistoryListResponseDTO = $kakaoAlimTalkHistoryService->getMemberBizmKakaoAlimTalkHistoryList($requestDTO);
            } catch (\Throwable $e) {
                \Logger::channel('mobileMessage')->error($e->getMessage(), ['requestDTO' => $requestDTO->toArray()]);
                $bizmKakaoAlimTalkHistoryListResponseDTO = new MemberBizmKakaoAlimTalkHistoryListResponseDTO(['totalCount' => 0, 'contents' => []], $requestDTO->getPage(), $requestDTO->getPageSize());
            }
            $page = $bizmKakaoAlimTalkHistoryListResponseDTO->getPage();

            $this->setData('searchFormData', $searchFormDTO->toRecursiveArray());
            $this->setData('sort', KakaoAlimTalkHistoryService::SORT_LIST);
            $this->setData('bizmKakaoAlimTalkHistoryList', $this->sanitizeHistoryList($bizmKakaoAlimTalkHistoryListResponseDTO->getBizmKakaoAlimTalkHistoryList()));
            $this->setData('isEnabled', $isEnabled);
            $this->setData('page', $page);
            $this->getView()->setDefine('layout', 'layout_layer.php');
        } catch (\Throwable $throwable) {
            $this->json(['success' => false, 'message' => $throwable->getMessage()], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    private function prepareRequestDataForDto(array $postData): array
    {
        $preparedData = $postData;
        // TODO 정렬 기준 추가를 대비하여 분리
        $sort = explode(' ', $preparedData['sort'] ?? 'reservedAt desc');
        $preparedData['sortDescending'] = ($sort[1] === 'desc') ? 'true' : 'false';

        unset($preparedData['triggerTypes']); // bizm 에서는 사용하지 않음

        $allSendStatuses = array_map(fn($e) => $e->name, KakaoAlimTalkSendStatus::availableForMemberHistorySearch());
        if (
            !empty($preparedData['sendStatuses']) &&
            !array_diff($preparedData['sendStatuses'], $allSendStatuses) &&
            !array_diff($allSendStatuses, $preparedData['sendStatuses'])
        ) {
            unset($preparedData['sendStatuses']);
        }

        if (empty($preparedData['searchType'])) {
            unset($preparedData['searchType']);
        }

        if (empty($preparedData['keyword'])) {
            unset($preparedData['keyword']);
        }

        return $preparedData;
    }

    /**
     * DTO 리스트의 문자열 필드에 htmlspecialchars 처리
     *
     * @param MemberBizmKakaoAlimTalkHistoryDTO[] $historyList
     * @return MemberBizmKakaoAlimTalkHistoryDTO[]
     */
    private function sanitizeHistoryList(array $historyList): array
    {
        foreach ($historyList as $dto) {
            $sendKey = $dto->getSendKey();
            if (!empty($sendKey)) {
                $dto->setSendKey(htmlspecialchars($sendKey, ENT_QUOTES, 'UTF-8'));
            }

            $content = $dto->getContent();
            if (!empty($content)) {
                $dto->setContent(htmlspecialchars($content, ENT_QUOTES, 'UTF-8'));
            }
        }

        return $historyList;
    }
}
