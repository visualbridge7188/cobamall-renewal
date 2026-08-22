<?php
/*
 * Copyright (C) 2025 NHN COMMERCE. - All Rights Reserved
 *
 * Unauthorized copying or redistribution of this file in source and binary forms via any medium
 * is strictly prohibited.
 */

namespace Bundle\Controller\Admin\Order;

use Bundle\Controller\Admin\Controller;
use Component\Member\Manager;
use Origin\Enum\RegularDelivery\RegularOrder\RegularOrderStatus;

class RegularOrderViewController extends Controller
{
    public function index()
    {
        $applyNo = \Request::get()->get('applyNo');

        try {
            if (empty($applyNo)) {
                throw new \Exception('정기결제 신청번호가 존재하지 않습니다.');
            }

            // 메뉴 설정
            $this->callMenu('order', 'order', 'regularDeliveryView');

            $this->setData('applyNo', $applyNo);

            // 정기결제 신청서 데이터
            $regularOrderAdminDetail = \App::getInstance(\Component\RegularDelivery\RegularOrderAdmin\RegularOrderAdminDetail::class);
            $regularOrderData = $regularOrderAdminDetail->getRegularOrderApplyInfo($applyNo);
            $this->setData('data', $regularOrderData['regularOrderGoods']);
            $this->setData('gift', $regularOrderData['giftInfo']);
            $this->setData('giftEnable', gd_policy('goods.gift'));
            $this->setData('regularOrderPrice', $regularOrderData['regularOrderPrice']);
            $this->setData('regularOrderTotalPrice', $regularOrderData['regularOrderTotalPrice']);
            $this->setData('originOrderPrice', $regularOrderData['originOrderPrice']);
            $this->setData('originOrderTotalPrice', $regularOrderData['originOrderTotalPrice']);
            $this->setData('totalDcPrice', $regularOrderData['totalDcPrice']);
            $this->setData('cardCompany', $regularOrderData['cardCompany']);
            $this->setData('cardNo', $regularOrderData['cardNo']);
            $this->setData('applyStatus', $regularOrderData['applyStatus']);
            $this->setData('canSkipFl', $regularOrderData['canSkipFl']);
            $this->setData('pausedStatusList', RegularOrderStatus::getPauseStatus());
            $this->setData('inactiveStatusList', RegularOrderStatus::getInactiveStatus());

            // 정기결제 신청서 신청자 및 수령자 정보
            $regularOrderShippingInfo = $regularOrderAdminDetail->getRegularOrderShippingInfo($applyNo);
            $this->setData('applierInfo', $regularOrderShippingInfo['applierInfo']);
            $this->setData('shippingInfo', $regularOrderShippingInfo['shippingInfo']);

            // 정기결제 신청서 상담 및 관리자 메모 정보
            $consultMemo = $regularOrderAdminDetail->getRegularOrderConsultMemo($applyNo);
            $this->setData('consultMemo', $consultMemo);

            // 조회 항목 설정 및 노출 컬럼 선택
            $orderAdminGrid = \App::load('\\Component\\Order\\OrderAdminGrid');
            $orderAdminGridMode = $orderAdminGrid->getOrderAdminGridMode('regularOrderDetail');
            $orderGridConfigList = $orderAdminGrid->getSelectOrderGridConfigList($orderAdminGridMode);
            $this->setData('orderGridConfigList', $orderGridConfigList);

            // 일괄 처리 셀렉트박스
            $selectBoxApplyStatus = $regularOrderAdminDetail->getSelectBoxApplyStatus();
            $this->setData('selectBoxApplyStatus', $selectBoxApplyStatus);

            // 공급사
            $this->setData('isProvider', Manager::isProvider());

            // 메일도메인
            $emailDomain = gd_array_change_key_value(gd_code('01004'));
            $emailDomain = array_merge(['self' => __('직접입력')], $emailDomain);
            $this->setData('emailDomain', $emailDomain); // 메일주소 리스팅

            // 관리자 메모 메모 구분
            $adminMemo = $regularOrderAdminDetail->getAdminMemo($applyNo);
            $this->setData('memoData', $adminMemo);
            $this->setData('managerSno', \Session::get('manager.sno'));
            $adminMemoCode = $regularOrderAdminDetail->getAdminMemoCode();
            $this->setData('memoCd', $adminMemoCode);

            if (\Request::get()->get('popupMode', '') === 'yes') {
                $this->getView()->setDefine('layout', 'layout_blank.php');
            }

            // 공급사와 동일한 페이지 사용
            $this->getView()->setPageName('order/regular_order_view.php');
        } catch (\Throwable $e) {
            \Logger::channel('regularOrderAdmin')->warning('정기결제 신청서 상세페이지 조회 실패', [$e->getMessage(), $e->getTrace()]);
            throw $e;
        }
    }
}
