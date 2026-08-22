<?php
/*
 * Copyright (C) 2025 NHN COMMERCE. - All Rights Reserved
 *
 * Unauthorized copying or redistribution of this file in source and binary forms via any medium is strictly prohibited.
 */

namespace Bundle\Component\Cart;

use DTO\Cart\CartRequestDTO;
use Repository\Goods\GoodsOptionRepository;
use Repository\Goods\GoodsOptionTextRepository;
use Repository\Goods\GoodsRepository;
use Repository\Cart\CartRepository;
use Repository\Member\MemberOrderGoodsCountRepository;

class ReorderCart
{
    public const MAX_REORDER_GOODS_COUNT = 50;

    public function __construct(
        protected GoodsRepository $goodsRepository,
        protected GoodsOptionRepository $goodsOptionRepository,
        protected GoodsOptionTextRepository $goodsOptionTextRepository,
        protected CartRepository $cartRepository,
        protected MemberOrderGoodsCountRepository $memberOrderGoodsCountRepository,
    )
    {
    }

    /**
     * 재주문 대상 주문상품 목록을 장바구니 데이터로 변환
     *
     * @param array $orderGoodsList 재주문 대상 주문상품 목록
     * @param string $cartMode 장바구니 모드 ('' = 장바구니, 'd' = 바로구매)
     * @return array
     */
    public function convertToCartData(array $orderGoodsList, string $cartMode): array
    {
        $cartData = [];
        
        foreach ($orderGoodsList as $orderGoods) {
            $cartKey = $this->generateCartKey($orderGoods);

            // 동일 상품(상품번호, 옵션번호, 텍스트옵션 동일) 제외 처리
            if (isset($cartData[$cartKey])) {
                continue;
            }
            
            $cartDTO = $this->convertOrderGoodsToCartData($orderGoods, $cartMode);
            $cartData[$cartKey] = $cartDTO->toArray();
        }
        
        return $cartData;
    }
    
    /**
     * 장바구니 데이터 키 생성
     * 상품번호, 옵션번호, 텍스트옵션이 모두 같아야 동일 상품으로 처리
     *
     * @param array $orderGoods 주문상품 정보
     * @return string
     */
    protected function generateCartKey(array $orderGoods): string
    {
        $key = $orderGoods['goodsNo'] . '_' . $orderGoods['optionSno'];
        
        // 텍스트 옵션이 있으면 키에 포함
        if (!empty($orderGoods['optionTextInfo'])) {
            $optionText = $this->extractOptionTextValues($orderGoods['optionTextInfo']);
            
            if (!empty($optionText)) {
                $key .= '_' . hash('xxh64', serialize($optionText));
            }
        }
        
        return $key;
    }
    
    /**
     * optionTextInfo에서 텍스트 옵션 정보를 추출
     *  
     * @param mixed $optionTextInfo
     * @return array
     */
    protected function extractOptionTextValues($optionTextInfo): array
    {
        // JSON 문자열인 경우 디코딩
        if (is_string($optionTextInfo)) {
            $optionTextInfo = json_decode($optionTextInfo, true);
        }
        
        if (!is_array($optionTextInfo)) {
            return [];
        }
        
        $optionText = [];
        
        foreach ($optionTextInfo as $optionSno => $optionInfo) {
            if (isset($optionInfo['optionValue'])) {
                $optionText[$optionSno] = $optionInfo['optionValue'];
            }
        }
        
        return $optionText;
    }
    
    /**
     * 개별 주문상품을 장바구니 데이터로 변환
     *
     * @param array $orderGoods 주문상품 정보
     * @param string $cartMode 장바구니 모드
     * @return CartRequestDTO
     */
    protected function convertOrderGoodsToCartData(array $orderGoods, string $cartMode = ''): CartRequestDTO
    {
        // 텍스트 옵션 정보 변환
        $optionText = null;
        if (!empty($orderGoods['optionTextInfo'])) {
            $optionText = $this->convertOptionTextInfoToCartData($orderGoods['optionTextInfo']);
        }

        return new CartRequestDTO(
            scmNo: $orderGoods['scmNo'],
            goodsNo: [$orderGoods['goodsNo']],
            optionSno: [$orderGoods['optionSno']],
            goodsCnt: [$orderGoods['goodsCnt']],
            setTotalPrice: $orderGoods['goodsPrice'],
            deliveryCollectFl: $orderGoods['deliveryCollectFl'],
            deliveryMethodFl: $orderGoods['deliveryMethodFl'],
            cartMode: $cartMode,
            optionText: $optionText,
        );
    }
    
    /**
     * 텍스트 옵션 정보를 장바구니 데이터로 변환
     *
     * @param array|string $optionTextInfo 텍스트 옵션 정보 (배열 또는 JSON 문자열)
     * @return array
     */
    protected function convertOptionTextInfoToCartData($optionTextInfo): array
    {
        $optionText = $this->extractOptionTextValues($optionTextInfo);
        
        return [$optionText];
    }
    
    /**
     * 장바구니 데이터의 가격을 현재 가격으로 재계산
     *
     * @param array $cartDataList 장바구니 데이터 목록
     * @return array
     */
    public function recalculateCartPrices(array $cartDataList): array
    {
        $keyList = $this->extractGoodsKeyList($cartDataList);
        $priceData = $this->getGoodsPriceData($keyList);

        foreach ($cartDataList as $cartKey => $cartData) {
            $cartDataList[$cartKey]['set_total_price'] = $this->calculateCartTotalPrice($cartData, $priceData);
        }

        return $cartDataList;
    }
    
    /**
     * 가격 데이터 조회 (상품, 옵션, 텍스트 옵션)
     *
     * @param array $keyList 추출된 번호 리스트
     * @return array
     */
    protected function getGoodsPriceData(array $keyList): array
    {
        // 상품 가격 배치 조회
        if (!empty($keyList['goodsNoList'])) {
            $goodsPriceData = $this->goodsRepository->findGoodsPricesByGoodsNoList($keyList['goodsNoList']);
        }

        // 옵션 가격 배치 조회
        if (!empty($keyList['optionSnoList'])) {
            $optionPriceData = $this->goodsOptionRepository->findOptionPricesBySnoList($keyList['optionSnoList']);
        }

        // 텍스트 옵션 가격 배치 조회
        if (!empty($keyList['optionTextSnoList'])) {
            $textOptionPriceData = $this->goodsOptionTextRepository->findOptionTextPricesBySnoList($keyList['optionTextSnoList']);
        }

        return [
            'goods' => $goodsPriceData ?? [],
            'options' => $optionPriceData ?? [],
            'textOptions' => $textOptionPriceData ?? [],
        ];
    }

    /**
     * 장바구니 키 추출 (상품번호, 옵션번호, 텍스트 옵션번호)
     *
     * @param array $cartDataList 장바구니 목록
     * @return array
     */
    protected function extractGoodsKeyList(array $cartDataList): array
    {
       // 상품번호 추출
       $goodsNoList = array_unique(array_column(array_column($cartDataList, 'goodsNo'), 0));
       // 옵션번호 추출
       $optionSnoList = array_unique(array_column(array_column($cartDataList, 'optionSno'), 0));

       // 텍스트 옵션번호 추출
       $optionTextSnoList = [];
       foreach ($cartDataList as $cartData) {
           $optionTextSnoList = array_merge($optionTextSnoList, array_keys($cartData['optionText'][0] ?? []));
       }
       $optionTextSnoList = array_unique($optionTextSnoList);

       return [
           'goodsNoList' => $goodsNoList,
           'optionSnoList' => $optionSnoList,
           'optionTextSnoList' => $optionTextSnoList,
       ];
    }

    /**
     * 장바구니 총 가격 계산
     *
     * @param array $cartData 장바구니 데이터
     * @param array $priceData 가격 데이터 (상품, 옵션, 텍스트 옵션, 추가상품)
     * @return int|float
     */
    protected function calculateCartTotalPrice(array $cartData, array $priceData): int|float
    {
        $goodsNo = $cartData['goodsNo'][0] ?? 0;
        $optionSno = $cartData['optionSno'][0] ?? 0;
        $goodsCnt = $cartData['goodsCnt'][0] ?? 1;

        // 상품 가격
        $goodsPrice = $priceData['goods'][$goodsNo] ?? 0;

        // 옵션 가격
        $optionPrice = $priceData['options'][$optionSno] ?? 0;

        // 텍스트 옵션 가격
        $optionTextPrice = 0;
        foreach ($cartData['optionText'][0] ?? [] as $textSno => $textValue) {
            $optionTextPrice += $priceData['textOptions'][$textSno] ?? 0;
        }

        // 총 가격 합산
        return ($goodsPrice + $optionPrice + $optionTextPrice) * $goodsCnt;
    }

    /**
     * 재구매 상품 목록을 장바구니 데이터로 변환
     *
     * @param array $orderGoodsList 재구매 상품 목록
     * @param string $cartMode 장바구니 모드
     * @return array
     */
    public function convertToReorderCartData(array $orderGoodsList, string $cartMode): array
    {
        $reorderCartDataList = $this->convertToCartData($orderGoodsList, $cartMode);
        return $this->recalculateCartPrices($reorderCartDataList);
    }

    /**
     * 장바구니에 담을 수 있는 데이터만 필터링
     *
     * @param array $cartDataList 장바구니 데이터 목록
     * @return array 장바구니에 담을 수 있는 데이터만 반환
     */
    public function filterAvailableCartData(array $cartDataList): array
    {
        $availableCartDataList = [];
        $zeroPriceOrderFl = gd_policy('order.cart')['zeroPriceOrderFl'];
        $goodsNoList = array_unique(array_map(fn($cartData) => $cartData['goodsNo'][0], $cartDataList));
        $existingGoodsNoList = $this->goodsRepository->findExistGoodsNoListByGoodsNoList($goodsNoList);

        foreach ($cartDataList as $cartKey => $cartData) {
            $goodsNo = $cartData['goodsNo'][0];
            // 삭제 상품 제외 (완전 삭제 또는 delFl='y' 상품)
            if (!in_array($goodsNo, $existingGoodsNoList)) {
                continue;
            }
            // 가격이 0원인 상품을 담을 수 없는 경우
            if ($zeroPriceOrderFl === 'n' && $cartData['set_total_price'] <= 0) {
                continue;
            }
            $availableCartDataList[$cartKey] = $cartData;
        }

        return $availableCartDataList;
    }

    /**
     * 바로구매 시 구매 제한 유효성 검사 (묶음주문 단위 + 구매수량 제한)
     *
     * @param array $cartDataList 장바구니 데이터 목록
     * @param int $memNo 회원번호 (ID 기준 구매수량 체크용)
     * @return bool
     */
    public function isPurchasable(array $cartDataList, int $memNo): bool
    {
        if (empty($cartDataList)) {
            return true;
        }

        $goodsNoList = array_unique(array_column(array_column($cartDataList, 'goodsNo'), 0));

        // 상품별 수량 집계 (동일 상품의 각 옵션 수량을 배열로 수집)
        $goodsCntList = [];
        foreach ($cartDataList as $cartData) {
            $goodsNo = $cartData['goodsNo'][0];
            $goodsCnt = (int) $cartData['goodsCnt'][0];
            $goodsCntList[$goodsNo][] = $goodsCnt;
        }

        $salesInfoList = $this->goodsRepository->findSalesInfoByGoodsNoList($goodsNoList);
        if (empty($salesInfoList)) {
            return true;
        }

        // ID 기준 구매수량 제한 상품의 회원 누적 구매수량 일괄 조회 (비회원은 조회 불필요)
        $idLimitGoodsNoList = array_keys(array_filter($salesInfoList, fn($info) => $info['fixedOrderCnt'] === 'id'));
        $memberOrderCounts = (!empty($idLimitGoodsNoList) && $memNo > 0)
            ? $this->memberOrderGoodsCountRepository->findOrderCountByGoodsNoList($memNo, $idLimitGoodsNoList)
            : [];

        foreach ($goodsCntList as $goodsNo => $optionCntList) {
            $salesInfo = $salesInfoList[$goodsNo] ?? null;
            if (empty($salesInfo)) {
                continue;
            }

            //구매수량 제한 체크
            if (!$this->isWithinOrderCntLimit($optionCntList, $salesInfo, $memberOrderCounts[$goodsNo] ?? 0)) {
                return false;
            }

            // 묶음주문 단위 체크
            if (!$this->isWithinSalesUnitLimit($optionCntList, $salesInfo)) {
                return false;
            }
        }

        return true;
    }

    /**
     * 구매수량 제한 체크 (옵션/상품/ID 기준)
     *
     * - option: 각 옵션별 수량이 min~max 범위 내인지 체크
     * - goods: 동일 상품의 전체 옵션 수량 합산으로 체크
     * - id: 회원 누적 구매수량 + 현재 주문 수량 합산으로 체크
     *
     * @param array $optionCntList 동일 상품 내 각 옵션의 주문 수량 목록
     * @param array $salesInfo 상품 판매 정보 (fixedOrderCnt, minOrderCnt, maxOrderCnt)
     * @param int $historicalCnt 회원의 해당 상품 누적 구매수량 (ID 기준일 때 사용)
     * @return bool
     */
    protected function isWithinOrderCntLimit(array $optionCntList, array $salesInfo, int $historicalCnt = 0): bool
    {
        $minOrderCnt = (int) $salesInfo['minOrderCnt'];
        $maxOrderCnt = (int) $salesInfo['maxOrderCnt'];
        $fixedOrderCnt = $salesInfo['fixedOrderCnt'];
        $totalCnt = array_sum($optionCntList);

        // 구매수량 제한 설정이 없으면 true 반환
        $hasMinMaxRestriction = !($minOrderCnt <= 1 && $maxOrderCnt == 0);
        if (!$hasMinMaxRestriction) {
            return true;
        }

        if ($fixedOrderCnt === 'option') {
            foreach ($optionCntList as $cnt) {
                if ($cnt < $minOrderCnt || ($maxOrderCnt > 0 && $cnt > $maxOrderCnt)) {
                    return false;
                }
            }
        } elseif ($fixedOrderCnt === 'id') {
            $totalWithHistory = $totalCnt + $historicalCnt;
            if ($totalWithHistory < $minOrderCnt || ($maxOrderCnt > 0 && $totalWithHistory > $maxOrderCnt)) {
                return false;
            }
        } else {
            if ($totalCnt < $minOrderCnt || ($maxOrderCnt > 0 && $totalCnt > $maxOrderCnt)) {
                return false;
            }
        }

        return true;
    }

    /**
     * 묶음주문 단위 체크 (옵션/상품 기준)
     *
     * - option: 각 옵션별 수량이 묶음 단위(salesUnit)의 배수인지 체크
     * - goods: 동일 상품의 전체 옵션 수량 합산이 묶음 단위의 배수인지 체크
     *
     * @param array $optionCntList 동일 상품 내 각 옵션의 주문 수량 목록
     * @param array $salesInfo 상품 판매 정보 (fixedSales, salesUnit)
     * @return bool
     */
    protected function isWithinSalesUnitLimit(array $optionCntList, array $salesInfo): bool
    {
        $salesUnit = (int) $salesInfo['salesUnit'];
        $fixedSales = $salesInfo['fixedSales'];

        if (empty($salesUnit)) {
            return true;
        }

        $totalCnt = array_sum($optionCntList);

        if ($fixedSales === 'option') {
            foreach ($optionCntList as $cnt) {
                if ($cnt % $salesUnit !== 0) {
                    return false;
                }
            }
        } else {
            if ($totalCnt % $salesUnit !== 0) {
                return false;
            }
        }

        return true;
    }

    /**
     * 바로구매 상품을 일반 장바구니로 변환
     *
     * @param array $cartSnoList 바로구매 상품 sno 목록
     * @return void
     */
    public function moveDirectToNormalCart(array $cartSnoList): void
    {
        try {
            $this->cartRepository->updateDirectCartBySnoList($cartSnoList);
        } catch (\Throwable $e) {
            throw new \Exception('바로구매 상품을 일반 장바구니로 변환 중 오류가 발생했습니다.');
        }
    }
}
