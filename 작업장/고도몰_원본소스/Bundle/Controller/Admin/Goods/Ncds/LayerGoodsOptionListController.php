<?php
/*
 * Copyright (C) 2026 NHN COMMERCE. - All Rights Reserved
 *
 * Unauthorized copying or redistribution of this file in source and binary forms via any medium
 * is strictly prohibited.
 */

namespace Bundle\Controller\Admin\Goods\Ncds;

class LayerGoodsOptionListController extends \Controller\Admin\Goods\LayerGoodsOptionListController
{
    public function index()
    {
        parent::index();

        $callFunc = \Request::get()->get('callFunc', '');
        $this->setData('callFunc', $callFunc);

        // getAdminListOption('layer') 모드는 SQL LIMIT을 적용하지 않으므로 직접 페이지네이션 처리
        $data = $this->getData('data');
        $page = $this->getData('page');
        if (is_array($data) && $page) {
            $start = max(0, (int)$page->recode['start']);
            $limit = max(1, min(100, (int)$page->page['list'])); // 최대 100개로 제한
            $this->setData('data', array_slice($data, $start, $limit));
        }

        $this->getView()->setPageName('goods/ncds/layer_goods_option_list.php');
    }
}
