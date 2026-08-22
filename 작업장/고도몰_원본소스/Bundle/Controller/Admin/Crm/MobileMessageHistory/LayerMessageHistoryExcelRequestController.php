<?php
/*
 * Copyright (C) 2026 NHN COMMERCE. - All Rights Reserved
 *
 * Unauthorized copying or redistribution of this file in source and binary forms via any medium
 * is strictly prohibited.
 */

namespace Bundle\Controller\Admin\Crm\MobileMessageHistory;

use Framework\Http\Request;
use Framework\Http\Response;
use Origin\Enum\ApiClient\Excel\ExcelDownloadRange;
use Origin\Enum\ApiClient\Excel\ExcelGenerateMenu;
use Session;

class LayerMessageHistoryExcelRequestController extends \Controller\Admin\Controller
{
    public function index()
    {
        /** @var Request $request */
        $request = \App::getInstance('request');
        $postData = $request->post();
        $menu = ExcelGenerateMenu::fromName($postData->get('type'));
        $selectedSendKeys = $postData->get('selectedSendKeys', []);
        $searchQuery = $postData->get('searchQuery', []);

        if (Session::get('manager.functionAuthState') == 'check' && Session::get('manager.functionAuth.messageExcelDown') != 'y') {
            $this->json(['success' => false, 'message' => '권한이 없습니다.<br>권한은 대표운영자에게 문의하시기 바랍니다.'], Response::HTTP_FORBIDDEN);
        }

        $this->setData('menu', $menu);
        $this->setData('searchQuery', $searchQuery);
        $this->setData('selectedSendKeys', $selectedSendKeys);
        $this->setData('excelRequestDownloadRanges', ExcelDownloadRange::cases());
        $this->getView()->setDefine('layout', 'layout_layer.php');
    }
}
