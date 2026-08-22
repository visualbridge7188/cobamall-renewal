<?php
/*
 * Copyright (C) 2025 NHN COMMERCE. - All Rights Reserved
 *
 * Unauthorized copying or redistribution of this file in source and binary forms via any medium
 * is strictly prohibited.
 */

namespace Bundle\Controller\Admin\Crm;

use Framework\Utility\GodoUtils;
use Origin\DTO\ApiClient\Commerce\Apps\MyappStatusResponseDTO;
use Origin\DTO\ApiClient\Commerce\Notification\MessagePointConfigResponseDTO;
use Origin\Enum\Crm\Message\AutoSmsAlternativeSendType;
use Origin\Enum\Crm\Message\FailedMessageSendType;
use Origin\Enum\Crm\Message\KakaoAlimTalkSender;
use Origin\Enum\Crm\Message\LinkPlatformType;
use Origin\Enum\Crm\Message\SmsAutoSendOverType;
use Origin\Service\Crm\Message\KakaoAlimTalkSettingService;
use Origin\Service\Crm\Message\KakaoFriendTalkSettingService;
use Origin\Service\Crm\Message\MessagePointConfigService;
use Origin\Service\Crm\Message\MyappPushSettingService;
use Origin\Service\Crm\Message\SmsSettingService;
use Origin\Service\MyApp\MyappSettingService;
use Origin\Traits\Member\KakaoAlrim\KakaoAlrimCloudAvailable;

class MessageConfigController extends \Controller\Admin\Controller
{
    use KakaoAlrimCloudAvailable;

    public function index()
    {
        // 메뉴 설정
        $this->callMenu('crm', 'messageConfig', 'messageConfig');
        $this->setMenuCode('crm', 'messageConfig', 'messageConfig');

        try {
            /** @var MessagePointConfigService $messagePointConfigService */
            $messagePointConfigService = \App::getInstance(MessagePointConfigService::class);
            $messagePointConfig = $messagePointConfigService->getMessagePointConfig();
        } catch (\Throwable $e) {
            \Logger::channel('mobileMessage')->error($e->getMessage());
            $messagePointConfig = new MessagePointConfigResponseDTO(null);
        }

        /** @var SmsSettingService $smsSettingService */
        $smsSettingService = \App::getInstance(SmsSettingService::class);
        $smsAutoConfig = $smsSettingService->getSmsAutoConfig();
        $smsRejectPolicy = $smsSettingService->getSmsRejectConfig();

        /** @var KakaoFriendTalkSettingService $kakaoFriendTalkSettingService */
        $kakaoFriendTalkSettingService = \App::getInstance(KakaoFriendTalkSettingService::class);
        $kakaoFriendTalkConfig = $kakaoFriendTalkSettingService->getKakaoFriendTalkConfig();

        /** @var KakaoAlimTalkSettingService $kakaoAlimTalkSettingService */
        $kakaoAlimTalkSettingService = \App::getInstance(KakaoAlimTalkSettingService::class);
        $kakaoAlimTalkConfig = $kakaoAlimTalkSettingService->getKakaoAlimTalkConfig();

        /** @var MyappPushSettingService $myappPushSettingService */
        $myappPushSettingService = \App::getInstance(MyappPushSettingService::class);
        $myappPushConfig = $myappPushSettingService->getMyappPushConfig();

        try {
            /** @var MyappSettingService $myappSettingService */
            $myappSettingService = \App::getInstance(MyappSettingService::class);
            $myappStatus = $myappSettingService->getMyappStatus();
        } catch (\Throwable $e) {
            \Logger::channel('myapp')->error($e->getMessage());
            $myappStatus = new MyappStatusResponseDTO(['isInstalled' => false, 'isReleased' => false]);
        }

        $isKakaoAlrimAvailable = !$this->isNewMall();
        $isKakaoAlrimLunaInstalled = GodoUtils::isPlusShop(PLUSSHOP_CODE_KAKAOALRIMLUNA);

        $this->setData('messagePointConfig', $messagePointConfig);
        $this->setData('smsRejectPolicy', $smsRejectPolicy);
        $this->setData('smsAutoConfig', $smsAutoConfig);
        $this->setData('kakaoFriendTalkConfig', $kakaoFriendTalkConfig);
        $this->setData('isKakaoAlrimAvailable', $isKakaoAlrimAvailable);
        $this->setData('isKakaoAlrimLunaInstalled', $isKakaoAlrimLunaInstalled);
        $this->setData('kakaoAlimTalkConfig', $kakaoAlimTalkConfig);
        $this->setData('isMyappInstalled', $myappStatus->isInstalled());
        $this->setData('isMyappReleased', $myappStatus->isReleased());
        $this->setData('myappPushConfig', $myappPushConfig);

        $this->setData('linkPlatformTypes', LinkPlatformType::cases());
        $this->setData('kakaoAlimTalkSenders', KakaoAlimTalkSender::cases());
        $this->setData('autoSmsAlternativeSendTypes', AutoSmsAlternativeSendType::cases());
        $this->setData('failedMessageSendTypes', FailedMessageSendType::cases());
        $this->setData('smsAutoSendOverTypes', SmsAutoSendOverType::cases());

        $this->getView()->setPageName('crm/message_config.php');
    }
}
