<?php
/*
 * Copyright (C) 2026 NHN COMMERCE. - All Rights Reserved
 *
 * Unauthorized copying or redistribution of this file in source and binary forms via any medium
 * is strictly prohibited.
 */

namespace Bundle\Controller\Admin\Crm\MobileSend;

class LayerPreviewMessageController extends \Controller\Admin\Controller
{
    public function index()
    {
        $this->callMenu('crm', 'messageSend', 'mobileSend');

        $this->getView()->setDefine('layout', 'layout_layer.php');
    }
}
