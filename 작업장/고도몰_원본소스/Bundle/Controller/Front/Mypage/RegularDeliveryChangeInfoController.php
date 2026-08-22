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

class RegularDeliveryChangeInfoController extends \Controller\Front\Controller
{
    public function index()
    {
        try {
            $applyNo = \Request::request()->get('applyNo');

            // 신청서 소유권 확인
            $regularDelivery = \App::getInstance(RegularDelivery::class);
            $regularDelivery->verifyOwnership((int)$applyNo, (int)\Session::get('member.memNo'));

            // 정기배송 변경 정보 조회
            $deliveryChangeInfo = $regularDelivery->getRegularDeliveryChangeInfo($applyNo);

            if (empty($deliveryChangeInfo)) {
                throw new AlertRedirectException('정기배송 변경 정보를 찾을 수 없습니다.', null, null, '/mypage/regular_delivery.php');
            }

            $this->setData('applyNo', $applyNo);
            $this->setData('goodsNm', $deliveryChangeInfo['goodsNm']);
            $this->setData('addGoodsCount', $deliveryChangeInfo['addGoodsCount']);
            $this->setData('shippingInfo', $deliveryChangeInfo['shippingInfo']);
            $this->setData('paymentData', $deliveryChangeInfo['paymentData']);
            $this->setData('cycleData', $deliveryChangeInfo['cycleData']);
            $this->setData('selectedCycleData', $deliveryChangeInfo['selectedCycleData']);
        } catch (AlertRedirectException $e) {
            throw $e;
        } catch (\Throwable $e) {
            throw new AlertRedirectException($e->getMessage(), null, null, '/mypage/regular_delivery.php');
        }
    }
}
