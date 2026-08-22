<?php
/*
 * Copyright (C) 2025 NHN COMMERCE. - All Rights Reserved
 *
 * Unauthorized copying or redistribution of this file in source and binary forms via any medium
 * is strictly prohibited.
 */

namespace Bundle\Component\Present\Goods;

use Framework\Log\Logger;
use Component\Present\Config\PresentConfig;
use Repository\Present\Category\PresentCategoryRepository;
use Repository\Present\Goods\PresentExceptGoodsRepository;
use Repository\Present\Goods\PresentApplyGoodsRepository;

class PresentGoods
{
    public function __construct(
        private readonly Logger $logger,
        private readonly PresentConfig $presentConfig,
        protected PresentCategoryRepository $presentCategoryRepository,
        protected PresentExceptGoodsRepository $presentExceptGoodsRepository,
        protected PresentApplyGoodsRepository $presentApplyGoodsRepository
    ) {
    }

    /**
     * 상품 상세에서 선물하기 기능 사용 가능 여부
     *
     * @param int $goodsNo 상품 번호
     * @param array $goodsView 상품 정보 배열
     * @return bool
     */
    public function canUsePresentByGoods(int $goodsNo, array $goodsView): bool
    {
        // 무료배송 상품이 아닌 경우 선물하기 불가
        if (!isset($goodsView['delivery']['basic']['fixFl']) || $goodsView['delivery']['basic']['fixFl'] !== 'free') {
            return false;
        }

        // 지역별 추가배송비가 있을 경우 선물하기 불가
        if (!isset($goodsView['delivery']['basic']['areaFl']) || $goodsView['delivery']['basic']['areaFl'] !== 'n') {
            return false;
        }

        // 성인인증 상품 선물하기 불가
        if ($goodsView['onlyAdultFl'] == 'y') {
            return false;
        }

        // 선물하기 기능 설정에 따른 사용 여부
        if ($this->canUsePresentByConfig($goodsNo) === false) {
            return false;
        }

        // 모든 조건을 만족하면 true
        return true;
    }

    /**
     * 선물하기 기능 설정에 따른 사용 여부
     *
     * @param int $goodsNo 상품 번호
     * @return bool
     */
    public function canUsePresentByConfig(int $goodsNo): bool
    {
        // 선물하기 기능 사용 여부
        if ($this->presentConfig->isUsePresent() !== true) {
            return false;
        }

        // 선물하기 상품 설정 타입
        $applyType = $this->presentConfig->getCurrentApplyType();

        // 선물하기 상품 설정 타입이 카테고리별 적용일 경우
        if ($applyType === 'category') {
            // 예외(제외) 상품에 등록되어 있을 경우 선물하기 불가
            if ($this->presentExceptGoodsRepository->hasExceptGoodsByGoodsNo($goodsNo) === true) {
                return false;
            }

            // 노출 카테고리에 포함되어 있지 않을 경우 선물하기 불가
            if ($this->presentCategoryRepository->hasPresentCategoryByGoodsNo($goodsNo) === false) {
                return false;
            }

        // 선물하기 상품 설정 타입이 상품 직접선택일 경우
        } elseif ($applyType === 'applyGoods') {
            // 상품이 개별 상품 목록에 포함되어 있지 않을 경우 선물하기 불가
            if ($this->presentApplyGoodsRepository->hasApplyGoodsByGoodsNo($goodsNo) === false) {
                return false;
            }
        }

        return true;
    }
}
