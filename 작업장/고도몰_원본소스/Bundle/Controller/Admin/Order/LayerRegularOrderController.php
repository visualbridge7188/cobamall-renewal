<?php
/*
 * Copyright (C) 2025 NHN COMMERCE. - All Rights Reserved
 *
 * Unauthorized copying or redistribution of this file in source and binary forms via any medium
 * is strictly prohibited.
 */

namespace Bundle\Controller\Admin\Order;

use Component\Member\Manager;
use Bundle\Controller\Admin\Controller;
use DTO\RegularDelivery\RegularOrder\RegularOrderGiftConditionDTO;
use Origin\Enum\RegularDelivery\RegularGoods\RegularGoodsGift;

class LayerRegularOrderController extends Controller
{
    public function index()
    {
        try {
            $getValue = \Request::get()->toArray();

            switch ($getValue['mode']) {
                case 'change-delivery-round' :
                    $regularOrderAdminDetail = \App::getInstance(\Component\RegularDelivery\RegularOrderAdmin\RegularOrderAdminDetail::class);
                    $deliveryCycle = $regularOrderAdminDetail->getRegularGoodsDeliveryCycle($getValue['applyNo']);
                    $this->setData('deliveryCycleType', $deliveryCycle['cycleType']);
                    $this->setData('deliveryCycle', $deliveryCycle['cycle']);
                    $this->setData('roundDisplayType', $deliveryCycle['roundDisplayType']);
                    $this->setData('maxDeliveryRounds', $deliveryCycle['maxDeliveryRounds']);
                    $this->setData('currentDeliveryInfo', $deliveryCycle['currentDeliveryInfo']);

                    $this->setData('applyNo', $getValue['applyNo']);

                    // 레이어 템플릿
                    $this->getView()->setDefine('layout', 'layout_layer.php');
                    $this->getView()->setPageName('order/layer_regular_order_delivery_change.php');
                    break;
                case 'change-shipping-address' :
                    $pageNum = (int) \Request::get()->get('page', 1);
                    $memNo = (int) $getValue['memNo'];

                    $regularOrderShippingAddress = \App::getInstance(\Component\RegularDelivery\RegularOrder\RegularOrderShippingAddress::class);
                    $shippingInfo = $regularOrderShippingAddress->getRegularOrderShippingAddress($memNo, $pageNum);
                    $shippingList = $shippingInfo['list'];
                    $pager = $shippingInfo['pager'];

                    $this->setData('applyNo', $getValue['applyNo']);
                    $this->setData('shippingInfo', $shippingList);
                    $this->setData('memNo', $memNo);
                    $this->setData('page', $pager);

                    // 레이어 템플릿
                    $this->getView()->setDefine('layout', 'layout_layer.php');
                    $this->getView()->setPageName('order/layer_regular_order_shipping_address.php');
                    break;
                case 'regular-delivery-log' :
                    $regularOrderAdminDetail = \App::getInstance(\Component\RegularDelivery\RegularOrderAdmin\RegularOrderAdminDetail::class);
                    $deliveryLog = $regularOrderAdminDetail->getRegularDeliveryLog($getValue['applyNo']);
                    $isProvider = Manager::isProvider();
                    $this->setData('isProvider', $isProvider);
                    $this->setData('deliveryLog', $deliveryLog);

                    // 레이어 템플릿
                    $this->getView()->setDefine('layout', 'layout_layer.php');
                    $this->getView()->setPageName('order/layer_regular_delivery_log.php');
                    break;
                case 'regular-status-log' :
                    $regularOrderAdminDetail = \App::getInstance(\Component\RegularDelivery\RegularOrderAdmin\RegularOrderAdminDetail::class);
                    $statusLog = $regularOrderAdminDetail->getRegularStatusLog($getValue['applyNo']);
                    $this->setData('statusLog', $statusLog);

                    // 레이어 템플릿
                    $this->getView()->setDefine('layout', 'layout_layer.php');
                    $this->getView()->setPageName('order/layer_regular_status_log.php');
                    break;
                case 'regular-change-log' :
                    $regularOrderAdminDetail = \App::getInstance(\Component\RegularDelivery\RegularOrderAdmin\RegularOrderAdminDetail::class);
                    $changeLog = $regularOrderAdminDetail->getRegularChangeLog($getValue['applyNo']);
                    $this->setData('changeLog', $changeLog);

                    // 레이어 템플릿
                    $this->getView()->setDefine('layout', 'layout_layer.php');
                    $this->getView()->setPageName('order/layer_regular_change_log.php');
                    break;
                case 'regular-gift-update-list' :
                    $regularOrderGiftConditionDTO = new RegularOrderGiftConditionDTO($getValue);
                    $regularGift = \App::getInstance(\Component\RegularDelivery\RegularGoods\RegularGift::class);
                    $regularGoodsGiftData = $regularGift->getRegularGoodsGiftPresentInfo($regularOrderGiftConditionDTO);

                    $this->setData('applyNo', $getValue['applyNo']);
                    $this->setData('selectGiftData', $regularGoodsGiftData['selectGiftData']);
                    $this->setData('regularGiftPresentInfoSno', $regularGoodsGiftData['regularGiftPresentInfoSno']);
                    $this->setData('selectCount', $regularGoodsGiftData['selectCount']);
                    $this->setData('totalMultiGiftNum', $regularGoodsGiftData['totalMultiGiftNum']);
                    $this->setData('multiGiftData', $regularGoodsGiftData['multiGiftData']);
                    $this->setData('page', $regularGoodsGiftData['page']);
                    $this->setData('ERROR_REGULAR_GIFT_DELETE', RegularGoodsGift::ERROR_REGULAR_GIFT_DELETE);

                    // 레이어 템플릿
                    $this->getView()->setDefine('layout', 'layout_layer.php');
                    $this->getView()->setPageName('order/layer_regular_gift_update.php');
                    break;
            }

        } catch (\Throwable $e) {
            \Logger::channel('regularOrderAdmin')->warning('정기결제 신청서 상태 변경 레이어 노출 실패', [$e->getMessage(), $e->getTrace()]);
            throw $e;
        }
    }
}
