<?php
/*
 * Copyright (C) 2025 NHN COMMERCE. - All Rights Reserved
 *
 * Unauthorized copying or redistribution of this file in source and binary forms via any medium
 * is strictly prohibited.
 */

namespace Bundle\Controller\Admin\Crm\MobileMessageHistory;

use Origin\Enum\ApiClient\Notification\SmsResultSearchType;
use Origin\Enum\ApiClient\Notification\SmsSendStatus;
use Origin\Enum\ApiClient\Notification\SmsTriggerType;
use Origin\Enum\ApiClient\Notification\SmsType;

class LayerSmsHistorySearchController extends \Controller\Admin\Controller
{
    public function index()
    {
        $triggerTypes = SmsTriggerType::cases();
        $sendStatuses = SmsSendStatus::availableForSearch();
        $smsTypes = SmsType::cases();
        $searchType = SmsResultSearchType::generateSelectBoxData();

        $this->setData('triggerTypes', $triggerTypes);
        $this->setData('smsTypes', $smsTypes);
        $this->setData('sendStatuses', $sendStatuses);
        $this->setData('searchType', $searchType);
        $this->getView()->setDefine('layout', 'layout_layer.php');
    }
}
