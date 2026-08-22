<?php
/*
 * Copyright (C) 2025 NHN COMMERCE. - All Rights Reserved
 *
 * Unauthorized copying or redistribution of this file in source and binary forms via any medium
 * is strictly prohibited.
 */

namespace Bundle\Controller\Front\Goods;

use Util\Order\RegularOrderUtil;
use Controller\Front\Controller;

class LayerOptionRegularDeliveryController extends Controller
{
    public function index()
    {
        try {
            $getValue = \Request::get()->toArray();
            $deliveryDueDate = RegularOrderUtil::calculateOrderDate(
                date('Y-m-d'), $getValue['type'], $getValue['period'], $getValue['roundDay'], true);

            $this->json([
                'result' => 'success',
                'deliveryDueDate' => $deliveryDueDate[0], // 0: 배송예정일 / 1:주문생성일
            ]);
        } catch (\Exception $e) {
            $this->json([
                'result' => 'fail',
                'deliveryDueDate' => '',
            ]);
        }
    }
}
