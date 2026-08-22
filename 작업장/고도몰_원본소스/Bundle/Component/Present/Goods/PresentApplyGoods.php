<?php
/*
 * Copyright (C) 2025 NHN COMMERCE. - All Rights Reserved
 *
 * Unauthorized copying or redistribution of this file in source and binary forms via any medium is strictly prohibited.
 */

namespace Bundle\Component\Present\Goods;

use Component\Present\Goods\BasePresentGoods;
use Component\Goods\Goods;
use Framework\Log\Logger;
use Component\Present\PresentApplyTypeInterface;
use Repository\Present\Goods\PresentApplyGoodsRepository;
use Repository\Present\Goods\PresentExceptGoodsRepository;
use Repository\Present\Category\PresentCategoryRepository;

class PresentApplyGoods extends BasePresentGoods implements PresentApplyTypeInterface
{

    public function __construct(
        protected Logger $logger,
        protected Goods $goods,
        protected PresentApplyGoodsRepository $presentApplyGoodsRepository,
        protected PresentExceptGoodsRepository $presentExceptGoodsRepository,
        protected PresentCategoryRepository $presentCategoryRepository,
    )
    {
        parent::__construct($logger, $goods, $presentApplyGoodsRepository);
    }

    /**
     * 저장된 선물하기 카테고리, 예외상품 삭제
     *
     * @return void
     */
    public function clearExclusiveConfig(): void
    {
        $this->presentExceptGoodsRepository->deleteAll();
        $this->presentCategoryRepository->deleteAll();
    }
}
