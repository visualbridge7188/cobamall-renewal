<?php
/*
 * Copyright (C) 2025 NHN COMMERCE. - All Rights Reserved
 *
 * Unauthorized copying or redistribution of this file in source and binary forms via any medium
 * is strictly prohibited.
 */

namespace Bundle\Repository\RegularDelivery\RegularOrder;

use DTO\RegularDelivery\RegularOrder\RegularOrderDeliveryCreateDTO;
use Origin\Model\RegularDelivery\RegularOrder\RegularOrderDelivery;

class RegularOrderDeliveryRepository
{
    /**
     * 정기결제 신청서 배송관련 정보 저장
     * @param RegularOrderDeliveryCreateDTO $dto
     * @return void
     */
    public function insertRegularOrderDelivery(RegularOrderDeliveryCreateDTO $dto)
    {
        RegularOrderDelivery::query()
            ->insert([
                'applyNo' => $dto->getApplyNo(),
                'deliverySno' => $dto->getDeliverySno(),
                'scmNo' => $dto->getScmNo(),
                'deliveryPolicy' => $dto->getDeliveryPolicy(),
                'regDt' => date('Y-m-d H:i:s'),
            ]);
    }

    /**
     * 정기결제 신청서 배송비 조건 일련번호 조회
     *
     * SELECT deliverySno FROM es_regularOrderDelivery WHERE applyNo = ?
     *
     * @param int $applyNo
     * @return int
     */
    public function findRegularOrderDeliverySnoByApplyNo(int $applyNo): int
    {
        return RegularOrderDelivery::query()
            ->where('applyNo', $applyNo)
            ->value('deliverySno');
    }

    /**
     * 신청서 번호로 배송관련 정보 삭제
     *
     * DELETE FROM es_regularOrderDelivery WHERE applyNo = ?
     *
     * @param int $applyNo
     * @return void
     */
    public function deleteRegularOrderDeliveryByApplyNo(int $applyNo)
    {
        RegularOrderDelivery::query()
            ->where('applyNo', '=', $applyNo)
            ->delete();
    }

    /**
     * 신청번호로 본사가 아닌 공급사 번호 조회
     *
     * SELECT scmNo FROM es_regularOrderDelivery WHERE applyNo = ? AND scmNo != 1
     *
     * @param int $applyNo
     * @return int|null
     */
    public function findRegularOrderScmNoByApplyNo(int $applyNo)
    {
        return RegularOrderDelivery::query()
            ->where('applyNo', $applyNo)
            ->where('scmNo', '!=', DEFAULT_CODE_SCMNO)
            ->value('scmNo');
    }

    /**
     * 신청그룹번호로 본사가 아닌 공급사 번호 리스트 조회
     *
     * SELECT es_regularOrderDelivery.scmNo 
     * FROM es_regularOrderDelivery 
     * JOIN es_regularOrderGoods ON es_regularOrderDelivery.applyNo = es_regularOrderGoods.applyNo
     * WHERE es_regularOrderGoods.applyGroupNo = ? AND es_regularOrderDelivery.scmNo != 1
     *
     * @param int $applyGroupNo
     * @return array
     */
    public function findRegularOrderScmNoListByApplyGroupNo(int $applyGroupNo): array
    {
        return RegularOrderDelivery::query()
            ->select('es_regularOrderDelivery.scmNo')
            ->join('es_regularOrderGoods', 'es_regularOrderDelivery.applyNo', '=', 'es_regularOrderGoods.applyNo')
            ->where('es_regularOrderGoods.applyGroupNo', $applyGroupNo)
            ->where('es_regularOrderDelivery.scmNo', '!=', DEFAULT_CODE_SCMNO)
            ->get()
            ->toArray();
    }

    /**
     * 신청번호 리스트로 본사가 아닌 공급사 번호 리스트 조회
     *
     * SELECT scmNo FROM es_regularOrderDelivery WHERE applyNo IN (?) AND scmNo != 1
     *
     * @param array $applyNoList
     * @return array
     */
    public function findRegularOrderScmNoListByApplyNoList(array $applyNoList): array
    {
        return RegularOrderDelivery::query()
            ->whereIn('applyNo', $applyNoList)
            ->where('scmNo', '!=', DEFAULT_CODE_SCMNO)
            ->pluck('scmNo')
            ->unique()
            ->toArray();
    }
}
