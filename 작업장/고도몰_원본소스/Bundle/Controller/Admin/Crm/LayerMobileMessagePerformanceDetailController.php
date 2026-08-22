<?php
/*
 * Copyright (C) 2025 NHN COMMERCE. - All Rights Reserved
 *
 * Unauthorized copying or redistribution of this file in source and binary forms via any medium
 * is strictly prohibited.
 */

namespace Bundle\Controller\Admin\Crm;

class LayerMobileMessagePerformanceDetailController extends \Controller\Admin\Controller
{
    public function index()
    {
        $this->getView()->setDefine('layout', 'layout_layer.php');
    }
}
