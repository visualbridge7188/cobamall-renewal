<?php
/*
 * Copyright (C) 2025 NHN COMMERCE. - All Rights Reserved
 *
 * Unauthorized copying or redistribution of this file in source and binary forms via any medium
 * is strictly prohibited.
 */

namespace Bundle\Controller\Mobile\Order;

use Component\Member\Util\MemberUtil;
use Controller\Mobile\Controller;
use Exception;
use Request;

class LayerRegularOrderShippingAddressController extends Controller
{
    /**
     * @return void
     * @throws Exception
     */
    public function index()
    {
        try {
            if (!Request::isAjax()) {
                throw new Exception('Ajax ' . __('전용 페이지 입니다.'));
            }

            if (!MemberUtil::isLogin()) {
                $this->js("alert('" . __('로그인을 하셔야 이용하실 수 있습니다.') . "'); top.location.href = '../member/login.php';");
            }

            $pageNum = (int) Request::get()->get('page', 1);
            $memNo = \Session::get('member.memNo');

            $regularOrderShippingAddress = \App::getInstance(\Component\RegularDelivery\RegularOrder\RegularOrderShippingAddress::class);
            $shippingInfo = $regularOrderShippingAddress->getRegularOrderShippingAddress($memNo, $pageNum);
            $shippingList = $shippingInfo['list'];
            $pager = $shippingInfo['pager'];

            $this->setData('deliveryAddress', $shippingList);
            $this->setData('pagination', $pager->getPage('goPageOnDeliveryAddress(\'PAGELINK\');'));
        } catch (Exception $e) {
            \Logger::channel('regularOrder')->error(__CLASS__ . 'Regular order shipping address 조회 실패', $e->getMessage());
        }
    }
}
