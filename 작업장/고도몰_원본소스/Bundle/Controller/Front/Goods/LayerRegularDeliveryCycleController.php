<?php
/*
 * Copyright (C) 2025 NHN COMMERCE. - All Rights Reserved
 *
 * Unauthorized copying or redistribution of this file in source and binary forms via any medium
 * is strictly prohibited.
 */

namespace Bundle\Controller\Front\Goods;

class LayerRegularDeliveryCycleController extends \Controller\Front\Controller
{
    public function index()
    {
        try {
            $postValue = \Request::post()->toArray();

            // 정기배송 상품 정보 조회
            $regularGoods = \App::getInstance(\Component\RegularDelivery\RegularGoods\RegularGoods::class);
            $regularGoodsData = $regularGoods->getRegularGoodsData($postValue['goodsNo']);
            $this->setData('regularGoodsData', $regularGoodsData);

            $selectedCycleData = $regularGoods->getSelectedCycleData($postValue, $regularGoodsData);
            $this->setData('selectedCycleData', $selectedCycleData);
            $this->setData('sno', $postValue['sno']);
            $this->setData('goodsNo', $postValue['goodsNo']);
            $this->setData('addGoodsListCount', $postValue['addGoodsListCount']);

        } catch (\Exception $e) {
            $this->json([
                'error' => 0,
                'message' => $e->getMessage(),
            ]);
        }
    }
}
