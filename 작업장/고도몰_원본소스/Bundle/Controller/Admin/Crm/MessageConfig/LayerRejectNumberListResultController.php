<?php
/*
 * Copyright (C) 2025 NHN COMMERCE. - All Rights Reserved
 *
 * Unauthorized copying or redistribution of this file in source and binary forms via any medium
 * is strictly prohibited.
 */

namespace Bundle\Controller\Admin\Crm\MessageConfig;

use Bundle\Component\Page\Page;
use Bundle\Component\Sms\Sms080DAO;
use Framework\Http\Request;

class LayerRejectNumberListResultController extends \Controller\Admin\Controller
{
    public function index()
    {
        $this->callMenu('crm', 'messageConfig', 'messageConfig');
        $naviMenu = $this->getData('naviMenu');

        /** @var Request $request */
        $request = \App::getInstance('request');
        $postData = $request->post();
        $page = $postData->get('page', 1);
        $pageSize = $postData->get('pageSize', 10);

        $keyword = str_replace('-', '', $postData->get('keyword', ''));

        /** @var Sms080DAO $dao */
        $dao = \App::load('Component\\Sms\\Sms080DAO');
        $sms080RejectList = $dao->selectList(['offset' => $page, 'limit' => $pageSize, 'keyword' => $keyword]);

        $totalCount = $dao->countList(['keyword' => $keyword]);
        $amountCount = $dao->countList([]);
        $page = new Page($page, $totalCount, $amountCount, $pageSize);

        $this->setData('sms080RejectList', $sms080RejectList);
        $this->setData('keyword', $keyword);
        $this->setData('page', $page);

        $this->getView()->setDefine('layout', 'layout_layer.php');
    }
}
