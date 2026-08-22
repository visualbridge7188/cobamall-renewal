<?php
/*
 * Copyright (C) 2025 NHN COMMERCE. - All Rights Reserved
 *
 * Unauthorized copying or redistribution of this file in source and binary forms via any medium
 * is strictly prohibited.
 */

namespace Bundle\Controller\Admin\Order;

use DTO\RegularDelivery\RegularGoods\RegularGoodsChangeDTO;
use DTO\RegularDelivery\RegularOrder\DeliveryCycleChangeDTO;
use DTO\RegularDelivery\RegularOrder\RegularOrderGiftUpdateDTO;
use DTO\RegularDelivery\RegularOrder\RegularOrderSkipDTO;
use DTO\RegularDelivery\RegularOrder\RegularOrderShippingAddressUpdateDTO;
use Component\RegularDelivery\RegularGoods\RegularGoods;
use Component\RegularDelivery\RegularGoods\RegularGoodsChange;
use Component\RegularDelivery\RegularOrder\RegularOrderSkip;
use Component\RegularDelivery\RegularOrder\RegularOrderShippingAddress;
use Component\RegularDelivery\RegularOrder\RegularOrderCycleChange;
use Controller\Admin\Controller;
use Component\RegularDelivery\RegularGoods\RegularGift;
use Origin\Exception\Order\RegularOrder\DeliveryRoundUpdateFailException;

class LayerRegularOrderPsController extends Controller
{
    /**
     * @return void
     * @throws \Throwable
     */
    public function index()
    {
        try {
            $postValue = \Request::post()->toArray();
            $postValue['sessionType'] = 'admin'; // 관리자 변경임으로 관리자 타입으로 고정
            $postValue['sessionSno'] = \Session::get('manager.sno'); // 관리자 변경임으로 관리자 번호 고정
            switch ($postValue['mode']) {
                case 'change_delivery_cycle' : // 신청서 배송주기 업데이트
                    $cycleChangeDto = new DeliveryCycleChangeDTO($postValue);
                    $regularOrderCycleChange = \App::getInstance(RegularOrderCycleChange::class);
                    $regularOrderCycleChange->updateRegularDeliveryCycle($cycleChangeDto);
                    $this->json(['result' => 'success', 'message' => '저장이 완료 되었습니다.']);
                    break;
                case 'skip-delivery-round' : // 신청서 건너뛰기
                    $skipDTO = new RegularOrderSkipDTO($postValue);
                    $regularOrderSkip = \App::getInstance(RegularOrderSkip::class);
                    $regularOrderSkip->skipRegularOrder($skipDTO);
                    $this->json(['result' => 'success', 'message' => '저장이 완료 되었습니다.']);
                    break;
                case 'change_shipping_address' : // 신청서 배송지 변경
                    $addressUpdateDTO = new RegularOrderShippingAddressUpdateDTO($postValue);
                    $regularOrderAddress = \App::getInstance(RegularOrderShippingAddress::class);
                    $regularOrderAddress->updateRegularOrderShippingAddress($addressUpdateDTO);
                    $this->json(['result' => 'success', 'message' => '저장이 완료 되었습니다.']);
                    break;
                case 'update_regular_goods' :
                    $addGoodsCnt = $postValue['addGoodsCnt'] ? array_sum($postValue['addGoodsCnt']) : 0;
                    $regularGoodsService = \App::getInstance(RegularGoods::class);
                    $giftData = $regularGoodsService->getFilteredRegularGiftData($postValue['regularGoodsNo'], $postValue['goodsCnt'], $addGoodsCnt, 50);
                    $giftInfo = [];
                    if (!empty($giftData) && !empty($giftData['list'])) {
                        foreach ($giftData['list'] as $gift) {
                            $giftInfo[] = [
                                'applyNo' => $postValue['applyNo'],
                                'giftNo' => $gift['giftNo'],
                                'regularGiftPresentInfoSno' => $giftData['regularGiftPresentInfoSno']
                            ];
                        }
                    }
                    $goodsInfoData = array_merge($postValue, [
                        'giftInfo' => $giftInfo
                    ]);
                    $goodsChangeDto = new RegularGoodsChangeDTO($goodsInfoData);
                    $goodsChangeService = \App::getInstance(RegularGoodsChange::class);
                    $goodsChangeService->update($goodsChangeDto);
                    $this->json(['result' => 'success', 'message' => '저장이 완료되었습니다.']);
                    break;
                case 'update_regular_gift' :
                    $giftUpdateDTO = new RegularOrderGiftUpdateDTO($postValue);
                    $regularGift = \App::getInstance(RegularGift::class);
                    $regularGift->updateRegularGift($giftUpdateDTO);
                    $this->json(['result' => 'success', 'message' => '저장이 완료되었습니다.']);
                    break;
                default :
                    $this->json('변경에 실패하였습니다. 잠시 후 다시 시도해주세요.');
            }
        } catch (DeliveryRoundUpdateFailException $e) {
            \Logger::channel('regularOrderAdmin')->warning('정기결제 신청서 상태 변경 실패', [$e->getMessage(), $e->getTrace()]);
            $this->json(['result' => 'fail', 'message' => $e->getMessage()]);
        } catch (\Throwable $e) {
            \Logger::channel('regularOrderAdmin')->warning('정기결제 신청서 상태 변경 실패', [$e->getMessage(), $e->getTrace()]);

            if ($postValue['mode'] === 'change_delivery_cycle') {
                $this->json(['result' => 'fail', 'message' => '배송주기 변경에 실패하였습니다. 잠시 후 다시 시도해주세요.']);
            } elseif ($postValue['mode'] === 'skip-delivery-round') {
                $this->json(['result' => 'fail', 'message' => '회차 건너뛰기에 실패하였습니다. 잠시 후 다시 시도해주세요.']);
            } elseif ($postValue['mode'] === 'update_regular_gift') {
                $this->json(['result' => 'fail', 'message' => $e->getMessage(), 'code' => $e->getCode()]);
            } elseif (in_array($postValue['mode'], ['update_regular_goods', 'change_shipping_address'], true)) {
                $this->json(['result' => 'fail', 'message' => $e->getMessage()]);
            } else {
                throw $e;
            }
        }
    }
}
