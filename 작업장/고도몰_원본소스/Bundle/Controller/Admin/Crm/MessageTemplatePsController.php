<?php
/*
 * Copyright (C) 2026 NHN COMMERCE. - All Rights Reserved
 *
 * Unauthorized copying or redistribution of this file in source and binary forms via any medium is strictly prohibited.
 */

namespace Bundle\Controller\Admin\Crm;

use Framework\Http\Request;
use Origin\DTO\ApiClient\Commerce\Notification\MessageTemplateCopyRequestDTO;
use Origin\DTO\ApiClient\Commerce\Notification\MessageTemplateMyappDTO;
use Origin\DTO\ApiClient\Commerce\Notification\MessageTemplateRemoveRequestDTO;
use Origin\DTO\ApiClient\Commerce\Notification\MessageTemplateSmsDTO;
use Origin\Service\Crm\Message\MessageTemplateFindService;
use Origin\Service\Crm\Message\MessageTemplateRegisterService;
use Origin\Service\Crm\Message\MessageTemplateRemoveService;

class MessageTemplatePsController extends \Controller\Admin\Controller
{
    public function index()
    {
        /** @var Request $request */
        $request = \App::getInstance('request');
        $requestData = $request->request()->toArray();
        $mode = $requestData['mode'] ?? '';

        switch ($mode) {
            case 'delete':
                $requestDto = new MessageTemplateRemoveRequestDTO($requestData);
                /** @var MessageTemplateRemoveService $service */
                $service = \App::getInstance(MessageTemplateRemoveService::class);
                $result = $service->deleteTemplate($requestDto);
                break;
            case 'copy':
                $requestDto = new MessageTemplateCopyRequestDTO($requestData);
                /** @var MessageTemplateRegisterService $service */
                $service = \App::getInstance(MessageTemplateRegisterService::class);
                $result = $service->copyTemplate($requestDto);
                break;
            default:
                $result = ['success' => false, 'message' => '잘못된 요청입니다.'];
                break;
        }
        $this->json($result);
    }
}
