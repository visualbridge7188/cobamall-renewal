<?php
/*
 * Copyright (C) 2025 NHN COMMERCE. - All Rights Reserved
 *
 * Unauthorized copying or redistribution of this file in source and binary forms via any medium
 * is strictly prohibited.
 */

namespace Bundle\Controller\Admin\Order;

use Bundle\Controller\Admin\Controller;

class LayerRegularOrderAdminMemoController extends Controller
{
    public function index()
    {
        try {
            $postValue = \Request::post()->toArray();

            $regularOrderAdminList = \App::getInstance(\Component\RegularDelivery\RegularOrderAdmin\RegularOrderAdminList::class);
            $memoData = $regularOrderAdminList->getAdminMemoList($postValue['applyNo']);
            $this->setData('memoData', $memoData);

            $this->getView()->setDefine('layout', 'layout_layer.php');
            $this->getView()->setPageName('order/layer_regular_order_admin_memo.php');
        } catch (\Throwable $e) {
            \Logger::channel('regularOrderAdmin')->warning(__CLASS__ . '정기결제 신청서 관리자 메모 조회 실패', [$e->getMessage(), $e->getTrace()]);
            throw $e;
        }
    }
}
