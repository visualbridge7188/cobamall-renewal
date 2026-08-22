<?php
/*
 * Copyright (C) 2025 NHN COMMERCE. - All Rights Reserved
 *
 * Unauthorized copying or redistribution of this file in source and binary forms via any medium
 * is strictly prohibited.
 */

namespace Bundle\Component\RegularDelivery\RegularOrder;

use Component\RegularDelivery\RegularOrder\RegularOrderLog;
use DTO\RegularDelivery\RegularOrder\RegularOrderStatusChangeDTO;
use DTO\RegularDelivery\RegularOrder\RegularOrderStatusLogDTO;
use Framework\Log\Logger;
use Illuminate\Database\Capsule\Manager;
use Origin\Enum\RegularDelivery\RegularOrder\RegularOrderStatus;
use Repository\RegularDelivery\RegularOrder\RegularOrderGoodsRepository;
use Repository\RegularDelivery\RegularOrder\RegularOrderGoodsOptionRepository;
use Util\Order\RegularOrderUtil;
use Component\RegularDelivery\Notification\RegularDeliveryNotificationSender;
use Origin\Repository\Payment\PgCardInfoRepositoryInterface;
/**
 * 정기결제 신청서 어드민 리스트 기능 핸들러
 */
class RegularOrderStatusChange
{
    /**
     * @var RegularOrderGoodsRepository
     */
    private $regularOrderGoodsRepository;
    /**
     * @var Manager
     */
    private $manager;
    /**
     * @var Logger
     */
    private $logger;
    /**
     * @var RegularOrderLog
     */
    private $regularOrderLog;
    /**
     * @var RegularDeliveryNotificationSender
     */
    private $notificationSender;
    /**
     * @var PgCardInfoRepository
     */
    private $pgCardInfoRepository;
    /**
     * @var RegularOrderGoodsOptionRepository
     */
    private $regularOrderGoodsOptionRepository;

    public function __construct(
        Logger $logger,
        Manager $manager,
        RegularOrderGoodsRepository $regularOrderGoodsRepository,
        RegularOrderGoodsOptionRepository $regularOrderGoodsOptionRepository,
        RegularOrderLog $regularOrderLog,
        RegularDeliveryNotificationSender $notificationSender,
        PgCardInfoRepositoryInterface $pgCardInfoRepository
    )
    {
        $this->logger = $logger;
        $this->manager = $manager;
        $this->regularOrderGoodsRepository = $regularOrderGoodsRepository;
        $this->regularOrderGoodsOptionRepository = $regularOrderGoodsOptionRepository;
        $this->regularOrderLog = $regularOrderLog;
        $this->notificationSender = $notificationSender;
        $this->pgCardInfoRepository = $pgCardInfoRepository;
    }

    /**
     * 정기배송 신청서 상태 변경
     *
     * @param RegularOrderStatusChangeDTO $statusChangeDTO
     * @return void
     * @throws \Exception
     */
    public function updateApplyStatus(RegularOrderStatusChangeDTO $statusChangeDTO)
    {
        $applyNoList = $statusChangeDTO->getApplyNoList();
        $updateStatus = $statusChangeDTO->getUpdateStatus();
        $sessionType = $statusChangeDTO->getSessionType();
        $sessionSno = $statusChangeDTO->getSessionSno();
        $updateReason = $statusChangeDTO->getReason();

        if (empty($applyNoList)) {
            return;
        }

        // validation
        $this->validateBeforeUpdate($applyNoList, $updateStatus);

        // 업데이트
        $this->manager->getConnection()->beginTransaction();
        try {
            $this->logger->channel('regularDelivery')->info('정기배송 신청서 상태변경', [
                'applyNoList' => $applyNoList,
                'status' => RegularOrderStatus::getStatusLabel($updateStatus)
            ]);

            // 변경 전 현재 상태값을 구하기 위한 bulk select
            $currentStatusList = $this->regularOrderGoodsRepository->findCurrentApplyStatusByApplyNoList($applyNoList);
            $beforeStatusList = [];
            foreach ($currentStatusList as $currentStatus) {
                $beforeStatusList[$currentStatus['applyNo']] = $currentStatus['applyStatus'];
            }

            // 상태값 업데이트
            if ($updateStatus === RegularOrderStatus::ACTIVE) {
                // 이용중 상태로의 변경은 상태값 + 배송 예정일, 주문 생성일을 추가로 업데이트 해줘야 함
                $this->updateStatusActiveProcess($applyNoList);
            } else {
                // 아니면 그냥 상태값만 업데이트
                $this->regularOrderGoodsRepository->updateApplyStatusByApplyNoList($applyNoList, $updateStatus);
            }

            // 상태값 변경 로그 추가
            $this->insertStatusUpdateLog($applyNoList, $updateStatus, $sessionType, $sessionSno, $beforeStatusList, $updateReason);

            $this->manager->getConnection()->commit();
        } catch (\Throwable $e) {
            $this->logger->channel('regularDelivery')->warning('정기배송 신청서 상태변경 실패', [$e->getMessage(), $e->getTrace()]);
            $this->manager->getConnection()->rollBack();
            throw new \Exception('이용상태 변경처리에 실패하였습니다. 잠시 후 다시 시도해주세요.');
        }

        try {
            $this->sendStatusChangeNotifications($applyNoList, $updateStatus);
        } catch (\Throwable $e) {
            $this->logger->warning('정기배송 신청서 상태 변경 처리 이후 알림 발송 실패: ' . $e->getMessage());
        }
    }

    /**
     * 신청서 상태 변경 전 유효성 검증
     *
     * @param array $applyNoList
     * @param string $updateStatus
     * @return void
     * @throws \Exception
     */
    private function validateBeforeUpdate(array $applyNoList, string $updateStatus)
    {
        // 해지 관련 상태를 가진 신청번호가 있다면 업데이트 불가
        if ($this->regularOrderGoodsRepository->existRegularOrderGoodsByApplyNoListAndStatus($applyNoList, RegularOrderStatus::getInactiveStatus()) === true) {
            throw new \Exception('이미 해지 처리된 신청 내역은 이용상태 변경이 불가합니다.');
        }

        // 이용중 상태로 변경하려고 하는 경우
        if ($updateStatus === RegularOrderStatus::ACTIVE) {
            // 동일한 상태값을 가진 신청번호 존재 확인
            if ($this->regularOrderGoodsRepository->existRegularOrderGoodsByApplyNoListAndStatus($applyNoList, [RegularOrderStatus::ACTIVE]) === true) {
                throw new \Exception('"이용중" 으로 변경이 불가능한 이용상태 입니다. 자세한 내용은 매뉴얼을 참고하시기 바랍니다.');
            }

            // 정기배송 상품 품절인 경우 업데이트 불가
            if ($this->regularOrderGoodsRepository->existSoldOutGoodsByApplyNoList($applyNoList) === true) {
                throw new \Exception('정기결제(배송) 상품이 품절되어 이용상태 변경이 불가합니다.');
            }

            // 옵션 상품 품절인 경우 업데이트 불가
            if ($this->regularOrderGoodsOptionRepository->existSoldOutGoodsOptionByApplyNoList($applyNoList) === true) {
                throw new \Exception('정기결제(배송) 옵션 상품이 품절되어 이용상태 변경이 불가합니다.');
            }

            // 추가 상품 품절인 경우 업데이트 불가
            if ($this->regularOrderGoodsRepository->existSoldOutAddGoodsByApplyNoList($applyNoList) === true) {
                throw new \Exception('정기결제(배송) 추가 상품이 품절되어 이용상태 변경이 불가합니다.');
            }

            // 등록된 카드가 없는 경우 업데이트 불가
            $regularOrderGoods = $this->regularOrderGoodsRepository->findRegularOrderInfoByApplyNoList($applyNoList);
            foreach ($regularOrderGoods as $regularOrder) {
                if(empty($this->pgCardInfoRepository->findUsedCardByCardNoAndMemNo($regularOrder['cardNo'], $regularOrder['memNo']))) {
                    throw new \Exception('정기결제 카드 정보가 없어 이용상태 변경이 불가합니다.');

                }
            }
        }

        // 일시정지 상태로 변경하려고 하는 경우
        if ($updateStatus === RegularOrderStatus::ADMIN_STOP) {
            // 일시정지 상태값을 가진 신청번호 존재 확인
            if ($this->regularOrderGoodsRepository->existRegularOrderGoodsByApplyNoListAndStatus($applyNoList, RegularOrderStatus::getPauseStatus()) === true) {
                throw new \Exception('"일시정지"로 변경이 불가능한 이용상태 입니다. 자세한 내용은 매뉴얼을 참고하시기 바랍니다.');
            }

            // 만료 배송회차가 존재하는지 확인
            if ($this->regularOrderGoodsRepository->existExpiredDeliveryRoundsByApplyNoList($applyNoList) === true) {
                throw new \Exception('"일시정지"로 변경이 불가능한 이용상태 입니다. 자세한 내용은 매뉴얼을 참고하시기 바랍니다.');
            }
        }
    }

    /**
     * 상태 변경 로그 저장
     *
     * @param array $applyNoList
     * @param string $updateStatus
     * @param string $sessionType
     * @param int $sessionSno
     * @param array $beforeStatusList
     * @param string $updateReason
     * @return void
     */
    private function insertStatusUpdateLog(array $applyNoList, string $updateStatus, string $sessionType, int $sessionSno, array $beforeStatusList, string $updateReason)
    {
        // bulk insert
        $statusLogDtoList = [];
        foreach ($applyNoList as $applyNo) {
            $statusLogDto = new RegularOrderStatusLogDTO($applyNo, $sessionType, $sessionSno, $beforeStatusList[$applyNo], $updateStatus, $updateReason);
            $statusLogDtoList[] = $statusLogDto;
        }
        $this->regularOrderLog->bulkInsertRegularOrderStatusLog($statusLogDtoList);
    }

    /**
     * active 상태로의 변경에서는 배송 관련 일자를 재계산 해서 같이 업데이트 해줘야 함
     *
     * @param array $applyNoList
     * @return void
     * @throws \Exception
     */
    private function updateStatusActiveProcess(array $applyNoList)
    {
        foreach ($applyNoList as $applyNo) {
            $regularOrderGoodsInfo = $this->regularOrderGoodsRepository->findRegularOrderGoodsInfoByApplyNo($applyNo);
            if (empty($regularOrderGoodsInfo)) {
                throw new \Exception('정기배송 신청서 정보가 존재하지 않습니다. 신청번호: ' . $applyNo);
            }

            // 현재 날짜 기준으로 재계산
            $regularOrderDates = RegularOrderUtil::calculateOrderDate(
                date('Y-m-d'),
                $regularOrderGoodsInfo['deliveryCycleType'],
                $regularOrderGoodsInfo['deliveryCycle'],
                $regularOrderGoodsInfo['deliveryCycleDay'],
                true
            );

            // 변경된 배송예정일
            $deliveryDueDate = $regularOrderDates[0];
            // 변경된 주문생성일
            $orderCreateDate = $regularOrderDates[1];

            $this->regularOrderGoodsRepository->updateResumeInfoByApplyNo($applyNo, RegularOrderStatus::ACTIVE, $deliveryDueDate, $orderCreateDate);
        }
    }

    /**
     * 상태 변경 알림 발송
     *
     * @param array $applyNoList
     * @param string $updateStatus
     * @return void
     */
    public function sendStatusChangeNotifications(array $applyNoList, string $updateStatus)
    {
        // 신청번호 리스트로 정기배송 상품 조회
        $regularOrderGoodsInfoList = $this->regularOrderGoodsRepository->findRegularOrderGoodsInfoByApplyNoList($applyNoList);
        // applyGroupNo 기준으로 그룹화
        $sendTargetList = collect($regularOrderGoodsInfoList)
            ->groupBy('applyGroupNo')
            ->toArray();

        // applyGroupNo별로 그룹화된 정보로 알림 발송
        foreach ($sendTargetList as $groupInfo) {
            // 상태 변경에 따른 알림 발송
            if (in_array($updateStatus, RegularOrderStatus::getPauseStatus())) {
                $this->notificationSender->sendPauseNotification($groupInfo);
            } elseif ($updateStatus === RegularOrderStatus::ACTIVE) {
                $this->notificationSender->sendResumeNotification($groupInfo);
            } elseif (in_array($updateStatus, RegularOrderStatus::getInactiveStatus())) {
                $this->notificationSender->sendCancelNotification($groupInfo, $updateStatus);
            }
        }
    }
}
