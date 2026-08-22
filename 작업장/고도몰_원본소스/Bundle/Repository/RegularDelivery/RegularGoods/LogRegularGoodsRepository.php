<?php
/*
 * Copyright (C) 2025 NHN COMMERCE. - All Rights Reserved
 *
 * Unauthorized copying or redistribution of this file in source and binary forms via any medium
 * is strictly prohibited.
 */

namespace Bundle\Repository\RegularDelivery\RegularGoods;

use Origin\Model\RegularDelivery\RegularGoods\LogRegularGoods;

class LogRegularGoodsRepository
{
    /**
     * logRegularGoods insert
     *
     * INSERT INTO es_logRegularGoods (....) VALUES (....),(....),....
     *
     * @param array $logRegularGoodsList : es_logRegularGoods에 insert 할 데이터 리스트
     * @return void
     */
    public function insertLogRegularGoodsList(array $logRegularGoodsList)
    {
        LogRegularGoods::query()
            ->insert($logRegularGoodsList);
    }
}
