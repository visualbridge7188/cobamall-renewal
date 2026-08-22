<?php
/*
 * Copyright (C) 2025 NHN COMMERCE. - All Rights Reserved
 *
 * Unauthorized copying or redistribution of this file in source and binary forms via any medium
 * is strictly prohibited.
 */

namespace Bundle\Controller\Admin\Crm\MessageConfig;

use Bundle\Component\Member\KakaoAlrim;
use Bundle\Component\Policy\Policy;
use Framework\Debug\Exception\AlertCloseException;
use Framework\Http\Request;
use Origin\Enum\Crm\Message\KakaoAlimTalkSender;
use Origin\Service\Crm\Message\KakaoAlimTalkSettingService;
use Origin\Service\Crm\Message\KakaoFriendTalkSettingService;
use Origin\Service\Crm\Message\MessageConfigService;
use Origin\Service\Crm\Message\MyappPushSettingService;

class LayerBizmKakaoTalkChannelPsController extends \Controller\Admin\Controller
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
                    $oKakao = new KakaoAlrim;
                    $result = $oKakao->deleteKakaoKey();
                    if ($result['result'] !== 'success') {
                        $this->json(['success' => false, 'message' => '프로필키 삭제에 실패하였습니다.', 'data' => ['type' => 'fail_delete_kakao_key']]);
                    }

                    /** @var Policy $policy */
                    $policy = \App::load('\\Component\\Policy\\Policy');
                    $result = $policy->setValue('kakaoAlrim.config', []);

                    if (!$result) {
                        $this->json(['success' => false, 'message' => '설정 업데이트에 실패하였습니다.', 'data' => ['type' => 'fail_update_policy']]);
                        break;
                    }

                    $oKakao->deleteAllTemplate();

                    /** @var KakaoAlimTalkSettingService $kakaoAlimTalkSettingService */
                    $kakaoAlimTalkSettingService = \App::getInstance(KakaoAlimTalkSettingService::class);
                    $kakaoAlimTalkConfig = $kakaoAlimTalkSettingService->getKakaoAlimTalkConfig();

                    // 비즈엠 알림톡 사용안함 처리
                    if ($kakaoAlimTalkConfig->getSender() === KakaoAlimTalkSender::BIZM && $kakaoAlimTalkConfig->getUseFlag() === 'y') {
                        $kakaoAlimTalkConfig->setUseFlag('n');

                        /** @var MessageConfigService $messageConfigService */
                        $messageConfigService = \App::getInstance(MessageConfigService::class);
                        $messageConfigService->save(null, null, null, null, $kakaoAlimTalkConfig, null);
                    }

                    $this->json(['success' => true, 'message' => '프로필키 삭제에 성공하였습니다.']);
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
