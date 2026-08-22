<?php
/*
 * Copyright (C) 2026 NHN COMMERCE. - All Rights Reserved
 *
 * Unauthorized copying or redistribution of this file in source and binary forms via any medium
 * is strictly prohibited.
 */

namespace Bundle\Controller\Admin\Crm\MobileSend;

use Bundle\Util\Crm\RecipeContextResolverTrait;
use Origin\Enum\Common\WeekDay;
use Origin\Enum\Crm\Message\MessageRepeatCycle;

class LayerRecipeSendTimeSettingController extends \Controller\Admin\Controller
{
    use RecipeContextResolverTrait;

    public function index()
    {
        $this->callMenu('crm', 'messageSend', 'mobileSend');

        $sendSchedule = $this->resolveRecipeContext()['sendSchedule'] ?? [];
        $sendTimeView = $this->buildSendTimeView($sendSchedule);

        $this->setData('messageRepeatCycles', MessageRepeatCycle::cases());
        $this->setData('sendTimeView', $sendTimeView);
        $this->setData('weekDays', WeekDay::cases());
        $this->getView()->setDefine('layout', 'layout_layer.php');
    }

    protected function buildSendTimeView(array $sendSchedule): array
    {
        $selectedRepeatType = $sendSchedule['repeatType'] ?? 'WEEKLY';
        $repeatConfig = $sendSchedule['repeatConfig'] ?? [];
        $sendTime = $repeatConfig['sendTime'] ?? '';

        return [
            'selectedRepeatType' => $selectedRepeatType,
            'selectedRepeatHour' => $sendTime !== '' ? substr($sendTime, 0, 2) : '',
            'selectedRepeatMinutes' => $sendTime !== '' ? substr($sendTime, 3, 2) : '',
            'selectedRepeatDays' => $repeatConfig['repeatDays'] ?? [],
        ];
    }
}
