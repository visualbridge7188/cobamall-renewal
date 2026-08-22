<?php
/*
 * Copyright (C) 2025 NHN COMMERCE. - All Rights Reserved
 *
 * Unauthorized copying or redistribution of this file in source and binary forms via any medium
 * is strictly prohibited.
 */

namespace Bundle\Controller\Mobile\Order;

use DTO\RegularDelivery\RegularOrder\RegularOrderShippingAddressModifyDTO;
use DTO\RegularDelivery\RegularOrder\RegularOrderShippingAddressRegistDTO;
use Component\Member\Util\MemberUtil;
use Framework\Utility\StringUtils;
use Request;
use Controller\Mobile\Controller;

class LayerRegularOrderShippingPsController extends Controller
{
    public function index()
    {
        try {

            if (!Request::isAjax()) {
                throw new \Exception('Ajax ' . __('전용 페이지 입니다.'));
            }

            if (!MemberUtil::isLogin()) {
                throw new \Exception(__('로그인을 하셔야 사용가능합니다.'));
            }

            // xss 취약점 보안
            $postValue = StringUtils::xssArrayClean(Request::post()->toArray());
            $postValue['shippingAddressSub'] = StringUtils::removeAttributeOnclick($postValue['shippingAddressSub']);
            $regularOrderShippingAddress = \App::getInstance(\Component\RegularDelivery\RegularOrder\RegularOrderShippingAddress::class);
            $memNo = \Session::get('member.memNo');

            switch (Request::post()->get('mode')) {
                // 등록 액션
                case 'shipping_regist':
                    $dto = new RegularOrderShippingAddressRegistDTO($postValue);
                    \Logger::channel('regularOrder')->info(__CLASS__ . ' 정기결제 신청서 - 배송지 등록', [$dto->toArray()]);

                    $shippingSno = $regularOrderShippingAddress->registerShippingAddress($dto);
                    $shippingInfo = $regularOrderShippingAddress->getShippingAddressInfo($shippingSno, $memNo);

                    $this->json([
                        'code' => 200, // Success
                        'shippingInfo' => $shippingInfo,
                        'message' => __('배송지가 정상적으로 등록되었습니다.'),
                    ]);
                    break;
                // 수정 액션
                case 'shipping_modify':
                    if (!Request::post()->has('sno')) {
                        throw new \Exception(__('배송지 관리 번호를 입력하세요.'));
                    }
                    $dto = new RegularOrderShippingAddressModifyDTO($postValue);
                    \Logger::channel('regularOrder')->info(__CLASS__ . '정기결제 신청서 - 배송지 수정', [$dto->toArray()]);

                    if (!$regularOrderShippingAddress->modifyShippingAddress($dto)) {
                        throw new \Exception(__('이미 등록된 배송지 입니다.'));
                    }

                    $this->json([
                        'code' => 200,
                        'shippingSno' => $postValue['sno'],
                        'message' => __('배송지가 정상적으로 등록되었습니다.'),
                    ]);
                    break;
                // 삭제 액션
                case 'shipping_delete':
                    if (!Request::post()->has('sno')) {
                        throw new \Exception(__('배송지 관리 번호를 입력하세요.'));
                    }

                    $sno = Request::post()->get('sno');
                    \Logger::channel('regularOrder')->info(__CLASS__ . '정기결제 신청서 - 배송지 삭제', [$sno]);
                    if (!$regularOrderShippingAddress->deleteShippingAddress($sno, $memNo)) {
                        throw new \Exception(__('배송지 삭제에 실패하였습니다.'));
                    }

                    $this->json([
                        'code' => 200,
                        'message' => __('배송지가 정상적으로 삭제되었습니다.'),
                    ]);
                    break;
                case 'shipping_default':
                    if (!Request::post()->has('sno')) {
                        throw new Exception(__('배송지 관리 번호를 입력하세요.'));
                    }

                    $regularOrderShippingAddress->setDefaultShippingAddress(Request::post()->get('sno'), $memNo);

                    $this->json([
                        'code' => 200,
                        'message' => __('기본배송지로 설정 되었습니다.'),
                    ]);
                    break;
                
                default:
                    \Logger::channel('regularOrder')->info(__CLASS__ . ' 정기결제 신청서 - 배송지 등록 모드 없음.');
                    $this->json([
                        'code' => 0, // Fail
                        'message' => __('배송지 등록에 실패하였습니다.'),
                    ]);
                    break;
            }
            exit;

        } catch (\Exception $e) {
            $message = !empty($e->getMessage()) ? $e->getMessage() : '일시적인 오류로 처리에 실패하였습니다. 잠시 후 다시 시도해주세요.';
            \Logger::channel('regularOrder')->warning(
                __CLASS__ .' '. __METHOD__. '정기결제 배송지 등록 실패 . ', [$message]
            );
            $this->json([
                'code' => 0, // Fail
                'message' => $message
            ]);
        }
    }
}
