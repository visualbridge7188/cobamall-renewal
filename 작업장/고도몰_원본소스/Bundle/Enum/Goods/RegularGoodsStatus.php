<?php
/*
 * Copyright (C) 2025 NHN COMMERCE. - All Rights Reserved
 *
 * Unauthorized copying or redistribution of this file in source and binary forms via any medium
 * is strictly prohibited.
 */

namespace Bundle\Enum\Goods;

class RegularGoodsStatus
{
    // 검색어
    const COMBINE_SEARCH = [
        'goodsNm' => '상품명',
        'goodsNo' => '상품코드',
        'goodsCd' => '자체상품코드'
    ];

    const COMBINE_SEARCH_FRONT = [
        'goodsNm' => '상품명',
        'goodsNo' => '상품코드'
    ];

    // 테이블 필드
    const REGULAR_GOODS_GRID_CONFIG_LIST = [
        ['gridKey' => 'check', 'gridName' => ''],
        ['gridKey' => 'no', 'gridName' => '번호'],
        ['gridKey' => 'goodsNo', 'gridName' => '상품코드'],
        ['gridKey' => 'goodsCd', 'gridName' => '자체상품코드'],
        ['gridKey' => 'goodsImage', 'gridName' => '이미지'],
        ['gridKey' => 'goodsNm', 'gridName' => '상품명'],
        ['gridKey' => 'goodsPrice', 'gridName' => '판매가'],
        ['gridKey' => 'regularPrice', 'gridName' => '정기결제가'],
        ['gridKey' => 'regularDiscountUseFl', 'gridName' => '정기할인'],
        ['gridKey' => 'scmNo', 'gridName' => '공급사'],
        ['gridKey' => 'regularDeliveryType', 'gridName' => '배송방법 노출여부'],
        ['gridKey' => 'regularDeliveryCycleType', 'gridName' => '배송 주기'],
        ['gridKey' => 'regularDeliveryRoundsDisplayType', 'gridName' => '종료회차'],
        ['gridKey' => 'regularGiftPresentUseFl', 'gridName' => '사은품 사용'],
        ['gridKey' => 'applyStatus', 'gridName' => '신청상태'],
        ['gridKey' => 'regDt', 'gridName' => '등록일/수정일'],
        ['gridKey' => 'memo', 'gridName' => '관리자 메모'],
        ['gridKey' => 'btn', 'gridName' => '수정'],
    ];

    // 정렬 방향
    const ASC = 'asc';
    const DESC = 'desc';

    // 정렬 기준
    const REGISTER_DATE = 'regDt';
    const GOODS_NM = 'goodsNm';
    const REGULAR_PRICE = 'regularPrice';
    const COMPANY_NM = 'companyNm';
    const ORDER_REGULAR_GOODS_CNT = 'orderRegularGoodsCnt';

    // 정렬 기준 라벨 목록
    private static $criteriaLabels = [
        self::REGISTER_DATE => '등록일',
        self::GOODS_NM => '상품명',
        self::REGULAR_PRICE => '정기결제가',
        self::COMPANY_NM => '공급사',
        self::ORDER_REGULAR_GOODS_CNT => '결제'
    ];

    // ASC만 허용되는 정렬 기준
    private static $ascOnlyCriteria = [
        self::ORDER_REGULAR_GOODS_CNT
    ];

    // 정렬 방향 라벨 목록
    private static $directionLabels = [
        self::ASC => '↑',
        self::DESC => '↓'
    ];

    /**
     * 정렬 기준과 방향을 조합하여 리스트 반환
     *
     * @return array
     */
    public static function getSortList(): array
    {
        $sortList = [];

        foreach (self::$criteriaLabels as $criteria => $label) {
            // ASC만 허용되는 기준인지 확인
            $allowedDirections = in_array($criteria, self::$ascOnlyCriteria, true)
                ? [self::ASC] // ASC만 허용
                : [self::DESC, self::ASC]; // ASC, DESC 모두 허용

            foreach ($allowedDirections as $direction) {
                $sortKey = "{$criteria} {$direction}";
                $sortList[$sortKey] = $label . ' ' . self::$directionLabels[$direction];
            }
        }

        return $sortList;
    }
}
