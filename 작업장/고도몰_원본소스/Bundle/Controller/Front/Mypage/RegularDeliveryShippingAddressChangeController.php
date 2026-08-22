<?php
/*
 * Copyright (C) 2025 NHN COMMERCE. - All Rights Reserved
 *
 * Unauthorized copying or redistribution of this file in source and binary forms via any medium
 * is strictly prohibited.
 */

namespace Bundle\Controller\Front\Mypage;

class RegularDeliveryShippingAddressChangeController extends \Controller\Front\Controller
{
    public function index()
    {
        $pageNumber = \Request::get()->get('page', 1);
        $memNo = \Session::get('member.memNo');

        $regularOrderShippingAddress = \App::getInstance(\Component\RegularDelivery\RegularOrder\RegularOrderShippingAddress::class);

        // 정기 배송지 목록 조회
        $shippingAddressList = $regularOrderShippingAddress->getRegularOrderShippingAddress($memNo, (int) $pageNumber);
        $shippingList = $shippingAddressList['list'];
        $pager = $shippingAddressList['pager'];

        // 데이터 뷰에 전달
        $this->setData('shippingAddressList', $shippingList);
        $this->setData('pagination', $pager->getPage('goPageOnDeliveryAddress(\'PAGELINK\');'));
    }
}
