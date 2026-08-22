<?php
/*
 * Copyright (C) 2025 NHN COMMERCE. - All Rights Reserved
 *
 * Unauthorized copying or redistribution of this file in source and binary forms via any medium
 * is strictly prohibited.
 */

namespace Bundle\Controller\Front\Order;

use Component\Cart\Exception\ValidationException;
use Component\Cart\Cart;
use Component\Cart\ReorderCart;
use Component\Validator\Validator;
use Exception;
use Framework\Http\Response;
use Framework\Utility\StringUtils;
use Origin\Service\WebHook\CartWebHookService;
use Request;

/**
 * 재구매 처리 페이지
 */
class ReorderPsController extends \Controller\Front\Controller
{
    protected const CART_MODE_CART = '';
    protected const CART_MODE_DIRECT = 'd';

    public function index()
    {
        $cart = \App::load(Cart::class);
        $reorderCart = \App::getInstance(ReorderCart::class);
        $session = \App::getInstance('session');
        $postValue = Request::post()->toArray();

        // 모드별 처리
        switch ($postValue['mode']) {
            case 'cartInMultiForReorder':
                try {
                    \Logger::channel('reorder')->info(__METHOD__ . ' cartInMultiForReorder ', [$postValue]);
                    $postValue = StringUtils::xssArrayClean($postValue);

                    // 바로구매 관련 쿠키 세션 제거
                    \Cookie::del("NPAY_BUY_UNIQUE_KEY");
                    if($session->get('related_goods_order') == 'y') {
                        $session->del('related_goods_order');
                    }
                    $cart->setDeleteDirectCartCont();

                    // 재구매를 위한 장바구니 데이터 유효성 검사 및 변환
                    $cartMode = in_array($postValue['data']['cartMode'], [self::CART_MODE_CART, self::CART_MODE_DIRECT])
                        ? $postValue['data']['cartMode']
                        : self::CART_MODE_CART; // '' = 장바구니, 'd' = 바로구매
                    $reorderGoodsList = $postValue['data']['reorderGoodsList'];
                    $this->validateReorderGoodsList($reorderGoodsList);
                    $reorderCartDataList = $reorderCart->convertToReorderCartData($reorderGoodsList, $cartMode);
                    $availableReorderCartDataList = $reorderCart->filterAvailableCartData($reorderCartDataList);

                    // 바로구매 시 구매 제한 여부 확인
                    $memNo = (int) \Session::get('member.memNo');
                    $isPurchasable = $cartMode !== self::CART_MODE_DIRECT
                        || $reorderCart->isPurchasable($availableReorderCartDataList, $memNo);

                    // 장바구니 담기
                    $cartSnoList = $cart->saveMultipleInfoCart($availableReorderCartDataList);
                    // 바로구매 모드에서 구매 불가 상품이 있으면 일반 장바구니로 변환
                    if ($cartMode == self::CART_MODE_DIRECT && !empty($cartSnoList) && (count($reorderCartDataList) !== count($availableReorderCartDataList) || !$isPurchasable)) {
                        $reorderCart->moveDirectToNormalCart($cartSnoList);
                    }

                    $flatCartSnos = [];
                    foreach (($cartSnoList ?? []) as $snoArray) {
                        if (is_array($snoArray)) {
                            $flatCartSnos = array_merge($flatCartSnos, $snoArray);
                        }
                    }
                    $cartWebHookService = \App::getInstance(CartWebHookService::class);
                    $cartWebHookService->sendCartUpdatedWebHook($flatCartSnos, 'CREATED');

                    $result = $this->handleReorderResult($cartMode, $reorderCartDataList, $availableReorderCartDataList, $isPurchasable);

                    $this->json([
                        'success' => $result['success'],
                        'message' => $result['message'],
                        'redirect' => $result['redirect'],
                    ]);
                } catch (ValidationException $e) {
                    \Logger::channel('reorder')->warning($e->getMessage(), [__METHOD__, $postValue]);
                    $this->json([
                        'success' => false,
                        'message' => $e->getMessage(),
                    ], Response::HTTP_BAD_REQUEST);
                } catch (Exception $e) {
                    \Logger::channel('reorder')->warning($e->getMessage(), [__METHOD__, $postValue]);
                    $this->json([
                        'success' => false,
                        'message' => $e->getMessage(),
                    ], Response::HTTP_INTERNAL_SERVER_ERROR);
                }
                break;

        }
        exit();
    }

    /**
     * 재구매 결과 처리
     *
     * @param string $cartMode 장바구니 모드
     * @param array $cartDataList 장바구니 데이터 목록
     * @param array $availableCartDataList 장바구니에 담을 수 있는 데이터 목록
     * @param bool $isPurchasable 구매수량 제한 통과 여부
     * @return array 재구매 결과
     */
    protected function handleReorderResult(string $cartMode, array $cartDataList, array $availableCartDataList, bool $isPurchasable = true): array
    {
        $success = true;
        $message = '';
        $redirect = '';

        $existsUnavailableItems = (count($cartDataList) !== count($availableCartDataList));
        $isAllItemsUnavailable = empty($availableCartDataList);

        if ($cartMode === self::CART_MODE_DIRECT) {
            if ($isAllItemsUnavailable || $existsUnavailableItems || !$isPurchasable) {
                $message = '구매 불가 상품이 포함되어 있으니 장바구니에서 확인 후 다시 주문해 주세요.';
                $redirect = '../order/cart.php';
            } else {
                $redirect = '../order/order.php';
            }
        } else {
            if ($isAllItemsUnavailable) {
                $success = false;
                $message = '선택하신 상품이 현재 구매 불가하여 장바구니에 담을 수 없습니다.';
            } elseif ($existsUnavailableItems) {
                $message = "구매 가능한 상품만 장바구니에 담겼습니다.\n장바구니로 이동하시겠습니까?";
                $redirect = '../order/cart.php';
            } else {
                $message = "선택하신 상품이 장바구니에 담겼습니다.\n장바구니로 이동하시겠습니까?";
                $redirect = '../order/cart.php';
            }
        }

        return [
            'success' => $success,
            'message' => $message,
            'redirect' => $redirect,
        ];
    }

    /**
     * 재구매 상품 목록 유효성 검사
     *
     * @param array $orderGoodsList 재구매 상품 목록
     * @throws ValidationException
     * @return void
     */
    protected function validateReorderGoodsList(array $orderGoodsList): void
    {
        if (empty($orderGoodsList)) {
            throw new ValidationException('선택하신 상품이 없습니다.');
        }
        if (count($orderGoodsList) > ReorderCart::MAX_REORDER_GOODS_COUNT) {
            throw new ValidationException('선택하신 주문의 상품 수가 ' . ReorderCart::MAX_REORDER_GOODS_COUNT . '개를 초과하여 장바구니에 담을 수 없습니다.');
        }

        // 개별 유효성 검사
        foreach ($orderGoodsList as $orderGoods) {
            //상품 번호 체크
            if (!Validator::required($orderGoods['goodsNo'] ?? null)) {
                throw new ValidationException('선택한 상품의 상품번호를 확인 할 수 없어 장바구니에 담을 수 없습니다.');
            }

            // 옵션 번호 체크
            if (!Validator::required(value: $orderGoods['optionSno'] ?? null)) {
                throw new ValidationException('선택한 상품의 옵션 번호를 확인할 수 없어 장바구니에 담을 수 없습니다.');
            }

            // 상품 수량 체크
            if (!Validator::number($orderGoods['goodsCnt'] ?? null, 1, null, true)) {
                throw new ValidationException('선택한 상품의 수량이 올바르지 않아 장바구니에 담을 수 없습니다.');
            }
        }
    }
}
