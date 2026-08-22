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

class LayerMessagePointController extends \Controller\Admin\Controller
{
    public function index()
    {
        $this->callMenu('crm', 'messageSend', 'mobileSend');

        // 현재 SMS 포인트 싱크
        Sms::saveSmsPoint();

        // 현재 메시지 포인트
        $nowSmsPoint = (float) Sms::getPoint();

        $availableCount = [
            'sms' => SmsUtil::getAvailableCount('sms', $nowSmsPoint),
            'lms' => SmsUtil::getAvailableCount('lms', $nowSmsPoint),
            'kakaoAlrimTalk' => SmsUtil::getAvailableCount('kakaoAlrimTalk', $nowSmsPoint),
            'kakaoFriendTalk' => SmsUtil::getAvailableCount('kakaoFriendTalk', $nowSmsPoint),
        ];

        $this->setData('availableCount', $availableCount);
        $this->getView()->setDefine('layout', 'layout_layer.php');
    }
}
