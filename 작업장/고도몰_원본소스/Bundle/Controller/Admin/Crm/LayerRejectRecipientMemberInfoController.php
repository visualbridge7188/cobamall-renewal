<?php
/*
 * Copyright (C) 2025 NHN COMMERCE. - All Rights Reserved
 *
 * Unauthorized copying or redistribution of this file in source and binary forms via any medium
 * is strictly prohibited.
 */

namespace Bundle\Controller\Admin\Crm;

use Request;

class LayerRejectRecipientMemberInfoController extends \Controller\Admin\Controller
{
    public function index()
    {
        $postValue = Request::post()->toArray();
        $this->setData('totalCount', $postValue['totalCount']);
        $this->setData('rejectedCount', $postValue['rejectedCount']);
        $this->getView()->setDefine('layout', 'layout_layer.php');
    }
}
