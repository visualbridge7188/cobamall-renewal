<?php
/*
 * Copyright (C) 2025 NHN COMMERCE. - All Rights Reserved
 *
 * Unauthorized copying or redistribution of this file in source and binary forms via any medium
 * is strictly prohibited.
 */

namespace Bundle\Controller\Admin\Crm;

use Bundle\Component\Page\Page;
use Origin\DTO\ApiClient\Commerce\Notification\MessageTemplateCategoryDTO;
use Origin\DTO\ApiClient\Commerce\Notification\MessageTemplateKakaoStatusDTO;
use Origin\DTO\ApiClient\Commerce\Notification\MessageTemplateListSearchDTO;
use Origin\Service\Crm\Message\MessageTemplateFindService;
use Origin\Traits\Member\KakaoAlrim\KakaoAlrimCloudAvailable;


class MessageTemplateController extends \Controller\Admin\Controller
{
    use KakaoAlrimCloudAvailable;

    public function index()
    {
        $this->callMenu('crm', 'messageSend', 'messageTemplate');
        $this->getView()->setPageName('crm/message_template.php');
        $this->getView()->setDefine('messageTemplateSearch', 'crm/message_template/message_template_search.php');
        $this->getView()->setDefine('messageTemplateResult', 'crm/message_template/message_template_result.php');

        $request = \App::getInstance('request');
        $requestData = $request->request()->toArray();

        /** @var MessageTemplateFindService $messageHistoryService */
        $service = \App::getInstance(MessageTemplateFindService::class);
        $configDto = $service->getUseFlagDto();

        $searchDto = new MessageTemplateListSearchDTO($requestData);
        $results = $service->getMessageTemplateList($searchDto);

        $isNewMall = $this->isNewMall();

        $this->setData('sendMethod', $searchDto->getSendMethod());
        $this->setData('search', $searchDto);
        $this->setData('config', $configDto);
        $this->setData('results', $results['result']);
        $this->setData('page', $results['page']);
        $this->setData('searchCategory', new MessageTemplateCategoryDTO());
        $this->setData('searchStatus', new MessageTemplateKakaoStatusDTO());
        $this->setData('isNewMall', $isNewMall);
    }
}
