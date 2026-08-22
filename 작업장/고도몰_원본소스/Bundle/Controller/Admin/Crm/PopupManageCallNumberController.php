<?php
/*
 * Copyright (C) 2025 NHN COMMERCE. - All Rights Reserved
 *
 * Unauthorized copying or redistribution of this file in source and binary forms via any medium
 * is strictly prohibited.
 */

namespace Bundle\Controller\Admin\Crm;

use Framework\Debug\Exception\AlertCloseException;
use Origin\Service\Crm\Message\SmsCallerService;

class PopupManageCallNumberController extends \Controller\Admin\Controller
{
    public function index()
    {
        $this->callMenu('crm', 'messageConfig', 'manageCallNumber');
        $this->setMenuCode('crm', 'messageConfig', 'manageCallNumber');

        /** @var SmsCallerService $smsCallerService */
        $smsCallerService = \App::getInstance(SmsCallerService::class);

        $smsCallerList = $smsCallerService->getSmsCallerList()->getSmsCallerList();
        // 해당 검증은 발신 관리책임자가 연동되어 있지 않을 경우에만 발동되므로, 방어코드 추가
        $selectedCaller = array_values(array_filter($smsCallerList, fn($item) => $item->isSelected()));

        if (empty($selectedCaller)) {
            throw new AlertCloseException('발신 관리책임자가 연동 되어 있지 않습니다.');
        }

        $this->setData('caller', $selectedCaller[0]);

        $this->getView()->setDefine('layout', 'layout_blank.php');
    }
}
