<?php
/*
 * Copyright (C) 2025 NHN COMMERCE. - All Rights Reserved
 *
 * Unauthorized copying or redistribution of this file in source and binary forms via any medium
 * is strictly prohibited.
 */

namespace Bundle\Controller\Admin\Share\Ncds;

class PopupOrderController extends \Controller\Admin\Share\PopupOrderController
{

    public function index()
    {
        parent::index();

        // sortList 배열 재정의 - 사용자 요청 순서대로
        $search = $this->getData('search');
        if (isset($search['sortList']) && is_array($search['sortList'])) {
            // 배열을 완전히 새로 생성하여 순서 보장
            $search['sortList'] = [
                'o.regDt desc' => __('최근 주문일순'),
                'o.regDt asc' => __('과거 주문일순'),
                'og.orderNo desc' => __('주문번호 내림차순'),
                'og.orderNo asc' => __('주문번호 오름차순'),
                'o.orderGoodsNm desc' => __('상품명 내림차순'),
                'o.orderGoodsNm asc' => __('상품명 오름차순'),
                'oi.orderName desc' => __('주문자 내림차순'),
                'oi.orderName asc' => __('주문자 오름차순'),
                'o.settlePrice desc' => __('총 결제금액 높은순'),
                'o.settlePrice asc' => __('총 결제금액 낮은순'),
                'oi.receiverName desc' => __('수령자 내림차순'),
                'oi.receiverName asc' => __('수령자 오름차순'),
                'sm.companyNm desc' => __('공급사 내림차순'),
                'sm.companyNm asc' => __('공급사 오름차순'),
            ];
            $this->setData('search', $search);
        }

        $this->getView()->setDefine('layoutOrderSearchForm', 'share/ncds/layout_order_search_form.php');
    }
}
