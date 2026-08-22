<?php
/*
 * Copyright (C) 2025 NHN COMMERCE. - All Rights Reserved
 *
 * Unauthorized copying or redistribution of this file in source and binary forms via any medium
 * is strictly prohibited.
 */

namespace Bundle\Controller\Admin\Crm\MessageConfig;

use Origin\Service\Crm\Message\SmsCallerService;

class LayerCallerConnectorController extends \Controller\Admin\Controller
{
    public function index()
    {
        $this->callMenu('crm', 'messageConfig', 'messageConfig');
        $this->setMenuCode('crm', 'messageConfig', 'messageConfig');

        /** @var SmsCallerService $smsCallerService */
        $smsCallerService = \App::getInstance(SmsCallerService::class);

        // 발신 관리책임자 목록 조회
        $smsCallerList = $smsCallerService->getSmsCallerList()->getSmsCallerList();

        $this->setData('smsCallerList', $smsCallerList);

        $this->getView()->setDefine('layout', 'layout_layer.php');
    }
}
