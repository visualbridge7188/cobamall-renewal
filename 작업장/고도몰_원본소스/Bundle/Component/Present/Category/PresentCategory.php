<?php
/*
 * Copyright (C) 2025 NHN COMMERCE. - All Rights Reserved
 *
 * Unauthorized copying or redistribution of this file in source and binary forms via any medium is strictly prohibited.
 */

namespace Bundle\Component\Present\Category;

use Component\Category\Category;
use Component\Present\Goods\PresentExceptGoods;
use Component\Present\Goods\PresentTempExceptGoods;
use Component\Present\PresentApplyTypeInterface;
use Framework\Log\Logger;
use Repository\Present\Category\PresentCategoryRepository;
use Repository\Present\Goods\PresentApplyGoodsRepository;

class PresentCategory implements PresentApplyTypeInterface
{

    public function __construct(
        private readonly Logger $logger,
        private readonly Category $category,
        private readonly PresentExceptGoods $presentExceptGoods,
        private readonly PresentCategoryRepository $presentCategoryRepository,
        private readonly PresentApplyGoodsRepository $presentApplyGoodsRepository,
    )
    {
    }

    /**
     * 선물하기 설정 여부를 포함한 모든 카테고리 조회
     * @return array
     */
    public function getCategoryListWithChecked(): array
    {
        $categories = [];
        try {
            $cateData = $this->category->getCategoryData();
            $applyCategories = $this->getAll();
            $applyCateCd = array_map(fn($category) => $category['cateCd'], $applyCategories);

            $categories = array_map(
                fn($category) => [
                    'cateCd' => $category['cateCd'],
                    'cateNm' => $this->category->getCategoryPosition($category['cateCd']),
                    'checked' => in_array($category['cateCd'], $applyCateCd, true) ? 'checked' : '',
                ], $cateData
            );
        } catch (\Throwable $throwable) {
            $this->logger->channel('presentGoods')->warning(__METHOD__, ['message' => $throwable->getMessage()]);
        }

        return $categories;
    }

    /**
     * 선물하기 설정된 카테고리 조회
     * @return array
     */
    public function getAll(): array
    {
        return $this->presentCategoryRepository->findAll();
    }

    /**
     * 카테고리 코드를 통한 선물하기 설정 카테고리 조회
     * @param string $cateCd
     * @return array
     */
    public function getByCateCd(string $cateCd): array
    {
        return $this->presentCategoryRepository->findByCateCd($cateCd);
    }

    /**
     * 선택되지 않은 카테고리 삭제, 설정된 카테고리에서 없는 카테고리만 저장
     * @param array $datas - 상위 카테고리
     * @return bool
     * @throws \Throwable
     */
    public function save(array $datas): bool
    {
        $cateCds = [];
        $selectedCateCds = $datas['cateCd'] ?: [];
        if (!empty($selectedCateCds)) {
            $cateCds = $this->presentCategoryRepository->findChildCateCdsByParentCateCd($selectedCateCds);
        }
        $existedCategory = $this->presentCategoryRepository->findAll();
        $existedCateCds = array_map(fn($category) => $category['cateCd'], $existedCategory);

        $newCateCds = array_filter($cateCds, fn($cateCd) => !in_array($cateCd, $existedCateCds));
        $insertCategories = array_map(fn($cateCd) => ['cateCd' => $cateCd], $newCateCds);

        $deleteCategories = array_diff($existedCateCds, $cateCds);

        $this->remove($deleteCategories);
        if (!empty($insertCategories)) {
            $this->presentCategoryRepository->insert($insertCategories);
        }

        // 예외 상품 저장
        $this->presentExceptGoods->save($datas['goodsNo']);
        return true;
    }

    /**
     * 카테고리 삭제
     *
     * @param array $cateCds
     * @return void
     */
    public function remove(array $cateCds): void
    {
        if (!empty($cateCds)) {
            $this->presentCategoryRepository->deleteByCateCd($cateCds);
        }
    }

    /**
     * 선택 상품 데이터 삭제
     *
     * @return void
     */
    public function clearExclusiveConfig(): void
    {
        $this->presentApplyGoodsRepository->deleteAll();
    }
}
