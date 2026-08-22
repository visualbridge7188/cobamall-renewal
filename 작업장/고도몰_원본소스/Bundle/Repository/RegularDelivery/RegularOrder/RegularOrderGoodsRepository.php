<?php
/*
 * Copyright (C) 2025 NHN COMMERCE. - All Rights Reserved
 *
 * Unauthorized copying or redistribution of this file in source and binary forms via any medium
 * is strictly prohibited.
 */

namespace Bundle\Repository\RegularDelivery\RegularOrder;

use DTO\RegularDelivery\RegularOrder\RegularOrderSearchCondition;
use DTO\RegularDelivery\RegularOrder\RegularOrderGoodsCreateDTO;
use DTO\RegularDelivery\RegularOrder\RegularOrderGoodsDeliveryInfoUpdateDTO;
use DTO\RegularDelivery\RegularOrder\RegularOrderGoodsSkipRoundDTO;
use Origin\Enum\RegularDelivery\RegularOrder\RegularOrderStatus;
use Origin\Model\RegularDelivery\RegularOrder\RegularOrderGoods;

class RegularOrderGoodsRepository
{
    /**
     *
     * 정기배송 신청 주문 생성
     *
     * INSERT INTO es_regularOrderGoods (
     * applyGroupNo, applyStatus, regularGoodsNo, regularGoodsNm,
     * regularGoodPrice, regularGoodsCnt, originGoodsPrice, discountPrice, shippingAddressSno, cardNo,
     * deliveryCycleType, deliveryCycle, deliveryCycleDay, deliveryRound, maxDeliveryRound,
     * deliveryDueDate, orderCreateDate, regularGoodsPolicy, regDt)
     *  VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?);
     *
     * @param RegularOrderGoodsCreateDTO $dto
     * @return int
     */
    public function insertRegularOrderGoods(RegularOrderGoodsCreateDTO $dto): int
    {
        return RegularOrderGoods::query()
            ->insertGetId([
                'applyGroupNo'         => $dto->getApplyGroupNo(),       // 신청그룹번호
                'applyStatus'          => $dto->getApplyStatus(),        // 신청상태
                'regularGoodsNo'       => $dto->getRegularGoodsNo(),     // 정기상품 번호
                'regularGoodsNm'       => $dto->getRegularGoodsNm(),     // 정기상품 이름
                'regularGoodsPrice'    => $dto->getRegularGoodsPrice(),  // 정기상품 가격
                'regularGoodsCnt'      => $dto->getRegularGoodsCnt(),    // 정기상품 수량
                'originGoodsPrice'     => $dto->getOriginGoodsPrice(),   // 원본 상품 가격
                'discountPrice'        => $dto->getDiscountPrice(),      // 정기 결제 할인 가격
                'shippingAddressSno'   => $dto->getShippingAddressSno(), // 배송지 주소 참조 번호
                'mileageInfo'          => $dto->getMileageInfo(),        // 마일리지 정보
                'cardNo'               => $dto->getCardNo(),             // 카드번호
                'deliveryCycleType'    => $dto->getDeliveryCycleType(), // 배송 주기 선택 타입
                'deliveryCycle'        => $dto->getDeliveryCycle(),     // 배송주기 (1~6 개월/주)
                'deliveryCycleDay'     => $dto->getDeliveryCycleDay(),  // 배송주기 (일/요일)
                'deliveryRound'        => $dto->getDeliveryRound(),      // 배송회차
                'maxDeliveryRound'     => $dto->getMaxDeliveryRound(),   // 최대 배송회차
                'deliveryDueDate'      => $dto->getDeliveryDueDate(),    // 배송예정일
                'orderCreateDate'      => $dto->getOrderCreateDate(),    // 주문서 생성 예정일
                'regularGoodsPolicy'   => $dto->getRegularGoodsPolicy(),  // 신청 시점 할인 정책
                'regDt'                => $dto->getRegDt(),              // 등록일 (현재 시간으로 설정)
        ]);
    }

    /**
     * 정기배송 상품 정보 업데이트
     *
     * UPDATE es_regularOrderGoods
     * SET regularGoodsNo = ?, regularGoodsNm = ?, regularGoodsPrice = ?, originGoodsPrice = ?,
     * discountPrice = ?, regularGoodsCnt = ?, mileageInfo = ?, regularGoodsPolicy = ?, modDt = ?
     * WHERE applyNo = ?
     *
     * @param array $regularOrderGoodsInfo
     * @return void
     */
    public function updateRegularOrderGoodsByApplyNo(array $regularOrderGoodsInfo)
    {
        RegularOrderGoods::query()
            ->where('applyNo', $regularOrderGoodsInfo['applyNo'])
            ->update([
                'regularGoodsNo' => $regularOrderGoodsInfo['regularGoodsNo'],
                'regularGoodsNm' => $regularOrderGoodsInfo['regularGoodsNm'],
                'regularGoodsPrice' => $regularOrderGoodsInfo['regularGoodsPrice'],
                'originGoodsPrice' => $regularOrderGoodsInfo['originGoodsPrice'],
                'discountPrice' => $regularOrderGoodsInfo['discountPrice'],
                'regularGoodsCnt' => $regularOrderGoodsInfo['regularGoodsCnt'],
                'mileageInfo' => $regularOrderGoodsInfo['mileageInfo'],
                'regularGoodsPolicy' => $regularOrderGoodsInfo['regularGoodsPolicy'],
                'modDt' => date('Y-m-d H:i:s')
            ]);
    }

    /**
     * regularGoodsNo를 기준으로 applyStatus 조회
     *
     * SELECT applyNo
     * FROM es_regularOrderGoods
     * WHERE regularGoodsNo IN (조회 기준이 될 es_regularGoods의 sno);
     *
     * @param array $regularGoodsNoList : 조회할 데이터의 regularGoodsNo
     * @return array
     */
    public function findApplyStatusByRegularGoodsNoList(array $regularGoodsNoList): array
    {
        return RegularOrderGoods::query()
            ->select('applyNo', 'applyStatus')
            ->whereIn('regularGoodsNo', $regularGoodsNoList)
            ->get()
            ->toArray();
    }

    /**
     * regularGoodsNo를 기준으로 applyStatus 조회
     *
     * SELECT applyNo, applyStatus
     * FROM es_regularOrderGoods
     * WHERE regularGoodsNo IN (?, ?, ?, ...);
     *
     * @param array $regularGoodsNoList : 조회할 데이터의 regularGoodsNoList
     * @return array
     */
    public function findApplyNoApplyStatusListByRegularGoodsNo(array $regularGoodsNoList): array
    {
        return RegularOrderGoods::query()
            ->select('applyNo', 'applyStatus')
            ->whereIn('regularGoodsNo', $regularGoodsNoList)
            ->get()
            ->toArray();
    }

    /**
     * 총 정기신청서 수 반환
     * @param RegularOrderSearchCondition $condition
     * @return int
     */
    public function countTotalRegularOrderList(RegularOrderSearchCondition $condition): int
    {
        return RegularOrderGoods::query()
            ->join('es_regularOrderDelivery', 'es_regularOrderDelivery.applyNo', '=', 'es_regularOrderGoods.applyNo')
            ->join('es_regularOrderShippingAddress', 'es_regularOrderShippingAddress.sno', '=', 'es_regularOrderGoods.shippingAddressSno')
            ->when($condition->isProvider === true, function ($query) use ($condition) {
                return $query->where('es_regularOrderDelivery.scmNo', $condition->scmNo);
            })
            ->count('*');
    }

    /**
     * 동일한 상태값을 가진 신청번호가 존재하는지 확인
     *
     * SELECT 1 FROM es_regularOrderGoods WHERE applyNo IN (?, ?, ?) AND applyStatus = ? LIMIT 1;
     *
     * @param array $applyNoList
     * @param array $applyStatus
     * @return bool
     */
    public function existRegularOrderGoodsByApplyNoListAndStatus(array $applyNoList, array $applyStatus): bool
    {
        return RegularOrderGoods::query()
            ->whereIn('applyNo', $applyNoList)
            ->whereIn('applyStatus', $applyStatus)
            ->exists();
    }

    /**
     * 정기결제 상품 품절 확인
     *
     * SELECT 1
     * FROM es_regularOrderGoods AS rog
     * INNER JOIN es_regularGoods AS rg ON rog.regularGoodsNo = rg.sno
     * INNER JOIN es_goods AS g ON rg.goodsNo = g.goodsNo
     * WHERE rog.applyNo IN (?, ?, ?...)
     * AND (
     *   g.soldOutFl = 'y'
     *   OR (g.stockFl = 'y' AND g.totalStock <= 0)
     * )
     * LIMIT 1;
     * 
     * 참고: 재고가 있는 경우 
     * 1. stockFl = 'n' 인 경우 - 무제한 재고
     * 2. stockFl = 'y' AND totalStock > 0 인 경우 - 재고 있음
     * 
     * 참고: 품절 상태
     * 1. soldOutFl = 'y' 이거나
     * 2. stockFl = 'y' AND totalStock <= 0 인 경우
     * 
     * @param array $applyNoList
     * @return bool
     */
    public function existSoldOutGoodsByApplyNoList(array $applyNoList): bool
    {
        return RegularOrderGoods::query()
            ->join('es_regularGoods', 'es_regularOrderGoods.regularGoodsNo', '=', 'es_regularGoods.sno')
            ->join('es_goods', 'es_goods.goodsNo', '=', 'es_regularGoods.goodsNo')
            ->whereIn('es_regularOrderGoods.applyNo', $applyNoList)
            ->where(function ($query) {
                $query
                    ->where('es_goods.soldOutFl', 'y')
                    ->orWhere(function ($subQuery) {
                        $subQuery
                            ->where('es_goods.stockFl', 'y')
                            ->where('es_goods.totalStock', '<=', 0);
                    });
                }
            )
            ->exists();
    }

    /**
     * 추가상품 재고 품절 확인
     *
     * SELECT 1
     * FROM es_regularOrderGoods
     * INNER JOIN es_regularOrderAddGoods
     * ON es_regularOrderAddGoods.applyNo = es_regularOrderGoods.applyNo
     * INNER JOIN es_addGoods
     * ON es_addGoods.addGoodsNo = es_regularOrderAddGoods.regularAddGoodsNo
     * WHERE es_regularOrderGoods.applyNo IN (?, ?, ?...)
     * AND (
     * es_addGoods.soldOutFl = 'y'
     * OR (
     * es_addGoods.stockUseFl = '1'
     * AND es_addGoods.stockCnt <= 0
     * )
     * )
     * LIMIT 1;
     * 
     * 참고: stockUseFl - 재고사용여부(0:재고제한없음, 1:재고사용)
     * 
     * 참고: 재고가 있는 경우
     * 1. 재고 제한이 없는 상품 (stockUseFl = 0)
     * 2. 또는 다음 조건을 모두 만족하는 경우
     *   - 품절상태가 아님 (soldOutFl = 'n')
     *   - 재고관리를 사용함 (stockUseFl = 1)
     *   - 실제 재고가 있음 (stockCnt > 0)
     *
     * @param array $applyNoList
     * @return mixed
     */
    public function existSoldOutAddGoodsByApplyNoList(array $applyNoList)
    {
        return RegularOrderGoods::query()
            ->join('es_regularOrderAddGoods', 'es_regularOrderAddGoods.applyNo', '=', 'es_regularOrderGoods.applyNo')
            ->join('es_addGoods', 'es_addGoods.addGoodsNo', '=', 'es_regularOrderAddGoods.regularAddGoodsNo')
            ->whereIn('es_regularOrderGoods.applyNo', $applyNoList)
            ->where(function ($query) {
                $query->where('es_addGoods.soldOutFl', 'y')
                    ->orWhere(function ($subQuery) {
                        $subQuery->where('es_addGoods.stockUseFl', '1')
                            ->where('es_addGoods.stockCnt', '<=', 0);
                    });
            })
            ->exists();
    }

    /**
     * SELECT *
     * FROM es_regularOrderGoods
     * JOIN es_regularOrder ON es_regularOrderGoods.applyGroupNo = es_regularOrder.applyGroupNo
     * WHERE es_regularOrderGoods.applyNo IN (?, ?, ?...)
     * @param array $applyNoList
     * @return array
     */
    public function findRegularOrderInfoByApplyNoList(array $applyNoList): array
    {
        return RegularOrderGoods::query()
            ->join('es_regularOrder', 'es_regularOrderGoods.applyGroupNo', '=', 'es_regularOrder.applyGroupNo')
            ->whereIn('es_regularOrderGoods.applyNo', $applyNoList)
            ->get()
            ->toArray();
    }

    /**
     * 정기배송 신청서 상태 업데이트
     *
     * UPDATE es_regularOrderGoods SET applyStatus = ?, modDt = ? WHERE applyNo IN (?, ?, ?..);
     * @param array $applyNoList
     * @param string $updateStatus
     * @return void
     */
    public function updateApplyStatusByApplyNoList(array $applyNoList, string $updateStatus)
    {
        $updateData = [
            'applyStatus' => $updateStatus,
            'modDt' => date('Y-m-d H:i:s')
        ];

        if (in_array($updateStatus, RegularOrderStatus::getPauseStatus())) {
            $updateData['pauseDt'] = date('Y-m-d H:i:s');
        }

        if (in_array($updateStatus, RegularOrderStatus::getInactiveStatus())) {
            $updateData['inactiveDt'] = date('Y-m-d H:i:s');
        }

        RegularOrderGoods::query()
            ->whereIn('applyNo', $applyNoList)
            ->update($updateData);
    }

    /**
     * @param array $applyNoList
     * @return array
     */
    public function findCurrentApplyStatusByApplyNoList(array $applyNoList): array
    {
        return RegularOrderGoods::query()
            ->select('applyNo', 'applyStatus')
            ->whereIn('applyNo', $applyNoList)
            ->get()
            ->toArray();
    }

    /**
     * 최종 배송회차까지 1회차가 남지 않은 신청서가 있는지 확인
     * maxDeliveryRound 0 은 무제한 배송임으로 제외
     *
     * @param array $applyNoList
     * @return bool
     */
    public function existExpiredDeliveryRoundsByApplyNoList(array $applyNoList): bool
    {
        return RegularOrderGoods::query()
            ->whereIn('applyNo', $applyNoList)
            ->where('maxDeliveryRound', '>', 0)
            ->whereRaw('maxDeliveryRound - deliveryRound <= 0')
            ->exists();
    }

    /**
     * applyNo 에 해당하는 신청서 조회
     *
     * @param int $applyNo
     * @return array
     */
    public function findRegularOrderGoodsByApplyNo(int $applyNo): array
    {
        $regularGoods = RegularOrderGoods::query()
            ->select([
                'es_regularOrderGoods.*',
                'es_goods.goodsNo',
                'es_goods.imagePath',
                'es_goods.imageStorage',
                'es_goodsImage.goodsImageStorage',
                'es_goodsImage.imageKind',
                'es_goodsImage.imageUrl',
                'es_goodsImage.imageName',
                'es_regularOrderGoodsOption.optionSno',
                'es_regularOrderGoodsOption.regularGoodsOptionInfo',
                'es_regularOrderGoodsOptionText.regularGoodsOptionTextInfo',
                'es_regularOrderGoodsOption.originGoodsOptionPrice',
                'es_regularOrderGoodsOption.regularGoodsOptionPrice',
                'es_regularOrderGoodsOptionText.originGoodsOptionTextPrice',
                'es_regularOrderGoodsOptionText.regularGoodsOptionTextPrice',
                'es_scmManage.companyNm',
                'es_categoryBrand.cateNm as brandNm'
            ])
            ->join('es_regularGoods', 'es_regularGoods.sno', '=', 'es_regularOrderGoods.regularGoodsNo')
            ->join('es_goods', 'es_goods.goodsNo', '=', 'es_regularGoods.goodsNo')
            ->join('es_scmManage', 'es_scmManage.scmNo', '=', 'es_goods.scmNo')
            ->leftJoin('es_categoryBrand', 'es_categoryBrand.cateCd', '=', 'es_goods.brandCd')
            ->join('es_regularOrderGoodsOption', 'es_regularOrderGoodsOption.applyNo', '=', 'es_regularOrderGoods.applyNo')
            ->leftJoin('es_regularOrderGoodsOptionText', 'es_regularOrderGoodsOptionText.applyNo', '=', 'es_regularOrderGoods.applyNo')
            ->leftJoin('es_goodsImage', function ($join) {
                $join->on('es_goodsImage.goodsNo', '=', 'es_goods.goodsNo')
                    ->where('es_goodsImage.imageKind', '=', 'main');
            })
            ->where('es_regularOrderGoods.applyNo', $applyNo)
            ->first();

        if ($regularGoods) {
            return $regularGoods->toArray();
        }

        return [];
    }

    /**
     * 현재 배송회차, 배송주기 조회
     *
     * SELECT deliveryRound, deliveryCycleType, deliveryCycle, deliveryCycleDay, maxDeliveryRound, deliveryDueDate FROM es_regularOrderGoods where applyNo = ?
     *
     * @param int $applyNo
     * @return array
     */
    public function findCurrentDeliveryRoundByApplyNo(int $applyNo): array
    {
        $regularGoods = RegularOrderGoods::query()
            ->select([
                'deliveryRound', 'deliveryCycleType', 'deliveryCycle', 'deliveryCycleDay', 'maxDeliveryRound', 'deliveryDueDate'
            ])
            ->where('applyNo', $applyNo)
            ->first();

        if ($regularGoods) {
            return $regularGoods->toArray();
        }

        return [];
    }

    /**
     * 배송주기 업데이트
     *
     * UPDATE es_regularOrderGoods
     * SET deliveryCycleType = ?, deliveryCycle = ?, deliveryCycleDay = ?, maxDeliveryRound = ?, deliveryDueDate = ?, orderCreateDate = ?, modDt = ?
     * WHERE applyNo = ?
     *
     * @param RegularOrderGoodsDeliveryInfoUpdateDTO $dto
     * @return void
     */
    public function updateRegularOrderGoodsDeliveryInfoByApplyNo(RegularOrderGoodsDeliveryInfoUpdateDTO $dto)
    {
        RegularOrderGoods::query()
            ->where('applyNo', $dto->getApplyNo())
            ->update([
                'deliveryCycleType' => $dto->getDeliveryCycleType(),
                'deliveryCycle' => $dto->getDeliveryCycle(),
                'deliveryCycleDay' => $dto->getDeliveryCycleDay(),
                'maxDeliveryRound' => $dto->getMaxDeliveryRound(),
                'deliveryDueDate' => $dto->getDeliveryDueDate(),
                'orderCreateDate' => $dto->getOrderCreateDate(),
                'modDt' => date('Y-m-d H:i:s')
            ]);
    }

    /**
     * 배송회차 건너뛰기
     * UPDATE es_regularOrderGoods
     * SET deliveryDueDate = ?, orderCreateDate = ?, deliveryRoundSkipFl = 'y', modDt = ?
     * WHERE applyNo = ?
     * @param RegularOrderGoodsSkipRoundDTO $dto
     * @return void
     */
    public function updateForSkipRegularOrderGoodsByApplyNo(RegularOrderGoodsSkipRoundDTO $dto)
    {
        RegularOrderGoods::query()
            ->where('applyNo', $dto->getApplyNo())
            ->update([
                'deliveryDueDate' => $dto->getDeliveryDueDate(),
                'orderCreateDate' => $dto->getOrderCreateDate(),
                'deliveryRoundSkipFl' => 'y',
                'modDt' => date('Y-m-d H:i:s')
            ]);
    }

    /**
     * 신청서에 해당하는 배송지 정보 조회
     *
     * SELECT es_regularOrderShippingAddress.*, es_member.smsFl
     * FROM es_regularOrderGoods
     * JOIN es_regularOrderShippingAddress on es_regularOrderShippingAddress.sno = es_regularOrderGoods.shippingAddressSno
     * WHERE es_regularOrderGoods.applyNo = ?
     *
     * @param int $applyNo
     * @return array
     */
    public function findShippingInfoByApplyNo(int $applyNo): array
    {
        $shipping = RegularOrderGoods::query()
            ->select('es_regularOrderShippingAddress.*', 'es_member.smsFl')
            ->leftJoin('es_regularOrderShippingAddress', 'es_regularOrderShippingAddress.sno', '=', 'es_regularOrderGoods.shippingAddressSno')
            ->leftJoin('es_member', 'es_member.memNo', '=', 'es_regularOrderShippingAddress.memNo')
            ->where('es_regularOrderGoods.applyNo', $applyNo)
            ->first();

        if ($shipping) {
            return $shipping->toArray();
        }

        return [];
    }

    /**
     * 신청서 배송정보 변경
     *
     * UPDATE es_regularOrderGoods SET shippingAddressSno = ? WHERE applyNo = ?
     * @param int $applyNo
     * @param int $shippingSno
     * @return void
     */
    public function updateRegularOrderShippingSnoByApplyNo(int $applyNo, int $shippingSno)
    {
        RegularOrderGoods::query()
            ->where('applyNo', $applyNo)
            ->update([
                'shippingAddressSno' => $shippingSno
            ]);
    }

    /**
     * 신청서 결제정보 변경
     *
     * UPDATE es_regularOrderGoods
     * SET cardNo = ?
     * WHERE applyNo = ?
     *
     * @param int $applyNo
     * @param string $cardNo
     * @return void
     */
    public function updateRegularOrderCardNoByApplyNo(int $applyNo, string $cardNo)
    {
        RegularOrderGoods::query()
            ->where('applyNo', $applyNo)
            ->update([
                'cardNo' => $cardNo
            ]);
    }

    /**
     * 신청 번호로 신청 그룹번호 조회
     *
     * SELECT applyGroupNo
     * FROM es_regularOrderGoods
     * WHERE applyNo = ?
     *
     * @param int $applyNo
     * @return int
     */
    public function findApplyGroupNoByApplyNo(int $applyNo): int
    {
        return RegularOrderGoods::query()
            ->where('applyNo', $applyNo)
            ->value('applyGroupNo');
    }

    /**
     * 신청 번호가 속한 신청그룹번호에 대한 신청서 데이터 조회
     *
     * SELECT
     * es_regularOrderGoods.applyGroupNo, es_regularOrderGoods.regularGoodsNo,
     * es_regularOrderGoods.regularGoodsPrice, es_regularOrderGoods.regularGoodsCnt,
     * es_regularOrderAddGoods.regularAddGoodsPrice, es_regularOrderAddGoods.regularAddGoodsCnt,
     * es_regularOrderDelivery.deliverySno,
     * es_regularOrderShippingAddress.shippingAddress,
     * es_regularOrderGoodsOption.regularGoodsOptionPrice,
     * es_regularOrderGoodsOptionText.regularGoodsOptionTextPrice,
     * es_goods.goodsWeight
     * FROM es_regularOrderGoods
     * LEFT JOIN es_regularOrderAddGoods ON es_regularOrderGoods.applyNo = es_regularOrderAddGoods.applyNo
     * LEFT JOIN es_regularOrderDelivery ON es_regularOrderGoods.applyNo = es_regularOrderDelivery.applyNo
     * LEFT JOIN es_regularOrderShippingAddress ON es_regularOrderGoods.ShippingAddressSno = es_regularOrderShippingAddress.sno
     * LEFT JOIN es_regularOrderGoodsOption ON es_regularOrderGoods.applyNo = es_regularOrderGoodsOption.applyNo
     * LEFT JOIN es_regularOrderGoodsOptionText ON es_regularOrderGoods.applyNo = es_regularOrderGoodsOptionText.applyNo
     * LEFT JOIN es_regularGoods ON es_regularOrderGoods.regularGoodsNo = es_regularGoods.sno
     * LEFT JOIN es_goods ON es_regularGoods.goodsNo = es_goods.goodsNo
     * WHERE es_regularOrderGoods.applyGroupNo =
     * ( SELECT es_regularOrderGoods.applyGroupNo FROM es_regularOrderGoods WHERE es_regularOrderGoods.applyNo = {applyNo} LIMIT 1 )
     * AND es_regularOrderGoods.applyStatus = 'active'
     * ORDER BY es_regularOrderDelivery.deliverySno;
     *
     * @param string $applyGroupNo
     * @return int
     */
    public function findRegularOrderInfoByApplyNo(string $applyGroupNo): array
    {
        return RegularOrderGoods::query()
            ->leftJoin('es_regularOrderDelivery', 'es_regularOrderGoods.applyNo', '=', 'es_regularOrderDelivery.applyNo')
            ->leftJoin('es_regularOrderShippingAddress', 'es_regularOrderGoods.ShippingAddressSno', '=', 'es_regularOrderShippingAddress.sno')
            ->leftJoin('es_regularOrderGoodsOption', 'es_regularOrderGoods.applyNo', '=', 'es_regularOrderGoodsOption.applyNo')
            ->leftJoin('es_regularOrderGoodsOptionText', 'es_regularOrderGoods.applyNo', '=', 'es_regularOrderGoodsOptionText.applyNo')
            ->leftJoin('es_regularGoods', 'es_regularOrderGoods.regularGoodsNo', '=', 'es_regularGoods.sno')
            ->leftJoin('es_goods', 'es_regularGoods.goodsNo', '=', 'es_goods.goodsNo')
            ->where('es_regularOrderGoods.applyGroupNo', $applyGroupNo)
            ->where('es_regularOrderGoods.applyStatus', 'active')
            ->select([
                'es_regularOrderGoods.applyNo',
                'es_regularOrderGoods.applyGroupNo',
                'es_regularOrderGoods.regularGoodsNo',
                'es_regularOrderGoods.regularGoodsPrice',
                'es_regularOrderGoods.regularGoodsCnt',
                'es_regularOrderDelivery.deliverySno',
                'es_regularOrderShippingAddress.shippingAddress',
                'es_regularOrderGoodsOption.regularGoodsOptionPrice',
                'es_regularOrderGoodsOptionText.regularGoodsOptionTextPrice',
                'es_goods.goodsWeight'
            ])
            ->get()
            ->toArray();
    }

    /**
     * 신청 번호로 정기배송 상품 조회
     *
     * SELECT * FROM es_regularOrderGoods WHERE applyNo = ?
     *
     * @param int $applyNo
     * @return array
     */
    public function findRegularOrderGoodsInfoByApplyNo(int $applyNo): array
    {
        $regularOrderGoods = RegularOrderGoods::query()
            ->join('es_regularOrder', 'es_regularOrderGoods.applyGroupNo', '=', 'es_regularOrder.applyGroupNo')
            ->where('applyNo', $applyNo)
            ->first();

        return $regularOrderGoods ? $regularOrderGoods->toArray() : [];
    }

    /**
     * 신청 번호 리스트로 정기배송 상품 조회
     * 
     * SELECT 
     *   es_regularOrder.*
     *   es_regularOrderGoods.*
     * FROM es_regularOrderGoods
     * JOIN es_regularOrder ON es_regularOrderGoods.applyGroupNo = es_regularOrder.applyGroupNo
     * WHERE es_regularOrderGoods.applyNo IN (?)
     * 
     * 
     * SELECT 
     *   es_regularOrder.*
     *   es_regularOrderGoods.*
     * FROM es_regularOrderGoods
     * JOIN es_regularOrder ON es_regularOrderGoods.applyGroupNo = es_regularOrder.applyGroupNo
     * WHERE es_regularOrderGoods.applyNo IN (?)
     * 
     * @param array $applyNoList
     * @return array
     */
    public function findRegularOrderGoodsInfoByApplyNoList(array $applyNoList): array
    {
        return RegularOrderGoods::query()
            ->select(
                'es_regularOrder.*',
                'es_regularOrderGoods.*'
            )
            ->join('es_regularOrder', 'es_regularOrderGoods.applyGroupNo', '=', 'es_regularOrder.applyGroupNo')
            ->whereIn('es_regularOrderGoods.applyNo', $applyNoList)
            ->get()
            ->toArray();
    }

    /**
     * 신청그룹번호 리스트로 정기배송 상품 조회
     * 
     * SELECT 
     *   es_regularOrder.*
     *   es_regularOrderGoods.*
     * FROM es_regularOrderGoods
     * JOIN es_regularOrder ON es_regularOrderGoods.applyGroupNo = es_regularOrder.applyGroupNo
     * WHERE es_regularOrderGoods.applyGroupNo IN (?)
     * ORDER BY es_regularOrderGoods.applyNo ASC
     * 
     * @param array $applyGroupNoList
     * @return array
     */
    public function findRegularOrderGoodsInfoByApplyGroupNoList(array $applyGroupNoList): array
    {
        return RegularOrderGoods::query()
            ->select(
                'es_regularOrder.*',
                'es_regularOrderGoods.*'
            )
            ->join('es_regularOrder', 'es_regularOrderGoods.applyGroupNo', '=', 'es_regularOrder.applyGroupNo')
            ->whereIn('es_regularOrderGoods.applyGroupNo', $applyGroupNoList)
            ->orderBy('es_regularOrderGoods.applyNo', 'asc')
            ->get()
            ->toArray();
    }

    /**
     *  UPDATE es_regularOrderGoods
     *  SET applyStatus = ?, modDt = ?, pauseDt = ?
     *  WHERE applyNo = ?
     *
     * 신청 번호로 신청상태 정보 업데이트
     * @param int $applyNo
     * @param string $applyStatus
     * @return void
     */
    public function updateApplyStatusByApplyNo(int $applyNo, string $applyStatus)
    {
        $updateData = [
            'applyStatus' => $applyStatus,
            'modDt' => date('Y-m-d H:i:s')
        ];

        if (in_array($applyStatus, RegularOrderStatus::getPauseStatus())) {
            $updateData['pauseDt'] = date('Y-m-d H:i:s');
        }

        RegularOrderGoods::query()
            ->where('applyNo', $applyNo)
            ->update($updateData);
    }

    /**
     * 신청 번호로 신청상태, 배송예정일, 주문생성일 정보 업데이트
     * @param int $applyNo
     * @param string $applyStatus
     * @param string $deliveryDueDate
     * @param string $orderCreateDate
     * @return void
     */
    public function updateResumeInfoByApplyNo(int $applyNo, string $applyStatus, string $deliveryDueDate, string $orderCreateDate)
    {
        RegularOrderGoods::query()
            ->where('applyNo', $applyNo)
            ->update([
                'applyStatus' => $applyStatus,
                'deliveryDueDate' => $deliveryDueDate,
                'orderCreateDate' => $orderCreateDate,
                'modDt' => date('Y-m-d H:i:s')
            ]);
    }

    /**
     * 배송정보 변경 관련 데이터 조회
     * 
     * SELECT 
     *   es_regularOrderGoods.*
     *   es_goods.goodsNo,
     *   es_goods.goodsNm,
     *   es_regularOrderShippingAddress.sno
     *   es_regularOrderShippingAddress.shippingTitle
     *   es_regularOrderShippingAddress.shippingName,
     *   es_regularOrderShippingAddress.shippingAddress,
     *   es_regularOrderShippingAddress.shippingAddressSub,
     *   es_regularOrderShippingAddress.shippingPhone
     *   es_regularOrderShippingAddress.shippingCellPhone,
     *   es_regularOrderShippingAddress.shippingMessage
     *   es_regularGoods.applyStatus as regularGoodsApplyStatus,
     *   es_regularGoods.deliveryCycleType as regularGoodsDeliveryCycleType,
     *   es_regularGoods.maxDeliveryRounds as regularGoodsMaxDeliveryRounds,
     *   es_regularGoods.deliveryRoundsDisplayType
     * FROM es_regularOrderGoods
     * JOIN es_regularGoods 
     *   ON es_regularGoods.sno = es_regularOrderGoods.regularGoodsNo
     * JOIN es_goods 
     *   ON es_goods.goodsNo = es_regularGoods.goodsNo
     * JOIN es_regularOrderShippingAddress 
     *   ON es_regularOrderShippingAddress.sno = es_regularOrderGoods.shippingAddressSno
     * WHERE es_regularOrderGoods.applyNo = :applyNo
     * LIMIT 1
     *
     * @param int $applyNo
     * @return array
     */
    public function findRegularDeliveryChangeInfoByApplyNo(int $applyNo): array
    {
        $deliveryInfo = RegularOrderGoods::query()
            ->join('es_regularGoods', 'es_regularGoods.sno', '=', 'es_regularOrderGoods.regularGoodsNo')
            ->join('es_goods', 'es_goods.goodsNo', '=', 'es_regularGoods.goodsNo')
            ->join('es_regularOrderShippingAddress', 'es_regularOrderShippingAddress.sno', '=', 'es_regularOrderGoods.shippingAddressSno')
            ->select(
                'es_regularOrderGoods.*',
                'es_goods.goodsNo',
                'es_goods.goodsNm',
                'es_regularOrderShippingAddress.sno as shippingSno',
                'es_regularOrderShippingAddress.shippingTitle',
                'es_regularOrderShippingAddress.shippingName',
                'es_regularOrderShippingAddress.shippingAddress',
                'es_regularOrderShippingAddress.shippingAddressSub',
                'es_regularOrderShippingAddress.shippingPhone',
                'es_regularOrderShippingAddress.shippingCellPhone',
                'es_regularOrderShippingAddress.shippingMessage',
                'es_regularGoods.applyStatus as regularGoodsApplyStatus',
                'es_regularGoods.deliveryCycleType as regularGoodsDeliveryCycleType',
                'es_regularGoods.maxDeliveryRounds as regularGoodsMaxDeliveryRounds',
                'es_regularGoods.deliveryRoundsDisplayType'
            )
            ->where('es_regularOrderGoods.applyNo', $applyNo)
            ->first();

        if ($deliveryInfo) {
            return $deliveryInfo->toArray();
        }

        return [];
    }


    /**
     * 신청 번호로 옵션 정보 조회
     *
     * SELECT
     *   es_regularOrderGoodsOption.regularGoodsOptionInfo,
     *   es_regularOrderGoodsOption.regularGoodsOptionPrice,
     *   es_regularOrderGoodsOptionText.regularGoodsOptionTextInfo,
     *   es_regularOrderGoodsOptionText.regularGoodsOptionTextPrice
     * FROM es_regularOrderGoods
     * JOIN es_regularOrderGoodsOption
     *   ON es_regularOrderGoodsOption.applyNo = es_regularOrderGoods.applyNo
     * LEFT JOIN es_regularOrderGoodsOptionText
     *   ON es_regularOrderGoodsOptionText.applyNo = es_regularOrderGoods.applyNo
     * WHERE es_regularOrderGoods.applyNo = :applyNo
     * LIMIT 1
     *
     * @param int $applyNo
     * @return array
     */
    public function findGoodsOptionInfoByApplyNo(int $applyNo): array
    {
        $replaceInfoForEmail = RegularOrderGoods::query()
            ->select([
                'es_regularOrderGoodsOption.regularGoodsOptionInfo',
                'es_regularOrderGoodsOption.regularGoodsOptionPrice',
                'es_regularOrderGoodsOptionText.regularGoodsOptionTextInfo',
                'es_regularOrderGoodsOptionText.regularGoodsOptionTextPrice',
            ])
            ->join('es_regularOrderGoodsOption', 'es_regularOrderGoodsOption.applyNo', '=', 'es_regularOrderGoods.applyNo')
            ->leftJoin('es_regularOrderGoodsOptionText', 'es_regularOrderGoodsOptionText.applyNo', '=', 'es_regularOrderGoods.applyNo')
            ->where('es_regularOrderGoods.applyNo', $applyNo)
            ->first();

        if ($replaceInfoForEmail) {
            return $replaceInfoForEmail->toArray();
        }

        return [];
    }

    /**
     *  원하는 상태의 정기결제(배송) 신청서 조회
     *
     *  SELECT COUNT(*) AS cnt
     *  FROM es_regularOrderGoods
     *  WHERE applyStatus = ?;
     *
     * @param string $applyStatus
     * @return int
     */
    public function countRegularOrderGoodsByApplyStatus(string $applyStatus): int
    {
        return RegularOrderGoods::query()
            ->where('applyStatus', $applyStatus)
            ->count();
    }

    /**
     * 카드번호에 대해 정기배송 신청 여부 체크
     *
     * SELECT EXISTS(
     *   SELECT 1
     *   FROM es_regularOrderGoods
     *   WHERE cardNo = ?
     *   AND applyStatus = ?
     * ) AS exists
     *
     * @param string $cardNo
     * @return bool
     */
    public function existsActiveRegularOrderGoodsByCardNo(string $cardNo): bool
    {
        return RegularOrderGoods::query()
            ->where('cardNo', $cardNo)
            ->where('applyStatus', RegularOrderStatus::ACTIVE)
            ->exists();
    }

    /**
     * 신청그룹번호로 신청번호 리스트 조회
     *
     * SELECT es_regularOrderGoods.applyNo
     * FROM es_regularOrderGoods
     * JOIN es_regularOrder ON es_regularOrderGoods.applyGroupNo = es_regularOrder.applyGroupNo
     * WHERE es_regularOrderGoods.applyGroupNo = ?
     *
     * @param string $applyGroupNo
     * @return array
     */
    public function findRegularOrderApplyNoListByApplyGroupNo(string $applyGroupNo): array
    {
        return RegularOrderGoods::query()
            ->select('es_regularOrderGoods.applyNo')
            ->join('es_regularOrder', 'es_regularOrder.applyGroupNo', '=', 'es_regularOrderGoods.applyGroupNo')
            ->where('es_regularOrderGoods.applyGroupNo', $applyGroupNo)
            ->get()
            ->toArray();
    }
}
