<?php
/*
 * Copyright (C) 2025 NHN COMMERCE. - All Rights Reserved
 *
 * Unauthorized copying or redistribution of this file in source and binary forms via any medium
 * is strictly prohibited.
 */

namespace Bundle\Controller\Front\Order;

use Component\Mall\Mall;
use Component\Cart\RegularOrderCart;
use Controller\Front\Controller;
use Framework\Debug\Exception\AlertRedirectException;
use Request;

class RegularOrderController extends Controller
{
    /**
     * @throws AlertRedirectException
     */
    public function index()
    {
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
        \Logger::channel('regularOrder')->info('정기배송 신청서 카트에서 넘어온 정보 : ', $cartInfo);
        $this->setData('cartInfo', $cartInfo);

        // 신청 불가한 경우 진행 중지
        if (!$regularOrderCart->orderPossible) {
            if (trim($regularOrderCart->orderPossibleMessage) !== '') {
                throw new AlertRedirectException(__($regularOrderCart->orderPossibleMessage), null, null, '../order/cart.php');
            } else {
                throw new AlertRedirectException(__('구매 불가 상품이 포함되어 있으니 장바구니에서 확인 후 다시 주문해주세요.'), null, null, '../order/cart.php');
            }
        }

        if (empty($cartInfo)) {
            throw new AlertRedirectException(__('장바구니에 상품이 없습니다. 장바구니에 상품을 담으신 후 신청해주세요.'), null, null, '../main/index.php');
        }

        try {
            // 카트 정보
            $this->setData('cartCnt', $regularOrderCart->cartCnt); // 장바구니 수량
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
            $this->setData('regularGoodsPrice', $regularGoodsPriceInfo['regularGoodsPriceList']); // 주문 상품 별 판매 가격
            $this->setData('regularGoodsPriceOption', $regularGoodsPriceInfo['regularGoodsPriceOptionList']); // 주문 상품 별 옵션 가격
            $this->setData('regularGoodsPriceOptionText', $regularGoodsPriceInfo['regularGoodsPriceOptionTextList']); // 주문 상품 별 옵션 텍스트 가격
            $this->setData('regularGoodsPriceOptionTextTotal', $regularGoodsPriceInfo['regularGoodsPriceOptionTextTotalList']); // 주문 상품 별 옵션 텍스트 총 가격
            $this->setData('regularAddGoodsPrice', $regularGoodsPriceInfo['regularAddGoodsPriceList']); // 주문 상품 별 추가 상품 가격
            $this->setData('regularTotalPriceList', $regularGoodsPriceInfo['regularTotalPriceList']); // 주문 상품 별 총 가격
            $this->setData('regularTotalPrice', $regularGoodsPriceInfo['regularTotalPrice']); // 주문 상품 총 가격
            $this->setData('regularGoodsDiscountUseFl', $regularGoodsPriceInfo['regularGoodsDiscountUseFl']);

            $regularOrderView = \App::getInstance(\Component\RegularDelivery\RegularOrder\RegularOrderView::class);
            // 사은품 정보
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
            $memNo = \Session::get('member.memNo');
            $defaultShippingInfo = $regularOrderShippingAddress->findDefaultShippingInfo($memNo);
            $this->setData('shippingInfo', $defaultShippingInfo);

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
        } catch (\Throwable $e) {
            throw new AlertRedirectException($e->getMessage(), null, null, '../main/index.php');
        }
    }
}
