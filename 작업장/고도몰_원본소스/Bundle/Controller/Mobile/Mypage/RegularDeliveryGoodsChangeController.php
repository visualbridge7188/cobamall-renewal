<?php
/*
 * Copyright (C) 2025 NHN COMMERCE. - All Rights Reserved
 *
 * Unauthorized copying or redistribution of this file in source and binary forms via any medium
 * is strictly prohibited.
 */

namespace Bundle\Controller\Mobile\Mypage;

class RegularDeliveryGoodsChangeController extends \Controller\Front\Mypage\RegularDeliveryGoodsChangeController
{
    public function index()
    {
        parent::index();

        // 페이지 이름 설정
        $gPageName = __("상품정보 변경");
        $this->setData('gPageName', $gPageName);
    }
}
