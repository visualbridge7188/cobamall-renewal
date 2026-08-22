<?php
/*
 * Copyright (C) 2026 NHN COMMERCE. - All Rights Reserved
 *
 * Unauthorized copying or redistribution of this file in source and binary forms via any medium
 * is strictly prohibited.
 */

namespace Bundle\Controller\Admin\Crm\MobileMessageHistory;

use Origin\Enum\ApiClient\Excel\ExcelGenerateMenu;
use Framework\Http\Request;
use Framework\Http\Response;
use Session;

class LayerMessageHistoryExcelDownloadController extends \Controller\Admin\Controller
{
    public function index()
    {
        /** @var Request $request */
        $request = \App::getInstance('request');
        $postData = $request->post()->toArray();

        if (Session::get('manager.functionAuthState') == 'check' && Session::get('manager.functionAuth.messageExcelDown') != 'y') {
            $this->json(['success' => false, 'message' => '권한이 없습니다.<br>권한은 대표운영자에게 문의하시기 바랍니다.'], Response::HTTP_FORBIDDEN);
        }
        // 대응되는 es_code itemCd를 이용해 사유들 가져오기
        $excelDownloadReasonList = ExcelGenerateMenu::fromName($request->post()->get('menu'))->getCode();

        $this->setData('menu', $excelDownloadReasonList);
        $this->setData('excelNo', $postData['excelNo']);
        $this->getView()->setDefine('layout', 'layout_layer.php');
    }
}
