<?php
/*
 * Copyright (C) 2025 NHN COMMERCE. - All Rights Reserved
 *
 * Unauthorized copying or redistribution of this file in source and binary forms via any medium
 * is strictly prohibited.
 */

namespace Bundle\Repository\RegularDelivery\RegularGoods;

use DTO\RegularDelivery\RegularGoods\RegularGoodsCreateDTO;
use DTO\RegularDelivery\RegularGoods\RegularGoodsDTO;
use Framework\Debug\Exception\DatabaseException;
use Illuminate\Database\Query\Expression;
use Origin\Model\RegularDelivery\RegularGoods\RegularGoods;
use Origin\Enum\RegularDelivery\RegularGoods\RegularGoodsAttribute;

class RegularGoodsRepository
{
    /**
     * regularGoods insert
     *
     * INSERT INTO es_regularGoods (....) VALUES (....),(....),....
     *
     * @param RegularGoodsCreateDTO $regularGoodsCreateDTO : es_regularGoods에 insert 할 데이터 리스트
     * @return void
     */
    public function insertRegularGoodsList(RegularGoodsCreateDTO $regularGoodsCreateDTO)
    {
        RegularGoods::query()
            ->insert($regularGoodsCreateDTO->getRegularGoodsCreateData());
    }

    /**
     * sno를 기준으로 데이터 업데이트
     *
     * UPDATE es_regularGoods
     * SET `컬럼명1` = ?, `컬럼명2` = ?, `컬럼명3` = ?, ...
     * WHERE `sno` = ?;
     *
     * @param int $sno : 수정할 es_regularGoods 데이터의 pk
     * @param array $regularGoods : 수정할 데이터
     * @return void
     */
    public function updateRegularGoodsBySno(int $sno, array $regularGoods)
    {
        RegularGoods::query()
            ->where('sno', $sno)
            ->update($regularGoods);
    }

    /**
     * 일반 상품 번호 리스트에 대해 정기결제(배송) 등록 여부 확인
     *
     * SELECT EXISTS (
     * SELECT 1
     * FROM es_regularGoods
     * WHERE goodsNo IN (?, ?, ?, ?...)
     * AND delFl = 'n'
     * ) AS result;
     *
     * @param array $goodsNoList : 정기결제(배송) 등록 여부를 확인할 일반 상품 번호 리스트
     * @return bool : 존재 여부
     */
    public function hasRegularGoodsByGoodsNoList(array $goodsNoList): bool
    {
        return RegularGoods::query()
            ->whereIn('goodsNo', $goodsNoList)
            ->where('delFl', '=', 'n')
            ->exists();
    }

    /**
     * goodsNo 리스트와 delFl 값을 기준으로 매치하는 sno 조회 홤수
     *
     * SELECT sno
     * FROM es_regularGoods
     * WHERE goodsNo IN (?, ?, ?, ?...)
     * AND delFl = 'n'
     *
     * @param array $goodsNoList : fk인 goodsNo 리스트
     * @return array : 조회된 sno 리스트
     */
    public function findSnoByGoodsNoListNotDelete(array $goodsNoList): array
    {
        return RegularGoods::query()
            ->select('sno')
            ->whereIn('goodsNo', $goodsNoList)
            ->where('delFl', '=', 'n')
            ->get()
            ->toArray();
    }

    /**
     * regularGoods의 sno 값을 바탕으로 정기결제(배송)관련 es_regulargiftPresentInfo외의 전체 데이터 값을 찾음
     *
     * SELECT
     * rgp.*,
     * rgp.sno AS giftPresentSno,
     * rg.*,
     * rg.sno AS regularGoodsSno
     * g.goodsNo, g.goodsNm, g.goodsPrice, g.totalStock, g.imageStorage, g.imagePath,
     * g.soldOutFl, g.stockFl, g.scmNo,
     * sm.companyNm,
     * gi.imageUrl, gi.imageName
     * FROM es_regularGoods AS rg
     * LEFT JOIN es_regularGiftPresent AS rgp ON rgp.regularGoodsSno = rg.sno
     * JOIN es_goods AS g ON g.goodsNo = rg.goodsNo
     * LEFT JOIN es_scmManage AS sm ON sm.scmNo = g.scmNo
     * LEFT JOIN es_goodsImage AS gi ON gi.goodsNo = g.goodsNo AND gi.imageKind = 'imageKind값'
     * WHERE rg.sno = :sno
     * LIMIT 1;
     *
     * @param int $sno : es_regularGoods의 sno 값
     * @param string $imageKind : 이미지 종류
     * @return array : 정기결제(배송)관련 전체 데이터 값
     */
    public function findRegularGoodsBySno(int $sno, string $imageKind): array
    {
        // sno를 기준으로 RegularGoods 모델과 관련된 모든 데이터 가져오기
        $regularGoodsData = RegularGoods::leftJoin('es_regularGiftPresent', function ($join) {
            $join->on('es_regularGiftPresent.regularGoodsSno', '=', 'es_regularGoods.sno')
                ->where('es_regularGiftPresent.delFl', '=', 'n'); // 추가 조건
        })
            ->join('es_goods', 'es_goods.goodsNo', '=', 'es_regularGoods.goodsNo')
            ->leftJoin('es_scmManage', 'es_scmManage.scmNo', '=', 'es_goods.scmNo')
            ->leftJoin('es_goodsImage', function ($join) use ($imageKind) {
                $join->on('es_goodsImage.goodsNo', '=', 'es_goods.goodsNo')
                    ->where('es_goodsImage.imageKind', '=', $imageKind);
            })
            ->select(
                'es_regularGiftPresent.*',
                'es_regularGiftPresent.sno AS giftPresentSno',
                'es_regularGoods.*',
                'es_regularGoods.sno AS regularGoodsSno',
                'es_goods.goodsNo', 'es_goods.goodsNm', 'es_goods.goodsPrice', 'es_goods.totalStock', 'es_goods.imageStorage', 'es_goods.imagePath',
                'es_goods.soldOutFl', 'es_goods.stockFl', 'es_goods.scmNo',
                'es_scmManage.companyNm as scmNm',
                'es_goodsImage.imageUrl', 'es_goodsImage.imageName', 'es_goodsImage.goodsImageStorage'
            )
            ->where('es_regularGoods.sno', $sno)
            ->first();

        if (empty($regularGoodsData)) {
            throw new DatabaseException('regularGoods관련 정보 불러오기 실패');
        }

        // 모델의 모든 데이터를 배열로 변환
        return $regularGoodsData->toArray();
    }

    /**
     * SELECT
     * es_regularGoods.*,
     * es_goods.optionFl, es_goods.soldOutFl, es_goods.stockFl, es_goods.goodsNm,
     * es_goods.totalStock, es_goods.optionTextFl, es_goods.salesEndYmd
     * FROM es_regularGoods
     * INNER JOIN es_goods ON es_regularGoods.goodsNo = es_goods.goodsNo
     * WHERE es_regularGoods.goodsNo = :goodsNo
     * AND es_regularGoods.delFl = 'n'
     * LIMIT 1;
     *
     * 특정 상품의 정기 상품 정보 조회
     * @param int $goodsNo
     * @return array
     */
    public function findRegularGoodsDetailByGoodsNo(int $goodsNo): array
    {
        $result = RegularGoods::query()
            ->select('es_regularGoods.*',
                'es_goods.optionFl', 'es_goods.soldOutFl', 'es_goods.stockFl', 'es_goods.goodsNm',
                'es_goods.totalStock', 'es_goods.optionTextFl', 'es_goods.salesEndYmd')
            ->join('es_goods', 'es_regularGoods.goodsNo', '=', 'es_goods.goodsNo')
            ->where('es_regularGoods.goodsNo', $goodsNo)
            ->where('es_regularGoods.delFl', 'n')
            ->first();

        return $result ? $result->toArray() : [];
    }

    /**
     * SELECT es_goods.deliverySno FROM es_regularGoods
     * JOIN es_goods on es_regularGoods.goodsNo = es_goods.goodsNo
     * where es_regularGoods.sno = ?
     *
     * @param int $sno
     * @return int
     */
    public function findRegularGoodsDeliverySnoByGoodsNo(int $sno): int
    {
        return RegularGoods::query()
            ->join('es_goods', 'es_regularGoods.goodsNo', '=', 'es_goods.goodsNo')
            ->where('es_regularGoods.sno', $sno)
            ->value('deliverySno');
    }

    /**
     * 정기상품에 해당하는 모체인 원본 상품 정보 조회
     *
     * SELECT es_goods.*,
     * es_regularGoods.discountUseFl, es_regularGoods.discountType, es_regularGoods.discountRate, es_regularGoods.discountPrice,
     * es_regularGoods.delFl, es_regularGoods.applyStatus, es_regularGoods.regularPrice
     * FROM es_regularGoods
     * JOIN es_goods on es_regularGoods.goodsNo = es_goods.goodsNo
     * where es_regularGoods.sno = ?
     *
     * @param int $regularGoodsNo
     * @return array
     */
    public function findRegularGoodsDataByRegularGoodsNo(int $regularGoodsNo): array
    {
        $regularGoods = RegularGoods::query()
            ->select([
                'es_goods.*',
                'es_regularGoods.discountUseFl',
                'es_regularGoods.discountType',
                'es_regularGoods.discountRate',
                'es_regularGoods.discountPrice',
                'es_regularGoods.delFl',
                'es_regularGoods.applyStatus',
                'es_regularGoods.regularPrice',
                'es_regularGoods.deliveryCycleType',
                'es_regularGoods.deliveryRoundsDisplayType',
                'es_regularGoods.maxDeliveryRounds',
            ])
            ->join('es_goods', 'es_regularGoods.goodsNo', '=', 'es_goods.goodsNo')
            ->where('es_regularGoods.sno', $regularGoodsNo)
            ->first();

        if ($regularGoods) {
            return $regularGoods->toArray();
        }

        return [];
    }

    /**
     * 정기상품 조회
     *
     * SELECT *
     *  FROM es_regularGoods
     *  WHERE sno = ?
     *
     * @param int $regularGoodsNo
     * @return array
     */
    public function findRegularGoodsInfoByRegularGoodsNoList(int $regularGoodsNo): array
    {
        $regularGoods = RegularGoods::query()
            ->where('sno', $regularGoodsNo)
            ->first();

        return $regularGoods ? $regularGoods->toArray() : [];
    }

    /**
     * 정기배송 상품 가격 조회
     *
     * SELECT regularPrice FROM es_regularGoods WHERE sno = ?
     *
     * @param int $regularGoodsNo
     * @return int
     */
    public function findRegularGoodsPriceByRegularGoodsNo(int $regularGoodsNo): int
    {
        return RegularGoods::query()
            ->where('sno', $regularGoodsNo)
            ->value('regularPrice');
    }

    /**
     * 정기결제(배송)상품 관련 데이터 조회 함수
     * es_regularGoods와 es_regularGoodsDeliveryCycle의 경우, 1:n관계이기 때문에 한개의 상품만 보여주기 위해 GroupBy사용
     * es_regularGoodsDeliveryCycle에 대한 데이터의 경우, 리스트 내 '보기'버튼을 통한 레이어에서 따로 보여주기 때문에 이슈 없음
     *
     * SELECT
     * es_regularGoods.*,
     * es_goods.goodsNo, es_goods.goodsCd, es_goods.goodsNm, es_goods.goodsPrice, es_goods.totalStock,
     * es_goods.imageStorage, es_goods.imagePath, es_goods.soldOutFl, es_goods.stockFl, es_goods.scmNo,
     * es_scmManage.companyNm,
     * es_goodsImage.imageUrl, es_goodsImage.imageName, es_goodsImage.goodsImageStorage
     * FROM es_regularGoods
     * INNER JOIN es_goods ON es_goods.goodsNo = es_regularGoods.goodsNo
     * LEFT JOIN es_scmManage ON es_scmManage.scmNo = es_goods.scmNo
     * LEFT JOIN es_goodsImage ON es_goodsImage.goodsNo = es_goods.goodsNo
     * AND es_goodsImage.imageKind = ?  -- ?는 $imageKind에 해당
     * LEFT JOIN es_regularGoodsDeliveryCycle ON es_regularGoodsDeliveryCycle.regularGoodsSno = es_regularGoods.sno GROUP BY es_regularGoods.sno
     * WHERE
     * es_regularGoods.delFl = 'n'
     * -- 여기에 필터 조건 추가
     * ORDER BY ?
     * LIMIT ?, ?;
     *
     * @param string $imageKind : 이미지 종류
     * @param array $filter : 필터링 조건
     * @param int $currentPageNum : 현제 페이지 번호
     * @param int $pageSizeNum : 페이지 당 출력되는 데이터 갯수
     * @return array: 조회된 상품
     */
    public function findRegularGoodsListByListSearchInfo(string $imageKind, array $filter, array $extraJoinTable, int $currentPageNum, int $pageSizeNum): array
    {
        $query = RegularGoods::query()
            ->select(
                'es_regularGoods.sno', 'es_regularGoods.goodsNo', 'es_regularGoods.applyStatus', 'es_regularGoods.discountUseFl', 'es_regularGoods.regularPrice',
                'es_regularGoods.deliveryType', 'es_regularGoods.deliveryCycleType', 'es_regularGoods.deliveryRoundsDisplayType', 'es_regularGoods.maxDeliveryRounds',
                'es_regularGoods.giftPresentUseFl', 'es_regularGoods.adminMemo', 'es_regularGoods.regDt', 'es_regularGoods.modDt',
                'es_goods.goodsNo', 'es_goods.goodsCd', 'es_goods.goodsNm', 'es_goods.goodsPrice', 'es_goods.totalStock', 'es_goods.imageStorage', 'es_goods.imagePath',
                'es_goods.soldOutFl', 'es_goods.stockFl', 'es_goods.scmNo',
                'es_scmManage.companyNm',
                'es_goodsImage.imageUrl', 'es_goodsImage.imageName', 'es_goodsImage.goodsImageStorage'
            )
            ->join('es_goods', 'es_goods.goodsNo', '=', 'es_regularGoods.goodsNo')
            ->leftJoin('es_scmManage', 'es_scmManage.scmNo', '=', 'es_goods.scmNo')
            ->leftJoin('es_goodsImage', function ($join) use ($imageKind) {
                $join->on('es_goodsImage.goodsNo', '=', 'es_goods.goodsNo')
                    ->where('es_goodsImage.imageKind', '=', $imageKind);
            });

        if (in_array('regularGoodsDeliveryCycle', $extraJoinTable)) {
            $query->leftJoin('es_regularGoodsDeliveryCycle', 'es_regularGoodsDeliveryCycle.regularGoodsSno', '=', 'es_regularGoods.sno')
                ->groupBy('es_regularGoods.sno');
        }

        // 기본 where 조건
        $query->where('es_regularGoods.delFl', '=', 'n');

        foreach ($filter as $filterType => $filterValueList) {
            if (empty($filterValueList)) {
                continue;
            }
            switch ($filterType) {
                case 'where':
                    $query->where($filterValueList);
                    break;

                case 'whereIn':
                    foreach ($filterValueList as $filterValue) {
                        $query->whereIn($filterValue[0], $filterValue[1]);
                    }
                    break;

                case 'whereDate':
                    foreach ($filterValueList as $filterValue) {
                        $query->whereDate($filterValue[0], $filterValue[1], $filterValue[2]);
                    }
                    break;

                case 'orWhereRaw':
                    foreach ($filterValueList as $filterValue) {
                        $query->where(function ($query) use ($filterValue) {
                            foreach ($filterValue[1] as $orWhereRawValue) {
                                $query->orWhereRaw("FIND_IN_SET(?, " . $filterValue[0] . ")", [$orWhereRawValue]);
                            }
                        });
                    }
                    break;

                case 'orderByRaw':
                    foreach ($filterValueList as $filterValue) {
                        $query->orderByRaw($filterValue);
                    }
                    break;
            }
        }

        // 페이지네이션 적용
        return $query->skip(($currentPageNum - 1) * $pageSizeNum)
            ->take($pageSizeNum)
            ->get()
            ->toArray();
    }

    /**
     * 조건에 따른 정기결제(배송) 상품의 갯수 반환  함수
     *
     * SELECT COUNT(DISTINCT es_regularGoods.goodsNo)
     * FROM es_regularGoods
     * JOIN es_goods ON es_goods.goodsNo = es_regularGoods.goodsNo
     * LEFT JOIN es_scmManage ON es_scmManage.scmNo = es_goods.scmNo
     * LEFT JOIN es_goodsImage ON es_goodsImage.goodsNo = es_goods.goodsNo
     * AND es_goodsImage.imageKind = :imageKind
     * LEFT JOIN es_regularGoodsDeliveryCycle ON es_regularGoodsDeliveryCycle.regularGoodsSno = es_regularGoods.sno
     * LEFT JOIN es_goodsLinkCategory ON es_goodsLinkCategory.goodsNo = es_goods.goodsNo
     * WHERE es_regularGoods.delFl = 'n'
     * -- 여기에 필터 조건 추가
     *
     * @param string $imageKind
     * @param array $filter
     * @return int
     */
    public function countRegularGoodsListByListSearchInfo(string $imageKind, array $filter, array $extraJoinTable): int
    {
        $query = RegularGoods::join('es_goods', 'es_goods.goodsNo', '=', 'es_regularGoods.goodsNo')
            ->leftJoin('es_scmManage', 'es_scmManage.scmNo', '=', 'es_goods.scmNo')
            ->leftJoin('es_goodsImage', function ($join) use ($imageKind) {
                $join->on('es_goodsImage.goodsNo', '=', 'es_goods.goodsNo')
                    ->where('es_goodsImage.imageKind', '=', $imageKind);
            });

        if (in_array('regularGoodsDeliveryCycle', $extraJoinTable)) {
            $query->leftJoin('es_regularGoodsDeliveryCycle', 'es_regularGoodsDeliveryCycle.regularGoodsSno', '=', 'es_regularGoods.sno');
        }

        if (in_array('goodsLinkCategory', $extraJoinTable)) {
            $query->leftJoin('es_goodsLinkCategory', 'es_goodsLinkCategory.goodsNo', '=', 'es_goods.goodsNo');
        }

        // 기본 where 조건
        $query->where('es_regularGoods.delFl', '=', 'n');

        foreach ($filter as $filterType => $filterValueList) {
            if (empty($filterValueList)) {
                continue;
            }
            switch ($filterType) {
                case 'where':
                    $query->where($filterValueList);
                    break;

                case 'whereIn':
                    foreach ($filterValueList as $filterValue) {
                        $query->whereIn($filterValue[0], $filterValue[1]);
                    }
                    break;

                case 'whereDate':
                    foreach ($filterValueList as $filterValue) {
                        $query->whereDate($filterValue[0], $filterValue[1], $filterValue[2]);
                    }
                    break;

                case 'orWhereRaw':
                    foreach ($filterValueList as $filterValue) {
                        $query->where(function ($query) use ($filterValue) {
                            foreach ($filterValue[1] as $orWhereRawValue) {
                                $query->orWhereRaw("FIND_IN_SET(?, " . $filterValue[0] . ")", [$orWhereRawValue]);
                            }
                        });
                    }
                    break;
            }
        }

        // 중복 방지를 위해 DISTINCT 추가 가능
        return $query->distinct()->count('es_regularGoods.goodsNo');
    }

    /**
     * 정기결제(배송) 상품의 갯수를 반환하는 함수
     *
     * SELECT COUNT(DISTINCT es_regularGoods.goodsNo)
     * FROM es_regularGoods
     * JOIN es_goods ON es_goods.goodsNo = es_regularGoods.goodsNo
     * WHERE es_regularGoods.delFl = 'n';
     *
     * @return int : 조회된 데이터 수
     */
    public function countRegularGoodsList(): int
    {
        $query = RegularGoods::join('es_goods', 'es_goods.goodsNo', '=', 'es_regularGoods.goodsNo')
            ->where('es_regularGoods.delFl', '=', 'n');

        // 중복 방지를 위해 DISTINCT 추가 가능
        return $query->distinct()->count('es_regularGoods.goodsNo');
    }

    /**
     * 정기결제(배송) 상품 삭제
     *
     * UPDATE es_regularGoods
     * SET delFl = 'y', modDt = ?
     * WHERE sno IN (?, ?, ?, ?...);
     *
     * @param array $snoList : 삭제할 정기결제(배송) 상품 sno array
     * @param string $modDt : 삭제 시각
     * @return void
     */
    public function deleteRegularGoodsBySno(array $snoList, string $modDt)
    {
        RegularGoods::whereIn('sno', $snoList)
            ->update([
                'delFl' => 'y',
                'modDt' => $modDt
            ]);
    }

    /**
     * sno를 기준으로 최소 가격 이하의 상품이 존재하는 지 확인
     *
     * SELECT EXISTS(
     * SELECT 1
     * FROM es_regularGoods
     * WHERE sno IN (?, ?, ?, ?...)
     * AND regularPrice < <minPrice>
     * ) AS exists_result;
     *
     * @param array $snoList : 확인할 sno
     * @param int $minPrice : 최소 가격
     * @return bool : 확인이 필요한 sno 값 중, 최소 가격 이하인 상품이 존재하는지 여부
     */
    public function hasRegularGoodsBelowMinPriceBySno(array $snoList, int $minPrice): bool
    {
        return RegularGoods::query()
            ->whereIn('sno', $snoList)
            ->where('regularPrice', '<', $minPrice)
            ->exists();
    }

    /**
     * 파라미터로 전달된 applyStatus인 sno값 전달
     *
     * SELECT `sno`
     * FROM `regular_goods`
     * WHERE `sno` IN (?, ?, ?, ...)
     * AND `applyStatus` != ?;
     *
     * @param array $snoList
     * @param string $applyStatus
     * @return array
     */
    public function findSnoListByApplyStatus(array $snoList, string $applyStatus): array
    {
        return RegularGoods::select('sno')
            ->whereIn('sno', $snoList)
            ->where('applyStatus', '=', $applyStatus)
            ->get()
            ->toArray();
    }


    /**
     * 정기결제(배송) 상품 신청 사용 가능 여부 update
     *
     * UPDATE es_regularGoods
     * SET applyStatus = ?, modDt = ?
     * WHERE sno IN (?, ?, ?, ?...);
     *
     * @param array $snoList : 업데이트할 정기결제(배송) 상품 sno array
     * @param string $applyStatus : 변경할 값
     * @param string $modDt : 변경 시각
     * @return void
     */
    public function updateApplyStatusBySno(array $snoList, string $applyStatus, string $modDt)
    {
        RegularGoods::whereIn('sno', $snoList)
            ->update([
                'applyStatus' => $applyStatus,
                'modDt' => $modDt
            ]);
    }


    /**
     * 신청 상태를 수정하고자 하는 정기결제(배송) 상품에 대해 변경 가능 여부 확인 함수
     *
     * SELECT EXISTS (
     * SELECT 1
     * FROM es_regularGoods
     * INNER JOIN es_goods ON es_regularGoods.goodsNo = es_goods.goodsNo
     * WHERE es_regularGoods.sno IN ('값1', '값2', '값3') -- 여기에 실제 sno 값 입력
     * AND (
     * (es_goods.goodsDisplayFl = 'n' AND es_goods.goodsDisplayMobileFl = 'n')
     * OR (es_goods.goodsSellFl = 'n' AND es_goods.goodsSellMobileFl = 'n')
     * OR (es_goods.stockFl = 'y' AND es_goods.totalStock < 1)
     * OR (es_goods.applyFl = 'n')
     * OR (es_goods.salesEndYmd <= ?))
     * ) AS result;
     *
     * @param array $snoList : 확인하고자 하는 sno
     * @param string $modDt : 현재 시각
     * @return bool : 변경 가능 여부
     */
    public function canUpdateApplyStatusByRegularGoodsNoList(array $snoList, string $modDt): bool
    {
        return RegularGoods::query()
            ->join('es_goods', 'es_regularGoods.goodsNo', '=', 'es_goods.goodsNo')
            ->whereIn('es_regularGoods.sno', $snoList)
            ->where(function ($query) use ($modDt) {  // $modDt를 use로 전달
                $query->where(function ($query) {           // pc, mobile 모두 노출안함 상태일 경우
                    $query->where('es_goods.goodsDisplayFl', 'n')
                        ->where('es_goods.goodsDisplayMobileFl', 'n');
                })
                    ->orWhere(function ($query) {           // pc, mobile 모두 판매 안함 상태일 경우
                        $query->where('es_goods.goodsSellFl', 'n')
                            ->where('es_goods.goodsSellMobileFl', 'n');
                    })
                    ->orWhere(function ($query) {           //무한 재고가 아니고 제고가 없을 경우
                        $query->where('es_goods.stockFl', 'y')
                            ->where('es_goods.totalStock', '<', 1);
                    })
                    ->orWhere('es_goods.soldOutFl', '=', 'y')       //품절상태인 경우
                    ->orWhere('es_goods.applyFl', '!=', 'y')         //공급사 상품 승인여부가 승인되지 않았을 경우
                    ->orWhere(function ($query) use ($modDt) {
                        $query->whereDate('salesEndYmd', '<=', $modDt)
                            ->whereNotNull('salesEndYmd')
                            ->where('salesEndYmd', '!=', '0000-00-00 00:00:00');
                    });
            })
            ->exists();
    }


    /**
     * 엑셀 다운로드 기능에서 사용할 상품 데이터 조회를 위한 베이스 쿼리
     * es_regularGoods와 es_regularGoodsDeliveryCycle의 경우, 1:n관계이기 때문에 한개의 상품만 보여주기 위해 GroupBy사용
     * es_regularGoodsDeliveryCycle에 대한 데이터의 경우, 따로 조회하기 때문에 이슈 없음
     *
     * SELECT
     * es_regularGoods.*,
     * es_regularGiftPresent.conditionTitle,
     * es_goods.goodsCd, es_goods.goodsNm, es_goods.goodsPrice,
     * es_scmManage.companyNm
     * FROM es_regularGoods
     * LEFT JOIN es_regularGiftPresent ON es_regularGiftPresent.regularGoodsSno = es_regularGoods.sno
     * AND es_regularGiftPresent.delFl = 'n'
     * JOIN es_goods ON es_goods.goodsNo = es_regularGoods.goodsNo
     * LEFT JOIN es_scmManage ON es_scmManage.scmNo = es_goods.scmNo
     * LEFT JOIN es_regularGoodsDeliveryCycle ON es_regularGoodsDeliveryCycle.regularGoodsSno = es_regularGoods.sno GROUP BY es_regularGoods.sno
     * WHERE es_regularGoods.delFl = 'n'
     * -- 여기에 필터 조건 추가
     *
     * @param $filter : 엑셀로 다운 받을 데이터 조건
     * @return array : 조회된 데이터
     */
    public function findRegularGoodsListToExcelByListSearchInfo($filter, array $extraJoinTable): array
    {
        $query = RegularGoods::query()
            ->select(
                'es_regularGoods.*',
                'es_regularGiftPresent.sno as regularGiftPresentSno', 'es_regularGiftPresent.conditionTitle', 'es_regularGiftPresent.conditionType',
                'es_goods.goodsCd', 'es_goods.goodsNm', 'es_goods.goodsPrice',
                'es_scmManage.companyNm'
            )
            ->leftJoin('es_regularGiftPresent', function ($join) {
                $join->on('es_regularGiftPresent.regularGoodsSno', '=', 'es_regularGoods.sno')
                    ->where('es_regularGiftPresent.delFl', '=', 'n');
            })
            ->join('es_goods', 'es_goods.goodsNo', '=', 'es_regularGoods.goodsNo')
            ->leftJoin('es_scmManage', 'es_scmManage.scmNo', '=', 'es_goods.scmNo');

        if (in_array('regularGoodsDeliveryCycle', $extraJoinTable)) {
            $query->leftJoin('es_regularGoodsDeliveryCycle', 'es_regularGoodsDeliveryCycle.regularGoodsSno', '=', 'es_regularGoods.sno')
                ->groupBy('es_regularGoods.sno');
        }

        // 기본 where 조건
        $query->where('es_regularGoods.delFl', '=', 'n');

        foreach ($filter as $filterType => $filterValueList) {
            if (empty($filterValueList)) {
                continue;
            }
            switch ($filterType) {
                case 'where':
                    $query->where($filterValueList);
                    break;

                case 'whereIn':
                    foreach ($filterValueList as $filterValue) {
                        $query->whereIn($filterValue[0], $filterValue[1]);
                    }
                    break;

                case 'whereDate':
                    foreach ($filterValueList as $filterValue) {
                        $query->whereDate($filterValue[0], $filterValue[1], $filterValue[2]);
                    }
                    break;

                case 'orWhereRaw':
                    foreach ($filterValueList as $filterValue) {
                        $query->where(function ($query) use ($filterValue) {
                            foreach ($filterValue[1] as $orWhereRawValue) {
                                $query->orWhereRaw("FIND_IN_SET(?, " . $filterValue[0] . ")", [$orWhereRawValue]);
                            }
                        });
                    }
                    break;

                case 'orderByRaw':
                    foreach ($filterValueList as $filterValue) {
                        $query->orderByRaw($filterValue);
                    }
                    break;
            }
        }

        // 데이터를 배열로 변환
        return $query->get()->toArray();
    }

    /**
     * snoList에 속하면서 정기 상품 배송 방법(deliveryType)이 regular인 일반 상품 번호 반환
     *
     * SELECT goodsNo
     * FROM regularGoods
     * WHERE sno IN (?, ?, ?, ...)
     * AND deliveryType = ?
     *
     * @param array $snoList
     * @param string $deliveryType
     * @return mixed
     */
    public function findSnoBySnoListAndDeliveryType(array $snoList, string $deliveryType)
    {
        return RegularGoods::select('goodsNo')
            ->whereIn('sno', $snoList)
            ->where('deliveryType', $deliveryType)
            ->get()
            ->toArray();
    }

    /**
     * es_regularGoods.sno를 바탕으로 es_regularGoods.adminMemo와 es_goods의 goodsNm을 조회
     *
     * SELECT es_goods.goodsNm, es_regularGoods.adminMemo
     * FROM es_regularGoods
     * JOIN es_goods ON es_regularGoods.goodsNo = es_goods.goodsNo
     * WHERE es_regularGoods.sno = ?;
     *
     * @param int $sno
     * @return array
     */
    public function findAdminMemoAndGoodsNmBySno(int $sno): array
    {
        $adminMemoData = RegularGoods::query()
            ->select('es_goods.goodsNm', 'es_regularGoods.adminMemo')
            ->join('es_goods', 'es_regularGoods.goodsNo', '=', 'es_goods.goodsNo')
            ->where('es_regularGoods.sno', $sno)
            ->first();

        return $adminMemoData ? $adminMemoData->toArray() : [];
    }

    /**
     * es_regularGoods.sno를 바탕으로 es_regularGoods.adminMemo 업데이트
     *
     * UPDATE es_regularGoods
     * SET adminMemo = ?, modDt = NOW()
     * WHERE sno = ?;
     *
     * @param int $sno
     * @param string $adminMemo
     * @return void
     */
    public function updateAdminMemoBySno(int $sno, string $adminMemo)
    {
        RegularGoods::query()
            ->where('sno', $sno)
            ->update([
                'adminMemo' => $adminMemo,
                'modDt' => new Expression('NOW()')  // MySQL 서버 시간 기준으로 업데이트
            ]);
    }

    /**
     * 조건에 의해 조회된 정기결제(배송) 상품 정보
     * SELECT
     * es_regularGoods.sno, es_regularGoods.goodsNo, es_regularGoods.regularPrice,
     * es_goods.goodsNm, es_goods.imageStorage, es_goods.imagePath,
     * es_goodsImage.imageUrl, es_goodsImage.imageName, es_goodsImage.goodsImageStorage
     * FROM es_regularGoods
     * INNER JOIN es_goods ON es_goods.goodsNo = es_regularGoods.goodsNo
     * LEFT JOIN es_goodsImage ON es_goodsImage.goodsNo = es_goods.goodsNo AND es_goodsImage.imageKind = '지정된값'
     * LEFT JOIN es_goodsLinkCategory ON es_goodsLinkCategory.goodsNo = es_goods.goodsNo
     * WHERE es_regularGoods.delFl = 'n'
     * AND es_regularGoods.applyStatus = 'abled'
     *
     * -- 여기에 조인 및 필터 조건 추가
     * LIMIT {offset: ($currentPageNum - 1) * $pageSizeNum}, {pageSizeNum};
     *
     * @param string $imageKind : 이미지 종류
     * @param array $filter : 필터링 조건
     * @param array $extraJoinTable : 추가로 join할 테이블 목록
     * @param int $currentPageNum : 현제 페이지 번호
     * @param int $pageSizeNum : 페이지 당 출력되는 데이터 갯수
     * @return array: 조회된 상품
     */

    public function findLayerRegularGoodsListByListSearchInfo(string $imageKind, array $filter, array $extraJoinTable, int $currentPageNum, int $pageSizeNum): array
    {
        $query = RegularGoods::query()
            ->select(
                'es_regularGoods.sno', 'es_regularGoods.goodsNo', 'es_regularGoods.regularPrice',
                'es_goods.goodsNm', 'es_goods.imageStorage', 'es_goods.imagePath',
                'es_goodsImage.imageUrl', 'es_goodsImage.imageName', 'es_goodsImage.goodsImageStorage'
            )
            ->join('es_goods', 'es_goods.goodsNo', '=', 'es_regularGoods.goodsNo')
            ->leftJoin('es_goodsImage', function ($join) use ($imageKind) {
                $join->on('es_goodsImage.goodsNo', '=', 'es_goods.goodsNo')
                    ->where('es_goodsImage.imageKind', '=', $imageKind);
            });

        if (in_array('goodsLinkCategory', $extraJoinTable)) {
            $query->leftJoin('es_goodsLinkCategory', 'es_goodsLinkCategory.goodsNo', '=', 'es_goods.goodsNo');
        }

        // 기본 where 조건
        $query->where('es_regularGoods.delFl', '=', 'n');
        $query->where('es_regularGoods.applyStatus', '=', 'abled');

        foreach ($filter as $filterType => $filterValueList) {
            if (empty($filterValueList)) {
                continue;
            }
            switch ($filterType) {
                case 'where':
                    $query->where($filterValueList);
                    break;

                case 'whereIn':
                    foreach ($filterValueList as $filterValue) {
                        $query->whereIn($filterValue[0], $filterValue[1]);
                    }
                    break;
            }
        }

        // 페이지네이션 적용
        return $query->orderBy('es_regularGoods.regDt', 'desc')
            ->skip(($currentPageNum - 1) * $pageSizeNum)
            ->take($pageSizeNum)
            ->get()
            ->toArray();
    }

    /**
     * 정기결제(배송)상품 팝업 관련 데이터 조회 함수
     * es_regularGoods와 es_regularGoodsDeliveryCycle의 경우, 1:n관계이기 때문에 한개의 상품만 보여주기 위해 GroupBy사용
     * es_regularGoodsDeliveryCycle에 대한 데이터의 경우, 리스트 내 '보기'버튼을 통한 레이어에서 따로 보여주기 때문에 이슈 없음
     *
     * SELECT
     * es_regularGoods.sno, es_regularGoods.goodsNo, es_regularGoods.regularPrice, es_regularGoods.regDt, es_regularGoods.modDt,
     * es_goods.goodsNm, es_goods.totalStock, es_goods.imageStorage, es_goods.imagePath, es_goods.stockFl, es_goods.scmNo,
     * es_scmManage.companyNm,
     * es_goodsImage.imageUrl, es_goodsImage.imageName, es_goodsImage.goodsImageStorage
     * FROM es_regularGoods
     * INNER JOIN es_goods ON es_goods.goodsNo = es_regularGoods.goodsNo
     * LEFT JOIN es_scmManage ON es_scmManage.scmNo = es_goods.scmNo
     * LEFT JOIN es_goodsImage ON es_goodsImage.goodsNo = es_goods.goodsNo
     * AND es_goodsImage.imageKind = ?  -- ?는 $imageKind에 해당
     * LEFT JOIN es_regularGoodsDeliveryCycle ON es_regularGoodsDeliveryCycle.regularGoodsSno = es_regularGoods.sno GROUP BY es_regularGoods.sno
     * WHERE
     * es_regularGoods.delFl = 'n'
     * -- 여기에 필터 조건 추가
     * ORDER BY ?
     * LIMIT ?, ?;
     *
     * @param string $imageKind : 이미지 종류
     * @param array $filter : 필터링 조건
     * @param array $extraJoinTable : 추가 조인 테이블 목록
     * @param int $currentPageNum : 현제 페이지 번호
     * @param int $pageSizeNum : 페이지 당 출력되는 데이터 갯수
     * @return array: 조회된 상품
     */
    public function findPopupRegularGoodsListByListSearchInfo(string $imageKind, array $filter, array $extraJoinTable, int $currentPageNum, int $pageSizeNum): array
    {
        $query = RegularGoods::query()
            ->select(
                'es_regularGoods.sno', 'es_regularGoods.goodsNo', 'es_regularGoods.regularPrice', 'es_regularGoods.regDt', 'es_regularGoods.modDt',
                'es_goods.goodsNm', 'es_goods.totalStock', 'es_goods.imageStorage', 'es_goods.imagePath', 'es_goods.stockFl', 'es_goods.scmNo',
                'es_scmManage.companyNm',
                'es_goodsImage.imageUrl', 'es_goodsImage.imageName', 'es_goodsImage.goodsImageStorage'
            )
            ->join('es_goods', 'es_goods.goodsNo', '=', 'es_regularGoods.goodsNo')
            ->leftJoin('es_scmManage', 'es_scmManage.scmNo', '=', 'es_goods.scmNo')
            ->leftJoin('es_goodsImage', function ($join) use ($imageKind) {
                $join->on('es_goodsImage.goodsNo', '=', 'es_goods.goodsNo')
                    ->where('es_goodsImage.imageKind', '=', $imageKind);
            });

        if (in_array('regularGoodsDeliveryCycle', $extraJoinTable)) {
            $query->leftJoin('es_regularGoodsDeliveryCycle', 'es_regularGoodsDeliveryCycle.regularGoodsSno', '=', 'es_regularGoods.sno')
                ->groupBy('es_regularGoods.sno');
        }

        // 기본 where 조건
        $query->where('es_regularGoods.delFl', '=', 'n');
        $query->where('es_regularGoods.applyStatus', '=', 'abled');

        foreach ($filter as $filterType => $filterValueList) {
            if (empty($filterValueList)) {
                continue;
            }
            switch ($filterType) {
                case 'where':
                    $query->where($filterValueList);
                    break;

                case 'whereIn':
                    foreach ($filterValueList as $filterValue) {
                        $query->whereIn($filterValue[0], $filterValue[1]);
                    }
                    break;

                case 'whereDate':
                    foreach ($filterValueList as $filterValue) {
                        $query->whereDate($filterValue[0], $filterValue[1], $filterValue[2]);
                    }
                    break;

                case 'orWhereRaw':
                    foreach ($filterValueList as $filterValue) {
                        $query->where(function ($query) use ($filterValue) {
                            foreach ($filterValue[1] as $orWhereRawValue) {
                                $query->orWhereRaw("FIND_IN_SET(?, " . $filterValue[0] . ")", [$orWhereRawValue]);
                            }
                        });
                    }
                    break;

                case 'orderByRaw':
                    foreach ($filterValueList as $filterValue) {
                        $query->orderByRaw($filterValue);
                    }
                    break;
            }
        }

        // 페이지네이션 적용
        return $query->skip(($currentPageNum - 1) * $pageSizeNum)
            ->take($pageSizeNum)
            ->get()
            ->toArray();
    }

    /**
     * 정기상품 삭제 플래그 조회
     *
     * SELECT delFl FROM es_regularGoods WHERE sno = ?
     *
     * @param int $regularGoodsSno
     * @return array
     */
    public function findRegularGoodsDelFlByRegularGoodsNo(int $regularGoodsSno): array
    {
        $regularGoods = RegularGoods::query()
            ->select('delFl')
            ->where('sno', $regularGoodsSno)
            ->first();

        if ($regularGoods) {
            return $regularGoods->toArray();
        }

        return [];
    }

    /**
     * 정기 상품 배송 관련 정보 조회
     *
     * SELECT  es_regularGoods.deliveryCycleType, es_regularGoods.maxDeliveryRounds,
     * es_regularGoodsDeliveryCycle.monthCycle, es_regularGoodsDeliveryCycle.weekCycle, es_regularGoodsDeliveryCycle.weekDayCycle
     * FROM es_regularGoods
     * LEFT JOIN es_regularGoodsDeliveryCycle ON es_regularGoodsDeliveryCycle.regularGoodsSno = es_regularGoods.sno
     * WHERE es_regularGoods.sno = ?
     *
     * @param int $regularGoodsSno
     * @return array
     */
    public function findRegularGoodsDeliveryInfoByRegularGoodsNo(int $regularGoodsSno)
    {
        return RegularGoods::query()
            ->select([
                'es_regularGoods.deliveryCycleType', 'es_regularGoods.maxDeliveryRounds',
                'es_regularGoodsDeliveryCycle.monthCycle', 'es_regularGoodsDeliveryCycle.weekCycle', 'es_regularGoodsDeliveryCycle.weekDayCycle'
            ])
            ->leftJoin('es_regularGoodsDeliveryCycle', 'es_regularGoodsDeliveryCycle.regularGoodsSno', '=', 'es_regularGoods.sno')
            ->where('es_regularGoods.sno', $regularGoodsSno)
            ->get()
            ->toArray();
    }


    /**
     * 정기결제(배송)의 특정 종료회차와 배송주기 중 개월이나 주차를 기준으로
     * 해당 배송주기를 포함하는 정기결제(배송)상품 번호 반환
     *
     * SELECT es_regularGoods.sno
     * FROM es_regularGoods
     * LEFT JOIN es_regularGoodsDeliveryCycle ON es_regularGoodsDeliveryCycle.regularGoodsSno = es_regularGoods.sno
     * WHERE es_regularGoods.delFl = 'n'
     *
     * AND ( es_regularGoods.deliveryRoundsDisplayType = 'DELIVERY_ROUNDS_DISPLAY_ALL'
     * OR ( ( 0 = :maxDeliveryRound AND es_regularGoods.maxDeliveryRounds = 0 )
     * OR ( :maxDeliveryRound != 0 AND es_regularGoods.maxDeliveryRounds >= :maxDeliveryRound ) ) )
     *
     * AND ( es_regularGoods.deliveryCycleType = 'all'
     * OR ( es_regularGoods.deliveryCycleType = :deliveryCycleType
     * AND es_regularGoodsDeliveryCycle.:deliveryCycleTypeCycle = :deliveryCycle) );
     *
     * @param array $orderDelivery
     * @return array
     */
    public function findSnoByRegularDeliveryInfo(array $orderDelivery): array
    {
        $maxDeliveryRound = $orderDelivery['maxDeliveryRound'];
        $deliveryCycleType = $orderDelivery['deliveryCycleType'];
        $deliveryCycle = $orderDelivery['deliveryCycle'];
        $deliveryCycleColumnMap = [
            'month' => 'es_regularGoodsDeliveryCycle.monthCycle',
            'week' => 'es_regularGoodsDeliveryCycle.weekCycle',
        ];

        $query = RegularGoods::query()
            ->select('es_regularGoods.sno')
            ->leftJoin(
                'es_regularGoodsDeliveryCycle',
                'es_regularGoodsDeliveryCycle.regularGoodsSno',
                '=',
                'es_regularGoods.sno'
            )
            ->where('es_regularGoods.delFl', '=', 'n');

        // 회차 조건
        $query->where(function ($query) use ($maxDeliveryRound) {
            $query->where('es_regularGoods.deliveryRoundsDisplayType', RegularGoodsAttribute::DELIVERY_ROUNDS_DISPLAY_ALL)
                ->orWhere(function ($q) use ($maxDeliveryRound) {
                    if ($maxDeliveryRound === 0) {
                        $q->where('es_regularGoods.maxDeliveryRounds', 0);
                    } else {
                        $q->where('es_regularGoods.maxDeliveryRounds', '>=', $maxDeliveryRound);
                    }
                });
        });

        // 주기 조건
        $query->where(function ($query) use ($deliveryCycleColumnMap, $deliveryCycleType, $deliveryCycle) {
            $query->where('es_regularGoods.deliveryCycleType', 'all')
                ->orWhere(function ($q) use ($deliveryCycleColumnMap, $deliveryCycleType, $deliveryCycle) {
                    $q->where('es_regularGoods.deliveryCycleType', $deliveryCycleType)
                        ->where($deliveryCycleColumnMap[$deliveryCycleType], $deliveryCycle);
                });
        });

        return $query->get()->toArray();
    }


    /**
     * 정기결제(배송)의 특정 배송주기 중 요일을 기준으로
     * 해당 배송주기를 포함하는 정기결제(배송)상품 번호 반환
     *
     * SELECT es_regularGoods.sno
     * FROM es_regularGoods
     * LEFT JOIN es_regularGoodsDeliveryCycle ON es_regularGoodsDeliveryCycle.regularGoodsSno = es_regularGoods.sno
     * WHERE es_regularGoods.sno IN (1, 2, 3, 4) -- 예시: $regularGoodsSnoList
     * AND ( es_regularGoods.deliveryCycleType = 'all'
     * OR ( es_regularGoods.deliveryCycleType = 'week' AND es_regularGoodsDeliveryCycle.weekDayCycle = ? ));
     *
     * @param array $regularGoodsSnoList
     * @param int $deliveryCycleDay
     * @return array
     */
    public function findSnoByDeliveryCycleDay(array $regularGoodsSnoList, int $deliveryCycleDay): array
    {
        $query = RegularGoods::query()
            ->select('es_regularGoods.sno')
            ->leftJoin(
                'es_regularGoodsDeliveryCycle',
                'es_regularGoodsDeliveryCycle.regularGoodsSno',
                '=',
                'es_regularGoods.sno'
            )
            ->whereIn('es_regularGoods.sno', $regularGoodsSnoList)
            ->where(function ($query) use ($deliveryCycleDay) {
                $query->where('es_regularGoods.deliveryCycleType', 'all')
                    ->orWhere(function ($q) use ($deliveryCycleDay) {
                        $q->where('es_regularGoods.deliveryCycleType', 'week')
                            ->where('es_regularGoodsDeliveryCycle.weekDayCycle', $deliveryCycleDay);
                    });
            });

        return $query->get()->toArray();
    }


    /**
     * 일반 상품번호를 바탕으로 정기결제상품 정보 조회
     *
     * SELECT *
     * FROM regularGoods
     * WHERE goodsNo IN (?, ?, ?)
     * AND delFl = 'n';
     *
     * @param array $goodsNoList
     * @return array
     */
    public function findRegularGoodsNoByGoodsNoList(array $goodsNoList): array
    {
        return RegularGoods::query()
            ->whereIn('goodsNo', $goodsNoList)
            ->where('delFl', 'n')
            ->get()
            ->toArray();
    }

    /**
     * 정기결제(배송) 상품 리스트 중, 신청가능한 정기결제(배송) 상품 번호만 조회
     *
     * SELECT rg.sno
     * FROM es_regularGoods rg
     * LEFT JOIN es_goods g ON g.goodsNo = rg.goodsNo
     * WHERE rg.sno IN (" . implode(',', $regularGoodsSnoList) . ")
     * AND (
     * (g.goodsDisplayFl = 'y' OR g.goodsDisplayMobileFl = 'y')
     * AND (g.goodsSellFl = 'y' OR g.goodsSellMobileFl = 'y')
     * AND (g.stockFl = 'n' OR g.totalStock >= 1)
     * AND g.soldOutFl != 'y'
     * AND g.applyFl = 'y'
     * AND (g.salesEndYmd IS NULL OR g.salesEndYmd = '0000-00-00 00:00:00' OR g.salesEndYmd > '$date')
     * )
     *
     * @param array $regularGoodsSnoList
     * @param string $date
     * @return array
     *
     */
    public function findApplyAbledRegularGoodsBySno(array $regularGoodsSnoList, string $date): array
    {
        $query = RegularGoods::query()
            ->select('es_regularGoods.sno')
            ->leftJoin('es_goods', 'es_goods.goodsNo', '=', 'es_regularGoods.goodsNo')
            ->whereIn('es_regularGoods.sno', $regularGoodsSnoList)
            ->where('es_regularGoods.applyStatus', '=', RegularGoodsAttribute::ABLED)
            ->where(function ($query) use ($date) {
                $query->where(function ($query) {
                    // PC 또는 Mobile 노출됨
                    $query->where('es_goods.goodsDisplayFl', 'y')
                        ->orWhere('es_goods.goodsDisplayMobileFl', 'y');
                })
                    // PC 또는 Mobile 판매중
                    ->where(function ($query) {
                        $query->where('es_goods.goodsSellFl', 'y')
                            ->orWhere('es_goods.goodsSellMobileFl', 'y');
                    })

                    // 무한재고이거나 재고 1개 이상
                    ->where(function ($query) {
                        $query->where('es_goods.stockFl', 'n')
                            ->orWhere('es_goods.totalStock', '>=', 1);
                    })

                    // 품절 아님
                    ->where('es_goods.soldOutFl', '!=', 'y')

                    // 판매 종료일이 없거나 미래
                    ->where(function ($query) use ($date) {
                        $query->whereNull('salesEndYmd')
                            ->orWhere('salesEndYmd', '=', '0000-00-00 00:00:00')
                            ->orWhere('salesEndYmd', '>', $date);
                    });
            });

        return $query->get()->toArray();
    }

    /**
     * 정기상품과 배송주기 정보 조회
     *
     * @param int $regularGoodsNo 정기상품 sno
     * @return array 정기상품 정보와 배송주기 정보
     */
    public function findRegularGoodsWithDeliveryCycle(int $regularGoodsNo): array
    {
        return RegularGoods::query()
            ->from('es_regularGoods as rg')
            ->leftJoin('es_regularGoodsDeliveryCycle as rc', 'rg.sno', '=', 'rc.regularGoodsSno')
            ->where('rg.sno', $regularGoodsNo)
            ->get()
            ->toArray();
    }
}
