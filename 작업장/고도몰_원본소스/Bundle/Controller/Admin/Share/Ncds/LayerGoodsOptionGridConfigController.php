<?php

/*
 * Copyright (C) 2026 NHN COMMERCE. - All Rights Reserved
 *
 * Unauthorized copying or redistribution of this file in source and binary forms via any medium
 * is strictly prohibited.
 */

namespace Bundle\Controller\Admin\Share\Ncds;

class LayerGoodsOptionGridConfigController extends \Controller\Admin\Share\LayerGoodsOptionGridConfigController
{
    public function index()
    {
        parent::index();
        $this->getView()->setPageName('share/ncds/layer_goods_option_grid_config.php');
    }
}
