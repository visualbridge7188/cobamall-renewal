<?php
/*
 * Copyright (C) 2025 NHN COMMERCE. - All Rights Reserved
 *
 * Unauthorized copying or redistribution of this file in source and binary forms via any medium
 * is strictly prohibited.
 */

namespace Bundle\Controller\Admin\Crm\MobileMessageHistory;

use Framework\Http\Request;
use Framework\Http\Response;
use Origin\DTO\ApiClient\Commerce\Notification\SmsHistoryContentRequestDTO;
use Origin\Enum\ApiClient\Notification\NotificationSendType;
use Origin\Service\Crm\Message\SmsHistoryService;

class LayerSmsContentsController extends \Controller\Admin\Controller
{
    public function index()
    {
        $this->callMenu('crm', 'messageHistory', 'mobileHistoryList');
        $this->setMenuCode('crm', 'messageHistory', 'mobileHistoryList');

        /** @var Request $request */
        $request = \App::getInstance('request');
        $sendGroupKey = $request->post()->get('sendGroupKey');

        try {
            $requestDTO = (new SmsHistoryContentRequestDTO(['type' => NotificationSendType::SMS->name]));
            /** @var SmsHistoryService $smsHistoryService */
            $smsHistoryService = \App::getInstance(SmsHistoryService::class);
            $smsHistoryContentDTO = $smsHistoryService->getSmsHistoryContent($sendGroupKey, $requestDTO);
        } catch (\Throwable $e) {
            $this->json(['success' => false], Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        $this->setData('title', $smsHistoryContentDTO->getTitle());
        $this->setData('content', $smsHistoryContentDTO->getContent());

        $this->getView()->setDefine('layout', 'layout_layer.php');
    }
}
