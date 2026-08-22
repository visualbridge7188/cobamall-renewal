<?php
/*
 * Copyright (C) 2025 NHN COMMERCE. - All Rights Reserved 
 *
 * Unauthorized copying or redistribution of this file in source and binary forms via any medium 
 * is strictly prohibited.
 */

namespace Bundle\Component\RegularDelivery\RegularOrder;

use Bundle\Repository\RegularDelivery\RegularOrder\RegularOrderDeliveryLogRepository;
use Component\RegularDelivery\RegularOrder\RegularOrderLog;
use DTO\RegularDelivery\RegularOrder\DeliveryCycleChangeDTO;
use DTO\RegularDelivery\RegularOrder\RegularOrderGoodsDeliveryInfoUpdateDTO;
use DTO\RegularDelivery\RegularOrder\RegularOrderLogDTO;
use Framework\Log\Logger;
use Illuminate\Database\Capsule\Manager;
use Origin\Enum\RegularDelivery\RegularGoods\DeliveryCycle;
use Origin\Enum\RegularDelivery\RegularOrder\RegularOrderLogActionType;
use Origin\Exception\Order\RegularOrder\DeliveryRoundUpdateFailException;
use Repository\RegularDelivery\RegularOrder\RegularOrderGoodsRepository;
use Component\RegularDelivery\Notification\RegularDeliveryNotificationSender;
use Util\Order\RegularOrderUtil;

class RegularOrderCycleChange
{
    /**
     * @var Manager
     */
    private $manager;
    /**
     * @var Logger
     */
    private $logger;
    /**
     * @var RegularOrderGoodsRepository
     */
    private $regularOrderGoodsRepository;
    /**
     * @var RegularOrderDeliveryLogRepository
     */
    private $regularOrderDeliveryLogRepository;
    /**
     * @var RegularOrderLog
     */
    private $regularOrderLog;
    /**
     * @var RegularDeliveryNotificationSender
     */
    private $notificationSender;

    public function __construct(
        Manager $manager,
        Logger $logger,
        RegularOrderGoodsRepository $regularOrderGoodsRepository,
        RegularOrderDeliveryLogRepository $regularOrderDeliveryLogRepository,
        RegularOrderLog $regularOrderLog,
        RegularDeliveryNotificationSender $notificationSender
    )
    {
        $this->manager = $manager;
        $this->logger = $logger;
        $this->regularOrderGoodsRepository = $regularOrderGoodsRepository;
        $this->regularOrderDeliveryLogRepository = $regularOrderDeliveryLogRepository;
        $this->regularOrderLog = $regularOrderLog;
        $this->notificationSender = $notificationSender;
    }

    /**
     * 신청서 배송주기 업데이트
     *
     * @param DeliveryCycleChangeDTO $deliveryCycleChangeDTO
     * @return void
     * @throws DeliveryRoundUpdateFailException
     * @throws \Throwable
     */
    public function updateRegularDeliveryCycle(DeliveryCycleChangeDTO $deliveryCycleChangeDTO)
    {
        $this->manager->getConnection()->beginTransaction();
        try {
            $applyNo = $deliveryCycleChangeDTO->getApplyNo();
            $maxDeliveryRound = $deliveryCycleChangeDTO->getMaxDeliveryRound();
            $deliveryCycleType = $deliveryCycleChangeDTO->getDeliveryCycleType();
            $deliveryCycle = $deliveryCycleChangeDTO->getDeliveryCycle();
            $deliveryCycleDay = $deliveryCycleChangeDTO->getDeliveryCycleDay();
            $sessionType = $deliveryCycleChangeDTO->getSessionType();
            $sessionSno = $deliveryCycleChangeDTO->getSessionSno();

            // 현재 배송회차 조회
            $currentDeliveryInfo = $this->regularOrderGoodsRepository->findCurrentDeliveryRoundByApplyNo($applyNo);
            $currentDeliveryRound = $currentDeliveryInfo['deliveryRound'];

            // 현재 배송회차보다 적은 배송회차면 안됨($maxDeliveryRound 0 은 무제한이라 체크 안함)
            if ($maxDeliveryRound !== 0 && $currentDeliveryRound >= $maxDeliveryRound) {
                throw new DeliveryRoundUpdateFailException('현재 진행된 회차보다 적은 배송회차를 선택할 수 없습니다.');
            }

            // 현재 배송회차에 따른 배송예정일,주문생성일 재 계산
            if ($currentDeliveryRound === 1) {
                $isFirst = true;
                // 1회차라면 신청서 생성일이 기준
                $baseDate = date('Y-m-d', strtotime($currentDeliveryInfo['regDt']));
            } else {
                $isFirst = false;
                $baseDate = $this->regularOrderDeliveryLogRepository->findDeliveryDueDateByApplyNoAndDeliveryRound($applyNo, $currentDeliveryRound-1);
            }

            $deliveryDate = RegularOrderUtil::calculateOrderDate(
                $baseDate,
                $deliveryCycleType,
                $deliveryCycle,
                $deliveryCycleDay,
                $isFirst
            );
            $deliveryDueDate = $deliveryDate[0];
            $orderCreateDate = $deliveryDate[1];

            // 변경되어야 하는 데이터
            $changeData = [
                'applyNo' => $applyNo,
                'deliveryCycleType' => $deliveryCycleType,
                'deliveryCycle' => $deliveryCycle,
                'deliveryCycleDay' => $deliveryCycleDay,
                'maxDeliveryRound' => $maxDeliveryRound,
            ];

            $this->logger->channel('regularDelivery')->info('배송주기 업데이트 신청, 현재 데이터 : ', $currentDeliveryInfo);
            $this->logger->channel('regularDelivery')->info('배송주기 업데이트 신청, 변경 데이터 : ', $changeData);
            $this->logger->channel('regularDelivery')->info('배송주기 업데이트 신청, 변경 회차(배송예정일, 주문생성일) : ', $deliveryDate);

            // 배송주기 정보 및 배송예정일,주문생성일 업데이트
            $updateDto = new RegularOrderGoodsDeliveryInfoUpdateDTO($changeData, $deliveryDueDate, $orderCreateDate);
            $this->regularOrderGoodsRepository->updateRegularOrderGoodsDeliveryInfoByApplyNo($updateDto);

            // 로그 저장
            // 배송주기 업데이트가 일어났다면
            if ($this->isDeliveryCycleChanged($currentDeliveryInfo, $changeData)) {
                $this->logDeliveryCycleChange($applyNo, $currentDeliveryInfo, $changeData, $sessionType, $sessionSno);
                $this->updateRegularDeliveryLog($applyNo, $currentDeliveryRound, $deliveryDueDate);
            }

            // 배송회차 업데이트가 일어났다면
            if ($currentDeliveryInfo['maxDeliveryRound'] !== $maxDeliveryRound) {
                $before = ($currentDeliveryInfo['maxDeliveryRound'] === 0 || $maxDeliveryRound === null)
                    ? '무제한'
                    : $currentDeliveryInfo['maxDeliveryRound'].'회차';
                $after = $maxDeliveryRound === 0 ? '무제한' : $maxDeliveryRound.'회차';

                $this->insertRegularOrderLog($applyNo, RegularOrderLogActionType::DELIVERY_ROUND_CHANGE, $before, $after, $sessionType, $sessionSno);
            }
            $this->manager->getConnection()->commit();
        } catch (\Throwable $e) {
            $this->manager->getConnection()->rollBack();
            throw $e;
        }

        // 배송 일정 변경 알림 발송
        $this->notificationSender->sendRegularDeliveryInfoChanged($changeData);
    }

    /**
     * 배송주기 변경이 일어났는지 체크
     * @param array $current
     * @param array $changeData
     * @return bool
     */
    private function isDeliveryCycleChanged(array $current, array $changeData): bool
    {
        return $current['deliveryCycleType'] !== $changeData['deliveryCycleType']
            || $current['deliveryCycle'] !== $changeData['deliveryCycle']
            || $current['deliveryCycleDay'] !== $changeData['deliveryCycleDay'];
    }

    /**
     * 배송주기 변경 있을 시 배송주기에 대한 로그 저장
     * @param int $applyNo
     * @param array $current
     * @param array $changeData
     * @param string $sessionType
     * @param int $sessionSno
     * @return void
     */
    private function logDeliveryCycleChange(int $applyNo, array $current, array $changeData, string $sessionType, int $sessionSno)
    {
        $beforeCycle = $this->formatCycle($current['deliveryCycleType'], (string) $current['deliveryCycle']);
        $afterCycle = $this->formatCycle($changeData['deliveryCycleType'], (string) $changeData['deliveryCycle']);

        $beforeCycleDay = $this->formatCycleDay($current['deliveryCycleType'], (string) $current['deliveryCycleDay']);
        $afterCycleDay = $this->formatCycleDay($changeData['deliveryCycleType'], (string) $changeData['deliveryCycleDay']);

        $beforeData = $beforeCycle.'/'.$beforeCycleDay;
        $afterData = $afterCycle.'/'.$afterCycleDay;

        $this->insertRegularOrderLog($applyNo, RegularOrderLogActionType::PERIOD_CHANGE, $beforeData, $afterData, $sessionType, $sessionSno);
    }

    /**
     * 현재 cycle(월/주) 에 해당하는 텍스트 반환
     *
     * @param string $type
     * @param string $cycle
     * @return string
     */
    private function formatCycle(string $type, string $cycle): string
    {
        return $type === 'month'
            ? DeliveryCycle::DELIVERY_CYCLE_MONTH[$cycle]
            : DeliveryCycle::DELIVERY_CYCLE_WEEK[$cycle];
    }

    /**
     * 현재 cycleDay(일/요일) 에 해당하는 텍스트 반환
     *
     * @param string $type
     * @param string $day
     * @return string
     */
    private function formatCycleDay(string $type, string $day): string
    {
        return $type === 'month'
            ? $day.'일'
            : DeliveryCycle::DELIVERY_CYCLE_WEEK_DAY[$day] . '요일';
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
        $this->regularOrderLog->insertRegularOrderLog($logDto);
    }

    /**
     * 배송회차 로그 배송예정일 업데이트
     *
     * @param string $applyNo
     * @param int $deliveryRound
     * @param string $deliveryDueDate
     * @return void
     */
    private function updateRegularDeliveryLog(string $applyNo, int $deliveryRound, string $deliveryDueDate)
    {
        $this->regularOrderDeliveryLogRepository->updateDeliveryDueDateByApplyNo($applyNo, $deliveryRound, $deliveryDueDate);
    }

    public function calculateRegularOrderDelivery()
    {

    }
}
