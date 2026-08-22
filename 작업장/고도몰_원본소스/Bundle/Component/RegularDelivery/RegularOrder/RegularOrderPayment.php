<?php
/*
 * Copyright (C) 2025 NHN COMMERCE. - All Rights Reserved
 *
 * Unauthorized copying or redistribution of this file in source and binary forms via any medium
 * is strictly prohibited.
 */

namespace Bundle\Component\RegularDelivery\RegularOrder;

use Repository\RegularDelivery\RegularOrder\RegularOrderRepository;
use Repository\RegularDelivery\RegularOrder\RegularOrderGoodsRepository;
use Framework\Security\Encryptor;
use DTO\RegularDelivery\RegularOrder\RegularOrderLogDTO;
use DTO\RegularDelivery\RegularOrder\RegularOrderPaymentCardUpdateDTO;
use Component\RegularDelivery\RegularOrder\RegularOrderLog;
use Origin\Enum\RegularDelivery\RegularOrder\RegularOrderLogActionType;
use Origin\DTO\Payment\PgCardDTO;
use Origin\Repository\Payment\PgCardInfoRepositoryInterface;
use Framework\Log\Logger;
use Illuminate\Database\Capsule\Manager;

/**
 * 정기결제 결제 관련
 */
class RegularOrderPayment
{
    /**
     * 최대 카드 등록 개수
     */
    const MAX_CARD_COUNT = 5;

    /**
     * @var Encryptor
     */
    private $encryptor;

    /**
     * @var Manager
     */
    private $dbManager;

    /**
     * @var Logger
     */
    private $logger;

    /**
     * @var PgCardInfoRepositoryInterface
     */
    private $cardInfoRepository;

    /**
     * @var RegularOrderRepository
     */
    private $regularOrderRepository;

    /**
     * @var RegularOrderGoodsRepository
     */
    private $regularOrderGoodsRepository;

    /**
     * @var RegularOrderLog
     */
    private $regularOrderLog;

    public function __construct(
        Encryptor $encryptor,
        Manager $dbManager,
        Logger $logger,
        PgCardInfoRepositoryInterface $cardInfoRepository,
        RegularOrderRepository $regularOrderRepository,
        RegularOrderGoodsRepository $regularOrderGoodsRepository,
        RegularOrderLog $regularOrderLog
    )
    {
        $this->encryptor = $encryptor;
        $this->dbManager = $dbManager;
        $this->logger = $logger;
        $this->cardInfoRepository = $cardInfoRepository;
        $this->regularOrderRepository = $regularOrderRepository;
        $this->regularOrderGoodsRepository = $regularOrderGoodsRepository;
        $this->regularOrderLog = $regularOrderLog;
    }

    /**
     * 사용자 메인 카드 정보 조회
     *
     * @param int $memNo
     * @return array
     */
    public function getMainCard(int $memNo): array
    {
        $cardData = $this->cardInfoRepository->findMainCardInfo($memNo);
        if ($cardData === null) {
            return [];
        }

        $cardInfo = [
            'sno' => $cardData->sno,
            'cardNm' => $cardData->cardNm,
            'cardNo' => $cardData->cardNo,
            'decryptCardNo' => $this->encryptor->decrypt($cardData->cardNo),
            'mainFl' => $cardData->mainFl,
            'regDt' => $cardData->regDt,
        ];

        return (new PgCardDTO($cardInfo))->toArray();
    }

    /**
     * 등록된 사용자 카드 정보
     *
     * @param int $memNo
     * @return array
     */
    public function getCardList(int $memNo): array
    {
        $cardData = $this->cardInfoRepository->findCardInfoByMemNoOrderByPriority($memNo);

        $cardList = [];
        foreach ($cardData as $cardInfo) {
            $cardInfo['decryptCardNo'] = $this->encryptor->decrypt($cardInfo['cardNo']); // 암호화 된 카드번호 복호화
            $cardInfoDto = new PgCardDTO($cardInfo);

            $cardList[] = $cardInfoDto->toArray();
        }

        return $cardList;
    }

    /**
     * 이용중 또는 일시정지에 사용중인 카드 데이터를 추가로 조회해서 카드 리스트 조회
     *
     * @param int $memNo
     * @return array
     */
    public function getCardListWithUsedStatus(int $memNo): array
    {
        $cardList = $this->getCardList($memNo);

        // 정기배송 신청 중인 카드 목록 조회
        $regularPaymentCardNoList = $this->regularOrderRepository->findRegularPaymentCardNoList($memNo);
        $cardNoList = array_column($regularPaymentCardNoList, 'cardNo');

        return array_map(function ($item) use ($cardNoList) {
            // 정기결제 사용여부 확인
            $regularPaymentFl = in_array($item['encryptCardNo'], $cardNoList) ? 'y' : 'n';

            return [
                'no' => $item['sno'],
                'cardNm' => $item['cardName'],
                'cardNo' => $item['cardNo'],
                'encryptCardNo' => $item['encryptCardNo'],
                'cardLastNum' => $item['cardLastNum'],
                'regDt' => date('Y-m-d', strtotime($item['regDt'])),
                'mainCard' => $item['mainFl'],
                'regularPayment' => $regularPaymentFl
            ];
        }, $cardList);
    }

    /**
     * 메인카드 변경 및 우선순위 변경
     *
     * @param int $memNo
     * @param array $cardOrder
     * @throws \Throwable
     */
    public function changeMainCardAndPriority(int $memNo, array $cardOrder)
    {
        // cardOrder 배열의 첫번째 요소가 메인카드
        $mainCardSno = $cardOrder[0]['sno'];
        $this->dbManager->getConnection()->beginTransaction();
        try {
            // 메인카드 변겅
            $this->cardInfoRepository->updateMainCardFl($memNo, $mainCardSno);
            // 우선순위 변경
            foreach ($cardOrder as $card) {
                $this->cardInfoRepository->updateCardPriority($memNo, $card['sno'], $card['priority']);
            }
            $this->dbManager->getConnection()->commit();
        } catch (\Throwable $e) {
            $this->dbManager->getConnection()->rollBack();
            throw $e;
        }
    }

    /**
     * 카드 삭제, 삭제 후 우선순위 재조정
     *
     * @param int $memNo
     * @param int $cardSno
     * @throws \Throwable
     */
    public function deleteCard(int $memNo, int $cardSno)
    {
        $this->dbManager->getConnection()->beginTransaction();
        try {
            // 회원의 카드정보 모두 조회
            $cardInfoList = $this->cardInfoRepository->findCardInfoByMemNo($memNo);

            // 삭제할 카드의 priority 찾기
            $deletedCardPriority = null;
            foreach ($cardInfoList as $cardInfo) {
                if ($cardInfo['sno'] == $cardSno) {
                    $deletedCardPriority = $cardInfo['cardPriority'];
                    break;
                }
            }

            // 카드 삭제
            $this->cardInfoRepository->deleteCardByMemNoAndSno($memNo, $cardSno);

            // 삭제된 카드보다 priority가 높은 카드들의 priority 재조정
            if ($deletedCardPriority !== null) {
                foreach ($cardInfoList as $cardInfo) {
                    if ($cardInfo['sno'] != $cardSno && $cardInfo['cardPriority'] > $deletedCardPriority) {
                        $this->cardInfoRepository->updateCardPriority(
                            $memNo,
                            $cardInfo['sno'],
                            $cardInfo['cardPriority'] - 1
                        );
                    }
                }
            }

            $this->dbManager->getConnection()->commit();
        } catch (\Throwable $e) {
            $this->dbManager->getConnection()->rollBack();
            throw $e;
        }
    }

    /**
     * 회원의 결제 카드 리스트 조회
     *
     * @param int $memNo
     * @return array
     */
    public function getPaymentCardList(int $memNo): array
    {
        $paymentCardList = $this->cardInfoRepository->findCardInfoOrderByMainFlAndPriority($memNo);
        foreach ($paymentCardList as &$paymentCard) {
            $paymentCard['decryptCardNo'] = $this->encryptor->decrypt($paymentCard['cardNo']);
        }

        return $paymentCardList;
    }

    /**
     * 결제카드 변경
     *
     * @param RegularOrderPaymentCardUpdateDTO $paymentCardUpdateDTO
     * @return void
     * @throws \Exception
     */
    public function updateRegularOrderPaymentCard(RegularOrderPaymentCardUpdateDTO $paymentCardUpdateDTO)
    {
        try {
            $this->dbManager->getConnection()->beginTransaction();
            $applyNo = $paymentCardUpdateDTO->getApplyNo();
            $currentCardNo = $paymentCardUpdateDTO->getCurrentCardNo();
            $updatedCardNo = $paymentCardUpdateDTO->getUpdatedCardNo();
            $sessionType = $paymentCardUpdateDTO->getSessionType();
            $sessionSno = $paymentCardUpdateDTO->getSessionSno();

            if (empty($this->cardInfoRepository->findCardNmByCardNo($updatedCardNo))) {
                throw new \Exception('변경할 결제카드가 존재하지 않습니다.');
            }

            // 기존 카드 정보
            $beforeData = $this->getCardDataForLogging($currentCardNo);

            // 변경 카드 정보
            $afterData = $this->getCardDataForLogging($updatedCardNo);

            // 결제카드 정보 업데이트
            $this->regularOrderGoodsRepository->updateRegularOrderCardNoByApplyNo($applyNo, $updatedCardNo);

            // 로그 추가
            $logDto = new RegularOrderLogDTO($applyNo, $sessionType, $sessionSno, RegularOrderLogActionType::PAYMENT_CARD_CHANGE, $beforeData, $afterData);
            $this->regularOrderLog->insertRegularOrderLog($logDto);

            $this->dbManager->getConnection()->commit();
        } catch (\Throwable $e) {
            $this->dbManager->getConnection()->rollBack();
            $this->logger->channel('regularDelivery')->warning('결제카드 변경 실패', [$e->getMessage(), $e->getTrace()]);
            throw new \Exception('결제카드 변경 처리에 실패하였습니다. 잠시 후 다시 시도해주세요.');
        }

    }

    /**
     * 로그 처리용 카드 정보 생성
     *
     * @param string $cardNo
     * @return string
     */
    private function getCardDataForLogging(string $cardNo): string
    {
        $cardNm = $this->cardInfoRepository->findCardNmByCardNo($cardNo);
        $decryptedCardNo = $this->encryptor->decrypt($cardNo);
        return $cardNm . ' (' . $decryptedCardNo . ')';
    }

    /**
     * 정기배송 신청 여부 체크
     * 
     * @param int $memNo
     * @param int $cardSno
     * @return bool
     */
    public function hasActiveRegularOrderGoods(int $memNo, int $cardSno): bool
    {
        // cardNo 가져오기
        $cardInfo = $this->cardInfoRepository->findUsedCardByCardSnoAndMemNo($cardSno, $memNo);
        $cardNo = $cardInfo['cardNo'];

        // 정기배송 신청 여부 체크
        return $this->regularOrderGoodsRepository->existsActiveRegularOrderGoodsByCardNo($cardNo);
    }
}
