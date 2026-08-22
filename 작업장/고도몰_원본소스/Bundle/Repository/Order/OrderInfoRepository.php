<?php
/*
 * Copyright (C) 2025 NHN COMMERCE. - All Rights Reserved
 *
 * Unauthorized copying or redistribution of this file in source and binary forms via any medium
 * is strictly prohibited.
 */

namespace Bundle\Repository\Order;

use Origin\Model\Order\OrderInfo;

class OrderInfoRepository
{
    /**
     * 주문번호로 주문자 정보 반환
     *
     * SELECT * FROM es_orderInfo
     *  WHERE orderNo = ?
     *
     * @param string $orderNo
     * @return array
     */
    public function findOrderInfoByOrderNo(string $orderNo): array
    {
        $orderInfo = OrderInfo::query()
            ->where('orderNo', $orderNo)
            ->first();

        return is_null($orderInfo) ? [] : $orderInfo->toArray();
    }

    /**
     * 주문 정보 수령자 정보 수정
     *
     * UPDATE es_orderInfo
     * SET receiverName = ?, receiverPhone = ?, receiverCellPhone = ?, receiverZipcode = ?, receiverZonecode = ?, receiverAddress = ?, receiverAddressSub = ?, modDt = ?
     * WHERE orderNo = ?
     *
     * @param string $orderNo
     * @param array $dto
     * @return void
     */
    public function updateReceiverInfo(string $orderNo, array $dto): void
    {
        OrderInfo::query()
            ->where('orderNo', $orderNo)
            ->update($dto);
    }
}

