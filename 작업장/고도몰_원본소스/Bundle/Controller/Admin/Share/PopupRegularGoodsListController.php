<?php

namespace Bundle\Controller\Admin\Share;

use Component\RegularDelivery\RegularGoods\RegularGoods;
use Enum\Goods\RegularGoodsStatus;
use Exception;
use Origin\Enum\RegularDelivery\RegularGoods\RegularGoodsAttribute;
use Request;

/**
 * 정기결제(배송) 상품 리스트 페이지
 */
class PopupRegularGoodsListController extends \Controller\Admin\Controller
{
    public function index()
    {
        try {
            $regularGoods = \App::getInstance(RegularGoods::class);

            $applyPath = RegularGoodsAttribute::POPUP_REGULAR_GOODS_LIST_FILTER_APPLY_PATH;
            $search = $regularGoods->getDefaultSearchData(Request::get()->toArray(), $applyPath);

            $regularGoodsListData = $regularGoods->getPopupRegularGoodsList($search);

            $this->setData('search', $search);
            $this->setData('regularGoodsList', $regularGoodsListData['regularGoodsList']);
            $this->setData('page',$regularGoodsListData['page']);
            $this->setData('combineSearch', RegularGoodsStatus::COMBINE_SEARCH);
            $this->setData('sortList', RegularGoodsStatus::getSortList());
            $this->setData('regularGoodsGridConfigList', RegularGoodsAttribute::POPUP_REGULAR_GOODS_GRID_CONFIG_LIST);
            $this->setData('regularGoodsGridKey', array_column(RegularGoodsAttribute::POPUP_REGULAR_GOODS_GRID_CONFIG_LIST, 'gridKey'));

            $this->getView()->setDefine('layout', 'layout_blank.php');
            $this->getView()->setDefine('regularGoodsSearchFrm', Request::getDirectoryUri() . '/popup_regular_goods_list_search.php');
            $this->getView()->setPageName('goods/popup_regular_goods_list.php');

        } catch (Exception $e) {
            $logger = \App::getInstance('logger')->channel('regularGoods');
            $logger->info(__CLASS__ . ' Popup Regular Goods List Error', $e);
            throw $e;
        }

    }
}

