<?php
/*
 * Copyright (C) 2025 NHN COMMERCE. - All Rights Reserved
 *
 * Unauthorized copying or redistribution of this file in source and binary forms via any medium
 * is strictly prohibited.
 */

namespace Bundle\Controller\Mobile\Mypage;

use Component\Mypage\RegularDelivery;
use Component\RegularDelivery\RegularOrder\RegularOrderShippingAddress;
use Component\RegularDelivery\RegularOrder\RegularOrderPayment;

class RegularDeliveryChangeInfoController extends \Controller\Front\Mypage\RegularDeliveryChangeInfoController
{
    public function index()
    {
        parent::index();

        $memNo = \Session::get('member.memNo');

        // 전체 배송지 목록
        $shippingAddress = \App::getInstance(RegularOrderShippingAddress::class);
        $allShippingList = $shippingAddress->getAllShippingAddressList($memNo);

        // 전체 결제정보 목록
        $regularOrderPayment = \App::getInstance(RegularOrderPayment::class);
        $allPaymentCardList = $regularOrderPayment->getCardList($memNo);

        // 페이지 이름 설정
        $gPageName = __("배송정보 변경");
        $this->setData('allShippingList', $allShippingList);
        $this->setData('allPaymentCardList', $allPaymentCardList);
        $this->setData('gPageName', $gPageName);
    }
}
