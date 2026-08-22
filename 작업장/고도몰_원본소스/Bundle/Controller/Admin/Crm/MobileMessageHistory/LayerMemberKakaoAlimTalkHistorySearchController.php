<?php
/*
 * Copyright (C) 2025 NHN COMMERCE. - All Rights Reserved
 *
 * Unauthorized copying or redistribution of this file in source and binary forms via any medium
 * is strictly prohibited.
 */

namespace Bundle\Controller\Admin\Crm\MobileMessageHistory;

use Framework\Enum\ExternalUrl;
use Framework\Http\Request;
use Framework\Utility\GodoUtils;
use Origin\Enum\ApiClient\Notification\KakaoAlimTalkResultSearchType;
use Origin\Enum\ApiClient\Notification\KakaoAlimTalkSendStatus;
use Origin\Enum\ApiClient\Notification\KakaoAlimTalkTriggerType;
use Origin\Enum\Crm\Message\KakaoAlimTalkSender;
use Origin\Service\Crm\Message\KakaoAlimTalkSettingService;
use Origin\Traits\Member\KakaoAlrim\KakaoAlrimCloudAvailable;

class LayerMemberKakaoAlimTalkHistorySearchController extends \Controller\Admin\Controller
{
    use KakaoAlrimCloudAvailable;

    public function index()
    {
        /** @var Request $request */
        $request = \App::getInstance('request');
        $postData = $request->post()->toArray();
        $memberNo = filter_var($postData['memberNo'] ?? 0, FILTER_VALIDATE_INT) ?: 0;

        $triggerTypes = KakaoAlimTalkTriggerType::availableForSearch();
        $sendStatuses = KakaoAlimTalkSendStatus::availableForMemberHistorySearch();
        $searchType = KakaoAlimTalkResultSearchType::generateSelectBoxData();

        $isKakaoAlrimAvailable = !$this->isNewMall();
        $isKakaoAlrimLunaInstalled = GodoUtils::isPlusShop(PLUSSHOP_CODE_KAKAOALRIMLUNA);

        /** @var KakaoAlimTalkSettingService $kakaoAlimTalkSettingService */
        $kakaoAlimTalkSettingService = \App::getInstance(KakaoAlimTalkSettingService::class);
        $kakaoAlimTalkConfig = $kakaoAlimTalkSettingService->getKakaoAlimTalkConfig();

        $isSelectedBizm = $kakaoAlimTalkConfig->getSender() === KakaoAlimTalkSender::BIZM;

        $this->setData('memberNo', $memberNo);
        $this->setData('searchType', $searchType);
        $this->setData('isSelectedBizm', $isSelectedBizm);
        $this->setData('triggerTypes', $triggerTypes);
        $this->setData('sendStatuses', $sendStatuses);
        $this->setData('isKakaoAlrimAvailable', $isKakaoAlrimAvailable);
        $this->setData('isKakaoAlrimLunaInstalled', $isKakaoAlrimLunaInstalled);
        $this->setData('blumnAiStatisticsUrl', ExternalUrl::BIZMSG_BLUMN_AI->getUrl('/statistics-alimtalk'));

        $this->getView()->setDefine('layout', 'layout_layer.php');
    }
}
