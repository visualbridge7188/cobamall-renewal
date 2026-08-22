<?php
/*
 * Copyright (C) 2025 NHN COMMERCE. - All Rights Reserved
 *
 * Unauthorized copying or redistribution of this file in source and binary forms via any medium
 * is strictly prohibited.
 */

namespace Bundle\Controller\Admin\Crm\MobileMessageHistory;

use Origin\Enum\ApiClient\Apps\MyappPushScheduleType;
use Origin\Enum\ApiClient\Apps\MyappPushSendStatus;
use Origin\Enum\ApiClient\Notification\MyappPushResultSearchType;

class LayerMyappPushHistorySearchController extends \Controller\Admin\Controller
{
    public function index()
    {
        $scheduleTypes = MyappPushScheduleType::cases();
        $sendStatuses = MyappPushSendStatus::availableForSearch();
        $searchType = MyappPushResultSearchType::generateSelectBoxData();

        $this->setData('searchType', $searchType);
        $this->setData('scheduleTypes', $scheduleTypes);
        $this->setData('sendStatuses', $sendStatuses);
        $this->getView()->setDefine('layout', 'layout_layer.php');
    }
}
