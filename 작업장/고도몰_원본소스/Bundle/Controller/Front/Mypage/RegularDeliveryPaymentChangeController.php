<?php
/*
 * Copyright (C) 2025 NHN COMMERCE. - All Rights Reserved
 *
 * Unauthorized copying or redistribution of this file in source and binary forms via any medium
 * is strictly prohibited.
 */

namespace Bundle\Controller\Front\Mypage;

use Component\RegularDelivery\RegularOrder\RegularOrderPayment;

class RegularDeliveryPaymentChangeController extends \Controller\Front\Controller
{
    public function index()
    {
        $paymentSno = \Request::request()->get('paymentSno');

        // 정기결제 카드 목록 조회
        $memNo = \Session::get('member.memNo');
        $regularPaymentView = \App::getInstance(RegularOrderPayment::class);
        $regularPaymentCardList = $regularPaymentView->getCardListWithUsedStatus($memNo);

        // 데이터 뷰에 전달
        $this->setData('paymentSno', $paymentSno);
        $this->setData('regularPaymentCardList', $regularPaymentCardList);
    }
}
