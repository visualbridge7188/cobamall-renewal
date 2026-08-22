<?php
/*
 * Copyright (C) 2025 NHN COMMERCE. - All Rights Reserved
 *
 * Unauthorized copying or redistribution of this file in source and binary forms via any medium
 * is strictly prohibited.
 */

namespace Bundle\Controller\Admin\Crm\MobileSend;


use Origin\Enum\Crm\Message\MyappPushTemplateCategory;
use Request;

class LayerMobileSendSaveMyappPushTemplateController extends \Controller\Admin\Controller
{
    public function index()
    {
        $request = Request::post()->toArray();
        $pushSubject = $request['pushSubject'] ?? '';
        $pushContent = $request['pushContent'] ?? '';
        $pushImage = $request['pushImage'] ?? '';
        $pushUrl = $request['pushUrl'] ?? '';
        $pushWithdraw = $request['pushWithdraw'] ?? '';

        $categories = MyappPushTemplateCategory::getFindOptions();

        $this->setData('categories', $categories);
        $this->setData('pushSubject', $pushSubject);
        $this->setData('pushContent', $pushContent);
        $this->setData('pushImage', $pushImage);
        $this->setData('pushUrl', $pushUrl);
        $this->setData('pushWithdraw', $pushWithdraw);

        $this->getView()->setDefine('layout', 'layout_layer.php');
    }
}
