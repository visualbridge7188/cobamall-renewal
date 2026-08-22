<?php
/*
 * Copyright (C) 2025 NHN COMMERCE. - All Rights Reserved
 *
 * Unauthorized copying or redistribution of this file in source and binary forms via any medium
 * is strictly prohibited.
 */

namespace Bundle\Controller\Admin\Crm;

use Bundle\Component\Godo\GodoSmsServerApi;
use Bundle\Component\Sms\SmsAdmin;
use Component\Sms\Sms;
use Origin\Service\Crm\Message\KakaoAlimTalkSettingService;
use Origin\Service\Crm\Message\KakaoFriendTalkSettingService;
use Origin\Service\Crm\Message\MyappPushSettingService;

class MobileSendController extends \Controller\Admin\Controller
{
    public function index()
    {
        $this->callMenu('crm', 'messageSend', 'mobileSend');

        // 현재 SMS 포인트 싱크
        Sms::saveSmsPoint();

        /** @var KakaoFriendTalkSettingService $kakaoFriendTalkSettingService */
        $kakaoFriendTalkSettingService = \App::getInstance(KakaoFriendTalkSettingService::class);
        $kakaoFriendTalkConfig = $kakaoFriendTalkSettingService->getKakaoFriendTalkConfig();

        /** @var KakaoAlimTalkSettingService $kakaoAlimTalkSettingService */
        $kakaoAlimTalkSettingService = \App::getInstance(KakaoAlimTalkSettingService::class);
        $kakaoAlimTalkConfig = $kakaoAlimTalkSettingService->getKakaoAlimTalkConfig();

        /** @var MyappPushSettingService $myappPushSettingService */
        $myappPushSettingService = \App::getInstance(MyappPushSettingService::class);
        $myappPushConfig = $myappPushSettingService->getMyappPushConfig();

        /** @var SmsAdmin $smsAdmin */
        $smsAdmin = \App::load('Component\\Sms\\SmsAdmin');
        $smsAutoData = $smsAdmin->getSmsAutoData();

        /** @var GodoSmsServerApi $godoSms */
        $godoSms = \App::load('Component\\Godo\\GodoSmsServerApi');
        $smsPreRegister = $godoSms->checkSmsCallNumber($smsAutoData['smsCallNum']);

        $hasCallNumber = $smsPreRegister && !empty($smsAutoData['smsCallNum']);

        $messageConfig = [
            "hasCallNumber" => $hasCallNumber,
            "kakaoAlimTalk" => [
                'sender' => $kakaoAlimTalkConfig->getSender()->value,
                'useFlag' => $kakaoAlimTalkConfig->getUseFlag(),
                'useAlternative' => $kakaoAlimTalkConfig->getManualSmsAlternativeSendFlag(),
            ],
            "kakaoFriendTalk" => [
                'sender' => $kakaoFriendTalkConfig->getSender()->value,
                'useFlag' => $kakaoFriendTalkConfig->getUseFlag(),
                'linkPlatformType' => $kakaoFriendTalkConfig->getLinkPlatformType()->name,
                'useAlternative' => $kakaoFriendTalkConfig->getSmsAlternativeSendFlag(),
            ],
            "myappPush" => [
                'useFlag' => $myappPushConfig->getUseFlag(),
                'useAlternative' => $myappPushConfig->getManualSmsAlternativeSendFlag(),
            ],
        ];

        $this->setData('messageConfig', $messageConfig);
        $this->setData('smsPointEach', Sms::SMS_POINT);
        $this->setData('lmsPointEach', Sms::LMS_POINT);
        $this->setData('kakaoAlrimTalkPointEach', Sms::KAKAO_POINT);
        $this->setData('kakaoFriendTalkPointEach', Sms::KAKAO_FRIEND_TALK_POINT);
        $this->getView()->setPageName('crm/mobile_send.php');
    }
}
