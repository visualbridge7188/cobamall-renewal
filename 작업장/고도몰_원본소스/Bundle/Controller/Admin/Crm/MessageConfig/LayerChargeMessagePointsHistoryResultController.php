<?php
/*
 * Copyright (C) 2025 NHN COMMERCE. - All Rights Reserved
 *
 * Unauthorized copying or redistribution of this file in source and binary forms via any medium
 * is strictly prohibited.
 */

namespace Bundle\Controller\Admin\Crm\MessageConfig;

use Framework\Http\Request;
use Origin\DTO\Member\Sms\SmsPointChargeDTO;
use Origin\Service\Member\Sms\SmsPointChargeService;

class LayerChargeMessagePointsHistoryResultController extends \Controller\Admin\Controller
{
    public function index()
    {
        $this->callMenu('crm', 'messageConfig', 'chargeMessagePoints');
        $naviMenu = $this->getData('naviMenu');

        /** @var Request $request */
        $request = \App::getInstance('request');
        $requestData = $request->post()->toArray();

        /** @var SmsPointChargeService $smsPointChargeService */
        $smsPointChargeService = \App::getInstance(SmsPointChargeService::class);

        $smsPointChargeListResponseDTO = $smsPointChargeService->getSmsPointChargeList(
            $naviMenu->lno['2'],
            $requestData['page'] ?? SmsPointChargeService::DEFAULT_PAGE,
            $requestData['pageSize'] ?? SmsPointChargeService::DEFAULT_CRM_PAGE_SIZE
        );

        $smsPointChargeList = array_map(function(SmsPointChargeDTO $dto) {
            $item = $dto->toArray();
            $item['payTypeName'] = $dto->getPayTypeName(); // 원하는 값으로 변경
            return $item;
        }, $smsPointChargeListResponseDTO->getSmsPointChargeList());

        $page = $smsPointChargeListResponseDTO->getPage();

        $this->setData('smsPointChargeList', $smsPointChargeList);
        $this->setData('page', $page);
        $this->getView()->setDefine('layout', 'layout_layer.php');
    }
}
