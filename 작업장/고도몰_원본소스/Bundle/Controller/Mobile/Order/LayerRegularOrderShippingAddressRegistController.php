<?php
/*
 * Copyright (C) 2025 NHN COMMERCE. - All Rights Reserved
 *
 * Unauthorized copying or redistribution of this file in source and binary forms via any medium
 * is strictly prohibited.
 */

namespace Bundle\Controller\Mobile\Order;

use Controller\Mobile\Controller;
use Request;
use Component\Member\Util\MemberUtil;

class LayerRegularOrderShippingAddressRegistController extends Controller
{
    public function index()
    {
        try {

            if (!Request::isAjax()) {
                throw new \Exception('Ajax ' . __('전용 페이지 입니다.'));
            }

            if (!MemberUtil::isLogin()) {
                $this->js("alert('" . __('로그인을 하셔야 이용하실 수 있습니다.') . "'); top.location.href = '../member/login.php';");
            }

            $shippingAddressSno = Request::get()->get('sno');
            $memNo = \Session::get('member.memNo');

            $regularOrderShippingAddress = \App::getInstance(\Component\RegularDelivery\RegularOrder\RegularOrderShippingAddress::class);
            if ($shippingAddressSno && is_numeric($shippingAddressSno)) {
                // 수정 화면
                $regularOrderShippingAddress->hasActiveOrPausedOrdersUsingShippingAddress($shippingAddressSno, $memNo); // 이미 등록된 배송지로 활성화 또는 일시정지된 정기배송이 있는지 확인
                $shippingInfo = $regularOrderShippingAddress->getShippingAddressInfo($shippingAddressSno, $memNo);
                $this->setData('data', $shippingInfo);
                $this->setData('mode', 'shipping_modify');
            } else {
                // 등록 화면
                $shippingInfo = $regularOrderShippingAddress->getAllShippingAddressList($memNo);
                if (empty($shippingInfo)){
                    $data['defaultFl'] = 'y';
                    $data['defaultFlDisabled'] = true;
                    $this->setData('data', $data);
                }
                $this->setData('mode', 'shipping_regist');
            }
            $this->setData('shippingNo', Request::get()->get('shippingNo'));

        } catch (\Exception $e) {
            $this->json([
                'error' => true,
                'message' => $e->getMessage(),
            ]);
        }
    }
}
