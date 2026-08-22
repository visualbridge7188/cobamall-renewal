<?php
/*
 * Copyright (C) 2025 NHN COMMERCE. - All Rights Reserved
 *
 * Unauthorized copying or redistribution of this file in source and binary forms via any medium
 * is strictly prohibited.
 */

namespace Bundle\Controller\Admin\Crm\MessageConfig;

use Component\Sms\SmsUtil;
use Component\Sms\Sms;
use Origin\Service\Member\Sms\SmsPointChargeService;

class LayerMessagePointController extends \Controller\Admin\Controller
{
    public function index()
    {
        // 메뉴 설정
        $this->callMenu('crm', 'messageConfig', 'messageConfig');
        $naviMenu = $this->getData('naviMenu');

        // 현재 SMS 포인트 싱크
        Sms::saveSmsPoint();

        // 포인트 충전 내역
        /** @var SmsPointChargeService $smsPointChargeService */
        $smsPointChargeService = \App::getInstance(SmsPointChargeService::class);
        $existsChargeHistory = $smsPointChargeService->existsSmsPointChargeHistory($naviMenu->lno['2']);

        if ($existsChargeHistory) {
            // 현재 메시지 포인트
            $nowSmsPoint = (float) Sms::getPoint();

            $availableCount = [
                'sms' => SmsUtil::getAvailableCount('sms', $nowSmsPoint),
                'lms' => SmsUtil::getAvailableCount('lms', $nowSmsPoint),
                'kakaoAlrimTalk' => SmsUtil::getAvailableCount('kakaoAlrimTalk', $nowSmsPoint),
                'kakaoFriendTalk' => SmsUtil::getAvailableCount('kakaoFriendTalk', $nowSmsPoint),
            ];

            $this->setData('availableCount', $availableCount);
        }

        // 포인트 충전내역 존재 여부 set
        $this->setData('existsChargeHistory', $existsChargeHistory);

        $this->getView()->setDefine('layout', 'layout_layer.php');
    }
}
