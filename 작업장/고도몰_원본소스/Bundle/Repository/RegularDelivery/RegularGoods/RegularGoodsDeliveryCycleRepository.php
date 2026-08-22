<?php
/*
 * Copyright (C) 2025 NHN COMMERCE. - All Rights Reserved
 *
 * Unauthorized copying or redistribution of this file in source and binary forms via any medium
 * is strictly prohibited.
 */

namespace Bundle\Repository\RegularDelivery\RegularGoods;

use Origin\Model\RegularDelivery\RegularGoods\RegularGoodsDeliveryCycle;

class RegularGoodsDeliveryCycleRepository
{
    /**
     * regularGoodsDeliveryCycle insert
     *
     * INSERT INTO es_regularGoodsDeliveryCycle (....) VALUES (....),(....),....
     *
     * @param array $regularGoodsDeliveryCycle
     * @return void
     */
    public function insertRegularGoodsDeliveryCycleList(array $regularGoodsDeliveryCycle)
    {
        RegularGoodsDeliveryCycle::query()
            ->insert($regularGoodsDeliveryCycle);
    }

    /**
     * regularGoodsSno를 기준으로 데이터 조회
     *
     * SELECT * FROM es_regularGoodsDeliveryCycle WHERE regularGoodsSno = ?
     *
     * @param int $regularGoodsSno : 기준이 되는 regularGoodsSno
     * @return array
     */
    public function findRegularGoodsDeliveryCycleByRegularGoodsSno(int $regularGoodsSno): array
    {
        return RegularGoodsDeliveryCycle::query()
            ->where('regularGoodsSno', '=', $regularGoodsSno)
            ->get()
            ->toArray();
    }

    /**
     * regularGoodsSno를 기준으로 데이터 삭제
     *
     * @param int $regularGoodsSno
     * @return void
     */
    public function deleteRegularGoodsDeliveryCycleByRegularGoodsSno(int $regularGoodsSno)
    {
        RegularGoodsDeliveryCycle::query()
            ->where('regularGoodsSno', '=', $regularGoodsSno)
            ->delete();
    }

    /**
     * regularGoodsSno를 기준으로 es_regularGoodsDeliveryCycle에 대한 데이터 조회
     *
     * SELECT *
     * FROM es_regularGoodsDeliveryCycle
     * WHERE regularGoodsSno IN (...)
     * ORDER BY regularGoodsSno;
     *
     * @param array $regularGoodsSnoList
     * @return array
     */
    public function findRegularGoodsDeliveryCycleByRegularGoodsSnoList(array $regularGoodsSnoList): array
    {
        return RegularGoodsDeliveryCycle::whereIn('regularGoodsSno', $regularGoodsSnoList)
            ->select('regularGoodsSno', 'monthCycle', 'weekCycle', 'weekDayCycle')
            ->get()
            ->toArray();
    }
}
