<?php
/*
 * Copyright (C) 2026 NHN COMMERCE. - All Rights Reserved
 *
 * Unauthorized copying or redistribution of this file in source and binary forms via any medium
 * is strictly prohibited.
 */

namespace Bundle\Controller\Admin\Goods\Ncds;

class LayerGoodsOptionRegisterController extends \Controller\Admin\Goods\LayerGoodsOptionRegisterController
{
    public function index()
    {
        parent::index();

        $this->getView()->setPageName('goods/ncds/layer_goods_option_register.php');
    }
}
