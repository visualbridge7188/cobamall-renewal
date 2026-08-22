<?php
/*
 * Copyright (C) 2025 NHN COMMERCE. - All Rights Reserved
 *
 * Unauthorized copying or redistribution of this file in source and binary forms via any medium
 * is strictly prohibited.
 */

namespace Bundle\Controller\Mobile\Order;

use Component\RegularDelivery\Exception\RegularOrderGoodsChangeException;
use Component\RegularDelivery\Exception\RegularOrderGoodsDiscountPriceException;
use Component\RegularDelivery\RegularOrder\RegularOrderApplicationCreate;
use Controller\Mobile\Controller;
use DTO\RegularDelivery\RegularOrder\RegularOrderDTO;
use DTO\RegularDelivery\RegularOrder\RegularOrderApplierDTO;
use DTO\RegularDelivery\RegularOrder\RegularOrderGoodsDTO;
use Framework\Debug\Exception\AlertBackException;
use Framework\Debug\Exception\AlertOnlyException;
use Framework\Debug\Exception\AlertRedirectException;
use Request;

/**
 * 신청서 생성 ps controller
 * 주문서 아님!
 */
class RegularOrderPsController extends Controller
{
    public function index()
    {
        try {
            $orderData = Request::post()->toArray();
            \Logger::channel('regularOrder')->info('Regular Order create post value : ', $orderData);

            // 1. 신청 정보 DTO
            $regularOrderDTO = new RegularOrderDTO($orderData);

            // 2. 신청 정보 저장 로직
            $regularOrderCreate = \App::getInstance(RegularOrderApplicationCreate::class);
            $applyGroupNo = $regularOrderCreate->create($regularOrderDTO);

            // 3. 완료 후 관련 카트 데이터 삭제
            $regularOrderCreate->deleteCart($regularOrderDTO);

            throw new AlertRedirectException(null, null, null, '../order/regular_order_end.php?applyGroupNo=' . $applyGroupNo, 'parent');
        } catch (AlertRedirectException $e) {
            throw $e;
        } catch (RegularOrderGoodsChangeException $e) {
            \Logger::channel('regularOrder')->warning('Regular Order create error : ', $e->getMessage());
            throw new AlertRedirectException('정기배송 신청 불가 상품이 포함되어 있으니 장바구니에서 확인 후 다시 주문해 주세요.', null, null, '../order/cart.php', 'parent');
        } catch (RegularOrderGoodsDiscountPriceException $e) {
            \Logger::channel('regularOrder')->warning('Regular Order create error : ', $e->getMessage());
            $cartIdx = $orderData['cartSno'];
            $url = '../order/regular_order.php';
            if (!empty($cartIdx)) {
                $encodeData = urlencode(json_encode($cartIdx));
                $url .= '?cartIdx=' . $encodeData;
            }
            throw new AlertRedirectException('결제할 금액이 일치하지 않습니다. 할인/적립 금액이 변경되었을 수 있습니다. 새로고침 후 다시 시도해 주세요.', null, null, $url, 'parent');
        } catch (AlertBackException $e) {
            // 사은품 선택수량 검증 실패 사유
            \Logger::channel('regularOrder')->warning('Regular Order gift validation : ', [$e->getMessage()]);
            throw new AlertOnlyException($e->getMessage());
        } catch (\Throwable $e) {
            \Logger::channel('regularOrder')->error('Regular Order create error : ', [$e->getMessage(), $e->getTrace()]);
            throw new AlertOnlyException('일시적인 오류로 처리에 실패하였습니다. 잠시 후 다시 시도해주세요.');
        }
    }
}
