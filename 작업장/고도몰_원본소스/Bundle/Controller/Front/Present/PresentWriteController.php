<?php

namespace Bundle\Controller\Front\Present;

use Framework\Debug\Exception\AlertRedirectException;
use Framework\Debug\Exception\AlertBackException;
use Exception;
use Request;
use Component\Present\Config\PresentConfig;
use Component\Present\Card\PresentCard;
use Component\Present\Cart\PresentCart;
use Component\Present\Exception\PresentStockException;

class PresentWriteController extends \Controller\Front\Controller
{
    public function index()
    {
        try {
            // 선물하기 기능 사용 가능 여부 체크
            $presentConfig = \App::getInstance(PresentConfig::class);
            if (!$presentConfig->isUsePresent()) {
                throw new Exception('선물하기 기능이 비활성화되어 있습니다.');
            }
            
            // cartIdx 파라미터 받기
            $cartIdx = Request::request()->get('cartIdx');
            if (empty($cartIdx)) {
                throw new Exception('현재 선물할 상품이 없습니다. 상품을 선택한 후 다시 시도해 주세요.');
            }

            // 재고 체크
            $presentCart = \App::getInstance(PresentCart::class);
            $presentCart->checkCartStock($cartIdx);

            // 선물카드 정보 가져오기
            $presentCard = \App::getInstance(PresentCard::class);
            $availablePresentCards = $presentCard->getAvailablePresentCards();
            $this->setData('presentCardsCount', count($availablePresentCards));
            $this->setData('presentCards', $availablePresentCards);

            // 만료일자 정보 가져오기
            $expireDt = $presentCart->getExpirationDatetime();
            $this->setData('expireDt', $expireDt);
        } catch (PresentStockException $e) {
            throw new AlertBackException($e->getMessage());
        } catch (Exception $e) {
            throw new AlertRedirectException($e->getMessage(), null, null, '../main/index.php');
        }
    }
}
