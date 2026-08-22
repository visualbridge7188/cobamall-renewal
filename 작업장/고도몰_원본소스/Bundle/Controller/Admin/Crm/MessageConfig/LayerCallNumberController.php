<?php
/*
 * Copyright (C) 2025 NHN COMMERCE. - All Rights Reserved
 *
 * Unauthorized copying or redistribution of this file in source and binary forms via any medium
 * is strictly prohibited.
 */

namespace Bundle\Controller\Admin\Crm\MessageConfig;

use Bundle\Component\Godo\GodoSmsServerApi;
use Bundle\Component\Sms\SmsAdmin;
use Origin\Service\Crm\Message\SmsCallerService;

class LayerCallNumberController extends \Controller\Admin\Controller
{
    public function index()
    {
        /** @var SmsAdmin $smsAdmin */
        $smsAdmin = \App::load('Component\\Sms\\SmsAdmin');
        $smsAutoData = $smsAdmin->getSmsAutoData();

        /** @var GodoSmsServerApi $godoSms */
        $godoSms = \App::load('Component\\Godo\\GodoSmsServerApi');
        $smsPreRegister = $godoSms->checkSmsCallNumber($smsAutoData['smsCallNum']);

        /** @var SmsCallerService $smsCallerService */
        $smsCallerService = \App::getInstance(SmsCallerService::class);

        // 발신 관리책임자 목록 조회
        $smsCallerList = $smsCallerService->getSmsCallerList()->getSmsCallerList();

        // 해당 검증은 발신 관리책임자가 연동되어 있지 않을 경우에만 발동되므로, 방어코드 추가
        $hasCaller = !empty(array_values(array_filter($smsCallerList, fn($item) => $item->isSelected())));

        $this->setData('hasCaller', $hasCaller);
        $this->setData('smsPreRegister', $smsPreRegister);
        $this->setData('smsAutoData', gd_htmlspecialchars($smsAutoData));

        $this->getView()->setDefine('layout', 'layout_layer.php');
    }
}
