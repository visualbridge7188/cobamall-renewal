<?php
/*
 * Copyright (C) 2026 NHN COMMERCE. - All Rights Reserved
 *
 * Unauthorized copying or redistribution of this file in source and binary forms via any medium
 * is strictly prohibited.
 */

namespace Bundle\Widget\Front\Goods;

class GoodsDisplayDesignEditorWidget extends \Widget\Front\Widget
{
    public function index(): void
    {
        $goodsDisplay = \App::load('\\Component\\Goods\\GoodsDisplayDesignEditor');

        // 디자인 에디터 데이터 병합 (컴포넌트 기본값 + 스킨 DED 값)
        $designEditorData = $goodsDisplay->buildDesignEditorDataFromRequest(
            $this->getData('designEditorData') ?? []
        );

        // 상품 목록 조회
        $displayFl = false;
        $sourceType = $designEditorData['goodsSourceType'];
        $ndeWidgetKey = $sourceType;

        if ($sourceType === $goodsDisplay::SOURCE_TYPE_CATEGORY) {
            $displayFl = $this->getCategoryGoodsList($goodsDisplay, $designEditorData);
            $ndeWidgetKey .= '_' . $designEditorData['goodsCategoryCode'];
        } elseif ($sourceType === $goodsDisplay::SOURCE_TYPE_PROMOTION) {
            $displayFl = $this->getPromotionGoodsList($goodsDisplay, $designEditorData);
            $ndeWidgetKey .= '_' . $designEditorData['goodsPromotionCode'];
        } elseif ($sourceType === $goodsDisplay::SOURCE_TYPE_EXTERNAL) {
            $displayFl = $this->getExternalGoodsList($goodsDisplay, $designEditorData);
            $ndeWidgetKey .= '_external';
        }

        $ndeWidgetKey .= '_' . sprintf('%06d', random_int(0, 999999));

        // 디자인 에디터 데이터 저장
        $this->setData('designEditorData', $designEditorData);

        // 추가 표시 데이터 설정
        if ($displayFl) {
            $this->setAdditionalDisplayData($designEditorData, $ndeWidgetKey);
        }
    }

    /**
     * 카테고리 상품 목록 조회
     *
     * @param object $goodsDisplay GoodsDisplayDesignEditor 컴포넌트
     * @param array $designEditorData 디자인 에디터 데이터
     * @return bool
     */
    protected function getCategoryGoodsList($goodsDisplay, array $designEditorData): bool
    {
        $cateCd = $designEditorData['goodsCategoryCode'];
        if (empty($cateCd)) {
            return false;
        }

        $displayCnt = max(1, (int)$designEditorData['goodsPerPage']);
        $displayMode = $designEditorData['goodsDisplayMode'] ?: 'none';
        $isLoadMore = $displayMode === 'loadMore';
        $isPagination = $displayMode === 'pagination';

        $result = $goodsDisplay->loadCategoryGoods($cateCd, [
            'displayCnt' => $displayCnt,
            'soldOutToBack' => $designEditorData['goodsSoldOutToBack'] === 'true',
            'usePage' => $isLoadMore || $isPagination,
        ]);

        if (empty($result)) {
            return false;
        }

        $finalized = $this->finalizeGoodsList($result['goodsList'], $result['themeInfo'], $displayCnt);

        // 더보기/페이지네이션 모드: totalPage 계산 및 템플릿 데이터 설정
        if ($finalized && ($isLoadMore || $isPagination)) {
            $totalPage = $result['totalPage'] ?? 1;
            $this->setData('totalPage', $totalPage);

            if ($isLoadMore) {
                $this->setData('showLoadMore', true);
            } elseif ($isPagination && $totalPage > 1) {
                $this->setPaginationData();
            }
        }

        return $finalized;
    }

    /**
     * 기획전 상품 목록 조회
     *
     * @param object $goodsDisplay GoodsDisplayDesignEditor 컴포넌트
     * @param array $designEditorData 디자인 에디터 데이터
     * @return bool
     */
    protected function getPromotionGoodsList($goodsDisplay, array $designEditorData): bool
    {
        $sno = (int)$designEditorData['goodsPromotionCode'];
        if ($sno <= 0) {
            return false;
        }

        $displayCnt = max(1, (int)$designEditorData['goodsPerPage']);
        $displayMode = $designEditorData['goodsDisplayMode'] ?: 'none';
        $isLoadMore = $displayMode === 'loadMore';
        $isPagination = $displayMode === 'pagination';

        $result = $goodsDisplay->loadPromotionGoods($sno, [
            'displayCnt' => $displayCnt,
            'soldOutToBack' => $designEditorData['goodsSoldOutToBack'] === 'true',
            'usePage' => $isLoadMore || $isPagination,
        ]);

        if (empty($result)) {
            return false;
        }

        $finalized = $this->finalizeGoodsList($result['goodsList'], $result['themeInfo'], $displayCnt);

        // 더보기/페이지네이션 모드: totalPage 계산 및 템플릿 데이터 설정
        if ($finalized && ($isLoadMore || $isPagination)) {
            $totalPage = $result['totalPage'] ?? 1;
            $this->setData('totalPage', $totalPage);

            if ($isLoadMore) {
                $this->setData('showLoadMore', true);
            } elseif ($isPagination && $totalPage > 1) {
                $this->setPaginationData();
            }
        }

        return $finalized;
    }

    /**
     * 외부 주입 상품 목록 처리
     *
     * @param object $goodsDisplay GoodsDisplayDesignEditor 컴포넌트
     * @param array $designEditorData 디자인 에디터 데이터
     * @return bool
     */
    protected function getExternalGoodsList($goodsDisplay, array $designEditorData): bool
    {
        // 외부 주입 데이터 조회 (designEditorData 내부 → setData 폴백)
        $goodsList = $designEditorData['widgetGoodsList']
            ?? $this->getData('setWidgetData')
            ?? $this->getData('widgetGoodsList');

        if (empty($goodsList)) {
            return false;
        }

        // 테마 정보 조회 (designEditorData 내부 → setData 폴백 → 기본값)
        $themeInfo = $designEditorData['widgetTheme']
            ?? $this->getData('setWidgetTheme')
            ?? $this->getData('widgetTheme')
            ?? $goodsDisplay::EXTERNAL_DEFAULT_THEME;

        // 콤마 구분 필드를 배열로 변환 (컨트롤러별로 문자열/배열 형태가 다를 수 있음)
        $arrayFields = ['displayField', 'goodsDiscount', 'displayAddField', 'priceStrike'];
        foreach ($arrayFields as $field) {
            if (!empty($themeInfo[$field]) && is_string($themeInfo[$field])) {
                $themeInfo[$field] = explode(',', $themeInfo[$field]);
            }
        }

        // 이미 chunk된 2차원 배열인지 판별 (첫 번째 요소가 배열의 배열인지)
        $isChunked = !empty($goodsList) && isset($goodsList[0]) && is_array($goodsList[0])
            && !empty($goodsList[0]) && isset($goodsList[0][0]) && is_array($goodsList[0][0]);

        // chunk된 데이터 → 1차원으로 펼쳐서 처리
        $flatList = $isChunked ? array_merge(...$goodsList) : $goodsList;

        if (empty($flatList)) {
            return false;
        }

        $displayCnt = max(1, (int)$designEditorData['goodsPerPage']);
        $displayFields = gd_array_values($themeInfo['displayField'] ?? []);

        /** @var \Component\Goods\Goods $goods */
        $goods = \App::load('\\Component\\Goods\\Goods');

        // 상품 할인가 계산 → 할인가·할인율 보정 (순서 보장)
        if (gd_in_array('goodsDcPrice', $displayFields)) {
            foreach ($flatList as $key => $item) {
                $flatList[$key]['goodsDcPrice'] = $goods->getGoodsDcPrice($item);
            }
        }
        $flatList = $this->supplementExternalDiscountFields($flatList, $themeInfo);

        return $this->finalizeGoodsList($flatList, $themeInfo, $displayCnt);
    }

    /**
     * 상품 목록 최종 처리 (청크 분할 + 데이터 설정)
     *
     * @param array $goodsList 계산 완료된 1차원 상품 목록
     * @param array $themeInfo 테마 정보
     * @param int $displayCnt 페이지당 표시 수
     * @return bool
     */
    protected function finalizeGoodsList(array $goodsList, array $themeInfo, int $displayCnt): bool
    {
        if (empty($goodsList)) {
            return false;
        }

        $this->setData('goodsList', array_chunk($goodsList, $displayCnt));
        $this->setData('themeInfo', $themeInfo);

        return true;
    }

    /**
     * 외부 주입 데이터의 할인가·할인율 보정
     *
     * @param array $goodsList 상품 목록
     * @param array $themeInfo 위젯 테마 정보
     * @return array
     */
    protected function supplementExternalDiscountFields(array $goodsList, array $themeInfo): array
    {
        foreach ($goodsList as $key => $item) {
            if (!empty($item['goodsPriceString'])) {
                continue;
            }

            // dcPrice 재계산: 상품할인 + 쿠폰할인 합산
            $goodsDcPrice = gd_isset($item['goodsDcPrice'], 0);
            $couponDcPrice = gd_isset($item['couponDcPrice'], 0);
            $dcPrice = $goodsDcPrice + $couponDcPrice;

            $goodsPrice = gd_isset($item['goodsPrice'], 0);
            if ($dcPrice >= $goodsPrice) {
                $dcPrice = 0;
            }

            $goodsList[$key]['dcPrice'] = $dcPrice;

            // 할인율 항상 계산 (노출 여부는 스킨에서 결정)
            if ($goodsPrice == 0) {
                $goodsList[$key]['goodsDcRate'] = 0;
                $goodsList[$key]['couponDcRate'] = 0;
            } else {
                $goodsList[$key]['goodsDcRate'] = round((100 * $dcPrice) / $goodsPrice);
                $goodsList[$key]['couponDcRate'] = round((100 * $couponDcPrice) / $goodsPrice);
            }
        }

        return $goodsList;
    }

    /**
     * 추가 표시 데이터 설정
     *
     * @param array $designEditorData 디자인 에디터 데이터
     * @param string $ndeWidgetKey 위젯 고유 키
     * @return void
     */
    protected function setAdditionalDisplayData(array $designEditorData, string $ndeWidgetKey): void
    {
        // 품절상품 정책
        $soldoutDisplay = gd_policy('soldout.pc');
        $soldoutDisplay = $this->parseSoldoutImagePaths($soldoutDisplay);
        $this->setData('soldoutDisplay', $soldoutDisplay);

        // 마일리지 정책
        $mileage = gd_mileage_give_info();
        $this->setData('mileageData', $mileage['info'] ?? null);

        // 장바구니 정책
        $cartInfo = gd_policy('order.cart');
        $this->setData('cartInfo', $cartInfo);

        // 위젯 정보
        $this->setData('displayFl', true);
        $this->setData('ndeWidgetKey', $ndeWidgetKey);

        // 템플릿 정의
        $templatePath = 'goods/list/list_nde_' . $designEditorData['goodsListType'] . '.html';
        $this->getView()->setDefine('goodsTemplate', $templatePath);
    }

    /**
     * 페이지네이션 데이터 설정
     *
     * @return void
     */
    protected function setPaginationData(): void
    {
        $page = \App::load('\\Component\\Page\\Page');
        $this->setData('showPagination', true);
        $this->setData('pagination', $page->getPage('#'));
    }

    /**
     * 품절 이미지 경로 파싱
     *
     * @param array $soldoutDisplay 품절 정책 데이터
     * @return array
     */
    protected function parseSoldoutImagePaths(array $soldoutDisplay): array
    {
        $imageKeys = ['soldout_icon_img', 'soldout_price_img'];

        foreach ($imageKeys as $key) {
            if (empty($soldoutDisplay[$key])) {
                continue;
            }

            $pathParts = explode(DIRECTORY_SEPARATOR, $soldoutDisplay[$key]);
            $soldoutDisplay[$key . '_filename'] = array_pop($pathParts);
        }

        return $soldoutDisplay;
    }
}
