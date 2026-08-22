<?php
/*
 * Copyright (C) 2025 NHN COMMERCE. - All Rights Reserved
 *
 * Unauthorized copying or redistribution of this file in source and binary forms via any medium
 * is strictly prohibited.
 */

namespace Bundle\Component\RegularDelivery\RegularOrderAdmin;

use Origin\Enum\RegularDelivery\RegularOrder\RegularOrderStatus;
use Repository\RegularDelivery\RegularOrder\RegularOrderGoodsRepository;

class RegularOrderAdminPolicy
{
    private $regularOrderGoodsRepository;

    public function __construct(
        RegularOrderGoodsRepository $regularOrderGoodsRepository
    )
    {
        $this->regularOrderGoodsRepository = $regularOrderGoodsRepository;
    }

    /**
     * 이용중인 정기결제(배송) 신청서 조회
     * @return int
     */
    public function countRegularOrderGoodsToAbled(): int
    {
        return $this->regularOrderGoodsRepository->countRegularOrderGoodsByApplyStatus(RegularOrderStatus::ACTIVE);
    }
}
