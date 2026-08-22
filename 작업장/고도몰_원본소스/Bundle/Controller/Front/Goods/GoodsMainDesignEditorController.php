<?php
/*
 * Copyright (C) 2026 NHN COMMERCE. - All Rights Reserved
 *
 * Unauthorized copying or redistribution of this file in source and binary forms via any medium
 * is strictly prohibited.
 */

namespace Bundle\Controller\Front\Goods;

use Request;

class GoodsMainDesignEditorController extends \Controller\Front\Controller
{
    public function index()
    {
        $getValue = gd_htmlspecialchars(Request::get()->toArray());

        $goodsDisplay = \App::load('\\Component\\Goods\\GoodsDisplayDesignEditor');

        // 디자인 에디터 데이터 복원
        $designEditorData = $goodsDisplay->buildDesignEditorDataFromRequest($getValue);
        $this->setData('goodsListType', $designEditorData['goodsListType']);
        $this->setData('designEditorData', $designEditorData);

        $sourceType = $designEditorData['goodsSourceType'];
        $perPage = max(1, (int)$designEditorData['goodsPerPage']);
        $soldOutToBack = $designEditorData['goodsSoldOutToBack'] === 'true';

        // displayMode별 표시 수/페이징 분기
        $displayMode = $designEditorData['goodsDisplayMode'] ?: 'none';
        if ($displayMode === 'loadMore') {
            // 더보기: 누적 로딩 (more=N → perPage * N개)
            $displayCnt = $perPage * max(1, (int)($getValue['more'] ?? 1));
            $usePage = false;
        } elseif ($displayMode === 'pagination') {
            // 페이지네이션: 페이지 단위 로딩 (page 파라미터는 Page 컴포넌트가 자동 처리)
            $displayCnt = $perPage;
            $usePage = true;
        } else {
            // goodsDisplayMode 미설정: perPage 만큼 로딩
            $displayCnt = $perPage;
            $usePage = false;
        }

        // sourceType별 상품 로딩
        $result = null;
        if ($sourceType === $goodsDisplay::SOURCE_TYPE_PROMOTION) {
            $sno = (int)($designEditorData['goodsPromotionCode'] ?: ($getValue['sno'] ?? 0));
            Request::get()->set('isMain', true);
            $result = $goodsDisplay->loadPromotionGoods($sno, [
                'displayCnt' => $displayCnt,
                'soldOutToBack' => $soldOutToBack,
                'usePage' => $usePage,
                'groupSno' => (int)($getValue['groupSno'] ?? 0),
            ]);
        } elseif ($sourceType === $goodsDisplay::SOURCE_TYPE_CATEGORY) {
            $cateCd = $designEditorData['goodsCategoryCode'] ?: ($getValue['sno'] ?? '');
            $result = $goodsDisplay->loadCategoryGoods($cateCd, [
                'displayCnt' => $displayCnt,
                'soldOutToBack' => $soldOutToBack,
                'usePage' => $usePage,
            ]);
        }

        // 공통 데이터 설정
        if ($result) {
            $this->setData('goodsList', array_chunk($result['goodsList'], $displayCnt));
            $this->setData('themeInfo', $result['themeInfo']);
            $this->setData('soldoutDisplay', gd_policy('soldout.pc'));
            $mileage = gd_mileage_give_info();
            $this->setData('mileageData', $mileage['info'] ?? null);

            // 페이지네이션 모드: Page 컴포넌트로 pagination HTML 생성
            if ($usePage) {
                $page = \App::load('\\Component\\Page\\Page');
                $this->setData('pagination', $page->getPage('#'));
            }
        }

        $this->getView()->setPageName('goods/list/list_nde_ajax');
    }
}
