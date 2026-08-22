<?php
/*
 * Copyright (C) 2025 NHN COMMERCE. - All Rights Reserved
 *
 * Unauthorized copying or redistribution of this file in source and binary forms via any medium
 * is strictly prohibited.
 */

namespace Bundle\Controller\Front\Goods;

use Component\RegularDelivery\RegularGoods\RegularGift;
use DTO\RegularDelivery\RegularOrder\RegularOrderSelectedGiftDTO;
use Exception;
use Request;

class RegularGiftPsController extends \Controller\Front\Controller
{
    public function index()
    {
        try {
            $postValue = Request::request()->toArray();
            $result = [];

            // 선택한 정기결제(배송) 사은품 조회
            $regularGift = \App::getInstance(RegularGift::class);
            $regularOrderSelectedGiftDTO = new RegularOrderSelectedGiftDTO($postValue);
            $result['giftData'] = $regularGift->getSelectedRegularGiftData($regularOrderSelectedGiftDTO);
            $message = '선택한 정기배송 상품의 사은품 조회 완료';

            $this->json([
                'result' => 'success',
                'message' => $message,
                'data' => $result
            ]);
        } catch (Exception $e) {
            $message = ($e->getMessage() === '') ? 'RegularGift selected failed' : $e->getMessage();
            \Logger::channel('regularGoods')->error(__CLASS__ . ' ' . $message, $postValue);

            $this->json([
                'result' => 'error',
                'message' => $message,
                'data' => []
            ]);
        }
    }
}
