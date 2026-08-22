<?php
/*
 * Copyright (C) 2025 NHN COMMERCE. - All Rights Reserved
 *
 * Unauthorized copying or redistribution of this file in source and binary forms via any medium
 * is strictly prohibited.
 */

namespace Bundle\Component\RegularDelivery\RegularGoods;


use Framework\Http\Session\SessionManager as Session;
use Framework\Log\Logger;
use Illuminate\Database\Capsule\Manager;
use Repository\RegularDelivery\RegularOrder\RegularOrderGiftRepository;
use Repository\RegularDelivery\RegularGoods\RegularGiftPresentRepository;
use Repository\RegularDelivery\RegularGoods\RegularGiftPresentInfoRepository;
use Repository\Goods\GiftRepository;
use Component\RegularDelivery\RegularOrder\RegularOrderLog;
use Component\Page\Page;
use DTO\RegularDelivery\RegularOrder\RegularOrderGiftCreateDTO;
use DTO\RegularDelivery\RegularOrder\RegularOrderGiftUpdateDTO;
use DTO\RegularDelivery\RegularOrder\RegularOrderLogDTO;
use DTO\RegularDelivery\RegularOrder\RegularOrderGiftConditionDTO;
use DTO\RegularDelivery\RegularOrder\RegularOrderSelectedGiftDTO;
use Origin\Enum\RegularDelivery\RegularOrder\RegularOrderLogActionType;
use Origin\Exception\RegularDelivery\RegularGoods\GiftNotAvailableException;
use Origin\Enum\RegularDelivery\RegularGoods\RegularGoodsGift;

class RegularGift
{
    /**
     * @var Manager
     */
    private $manager;
    /**
     * @var Session
     */
    private $session;
    /**
     * @var Logger
     */
    private $logger;
    /**
     * @var GiftRepository
     */
    private $giftRepository;
    /**
     * @var RegularGiftPresentRepository
     */
    private $regularGiftPresentRepository;
    /**
     * @var RegularGiftPresentRepository
     */
    private $regularGiftPresentInfoRepository;
    /**
     * @var RegularOrderGiftRepository
     */
    private $regularOrderGiftRepository;
    /**
     * @var RegularOrderLog
     */
    private $regularOrderLog;


    public function __construct(
        Manager                          $manager,
        Session                          $session,
        Logger                           $logger,
        GiftRepository                   $giftRepository,
        RegularGiftPresentRepository     $regularGiftPresentRepository,
        RegularGiftPresentInfoRepository $regularGiftPresentInfoRepository,
        RegularOrderGiftRepository       $regularOrderGiftRepository,
        RegularOrderLog                  $regularOrderLog
    )
    {
        $this->manager = $manager;
        $this->session = $session;
        $this->logger = $logger;
        $this->giftRepository = $giftRepository;
        $this->regularGiftPresentRepository = $regularGiftPresentRepository;
        $this->regularGiftPresentInfoRepository = $regularGiftPresentInfoRepository;
        $this->regularOrderGiftRepository = $regularOrderGiftRepository;
        $this->regularOrderLog = $regularOrderLog;
    }

    /**
     * 신청서 번호를 바탕으로 관련 사은품 정보 조회
     *
     * @param RegularOrderGiftConditionDTO $regularOrderGiftConditionDTO
     * @return array
     */
    public function getRegularGoodsGiftPresentInfo(RegularOrderGiftConditionDTO $regularOrderGiftConditionDTO): array
    {
        // 페이지 별 리스트 사이즈
        $pageSizeNum = 10;
        $currentPageNum = $regularOrderGiftConditionDTO->getPage();

        if ($regularOrderGiftConditionDTO->getChangeGoodsFl() === 'n') {
            // 신청서 내 사은품 조건 번호, 상품 주문 및 추가 상품 수량 조회
            $regularOrderData = $this->regularOrderGiftRepository->findRegularGiftPresentSnoByApplyNo($regularOrderGiftConditionDTO->getApplyNo());

            // 중복 제거 & 총합 계산
            $uniqueAddGoods = [];

            foreach ($regularOrderData as $item) {
                if (!empty($item['regularAddGoodsNo'])) {
                    $uniqueAddGoods[$item['regularAddGoodsNo']] = $item['regularAddGoodsCnt'];
                }
            }

            $regularAddGoodsCnt = array_sum($uniqueAddGoods);

            // 신청서에 대한 사은품 정보 조회
            $regularOrderGiftData = $this->findRegularOrderGiftInfo($regularOrderData[0]['regularGiftPresentSno'], $regularOrderData[0]['regularGoodsCnt'],
                $regularAddGoodsCnt);
        } else {
            // 정기결제(배송) 상품 번호를 바탕으로 regularGiftPrsentSno 조회
            $regularGiftPresentSno = $this->regularGiftPresentRepository->findGiftPresentInfoByRegularGoodsNo($regularOrderGiftConditionDTO->getRegularGoodsNo())['sno'];

            // 신청서에 대한 사은품 정보 조회
            $regularOrderGiftData = $this->findRegularOrderGiftInfo($regularGiftPresentSno, $regularOrderGiftConditionDTO->getRegularGoodsCnt(),
                $regularOrderGiftConditionDTO->getRegularAddGoodsCnt());
        }

        $multiGiftData = [];
        if (!empty($regularOrderGiftData['multiGiftNoList'])) {
            $multiGiftData = $this->getGiftInfoByGiftNoList($regularOrderGiftData['multiGiftNoList'], $currentPageNum, $pageSizeNum, 65);
        }

        // 페이징
        $page = new Page($currentPageNum, $regularOrderGiftData['totalMultiGiftNum'], $regularOrderGiftData['totalMultiGiftNum'], $pageSizeNum);
        $page->setCache(true);

        return [
            'regularGiftPresentInfoSno' => $regularOrderGiftData['regularGiftPresentInfoSno'],
            'conditionTitle' => $regularOrderGiftData['conditionTitle'],
            'totalMultiGiftNum' => count($regularOrderGiftData['multiGiftNoList']),
            'currentMultiGiftNum' => count($multiGiftData),
            'selectCount' => $regularOrderGiftData['selectCount'],
            'giveCount' => $regularOrderGiftData['giveCount'],
            'multiGiftData' => $multiGiftData,
            'page' => $page
        ];
    }

    /**
     * 사은품 증정조건 번호, 상품 및 추가상품 구매수량을 바탕으로 총 사은품 갯수, 지급 수량, 페이징된 사은품 리스트 반환
     *
     * @param int $regularGiftPresentSno : 사은품 증정 조건 번호
     * @param int $regularGoodsCount : 구매 상품 수량
     * @param $regularAddGoodsCount : 구매 추가 상품 수량
     * @return array
     */
    private function findRegularOrderGiftInfo(int $regularGiftPresentSno, int $regularGoodsCount, $regularAddGoodsCount): array
    {
        // 사은품 조건 정보 조회
        $regularGiftPresentData = $this->regularGiftPresentRepository->findRegularGiftPresentConditionTypeAndInfo($regularGiftPresentSno);

        // 조회할 사은품 번호
        $multiGiftNoList = [];
        $selectCount = 0;
        $giveCount = 0;
        $regularGiftPresentInfoSno = null;
        if ($regularGiftPresentData[0]['conditionType'] === 'unconditional') {
            $multiGiftNoList = array_values(json_decode($regularGiftPresentData[0]['multiGiftNo'], true));
            $selectCount = $regularGiftPresentData[0]['selectCnt'];
            $giveCount = $regularGiftPresentData[0]['giveCnt'];
            $regularGiftPresentInfoSno = $regularGiftPresentData[0]['sno'];
        } else {
            $totalGoodsNum = $regularGoodsCount;
            if ($regularGiftPresentData[0]['addGoodsFl'] === 'y' && !empty($regularAddGoodsCount)) {
                $totalGoodsNum += $regularAddGoodsCount;
            }
            foreach ($regularGiftPresentData as $regularGiftPresent) {
                if ($regularGiftPresent['conditionStart'] <= $totalGoodsNum && $regularGiftPresent['conditionEnd'] >= $totalGoodsNum) {
                    $multiGiftNoList = array_values(json_decode($regularGiftPresent['multiGiftNo'], true));
                    $selectCount = $regularGiftPresent['selectCnt'];
                    $giveCount = $regularGiftPresent['giveCnt'];
                    $regularGiftPresentInfoSno = $regularGiftPresent['sno'];
                    break;
                }
            }
        }

        return [
            'conditionTitle' => $regularGiftPresentData[0]['conditionTitle'],
            'regularGiftPresentInfoSno' => $regularGiftPresentInfoSno,
            'totalMultiGiftNum' => count($multiGiftNoList),
            'selectCount' => $selectCount,
            'giveCount' => $giveCount,
            'multiGiftNoList' => $multiGiftNoList
        ];
    }

    /**
     * 사은품 번호 리스트를 바탕으로 사은품 정보 조회
     *
     * @param array $giftNoList : 사은품 번호 리스트
     * @param $currentPageNum : 현재 페이지 번호
     * @param $pageSizeNum : 페이지 별 리스트 사이즈
     * @param int $imageSize : 이미지 사이즈
     * @return array
     */
    private function getGiftInfoByGiftNoList(array $giftNoList, $currentPageNum, $pageSizeNum, int $imageSize): array
    {
        // 사은품 조회
        if (!empty($currentPageNum) && !empty($pageSizeNum)) {
            $multiGiftData = $this->giftRepository->findGiftByGiftNoListWithPaging($giftNoList, $currentPageNum, $pageSizeNum);
        } else {
            $multiGiftData = $this->giftRepository->findGiftInfoByGiftNo($giftNoList);
        }

        // 정기결제(배송) 상품의 사은품 상세 정보에 사은품 정보 세팅
        foreach ($multiGiftData as $index => $gift) {
            $multiGiftData[$index]['giftImage'] = gd_html_gift_image($gift['imageNm'], $gift['imagePath'], $gift['imageStorage'], $imageSize, $gift['giftNm']);
        }

        return $multiGiftData;
    }

    /**
     * 신청서 내 사은품 정보 변경
     *
     * @param RegularOrderGiftUpdateDTO $giftUpdateDTO : 사은품 정보 변경을 위해 필요한 데이터
     * @return void
     * @throws \Throwable
     */
    public function updateRegularGift(RegularOrderGiftUpdateDTO $giftUpdateDTO)
    {

        $applyNo = $giftUpdateDTO->getApplyNo();
        $regularGiftPresentInfoSno = $giftUpdateDTO->getRegularGiftPresentInfoSno();
        $giftNoList = $giftUpdateDTO->getGiftNoList();

        try {
            $this->manager->getConnection()->beginTransaction();

            // 기존 사은품 정보 조회
            $selectGiftData = $this->regularOrderGiftRepository->findGiftNameAndGiftNoByApplyNo($applyNo);

            // 기존 사은품 정보 세팅
            $giftNames = [];
            foreach ($selectGiftData as $gift) {
                $giftNames[] = $gift['giftNm'] . '(' . $gift['giftNo'] . ')';
            }
            $prevAction = implode(' ', $giftNames);

            // 기존 regularOrderGift 정보 삭제
            $this->regularOrderGiftRepository->deleteRegularOrderGiftByApplyNo($applyNo);

            // 사은품 전체 지급일 경우
            if ($giftUpdateDTO->getSelectCount() === 0) {
                // 신청서에 대한 사은품 정보 조회
                $multiGiftNo = $this->regularGiftPresentInfoRepository->findMultiGiftNoBySno($regularGiftPresentInfoSno)[0]['multiGiftNo'];
                $giftNoList = array_values(json_decode($multiGiftNo, true));
            }

            // 신규 사은품의 사은품번호 사은품명 조회
            $giftList = $this->giftRepository->getGiftNmByGiftNo($giftNoList);

            if (!empty($giftList)) {
                // 신규 정보 세팅
                $giftInfo = [];
                $giftNames = [];
                foreach ($giftList as $gift) {
                    $giftInfo[] = [
                        'applyNo' => $applyNo,
                        'giftNo' => $gift['giftNo'],
                        'regularGiftPresentInfoSno' => $regularGiftPresentInfoSno,
                    ];
                    $giftNames[] = $gift['giftNm'] . '(' . $gift['giftNo'] . ')';
                }
                $changeAction = implode(' ', $giftNames);

                // 신규 정보 추가
                $regularOrderGiftCreateDTO = new RegularOrderGiftCreateDTO($giftInfo);
                $insertFl = $this->regularOrderGiftRepository->insertRegularOrderGift($regularOrderGiftCreateDTO);
                if (!$insertFl) {
                    throw new \Exception('insertRegularOrderGift return value is False');
                }
            }

            // 변경 이력 로그 추가
            $regularOrderLogDTO = new RegularOrderLogDTO($applyNo, $giftUpdateDTO->getSessionType(), $giftUpdateDTO->getSessionSno(), RegularOrderLogActionType::GIFT_CHANGE, $prevAction, $changeAction);
            $this->regularOrderLog->insertRegularOrderLog($regularOrderLogDTO);

            $this->manager->getConnection()->commit();

        } catch (\Throwable $e) {
            $this->manager->getConnection()->rollBack();
            $this->logger->channel('regularOrderAdmin')->warning('정기결제 신청서 사은품 변경 실패', [$e->getMessage(), $e->getTrace()]);
            throw new \Exception('사은품 정보 변경에 실패하였습니다. 잠시 후 다시 시도해주세요.', RegularGoodsGift::ERROR_REGULAR_GIFT_DATA_CHANGE);
        }
    }

    /**
     * 프론트 상품 변경 > 사은품 선택을 통해 선택한 사은품에 대해 폼에 업데이트할 수 있도록 정리
     *
     * @param RegularOrderSelectedGiftDTO $regularOrderSelectedGiftDTO
     * @return array
     * @throws \Exception
     */
    public function getSelectedRegularGiftData(RegularOrderSelectedGiftDTO $dto): array
    {
        try {
            // 사은품 지급 상세조건 번호
            $regularGiftPresentInfoSno = $dto->getRegularGiftPresentInfoSno();
            // 선택한 사은품 번호
            $selectedGiftNums = $dto->getSelectGiftNum();
            // 선택 가능한 사은품 수 ( 0 : 전체지급, 그외 : 숫자만큼 지급)
            $selectedCount = $dto->getSelectCount();

            // 사은품 전체 지급일 경우, 사은품 지급 상세조건 내 사은품 전체 조회
            if ($selectedCount === 0) {
                $multiGiftJson = $this->regularGiftPresentInfoRepository->findMultiGiftNoBySno($regularGiftPresentInfoSno)[0]['multiGiftNo'] ?? '[]';
                $selectedGiftNums = array_values(json_decode($multiGiftJson, true) ?: []);
            }

            if (empty($selectedGiftNums)) {
                throw new \Exception('선택된 사은품 번호가 없습니다.');
            }

            // 선택한 사은품 정보 조회
            $giftDataList = $this->getGiftInfoByGiftNoList($selectedGiftNums, null, null, 65);

            // 전체 지급이 아닐 경우, 선택한 사은품 삭제 여부 확인
            if ($selectedCount !== 0 && count($giftDataList) !== $selectedCount) {
                throw new GiftNotAvailableException();
            }

            return [
                'regularGiftPresentInfoSno' => $regularGiftPresentInfoSno,
                'conditionTitle' => $dto->getConditionTitle(),
                'giveCnt' => $dto->getGiveCount(),
                'list' => $giftDataList
            ];
        } catch (GiftNotAvailableException $e) {
            $this->logger->channel('regularOrderAdmin')->warning('정기결제 신청서 사은품 선택 실패', [$e->getMessage(), $e->getTrace()]);
            throw new \Exception('삭제된 사은품입니다. 다시 선택해주세요.');
        } catch (\Throwable $e) {
            $this->manager->getConnection()->rollBack();
            $this->logger->channel('regularOrderAdmin')->warning('정기결제 신청서 사은품 변경 실패', [$e->getMessage(), $e->getTrace()]);
            throw new \Exception('사은품 정보 변경에 실패하였습니다. 잠시 후 다시 시도해주세요.');
        }
    }

    /**
     * 선택 가능한 정기결제(배송)사은품 외 기존에 신청한 사은품도 같이 반환
     *
     * @param RegularOrderGiftConditionDTO $regularOrderGiftConditionDTO
     * @return array
     */
    public function getRegularGoodsGiftPresentInfoWithPrevGiftInfo(RegularOrderGiftConditionDTO $regularOrderGiftConditionDTO)
    {
        // 변경 가능한 사은품 정보 조회
        $regularGoodsGiftData = $this->getRegularGoodsGiftPresentInfo($regularOrderGiftConditionDTO);

        // 기존 사은품 정보
        if ($regularOrderGiftConditionDTO->getChangeGoodsFl() === 'n') {
            // 신청서 내 상품 정보를 변경하지 않았을 경우, 기존 신청서 내 선택한 사은품 조회

            $prevGiftData = $this->regularOrderGiftRepository->findGiftNameAndGiftNoByApplyNo($regularOrderGiftConditionDTO->getApplyNo());
            $prevGiftData = array_column($prevGiftData, 'giftNo');

            $regularGoodsGiftData['prevGiftData'] = [];
            foreach ($regularGoodsGiftData['multiGiftData'] as $gift) {
                if (in_array($gift['giftNo'], $prevGiftData)) {
                    $regularGoodsGiftData['prevGiftData'][] = $gift;
                }
            }
        } else {
            // 신청서 내 상품 정보를 변경하였을 경우, 조회한 변경 가능 사은품 정보 활용

            if ($regularGoodsGiftData['selectCount'] !== 0) {
                $regularGoodsGiftData['prevGiftData'] = array_slice($regularGoodsGiftData['multiGiftData'], 0, $regularGoodsGiftData['selectCount']);
            } else {
                $regularGoodsGiftData['prevGiftData'] = $regularGoodsGiftData['multiGiftData'];
            }
        }

        return $regularGoodsGiftData;
    }

}
