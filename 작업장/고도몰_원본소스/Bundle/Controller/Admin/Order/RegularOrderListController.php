<?php
/*
 * Copyright (C) 2025 NHN COMMERCE. - All Rights Reserved
 *
 * Unauthorized copying or redistribution of this file in source and binary forms via any medium
 * is strictly prohibited.
 */

namespace Bundle\Controller\Admin\Order;

use Origin\Enum\RegularDelivery\RegularOrder\RegularOrderStatus;
use Request;

class RegularOrderListController extends \Origin\Controller\Admin\Controller
{
    public function index()
    {
        try {
            // --- 메뉴 설정
            $this->callMenu('order', 'order', 'regularDelivery');
            $this->addScript(
                [
                    'jquery/jquery.multi_select_box.js',
                ]
            );

            /* 운영자별 검색 설정값 */
            $searchConf = \App::load('\\Component\\Member\\ManagerSearchConfig');
            $searchConf->setGetData();

            $getValue = Request::get()->toArray();

            // 검색폼 키 데이터
            $regularOrderAdminList = \App::getInstance(\Component\RegularDelivery\RegularOrderAdmin\RegularOrderAdminList::class);
            $searchKeys = $regularOrderAdminList->getSearchKeys($getValue);
            $this->setData('search', $searchKeys);

            $applyStatusList = $regularOrderAdminList->getApplyStatusList();
            $this->setData('totalApplyStatus', RegularOrderStatus::getAllStatus());
            $this->setData('activeStatusList', RegularOrderStatus::getActiveStatus());
            $this->setData('activeStatusLabel', $applyStatusList['activeStatus']);
            $this->setData('inactiveStatusList', RegularOrderStatus::getInactiveStatus());
            $this->setData('inactiveStatusLabel', $applyStatusList['inactiveStatus']);
            $this->setData('pausedStatusList', RegularOrderStatus::getPauseStatus());

            $deliveryCycle = $regularOrderAdminList->getDeliveryCycle();
            $this->setData('deliveryCycleType', $deliveryCycle['deliveryCycleType']);
            $this->setData('deliveryCycleMonth', $deliveryCycle['deliveryCycleMonth']);
            $this->setData('deliveryCycleWeek', $deliveryCycle['deliveryCycleWeek']);
            $this->setData('deliveryCycleWeekDay', $deliveryCycle['deliveryCycleWeekDay']);

            // 일괄 처리 셀렉트박스
            $selectBoxApplyStatus = $regularOrderAdminList->getSelectBoxApplyStatus();
            $this->setData('selectBoxApplyStatus', $selectBoxApplyStatus);

            // 조회 항목 설정 및 노출 컬럼 선택
            $orderAdminGrid = \App::load('\\Component\\Order\\OrderAdminGrid');
            $orderAdminGridMode = $orderAdminGrid->getOrderAdminGridMode('regularOrder');
            $orderGridConfigList = $orderAdminGrid->getSelectOrderGridConfigList($orderAdminGridMode);
            $this->setData('orderAdminGridMode', $orderAdminGridMode);
            $this->setData('orderGridConfigList', $orderGridConfigList);
            
            // 검색 값으로 조회
            // 신청서 정보
            $regularOrderList = $regularOrderAdminList->getRegularOrderList($getValue, $orderGridConfigList);
            $this->setData('regularOrderList', $regularOrderList['list']);
            $totalRegularOrderCount = $regularOrderAdminList->getTotalCountRegularOrderList($getValue, $orderGridConfigList);

            // 페이지 설정
            $page = \App::load('Component\\Page\\Page');
            $page->setCurrentPage($getValue['page']);
            $page->setTotal($regularOrderList['count']);
            $page->setAmount($totalRegularOrderCount);
            $page->setList($getValue['pageNum'] ?? 10);
            $page->setUrl(\Request::getQueryString());
            // 페이지 내 레코드 개수 출력 목록을 재정의
            $pageNumList = [10, 20, 30, 40, 50, 60, 70, 80, 90, 100, 200, 300, 500];
            $page->setPageNumList(gd_isset($pageNumList));
            $page->setPage();
            $this->setData('page', gd_isset($page));

            $this->getView()->setDefine('layoutRegularOrderSearchForm', Request::getDirectoryUri() . '/layout_regular_order_search_form.php');// 검색폼

            // 공급사와 동일한 페이지 사용
            $this->getView()->setPageName('order/regular_order_list.php');
        } catch (\Throwable $e) {
            \Logger::channel('regularOrder')->warning("정기결제 신청서 목록 조회 실패 ", [$e->getMessage(), $e->getTrace()]);
            throw $e;
        }
    }
}
