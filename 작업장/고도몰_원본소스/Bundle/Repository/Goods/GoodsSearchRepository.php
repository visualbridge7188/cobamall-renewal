<?php

namespace Bundle\Repository\Goods;

use Origin\Model\Goods\GoodsSearch;
use Exception;

class GoodsSearchRepository
{
    /**
     * 원상품의 PC/MO 판매상태, 노출 상태에 대해 판매안함 및 노출안함으로 변경
     *
     * UPDATE es_goodsSearch
     * SET
     * goodsDisplayFl = 'n',
     * goodsDisplayMobileFl = 'n',
     * goodsSellFl = 'n',
     * goodsSellMobileFl = 'n',
     * modDt = ?
     * WHERE goodsNo IN (?, ?, ?, ...);
     *
     * @param array $goodsNoList
     * @param string $modDt
     * @return void
     */
    public function updateDisplayAndSellByGoodsNo(array $goodsNoList, string $modDt)
    {
        GoodsSearch::whereIn('goodsNo', $goodsNoList)
            ->update([
                'goodsDisplayFl' => 'n',
                'goodsDisplayMobileFl' => 'n',
                'goodsSellFl' => 'n',
                'goodsSellMobileFl' => 'n',
                'modDt' => $modDt
            ]);
    }
}
