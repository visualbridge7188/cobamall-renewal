<?php
/*
 * Copyright (C) 2025 NHN COMMERCE. - All Rights Reserved
 *
 * Unauthorized copying or redistribution of this file in source and binary forms via any medium
 * is strictly prohibited.
 */

namespace Bundle\Controller\Admin\Crm;

use Framework\Http\Request;

class MobileHistoryListController extends \Controller\Admin\Controller
{
    public function index()
    {
        $this->callMenu('crm', 'messageHistory', 'mobileHistoryList');

        /** @var Request $request */
        $request = \App::getInstance('request');
        $getData = $request->get()->toArray();

        $this->setData('sendMethod', $getData['sendMethod']);
        $this->getView()->setPageName('crm/mobile_history_list.php');
    }
}
