<?php
/*
 * Copyright (C) 2025 NHN COMMERCE. - All Rights Reserved
 *
 * Unauthorized copying or redistribution of this file in source and binary forms via any medium
 * is strictly prohibited.
 */

namespace Bundle\Controller\Front\Goods;

use Exception;
use Util\Order\RegularOrderUtil;
use Request;
use Component\RegularDelivery\RegularGoods\RegularGoods;
use DTO\RegularDelivery\RegularGoods\RegularGoodsValidateDTO;
use Origin\Enum\RegularDelivery\RegularGoods\RegularGoodsAttribute;
use Origin\Exception\RegularDelivery\RegularGoods\RegularGoodsValidateException;

class RegularGoodsPsController extends \Controller\Front\Controller
{
    const EXCEPTION_MESSAGES = [
        RegularGoodsAttribute::ERROR_REGULAR_GOODS_DELIVERY_DATA_CHANGE => "선택하신 상품의 정기배송 신청 가능 조건이 변경되었습니다.\n배송주기를 다시 선택하여 정기배송을 신청해주세요.",
        RegularGoodsAttribute::ERROR_REGULAR_GOODS_DELETE => "해당 상품은 현재 구매가 불가한 상품입니다."
    ];

    public function index()
    {
        try {
            $postValue = Request::request()->toArray();
            $message = '';
            $result = [];
            switch ($postValue['mode']) {
                case 'calculateOrderDate':
                    $service = \App::getInstance(RegularGoods::class);
                    $firstDeliveryDate = $service->calculateOrderDate($postValue);

                    $result['firstDeliveryDate'] = $firstDeliveryDate[0];
                    $message = '첫 배송예정일 계산 완료';
                    break;

                case 'calculateTotalRegularPrice':
                    $regularPrice = $postValue['regularPrice'];
                    $optionPrice = $postValue['optionPrice'];
                    $discountUseFl = $postValue['discountUseFl'];
                    $discountType = $postValue['discountType'];
                    $discountRate = $postValue['discountRate'];

                    // 정기상품 옵션가 계산
                    $regularOptionPrice = RegularOrderUtil::calculateRegularOptionPrice($optionPrice, $discountUseFl, $discountType, $discountRate);

                    $result['regularTotalPrice'] = $regularPrice + $regularOptionPrice;
                    $message = '정기상품 총 합계 금액 계산 완료';
                    break;

                case 'filterRegularGift':
                    $regularGoodsNo = $postValue['regularGoodsNo'];
                    $goodsCnt = (int) $postValue['goodsCnt'];
                    $addGoodsCnt = (int) $postValue['addGoodsCnt'];

                    // 정기배송 상품의 사은품 조회
                    $service = \App::getInstance(RegularGoods::class);
                    $result['giftData'] = $service->getFilteredRegularGiftData($regularGoodsNo, $goodsCnt, $addGoodsCnt, 65);
                    $message = '정기배송 상품의 사은품 조회 완료';
                    break;

                case 'validateRegularDeliveryOption':
                    $service = \App::getInstance(RegularGoods::class);
                    $regularGoodsValidateDTO = new RegularGoodsValidateDTO($postValue);
                    $service->validateGoodsStatus($regularGoodsValidateDTO);
                    $message = '정기배송 상품의 배송주기 및 종료회차 유효성 검사 완료';
                    break;

                default:
                    throw new Exception('RegularGoods missing parameter');
            }

            $this->json([
                'status' => 'success',
                'message' => $message,
                'data' => $result
            ]);
        } catch (RegularGoodsValidateException $e) {
            $this->handleRegularGoodsException($e, $postValue);
        } catch (Exception $e) {
            $message = ($e->getMessage() === '') ? 'RegularGoods calculation failed' : $e->getMessage();
            \Logger::channel('regularGoods')->warning(__CLASS__ . ' ' . $message, $postValue);

            $this->json([
                'status' => 'error',
                'message' => $message,
                'data' => []
            ]);
        }
    }

    private function handleRegularGoodsException($e, $postValue)
    {
        \Logger::channel('regularGoods')->warning(__CLASS__ . ' ' . $e->getMessage(), $postValue);

        $errorCode = $e->getCode();
        $message = self::EXCEPTION_MESSAGES[$errorCode] ?? $e->getMessage();

        $this->json([
            'status' => 'error',
            'code' => $errorCode,
            'message' => $message
        ]);
    }
}
