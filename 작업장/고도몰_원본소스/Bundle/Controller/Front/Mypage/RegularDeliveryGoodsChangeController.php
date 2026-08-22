<?php
/*
 * Copyright (C) 2025 NHN COMMERCE. - All Rights Reserved
 *
 * Unauthorized copying or redistribution of this file in source and binary forms via any medium
 * is strictly prohibited.
 */

namespace Bundle\Controller\Front\Mypage;

use Component\Mypage\RegularDelivery;
use Framework\Debug\Exception\AlertRedirectException;

class RegularDeliveryGoodsChangeController extends \Controller\Front\Controller
{
    public function index()
    {
        $applyNo = \Request::request()->get('applyNo');

        $regularDelivery = \App::getInstance(RegularDelivery::class);
        $goodsChangeInfo = $regularDelivery->getRegularDeliveryGoodsChangeInfo($applyNo);

        if (empty($goodsChangeInfo)) {
            throw new AlertRedirectException('정기배송 변경 정보를 찾을 수 없습니다.', null, null, '/mypage/regular_delivery.php');
        }

        $this->setData('applyNo', $applyNo);
        $this->setData('goodsData', $goodsChangeInfo['goodsData']);
        $this->setData('addGoodsData', $goodsChangeInfo['addGoodsData']);
        $this->setData('giftData', $goodsChangeInfo['giftData']);
    }
}
