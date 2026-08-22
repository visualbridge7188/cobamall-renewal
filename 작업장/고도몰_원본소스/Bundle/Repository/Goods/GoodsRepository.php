<?php

namespace Bundle\Repository\Goods;

use Origin\Model\Goods\Goods;
use Exception;

class GoodsRepository
{

    /**
     * 정기결제(배송)에 등록 가능한 상품인지 확인 및 가격 리스트 반환
     *
     * SELECT goodsNo, goodsPrice
     * FROM es_goods
     * WHERE goodsNo IN (?, ?, ?, ?...)  -- $goodsNoList 배열에 해당하는 값들
     * AND (
     * goodsDisplayFl = 'y'
     * OR goodsDisplayMobileFl = 'y'
     * )
     * AND (
     * goodsSellFl = 'y'
     * OR goodsSellMobileFl = 'y'
     * )
     * AND (
     * stockFl = 'n'
     * OR (stockFl = 'y' AND totalStock >= 1)
     * )
     * AND applyFl = 'y'
     * AND goodsPrice >= ?;  -- $minPrice 값
     *
     * @param array $goodsNoList : 일반 상품 번호 리스트
     * @param int $minPrice : 최소 결제가
     * @return array : 일반 상품 가격 리스트
     */
    public function findGoodsPricesByConditionsBySaveRegularGoodsPossibleConditions(array $goodsNoList, int $minPrice): array
    {
        // 모든 상품 번호에 대해 조건을 확인
        $goodsPrices = Goods::query()
            ->whereIn('goodsNo', $goodsNoList)
            ->where(function ($query) {
                $query->orWhere('goodsDisplayFl', 'y')
                    ->orWhere('goodsDisplayMobileFl', 'y');
            })
            ->where(function ($query) {
                $query->orWhere('goodsSellFl', 'y')
                    ->orWhere('goodsSellMobileFl', 'y');
            })
            ->where(function ($query) {
                $query->where('stockFl', 'n')
                    ->orWhere(function ($query) {
                        $query->where('stockFl', 'y')
                            ->where('totalStock', '>=', 1);
                    });
            })
            ->where('applyFl', '=', 'y')
            ->where('goodsPrice', '>=', $minPrice)
            ->pluck('goodsPrice', 'goodsNo'); // 상품 번호와 가격을 키-값 형태로 반환

        return empty($goodsPrices) ? [] : $goodsPrices->toArray(); // 결과를 배열로 반환
    }

    /**
     * 정기결제(배송)에 등록 가능한 상품인지 확인 및 가격 반환
     *
     * SELECT goodsPrice
     * FROM es_goods
     * WHERE goodsNo = ?  -- $goodsNo에 해당하는 값
     * AND ( goodsDisplayFl = 'y' OR goodsDisplayMobileFl = 'y' )
     * AND ( goodsSellFl = 'y' OR goodsSellMobileFl = 'y' )
     * AND ( stockFl = 'n' OR (stockFl = 'y' AND totalStock >= 1))
     * AND applyFl = 'y'
     * AND goodsPrice >= ?;  -- $minPrice 값
     *
     * @param int $goodsNo : 일반 상품 번호
     * @param int $minPrice : 최소 결제가
     */
    public function findOneGoodsPriceBySaveRegularGoodsPossibleConditions(int $goodsNo, int $minPrice)
    {
        // 상품 번호에 대해 조건을 확인
        $price = Goods::query()
            ->where('goodsNo', '=', $goodsNo)
            ->where(function ($query) {
                $query->orWhere('goodsDisplayFl', 'y')
                    ->orWhere('goodsDisplayMobileFl', 'y');
            })
            ->where(function ($query) {
                $query->orWhere('goodsSellFl', 'y')
                    ->orWhere('goodsSellMobileFl', 'y');
            })
            ->where(function ($query) {
                $query->where('stockFl', 'n')
                    ->orWhere(function ($query) {
                        $query->where('stockFl', 'y')
                            ->where('totalStock', '>=', 1);
                    });
            })
            ->where('applyFl', '=', 'y')
            ->where('goodsPrice', '>=', $minPrice)
            ->value('goodsPrice'); // 단일 값 반환

        return $price;
    }

    /**
     * 원상품의 PC/MO 판매상태, 노출 상태에 대해 판매안함 및 노출안함으로 변경
     *
     * UPDATE es_goods
     * SET
     * goodsDisplayFl = 'n',
     * goodsDisplayMobileFl = 'n',
     * goodsSellFl = 'n',
     * goodsSellMobileFl = 'n',
     * modDt = ?
     * WHERE goodsNo IN (?, ?, ?, ...);
     *
     * @param array $goodsNoList
     * @param string $modDt
     * @return void
     */
    public function updateDisplayAndSellByGoodsNo(array $goodsNoList, string $modDt)
    {
        Goods::whereIn('goodsNo', $goodsNoList)
            ->update([
                'goodsDisplayFl' => 'n',
                'goodsDisplayMobileFl' => 'n',
                'goodsSellFl' => 'n',
                'goodsSellMobileFl' => 'n',
                'modDt' => $modDt
            ]);
    }

    /**
     * goodsNo를 바탕으로 상품이 존재하는 지 확인
     *
     * SELECT 1
     * FROM es_goods
     * WHERE goodsNo IN (?) AND delFl = 'y'
     * LIMIT 1;
     *
     * @param array $goodsNoList
     * @return bool
     */
    public function existGoodsByGoodsNoList(array $goodsNoList): bool
    {
        return Goods::whereIn('goodsNo', $goodsNoList)
            ->where('delFl', 'y')
            ->exists();
    }

    /**
     * goodsNo를 바탕으로 상품이 존재하는 지 확인
     *
     * SELECT 1
     * FROM goods
     * WHERE goodsNo IN ( ... ) -- 여기에 $goodsNoList 배열의 값들이 콤마로 나열됩니다
     * AND (
     * (goodsDisplayFl = 'n' AND goodsDisplayMobileFl = 'n')
     * OR (goodsSellFl = 'n' AND goodsSellMobileFl = 'n')
     * OR (stockFl = 'y' AND totalStock < 1)
     * OR (salesEndYmd IS NOT NULL AND salesEndYmd < '2025-06-19') -- 예시 날짜
     * OR soldOutFl = 'y'
     * OR applyFl = 'n'
     * )
     * LIMIT 1;
     *
     * @param array $goodsNoList
     * @param string $date
     * @param int $minPrice
     * @return bool
     */
    public function existNonSellableGoodsByGoodsNoList(array $goodsNoList, string $date, int $minPrice): bool
    {
        return Goods::whereIn('goodsNo', $goodsNoList)
            ->where(function ($query) use ($date, $minPrice) {
                $query
                    ->orWhere(function ($query) {
                        $query->where('goodsDisplayFl', 'n')
                            ->where('goodsDisplayMobileFl', 'n');
                    })
                    ->orWhere(function ($query) {
                        $query->where('goodsSellFl', 'n')
                            ->where('goodsSellMobileFl', 'n');
                    })
                    ->orWhere(function ($query) {
                        $query->where('stockFl', 'y')
                            ->where('totalStock', '<', 1);
                    })
                    ->orWhere(function ($query) use ($date) {
                        $query->whereNotNull('salesEndYmd')
                            ->where('salesEndYmd', '!=', '0000-00-00 00:00:00')
                            ->where('salesEndYmd', '<', $date);
                    })
                    ->orWhere('soldOutFl', '=', 'y')
                    ->orWhere('applyFl', '!=', 'y')
                    ->orWhere('goodsPrice', '<', $minPrice);
            })
            ->exists();
    }

    /**
     * 상품번호로 판매 관련 정보 조회
     *
     * SELECT fixedSales, minOrderCnt, maxOrderCnt, salesUnit
     * FROM es_goods
     * WHERE goodsNo = ?
     *
     * @param int $goodsNo 상품번호
     * @return array|null 판매 관련 정보 (fixedSales, minOrderCnt, maxOrderCnt, salesUnit)
     */
    public function findSalesInfoByGoodsNo(int $goodsNo): ?array
    {
        $goods = Goods::query()
            ->where('goodsNo', $goodsNo)
            ->select(['fixedSales', 'minOrderCnt', 'maxOrderCnt', 'salesUnit'])
            ->first();

        if ($goods) {
            return $goods->toArray();
        }
        return null;
    }

    /**
     * 상품번호로 배송비 관련 정보 조회 (무게별 배송비 범위 제한 체크용)
     *
     * SELECT sdb.fixFl, sdb.rangeLimitFl, sdb.rangeLimitWeight, g.goodsWeight
     * FROM es_goods as g
     * LEFT JOIN es_scmDeliveryBasic as sdb ON g.deliverySno = sdb.sno
     * WHERE g.goodsNo = ?
     *
     * @param int $goodsNo 상품번호
     * @return array|null 배송비 관련 정보 (fixFl, rangeLimitFl, rangeLimitWeight, goodsWeight)
     */
    public function findDeliveryWeightInfoByGoodsNo(int $goodsNo): ?array
    {
        $result = Goods::query()
            ->leftJoin('es_scmDeliveryBasic as sdb', 'es_goods.deliverySno', '=', 'sdb.sno')
            ->where('es_goods.goodsNo', $goodsNo)
            ->select([
                'sdb.fixFl',
                'sdb.rangeLimitFl',
                'sdb.rangeLimitWeight',
                'es_goods.goodsWeight'
            ])
            ->first();

        if ($result) {
            return $result->toArray();
        }
        return null;
    }

    /**
     * 상품 구매 제한 정보 조회
     *
     * SELECT goodsNo, fixedSales, fixedOrderCnt, minOrderCnt, maxOrderCnt, salesUnit
     * FROM es_goods
     * WHERE goodsNo IN (...)
     *
     * @param array $goodsNoList 상품 번호 리스트
     * @return array goodsNo를 키로 하는 판매 제한 정보 배열
     */
    public function findSalesInfoByGoodsNoList(array $goodsNoList): array
    {
        return Goods::query()
            ->whereIn('goodsNo', $goodsNoList)
            ->select(['goodsNo', 'fixedSales', 'fixedOrderCnt', 'minOrderCnt', 'maxOrderCnt', 'salesUnit'])
            ->get()
            ->keyBy('goodsNo')
            ->toArray();
    }

    /**
     * 상품 번호 리스트로 상품 가격 정보를 배치 조회
     *
     * SELECT goodsNo, goodsPrice
     * FROM es_goods
     * WHERE goodsNo IN (...) AND delFl = 'n'
     *
     * @param array $goodsNoList 상품 번호 리스트
     * @return array
     */
    public function findGoodsPricesByGoodsNoList(array $goodsNoList): array
    {
        return Goods::query()
            ->whereIn('goodsNo', $goodsNoList)
            ->where('delFl', 'n')
            ->select('goodsNo', 'goodsPrice')
            ->get()
            ->pluck('goodsPrice', 'goodsNo')
            ->toArray();
    }

    /**
     * 존재하는 상품 번호 목록 조회 (delFl = 'n')
     *
     * SELECT goodsNo
     * FROM es_goods
     * WHERE goodsNo IN (...) AND delFl = 'n'
     *
     * @param array $goodsNoList 상품 번호 리스트
     * @return array 존재하는 goodsNo 목록
     */
    public function findExistGoodsNoListByGoodsNoList(array $goodsNoList): array
    {
        return Goods::query()
            ->whereIn('goodsNo', $goodsNoList)
            ->where('delFl', 'n')
            ->pluck('goodsNo')
            ->toArray();
    }
}

