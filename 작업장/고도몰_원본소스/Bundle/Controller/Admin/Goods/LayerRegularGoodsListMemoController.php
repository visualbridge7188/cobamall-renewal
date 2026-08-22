<?php

/*
 * Copyright (C) 2025 NHN COMMERCE. - All Rights Reserved
 *
 * Unauthorized copying or redistribution of this file in source and binary forms via any medium
 * is strictly prohibited.
 */

namespace Bundle\Controller\Admin\Goods;

use Component\RegularDelivery\RegularGoods\RegularGoods;
use Request;

class LayerRegularGoodsListMemoController extends \Controller\Admin\Controller
{
    public function index()
    {
        $postValue = Request::post()->toArray();
        $regularGoodsSno = $postValue['regularGoodsSno'];
        $regularGoods = \App::getInstance(RegularGoods::class);

        // 상품 관리자 메모 데이터 로드
        $regularGoodsAdminMemoData = $regularGoods->getRegularGoodsAdminMemo($regularGoodsSno);

        $this->setData('regularGoodsSno', $regularGoodsSno);
        $this->setData('regularGoodsAdminMemoData', $regularGoodsAdminMemoData);

        $this->getView()->setDefine('layout', 'layout_layer.php');

        $this->getView()->setPageName('goods/layer_regular_goods_list_memo.php');
    }
}
