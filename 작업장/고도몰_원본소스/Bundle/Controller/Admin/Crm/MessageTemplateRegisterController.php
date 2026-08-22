<?php
/*
 * Copyright (C) 2026 NHN COMMERCE. - All Rights Reserved
 *
 * Unauthorized copying or redistribution of this file in source and binary forms via any medium is strictly prohibited.
 */

namespace Bundle\Controller\Admin\Crm;

use Framework\Http\Request;
use Origin\DTO\ApiClient\Commerce\Notification\MessageTemplateCategoryDTO;
use Origin\DTO\ApiClient\Commerce\Notification\MessageTemplateKakaoStatusDTO;
use Origin\DTO\ApiClient\Commerce\Notification\MessageTemplateRegisterRequestDTO;
use Origin\DTO\ApiClient\Commerce\Notification\MessageTemplateResultDTO;
use Origin\DTO\ApiClient\Commerce\Notification\MessageTemplateSearchDTO;
use Origin\Service\Crm\Message\MessageTemplateFindService;
use Origin\Service\Crm\Message\MyappPushTemplateService;
use Origin\Traits\Member\KakaoAlrim\KakaoAlrimCloudAvailable;
use function JmesPath\search;

class MessageTemplateRegisterController extends \Controller\Admin\Controller
{
    use KakaoAlrimCloudAvailable;

    public function index()
    {
        $request = \App::getInstance('request');
        $requestData = $request->request()->toArray();


        /** @var MessageTemplateFindService $messageHistoryService */
        $service = \App::getInstance(MessageTemplateFindService::class);
        $configDto = $service->getUseFlagDto();

        /** @var MessageTemplateRegisterRequestDTO $messageTemplateRegisterRequestDTO */
        $requestDto = new MessageTemplateRegisterRequestDTO($requestData);
        $searchDto = new MessageTemplateSearchDTO($requestData);

        $this->getView()->setPageName('crm/message_template_register.php');
        $this->getView()->setDefine('basicConfig', 'crm/message_template_register/basic_config.php');
        $this->getView()->setDefine('templateContentsSms', 'crm/message_template_register/template_contents_sms.php');
        $this->getView()->setDefine('templateContentsKakao', 'crm/message_template_register/template_contents_kakao.php');
        $this->getView()->setDefine('templateContentsMyapp', 'crm/message_template_register/template_contents_myapp.php');
        $this->getView()->setDefine('previewMessage', 'crm/message_template_register/preview_message.php');

        switch ($requestDto->getMode()) {
            case 'register':
                $requestDto = $service->changeRegisterPormat($requestDto,$configDto);
                $this->callMenu('crm', 'messageSend', 'messageTemplateRegister');
                $responseDto = new MessageTemplateResultDTO();
                break;
            case 'copy':
                $this->callMenu('crm', 'messageSend', 'messageTemplateRegister');
                $responseDto = $service->getMessageTemplate($searchDto);
                break;
            case 'modify':
                $this->callMenu('crm', 'messageSend', 'messageTemplateModify');
                $responseDto = $service->getMessageTemplate($searchDto);
                break;
            default:
                $responseDto = new MessageTemplateResultDTO();
        }
        $isNewMall = $this->isNewMall();

        $myappService = \App::getInstance(MyappPushTemplateService::class);
        $this->getView()->setData('myappUrl', $myappService->getMyappDomainUrl());
        $this->getView()->setData('config', $configDto);
        $this->getView()->setData('request', $requestDto);
        $this->getView()->setData('response', $responseDto);
        $this->setData('category', new MessageTemplateCategoryDTO());
        $this->setData('status', new MessageTemplateKakaoStatusDTO());
        $this->setData('isNewMall', $isNewMall);
    }
}
