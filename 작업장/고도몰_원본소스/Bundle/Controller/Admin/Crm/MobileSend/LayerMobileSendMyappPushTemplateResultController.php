<?php
/*
 * Copyright (C) 2026 NHN COMMERCE. - All Rights Reserved
 *
 * Unauthorized copying or redistribution of this file in source and binary forms via any medium is strictly prohibited.
 */

namespace Bundle\Controller\Admin\Crm\MobileSend;

use Component\Page\Page;
use Framework\StaticProxy\Proxy\UserFilePath;
use Origin\Enum\Sms\SmsContentsType;
use Origin\Repository\Member\Myapp\MyappPushRepository;
use Origin\Repository\Member\Sms\SmsContentsRepository;
use Request;

class LayerMobileSendMyappPushTemplateResultController extends \Controller\Admin\Controller
{
    public function index()
    {
        $request = \App::getInstance('request');
        $getParams = $request->get()->all();

        $pageNum = 10;
        $page = $getParams['page'] ?? 1;
        $pushType = $getParams['pushType'] ?? '';
        $keyword = $getParams['keyword'] ?? '';
        $searchParams = ['keyword' => $keyword, 'templateFl'=> 'y', 'pushType' => $pushType];

        $repository =   \App::getInstance(MyappPushRepository::class);
        $templates = $repository->findAllWithPagination($searchParams, $pageNum, ($page - 1) * $pageNum);
        $totalCount = $repository->countAll($searchParams);

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
    }
}
