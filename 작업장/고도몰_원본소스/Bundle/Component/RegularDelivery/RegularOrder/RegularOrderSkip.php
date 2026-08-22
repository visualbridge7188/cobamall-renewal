<?php
/*
 * Copyright (C) 2025 NHN COMMERCE. - All Rights Reserved
 *
 * Unauthorized copying or redistribution of this file in source and binary forms via any medium
 * is strictly prohibited.
 */

namespace Bundle\Component\RegularDelivery\RegularOrder;

use Component\RegularDelivery\RegularOrder\RegularOrderLog;
use DTO\RegularDelivery\RegularOrder\RegularOrderGoodsSkipRoundDTO;
use DTO\RegularDelivery\RegularOrder\RegularOrderLogDTO;
use DTO\RegularDelivery\RegularOrder\RegularOrderSkipDTO;
use Framework\Http\Session\SessionManager as Session;
use Framework\Log\Logger;
use Illuminate\Database\Capsule\Manager;
use Origin\Enum\RegularDelivery\RegularOrder\RegularOrderLogActionType;
use Repository\RegularDelivery\RegularOrder\RegularOrderGoodsRepository;
use Util\Order\RegularOrderUtil;
use Component\RegularDelivery\Notification\RegularDeliveryNotificationSender;

class RegularOrderSkip
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
     * @var Session
     */
    private $session;
    /**
     * @var RegularOrderGoodsRepository
     */
    private $regularOrderGoodsRepository;
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
        Session $session,
        RegularOrderGoodsRepository $regularOrderGoodsRepository,
        RegularOrderLog $regularOrderLog,
        RegularDeliveryNotificationSender $notificationSender
    )
    {
        $this->manager = $manager;
        $this->logger = $logger;
        $this->session = $session;
        $this->regularOrderGoodsRepository = $regularOrderGoodsRepository;
        $this->regularOrderLog = $regularOrderLog;
        $this->notificationSender = $notificationSender;
    }

    /**
     * 정기결제 신청서 회차 건너뛰기
     * 회차 업데이트는 하지 않는다.
     *
     * @return void
     * @throws \Throwable
     */
    public function skipRegularOrder(RegularOrderSkipDTO $skipDTO)
    {
        $this->manager->getConnection()->beginTransaction();
        try {
            $applyNo = $skipDTO->getApplyNo();
            $sessionType = $skipDTO->getSessionType();
            $sessionSno = $skipDTO->getSessionSno();

            // 현재 신청서 정보 조회
            $currentDeliveryInfo = $this->regularOrderGoodsRepository->findCurrentDeliveryRoundByApplyNo($applyNo);

            // 회차 건너뛰기 시 예정되었던 배송일 기준으로 다시 재 계산
            $deliveryDate = RegularOrderUtil::calculateOrderDate(
                $currentDeliveryInfo['deliveryDueDate'],
                $currentDeliveryInfo['deliveryCycleType'],
                $currentDeliveryInfo['deliveryCycle'],
                $currentDeliveryInfo['deliveryCycleDay']
            );
            $nextDeliveryDueDate = $deliveryDate[0];
            $nextOrderCreateDate = $deliveryDate[1];

            $this->logger->channel('regularOrderAdmin')->info('배송회차 건너뛰기 신청, 현재 데이터 : ', $currentDeliveryInfo);
            $this->logger->channel('regularOrderAdmin')->info('배송회차 건너뛰기 신청, 변경 회차(배송예정일, 주문생성일) : ', $deliveryDate);

            // 업데이트
            $dto = new RegularOrderGoodsSkipRoundDTO($applyNo, $nextDeliveryDueDate, $nextOrderCreateDate);
            $this->regularOrderGoodsRepository->updateForSkipRegularOrderGoodsByApplyNo($dto);

            // 로그 저장
            $this->insertRegularOrderLog($applyNo, '', '건너뛰기 신청', $sessionType, $sessionSno);

            $this->manager->getConnection()->commit();

        } catch (\Throwable $e) {
            $this->manager->getConnection()->rollBack();
            throw $e;
        }

        try {
            // 신청서 정보 조회
            $regularOrderGoodsInfo = $this->regularOrderGoodsRepository->findRegularOrderGoodsInfoByApplyNo($applyNo);
            if (empty($regularOrderGoodsInfo)) {
                throw new \Exception('정기배송 신청서 정보가 없습니다. applyNo: ' . $applyNo);
            }

            // 회차 건너뛰기  알림 발송
            $this->notificationSender->sendSkipDeliveryRoundNotification($regularOrderGoodsInfo, $nextDeliveryDueDate);
        } catch (\Throwable $e) {
            $this->logger->warning('정기배송 회차 건너뛰기 처리 이후 알림 발송 실패: ' . $e->getMessage());
        }
    }

    /**
     * 신청서 관련 로그 저장
     *
     * @param string $applyNo
     * @param string $before
     * @param string $after
     * @param string $sessionType
     * @param int $sessionSno
     * @return void
     */
    private function insertRegularOrderLog(string $applyNo, string $before, string $after, string $sessionType, int $sessionSno)
    {
        $type = ($sessionType === 'admin') ? RegularOrderLogActionType::SKIP : RegularOrderLogActionType::USER_SKIP;
        $logDto = new RegularOrderLogDTO($applyNo, $sessionType, $sessionSno, $type, $before, $after);
        $this->regularOrderLog->insertRegularOrderLog($logDto);
    }
}
