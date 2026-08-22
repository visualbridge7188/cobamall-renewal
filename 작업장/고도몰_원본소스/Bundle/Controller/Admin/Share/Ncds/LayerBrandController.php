<?php

/* 
 * Copyright (C) 2025 NHN COMMERCE. - All Rights Reserved 
 * 
 * Unauthorized copying or redistribution of this file in source and binary forms via any medium 
 * is strictly prohibited. 
 */

namespace Bundle\Controller\Admin\Share\Ncds;

class LayerBrandController extends \Controller\Admin\Share\LayerBrandController
{
    public function index()
    {
        parent::index();
        $this->getView()->setPageName('share/ncds/layer_brand.php');
    }
}
