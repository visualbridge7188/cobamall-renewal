<?php
/*
 * Copyright (C) 2026 NHN COMMERCE. - All Rights Reserved
 *
 * Unauthorized copying or redistribution of this file in source and binary forms via any medium
 * is strictly prohibited.
 */

namespace Bundle\Controller\Admin\Crm\AutoSend;


class LayerListSearchController extends \Controller\Admin\Controller
{
    public function index()
    {
        $this->getView()->setPageName('crm/auto_send/layer_list_search.php');
        $this->getView()->setDefine('layout', 'layout_layer.php');
    }
}