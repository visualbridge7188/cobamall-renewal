<?php
/*
 * Copyright (C) 2026 NHN COMMERCE. - All Rights Reserved
 *
 * Unauthorized copying or redistribution of this file in source and binary forms via any medium is strictly prohibited.
 */

namespace Bundle\Controller\Admin\Crm\AutoSendConfig;

use Request;

class LayerAutoSendTemplateController extends \Controller\Admin\Controller
{

    public function index()
    {
        $request = Request::post()->toArray();
        $channel = $request['channel'] ?? 'SMS';

        $this->setData('channel', $channel);
        $this->getView()->setDefine('layout', 'layout_layer.php');
        $this->getView()->setPageName('crm/auto_send_config/layer_auto_send_template.php');
    }
}
