<?php
/*
 * Copyright (C) 2026 NHN COMMERCE. - All Rights Reserved
 *
 * Unauthorized copying or redistribution of this file in source and binary forms via any medium
 * is strictly prohibited.
 */

namespace Bundle\Controller\Admin\Crm\AutoSend;

use Origin\Service\AutoSend\SearchAutoSendListService;

class LayerListRecommendController extends \Controller\Admin\Controller
{
    public function index()
    {
        $this->getView()->setPageName('crm/auto_send/layer_list_recommend.php');
        $this->getView()->setDefine('layout', 'layout_layer.php');
    }
}