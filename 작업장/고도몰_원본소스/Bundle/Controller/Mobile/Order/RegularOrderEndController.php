<?php
/*
 * Copyright (C) 2025 NHN COMMERCE. - All Rights Reserved
 *
 * Unauthorized copying or redistribution of this file in source and binary forms via any medium
 * is strictly prohibited.
 */

namespace Bundle\Controller\Mobile\Order;

use Controller\Mobile\Controller;

class RegularOrderEndController extends Controller
{
    public function index()
    {
        try {
            $applyGroupNo = \Request::get()->get('applyGroupNo');

            $regularOrder = \App::getInstance(\Component\Order\RegularOrderEnd::class);
            $orderInfo = $regularOrder->getOrderEndInfo($applyGroupNo);
            $this->setData('applyNos', implode(', ', $orderInfo['applyNoList']));
            $this->setData('gPageName', __("정기배송 신청 완료"));

        } catch (\Throwable $e) {
            \Logger::channel('regularOrder')->warning('Regular Order End Fail : ', [$e->getMessage()]);
        }
    }
}
