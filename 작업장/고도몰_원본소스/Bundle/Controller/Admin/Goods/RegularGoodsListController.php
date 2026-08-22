<?php

namespace Bundle\Controller\Admin\Goods;

use Component\RegularDelivery\RegularGoods\RegularGoods;
use Enum\Goods\RegularGoodsStatus;
use Exception;
use Origin\Enum\RegularDelivery\RegularGoods\RegularGoodsAttribute;
use Request;

/**
 * 상품 리스트 페이지
 */
class RegularGoodsListController extends \Controller\Admin\Controller
{
    public function index()
    {
        $this->callMenu('goods', 'goods', 'regularGoodsList');

        try {
            $regularGoods = \App::getInstance(RegularGoods::class);

            $applyPath = RegularGoodsAttribute::REGULAR_GOODS_LIST_FILTER_APPLY_PATH;
            $search = $regularGoods->getDefaultSearchData(Request::get()->toArray(), $applyPath);

            $regularGoodsListData = $regularGoods->getRegularGoodsList($search);
            $regularGoodsGridConfigList = $regularGoods->getRegularGoodsGridConfigList();

            $this->setData('search', $search);
            $this->setData('regularGoodsList', $regularGoodsListData['regularGoodsList']);
            $this->setData('page',$regularGoodsListData['page']);
            $this->setData('combineSearch', RegularGoodsStatus::COMBINE_SEARCH);
            $this->setData('sortList', RegularGoodsStatus::getSortList());
            $this->setData('regularGoodsGridConfigList', $regularGoodsGridConfigList);
            $this->setData('regularGoodsGridKey', array_column($regularGoodsGridConfigList, 'gridKey'));

            $this->getView()->setDefine('regularGoodsSearchFrm', Request::getDirectoryUri() . '/regular_goods_list_search.php');
            $this->getView()->setPageName('goods/regular_goods_list.php');

        } catch (Exception $e) {
            throw $e;
        }

    }
}

