<?php
/*
 * Copyright (C) 2026 NHN COMMERCE. - All Rights Reserved
 *
 * Unauthorized copying or redistribution of this file in source and binary forms via any medium is strictly prohibited.
 */

namespace Bundle\Controller\Admin\Crm\AutoSend;

use Origin\Enum\AutoSend\AutoSendSupport;
use Origin\Service\AutoSend\SearchAutoSendListService;
use Origin\Service\AutoSend\SearchAutoSendsRequest;
use Request;

class LayerListController extends \Controller\Admin\Controller
{
    public function index()
    {
        $request = SearchAutoSendsRequest::fromArray(Request::post()->toArray());

        /** @var SearchAutoSendListService $findAutoSendService */
        $findAutoSendService = \App::getInstance(SearchAutoSendListService::class);
        $response = $findAutoSendService->search($request);
        $this->setData('newIconCodes', AutoSendSupport::getNewIconCodes());
        $this->setData('autoSends', $response['autoSends']);
        $this->setData('pageCounts', $response['pageCounts']);
        $this->getView()->setPageName('crm/auto_send/layer_list.php');
        $this->getView()->setDefine('layout', 'layout_layer.php');
    }
}
