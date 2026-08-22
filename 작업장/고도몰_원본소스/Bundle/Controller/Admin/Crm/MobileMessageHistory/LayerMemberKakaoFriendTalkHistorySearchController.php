<?php
/*
 * Copyright (C) 2026 NHN COMMERCE. - All Rights Reserved
 *
 * Unauthorized copying or redistribution of this file in source and binary forms via any medium
 * is strictly prohibited.
 */

namespace Bundle\Controller\Admin\Crm\MobileMessageHistory;

use Framework\Http\Request;
use Origin\Enum\ApiClient\Apps\KakaoFriendTalkResultSearchType;
use Origin\Enum\ApiClient\Apps\KakaoFriendTalkSendStatus;
use Origin\Enum\ApiClient\Apps\KakaoFriendTalkTriggerType;

class LayerMemberKakaoFriendTalkHistorySearchController extends \Controller\Admin\Controller
{
    public function index()
    {
        /** @var Request $request */
        $request = \App::getInstance('request');
        $postData = $request->post()->toArray();
        $memberNo = filter_var($postData['memberNo'] ?? 0, FILTER_VALIDATE_INT) ?: 0;

        $triggerTypes = KakaoFriendTalkTriggerType::cases();
        $sendStatuses = KakaoFriendTalkSendStatus::availableForMemberHistorySearch();
        $searchType = KakaoFriendTalkResultSearchType::generateSelectBoxData();

        $this->setData('memberNo', $memberNo);
        $this->setData('searchType', $searchType);
        $this->setData('triggerTypes', $triggerTypes);
        $this->setData('sendStatuses', $sendStatuses);
        $this->getView()->setDefine('layout', 'layout_layer.php');
    }
}
