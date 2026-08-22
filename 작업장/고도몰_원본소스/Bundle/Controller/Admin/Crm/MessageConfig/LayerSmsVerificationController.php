<?php
/*
 * Copyright (C) 2025 NHN COMMERCE. - All Rights Reserved
 *
 * Unauthorized copying or redistribution of this file in source and binary forms via any medium
 * is strictly prohibited.
 */

namespace Bundle\Controller\Admin\Crm\MessageConfig;

use Origin\Service\Crm\Message\SmsPasswordService;

class LayerSmsVerificationController extends \Controller\Admin\Controller
{
    public function index()
    {
        $this->callMenu('crm', 'messageConfig', 'messageConfig');
        $this->setMenuCode('crm', 'messageConfig', 'messageConfig');

        try {
            /** @var SmsPasswordService $smsPasswordService */
            $smsPasswordService = \App::getInstance(SmsPasswordService::class);
            $smsPassword = $smsPasswordService->getSmsPassword(true);
            $isVerified = $smsPasswordService->verifySmsPassword($smsPassword);
        } catch (\Throwable $e) {
            \Logger::channel('mobileMessage')->error($e->getMessage());
            $isVerified = false;
        }

        $this->setData('smsPassword', $smsPassword);
        $this->setData('isVerified', $isVerified);
        $this->getView()->setDefine('layout', 'layout_layer.php');
    }
}
