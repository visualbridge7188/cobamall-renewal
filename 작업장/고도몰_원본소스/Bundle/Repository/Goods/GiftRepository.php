<?php
/*
 * Copyright (C) 2025 NHN COMMERCE. - All Rights Reserved
 *
 * Unauthorized copying or redistribution of this file in source and binary forms via any medium
 * is strictly prohibited.
 */

namespace Bundle\Repository\Goods;

use Origin\Model\Goods\Gift;

class GiftRepository
{
    /**
     * es_gift 테이블에서 특정 giftNo 리스트에 해당하는 데이터 조회
     *
     * SELECT giftNo, giftNm, imageNm, imagePath, scmNo, stockCnt, stockFl
     * 사은품 번호에 해당하는 사은품 상품 정보 조회
     *
     * SELECT *
     * FROM es_gift
     * WHERE giftNo IN (?, ?, ?, ?...);
     *
     *
     * @param array $giftNoList : 사은품 번호 리스트
     * @return array : 사은품 정보 array
     */
    public function findGiftInfoByGiftNo(array $giftNoList): array
    {
        return Gift::query()
            ->whereIn('giftNo', $giftNoList)
            ->get()
            ->toArray();
    }

    /**
     *
     * 사은품 번호에 해당하는 재고가 있는 사은품 상품 정보 조회
     *
     * SELECT giftNo, giftNm, scmNo, stockFl, stockCnt, imageNm, imagePath, imageStorage
     * FROM es_gift
     * WHERE giftNo IN (?, ?, ?, ?...)
     * AND stockFl = 'n' OR (stockFl = 'y' AND stockCnt > 0);
     *
     * @param array $multiGiftNo
     * @return array
     */
    public function getAvailableGiftInfoByGiftNos(array $multiGiftNo): array
    {
        return Gift::query()
            ->select(['giftNo', 'giftNm', 'scmNo', 'stockFl', 'stockCnt', 'imageNm', 'imagePath', 'imageStorage'])
            ->whereIn('giftNo', $multiGiftNo)
            ->where(function ($query) {
                $query->where('stockFl', 'n')
                    ->orWhere(function ($q) {
                        $q->where('stockFl', 'y')
                            ->where('stockCnt', '>', 0);
                    });
            })
            ->get()
            ->toArray();
    }

    /**
     * 사은품 번호에 해당하는 사은품명 조회
     *
     * SELECT giftNo, giftNm
     * FROM es_gift
     * WHERE giftNo IN (?, ?, ?, ?...);
     *
     * @param array $giftNoList
     * @return array
     */
    public function getGiftNmByGiftNo(array $giftNoList): array
    {
        return Gift::query()
            ->whereIn('giftNo', $giftNoList)
            ->select('giftNo', 'giftNm')
            ->get()
            ->toArray();
    }



    /**
     * 사은품 번호 리스트를 통해 페이징된 사은품 정보 조회
     *
     * SELECT giftNo, giftNm, imageNm, imagePath, imageStorage, stockFl, stockCnt, regDt
     * FROM Gift
     * WHERE giftNo IN (....)
     * LIMIT [offset], [limit];
     *
     * @param array $giftNoList : 조회할 사은품 번호 리스트
     * @param int $currentPageNum : 현재 페이지 번호
     * @param $pageSizeNum : 페이지 당 사은품 갯수
     * @return array
     */
    public function findGiftByGiftNoListWithPaging(array $giftNoList, int $currentPageNum, int $pageSizeNum): array
    {
        return Gift::query()
            ->select('giftNo', 'giftNm', 'imageNm', 'imagePath', 'imageStorage', 'stockFl', 'stockCnt', 'regDt')
            ->whereIn('giftNo', $giftNoList)
            ->forPage($currentPageNum, $pageSizeNum)
            ->get()
            ->toArray();
    }
}
