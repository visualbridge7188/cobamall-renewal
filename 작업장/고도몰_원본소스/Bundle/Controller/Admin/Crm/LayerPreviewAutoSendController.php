<?php
/*
 * Copyright (C) 2025 NHN COMMERCE. - All Rights Reserved
 *
 * Unauthorized copying or redistribution of this file in source and binary forms via any medium
 * is strictly prohibited.
 */

namespace Bundle\Controller\Admin\Crm;

use App;
use Bundle\Component\Sms\SmsAutoCode;
use Origin\Enum\AutoSend\AutoSendChannel;
use Origin\Enum\AutoSend\AutoSendChannelPolicy;
use Origin\Enum\AutoSend\AutoSendRecipient;
use Origin\Enum\AutoSend\AutoSendRecipientPolicy;
use Origin\Enum\AutoSend\AutoSendSupport;
use Origin\Enum\Crm\Message\MyappPushTemplateCategory;
use Origin\Enum\Crm\Message\SmsTemplateCategory;
use Origin\Repository\Member\Myapp\MyappPushRepository;
use Origin\Repository\Member\Sms\SmsContentsRepository;
use Origin\Service\AutoSend\FindAutoSendCodeConfigService;
use Origin\Service\AutoSend\FindAutoSendConfigService;
use Origin\Service\AutoSend\FindAutoSendTemplateService;
use Request;

class LayerPreviewAutoSendController extends \Controller\Admin\Controller
{
    public function index()
    {
        $request = Request::get()->toArray();
        $code = $request['code'];
        $supportChannels = AutoSendChannelPolicy::getCodesWithChannelsWithBoard()[$code] ?? [];

        // 수신대상 라벨 (key => label)
        $recipientLabels = [];
        foreach (AutoSendRecipient::cases() as $recipient) {
            $recipientLabels[strtolower($recipient->name)] = $recipient->getTitle();
        }

        // 발송 수단 라벨 (key => label)
        $channelLabels = [];
        foreach ($supportChannels as $channel) {
            $channelLabels[$channel->name] = $channel->getShortTitle();
        }

        /** @var FindAutoSendCodeConfigService $codeConfigService */
        $codeConfigService = App::getInstance(FindAutoSendCodeConfigService::class);
        [$_, $autoSendConfigsResponse, $channelCodeConfigDto] = $codeConfigService->createRecipientConfigByCode($code, false);

        // kakaoTemplates를 templateCode 기준 flat map으로 변환
        $kakaoTemplateMap = [];
        foreach ($channelCodeConfigDto->kakaoTemplates as $categoryTemplates) {
            foreach ($categoryTemplates as $template) {
                if (!empty($template['templateCode'])) {
                    $kakaoTemplateMap[$template['templateCode']] = $template;
                }
            }
        }


        $this->setData('allRecipients', $this->getSortedAllRecipients());
        $this->setData('recipientLabels', $recipientLabels);
        $this->setData('channelLabels', $channelLabels);
        $this->setData('templates', $autoSendConfigsResponse);
        $this->setData('kakaoTemplateMap', $kakaoTemplateMap);
        $this->getView()->setDefine('layout', 'layout_layer.php');
    }

    private function getAutoSendMeta($code)
    {
        /** @var SmsAutoCode $smsAutoCode */
        $smsAutoCode = App::load('Component\\Sms\\SmsAutoCode');
        $groupKey = $smsAutoCode->getGroupByCode($code);
        $recipients = AutoSendRecipientPolicy::getCodesWithRecipientsWithBoard()[$code] ?? [];
        $channels = AutoSendChannelPolicy::getCodesWithChannelsWithBoard()[$code] ?? [];
        return [$groupKey, $recipients, $channels];
    }

    /**
     * 전체 수신 대상들 반환 (탭 순서용)
     * 순서 정책: 회원 - 수령자 - 본사 운영자 - 공급사
     *
     * @return array
     */
    public function getSortedAllRecipients(): array
    {
        return array_map(
            fn(AutoSendRecipient $r) => strtolower($r->name),
            [
                AutoSendRecipient::MEMBER,
                AutoSendRecipient::RECIPIENT,
                AutoSendRecipient::ADMIN,
                AutoSendRecipient::PROVIDER,
            ]
        );
    }
}
