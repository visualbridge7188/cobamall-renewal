<?php
/*
 * Copyright (C) 2025 NHN COMMERCE. - All Rights Reserved
 *
 * Unauthorized copying or redistribution of this file in source and binary forms via any medium
 * is strictly prohibited.
 */

namespace Bundle\Repository\RegularDelivery\RegularOrder;

use DTO\RegularDelivery\RegularOrder\RegularOrderShippingAddressModifyDTO;
use DTO\RegularDelivery\RegularOrder\RegularOrderShippingAddressRegistDTO;
use Origin\Model\RegularDelivery\RegularOrder\RegularOrderShippingAddress;

class RegularOrderShippingAddressRepository
{
    const DEFAULT_PAGE_SIZE = 5;

    /**
     * 정기배송 배송지 리스트 (5개씩 출력)
     *
     * SELECT *
     *  FROM es_regularOrderShippingAddress
     *  WHERE memNo = ?
     *  ORDER BY defaultFl DESC, regDt DESC
     *  LIMIT 5
     *
     * @param int $memNo
     * @param int $pageNum
     * @param int $pageSize
     * @return array
     */
    public function getShippingAddressListByMemNo(int $memNo, int $pageNum, int $pageSize = self::DEFAULT_PAGE_SIZE): array
    {
        return RegularOrderShippingAddress::query()
            ->where('memNo', '=', $memNo)
            ->orderBy('defaultFl', 'DESC')
            ->orderBy('regDt', 'DESC')
            ->skip(($pageNum - 1) * $pageSize)
            ->take($pageSize)
            ->get()
            ->toArray();
    }

    /**
     * 회원의 정기배송 목록 총 개수
     *
     * SELECT count(*) FROM es_regularOrderShippingAddress WHERE memNo = ?
     *
     * @param int $memNo
     * @return int
     */
    public function countTotalShippingAddressListByMemNo(int $memNo): int
    {
        return RegularOrderShippingAddress::query()
            ->where('memNo', '=', $memNo)
            ->count();
    }

    /**
     * SNO 로 배송지 정보 조회
     *
     * SELECT * FROM es_regularOrderShippingAddress WHERE sno = ?
     *
     * @param int $sno
     * @param int $memNo
     * @return array
     */
    public function getShippingAddressInfoBySno(int $sno, int $memNo): array
    {
        $shippingAddress = RegularOrderShippingAddress::query()
            ->where('sno', '=', $sno)
            ->where('memNo', '=', $memNo)
            ->first();

        if ($shippingAddress) {
            return $shippingAddress->toArray();
        }

        return [];
    }

    /**
     * 배송지 등록
     *
     * INSERT INTO es_regularOrderShippingAddress
     * (defaultFl, memNo, shippingTitle, shippingName, shippingZonecode, shippingAddress, shippingAddressSub, shippingMessage, shippingPhone, shippingCellPhone, regDt)
     * VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
     *
     * @param RegularOrderShippingAddressRegistDTO $dto
     * @return int
     */
    public function registerShippingAddress(RegularOrderShippingAddressRegistDTO $dto) :int
    {
        return RegularOrderShippingAddress::insertGetId($dto->toArray());
    }

    /**
     * 배송지 수정
     *
     * UPDATE es_regularOrderShippingAddress
     *  SET shippingTitle = ?,
     *      shippingName = ?,
     *      shippingZonecode = ?,
     *      shippingAddress = ?,
     *      shippingAddressSub = ?,
     *      shippingMessage = ?,
     *      shippingPhone = ?,
     *      shippingCellPhone = ?,
     *      modDt = ?
     * WHERE sno = ?
     *
     * @param RegularOrderShippingAddressModifyDTO $dto
     * @return bool
     */
    public function modifyShippingAddress(RegularOrderShippingAddressModifyDTO $dto): bool
    {
        return RegularOrderShippingAddress::where('sno', $dto->sno)
            ->update($dto->toArray());
    }

    /**
     * 기본 배송지를 가지고 있는 sno 반환
     *
     * SELECT sno FROM es_regularOrderShippingAddress where memNo = ? AND defaultFl = 'y'
     *
     * @return int|null
     */
    public function findDefaultFlSno(int $memNo)
    {
        return RegularOrderShippingAddress::query()
            ->where('defaultFl', '=', 'y')
            ->where('memNo', '=', $memNo)
            ->value('sno');
    }

    /**
     * 기본 배송지 플래그 업데이트
     *
     * UPDATE es_regularOrderShippingAddress SET defaultFl = ? WHERE sno = ?
     *
     * @param int $sno
     * @param string $flag
     * @return bool
     */
    public function updateDefaultFl(int $sno, string $flag): bool
    {
        return RegularOrderShippingAddress::where('sno', $sno)
            ->update(['defaultFl' => $flag]);
    }

    /**
     * 배송지 삭제
     *
     * DELETE FROM es_regularOrderShippingAddress WHERE sno = ?
     *
     * @param int $sno
     * @return bool
     */
    public function deleteShippingAddress(int $sno): bool
    {
        return RegularOrderShippingAddress::where('sno', $sno)
            ->delete();
    }

    /**
     * 회원 전체 배송목록 조회
     *
     * SELECT *
     *  FROM es_regularOrderShippingAddress
     *  WHERE memNo = ?
     *  ORDER BY defaultFl DESC
     *
     * @param int $memNo
     * @return array
     */
    public function getAllShippingAddressListByMemNo(int $memNo): array
    {
        return RegularOrderShippingAddress::query()
            ->where('memNo', $memNo)
            ->orderBy('defaultFl', 'DESC')
            ->orderBy('regDt', 'DESC')
            ->get()
            ->toArray();
    }

    /**
     *
     * 신청번호에 해당하는 사용자의 정기결제 배송지 조회
     *
     * SELECT es_regularOrderShippingAddress.*
     * FROM es_regularOrderShippingAddress
     * JOIN es_regularOrder ON es_regularOrder.memNo = es_regularOrderShippingAddress.memNo
     * JOIN es_regularOrderGoods ON es_regularOrderGoods.applyGroupNo = es_regularOrder.applyGroupNo
     * WHERE es_regularOrderGoods.applyNo = ?
     * ORDER BY es_regularOrderShippingAddress.defaultFl DESC;
     *
     * @param int $applyNo
     * @return array
     */
    public function getTotalShippingListByApplyNo(int $applyNo): array
    {
        return RegularOrderShippingAddress::query()
            ->select('es_regularOrderShippingAddress.*')
            ->join('es_regularOrder', 'es_regularOrder.memNo', '=', 'es_regularOrderShippingAddress.memNo')
            ->join('es_regularOrderGoods', 'es_regularOrderGoods.applyGroupNo', '=', 'es_regularOrder.applyGroupNo')
            ->where('es_regularOrderGoods.applyNo', $applyNo)
            ->orderBy('es_regularOrderShippingAddress.defaultFl', 'DESC')
            ->get()
            ->toArray();
    }

    /**
     * 정기결제 배송지 정보 조회
     *
     * SELECT * FROM es_regularOrderShippingAddress WHERE sno = ?
     *
     * @param int $sno
     * @return array
     */
    public function findShippingAddressBySno(int $sno): array
    {
        $shippingAddress = RegularOrderShippingAddress::query()
            ->where('sno', $sno)
            ->first();

        return $shippingAddress ? $shippingAddress->toArray() : [];
    }
}
