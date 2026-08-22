<?php
/*
 * Copyright (C) 2025 NHN COMMERCE. - All Rights Reserved
 *
 * Unauthorized copying or redistribution of this file in source and binary forms via any medium
 * is strictly prohibited.
 */

namespace Bundle\Controller\Admin\Crm;

use Component\Page\Page;
use Component\Member\MemberGroup;
use Framework\StaticProxy\Proxy\UserFilePath;
use Framework\Utility\StringUtils;

class LayerSelectMemberGroupsController extends \Controller\Admin\Controller
{
    public function index()
    {
        $this->getView()->setDefine('layout', 'layout_layer.php');
    }
}
