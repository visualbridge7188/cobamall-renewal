<?php
/*
 * Copyright (C) 2025 NHN COMMERCE. - All Rights Reserved
 *
 * Unauthorized copying or redistribution of this file in source and binary forms via any medium
 * is strictly prohibited.
 */

namespace Bundle\Controller\Mobile\Order;

use Controller\Mobile\Controller;
use Component\Mall\Mall;
use Component\Cart\RegularOrderCart;
use Framework\Debug\Exception\AlertRedirectException;
use Request;

class RegularOrderController extends Controller
{
    /**
     * @throws AlertRedirectException
     * @throws \Throwable
     */
    public function index()
    {
        $memNo = \Session::get('member.memNo');

        try {
            // 신청서 관련 세션 데이터
            $sessionOrderData = \Session::get('regularOrder.data.' . $memNo);
            if ($sessionOrderData !== null) {
                $decryptedSessionData = \Encryptor::decrypt($sessionOrderData);
                if (is_string($decryptedSessionData)) {
                    $regularOrderSession = json_decode($decryptedSessionData, true);
                    $this->setData('regularOrderSession', $regularOrderSession);
                }
            }
            // 카드 관련 세션 데이터
            $sessionCardNo = \Session::get('regularOrder.cardData.' . $memNo);
            if ($sessionCardNo !== null) {
                $this->setData('regularOrderCardSession', $sessionCardNo);
            }

            if (gd_is_login() === false) {
                throw new AlertRedirectException(__('비회원은 정기배송 신청을 할 수 없습니다.'), null, null, '../main/index.php');
            }

             /** @var RegularOrderCart $regularOrderCart */
            $regularOrderCart = \App::getInstance(RegularOrderCart::class);

            // 선택된 상품만 신청 시
            $cartIdx = null;
            if (Request::get()->has('cartIdx')) {
                $cartIdx = $regularOrderCart->getRegularOrderSelect(Request::get()->get('cartIdx'));
                $this->setData('cartIdx', $cartIdx);
            }

            $cartInfo = $regularOrderCart->getCartGoodsData($cartIdx);
            $this->setData('cartInfo', $cartInfo);

            // 신청 불가한 경우 진행 중지
            if (!$regularOrderCart->orderPossible) {
                if(trim($regularOrderCart->orderPossibleMessage) !== ''){
                    throw new AlertRedirectException(__($regularOrderCart->orderPossibleMessage), null, null, '../order/cart.php');
                } else {
                    throw new AlertRedirectException(__('구매 불가 상품이 포함되어 있으니 장바구니에서 확인 후 다시 주문해주세요.'), null, null, '../order/cart.php');
                }
            }
            
            if (empty($cartInfo)) {
                throw new AlertRedirectException(__('장바구니에 상품이 없습니다. 장바구니에 상품을 담으신 후 신청해주세요.'), null, null, '../main/index.php');
            }

            // 카트 정보
            $this->setData('cartCnt', $regularOrderCart->cartCnt); // 장바구니 수량
            $this->setData('cartScmInfo', $regularOrderCart->cartScmInfo); // 장바구니 SCM 정보
            $this->setData('cartScmCnt', $regularOrderCart->cartScmCnt); // 장바구니 SCM 수량
            $this->setData('setDeliveryInfo', $regularOrderCart->setDeliveryInfo); // 배송비조건별 배송 정보
            $this->setData('totalGoodsPrice', $regularOrderCart->totalGoodsPrice); // 상품 총 가격
            $this->setData('totalDeliveryCharge', $regularOrderCart->totalDeliveryCharge); // 상품 배송정책별 총 배송 금액
            $this->setData('totalSettlePrice', $regularOrderCart->totalSettlePrice); // 총 결제 금액 (예정)
            $this->setData('totalMileage', $regularOrderCart->totalMileage); // 총 적립 마일리지 (예정)

            // 배송 관련
            $deliveryDueDates = $regularOrderCart->getDeliveryDueDates($cartInfo);
            $this->setData('deliveryDueDates', $deliveryDueDates); // 배송 예정일

            // 정기상품 가격
            $regularGoodsPriceInfo = $regularOrderCart->getRegularGoodsPrice($cartInfo);
            $this->setData('regularGoodsPrice', $regularGoodsPriceInfo['regularGoodsPriceList']);
            $this->setData('regularGoodsPriceOption', $regularGoodsPriceInfo['regularGoodsPriceOptionList']);
            $this->setData('regularGoodsPriceOptionText', $regularGoodsPriceInfo['regularGoodsPriceOptionTextList']);
            $this->setData('regularGoodsPriceOptionTextTotal', $regularGoodsPriceInfo['regularGoodsPriceOptionTextTotalList']);
            $this->setData('regularAddGoodsPrice', $regularGoodsPriceInfo['regularAddGoodsPriceList']);
            $this->setData('regularTotalPrice', $regularGoodsPriceInfo['regularTotalPrice']);
            $this->setData('regularGoodsDiscountUseFl', $regularGoodsPriceInfo['regularGoodsDiscountUseFl']);

            // 사은품 정보
            $regularOrderView = \App::getInstance(\Component\RegularDelivery\RegularOrder\RegularOrderView::class);
            $giftConf = gd_policy('goods.gift');
            $giftInfo = $regularOrderView->getGiftInfo($cartInfo);
            $this->setData('giftConf', $giftConf);
            $this->setData('giftInfo', $giftInfo);

            // 쇼핑몰 정보
            $mall = new Mall();
            $serviceInfo = $mall->getServiceInfo();
            $this->setData('serviceInfo', $serviceInfo);

            // 공급사
            $scmInfo = $regularOrderView->regularOrderScmInfo($cartInfo);
            $this->setData('hasScmGoods', $scmInfo['hasScmGoods']);

            // 이용약관
            $agreement = $regularOrderView->getAgreement($cartInfo);
            $this->setData('privateAgreementInfo', $agreement['privateAgreementInfo']);
            $this->setData('autoPgAgreementInfo', $agreement['autoPgAgreementInfo']);
            if ($scmInfo['hasScmGoods']) {
                $this->setData('privateProvider', $agreement['privateProvider']);
            }

            // 메일도메인
            $emailDomain = gd_array_change_key_value(gd_code('01004'));
            $emailDomain = array_merge(['self' => __('직접입력')], $emailDomain);
            $this->setData('emailDomain', $emailDomain); // 메일주소 리스팅

            // 배송지 정보
            $regularOrderShippingAddress = \App::getInstance(\Component\RegularDelivery\RegularOrder\RegularOrderShippingAddress::class);
            if (isset($regularOrderSession)) {
                // 신청서 정보를 담고 있는 세션이 있다면 세션에 담긴 배송지 번호로 배송지 조회
                $defaultShippingInfo = $regularOrderShippingAddress->getShippingAddressInfo($regularOrderSession['shippingSno'], $memNo);
            } else {
                $defaultShippingInfo = $regularOrderShippingAddress->findDefaultShippingInfo($memNo);
            }
            $shippingList = $regularOrderShippingAddress->getRegularOrderShippingAddress($memNo, 1, 999);
            $this->setData('shippingInfo', $defaultShippingInfo);
            $this->setData('shippingList', $shippingList['list']);

            // pg 정보
            $pgConf = gd_policy('autoPg.pgName');
            $this->setData('pgName', $pgConf['pgName']);

            // 카드 정보
            $paymentCard = \App::getInstance(\Component\RegularDelivery\RegularOrder\RegularOrderPayment::class);
            $cardList = $paymentCard->getPaymentCardList($memNo);
            $this->setData('cardList', $cardList);
            $this->setData('maxCardCount', $paymentCard::MAX_CARD_COUNT); // 카드 등록 최대 개수

            // 상품 옵션가 표시설정 config 불러오기
            $optionPriceConf = gd_policy('goods.display');
            $this->setData('optionPriceFl', $optionPriceConf['optionPriceFl'] ?? 'y');

            // 회원 세션 데이터
            $memberName = \Session::get('member.memNm');
            $this->setData('memberName', $memberName);

            // 기타
            $this->setData('reAgreeConfirmFl', gd_policy('order.basic')['reagreeConfirmFl']);

            $gPageName = __("정기배송 신청서 작성");
            $this->setData('gPageName', $gPageName);
        } catch (\Throwable $e) {
            \Logger::channel('regularOrder')->warning('정기결제 신청서 페이지 조회 실패', [$e->getMessage(), $e->getTrace()]);
            throw $e;
        } finally {
            // 한번 조회 한 이후에는 세션에 저장된 신청서 데이터를 삭제.
            \Session::del('regularOrder.data.' . $memNo);
            \Session::del('regularOrder.cardData.' . $memNo);
        }
    }
}
