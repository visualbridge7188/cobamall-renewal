<?php
/*
 * Copyright (C) 2025 NHN COMMERCE. - All Rights Reserved
 *
 * Unauthorized copying or redistribution of this file in source and binary forms via any medium
 * is strictly prohibited.
 */

namespace Bundle\Controller\Admin\Crm\MobileMessageHistory;

use Framework\Http\Request;

class LayerLunaKakaoAlimTalkHistoryResultController extends \Controller\Admin\Controller
{
    public function index()
    {
        $this->callMenu('crm', 'messageHistory', 'mobileHistoryList');
        $naviMenu = $this->getData('naviMenu');

        /** @var Request $request */
        $request = \App::getInstance('request');
        $requestData = $request->request()->toArray();

        $this->getView()->setDefine('layout', 'layout_layer.php');
    }
}
