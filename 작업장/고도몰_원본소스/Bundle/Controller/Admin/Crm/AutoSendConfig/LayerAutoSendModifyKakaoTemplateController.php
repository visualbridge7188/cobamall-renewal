<?php
/*
 * Copyright (C) 2025 NHN COMMERCE. - All Rights Reserved
 *
 * Unauthorized copying or redistribution of this file in source and binary forms via any medium
 * is strictly prohibited.
 */

namespace Bundle\Controller\Admin\Crm\AutoSendConfig;

use App;
use Origin\Service\Member\KakaoAlrim\KakaoAlrimCloudMessageTemplateService;
use Request;

class LayerAutoSendModifyKakaoTemplateController extends \Controller\Admin\Controller
{
    public function index()
    {
        $request = Request::post()->toArray();
        $templateCode = $request['templateCode'] ?? '';

        // todo test
        $templateCode = 'custom_order_0011';

        $this->setData('template', $template);
        $this->getView()->setDefine('layout', 'layout_layer.php');
    }
}
