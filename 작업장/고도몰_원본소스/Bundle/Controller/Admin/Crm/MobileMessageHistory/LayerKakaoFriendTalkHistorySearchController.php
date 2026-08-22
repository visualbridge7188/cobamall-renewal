<?php
/*
 * Copyright (C) 2025 NHN COMMERCE. - All Rights Reserved
 *
 * Unauthorized copying or redistribution of this file in source and binary forms via any medium
 * is strictly prohibited.
 */

namespace Bundle\Controller\Admin\Crm\MobileMessageHistory;

use Origin\Enum\ApiClient\Apps\KakaoFriendTalkResultSearchType;
use Origin\Enum\ApiClient\Apps\KakaoFriendTalkScheduleType;
use Origin\Enum\ApiClient\Apps\KakaoFriendTalkSendStatus;

class LayerKakaoFriendTalkHistorySearchController extends \Controller\Admin\Controller
{
    public function index()
    {
        $scheduleTypes = KakaoFriendTalkScheduleType::cases();
        $sendResultStatuses = KakaoFriendTalkSendStatus::availableForSearch();
        $searchType = KakaoFriendTalkResultSearchType::generateSelectBoxData();

        $this->setData('searchType', $searchType);
        $this->setData('scheduleTypes', $scheduleTypes);
        $this->setData('sendResultStatuses', $sendResultStatuses);
        $this->getView()->setDefine('layout', 'layout_layer.php');
    }
}
