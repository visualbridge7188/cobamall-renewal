<?php
/*
 * Copyright (C) 2025 NHN COMMERCE. - All Rights Reserved
 *
 * Unauthorized copying or redistribution of this file in source and binary forms via any medium
 * is strictly prohibited.
 */

namespace Bundle\Component\RegularDelivery\Notification;

use Repository\Member\MemberRepository;
use Component\Sms\Code;
use Component\Sms\SmsAuto;
use Component\Sms\SmsAutoCode;
use Component\RegularDelivery\Notification\RegularDeliveryReplaceCodeFactory;
use Illuminate\Database\Eloquent\Collection;
use Repository\RegularDelivery\RegularOrder\RegularOrderDeliveryLogRepository;
use Repository\RegularDelivery\RegularOrder\RegularOrderApplierRepository;
use Repository\RegularDelivery\RegularOrder\RegularOrderGoodsRepository;
use Repository\Order\OrderInfoRepository;
use Origin\Enum\RegularDelivery\RegularGoods\DeliveryCycle;
use Framework\Log\Logger;
use Origin\Repository\Payment\PgCardInfoRepositoryInterface;
use Repository\Delivery\ManageDeliveryCompanyRepository;
use Repository\RegularDelivery\RegularOrder\RegularOrderAddGoodsRepository;
use Repository\RegularDelivery\RegularOrder\RegularOrderDeliveryRepository;
use Repository\RegularDelivery\RegularOrder\RegularOrderRepository;
use Framework\Security\Encryptor;
use Util\Order\RegularOrderUtil;
use Repository\Order\OrderRepository;
use Origin\Enum\RegularDelivery\RegularOrder\RegularOrderStatus;

class RegularDeliverySmsSender
{
    /**
     * @var MemberRepository
     */
    private $memberRepo;
    /**
     * @var string
     */
    private $smsType;
    /**
     * @var array
     */
    private $mallInfo;
    /**
     * @var OrderInfoRepository
     */
    private $orderInfoRepo;
    /**
     * @var RegularOrderDeliveryLogRepository
     */
    private $regularOrderDeliveryLogRepo;
    /**
     * @var RegularOrderApplierRepository
     */
    private $regularOrderApplierRepo;
    /**
     * @var RegularOrderGoodsRepository
     */
    private $regularOrderGoodsRepo;
    /**
     * @var PgCardInfoRepositoryInterface
     */
    private $pgCardInfoRepository;
    /**
     * @var ManageDeliveryCompanyRepository
     */
    private $manageDeliveryCompanyRepo;
    /**
     * @var RegularOrderAddGoodsRepository
     */
    private $regularOrderAddGoodsRepo;
    /**
     * @var RegularOrderDeliveryRepository
     */
    private $regularOrderDeliveryRepo;
    /**
     * @var Logger
     */
    private $logger;
    /**
     * @var Encryptor
     */
    private $encryptor;
    /**
     * @var OrderRepository
     */
    private $orderRepository;
    /**
     * @var RegularOrderRepository
     */
    private $regularOrderRepo;

    public function __construct(
        MemberRepository $memberRepo,
        OrderInfoRepository $orderInfoRepo,
        RegularOrderDeliveryLogRepository $regularOrderDeliveryLogRepo,
        RegularOrderApplierRepository $regularOrderApplierRepo,
        RegularOrderGoodsRepository $regularOrderGoodsRepo,
        PgCardInfoRepositoryInterface $pgCardInfoRepository,
        ManageDeliveryCompanyRepository $manageDeliveryCompanyRepo,
        RegularOrderAddGoodsRepository $regularOrderAddGoodsRepo,
        RegularOrderDeliveryRepository $regularOrderDeliveryRepo,
        Logger $logger,
        Encryptor $encryptor,
        OrderRepository $orderRepository,
        RegularOrderRepository $regularOrderRepo
    )
    {
        $this->memberRepo = $memberRepo;
        $this->smsType = SmsAutoCode::REGULAR_DELIVERY;
        $this->mallInfo = gd_policy('basic.info');
        $this->orderInfoRepo = $orderInfoRepo;
        $this->regularOrderDeliveryLogRepo = $regularOrderDeliveryLogRepo;
        $this->regularOrderApplierRepo = $regularOrderApplierRepo;
        $this->regularOrderGoodsRepo = $regularOrderGoodsRepo;
        $this->pgCardInfoRepository = $pgCardInfoRepository;
        $this->manageDeliveryCompanyRepo = $manageDeliveryCompanyRepo;
        $this->regularOrderAddGoodsRepo = $regularOrderAddGoodsRepo;
        $this->regularOrderDeliveryRepo = $regularOrderDeliveryRepo;
        $this->logger = $logger;
        $this->encryptor = $encryptor;
        $this->orderRepository = $orderRepository;
        $this->regularOrderRepo = $regularOrderRepo;
    }

    /**
     * 신청자 정보 조회
     * @param string $applyGroupNo
     * @return array
     */
    public function getApplierInfo(string $applyGroupNo): array
    {
        $applierInfo = $this->regularOrderApplierRepo->findApplierInfoByApplyGroupNo($applyGroupNo);
        // 발송 정보용 데이터 세팅
        $applierInfo['cellPhone'] = $applierInfo['applierCellPhone'];

        $regularOrder = $this->regularOrderRepo->findByApplyGroupNo($applyGroupNo);
        $applierInfo['memNo'] = $regularOrder['memNo'] ?? null;

        return $applierInfo;
    }

    /**
     * 카드 정보 조회
     * @param string $memNo
     * @param array $regularOrderReplaceInfo
     * @return array
     */
    private function getCardInfo(string $memNo, array $regularOrderReplaceInfo): array
    {
        $cardInfo = $this->pgCardInfoRepository->findUsedCardByCardNoAndMemNo($regularOrderReplaceInfo['cardNo'], $memNo);

        if (empty($cardInfo)) {
            $this->logger->channel('regularDelivery')->warning(__CLASS__ . ' ' . __METHOD__ . ' 정기배송 신청 완료 알림 발송 오류 - 카드 정보 없음, 신청 그룹번호 :', [$regularOrderReplaceInfo['applyGroupNo']]);
            return [];
        }
        $deliveryCardInfo = $this->getDeliveryCardInfo($cardInfo);

        $replaceCardInfo = [];
        $replaceCardInfo['cardNm'] = $deliveryCardInfo['cardName'] ?? '';
        $replaceCardInfo['cardNo'] = $deliveryCardInfo['cardLastNum'] ?? '';

        return $replaceCardInfo;
    }

    /**
     * 정기결제 카드정보 조회
     * @param array $cardInfo
     * @return array
     */
    private function getDeliveryCardInfo(array $cardInfo): array
    {
        // 암호화된 카드번호 복호화(카드 뒷번호 4자리)
        $decryptCardNo = $this->encryptor->decrypt($cardInfo['cardNo']);

        return [
            'cardName' => $cardInfo['cardNm'] ?? '',
            'cardLastNum' => $decryptCardNo ?? '',
        ];
    }

    /**
     * 정기배송 일시정지 시 발송 데이터 생성 후 발송
     * @param array $regularOrderGoodsInfo
     */
    public function sendPauseSms(array $regularOrderGoodsInfo)
    {
        // 공통 치환 정보 조회
        list($receiverInfo, $replaceInfo) = $this->buildReplaceInfoByApplyInfoList($regularOrderGoodsInfo);

        $replaceArguments = RegularDeliveryReplaceCodeFactory::makePauseData($this->mallInfo, $receiverInfo, $replaceInfo);

        // SMS 발송
        $this->sendSms($receiverInfo, $replaceArguments, $this->smsType, Code::REGULAR_DELIVERY_PAUSED);
    }

    /**
     * 정기배송 일시정지 해제 시 발송 데이터 생성 후 발송
     * @param array $regularOrderGoodsInfo
     */
    public function sendResumeSms(array $regularOrderGoodsInfo)
    {
        // 공통 치환 정보 조회
        list($receiverInfo, $replaceInfo, $sendApplyNoInfo) = $this->buildReplaceInfoByApplyInfoList($regularOrderGoodsInfo);
        // 카드 정보 조회
        $cardInfo = $this->getCardInfo($sendApplyNoInfo['memNo'], $sendApplyNoInfo);
        $replaceInfo['cardName'] = $cardInfo['cardNm'];
        $replaceInfo['cardNo'] = $cardInfo['cardNo'];

        $replaceArguments = RegularDeliveryReplaceCodeFactory::makeResumeData($this->mallInfo, $receiverInfo, $replaceInfo);

        // SMS 발송
        $this->sendSms($receiverInfo, $replaceArguments, $this->smsType, Code::REGULAR_DELIVERY_RESUMED);
    }

    /**
     * 회차 건너뛰기 시 발송 데이터 생성 후 발송
     * @param array $regularOrderGoodsInfo
     */
    public function sendSkipDeliveryRoundSms(array $regularOrderGoodsInfo, string $skipDeliveryDate)
    {
        $receiverInfo = $this->getApplierInfo($regularOrderGoodsInfo['applyGroupNo']);
        // 신청번호로 공급사 번호 조회
        $receiverInfo['scmNo'] = $this->getScmNoByApplyNo($regularOrderGoodsInfo['applyNo']);

        $replaceInfo = $this->buildReplaceInfo($regularOrderGoodsInfo);
        $replaceInfo['deliveryDueDate'] = $skipDeliveryDate;
        $replaceArguments = RegularDeliveryReplaceCodeFactory::makeSkipDeliveryRoundData($this->mallInfo, $receiverInfo, $replaceInfo);

        // SMS 발송
        $this->sendSms($receiverInfo, $replaceArguments, $this->smsType, Code::REGULAR_DELIVERY_SKIPPED);
    }

    /**
     * 해지 시 발송 데이터 생성 후 발송
     * @param array $regularOrderGoodsInfo
     * @param string $cancelReason
     */
    public function sendCancelSms(array $regularOrderGoodsInfo, string $cancelReason)
    {
        // 공통 치환 정보 조회
        list($receiverInfo, $replaceInfo) = $this->buildReplaceInfoByApplyInfoList($regularOrderGoodsInfo);
        // 해지 사유 변환
        $replaceInfo['cancelReason'] = RegularOrderStatus::getStatusLabel($cancelReason);

        $replaceArguments = RegularDeliveryReplaceCodeFactory::makeCancelData($this->mallInfo, $receiverInfo, $replaceInfo);

        // SMS 발송
        $this->sendSms($receiverInfo, $replaceArguments, $this->smsType, Code::REGULAR_DELIVERY_CANCELED);
    }

    /**
     * 결제 예정 시 발송 데이터 생성 후 발송
     * @param Collection $groups
     */
    public function sendPaymentScheduledSms(Collection $groups)
    {
        // 결제 예정에 해당 하는 모든 타겟 정보(주문생성 대상 중 주문생성일 -1일 전인 신청건들)
        $targets = $groups->toArray();

        // 배송회차가 가장 높고 신청번호가 가장 빠른 상품 기준 치환
        $rows = $groups
            ->sort(function ($order1, $order2) { 
                return $order1['deliveryRound'] == $order2['deliveryRound'] 
                ? $order1['applyNo'] <=> $order2['applyNo'] // 2차 정렬: 신청번호 빠른 순서
                : $order2['deliveryRound'] <=> $order1['deliveryRound']; // 1차 정렬: 배송회차 높은 순서
            })
            ->first();

        if (!$rows) {
            $this->logger->channel('regularDelivery')->warning(__CLASS__ . ' ' . __METHOD__ . ' 정기결제 예정 알림 발송 오류 - 주문 정보 없음 :', [$groups->toArray()]);
            return;
        }

        $receiverInfo = $this->getApplierInfo($rows->applyGroupNo);

        // 카드 정보 조회
        $cardInfo = $this->getCardInfo($rows->memNo, $rows->toArray());

        // 상품명
        $subGoodsNm = $this->getSubGoodsNm($targets, $rows->regularGoodsNm);

        // 총 상품 금액((정기결제가 + 옵션가 + 텍스트옵션가) × 상품개수 + (추가상품가 × 추가상품개수))
        $totalGoodsPrice = $this->getTotalPrice($targets);

        // 총 배송비
        $totalDeliveryCharge = $this->getTotalDeliveryCharge();

        // 총 결제 금액
        $totalPrice = $totalGoodsPrice + $totalDeliveryCharge;

        $replaceInfo = [
            'orderCreateDate' => $rows->orderCreateDate,
            'totalPrice' => $totalPrice,
            'cardName' => $cardInfo['cardNm'],
            'cardNo' => $cardInfo['cardNo'],
            'subGoodsNm' => $subGoodsNm
        ];

        $replaceArguments = RegularDeliveryReplaceCodeFactory::makePaymentScheduledData($this->mallInfo, $receiverInfo, $replaceInfo);

        // SMS 발송
        $this->sendSms($receiverInfo, $replaceArguments, $this->smsType, Code::REGULAR_PAYMENT_SCHEDULED);
    }

    /**
     * 결제 실패 시 발송 데이터 생성 후 발송
     * @param Collection $groups
     * @param string $memNo
     */
    public function sendPaymentFailSms(Collection $groups)
    {
        // 결제 실패에 해당 하는 모든 타겟(주문생성일이 당일이고 주문생성 조건에 부합한 신청건들)
        $targets = $groups->toArray();

        // 배송회차가 가장 높고 신청번호가 가장 빠른 상품 기준 치환
        $rows = $groups
            ->sort(function ($order1, $order2) { 
                return $order1['deliveryRound'] == $order2['deliveryRound'] 
                ? $order1['applyNo'] <=> $order2['applyNo'] // 2차 정렬: 신청번호 빠른 순서
                : $order2['deliveryRound'] <=> $order1['deliveryRound']; // 1차 정렬: 배송회차 높은 순서
            })
            ->first();

        if (!$rows) {
            $this->logger->channel('regularDelivery')->warning(__CLASS__ . ' ' . __METHOD__ . ' 정기결제 실패 알림 발송 오류 - 주문 정보 없음 :', [$groups->toArray()]);
            return;
        }

        $receiverInfo = $this->getApplierInfo($rows->applyGroupNo);

        // 카드 정보 조회
        $cardInfo = $this->getCardInfo($rows->memNo, $rows->toArray());

        // 상품명
        $subGoodsNm = $this->getSubGoodsNm($targets, $rows->regularGoodsNm);

        // 총 상품 금액((정기결제가 + 옵션가 + 텍스트옵션가) × 상품개수 + (추가상품가 × 추가상품개수))
        $totalGoodsPrice = $this->getTotalPrice($targets);

        // 총 배송비
        $totalDeliveryCharge = $this->getTotalDeliveryCharge();

        // 총 결제 금액
        $totalPrice = $totalGoodsPrice + $totalDeliveryCharge;

        $replaceInfo = [
            'orderCreateDate' => $rows->orderCreateDate,
            'totalPrice' => $totalPrice,
            'cardName' => $cardInfo['cardNm'],
            'cardNo' => $cardInfo['cardNo'],
            'subGoodsNm' => $subGoodsNm
        ];

        $replaceArguments = RegularDeliveryReplaceCodeFactory::makePaymentFailData($this->mallInfo, $receiverInfo, $replaceInfo);

        // SMS 발송
        $this->sendSms($receiverInfo, $replaceArguments, $this->smsType, Code::REGULAR_PAYMENT_FAILED);
    }

    /**
     * 신청 완료 시 발송 데이터 생성 후 발송
     * @param string $memNo
     * @param array $regularOrderReplaceInfo
     */
    public function sendApplyCompleteSms(string $memNo, array $regularOrderReplaceInfo)
    {
        $applierInfo = $this->getApplierInfo($regularOrderReplaceInfo['applyGroupNo']);

        // 카드 정보 조회
        $cardInfo = $this->getCardInfo($memNo, $regularOrderReplaceInfo);

        // 신청그룹번호에서 신청번호가 가장 높은 상품(regularOrderReplaceInfo) 기준으로 치환코드 정보 세팅
        $replaceInfo = [];
        // 배송주기
        $replaceInfo['deliveryCycle'] = $this->replaceDeliveryCycle($regularOrderReplaceInfo);
        // 정기배송지
        $replaceInfo['deliveryAddress'] = $regularOrderReplaceInfo['shippingAddress'] . ' ' . $regularOrderReplaceInfo['shippingAddressSub'];

        // 카드명
        $replaceInfo['cardName'] = $cardInfo['cardNm'];

        // 카드번호
        $replaceInfo['cardNo'] = $cardInfo['cardNo'];

        // 상품명
        $regularOrderGoodsInfo = $this->regularOrderGoodsRepo->findRegularOrderGoodsInfoByApplyGroupNoList([$regularOrderReplaceInfo['applyGroupNo']]);
        $replaceInfo['goodsNm'] = $this->getSubGoodsNm($regularOrderGoodsInfo, $regularOrderReplaceInfo['regularGoodsNm']);

        // 배송예정일
        $replaceInfo['deliveryDueDate'] = $regularOrderReplaceInfo['deliveryDueDate'];

        // 종료 회차
        $replaceInfo['endPayRound'] = $regularOrderReplaceInfo['maxDeliveryRound'];

        $replaceArguments = RegularDeliveryReplaceCodeFactory::makeApplyCompleteData($this->mallInfo, $applierInfo, $replaceInfo);

        // SMS 발송
        $this->sendSms($applierInfo, $replaceArguments, $this->smsType, Code::REGULAR_DELIVERY_APPLIED);
    }

    /**
     * 결제 완료 시 발송 데이터 생성 후 발송
     * @param Collection $groups
     * @param string $memNo
     * @param string $orderNo
     */
    public function sendPaymentCompleteSms(Collection $groups, string $memNo, string $orderNo)
    {
        // 결제 완료에 해당 하는 모든 타겟(주문생성일이 당일이고 주문생성 조건에 부합한 신청건들)
        $targets = $groups->toArray();

        // 배송회차가 가장 높고 신청번호가 가장 빠른 상품 기준 치환
        $rows = $groups
            ->sort(function ($order1, $order2) { 
                return $order1['deliveryRound'] == $order2['deliveryRound'] 
                ? $order1['applyNo'] <=> $order2['applyNo'] // 2차 정렬: 신청번호 빠른 순서
                : $order2['deliveryRound'] <=> $order1['deliveryRound']; // 1차 정렬: 배송회차 높은 순서
            })
            ->first();

        if (!$rows) {
            $this->logger->channel('regularDelivery')->warning(__CLASS__ . ' ' . __METHOD__ . ' 정기결제 성공 알림 발송 오류 - 주문 정보 없음 :', [$groups->toArray()]);
            return;
        }

        $receiverInfo = $this->memberRepo->findMemberInfoByMemNo($memNo);

        // 신청번호 리스트로 공급사 번호 조회
        $applyNoList = array_column($targets, 'applyNo');
        $receiverInfo['scmNo'] = $this->getScmNoByApplyNo($applyNoList);


        $subGoodsNm = $this->getSubGoodsNm($targets, $rows->regularGoodsNm);

        // 총 상품 금액((정기결제가 + 옵션가 + 텍스트옵션가) × 상품개수 + (추가상품가 × 추가상품개수))
        $totalGoodsPrice = $this->getTotalPrice($targets);

        // 총 배송비
        $totalDeliveryCharge = $this->getTotalDeliveryCharge($orderNo);

        // 총 결제 금액
        $totalPrice = $totalGoodsPrice + $totalDeliveryCharge;

        $replaceRegularOrderInfo = [
            'deliveryDueDate' => $rows->deliveryDueDate,
            'deliveryRound' => $rows->deliveryRound,
            'orderNo' => $orderNo,
            'subGoodsNm' => $subGoodsNm,
            'totalPrice' => $totalPrice
        ];
        $replaceArguments = RegularDeliveryReplaceCodeFactory::makePaymentCompleteData($this->mallInfo, $receiverInfo, $replaceRegularOrderInfo);

        // SMS 발송
        $this->sendSms($receiverInfo, $replaceArguments, $this->smsType, Code::REGULAR_PAYMENT_COMPLETED);
    }

    /**
     * 정기배송 안내용 발송
     * @param array $orderData
     */
    public function sendRegularDeliveryNoticeSms(array $orderData)
    {
        $snoList = array_column($orderData['goods'], 'goodsNo');

        // 배송중 처리 주문상품 중 가장 높은 배송회차, 배송예정일 조회
        $highestDeliveryRoundGoodsInfo = $this->regularOrderDeliveryLogRepo->findHighestDeliveryRoundInfoByOrderGoodsSnoList($snoList, $orderData['orderNo']);

        // 주문 상품 중 발송 대상의 상품 정보 추출
        $orderGoodsInfo = array_filter($orderData['goods'], function($goods) use ($highestDeliveryRoundGoodsInfo) {
            return $goods['goodsNo'] == $highestDeliveryRoundGoodsInfo['orderGoodsSno'];
        });
        $orderGoodsInfo = reset($orderGoodsInfo);

        if (empty($orderGoodsInfo)) {
            $this->logger->channel('regularDelivery')->warning(__CLASS__ . ' ' . __METHOD__ . ' 정기배송 안내용 발송 오류 - 주문 상품 정보 없음, 주문번호 :', [$orderData['orderNo'], $orderData]);
            return;
        }

        // 택배사 조회
        $deliveryCompanyInfo = $this->manageDeliveryCompanyRepo->findManageDeliveryCompanyBySno((int)$orderGoodsInfo['invoiceCompanySno']);
        $orderGoodsInfo['deliveryName'] = $deliveryCompanyInfo['companyName'];

        // 주문자 정보
        $orderInfo = $this->orderInfoRepo->findOrderInfoByOrderNo($orderData['orderNo']);

        // 상품명 추출
        $orderGoodsInfo['goodsNm']  = mb_substr($orderGoodsInfo['goodsNm'], 0, 18, 'UTF-8');
        if (count($orderData['goods']) > 1) {
            $orderGoodsInfo['goodsNm'] = $orderGoodsInfo['goodsNm'] . ' 외 ' . (count($orderData['goods']) - 1) . '건';
        }

        // 주문 및 주문자 정보
        $orderData = array_merge($orderInfo, $orderData, $highestDeliveryRoundGoodsInfo);

        $replaceArguments = RegularDeliveryReplaceCodeFactory::makeRegularDeliveryNoticeData($this->mallInfo, $orderData, $orderGoodsInfo);

        // 주문자 전화번호
        $receiverInfo['cellPhone'] = $orderData['orderCellPhone'];
        $receiverInfo['memNo'] = $orderData['memNo'] ?? '';

        // 공급사 번호 (본사는 기존 발송 로직에 포함되어 있어 제외 처리)
        $scmNoList = array_unique(array_column($orderData['goods'], 'scmNo'));
        $receiverInfo['scmNo'] = array_filter($scmNoList, function($scmNo) {
            return $scmNo != DEFAULT_CODE_SCMNO;
        });

        // SMS 발송
        $this->sendSms($receiverInfo, $replaceArguments, $this->smsType, Code::REGULAR_DELIVERY_NOTICE);
    }

    /**
     * 정기배송 일정 변경 시 발송 데이터 생성 후 발송
     * @param array $changeData
     * @param array $regularOrderGoodsInfo
     */
    public function sendRegularDeliveryInfoChangeSms(array $changeData, array $regularOrderGoodsInfo)
    {
        $receiverInfo = $this->getApplierInfo($regularOrderGoodsInfo['applyGroupNo']);
        // 신청번호로 공급사 번호 조회
        $receiverInfo['scmNo'] = $this->getScmNoByApplyNo($regularOrderGoodsInfo['applyNo']);

        $changeData['deliveryCycle'] = $this->replaceDeliveryCycle($regularOrderGoodsInfo);
        $changeData = array_merge($receiverInfo, $changeData);

        $replaceArguments = RegularDeliveryReplaceCodeFactory::makeRegularDeliveryInfoChangeData($this->mallInfo, $changeData);

        $replaceArguments['subGoodsNm'] = $this->getSubGoodsNm($regularOrderGoodsInfo);

        // SMS 발송
        $this->sendSms($receiverInfo, $replaceArguments, $this->smsType, Code::REGULAR_DELIVERY_INFO_CHANGED);
    }

    /**
     * 상품명 가져오기
     * @param array $regularOrderGoodsInfo
     * @param string $regularGoodsNm
     * @return string
     */
    public function getSubGoodsNm(array $regularOrderGoodsInfo, string $regularGoodsNm = '')
    {
        $applyNoList = [];
        // 단 건의 경우 1차 배열로 넘어와서 applyNo 값 자체를 체크
        if (isset($regularOrderGoodsInfo['applyNo'])) {
            $applyNoList = [$regularOrderGoodsInfo['applyNo']];
        } else { // 여러 건의 경우 N차 배열로 넘어와서 applyNo 값 추출
            $applyNoList = array_column($regularOrderGoodsInfo, 'applyNo');
        }

        // 신청 상품 개수
        $applyNoCnt = count($applyNoList);

        // applyNo 리스트에 따른 추가상품 개수
        $addGoodsCnt = $this->regularOrderAddGoodsRepo->countRegularOrderAddGoodsByApplyNoList($applyNoList);

        $goodsCnt = $applyNoCnt + $addGoodsCnt; // 신청상품 수 + 추가 상품 수

        if (empty($regularGoodsNm)) {
            $regularGoodsNm = $regularOrderGoodsInfo['regularGoodsNm'];
        }

        $regularGoodsNm = mb_substr($regularGoodsNm, 0, 18, 'UTF-8');
        if ($goodsCnt > 1) {
            return $regularGoodsNm . ' 외 ' . ($goodsCnt - 1) . '건';
        }
        return $regularGoodsNm;
    }

    /**
     * 공급사 번호 조회 (applyNo 기준)
     * @param array|int $applyNo
     * @return array|null
     */
    private function getScmNoByApplyNo($applyNo)
    {
        $applyNoList = (array) $applyNo;
        $scmNo = $this->regularOrderDeliveryRepo->findRegularOrderScmNoListByApplyNoList($applyNoList);
        
        return empty($scmNo) ? null : $scmNo;
    }

    /**
     * 배송주기 치환
     * @param array $regularOrderGoodsInfo
     * @return string
     */
    private function replaceDeliveryCycle(array $regularOrderGoodsInfo): string
    {
        if ($regularOrderGoodsInfo['deliveryCycleType'] === 'week') {
            $dayNum = (int)$regularOrderGoodsInfo['deliveryCycleDay'];
            // 요일로 치환
            $dayName = DeliveryCycle::DELIVERY_CYCLE_WEEK_DAY[$dayNum] ?? '';

            return $regularOrderGoodsInfo['deliveryCycle'] . '주/' . $dayName;
        }

        return $regularOrderGoodsInfo['deliveryCycle'] . '개월/' . $regularOrderGoodsInfo['deliveryCycleDay'] . '일';
    }

    /**
     * SMS 발송
     * @param array $receiverInfo
     * @param array $replaceArguments
     * @param string $smsType
     * @param string $smsAutoCodeType
     */
    private function sendSms(array $receiverInfo, array $replaceArguments, string $smsType, string $smsAutoCodeType)
    {
        $this->logger->channel('regularDelivery')->info('정기결제(배송) SMS 발송 START');
        $this->logger->channel('regularDelivery')->info('정기결제(배송) SMS 발송 정보 : ' , [
            'receiverInfo' => $receiverInfo,
            'replaceArguments' => $replaceArguments,
            'smsType' => $smsType,
            'smsAutoCodeType' => $smsAutoCodeType
        ]);

        // cellPhone이 있을 때만 SMS 발송
        if (isset($receiverInfo['cellPhone'])) {
            if (!empty($receiverInfo['memNo']) && empty($replaceArguments['memNo'])) {
                $replaceArguments['memNo'] = $receiverInfo['memNo'];
            }
            // SmsAuto 인스턴스를 새로 생성하여 이전 호출의 상태가 남아있지 않도록 함
            $smsAuto = new SmsAuto();
            $smsAuto->setSmsType($smsType);
            $smsAuto->setSmsAutoCodeType($smsAutoCodeType);
            $smsAuto->setReceiver($receiverInfo);
            $smsAuto->setReplaceArguments($replaceArguments);
            $smsAuto->autoSend();
        } else {
            $this->logger->channel('regularDelivery')->warning(__CLASS__ . ' ' . __METHOD__ . ' 정기결제(배송) SMS 발송 오류 - cellPhone 없음, 수신자 정보 :', [$receiverInfo]);
        }
        $this->logger->channel('regularDelivery')->info('정기결제(배송) SMS 발송 END');
    }

    /**
     * make 함수에 필요한 공통 치환 데이터 구성
     * @param array $regularOrderGoodsInfo
     * @return array
     */
    private function buildReplaceInfo(array $regularOrderGoodsInfo): array
    {
        return [
            'subGoodsNm' => $this->getSubGoodsNm($regularOrderGoodsInfo),
            'deliveryDueDate' => $regularOrderGoodsInfo['deliveryDueDate'] ?? '',
            'deliveryRound' => (int)($regularOrderGoodsInfo['deliveryRound'] ?? 0),
            'cardName' => $regularOrderGoodsInfo['cardName'] ?? '',
            'cardNo' => $regularOrderGoodsInfo['cardNo'] ?? ''
        ];
    }

    /**
     * 총 결제 금액 조회
     * @param array $targets
     * @return int
     */
    private function getTotalPrice(array $targets): int
    {
        $totalPrice = 0;
        
        foreach ($targets as $target) {
            $applyNo = $target['applyNo'];

            // 정기결제가 × 상품 개수
            $regularGoodsPrice = (int)($target['regularGoodsPrice'] ?? 0);
            $goodsCnt = (int)($target['regularGoodsCnt'] ?? 0);
            $totalPrice += $regularGoodsPrice * $goodsCnt;
            
            // 옵션가 × 상품개수
            $optionData = $this->getOptionInfo($applyNo);
            $optionPrice = (int)($optionData['optionPrice'] ?? 0);
            $totalPrice += $optionPrice * $goodsCnt;
            
            // 텍스트옵션가 × 상품개수
            $optionTextPrice = (int)($optionData['optionTextPrice'] ?? 0);
            $totalPrice += $optionTextPrice * $goodsCnt;
            
            // 추가상품가 × 추가상품개수
            $addGoodsInfo = $this->getAddGoodsInfo($applyNo);
            $addGoodsList = $addGoodsInfo ?? [];
            foreach ($addGoodsList as $addGoods) {
                $addGoodsPrice = (int)($addGoods['addGoodsPrice'] ?? 0);
                $addGoodsCnt = (int)($addGoods['addGoodsCnt'] ?? 0);
                $totalPrice += $addGoodsPrice * $addGoodsCnt;
            }
        }
        
        return $totalPrice;
    }

    /**
     * 옵션 정보 조회(일반옵션, 텍스트옵션)
     * @param string $applyNo
     * @return array
     */
    private function getOptionInfo(string $applyNo): array
    {
        $optionData = $this->regularOrderGoodsRepo->findGoodsOptionInfoByApplyNo($applyNo);

        $optionInfo = [];
        $optionPrice = 0;
        if (!empty($optionData['regularGoodsOptionInfo'])) {
            $optionInfo = RegularOrderUtil::parseOptionData(
                $optionData['regularGoodsOptionInfo'],
                'optionName',
                'optionValue',
                $optionData['regularGoodsOptionPrice']
            );

            // 옵션 가격
            $optionPrice = $optionData['regularGoodsOptionPrice'];
        }

        $optionTextInfo = [];
        $optionTextPrice = 0;
        if (!empty($optionData['regularGoodsOptionTextInfo'])) {
            $optionTextInfo = RegularOrderUtil::parseOptionData(
                $optionData['regularGoodsOptionTextInfo'],
                'optionTextName',
                'optionTextValue',
                $optionData['regularGoodsOptionTextPrice']
            );

            // 텍스트 옵션 가격
            $optionTextPrice = $optionData['regularGoodsOptionTextPrice'];
        }

        return [
            'optionInfo' => $optionInfo['summary'],
            'optionTextInfo' => $optionTextInfo['summary'],
            'optionPrice' => $optionPrice,
            'optionTextPrice' => $optionTextPrice
        ];
    }

    /**
     * 추가상품 정보 조회
     * @param string $applyNo
     * @return array
     */
    private function getAddGoodsInfo(string $applyNo): array
    {
        // 추가상품 정보 조회
        $addGoodsInfo = $this->regularOrderAddGoodsRepo->findRegularOrderAddGoodsByApplyNo($applyNo);
        $addGoodsList = [];

        if (!empty($addGoodsInfo)) {
            foreach ($addGoodsInfo as $addGoods) {
                $addGoodsList[] = [
                    'addGoodsNm' => $addGoods['goodsNm'],
                    'addGoodsCnt' => $addGoods['regularAddGoodsCnt'],
                    'addGoodsOptionName' => $addGoods['addGoodsOptionName'],
                    'addGoodsPrice' => $addGoods['regularAddGoodsPrice'],
                    'totalAddGoodsPrice' => $addGoods['regularAddGoodsPrice'] * $addGoods['regularAddGoodsCnt']
                ];
            }
        }

        return $addGoodsList;
    }

    /**
     * 총 배송비 조회
     * @param string $orderNo
     * @return int
     */
    private function getTotalDeliveryCharge(string $orderNo = ''): int
    {
        $totalDeliveryCharge = 0;
        if (!empty($orderNo)) {
            $totalDeliveryCharge = $this->orderRepository->findTotalDeliveryChargeByOrderNo($orderNo);
        }

        return $totalDeliveryCharge;
    }


    /**
     * 신청번호 리스트에 대한 치환 정보 생성
     * @param array $applyInfo
     * @return array
     */
    private function buildReplaceInfoByApplyInfoList(array $applyInfo): array
    {
        $applyInfoCollect = collect($applyInfo);

        // 신청번호 가장 빠른 상품 조회
        $sendApplyNoInfo = $applyInfoCollect
            ->sortBy('applyNo') // 1차 정렬: 신청번호 빠른 순서
            ->first(); // 배열 반환
        
        // 수신자 정보 조회
        $receiverInfo = $this->getApplierInfo($sendApplyNoInfo['applyGroupNo']);
        $applyNoList = $applyInfoCollect->pluck('applyNo')->unique()->toArray();
        $receiverInfo['scmNo'] = $this->getScmNoByApplyNo($applyNoList);

        // 치환 정보 생성
        $replaceInfo = $this->buildReplaceInfo($sendApplyNoInfo);
        // 상품명 넘어온 데이터 기준 ~외 n건 치환 처리를 위하여 재정의
        $replaceInfo['subGoodsNm'] = $this->getSubGoodsNm($applyInfo, $sendApplyNoInfo['regularGoodsNm']);

        return [$receiverInfo, $replaceInfo, $sendApplyNoInfo];
    }
}
