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
use Origin\DTO\ApiClient\Commerce\Notification\BizmKakaoAlimTalkHistoryDTO;
use Origin\DTO\ApiClient\Commerce\Notification\BizmKakaoAlimTalkHistoryListRequestDTO;
use Origin\DTO\ApiClient\Commerce\Notification\BizmKakaoAlimTalkHistoryListResponseDTO;
use Origin\DTO\ApiClient\Commerce\Notification\BizmKakaoAlimTalkHistorySearchFormDTO;
use Origin\Enum\ApiClient\Excel\ExcelGenerateMenu;
use Origin\Enum\ApiClient\Notification\KakaoAlimTalkSendStatus;
use Origin\Service\Crm\Message\KakaoAlimTalkHistoryService;
use Origin\Service\Crm\Message\KakaoAlimTalkSettingService;

class LayerBizmKakaoAlimTalkHistoryResultController extends \Controller\Admin\Controller
{
    public function index()
    {
        try {
            $this->callMenu('crm', 'messageHistory', 'mobileHistoryList');
            $this->setMenuCode('crm', 'messageHistory', 'mobileHistoryList');

            /** @var Request $request */
            $request = \App::getInstance('request');
            $postData = $request->post()->toArray();

            $searchFormDTO = new BizmKakaoAlimTalkHistorySearchFormDTO($postData);
            $searchFromArray = $searchFormDTO->toRecursiveArray();
            $preparedData = $this->prepareRequestDataForDto($searchFromArray);
            $requestDTO = new BizmKakaoAlimTalkHistoryListRequestDTO($preparedData);

            /** @var KakaoAlimTalkSettingService $kakaoAlimTalkSettingService */
            $kakaoAlimTalkSettingService = \App::getInstance(KakaoAlimTalkSettingService::class);
            $kakaoAlimTalkConfig = $kakaoAlimTalkSettingService->getKakaoAlimTalkConfig();
            $isEnabled = $kakaoAlimTalkConfig->getUseFlag() === 'y';

            try {
                /** @var KakaoAlimTalkHistoryService $kakaoAlimTalkHistoryService */
                $kakaoAlimTalkHistoryService = \App::getInstance(KakaoAlimTalkHistoryService::class);
                $bizmKakaoAlimTalkHistoryListResponseDTO = $kakaoAlimTalkHistoryService->getBizmKakaoAlimTalkHistoryList($requestDTO);
            } catch (\Throwable $e) {
                \Logger::channel('mobileMessage')->error($e->getMessage(), ['requestDTO' => $requestDTO->toArray()]);
                $bizmKakaoAlimTalkHistoryListResponseDTO = new BizmKakaoAlimTalkHistoryListResponseDTO(['totalCount' => 0, 'contents' => []], $requestDTO->getPage(), $requestDTO->getPageSize());
            }
            $page = $bizmKakaoAlimTalkHistoryListResponseDTO->getPage();

            $this->setData('searchFormData', $searchFormDTO->toRecursiveArray());
            $this->setData('excelSearchQuery', $requestDTO->toRecursiveArray());
            $this->setData('excelMenu', ExcelGenerateMenu::ADMIN_BIZM_ALIMTALK_SEND_RESULT);
            $this->setData('sort', KakaoAlimTalkHistoryService::SORT_LIST);
            $this->setData('bizmKakaoAlimTalkHistoryList', $this->sanitizeHistoryList($bizmKakaoAlimTalkHistoryListResponseDTO->getBizmKakaoAlimTalkHistoryList()));
            $this->setData('isEnabled', $isEnabled);
            $this->setData('page', $page);
            $this->getView()->setDefine('layout', 'layout_layer.php');
        } catch (\Throwable $e) {
            $this->json(['success' => false, 'message' => $e->getMessage()], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    private function prepareRequestDataForDto(array $postData): array
    {
        $preparedData = $postData;
        // TODO 정렬 기준 추가를 대비하여 분리
        $sort = explode(' ', $preparedData['sort'] ?? 'reservedAt desc');
        $preparedData['sortDescending'] = ($sort[1] === 'desc') ? 'true' : 'false';

        unset($preparedData['triggerTypes']); // bizm 에서는 사용하지 않음

        $allSendStatuses = array_map(fn($e) => $e->name, KakaoAlimTalkSendStatus::availableForSearch());
        if (!empty($preparedData['sendStatuses']) && !array_diff($preparedData['sendStatuses'], $allSendStatuses) && !array_diff($allSendStatuses, $preparedData['sendStatuses'])) {
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
     * @param BizmKakaoAlimTalkHistoryDTO[] $historyList
     * @return BizmKakaoAlimTalkHistoryDTO[]
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
