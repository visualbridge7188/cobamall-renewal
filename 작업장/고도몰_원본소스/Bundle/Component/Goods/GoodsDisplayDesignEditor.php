<?php
/*
 * Copyright (C) 2026 NHN COMMERCE. - All Rights Reserved
 *
 * Unauthorized copying or redistribution of this file in source and binary forms via any medium
 * is strictly prohibited.
 */

namespace Bundle\Component\Goods;

class GoodsDisplayDesignEditor
{
    /** @var int 페이지당 상품 수 */
    const GOODS_PER_PAGE = 20;

    /** @var string 기본 이미지 코드 */
    const DEFAULT_IMAGE_CODE = 'main';

    /** @var string 상품 소스 타입: 기획전 */
    const SOURCE_TYPE_PROMOTION = 'promotion';

    /** @var string 상품 소스 타입: 카테고리 */
    const SOURCE_TYPE_CATEGORY = 'category';

    /** @var string 상품 소스 타입: 외부 주입 데이터 */
    const SOURCE_TYPE_EXTERNAL = 'external';

    /** @var array 외부 주입 시 기본 테마 설정 */
    const EXTERNAL_DEFAULT_THEME = [
        'lineCnt' => '4',
        'iconFl' => 'y',
        'soldOutIconFl' => 'y',
        'displayField' => [
            'img',
            'brandCd',
            'makerNm',
            'goodsNm',
            'fixedPrice',
            'goodsPrice',
            'coupon',
            'mileage',
            'shortDescription',
        ],
        'goodsDiscount' => ['goods', 'coupon'],
        'displayAddField' => ['dcRate'],
        'priceStrike' => ['fixedPrice'],
    ];

    /** @var array 디자인 에디터 기본 설정값 */
    const DESIGN_EDITOR_DEFAULTS = [
        // 상품 소스 설정
        'goodsSourceType' => self::SOURCE_TYPE_CATEGORY,
        'goodsCategoryCode' => '',
        'goodsPromotionCode' => '',

        // 진열 설정
        'goodsListType' => 'gallery',
        'goodsImageRatio' => '1:1',
        'goodsDesktopColumnsCount' => '4',
        'goodsMobileColumnsCount' => '2',
        'goodsDisplayMode' => 'none',
        'goodsPerPage' => '20',
        'goodsSoldOutToBack' => 'false',

        // 상품 정보 표시 설정
        'goodsShowName' => 'true',
        'goodsShowPrice' => 'true',
        'goodsShowShortDescription' => 'true',
        'goodsShowLike' => 'true',
        'goodsShowCart' => 'true',
        'goodsShowBrand' => 'true',
        'goodsShowColor' => 'true',
        'goodsShowCode' => 'true',
        'goodsShowModelNo' => 'true',
        'goodsShowMileage' => 'true',

        // 슬라이드 설정
        'goodsSlideTransitionSpeed' => '3',
    ];

    /**
     * 요청 파라미터로부터 디자인 에디터 데이터를 복원
     *
     * @param array $requestData 요청 파라미터 배열
     * @return array 디자인 에디터 데이터
     */
    public function buildDesignEditorDataFromRequest(array $requestData): array
    {
        return array_merge(
            self::DESIGN_EDITOR_DEFAULTS,
            array_intersect_key($requestData, self::DESIGN_EDITOR_DEFAULTS)
        );
    }

    /**
     * 기획전 상품 로딩
     *
     * @param int $sno 기획전 테마 번호
     * @param array $options 옵션
     *     - 'displayCnt' (int): 표시 상품 수 (기본: GOODS_PER_PAGE)
     *     - 'soldOutToBack' (bool): 품절상품 뒤로 정렬 (기본: true)
     *     - 'usePage' (bool): 페이지네이션 활성화 - totalPage 계산 (기본: false)
     *     - 'groupSno' (int): 기획전 그룹 번호 (기본: 0)
     * @return array|null ['goodsList' => array, 'themeInfo' => array, 'totalPage' => int|null]
     */
    public function loadPromotionGoods(int $sno, array $options = []): ?array
    {
        $displayCnt = $options['displayCnt'] ?? self::GOODS_PER_PAGE;
        $soldOutToBack = $options['soldOutToBack'] ?? true;
        $usePage = $options['usePage'] ?? false;
        $groupSno = (int)($options['groupSno'] ?? 0);

        if ($sno <= 0) {
            return null;
        }

        $goods = \App::load('\\Component\\Goods\\Goods');
        $displayConfig = \App::load('\\Component\\Display\\DisplayConfig');

        // 진열 테마 정보 조회
        $promotionThemeInfo = $goods->getDisplayThemeInfo($sno);

        // 기획전 그룹형 그룹정보 로드
        if ($groupSno > 0) {
            $eventGroup = \App::load('\\Component\\Promotion\\EventGroupTheme');
            $promotionThemeInfo = $eventGroup->replaceEventData($groupSno, $promotionThemeInfo);
        }

        if (empty($promotionThemeInfo) || $promotionThemeInfo['displayFl'] !== 'y') {
            return null;
        }

        // 테마 설정 조회 및 파싱
        $themeInfo = $this->parsePromotionThemeInfo($promotionThemeInfo, $displayConfig);
        if (empty($themeInfo)) {
            return null;
        }

        // 상품 번호 목록 추출
        $goodsNoData = $this->extractGoodsNoData($promotionThemeInfo);
        if (empty($goodsNoData)) {
            return null;
        }

        // 정렬 순서 설정
        $displayOrder = $this->buildPromotionDisplayOrder($promotionThemeInfo, $goodsNoData, $soldOutToBack);

        // 조회 옵션 추출 (스킨에서 노출 여부를 제어하므로 전체 데이터 조회)
        $imageCd = $themeInfo['imageCd'] ?? self::DEFAULT_IMAGE_CODE;

        // 디자인 에디터 스킨: 품절상품 무조건 노출 (노출 여부는 스킨에서 제어)
        $soldOutFl = true;

        // 테마 설정 적용 (Goods 컴포넌트 내부 할인/가격 계산에 필요)
        $goods->setThemeConfig($themeInfo);

        // 상품 목록 조회
        \Request::get()->set('goodsNo', explode(INT_DIVISION, $goodsNoData));
        $goodsData = $goods->getGoodsSearchList(
            $displayCnt, $displayOrder, $imageCd,
            true, $soldOutFl, true, true,
            $displayCnt, false, $usePage
        );
        \Request::get()->del('goodsNo');

        $goodsList = $goodsData['listData'] ?? [];
        if (empty($goodsList)) {
            return null;
        }

        // 할인가 계산
        $goodsList = $this->calculateGoodsDcPrice($goodsList, $goods);

        $result = [
            'goodsList' => $goodsList,
            'themeInfo' => $themeInfo,
        ];

        // 페이지네이션 정보
        if ($usePage) {
            $page = \App::load('\\Component\\Page\\Page');
            $result['totalPage'] = $page->page['total'] ?? 1;
        }

        return $result;
    }

    /**
     * 카테고리 상품 로딩
     *
     * @param string $cateCd 카테고리 코드
     * @param array $options 옵션
     *     - 'displayCnt' (int): 표시 상품 수 (기본: GOODS_PER_PAGE)
     *     - 'soldOutToBack' (bool): 품절상품 뒤로 정렬 (기본: false)
     *     - 'usePage' (bool): 페이지네이션 활성화 - totalPage 계산 (기본: false)
     * @return array|null ['goodsList' => array, 'themeInfo' => array, 'totalPage' => int|null]
     */
    public function loadCategoryGoods(string $cateCd, array $options = []): ?array
    {
        $displayCnt = $options['displayCnt'] ?? self::GOODS_PER_PAGE;
        $soldOutToBack = $options['soldOutToBack'] ?? false;
        $usePage = $options['usePage'] ?? false;

        if (empty($cateCd)) {
            return null;
        }

        try {
            $goods = \App::load('\\Component\\Goods\\Goods');
            $category = \App::load('\\Component\\Category\\Category');

            // 카테고리의 테마 정보 조회
            $themeInfo = $category->getCategoryGoodsList($cateCd);
            if (empty($themeInfo) || empty($themeInfo['themeCd'])) {
                return null;
            }

            // 디자인 에디터 스킨: 품절상품 무조건 노출 (노출 여부는 스킨에서 제어)
            $category->setCateSoldOutFl('y');
            $goods->setSoldOutDisplayFl($themeInfo['soldOutDisplayFl']);

            // 정렬 순서 설정
            $displayOrder = $this->buildCategoryDisplayOrder($themeInfo, $goods, $cateCd, $soldOutToBack);

            // 조회 옵션 추출 (스킨에서 노출 여부를 제어하므로 전체 데이터 조회)
            $imageCd = $themeInfo['imageCd'] ?? self::DEFAULT_IMAGE_CODE;

            // 디자인 에디터 스킨: 품절상품 무조건 노출 (노출 여부는 스킨에서 제어)
            $soldOutFl = true;

            // 테마 설정 적용
            $goods->setThemeConfig($themeInfo);

            // 상품 목록 조회
            $goodsData = $goods->getGoodsList(
                $cateCd,
                self::SOURCE_TYPE_CATEGORY,
                $displayCnt,
                $displayOrder,
                $imageCd,
                true,
                $soldOutFl,
                true,
                true,
                $displayCnt,
                false,
                $usePage
            );
        } catch (\Exception $e) {
            return null;
        }

        $goodsList = $goodsData['listData'] ?? [];
        if (empty($goodsList)) {
            return null;
        }

        // 할인가 계산
        $goodsList = $this->calculateGoodsDcPrice($goodsList, $goods);

        $result = [
            'goodsList' => $goodsList,
            'themeInfo' => $themeInfo,
        ];

        // 페이지네이션 정보
        if ($usePage) {
            $page = \App::load('\\Component\\Page\\Page');
            $result['totalPage'] = $page->page['total'] ?? 1;
        }

        return $result;
    }

    /**
     * 기획전 테마 정보 파싱
     *
     * @param array $promotionThemeInfo 기획전 테마 정보
     * @param object $displayConfig 디스플레이 설정 컴포넌트
     * @return array|null
     */
    protected function parsePromotionThemeInfo(array $promotionThemeInfo, $displayConfig): ?array
    {
        $themeCd = $promotionThemeInfo['themeCd'] ?? '';

        $themeConfig = empty($themeCd)
            ? ($displayConfig->getInfoThemeConfigCate('B')[0] ?? null)
            : $displayConfig->getInfoThemeConfig($themeCd);

        if (empty($themeConfig)) {
            return null;
        }

        // 기획전 정보와 테마 설정 병합 (기획전 정보 우선)
        $themeInfo = array_merge($themeConfig, $promotionThemeInfo);

        // 직렬화된 상세 설정 파싱
        if (!empty($themeInfo['detailSet'])) {
            $themeInfo['detailSet'] = unserialize($themeInfo['detailSet']);
        }

        // 콤마 구분 필드를 배열로 변환
        $arrayFields = ['displayField', 'goodsDiscount', 'priceStrike', 'displayAddField'];
        foreach ($arrayFields as $field) {
            if (!empty($themeInfo[$field]) && is_string($themeInfo[$field])) {
                $themeInfo[$field] = explode(',', $themeInfo[$field]);
            } else {
                $themeInfo[$field] = $themeInfo[$field] ?? [];
            }
        }

        return $themeInfo;
    }

    /**
     * 상품 번호 데이터 추출
     *
     * @param array $promotionThemeInfo 기획전 테마 정보
     * @return string
     */
    protected function extractGoodsNoData(array $promotionThemeInfo): string
    {
        $goodsNoRaw = $promotionThemeInfo['goodsNo'] ?? '';
        if (empty($goodsNoRaw)) {
            return '';
        }

        $goodsNoArray = explode(STR_DIVISION, $goodsNoRaw);
        $filteredArray = gd_array_filter($goodsNoArray);

        return gd_implode(INT_DIVISION, $filteredArray);
    }

    /**
     * 기획전 정렬 순서 생성
     *
     * @param array $promotionThemeInfo 기획전 테마 정보
     * @param string $goodsNoData 상품 번호 데이터
     * @param bool $soldOutToBack 품절상품 뒤로 정렬 여부
     * @return string
     */
    protected function buildPromotionDisplayOrder(array $promotionThemeInfo, string $goodsNoData, bool $soldOutToBack): string
    {
        $orderParts = [];

        if ($soldOutToBack) {
            $orderParts[] = "soldOut asc";
        }

        // 수동 정렬: FIELD 함수로 등록 순서 유지 / 자동 정렬: 설정된 정렬 조건 사용
        if (($promotionThemeInfo['sortAutoFl'] ?? '') === 'n') {
            $goodsNoList = str_replace(INT_DIVISION, ',', $goodsNoData);
            $orderParts[] = "FIELD(g.goodsNo,{$goodsNoList})";
        } else {
            $orderParts[] = $promotionThemeInfo['sort'] ?? 'g.goodsNo desc';
        }

        return implode(',', $orderParts);
    }

    /**
     * 카테고리 정렬 순서 생성
     *
     * @param array $themeInfo 테마 정보
     * @param object $goods 상품 컴포넌트
     * @param string $cateCd 카테고리 코드
     * @param bool $soldOutToBack 품절상품 뒤로 정렬 여부
     * @return array
     */
    protected function buildCategoryDisplayOrder(array $themeInfo, $goods, string $cateCd, bool $soldOutToBack): array
    {
        $displayOrder = [];

        if ($soldOutToBack) {
            $displayOrder[] = "soldOut asc";
        }

        $goodsListSortLinkFl = $goods->getGoodsListSortLinkFl('cate', $cateCd);

        // 고정 정렬 적용
        if ($goodsListSortLinkFl['fixSortCnt']) {
            $displayOrder[] = "gl.fixSort desc";
        }

        // 자동 정렬 또는 수동 정렬 적용
        if ($themeInfo['sortAutoFl'] === 'y') {
            $displayOrder[] = gd_isset($themeInfo['sortType'], 'gl.goodsNo desc');
        } elseif ($goodsListSortLinkFl['goodsSortCnt']) {
            $goods->setGoodsSortFl('y');
            $displayOrder[] = "gl.goodsSort desc";
        }

        return $displayOrder;
    }

    /**
     * 상품 할인가 계산
     *
     * @param array $goodsList 상품 목록
     * @param object $goods 상품 컴포넌트
     * @return array
     */
    protected function calculateGoodsDcPrice(array $goodsList, $goods): array
    {

        foreach ($goodsList as $key => $item) {
            $goodsList[$key]['goodsDcPrice'] = $goods->getGoodsDcPrice($item);
        }

        return $goodsList;
    }
}
