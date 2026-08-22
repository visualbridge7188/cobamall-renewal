<?php
/*
 * Copyright (C) 2025 NHN COMMERCE. - All Rights Reserved
 *
 * Unauthorized copying or redistribution of this file in source and binary forms via any medium
 * is strictly prohibited.
 */

namespace Bundle\Component\RegularDelivery\Notification;

use Component\RegularDelivery\Notification\RegularDeliveryReplaceCodeFactory;
use Illuminate\Database\Eloquent\Collection;
use Component\Mail\MailMimeAuto;
use Origin\Repository\Payment\PgCardInfoRepositoryInterface;
use Framework\Security\Encryptor;
use Origin\DTO\Payment\PgCardDTO;
use Repository\RegularDelivery\RegularOrder\RegularOrderGoodsRepository;
use Util\Order\RegularOrderUtil;
use Repository\RegularDelivery\RegularOrder\RegularOrderAddGoodsRepository;
use Framework\Log\Logger;
use Repository\RegularDelivery\RegularOrder\RegularOrderApplierRepository;
use Repository\Order\OrderGiftRepository;
use Repository\RegularDelivery\RegularGoods\RegularGiftPresentRepository;
use Repository\RegularDelivery\RegularOrder\RegularOrderShippingAddressRepository;
use Repository\Order\OrderInfoRepository;
use Repository\RegularDelivery\RegularOrder\RegularOrderDeliveryLogRepository;
use Origin\Enum\RegularDelivery\RegularGoods\DeliveryCycle;
use Repository\RegularDelivery\RegularOrder\RegularOrderGiftRepository;
use Repository\Delivery\ManageDeliveryCompanyRepository;
use Repository\Order\OrderRepository;
use Repository\RegularDelivery\RegularOrder\RegularOrderGoodsDeliveryLogRepository;

class RegularDeliveryMailSender
{
    /**
     * @var MailMimeAuto
     */
    private $mailMimeAuto;

    /**
     * @var PgCardInfoRepositoryInterface
     */
    private $pgCardInfoRepository;

    /**
     * @var Encryptor
     */
    private $encryptor;

    /**
     * @var RegularOrderGoodsRepository
     */
    private $regularOrderGoodsRepo;

    /**
     * @var RegularOrderAddGoodsRepository
     */
    private $regularOrderAddGoodsRepo;

    /**
     * @var array
     */
    private $mallInfo;

    /**
     * @var Logger
     */
    private $logger;

    /**
     * @var RegularOrderApplierRepository
     */
    private $regularOrderApplierRepo;

    /**
     * @var OrderGiftRepository
     */
    private $orderGiftRepo;

    /**
     * @var RegularGiftPresentRepository
     */
    private $regularGiftPresentRepo;

    /**
     * @var RegularOrderShippingAddressRepository
     */
    private $regularOrderShippingAddressRepo;

    /**
     * @var OrderInfoRepository
     */
    private $orderInfoRepo;

    /**
     * @var RegularOrderDeliveryLogRepository
     */
    private $regularOrderDeliveryLogRepo;

    /**
     * @var RegularOrderGiftRepository
     */
    private $regularOrderGiftRepo;

    /**
     * @var ManageDeliveryCompanyRepository
     */
    private $manageDeliveryCompanyRepo;

    /**
     * @var OrderRepository
     */
    private $orderRepository;


    /**
     * @var RegularOrderGoodsDeliveryLogRepository
     */
    private $regularOrderGoodsDeliveryLogRepo;

    /**
     * @param MailMimeAuto $mailMimeAuto
     * @param PgCardInfoRepositoryInterface $pgCardInfoRepository
     * @param Encryptor $encryptor
     * @param RegularOrderGoodsRepository $regularOrderGoodsRepo
     * @param RegularOrderAddGoodsRepository $regularOrderAddGoodsRepo
     * @param Logger $logger
     * @param RegularOrderApplierRepository $regularOrderApplierRepo
     * @param OrderGiftRepository $orderGiftRepo
     * @param RegularGiftPresentRepository $regularGiftPresentRepo
     * @param RegularOrderShippingAddressRepository $regularOrderShippingAddressRepo
     * @param OrderInfoRepository $orderInfoRepo
     * @param RegularOrderDeliveryLogRepository $regularOrderDeliveryLogRepo
     * @param RegularOrderGiftRepository $regularOrderGiftRepo
     * @param ManageDeliveryCompanyRepository $manageDeliveryCompanyRepo
     */
    public function __construct(
        MailMimeAuto $mailMimeAuto,
        PgCardInfoRepositoryInterface $pgCardInfoRepository,
        Encryptor $encryptor,
        RegularOrderGoodsRepository $regularOrderGoodsRepo,
        RegularOrderAddGoodsRepository $regularOrderAddGoodsRepo,
        Logger $logger,
        RegularOrderApplierRepository $regularOrderApplierRepo,
        OrderGiftRepository $orderGiftRepo,
        RegularGiftPresentRepository $regularGiftPresentRepo,
        RegularOrderShippingAddressRepository $regularOrderShippingAddressRepo,
        OrderInfoRepository $orderInfoRepo,
        RegularOrderDeliveryLogRepository $regularOrderDeliveryLogRepo,
        RegularOrderGiftRepository $regularOrderGiftRepo,
        ManageDeliveryCompanyRepository $manageDeliveryCompanyRepo,
        OrderRepository $orderRepository,
        RegularOrderGoodsDeliveryLogRepository $regularOrderGoodsDeliveryLogRepo
    )
    {
        $this->mailMimeAuto = $mailMimeAuto;
        $this->mallInfo = gd_policy('basic.info');
        $this->pgCardInfoRepository = $pgCardInfoRepository;
        $this->encryptor = $encryptor;
        $this->regularOrderGoodsRepo = $regularOrderGoodsRepo;
        $this->regularOrderAddGoodsRepo = $regularOrderAddGoodsRepo;
        $this->logger = $logger;
        $this->regularOrderApplierRepo = $regularOrderApplierRepo;
        $this->orderGiftRepo = $orderGiftRepo;
        $this->regularGiftPresentRepo = $regularGiftPresentRepo;
        $this->regularOrderShippingAddressRepo = $regularOrderShippingAddressRepo;
        $this->orderInfoRepo = $orderInfoRepo;
        $this->regularOrderDeliveryLogRepo = $regularOrderDeliveryLogRepo;
        $this->regularOrderGiftRepo = $regularOrderGiftRepo;
        $this->manageDeliveryCompanyRepo = $manageDeliveryCompanyRepo;
        $this->orderRepository = $orderRepository;
        $this->regularOrderGoodsDeliveryLogRepo = $regularOrderGoodsDeliveryLogRepo;
    }

    /**
     * 정기배송 일시정지 시 발송 데이터 생성 후 발송
     * @param array $regularOrderGoodsInfo
     */
    public function sendPauseMail(array $regularOrderGoodsInfo)
    {
        $replaceArguments = $this->buildReplaceArgumentsByApplyInfoList($regularOrderGoodsInfo);
        $this->sendMail(MailMimeAuto::REGULAR_DELIVERY_PAUSED, $replaceArguments);
    }

    /**
     * 정기배송 일시정지 해제 시 발송 데이터 생성 후 발송
     * @param array $regularOrderGoodsInfo
     */
    public function sendResumeMail(array $regularOrderGoodsInfo)
    {
        $replaceArguments = $this->buildReplaceArgumentsByApplyInfoList($regularOrderGoodsInfo);
        $this->sendMail(MailMimeAuto::REGULAR_DELIVERY_RESUMED, $replaceArguments);
    }

    /**
     * 정기배송 건너뛰기 시 발송 데이터 생성 후 발송
     * @param array $regularOrderGoodsInfo
     * @param string $skipDeliveryDate
     */
    public function sendSkipDeliveryRoundMail(array $regularOrderGoodsInfo, string $skipDeliveryDate)
    {
        $regularGoodsApplyInfo = $this->regularOrderGoodsRepo->findRegularOrderGoodsInfoByApplyNo($regularOrderGoodsInfo['applyNo']);
        $memNo = $regularOrderGoodsInfo['memNo'];
        $replaceArguments = $this->buildReplaceArguments($regularGoodsApplyInfo, $memNo);

        // 회차 건너뛰기 정보
        $skipDeliveryRoundInfo = RegularDeliveryReplaceCodeFactory::makeSkipDeliveryRoundInfoForMail($regularOrderGoodsInfo['deliveryRound'], $skipDeliveryDate);

        $replaceArguments = array_merge($replaceArguments, $skipDeliveryRoundInfo);

        $this->sendMail(MailMimeAuto::REGULAR_DELIVERY_SKIPPED, $replaceArguments);
    }

    /**
     * 정기배송 해지 시 발송 데이터 생성 후 발송
     * @param array $regularOrderGoodsInfo
     */
    public function sendCancelMail(array $regularOrderGoodsInfo)
    {
        $replaceArguments = $this->buildReplaceArgumentsByApplyInfoList($regularOrderGoodsInfo);
        $this->sendMail(MailMimeAuto::REGULAR_DELIVERY_CANCELED, $replaceArguments);
    }

    /**
     * 정기배송 신청 완료 시 발송 데이터 생성 후 발송
     * @param string $memNo
     * @param array $regularOrderReplaceInfo
     */
    public function sendApplyCompleteMail(string $memNo, array $regularOrderReplaceInfo)
    {
        $replaceArguments = $this->buildReplaceArguments($regularOrderReplaceInfo, $memNo, true);

        // 사은품 정보
        $applyNoList = $this->regularOrderGoodsRepo->findRegularOrderApplyNoListByApplyGroupNo($regularOrderReplaceInfo['applyGroupNo']);
        $giftInfo = $this->getGiftInfo($applyNoList);  
        $replaceArguments = array_merge($replaceArguments, $giftInfo);
        
        $this->sendMail(MailMimeAuto::REGULAR_DELIVERY_APPLY, $replaceArguments);
    }

    /**
     * 정기배송 결제 성공 시 발송 데이터 생성 후 발송
     * @param Collection $groups
     * @param string $memNo
     * @param string $orderNo
     */
    public function sendPaymentCompleteMail(Collection $groups, string $memNo, string $orderNo)
    {
        $orderInfo = $this->buildOrderInfo($groups, $orderNo);
        $replaceOrderInfo = $orderInfo['replaceOrderInfo'];

        // 주문자 정보 조회 및 치환코드 정보 세팅
        $ordererInfo = $this->orderInfoRepo->findOrderInfoByOrderNo($orderNo);

        if (empty($ordererInfo)) {
            $this->logger->channel('regularDelivery')->warning(__METHOD__ . ' 주문자 정보가 없습니다.' , [$orderNo]);
            return;
        }

        $ordererInfo['orderNo'] = $orderNo;
        $ordererInfo['memNm'] = $ordererInfo['orderName'];
        $ordererInfo['applierName'] = $ordererInfo['orderName'];
        $ordererInfo['applierEmail'] = $ordererInfo['orderEmail'];
        
        $paymentCompleteInfo = RegularDeliveryReplaceCodeFactory::makePaymentCompleteDataForMail($ordererInfo, $replaceOrderInfo);

        $basicInfo = RegularDeliveryReplaceCodeFactory::makeBasicInfoForMail($this->mallInfo, $ordererInfo);

        $applyInfo = $this->getApplyInfo($ordererInfo, $memNo, $replaceOrderInfo);

        // 배송정보 조회
        $shippingInfo = $this->regularOrderShippingAddressRepo->findShippingAddressBySno($replaceOrderInfo['shippingAddressSno']);

        if (empty($shippingInfo)) {
            $this->logger->channel('regularDelivery')->warning(__METHOD__ . ' 배송지 정보가 없습니다.' , [$orderNo]);
            return;
        }

        $shippingReplaceInfo = RegularDeliveryReplaceCodeFactory::makeShippingInfoForMail($shippingInfo, $replaceOrderInfo['deliveryRound']);

        // 사은품 정보
        $giftInfo = $this->getGiftInfo($orderInfo['allApplyNos']);  

        $replaceArguments = array_merge($orderInfo['goodsListInfo'], $applyInfo, $shippingReplaceInfo, $basicInfo, $paymentCompleteInfo, $giftInfo);
        $this->sendMail(MailMimeAuto::REGULAR_DELIVERY_ORDER, $replaceArguments);
    }

    /**
     * 정기배송 결제 실패 시 발송 데이터 생성 후 발송
     * @param Collection $groups
     * @param string $memNo
     */
    public function sendPaymentFailMail(Collection $groups, string $memNo)
    {
        $orderInfo = $this->buildOrderInfo($groups);
        
        // 신청자 정보(주문번호 생성 전으로 주문자 정보로 발송 불가)
        $applierInfo = $this->getApplierInfo($orderInfo['replaceOrderInfo']['applyGroupNo']);
        $applierInfo = RegularDeliveryReplaceCodeFactory::makeBasicInfoForMail($this->mallInfo, $applierInfo);
        
        $applyInfo = $this->getApplyInfo($applierInfo, $memNo, $orderInfo['replaceOrderInfo']);

        $replaceArguments = [];
        $replaceArguments = array_merge(
            $replaceArguments,
            $applierInfo,
            $applyInfo,
            $orderInfo['goodsListInfo']
        );

        // 자동 결제 정보
        $autoPaymentInfo = RegularDeliveryReplaceCodeFactory::makeAutoPaymentInfoForMail($orderInfo['replaceOrderInfo']);
        $replaceArguments = array_merge($replaceArguments, $autoPaymentInfo);

        $this->sendMail(MailMimeAuto::REGULAR_PAYMENT_FAILED, $replaceArguments);
    }

    /**
     * 정기배송 주문 생성 예정 시 발송 데이터 생성 후 발송
     * @param Collection $sendOrderGroup
     */
    public function sendPaymentScheduledMail(Collection $sendOrderGroup)
    {
        $this->logger->channel('regularDelivery')->info("정기결제 주문 생성 예정 알림 치환 대상 : ", $sendOrderGroup->toArray());
        
        // 주문 정보 생성
        $orderInfo = $this->buildOrderInfo($sendOrderGroup);

        // 신청자 정보(주문번호 생성 전으로 주문자 정보로 발송 불가)
        $applierInfo = $this->getApplierInfo($orderInfo['replaceOrderInfo']['applyGroupNo']);
        $applierInfo = RegularDeliveryReplaceCodeFactory::makeBasicInfoForMail($this->mallInfo, $applierInfo);
        $applierInfo['subName'] = $applierInfo['memNm'];

        $replaceArguments = [];
        $replaceArguments = array_merge(
            $replaceArguments,
            $applierInfo,
            $orderInfo['goodsListInfo']
        );
        
        // 자동 결제 정보
        $autoPaymentInfo = RegularDeliveryReplaceCodeFactory::makeAutoPaymentInfoForMail($orderInfo['replaceOrderInfo']);
        $replaceArguments = array_merge($replaceArguments, $autoPaymentInfo);

        $this->sendMail(MailMimeAuto::REGULAR_PAYMENT_SCHEDULED, $replaceArguments);
    }

    /**
     * 주문 정보 생성 (결제 예정, 결제 실패, 주문 생성 등에서 공통 사용)
     * @param Collection $sendOrderGroup
     * @param string $orderNo
     * @return array
     */
    private function buildOrderInfo(Collection $sendOrderGroup, string $orderNo = ''): array
    {
        // 1. 주문번호가 동일한 모든 applyNo의 데이터 (subGoodsListInfo 리스트용)
        $allApplyNos = $sendOrderGroup->pluck('applyNo')->toArray();

        // 2. 주문번호 중 배송회차가 가장 높고 신청번호가 가장 빠른 기준의 applyNo 데이터 (치환정보용)
        $sendApplyNoInfo = $sendOrderGroup
            ->sort(function ($order1, $order2) { 
                return $order1['deliveryRound'] == $order2['deliveryRound'] 
                ? $order1['applyNo'] <=> $order2['applyNo'] // 2차 정렬: 신청번호 빠른 순서
                : $order2['deliveryRound'] <=> $order1['deliveryRound']; // 1차 정렬: 배송회차 높은 순서
            })
            ->first();

        if(!$sendApplyNoInfo) {
            $this->logger->channel('regularDelivery')->warning(__METHOD__ . ' 정기결제 주문 생성 예정 알림 발송 대상이 없습니다.' , [$sendOrderGroup->toArray()]);
            return [];
        }

        $regularOrderInfo = $sendApplyNoInfo->toArray();
        $regularOrderInfo['applyGroupNo'] = $sendApplyNoInfo->applyGroupNo;

        // 상품 정보 조회 (모든 applyNo로 subGoodsListInfo 리스트 조회)
        $regularGoodsApplyListInfo = $this->regularOrderGoodsRepo->findRegularOrderGoodsInfoByApplyNoList($allApplyNos);
        
        // 상품 리스트 정보
        $subGoodsListInfo = $this->buildGoodsListInfoByApplyNoList($regularGoodsApplyListInfo, $regularOrderInfo);

        // 가격 정보 생성
        $priceInfo = $this->buildPriceInfo($subGoodsListInfo, $orderNo);

        // 주문 생성 기준의 상품 정보 치환코드 데이터 생성
        $goodsListInfo = RegularDeliveryReplaceCodeFactory::makeGoodsListInfoByOrderForMail($subGoodsListInfo, $priceInfo);

        return [
            'replaceOrderInfo' => $regularOrderInfo,
            'subGoodsListInfo' => $subGoodsListInfo,
            'priceInfo' => $priceInfo,
            'goodsListInfo' => $goodsListInfo,
            'allApplyNos' => $allApplyNos
        ];
    }

    /**
     * 가격 정보 생성
     *  
     * @param array $subGoodsListInfo
     * @param string $orderNo
     * @return array
     */
    private function buildPriceInfo(array $subGoodsListInfo, string $orderNo = ''): array
    {
        $totalGoodsPrice = array_sum(array_column($subGoodsListInfo, 'totalGoodsPrice'));

        $totalDeliveryCharge = 0;
        if (!empty($orderNo)) {
            $totalDeliveryCharge = $this->orderRepository->findTotalDeliveryChargeByOrderNo($orderNo);
        }
        
        return [
            'totalGoodsPrice' => $totalGoodsPrice,
            'totalDeliveryCharge' => $totalDeliveryCharge,
            'settlePrice' => $totalGoodsPrice + $totalDeliveryCharge
        ];
    }

    /**
     * 정기배송 안내 메일 발송
     * @param array $orderData
     */
    public function sendRegularDeliveryNoticeMail(array $orderData)
    {
        $snoList = array_column($orderData['goods'], 'sno');

        // 배송중 처리 주문상품 중 가장 높은 배송회차, 배송예정일 조회
        $highestDeliveryRoundGoodsInfo = $this->regularOrderDeliveryLogRepo->findHighestDeliveryRoundInfoByOrderGoodsSnoList($snoList, $orderData['orderNo']);
        
        // 주문자 정보
        $orderInfo = $this->orderInfoRepo->findOrderInfoByOrderNo($orderData['orderNo']);
        $basicInfo = RegularDeliveryReplaceCodeFactory::makeBasicInfoForMail($this->mallInfo, $orderInfo);
        
        // 주문 및 주문자 정보
        $orderData = array_merge($orderInfo, $orderData, $highestDeliveryRoundGoodsInfo);
        // goods 배열에 직접 deliveryName 추가
        if (isset($orderData['goods'])) {
            foreach ($orderData['goods'] as $index => $goods) {
                if (isset($goods['invoiceCompanySno'])) {
                    $deliveryCompanyInfo = $this->manageDeliveryCompanyRepo->findManageDeliveryCompanyBySno((int)$goods['invoiceCompanySno']);

                    // 인덱스로 직접 접근하여 수정
                    $orderData['goods'][$index]['deliveryName'] = $deliveryCompanyInfo['companyName'] ?? '';
                }
            }
        }

        // 사은품 정보
        $applyNoList = $this->regularOrderGoodsDeliveryLogRepo->findApplyNoByOrderGoodsSnoList($snoList);
        $giftInfo = $this->getGiftInfo($applyNoList);

        $replaceArguments = RegularDeliveryReplaceCodeFactory::makeRegularDeliveryNoticeMail($this->mallInfo, $orderData, $highestDeliveryRoundGoodsInfo);

        $replaceArguments = array_merge($basicInfo, $replaceArguments, $giftInfo);

        $this->sendMail(MailMimeAuto::REGULAR_DELIVERY_NOTICE, $replaceArguments);
    }

    /**
     * 정기배송 일정 변경 시 발송 데이터 생성 후 발송
     * @param array $changeData
     * @param array $regularOrderGoodsInfo
     */
    public function sendRegularDeliveryInfoChangeMail(array $changeData, array $regularOrderGoodsInfo)
    {
        $regularOrderGoodsInfo = array_merge($changeData, $regularOrderGoodsInfo);
        $memNo = $regularOrderGoodsInfo['memNo'];
        $replaceArguments = $this->buildReplaceArguments($regularOrderGoodsInfo, $memNo);
        $replaceArguments = array_merge(
            $replaceArguments, 
            RegularDeliveryReplaceCodeFactory::makeRegularDeliveryInfoChangeMail($changeData)
        );

        $this->sendMail(MailMimeAuto::REGULAR_DELIVERY_INFO_CHANGED, $replaceArguments);
    }

    /**
     * 자동 메일 발송
     * @param string $mailType
     * @param array $replaceArguments
     */
    private function sendMail(string $mailType, array $replaceArguments)
    {
        $this->logger->channel('regularDelivery')->info(sprintf('정기배송 메일 발송 [%s] 정보 : ', $mailType), $replaceArguments);
        $this->mailMimeAuto->init($mailType, $replaceArguments)->autoSend();
    }

    /**
     * 신청내역 데이터 세팅
     * @param array $applierInfo
     * @param string $memNo
     * @param array $regularOrderGoodsInfo
     * @return array
     */
    public function getApplyInfo(array $applierInfo, string $memNo, array $regularOrderGoodsInfo): array
    {
        // 카드 정보 조회
        $cardInfo = [];
        if (!empty($memNo) && !empty($regularOrderGoodsInfo['cardNo'])) {
            $cardInfo = $this->pgCardInfoRepository->findUsedCardByCardNoAndMemNo($regularOrderGoodsInfo['cardNo'], $memNo);
            $cardInfo = $this->getDeliveryCardInfo($cardInfo);
        }

        return RegularDeliveryReplaceCodeFactory::makeApplyInfoForMail($applierInfo, $regularOrderGoodsInfo['regDt'], $cardInfo);
    }

    /**
     * 발송 대상 정보 + 신청그룹 전체 상품 리스트 관련 치환 정보
     * @param array $regularOrderGoodsInfo
     * @param string $memNo
     * @param bool $includeGoodsListInfo
     * @return array
     */
    private function buildReplaceArguments(array $regularOrderGoodsInfo, string $memNo = '', bool $includeGoodsListInfo = false): array
    {
        // 신청 그룹 번호가 없는 경우 예외 처리
        if (empty($regularOrderGoodsInfo['applyGroupNo'])) {
            $this->logger->channel('regularDelivery')->warning(__METHOD__ . ' 치환할 신청 그룹 번호가 없습니다.' , [$regularOrderGoodsInfo]);
            return [];
        }

        // 신청자 정보 조회
        $applierInfo = $this->getApplierInfo($regularOrderGoodsInfo['applyGroupNo']);

        // 회원 정보 조회 - 결제 예정 안내 알림에서는 불필요
        $memNo = $memNo ? $memNo : $regularOrderGoodsInfo['memNo'];

        // 신청번호 리스트 가져오기
        $applyNos = $this->regularOrderGoodsRepo->findRegularOrderGoodsInfoByApplyGroupNoList([$regularOrderGoodsInfo['applyGroupNo']]);
        $applyNoList = array_column($applyNos, 'applyNo');
        
        $basicInfo = RegularDeliveryReplaceCodeFactory::makeBasicInfoForMail($this->mallInfo, $applierInfo);
        $applyInfo = $this->getApplyInfo($applierInfo, $memNo, $regularOrderGoodsInfo);
        $goodsInfo = $this->getGoodsInfo($regularOrderGoodsInfo);
        
        $replaceArguments = array_merge($basicInfo, $applyInfo, $goodsInfo);
        
        // 신청번호 리스트 기반 템플릿 노출용 상품 리스트 정보 병합
        if ($includeGoodsListInfo) {
            // 신청그룹번호 리스트가 존재하는 경우 - 결제 완료 안내 메일
            if (!empty($regularOrderGoodsInfo['applyGroupNoList'])) {
                $applyGroupNoList = $regularOrderGoodsInfo['applyGroupNoList'];
                $regularGoodsApplyListInfo = $this->regularOrderGoodsRepo->findRegularOrderGoodsInfoByApplyGroupNoList($applyGroupNoList);
            } else {
                $regularGoodsApplyListInfo = $this->regularOrderGoodsRepo->findRegularOrderGoodsInfoByApplyNoList($applyNoList);
            }
            
            // 상품 리스트 정보
            $subGoodsListInfo = $this->buildGoodsListInfoByApplyNoList($regularGoodsApplyListInfo);

            // 총 상품 가격
            $totalGoodsPrice = array_sum(array_column($subGoodsListInfo, 'totalGoodsPrice'));
            $totalDeliveryCharge = $subGoodsListInfo[0]['totalDeliveryCharge'] ?? 0;
            $replaceArguments = array_merge(
                $replaceArguments,
                RegularDeliveryReplaceCodeFactory::makeGoodsListInfoForMail($subGoodsListInfo),
                [
                    'totalGoodsPrice' => $totalGoodsPrice,
                    'totalDeliveryCharge' => $totalDeliveryCharge,
                    'settlePrice' => $totalGoodsPrice + $totalDeliveryCharge
                ]
            );
        }

        return $replaceArguments;
    }

    /**
     * 정기결제 카드정보 조회
     * @param array $cardInfo
     * @return array
     */
    public function getDeliveryCardInfo(array $cardInfo): array
    {
        // 카드 정보가 없는 경우
        if (empty($cardInfo) || !isset($cardInfo['cardNo'])) {
            return [];
        }

        // 암호화된 카드번호 복호화(카드 뒷번호 4자리)
        $decryptCardNo = $this->encryptor->decrypt($cardInfo['cardNo']);
        $cardInfo['decryptCardNo'] = $decryptCardNo;

        // DTO 변환 및 복호화된 카드번호 추가
        return array_merge(
            (new PgCardDTO($cardInfo))->toArray(),
            ['decryptCardNo' => $decryptCardNo]
        );
    }

    /**
     * 정기결제 상품정보 조회
     * @param array $regularOrderGoodsInfo
     * @return array
     */
    public function getGoodsInfo(array $regularOrderGoodsInfo): array
    {
        // 배송주기 데이터 세팅
        if ($regularOrderGoodsInfo['deliveryCycleType'] == 'week') {
            $regularOrderGoodsInfo['deliveryCycle'] = $regularOrderGoodsInfo['deliveryCycle'] . '주';
            $regularOrderGoodsInfo['deliveryCycleDay'] = DeliveryCycle::DELIVERY_CYCLE_WEEK_DAY[$regularOrderGoodsInfo['deliveryCycleDay']] . '요일';
        } else {
            $regularOrderGoodsInfo['deliveryCycle'] = $regularOrderGoodsInfo['deliveryCycle'] . '개월';
            $regularOrderGoodsInfo['deliveryCycleDay'] = $regularOrderGoodsInfo['deliveryCycleDay'] . '일';
        }
        
        // 옵션 정보 조회
        $optionInfo = $this->getOptionInfo($regularOrderGoodsInfo['applyNo']);
        
        // 추가상품 정보 조회
        $addGoodsData = $this->getAddGoodsInfo($regularOrderGoodsInfo['applyNo']);
        $addGoodsList = $addGoodsData['addGoodsInfo'] ?? [];

        // 금액 계산
        $goodsCnt = (int)($regularOrderGoodsInfo['regularGoodsCnt'] ?? 0);
        $regularGoodsPrice = (int)($regularOrderGoodsInfo['regularGoodsPrice'] ?? 0);
        $totalRegularGoodsPrice = $regularGoodsPrice * $goodsCnt;

        $optionPrice = (int)($optionInfo['optionPrice'] ?? 0);
        $optionTextPrice = (int)($optionInfo['optionTextPrice'] ?? 0);
        $totalOptionPrice = ($optionPrice + $optionTextPrice) * $goodsCnt;

        // 상품 가격(정기결제가 + 옵션가 + 텍스트옵션가)
        $regularOrderGoodsInfo['regularGoodsPrice'] = $regularGoodsPrice + $optionPrice + $optionTextPrice;

        $totalAddGoodsPrice = array_sum(array_column($addGoodsList, 'totalAddGoodsPrice'));

        // 신청번호 별 상품 총 가격
        $totalGoodsPrice = $totalRegularGoodsPrice + $totalOptionPrice + $totalAddGoodsPrice;

        // 총 상품 가격
        $regularOrderGoodsInfo['totalGoodsPrice'] = $totalGoodsPrice;
        
        // rowSpan 계산
        $regularOrderGoodsInfo['rowSpan'] = count($addGoodsData['addGoodsInfo']) + 1;
        
        $regularOrderGoodsInfo = array_merge($regularOrderGoodsInfo, $optionInfo, $addGoodsData);

        return RegularDeliveryReplaceCodeFactory::makeGoodsInfoForMail($regularOrderGoodsInfo);
    }

    /**
     * 옵션 정보 조회(일반옵션, 텍스트옵션)
     * @param string $applyNo
     * @return array
     */
    public function getOptionInfo(string $applyNo): array
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
            'optionTextPrice' => $optionTextPrice,
            'optionTotalPrice' => $optionPrice + $optionTextPrice
        ];
    }

    /**
     * 추가상품 정보 조회
     * @param string $applyNo
     * @return array
     */
    public function getAddGoodsInfo(string $applyNo): array
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

        return ['addGoodsInfo' => $addGoodsList];
    }

    /**
     * 신청자 정보 조회
     * @param string $applyGroupNo
     * @param string|null $orderNo
     * @return array
     */
    public function getApplierInfo(string $applyGroupNo, $orderNo = null): array
    {
        $applierInfo = $this->regularOrderApplierRepo->findApplierInfoByApplyGroupNo($applyGroupNo);
        // 발송 정보용 데이터 세팅
        $applierInfo['memNm'] = $applierInfo['applierName'];
        $applierInfo['email'] = $applierInfo['applierEmail'];

        // 주문자 데이터 세팅
        if ($orderNo) {
            $applierInfo['orderNo'] = $orderNo;
            $applierInfo['orderName'] = $applierInfo['applierName'];
        }

        return $applierInfo;
    }

    /**
     * 사은품 정보 조회
     * @param array $applyNoList
     * @return array
     */
    public function getGiftInfo(array $applyNoList): array
    {
        $applyGiftInfo = $this->regularOrderGiftRepo->findGiftInfoByApplyNoList($applyNoList);
        return RegularDeliveryReplaceCodeFactory::makeApplyGiftInfoForMail($applyGiftInfo);
    }

    /**
     * 신청번호 리스트에 대한 템플릿 노출용 상품/옵션/금액/주기 정보를 구성
     *
     * @param array $regularGoodsApplyListInfo
     * @param array $replaceOrderGroup
     * @return array
     */
    private function buildGoodsListInfoByApplyNoList(array $regularGoodsApplyListInfo, array $replaceOrderGroup = []): array
    {
        if (empty($regularGoodsApplyListInfo)) {
            return [];
        }

        return array_map(function (array $row) use ($replaceOrderGroup) {
            // 배송주기/요일 포맷
            if ($row['deliveryCycleType'] === 'week') {
                $deliveryCycle = $row['deliveryCycle'] . '주';
                $deliveryCycleDay = DeliveryCycle::DELIVERY_CYCLE_WEEK_DAY[$row['deliveryCycleDay']] ?? '';
            } else {
                $deliveryCycle = $row['deliveryCycle'] . '개월';
                $deliveryCycleDay = $row['deliveryCycleDay'] . '일';
            }

            // 옵션/텍스트옵션 정보
            $optionData = $this->getOptionInfo($row['applyNo']);

            // 추가상품 정보
            $addGoodsInfo = $this->getAddGoodsInfo($row['applyNo']);
            $addGoodsList = $addGoodsInfo['addGoodsInfo'] ?? [];

            // 금액 계산
            $goodsCnt = (int)($row['regularGoodsCnt'] ?? 0);
            $regularGoodsPrice = (int)($row['regularGoodsPrice'] ?? 0);
            $totalRegularGoodsPrice = $regularGoodsPrice * $goodsCnt;

            $optionPrice = (int)($optionData['optionPrice'] ?? 0);
            $optionTextPrice = (int)($optionData['optionTextPrice'] ?? 0);
            $totalOptionPrice = ($optionPrice + $optionTextPrice) * $goodsCnt;

            $totalAddGoodsPrice = array_sum(array_column($addGoodsList, 'totalAddGoodsPrice'));
            
            // 신청번호 별 상품 총 가격
            $totalGoodsPrice = $totalRegularGoodsPrice + $totalOptionPrice + $totalAddGoodsPrice;  

            // 총 결제 금액
            $totalPrice = (int)($row['totalPrice'] ?? 0);

            // body_REGULAR_*.php 에서 상품 행 병합을 위한 행 수 계산
            $rowSpan = count($addGoodsList) + 1;

            // 배송예정일 세팅
            if (empty($replaceOrderGroup['deliveryDueDate'])) {
                $deliveryDueDate = $row['deliveryDueDate'];
            } else {
                $deliveryDueDate = $replaceOrderGroup['deliveryDueDate'];
            }

            return [
                'goodsNm' => $row['regularGoodsNm'] ?? '',
                'optionInfo' => $optionData['optionInfo'] ?? '',
                'optionTextInfo' => $optionData['optionTextInfo'] ?? '',
                'addGoodsInfo' => $addGoodsList,
                'goodsCnt' => $goodsCnt,
                'regularGoodsPrice' => $regularGoodsPrice + $optionPrice + $optionTextPrice,
                'totalGoodsPrice' => $totalGoodsPrice,
                'settlePrice' => $totalPrice,
                'deliveryCycle' => $deliveryCycle,
                'deliveryCycleDay' => $deliveryCycleDay,
                'deliveryDueDate' => $deliveryDueDate,
                'rowSpan' => $rowSpan
            ];
        }, $regularGoodsApplyListInfo);
    }

    /**
     * 발송 대상 정보 + 신청번호 기준 상품 리스트 관련 치환 정보
     * @param array $regularOrderGoodsInfo
     * @return array
     */
    private function buildReplaceArgumentsByApplyInfoList(array $regularOrderGoodsInfo): array
    {
        // 신청번호 가장 빠른 상품 조회
        $sendApplyNoInfo = collect($regularOrderGoodsInfo)
            ->sortBy('applyNo')             // 1차 정렬: 신청번호 빠른 순서
            ->first();  // 배열 반환
        
        // 신청자 정보 조회
        $applierInfo = $this->getApplierInfo($sendApplyNoInfo['applyGroupNo']);

        $basicInfo = RegularDeliveryReplaceCodeFactory::makeBasicInfoForMail($this->mallInfo, $applierInfo);
        $applyInfo = $this->getApplyInfo($applierInfo, $sendApplyNoInfo['memNo'], $sendApplyNoInfo);
        $subGoodsListInfo = $this->buildGoodsListInfoByApplyNoList($regularOrderGoodsInfo);
        $goodsListInfo = RegularDeliveryReplaceCodeFactory::makeGoodsListInfoForMail($subGoodsListInfo);
        
        return array_merge($basicInfo, $applyInfo, $goodsListInfo);
    }
}
