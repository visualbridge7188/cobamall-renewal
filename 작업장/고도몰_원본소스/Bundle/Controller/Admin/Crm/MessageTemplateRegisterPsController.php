<?php
/*
 * Copyright (C) 2026 NHN COMMERCE. - All Rights Reserved
 *
 * Unauthorized copying or redistribution of this file in source and binary forms via any medium is strictly prohibited.
 */

namespace Bundle\Controller\Admin\Crm;

use Origin\DTO\ApiClient\Commerce\Notification\MessageTemplateKakaoBizmDTO;
use Origin\DTO\ApiClient\Commerce\Notification\MessageTemplateKakaoCloudDTO;
use Origin\DTO\ApiClient\Commerce\Notification\MessageTemplateMyappDTO;
use Origin\DTO\ApiClient\Commerce\Notification\MessageTemplateRegisterRequestDTO;
use Origin\DTO\ApiClient\Commerce\Notification\MessageTemplateSmsDTO;
use Origin\Service\Crm\Message\MessageTemplateModifyService;
use Origin\Service\Crm\Message\MessageTemplateRegisterService;

class MessageTemplateRegisterPsController extends \Controller\Admin\Controller
{
    public function index()
    {
        try {
            $request = \App::getInstance('request');
            $requestData = $request->request()->toArray();
            $filesData = $request->files()->toArray();

            $requestData['mode'] = ($requestData['mode'] === 'copy') ? 'register' : $requestData['mode'];
            $requestDto = new MessageTemplateRegisterRequestDTO($requestData);

            switch ($requestDto->getMode()){
                case 'register':
                    /** @var MessageTemplateRegisterService $service */
                    $service = \App::getInstance(MessageTemplateRegisterService::class);
                    $result = $service->saveTemplate($requestDto, $requestData, $filesData);
                    break;
                case 'modify':
                    /** @var MessageTemplateModifyService $service */
                    $service = \App::getInstance(MessageTemplateModifyService::class);
                    $result = $service->updateTemplate($requestDto, $requestData, $filesData);
                    break;
                default:
                    $result = ['success' => false, 'message' => '잘못된 모드입니다.'];
                    break;
            }
            $this->json($result);
        } catch (\Throwable $e) {
            $this->json(['success' => false, 'message' => '등록, 수정에 실패하였습니다']);
        }
    }
}
