<?php
/*
 * Copyright (C) 2026 NHN COMMERCE. - All Rights Reserved
 *
 * Unauthorized copying or redistribution of this file in source and binary forms via any medium
 * is strictly prohibited.
 */

namespace Bundle\Controller\Admin\Crm;

use Bundle\Component\Sms\Code;
use Bundle\Component\Sms\Sms;
use Bundle\Component\Sms\SmsUtil;
use Component\Admin\AdminMenu;
use Origin\Enum\AutoSend\AutoSendRecipient;
use Origin\Enum\AutoSend\AutoSendSupport;
use Origin\Service\AutoSend\SearchAutoSendListService;
use Origin\Service\AutoSend\SearchAutoSendsRequest;
use Request;

class AutoSendController extends \Controller\Admin\Controller
{
    public function index()
    {
        $request = Request::get()->toArray();

        $this->callMenu('crm', 'messageSend', 'autoSend');

        $smsPoint = (float) Sms::getPoint();
        $sendableMethodCountByPoint = [
            'sms' => SmsUtil::getAvailableCount('sms', $smsPoint),
            'lms' => SmsUtil::getAvailableCount('lms', $smsPoint),
            'kakaoAlrimTalk' => SmsUtil::getAvailableCount('kakaoAlrimTalk', $smsPoint),
            'kakaoFriendTalk' => SmsUtil::getAvailableCount('kakaoFriendTalk', $smsPoint),
        ];
        Sms::saveSmsPoint();

        /** @var SearchAutoSendListService $findAutoSendService */
        $findAutoSendService = \App::getInstance(SearchAutoSendListService::class);
        $response = $findAutoSendService->search(SearchAutoSendsRequest::fromArray($request));

        $this->setData('shouldExpandRecommend', AutoSendSupport::shouldExpandRecommend());
        $this->setData('newIconCodes', AutoSendSupport::getNewIconCodes());
        $this->setData('useJoinPolicy', AutoSendSupport::useJoinPolicy());
        $this->setData('autoSends', $response['autoSends']);
        $this->setData('recommendAutoSends', $response['recommendAutoSends']);
        $this->setData('pageCounts', $response['pageCounts']);
        $this->setData('smsPoint', $smsPoint);
        $this->setData('sendableMethodCountByPoint', $sendableMethodCountByPoint);
        $this->setData('recipients', AutoSendRecipient::cases());
        $this->setData('request', $request);
        $this->getView()->setPageName('crm/auto_send.php');
        $this->getView()->setDefine('messagePoint', 'crm/auto_send/layer_list_message_point.php');
        $this->getView()->setDefine('autoRecommend', 'crm/auto_send/layer_list_recommend.php');
        $this->getView()->setDefine('autoListSearch', 'crm/auto_send/layer_list_search.php');
        $this->getView()->setDefine('autoList', 'crm/auto_send/layer_list.php');
    }
}
