<?php
/*
 * Copyright (C) 2025 NHN COMMERCE. - All Rights Reserved
 *
 * Unauthorized copying or redistribution of this file in source and binary forms via any medium
 * is strictly prohibited.
 */

namespace Bundle\Component\RegularDelivery\RegularGoods;

use Framework\Log\Logger;
use Illuminate\Database\Capsule\Manager;
use Origin\Enum\RegularDelivery\RegularGoods\RegularGoodsAttribute;
use Origin\Enum\RegularDelivery\RegularOrder\RegularOrderLogActionType;
use DTO\RegularDelivery\RegularOrder\RegularOrderLogDTO;
use DTO\RegularDelivery\RegularOrder\RegularOrderGoodsOptionDTO;
use DTO\RegularDelivery\RegularOrder\RegularOrderGoodsOptionTextDTO;
use DTO\RegularDelivery\RegularOrder\RegularOrderAddGoodsCreateDTO;
use DTO\RegularDelivery\RegularOrder\RegularOrderDeliveryCreateDTO;
use DTO\RegularDelivery\RegularGoods\RegularGoodsChangeDTO;
use Repository\Goods\GoodsOptionRepository;
use Repository\Goods\GoodsOptionTextRepository;
use Repository\Goods\AddGoodsRepository;
use Repository\Scm\ScmDeliveryBasicRepository;
use Repository\RegularDelivery\RegularGoods\RegularGoodsRepository;
use Repository\RegularDelivery\RegularOrder\RegularOrderRepository;
use Repository\RegularDelivery\RegularOrder\RegularOrderGoodsRepository;
use Repository\RegularDelivery\RegularOrder\RegularOrderGoodsOptionRepository;
use Repository\RegularDelivery\RegularOrder\RegularOrderGoodsOptionTextRepository;
use Repository\RegularDelivery\RegularOrder\RegularOrderAddGoodsRepository;
use Repository\RegularDelivery\RegularOrder\RegularOrderGiftRepository;
use Repository\RegularDelivery\RegularOrder\RegularOrderDeliveryRepository;
use Repository\RegularDelivery\RegularOrder\RegularOrderLogRepository;
use Bundle\Util\Order\RegularOrderUtil;
use Origin\Enum\RegularDelivery\RegularGoods\RegularAddGoodsAttribute;

class RegularGoodsChange
{
    /** @var GoodsOptionRepository */
    private $goodsOptionRepository;

    /** @var GoodsOptionTextRepository */
    private $goodsOptionTextRepository;

    /** @var AddGoodsRepository */
    private $addGoodsRepository;

    /** @var ScmDeliveryBasicRepository */
    private $scmDeliveryBasicRepository;

    /** @var RegularGoodsRepository */
    private $regularGoodsRepository;

    /** @var RegularOrderRepository */
    private $regularOrderRepository;

    /** @var RegularOrderGoodsRepository */
    private $regularOrderGoodsRepository;

    /** @var RegularOrderGoodsOptionRepository */
    private $regularOrderGoodsOptionRepository;

    /** @var RegularOrderGoodsOptionTextRepository */
    private $regularOrderGoodsOptionTextRepository;

    /** @var RegularOrderAddGoodsRepository */
    private $regularOrderAddGoodsRepository;

    /** @var RegularOrderGiftRepository */
    private $regularOrderGiftRepository;

    /** @var RegularOrderDeliveryRepository */
    private $regularOrderDeliveryRepository;

    /** @var RegularOrderLogRepository */
    private $regularOrderLogRepository;

    /** @var Logger */
    private $logger;

    /** @var Manager */
    private $dbManager;

    /** @var int 단일 신청서 내 상품 합계 금액 */
    private $totalPrice;

    /** @var RegularGiftSelectCntValidator */
    private $regularGiftSelectCntValidator;

    public function __construct(
        GoodsOptionRepository $goodsOptionRepository,
        GoodsOptionTextRepository $goodsOptionTextRepository,
        AddGoodsRepository $addGoodsRepository,
        ScmDeliveryBasicRepository $scmDeliveryBasicRepository,
        RegularGoodsRepository $regularGoodsRepository,
        RegularOrderRepository $regularOrderRepository,
        RegularOrderGoodsRepository $regularOrderGoodsRepository,
        RegularOrderGoodsOptionRepository $regularOrderGoodsOptionRepository,
        RegularOrderGoodsOptionTextRepository $regularOrderGoodsOptionTextRepository,
        RegularOrderAddGoodsRepository $regularOrderAddGoodsRepository,
        RegularOrderGiftRepository $regularOrderGiftRepository,
        RegularOrderDeliveryRepository $regularOrderDeliveryRepository,
        RegularOrderLogRepository $regularOrderLogRepository,
        Logger $logger,
        Manager $dbManager,
        RegularGiftSelectCntValidator $regularGiftSelectCntValidator
    )
    {
        $this->goodsOptionRepository = $goodsOptionRepository;
        $this->goodsOptionTextRepository = $goodsOptionTextRepository;
        $this->addGoodsRepository = $addGoodsRepository;
        $this->regularGoodsRepository = $regularGoodsRepository;
        $this->scmDeliveryBasicRepository = $scmDeliveryBasicRepository;
        $this->regularOrderRepository = $regularOrderRepository;
        $this->regularOrderGoodsRepository = $regularOrderGoodsRepository;
        $this->regularOrderGoodsOptionRepository = $regularOrderGoodsOptionRepository;
        $this->regularOrderGoodsOptionTextRepository = $regularOrderGoodsOptionTextRepository;
        $this->regularOrderAddGoodsRepository = $regularOrderAddGoodsRepository;
        $this->regularOrderGiftRepository = $regularOrderGiftRepository;
        $this->regularOrderDeliveryRepository = $regularOrderDeliveryRepository;
        $this->regularOrderLogRepository = $regularOrderLogRepository;
        $this->logger = $logger;
        $this->dbManager = $dbManager;
        $this->totalPrice = 0;
        $this->regularGiftSelectCntValidator = $regularGiftSelectCntValidator;
    }

    /**
     * 정기배송 신청서 배송정보 업데이트
     *
     * @param RegularGoodsChangeDTO $goodsChangeDto
     * @return void
     * @throws \Throwable
     */
    public function update(RegularGoodsChangeDTO $goodsChangeDto)
    {
        $applyNo = $goodsChangeDto->getApplyNo();
        $regularGoodsNo = $goodsChangeDto->getRegularGoodsNo();
        $sessionType = $goodsChangeDto->getSessionType();
        $sessionSno = $goodsChangeDto->getSessionSno();

        // 본인 소유의 신청서인지 체크
        if ($sessionType === 'user' && !$this->regularOrderRepository->hasRegularOrderOwnership($applyNo, $sessionSno)) {
            throw new \Exception('본인 소유의 신청서만 변경 가능합니다.');
        }

        // 변경하려는 정기배송 상품 번호의 원본 상품 정보를 조회
        $originGoodsInfo = $this->getOriginGoodsInfo($applyNo, $regularGoodsNo);

        // 유효성 검사
        $this->validate($goodsChangeDto, $originGoodsInfo, true);

        // 사은품 선택수량 검증 (변조 방지) - 회원(마이페이지) 변경만 차단, 관리자 수기 변경은 제외
        $giftInfoDto = $goodsChangeDto->getGiftInfo();
        if ($sessionType === 'user' && $giftInfoDto !== null) {
            $this->regularGiftSelectCntValidator->assertValid($giftInfoDto->getGiftData());
        }

        $this->dbManager->getConnection()->beginTransaction();
        try {
            // 상품 정보 업데이트
            $this->replaceRegularOrderGoods($goodsChangeDto, $originGoodsInfo);

            // 상품 변경에 따른 배송비 정책 업데이트
            $this->replaceRegularOrderDelivery($goodsChangeDto, $originGoodsInfo);

            // 선택 옵션 업데이트
            $this->replaceRegularOrderGoodsOption($goodsChangeDto, $originGoodsInfo);

            // 문구 옵션 업데이트
            $this->replaceRegularOrderGoodsOptionText($goodsChangeDto, $originGoodsInfo);

            // 추가 상품 업데이트
            $this->replaceRegularOrderAddGoods($goodsChangeDto, $originGoodsInfo);

            // 사은품 정보 업데이트
            $this->replaceRegularOrderGift($goodsChangeDto);

            // 합계 금액 및 배송비 업데이트
            $this->replaceRegularOrder($goodsChangeDto, $originGoodsInfo);

            $this->dbManager->getConnection()->commit();
        } catch (\Throwable $e) {
            $this->dbManager->getConnection()->rollBack();
            $this->logger->channel('regularDelivery')->warning(__CLASS__ .' '. __METHOD__. ' 정기배송 신청서 상품 정보 업데이트 오류 ', [$e->getMessage()]);
            throw new \Exception();
        }
    }

    /**
     * 정기배송 상품의 원본 상품 조회
     *
     * @param int $applyNo
     * @param int $regularGoodsNo
     * @return array
     * @throws \Exception
     */
    private function getOriginGoodsInfo(int $applyNo, int $regularGoodsNo): array
    {
        // 정기배송 상품 번호로 원본 상품 조회
        $goodsData = $this->regularGoodsRepository->findRegularGoodsDataByRegularGoodsNo($regularGoodsNo);

        $deliverySno = $goodsData['deliverySno'];

        // 배송 관련 정책 조회
        $deliveryPolicy = $this->scmDeliveryBasicRepository->findScmDeliveryBasicInfoBySno($deliverySno);
        if (empty($deliveryPolicy)) {
            throw new \Exception('존재하지 않는 배송 정책 번호입니다: ' . $deliverySno);
        }

        $deliveryCharge = $this->scmDeliveryBasicRepository->findScmDeliveryChargeBySno($deliverySno);
        $deliveryArea = $this->scmDeliveryBasicRepository->findScmDeliveryAreaBySno($deliverySno);

        // 전체 배송주기 사용이 아니면 배송주기 정보 조회
        $cycleData = [];
        if ($goodsData['deliveryCycleType'] !== 'all') {
            $regularGoodsCycle = $this->regularGoodsRepository->findRegularGoodsDeliveryInfoByRegularGoodsNo($regularGoodsNo);
            foreach ($regularGoodsCycle as $cycle) {
                if ($goodsData['deliveryCycleType'] === 'month') {
                    $cycleData['monthCycle'][] = $cycle['monthCycle'];
                }

                if ($goodsData['deliveryCycleType'] === 'week') {
                    if (!is_null($cycle['weekCycle'])) {
                        $cycleData['weekCycle'][] = $cycle['weekCycle'];
                    }
                    if (!is_null($cycle['weekDayCycle'])) {
                        $cycleData['weekDayCycle'][] = $cycle['weekDayCycle'];
                    }
                }
            }
        }

        return [
            'goodsNo' => $goodsData['goodsNo'],
            'goodsNm' => $goodsData['goodsNm'],
            'goodsPrice' => $goodsData['goodsPrice'],
            'delFl' => $goodsData['delFl'],
            'applyStatus' => $goodsData['applyStatus'],
            'soldOutFl' => $goodsData['soldOutFl'],
            'stockFl' => $goodsData['stockFl'],
            'totalStock' => $goodsData['totalStock'],
            'deliverySno' => $deliverySno,
            'deliveryPolicy' => $deliveryPolicy,
            'deliveryCharge' => $deliveryCharge,
            'deliveryArea' => $deliveryArea,
            'mileageInfo' => [
                'mileageFl' => $goodsData['mileageFl'],
                'mileageGroup' => $goodsData['mileageGroup'],
                'mileageGoods' => $goodsData['mileageGoods'],
                'mileageGoodsUnit' => $goodsData['mileageGoodsUnit'],
                'mileageGroupInfo' => $goodsData['mileageGroupInfo'],
                'mileageGroupMemberInfo' => $goodsData['mileageGroupMemberInfo']
            ],
            'regularGoodsPolicy' => [
                'discountUseFl' => $goodsData['discountUseFl'],
                'discountType' => $goodsData['discountType'],
                'discountRate' => $goodsData['discountRate'],
                'discountPrice' => $goodsData['discountPrice'],
                'deliveryCycleType' => $goodsData['deliveryCycleType'],
                'cycleData' => $cycleData,
                'deliveryRoundsDisplayType' => $goodsData['deliveryRoundsDisplayType'],
                'maxDeliveryRounds' => $goodsData['maxDeliveryRounds']
            ],
            'addGoodsFl' => $goodsData['addGoodsFl'],
            'addGoods' => $goodsData['addGoods']
        ];
    }

    /**
     * 상품 정보 업데이트
     *
     * @param RegularGoodsChangeDTO $goodsChangeDto
     * @param array $originGoodsInfo
     * @return void
     */
    private function replaceRegularOrderGoods(RegularGoodsChangeDTO $goodsChangeDto, array $originGoodsInfo)
    {
        $applyNo = $goodsChangeDto->getApplyNo();
        $regularGoodsNo = $goodsChangeDto->getRegularGoodsNo();
        $regularGoodsCnt = $goodsChangeDto->getGoodsCnt();
        $sessionType = $goodsChangeDto->getSessionType();
        $sessionSno = $goodsChangeDto->getSessionSno();

        // 신청서 번호로 기존 상품 정보 조회
        $currentRegularOrderGoods = $this->regularOrderGoodsRepository->findRegularOrderGoodsByApplyNo($applyNo);
        $beforeData = $currentRegularOrderGoods['regularGoodsNm'] . '(' . $currentRegularOrderGoods['goodsNo'] . ') ';

        // 정기배송 상품 가격
        $regularGoodsPrice = $this->regularGoodsRepository->findRegularGoodsPriceByRegularGoodsNo($regularGoodsNo);

        // 정기 결제 상품의 할인 가격
        $discountPrice = max(0, $originGoodsInfo['goodsPrice'] - $regularGoodsPrice);

        // 업데이트 정보 취합
        $updateDataInfo = [
            'applyNo' => $applyNo,
            'regularGoodsNo' => $regularGoodsNo,
            'regularGoodsNm' => $originGoodsInfo['goodsNm'],
            'regularGoodsPrice' => $regularGoodsPrice,
            'originGoodsPrice' => $originGoodsInfo['goodsPrice'],
            'discountPrice' => $discountPrice,
            'regularGoodsCnt' => $regularGoodsCnt,
            'mileageInfo' => json_encode($originGoodsInfo['mileageInfo'], JSON_UNESCAPED_UNICODE),
            'regularGoodsPolicy' => json_encode($originGoodsInfo['regularGoodsPolicy'], JSON_UNESCAPED_UNICODE),
        ];
        $afterData = $originGoodsInfo['goodsNm'] . '(' . $originGoodsInfo['goodsNo'] . ') ';
        $this->logger->channel('regularDelivery')->info('상품 정보 변경 : ', $updateDataInfo);

        // 정기배송 상품 정보 업데이트
        $this->regularOrderGoodsRepository->updateRegularOrderGoodsByApplyNo($updateDataInfo);

        // 상품 변경 로그 저장
        $this->insertRegularOrderLog($applyNo, RegularOrderLogActionType::PRODUCT_CHANGE, $beforeData, $afterData, $sessionType, $sessionSno);

        // 상품 합계 금액 추가 합산 처리
        $this->totalPrice += ($regularGoodsPrice * $regularGoodsCnt);
    }

    /**
     * 상품 변경에 따른 배송비 정책 업데이트
     *  - 삭제 후 신규 등록 처리
     *
     * @param RegularGoodsChangeDTO $goodsChangeDto
     * @param array $originGoodsInfo
     * @return void
     */
    private function replaceRegularOrderDelivery(RegularGoodsChangeDTO $goodsChangeDto, array $originGoodsInfo)
    {
        $applyNo = $goodsChangeDto->getApplyNo();

        // 기존 배송비 조건 일련번호
        $deliverySno = $this->regularOrderDeliveryRepository->findRegularOrderDeliverySnoByApplyNo($applyNo);

        // 배송비 정책이 변경될 경우만 업데이트 처리
        if ($deliverySno !== $originGoodsInfo['deliverySno']) {

            // 기존 배송비 정책 정보 삭제
            $this->regularOrderDeliveryRepository->deleteRegularOrderDeliveryByApplyNo($applyNo);
            $this->logger->channel('regularDelivery')->info('상품 정보 변경, 배송 정보 삭제 : ', [
                'applyNo' => $applyNo
            ]);

            // 신규 배송비 정책 정보 등록
            $regularOrderDeliveryDto = new RegularOrderDeliveryCreateDTO(
                $applyNo,
                $originGoodsInfo['deliverySno'],
                $originGoodsInfo['deliveryPolicy'],
                $originGoodsInfo['deliveryCharge'],
                $originGoodsInfo['deliveryArea']
            );
            $this->regularOrderDeliveryRepository->insertRegularOrderDelivery($regularOrderDeliveryDto);
            $this->logger->channel('regularDelivery')->info('상품 정보 변경, 배송 정보 등록 : ', [
                'applyNo' => $applyNo,
                'deliverySno' => $originGoodsInfo['deliverySno']
            ]);
        }
    }

    /**
     * 선택 옵션 업데이트
     *  - 삭제 후 신규 등록 처리
     *
     * @param RegularGoodsChangeDTO $goodsChangeDto
     * @param array $originGoodsInfo
     * @return void
     * @throws \Throwable
     */
    private function replaceRegularOrderGoodsOption(RegularGoodsChangeDTO $goodsChangeDto, array $originGoodsInfo)
    {
        $applyNo = $goodsChangeDto->getApplyNo();
        $regularGoodsNo = $goodsChangeDto->getRegularGoodsNo();
        $optionSno = $goodsChangeDto->getOptionSno();
        $optionCnt = $goodsChangeDto->getGoodsCnt();

        // 기존 선택 옵션 정보 삭제
        $this->regularOrderGoodsOptionRepository->deleteRegularOrderGoodsOptionByApplyNo($applyNo);
        $this->logger->channel('regularDelivery')->info('상품 정보 변경, 선택 옵션 삭제 : ', [
            'applyNo' => $applyNo
        ]);

        // 원본 선택 옵션 정보 조회
        $goodsOption = $this->goodsOptionRepository->findGoodsOptionByOptionSno($optionSno);
        $optionPrice = floatval($goodsOption['optionPrice']);

        // 정기배송 옵션가 계산
        $regularOptionPrice = RegularOrderUtil::calculateRegularOptionPrice(
            $optionPrice,
            $originGoodsInfo['regularGoodsPolicy']['discountUseFl'],
            $originGoodsInfo['regularGoodsPolicy']['discountType'],
            $originGoodsInfo['regularGoodsPolicy']['discountRate']
        );

        // 옵션 정보 정리
        $optionInfo = '';
        if (!empty($goodsOption['optionName'])) {
            $optionNames = explode('^|^', $goodsOption['optionName']);
            $tmpOptionInfo = [];
            foreach ($optionNames as $index => $optionName) {
                $tmpOptionInfo[] = [
                    $optionName,
                    $goodsOption['optionValue' . ($index + 1)],
                    !empty($goodsOption['optionCode']) ? $goodsOption['optionCode'] : null,
                    $optionPrice,
                    !empty($goodsOption['optionDeliveryCode']) ? $goodsOption['optionDeliveryCode'] : null,
                ];
            }
            $optionInfo = json_encode($tmpOptionInfo, JSON_UNESCAPED_UNICODE);
        }

        // 선택 옵션 등록
        $regularOrderGoodsOptionDTO = new RegularOrderGoodsOptionDTO($applyNo, $optionSno, $regularGoodsNo, $regularOptionPrice, $optionInfo, $optionPrice);
        $this->regularOrderGoodsOptionRepository->insertRegularOrderGoodsOption($regularOrderGoodsOptionDTO);
        $this->logger->channel('regularDelivery')->info('상품 정보 변경, 선택 옵션 등록 : ', [
            'applyNo' => $applyNo,
            'optionSno' => $optionSno,
            'regularGoodsNo' => $regularGoodsNo,
            'regularGoodsOptionPrice' => $regularOptionPrice,
            'originGoodsOptionPrice' => $optionPrice,
            'optionCnt' => $optionCnt,
            'optionInfo' => $optionInfo
        ]);

        // 상품 합계 금액 추가 합산 처리
        $this->totalPrice += ($regularOptionPrice * $optionCnt);
    }

    /**
     * 문구 옵션 업데이트
     *  - 삭제 후 신규 등록 처리
     *
     * @param RegularGoodsChangeDTO $goodsChangeDto
     * @param array $originGoodsInfo
     * @return void
     * @throws \Throwable
     */
    private function replaceRegularOrderGoodsOptionText(RegularGoodsChangeDTO $goodsChangeDto, array $originGoodsInfo)
    {
        $applyNo = $goodsChangeDto->getApplyNo();
        $regularGoodsNo = $goodsChangeDto->getRegularGoodsNo();
        $optionTextSnoList = $goodsChangeDto->getOptionTextSno();
        $optionTextValueList = $goodsChangeDto->getOptionTextValue();
        $optionCnt = $goodsChangeDto->getGoodsCnt();

        // 기존 문구 옵션 정보 삭제
        $this->regularOrderGoodsOptionTextRepository->deleteRegularOrderGoodsOptionTextByApplyNo($applyNo);
        $this->logger->channel('regularDelivery')->info('상품 정보 변경, 문구 옵션 삭제 : ', [
            'applyNo' => $applyNo
        ]);

        // 추가할 문구 옵션이 있을 경우만 실행
        if (!empty($optionTextSnoList) && !empty($optionTextValueList) && count($optionTextSnoList) === count($optionTextValueList))
        {
            $regularGoodsOptionTextPrice = 0;
            $originGoodsOptionTextPrice = 0;
            foreach ($optionTextSnoList as $index => $optionTextSno) {
                // 원본 문구 옵션 정보 조회
                $optionText = $this->goodsOptionTextRepository->findGoodsOptionTextByOptionTextSno($optionTextSno);
                if (empty($optionText)) {
                    throw new \Exception('존재하지 않는 텍스트 옵션 번호입니다: ' . $optionTextSno);
                }
                $addPrice = floatval($optionText['addPrice']);

                // 정기배송 문구 옵션 할인가 계산
                $regularAddPrice = RegularOrderUtil::calculateRegularOptionPrice(
                    $addPrice,
                    $originGoodsInfo['regularGoodsPolicy']['discountUseFl'],
                    $originGoodsInfo['regularGoodsPolicy']['discountType'],
                    $originGoodsInfo['regularGoodsPolicy']['discountRate']
                );

                // 문구 옵션 정보 정리
                $optionTextValue = $optionTextValueList[$index];
                $optionTextInfo[(string) $optionTextSno] = [
                    $optionText['optionName'],
                    $optionTextValue,
                    $addPrice,
                ];
                $regularGoodsOptionTextPrice += $regularAddPrice;
                $originGoodsOptionTextPrice += $addPrice;
            }

            // 문구 옵션 등록
            $regularOrderGoodsOptionTextDTO = new RegularOrderGoodsOptionTextDTO(
                $applyNo,
                $regularGoodsNo,
                $regularGoodsOptionTextPrice,
                json_encode($optionTextInfo, JSON_UNESCAPED_UNICODE),
                $originGoodsOptionTextPrice
            );
            $this->regularOrderGoodsOptionTextRepository->insertRegularOrderGoodsOptionText($regularOrderGoodsOptionTextDTO);
            $this->logger->channel('regularDelivery')->info('상품 정보 변경, 문구 옵션 등록 : ', [
                'applyNo' => $applyNo,
                'regularGoodsNo' => $regularGoodsNo,
                'regularGoodsOptionTextPrice' => $regularGoodsOptionTextPrice,
                'originGoodsOptionTextPrice' => $originGoodsOptionTextPrice,
                'optionTextInfo' => $optionTextInfo
            ]);

            // 상품 합계 금액 추가 합산 처리
            $this->totalPrice += ($regularGoodsOptionTextPrice * $optionCnt);
        }
    }

    /**
     * 추가 상품 업데이트
     *  - 삭제 후 신규 등록 처리
     *
     * @param RegularGoodsChangeDTO $goodsChangeDto
     * @param array $originGoodsInfo
     * @return void
     * @throws \Exception
     */
    private function replaceRegularOrderAddGoods(RegularGoodsChangeDTO $goodsChangeDto, array $originGoodsInfo)
    {
        $applyNo = $goodsChangeDto->getApplyNo();
        $addGoodsNoList = $goodsChangeDto->getAddGoodsNo();
        $addGoodsCntList = $goodsChangeDto->getAddGoodsCnt();

        // 기존 추가 상품 정보 삭제
        $this->regularOrderAddGoodsRepository->deleteRegularOrderAddGoodsByApplyNo($applyNo);
        $this->logger->channel('regularDelivery')->info('상품 정보 변경, 추가 상품 삭제 : ', [
            'applyNo' => $applyNo
        ]);

        // 추가할 상품이 있을 경우만 실행
        if (!empty($addGoodsNoList) && !empty($addGoodsCntList) && count($addGoodsNoList) === count($addGoodsCntList))
        {
            // 추가 상품 정보 정리
            $addGoodsInfo = $this->getAddGoodsInfo($addGoodsNoList, $addGoodsCntList);

            // 추가 상품 등록
            $regularOrderAddGoodsCreateDTO = new RegularOrderAddGoodsCreateDTO($addGoodsInfo, $applyNo);
            $this->regularOrderAddGoodsRepository->insertRegularOrderAddGoods($regularOrderAddGoodsCreateDTO);
            $this->logger->channel('regularDelivery')->info('상품 정보 변경, 추가 상품 등록 : ', $addGoodsInfo);
        }
    }

    /**
     * 추가 상품 데이터 정리
     *
     * @param array $addGoodsNoList
     * @param array $addGoodsCntList
     * @return array
     * @throws \Exception
     */
    private function getAddGoodsInfo(array $addGoodsNoList, array $addGoodsCntList): array
    {
        $addGoodsInfo = [];
        foreach ($addGoodsNoList as $index => $addGoodsNo) {

            // 원본 추가 상품 정보 조회
            $addGoods = $this->addGoodsRepository->findAddGoodsInfoByAddGoodsNo($addGoodsNo);
            if (empty($addGoods)) {
                $this->logger->channel('regularDelivery')->info('삭제된 추가 상품 번호: ' . $addGoodsNo);
                throw new \Exception('삭제된 추가 상품입니다. 다시 선택해주세요.');
            }

            // 추가 상품 정보 정리
            $addGoodsCnt = (int) $addGoodsCntList[$index];
            $addGoodsPrice = $addGoods['goodsPrice'] ?? 0;
            $addGoodsInfo[] = [
                'addGoodsNo' => $addGoodsNo,
                'addGoodsCnt' => $addGoodsCnt,
                'addGoodsPrice' => $addGoodsPrice,
                'addGoodsOptionName' => $addGoods['optionNm']
            ];

            // 상품 합계 금액 추가 합산 처리
            $this->totalPrice += ($addGoodsPrice * $addGoodsCnt);
        }

        return $addGoodsInfo;
    }

    /**
     * 사은품 정보 업데이트
     *  - 삭제 후 신규 등록 처리
     *
     * @param RegularGoodsChangeDTO $goodsChangeDto
     * @return void
     */
    private function replaceRegularOrderGift(RegularGoodsChangeDTO $goodsChangeDto)
    {
        $applyNo = $goodsChangeDto->getApplyNo();
        $giftInfoDto = $goodsChangeDto->getGiftInfo();
        $sessionType = $goodsChangeDto->getSessionType();
        $sessionSno = $goodsChangeDto->getSessionSno();

        // 신청서 번호로 기존 사은품 정보 조회
        $beforeGiftList = $this->regularOrderGiftRepository->findGiftInfoByApplyNo($applyNo);

        // 기존 사은픔 정보 정리
        $beforeGiftData = [];
        $beforeData = '';
        foreach ($beforeGiftList as $gift) {
            $beforeGiftData[] = [
                'applyNo' => $gift['applyNo'],
                'giftNo' => $gift['giftNo'],
                'regularGiftPresentInfoSno' => $gift['regularGiftPresentInfoSno']
            ];
            $beforeData .= $gift['giftNm'] . '(' . $gift['giftNo'] . ') ';
        }

        // 사은품이 변경될 경우만 업데이트 처리
        if ($giftInfoDto->getGiftData() !== $beforeGiftData) {
            // 기존 사은품 정보 삭제
            $this->regularOrderGiftRepository->deleteRegularOrderGiftByApplyNo($applyNo);
            $this->logger->channel('regularDelivery')->info('상품 정보 변경, 사은품 삭제 : ', [
                'applyNo' => $applyNo,
                'prevGiftData' => $beforeGiftData
            ]);

            // 신규 사은품 정보 등록
            $this->regularOrderGiftRepository->insertRegularOrderGift($giftInfoDto);

            // 신청서 번호로 신규 사은품 정보 조회
            $afterGiftList = $this->regularOrderGiftRepository->findGiftInfoByApplyNo($applyNo);

            // 변경 사은품 정보 정리
            $afterGiftData = [];
            $afterData = '';
            foreach ($afterGiftList as $gift) {
                $afterGiftData[] = [
                    'applyNo' => $gift['applyNo'],
                    'giftNo' => $gift['giftNo'],
                    'regularGiftPresentInfoSno' => $gift['regularGiftPresentInfoSno']
                ];
                $afterData .= $gift['giftNm'] . '(' . $gift['giftNo'] . ') ';
            }

            // 사은품 정보 등록 로깅
            $this->logger->channel('regularDelivery')->info('상품 정보 변경, 사은품 등록 : ', [
                'applyNo' => $applyNo,
                'beforeGiftData' => $beforeGiftData,
                'afterGiftData' => $afterGiftData
            ]);

            // 사은품 변경 로그 저장
            $this->insertRegularOrderLog($applyNo,RegularOrderLogActionType::GIFT_CHANGE, $beforeData, $afterData, $sessionType, $sessionSno);
        }
    }

    /**
     * 신청서 관련 로그 저장
     *
     * @param string $applyNo
     * @param string $type
     * @param string $before
     * @param string $after
     * @param string $sessionType
     * @param int $sessionSno
     * @return void
     */
    private function insertRegularOrderLog(string $applyNo, string $type, string $before, string $after, string $sessionType, int $sessionSno)
    {
        $logDto = new RegularOrderLogDTO($applyNo, $sessionType, $sessionSno, $type, $before, $after);

        $this->regularOrderLogRepository->insertRegularOrderLog($logDto);
    }


    /**
     * 정기결제 신청 그룹의 총 상품금액 및 총 배송비 업데이트
     */
    private function replaceRegularOrder(RegularGoodsChangeDTO $goodsChangeDto, array $originGoodsInfo)
    {
        $applyNo = $goodsChangeDto->getApplyNo();

        // applyGroupNo 조회
        $applyGroupNo = $this->regularOrderGoodsRepository->findApplyGroupNoByApplyNo($applyNo);

        // applyGroupNo에 속한 신청서 정보 조회
        $regularOrderInfoList = $this->regularOrderGoodsRepository->findRegularOrderInfoByApplyNo($applyGroupNo);

        // 총 상품 금액 계산
        $totalGroupPrice = 0;
        foreach ($regularOrderInfoList as $index => $apply) {
            $optionPrice = (int)($apply['regularGoodsOptionPrice'] ?? 0);
            $optionTextPrice = (int)($apply['regularGoodsOptionTextPrice'] ?? 0);
            $goodsPrice = (int)($apply['regularGoodsPrice'] ?? 0);
            $goodsCnt = (int)($apply['regularGoodsCnt'] ?? 0);

            // 추가 상품 조회 및 계산
            $regularAddGoodsList = $this->regularOrderAddGoodsRepository->findAddGoodsInfoByApplyNo($apply['applyNo']);
            $addGoodsPrice = 0;
            $addGoodsCnt = 0;
            foreach ($regularAddGoodsList as $regularAddGoods){
                $addGoodsPrice += ((float)$regularAddGoods['regularAddGoodsPrice'] * $regularAddGoods['regularAddGoodsCnt']);
                $addGoodsCnt += $regularAddGoods['regularAddGoodsCnt'];
            }

            $totalGroupPrice += (($goodsPrice + $optionPrice + $optionTextPrice) * $goodsCnt) + $addGoodsPrice;

            $regularOrderInfoList[$index]['regularAddGoodsPrice'] = $addGoodsPrice;
            $regularOrderInfoList[$index]['regularAddGoodsCnt'] = $addGoodsCnt;
        }

        // 배송비 계산
        $groupedApplyList = [];
        foreach ($regularOrderInfoList as $item) {
            $groupedApplyList[$item['deliverySno']][] = $item;
        }
        $totalDeliveryCost = $this->calculateDeliveryCost($groupedApplyList);

        // 최종 저장
        $this->regularOrderRepository->updateTotalPriceAndDeliveryCharge($applyGroupNo, $totalGroupPrice, $totalDeliveryCost);
    }


    /**
     * 배송비 전체 계산
     */
    private function calculateDeliveryCost(array $groupedApplyList): int
    {
        $deliverySnoList = array_keys($groupedApplyList);

        // 전체 무료 배송 항목이 있는지 먼저 확인
        if ($this->scmDeliveryBasicRepository->findFreeDeliveryBySnoList($deliverySnoList)){
            return 0;
        }

        $rawDeliveryInfoList = $this->scmDeliveryBasicRepository->findScmDeliveryInfoBySnoList($deliverySnoList);

        $deliveryInfoList = $this->parseDeliveryInfoList($rawDeliveryInfoList);
        $totalDeliveryCost = 0;

        foreach ($deliveryInfoList as $deliveryInfo) {
            // 무료배송 여부
            if ($this->isFreeShipping($deliveryInfo)) {
                continue;
            }

            $applies = $groupedApplyList[$deliveryInfo['sno']];
            $aggregatedApplyList = $this->aggregateApplyList($applies, $deliveryInfo);
            $deliveryCost = $this->calculateCostByPolicy($aggregatedApplyList, $deliveryInfo);
            $totalDeliveryCost += $deliveryCost;
        }

        return $totalDeliveryCost;
    }

    /**
     * 배송 raw data 가공
     */
    private function parseDeliveryInfoList(array $rawDeliveryInfoList): array
    {
        foreach ($rawDeliveryInfoList as $index => $row) {
            $sno = $row['sno'];
            $rawDeliveryInfoList[$index]['deliveryAreaList'] = $this->scmDeliveryBasicRepository->findScmDeliveryAreaBySno($sno);
            $rawDeliveryInfoList[$index]['deliveryChargeList'] = $this->scmDeliveryBasicRepository->findScmDeliveryChargeBySno($sno);
        }

        return $rawDeliveryInfoList;
    }

    /**
     * 무료배송 여부 판단
     */
    private function isFreeShipping(array $deliveryInfo): bool
    {
        return ($deliveryInfo['deliveryMethodFl'] === 'visit' && $deliveryInfo['deliveryVisitPayFl'] === 'n')
            || $deliveryInfo['fixFl'] === 'free';
    }

    /**
     * 배송비 계산을 위한 상품 가격 계산용 공통 함수
     *
     * @param array $apply
     * @param array $pricePlusStandardParts
     * @return float
     */
    private function calculateGoodsPrice(array $apply, array $pricePlusStandard): float
    {
        $price = $apply['regularGoodsPrice'];

        if (in_array('option', $pricePlusStandard, true)) {
            $price += $apply['regularGoodsOptionPrice'];
        }
        if (in_array('text', $pricePlusStandard, true)) {
            $price += $apply['regularGoodsOptionTextPrice'];
        }

        $totalPrice = $price * $apply['regularGoodsCnt'];

        if (in_array('add', $pricePlusStandard, true)) {
            $totalPrice += $apply['regularAddGoodsPrice'] * $apply['regularAddGoodsCnt'];
        }

        return $totalPrice;
    }

    /**
     * 신청서 데이터 집계 (배송정책별)
     */
    private function aggregateApplyList(array $applyList, array $deliveryInfo): array
    {
        $result = [];
        $pricePlusStandard = explode('^|^', $deliveryInfo['pricePlusStandard']);

        if ($deliveryInfo['goodsDeliveryFl'] === 'y') {
            // 묶음 배송
            $aggregated = [
                'goodsPrice' => 0,
                'goodsCnt' => 0,
                'addGoodsCnt' => 0,
                'goodsWeight' => 0,
                'shippingAddress' => ''
            ];

            foreach ($applyList as $apply) {
                $aggregated['goodsPrice'] += $this->calculateGoodsPrice($apply, $pricePlusStandard);
                $aggregated['goodsCnt'] += $apply['regularGoodsCnt'];
                $aggregated['addGoodsCnt'] += $apply['regularAddGoodsCnt'];
                $aggregated['goodsWeight'] += $apply['goodsWeight'] * $apply['regularGoodsCnt'];
                $aggregated['shippingAddress'] = $apply['shippingAddress'];
            }
            $result[] = $aggregated;

        } elseif ($deliveryInfo['sameGoodsDeliveryFl'] === 'y') {
            // 동일상품일 경우, 1회만 부과
            foreach ($applyList as $apply) {
                $goodsNo = $apply['regularGoodsNo'];

                if (!isset($result[$goodsNo])) {
                    $result[$goodsNo] = [
                        'goodsPrice' => 0,
                        'goodsCnt' => 0,
                        'addGoodsCnt' => 0,
                        'goodsWeight' => 0,
                        'shippingAddress' => ''
                    ];
                }

                $result[$goodsNo]['goodsPrice'] += $this->calculateGoodsPrice($apply, $pricePlusStandard);
                $result[$goodsNo]['goodsCnt'] += $apply['regularGoodsCnt'];
                $result[$goodsNo]['addGoodsCnt'] += $apply['regularAddGoodsCnt'];
                $result[$goodsNo]['goodsWeight'] += $apply['goodsWeight'] * $apply['regularGoodsCnt'];
                $result[$goodsNo]['shippingAddress'] = $apply['shippingAddress'];
            }
            $result = array_values($result);

        } else {
            // 완전 개별배송
            foreach ($applyList as $apply) {
                $result[] = [
                    'goodsPrice' => $this->calculateGoodsPrice($apply, $pricePlusStandard),
                    'goodsCnt' => $apply['regularGoodsCnt'],
                    'addGoodsCnt' => $apply['regularAddGoodsCnt'],
                    'goodsWeight' => $apply['goodsWeight'] * $apply['regularGoodsCnt'],
                    'shippingAddress' => $apply['shippingAddress']
                ];
            }
        }

        return $result;
    }

    /**
     * 실제 배송비 정책에 따른 금액 산정
     */
    private function calculateCostByPolicy(array $aggregatedApplyList, array $deliveryInfo): int
    {
        $deliveryCost = 0;

        foreach ($aggregatedApplyList as $apply) {
            $baseCnt = $apply['goodsCnt'];
            if ($deliveryInfo['addGoodsCountInclude'] === 'y') {
                $baseCnt += $apply['addGoodsCnt'];
            }

            switch ($deliveryInfo['fixFl']) {
                case 'count':
                    if ($deliveryInfo['rangeRepeat'] === 'y') {
                        $deliveryCost += $this->matchDeliveryChargeByRange($baseCnt, $deliveryInfo['deliveryChargeList']);
                    } else {
                        $deliveryCost += $this->matchDeliveryCharge($baseCnt, $deliveryInfo['deliveryChargeList']);
                    }
                    break;

                case 'price':
                    if ($deliveryInfo['rangeRepeat'] === 'y') {
                        $deliveryCost += $this->matchDeliveryChargeByRange($apply['goodsPrice'], $deliveryInfo['deliveryChargeList']);
                    } else {
                        $deliveryCost += $this->matchDeliveryCharge($apply['goodsPrice'], $deliveryInfo['deliveryChargeList']);
                    }
                    break;

                case 'weight':
                    if ($deliveryInfo['rangeLimitFl'] === 'y' && $deliveryInfo['rangeLimitWeight'] <= $apply['goodsWeight']) {
                        throw new \Exception('무게가 '.$deliveryInfo['rangeLimitWeight'].'g 이상의 상품은 구매할 수 없습니다.');
                    }
                    if ($deliveryInfo['rangeRepeat'] === 'y') {
                        $deliveryCost += $this->matchDeliveryChargeByRange($apply['goodsWeight'], $deliveryInfo['deliveryChargeList']);
                    } else {
                        $deliveryCost += $this->matchDeliveryCharge($apply['goodsWeight'], $deliveryInfo['deliveryChargeList']);
                    }
                    break;

                default:
                    $deliveryCost += $deliveryInfo['deliveryChargeList'][0]['price'];
                    break;
            }

            // 지역별 추가 배송비
            if ($deliveryInfo['areaFl'] === 'y') {
                foreach ($deliveryInfo['deliveryAreaList'] as $area) {
                    if (strpos($apply['shippingAddress'], $area['addArea']) !== false) {
                        $deliveryCost += $area['addPrice'];
                    }
                }
            }
        }

        return $deliveryCost;
    }

    /**
     * 구간별 배송비 찾기 : 반복 범위 설정 미사용
     */
    private function matchDeliveryCharge(float $base, array $chargeList): int
    {
        foreach ($chargeList as $charge) {
            $unitStart = (float)$charge['unitStart'];
            $unitEnd = (float)$charge['unitEnd'];
            if ($unitStart <= $base && (empty($unitEnd) || $unitEnd > $base)) {
                return $charge['price'];
            }
        }
        return 0;
    }

    /**
     * 구간별 배송비 찾기 : 반복 범위 설정 사용
     */
    private function matchDeliveryChargeByRange(float $base, array $chargeList): int
    {
        if ($base <= 0) {
            return 0;
        }

        $unitStart = $chargeList[1]['unitStart'];
        $unitEnd = $chargeList[1]['unitEnd'];

        $chargePrice = $chargeList[0]['price'];

        if ($base < $unitStart || $unitEnd <= 0) {
            return $chargePrice;
        }

        // 시작 단위 차감
        $base -= $unitStart;

        if ($base < 0) {
            $base = 0;
        }

        // 단위로 나누기
        $base /= $unitEnd;

        // 소수점 올림, 정수면 1 더하기
        if (floor($base) != $base) {
            $base = (int) ceil($base);
        } else {
            $base += 1;
        }

        // 추가 요금 계산
        $chargePrice += $base * $chargeList[1]['price'];

        return $chargePrice;
    }

    /**
     * 상품 변경 전 상품 및 추가 상품에 대해 유효성 검사 진행
     *
     * @param RegularGoodsChangeDTO $goodsChangeDto
     * @param array $originGoodsInfo
     * @param bool $checkRegularDeliveryFl
     * @return void
     * @throws \Exception
     */
    private function validate(RegularGoodsChangeDTO $goodsChangeDto, array $originGoodsInfo, bool $checkRegularDeliveryFl)
    {
        $applyNo = $goodsChangeDto->getApplyNo();
        $optionSno = $goodsChangeDto->getOptionSno();
        $addGoodsNoList = $goodsChangeDto->getAddGoodsNo();

        // 정기결제(배송) 상품 변경 가능 여부 유효성 검사
        $this->validateRegularGoodsStatus($originGoodsInfo);

       if ($checkRegularDeliveryFl) {
           // 배송주기 및 종료회차에 대해 유효성 검사
           $this->validateRegularDelivery($applyNo, $originGoodsInfo['regularGoodsPolicy']);
       }

        // 옵션 정보 유효성 검사
        $this->validateOption($optionSno);

        // 일반상품에 대한 추가 상품 유효성 검사
        $this->validateAddGoodsExists($originGoodsInfo, $addGoodsNoList);
    }

    /**
     * 변경 가능한 상품인지 체크
     *
     * @param array $goodsData
     * @return void
     */
    private function validateRegularGoodsStatus(array $goodsData)
    {
        // 일반 상품 품절 여부 확인
        if ($goodsData['soldOutFl'] === 'y' || ($goodsData['stockFl'] === 'y' && $goodsData['totalStock'] <= 0)) {
            throw new \Exception('품절된 상품입니다. 다시 선택해 주세요.', RegularGoodsAttribute::ERROR_REGULAR_GOODS_OUT_OF_STOCK);
        }

        // 정기 결제(배송) 상품 존재 및 삭제 여부 확인
        if (empty($goodsData) || $goodsData['delFl'] === 'y') {
            throw new \Exception('삭제된 상품입니다. 다시 선택해 주세요.', RegularGoodsAttribute::ERROR_REGULAR_GOODS_DELETE);
        }

        // 정기결제(배송) 상품 신청 가능 여부 확인
        if ($goodsData['applyStatus'] === 'disabled') {
            throw new \Exception('신청 불가한 상품입니다. 다시 선택해 주세요.', RegularGoodsAttribute::ERROR_REGULAR_GOODS_APPLY_DISABLED);
        }
    }

    /**
     * 정기결제(배송) 배송주기 및 종료회차에 대해 유효성 검사
     *
     * @param int $applyNo
     * @param array $regularGoodsPolicy
     * @return void
     * @throws \Exception
     */
    private function validateRegularDelivery(int $applyNo, array $regularGoodsPolicy)
    {
        $cycleData = $regularGoodsPolicy['cycleData'];
        $deliveryRoundsDisplayType = $regularGoodsPolicy['deliveryRoundsDisplayType'];
        $maxDeliveryRounds = $regularGoodsPolicy['maxDeliveryRounds'];

        // 현재 신청서 조회
        $applyDeliveryInfo = $this->regularOrderGoodsRepository->findCurrentDeliveryRoundByApplyNo($applyNo);

        $isCycleValid = true;
        // cycleData가 비어 있으면 유효성 검사를 생략
        if (!empty($cycleData)) {
            $cycleType = $applyDeliveryInfo['deliveryCycleType'];
            $cycle     = $applyDeliveryInfo['deliveryCycle'];
            $cycleDay  = $applyDeliveryInfo['deliveryCycleDay'];

            if ($cycleType === 'month') {
                $isCycleValid = (
                    isset($cycleData['monthCycle']) &&
                    in_array($cycle, $cycleData['monthCycle'])
                );
            } else {
                $isCycleValid = (
                    isset($cycleData['weekCycle']) &&
                    isset($cycleData['weekDayCycle']) &&
                    in_array($cycle, $cycleData['weekCycle']) &&
                    in_array($cycleDay, $cycleData['weekDayCycle'])
                );
            }
        }

        if (!$isCycleValid) {
            throw new \Exception('신청 불가한 상품입니다. 다시 선택해 주세요.', RegularGoodsAttribute::ERROR_REGULAR_GOODS_DELIVERY_DATA_CHANGE);
        }

        $isDeliveryRoundValid = true;
        if ($deliveryRoundsDisplayType !== 'all') {
            if ((int)$applyDeliveryInfo['maxDeliveryRound'] === 0) {
                $isDeliveryRoundValid = ((int)$maxDeliveryRounds === 0);
            } else {
                $isDeliveryRoundValid = ((int)$maxDeliveryRounds >= (int)$applyDeliveryInfo['maxDeliveryRound']);
            }
        }

        if (!$isDeliveryRoundValid) {
            throw new \Exception('신청 불가한 상품입니다. 다시 선택해 주세요.', RegularGoodsAttribute::ERROR_REGULAR_GOODS_DELIVERY_DATA_CHANGE);
        }
    }

    /**
     * 옵션 정보 유효성 검사
     *
     * @param int $optionSno
     * @return void
     */
    private function validateOption(int $optionSno)
    {
        $optionData = $this->goodsOptionRepository->findGoodsOptionByOptionSno($optionSno);

        if (empty($optionData)) {
            throw new \Exception('삭제된 상품입니다. 다시 선택해 주세요.', RegularGoodsAttribute::ERROR_REGULAR_GOODS_DELETE);
        }

        $isSoldOut = $optionData['optionSellFl'] !== 'y';
        $isOutOfStock = $optionData['stockFl'] === 'y' && $optionData['stockCnt'] <= 0;

        if ($isSoldOut || $isOutOfStock) {
            throw new \Exception("품절된 상품입니다. 다시 선택해주세요.", RegularGoodsAttribute::ERROR_REGULAR_GOODS_OUT_OF_STOCK);
        }
    }

    /**
     * 일반상품정보에 대한 추가상품 유효성 검사
     *
     * @param array $originGoodsInfo
     * @param array $addGoodsInfo
     * @return void
     */
    private function validateAddGoodsExists(array $originGoodsInfo, array $addGoodsNoList)
    {
        if (empty($addGoodsNoList)){
            return ;
        }

        if ($originGoodsInfo['addGoodsFl'] === 'n') {
            throw new \Exception('삭제된 추가상품입니다. 다시 선택해 주세요.', RegularAddGoodsAttribute::ERROR_REGULAR_ADD_GOODS_DELETE);
        }

        // 일반상품 설정에 등록된 추가상품 정보 파싱
        $settingGroup = json_decode(stripslashes($originGoodsInfo['addGoods']), true);

        // 선택한 추가상품이 일반상품 설정에 존재하는지 확인
        foreach ($addGoodsNoList as $addGoodsNo) {
            $existsInSetting = false;

            foreach ($settingGroup as $setting) {
                if (in_array($addGoodsNo, $setting['addGoods'], true)) {
                    $existsInSetting = true;
                    break;
                }
            }

            if (!$existsInSetting) {
                throw new \Exception('삭제된 추가상품입니다. 다시 선택해 주세요.', RegularAddGoodsAttribute::ERROR_REGULAR_ADD_GOODS_DELETE);
            }
        }

        // 선택한 추가상품의 원본 데이터 조회
        $addGoodsList = $this->addGoodsRepository->findAddGoodsByAddGoodsNoList($addGoodsNoList);

        // 선택한 추가상품의 원본 데이터가 존재하는지 확인
        if (count($addGoodsList) !== count($addGoodsNoList)) {
            throw new \Exception('삭제된 추가상품입니다. 다시 선택해 주세요.', RegularAddGoodsAttribute::ERROR_REGULAR_ADD_GOODS_DELETE);
        }

        // 품절 여부 확인
        foreach ($addGoodsList as $addGoods) {
            $isSoldOut = $addGoods['soldOutFl'] === 'y';
            $isOutOfStock = $addGoods['stockUseFl'] === '1' && $addGoods['stockCnt'] <= 0;

            if ($isSoldOut || $isOutOfStock) {
                throw new \Exception('품절된 추가 상품입니다. 다시 선택해주세요.', RegularAddGoodsAttribute::ERROR_REGULAR_ADD_GOODS_OUT_OF_STOCK);
            }
        }
    }

    /**
     * 현재 정기결제(배송) 상품의 유효성 여부 검사
     *
     * @param RegularGoodsChangeDTO $goodsChangeDto
     * @return void
     * @throws \Exception
     */
    public function validateCurrentRegularOrderGoodsInfo(RegularGoodsChangeDTO $goodsChangeDto)
    {
        $applyNo = $goodsChangeDto->getApplyNo();
        $regularGoodsNo = $goodsChangeDto->getRegularGoodsNo();
        $sessionType = $goodsChangeDto->getSessionType();
        $sessionSno = $goodsChangeDto->getSessionSno();

        // 본인 소유의 신청서인지 체크
        if ($sessionType === 'user' && !$this->regularOrderRepository->hasRegularOrderOwnership($applyNo, $sessionSno)) {
            throw new \Exception('본인 소유의 신청서만 변경 가능합니다.');
        }

        // 변경하려는 정기배송 상품 번호의 원본 상품 정보를 조회
        $originGoodsInfo = $this->getOriginGoodsInfo($applyNo, $regularGoodsNo);

        $this->validate($goodsChangeDto, $originGoodsInfo, false);
    }
}
