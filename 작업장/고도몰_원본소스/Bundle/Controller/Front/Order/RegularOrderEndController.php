<?php
/*
 * Copyright (C) 2025 NHN COMMERCE. - All Rights Reserved
 *
 * Unauthorized copying or redistribution of this file in source and binary forms via any medium
 * is strictly prohibited.
 */

namespace Bundle\Controller\Front\Order;

use Controller\Front\Controller;

class RegularOrderEndController extends Controller
{
    public function index()
    {
        try {
            $applyGroupNo = \Request::get()->get('applyGroupNo');

            $orderInfo = [];
            if ($applyGroupNo !== null) {
                $regularOrder = \App::getInstance(\Component\Order\RegularOrderEnd::class);
                $orderInfo = $regularOrder->getOrderEndInfo($applyGroupNo);
            }

            $this->setData('orderInfo', $orderInfo['orderInfo']);
            $this->setData('orderGoodsName', $orderInfo['orderGoodsName']);
            $this->setData('cardName', $orderInfo['cardName']);
            $this->setData('cardNo', $orderInfo['cardNo']);

        } catch (\Throwable $e) {
            \Logger::channel('regularOrder')->warning('Regular Order End Fail : ', [$e->getMessage()]);
        }
    }
}
