<?php
/*
 * Copyright (C) 2025 NHN COMMERCE. - All Rights Reserved
 *
 * Unauthorized copying or redistribution of this file in source and binary forms via any medium
 * is strictly prohibited.
 */

namespace Bundle\Controller\Admin\Crm\MessageConfig;

use Bundle\Component\Sms\SmsAdmin;
use Framework\Debug\Exception\AlertCloseException;
use Framework\Http\Request;
use Origin\Service\Crm\Message\SmsCallerService;

class LayerManageCallNumberResultController extends \Controller\Admin\Controller
{
    public function index()
    {
        $this->callMenu('crm', 'messageConfig', 'manageCallNumber');
        $this->setMenuCode('crm', 'messageConfig', 'manageCallNumber');

        /** @var Request $request */
        $request = \App::getInstance('request');
        $page = $request->post()->get('page', 1);

        /** @var SmsCallerService $smsCallerService */
        $smsCallerService = \App::getInstance(SmsCallerService::class);

        $smsCallerList = $smsCallerService->getSmsCallerList()->getSmsCallerList();
        // 해당 검증은 발신 관리책임자가 연동되어 있지 않을 경우에만 발동되므로, 방어코드 추가
        $selectedCaller = array_values(array_filter($smsCallerList, fn($item) => $item->isSelected()));

        if (empty($selectedCaller)) {
            throw new AlertCloseException('발신 관리책임자가 연동 되어 있지 않습니다.');
        }

        $caller = $selectedCaller[0];
        $smsCallNumberListResponseDTO = $smsCallerService->getSmsNumberListByCallerNo($caller->getCallerNo(), $page);

        $smsCallNumberList = $smsCallNumberListResponseDTO->getSmsCallNumberList();
        $page = $smsCallNumberListResponseDTO->getPage();

        /** @var SmsAdmin $smsAdmin */
        $smsAdmin = \App::load('Component\\Sms\\SmsAdmin');
        $smsAutoData = $smsAdmin->getSmsAutoData();
        $currentCallNumber = $smsAutoData['smsCallNum'] ?? '';

        $this->setData('caller', $selectedCaller[0]);
        $this->setData('currentCallNumber', $currentCallNumber);
        $this->setData('smsCallNumberList', $smsCallNumberList);
        $this->setData('page', $page);

        $this->getView()->setDefine('layout', 'layout_layer.php');
    }
}
