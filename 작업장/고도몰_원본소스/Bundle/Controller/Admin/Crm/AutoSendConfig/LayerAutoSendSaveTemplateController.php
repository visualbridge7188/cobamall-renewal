<?php
/*
 * Copyright (C) 2025 NHN COMMERCE. - All Rights Reserved
 *
 * Unauthorized copying or redistribution of this file in source and binary forms via any medium
 * is strictly prohibited.
 */

namespace Bundle\Controller\Admin\Crm\AutoSendConfig;

use Exception;
use Origin\Enum\Crm\Message\MyappPushTemplateCategory;
use Origin\Enum\Crm\Message\SmsTemplateCategory;
use Request;

class LayerAutoSendSaveTemplateController extends \Controller\Admin\Controller
{
    public function index()
    {
        $request = Request::post()->toArray();
        $contents = $request['contents'] ?? '';
        $title = $request['title'] ?? '';
        $channel = $request['channel'];

        switch ($channel) {
            case 'MYAPP_PUSH':
                $categories = MyappPushTemplateCategory::getFindOptions();
                break;
            case 'SMS':
                $categories = SmsTemplateCategory::getFindOptions();
                break;
            default:
                throw new Exception("지원하지 않는 채널");
        }

        $url = $request['url'] ?? '';
        $image = $request['image'] ?? '';

        $this->setData('channel', $channel);
        $this->setData('categories', $categories);
        $this->setData('contents', $contents);
        $this->setData('title', $title);
        $this->setData('url', $url);
        $this->setData('image', $image);

        $this->getView()->setDefine('layout', 'layout_layer.php');
        $this->getView()->setPageName('crm/auto_send_config/layer_auto_send_save_template.php');
    }
}
