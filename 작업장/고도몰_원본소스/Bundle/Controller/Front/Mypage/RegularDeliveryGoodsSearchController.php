<?php
/*
 * Copyright (C) 2025 NHN COMMERCE. - All Rights Reserved
 *
 * Unauthorized copying or redistribution of this file in source and binary forms via any medium
 * is strictly prohibited.
 */

namespace Bundle\Controller\Front\Mypage;

use Enum\Goods\RegularGoodsStatus;
use Component\Category\Category;
use Component\RegularDelivery\RegularGoods\RegularGoods;
use Framework\Debug\Exception\AlertRedirectException;

class RegularDeliveryGoodsSearchController extends \Controller\Front\Controller
{
    /**
     * @throws AlertRedirectException
     */
    public function index()
    {
        try {
            $search = \Request::get()->toArray();

            $regularGoods = \App::getInstance(RegularGoods::class);
            $regularGoodsListData = $regularGoods->getLayerRegularGoodsList($search);
            $page = $regularGoodsListData['page'];

            $addCss = null;
            if (!gd_is_skin_division()) {
                $addCss = "style='width:100%;'";
            }

            $category = \App::getInstance(Category::class);
            $displayCategory = $category->getMultiCategoryBox(null, null, $addCss, true);

            $this->setData('search', $search);
            $this->setData('applyNo', $search['applyNo']);
            $this->setData('regularGoodsList', $regularGoodsListData['regularGoodsList']);
            $this->setData('searchTypeData', RegularGoodsStatus::COMBINE_SEARCH_FRONT);
            $this->setData('regularGoodsCurrentCount', $regularGoodsListData['regularGoodsCurrentCount']);
            $this->setData('regularGoodsTotalCount', $regularGoodsListData['regularGoodsTotalCount']);
            $this->setData('displayCategory', gd_isset($displayCategory));
            $this->setData('page', $page);
            $this->setData('pageSize', $page->getList());

        } catch (\Throwable $e) {
            throw new AlertRedirectException($e->getMessage(), null, null, URI_HOME);
        }
    }
}
