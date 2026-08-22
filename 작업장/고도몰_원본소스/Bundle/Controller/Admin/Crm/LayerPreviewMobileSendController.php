<?php
/*
 * Copyright (C) 2025 NHN COMMERCE. - All Rights Reserved
 *
 * Unauthorized copying or redistribution of this file in source and binary forms via any medium
 * is strictly prohibited.
 */

namespace Bundle\Controller\Admin\Crm;

use Framework\StaticProxy\Proxy\UserFilePath;
use Origin\Service\Crm\Message\KakaoFriendTalkSettingService;
use Origin\Service\Crm\Message\MyappPushSettingService;
use Request;
use Logger;
use Respect\Validation\Exceptions\ValidationException;
use Respect\Validation\Validator;

class LayerPreviewMobileSendController extends \Controller\Admin\Controller
{
    public function index()
    {
        try {
            $postValue = Request::post()->toArray();

            // 요청 검증
            Validator::key('sendMethod', Validator::in(['SMS', 'FRIENDTALK', 'ALIMTALK', 'MYAPP']))
                ->setTemplate('유효하지 않은 요청입니다.')
                ->assert($postValue);
            Validator::key('viewType', Validator::in(['layer', 'side']))
                ->setTemplate('유효하지 않은 요청입니다.')
                ->assert($postValue);
            Validator::key('payload', Validator::stringType()->notEmpty())
                ->setTemplate('유효하지 않은 요청입니다.')
                ->assert($postValue);

            $payload = json_decode($postValue['payload'], true);
            $sendMethod = $postValue['sendMethod'];

            // 공통 데이터
            $this->setData('sendMethod', $sendMethod);
            $this->setData('payloadJson', $postValue['payload']);
            $useAlternative = !empty($payload['enableAlternative']) ? 'y' : 'n';
            $this->setData('useAlternative', $useAlternative);

            $previewContents = "";
            $alternativePreviewContents = 'crm/preview_mobile_send/layer_preview_mobile_send_alternative.php';
            switch ($sendMethod) {
                case 'SMS':
                    $smsData = $payload['smsLmsRequest'] ?? [];
                    $this->setData('mainContents', $smsData['content'] ?? '');
                    $this->setData('showAdWarningText', 'n');
                    $previewContents = 'crm/preview_mobile_send/layer_preview_mobile_send_sms.php';
                    break;
                case 'FRIENDTALK':
                    $friendTalkData = $payload['kakaoFriendtalkRequest'] ?? [];

                    /** @var KakaoFriendTalkSettingService $kakaoFriendTalkSettingService */
                    $kakaoFriendTalkSettingService = \App::getInstance(KakaoFriendTalkSettingService::class);
                    $kakaoFriendTalkConfig = $kakaoFriendTalkSettingService->getKakaoFriendTalkConfig();

                    $showAdWarningText = 'n';
                    if ($kakaoFriendTalkConfig->getSmsAlternativeSendFlag() === 'y' && !$friendTalkData['alternativeInfo']['isAdText'] && $friendTalkData['type'] !== 'WIDE_ITEM_LIST' && $friendTalkData['type'] !== 'CAROUSEL_FEED') {
                        $showAdWarningText = 'y';
                    }

                    $this->setData('friendtalk', $friendTalkData);
                    $this->setData('alternativeContents', $friendTalkData['alternativeInfo']['content'] ?? '');
                    $this->setData('showAdWarningText', $showAdWarningText);
                    $previewContents = 'crm/preview_mobile_send/layer_preview_mobile_send_kakao_friendtalk.php';
                    break;
                case 'ALIMTALK':
                    $alimTalkData = $payload['kakaoAlimtalkRequest'] ?? [];
                    $previewMeta = json_decode($postValue['previewMeta'] ?? '{}', true);
                    $hasAddChannelButton = !empty(array_filter(
                        $previewMeta['templateButtons'] ?? [],
                        fn($button) => ($button['type'] ?? '') === 'ADD_CHANNEL'
                    ));
                    $this->setData('template', $previewMeta);
                    $this->setData('hasAddChannelButton', $hasAddChannelButton);
                    $this->setData('alternativeContents', $alimTalkData['alternativeInfo']['content'] ?? '');
                    $this->setData('showAdWarningText', 'n');
                    $previewContents = 'crm/preview_mobile_send/layer_preview_mobile_send_kakao_alimtalk.php';
                    break;
                case 'MYAPP':
                    $myappPushData = $payload['myappRequest'] ?? [];

                    /** @var MyappPushSettingService $myappPushSettingService */
                    $myappPushSettingService = \App::getInstance(MyappPushSettingService::class);
                    $myappPushConfig = $myappPushSettingService->getMyappPushConfig();
                    $showAdWarningText = 'n';
                    if ($myappPushData['notificationType'] === 'AD' && $myappPushConfig->getManualSmsAlternativeSendFlag() === 'y' && !$myappPushData['alternativeInfo']['isAdText']) {
                        $showAdWarningText = 'y';
                    }
                    $this->setData('myappPayload', $myappPushData);
                    $this->setData('showAdWarningText', $showAdWarningText);
                    $this->setData('alternativeContents', $payload['myappRequest']['alternativeInfo']['content'] ?? '');
                    $previewContents = 'crm/preview_mobile_send/layer_preview_mobile_send_myapp_push.php';
                    break;
            }

            if($postValue['viewType'] === 'side') {
                ob_start();
                $paths = explode('/', $previewContents);
                include UserFilePath::adminSkin(...$paths);
                if($out = ob_get_clean()) {
                    echo $out;
                }
                exit;
            } else {
                $this->getView()->setDefine('layout', 'layout_layer.php');
                $this->getView()->setDefine('previewContents', $previewContents);
                $this->getView()->setDefine('alternativePreviewContents', $alternativePreviewContents);
            }
        } catch (ValidationException $e) {
            Logger::channel('mobileMessage')->warning(__CLASS__ . ' 모바일 메시지 전송 처리 검증 실패 : ', [
                'error' => $e->getMainMessage(),
                'postValue' => $postValue ?? []
            ]);

            $this->alert(__('잘못된 요청입니다 발송전 미리보기 로드에 실패하였습니다. 잠시후 다시 시도해주세요.'));
        } catch (\Throwable $e) {
            Logger::channel('mobileMessage')->warning(__CLASS__ . ' 모바일 메시지 전송 처리 중 에러 발생 : ', [
                'error' => $e->getMessage(),
                'postValue' => $postValue ?? []
            ]);
            $this->alert(__('발송전 미리보기 로드에 실패하였습니다. 잠시후 다시 시도해주세요.'));
        }
    }
}
