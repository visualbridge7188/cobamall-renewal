<?php
/*
 * Copyright (C) 2026 NHN COMMERCE. - All Rights Reserved
 *
 * Unauthorized copying or redistribution of this file in source and binary forms via any medium
 * is strictly prohibited.
 */

namespace Bundle\Controller\Admin\Crm\MobileMessageHistory;

use Framework\Http\Request;
use Origin\Enum\ApiClient\Apps\MyappPushNotificationType;
use Origin\Service\Crm\Message\MyappPushHistoryService;

class LayerMyappPushContentsController extends \Controller\Admin\Controller
{
    public function index()
    {
        $this->callMenu('crm', 'messageHistory', 'mobileHistoryList');
        $this->setMenuCode('crm', 'messageHistory', 'mobileHistoryList');

        /** @var Request $request */
        $request = \App::getInstance('request');
        $pushNo = $request->post()->get('pushNo');

        try {
            /** @var MyappPushHistoryService $myappPushHistoryService */
            $myappPushHistoryService = \App::getInstance(MyappPushHistoryService::class);
            $myappPushHistoryContentDTO = $myappPushHistoryService->getMyappPushHistoryContent($pushNo);
        } catch (\Throwable $e) {
            \Logger::channel('mobileMessage')->error($e->getMessage(), ['pushNo' => $pushNo]);
            exit;
        }

        $this->setData('title', $myappPushHistoryContentDTO->getTitle());
        $this->setData('content', $myappPushHistoryContentDTO->getContent());
        $this->setData('unsubscribeGuide', $myappPushHistoryContentDTO->getUnsubscribeGuide());
        $this->setData('imageUrl', $myappPushHistoryContentDTO->getImageUrl());
        $this->setData('pushUrl', $myappPushHistoryContentDTO->getPushUrl());

        $this->getView()->setDefine('layout', 'layout_layer.php');
    }
}
