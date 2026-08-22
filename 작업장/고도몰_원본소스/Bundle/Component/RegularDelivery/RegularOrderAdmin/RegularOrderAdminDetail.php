<?php
/*
 * Copyright (C) 2025 NHN COMMERCE. - All Rights Reserved
 *
 * Unauthorized copying or redistribution of this file in source and binary forms via any medium
 * is strictly prohibited.
 */

namespace Bundle\Component\RegularDelivery\RegularOrderAdmin;

use Origin\Enum\RegularDelivery\RegularGoods\RegularGoodsAttribute;
use Util\Order\RegularOrderUtil;
use Framework\Security\Encryptor;
use Origin\Enum\RegularDelivery\RegularOrder\RegularOrderLogActionType;
use Origin\Enum\RegularDelivery\RegularOrder\RegularOrderStatus;
use Origin\Repository\Payment\PgCardInfoRepositoryInterface;
use Repository\Code\CodeRepository;
use Repository\RegularDelivery\RegularGoods\RegularGoodsDeliveryCycleRepository;
use Repository\RegularDelivery\RegularOrder\RegularOrderAddGoodsRepository;
use Repository\RegularDelivery\RegularOrder\RegularOrderAdminMemoRepository;
use Repository\RegularDelivery\RegularOrder\RegularOrderConsultRepository;
use Repository\RegularDelivery\RegularOrder\RegularOrderDeliveryLogRepository;
use Repository\RegularDelivery\RegularOrder\RegularOrderGiftRepository;
use Repository\RegularDelivery\RegularOrder\RegularOrderGoodsRepository;
use Repository\RegularDelivery\RegularOrder\RegularOrderLogRepository;
use Repository\RegularDelivery\RegularOrder\RegularOrderStatusLogRepository;
use Repository\RegularDelivery\RegularOrder\RegularOrderApplierRepository;

/**
 * 관리자 정기결제 신청서 상세 페이지 조회
 */
class RegularOrderAdminDetail
{
    const CARD_MASKING = '****-****-****-';

    /**
     * @var RegularOrderGoodsRepository
     */
    private $regularOrderGoodsRepository;
    /**
     * @var RegularOrderConsultRepository
     */
    private $regularOrderConsultRepository;
    /**
     * @var RegularOrderAddGoodsRepository
     */
    private $regularOrderAddGoodsRepository;
    /**
     * @var Encryptor
     */
    private $encryptor;
    /**
     * @var RegularOrderGiftRepository
     */
    private $regularOrderGiftRepository;
    /**
     * @var RegularGoodsDeliveryCycleRepository
     */
    private $regularGoodsDeliveryCycleRepository;
    /**
     * @var RegularOrderLogRepository
     */
    private $regularOrderLogRepository;
    /**
     * @var RegularOrderStatusLogRepository
     */
    private $regularOrderStatusLogRepository;
    /**
     * @var RegularOrderDeliveryLogRepository
     */
    private $regularOrderDeliveryLogRepository;
    /**
     * @var PgCardInfoRepositoryInterface
     */
    private $cardInfoRepository;
    /**
     * @var CodeRepository
     */
    private $codeRepository;
    /**
     * @var RegularOrderAdminMemoRepository
     */
    private $regularOrderAdminMemoRepository;
    /**
     * @var RegularOrderApplierRepository
     */
    private $regularOrderApplierRepository;

    public function __construct(
        RegularOrderGoodsRepository $regularOrderGoodsRepository,
        RegularOrderGiftRepository $regularOrderGiftRepository,
        RegularGoodsDeliveryCycleRepository $regularGoodsDeliveryCycleRepository,
        RegularOrderConsultRepository $regularOrderConsultRepository,
        RegularOrderAddGoodsRepository $regularOrderAddGoodsRepository,
        RegularOrderLogRepository $regularOrderLogRepository,
        RegularOrderStatusLogRepository $regularOrderStatusLogRepository,
        RegularOrderDeliveryLogRepository $regularOrderDeliveryLogRepository,
        RegularOrderApplierRepository $regularOrderApplierRepository,
        CodeRepository $codeRepository,
        RegularOrderAdminMemoRepository $regularOrderAdminMemoRepository,
        PgCardInfoRepositoryInterface $cardInfoRepository,
        Encryptor $encryptor

    )
    {
        $this->regularOrderGoodsRepository = $regularOrderGoodsRepository;
        $this->regularOrderGiftRepository = $regularOrderGiftRepository;
        $this->regularGoodsDeliveryCycleRepository = $regularGoodsDeliveryCycleRepository;
        $this->regularOrderConsultRepository = $regularOrderConsultRepository;
        $this->regularOrderAddGoodsRepository = $regularOrderAddGoodsRepository;
        $this->regularOrderLogRepository = $regularOrderLogRepository;
        $this->regularOrderStatusLogRepository = $regularOrderStatusLogRepository;
        $this->regularOrderDeliveryLogRepository = $regularOrderDeliveryLogRepository;
        $this->regularOrderApplierRepository = $regularOrderApplierRepository;
        $this->codeRepository = $codeRepository;
        $this->regularOrderAdminMemoRepository = $regularOrderAdminMemoRepository;
        $this->cardInfoRepository = $cardInfoRepository;
        $this->encryptor = $encryptor;

    }

    /**
     * 신청번호에 해당하는 정보 조회
     *
     * @param int $applyNo
     * @return array
     */
    public function getRegularOrderApplyInfo(int $applyNo): array
    {
        $regularOrderGoods = $this->regularOrderGoodsRepository->findRegularOrderGoodsByApplyNo($applyNo);
        // 상품 이미지 url 생성
        $imageName = $regularOrderGoods['imageStorage'] === 'obs' ? $regularOrderGoods['imageUrl'] : $regularOrderGoods['imageName'];
        $regularOrderGoods['imageUrl'] = gd_html_goods_image($regularOrderGoods['goodsNo'], $imageName, $regularOrderGoods['imagePath'], $regularOrderGoods['imageStorage'], 40, $regularOrderGoods['regularGoodsNm'], '_blank');

        // 상품 옵션 명 조합
        $regularOrderGoods['optionName'] = json_decode($regularOrderGoods['regularGoodsOptionInfo'], true);

        // 상품 텍스트 옵션 정보 및 텍스트 옵션 가격 재계산
        $regularOrderGoods['optionTextInfo'] = $this->calculateOptionTextPrice($regularOrderGoods);

        // 이용 상태 라벨
        $regularOrderGoods['applyStatusLabel'] = RegularOrderStatus::getStatusLabel($regularOrderGoods['applyStatus']);

        // 사은품 정보 조회
        $giftInfo = $this->regularOrderGiftRepository->findGiftInfoByApplyNo($applyNo);
        $giftList = [];
        foreach ($giftInfo as $gift) {
            $isGiftSoldOut = $gift['stockFl'] === 'y' && $gift['stockCnt'] <= 0; // 재고 사용함 인데 0보다 작은 경우 품절

            $giftList[] = [
                'conditionTitle' => $gift['conditionTitle'],
                'giftNm' => $gift['giftNm'],
                'giveCnt' => $gift['giveCnt'],
                'imageUrl' => gd_html_preview_image($gift['imageNm'], $gift['imagePath'], $gift['imageStorage'], 40, 'gift', $gift['giftNm'], null, false),
                'isGiftSoldOut' => $isGiftSoldOut
            ];
        }

        // 추가상품은 다음 배열 인덱스로 추가되어야 화면에서 정기결제 상품처럼 row 로 뿌려줄 수 있음.
        $regularOrderGoodsList = [];
        $regularOrderGoodsList[] = $regularOrderGoods; // 첫번째 인덱스는 원본

        // 추가상품 정보 조회
        $addGoodsInfo = $this->regularOrderAddGoodsRepository->findRegularOrderAddGoodsByApplyNo($applyNo);
        $addGoodsTotalPrice = 0;
        foreach ($addGoodsInfo as $addGoods) {
            $regularOrderGoodsList[] = array_merge($regularOrderGoods, [
                'addGoodsNo' => $addGoods['addGoodsNo'],
                'addGoodsScmCompanyName' => $addGoods['companyNm'],
                'addGoodsName' => $addGoods['goodsNm'],
                'addGoodsCnt' => $addGoods['regularAddGoodsCnt'],
                'addGoodsPrice' => $addGoods['regularAddGoodsPrice'],
                'addGoodsOptionName' => $addGoods['addGoodsOptionName'],
                'addGoodsImage' => gd_html_add_goods_image($addGoods['goodsNo'], $addGoods['imageNm'], $addGoods['imagePath'], $addGoods['imageStorage'], 40, $addGoods['goodsNm'], '_blank'),
            ]);
            $addGoodsTotalPrice += ($addGoods['regularAddGoodsPrice'] * $addGoods['regularAddGoodsCnt']); // (추가상품가 * 수량)
        }

        // 가격
        // 정기결제 금액 (정기결제가 * 수량) -> 정기결제 금액
        $regularOrderPrice = ($regularOrderGoods['regularGoodsPrice'] + $regularOrderGoods['regularGoodsOptionPrice'] + $regularOrderGoods['regularGoodsOptionTextPrice']) * $regularOrderGoods['regularGoodsCnt'];
        // 정기결제 금액 + (추가상품가 * 수량) -> 총 정기결제금액
        $regularOrderTotalPrice = $regularOrderPrice + $addGoodsTotalPrice;
        // 원본결제 금액 -> 원본결제금액 (원본 상품의 가격 + 옵션가 + 텍스트옵션가) * 수량
        $originOrderPrice = RegularOrderUtil::calculateTotalRegularOrderOriginGoodsPrice($regularOrderGoods);
        // 원본결제 금액 + (추가상품가 * 수량) -> 총 원본결제금액
        $originOrderTotalPrice = $originOrderPrice + $addGoodsTotalPrice;

        return [
            'regularOrderGoods' => $regularOrderGoodsList,
            'giftInfo' => $giftList,
            'regularOrderPrice' => $regularOrderPrice,
            'regularOrderTotalPrice' => $regularOrderTotalPrice,
            'originOrderPrice' => $originOrderPrice,
            'originOrderTotalPrice' => $originOrderTotalPrice,
            'totalDcPrice' => RegularOrderUtil::calculateRegularOrderOriginGoodsDiscountPrice($regularOrderGoods),
            'cardCompany' => $this->cardInfoRepository->findCardNmByCardNo($regularOrderGoods['cardNo']) ?? '', // 카드사 명
            'cardNo' => self::CARD_MASKING.$this->encryptor->decrypt($regularOrderGoods['cardNo']), // 마스킹 된 카드 번호 4자리
            'applyStatus' => $regularOrderGoods['applyStatus'],
            'canSkipFl' =>
                $regularOrderGoods['deliveryRoundSkipFl'] === 'n' // n = 스킵 되지 않은 상태와
                && ($regularOrderGoods['maxDeliveryRound'] === 0 || $regularOrderGoods['deliveryRound'] < $regularOrderGoods['maxDeliveryRound']) // 무제한 배송 또는 현재 배송회차가 최대 배송회차보다 작을 때
        ];
    }
    

    /**
     * 정기결제 상세페이지 배송주기 변경 모달에서 사용할 정보
     *
     * @param int $applyNo
     * @return array
     * @throws \Exception
     */
    public function getRegularGoodsDeliveryCycle(int $applyNo): array
    {
        $deliveryCycle = [];
        $deliveryCycleWeekDay = [];

        $regularGoodsInfo = $this->regularOrderGoodsRepository->findRegularOrderGoodsInfoByApplyNo($applyNo);
        if (empty($regularGoodsInfo)) {
            throw new \Exception('정기결제 신청번호에 해당하는 정기결제 상품 정보가 존재하지 않습니다.');
        }

        $regularGoodsPolicy = json_decode($regularGoodsInfo['regularGoodsPolicy'], true);

        // 월/주 타입에 따른 컬럼 조회 및 세팅
        if ($regularGoodsPolicy['deliveryCycleType'] === 'month') {
            // month
            $deliveryCycle = $regularGoodsPolicy['cycleData']['monthCycle'];
        } else if ($regularGoodsPolicy['deliveryCycleType'] === 'week') {
            // week
            $deliveryCycle = $regularGoodsPolicy['cycleData']['weekCycle'];
            $deliveryCycleWeekDay = $regularGoodsPolicy['cycleData']['weekDayCycle'];
        }

        // 종료회차 세팅
        if ($regularGoodsPolicy['deliveryRoundsDisplayType'] === 'all') {
            // 2회차부터 가능
            $maxDeliveryRoundsList = range(2, RegularGoodsAttribute::MAX_DELIVERY_ROUNDS);
        } elseif ($regularGoodsPolicy['deliveryRoundsDisplayType'] === 'abled') {
            $maxDeliveryRoundsList = range(2, $regularGoodsPolicy['maxDeliveryRounds']);
        } else {
            $maxDeliveryRoundsList = null;
        }

        return [
            'cycleType' => $regularGoodsPolicy['deliveryCycleType'],
            'cycle' => RegularOrderUtil::getDeliveryCycleData($regularGoodsPolicy['deliveryCycleType'], $deliveryCycle, $deliveryCycleWeekDay),
            'roundDisplayType' => $regularGoodsPolicy['deliveryRoundsDisplayType'],
            'maxDeliveryRounds' => $maxDeliveryRoundsList,
            'currentDeliveryInfo' => [ // 현재 주문서의 배송주기 및 종료회차
                'cycleType' => $regularGoodsInfo['deliveryCycleType'],
                'cycle' => $regularGoodsInfo['deliveryCycle'],
                'cycleDay' => $regularGoodsInfo['deliveryCycleDay'],
                'maxDeliveryRounds' => $regularGoodsInfo['maxDeliveryRound'],
            ]
        ];
    }

    /**
     * 신청자 및 배송지 관련 정보 조회
     *
     * @param int $applyNo
     * @return array
     */
    public function getRegularOrderShippingInfo(int $applyNo): array
    {
        $applierInfo = $this->regularOrderApplierRepository->findApplierInfoByApplyNo($applyNo);
        $shippingInfo = $this->regularOrderGoodsRepository->findShippingInfoByApplyNo($applyNo);

        return [
            'applierInfo' => $applierInfo,
            'shippingInfo' => $shippingInfo
        ];
    }

    /**
     * 정기결제 신청서의 상담 메모 조회
     *
     * @param int $applyNo
     * @return array
     */
    public function getRegularOrderConsultMemo(int $applyNo): array
    {
        return $this->regularOrderConsultRepository->findRegularOrderConsultByApplyNo($applyNo);
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
     * 신청 배송 로그
     * @param int $applyNo
     * @return array
     */
    public function getRegularDeliveryLog(int $applyNo): array
    {
        $deliveryLog = $this->regularOrderDeliveryLogRepository->findRegularOrderDeliveryLogByApplyNo($applyNo);
        $regularOrderGoods = $this->regularOrderGoodsRepository->findRegularOrderGoodsInfoByApplyNo($applyNo);
        $regularOrderStatus = $regularOrderGoods['applyStatus'];

        // 정기결제 신청서가 일시정지 또는 해지 상태인 경우, 마지막 배송 로그는 0000-00-00 으로 표시
        if (in_array($regularOrderStatus, RegularOrderStatus::getPauseStatus()) || in_array($regularOrderStatus, RegularOrderStatus::getInactiveStatus())) {
            reset($deliveryLog); // 첫번쨰 인덱스로 이동
            $firstIndex = key($deliveryLog);
            if ($firstIndex !== null) {
                $deliveryLog[$firstIndex]['deliveryDate'] = '0000-00-00';
            }
        }

        return $deliveryLog;
    }

    /**
     * 정기결제 신청서의 배송예정일 조회
     *
     * @param string $orderNo
     * @return string
     */
    public function findDeliveryDueDateByOrderNo(string $orderNo): string
    {
        return $this->regularOrderDeliveryLogRepository->findDeliveryDueDateByOrderNo($orderNo);
    }

    /**
     * 신청서 이용상태 로그
     * @param int $applyNo
     * @return array
     */
    public function getRegularStatusLog(int $applyNo): array
    {
        $statusLogList = $this->regularOrderStatusLogRepository->findRegularOrderStatusLogByApplyNo($applyNo);
        $logList = [];
        foreach ($statusLogList as $statusLog) {
            $logList[] = [
                'regDt' => $statusLog['regDt'],
                'modifierIP' => $statusLog['modifierIP'],
                'managerId' => (!empty($statusLog['managerId'])) ? $statusLog['managerId'] : '',
                'status' => RegularOrderLogActionType::combineDescription(RegularOrderStatus::getStatusLabel($statusLog['prevOrderStatus']), RegularOrderStatus::getStatusLabel($statusLog['changeOrderStatus'])),
                'description' => $statusLog['description']
            ];
        }

        return $logList;
    }

    /**
     * 신청서 변경 이력 로그
     * @param int $applyNo
     * @return array
     */
    public function getRegularChangeLog(int $applyNo): array
    {
        $changeLogList = $this->regularOrderLogRepository->findRegularOrderLogByApplyNo($applyNo);

        $logList = [];
        foreach ($changeLogList as $changeLog) {
            $logList[] = [
                'regDt' => $changeLog['regDt'],
                'modifierIP' => $changeLog['modifierIP'],
                'managerId' => (!empty($changeLog['managerId'])) ? $changeLog['managerId'] : '',
                'actionType' => $changeLog['actionType'],
                'actionDesc' => $changeLog['actionDesc']
            ];
        }

        return $logList;
    }

    /**
     * 관리자 메모 조회
     *
     * @param int $applyNo
     * @return array|null
     */
    public function getAdminMemo(int $applyNo)
    {
        $adminMemo = $this->regularOrderAdminMemoRepository->findAdminMemoByApplyNo($applyNo);

        return !empty($adminMemo) ? $adminMemo : null;
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

    /**
     * 텍스트 옵션 정보에 할인된 정기결제 텍스트 옵션 가격을 계산하여 추가
     * optionTextInfo 는 실제 주문 생성때 사용되는 데이터이기 때문에 정기결제 가격을 가지고 있지는 않다.
     * 따라서 관리자 정기결제 신청서 상세 페이지에서 텍스트 옵션 가격을 계산하여 보여주기 위해 추가한다.
     *
     * @param array $regularOrderGoods
     * @return array
     */
    private function calculateOptionTextPrice(array $regularOrderGoods): array
    {
        $optionTextInfo = json_decode($regularOrderGoods['regularGoodsOptionTextInfo'], true) ?? [];
        $regularGoodsPolicy = json_decode($regularOrderGoods['regularGoodsPolicy'], true);

        foreach ($optionTextInfo as $key => $textOption) {
            $originTextOptionPrice = $textOption[2];
            $discountTextOptionPrice = RegularOrderUtil::calculateRegularOptionPrice(
                $originTextOptionPrice,
                $regularGoodsPolicy['discountUseFl'],
                $regularGoodsPolicy['discountType'],
                $regularGoodsPolicy['discountRate']
            );

            $optionTextInfo[$key][] = (int) $discountTextOptionPrice; // 할인된 텍스트 옵션 가격
        }

        return $optionTextInfo;
    }
}
