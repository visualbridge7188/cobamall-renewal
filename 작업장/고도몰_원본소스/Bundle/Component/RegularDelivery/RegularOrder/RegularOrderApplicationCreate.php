<?php
/*
 * Copyright (C) 2025 NHN COMMERCE. - All Rights Reserved
 *
 * Unauthorized copying or redistribution of this file in source and binary forms via any medium
 * is strictly prohibited.
 */

namespace Bundle\Component\RegularDelivery\RegularOrder;

use Component\RegularDelivery\Exception\RegularOrderGoodsDiscountPriceException;
use Component\RegularDelivery\Exception\RegularOrderGoodsChangeException;
use DTO\RegularDelivery\RegularOrder\RegularOrderAddGoodsCreateDTO;
use DTO\RegularDelivery\RegularOrder\RegularOrderApplierCreateDTO;
use DTO\RegularDelivery\RegularOrder\RegularOrderCreateDTO;
use DTO\RegularDelivery\RegularOrder\RegularOrderDeliveryCreateDTO;
use DTO\RegularDelivery\RegularOrder\RegularOrderDeliveryLogDTO;
use DTO\RegularDelivery\RegularOrder\RegularOrderDTO;
use DTO\RegularDelivery\RegularOrder\RegularOrderGiftCreateDTO;
use DTO\RegularDelivery\RegularOrder\RegularOrderGoodsCreateDTO;
use DTO\RegularDelivery\RegularOrder\RegularOrderGoodsOptionDTO;
use DTO\RegularDelivery\RegularOrder\RegularOrderGoodsOptionTextDTO;
use DTO\RegularDelivery\RegularOrder\RegularOrderLogDTO;
use DTO\RegularDelivery\RegularOrder\RegularOrderStatusLogDTO;
use Framework\Log\Logger;
use Illuminate\Database\Capsule\Manager;
use Origin\Enum\RegularDelivery\RegularOrder\RegularOrderLogActionType;
use Origin\Enum\RegularDelivery\RegularOrder\RegularOrderStatus;
use Origin\Model\Order\Cart;
use Repository\Goods\AddGoodsRepository;
use Repository\Goods\GoodsOptionRepository;
use Repository\Goods\GoodsOptionTextRepository;
use Repository\Order\RegularOrderCartRepository;
use Repository\RegularDelivery\RegularGoods\RegularGoodsRepository;
use Repository\RegularDelivery\RegularOrder\RegularOrderAddGoodsRepository;
use Repository\RegularDelivery\RegularOrder\RegularOrderApplierRepository;
use Repository\RegularDelivery\RegularOrder\RegularOrderDeliveryLogRepository;
use Repository\RegularDelivery\RegularOrder\RegularOrderDeliveryRepository;
use Repository\RegularDelivery\RegularOrder\RegularOrderGiftRepository;
use Repository\RegularDelivery\RegularOrder\RegularOrderGoodsOptionRepository;
use Repository\RegularDelivery\RegularOrder\RegularOrderGoodsOptionTextRepository;
use Repository\RegularDelivery\RegularOrder\RegularOrderGoodsRepository;
use Repository\RegularDelivery\RegularOrder\RegularOrderLogRepository;
use Repository\RegularDelivery\RegularOrder\RegularOrderRepository;
use Repository\RegularDelivery\RegularOrder\RegularOrderStatusLogRepository;
use Repository\Scm\ScmDeliveryBasicRepository;
use Util\Order\RegularOrderUtil;
use Component\RegularDelivery\Notification\RegularDeliveryNotificationSender;
use Component\RegularDelivery\RegularGoods\RegularGiftSelectCntValidator;

/**
 * 정기 결제 신청서 생성
 */
class RegularOrderApplicationCreate
{
    const FIRST_DELIVERY_ROUND = 1;

    /**
     * @var RegularOrderRepository
     */
    private $regularOrderRepository;
    /**
     * @var RegularOrderGoodsRepository
     */
    private $regularOrderGoodsRepository;
    /**
     * @var RegularOrderApplierRepository
     */
    private $regularOrderApplierRepository;
    /**
     * @var RegularOrderGiftRepository
     */
    private $regularOrderGiftRepository;
    /**
     * @var RegularOrderLogRepository
     */
    private $regularOrderLogRepository;
    /**
     * @var RegularOrderCartRepository
     */
    private $regularOrderCartRepository;
    /**
     * @var RegularGoodsRepository
     */
    private $regularGoodsRepository;
    /**
     * @var RegularOrderAddGoodsRepository
     */
    private $regularOrderAddGoodsRepository;
    /**
     * @var RegularOrderStatusLogRepository
     */
    private $regularOrderStatusLogRepository;
    /**
     * @var RegularOrderDeliveryLogRepository
     */
    private $regularOrderDeliveryLogRepository;
    /**
     * @var RegularOrderDeliveryRepository
     */
    private $regularOrderDeliveryRepository;
    /**
     * @var ScmDeliveryBasicRepository
     */
    private $scmDeliveryBasicRepository;
    /**
     * @var RegularOrderGoodsOptionRepository
     */
    private $regularOrderGoodsOptionRepository;

    /**
     * @var RegularOrderGoodsOptionTextRepository
     */
    private $regularOrderGoodsOptionTextRepository;
    /**
     * @var GoodsOptionRepository
     */
    private $goodsOptionRepository;
    /**
     * @var GoodsOptionTextRepository
     */
    private $goodsOptionTextRepository;
    /**
     * @var AddGoodsRepository
     */
    private $addGoodsRepository;
    /**
     * @var Manager
     */
    private $dbManager;
    /**
     * @var Logger
     */
    private $logger;

    private $totalPrice = 0;

    /**
     * @var RegularDeliveryNotificationSender
     */
    private $notificationSender;

    /**
     * @var RegularGiftSelectCntValidator
     */
    private $regularGiftSelectCntValidator;

    public function __construct(
        RegularOrderRepository $regularOrderRepository,
        RegularOrderGoodsRepository $regularOrderGoodsRepository,
        RegularOrderApplierRepository $regularOrderApplierRepository,
        RegularOrderGiftRepository $regularOrderGiftRepository,
        RegularOrderLogRepository $regularOrderLogRepository,
        RegularOrderCartRepository $regularOrderCartRepository,
        RegularGoodsRepository $regularGoodsRepository,
        RegularOrderAddGoodsRepository $regularOrderAddGoodsRepository,
        RegularOrderStatusLogRepository $regularOrderStatusLogRepository,
        RegularOrderDeliveryLogRepository $regularOrderDeliveryLogRepository,
        RegularOrderDeliveryRepository $regularOrderDeliveryRepository,
        ScmDeliveryBasicRepository $scmDeliveryBasicRepository,
        RegularOrderGoodsOptionRepository $regularOrderGoodsOptionRepository,
        RegularOrderGoodsOptionTextRepository $regularOrderGoodsOptionTextRepository,
        GoodsOptionRepository $goodsOptionRepository,
        GoodsOptionTextRepository $goodsOptionTextRepository,
        AddGoodsRepository $addGoodsRepository,
        Manager $dbManager,
        Logger $logger,
        RegularDeliveryNotificationSender $notificationSender,
        RegularGiftSelectCntValidator $regularGiftSelectCntValidator
    )
    {
        $this->regularOrderRepository = $regularOrderRepository;
        $this->regularOrderGoodsRepository = $regularOrderGoodsRepository;
        $this->regularOrderApplierRepository = $regularOrderApplierRepository;
        $this->regularOrderGiftRepository = $regularOrderGiftRepository;
        $this->regularOrderLogRepository = $regularOrderLogRepository;
        $this->regularOrderCartRepository = $regularOrderCartRepository;
        $this->regularGoodsRepository = $regularGoodsRepository;
        $this->regularOrderAddGoodsRepository = $regularOrderAddGoodsRepository;
        $this->regularOrderStatusLogRepository = $regularOrderStatusLogRepository;
        $this->regularOrderDeliveryLogRepository = $regularOrderDeliveryLogRepository;
        $this->regularOrderDeliveryRepository = $regularOrderDeliveryRepository;
        $this->scmDeliveryBasicRepository = $scmDeliveryBasicRepository;
        $this->regularOrderGoodsOptionRepository = $regularOrderGoodsOptionRepository;
        $this->regularOrderGoodsOptionTextRepository = $regularOrderGoodsOptionTextRepository;
        $this->goodsOptionRepository = $goodsOptionRepository;
        $this->goodsOptionTextRepository = $goodsOptionTextRepository;
        $this->addGoodsRepository = $addGoodsRepository;
        $this->dbManager = $dbManager;
        $this->logger = $logger;
        $this->totalPrice = 0; // 신청서 총 금액 ((상품가 + 옵션가 + 텍스트옵션가) * 수량) * 주문 상품
        $this->notificationSender = $notificationSender;
        $this->regularGiftSelectCntValidator = $regularGiftSelectCntValidator;
    }

    /**
     * 신청서 생성
     * @param RegularOrderDTO $regularOrderDTO
     * @return string
     * @throws \Throwableㅊ
     */
    public function create(RegularOrderDTO $regularOrderDTO): string
    {
        $applyGroupNo = $this->generateApplyGroupNo();
        $cartSnoList = $regularOrderDTO->getCartSno();
        $memNo = $regularOrderDTO->getMemNo();

        $this->dbManager->getConnection()->beginTransaction();
        try {
            // 1. es_regularOrder insert
            $regularOrderCreateDTO = new RegularOrderCreateDTO($applyGroupNo, $regularOrderDTO);
            $this->regularOrderRepository->insertRegularOrder($regularOrderCreateDTO);
            foreach ($cartSnoList as $cartSno) {
                // 2. es_regularOrderGoods insert
                $cartInfo = $this->getCartInfo($cartSno, $memNo);
                $goodsInfo = $this->getOriginGoodsInfo($regularOrderDTO, $cartInfo, $cartSno);
                $deliveryInfo = $this->getDeliveryInfo($regularOrderDTO, $cartInfo['regularGoodsNo'], $cartInfo['deliveryInfo']); // 배송 관련 필요 정보 조회
                $regularGoodsPrice = $this->getRegularGoodsPrice($regularOrderDTO, $cartSno, $cartInfo); // 정기 상품 가격 정보 조회
                $discountPrice = max(0, $goodsInfo['goodsPrice'] - $regularGoodsPrice); // 정기 결제 상품의 할인 가격

                // 신청 정보 취합
                $regularOrderGoodsInfo = array_merge($cartInfo, $goodsInfo, $deliveryInfo, [
                    'regularGoodsPrice' => $regularGoodsPrice,
                    'originGoodsPrice' => $goodsInfo['goodsPrice'],
                    'discountPrice' => $discountPrice,
                    'cardNo' => $regularOrderDTO->getEncryptCardNo(),
                    'regDt' => $regularOrderDTO->getRegDt()
                ]);
                $this->logger->channel('regularOrder')->info(__CLASS__ .' '. __METHOD__ . ' Regular Order Goods INFO : ', $regularOrderGoodsInfo);

                $regularOrderGoodsCreateDTO = new RegularOrderGoodsCreateDTO($applyGroupNo, $regularOrderGoodsInfo);
                $applyNo = $this->regularOrderGoodsRepository->insertRegularOrderGoods($regularOrderGoodsCreateDTO);

                // 3. es_regularOrderDelivery insert
                $regularOrderDeliveryDto = new RegularOrderDeliveryCreateDTO($applyNo, $deliveryInfo['deliverySno'], $deliveryInfo['deliveryPolicy'], $deliveryInfo['deliveryCharge'], $deliveryInfo['deliveryArea']);
                $this->regularOrderDeliveryRepository->insertRegularOrderDelivery($regularOrderDeliveryDto);

                // 4. es_regularAddGoods insert
                $this->insertRegularOrderAddGoods($applyNo, $regularOrderDTO, $cartInfo, $cartSno);

                // 5. es_regularOrderGoodsOption insert
                $this->insertRegularOrderGoodsOption($applyNo, $cartInfo, $regularOrderDTO, $cartSno);

                // 6. es_regularOrderGoodsOptionText insert
                $this->insertRegularOrderGoodsOptionText($applyNo, $cartInfo, $regularOrderDTO, $cartSno);

                // 7. es_regularOrderDeliverLog insert
                $this->insertRegularOrderDeliveryLog($applyNo, $deliveryInfo);

                // 8. es_regularOrderLog insert
                $this->insertRegularOrderLog($applyNo, $memNo);

                // 9. es_regularOrderStatusLog insert
                $this->insertRegularOrderStatusLog($applyNo, $memNo);

                // 10. es_regularOrderGift insert
                $this->insertRegularOrderGift($applyNo, $cartSno, $regularOrderDTO);
            }

            // 11. es_regularOrderApplier insert
            $this->insertRegularOrderApplier($applyGroupNo, $regularOrderDTO);

            // 12. update total goods price & deliver price
            $this->regularOrderRepository->updateTotalPriceAndDeliveryCharge($applyGroupNo, $this->totalPrice, $regularOrderDTO->getTotalDeliveryCharge());

            $this->dbManager->getConnection()->commit();
        } catch (\Throwable $e) {
            $this->dbManager->getConnection()->rollBack();
            $this->logger->channel('regularOrder')->warning(__CLASS__ .' '. __METHOD__. ' 신청서 저장 오류 ', [$e->getMessage()]);
            throw $e;
        }

        // 정기배송 신청 완료 알림 발송
        $this->notificationSender->sendApplyCompleteNotification($memNo, $applyGroupNo);

        return $applyGroupNo;
    }

    /**
     * 신청그룹번호 생성
     * @return string
     */
    private function generateApplyGroupNo(): string
    {
        // 0 ~ 999 마이크로초 중 랜덤으로 sleep 처리 (동일 시간에 들어온 경우 중복을 막기 위해서.)
        usleep(random_int(0, 999));

        $today = date('YmdHi');
        $tmpNo = sprintf('%04d', round(microtime() * 10000));
        return $today . $tmpNo;
    }

    /**
     * 카트에 담긴 상품 정보 조회
     * @param string $cartSno
     * @param int $memNo
     * @return array
     * @throws \Exception
     */
    private function getCartInfo(string $cartSno, int $memNo): array
    {
        $cartInfo = $this->regularOrderCartRepository->findCartInfoBySnoAndMemNo((int) $cartSno, $memNo);
        if (empty($cartInfo)) {
            throw new \Exception(message: '존재하지 않는 카트 번호입니다: ' . $cartSno);
        }

        return [
            'regularGoodsNo' => $cartInfo['regularGoodsNo'], // 정기결제 주문은 goodsNo 가 정기결제 상품번호
            'goodsCnt' => $cartInfo['goodsCnt'], // 상품 수
            'optionSno' => $cartInfo['optionSno'], // 옵션 번호
            'optionTextInfo' => $cartInfo['optionText'], // 옵션텍스트 정보
            'addGoodsInfo' => [
                'addGoodsNo' => $cartInfo['addGoodsNo'],
                'addGoodsCnt' => $cartInfo['addGoodsCnt']
            ],
            'deliveryInfo' => [
                'deliveryCycleType' => $cartInfo['deliveryCycleType'],
                'deliveryCycle' => $cartInfo['deliveryCycle'],
                'deliveryCycleDay' => $cartInfo['deliveryCycleDay'],
                'maxDeliveryRound' => $cartInfo['maxDeliveryRound']
            ],
        ];
    }

    /**
     * 원본 상품 조회
     * 여기서 상품에 대한 유효성 검사를 진행한다.
     *
     * @param RegularOrderDTO $regularOrderDTO
     * @param array $cartInfo
     * @param int $cartSno
     * @return array
     */
    private function getOriginGoodsInfo(RegularOrderDTO $regularOrderDTO, array $cartInfo, int $cartSno): array
    {
        $goodsData = $this->regularGoodsRepository->findRegularGoodsDataByRegularGoodsNo($cartInfo['regularGoodsNo']);
        $deliveryCycleType = $goodsData['deliveryCycleType'];
        $cycleData = [
            'monthCycle' => [],
            'weekCycle' => [],
            'weekDayCycle' => [],
        ];
        if ($deliveryCycleType !== 'all') {
            $regularGoodsCycle = $this->regularGoodsRepository->findRegularGoodsDeliveryInfoByRegularGoodsNo($cartInfo['regularGoodsNo']);
            foreach ($regularGoodsCycle as $cycle) {
                if ($deliveryCycleType === 'month') {
                    $cycleData['monthCycle'][] = $cycle['monthCycle'];
                }
                if ($deliveryCycleType === 'week') {
                    if (!is_null($cycle['weekCycle'])) {
                        $cycleData['weekCycle'][] = $cycle['weekCycle'];
                    }
                    if (!is_null($cycle['weekDayCycle'])) {
                        $cycleData['weekDayCycle'][] = $cycle['weekDayCycle'];
                    }
                }
            }
        }

        // 신청 시점에 신청하려는 상품이 변경 되었는지 유효성 검증
        $this->validateRegularGoods($regularOrderDTO, $cartInfo, $cartSno, $goodsData, $cycleData);

        return [
            'goodsNm' => $goodsData['goodsNm'],
            'goodsPrice' => $goodsData['goodsPrice'], // 원본 상품 가격
            'mileageInfo' => [
                'mileageFl' => $goodsData['mileageFl'],
                'mileageGroup' => $goodsData['mileageGroup'],
                'mileageGoods' => $goodsData['mileageGoods'],
                'mileageGoodsUnit' => $goodsData['mileageGoodsUnit'],
                'mileageGroupInfo' => $goodsData['mileageGroupInfo'],
                'mileageGroupMemberInfo' => $goodsData['mileageGroupMemberInfo'],
            ],
            'regularGoodsPolicy' => json_encode([
                'discountUseFl' => $goodsData['discountUseFl'],
                'discountType' => $goodsData['discountType'],
                'discountRate' => $goodsData['discountRate'],
                'discountPrice' => $goodsData['discountPrice'],
                'deliveryCycleType' => $goodsData['deliveryCycleType'],
                'cycleData' => $cycleData,
                'deliveryRoundsDisplayType' => $goodsData['deliveryRoundsDisplayType'],
                'maxDeliveryRounds' => $goodsData['maxDeliveryRounds'],
            ])
        ];
    }

    /**
     * 배송관련 정보 취합
     *
     * @param RegularOrderDTO $regularOrderDTO
     * @param int $goodsNo
     * @param array $deliveryInfo
     * @return array
     * @throws \Exception
     */
    private function getDeliveryInfo(RegularOrderDTO $regularOrderDTO, int $goodsNo, array $deliveryInfo): array
    {
        $deliverySno = $this->regularGoodsRepository->findRegularGoodsDeliverySnoByGoodsNo($goodsNo);

        // 배송 관련 policy 정보들은 scm 관련된 repo 에 존재
        $deliveryPolicy = $this->scmDeliveryBasicRepository->findScmDeliveryBasicInfoBySno($deliverySno);
        if (empty($deliveryPolicy)) {
            throw new \Exception('존재하지 않는 배송 정책 번호입니다: ' . $deliverySno);
        }

        $deliveryCharge = $this->scmDeliveryBasicRepository->findScmDeliveryChargeBySno($deliverySno);
        $deliveryArea = $this->scmDeliveryBasicRepository->findScmDeliveryAreaBySno($deliverySno);

        // 신청서 작성 시점인 오늘날짜 기준으로 배송예정일 및 주문 생성일 계산
        $orderCreateDate = RegularOrderUtil::calculateOrderDate(
            date('Y-m-d'),
            $deliveryInfo['deliveryCycleType'],
            $deliveryInfo['deliveryCycle'],
            $deliveryInfo['deliveryCycleDay'],
            true // 첫 배송일 계산 flag, 신청서 생성 시점은 무조건 처음
        );

        return [
            'deliveryPolicy' => $deliveryPolicy,
            'deliveryCharge' => $deliveryCharge,
            'deliveryArea' => $deliveryArea,
            'deliverySno' => $deliverySno,
            'shippingAddressSno' => $regularOrderDTO->getShippingSno(),
            'deliveryCycleType' => $deliveryInfo['deliveryCycleType'],
            'deliveryCycle' => $deliveryInfo['deliveryCycle'],
            'deliveryCycleDay' => $deliveryInfo['deliveryCycleDay'],
            'maxDeliveryRound' => $deliveryInfo['maxDeliveryRound'],
            'deliveryDueDate' => $orderCreateDate[0], // 0은 배송예정일
            'orderCreateDate' => $orderCreateDate[1] // 1은 주문 생성 예정일
        ];
    }

    /**
     * 정기결제 상품 가격
     *
     * @param RegularOrderDTO $regularOrderDTO
     * @param int $cartSno
     * @param array $cartInfo
     * @return int
     */
    private function getRegularGoodsPrice(RegularOrderDTO $regularOrderDTO, int $cartSno, array $cartInfo): int
    {
        $regularGoodsPrice = $regularOrderDTO->getRegularGoodsPrice()[$cartSno];
        $cnt = $cartInfo['goodsCnt']; // 주문 수량

        // 총 결제 금액 계산을 위함
        $this->totalPrice += ($regularGoodsPrice * $cnt); // 정기결제 가격을 더함

        return $regularGoodsPrice;
    }

    /**
     * 추가 상품 정보 저장
     *
     * @param string $applyNo 신청번호
     * @param RegularOrderDTO $regularOrderDTO 신청서에서 넘어온 정보
     * @param array $cartInfo 카트에서 찾은 상품정보
     * @param int $cartSno 카트 번호
     * @return void
     * @throws \Exception
     */
    private function insertRegularOrderAddGoods(string $applyNo, RegularOrderDTO $regularOrderDTO, array $cartInfo, int $cartSno)
    {
        $addGoodsInfo = $this->getAddGoodsInfo($regularOrderDTO, $cartInfo, $cartSno);
        if (!empty($addGoodsInfo)) {
            $this->logger->channel('regularOrder')->info(__CLASS__ .' '. __METHOD__ . ' Regular Order Add Goods INFO : ', $addGoodsInfo);
            $regularOrderAddGoodsCreateDTO = new RegularOrderAddGoodsCreateDTO($addGoodsInfo, $applyNo);
            $this->regularOrderAddGoodsRepository->insertRegularOrderAddGoods($regularOrderAddGoodsCreateDTO);
        }
    }

    /**
     * 추가 상품 데이터
     *
     * @param RegularOrderDTO $regularOrderDTO 신청서에서 넘어온 정보
     * @param array $cartInfo 카트에서 찾은 상품정보
     * @param int $cartSno 카트번호
     * @return array
     * @throws \Exception
     */
    private function getAddGoodsInfo(RegularOrderDTO $regularOrderDTO, array $cartInfo, int $cartSno): array
    {
        $regularAddGoods = $regularOrderDTO->getRegularAddGoodsPrice();
        if (empty($regularAddGoods)) {
            return [];
        }
        $addGoodsPriceList = $regularAddGoods[$cartSno];

        $addGoodsInfo = [];
        if(!empty($cartInfo['addGoodsInfo']['addGoodsNo'])) {
            // 카트에 들어간 데이터가 '[3,\"1\"]' 이런 식으로 이상하게 되어 있어서 stripslashes 해줘야 함
            $addGoodsNoList = json_decode(stripslashes($cartInfo['addGoodsInfo']['addGoodsNo']), true);
            $addGoodsCnt = json_decode(stripslashes($cartInfo['addGoodsInfo']['addGoodsCnt']), true);

            foreach ($addGoodsNoList as $index => $addGoodsNo) {
                $addGoods = $this->addGoodsRepository->findAddGoodsInfoByAddGoodsNo($addGoodsNo);
                if (empty($addGoods)) {
                    throw new RegularOrderGoodsChangeException('추가 상품 삭제로 인한 신청서 생성 불가, 정기배송 상품번호 : '. $cartInfo['regularGoodsNo'] . ' 추가상품 번호 : '. $addGoodsNo);
                }

                $addGoodsInfo[] = [
                    'addGoodsNo' => $addGoodsNo,
                    'addGoodsCnt' => (int) $addGoodsCnt[$index],
                    'addGoodsPrice' => $addGoodsPriceList[$addGoodsNo],
                    'addGoodsOptionName' => $addGoods['optionNm']
                ];

                // 총 결제 금액 계산을 위함
                $this->totalPrice += ($addGoodsPriceList[$addGoodsNo] * $addGoodsCnt[$index]); // 추가상품 가격을 총 가격에 더함
            }
        }

        return $addGoodsInfo;
    }

    /**
     * 사은품 정보 저장
     *
     * @param RegularOrderDTO $regularOrderDTO 신청서에서 넘어온 정보
     * @return void
     * @throws \Exception
     */
    private function insertRegularOrderGift(string $applyNo, int $cartSno, RegularOrderDTO $regularOrderDTO)
    {
        $originGiftInfo = $regularOrderDTO->getGiftInfo();
        if (empty($originGiftInfo)) {
            return;
        }

        $optionGiftInfo = $originGiftInfo[$cartSno] ?? [];

        // 사은품 선택수량 검증 (변조 방지) - 위반 시 AlertBackException
        $this->regularGiftSelectCntValidator->assertValid($optionGiftInfo);

        $giftInfo = [];
        foreach ($optionGiftInfo as $gift) {
            if (!isset($gift['giftNo'])) {
                continue; // 사은품 select 되지 않으면 giftNo 없음
            }
            $giftInfo[] = [
                'applyNo' => $applyNo, // 신청 번호
                'giftNo' => $gift['giftNo'], // 사은품 상품 번호
                'regularGiftPresentInfoSno' => $gift['regularGiftPresentInfoSno'] // 사은품조건 번호
            ];
        }
        $this->logger->channel('regularOrder')->info(__CLASS__ .' '. __METHOD__ . ' Regular Order Gift INFO : ', $giftInfo);

        $regularOrderGiftCreateDTO = new RegularOrderGiftCreateDTO($giftInfo);
        $this->regularOrderGiftRepository->insertRegularOrderGift($regularOrderGiftCreateDTO);
    }

    /**
     * 배송예정일(회차) 로그 저장
     *
     * @param string $applyNo 신청번호
     * @param array $deliveryInfo 배송관련 정보
     * @return void
     */
    private function insertRegularOrderDeliveryLog(string $applyNo, array $deliveryInfo)
    {
        // 신청서 작성 시점은 무조건 1회차 RegularOrderApplicationCreate::FIRST_DELIVERY_ROUND = 1
        $regularOrderDeliveryLogDTO = new RegularOrderDeliveryLogDTO($applyNo, RegularOrderApplicationCreate::FIRST_DELIVERY_ROUND, $deliveryInfo['deliveryDueDate']);
        $this->regularOrderDeliveryLogRepository->insertRegularOrderDeliveryLog($regularOrderDeliveryLogDTO);
    }

    /**
     * 변경이력 로그 저장
     *
     * @param string $applyNo 신청번호
     * @param int $memNo 회원번호
     * @return void
     */
    private function insertRegularOrderLog(string $applyNo, int $memNo)
    {
        $regularOrderLogDTO = new RegularOrderLogDTO($applyNo, 'user', $memNo, RegularOrderLogActionType::INITIAL);
        $this->regularOrderLogRepository->insertRegularOrderLog($regularOrderLogDTO);
    }

    /**
     * 신청자 정보 저장
     * @param string $applyGroupNo 신청그룹 번호
     * @param RegularOrderDTO $regularOrderDTO 신청서에서 넘어온 정보
     * @return void
     */
    private function insertRegularOrderApplier(string $applyGroupNo, RegularOrderDTO $regularOrderDTO)
    {
        $regularOrderApplierCreateDTO = new RegularOrderApplierCreateDTO($regularOrderDTO, $applyGroupNo);
        $this->regularOrderApplierRepository->insertRegularOrderApplier($regularOrderApplierCreateDTO);
    }

    /**
     * 이용상태 변경 로그 저장
     *
     * @param string $applyNo 신청 번호
     * @param int $memNo
     * @return void
     */
    private function insertRegularOrderStatusLog(string $applyNo, int $memNo)
    {
        $regularOrderStatusLogDTO = new RegularOrderStatusLogDTO($applyNo, 'user', $memNo, RegularOrderStatus::ACTIVE);
        $this->regularOrderStatusLogRepository->insertRegularOrderStatusLog($regularOrderStatusLogDTO);
    }

    /**
     * 옵션 정보 저장
     * @param int $applyNo 신청 번호
     * @param array $cartInfo 카트에서 찾은 상품 정보
     * @param RegularOrderDTO $regularOrderDTO 신청서에서 넘어온 정보
     * @param int $cartSno 카트 번호
     * @return void
     * @throws \Exception
     */
    private function insertRegularOrderGoodsOption(int $applyNo, array $cartInfo, RegularOrderDTO $regularOrderDTO, int $cartSno)
    {
        $regularGoodsNo = $cartInfo['regularGoodsNo'];
        $optionSno = $cartInfo['optionSno'];

        // option price
        $regularGoodsOptionPrice = $regularOrderDTO->getRegularGoodsPriceOption();
        $optionPrice = $regularGoodsOptionPrice[$cartSno]; // 계산된 정기결제 옵션 가격

        // 총 결제 금액 계산을 위함
        $cnt = $cartInfo['goodsCnt']; // 주문 수량
        $this->totalPrice += ($optionPrice * $cnt); // 정기결제 가격을 더함

        // optionInfo
        $goodsOption = $this->goodsOptionRepository->findGoodsOptionByOptionSno($optionSno);
        if (empty($goodsOption)) {
            throw new RegularOrderGoodsChangeException('원본 상품 옵션 삭제로 인한 신청서 생성 불가, 정기배송 상품번호 : '. $cartInfo['regularGoodsNo'] . ' 옵션 번호 : '. $optionSno);
        }

        // option info 생성
        $optionInfo = '';
        if (!empty($goodsOption['optionName'])) {
            $optionNames = explode('^|^', $goodsOption['optionName']);
            $tmp = [];
            foreach ($optionNames as $index => $optionName) {
                $tmp[] = [
                    $optionName,
                    $goodsOption['optionValue'.($index + 1)], // optionValue1, optionValue2 형태
                    !empty($goodsOption['optionCode']) ? $goodsOption['optionCode'] : null,
                    floatval($goodsOption['optionPrice']),
                    !empty($goodsOption['optionDeliveryCode']) ? $goodsOption['optionDeliveryCode'] : null,
                ];
            }
            $optionInfo = json_encode($tmp, JSON_UNESCAPED_UNICODE);
        }

        $regularOrderGoodsOptionDTO = new RegularOrderGoodsOptionDTO($applyNo, $optionSno, $regularGoodsNo, $optionPrice, $optionInfo, floatval($goodsOption['optionPrice']));
        $this->regularOrderGoodsOptionRepository->insertRegularOrderGoodsOption($regularOrderGoodsOptionDTO);
    }

    /**
     * 옵션 텍스트 정보
     * @param int $applyNo 신청 번호
     * @param array $cartInfo 상품 정보
     * @param RegularOrderDTO $regularOrderDTO 신청서에서 넘어온 정보
     * @param int $cartSno 카트 번호
     * @return void
     * @throws \Exception
     */
    private function insertRegularOrderGoodsOptionText(int $applyNo, array $cartInfo, RegularOrderDTO $regularOrderDTO, int $cartSno)
    {
        // option text price
        $regularGoodsPriceOptionText = $regularOrderDTO->getRegularGoodsPriceOptionText();
        if (empty($regularGoodsPriceOptionText)) {
            return;
        }
        $optionTextPrice = $regularGoodsPriceOptionText[$cartSno];

        // 총 결제 금액 계산을 위함
        $cnt = $cartInfo['goodsCnt']; // 주문 수량
        $this->totalPrice += ($optionTextPrice * $cnt); // 정기결제 가격을 더함

        // option text info
        if (!empty($cartInfo['optionTextInfo'])) {
            $regularGoodsNo = $cartInfo['regularGoodsNo'];
            $optionTextList = json_decode($cartInfo['optionTextInfo'], true);

            $optionTextInfo = [];
            $originOptionTextPrice = 0;
            foreach ($optionTextList as $optionTextSno => $optionTextValue) {
                $optionText = $this->goodsOptionTextRepository->findGoodsOptionTextByOptionTextSno($optionTextSno);
                if (empty($optionText)) {
                    throw new RegularOrderGoodsChangeException('원본 상품 텍스트 옵션 삭제로 인한 신청서 생성 불가, 정기배송 상품번호 : '. $cartInfo['regularGoodsNo'] . ' 텍스트 옵션 번호 : '. $optionTextSno);
                }
                $optionTextInfo[$optionTextSno] = [
                    $optionText['optionName'],
                    $optionTextValue,
                    floatval($optionText['addPrice']),
                ];
                $originOptionTextPrice += floatval($optionText['addPrice']);
            }
            $regularOrderGoodsOptionTextDTO = new RegularOrderGoodsOptionTextDTO($applyNo, $regularGoodsNo, $optionTextPrice, json_encode($optionTextInfo, JSON_UNESCAPED_UNICODE), $originOptionTextPrice);
            $this->regularOrderGoodsOptionTextRepository->insertRegularOrderGoodsOptionText($regularOrderGoodsOptionTextDTO);
        }
    }

    /**
     * 신청서 완료 후 관련 카트 정보 삭제
     * @param RegularOrderDTO $regularOrderDTO
     * @return void
     */
    public function deleteCart(RegularOrderDTO $regularOrderDTO)
    {
        $cartSnoList = $regularOrderDTO->getCartSno();
        $this->regularOrderCartRepository->deleteCartBySnoList($cartSnoList);
    }

    /**
     * 신청 시점에 상품 변경이 있었는지에 대한 유효성 검증
     *
     * @param RegularOrderDTO $regularOrderDTO
     * @param array $cartInfo
     * @param int $cartSno
     * @param array $goodsData
     * @param array $cycleData
     * @return void
     */
    private function validateRegularGoods(RegularOrderDTO $regularOrderDTO, array $cartInfo, int $cartSno, array $goodsData, array $cycleData)
    {
        // 정기상품 삭제 및 비활성화 상태 체크
        if ($goodsData['delFl'] === 'y' || $goodsData['applyStatus'] === 'disabled') {
            throw new RegularOrderGoodsChangeException('정기배송 상품 삭제로 인한 신청서 생성 불가, 정기배송 상품번호 : '. $cartInfo['regularGoodsNo']);
        }

        // 추가상품이 있는 경우 원본상품에 추가 상품이 삭제 됐는지 체크
        $cartAddGoods = json_decode(stripslashes($cartInfo['addGoodsInfo']['addGoodsNo']), true);
        if (!empty($cartAddGoods)) {
            // 현재 원본상품 추가상품 번호 추출
            $currentAddGoods = json_decode(stripslashes($goodsData['addGoods']), true);
            $currentAddGoodsNo = array_merge(...array_column($currentAddGoods, 'addGoods'));
            if (empty($currentAddGoodsNo) || !empty(array_diff($cartAddGoods, $currentAddGoodsNo))) {
                throw new RegularOrderGoodsChangeException('정기배송 상품에 추가 상품 삭제로 인한 신청서 생성 불가, 정기배송 상품번호 : '. $cartInfo['regularGoodsNo']);
            }
        }

        $discountUseFl = $regularOrderDTO->getRegularGoodsDiscountUseFl()[$cartSno];
        $regularGoodsPrice = $regularOrderDTO->getRegularGoodsPrice()[$cartSno];
        if ($discountUseFl !== $goodsData['discountUseFl'] || $regularGoodsPrice !== $goodsData['regularPrice']) {
            throw new RegularOrderGoodsDiscountPriceException('정기배송 상품 할인 여부 변경으로 인한 신청서 생성 불가, 정기배송 상품번호 : '. $cartInfo['regularGoodsNo']);
        }

        // === 배송주기 및 회차 검증 ===
        $deliveryCycleType = $goodsData['deliveryCycleType'];
        $cartDeliveryInfo = $cartInfo['deliveryInfo'];

        // 1. 배송주기 타입 불일치(전체는 체크 안함)
        if ($deliveryCycleType !== 'all' && $deliveryCycleType !== $cartDeliveryInfo['deliveryCycleType']) {
            throw new RegularOrderGoodsChangeException('정기배송 상품 배송주기 타입 불일치로 인한 신청서 생성 불가, 정기배송 상품번호 : '. $cartInfo['regularGoodsNo']);
        }

        // 2. 월단위 배송주기 검증
        if ($deliveryCycleType === 'month' && !in_array($cartDeliveryInfo['deliveryCycle'], $cycleData['monthCycle'])) {
            throw new RegularOrderGoodsChangeException('정기배송 상품 월단위 배송 주기 변경 으로 인한 신청서 생성 불가, 정기배송 상품번호 : '. $cartInfo['regularGoodsNo']);
        }

        // 3. 주단위 배송주기 및 요일 검증
        if ($deliveryCycleType === 'week') {
            if (!in_array($cartDeliveryInfo['deliveryCycle'], $cycleData['weekCycle'])) {
                throw new RegularOrderGoodsChangeException('정기배송 상품 주단위 배송 주기 변경 으로인한 신청서 생성 불가, 정기배송 상품번호 : '. $cartInfo['regularGoodsNo']);
            }
            if (!in_array($cartDeliveryInfo['deliveryCycleDay'], $cycleData['weekDayCycle'])) {
                throw new RegularOrderGoodsChangeException('정기배송 상품 요일 배송 주기 변경 으로 인한 신청서 생성 불가, 정기배송 상품번호 : '. $cartInfo['regularGoodsNo']);
            }
        }

        // 4. 배송회차 검증
        $cartMaxRound = $cartDeliveryInfo['maxDeliveryRound'];
        $goodsMaxRound = $goodsData['maxDeliveryRounds'];
        if ($goodsMaxRound !== null) {
            if ($cartMaxRound > 0 && $goodsMaxRound === 0) {
                throw new RegularOrderGoodsChangeException('정기배송 상품 배송회차가 설정 -> 미설정으로 변경됨 으로 인한 신청서 생성 불가, 정기배송 상품번호 : '. $cartInfo['regularGoodsNo']);
            }
            if ($cartMaxRound === 0 && $goodsMaxRound > 0) {
                throw new RegularOrderGoodsChangeException('정기배송 상품 배송회차가 미설정 -> 설정으로 변경됨 으로 인한 신청서 생성 불가, 정기배송 상품번호 : '. $cartInfo['regularGoodsNo']);
            }
            if ($cartMaxRound > $goodsMaxRound) {
                throw new RegularOrderGoodsChangeException('정기배송 상품 배송회차가 기존보다 적어졌음 으로 인한 신청서 생성 불가, 정기배송 상품번호 : '. $cartInfo['regularGoodsNo']);
            }
        }
    }
}
