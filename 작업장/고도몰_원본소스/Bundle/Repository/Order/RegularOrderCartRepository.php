<?php
/*
 * Copyright (C) 2025 NHN COMMERCE. - All Rights Reserved
 *
 * Unauthorized copying or redistribution of this file in source and binary forms via any medium
 * is strictly prohibited.
 */

namespace Bundle\Repository\Order;

use Origin\Model\Order\RegularOrderCart;
class RegularOrderCartRepository
{
    /**
     * 마지막 장바구니 날짜 조회 (modDt 없으면 regDt)
     *
     * @param int $memNo
     * @return string|null
     */
    public function findLastCartDate(int $memNo): ?string
    {
        $cart = RegularOrderCart::query()
            ->selectRaw('COALESCE(modDt, regDt) as lastDt')
            ->where('memNo', $memNo)
            ->where('directCart', 'n')
            ->orderByRaw('COALESCE(modDt, regDt) DESC, regDt DESC')
            ->first();

        return $cart?->lastDt;
    }

    /**
     * 정기결제 장바구니 바로구매 상품 삭제
     *
     * @param int $memNo 회원번호
     * @return int 삭제된 레코드 수
     */
    public function deleteDirectCartByMemNo(int $memNo): int
    {
        return RegularOrderCart::query()
            ->where('memNo', $memNo)
            ->where('directCart', 'y')
            ->delete();
    }

    /**
     * 정기결제 장바구니에 상품 저장
     *
     * @param array $cartData 장바구니 데이터
     * @return int 저장된 장바구니 sno
     */
    public function saveCart(array $cartData): int
    {
        $cart = new RegularOrderCart();
        $cart->fill($cartData);
        $cart->save();

        return $cart->sno;
    }

    /**
     * 장바구니 상품 갯수 조회
     *
     * @param int $memNo 회원번호
     * @return int 상품 갯수
     */
    public function getCartCount(int $memNo): int
    {
        return RegularOrderCart::query()
            ->where('memNo', $memNo)
            ->where('directCart', 'n')
            ->count();
    }

    /**
     * 정기상품 중복 체크 (동일 옵션)
     *
     * @param int $memNo 회원번호
     * @param int $goodsNo 상품번호
     * @param int $optionSno 옵션번호
     * @param string $optionText 옵션텍스트
     * @return int 중복 갯수
     */
    public function countDuplicateRegularGoods(int $memNo, int $goodsNo, int $optionSno, string $optionText): int
    {
        return RegularOrderCart::query()
            ->where('memNo', $memNo)
            ->where('directCart', 'n')
            ->where('goodsNo', $goodsNo)
            ->where('optionSno', $optionSno)
            ->where('optionText', $optionText)
            ->count();
    }

    /**
     * 배송주기 업데이트
     *
     * @param int $sno 장바구니 sno
     * @param int $memNo 회원번호
     * @param array $cycleData 배송주기 데이터
     * @return int 업데이트된 레코드 수
     */
    public function updateDeliveryCycle(int $sno, int $memNo, array $cycleData): int
    {
        return RegularOrderCart::query()
            ->where('sno', $sno)
            ->where('memNo', $memNo)
            ->update($cycleData);
    }

    /**
     * 정기결제 장바구니 상품 정보 조회 (상품, 옵션, 이미지 JOIN)
     *
     * @param int $memNo 회원번호
     * @param array|null $cartIdx 조회할 장바구니 sno 목록
     * @param bool $isDirectCart 바로구매 여부
     * @return array 장바구니 상품 정보
     */
    public function findCartGoodsWithDetails(int $memNo, ?array $cartIdx = null, bool $isDirectCart = false): array
    {
        $query = RegularOrderCart::query()
            ->from('es_regularOrderCart as c')
            ->select([
                'c.*',
                // 상품 정보
                'g.goodsNm', 'g.commission', 'g.scmNo', 'g.purchaseNo', 'g.goodsCd', 'g.cateCd',
                'g.goodsOpenDt', 'g.goodsState', 'g.imageStorage', 'g.imagePath', 'g.brandCd',
                'g.makerNm', 'g.originNm', 'g.goodsModelNo', 'g.goodsPermission', 'g.goodsPermissionGroup',
                'g.goodsPermissionPriceStringFl', 'g.goodsPermissionPriceString', 'g.onlyAdultFl',
                'g.onlyAdultImageFl', 'g.goodsAccess', 'g.goodsAccessGroup', 'g.taxFreeFl', 'g.taxPercent',
                'g.goodsWeight', 'g.goodsVolume', 'g.totalStock', 'g.stockFl', 'g.soldOutFl', 'g.salesUnit',
                'g.minOrderCnt', 'g.maxOrderCnt', 'g.salesStartYmd', 'g.salesEndYmd',
                'g.mileageFl', 'g.mileageGoods', 'g.mileageGoodsUnit',
                'g.mileageGroup', 'g.mileageGroupInfo', 'g.mileageGroupMemberInfo',
                'g.goodsDiscountFl', 'g.goodsDiscount', 'g.goodsDiscountUnit',
                'g.payLimitFl', 'g.payLimit', 'g.goodsPriceString', 'g.goodsPrice', 'g.fixedPrice', 'g.costPrice',
                'g.optionFl', 'g.optionName', 'g.optionTextFl', 'g.addGoodsFl', 'g.addGoods', 'g.deliverySno',
                'g.delFl', 'g.hscode', 'g.goodsSellFl', 'g.goodsSellMobileFl', 'g.goodsDisplayFl', 'g.goodsDisplayMobileFl',
                'g.fixedGoodsDiscount', 'g.goodsDiscountGroup', 'g.goodsDiscountGroupMemberInfo',
                'g.exceptBenefit', 'g.exceptBenefitGroup', 'g.exceptBenefitGroupInfo',
                'g.fixedSales', 'g.fixedOrderCnt', 'g.goodsBenefitSetFl', 'g.benefitUseType',
                'g.newGoodsRegFl', 'g.newGoodsDate', 'g.newGoodsDateFl',
                'g.periodDiscountStart', 'g.periodDiscountEnd', 'g.regDt as goodsRegDt', 'g.modDt as goodsModDt',
                // 옵션 정보
                'go.optionValue1', 'go.optionValue2', 'go.optionValue3', 'go.optionValue4', 'go.optionValue5',
                'go.optionPrice', 'go.optionCostPrice', 'go.optionCode', 'go.stockCnt', 'go.optionSellFl',
                'go.optionViewFl', 'go.optionDeliveryFl',
                // 이미지 정보
                'gi.imageSize', 'gi.imageName', 'gi.goodsImageStorage', 'gi.imageUrl',
            ])
            ->join('es_goods as g', 'c.goodsNo', '=', 'g.goodsNo')
            ->leftJoin('es_goodsOption as go', function ($join) {
                $join->on('c.optionSno', '=', 'go.sno')
                    ->on('c.goodsNo', '=', 'go.goodsNo');
            })
            ->leftJoin('es_goodsImage as gi', function ($join) {
                $join->on('g.goodsNo', '=', 'gi.goodsNo')
                    ->where('gi.imageKind', '=', 'list');
            })
            ->where('c.memNo', $memNo)
            ->orderByDesc('c.sno');

        // 선택한 상품만 조회
        if (!empty($cartIdx)) {
            $query->whereIn('c.sno', $cartIdx);
        }

        // 바로구매 조건
        if ($isDirectCart) {
            $query->where('c.directCart', 'y');
        }

        return $query->get()->toArray();
    }

    /**
     * 장바구니 상품 삭제
     *
     * @param array $cartSnos 삭제할 장바구니 sno 목록
     * @param int|null $memNo 회원번호 (null이면 memNo 조건 없이 삭제)
     * @return int 삭제된 레코드 수
     */
    public function deleteBySnos(array $cartSnos, ?int $memNo = null): int
    {
        if (empty($cartSnos)) {
            return 0;
        }

        $query = RegularOrderCart::query()
            ->whereIn('sno', $cartSnos);

        if ($memNo !== null) {
            $query->where('memNo', $memNo);
        }

        return $query->delete();
    }

    /**
     * 장바구니 sno 목록으로 goodsNo 조회 (중복 제거)
     *
     * @param array $snos 장바구니 sno 목록
     * @return array goodsNo 목록
     */
    public function findGoodsNosBySnos(array $snos): array
    {
        if (empty($snos)) {
            return [];
        }

        return RegularOrderCart::query()
            ->select('goodsNo')
            ->whereIn('sno', $snos)
            ->groupBy('goodsNo')
            ->pluck('goodsNo')
            ->toArray();
    }

    /**
     * 묶음상품 사용 장바구니 개수 조회
     *
     * SELECT COUNT(*) as cnt
     * FROM es_regularOrderCart
     * WHERE useBundleGoods = 1
     *
     * @return int 묶음상품 사용 장바구니 개수
     */
    public function countUseBundleGoods(): int
    {
        return RegularOrderCart::query()
            ->where('useBundleGoods', 1)
            ->count();
    }

    /**
     * 장바구니 정보 조회 (sno 기준)
     *
     * SELECT *
     * FROM es_regularOrderCart
     * WHERE memNo = ? AND sno = ?
     *
     * @param int $memNo 회원번호
     * @param int $cartSno 장바구니 sno (단일 또는 배열, null이면 전체)
     * @return array 장바구니 정보
     */
    public function findRegularOrderCartInfoBySno(int $memNo, int $cartSno): array
    {
        $query = RegularOrderCart::query()
            ->where('memNo', $memNo)
            ->where('sno', $cartSno);

        return $query->get()->toArray();
    }

    /**
     * 장바구니 옵션 정보 업데이트 (sno 기준)
     *
     * UPDATE es_regularOrderCart
     * SET optionSno = ?, goodsCnt = ?, optionText = ?, addGoodsNo = ?, addGoodsCnt = ?, ...
     * WHERE sno = ?
     *
     * @param int $sno 장바구니 sno
     * @param array $data 업데이트할 데이터
     * @return int 업데이트된 레코드 수
     */
    public function updateCartOptionBySno(int $sno, array $data): int
    {
        return RegularOrderCart::query()
            ->where('sno', $sno)
            ->update($data);
    }

    /**
     * 배송 방식 일괄 업데이트 (동일 상품)
     *
     * UPDATE es_regularOrderCart
     * SET deliveryCollectFl = ?, deliveryMethodFl = ?
     * WHERE mallSno = ? AND directCart = ? AND goodsNo = ? AND memNo = ?
     *
     * @param array $conditions 조건 데이터 (mallSno, directCart, goodsNo, memNo)
     * @param array $data 업데이트할 데이터 (deliveryCollectFl, deliveryMethodFl)
     * @return int 업데이트된 레코드 수
     */
    public function updateDeliveryMethodByConditions(array $conditions, array $data): int
    {
        $query = RegularOrderCart::query()
            ->where('mallSno', $conditions['mallSno'])
            ->where('directCart', $conditions['directCart'])
            ->where('goodsNo', $conditions['goodsNo'])
            ->where('memNo', $conditions['memNo']);

        return $query->update($data);
    }

    /**
     * 장바구니 상품 수량 업데이트 (sno 기준)
     *
     * UPDATE es_regularOrderCart
     * SET goodsCnt = ?
     * WHERE sno = ?
     *
     * @param int $sno 장바구니 sno
     * @param int $goodsCnt 상품 수량
     * @return int 업데이트된 레코드 수
     */
    public function updateGoodsCntBySno(int $sno, int $goodsCnt): int
    {
        return RegularOrderCart::query()
            ->where('sno', $sno)
            ->update(['goodsCnt' => $goodsCnt]);
    }

    /**
     * 장바구니 추가 상품 정보 조회 (sno 기준)
     *
     * SELECT addGoodsNo, addGoodsCnt
     * FROM es_regularOrderCart
     * WHERE sno = ?
     *
     * @param int $sno 장바구니 sno
     * @return array 추가 상품 정보 (addGoodsNo, addGoodsCnt)
     */
    public function findAddGoodsBySno(int $sno): array
    {
        $result = RegularOrderCart::query()
            ->select(['addGoodsNo', 'addGoodsCnt'])
            ->where('sno', $sno)
            ->first();

        return $result ? $result->toArray() : [];
    }

    /**
     * 장바구니 추가 상품 수량 업데이트 (sno 기준)
     *
     * UPDATE es_regularOrderCart
     * SET addGoodsCnt = ?
     * WHERE sno = ?
     *
     * @param int $sno 장바구니 sno
     * @param string $addGoodsCnt 추가 상품 수량 (JSON 문자열)
     * @return int 업데이트된 레코드 수
     */
    public function updateAddGoodsCntBySno(int $sno, string $addGoodsCnt): int
    {
        return RegularOrderCart::query()
            ->where('sno', $sno)
            ->update(['addGoodsCnt' => $addGoodsCnt]);
    }

    /**
     * 카트 pk 와 사용자 번호로 카트 정보 반환
     *
     * SELECT * FROM es_regularOrderCart
     *  WHERE sno = ? AND memNo = ?
     *
     * @param int $sno
     * @param int $memNo
     * @return array
     */
    public function findCartInfoBySnoAndMemNo(int $sno, int $memNo): array
    {
        $cart = RegularOrderCart::query()
            ->where('sno', $sno)
            ->where('memNo', $memNo)
            ->first();

        return $cart ? $cart->toArray() : [];
    }

    /**
     * DELETE FROM es_regularOrderCart
     *  WHERE sno in (?, ?, ?, ?...)
     * @param array $snoList
     * @return void
     */
    public function deleteCartBySnoList(array $snoList): void
    {
        RegularOrderCart::query()
            ->whereIn('sno', $snoList)
            ->delete();
    }
}
