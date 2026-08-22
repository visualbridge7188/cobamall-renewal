<?php

/*
 * Copyright (C) 2025 NHN COMMERCE. - All Rights Reserved
 *
 * Unauthorized copying or redistribution of this file in source and binary forms via any medium
 * is strictly prohibited.
 */

namespace Bundle\Component\RegularDelivery\RegularGoods;

use DateTime;
use DTO\RegularDelivery\RegularGoods\RegularGoodsAdminDTO;
use DTO\RegularDelivery\RegularGoods\RegularGoodsCreateDTO;
use DTO\RegularDelivery\RegularGoods\RegularGoodsDeleteDTO;
use Exception;
use Framework\Log\Logger;
use Illuminate\Database\Capsule\Manager;
use Origin\Enum\RegularDelivery\RegularGoods\RegularGoodsAttribute;
use Origin\Enum\RegularDelivery\RegularOrder\RegularOrderStatus;
use Repository\Goods\GoodsRepository;
use Repository\Goods\GoodsSearchRepository;
use Repository\RegularDelivery\RegularGoods\LogRegularGoodsRepository;
use Repository\RegularDelivery\RegularGoods\RegularGiftPresentInfoRepository;
use Repository\RegularDelivery\RegularGoods\RegularGiftPresentRepository;
use Repository\RegularDelivery\RegularGoods\RegularGoodsDeliveryCycleRepository;
use Repository\RegularDelivery\RegularGoods\RegularGoodsRepository;
use Repository\RegularDelivery\RegularOrder\RegularOrderGoodsRepository;
use Session;
use Throwable;
use Component\RegularDelivery\Exception\RegularGoodsApplyStatusValidateException;
use Component\RegularDelivery\RegularOrder\RegularOrderStatusChange;
use DTO\RegularDelivery\RegularOrder\RegularOrderStatusChangeDTO;
/**
 * 정기배송 등록/수정/삭제만을 위한 클래스로
 * create, update, validate, delete를 위한 함수만 퍼블릭으로 설정되어 있습니다.
 */
class RegularGoodsHandler
{
    /**
     * @var GoodsRepository
     */
    private $goodsRepository;

    /**
     * @var GoodsSearchRepository
     */
    private $goodsSearchRepository;

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
     * @var LogRegularGoodsRepository
     */
    private $logRegularGoodsRepository;

    /**
     * @var RegularOrderGoodsRepository
     */
    private $regularOrderGoodsRepository;

    /**
     * @var \Framework\Log\Channel
     */
    private $logger;

    /**
     * @var Manager
     */
    private $dbManager;

    /**
     * @var RegularOrderStatusChange
     */
    private $regularOrderStatusChange;

    public function __construct(
        GoodsRepository                     $goodsRepository,
        GoodsSearchRepository               $goodsSearchRepository,
        RegularGoodsRepository              $regularGoodsRepository,
        RegularGoodsDeliveryCycleRepository $regularGoodsDeliveryCycleRepository,
        RegularGiftPresentRepository        $regularGiftPresentRepository,
        RegularGiftPresentInfoRepository    $regularGiftPresentInfoRepository,
        LogRegularGoodsRepository           $logRegularGoodsRepository,
        RegularOrderGoodsRepository         $regularOrderGoodsRepository,
        Manager                             $dbManager,
        Logger                              $logger,
        RegularOrderStatusChange            $regularOrderStatusChange
    )
    {
        $this->goodsRepository = $goodsRepository;
        $this->goodsSearchRepository = $goodsSearchRepository;
        $this->regularGoodsRepository = $regularGoodsRepository;
        $this->regularGoodsDeliveryCycleRepository = $regularGoodsDeliveryCycleRepository;
        $this->regularGiftPresentRepository = $regularGiftPresentRepository;
        $this->regularGiftPresentInfoRepository = $regularGiftPresentInfoRepository;
        $this->logRegularGoodsRepository = $logRegularGoodsRepository;
        $this->regularOrderGoodsRepository = $regularOrderGoodsRepository;
        $this->dbManager = $dbManager;
        $this->logger = $logger->channel('regularGoods');
        $this->regularOrderStatusChange = $regularOrderStatusChange;

    }

    /**
     * 정기결제(배송) 상품에 대한 유효성 검사
     *
     * @param RegularGoodsAdminDTO $regularGoodsAdminDTO : 정기결제(배송) 상품 어드민 관련 공통 데이터 DTO
     * @return void
     * @throws Exception
     */
    public function validate(string $mode, RegularGoodsAdminDTO $regularGoodsAdminDTO)
    {
        $regularGoods = $regularGoodsAdminDTO->getRegularGoods();

        // 원상품이 삭제되지 않았는지 확인
        $deletedGoodsExistFl = $this->goodsRepository->existGoodsByGoodsNoList($regularGoodsAdminDTO->getTargetGoodsNoList());
        if ($deletedGoodsExistFl) {
            throw new Exception('삭제된 상품입니다. 정기결제(배송) 상품을 다시 선택해 주세요.');
        }

        // 판매 가능한 상품인지 확인
        $nonSellableGoodsExistFl = $this->goodsRepository->existNonSellableGoodsByGoodsNoList($regularGoodsAdminDTO->getTargetGoodsNoList(), date("Y-m-d H:i:s"), RegularGoodsAttribute::MIN_PRICE);
        if ($nonSellableGoodsExistFl) {
            throw new Exception('정기결제(배송) 상품 저장에 실패하였습니다. 새로고침 후 다시 시도해주세요.');
        }

        if ($mode === 'register') {
            // 이미 정기결제(배송) 상품으로 등록된 상품인지 확인
            $this->isRegisteredAsRegularProduct($regularGoodsAdminDTO->getTargetGoodsNoList());
        }

        // es_regularGoods에 저장할 데이터에 대해 유효성 검사
        $this->validateRegularGoods($regularGoods);

        // 정기결제(배송) 상품의 사은품 지급 설정이 활성화된 경우 추가 검사
        if ($regularGoods['giftPresentUseFl'] === 'y') {
            // 정기결제(배송) 상품 사은품 설정 정보
            $regularGiftPresent = $regularGoodsAdminDTO->getRegularGiftPresent();

            // es_regularGiftPresent에 저장할 데이터에 대해 유효성 검사
            $this->validateGiftPresent($regularGiftPresent);

            // es_regularGiftPresentInfo에 저장할 데이터에 대해 유효성 검사
            $this->validateRegularGiftPresentInfo($regularGiftPresent['conditionType'], $regularGoodsAdminDTO->getRegularGiftPresentInfo());
        }
    }

    /**
     * 정기결제(배송) 관련 데이터 등록
     *
     * @param RegularGoodsAdminDTO $regularGoodsAdminDTO : 정기결제(배송) 상품 어드민 관련 공통 데이터 DTO
     * @return void
     * @throws Exception
     */
    public function create(RegularGoodsAdminDTO $regularGoodsAdminDTO)
    {
        $regularGoods = $regularGoodsAdminDTO->getRegularGoods();

        $deliveryCycleType = $regularGoods['deliveryCycleType'];

        try {
            $this->dbManager->getConnection()->beginTransaction();

            // es_regularGoods에 저장할 배열형태의 DTO 구성
            $regularGoodsCreateDTO = $this->prepareRegularGoodsList($regularGoodsAdminDTO->getTargetGoodsNoList(), $regularGoods);

            $this->logger->info(__CLASS__ . ' Register regularGoods', $regularGoodsCreateDTO->getRegularGoodsCreateData());

            // es_regularGoods : 데이터 등록
            $this->regularGoodsRepository->insertRegularGoodsList($regularGoodsCreateDTO);

            // es_regularGoods에 등록한 데이터에 대해 sno(PK)와 goodsNo(일반상품 번호) 조회
            $regularGoodsSnoAndGoodsNoList = $this->regularGoodsRepository->findRegularGoodsNoByGoodsNoList($regularGoodsAdminDTO->getTargetGoodsNoList());

            $snoList = array_column($regularGoodsSnoAndGoodsNoList, 'sno');

            // 정기결제(배송) 상품 정보 중 배송 주기가 전체가 아닐 경우
            if ($deliveryCycleType !== 'all') {
                // es_regularGoodsDeliveryCycle : 등록 데이터 구성
                $regularGoodsDeliveryCycleList = $this->prepareRegularGoodsDeliveryCycle($snoList, $deliveryCycleType, $regularGoodsAdminDTO->getRegularGoodsDeliveryCycle());

                $this->logger->info(__CLASS__ . ' Register regularGoodsDeliveryCycle', $regularGoodsDeliveryCycleList);

                // es_regularGoodsDeliveryCycle : 데이터 등록
                $this->regularGoodsDeliveryCycleRepository->insertRegularGoodsDeliveryCycleList($regularGoodsDeliveryCycleList);

            }

            // 정기결제(배송) 상품에 대한 사은품을 지급할 경우
            if ($regularGoods['giftPresentUseFl'] === 'y') {
                // es_regularGiftPresent : 등록 데이터 구성
                $regularGiftPresentList = $this->prepareRegularGiftPresent($snoList, $regularGoodsAdminDTO->getRegularGiftPresent());

                $this->logger->info(__CLASS__ . ' Register regularGiftPresent', $regularGiftPresentList);

                // es_regularGiftPresent : 데이터 등록
                $this->regularGiftPresentRepository->insertRegularGiftPresentList($regularGiftPresentList);

                // es_regularGiftPresent : 등록한 데이터에서 sno(PK) 목록 조회
                $regularGiftPresentSnoList = $this->regularGiftPresentRepository->findSnoByRegularGoodsSnoList($snoList);

                // es_regularGiftPresentInfo : 등록 데이터 구성
                $regularGiftPresentInfoList = $this->prepareRegularGiftPresentInfoList($regularGiftPresentSnoList, $regularGoodsAdminDTO->getRegularGiftPresentInfo());

                $this->logger->info(__CLASS__ . ' Register regularGiftPresentInfo', $regularGiftPresentInfoList);

                // es_regularGiftPresentInfo : 데이터 등록
                $this->regularGiftPresentInfoRepository->insertRegularGiftPresentInfoList($regularGiftPresentInfoList);
            }

            // es_logRegularGoods : 등록 데이터 구성
            $logRegularGoodsList = $this->prepareLogRegularGoods('insert', null, $regularGoodsSnoAndGoodsNoList, $regularGoodsAdminDTO);

            $this->logger->info(__CLASS__ . ' Register logRegularGoods', $logRegularGoodsList);

            // es_logRegularGoods : 데이터 등록
            $this->logRegularGoodsRepository->insertLogRegularGoodsList($logRegularGoodsList);


            $this->dbManager->getConnection()->commit();
        } catch (Throwable $e) {
            $this->dbManager->getConnection()->rollBack();

            $this->logger->warning('Register regular goods error', [
                $e->getMessage(),
                $e->getTrace()
            ]);

            throw new Exception('정기결제(배송) 상품 저장에 실패하였습니다. 새로고침 후 다시 시도해주세요.');
        }
    }


    /**
     * 현재 배열($current)과 이전 배열($prev)의 차이를 양방향으로 비교하여
     * 두 배열 사이에 존재하지만 서로 다른 값들을 모두 찾아 반환
     *
     * @param array $current 현재 배열 (비교 대상)
     * @param array $prev 이전 배열 (비교 대상)
     * @return array 두 배열 중 한쪽에만 존재하는 값들의 배열 (중복 없이, 인덱스 재정렬됨)
     */
    private function compareRegularGoodsDataChanges(array $current, array $prev): array
    {
        $difference = [];

        foreach ($current as $key => $value) {
            if (is_array($value)) {
                if (!isset($prev[$key]) || !is_array($prev[$key])) {
                    $difference[$key] = $value;
                } else {
                    $newDiff = $this->compareRegularGoodsDataChanges($value, $prev[$key]);
                    if (!empty($newDiff)) {
                        $difference[$key] = $newDiff;
                    }
                }
            } else {
                if (!array_key_exists($key, $prev) || $prev[$key] !== $value) {
                    $difference[$key] = $value;
                }
            }
        }

        // $prev 기준으로 삭제 감지
        foreach ($prev as $key => $value) {
            if (!array_key_exists($key, $current)) {
                $difference[$key] = null;
            }
        }

        return $difference;
    }

    /**
     * 정기결제(배송) 관련 데이터 수정
     *
     * @param RegularGoodsAdminDTO $regularGoodsAdminDTO : 정기결제(배송) 상품 어드민 관련 데이터
     * @return void
     * @throws Exception
     */
    public function update(RegularGoodsAdminDTO $regularGoodsAdminDTO)
    {
        $sno = $regularGoodsAdminDTO->getSno();

        $regularGoods = $regularGoodsAdminDTO->getRegularGoods();

        $deliveryCycleType = $regularGoods['deliveryCycleType'];

        $prevData = json_decode($regularGoodsAdminDTO->getPrevData(), true);
        $prevData = $this->parseFormData($prevData);

        try {
            $this->dbManager->getConnection()->beginTransaction();

            $updatedRegularGoods = $this->compareRegularGoodsDataChanges($regularGoods, $prevData['regularGoods'] ?? []);

            // es_regularGoods에 대해 변경된 데이터가 있을 경우
            if (!empty($updatedRegularGoods)) {

                $updatedRegularGoodsKey = array_keys($updatedRegularGoods);

                // es_regularGoods에 대해 업데이트 된 정보만 데이터 구성
                $updateRegularGoods = $this->prepareModifyRegularGoods($regularGoodsAdminDTO->getTargetGoodsNoList()[0], $updatedRegularGoodsKey, $regularGoods);

                $this->logger->info(__CLASS__ . ' Modify logRegularGoods', $updateRegularGoods);

                // es_regularGodos에 대해 업데이트
                $this->regularGoodsRepository->updateRegularGoodsBySno($sno, $updateRegularGoods);
            }


            $updateRegularGoodsDeliveryCycle = $this->compareRegularGoodsDataChanges($regularGoodsAdminDTO->getRegularGoodsDeliveryCycle(), $prevData['regularGoodsDeliveryCycle'] ?? []);

            // es_regularGoodsDeliveryCycle에 대해 변경된 데이터가 있을 경우
            if (isset($updatedRegularGoods['deliveryCycleType']) || !empty($updateRegularGoodsDeliveryCycle)) {
                // 기존 es_regularGoodsDeliveryCycle에 대해 전체 삭제
                $this->regularGoodsDeliveryCycleRepository->deleteRegularGoodsDeliveryCycleByRegularGoodsSno($sno);

                // 정기결제(배송) 상품 정보 중 배송 주기가 전체가 아닐 경우
                if ($deliveryCycleType !== 'all') {

                    // es_regularGoodsDeliveryCycle : 등록할 데이터 구성
                    $regularGoodsDeliveryCycleList = $this->prepareRegularGoodsDeliveryCycle([$sno], $deliveryCycleType, $regularGoodsAdminDTO->getRegularGoodsDeliveryCycle());

                    $this->logger->info(__CLASS__ . ' Register regularGoodsDeliveryCycle', $regularGoodsDeliveryCycleList);

                    // es_regularGoodsDeliveryCycle :  데이터 등록
                    $this->regularGoodsDeliveryCycleRepository->insertRegularGoodsDeliveryCycleList($regularGoodsDeliveryCycleList);
                }
            }

            $updateRegularGiftPresent = $this->compareRegularGoodsDataChanges($regularGoodsAdminDTO->getRegularGiftPresent(), $prevData['regularGiftPresent'] ?? []);

            $updateRegularGiftPresentInfo = $this->compareRegularGoodsDataChanges($regularGoodsAdminDTO->getRegularGiftPresentInfo(), $prevData['gift'] ?? []);

            // es_regularGiftPresent나 es_regularGiftPresentInfo에 대해 변경된 데이터가 있을 경우
            if (isset($updatedRegularGoods['giftPresentUseFl']) || !empty($updateRegularGiftPresent) || !empty($updateRegularGiftPresentInfo)) {

                // 기존 es_regularGiftPresent에 대해 soft delete(delFl을 y로 변경)
                $this->regularGiftPresentRepository->updateDelFlByRegularGoodsSno($sno);

                if ($regularGoods['giftPresentUseFl'] === 'y') {

                    // es_regularGiftPresent : 등록할 데이터 구성
                    $regularGiftPresentList = $this->prepareRegularGiftPresent([$sno], $regularGoodsAdminDTO->getRegularGiftPresent());

                    $this->logger->info(__CLASS__ . ' Register regularGiftPresent', $regularGiftPresentList);

                    // es_regularGiftPresent : 데이터 등록
                    $this->regularGiftPresentRepository->insertRegularGiftPresentList($regularGiftPresentList);

                    // 등록한 es_regularGiftPresent의 sno(PK) 리스트 조회
                    $regularGiftPresentSnoList = $this->regularGiftPresentRepository->findSnoByRegularGoodsSnoList([$sno]);

                    // es_regularGiftPresentInfo : 등록 데이터 구성
                    $regularGiftPresentInfoList = $this->prepareRegularGiftPresentInfoList($regularGiftPresentSnoList, $regularGoodsAdminDTO->getRegularGiftPresentInfo());

                    $this->logger->info(__CLASS__ . ' Register regularGiftPresentInfo', $regularGiftPresentInfoList);

                    // es_regularGiftPresentInfo : 데이터 등록
                    $this->regularGiftPresentInfoRepository->insertRegularGiftPresentInfoList($regularGiftPresentInfoList);
                }

            }

            // 변경된 데이터가 있을 경우
            if (!empty($updatedRegularGoods) || !empty($updateRegularGoodsDeliveryCycle) || !empty($updateRegularGiftPresent) || !empty($updateRegularGiftPresentInfo)) {

                // 로그 데이터 추가를 위한 es_regularGoods 테이블의 sno와 goodsNo 정보를 구성
                $regularGoodsSnoAndGoodsNo = ['goodsNo' => $regularGoodsAdminDTO->getTargetGoodsNoList()[0], 'sno' => $sno];

                // es_logRegularGoods : 등록을 위한 데이터 구성
                $logRegularGoodsList = $this->prepareLogRegularGoods('modify', $prevData, [$regularGoodsSnoAndGoodsNo], $regularGoodsAdminDTO);

                $this->logger->info(__CLASS__ . ' Register logRegularGoods', $logRegularGoodsList);

                // es_logRegularGoods :데이터 등록
                $this->logRegularGoodsRepository->insertLogRegularGoodsList($logRegularGoodsList);
            }

            $this->dbManager->getConnection()->commit();

        } catch (Throwable $e) {
            $this->dbManager->getConnection()->rollBack();

            $this->logger->warning('Modify regular goods error', [
                $e->getMessage(),
                $e->getTrace()
            ]);

            throw new Exception('정기결제(배송) 상품 저장에 실패하였습니다. 새로고침 후 다시 시도해주세요.');
        }
    }

    /**
     * 이미 정기결제(배송)에 등록된 상품인지 확인
     *
     * @param array $targetGoodsNoList : 일반 상품 번호 리스트
     * @return void
     * @throws Exception
     */
    private function isRegisteredAsRegularProduct(array $targetGoodsNoList)
    {
        if ($this->regularGoodsRepository->hasRegularGoodsByGoodsNoList($targetGoodsNoList)) {
            throw new Exception('이미 등록된 정기결제(배송) 상품입니다.');
        }
    }

    /**
     * es_regularGoods 관련 유효성 검사
     *
     * @param array $regularGoods : es_regularGoods 관련 데이터
     * @return void
     */
    private function validateRegularGoods(array $regularGoods)
    {
        // 정기결제(배송) 상품 할인 설정 유효성 검사
        if ($regularGoods['discountUseFl'] === 'y') {
            $discountVal = trim($regularGoods['discountValue'] ?? '');
            $discountType = $regularGoods['discountType'] ?? '';

            if ($discountVal === '' || !is_numeric($discountVal)) {
                throw new Exception("정기결제(배송) 상품 할인 금액을 입력해주세요.");
            }

            $discountVal = (float)$discountVal;

            if ($discountVal < 0.00) {
                throw new Exception("정기결제(배송) 상품 할인 금액에 대해 0이나 양수인 값을 입력해주세요.");
            }

            if ($discountType === 'percent' && $discountVal > 100.00) {
                throw new Exception("정기결제(배송) 상품 할인율에 대해 100.00이하의 값을 입력해주세요.");
            }

            if ($discountType === 'fix' && $discountVal > 9999999999) {
                throw new Exception("정기결제(배송) 상품 할인가에 대해 9,999,999,999이하의 값을 입력해주세요.");
            }
        }

        // 최대 종료회차 유효성 검사
        if ($regularGoods['deliveryRoundsDisplayType'] === 'abled') {
            $maxDeliveryRounds = $regularGoods['maxDeliveryRounds'] ?? '';

            if ($maxDeliveryRounds === '' || !ctype_digit($maxDeliveryRounds)) {
                throw new Exception("최대 종료회차를 입력해주세요.");
            }

            $maxDeliveryRounds = (int)$maxDeliveryRounds;

            if ($maxDeliveryRounds < 2 || $maxDeliveryRounds > 50) {
                throw new Exception("최대 종료회차에 대해 2이상 50이하의 값을 입력해주세요.");
            }
        }
    }

    /**
     * es_regularGiftPrsent관련 유효성 검사
     *
     * @param array $regularGiftPresent : es_regularGiftPresent 관련 데이터
     * @return void
     */
    private function validateGiftPresent(array $regularGiftPresent)
    {
        // 정기 상품 사은품 지급 기간 사용 여부에 대한 유효성 검사
        if ($regularGiftPresent['periodUseFl'] === 'y') {
            $startDate = isset($regularGiftPresent['startDate']) ? trim($regularGiftPresent['startDate']) : '';
            $endDate = isset($regularGiftPresent['endDate']) ? trim($regularGiftPresent['endDate']) : '';

            if ($startDate === '' || $endDate === '') {
                throw new Exception("사은품 지급 기간을 입력해주세요.");
            }

            $endDateTime = new DateTime($endDate);
            $now = new DateTime();

            if ($endDateTime < $now) {
                throw new Exception("사은품 지급 기간 종료일이 현재 시간 보다 과거입니다.");
            }
        }

        // 정기 상품 사은품 지급 회차 조건에 대한 유효성 검사
        $roundsType = $regularGiftPresent['roundsType'];

        if ($roundsType === 'multiplier') {
            // 지급 회차 조건이 '배수 설정'일 경우
            $multiplierNum = isset($regularGiftPresent['multiplierNum']) ? trim($regularGiftPresent['multiplierNum']) : '';

            if ($multiplierNum === '' || !ctype_digit($multiplierNum)) {
                throw new Exception("사은품 지급 회차를 입력해주세요.");
            }

            $multiplierNum = (int)$multiplierNum;

            if ($multiplierNum < 1 || $multiplierNum > 50) {
                throw new Exception("사은품 지급 회차에 대해 1이상 50이하의 값을 입력해주세요.");
            }
        } elseif ($roundsType === 'directInput') {
            // 지급 회차 조건이 '직접 입력'일 경우
            $directInput = $regularGiftPresent['fixGiftRoundsNumArray'];

            if ($directInput === '') {
                throw new Exception("사은품 지급 회차를 입력해주세요.");
            }
        }
    }

    /**
     * es_regularGiftPresentInfo 관련 유효성 검사
     *
     * @param string $regularGiftPresentConditionType
     * @param array $regularGiftPresentInfoArray
     * @return void
     */
    private function validateRegularGiftPresentInfo(string $regularGiftPresentConditionType, array $regularGiftPresentInfoArray)
    {
        $conditionNumList = [];

        // 정기결제(배송) 사은품 증정 조건이 '무조건 지급'이 아닐 경우
        if ($regularGiftPresentConditionType !== 'unconditional') {
            foreach (array_keys($regularGiftPresentInfoArray['conditionStart']) as $key) {
                // 사은품 지급 조건 시작 범위 값
                $conditionStart = $regularGiftPresentInfoArray['conditionStart'][$key] ?? null;

                // 사은품 지급 조건 최대 범위 값
                $conditionEnd = $regularGiftPresentInfoArray['conditionEnd'][$key] ?? null;

                if (empty($conditionStart) || empty($conditionEnd)) {
                    throw new Exception("구매 상품 수량을 입력해 주세요.");
                }

                $conditionStart = (int)$conditionStart;
                $conditionEnd = (int)$conditionEnd;

                if ($conditionStart <= 0 || $conditionEnd <= 0) {
                    throw new Exception("구매 상품 수량은 0을 입력하실 수 없습니다.");
                }

                // 중복 범위인지 확인
                $isSameRange = $conditionStart === $conditionEnd;
                $isDuplicateRange = in_array($conditionStart, $conditionNumList, true) ||
                    in_array($conditionEnd, $conditionNumList, true);

                if ($isSameRange || $isDuplicateRange) {
                    throw new Exception("사은품 증정 조건 중, 구매 상품 수량 범위가 중복되었습니다.");
                }

                $conditionNumList[] = $conditionStart;
                $conditionNumList[] = $conditionEnd;
            }
        }
    }

    /**
     * regularGoods에 대한 데이터 가공
     *
     * @param array $targetGoodsNoList : 일반 상품 번호 리스트
     * @param array $regularGoodsArray : 정기결제(배송) 상품에 대해 입력된 데이터
     * @return RegularGoodsCreateDTO
     * @throws Exception
     */
    private function prepareRegularGoodsList(array $targetGoodsNoList, array $regularGoodsArray): RegularGoodsCreateDTO
    {
        $goodsTrunc = gd_policy('basic.trunc');

        // 정기결제(배송) 상품 등록 가능 여부 확인 및 일반 상품 가격 리스트를 얻어옴
        $priceList = $this->getGoodsPriceList($targetGoodsNoList);

        // 정기결제(배송) 상품 할인 정보에 대한 처리
        if ($regularGoodsArray['discountUseFl'] === 'y') {
            if ($regularGoodsArray['discountType'] === 'percent') {
                $regularGoodsArray['discountRate'] = round((float)$regularGoodsArray['discountValue'], 2);
            } else {
                $regularGoodsArray['discountPrice'] = round($regularGoodsArray['discountValue']);
            }
        } else {
            unset($regularGoodsArray['discountType']);
        }
        unset($regularGoodsArray['discountValue']);

        // 정기결제(배송) 상품 회차 정보에 대한 처리
        switch ($regularGoodsArray['deliveryRoundsDisplayType']) {
            case 'all':
                $regularGoodsArray['maxDeliveryRounds'] = null;
                break;
            case 'disabled':
                $regularGoodsArray['maxDeliveryRounds'] = 0;
                break;
            default:
                $regularGoodsArray['maxDeliveryRounds'] = (int)$regularGoodsArray['maxDeliveryRounds'];
        }

        // 관리자 메모에 대한 처리
        if (empty($regularGoodsArray['adminMemo'])) {
            $regularGoodsArray['adminMemo'] = null;
        }

        $regularGoodsCreateList = [];

        // 삽입할 정기결제(배송) 상품 정보 가공
        foreach ($targetGoodsNoList as $goodsNo) {
            // 정기결제가 계산 및 최소 결제가 이상인지 확인
            $regularPrice = $this->calculateAndValidateRegularPrice(
                $priceList[$goodsNo],
                $regularGoodsArray['discountUseFl'],
                $regularGoodsArray['discountType'],
                $regularGoodsArray['discountRate'],
                $regularGoodsArray['discountPrice'],
                (int)$goodsTrunc['goods']['unitPrecision'],
                $goodsTrunc['goods']['unitRound']
            );
            $regularGoodsArray['regularPrice'] = $regularPrice;
            $regularGoodsArray['goodsNo'] = $goodsNo;
            $regularGoodsArray['applyStatus'] = 'abled';
            $regularGoodsArray['delFl'] = 'n';

            $regularGoodsCreateList[] = $regularGoodsArray;
        }

        return new RegularGoodsCreateDTO($regularGoodsCreateList);
    }

    /**
     * es_regularGoods 중 수정할 데이터에 대해 구성
     *
     * @param int $goodsNo : 일반 상품 번호
     * @param array $updatedRegularGoodsKey : 변경된 데이터 키 리스트
     * @param array $regularGoodsArray : 수정 폼에 대한 데이터
     * @return array
     * @throws Exception
     */
    private function prepareModifyRegularGoods(int $goodsNo, array $updatedRegularGoodsKey, array $regularGoodsArray): array
    {
        $updateRegularGoods = [];

        $goodsTrunc = gd_policy('basic.trunc');

        $calculateRegularPriceFl = false;

        // 변경된 키값을 기반으로 업데이트할 데이터 구성
        foreach ($updatedRegularGoodsKey as $key) {
            switch ($key) {
                // 상품 할인 사용 여부 또는 할인 타입, 상품 할인 값(비율, 고정값) 변경 시
                case 'discountUseFl':
                case 'discountType':
                case 'discountValue':
                    $calculateRegularPriceFl = true;
                    break;

                // 정기결제(배송) 상품의 배송 주기 display 타입 변경 시
                case 'deliveryRoundsDisplayType':
                    // 변경된 정기결제(배송) 상품의 배송 주기 display 타입 세팅
                    $updateRegularGoods['deliveryRoundsDisplayType'] = $regularGoodsArray['deliveryRoundsDisplayType'];

                    // 변경된 정기결제(배송) 상품의 배송 주기 display 타입에 맞추어 maxDeliveryRounds 세팅
                    if ($regularGoodsArray['deliveryRoundsDisplayType'] === 'all') {
                        $updateRegularGoods['maxDeliveryRounds'] = null;
                    } elseif ($regularGoodsArray['deliveryRoundsDisplayType'] === 'disabled') {
                        $updateRegularGoods['maxDeliveryRounds'] = 0;
                    } else {
                        $updateRegularGoods['maxDeliveryRounds'] = (int)$regularGoodsArray['maxDeliveryRounds'];
                    }
                    break;

                case 'adminMemo' :
                    if (empty($regularGoodsArray['adminMemo'])) {
                        $updateRegularGoods['adminMemo'] = null;
                    } else {
                        $updateRegularGoods['adminMemo'] = $regularGoodsArray['adminMemo'];
                    }
                    break;
                default:
                    $updateRegularGoods[$key] = $regularGoodsArray[$key];
                    break;
            }
        }

        // 정기결제 가격 재계산이 필요한 경우
        if ($calculateRegularPriceFl) {
            $updateRegularGoods['discountUseFl'] = $regularGoodsArray['discountUseFl'];
            if ($regularGoodsArray['discountUseFl'] === 'n') {
                // 할인 미사용 시, 관련 할인 값 초기화
                $updateRegularGoods['discountType'] = null;
                $updateRegularGoods['discountRate'] = null;
                $updateRegularGoods['discountPrice'] = null;
            } else {
                $updateRegularGoods['discountType'] = $regularGoodsArray['discountType'];
                // 할인 사용 시, 타입에 따라 값 설정
                if ($regularGoodsArray['discountType'] === 'percent') {
                    $updateRegularGoods['discountRate'] = round((float)$regularGoodsArray['discountValue'], 2);
                    $updateRegularGoods['discountPrice'] = null;
                } else {
                    $updateRegularGoods['discountPrice'] = round($regularGoodsArray['discountValue']);
                    $updateRegularGoods['discountRate'] = null;
                }
            }

            // 일반 상품의 판매가 가져오기
            $price = $this->getGoodsPrice($goodsNo);

            // 정기결제 가격 계산 및 검증
            $updateRegularGoods['regularPrice'] = $this->calculateAndValidateRegularPrice(
                $price,
                $regularGoodsArray['discountUseFl'],
                $regularGoodsArray['discountType'],
                isset($updateRegularGoods['discountRate']) ? $updateRegularGoods['discountRate'] : null,
                isset($updateRegularGoods['discountPrice']) ? $updateRegularGoods['discountPrice'] : null,
                isset($goodsTrunc['goods']['unitPrecision']) ? (int)$goodsTrunc['goods']['unitPrecision'] : 0,
                isset($goodsTrunc['goods']['unitRound']) ? $goodsTrunc['goods']['unitRound'] : 0
            );
        }

        $updateRegularGoods['modDt'] = date("Y-m-d H:i:s");

        return $updateRegularGoods;
    }

    /**
     * 정기결제(배송) 등록 가능 여부 확인 및 일반 상품 가격을 return
     *
     * @param array $goodsNoList : 일반 상품 번호 리스트
     * @return array : 일반 상품 번호에 따른 상품 가격 array
     * @throws Exception
     */
    private function getGoodsPriceList(array $goodsNoList): array
    {
        $goodsPrices = $this->goodsRepository->findGoodsPricesByConditionsBySaveRegularGoodsPossibleConditions($goodsNoList, RegularGoodsAttribute::MIN_PRICE);
        // 모든 상품 번호가 결과에 있는지 확인
        if (count($goodsPrices) !== count($goodsNoList)) {
            throw new Exception('정기결제(배송)으로 등록 불가능한 상품');
        }
        return $goodsPrices;
    }

    /**
     * 정기결제가 계산 및 최소 결제가보다 높은지 확인
     *
     * @param float $price : 일반상품 가격
     * @param string $discountUseFl : 정기결제(배송) 상품 할인 여부
     * @param string|null $discountType : 정기결제(배송) 상품 할인 타입
     * @param float|null $discountRate : 정기결제(배송) 상품 비율
     * @param int|null $discountPrice : 정기결제(배송) 상품 할인가
     * @param int $unitPrecision : 자리수
     * @param string $unitRound : 올림 반올림 내림 방법
     * @return float : 정기결제가
     * @throws Exception
     */
    private function calculateAndValidateRegularPrice(
        float  $price,
        string $discountUseFl,
               $discountType,
               $discountRate,
               $discountPrice,
        int    $unitPrecision,
        string $unitRound
    ): float
    {
        $regularPrice = $this->calculateRegularPrice($price, $discountUseFl, $discountType, $discountRate, $discountPrice, $unitPrecision, $unitRound);

        if ($regularPrice < RegularGoodsAttribute::MIN_PRICE) {
            throw new Exception('정기결제(배송) 상품가가 최소 결제가보다 적음');
        }

        return $regularPrice;
    }

    /**
     * 정기결제가 계산
     *
     * @param float $price : 일반상품 가격
     * @param string $discountUseFl : 정기결제(배송) 상품 할인 여부
     * @param string|null $discountType : 정기결제(배송) 상품 할인 타입
     * @param float|null $discountRate : 정기결제(배송) 상품 비율
     * @param int|null $discountPrice : 정기결제(배송) 상품 할인가
     * @param int $unitPrecision : 자리수
     * @param string $unitRound : 올림 반올림 내림 방법
     * @return float : 정기결제가
     * @throws Exception
     */
    public function calculateRegularPrice(
        float  $price,
        string $discountUseFl,
               $discountType,
               $discountRate,
               $discountPrice,
        int    $unitPrecision,
        string $unitRound
    ): float
    {
        // 정기결제(배송) 상품 할인을 진행하지 않으므로 일반 상품 가격 반환
        if ($discountUseFl === 'n') {
            return $price;
        }

        switch ($discountType) {
            case 'percent':
                // 할인 방법이 'percent'일 경우
                $regularPrice = $price * ((100 - $discountRate) / 100);
                break;
            case 'fix':
                // 할인 방법이 'fix'일 경우
                $regularPrice = $price - $discountPrice;
                break;
        }

        // 유틸 절삭 정책 사용
        $regularPrice = gd_number_figure($regularPrice, $unitPrecision, $unitRound);

        return $regularPrice;
    }


    /**
     * es_regularGodosDeliveryCycle에 넣을 데이터
     *
     * @param array $regularGoodsSnoList
     * @param string $deliveryCycleType
     * @param array $regularGoodsDeliveryCycleArray
     * @return array
     */
    private function prepareRegularGoodsDeliveryCycle(array $regularGoodsSnoList, string $deliveryCycleType, array $regularGoodsDeliveryCycleArray): array
    {
        $regularGoodsDeliveryCycleList = [];

        if ($deliveryCycleType === 'month') {
            // 배송 주기가 월 단위일 경우
            foreach ($regularGoodsSnoList as $sno) {
                foreach ($regularGoodsDeliveryCycleArray['deliveryCycleMonth'] as $monthCycle) {
                    $regularGoodsDeliveryCycleList[] = [
                        'regularGoodsSno' => $sno,
                        'monthCycle' => $monthCycle,
                        'weekCycle' => null,
                        'weekDayCycle' => null,
                        'regDt' => date("Y-m-d H:i:s")
                    ];
                }
            }
        } else {
            // 배송 주기가 주 단위 일 경우
            foreach ($regularGoodsSnoList as $sno) {
                foreach ($regularGoodsDeliveryCycleArray['deliveryCycleWeek'] as $weekCycle) {
                    $regularGoodsDeliveryCycleList[] = [
                        'regularGoodsSno' => $sno,
                        'monthCycle' => null,
                        'weekCycle' => $weekCycle,
                        'weekDayCycle' => null,
                        'regDt' => date("Y-m-d H:i:s")
                    ];
                }

                foreach ($regularGoodsDeliveryCycleArray['deliveryCycleWeekDay'] as $weekDayCycle) {
                    $regularGoodsDeliveryCycleList[] = [
                        'regularGoodsSno' => $sno,
                        'monthCycle' => null,
                        'weekCycle' => null,
                        'weekDayCycle' => $weekDayCycle,
                        'regDt' => date("Y-m-d H:i:s")
                    ];
                }
            }
        }

        return $regularGoodsDeliveryCycleList;
    }

    /**
     * regularGiftPresent 및 regularGiftPresentInfo 저장
     *
     * @param array $regularGoodsSnoList : 저장된 regularGoods sno의 리스트
     * @param array $regularGiftPresentArray : 정기결제(배송) 사은품 관련 데이터
     * @return array
     */
    private function prepareRegularGiftPresent(array $regularGoodsSnoList, array $regularGiftPresentArray): array
    {
        if ($regularGiftPresentArray['periodUseFl'] === 'n') {
            // 사은품 지급 기간이 없을 경우
            $regularGiftPresentArray['startDate'] = null;
            $regularGiftPresentArray['endDate'] = null;
        }

        if ($regularGiftPresentArray['roundsType'] !== 'multiplier') {
            // 지급 회차 조건 중 배수 설정이 아닐 경우
            $regularGiftPresentArray['multiplierNum'] = null;
        }

        if ($regularGiftPresentArray['roundsType'] === 'directInput') {
            // 지급 회차 조건이 직접 입력일 경우
            $regularGiftPresentArray['fixGiftRoundsNum'] = json_encode(explode(',', $regularGiftPresentArray['fixGiftRoundsNumArray']), JSON_FORCE_OBJECT);
        } else {
            // 지급 회차 조건이 직접 입력이 아닐 경우
            $regularGiftPresentArray['fixGiftRoundsNum'] = null;
        }
        unset($regularGiftPresentArray['fixGiftRoundsNumArray']);

        if (!isset($regularGiftPresentArray['addGoodsFl'])) {
            $regularGiftPresentArray['addGoodsFl'] = 'n';
        }

        $regularGiftPresentArray['delFl'] = 'n';

        $regularGiftPresentList = [];

        // 저장한 regularGoods의 sno값에 대해 반복문 진행 및 데이터 세팅
        foreach ($regularGoodsSnoList as $regularGoodsSno) {
            $regularGiftPresentArray['regularGoodsSno'] = $regularGoodsSno;
            $regularGiftPresentArray['regDt'] = date("Y-m-d H:i:s");

            $regularGiftPresentList[] = $regularGiftPresentArray;
        }

        return $regularGiftPresentList;
    }

    /**
     * regularGiftPresentInfo에 대한 데이터 세팅
     *
     * @param array $regularGiftPresentSnoList : 저장한 regularGiftPresent의 sno 리스트
     * @param array $regularGiftPresentInfo : 정기결제(배송) 사은품 상세 정보
     * @return array
     */
    private function prepareRegularGiftPresentInfoList(array $regularGiftPresentSnoList, array $regularGiftPresentInfo): array
    {
        $regularGiftPresentInfoList = [];

        // giftPresentInfo의 각 인덱스별로 데이터를 순회
        foreach ($regularGiftPresentInfo['giftSno'] as $index => $giftSno) {
            // 해당 인덱스의 값들이 존재하는지 (안전하게 접근)
            $conditionStart = $regularGiftPresentInfo['conditionStart'][$index];
            $conditionEnd = $regularGiftPresentInfo['conditionEnd'][$index];

            $multiGiftNo = $regularGiftPresentInfo['multiGiftNo'][$index] ?? null;
            if ($multiGiftNo) {
                sort($multiGiftNo);
                $multiGiftNo = json_encode($multiGiftNo, JSON_FORCE_OBJECT);
            } else {
                $multiGiftNo = null;
            }

            $selectCnt = $regularGiftPresentInfo['selectCnt'][$index];
            $giveCnt = $regularGiftPresentInfo['giveCnt'][$index];

            // 전달받은 regularGiftPresent의 sno 배열의 각 값에 대해 데이터 생성
            foreach ($regularGiftPresentSnoList as $sno) {
                $regularGiftPresentInfoList[] = [
                    'regularGiftPresentSno' => $sno,
                    'conditionStart' => $conditionStart,
                    'conditionEnd' => $conditionEnd,
                    'multiGiftNo' => $multiGiftNo,
                    'selectCnt' => $selectCnt,
                    'giveCnt' => $giveCnt,
                    'regDt' => date("Y-m-d H:i:s")
                ];
            }
        }

        return $regularGiftPresentInfoList;
    }

    /**
     * 정기결제(배송)에 대한 log 세팅
     *
     * @param string $modeType : log mode
     * @param $prevData
     * @param array $regularGoodsSnoAndGoodsNoList : regularGoods의 sno값과 일반 상품 번호
     * @param RegularGoodsAdminDTO $regularGoodsAdminDTO : 정기결제(배송)에 대한 DTO
     * @return array
     */
    private function prepareLogRegularGoods(
        string               $modeType,
                             $prevData,
        array                $regularGoodsSnoAndGoodsNoList,
        RegularGoodsAdminDTO $regularGoodsAdminDTO
    ): array
    {
        $request = \App::getInstance('request');
        // 업데이트 데이타로 저장할 데이터
        $regularGoodsArray = [
            'regularGoods' => $regularGoodsAdminDTO->getRegularGoods(),
            'regularGoodsDeliveryCycle' => $regularGoodsAdminDTO->getRegularGoodsDeliveryCycle(),
            'regularGiftPresent' => $regularGoodsAdminDTO->getRegularGiftPresent(),
            'regularGiftPresentInfo' => $regularGoodsAdminDTO->getRegularGiftPresentInfo()
        ];

        if ($modeType === 'modify') {
            // 수정에 대한 로그일 경우, 이전 데이터(prevData)에 대해 구성
            $prevDataArray = [
                'regularGoods' => $prevData['regularGoods'],
                'regularGoodsDeliveryCycle' => $prevData['regularGoodsDeliveryCycle'],
                'regularGiftPresent' => $prevData['regularGiftPresent'],
                'regularGiftPresentInfo' => $prevData['gift']
            ];

            $prevDataArray['regularGoods']['goodsNo'] = $regularGoodsSnoAndGoodsNoList[0]['goodsNo'];
            $prevDataArray['regularGoods']['sno'] = $regularGoodsSnoAndGoodsNoList[0]['sno'];
        }

        $logRegularGoodsList = [];
        // 로그 데이터로 넣을 공통 데이터 구성
        $logRegularGoods = [
            'modeType' => $modeType,
            'managerSno' => Session::get('manager.sno'),
            'accessIp' => $request->getRemoteAddress(),
            'prevData' => $modeType === 'modify' ? json_encode($prevDataArray) : $prevData,
            'regDt' => date("Y-m-d H:i:s")
        ];

        foreach ($regularGoodsSnoAndGoodsNoList as $regularGoods) {
            // updatedData에 넣을 데이터 세팅
            $regularGoodsArray['regularGoods']['goodsNo'] = $regularGoods['goodsNo'];
            $regularGoodsArray['regularGoods']['sno'] = $regularGoods['sno'];

            // 로그 테이블에 추가할 데이터 세팅
            $logRegularGoods['regularGoodsSno'] = $regularGoods['sno'];
            $logRegularGoods['updateData'] = json_encode($regularGoodsArray);

            $logRegularGoodsList[] = $logRegularGoods;
        }

        return $logRegularGoodsList;
    }

    /**
     * 배열로 데이터에 대해 중첩된 배열 구조로 변환
     *
     * @param array $formData : 중첩된 배열 구조로 변환하고자 하는 배열 데이터
     * @return array
     */
    private function parseFormData(array $formData): array
    {
        $result = [];

        foreach ($formData as $key => $values) {
            if (!is_array($values)) {
                // 단일 값도 배열로 변환
                $values = [$values];
            }

            // 키에서 중첩 구조 파싱
            $keys = preg_split('/\]\[|\[|\]/', trim($key, '[]'));

            // 중첩된 배열로 변환
            $temp = &$result;
            foreach ($keys as $index => $k) {
                if (!isset($temp[$k])) {
                    $temp[$k] = [];
                }
                $temp = &$temp[$k];
            }

            // 값이 하나만 있으면 단일 값으로 설정
            $temp = (count($values) === 1) ? $values[0] : $values;
        }

        return $result;
    }

    /**
     * 정기결제(배송) 등록 가능 여부 확인 및 일반 상품 가격을 return
     *
     * @param int $goodsNo : 일반 상품 번호
     * @return int : 일반 상품 번호에 따른 상품 가격
     * @throws Exception
     */
    private function getGoodsPrice(int $goodsNo): int
    {
        $price = $this->goodsRepository->findOneGoodsPriceBySaveRegularGoodsPossibleConditions($goodsNo, RegularGoodsAttribute::MIN_PRICE);

        // 모든 상품 번호가 결과에 있는지 확인
        if (empty($price)) {
            throw new Exception('정기결제(배송)으로 등록 불가능한 상품');
        }

        return $price;
    }

    /**
     * 정기결제(배송)상품의 adminMemo를 update
     *
     * @param array $adminMemoData
     * @return void
     */
    public function updateAdminMemo(array $adminMemoData)
    {
        if ($adminMemoData['preAdminMemo'] != $adminMemoData['adminMemo']) {
            try {
                $this->dbManager->getConnection()->beginTransaction();

                $this->logger->info('Update regularGoods adminMemo to ' . $adminMemoData['adminMemo'] . ' : sno : ', $adminMemoData['regularGoodsSno']);

                // 관리자 메모 업데이트
                $this->regularGoodsRepository->updateAdminMemoBySno($adminMemoData['regularGoodsSno'], $adminMemoData['adminMemo']);

                $logRegularGoodsList = $this->prepareLogRegularGoodsBySnoList([$adminMemoData['regularGoodsSno']], 'modify', json_encode(['adminMemo' => $adminMemoData['preAdminMemo']]), json_encode(['adminMemo' => $adminMemoData['adminMemo']]));

                $this->logger->info(__CLASS__ . ' Register logRegularGoods', $logRegularGoodsList);

                $this->logRegularGoodsRepository->insertLogRegularGoodsList($logRegularGoodsList);

                $this->dbManager->getConnection()->commit();
            } catch (Throwable $e) {
                $this->dbManager->getConnection()->rollBack();

                $this->logger->warning('Modify regular goods error', [
                    $e->getMessage(),
                    $e->getTrace()
                ]);

                throw new Exception('저장에 실패하였습니다.');

            }
        }
    }

    /**
     * 정기결제(배송) 상품 삭제
     *
     * @param RegularGoodsDeleteDTO $regularGoodsDeleteDTO : 삭제할 정기 결제(배송) 상품의 sno list
     * @return bool : 정기결제(배송) 상품 삭제 및 신청서 변경 성공 여부
     */
    public function deleteRegularGoods(RegularGoodsDeleteDTO $regularGoodsDeleteDTO): bool
    {
        try {
            $this->dbManager->getConnection()->beginTransaction();
            $regularGoodsSnoList = $regularGoodsDeleteDTO->getRegularGoodsSnoList();
            $modDt = date("Y-m-d H:i:s");

            // 배송방법 노출여부가 "정기 배송"인 일반상품 번호 조회
            $goodsNoList = $this->regularGoodsRepository->findSnoBySnoListAndDeliveryType($regularGoodsSnoList, RegularGoodsAttribute::DELIVERY_REGULAR);
            $goodsNoList = array_column($goodsNoList, 'goodsNo');

            $this->logger->info('Delete regularGoods : snoList : ', $regularGoodsSnoList);

            // regularGoods softDelete
            $this->regularGoodsRepository->deleteRegularGoodsBySno($regularGoodsSnoList, $modDt);

            $this->logger->info('Update displayFl And sellFl to "n" by goodsNoList ', $goodsNoList);

            // 원상품의 PC/MO 판매상태 및 노출상태를 각각 '판매안함'과 '노출안함'으로 변경
            $this->goodsRepository->updateDisplayAndSellByGoodsNo($goodsNoList, date("Y-m-d H:i:s"));
            $this->goodsSearchRepository->updateDisplayAndSellByGoodsNo($goodsNoList, date("Y-m-d H:i:s"));

            // 해지할 신청서에 대한 applyNo, applyStatus 조회
            $orderList = $this->regularOrderGoodsRepository->findApplyStatusByRegularGoodsNoList($regularGoodsSnoList);

            // 해지 처리가 되지 않은 신청사만 필터링
            $orderList = array_filter($orderList, function ($row) {
                return !in_array($row['applyStatus'], RegularOrderStatus::getInactiveStatus(), true);
            });

            // RegularOrderStatusChange를 사용하여 신청서 해지 공통 처리
            $applyNoList = array_column($orderList, 'applyNo');
            if (!empty($applyNoList)) {
                $statusChangeDTO = new RegularOrderStatusChangeDTO([
                    'applyNoList' => $applyNoList,
                    'updateStatus' => RegularOrderStatus::SYSTEM_INACTIVE,
                    'sessionType' => '',
                    'sessionSno' => 0,
                    'reason' => '정기배송 상품 판매 불가로 인한 자동 해지'
                ]);
                
                $this->regularOrderStatusChange->updateApplyStatus($statusChangeDTO);
            }

            $this->logger->info(__METHOD__ .' delete regular goods complete', [
                'regularGoodsSnoList' => $regularGoodsSnoList,
                'modifier' => $regularGoodsDeleteDTO->getModifier(),
                'modifierNo' => $regularGoodsDeleteDTO->getModifierNo()
            ]);

            $this->dbManager->getConnection()->commit();

            return true;
        } catch (Throwable $e) {
            $this->dbManager->getConnection()->rollBack();

            $this->logger->warning('Regular goods delete Error', [
                $e->getMessage(),
                $e->getTrace()
            ]);

            return false;
        }
    }

    /**
     * sno 값을 기준으로 신청 상태값 변경
     *
     * @param array $snoList : 신청 상태를 변경할 sno
     * @param string $applyStatus : 변경할 신청 상태 값
     * @return string : 모달창에 띄울 문구
     * @throws \Exception
     */
    public function updateApplyStatusBySno(array $snoList, string $applyStatus): string
    {

        // 최소 가격
        $minPrice = RegularGoodsAttribute::MIN_PRICE;
        // 변경 시각
        $modDt = date("Y-m-d H:i:s");

        // 신청 상태를 'abled'로 변경 전 조건 확인
        if ($applyStatus === RegularGoodsAttribute::ABLED) {

            if ($this->regularGoodsRepository->hasRegularGoodsBelowMinPriceBySno($snoList, $minPrice)) {
                return 'errorMinPrice';
            }

            if ($this->regularGoodsRepository->canUpdateApplyStatusByRegularGoodsNoList($snoList, $modDt)) {
                return 'errorStatusUpdateNotAllowed';
            }
        }

        $oppositeApplyStatus = $applyStatus === RegularGoodsAttribute::ABLED ? RegularGoodsAttribute::DISABLED : RegularGoodsAttribute::ABLED;

        // applyStatus를 변경해야하는 sno 조회
        $snoListToChange = $this->regularGoodsRepository->findSnoListByApplyStatus($snoList, $oppositeApplyStatus);
        $snoListToChange = array_column($snoListToChange, 'sno');

        if (!empty($snoListToChange)) {
            try {
                $this->dbManager->getConnection()->beginTransaction();

                $this->logger->info('Update regularGoods applyStatus to ' . $applyStatus . ' : snoList : ', $snoList);

                // 신청 상태 변경
                $this->regularGoodsRepository->updateApplyStatusBySno($snoListToChange, $applyStatus, $modDt);

                $logRegularGoodsList = $this->prepareLogRegularGoodsBySnoList($snoListToChange, 'modify', json_encode(['applyStatus' => $oppositeApplyStatus]), json_encode(['applyStatus' => $applyStatus]));

                $this->logger->info(__CLASS__ . ' Register logRegularGoods', $logRegularGoodsList);

                // es_logRegularGoods : 데이터 등록
                $this->logRegularGoodsRepository->insertLogRegularGoodsList($logRegularGoodsList);

                $this->dbManager->getConnection()->commit();
            } catch (Throwable $e) {
                $this->dbManager->getConnection()->rollBack();

                $this->logger->warning('Regular goods update applyStatus error', [
                    $e->getMessage(),
                    $e->getTrace()
                ]);

                return 'error';
            }
        }

        return 'success';
    }

    /**
     * RegularGoodsSno외 데이터가 모두 동일할 경우, 등록한 logRegularGoods를 반환
     *
     * @param array $snoList : log를 추가하고자 하는 es_regularGoods의 sno 리스트
     * @param string $modeType : log의 mode
     * @param string $prevData : 변경 전 데이터
     * @param string $updateData : 변경 후 데이터
     * @return array
     */
    public function prepareLogRegularGoodsBySnoList(array $snoList, string $modeType, string $prevData, string $updateData): array
    {
        $request = \App::getInstance('request');

        $logRegularGoodsList = [];

        foreach ($snoList as $sno) {
            $logRegularGoodsList[] = [
                'regularGoodsSno' => $sno,
                'modeType' => $modeType,
                'managerSno' => Session::get('manager.sno'),
                'accessIp' => $request->getRemoteAddress(),
                'prevData' => $prevData,
                'updateData' => $updateData,
                'regDt' => date('Y-m-d H:i:s')
            ];
        }

        return $logRegularGoodsList;
    }


    /**
     * 상품 신청 가능 여부 확인 및 상태 업데이트
     *
     * @param int $goodsNo
     * @return void
     */
    public function validateRegularGoodsApplyStatus(int $goodsNo)
    {
        $regularGoods = $this->regularGoodsRepository->findRegularGoodsDetailByGoodsNo($goodsNo);
        try {
            // 정기결제(배송) 상품 정보가 없을 경우
            if (empty($regularGoods)) {
                return;
            }

            // 재고 부족 시 신청불가 처리
            if ($regularGoods['stockFl'] === 'y' && $regularGoods['totalStock'] < 1) {
                throw new RegularGoodsApplyStatusValidateException('재고 부족으로 인한 정기결제(배송) 상품 신청 불가 처리');
            }

            // 판매 종료일이 지났다면 신청불가 처리
            if (!empty($regularGoods['salesEndYmd']) && $regularGoods['salesEndYmd'] !== '0000-00-00 00:00:00' && $regularGoods['salesEndYmd'] < date("Y-m-d H:i:s")) {
                throw new RegularGoodsApplyStatusValidateException('판매 종료일이 지남에 따라 정기결제(배송) 상품 신청 불가 처리');
            }

        } catch (RegularGoodsApplyStatusValidateException $e) {
            $this->logger->warning('Regular goods applyStatus validate failed', [
                $e->getMessage(),
                $e->getTrace()
            ]);

            // 유효성 검사 실패로 인한, 신청중지 처리
            $this->updateRegularGoodsAsDisabledAndGoodsInfo($regularGoods);
        } catch (Throwable $e) {
            $this->logger->warning('Regular goods applyStatus validate failed', [
                $e->getMessage(),
                $e->getTrace()
            ]);
        }
    }

    /**
     * 정기배송 상품을 신청불가 상태로 업데이트하고 관련 신청서까지 자동 해지 처리
     *
     * @param array $regularGoods
     * @return void
     */
    private function updateRegularGoodsAsDisabledAndGoodsInfo(array $regularGoods)
    {
        $snoList = [$regularGoods['sno']];
        $goodsNo = $regularGoods['goodsNo'];
        $now = date("Y-m-d H:i:s");

        try {
            $this->dbManager->getConnection()->beginTransaction();

            // 상태 변경 로그
            $this->logger->info('[RegularGoods] Updating applyStatus to DISABLED', [
                'snoList' => $snoList,
                'goodsNo' => $goodsNo,
            ]);

            // applyStatus 상태 변경
            $this->regularGoodsRepository->updateApplyStatusBySno($snoList, RegularGoodsAttribute::DISABLED, $now);

            // 변경 로그 기록
            $before = ['applyStatus' => RegularGoodsAttribute::ABLED];
            $after = ['applyStatus' => RegularGoodsAttribute::DISABLED];
            $logData = $this->prepareLogRegularGoodsBySnoList($snoList, 'modify', json_encode($before), json_encode($after));

            $this->logger->info('[RegularGoods] Logging applyStatus change', $logData);
            $this->logRegularGoodsRepository->insertLogRegularGoodsList($logData);

            // 정기배송 상품이라면 진열/판매 상태도 비활성화
            if ($regularGoods['deliveryType'] === 'regular') {
                $this->goodsRepository->updateDisplayAndSellByGoodsNo([$goodsNo], $now);
                $this->goodsSearchRepository->updateDisplayAndSellByGoodsNo([$goodsNo], $now);

                // 관련된 정기주문 신청서 상태 해지 처리
                $this->updateRegularOrderStatusBySystemInactive($snoList);
            }

            $this->dbManager->getConnection()->commit();
        } catch (Throwable $e) {
            $this->dbManager->getConnection()->rollBack();

            $this->logger->warning('[RegularGoods] Failed to update applyStatus', [
                'message' => $e->getMessage(),
                'trace' => $e->getTrace()
            ]);
        }
    }

    /**
     * 해당 정기배송 상품과 연결된 신청서를 자동 해지 처리
     *
     * @param array $snoList
     * @return void
     */
    private function updateRegularOrderStatusBySystemInactive(array $snoList)
    {
        $applyList = $this->regularOrderGoodsRepository->findApplyStatusByRegularGoodsNoList($snoList);

        // 해지 처리가 되지 않은 신청사만 필터링
        $applyList = array_filter($applyList, function ($row) {
            return !in_array($row['applyStatus'], RegularOrderStatus::getInactiveStatus(), true);
        });

        if (empty($applyList)) {
            $this->logger->info('[RegularGoods] No active apply records found to deactivate.');
            return;
        }

        // RegularOrderStatusChange를 사용하여 신청서 해지 공통 처리
        $applyNoList = array_column($applyList, 'applyNo');
        $statusChangeDTO = new RegularOrderStatusChangeDTO([
            'applyNoList' => $applyNoList,
            'updateStatus' => RegularOrderStatus::SYSTEM_INACTIVE,
            'sessionType' => '',
            'sessionSno' => 0,
            'reason' => '정기배송 상품 판매 불가로 인한 자동 해지'
        ]);
        
        $this->regularOrderStatusChange->updateApplyStatus($statusChangeDTO);

        $this->logger->info('[RegularGoods] Automatically deactivated related apply records', [
            'applyNoList' => $applyNoList,
        ]);
    }
}
