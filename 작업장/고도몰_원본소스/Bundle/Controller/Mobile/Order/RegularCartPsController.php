<?php
/*
 * Copyright (C) 2025 NHN COMMERCE. - All Rights Reserved
 *
 * Unauthorized copying or redistribution of this file in source and binary forms via any medium
 * is strictly prohibited.
 */

namespace Bundle\Controller\Mobile\Order;

/**
 * 정기결제 장바구니 처리 컨트롤러
 * cart_regular_order.html에서 호출하는 처리 전용
 *
 * @package Bundle\Controller\Mobile\Order
 */
class RegularCartPsController extends \Controller\Front\Order\RegularCartPsController
{

    public function index()
    {
        parent::index();
    }
}

