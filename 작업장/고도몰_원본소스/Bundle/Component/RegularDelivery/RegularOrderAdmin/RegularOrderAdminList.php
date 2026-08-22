<?php
/*
 * Copyright (C) 2025 NHN COMMERCE. - All Rights Reserved
 *
 * Unauthorized copying or redistribution of this file in source and binary forms via any medium
 * is strictly prohibited.
 */

namespace Bundle\Component\RegularDelivery\RegularOrderAdmin;

use Bundle\Component\Member\MemberMasking;
use Util\Order\RegularOrderUtil;
use DTO\RegularDelivery\RegularOrder\RegularOrderSearchCondition;
use Framework\Log\Logger;
use Origin\Enum\RegularDelivery\RegularGoods\DeliveryCycle;
use Origin\Enum\RegularDelivery\RegularOrder\RegularOrderStatus;
use Repository\Code\CodeRepository;
use Repository\RegularDelivery\RegularOrder\RegularOrderAddGoodsRepository;
use Repository\RegularDelivery\RegularOrder\RegularOrderAdminMemoRepository;
use Repository\RegularDelivery\RegularOrder\RegularOrderGiftRepository;
use Repository\RegularDelivery\RegularOrder\RegularOrderGoodsRepository;
use Repository\RegularDelivery\RegularOrder\RegularOrderRepository;

/**
 * 정기결제 신청서 어드민 리스트 조회
 */
class RegularOrderAdminList
{
    /**
     * @var RegularOrderRepository
     */
    private $regularOrderRepository;
    /**
     * @var RegularOrderGoodsRepository
     */
    private $regularOrderGoodsRepository;
    /**
     * @var RegularOrderGiftRepository
     */
    private $regularOrderGiftRepository;
    /**
     * @var RegularOrderAddGoodsRepository
     */
    private $regularOrderAddGoodsRepository;
    /**
     * @var MemberMasking
     */
    private $memberMasking;
    /**
     * @var Logger
     */
    private $logger;
    /**
     * @var RegularOrderAdminMemoRepository
     */
    private $regularOrderAdminMemoRepository;
    /**
     * @var CodeRepository
     */
    private $codeRepository;

    public function __construct(
        RegularOrderRepository $regularOrderRepository,
        RegularOrderGoodsRepository $regularOrderGoodsRepository,
        RegularOrderGiftRepository $regularOrderGiftRepository,
        RegularOrderAdminMemoRepository $regularOrderAdminMemoRepository,
        CodeRepository $codeRepository,
        RegularOrderAddGoodsRepository $regularOrderAddGoodsRepository,
        MemberMasking $memberMasking,
        Logger $logger
    )
    {
        $this->regularOrderRepository = $regularOrderRepository;
        $this->regularOrderGoodsRepository = $regularOrderGoodsRepository;
        $this->regularOrderGiftRepository = $regularOrderGiftRepository;
        $this->regularOrderAdminMemoRepository = $regularOrderAdminMemoRepository;
        $this->codeRepository = $codeRepository;
        $this->regularOrderAddGoodsRepository = $regularOrderAddGoodsRepository;
        $this->memberMasking = $memberMasking;
        $this->logger = $logger;
    }

    /**
     * @return string[]
     */
    public function getSearchKeys(array $getValue): array
    {
        $searchKey = [];
        if (!empty($getValue)) {
            foreach ($getValue as $key => $value) {
                $searchKey[$key] = $value;
            }
        }

        $defaultKeys = [
            'combineSearch' => [
                'applyNo' => '신청번호',
                'goodsNm' => '상품명',
                'goodsNo' => '상품코드',
                'goodsCd' => '자체상품코드',
                'applierNm' => '신청인명',
                'applierID' => '아이디',
                'applierPhone' => '신청자 전화번호',
                'applierCellPhone' => '신청자 휴대폰번호',
                'applierEmail' => '신청자 이메일',
            ],
            'sortList' => [
                'applyDt desc' => '신청일 ↓',
                'applyDt asc' => '신청일 ↑',
                'applyNo desc' => '신청번호 ↓',
                'applyNo asc' => '신청번호 ↑',
                'regularOrderGoodsNm desc' => '상품명 ↓',
                'regularOrderGoodsNm asc' => '상품명 ↑',
                'applierNm desc' => '신청자 ↓',
                'applierNm asc' => '신청자 ↑',
                'totalRegularOrderPrice desc' => '총 신청금액 ↓',
                'totalRegularOrderPrice asc' => '총 신청금액 ↑',
                'shippingName desc' => '수령자 ↓',
                'shippingName asc' => '수령자 ↑'
            ],
            'scmFl' => 'all',
            'applyStatus' => ['all'],
            'deliveryCycleType' => 'all',
            'deliveryCycleMonth' => ['all'],
            'deliveryCycleWeek' => ['all'],
            'deliveryCycleDayWeek' => ['all']
        ];

        return array_merge($defaultKeys, $searchKey);
    }

    /**
     * 이용상태 관련 리스트
     * @return array
     */
    public function getApplyStatusList(): array
    {
        $allStatus = RegularOrderStatus::getAllStatus();

        $activeStatus = [
            'active' => [
                RegularOrderStatus::ACTIVE => $allStatus['active']
            ],
            'pause' => [
                RegularOrderStatus::USER_STOP => $allStatus['userStop'],
                RegularOrderStatus::SYSTEM_STOP => $allStatus['systemStop'],
                RegularOrderStatus::ADMIN_STOP => $allStatus['adminStop']
            ]
        ];

        $inActiveStatus = [
            'inactive' => [
                RegularOrderStatus::USER_INACTIVE => $allStatus['userInactive'],
                RegularOrderStatus::SYSTEM_INACTIVE => $allStatus['systemInactive'],
                RegularOrderStatus::ADMIN_INACTIVE => $allStatus['adminInactive'],
                RegularOrderStatus::ROUND_FINISH => $allStatus['roundFinish'],
            ]
        ];

        return [
            'activeStatus' => $activeStatus,
            'inactiveStatus' => $inActiveStatus,
        ];
    }

    /**
     * 배송 주기 관련 목록
     * @return array
     */
    public function getDeliveryCycle(): array
    {
        return [
            'deliveryCycleType' => DeliveryCycle::DELIVERY_CYCLE_TYPE,
            'deliveryCycleMonth' => DeliveryCycle::DELIVERY_CYCLE_MONTH,
            'deliveryCycleWeek' => DeliveryCycle::DELIVERY_CYCLE_WEEK,
            'deliveryCycleWeekDay' => DeliveryCycle::DELIVERY_CYCLE_WEEK_DAY,
        ];
    }

    /**
     * 신청서 조회
     * @param array $getValue
     * @param array $orderGridConfigList
     * @return array
     */
    public function getRegularOrderList(array $getValue, array $orderGridConfigList): array
    {
        $conditions = new RegularOrderSearchCondition($getValue);

        // 정보 조회
        $regularOrderList = $this->regularOrderRepository->findRegularOrderList($conditions);
        $totalRegularOrderListCount = $this->regularOrderRepository->countTotalRegularOrderList($conditions);

        // 추가 조회나 가공 필요한 정보
        $regularOrderList = $this->processAdditionalOrderData($regularOrderList, $orderGridConfigList);
        $this->logger->channel('regularOrderAdmin')->info('정기결제 관리자 페이지 그리드 리스트 데이터 : ', $regularOrderList);

        return [
            'list' => $regularOrderList,
            'count' => $totalRegularOrderListCount
        ];
    }

    /**
     * 정기결제 신청 총 개수 반환
     * @return int
     */
    public function getTotalCountRegularOrderList(): int
    {
        $conditions = new RegularOrderSearchCondition([]);
        return $this->regularOrderGoodsRepository->countTotalRegularOrderList($conditions);
    }

    /**
     * 단순 쿼리 결과값을 가져온 정기결제 신청서 정보에서 추가로 계산하거나 가공해야 하는 정보들
     * ex) 사은품은 별도의 쿼리로 구성한다.
     * ex) 금액은 별도로 계산관련 로직을 추가한다.
     *
     * @param array $regularOrderList
     * @param array $orderGridConfigList
     * @return array
     */
    private function processAdditionalOrderData(array $regularOrderList, array $orderGridConfigList): array
    {
        foreach ($regularOrderList as $index => $regularOrder) {
            // 사은품은 별도 쿼리로 조회
            if (array_key_exists('gift', $orderGridConfigList)) {
                $giftInfo = $this->regularOrderGiftRepository->findGiftInfoByApplyNo($regularOrder['applyNo']);
                foreach ($giftInfo as $gift) {
                    $regularOrderList[$index]['giftInfoList'][] = [
                        'conditionTitle' => $gift['conditionTitle'],
                        'giveCnt' => $gift['giveCnt'],
                        'giftNm' => $gift['giftNm']
                    ];
                }
            }

            // 추가 상품 별도 쿼리 조회 (총 상품금액과, 총 할인금액 컬럼에 추가 계산 필요)
            $addGoodsTotalPrice = 0;
            $addGoodsCount = 0;
            $addGoodsInfo = $this->regularOrderAddGoodsRepository->findRegularOrderAddGoodsByApplyNo($regularOrder['applyNo']);
            foreach ($addGoodsInfo as $addGoods){
                $addGoodsCount += $addGoods['regularAddGoodsCnt'];
                $addGoodsTotalPrice += $addGoods['regularAddGoodsPrice'] * $addGoods['regularAddGoodsCnt'];
            }
            $regularOrderList[$index]['addGoodsCount'] = $addGoodsCount;
            $regularOrderList[$index]['addGoodsTypeCount'] = count($addGoodsInfo);

            // 상품 금액 관련
            $regularOrderList[$index]['totalGoodsPrice'] = RegularOrderUtil::calculateTotalRegularOrderOriginGoodsPrice($regularOrder) + $addGoodsTotalPrice;
            $regularOrderList[$index]['totalDcPrice'] = RegularOrderUtil::calculateRegularOrderOriginGoodsDiscountPrice($regularOrder);
            $regularOrderList[$index]['regularOrderTotalPrice'] = $regularOrderList[$index]['totalGoodsPrice'] - $regularOrderList[$index]['totalDcPrice'];

            // 마스킹 처리
            $regularOrderList[$index]['applierName'] = $this->memberMasking->masking('order','name', $regularOrder['applierName']);
            $regularOrderList[$index]['applierCellPhone'] = $this->memberMasking->masking('order','tel', $regularOrder['applierCellPhone']);
            $regularOrderList[$index]['memId'] = $this->memberMasking->masking('order','id', $regularOrder['memId']);

            // 관리자 메모
            $adminMemoList = $this->regularOrderAdminMemoRepository->findAdminMemoByApplyNo($regularOrder['applyNo']);
            $regularOrderList[$index]['adminMemo'] = !empty($adminMemoList) ? $adminMemoList : null;

            // 해지일 format
            $regularOrderList[$index]['inactiveDt'] = $regularOrder['inactiveDt'] ? date('Y-m-d', strtotime($regularOrder['inactiveDt'])) : null;
        }

        return $regularOrderList;
    }

    /**
     * 셀렉트박스를 사용한 상태변경 일괄 처리를 위한 값
     * @return array
     */
    public function getSelectBoxApplyStatus(): array
    {
        return [
            RegularOrderStatus::ACTIVE => RegularOrderStatus::getAdminStatus(RegularOrderStatus::ACTIVE),
            RegularOrderStatus::ADMIN_STOP => RegularOrderStatus::getAdminStatus(RegularOrderStatus::ADMIN_STOP),
            RegularOrderStatus::ADMIN_INACTIVE => RegularOrderStatus::getAdminStatus(RegularOrderStatus::ADMIN_INACTIVE),
        ];
    }

    /**
     * 관리자 메모 조회
     *
     * @param int $applyNo
     * @return array
     */
    public function getAdminMemoList(int $applyNo): array
    {
        return $this->regularOrderAdminMemoRepository->findAdminMemoByApplyNo($applyNo);
    }

    /**
     * 관리자 메모 페이징 조회
     *
     * @param int $applyNo
     * @param int $page
     * @param int $pageNum
     * @return mixed
     */
    public function getAdminMemoListByPage(int $applyNo, int $page, int $pageNum)
    {
        return $this->regularOrderAdminMemoRepository->findAdminMemoByApplyNo($applyNo, $page, $pageNum);
    }

    /**
     * 관리자 메모 코드 조회
     * @return array
     */
    public function getAdminMemoCode(): array
    {
        $adminMemoCode = $this->codeRepository->findAdminMemoCode();
        $adminMemoList = [];
        foreach ($adminMemoCode as $adminMemo) {
            $adminMemoList[$adminMemo['itemCd']] = $adminMemo['itemNm'];
        }
        return $adminMemoList;
    }
}
