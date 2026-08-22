<?php
/*
 * Copyright (C) 2026 NHN COMMERCE. - All Rights Reserved
 *
 * Unauthorized copying or redistribution of this file in source and binary forms via any medium is strictly prohibited.
 */

namespace Bundle\Controller\Admin\Crm;

class IndexController extends \Controller\Admin\Controller
{
    public function index()
    {
        $this->redirect('./crm_group.php');
    }
}
