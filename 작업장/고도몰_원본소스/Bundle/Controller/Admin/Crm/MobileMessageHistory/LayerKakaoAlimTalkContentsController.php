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
use Origin\DTO\ApiClient\Commerce\Notification\KakaoAlimTalkHistoryContentRequestDTO;
use Origin\DTO\ApiClient\Commerce\Notification\KakaoAlimTalkTemplateButtonDTO;
use Origin\Enum\ApiClient\Notification\KakaoAlimTalkTemplateButtonType;
use Origin\Enum\ApiClient\Notification\NotificationSendType;
use Origin\Service\Crm\Message\KakaoAlimTalkHistoryService;

class LayerKakaoAlimTalkContentsController extends \Controller\Admin\Controller
{
    public function index()
    {
        $this->callMenu('crm', 'messageHistory', 'mobileHistoryList');
        $this->setMenuCode('crm', 'messageHistory', 'mobileHistoryList');

        /** @var Request $request */
        $request = \App::getInstance('request');
        $sendGroupKey = $request->post()->get('sendGroupKey');

        try {
            $requestDTO = (new KakaoAlimTalkHistoryContentRequestDTO(['type' => NotificationSendType::ALIMTALK->name]));
            /** @var KakaoAlimTalkHistoryService $kakaoAlimTalkHistoryService */
            $kakaoAlimTalkHistoryService = \App::getInstance(KakaoAlimTalkHistoryService::class);
            $kakaoAlimTalkTalkHistoryContentDTO = $kakaoAlimTalkHistoryService->getKakaoAlimTalkHistoryContent($sendGroupKey, $requestDTO);
        } catch (\Throwable $e) {
            $this->json(['success' => false], Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        $templateButtons = $kakaoAlimTalkTalkHistoryContentDTO->getTemplateButtons();
        $hasAddChannelButton = !empty(array_filter($templateButtons ?? [], fn($button) => $button->getType() === KakaoAlimTalkTemplateButtonType::ADD_CHANNEL));
        $this->sortButtons($templateButtons);

        $previewMessage = null;
        if ($kakaoAlimTalkTalkHistoryContentDTO->isPendingOrRequestFailed()) {
            $previewMessage = '발송 중이거나 발송 요청 단계에서 실패한 메시지는 본문 내용만 제공됩니다.';
        } elseif (!$kakaoAlimTalkTalkHistoryContentDTO->isTemplateExists() || !empty($kakaoAlimTalkTalkHistoryContentDTO->getTemplateImageUrl())) {
            $previewMessage = '상단 이미지는 현재 템플릿 기준으로 제공되며, 템플릿이 삭제된 경우 이미지를 표시할 수 없습니다.';
        }

        $this->setData('kakaoAlimTalkTalkHistoryContentDTO', $kakaoAlimTalkTalkHistoryContentDTO);
        $this->setData('templateContent', $kakaoAlimTalkTalkHistoryContentDTO->getTemplateContent());
        $this->setData('templateExtra', $kakaoAlimTalkTalkHistoryContentDTO->getTemplateExtra());
        $this->setData('hasAddChannelButton', $hasAddChannelButton);
        $this->setData('templateButtons', $templateButtons);
        $this->setData('templateTitle', $kakaoAlimTalkTalkHistoryContentDTO->getTemplateTitle());
        $this->setData('templateSubTitle', $kakaoAlimTalkTalkHistoryContentDTO->getTemplateSubTitle());
        $this->setData('templateItemHighlight', $kakaoAlimTalkTalkHistoryContentDTO->getTemplateItemHighlight());
        $this->setData('templateItems', $kakaoAlimTalkTalkHistoryContentDTO->getTemplateItems());
        $this->setData('templateImageUrl', $kakaoAlimTalkTalkHistoryContentDTO->getTemplateImageUrl());
        $this->setData('isTemplateExists', $kakaoAlimTalkTalkHistoryContentDTO->isTemplateExists());
        $this->setData('previewMessage', $previewMessage);
        $this->setData('isPendingOrRequestFailed', $kakaoAlimTalkTalkHistoryContentDTO->isPendingOrRequestFailed());
        $this->setData('templateHeader', $kakaoAlimTalkTalkHistoryContentDTO->getTemplateHeader());

        $this->getView()->setDefine('layout', 'layout_layer.php');
    }

    /**
     * 버튼 유형에 따라 분리
     *
     * @param KakaoAlimTalkTemplateButtonDTO[] $templateButtons
     * @return void
     */
    protected function sortButtons(?array &$templateButtons): void
    {
        if (empty($templateButtons)) {
            return;
        }

        usort($templateButtons, function ($a, $b) {
            return ($a->getOrder() ?? 0) <=> ($b->getOrder() ?? 0);
        });
    }
}
