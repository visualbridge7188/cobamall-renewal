<?php
/*
 * Copyright (C) 2026 NHN COMMERCE. - All Rights Reserved
 *
 * Unauthorized copying or redistribution of this file in source and binary forms via any medium
 * is strictly prohibited.
 */

namespace Bundle\Controller\Admin\Crm\MobileMessageHistory;

use Framework\Http\Request;
use Origin\Enum\ApiClient\Notification\SmsResultSearchType;
use Origin\Enum\ApiClient\Notification\SmsSendStatus;
use Origin\Enum\ApiClient\Notification\SmsTriggerType;
use Origin\Enum\ApiClient\Notification\SmsType;

class LayerMemberSmsHistorySearchController extends \Controller\Admin\Controller
{
    public function index()
    {
        /** @var Request $request */
        $request = \App::getInstance('request');
        $postData = $request->post()->toArray();

        $triggerTypes = SmsTriggerType::cases();
        $sendStatuses = SmsSendStatus::availableForMemberHistorySearch();
        $smsTypes = SmsType::cases();
        $memberNo = filter_var($postData['memberNo'] ?? 0, FILTER_VALIDATE_INT) ?: 0;
        $searchType = SmsResultSearchType::generateSelectBoxData((bool) $memberNo);

        $this->setData('memberNo', $memberNo);
        $this->setData('triggerTypes', $triggerTypes);
        $this->setData('smsTypes', $smsTypes);
        $this->setData('sendStatuses', $sendStatuses);
        $this->setData('searchType', $searchType);
        $this->getView()->setDefine('layout', 'layout_layer.php');
    }
}
