<?php
/*
 * Copyright (C) 2025 NHN COMMERCE. - All Rights Reserved
 *
 * Unauthorized copying or redistribution of this file in source and binary forms via any medium
 * is strictly prohibited.
 */

namespace Bundle\Controller\Admin\Crm\MobileSend;

use Origin\Enum\Crm\Message\SmsTemplateCategory;
use Request;

class LayerMobileSendSaveSmsTemplateController extends \Controller\Admin\Controller
{
    public function index()
    {
        $request = Request::post()->toArray();
        $contents = $request['contents'] ?? '';

        $categories = SmsTemplateCategory::getFindOptions();

        $this->setData('categories', $categories);
        $this->setData('contents', $contents);

        $this->getView()->setDefine('layout', 'layout_layer.php');
    }
}
