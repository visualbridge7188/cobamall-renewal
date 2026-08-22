<?php
/*
 * Copyright (C) 2025 NHN COMMERCE. - All Rights Reserved
 *
 * Unauthorized copying or redistribution of this file in source and binary forms via any medium
 * is strictly prohibited.
 */

namespace Bundle\Controller\Front\Mypage;

use Origin\Enum\Delivery\RegularDeliveryCancelType;
use Component\Mypage\RegularDelivery;

class RegularDeliveryCancelController extends \Controller\Front\Controller
{
    public function index()
    {
        // 정기배송 해지 사유 코드 조회
        $regularDelivery = \App::getInstance(RegularDelivery::class);
        $cancelReasonsInfo = $regularDelivery->getCancelReasonCodes();
        
        $cancelReasons = array_map(function($item) {
            return [
                'itemCd' => $item['itemCd'],
                'itemNm' => $item['itemNm']
            ];
        }, $cancelReasonsInfo);

        $applyNo = \Request::get()->get('applyNo');

        $this->setData('applyNo', $applyNo);
        $this->setData('ETC_CODE', RegularDeliveryCancelType::ETC_CODE);
        $this->setData('cancelReasons', $cancelReasons);
    }

}
