<?php
/*
 * Copyright (C) 2025 NHN COMMERCE. - All Rights Reserved
 *
 * Unauthorized copying or redistribution of this file in source and binary forms via any medium
 * is strictly prohibited.
 */

namespace Bundle\Controller\Admin\Crm;

use Component\Coupon\CouponAdmin;

class LayerSelectCouponResultController extends \Controller\Admin\Controller
{
    public function index()
    {
        $request = \App::getInstance('request');
        $getParams = $request->get()->all();

        /** @var \Bundle\Component\Coupon\CouponAdmin $couponAdmin */
        $couponAdmin = \App::load('\\Component\\Coupon\\CouponAdmin');
        $useTypeArray = [];
        if (!empty($getParams['couponUseTypeList'])) {
            $useTypeArray = explode(',', $getParams['couponUseTypeList']);
            $useTypeArray = array_filter($useTypeArray); // 빈 문자열 제거
        }
        $deviceTypeArray = [];
        if (!empty($getParams['couponDeviceTypeList'])) {
            $deviceTypeArray = explode(',', $getParams['couponDeviceTypeList']);
            $deviceTypeArray = array_filter($deviceTypeArray); // 빈 문자열 제거
            $deviceTypeArray = array_map('strtolower', $deviceTypeArray); // 소문자로 변환
        }
        $couponAdminList = $couponAdmin->getCouponAdminList(addUseTypeWhereIn: $useTypeArray, addDeviceTypeWhereIn: $deviceTypeArray);

        $couponData = [];
        foreach ($couponAdminList['data'] as $index => $item) {
            $couponData[$item['couponNo']] = $item;
        }

        $page = \App::load('\\Component\\Page\\Page');
        $searchCount = $page->recode['total'];

        $this->setData('page', $page);
        $this->setData('couponData', $couponData);
        $this->setData('couponSearchCount', $searchCount);
        $this->setData('requestData', $getParams);
        $this->getView()->setDefine('layout', 'layout_layer.php');
    }
}
