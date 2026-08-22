<?php
/*
 * Copyright (C) 2026 NHN COMMERCE. - All Rights Reserved
 *
 * Unauthorized copying or redistribution of this file in source and binary forms via any medium
 * is strictly prohibited.
 */

namespace Bundle\Controller\Admin\Crm\MobileSend;

use Component\Sms\SmsUtil;
use Component\Sms\Sms;
use Framework\Http\Request;
use Origin\Enum\Crm\Message\RecipientType;

class LayerRecipientSettingController extends \Controller\Admin\Controller
{
    public function index()
    {
        $this->callMenu('crm', 'messageSend', 'mobileSend');

        /** @var Request $request */
        $request = \App::getInstance('request');
        $selectedCrmGroup = $request->post()->get('selectedCrmGroup');

        // PG 설정 불러오기
        $pgConfig = gd_pgs();
        $isConnectedPg = ($pgConfig['pgAutoSetting'] === 'y' || $pgConfig['pgApprovalSetting'] === 'y') ? 'y' : 'n';

        $this->setData('recipientTypes', RecipientType::cases());
        $this->setData('isConnectedPg', $isConnectedPg);
        $this->setData('selectedCrmGroup', $selectedCrmGroup);
        $this->getView()->setDefine('layout', 'layout_layer.php');
    }
}
