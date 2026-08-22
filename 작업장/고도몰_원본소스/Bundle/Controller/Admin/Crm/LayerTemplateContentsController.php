<?php
/*
 * Copyright (C) 2026 NHN COMMERCE. - All Rights Reserved
 *
 * Unauthorized copying or redistribution of this file in source and binary forms via any medium is strictly prohibited.
 */

namespace Bundle\Controller\Admin\Crm;

use Origin\DTO\ApiClient\Commerce\Notification\MessageTemplateResultDTO;
use Origin\DTO\ApiClient\Commerce\Notification\MessageTemplateSearchDTO;
use Origin\Service\Crm\Message\MessageTemplateFindService;

class LayerTemplateContentsController extends \Controller\Admin\Controller
{
    public function index()
    {
        $this->getView()->setDefine('layout', 'layout_layer.php');

        $request = \App::getInstance('request');
        $searchDto = new MessageTemplateSearchDTO($request->request()->toArray());

        /** @var MessageTemplateFindService $service */
        $service = \App::getInstance(MessageTemplateFindService::class);
        $responseDto = $service->getMessageTemplate($searchDto);

        $this->getView()->setDefine('layerTemplateContentsSms', 'crm/layer_template_contents_sms.php');
        $this->getView()->setDefine('layerTemplateContentsKakao', 'crm/layer_template_contents_kakao.php');
        $this->getView()->setDefine('layerTemplateContentsMyapp', 'crm/layer_template_contents_myapp.php');
        $this->setData('request', $searchDto);
        $this->setData('response', $responseDto);
    }
}
