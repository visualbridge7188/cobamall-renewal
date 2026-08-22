<?php
/*
 * Copyright (C) 2025 NHN COMMERCE. - All Rights Reserved
 *
 * Unauthorized copying or redistribution of this file in source and binary forms via any medium
 * is strictly prohibited.
 */

namespace Bundle\Controller\Admin\Crm;

use App;
use Component\Sms\Sms;
use Component\Sms\SmsUtil;
use DateTime;
use Origin\DTO\ApiClient\Commerce\Apps\MyappPushHistorySummaryRequestDTO;
use Origin\DTO\ApiClient\Commerce\Apps\MyappPushHistorySummaryResponseDTO;
use Origin\DTO\ApiClient\Commerce\Crm\CampaignCountsRequestDTO;
use Origin\DTO\ApiClient\Commerce\Crm\CampaignCountsResponseDTO;
use Origin\DTO\ApiClient\Commerce\Notification\MessageHistorySummaryResponseDTO;
use Origin\Enum\ApiClient\Crm\CampaignSendType;
use Origin\Enum\ApiClient\Crm\CampaignStatusType;
use Origin\Service\AutoSend\ActiveAutoSendService;
use Origin\Service\Crm\Message\CrmNotificationCampaignService;
use Origin\Service\Crm\Message\MessageHistoryService;
use Origin\Service\Crm\Message\MyappPushHistoryService;

class MobileHistoryController extends \Controller\Admin\Controller
{
    public function index()
    {
        $this->callMenu('crm', 'messageHistory', 'mobileHistory');
        $this->setMenuCode('crm', 'messageHistory', 'mobileHistory');

        try {
            /** @var MessageHistoryService $messageHistoryService */
            $messageHistoryService = \App::getInstance(MessageHistoryService::class);
            $messageHistorySummaryResponseDTO = $messageHistoryService->getMessageHistorySummary();

        } catch (\Throwable $e) {
            \Logger::channel('mobileMessage')->error($e->getMessage());
            $messageHistorySummaryResponseDTO = new MessageHistorySummaryResponseDTO(null);
        }

        try {
            $requestDTO = (new MyappPushHistorySummaryRequestDTO())
                ->setStartDate(new DateTime('-7 days'))
                ->setEndDate(new DateTime('-1 days'));

            /** @var MyappPushHistoryService $myappPushHistoryService */
            $myappPushHistoryService = \App::getInstance(MyappPushHistoryService::class);
            $myappPushHistorySummaryResponseDTO = $myappPushHistoryService->getMyappPushHistorySummary($requestDTO);

        } catch (\Throwable $e) {
            \Logger::channel('mobileMessage')->error($e->getMessage());
            $myappPushHistorySummaryResponseDTO = new MyappPushHistorySummaryResponseDTO(null);
        }

        try {
            $requestDTO = (new CampaignCountsRequestDTO())
                ->setSendType([CampaignSendType::SCHEDULED, CampaignSendType::REPEAT])
                ->setStatusType([CampaignStatusType::WAITING, CampaignStatusType::PROCESSING]);


            /** @var CrmNotificationCampaignService $crmNotificationCampaignService */
            $crmNotificationCampaignService = \App::getInstance(CrmNotificationCampaignService::class);
            $crmNotificationCampaignCountsResponseDTO = $crmNotificationCampaignService->getNotificationCampaignCounts($requestDTO);
        } catch (\Throwable $e) {
            \Logger::channel('mobileMessage')->error($e->getMessage());
            $crmNotificationCampaignCountsResponseDTO = new CampaignCountsResponseDTO(null);
        }

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

        /** @var ActiveAutoSendService $activeAutoSendService */
        $activeAutoSendService = \App::getInstance(ActiveAutoSendService::class);
        $autoSendCounts = [
            'active' => $activeAutoSendService->count(),
        ];

        $this->setData('availableCount', $availableCount);

        $this->setData('messageCountLast7Days', $messageHistorySummaryResponseDTO->getMessageCountLast7Days());
        $this->setData('friendtalkCountLast7Days', $messageHistorySummaryResponseDTO->getFriendtalkCountLast7Days());
        $this->setData('alimtalkCountLast7Days', $messageHistorySummaryResponseDTO->getAlimtalkCountLast7Days());
        $this->setData('myappPushCountLast7Days', $myappPushHistorySummaryResponseDTO->getTotalPushMessageCount());
        $this->setData('pointUsageLast7Days', $messageHistorySummaryResponseDTO->getPointUsageLast7Days());
        $this->setData('pointUsageLast28Days', $messageHistorySummaryResponseDTO->getPointUsageLast28Days());
        $this->setData('crmNotificationCampaignCounts', $crmNotificationCampaignCountsResponseDTO->getStatusCount());
        $this->setData('autoSendCounts', $autoSendCounts);

        $this->setData('scheduledSendWaitingUrl', './scheduled_send.php?statusType=WAITING');
        $this->setData('scheduledSendRepeatUrl', './scheduled_send.php?formType=REPEAT&statusType=PROCESSING&periodType=1YEAR&createdAtFrom=' . date('Y-m-d', strtotime('-1 year')) . '&createdAtTo=' . date('Y-m-d'));
        $this->setData('autoSendUrl', './auto_send.php?shouldAutoSend=y');

        $this->getView()->setPageName('crm/mobile_history.php');
    }
}
