<?php
/*
 * Copyright (C) 2026 NHN COMMERCE. - All Rights Reserved
 *
 * Unauthorized copying or redistribution of this file in source and binary forms via any medium is strictly prohibited.
 */

namespace Bundle\Controller\Admin\Crm;

use App;
use Bundle\Component\Sms\Code;
use Bundle\Component\Sms\SmsAutoCode;
use Bundle\Controller\Admin\Controller;
use Framework\Debug\Exception\AlertRedirectException;
use GuzzleHttp\Exception\ConnectException;
use Origin\Enum\AutoSend\AutoSendChannelPolicy;
use Origin\Enum\AutoSend\AutoSendRecipient;
use Origin\Enum\AutoSend\AutoSendRecipientPolicy;
use Origin\Enum\AutoSend\AutoSendOptionVisibilityPolicy;
use Origin\Enum\AutoSend\AutoSendSupport;
use Origin\Enum\AutoSend\AutoSendChannel;
use Origin\Enum\AutoSend\AutoSendTemplateVariableCatalog;
use Origin\Service\AutoSend\FindAutoSendCodeConfigService;
use Origin\Service\AutoSend\FindAutoSendConfigService;
use Origin\Service\Crm\Message\MyappPushTemplateService;
use Origin\Service\Crm\Message\SmsPasswordService;
use Request;

class AutoSendConfigController extends Controller
{
    public function index()
    {
        $code = Request::get()->get('code');
        $isBlock = false;
        $blockMode = null;
        $validAutoSendSms = false;

        if (!AutoSendSupport::canSendIfClaim($code)) {
            $isBlock = true;
            $blockMode = 'claim';
        }

        try {
            $validAutoSendSms = $this->validAutoSendSms();
        } catch (ConnectException) {
            $isBlock = true;
            $blockMode = 'smsConnect';
        }

        $this->applyBlockPage();

        if ($isBlock) {
            $this->setData('blockMode', $blockMode);
        } else {
            /** @var SmsAutoCode $smsAutoCode */
            $smsAutoCode = \App::load('\\Component\\Sms\\SmsAutoCode');
            $groupCode = $smsAutoCode->getByCode($code);

            $recipients = AutoSendRecipientPolicy::getCodesWithRecipientsWithBoard()[$code] ?? [];
            $channels = AutoSendChannelPolicy::getCodesWithChannelsWithBoard()[$code] ?? [];

            /** @var FindAutoSendCodeConfigService $codeConfigService */
            $codeConfigService = App::getInstance(FindAutoSendCodeConfigService::class);
            [$options, $autoSendConfigsResponse, $channelCodeConfigDto] = $codeConfigService->createRecipientConfigByCode($code);

            /** @var FindAutoSendConfigService $configService */
            $configService = App::getInstance(FindAutoSendConfigService::class);
            $crmMyappConfig = $configService->getCrmMyappConfig();
            $kakaoCrmConfig = $configService->getCrmKakaoConfig();
            $autoSendOptionVisibility = AutoSendOptionVisibilityPolicy::getOptionVisibilityByCode($code)
                ?? AutoSendOptionVisibilityPolicy::getDefaultBoardVisibility($code);
            $checkedRecipients = $this->collectGroupsHavingAutoSendEnabled($autoSendConfigsResponse);
            $shouldChangeKakaoTemplate = in_array($kakaoCrmConfig['sender'] ?? '', ['kakaoAlrim', 'kakaoAlrimCloud'], true);
            $kakaoVendorLabel = match ($kakaoCrmConfig['sender'] ?? '') {
                'kakaoAlrimCloud' => '클라우드',
                'kakaoAlrim' => '비즈엠',
                'kakaoAlrimLuna' => '블룸에이아이',
                default => '',
            };

            /** @var MyappPushTemplateService $myappPushTemplateService */
            $myappPushTemplateService = App::getInstance(MyappPushTemplateService::class);

            $this->callMenu('crm', 'messageSend', 'autoSendConfig');
            $this->setData('mode', 'save');
            $this->setData('code', $code);
            $this->setData('groupCode', $groupCode);
            $this->setData('displayOptions', $autoSendOptionVisibility->bindToDisplay());
            $this->setData('options', $options);
            $this->setData('defaultRecipient', AutoSendRecipientPolicy::getDefaultRecipient($code));
            $this->setData('recipientTitles', $this->buildRecipientTitles($recipients));
            $this->setData('forceCheckRecipients', $this->getForceCheckRecipients($code));
            $this->setData('recipients', $recipients);
            $this->setData('channels', $channels);
            $this->setData('useJoinPolicy', AutoSendSupport::useJoinPolicy());
            $this->setData('myappSupportRecipients', ['member']);
            $this->setData('kakaoCrmUseFlag', ($kakaoCrmConfig['useFlag'] ?? '') === 'y');
            $this->setData('myappCrmUseFlag', ($crmMyappConfig['useFlag'] ?? '') === 'y');
            $this->setData('isKakaoAlternativeBySms', ($kakaoCrmConfig['autoSmsAlternativeSendFlag'] ?? '') === 'y' && ($kakaoCrmConfig['autoSmsAlternativeSendType'] ?? '') === 'SMS');
            $this->setData('isMyappAlternativeBySms', ($crmMyappConfig['autoSmsAlternativeSendFlag'] ?? '') === 'y' && ($crmMyappConfig['autoSmsAlternativeSendType'] ?? '') === 'SMS');
            $this->setData('validAutoSendSms', $validAutoSendSms);
            $this->setData('checkedRecipients', $checkedRecipients);
            $this->setData('autoSendConfigsResponse', $autoSendConfigsResponse);
            $this->setData('channelTemplateVariables', [
                AutoSendChannel::SMS->name => AutoSendTemplateVariableCatalog::groupCodeVariablesByCategoryAndChannel($code, AutoSendChannel::SMS, true),
                AutoSendChannel::KAKAO_ALRIM_TALK->name => AutoSendTemplateVariableCatalog::groupCodeVariablesByCategoryAndChannel($code, AutoSendChannel::KAKAO_ALRIM_TALK, true),
                AutoSendChannel::MYAPP_PUSH->name => AutoSendTemplateVariableCatalog::groupCodeVariablesByCategoryAndChannel($code, AutoSendChannel::MYAPP_PUSH, true),
            ]);
            $this->setData('groupedTemplateAllVariables', AutoSendTemplateVariableCatalog::groupVariablesByCategory(true));
            $this->setData('shouldChangeKakaoTemplate', $shouldChangeKakaoTemplate);
            $this->setData('kakaoTemplates', $channelCodeConfigDto->kakaoTemplates);
            $this->setData('kakaoVendorLabel', $kakaoVendorLabel);
            $this->setData('myappDomain', $myappPushTemplateService->getMyappDomainUrl());
            $this->getView()->setDefine('autoSendConfigOptions', 'crm/auto_send_config/auto_send_config_options.php');
            $this->getView()->setDefine('autoSendConfigContents', 'crm/auto_send_config/auto_send_config_contents.php');
            $this->getView()->setDefine('autoSendConfigPreview', 'crm/auto_send_config/auto_send_config_preview.php');
        }
    }

    private function buildRecipientTitles(array $recipients): array
    {
        $titles = [];
        foreach ($recipients as $recipient) {
            $titles[strtolower($recipient->name)] = $recipient->getTitle();
        }
        return $titles;
    }

    /**
     * @throws ConnectException
     */
    private function validAutoSendSms(): bool
    {
        try {
            /** @var SmsPasswordService $smsPasswordService */
            $smsPasswordService = App::getInstance(SmsPasswordService::class);
            $smsPassword = $smsPasswordService->getSmsPassword(true);
            if (empty($smsPassword)) {
                return false;
            }

            if (empty(gd_policy('sms.config')['smsCallNum'])) {
                return false;
            }

            return $smsPasswordService->verifySmsPassword($smsPassword);
        } catch (ConnectException $e) {
            throw $e;
        } catch (\Exception) {
            return false;
        }
    }

    private function collectGroupsHavingAutoSendEnabled(array $autoSendConfigsResponse): array
    {
        $result = [];
        foreach ($autoSendConfigsResponse as $groupKey => $channels) {
            foreach ($channels as $channel) {
                if (($channel['isAutoSend'] ?? null) === 'y') {
                    $result[] = $groupKey;
                    break;
                }
            }
        }
        return $result;
    }

    /**
     * 정책상 무조건 수신 대상 설정에 체크 상태로 있어야 하는 수신자
     * @param string $code
     * @return array
     */
    public function getForceCheckRecipients(mixed $code): array
    {
        $forceCheckedRecipients = [];
        if ($code == Code::PRESENT) {
            $forceCheckedRecipients[] = AutoSendRecipient::RECIPIENT;
        }

        return $forceCheckedRecipients;
    }

    /**
     * @return void
     */
    public function applyBlockPage(): void
    {
        $this->getView()->setPageName('crm/auto_send_config.php');
        $this->getView()->setDefine('autoSendConfigBlock', 'crm/auto_send_config/auto_send_config_block.php');
    }
}
