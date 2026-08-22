<?php
/*
 * Copyright (C) 2025 NHN COMMERCE. - All Rights Reserved
 *
 * Unauthorized copying or redistribution of this file in source and binary forms via any medium
 * is strictly prohibited.
 */

namespace Bundle\Repository\RegularDelivery\RegularOrder;

use DTO\RegularDelivery\RegularOrder\RegularOrderGoodsOptionTextDTO;
use Origin\Model\RegularDelivery\RegularOrder\RegularOrderGoodsOptionText;

class RegularOrderGoodsOptionTextRepository
{
    /**
     * 정기배송 신청 텍스트 옵션 정보 등록
     *
     * INSERT INTO es_regularOrderGoodsOption
     * (applyNo, regularGoodsNo, regularGoodsOptionTextPrice, regularGoodsOptionTextInfo, originGoodsOptionTextPrice, regDt)
     * VALUES (?, ?, ?, ?, ?, ?, ?)
     *
     * @param RegularOrderGoodsOptionTextDTO $dto
     * @return void
     */
    public function insertRegularOrderGoodsOptionText(RegularOrderGoodsOptionTextDTO $dto)
    {
        RegularOrderGoodsOptionText::query()
            ->insert([
                'applyNo' => $dto->getApplyNo(),
                'regularGoodsNo' => $dto->getRegularGoodsNo(),
                'regularGoodsOptionTextPrice' => $dto->getRegularGoodsOptionTextPrice(),
                'regularGoodsOptionTextInfo' => $dto->getRegularGoodsOptionTextInfo(),
                'originGoodsOptionTextPrice' => $dto->getOriginGoodsOptionTextPrice(),
                'regDt' => $dto->getRegDt()
            ]);
    }

    /**
     * 신청서 번호로 문구 옵션 정보 삭제
     *
     * DELETE FROM es_regularOrderGoodsOptionText WHERE applyNo = ?
     *
     * @param int $applyNo
     * @return void
     */
    public function deleteRegularOrderGoodsOptionTextByApplyNo(int $applyNo)
    {
        RegularOrderGoodsOptionText::query()
            ->where('applyNo', '=', $applyNo)
            ->delete();
    }
}
