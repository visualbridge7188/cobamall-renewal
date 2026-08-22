<?php
/*
 * Copyright (C) 2025 NHN COMMERCE. - All Rights Reserved
 *
 * Unauthorized copying or redistribution of this file in source and binary forms via any medium
 * is strictly prohibited.
 */

namespace Bundle\Controller\Front\Mypage;

use Component\RegularDelivery\RegularGoods\RegularGift;
use Component\RegularDelivery\RegularGoods\RegularGoodsChange;
use Component\RegularDelivery\RegularOrder\RegularOrderSkip;
use Component\RegularDelivery\RegularOrder\RegularOrderStatusChange;
use Component\RegularDelivery\RegularOrder\RegularOrderDeliveryInfo;
use Component\Mypage\RegularDelivery;
use DTO\RegularDelivery\RegularGoods\RegularGoodsChangeDTO;
use DTO\RegularDelivery\RegularOrder\RegularOrderSkipDTO;
use DTO\RegularDelivery\RegularOrder\RegularOrderStatusChangeDTO;
use DTO\RegularDelivery\RegularOrder\RegularOrderDeliveryInfoUpdateDTO;
use DTO\RegularDelivery\RegularOrder\RegularOrderGiftUpdateDTO;
use Origin\Enum\Delivery\RegularDeliveryCancelType;
use Origin\Enum\RegularDelivery\RegularOrder\RegularOrderStatus;
use Request;

class RegularDeliveryPsController extends \Controller\Front\Controller
{
    /**
     * @return array JSON 응답 데이터
     */
    public function index() {
        try {
            $request = Request::post()->toArray();
            $applyNo = $request['applyNo'];
            $mode = $request['mode'];

            // 신청서 소유권 확인
            $regularDelivery = \App::getInstance(RegularDelivery::class);
            $regularDelivery->verifyOwnership((int)$applyNo, (int)\Session::get('member.memNo'));

            switch ($mode) {
                case 'update_goods_info':
                    // 요청 데이터 정리
                    $giftNoList = $request['giftNo'];
                    foreach ($giftNoList as $gift) {
                        $giftInfo[] = [
                            'applyNo' => $applyNo,
                            'giftNo' => $gift,
                            'regularGiftPresentInfoSno' => $request['regularGiftPresentInfoSno']
                        ];
                    }
                    $goodsInfoData = array_merge($request, [
                        'giftInfo' => $giftInfo,
                        'sessionType' => 'user',
                        'sessionSno' => \Session::get('member.memNo')
                    ]);
                    $goodsChangeDto = new RegularGoodsChangeDTO($goodsInfoData);

                    // 상품 업데이트 처리
                    $goodsChangeService = \App::getInstance(RegularGoodsChange::class);
                    $goodsChangeService->update($goodsChangeDto);
                    $message = '상품정보 변경이 완료되었습니다.';
                    break;

                case 'update_gift_info':
                    $giftInfoData = array_merge($request, [
                        'applyNo' => $applyNo,
                        'selectCount' => count($request['giftNo']),
                        'regularGiftPresentInfoSno' => $request['regularGiftPresentInfoSno'],
                        'giftNoList' => $request['giftNo'],
                        'sessionType' => 'user',
                        'sessionSno' => \Session::get('member.memNo')
                    ]);
                    $regularOrderGiftUpdateDTO = new RegularOrderGiftUpdateDTO($giftInfoData);
                    $regularGift = \App::getInstance(RegularGift::class);
                    $regularGift->updateRegularGift($regularOrderGiftUpdateDTO);
                    $message = '상품정보 변경이 완료되었습니다.';
                    break;

                case 'update_delivery_info':
                    $deliveryInfoData = array_merge($request, [
                        'sessionType' => 'user',
                        'sessionSno' => \Session::get('member.memNo')
                    ]);
                    $deliveryInfoUpdateDto = new RegularOrderDeliveryInfoUpdateDTO($deliveryInfoData);
                    $deliveryInfo = \App::getInstance(RegularOrderDeliveryInfo::class);
                    $message = $deliveryInfo->updateRegularDeliveryInfo($deliveryInfoUpdateDto);
                    break;

                case 'pause':
                    $changeData = [
                        'applyNoList' => [$applyNo], // 배열형태로 변경
                        'updateStatus' => RegularOrderStatus::USER_STOP,
                        'sessionType' => 'user',
                        'sessionSno' => \Session::get('member.memNo')
                    ];
                    $statusChangeDTO = new RegularOrderStatusChangeDTO($changeData);
                    $statusChange = \App::getInstance(RegularOrderStatusChange::class);
                    $statusChange->updateApplyStatus($statusChangeDTO);
                    $message = '일시정지 상태로 변경되었습니다.';
                    break;

                case 'skip':
                    $skipInfo = [
                        'applyNo' => $applyNo,
                        'sessionType' => 'user',
                        'sessionSno' => \Session::get('member.memNo')
                    ];
                    $skipDto = new RegularOrderSkipDTO($skipInfo);
                    $regularOrderSkip = \App::getInstance(RegularOrderSkip::class);
                    $regularOrderSkip->skipRegularOrder($skipDto);
                    $message = '회차 건너뛰기 처리되었습니다.';
                    break;

                case 'cancel':
                    $changeData = [
                        'applyNoList' => [$applyNo], // 배열형태로 변경
                        'updateStatus' => RegularOrderStatus::USER_INACTIVE,
                        'sessionType' => 'user',
                        'sessionSno' => \Session::get('member.memNo'),
                        'reason' => $request['cancelReasonMsg']
                    ];
                    // 기타 사유 선택 시 상세 사유로 로그에 저장
                    if ($request['cancelReasonCode'] === RegularDeliveryCancelType::ETC_CODE) {
                        $changeData['reason'] = $request['cancelDetail'];
                    }
                    $statusChangeDTO = new RegularOrderStatusChangeDTO($changeData);
                    $statusChange = \App::getInstance(RegularOrderStatusChange::class);
                    $statusChange->updateApplyStatus($statusChangeDTO);
                    $message = '해지 처리되었습니다.';
                    break;
                    
                case 'resume':
                    $changeData = [
                        'applyNoList' => [$applyNo], // 배열형태로 변경
                        'updateStatus' => RegularOrderStatus::ACTIVE,
                        'sessionType' => 'user',
                        'sessionSno' => \Session::get('member.memNo')
                    ];
                    $statusChangeDTO = new RegularOrderStatusChangeDTO($changeData);
                    $statusChange = \App::getInstance(RegularOrderStatusChange::class);
                    $statusChange->updateApplyStatus($statusChangeDTO);
                    $message = '일시정지 해제 처리되었습니다.';
                    break;

                case 'validate_current_regular_order_goods_info':
                    // 현재 정기결제(배송) 상품 정보에 대한 유효성 검사

                    $goodsInfoData = array_merge($request, [
                        'sessionType' => 'user',
                        'sessionSno' => \Session::get('member.memNo')
                    ]);
                    $goodsChangeDto = new RegularGoodsChangeDTO($goodsInfoData);

                    // 상품 업데이트 처리
                    $goodsChangeService = \App::getInstance(RegularGoodsChange::class);
                    $goodsChangeService->validateCurrentRegularOrderGoodsInfo($goodsChangeDto);
                    $message = '상품정보 변경이 완료되었습니다.';
                    break;
                    
                default:
                    throw new \Exception('Unsupported mode value');
            }

            $this->json(['success' => true, 'message' => $message]);

        } catch (\Throwable $e) {
            $message = empty($e->getMessage()) ? '일시적인 오류로 처리에 실패하였습니다. 잠시 후 다시 시도해주세요.' : $e->getMessage();
            \Logger::channel('regularOrder')->error(__CLASS__ . ' 마이페이지 정기배송 상태 변경 실패 : ',
                [
                    'message' => $message,
                    'type' => get_class($e),
                    'request' => $request,
                    'code' => $e->getCode() ?? null
                ]
            );

            $this->json(['success' => false, 'message' => $message]);
        }
    }
}
