<?php
/*
 * Copyright (C) 2026 NHN COMMERCE. - All Rights Reserved
 *
 * Unauthorized copying or redistribution of this file in source and binary forms via any medium
 * is strictly prohibited.
 */

namespace Bundle\Controller\Admin\Crm\MobileSend;

use Component\Page\Page;
use Framework\Http\Response;
use Origin\DTO\Crm\Message\SmsTemplateSearchDTO;
use Origin\Service\Crm\Message\SmsTemplateService;

class LayerMobileSendSmsTemplateResultController extends \Controller\Admin\Controller
{
    public function index()
    {
        try {
            $request = \App::getInstance('request');
            $getParams = $request->get()->all();

            /** @var SmsTemplateService $smsTemplateService */
            $smsTemplateService = \App::getInstance(SmsTemplateService::class);
            $dto = new SmsTemplateSearchDTO($getParams);
            $result = $smsTemplateService->getSmsTemplateList($dto);
            $templates = $result['templates'];
            $totalCount = $result['totalCount'];
            $pageNum = $result['pageNum'];
            $page = $result['page'];

            /** @var \Bundle\Component\Page\Page $pageComponent */
            $pageComponent = \App::load('\\Component\\Page\\Page', $page);
            $pageComponent->page['list'] = $pageNum;
            $pageComponent->recode['amount'] = $totalCount;
            $pageComponent->recode['total'] = $totalCount;
            $pageComponent->setPage();
            $pageComponent->setUrl(\Request::getQueryString());

            $this->setData('templates', $templates);
            $this->setData('page', $pageComponent);
            $this->setData('requestData', $getParams);

            $this->getView()->setDefine('layout', 'layout_layer.php');
        } catch (\Throwable $e) {
            $this->json(['success' => false, 'message' => $e->getMessage()], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
