<?php

/*
 * Copyright (C) 2025 NHN COMMERCE. - All Rights Reserved
 *
 * Unauthorized copying or redistribution of this file in source and binary forms via any medium
 * is strictly prohibited.
 */

namespace Bundle\Controller\Admin\Share;

use Component\RegularDelivery\RegularGoods\RegularGoods;
use Framework\Debug\Exception\LayerException;
use Throwable;
use Request;

class LayerRegularGoodsDeliveryCycleViewController extends \Controller\Admin\Controller
{
    public function index()
    {
        try {
            $postValue = Request::post()->toArray();

            $regularGoods = \App::getInstance(RegularGoods::class);
            $deliveryCycleData = $regularGoods->getDeliveryCycleData($postValue);

            $this->setData('deliveryCycleType', $deliveryCycleData['deliveryCycleType']);
            $this->setData('monthCycle', $deliveryCycleData['monthCycle']);
            $this->setData('weekCycle', $deliveryCycleData['weekCycle']);
            $this->setData('weekDayCycle', $deliveryCycleData['weekDayCycle']);

            // --- 관리자 디자인 템플릿
            $this->getView()->setDefine('layout', 'layout_layer.php');

            $this->getView()->setPageName('share/layer_regular_goods_delivery_cycle_view.php');
        } catch (Throwable $e) {
            $logger = \App::getInstance('logger')->channel('regularGoods');
            $logger->error('Regular Goods Delivery Cycle error', [
                $e->getMessage(),
                $e->getTrace()
            ]);

            throw new LayerException(__('처리중에 오류가 발생하여 실패되었습니다. '));
        }
    }
}
