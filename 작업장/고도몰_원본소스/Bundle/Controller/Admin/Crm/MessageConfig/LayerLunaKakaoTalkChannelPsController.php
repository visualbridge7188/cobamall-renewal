<?php
/*
 * Copyright (C) 2025 NHN COMMERCE. - All Rights Reserved
 *
 * Unauthorized copying or redistribution of this file in source and binary forms via any medium
 * is strictly prohibited.
 */

namespace Bundle\Controller\Admin\Crm\MessageConfig;

use Bundle\Component\Member\KakaoAlrimLuna;
use Bundle\Component\Policy\Policy;
use Framework\Debug\Exception\AlertCloseException;
use Framework\Http\Request;
use Origin\Enum\Crm\Message\KakaoAlimTalkSender;
use Origin\Service\Crm\Message\KakaoAlimTalkSettingService;
use Origin\Service\Crm\Message\KakaoFriendTalkSettingService;
use Origin\Service\Crm\Message\MessageConfigService;
use Origin\Service\Crm\Message\MyappPushSettingService;

class LayerLunaKakaoTalkChannelPsController extends \Controller\Admin\Controller
{
    public function index()
    {
        /** @var Request $request */
        $request = \App::getInstance('request');
        $postData = $request->post()->toArray();
        $mode = $postData['mode'];

        switch ($mode) {
            case 'generateSendData':
                try {
                    unset($postData['mode']);

                    $oKakao = new KakaoAlrimLuna;
                    $encodedContent = $oKakao->sendLunaId($postData);

                    $this->json(['success' => true, 'data' => $encodedContent]);
                } catch (\Throwable $e) {
                    \Logger::channel('kakao')->warning($e->getMessage(), [__METHOD__, $e->getTraceAsString()]);
                    $this->json(['success' => false, 'message' => $e->getMessage()]);
                }
                break;
            case 'logout':
                try {
                    $oKakao = new KakaoAlrimLuna;
                    $deleteResult = $oKakao->deleteLunaKey();

                    if ($deleteResult['result'] !== 'success') {
                        $this->json(['success' => false, 'message' => '처리중에 오류가 발생하여 실패되었습니다.']);
                    }

                    $updateData = ['lunaKeyDel' => 'y'];

                    /** @var Policy $policy */
                    $policy = \App::load('\\Component\\Policy\\Policy');
                    $result = $policy->saveKakaoAlrimLunaConfig($updateData);
                    if ($result) {
                        $tmpkakaoAutoSet = gd_policy('kakaoAlrimLuna.kakaoAuto');
                        $tmpLunaSet = gd_array_merge($tmpkakaoAutoSet, array('useFlag' => 'n'));
                        gd_set_policy('kakaoAlrimLuna.kakaoAuto', $tmpLunaSet);

                        /** @var KakaoAlimTalkSettingService $kakaoAlimTalkSettingService */
                        $kakaoAlimTalkSettingService = \App::getInstance(KakaoAlimTalkSettingService::class);
                        $kakaoAlimTalkConfig = $kakaoAlimTalkSettingService->getKakaoAlimTalkConfig();

                        // 루나 알림톡 사용안함 처리
                        if ($kakaoAlimTalkConfig->getSender() === KakaoAlimTalkSender::BLUMN_AI && $kakaoAlimTalkConfig->getUseFlag() === 'y') {
                            $kakaoAlimTalkConfig->setUseFlag('n');

                            /** @var MessageConfigService $messageConfigService */
                            $messageConfigService = \App::getInstance(MessageConfigService::class);
                            $messageConfigService->save(null, null, null, null, $kakaoAlimTalkConfig, null);
                        }
                    }

                    $this->json(['success' => $result]);
                    break;
                } catch (\Throwable $e) {
                    \Logger::channel('kakao')->warning($e->getMessage(), [__METHOD__, $e->getTraceAsString()]);
                    $this->json(['success' => false, 'message' => $e->getMessage()]);
                }
                break;
            default:
                throw new AlertCloseException(__("잘못된 접근입니다."));
        }
    }
}
