<?php
/*
 * Copyright (C) 2025 NHN COMMERCE. - All Rights Reserved
 *
 * Unauthorized copying or redistribution of this file in source and binary forms via any medium
 * is strictly prohibited.
 */

namespace Bundle\Controller\Admin\Order;

use Component\Member\Manager;
use Controller\Admin\Controller;
use Request;

class PopupRegularOrderAdminMemoController extends Controller
{
    public function index()
    {
        try {
            Request::get()->set('page', Request::get()->get('page', 1));
            Request::get()->set('pageNum', Request::get()->get('pageNum', 10));
            Request::get()->set('sort', Request::get()->get('sort', 'regDt DESC'));

            $requestGetParams = Request::get()->all();
            $this->setData('requestGetParams', $requestGetParams);

            // 메모 코드
            $regularOrderAdminList = \App::getInstance(\Component\RegularDelivery\RegularOrderAdmin\RegularOrderAdminList::class);
            $adminMemoCode = $regularOrderAdminList->getAdminMemoCode();
            $this->setData('memoCd', $adminMemoCode);
            $adminMemoTotalList = $regularOrderAdminList->getAdminMemoList($requestGetParams['applyNo']);
            $adminMemoList = $regularOrderAdminList->getAdminMemoListByPage($requestGetParams['applyNo'], $requestGetParams['page'], $requestGetParams['pageNum']);
            $this->setData('memoData', $adminMemoList);
            $this->setData('managerSno', \Session::get('manager.sno'));

            // 공급사
            $this->setData('isProvider', Manager::isProvider());

            // 페이지
            $page = \App::load('Component\\Page\\Page');
            $page->setCurrentPage($requestGetParams['page']);
            $page->setTotal(count($adminMemoTotalList));
            $page->setAmount(count($adminMemoList));
            $page->setUrl(Request::getQueryString());
            $page->setPage();
            $this->setData('page', $page);

            $this->getView()->setDefine('layout', 'layout_blank.php');
            $this->getView()->setPageName('order/popup_regular_order_admin_memo.php');
        } catch (\Throwable $e) {
            \Logger::channel('regularOrderAdmin')->warning(__CLASS__ . '정기결제 신청서 관리자 메모 팝업 생성 실패', [$e->getMessage(), $e->getTrace()]);
            throw $e;
        }
    }
}
