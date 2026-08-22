<?php
/*
 * Copyright (C) 2025 NHN COMMERCE. - All Rights Reserved
 *
 * Unauthorized copying or redistribution of this file in source and binary forms via any medium
 * is strictly prohibited.
 */

namespace Bundle\Repository\RegularDelivery\RegularOrder;

use DTO\RegularDelivery\RegularOrder\RegularOrderGoodsOptionDTO;
use Origin\Model\RegularDelivery\RegularOrder\RegularOrderGoodsOption;

class RegularOrderGoodsOptionRepository
{
    /**
     * 정기배송 신청 옵션 정보 등록
     *
     * INSERT INTO es_regularOrderGoodsOption
     * (applyNo, optionSno, regularGoodsNo, regularGoodsOptionPrice, regularGoodsOptionInfo, regularGoodsOptionCostPrice, regDt)
     * VALUES (?, ?, ?, ?, ?, ?, ?)
     *
     * @param RegularOrderGoodsOptionDTO $dto
     * @return void
     */
    public function insertRegularOrderGoodsOption(RegularOrderGoodsOptionDTO $dto)
    {
        RegularOrderGoodsOption::query()
            ->insert([
                'applyNo' => $dto->getApplyNo(),
                'optionSno' => $dto->getOptionSno(),
                'regularGoodsNo' => $dto->getRegularGoodsNo(),
                'regularGoodsOptionPrice' => $dto->getRegularGoodsOptionPrice(),
                'regularGoodsOptionInfo' => $dto->getRegularGoodsOptionInfo(),
                'originGoodsOptionPrice' => $dto->getOriginGoodsOptionPrice(),
                'regDt' => $dto->getRegDt()
            ]);
    }

    /**
     * 신청서 번호로 선택 옵션 삭제
     *
     * DELETE FROM es_regularOrderGoodsOption WHERE applyNo = ?
     *
     * @param int $applyNo
     * @return void
     */
    public function deleteRegularOrderGoodsOptionByApplyNo(int $applyNo)
    {
        RegularOrderGoodsOption::query()
            ->where('applyNo', '=', $applyNo)
            ->delete();
    }

    /**
     * 옵션 품절 확인
     *
     * SELECT 1
     * FROM es_regularOrderGoodsOption
     * INNER JOIN es_goodsOption ON es_goodsOption.sno = es_regularOrderGoodsOption.optionSno
     * INNER JOIN es_goods ON es_goods.goodsNo = es_goodsOption.goodsNo
     * WHERE es_regularOrderGoodsOption.applyNo IN (?, ?, ?...)
     * AND es_goods.optionFl = 'y'
     * AND (es_goodsOption.optionSellFl != 'y' OR (es_goods.stockFl = 'y' AND es_goodsOption.stockCnt <= 0))
     * LIMIT 1;
     *
     * 참고 : optionSellFl - (옵션판매여부 y:가능 n:품절 t:임시품절)
     *
     * 참고: 품절 상태
     * 1. optionSellFl != 'y' 인 경우
     * 2. es_goods.stockFl = 'y' AND es_goodsOption.stockCnt <= 0 인 경우
 *
     */
    public function existSoldOutGoodsOptionByApplyNoList(array $applyNoList): bool
    {
        return RegularOrderGoodsOption::query()
            ->join('es_goodsOption', 'es_goodsOption.sno', '=', 'es_regularOrderGoodsOption.optionSno')
            ->join('es_goods', 'es_goods.goodsNo', '=', 'es_goodsOption.goodsNo')
            ->whereIn('es_regularOrderGoodsOption.applyNo', $applyNoList)
            ->where('es_goods.optionFl', 'y')
            ->where(function ($query) {
                $query->where('es_goodsOption.optionSellFl', '!=', 'y')
                    ->orWhere(function ($subQuery) {
                        $subQuery->where('es_goods.stockFl', 'y')
                            ->where('es_goodsOption.stockCnt', '<=', 0);
                    });
            })
            ->exists();
    }


    /**
     * 옵션 번호를 기준으로 신청서 번호 조회
     *
     * SELECT es_regularOrderGoodsOption.applyNo
     * FROM es_regularOrderGoodsOption
     * WHERE es_regularOrderGoodsOption.optionSno = ?;
     *
     * @param int $optionSno : 옵션 번호
     * @return array
     */
    public function findApplyStatusByOptionSno(int $optionSno): array
    {
        return RegularOrderGoodsOption::query()
            ->select('es_regularOrderGoodsOption.applyNo', 'es_regularOrderGoods.applyStatus')
            ->join('es_regularOrderGoods', 'es_regularOrderGoodsOption.applyNo', '=', 'es_regularOrderGoods.applyNo')
            ->where('es_regularOrderGoodsOption.optionSno', $optionSno)
            ->get()
            ->toArray();
    }
}
