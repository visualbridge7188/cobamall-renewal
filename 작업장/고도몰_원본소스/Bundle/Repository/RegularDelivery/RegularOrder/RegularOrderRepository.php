<?php
/*
 * Copyright (C) 2025 NHN COMMERCE. - All Rights Reserved
 *
 * Unauthorized copying or redistribution of this file in source and binary forms via any medium
 * is strictly prohibited.
 */

namespace Bundle\Repository\RegularDelivery\RegularOrder;

use DTO\RegularDelivery\RegularOrder\RegularOrderCreateDTO;
use DTO\RegularDelivery\RegularOrder\RegularOrderSearchCondition;
use Origin\Enum\RegularDelivery\RegularOrder\RegularOrderStatus;
use Origin\Model\RegularDelivery\RegularOrder\RegularOrder;

class RegularOrderRepository
{
    /**
     * 신청서 그룹 생성
     *
     * INSERT INTO(applyGroupNo, memNo, regDt)
     *  VALUES (?, ?, ?)
     *
     * @param RegularOrderCreateDTO $dto
     * @return void
     */
    public function insertRegularOrder(RegularOrderCreateDTO $dto)
    {
        RegularOrder::query()
            ->insert([
                'applyGroupNo' => $dto->getApplyGroupNo(),
                'memNo' => $dto->getMemNo(),
                'regDt' => $dto->getRegDt(),
            ]);
    }

    /**
     * 변경하려는 배송지를 사용하는 신청서 중에 '이용중, 일시정지' 상태 있는 신청서가 있는지
     *
     * SELECT 1
     * FROM es_regularOrder AS ro
     * JOIN es_regularOrderGoods AS rog ON ro.applyGroupNo = rog.applyGroupNo
     * WHERE ro.memNo = ?
     * AND rog.applyStatus in ('active', 'userStop', 'systemStop', 'adminStop')
     * AND rog.shippingAddressSno = ?
     *
     * @param int $shippingAddressSno
     * @param int $memNo
     * @return bool
     */
    public function hasActiveOrPausedOrdersUsingShippingAddress(int $shippingAddressSno, int $memNo): bool
    {
        return RegularOrder::query()
            ->join('es_regularOrderGoods', 'es_regularOrder.applyGroupNo', '=', 'es_regularOrderGoods.applyGroupNo')
            ->where('es_regularOrder.memNo', $memNo)
            ->whereIn('es_regularOrderGoods.applyStatus', [RegularOrderStatus::ACTIVE, RegularOrderStatus::USER_STOP, RegularOrderStatus::SYSTEM_STOP, RegularOrderStatus::ADMIN_STOP])
            ->where('es_regularOrderGoods.shippingAddressSno', $shippingAddressSno)
            ->exists();
    }


    /**
     *
     * 신청서그룹 번호에 연관된 데이터 조회
     *
     * SELECT
     * es_regularOrder.memNo, es_regularOrder.totalPrice, es_regularOrder.totalDeliveryCharge, es_regularOrderGoods.applyNo, es_regularOrderGoods.cardNo,
     * es_goods.goodsNm, es_regularOrder.regDt, es_regularOrderApplier.applierName, es_regularOrderShippingAddress.shippingName,
     * es_regularOrderShippingAddress.shippingZonecode, es_regularOrderShippingAddress.shippingAddress, es_regularOrderShippingAddress.shippingAddressSub,
     * es_regularOrderShippingAddress.shippingPhone, es_regularOrderShippingAddress.shippingCellPhone, es_regularOrderShippingAddress.shippingMessage
     * FROM es_regularOrder
     * JOIN es_regularOrderGoods ON es_regularOrderGoods.applyGroupNo = es_regularOrder.applyGroupNo
     * JOIN es_regularGoods ON es_regularGoods.sno = es_regularOrderGoods.regularGoodsNo
     * JOIN es_goods ON es_goods.goodsNo = es_regularGoods.goodsNo
     *JOIN es_regularOrderApplier ON es_regularOrderApplier.applyGroupNo = es_regularOrder.applyGroupNo
     *JOIN es_regularOrderShippingAddress ON es_regularOrderShippingAddress.sno = es_regularOrderGoods.shippingAddressSno
     * WHERE es_regularOrder.applyGroupNo = ?
     *
     * @param string $applyGroupNo
     * @return array
     */
    public function findOrderSummaryByApplyGroupNo(string $applyGroupNo): array
    {
        return RegularOrder::query()
            ->select(
                    'es_regularOrder.memNo',
                    'es_regularOrder.totalPrice',
                    'es_regularOrder.totalDeliveryCharge',
                    'es_regularOrderGoods.applyNo',
                    'es_regularOrderGoods.cardNo',
                    'es_goods.goodsNm',
                    'es_regularOrder.regDt',
                    'es_regularOrderApplier.applierName',
                    'es_regularOrderShippingAddress.shippingName',
                    'es_regularOrderShippingAddress.shippingZonecode',
                    'es_regularOrderShippingAddress.shippingAddress',
                    'es_regularOrderShippingAddress.shippingAddressSub',
                    'es_regularOrderShippingAddress.shippingPhone',
                    'es_regularOrderShippingAddress.shippingCellPhone',
                    'es_regularOrderShippingAddress.shippingMessage'
            )
            ->join('es_regularOrderGoods', 'es_regularOrderGoods.applyGroupNo', '=', 'es_regularOrder.applyGroupNo')
            ->join('es_regularGoods', 'es_regularGoods.sno', '=', 'es_regularOrderGoods.regularGoodsNo')
            ->join('es_goods', 'es_goods.goodsNo', '=', 'es_regularGoods.goodsNo')
            ->join('es_regularOrderApplier', 'es_regularOrderApplier.applyGroupNo', '=', 'es_regularOrder.applyGroupNo')
            ->join('es_regularOrderShippingAddress', 'es_regularOrderShippingAddress.sno', '=', 'es_regularOrderGoods.shippingAddressSno')
            ->where('es_regularOrder.applyGroupNo', $applyGroupNo)
            ->get()
            ->toArray();
    }

    /**
     * 총 가격 및 배송비 업데이트
     *
     * UPDATE es_regularOrder
     *  SET totalPrice = ?, totalDeliveryCharge = ?
     *  WHERE applyGroupNo = ?
     *
     * @param string $applyGroupNo
     * @param int $totalPrice
     * @param int $totalDeliveryCharge
     * @return void
     */
    public function updateTotalPriceAndDeliveryCharge(string $applyGroupNo, int $totalPrice, int $totalDeliveryCharge)
    {
        RegularOrder::query()
            ->where('applyGroupNo', $applyGroupNo)
            ->update([
                'totalPrice' => $totalPrice,
                'totalDeliveryCharge' => $totalDeliveryCharge
            ]);
    }

    /**
     * 정기배송 신청 중인 카드 목록 조회
     * 신청 중인 조건: applyStatus가 이용중 또는 일시정지(사용자 일시정지, 관리자 일시정지, 시스템 일시정지) 상태
     *
     * SELECT rog.cardNo
     * FROM es_regularOrder ro
     * JOIN es_regularOrderGoods rog ON ro.applyGroupNo = rog.applyGroupNo
     * WHERE ro.memNo = ?
     * AND rog.applyStatus IN ('active', 'userStop', 'adminStop', 'systemStop')
     * GROUP BY rog.cardNo
     *
     * @param int $memNo
     * @return array
     */
    public function findRegularPaymentCardNoList(int $memNo): array
    {
        return RegularOrder::query()
            ->select('es_regularOrderGoods.cardNo')
            ->join('es_regularOrderGoods', 'es_regularOrder.applyGroupNo', '=', 'es_regularOrderGoods.applyGroupNo')
            ->where('es_regularOrder.memNo', $memNo)
            ->whereIn('es_regularOrderGoods.applyStatus', [
                RegularOrderStatus::ACTIVE,
                RegularOrderStatus::USER_STOP,
                RegularOrderStatus::ADMIN_STOP,
                RegularOrderStatus::SYSTEM_STOP
            ])
            ->groupBy('es_regularOrderGoods.cardNo')
            ->get()
            ->toArray();
    }

    /**
     * es_regularOrderGoods.regularGoodsNo를 기준으로 es_regularOrder.applyStatus 조회
     *
     * SELECT DISTINCT applyGroupNo
     * FROM es_regularOrder
     * WHERE regularGoodsNo IN (조회 기준이 될 es_regularGoods의 sno);
     *
     * @param array $regularGoodsNoList : 조회할 데이터의 regularGoodsNo
     * @return array
     */
    public function findApplyGroupNoListByRegularGoodsNo(array $regularGoodsNoList): array
    {
        return RegularOrder::query()
            ->whereIn('regularGoodsNo', $regularGoodsNoList)
            ->pluck('applyGroupNo')
            ->unique()
            ->toArray();
    }

    /**
     * 신청그룹번호로 정기배송 신청 정보 조회
     *
     * SELECT * FROM es_regularOrder WHERE applyGroupNo = ?
     *
     * @param string $applyGroupNo
     * @return array
     */
    public function findByApplyGroupNo(string $applyGroupNo): array
    {
        $regularOrder = RegularOrder::query()
            ->where('applyGroupNo', $applyGroupNo)
            ->first();

        return $regularOrder ? $regularOrder->toArray() : [];
    }

    /**
     * 신청서의 회원 소유 여부 조회
     *
     * SELECT 1
     * FROM es_regularOrder AS ro
     * JOIN es_regularOrderGoods AS rog ON ro.applyGroupNo = rog.applyGroupNo
     * WHERE ro.memNo = ?
     * AND rog.applyNo = ?
     *
     * @param int $applyNo
     * @param int $memNo
     * @return bool
     */
    public function hasRegularOrderOwnership(int $applyNo, int $memNo): bool
    {
        return RegularOrder::query()
            ->join('es_regularOrderGoods', 'es_regularOrder.applyGroupNo', '=', 'es_regularOrderGoods.applyGroupNo')
            ->where('es_regularOrder.memNo', $memNo)
            ->where('es_regularOrderGoods.applyNo', $applyNo)
            ->exists();
    }

    /**
     * 검색 조건에 따른 정기결제 신청서 리스트 조회
     * @param RegularOrderSearchCondition $condition
     * @return array
     */
    public function findRegularOrderList(RegularOrderSearchCondition $condition): array
    {
        $query = $this->buildRegularOrderQuery($condition);

        return $query->skip(($condition->page - 1) * $condition->pageNum)
            ->take($condition->pageNum)
            ->get()
            ->toArray();
    }


    /**
     * 검색 조건에 따른 정기결제 신청서 리스트 총 개수
     * @param RegularOrderSearchCondition $condition
     * @return int
     */
    public function countTotalRegularOrderList(RegularOrderSearchCondition $condition): int
    {
        $query = $this->buildRegularOrderQuery($condition);
        return $query->count();
    }

    /**
     * 공통 쿼리 생성 메서드
     * @param RegularOrderSearchCondition $condition
     * @return \Illuminate\Database\Eloquent\Builder
     */
    private function buildRegularOrderQuery(RegularOrderSearchCondition $condition)
    {
        $query = RegularOrder::query()
            ->select([
                'es_regularOrder.applyGroupNo',
                'es_regularOrder.totalPrice',
                'es_regularOrder.memNo',
                'es_regularOrderGoods.regDt',
                'es_regularOrderGoods.modDt',
                'es_regularOrderGoods.applyNo',
                'es_regularOrderGoods.applyStatus',
                'es_regularOrderGoods.deliveryDueDate',
                'es_regularOrderGoods.deliveryRound',
                'es_regularOrderGoods.deliveryCycleType',
                'es_regularOrderGoods.deliveryCycle',
                'es_regularOrderGoods.deliveryCycleDay',
                'es_regularOrderGoods.maxDeliveryRound',
                'es_regularOrderGoods.regularGoodsCnt',
                'es_regularOrderGoods.originGoodsPrice',
                'es_regularOrderGoods.regularGoodsPolicy',
                'es_regularOrderGoods.inactiveDt',
                'es_regularOrderApplier.applierName',
                'es_regularOrderApplier.applierEmail',
                'es_regularOrderApplier.applierPhone',
                'es_regularOrderApplier.applierCellPhone',
                'es_member.memId',
                'es_memberGroup.groupNm',
                'es_goods.scmNo',
                'es_goods.goodsPrice',
                'es_goods.goodsNm',
                'es_goods.goodsCd',
                'es_regularGoods.goodsNo',
                'es_regularOrderShippingAddress.shippingName',
                'es_regularOrderShippingAddress.shippingCellPhone',
                'es_regularOrderShippingAddress.shippingPhone',
                'es_regularOrderShippingAddress.shippingZonecode',
                'es_regularOrderShippingAddress.shippingAddress',
                'es_regularOrderShippingAddress.shippingAddressSub',
                'es_regularOrderGoodsOption.originGoodsOptionPrice',
                'es_regularOrderGoodsOptionText.originGoodsOptionTextPrice',
                'es_regularOrderGoodsOption.originGoodsOptionPrice',
                'es_regularOrderGoodsOptionText.originGoodsOptionTextPrice',
            ])
            ->leftJoin('es_member', 'es_member.memNo', '=', 'es_regularOrder.memNo')
            ->leftJoin('es_memberGroup', 'es_memberGroup.sno', '=', 'es_member.groupSno')
            ->join('es_regularOrderGoods', 'es_regularOrder.applyGroupNo', '=', 'es_regularOrderGoods.applyGroupNo')
            ->join('es_regularGoods', 'es_regularGoods.sno', '=', 'es_regularOrderGoods.regularGoodsNo')
            ->join('es_goods', 'es_goods.goodsNo', '=', 'es_regularGoods.goodsNo')
            ->join('es_regularOrderApplier', 'es_regularOrderApplier.applyGroupNo', '=', 'es_regularOrder.applyGroupNo')
            ->join('es_regularOrderShippingAddress', 'es_regularOrderShippingAddress.sno', '=', 'es_regularOrderGoods.shippingAddressSno')
            ->join('es_regularOrderGoodsOption', 'es_regularOrderGoodsOption.applyNo', '=', 'es_regularOrderGoods.applyNo')
            ->leftJoin('es_regularOrderGoodsOptionText', 'es_regularOrderGoodsOptionText.applyNo', '=', 'es_regularOrderGoods.applyNo');

        // 정렬
        if ($condition->sort) {
            $sortOptions = [
                'applyDt desc' => ['es_regularOrderGoods.regDt', 'desc'],
                'applyDt asc' => ['es_regularOrderGoods.regDt', 'asc'],
                'applyNo asc' => ['es_regularOrderGoods.applyNo', 'asc'],
                'applyNo desc' => ['es_regularOrderGoods.applyNo', 'desc'],
                'regularOrderGoodsNm desc' => ['es_goods.goodsNm', 'desc'],
                'regularOrderGoodsNm asc' => ['es_goods.goodsNm', 'asc'],
                'applierNm desc' => ['es_regularOrderApplier.applierName', 'desc'],
                'applierNm asc' => ['es_regularOrderApplier.applierName', 'asc'],
                'totalRegularOrderPrice desc' => ['es_regularOrderGoods.regularGoodsPrice', 'desc'],
                'totalRegularOrderPrice asc' => ['es_regularOrderGoods.regularGoodsPrice', 'asc'],
                'shippingName desc' => ['es_regularOrderShippingAddress.shippingName', 'desc'],
                'shippingName asc' => ['es_regularOrderShippingAddress.shippingName', 'asc'],
            ];

            $orderByColumn = $sortOptions[$condition->sort][0];
            $orderBySort = $sortOptions[$condition->sort][1];
            $query->orderBy($orderByColumn, $orderBySort)
                ->orderBy('es_regularOrderGoods.applyNo', 'desc');

        } else {
            $query->orderBy('es_regularOrderGoods.regDt', 'desc')
                ->orderBy('es_regularOrderGoods.applyNo', 'desc');
        }

        // 공급사 조건
        if ($condition->scmFl === '0') {
            $query->where('es_goods.scmNo', DEFAULT_CODE_SCMNO);
        } elseif ($condition->scmFl === '1') {
            $query->whereIn('es_goods.scmNo', $condition->scmNo);
        }

        // 검색어 조건
        if ($condition->key && $condition->keyword) {
            switch ($condition->key) {
                case 'goodsNm':
                    $query->where('es_goods.goodsNm', 'like', '%' . $condition->keyword . '%');
                    break;
                case 'goodsNo':
                    $query->where('es_regularGoods.goodsNo', $condition->keyword);
                    break;
                case 'goodsCd':
                    $query->where('es_goods.goodsCd', $condition->keyword);
                    break;
                case 'applierNm':
                    $query->where('es_regularOrderApplier.applierName', 'like', '%' . $condition->keyword . '%');
                    break;
                case 'applierID':
                    $query->where('es_member.memId', 'like', '%' . $condition->keyword . '%');
                    break;
                case 'applierPhone':
                    $query->where('es_regularOrderApplier.applierPhone', 'like', '%' . $condition->keyword . '%');
                    break;
                case 'applierCellPhone':
                    $query->where('es_regularOrderApplier.applierCellPhone', 'like', '%' . $condition->keyword . '%');
                    break;
                case 'applierEmail':
                    $query->where('es_regularOrderApplier.applierEmail', 'like', '%' . $condition->keyword . '%');
                    break;
                case 'applyNo':
                    $query->where('es_regularOrderGoods.applyNo', $condition->keyword);
                    break;
                default:
                    break;
            }
        }

        // 날짜 조건
        if (!$condition->treatDate) {
            $query->whereBetween('es_regularOrderGoods.regDt', [
                date('Y-m-d 00:00:00', strtotime('-6 days')),
                date('Y-m-d 23:59:59')
            ]);
        } else {
            $query->whereBetween('es_regularOrderGoods.regDt', [
                date('Y-m-d 00:00:00', strtotime($condition->treatDate[0])),
                date('Y-m-d 23:59:59', strtotime($condition->treatDate[1])),
            ]);
        }

        // 신청 상태 조건
        if (!empty($condition->applyStatus) && $condition->applyStatus !== ['all']) {
            $query->whereIn('es_regularOrderGoods.applyStatus', $condition->applyStatus);
        }

        // 배송 주기 조건
        if ($condition->deliveryCycleType && $condition->deliveryCycleType !== 'all') {
            $query->where('es_regularOrderGoods.deliveryCycleType', $condition->deliveryCycleType);

            if ($condition->deliveryCycleType === 'month' && $condition->deliveryCycleMonth !== ['all']) {
                $query->whereIn('es_regularOrderGoods.deliveryCycle', $condition->deliveryCycleMonth);
            }

            if ($condition->deliveryCycleType === 'week' && $condition->deliveryCycleWeek !== ['all']) {
                $query->whereIn('es_regularOrderGoods.deliveryCycle', $condition->deliveryCycleWeek);
            }

            if ($condition->deliveryCycleType === 'week' && $condition->deliveryCycleDayWeek !== ['all']) {
                $query->whereIn('es_regularOrderGoods.deliveryCycleDay', $condition->deliveryCycleDayWeek);
            }
        }

        if ($condition->isProvider === true) {
            $query->join('es_regularOrderDelivery', 'es_regularOrderDelivery.applyNo', '=', 'es_regularOrderGoods.applyNo')
                ->where('es_regularOrderDelivery.scmNo', $condition->scmNo);
        }

        if (!empty($condition->applyNoList)) {
            $query->whereIn('es_regularOrderGoods.applyNo', $condition->applyNoList);
        }

        return $query;
    }

    /**
     *
     * SELECT
     * es_regularOrderGoods *,
     * es_regularGoods.regularPrice '정기결제가',
     * es_goods.goodsNo '일반상품번호',
     * es_goods.goodsNm '일반상품명',
     * es_goods.imageStorage as imageStorage '이미지 저장소',
     * es_goods.imagePath as imagePath '이미지 경로',
     * es_goodsImage.imageKind as imageKind '이미지 종류',
     * es_goodsImage.imageUrl as imageUrl '이미지 URL'
     * es_regularOrderGoodsOption.regularGoodsOptionInfo as regularGoodsOptionInfo '옵션 정보',
     * es_regularOrderGoodsOption.regularGoodsOptionPrice as regularGoodsOptionPrice '옵션 가격'
     * es_regularOrderGoodsOptionText.regularGoodsOptionTextInfo as regularGoodsOptionTextInfo '텍스트 옵션 정보',
     * es_regularOrderGoodsOptionText.regularGoodsOptionTextPrice as regularGoodsOptionTextPrice '텍스트 옵션 가격'
     * FROM es_regularOrder
     * JOIN es_regularOrderGoods ON es_regularOrder.applyGroupNo = es_regularOrderGoods.applyGroupNo
     * JOIN es_regularGoods ON es_regularGoods.sno = es_regularOrderGoods.regularGoodsNo
     * JOIN es_goods ON es_goods.goodsNo = es_regularGoods.goodsNo
     * LEFT JOIN es_goodsImage ON es_goodsImage.goodsNo = es_goods.goodsNo
     * LEFT JOIN es_regularOrderGoodsOption ON es_regularOrderGoodsOption.applyNo = es_regularOrderGoods.applyNo
     * LEFT JOIN es_regularOrderGoodsOptionText ON es_regularOrderGoodsOptionText.applyNo = es_regularOrderGoods.applyNo
     * WHERE es_regularOrder.memNo = :memNo
     * AND es_regularOrderGoods.regDt BETWEEN :startDate AND :endDate
     * GROUP BY es_regularOrderGoods.applyNo
     * ORDER BY es_regularOrderGoods.applyNo DESC
     * LIMIT :offset, :limit
     *
     * @param int $memNo
     * @param string $startDate
     * @param string $endDate
     * @param int $pageNum
     * @param int $pageSize
     * @return array
     */
    public function getRegularOrderListBetweenDate(int $memNo, string $startDate, string $endDate, int $pageNum = 1, int $pageSize = 10): array
    {
        return RegularOrder::query()
            ->join('es_regularOrderGoods', 'es_regularOrder.applyGroupNo', '=', 'es_regularOrderGoods.applyGroupNo')
            ->join('es_regularGoods', 'es_regularGoods.sno', '=', 'es_regularOrderGoods.regularGoodsNo')
            ->join('es_goods', 'es_goods.goodsNo', '=', 'es_regularGoods.goodsNo')
            ->join('es_regularOrderShippingAddress', 'es_regularOrderShippingAddress.sno', '=', 'es_regularOrderGoods.shippingAddressSno')
            ->leftjoin('es_goodsImage', function($join) {
                $join->on('es_goods.goodsNo', '=', 'es_goodsImage.goodsNo')
                    ->where('es_goodsImage.imageKind', '=', 'list');
            })
            ->leftJoin('es_regularOrderGoodsOption', 'es_regularOrderGoodsOption.applyNo', '=', 'es_regularOrderGoods.applyNo')
            ->leftJoin('es_regularOrderGoodsOptionText', 'es_regularOrderGoodsOptionText.applyNo', '=', 'es_regularOrderGoods.applyNo')
            ->select(
                'es_regularOrderGoods.*',
                'es_regularGoods.regularPrice',
                'es_goods.goodsNo',
                'es_goods.goodsNm',
                'es_goods.imagePath as imagePath',
                'es_goods.imageStorage as imageStorage',
                'es_goodsImage.goodsImageStorage as goodsImageStorage',
                'es_goodsImage.imageUrl',
                'es_goodsImage.imageName',
                'es_regularOrderGoodsOption.regularGoodsOptionInfo as regularGoodsOptionInfo',
                'es_regularOrderGoodsOption.regularGoodsOptionPrice as regularGoodsOptionPrice',
                'es_regularOrderGoodsOptionText.regularGoodsOptionTextInfo as regularGoodsOptionTextInfo',
                'es_regularOrderGoodsOptionText.regularGoodsOptionTextPrice as regularGoodsOptionTextPrice'
            )
            ->where('es_regularOrder.memNo', $memNo)
            ->whereBetween('es_regularOrderGoods.regDt', [$startDate.' 00:00:00', $endDate. ' 23:59:59'])
            ->orderBy('es_regularOrderGoods.applyNo', 'desc')
            ->groupBy('es_regularOrderGoods.applyNo')
            ->skip(($pageNum - 1) * $pageSize)
            ->take($pageSize)
            ->get()
            ->toArray();
    }

    /**
     * 정기결제 신청서 리스트 총 개수
     *
     * @param int $memNo
     * @param string $startDate
     * @param string $endDate
     * @return int
     */
    public function countTotalRegularOrderListBetweenDate(int $memNo, string $startDate, string $endDate): int
    {
        return RegularOrder::query()
            ->join('es_regularOrderGoods', 'es_regularOrder.applyGroupNo', '=', 'es_regularOrderGoods.applyGroupNo')
            ->join('es_regularOrderShippingAddress', 'es_regularOrderShippingAddress.sno', '=', 'es_regularOrderGoods.shippingAddressSno')
            ->where('es_regularOrder.memNo', $memNo)
            ->whereBetween('es_regularOrderGoods.regDt', [$startDate.' 00:00:00', $endDate. ' 23:59:59'])
            ->count();
    }
    /**
     * SELECT es_regularOrder.*, es_regularOrderGoods.*, es_regularOrderShippingAddress.shippingAddress, es_regularOrderShippingAddress.shippingAddressSub
     * FROM es_regularOrder
     * JOIN es_regularOrderGoods ON es_regularOrderGoods.applyGroupNo = es_regularOrder.applyGroupNo
     * JOIN es_regularOrderShippingAddress ON es_regularOrderShippingAddress.memNo = es_regularOrder.memNo
     * WHERE es_regularOrder.applyGroupNo = ?
     * ORDER BY es_regularOrderGoods.applyNo ASC
     * LIMIT 1
     *
     * 정기배송 신청 완료 후 신청 정보 데이터 조회
     * @param string $applyGroupNo
     * @return array
     */
    public function findRegularOrderInfoByApplyGroupNo(string $applyGroupNo): array
    {
        $regularGoods = RegularOrder::query()
        ->select([
            'es_regularOrder.*',
            'es_regularOrderGoods.*',
            'es_regularOrderShippingAddress.shippingAddress',
            'es_regularOrderShippingAddress.shippingAddressSub'
        ])
        ->join('es_regularOrderGoods', 'es_regularOrderGoods.applyGroupNo', '=', 'es_regularOrder.applyGroupNo')
        ->join('es_regularOrderShippingAddress', 'es_regularOrderShippingAddress.memNo', '=', 'es_regularOrder.memNo')
        ->where('es_regularOrder.applyGroupNo', $applyGroupNo)
        ->orderBy('es_regularOrderGoods.applyNo')
        ->first();

        if ($regularGoods) {
            return $regularGoods->toArray();
        }

        return [];
    }

    /*
     * 정기배송 신청 완료 후 신청 정보 데이터 조회
     *
     * SELECT es_regularOrderGoods.applyNo
     * FROM es_regularOrder
     * JOIN es_regularOrderGoods ON es_regularOrderGoods.applyGroupNo = es_regularOrder.applyGroupNo
     * WHERE es_regularOrder.applyGroupNo = ?
     * ORDER BY es_regularOrderGoods.applyNo
     *
     * @param string $applyGroupNo
     * @return array
     */
    public function findRegularOrderApplyNoListByApplyGroupNo(string $applyGroupNo): array
    {
        return RegularOrder::query()
        ->select([
            'es_regularOrderGoods.applyNo',
        ])
        ->join('es_regularOrderGoods', 'es_regularOrderGoods.applyGroupNo', '=', 'es_regularOrder.applyGroupNo')
        ->where('es_regularOrder.applyGroupNo', $applyGroupNo)
        ->orderBy('es_regularOrderGoods.applyNo', 'asc')
        ->get()
        ->toArray();
    }
}
