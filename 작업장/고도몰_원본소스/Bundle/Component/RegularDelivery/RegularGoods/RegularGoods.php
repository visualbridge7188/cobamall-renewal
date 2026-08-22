<?php
/*
 * Copyright (C) 2025 NHN COMMERCE. - All Rights Reserved
 *
 * Unauthorized copying or redistribution of this file in source and binary forms via any medium
 * is strictly prohibited.
 */

namespace Bundle\Component\RegularDelivery\RegularGoods;

use Framework\Log\Logger;
use Component\Goods\GoodsAdminGrid;
use Component\Page\Page;
use Component\Policy\Policy;
use Enum\Goods\RegularGoodsStatus;
use Origin\Enum\RegularDelivery\RegularGoods\RegularGoodsGift;
use Exception;
use Origin\Enum\Goods\GoodsImageAttribute;
use Origin\Enum\RegularDelivery\RegularGoods\RegularGoodsAttribute;
use Origin\Enum\RegularDelivery\RegularGoods\DeliveryCycle;
use Repository\Goods\GiftRepository;
use Repository\Goods\ManageGoodsGridConfigRepository;
use Repository\RegularDelivery\RegularGoods\RegularGiftPresentRepository;
use Repository\RegularDelivery\RegularGoods\RegularGiftPresentInfoRepository;
use Repository\RegularDelivery\RegularGoods\RegularGoodsDeliveryCycleRepository;
use Repository\RegularDelivery\RegularGoods\RegularGoodsRepository;
use Repository\RegularDelivery\RegularOrder\RegularOrderGoodsRepository;
use Repository\Manager\ManagerSearchConfigRepository;
use Util\Order\RegularOrderUtil;
use DTO\RegularDelivery\RegularGoods\RegularGoodsValidateDTO;
use Origin\Exception\RegularDelivery\RegularGoods\RegularGoodsValidateException;

class RegularGoods
{
    /**
     * @var RegularGoodsRepository
     */
    private $regularGoodsRepository;

    /**
     * @var RegularGoodsDeliveryCycleRepository
     */
    private $regularGoodsDeliveryCycleRepository;

    /**
     * @var RegularGiftPresentRepository
     */
    private $regularGiftPresentRepository;

    /**
     * @var RegularGiftPresentInfoRepository
     */
    private $regularGiftPresentInfoRepository;

    /**
     * @var ManageGoodsGridConfigRepository
     */
    private $manageGoodsGridConfigRepository;

    /**
     * @var GiftRepository
     */
    private $giftRepository;

    /**
     * @var RegularOrderGoodsRepository
     */
    private $regularOrderGoodsRepository;

    /**
     * @var GoodsAdminGrid
     */
    private $goodsAdminGrid;

    /**
     * @var Policy
     */
    private $policy;

    /**
     * @var ManagerSearchConfigRepository
     */
    private $managerSearchConfigRepository;

    /**
     * @var Logger
     */
    private $logger;

    public function __construct(
        RegularGoodsRepository              $regularGoodsRepository,
        RegularGoodsDeliveryCycleRepository $regularGoodsDeliveryCycleRepository,
        RegularGiftPresentRepository        $regularGiftPresentRepository,
        RegularGiftPresentInfoRepository    $regularGiftPresentInfoRepository,
        ManageGoodsGridConfigRepository     $manageGoodsGridConfigRepository,
        GiftRepository                      $giftRepository,
        RegularOrderGoodsRepository         $regularOrderGoodsRepository,
        GoodsAdminGrid                      $goodsAdminGrid,
        Policy                              $policy,
        ManagerSearchConfigRepository       $managerSearchConfigRepository,
        Logger                              $logger
    )
    {
        $this->regularGoodsRepository = $regularGoodsRepository;
        $this->regularGoodsDeliveryCycleRepository = $regularGoodsDeliveryCycleRepository;
        $this->regularGiftPresentRepository = $regularGiftPresentRepository;
        $this->regularGiftPresentInfoRepository = $regularGiftPresentInfoRepository;
        $this->manageGoodsGridConfigRepository = $manageGoodsGridConfigRepository;
        $this->giftRepository = $giftRepository;
        $this->regularOrderGoodsRepository = $regularOrderGoodsRepository;
        $this->goodsAdminGrid = $goodsAdminGrid;
        $this->policy = $policy;
        $this->managerSearchConfigRepository = $managerSearchConfigRepository;
        $this->logger = $logger;
    }

    /**
     * 정기 결제(배송) 기능 사용 여부 체크
     *
     * @return bool
     */
    public function isUseRegularDelivery(): bool
    {
        return $this->policy->getValue("order.basic")['useRegularDelivery'] === 'y';
    }

    /**
     * 정기 상품 데이터 가져오기
     *
     * @param int $goodsNo
     * @return array
     * @throws \Throwable
     */
    public function getRegularGoodsData(int $goodsNo): array
    {
        // 정기 결제(배송) 정책 확인
        if (!$this->isUseRegularDelivery()) {
            return [];
        }

        try {
            // 정기 상품 정보 조회
            $regularGoodsData = $this->regularGoodsRepository->findRegularGoodsDetailByGoodsNo($goodsNo);

            // 정기 결제(배송) 상품 존재 및 삭제 여부 확인
            if (empty($regularGoodsData) || $regularGoodsData['delFl'] !== 'n') {
                return [];
            }

            // 배송 주기 정보 조회
            $regularGoodsDeliveryCycle = $this->getRegularGoodsDeliveryCycleByRegularGoodsData($regularGoodsData['sno'], $regularGoodsData);

            // 정기 상품 배송 회차 설정에 따른 최대 배송회차 값 설정
            $maxDeliveryRounds = (int)($regularGoodsData['maxDeliveryRounds'] ?? RegularGoodsAttribute::MAX_DELIVERY_ROUNDS);

            // 정기 상품 주기 정보 정리
            if ($regularGoodsData['deliveryCycleType'] === 'month') {
                $deliveryCycle = $regularGoodsDeliveryCycle['monthCycle'] ?? [];
                $deliveryCycleWeekDay = null;
            } else {
                $deliveryCycle = $regularGoodsDeliveryCycle['weekCycle'] ?? [];
                $deliveryCycleWeekDay = $regularGoodsDeliveryCycle['weekDayCycle'] ?? [];
            }

            // 정기 상품 배송 주기 데이터 조회
            $regularDeliveryCycleData = RegularOrderUtil::getDeliveryCycleData(
                $regularGoodsData['deliveryCycleType'],
                $deliveryCycle,
                $deliveryCycleWeekDay
            );

            // 정기 상품 첫 배송예정일 기본값 조회
            $firstDeliveryDate = $this->getDefaultFirstDeliveryDate(
                $regularGoodsData['deliveryCycleType'],
                $deliveryCycle,
                $deliveryCycleWeekDay
            );

            // 정기 상품 할인 정보 계산
            list($discountRate, $discountPrice) = RegularOrderUtil::calculateRegularGoodsDiscount(
                $regularGoodsData['regularPrice'],
                $regularGoodsData['discountUseFl'],
                $regularGoodsData['discountType'],
                $regularGoodsData['discountRate'],
                $regularGoodsData['discountPrice']
            );
        } catch (\Throwable $e) {
            $this->logger->channel('regularGoods')->warning('정기결제 상품 조회 오류', [$e->getMessage(), $e->getTrace()]);
        }

        return array_merge([
            'regularGoodsNo' => $regularGoodsData['sno'],
            'regularPrice' => $regularGoodsData['regularPrice'] ?? '',
            'applyStatus' => $regularGoodsData['applyStatus'] ?? '',
            'goodsNm' => $regularGoodsData['goodsNm'],
            'discountUseFl' => $regularGoodsData['discountUseFl'] ?? '',
            'discountType' => $regularGoodsData['discountType'],
            'discountRate' => $discountRate,
            'discountPrice' => $discountPrice,
            'deliveryType' => $regularGoodsData['deliveryType'] ?? '',
            'deliveryCycleType' => $regularGoodsData['deliveryCycleType'] ?? '',
            'deliveryRoundsDisplayType' => $regularGoodsData['deliveryRoundsDisplayType'] ?? '',
            'deliveryRoundOptions' => range(2, $maxDeliveryRounds),
            'firstDeliveryDateByMonth' => $firstDeliveryDate['month'],
            'firstDeliveryDateByWeek' => $firstDeliveryDate['week'],
        ], $regularDeliveryCycleData);
    }

    /**
     * 정기 상품 첫 배송예정일 기본값 가져오기
     * all 타입일 경우 - 1개월, 1일 / 1주, 월요일
     * month 타입일 경우 - $deliveryCycle[0] 개월, 1일
     * week 타입일 경우 - $deliveryCycle[0] 주, $deliveryCycleWeekDay[0] 요일
     *
     * @param string $deliveryCycleType
     * @param array|null $deliveryCycle
     * @param array|null $deliveryCycleWeekDay
     * @return array
     * @throws Exception
     */
    public function getDefaultFirstDeliveryDate(string $deliveryCycleType, array $deliveryCycle = null, array $deliveryCycleWeekDay = null): array
    {
        $firstDeliveryDate = [
            'month' => '0000-00-00',
            'week' => '0000-00-00'
        ];

        // 현재 시각 기준으로 첫 배송예정일 계산
        $deliveryDueDate = date('Y-m-d');

        switch ($deliveryCycleType) {
            case 'all':
                $firstDeliveryDate['month'] = RegularOrderUtil::calculateOrderDate($deliveryDueDate, 'month', 1, 1, true)[0];
                $firstDeliveryDate['week'] = RegularOrderUtil::calculateOrderDate($deliveryDueDate, 'week', 1, 1, true)[0];
                break;
            case 'month':
                $firstDeliveryDate['month'] = RegularOrderUtil::calculateOrderDate($deliveryDueDate, 'month', $deliveryCycle[0], 1, true)[0];
                break;
            case 'week':
                $firstDeliveryDate['week'] = RegularOrderUtil::calculateOrderDate($deliveryDueDate, 'week', $deliveryCycle[0], $deliveryCycleWeekDay[0], true)[0];
                break;
            default:
                throw new Exception('잘못된 배송 주기 타입입니다.');
        }

        return $firstDeliveryDate;
    }

    /**
     * 정기배송 상품이 정률 할인일 경우 상품의 옵션 배열에 반영하여 반환 처리
     *
     * @param array $options
     * @param string $priceKey
     * @param float $discountRate
     * @return array
     */
    function applyRegularGoodsPercentDiscount(array $options, string $priceKey, float $discountRate): array
    {
        // 옵션 배열을 순회하며 옵션가가 있을 경우 할인 반영
        foreach ($options as $k => $option) {
            if (isset($option[$priceKey]) && $option[$priceKey] > 0) {
                $options[$k][$priceKey] = RegularOrderUtil::calculateRegularOptionPrice($option[$priceKey], 'y', 'percent', $discountRate);
            }
        }
        return $options;
    }

    /**
     * 수정 화면 세팅을 위한 정기결제(배송) 상품 정보
     *
     * @param int $sno : 수정하고자 하는 하는 정기결제(배송) 상품의 sno
     * @return array : 수정하고자 하는 하는 정기결제(배송) 상품의 정보
     */
    public function getModificationFormInfo(int $sno): array
    {
        // 수정인 경우
        $regularGoodsData = $this->regularGoodsRepository->findRegularGoodsBySno($sno, GoodsImageAttribute::IMAGE_KIND_LIST);

        if ($regularGoodsData['roundsType'] === 'directInput') {
            $regularGoodsData['fixGiftRoundsNum'] = json_decode($regularGoodsData['fixGiftRoundsNum'], true);
        }

        $regularGoodsData['goodsData'] = $this->getGoodsDataByRegularGoodsData($regularGoodsData);
        $regularGoodsData['regularGoodsDeliveryCycle'] = $this->getRegularGoodsDeliveryCycleByRegularGoodsData($sno, $regularGoodsData);
        $regularGoodsData['giftPresentInfo'] = $this->getRegularGiftPresentInfoByRegularGoodsData($regularGoodsData);

        return $regularGoodsData;
    }

    /**
     * 조회된 정기결제(배송) 관련 데이터를 바탕으로 일반 상품 정보 반환
     *
     * @param array $regularGoodsData : 조회된 정기결제(배송) 관련 데이터
     * @return array : 일반 상품 정보
     */
    private function getGoodsDataByRegularGoodsData(array $regularGoodsData): array
    {
        // 일반 상품 이미지 Name
        $imageName = $regularGoodsData['goodsImageStorage'] === 'obs' ? $regularGoodsData['imageUrl'] : $regularGoodsData['imageName'];

        // 상품 이미지 html
        $image = gd_html_goods_image(
            $regularGoodsData['goodsNo'],
            $imageName,
            $regularGoodsData['imagePath'],
            $regularGoodsData['imageStorage'],
            30,
            $regularGoodsData['goodsNm'],
            '_blank'
        );

        /**
         * 정기결제 상품의 일반 상품 정보에 대해 form에 뿌리기 위한 array 구성
         * 상품 등록 화면과 수정 화면이 동일한 파일을 사용하기 때문에
         * 등록 상품 선택 시, return되는 형식과 동일하게 구성
         */
        return [[
            'goodsNo' => $regularGoodsData['goodsNo'],
            'goodsNm' => $regularGoodsData['goodsNm'],
            'goodsPrice' => gd_global_money_format((int)$regularGoodsData['goodsPrice']) . gd_global_currency_string(),
            'scmNm' => $regularGoodsData['scmNo'] == 1 ? '본사' : $regularGoodsData['scmNm'],
            'totalStock' => $regularGoodsData['totalStock'],
            'imageName' => $imageName,
            'image' => $image,
            'imagePath' => $regularGoodsData['imagePath'],
            'imageStorage' => $regularGoodsData['imageStorage'],
            'soldOutFl' => $regularGoodsData['soldOutFl'],
            'stockFl' => $regularGoodsData['stockFl']
        ]];
    }

    /**
     * 조회된 정기결제(배송) 관련 데이터를 바탕으로 배송 주기 반환
     *
     * @param int $regularGoodsSno : 수정하려는 es_regularGoods의 sno
     * @param array $regularGoodsData : 조회된 정기결제(배송) 관련 데이터
     * @return array : 설정된 배송 주기 데이터
     */
    private function getRegularGoodsDeliveryCycleByRegularGoodsData(int $regularGoodsSno, array $regularGoodsData): array
    {
        $regularGoodsDeliveryCycle = [];

        // 정기결제(배송) 상품 배송 정보 세팅
        $regularGoodsDeliveryCycleData = $this->regularGoodsDeliveryCycleRepository->findRegularGoodsDeliveryCycleByRegularGoodsSno($regularGoodsSno);

        if ($regularGoodsData['deliveryCycleType'] === 'month') {
            // 배송 주기가 월 단위일 경우

            $regularGoodsDeliveryCycle['monthCycle'] = array_values(array_filter(
                array_column($regularGoodsDeliveryCycleData, 'monthCycle')
            ));
        } elseif ($regularGoodsData['deliveryCycleType'] === 'week') {
            // 배송 주기가 주 단위일 경우

            $regularGoodsDeliveryCycle['weekCycle'] = array_values(array_filter(
                array_column($regularGoodsDeliveryCycleData, 'weekCycle'),
                function ($value) {
                    return !is_null($value);
                }
            ));

            $regularGoodsDeliveryCycle['weekDayCycle'] = array_values(array_filter(
                array_column($regularGoodsDeliveryCycleData, 'weekDayCycle')
            ));
        }

        return $regularGoodsDeliveryCycle;
    }

    /**
     * 조회된 정기결제(배송)을 바탕으로 사은품 정보 반환
     *
     * @param array $regularGoodsData : 조회된 정기결제(배송) 관련 데이터
     * @return array : 정기결제(배송) 사은품 정보
     */
    private function getRegularGiftPresentInfoByRegularGoodsData(array $regularGoodsData): array
    {
        // 정기결제(배송)사은품 관련 사은품 정보 세팅
        $regularGiftPresentInfoData = [];

        if ($regularGoodsData['giftPresentSno']) {
            // 정기결제(배송) 상품의 사은품 정보 존재 시

            // 정기결제(배송) 상품의 사은품 상세 정보 조회
            $regularGiftPresentInfoData = $this->regularGiftPresentInfoRepository->findRegularGiftPresentToSetFormByRegularGiftPresentSno($regularGoodsData['giftPresentSno']);

            // 사은품 정보 세팅
            foreach ($regularGiftPresentInfoData as $index => $info) {

                // 사은품 번호 세팅
                $giftNumbers = json_decode($info['multiGiftNo'], true);
                if (empty($giftNumbers)) {
                    continue;
                }

                $giftNumbers = array_values($giftNumbers);

                // 사은품 번호를 통해 사은품 정보 조회
                $giftData = $this->giftRepository->findGiftInfoByGiftNo($giftNumbers);

                // 정기결제(배송) 상품의 사은품 상세 정보에 사은품 정보 세팅
                foreach ($giftData as $key => $gift) {
                    $giftData[$key]['giftImage'] = gd_htmlspecialchars_slashes(
                        gd_html_gift_image($gift['imageNm'], $gift['imagePath'], $gift['imageStorage'], 50, $gift['giftNm']),
                        'add'
                    );
                }

                $regularGiftPresentInfoData[$index]['multiGiftNo'] = $giftData;
            }
        }

        return $regularGiftPresentInfoData;
    }


    /**
     * 조건에 맞는 정기결제(배송) 상품 리스트 조회 및  반환
     *
     * @param array $searchData : 검색 설정
     * @return array 조건에 맞는 정기결제(배송) 상품 리스트
     */
    public function getRegularGoodsList(array $searchData): array
    {
        // 현재 페이지
        $currentPageNum = 1;
        if (isset($searchData['page'])) {
            $currentPageNum = $searchData['page'];
        }

        // 페이지 별 리스트 사이즈
        $pageSizeNum = 10;
        if (isset($searchData['pageSizeNum'])) {
            $pageSizeNum = $searchData['pageSizeNum'];
        }

        // 검색 조건 설정
        $filterAndExtraJoinTable = $this->applyRegularGoodsFilterAndExtraJoinTableToSearch($searchData);
        $filter = $filterAndExtraJoinTable['filter'];
        $extraJoinTable = $filterAndExtraJoinTable['extraJoinTable'];

        // 데이터 검색
        $regularGoodsList = $this->regularGoodsRepository->findRegularGoodsListByListSearchInfo(GoodsImageAttribute::IMAGE_KIND_LIST, $filter, $extraJoinTable, $currentPageNum, $pageSizeNum);

        // 현재 검색조건에 맞는 totalPage
        $totalPage = $this->regularGoodsRepository->countRegularGoodsListByListSearchInfo(GoodsImageAttribute::IMAGE_KIND_LIST, $filter, $extraJoinTable);

        // 총 정기결제(배송) 상품 수
        $amountPage = $this->regularGoodsRepository->countRegularGoodsList();

        // 페이징 세팅
        $page = new Page($currentPageNum, $totalPage, $amountPage, $pageSizeNum);
        $page->setCache(true); // 페이지당 리스트 수
        $page->setPage(null, ['soldOutCnt', 'pcDisplayCnt', 'mobileDisplayCnt', 'pcNoDisplayCnt', 'mobileNoDisplayCnt']);

        return [
            'regularGoodsList' => $regularGoodsList,
            'page' => $page
        ];
    }

    /**
     * 검색 필터 설정
     * @param array $searchData : 가공되지 않은 필터링 조건 정보
     * @return array : 가공된 필터링 정보
     */
    public function applyRegularGoodsFilterAndExtraJoinTableToSearch(array $searchData): array
    {
        $extraJoinTable = [];
        $whereFilter = [];
        $whereInFilter = [];
        $whereDateFilter = [];
        $orWhereRawFilter = [];
        $orderByRawFilter = [];

        // 공급사 구분
        if (!empty($searchData['scmFl']) && $searchData['scmFl'] !== 'all') {
            if ($searchData['scmFl'] === 'n') {
                $whereInFilter[] = ['es_goods.scmNo', [DEFAULT_CODE_SCMNO]];
            } else {
                $whereInFilter[] = ['es_goods.scmNo', $searchData['scmNo']];
            }
        }

        // 검색어
        if (!empty($searchData['key']) && !empty($searchData['keyword'])) {

            // 검색어에 포함된 와일드카드 문자 이스케이프 처리
            $wildcards = ['\\', '%', '_'];

            $escapedKeyword = str_replace(
                $wildcards,
                array_map(function($char) {return '\\' . $char;}, $wildcards),
                $searchData['keyword']
            );

            $whereFilter[] = ['es_goods.' . $searchData['key'], 'like', '%' . $escapedKeyword . '%'];
        }

        // 카테고리
        if (!empty($searchData['categoryCode'])) {
            $extraJoinTable[] = 'goodsLinkCategory';
            $whereFilter[] = ['es_goodsLinkCategory.cateCd', '=', $searchData['categoryCode']];
        }

        // 기간 검색
        if (!empty($searchData['searchDateFl']) && !empty($searchData['searchDateStart'])) {
            $dateField = 'es_regularGoods.' . $searchData['searchDateFl'];
            $whereDateFilter[] = [$dateField, '>=', $searchData['searchDateStart']];
            if (!empty($searchData['searchDateEnd'])) {
                $whereDateFilter[] = [$dateField, '<=', $searchData['searchDateEnd']];
            }
        }

        // 배송방법 노출 여부
        if (!empty($searchData['deliveryType']) && $searchData['deliveryType'] !== 'all') {
            $whereFilter[] = ['es_regularGoods.deliveryType', '=', $searchData['deliveryType']];
        }

        // 배송 주기
        if (!empty($searchData['deliveryCycleType']) && $searchData['deliveryCycleType'] !== 'all') {
            $extraJoinTable[] = 'regularGoodsDeliveryCycle';
            $whereFilter[] = ['es_regularGoods.deliveryCycleType', '=', $searchData['deliveryCycleType']];

            if ($searchData['deliveryCycleType'] === 'month' && !empty($searchData['deliveryCycleMonth'])) {
                $whereInFilter[] = ['es_regularGoodsDeliveryCycle.monthCycle', $searchData['deliveryCycleMonth']];
            } elseif ($searchData['deliveryCycleType'] === 'week' && !empty($searchData['deliveryCycleWeek'])) {
                $whereInFilter[] = ['es_regularGoodsDeliveryCycle.weekCycle', $searchData['deliveryCycleWeek']];
            }
        }


        // 종료회차
        if (!empty($searchData['deliveryRoundsDisplayType']) && $searchData['deliveryRoundsDisplayType'] !== 'all') {
            $whereFilter[] = ['es_regularGoods.deliveryRoundsDisplayType', '=', $searchData['deliveryRoundsDisplayType']];
            if ($searchData['deliveryRoundsDisplayType'] === RegularGoodsAttribute::ABLED && !empty($searchData['maxDeliveryRounds'])) {
                $whereFilter[] = ['es_regularGoods.maxDeliveryRounds', '<=', $searchData['maxDeliveryRounds']];
            }
        }

        // 신청 상태
        if (!empty($searchData['applyStatus']) && $searchData['applyStatus'] !== 'all') {
            $whereFilter[] = ['es_regularGoods.applyStatus', '=', $searchData['applyStatus']];
        }

        // 특정 es_regularGoods의 sno 값 필터링
        if (!empty($searchData['sno']) && is_array($searchData['sno'])) {
            $whereInFilter[] = ['es_regularGoods.sno', array_values($searchData['sno'])];
        }

        // 정렬
        if (!empty($searchData['sort'])) {
            list($sortField, $sortDirection) = explode(' ', $searchData['sort']);

            // regDt는 무조건 es_regularGoods 기준
            if ($sortField === 'regDt') {
                $orderByRawFilter[] = 'es_regularGoods.regDt ' . $sortDirection;
                $orderByRawFilter[] = 'es_regularGoods.sno ' . RegularGoodsStatus::DESC;
            } else {
                // 그 외 컬럼도 필요하면 여기에 추가
                $orderByRawFilter[] = $sortField . ' ' . $sortDirection;
            }
        } else {
            $orderByRawFilter[] = 'es_regularGoods.'.RegularGoodsStatus::REGISTER_DATE . ' ' . RegularGoodsStatus::DESC;
            $orderByRawFilter[] = 'es_regularGoods.sno ' . RegularGoodsStatus::DESC;
        }

        return [
            'extraJoinTable' => $extraJoinTable,
            'filter' => [
                'where' => $whereFilter,
                'whereIn' => $whereInFilter,
                'whereDate' => $whereDateFilter,
                'orWhereRaw' => $orWhereRawFilter,
                'orderByRaw' => $orderByRawFilter
            ]
        ];
    }

    /**
     * 조회항목에서 설정한 바에 따라 리스트 정렬
     *
     * @return array : 리스트에서 출력할 조회 항목 목록
     */
    public function getRegularGoodsGridConfigList(): array
    {
        // 설정된 정기결제(배송) 리스트의 조회 항목 조회
        $goodsGroupData = $this->manageGoodsGridConfigRepository->findGoodsGroupDataByGoodsGroupApplyMode(RegularGoodsAttribute::REGULAR_GOODS_GROUP_APPLY_MODE);

        // 설정된 정기결제(배송) 리스트의 조회 항목이 없을 경우, 기본 정기결제(배송) 리스트의 조회 항목 사용
        if (empty($goodsGroupData)) {
            $goodsGroupData = $this->goodsAdminGrid->getGoodsScmMainListDefaultListByKey('regular_goods_list');
        } else {
            $goodsGroupData = json_decode($goodsGroupData['ggData'], true);
        }

        $goodsGroupData[] = 'btn';
        $sortedRegularGoodsGridConfig = [];

        // 설정된 정기결제(배송) 리스트의 조회 항목에 대해 순서에 맞추어 세팅
        foreach ($goodsGroupData as $gridKey) {
            foreach (RegularGoodsStatus::REGULAR_GOODS_GRID_CONFIG_LIST as $config) {
                if ($config['gridKey'] === $gridKey) {
                    $sortedRegularGoodsGridConfig[] = $config;
                    break; // 매칭된 항목을 찾으면 내부 루프 종료
                }
            }
        }

        return !empty($sortedRegularGoodsGridConfig) ? $sortedRegularGoodsGridConfig : RegularGoodsStatus::REGULAR_GOODS_GRID_CONFIG_LIST;
    }

    /**
     * @return array : 정기결제(배송) 상품 배송 주기 정보
     */
    public function getDeliveryCycleData(array $deliveryCycleData): array
    {
        $deliveryData = [];

        $deliveryData['deliveryCycleType'] = $deliveryCycleData['deliveryCycleType'];
        $deliveryData['monthCycle'] = '';
        $deliveryData['weekCycle'] = '';
        $deliveryData['weekDayCycle'] = '';

        // es_regularGoods의 sno 값을 기준으로 모든 데이터를 가져옴
        $regularDeliveryCycleDataList = $this->regularGoodsDeliveryCycleRepository->findRegularGoodsDeliveryCycleByRegularGoodsSno($deliveryCycleData['sno']);

        if ($deliveryCycleData['deliveryCycleType'] === 'month') {
            // 정기결제(배송) 상품 배송 주기가 '월 단위'일 경우

            foreach ($regularDeliveryCycleDataList as $regularDeliveryCycleData) {
                if (!empty($regularDeliveryCycleData['monthCycle'])) {
                    $deliveryData['monthCycle'] .= DeliveryCycle::DELIVERY_CYCLE_MONTH[$regularDeliveryCycleData['monthCycle']] . ', ';
                }
            }
            $deliveryData['monthCycle'] = rtrim($deliveryData['monthCycle'], ', ');

        } elseif ($deliveryCycleData['deliveryCycleType'] === 'week') {
            // 정기결제(배송) 상품 배송 주기가 '주 단위'일 경우

            foreach ($regularDeliveryCycleDataList as $regularDeliveryCycleData) {
                if (!empty($regularDeliveryCycleData['weekCycle'])) {
                    $deliveryData['weekCycle'] .= DeliveryCycle::DELIVERY_CYCLE_WEEK[$regularDeliveryCycleData['weekCycle']] . ', ';
                }

                if (!empty($regularDeliveryCycleData['weekDayCycle'])) {
                    $deliveryData['weekDayCycle'] .= DeliveryCycle::DELIVERY_CYCLE_WEEK_DAY[$regularDeliveryCycleData['weekDayCycle']] . '요일, ';
                }
            }
            $deliveryData['weekCycle'] = rtrim($deliveryData['weekCycle'], ', ');
            $deliveryData['weekDayCycle'] = rtrim($deliveryData['weekDayCycle'], ', ');
        }

        return $deliveryData;
    }

    /**
     * 정기결제(배송)상품의 sno를 기준으로 adminMemo관련 데이터 반환
     *
     * @param $sno
     * @return array
     */
    public function getRegularGoodsAdminMemo($sno): array
    {
        return $this->regularGoodsRepository->findAdminMemoAndGoodsNmBySno($sno);
    }

    /**
     * 엑셀 작성에 사용할 상품 데이터 및 조회된 총 레코드 수 반환
     *
     * @param array $whereCondition : 조회 조건
     * @return array : 조회된 상품 데이터 및 총 레코드 수
     */
    public function getRegularGoodsListToExcel(array $whereCondition): array
    {
        $regularGoodsData = [];

        // 검색할 데이터 조건 정리
        $filterAndExtraJoinTable = $this->applyRegularGoodsFilterAndExtraJoinTableToSearch($whereCondition);
        $filter = $filterAndExtraJoinTable['filter'];
        $extraJoinTable = $filterAndExtraJoinTable['extraJoinTable'];

        // 정기결제(배송) 데이터 검색
        $regularGoodsData['regularGoodsList'] = $this->regularGoodsRepository->findRegularGoodsListToExcelByListSearchInfo($filter, $extraJoinTable);

        $snoList = [];
        $regularGiftPresentSnoList = [];

        foreach ($regularGoodsData['regularGoodsList'] as $regularGoods) {
            $snoList[] = $regularGoods['sno'];

            if (isset($regularGoods['regularGiftPresentSno'])) {
                $regularGiftPresentSnoList[] = $regularGoods['regularGiftPresentSno'];
            }
        }

        // 정기결제(배송) 상품 배송 주기 정보 검색
        $regularGoodsDeliveryCycleListByGrouped = [];
        $regularGoodsDeliveryCycleList = $this->regularGoodsDeliveryCycleRepository->findRegularGoodsDeliveryCycleByRegularGoodsSnoList($snoList);
        foreach ($regularGoodsDeliveryCycleList as $regularGoodsDeliveryCycle) {
            $regularGoodsDeliveryCycleListByGrouped[$regularGoodsDeliveryCycle['regularGoodsSno']][] = $regularGoodsDeliveryCycle;
        }

        // 정기결제(배송) 상품 사은품 상세 조건 검색
        $regularGiftPresentInfoListByGrouped = [];
        $regularGiftPresentInfoList = $this->regularGiftPresentInfoRepository->findRegularGiftPresentInfoByRegularGiftPresentSno($regularGiftPresentSnoList);
        foreach ($regularGiftPresentInfoList as $regularGiftPresentInfo) {
            $regularGiftPresentInfoListByGrouped[$regularGiftPresentInfo['regularGiftPresentSno']][] = $regularGiftPresentInfo;
        }

        $giftNoList = [];

        foreach ($regularGiftPresentInfoListByGrouped as $row) {
            foreach ($row as $regularGiftPresentInfo) {
                $multiGiftNo = json_decode($regularGiftPresentInfo['multiGiftNo'], true);

                if (is_array($multiGiftNo)) {
                    $giftNoList = array_merge($giftNoList, array_values($multiGiftNo));
                }
            }
        }

        $giftNoList = array_unique($giftNoList);

        // 사은품 명 검색
        $giftData = $this->giftRepository->getGiftNmByGiftNo($giftNoList);
        $giftMap = [];
        foreach ($giftData as $gift) {
            $giftMap[$gift['giftNo']] = $gift['giftNm'];
        }

        $regularGoodsData['regularGoodsDeliveryCycleList'] = $regularGoodsDeliveryCycleListByGrouped;

        $regularGoodsData['regularGiftPresentInfoList'] = $regularGiftPresentInfoListByGrouped;

        $regularGoodsData['giftNmList'] = $giftMap;

        // totalPage
        $regularGoodsData['totalCount'] = $this->regularGoodsRepository->countRegularGoodsListByListSearchInfo(GoodsImageAttribute::IMAGE_KIND_LIST, $filter, $extraJoinTable);

        return $regularGoodsData;
    }

    /**
     * 정기결제(배송) 상품 검색 데이터 반환
     *
     * @param array $search
     * @param string $applyPath
     * @return array
     */
    public function getDefaultSearchData(array $search, string $applyPath): array
    {
        $searchConfig = $this->managerSearchConfigRepository->findManagerSearchConfigByApplyPath($applyPath);

        $basicSearch = [
            'searchDateFl' => 'regDt',
            'searchDateStart' => date('Y-m-d', strtotime('-89 days')),
            'searchDateEnd' => date('Y-m-d')
        ];

        if (!empty($searchConfig)) {
            $searchConfigData = json_decode($searchConfig['data'], true);
            $basicSearch = array_merge($basicSearch, $searchConfigData);
        }

        if (!empty($search)) {
            return array_merge($basicSearch, $search);
        }

        // 검색 조건이 없을 경우, 부하방지를 위해 일부 검색 조건 필수 지정
        return $basicSearch;
    }

    /**
     * 조건에 맞는 레이어 정기결제(배송) 상품 리스트 조회 및 반환
     *
     * @param array $searchData : 검색 설정
     * @return array 조건에 맞는 정기결제(배송) 상품 리스트
     */
    public function getLayerRegularGoodsList(array $searchData): array
    {
        // 현재 페이지
        $currentPageNum = 1;
        if (isset($searchData['page'])) {
            $currentPageNum = $searchData['page'];
        }

        // 페이지 별 리스트 사이즈
        $pageSizeNum = 10;

        // 배송주기로 변경 가능한 정기결제(배송) 상품 조회
        $searchData['sno'] = $this->findAvailableRegularGoodsToChange($searchData['applyNo']);

        // 검색 조건 설정
        $filterAndExtraJoinTable = $this->applyRegularGoodsFilterAndExtraJoinTableToSearch($searchData);
        $filter = $filterAndExtraJoinTable['filter'];
        $extraJoinTable = $filterAndExtraJoinTable['extraJoinTable'];

        // 데이터 검색
        $regularGoodsList = $this->regularGoodsRepository->findLayerRegularGoodsListByListSearchInfo(GoodsImageAttribute::IMAGE_KIND_LIST, $filter, $extraJoinTable, $currentPageNum, $pageSizeNum);
        $regularGoodsList = $this->convertRegularGoodsForLayer($regularGoodsList);

        // 현재 검색조건에 맞는 totalPage
        $totalPage = $this->regularGoodsRepository->countRegularGoodsListByListSearchInfo(GoodsImageAttribute::IMAGE_KIND_LIST, $filter, $extraJoinTable);

        // 총 정기결제(배송) 상품 수
        $amountPage = $this->regularGoodsRepository->countRegularGoodsList();

        // 페이징 세팅
        $page = new Page($currentPageNum, $totalPage, $amountPage, $pageSizeNum);
        $page->setCache(true); // 페이지당 리스트 수
        $page->setPage(null, ['soldOutCnt', 'pcDisplayCnt', 'mobileDisplayCnt', 'pcNoDisplayCnt', 'mobileNoDisplayCnt']);

        return [
            'regularGoodsList' => $regularGoodsList,
            'regularGoodsCurrentCount' => count($regularGoodsList),
            'regularGoodsTotalCount' => $totalPage,
            'page' => $page
        ];
    }

    /**
     * 조건에 맞는 정기결제(배송) 상품 리스트 조회 및  반환
     *
     * @param array $searchData : 검색 설정
     * @return array array 조건에 맞는 정기결제(배송) 상품 리스트
     */
    public function getPopupRegularGoodsList(array $searchData): array
    {
        // 현재 페이지
        $currentPageNum = 1;
        if (isset($searchData['page'])) {
            $currentPageNum = $searchData['page'];
        }

        // 페이지 별 리스트 사이즈
        $pageSizeNum = 10;
        if (isset($searchData['pageSizeNum'])) {
            $pageSizeNum = $searchData['pageSizeNum'];
        }

        // 배송주기로 변경 가능한 정기결제(배송) 상품 조회
        $searchData['sno'] = $this->findAvailableRegularGoodsToChange($searchData['applyNo']);

        // 신청가능한 상품만 조회
        $searchData['applyStatus'] = 'abled';

        // 검색 조건 설정
        $filterAndExtraJoinTable = $this->applyRegularGoodsFilterAndExtraJoinTableToSearch($searchData);
        $filter = $filterAndExtraJoinTable['filter'];
        $extraJoinTable = $filterAndExtraJoinTable['extraJoinTable'];

        // 데이터 검색
        $regularGoodsList = $this->regularGoodsRepository->findPopupRegularGoodsListByListSearchInfo(GoodsImageAttribute::IMAGE_KIND_LIST, $filter, $extraJoinTable, $currentPageNum, $pageSizeNum);

        // 현재 검색조건에 맞는 totalPage
        $totalPage = $this->regularGoodsRepository->countRegularGoodsListByListSearchInfo(GoodsImageAttribute::IMAGE_KIND_LIST, $filter, $extraJoinTable);

        // 총 정기결제(배송) 상품 수
        $amountPage = $this->regularGoodsRepository->countRegularGoodsList();

        // 페이징 세팅
        $page = new Page($currentPageNum, $totalPage, $amountPage, $pageSizeNum);
        $page->setCache(true); // 페이지당 리스트 수
        $page->setPage(null, ['soldOutCnt', 'pcDisplayCnt', 'mobileDisplayCnt', 'pcNoDisplayCnt', 'mobileNoDisplayCnt']);

        return [
            'regularGoodsList' => $regularGoodsList,
            'page' => $page
        ];
    }

    /**
     * front layer RegularGoods에 보여줄 상품 리스트 반환
     *
     * @param array $regularGoodsListData : 반환할 regularGoods 관련 데이터
     * @return array
     */
    private function convertRegularGoodsForLayer(array $regularGoodsListData): array
    {
        $regularGoodsList = [];
        foreach ($regularGoodsListData as $regularGoods) {
            $regularGoodsList[] = [
                'goodsNo' => $regularGoods['goodsNo'],
                'goodsImage' => gd_html_goods_image(
                    $regularGoods['goodsNo'],
                    $regularGoods['goodsImageStorage'] === 'obs' ? $regularGoods['imageUrl'] : $regularGoods['imageName'],
                    $regularGoods['imagePath'],
                    $regularGoods['imageStorage'],
                    GoodsImageAttribute::MY_PAGE_GOODS_IMAGE_SIZE,
                    $regularGoods['goodsNm'],
                    '_blank'
                ),
                'goodsNm' => $regularGoods['goodsNm'],
                'regularPrice' => gd_currency_display($regularGoods['regularPrice']),
            ];
        }

        return $regularGoodsList;
    }

    /**
     * 정기배송 상품의 사은품을 조건에 맞도록 필터링한 목록 반환
     *
     * @param int $regularGoodsNo
     * @param int $goodsCnt
     * @param int $addGoodsCnt
     * @param int $size
     * @return array
     */
    public function getFilteredRegularGiftData(int $regularGoodsNo, int $goodsCnt, int $addGoodsCnt, int $size): array
    {
        // 상품에 해당하는 사은품 지급 조건 조회
        $giftPresent = $this->regularGiftPresentRepository->findGiftPresentInfoByRegularGoodsNo($regularGoodsNo);
        if (empty($giftPresent)) {
            return [];
        }

        /**
         * 사은품 조건에 따른 수량 정리
         *  - 무조건 지급 : $cnt = 0
         *  - 수량별 지급 (추가상품 수량 제외) : conditionType == 'quantityLimited', addGoodsFl == 'n'
         *  - 수량별 지급 (추가상품 수량 포함) : conditionType == 'quantityLimited', addGoodsFl == 'y'
         */
        $cnt = 0;
        if ($giftPresent['conditionType'] === RegularGoodsGift::CONDITION_TYPE_QUANTITY_LIMIT) {
            $cnt += $goodsCnt;
            if ($giftPresent['addGoodsFl'] === 'y') {
                $cnt += $addGoodsCnt;
            }
        }

        // 지급 조건 번호로 받을 수 있는 사은품 관련 정보 조회
        $giftPresentSno = $giftPresent['sno'];
        $presentInfo = $this->regularGiftPresentInfoRepository->findGiftPresentInfoByRegularGiftPresentSno($giftPresentSno, $cnt);
        if (empty($presentInfo)) {
            return [];
        }

        // 사은품으로 받을 수 있는 상품 번호
        $multiGiftNo = json_decode($presentInfo['multiGiftNo'], true);

        // 사은품 상품 조회
        $multiGift = $this->giftRepository->findGiftInfoByGiftNo($multiGiftNo);

        // 사은품 목록 정리
        $giftList = [];
        foreach ($multiGift as $index => $gift) {
            if ($presentInfo['selectCnt'] === 0 || $index < $presentInfo['selectCnt']) {
                $giftList[] = [
                    'giftNo' => $gift['giftNo'],
                    'giftNm' => $gift['giftNm'],
                    'giftImage' => gd_html_gift_image(
                        $gift['imageNm'],
                        $gift['imagePath'],
                        $gift['imageStorage'],
                        $size,
                        $gift['giftNm']
                    )
                ];
            }
        }

        return [
            'regularGiftPresentInfoSno' => $presentInfo['sno'],
            'conditionTitle' => $giftPresent['conditionTitle'],
            'giveCnt' => $presentInfo['giveCnt'],
            'list' => $giftList
        ];
    }

    /**
     * 상품번호를 바탕으로 구매가능한 상품인지 체크
     *
     * @param RegularGoodsValidateDTO $regularGoodsValidateDTO
     * @return void
     * @throws Exception
     */
    public function validateGoodsStatus(RegularGoodsValidateDTO $regularGoodsValidateDTO)
    {
        $regularGoodsValidateDataList = $regularGoodsValidateDTO->getRegularGoodsValidateData();

        // 정기 상품 정보 조회
        foreach ($regularGoodsValidateDataList as $goodsNo => $regularGoodsValidateData) {
            if (empty($goodsNo)) {
                throw new RegularGoodsValidateException('유효성 검사할 상품번호가 존재하지 않음');
            }

            $regularGoodsData = $this->regularGoodsRepository->findRegularGoodsDetailByGoodsNo($goodsNo);

            // 정기 결제(배송) 상품 존재 및 삭제 여부 확인
            if (empty($regularGoodsData)) {
                throw new RegularGoodsValidateException('삭제된 상품', RegularGoodsAttribute::ERROR_REGULAR_GOODS_DELETE);
            }

            // 일반 상품 품절 여부 확인
            if ($regularGoodsData['soldOutFl'] === 'y' || ($regularGoodsData['stockFl'] === 'y' && $regularGoodsData['totalStock'] <= 0)) {
                throw new RegularGoodsValidateException('품절된 상품', RegularGoodsAttribute::ERROR_REGULAR_GOODS_OUT_OF_STOCK);
            }

            // 정기결제(배송) 상품 신청 가능 여부 확인
            if ($regularGoodsData['applyStatus'] === 'disabled') {
                throw new RegularGoodsValidateException('신청 불가한 상품', RegularGoodsAttribute::ERROR_REGULAR_GOODS_APPLY_DISABLED);
            }

            $applyNo = $regularGoodsValidateData['applyNo'];
            $deliveryCycleTypes = $regularGoodsValidateData['deliveryCycleTypes'];
            $deliveryCycles = $regularGoodsValidateData['deliveryCycles'];
            $deliveryCycleDays = $regularGoodsValidateData['deliveryCycleDays'];
            $maxDeliveryRounds = $regularGoodsValidateData['maxDeliveryRounds'];

            // 배송주기 및 종료 회차 확인
            if (!empty($applyNo)) {
                // 신청서번호가 있을 경우, 즉 신청서 내 상품 변경, 등
                if ($regularGoodsData['deliveryCycleType'] !== 'all' || $regularGoodsData['deliveryRoundsDisplayType'] !== 'all') {
                    $this->validateRegularDeliveryByApplyNo($applyNo, $regularGoodsData);
                }
            } else if (!empty($deliveryCycleTypes)){
                // 신청서 번호가 없을 경우, 즉 정기결제(배송) 신청 전 상품 상세, 등
                foreach($deliveryCycleTypes as $index => $deliveryCycleType) {
                    if ($deliveryCycleType !== 'all' || $maxDeliveryRounds[$index] !== 'all') {
                        $this->validateRegularDeliveryBySelectedData(
                            $regularGoodsData,
                            [
                                'deliveryCycleType' => $deliveryCycleType,
                                'deliveryCycle' => $deliveryCycles[$index],
                                'deliveryCycleDay' => $deliveryCycleDays[$index],
                                'maxDeliveryRound' => $maxDeliveryRounds[$index]
                            ]
                        );
                    }
                }
            } else {
                throw new RegularGoodsValidateException('정기결제 배송과 관련한 정보가 들어오지 않음', RegularGoodsAttribute::ERROR_REGULAR_GOODS_DELIVERY_DATA_CHANGE);
            }
        }
    }

    /**
     * 신청번호 및 정기결제(배송)상품 데이터를 바탕으로 배송주기 및 종료회차 유효성 검사
     *
     * @param int $applyNo
     * @param array $regularGoodsData
     * @return void
     * @throws Exception
     */
    private function validateRegularDeliveryByApplyNo(int $applyNo, array $regularGoodsData)
    {
        $applyDeliveryInfo = $this->regularOrderGoodsRepository->findCurrentDeliveryRoundByApplyNo($applyNo);

        if ($regularGoodsData['deliveryCycleType'] !== 'all') {
            $cycleData = $this->getValidCycleData($regularGoodsData);
            if ($this->isValidateCycle($cycleData, $applyDeliveryInfo) === false) {
                throw new RegularGoodsValidateException('배송주기가 변경되어 신청서 정보와 매치되지 않음', RegularGoodsAttribute::ERROR_REGULAR_GOODS_DELIVERY_DATA_CHANGE);
            }
        }

        if ($regularGoodsData['deliveryRoundsDisplayType'] !== 'all') {
            if ($this->isValidateDeliveryRounds((int)$regularGoodsData['maxDeliveryRounds'], (int)$applyDeliveryInfo['maxDeliveryRound']) === false) {
                throw new RegularGoodsValidateException('종료회차가 변경되어 신청서 정보와 매치되지 않음', RegularGoodsAttribute::ERROR_REGULAR_GOODS_DELIVERY_DATA_CHANGE);
            }
        }
    }

    /**
     * 선택된 배송주기와 종료회차 리스트를 바탕으로 유효성 검사
     *
     * @param array $regularGoodsData
     * @param array $selectedRegularDeliveryData
     * @return void
     * @throws Exception
     */
    public function validateRegularDeliveryBySelectedData(array $regularGoodsData, array $selectedRegularDeliveryData)
    {
        if ($regularGoodsData['deliveryCycleType'] !== 'all') {
            $cycleData = $this->getValidCycleData($regularGoodsData);
            if ($this->isValidateCycle($cycleData, $selectedRegularDeliveryData) === false) {
                throw new RegularGoodsValidateException('배송주기가 변경되어 선택한 값과 매치되지 않음', RegularGoodsAttribute::ERROR_REGULAR_GOODS_DELIVERY_DATA_CHANGE);

            }
        }

        if ($regularGoodsData['deliveryRoundsDisplayType'] !== 'all') {
            if ($this->isValidateDeliveryRounds((int)$regularGoodsData['maxDeliveryRounds'], (int)$selectedRegularDeliveryData['maxDeliveryRound'])  === false) {
                throw new RegularGoodsValidateException('종료회차가 변경되어 선택한 값과 매치되지 않음', RegularGoodsAttribute::ERROR_REGULAR_GOODS_DELIVERY_DATA_CHANGE);

            }
        }
    }

    /**
     * 배송주기에 대해 데이터 주기 키값을 기준으로 데이터 정리
     *
     * @param array $regularGoodsData
     * @return array
     */
    private function getValidCycleData(array $regularGoodsData): array
    {
        $cycleData = [];
        $cycles = $this->regularGoodsRepository
            ->findRegularGoodsDeliveryInfoByRegularGoodsNo($regularGoodsData['sno']);

        foreach ($cycles as $cycle) {
            switch ($regularGoodsData['deliveryCycleType']) {
                case 'month':
                    $cycleData['monthCycle'][] = $cycle['monthCycle'];
                    break;
                case 'week':
                    if ($cycle['weekCycle'] !== null) {
                        $cycleData['weekCycle'][] = $cycle['weekCycle'];
                    }
                    if ($cycle['weekDayCycle'] !== null) {
                        $cycleData['weekDayCycle'][] = $cycle['weekDayCycle'];
                    }
                    break;
            }
        }

        return $cycleData;
    }

    /**
     * 배송주기 유효성 검사
     *
     * @param array $cycleData
     * @param array $selectedDeliveryInfo
     * @return boolean
     * @throws Exception
     */
    private function isValidateCycle(array $cycleData, array $selectedDeliveryInfo): bool
    {
        if (empty($cycleData)) {
            return false;
        }

        $selectedDeliveryType = $selectedDeliveryInfo['deliveryCycleType'];
        $selectedDeliveryCycle = $selectedDeliveryInfo['deliveryCycle'];
        $selectedDeliveryCycleDay = $selectedDeliveryInfo['deliveryCycleDay'];

        $isValid = true;

        if ($selectedDeliveryType === 'month') {
            $isValid = (
                isset($cycleData['monthCycle']) &&
                in_array($selectedDeliveryCycle, $cycleData['monthCycle'])
            );
        } elseif ($selectedDeliveryType === 'week') {
            $isValid = (
                isset($cycleData['weekCycle'], $cycleData['weekDayCycle']) &&
                in_array($selectedDeliveryCycle, $cycleData['weekCycle']) &&
                in_array($selectedDeliveryCycleDay, $cycleData['weekDayCycle'])
            );
        }

        return $isValid;
    }

    /**
     * 종료회차 유효성 검사
     *
     * @param int $maxAllowed
     * @param int $currentRequested
     * @return boolean
     * @throws Exception
     */
    private function isValidateDeliveryRounds(int $maxAllowed, int $currentRequested): bool
    {
        return ($currentRequested == 0)
            ? ($maxAllowed == 0)
            : ($maxAllowed >= $currentRequested);
    }


    /**
     * 엔드유저가 선택한 배송주기 데이터 반환
     *
     * @param array $postValue
     * @param array $regularGoodsData
     * @return array
     */
    public function getSelectedCycleData(array $postValue, array $regularGoodsData): array
    {
        // 배송 주기 유효성 여부 확인
        $isValid = $this->validateSelectedCycleData($postValue, $regularGoodsData);

        // 종료 회차 데이터
        $maxDeliveryRound = $this->getMaxDeliveryRound($postValue, $regularGoodsData);

        if ($isValid) {
            $selectedCycleData = [
                'deliveryCycleType' => $postValue['deliveryCycleType'],
                'deliveryCycle' => $postValue['deliveryCycle'],
                'deliveryCycleDay' => $postValue['deliveryCycleDay'],
                'maxDeliveryRound' => $maxDeliveryRound
            ];
        } else {
            $selectedCycleData = [
                'deliveryCycleType' => $regularGoodsData['deliveryCycleType'],
                'deliveryCycle' => null,
                'deliveryCycleDay' => null,
                'maxDeliveryRound' => $maxDeliveryRound
            ];
        }

        return $selectedCycleData;
    }

    /**
     * 종료 회차 데이터 추출
     *
     * @param array $postValue 사용자 입력값
     * @param array $regularGoodsData 기존 상품 데이터
     * @return int|null 종료 회차값 또는 null
     */
    function getMaxDeliveryRound(array $postValue, array $regularGoodsData)
    {
        // deliveryRoundsDisplayType이 'all'이면 전체 회차 수로 설정
        if ($regularGoodsData['deliveryRoundsDisplayType'] === 'all') {
            return $postValue['maxDeliveryRound'];
        }

        // deliveryRoundsDisplayType이 'all'이 아닌 경우
        if ($regularGoodsData['deliveryRoundsDisplayType'] === 'disabled') {
            return 0;
        }
        if ($postValue['maxDeliveryRound'] <= (count($regularGoodsData['deliveryRoundOptions']) + 1)) {
            return (int)$postValue['maxDeliveryRound'];
        }

        return null; // 유효하지 않은 경우
    }

    /**
     * 엔드유저가 선택한 배송주기 데이터 유효성 여부 확인
     *
     * @param array $postValue
     * @param array $regularGoodsData
     * @return bool
     */
    private function validateSelectedCycleData(array $postValue, array $regularGoodsData): bool
    {
        if ($regularGoodsData['deliveryCycleType'] !== 'all') {
            // 배송 주기 비교
            if ($regularGoodsData['deliveryCycleType'] !== $postValue['deliveryCycleType']) {
                return false;
            }

            // 월 배송 주기일 경우
            if ($regularGoodsData['deliveryCycleType'] === 'month') {
                if (!in_array($postValue['deliveryCycle'], $regularGoodsData['deliveryCycleMonth'])) {
                    return false;
                }
            }

            // 주 배송 주기일 경우
            if ($regularGoodsData['deliveryCycleType'] === 'week') {
                if (!in_array($postValue['deliveryCycle'], $regularGoodsData['deliveryCycleWeek'])) {
                    return false;
                }

                if (!in_array($postValue['deliveryCycleDay'], $regularGoodsData['deliveryCycleWeekDay'])) {
                    return false;
                }
            }
        }

        return true;
    }

    /**
     * 일반 상품번호를 바탕으로 정기결제상품 정보 조회
     *
     * @param array $goodsNoList
     * @return array
     */
    public function getRegularGoodsInfoByGoodsNo(array $goodsNoList): array
    {
        if (!empty($goodsNoList)) {
            return $this->regularGoodsRepository->findRegularGoodsNoByGoodsNoList($goodsNoList);
        }

        return [];
    }

    /**
     * 노출항목 중 정기결제가 있을 경우, 정기결제가와 신청가능여부, 배송정보 반환
     *
     * @param array $displayField
     * @param int $goodsNo
     * @return array
     */
    public function getRegularPriceInfo(array $displayField, int $goodsNo): array
    {
        // 노출항목 중 정기결제가가 없을 경우
        if (!in_array('regularPrice', $displayField)) {
            return ['useRegularPriceFl' => false];
        }

        $regularGoodsDatas = $this->getRegularGoodsInfoByGoodsNo([$goodsNo]);
        if (empty($regularGoodsDatas)) {
            return ['useRegularPriceFl' => false];
        }

        $regularGoodsData = $regularGoodsDatas[0];

        return [
            'useRegularPriceFl' => true,
            'regularPrice' => $regularGoodsData['regularPrice'],
            'applyStatus' => $regularGoodsData['applyStatus'],
            'deliveryType' => $regularGoodsData['deliveryType'],
        ];
    }

    /**
     * 정기결제(배송) 상품 중 변경 가능한 상품 조회
     * @param int $applyNo
     * @return array
     */
    private function findAvailableRegularGoodsToChange(int $applyNo): array
    {
        // 신청서 내 배송 주기 검색
        $orderDelivery = $this->regularOrderGoodsRepository->findCurrentDeliveryRoundByApplyNo($applyNo);

        // 배송주기에 맞는 정기결제(배송) 상품 리스트 조회
        $regularGoodsSnoListByDeliveryCycle = $this->regularGoodsRepository->findSnoByRegularDeliveryInfo($orderDelivery);

        if (empty($regularGoodsSnoListByDeliveryCycle)) {
            return $regularGoodsSnoListByDeliveryCycle;
        }

        if ($orderDelivery['deliveryCycleType'] === 'week') {
            // 배송주기가 'week'라면 신청서 내 배송주기 요일에 맞는 데이터만 재조회
            $regularGoodsSnoListByDeliveryCycle = $this->regularGoodsRepository->findSnoByDeliveryCycleDay($regularGoodsSnoListByDeliveryCycle, $orderDelivery['deliveryCycleDay']);
        }

        // 배송주기가 충족한 상품 중 신청 가능 조건을 충족한 상품만 재조회
        $regularGoodsSnoList = $this->regularGoodsRepository->findApplyAbledRegularGoodsBySno($regularGoodsSnoListByDeliveryCycle, date('Y-m-d H:i:s'));

        return $regularGoodsSnoList;
    }

    /**
     * 배송 예정일 계산 함수
     *
     * @param array $postValue
     * @return array
     */
    public function calculateOrderDate(array $postValue): array
    {
        // 배송 타입
        $deliveryType = $postValue['deliveryType'];

        // 배송주기(1~6개월/주)
        $deliveryCycle = $postValue['selectedCycle'];

        // 배송주기(일/요일)
        $deliveryCycleDay = $postValue['selectedCycleDay'];

        // 정기결제(배송) 신청서 번호
        $applyNo = $postValue['applyNo'] ?? '';

        // 현재 배송회차에 따른 배송예정일,주문생성일 재 계산
        if ($postValue['beforeApplyFl']) {

            // 신청 전일 경우
            $isFirst = true;
            $baseDate = date('Y-m-d');
        }
        else {
            // 현재 배송회차 조회
            $currentDeliveryInfo = $this->regularOrderGoodsRepository->findCurrentDeliveryRoundByApplyNo($applyNo);
            $currentDeliveryRound = $currentDeliveryInfo['deliveryRound'];

            if ($currentDeliveryRound === 1) {
                $isFirst = true;
                // 1회차라면 신청서 생성일이 기준
                $baseDate = date('Y-m-d', strtotime($currentDeliveryInfo['regDt']));
            } else {
                $isFirst = false;
                $baseDate = $this->regularOrderDeliveryLogRepository->findDeliveryDueDateByApplyNoAndDeliveryRound($applyNo, $currentDeliveryRound-1);
            }
        }

        // 첫 배송예정일 계산
        return  RegularOrderUtil::calculateOrderDate($baseDate, $deliveryType, $deliveryCycle, $deliveryCycleDay, $isFirst);

    }
}
