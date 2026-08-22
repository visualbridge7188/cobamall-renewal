<?php
/*
 * Copyright (C) 2026 NHN COMMERCE. - All Rights Reserved
 *
 * Unauthorized copying or redistribution of this file in source and binary forms via any medium
 * is strictly prohibited.
 */

namespace Bundle\Controller\Admin\Crm\MobileSend;

use Origin\Enum\Common\WeekDay;
use Origin\Enum\Crm\Message\MessageRepeatCycle;
use Origin\Enum\Crm\Message\MessageSendType;

class LayerSendTimeSettingController extends \Controller\Admin\Controller
{
    public function index()
    {
        $this->callMenu('crm', 'messageSend', 'mobileSend');

        $this->setData('messageSendTypes', MessageSendType::cases());
        $this->setData('messageRepeatCycles', MessageRepeatCycle::cases());
        $this->setData('weekDays', WeekDay::cases());
        $this->getView()->setDefine('layout', 'layout_layer.php');
    }
}
