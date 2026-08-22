<?php
/*
 * Copyright (C) 2025 NHN COMMERCE. - All Rights Reserved
 *
 * Unauthorized copying or redistribution of this file in source and binary forms via any medium
 * is strictly prohibited.
 */

namespace Bundle\Component\RegularDelivery\Notification;

use Component\RegularDelivery\Notification\RegularDeliverySmsSender;
use Component\RegularDelivery\Notification\RegularDeliveryMailSender;
use Framework\Log\Logger;
use Repository\RegularDelivery\RegularOrder\RegularOrderRepository;
use Repository\RegularDelivery\RegularOrder\RegularOrderGoodsRepository;
use Illuminate\Database\Eloquent\Collection;

class RegularDeliveryNotificationSender
{
    /**
     * @var RegularDeliverySmsSender
     */
    private $regularDeliverySmsSender;
    /**
     * @var RegularDeliveryMailSender
     */
    private $regularDeliveryMailSender;
    /**
     * @var RegularOrderRepository
     */
    private $regularOrderRepository;
    /**
     * @var RegularOrderGoodsRepository
     */
    private $regularOrderGoodsRepository;
    /**
     * @var Logger
     */
    private $logger;

    /**
     * @param RegularDeliverySmsSender $regularDeliverySmsSender
     * @param RegularDeliveryMailSender $regularDeliveryMailSender
     * @param RegularOrderRepository $regularOrderRepository
     * @param RegularOrderGoodsRepository $regularOrderGoodsRepository
     * @param Logger $logger
     */
    public function __construct(
        RegularDeliverySmsSender $regularDeliverySmsSender,
        RegularDeliveryMailSender $regularDeliveryMailSender,
        RegularOrderRepository $regularOrderRepository,
        RegularOrderGoodsRepository $regularOrderGoodsRepository,
        Logger $logger
    ) {
        $this->regularDeliverySmsSender = $regularDeliverySmsSender;
        $this->regularDeliveryMailSender = $regularDeliveryMailSender;
        $this->regularOrderRepository = $regularOrderRepository;
        $this->regularOrderGoodsRepository = $regularOrderGoodsRepository;
        $this->logger = $logger;
    }

    // 정기배송 일시정지 알림
    public function sendPauseNotification(array $regularOrderGoodsInfo)
    {
        $this->logger->channel('regularDelivery')->info('================== sendPauseNotification START ==================');
        $this->regularDeliverySmsSender->sendPauseSms($regularOrderGoodsInfo);
        $this->regularDeliveryMailSender->sendPauseMail($regularOrderGoodsInfo);
        $this->logger->channel('regularDelivery')->info('================== sendPauseNotification END ==================');
    }

    // 정기배송 일시정지 해제 알림
    public function sendResumeNotification(array $regularOrderGoodsInfo)
    {
        $this->logger->channel('regularDelivery')->info('================== sendResumeNotification START ==================');
        $this->regularDeliverySmsSender->sendResumeSms($regularOrderGoodsInfo);
        $this->regularDeliveryMailSender->sendResumeMail($regularOrderGoodsInfo);
        $this->logger->channel('regularDelivery')->info('================== sendResumeNotification END ==================');
    }

    // 정기배송 건너뛰기 알림
    public function sendSkipDeliveryRoundNotification(array $regularOrderGoodsInfo, string $skipDeliveryDate)
    {
        $this->logger->channel('regularDelivery')->info('================== sendSkipDeliveryRoundNotification START ==================');
        $this->regularDeliverySmsSender->sendSkipDeliveryRoundSms($regularOrderGoodsInfo, $skipDeliveryDate);
        $this->regularDeliveryMailSender->sendSkipDeliveryRoundMail($regularOrderGoodsInfo, $skipDeliveryDate);
        $this->logger->channel('regularDelivery')->info('================== sendSkipDeliveryRoundNotification END ==================');
    }

    // 정기배송 해지 알림
    public function sendCancelNotification(array $regularOrderGoodsInfo, string $cancelReason)
    {
        $this->logger->channel('regularDelivery')->info('================== sendCancelNotification START ==================');
        $this->regularDeliverySmsSender->sendCancelSms($regularOrderGoodsInfo, $cancelReason);
        $this->regularDeliveryMailSender->sendCancelMail($regularOrderGoodsInfo);
        $this->logger->channel('regularDelivery')->info('================== sendCancelNotification END ==================');
    }

    // 정기배송 신청 완료 알림
    public function sendApplyCompleteNotification(string $memNo, string $applyGroupNo)
    {
        try {
            // 신청 완료 후 치환코드용 데이터 조회
            $regularOrderReplaceInfo = $this->regularOrderRepository->findRegularOrderInfoByApplyGroupNo($applyGroupNo);
            if (empty($regularOrderReplaceInfo)) {
                $this->logger->channel('regularDelivery')->warning(__CLASS__ .' '. __METHOD__. ' 정기배송 신청 완료 알림 발송 오류 - 신청 정보 없음, 신청 그룹번호 :', [$applyGroupNo]);
                return;
            }

            $this->logger->channel('regularDelivery')->info('================== sendApplyCompleteNotification START ==================');
            $this->regularDeliverySmsSender->sendApplyCompleteSms($memNo, $regularOrderReplaceInfo);
            $this->regularDeliveryMailSender->sendApplyCompleteMail($memNo, $regularOrderReplaceInfo);
            $this->logger->channel('regularDelivery')->info('================== sendApplyCompleteNotification END ==================');
        } catch (\Throwable $e) {
            $this->logger->channel('regularDelivery')->warning(__CLASS__ .' '. __METHOD__. ' 정기배송 신청 완료 알림 발송 오류 ', [$e->getMessage()]);
        }
    }

    // 정기결제 성공 알림
    public function sendPaymentCompleteNotification(Collection $groups, string $memNo, string $orderNo)
    {
        $this->logger->channel('regularDelivery')->info('================== sendPaymentCompleteNotification START ==================');
        $this->regularDeliverySmsSender->sendPaymentCompleteSms($groups, $memNo, $orderNo);
        $this->regularDeliveryMailSender->sendPaymentCompleteMail($groups, $memNo, $orderNo);
        $this->logger->channel('regularDelivery')->info('================== sendPaymentCompleteNotification END ==================');
    }
    
    // 정기결제 실패 알림
    public function sendPaymentFailNotification(Collection $groups, string $memNo)
    {
        $this->logger->channel('regularDelivery')->info('================== sendPaymentFailNotification START ==================');
        $this->regularDeliverySmsSender->sendPaymentFailSms($groups, $memNo);
        $this->regularDeliveryMailSender->sendPaymentFailMail($groups, $memNo);
        $this->logger->channel('regularDelivery')->info('================== sendPaymentFailNotification END ==================');
    }

    // 정기배송 주문 생성 예정 알림
    public function sendPaymentScheduledNotification(Collection $groups)
    {
        $this->logger->channel('regularDelivery')->info('================== sendPaymentScheduledNotification START ==================');
        // 주문 생성 대상 그룹별로 반복 처리
        foreach ($groups as $orderGroup) {
            $this->regularDeliverySmsSender->sendPaymentScheduledSms($orderGroup);
            $this->regularDeliveryMailSender->sendPaymentScheduledMail($orderGroup);
        }
        $this->logger->channel('regularDelivery')->info('================== sendPaymentScheduledNotification END ==================');
    }

    // 정기배송 일정 변경 알림
    public function sendRegularDeliveryInfoChanged(array $changeData)
    {
        // 신청정보 조회
        $regularOrderGoodsInfo = $this->regularOrderGoodsRepository->findRegularOrderGoodsInfoByApplyNo($changeData['applyNo']);
        $changeData['regularGoodsNm'] = $regularOrderGoodsInfo['regularGoodsNm']; // 상품명
        $changeData['deliveryRound'] = $regularOrderGoodsInfo['deliveryRound']; // 현재 배송회차
        $changeData['maxDeliveryRound'] = $regularOrderGoodsInfo['maxDeliveryRound']; // 종료 회차
        $changeData['deliveryDueDate'] = $regularOrderGoodsInfo['deliveryDueDate']; // 배송 예정일
        $changeData['memNo'] = $regularOrderGoodsInfo['memNo']; // 회원 번호

        // 알림 발송
        $this->logger->channel('regularDelivery')->info('================== sendRegularDeliveryInfoChanged START ==================');
        $this->regularDeliverySmsSender->sendRegularDeliveryInfoChangeSms($changeData, $regularOrderGoodsInfo);
        $this->regularDeliveryMailSender->sendRegularDeliveryInfoChangeMail($changeData, $regularOrderGoodsInfo);        
        $this->logger->channel('regularDelivery')->info('================== sendRegularDeliveryInfoChanged END ==================');
    }
}
