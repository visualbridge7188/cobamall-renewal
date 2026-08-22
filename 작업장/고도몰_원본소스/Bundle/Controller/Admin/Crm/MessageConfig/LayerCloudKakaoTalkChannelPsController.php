<?php
/*
 * Copyright (C) 2025 NHN COMMERCE. - All Rights Reserved
 *
 * Unauthorized copying or redistribution of this file in source and binary forms via any medium
 * is strictly prohibited.
 */

namespace Bundle\Controller\Admin\Crm\MessageConfig;

use Framework\Debug\Exception\AlertCloseException;
use Framework\Http\Request;
use Origin\Enum\Crm\Message\KakaoAlimTalkSender;
use Origin\Service\Crm\Message\KakaoAlimTalkSettingService;
use Origin\Service\Crm\Message\KakaoFriendTalkSettingService;
use Origin\Service\Crm\Message\MessageConfigService;
use Origin\Service\Crm\Message\MyappPushSettingService;
use Origin\Service\Member\KakaoAlrim\KakaoAlrimCloudPsService;

class LayerCloudKakaoTalkChannelPsController extends \Controller\Admin\Controller
{
    public function index()
    {
        /** @var Request $request */
        $request = \App::getInstance('request');
        $postData = $request->post()->toArray();
        $mode = $postData['mode'];

        switch ($mode) {
            case 'delete':
                try {
                    /** @var KakaoAlrimCloudPsService $service */
                    $service = \App::getInstance(KakaoAlrimCloudPsService::class);
                    $result = $service->deletePlusId($postData);

                    /** @var KakaoFriendTalkSettingService $kakaoFriendTalkSettingService */
                    $kakaoFriendTalkSettingService = \App::getInstance(KakaoFriendTalkSettingService::class);
                    $kakaoFriendTalkConfig = $kakaoFriendTalkSettingService->getKakaoFriendTalkConfig();

                    /** @var KakaoAlimTalkSettingService $kakaoAlimTalkSettingService */
                    $kakaoAlimTalkSettingService = \App::getInstance(KakaoAlimTalkSettingService::class);
                    $kakaoAlimTalkConfig = $kakaoAlimTalkSettingService->getKakaoAlimTalkConfig();

                    $needConfigUpdate = false;
                    // 친구톡 사용안함 처리
                    if ($kakaoFriendTalkConfig->getUseFlag() === 'y') {
                        $kakaoFriendTalkConfig->setUseFlag('n');
                        $needConfigUpdate = true;
                    }

                    // 클라우드 알림톡 사용안함 처리
                    if ($kakaoAlimTalkConfig->getSender() === KakaoAlimTalkSender::NHN_CLOUD && $kakaoAlimTalkConfig->getUseFlag() === 'y') {
                        $kakaoAlimTalkConfig->setUseFlag('n');
                        $needConfigUpdate = true;
                    }

                    if ($needConfigUpdate) {
                        /** @var MessageConfigService $messageConfigService */
                        $messageConfigService = \App::getInstance(MessageConfigService::class);
                        $messageConfigService->save(null, null, null, $kakaoFriendTalkConfig, $kakaoAlimTalkConfig, null);
                    }

                    $this->json(['success' => $result]);
                    break;
                } catch (\Throwable $e) {
                    \Logger::channel('mobileMessage')->warning($e->getMessage(), [__METHOD__, $e->getTraceAsString()]);
                    $this->json(['success' => false, 'message' => $e->getMessage()]);
                }
                break;
            default:
                throw new AlertCloseException(__("잘못된 접근입니다."));
        }
    }
}
