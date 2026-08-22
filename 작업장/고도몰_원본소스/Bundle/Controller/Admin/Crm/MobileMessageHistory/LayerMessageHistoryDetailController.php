<?php
/*
 * Copyright (C) 2026 NHN COMMERCE. - All Rights Reserved
 *
 * Unauthorized copying or redistribution of this file in source and binary forms via any medium
 * is strictly prohibited.
 */

namespace Bundle\Controller\Admin\Crm\MobileMessageHistory;

use Framework\Http\Request;
use Origin\Enum\ApiClient\Notification\MessageResultType;
use Origin\Enum\Crm\Message\SendMethod;

class LayerMessageHistoryDetailController extends \Controller\Admin\Controller
{
    public function index()
    {
        /** @var Request $request */
        $request = \App::getInstance('request');
        $postData = $request->post()->toArray();

        $sendGroupKey = $postData['sendGroupKey'];
        $type = $postData['type'];

        $this->setData('sendGroupKey', $sendGroupKey);
        $this->setData('type', $type);
        if ($type === SendMethod::SMS->name) {
            $this->setData('messageResultTypes', MessageResultType::cases());
        } else {
            $this->setData('messageResultTypes', MessageResultType::availableForSearch());
        }

        $this->getView()->setDefine('layout', 'layout_layer.php');
    }
}
