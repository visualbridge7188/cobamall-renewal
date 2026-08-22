<?php
/*
Copyright (C) 2025 NHN COMMERCE. - All Rights Reserved 

Unauthorized copying or redistribution of this file in source and binary forms via any medium 
is strictly prohibited.
*/

namespace Bundle\Controller\Front\Present;

use Request;
use Logger;
use Component\Present\Confirm\PresentConfirm;
use Component\Present\Order\PresentOrderAdmin;
use Framework\Debug\Exception\AlertRedirectException;
use Respect\Validation\Validator;
use Respect\Validation\Exceptions\ValidationException;
use Component\Member\Util\MemberUtil;
/**
 * 선물수락/배송지입력 컨트롤러
 * 
 */
class PresentConfirmController extends \Controller\Front\Controller
{
    public function index() {

        try {
            // URL 파라미터에서 선물하기용 토큰키 받기
            $confirmToken = Request::get()->get('token');

            // 토큰 값이 비어있는지 검증
            Validator::notEmpty()
                ->setTemplate('유효하지 않은 링크입니다.')
                ->assert($confirmToken);

            // 확인토큰으로 주문번호+주문상품번호 조회
            $presentConfirm = \App::getInstance(PresentConfirm::class);
            $orderInfo = $presentConfirm->getOrderInfoByConfirmToken($confirmToken);
            
            // confirmToken과 orderNo, orderGoodsNo 기준으로 검증
            Validator::not(Validator::falseVal())
                ->key('orderNo', Validator::notEmpty())
                ->key('orderGoodsNo', Validator::notEmpty())
                ->setTemplate('해당 URL은 파기되어 더 이상 접근할 수 없습니다.')
                ->assert($orderInfo);
            
            $orderNo = $orderInfo['orderNo'];
            $orderGoodsNo = $orderInfo['orderGoodsNo'];
            
            // 주문자 정보 가져오기
            $order = \App::load('\\Component\\Order\\Order');
            $orderInfo = $order->getOrderInfo($orderNo);
            $this->setData('orderName', $orderInfo[0]['orderName'] ?? '');
            
            // 주문 상태 확인 (결제완료 상태인지 확인)
            $isPaymentCompleted = $presentConfirm->isPaymentCompleted($orderNo, $orderGoodsNo);
            $this->setData('isPaymentCompleted', $isPaymentCompleted);
            
            // 주문 상품 정보 가져오기
            $orderGoods = $order->getOrderGoodsData($orderNo, null, null, null, null, false);
            // 주문 상품 정보 검증
            Validator::notEmpty()
                ->setTemplate('유효하지 않은 주문 정보입니다.')
                ->assert($orderGoods);

            // 주문 상품 정보 포맷팅 정보
            $orderGoodsData = $presentConfirm->formatOrderGoodsInfo($orderGoods);
            $this->setData('orderGoods', $orderGoodsData ?? []);
            
            // 송장 정보 전달 (배송조회용)
            $this->setData('invoiceCompanySno', $orderGoods[0]['invoiceCompanySno'] ?? '');
            $this->setData('invoiceNo', $orderGoods[0]['invoiceNo'] ?? '');
            
            // 수령자 정보 가져오기
            $presentInfo = $presentConfirm->getPresentInfoByOrderGoodsNo($orderGoodsNo);
            $presentInfoData = $presentConfirm->formatPresentInfo($presentInfo);
            $this->setData($presentInfoData);

            // 배송지 변경 가능 여부 확인
            $acceptFl = $presentInfo['acceptFl'] ?? '';
            $this->setData('acceptFl', $acceptFl);

            $expireDt = $presentInfo['expireDt'] ?? '';
            $isDeliveryChangeable = $presentConfirm->isDeliveryChangeable($acceptFl, $expireDt, $orderNo, $orderGoodsNo);
            $this->setData('isDeliveryChangeable', $isDeliveryChangeable);
            $this->setData('isDeliveryChangeableJs', json_encode($isDeliveryChangeable));
            
            // 배송지 입력 기한이 지났는지 확인
            $isDeliveryInputExpired = $presentConfirm->isDeliveryInputExpired($expireDt);
            $this->setData('isDeliveryInputExpired', $isDeliveryInputExpired);

            // 사용자 반품/교환/환불 신청 사용여부
            $isUserHandleEnabled = $presentConfirm->isUserHandleEnabled();
            $this->setData('isUserHandleEnabled', $isUserHandleEnabled);

            // 상점명 설정
            $this->setData('shopName', \Globals::get('gMall.mallNm'));
            
            // 확인토큰을 템플릿에 전달
            $this->setData('confirmToken', $confirmToken);
            
            // 배송 메모 리스트 가져오기
            $presentOrderAdmin = \App::getInstance(PresentOrderAdmin::class);
            $deliveryMemoList = $presentOrderAdmin->getDeliveryMemoList();
            $this->setData('deliveryMemoList', $deliveryMemoList ?? []);
            
            // 로그인 여부 확인 및 로그인 URL 생성
            $isLogin = MemberUtil::isLogin();
            $this->setData('isLogin', $isLogin);
            
            // 이미 등록된 배송 정보가 있는지 확인
            $receiverInfo = $presentConfirm->getReceiverInfoByOrderInfo($orderNo, $orderGoodsNo);
            
            if (!empty($receiverInfo) && $acceptFl === PresentConfirm::PRESENT_ACCEPT_COMPLETED) {
                // 이미 등록된 배송 정보가 있으면 해당 정보 사용 (마스킹 처리 포함)
                $deliveryInfo = $presentConfirm->formatReceiverInfoToDeliveryInfo($receiverInfo, $acceptFl);
                $this->setData($deliveryInfo);
            } else {
                // 등록된 배송 정보가 없으면 로그인한 회원의 기본 배송지 정보 사용
                if (!$isLogin) {
                    // 현재 URL을 returnUrl로 설정
                    $request = \App::getInstance('request');
                    $currentUrl = $request->getReturnUrl();
                    $loginUrl = '../member/login.php?returnUrl=' . urlencode($currentUrl);
                    $this->setData('loginUrl', $loginUrl);
                } else {
                    // 로그인한 회원의 배송지 정보 가져오기 (마스킹 처리 포함)
                    $memberInfo = MemberUtil::getMemberBySession();
                    $defaultShipping = $order->getDefaultShippingAddress();
                    
                    // 배송 정보 포맷팅
                    $deliveryInfo = $presentConfirm->formatDeliveryInfo($memberInfo, $defaultShipping, $acceptFl);
                    $this->setData($deliveryInfo);
                }
            }

            // 선물하기 이용약관정보 가져오기
            $presentAgreementContent = $presentConfirm->getPresentAgreementContent();
            $this->setData('presentAgreementContent', $presentAgreementContent);
        } catch (ValidationException $e) {
            Logger::channel('presentConfirm')->warning(__CLASS__ . ' 선물수락/배송지입력 검증 실패 : ', [
                'error' => $e->getMainMessage(),
                'confirmToken' => $confirmToken ?? '',
                'orderInfo' => $orderInfo ?? [],
                'presentInfo' => $presentInfo ?? []
            ]);
            
            throw new AlertRedirectException($e->getMainMessage(), null, null, '../present/present_expired.php', 'self');
            
        } catch (\Exception $e) {
            Logger::channel('presentConfirm')->warning(__CLASS__ . ' 선물수락/배송지입력 예외 발생 : ', [
                'error' => $e->getMessage(),
                'confirmToken' => $confirmToken ?? '',
                'orderInfo' => $orderInfo ?? [],
                'presentInfo' => $presentInfo ?? []
            ]);
            
            throw new AlertRedirectException('잘못된 접근입니다.', null, null, '../present/present_expired.php', 'self');   
        }
    }
}
