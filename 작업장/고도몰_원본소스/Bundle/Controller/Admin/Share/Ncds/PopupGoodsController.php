<?php

/* 
 * Copyright (C) 2025 NHN COMMERCE. - All Rights Reserved 
 * 
 * Unauthorized copying or redistribution of this file in source and binary forms via any medium 
 * is strictly prohibited. 
 */
namespace Bundle\Controller\Admin\Share\Ncds;

use Request;

class PopupGoodsController extends \Controller\Admin\Share\PopupGoodsController
{

    public function index()
    {
        parent::index();
        
        $this->getView()->setDefine('selectGoods','share/ncds/popup_goods/select_goods.php');
        $this->getView()->setDefine('addGoods','share/ncds/popup_goods/add_goods.php');

        $this->setData('displayMode', Request::get()->get('displayMode'));
    }
}
