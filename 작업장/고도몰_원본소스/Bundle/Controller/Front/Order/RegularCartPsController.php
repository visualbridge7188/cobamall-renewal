<?php
/*
 * Copyright (C) 2025 NHN COMMERCE. - All Rights Reserved
 *
 * Unauthorized copying or redistribution of this file in source and binary forms via any medium
 * is strictly prohibited.
 */

namespace Bundle\Controller\Front\Order;

use Component\Cart\RegularOrderCart;
use Component\RegularDelivery\Exception\RegularGoodsDuplicateOptionException;
use Exception;
use Framework\Debug\Exception\AlertBackException;
use Framework\Debug\Exception\AlertOnlyException;
use Framework\Debug\Exception\AlertRedirectException;
use Framework\Debug\Exception\AlertReloadException;
use Framework\Utility\StringUtils;
use Request;

/**
 * 정기결제 장바구니 처리 컨트롤러
 * cart_regular_order.html에서 호출하는 처리 전용
 *
 * @package Bundle\Controller\Front\Order
 */
class RegularCartPsController extends \Controller\Front\Controller
{
    /**
     * @inheritdoc
     */
    public function index()
    {
        $postValue = Request::request()->toArray();

        /** @var RegularOrderCart $regularOrderCart */
        $regularOrderCart = \App::getInstance(RegularOrderCart::class);

        // 정기결제 사용 여부 확인
        if (!$regularOrderCart->getRegularDeliveryUseFl()) {
            $this->json([
                'error' => 1,
                'message' => __('정기결제를 사용할 수 없습니다.'),
            ]);
            exit;
        }

        $request = \App::getInstance('request');
        $session = \App::getInstance('session');
        
        // xss 취약점 보안
        if (gd_isset($postValue['optionText'])) {
            $postValue['optionText'] = StringUtils::xssArrayClean($postValue['optionText']);
        }
        if (gd_isset($postValue['optionTextInput'])) {
            $postValue['optionTextInput'] = StringUtils::xssArrayClean($postValue['optionTextInput']);
        }

        // 각 모드별 처리
        switch (Request::request()->get('mode')) {
            // 정기결제 장바구니 추가 (상품상세에서 호출)
            case 'cartIn':
                try {
                    \Cookie::del("NPAY_BUY_UNIQUE_KEY");
                    //관련상품 관련 세션 삭제
                    if($session->get('related_goods_order') == 'y') {
                        $session->del('related_goods_order');
                    }
                    $regularOrderCart->setDeleteDirectCartCont();
                    // 메인 상품 진열 통계 처리
                    if (empty($postValue['mainSno']) === false && $postValue['mainSno'] > 0) {
                        $goods = \App::load('\\Component\\Goods\\Goods');
                        $getData = $goods->getDisplayThemeInfo($postValue['mainSno']);
                        $postValue['linkMainTheme'] = htmlentities($getData['sno'] . STR_DIVISION . $getData['themeNm'] . STR_DIVISION . $getData['mobileFl']);
                    } else {
                        $referer = $request->getReferer();
                        $refererResult = [];
                        parse_str($referer, $refererResult);
                        gd_isset($mtn);
                        if (empty($mtn) === false) {
                            $postValue['linkMainTheme'] = $mtn;
                        }
                    }

                    // 장바구니에 추가
                    $regularOrderCart->saveInfoCart($postValue);

                    if ($request->isAjax()) {
                        $this->json([
                            'error' => 0,
                            'message' => __('성공'),
                        ]);
                    } else {
                        // 바로구매일 경우 정기배송 주문서로 이동
                        if (gd_isset($postValue['cartMode']) == 'd') {
                            $returnUrl = './regular_order.php';
                        } else {
                            $returnUrl = './cart.php';
                        }
                        $this->redirect($returnUrl, null, 'parent');
                    }
                } catch (RegularGoodsDuplicateOptionException $e) {
                    $this->json([
                        'error' => 1,
                        'message' => $e->getMessage(),
                        'type' => 'regularGoods'
                    ]);
                } catch (Exception $e) {
                    if (Request::isAjax()) {
                        $this->json([
                            'error' => 1,
                            'message' => $e->getMessage(),
                        ]);
                    } else {
                        throw new AlertBackException($e->getMessage());
                    }
                }
                break;

            // 정기결제 장바구니 선택 상품의 총 결제금액 계산
            case 'cartSelectCalculation':
                try {
                    $setData = $regularOrderCart->calculateSelectedCartPrice($postValue['cartSno'] ?? []);
                    $this->json($setData);
                } catch (Exception $e) {
                    $this->json([
                        'error' => 1,
                        'message' => $e->getMessage(),
                    ]);
                }
                break;

            // 정기결제 장바구니 선택 상품 주문
            case 'regularOrderSelect':
                try {
                    $cartIdx = $regularOrderCart->setRegularOrderSelect($postValue['cartSno']);
                    $returnUrl = '../order/regular_order.php?cartIdx=' . $cartIdx;
                    throw new AlertRedirectException(null, null, null, $returnUrl, 'parent');
                } catch (AlertRedirectException $e) {
                    throw $e;
                } catch (Exception $e) {
                    throw new AlertBackException($e->getMessage());
                }
                break;

            // 정기결제 장바구니 상품 삭제
            case 'cartDelete':
                try {
                    \Cookie::del("NPAY_BUY_UNIQUE_KEY");
                    $regularOrderCart->setCartDelete($postValue['cartSno']);
                    throw new AlertReloadException(null, null, null, 'parent');
                } catch (AlertReloadException $e) {
                    throw $e;
                } catch (Exception $e) {
                    throw new AlertOnlyException($e->getMessage());
                }
                break;
            // 정기결제 장바구니 옵션수정
            case 'cartUpdate':
                try {
                    $regularOrderCart->updateInfoCart($postValue);
                } catch (Exception $e) {
                    if (Request::isAjax()) {
                        $this->json([
                            'error' => 1,
                            'message' => $e->getMessage(),
                        ]);
                    } else {
                        throw new AlertBackException($e->getMessage());
                    }
                }
                break;
            // 장바구니 정기배송 상품의 배송주기/종료회차 변경 처리
            case 'change_regular_delivery_cycle':
                try {
                    $regularOrderCart->updateRegularDeliveryCycle($postValue);
                    $this->json([
                        'success' => true,
                        'message' => '정기배송 상품의 배송주기가 변경되었습니다.',
                    ]);
                } catch (Exception $e) {
                    $this->json([
                        'success' => false,
                        'message' => $e->getMessage(),
                    ]);
                }
                break;
            // 장바구니 상품 수량 변경
            case 'cartCnt':
                try {
                    $postValue['cart']['useBundleGoods'] = $postValue['useBundleGoods'];
                    $regularOrderCart->setCartCnt($postValue['cart']);
                    throw new AlertReloadException(null, null, null, 'parent');
                } catch (AlertReloadException $e) {
                    throw $e;
                } catch (Exception $e) {
                    throw new AlertOnlyException($e->getMessage());
                }
                break;
            default:
                $this->json([
                    'error' => 1,
                    'message' => __('잘못된 요청입니다.'),
                ]);
                break;
        }

        exit();
    }
}

