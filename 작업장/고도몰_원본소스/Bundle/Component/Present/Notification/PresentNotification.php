<?php
/*
 * Copyright (C) 2025 NHN COMMERCE. - All Rights Reserved
 *
 * Unauthorized copying or redistribution of this file in source and binary forms via any medium
 * is strictly prohibited.
 */

namespace Bundle\Component\Present\Notification;

use Carbon\Carbon;
use Component\Member\MemberAdmin;
use Component\Policy\Policy;
use Component\Sms\Code;
use Component\Sms\SmsAuto;
use Component\Sms\SmsAutoCode;
use Component\Order\OrderNew;
use Exception;
use Framework\Http\Request;
use Framework\Log\Logger;
use Framework\Utility\StringUtils;
use Illuminate\Database\Capsule\Manager;
use Origin\Enum\Crm\Message\KakaoAlimTalkSender;
use Origin\Service\Crm\Message\KakaoAlimTalkSettingService;
use Repository\Present\Order\PresentReceiverInfoRepository;

class PresentNotification
{
    public function __construct(
        private readonly PresentReceiverInfoRepository $presentReceiverInfoRepository,
        private readonly OrderNew $order,
        private readonly MemberAdmin $memberAdmin,
        private readonly Policy $policy,
        private readonly Request $request,
        private readonly Logger $logger,
        private readonly Manager $dbManager,
        private readonly KakaoAlimTalkSettingService $kakaoAlimTalkSettingService,
    )
    {
    }

    /**
     * 선물하기 관련 메시지 전송 (주문 알림 발송 기반) - sms만 지원
     *
     * @param string $sendType 알림코드 (PRESENT - 선물발송, PRESENT_ACCEPT - 선물수락, PRESENT_REJECT - 선물거절, PRESENT_CHANGE_DEST - 배송지 변경, PRESENT_INVOICE_CODE - 선물 송장번호 안내)
     * @param string $orderNo 주문 번호
     * @return bool 발송 성공여부
     */
    public function sendPresentInfo(string $sendType, string $orderNo): bool
    {
        try {
            $this->logger->channel('presentNotification')->info(sprintf('Start sendPresentInfo. orderNo[%s], sendType[%s]', $orderNo, $sendType));
            $orderData = $this->order->getOrderDataSend($orderNo);

            if (!$this->validateSendPresentInfo($sendType, $orderNo, $orderData)) return false;
            if (!$this->checkPresentSmsAlrimTalkConfig($sendType, $orderNo)) return false;

            // 발송처리
            $presentReceiver = $this->presentReceiverInfoRepository->findByOrderNoForNotification($orderNo);
            $receiverInfo = $this->getReceiverInfo($orderData, $presentReceiver);
            $recipientInfo = $this->getRecipientInfo($presentReceiver);
            $replaceArguments = $this->getReplaceArguments($orderData, $presentReceiver);
            $isSuccess = $this->sendSms($sendType, $receiverInfo, $recipientInfo, $replaceArguments);

            if ($sendType == Code::PRESENT) {
                $this->updateSentPresentReceiver($presentReceiver['orderNo'], $presentReceiver['orderGoodsNo'], $isSuccess);
            }

            return $isSuccess;
        } catch (\Throwable $e) {
            $this->logger->channel('presentNotification')->warning(__METHOD__.' Exception occurred when sending present message '. $e->getMessage() , [
                'orderNo' => $orderNo,
                'sendType' => $sendType
            ]);
            return false;
        }
    }

    /**
     * 선물하기 관련 메시지 유효성 검증
     *
     * @param string $sendType       알림코드 (PRESENT - 선물발송, PRESENT_ACCEPT - 선물수락, PRESENT_REJECT - 선물거절, PRESENT_CHANGE_DEST - 배송지 변경, PRESENT_INVOICE_CODE - 선물 송장번호 안내)
     * @param string $orderNo        주문 번호
     * @return bool 유효 여부
     */
    protected function validateSendPresentInfo(string $sendType, string $orderNo, array $orderData): bool
    {
        try {
            if (empty($sendType) === true || empty($orderNo) === true) {
                throw new Exception(__('sendType or orderNo not exists'));
            }
            if (empty($orderData) === true) {
                throw new Exception(__('orderData not exists'));
            }
            if ((int)$orderData['mallSno'] !== DEFAULT_MALL_NUMBER) {
                throw new Exception(__('SMS only standard store. this order mall sno is %s', $orderData['mallSno']));
            }
            if (!$this->presentReceiverInfoRepository->existsByOrderNo($orderNo)) {
                throw new Exception(__('present receivers not exists'));
            }
        } catch (\Throwable $e) {
            $this->logger->channel('presentNotification')->warning(sprintf('Send present-related Message ValidateError orderNo[%s], sendType[%s], %s', $orderNo, $sendType, $e->getMessage()));
            return false;
        }

        return true;
    }

    /**
     * 선물하기 관련 메시지 발송 설정 확인
     *
     * @param string $sendType       알림코드 (PRESENT - 선물발송, PRESENT_ACCEPT - 선물수락, PRESENT_REJECT - 선물거절, PRESENT_CHANGE_DEST - 배송지 변경, PRESENT_INVOICE_CODE - 선물 송장번호 안내)
     * @param string $orderNo        주문 번호
     * @return bool 유효 여부
     */
    protected function checkPresentSmsAlrimTalkConfig(string $sendType, string $orderNo): bool
    {
        $kakaoAlimTalkConfig = $this->kakaoAlimTalkSettingService->getKakaoAlimTalkConfig();

        try {
            // 알림톡 사용
            if ($kakaoAlimTalkConfig->getUseFlag() === 'y'
                && $kakaoAlimTalkConfig->getSender()->value === KakaoAlimTalkSender::NHN_CLOUD->value
            ) {
                $kakaoAutoConfigs = $this->policy->getValue('kakaoAlrimCloud.kakaoAuto');
                if ($kakaoAutoConfigs['present'][$sendType] === 'y') {
                    return false;
                }
            }

            $smsAutoConfigs = $this->policy->getValue('sms.smsAuto');
            $smsSendTypePolicy = $smsAutoConfigs['present'][$sendType] ?? null;
            if (empty($smsSendTypePolicy)) {
                return false;
            }

            $this->logger->channel('presentNotification')->info(
                sprintf('Send present-related Message sendType=%s, orderNo=%s', $sendType, $orderNo)
            );
            return true;
        } catch (\Throwable $e) {
            $this->logger->channel('presentNotification')->warning(
                sprintf('Send present-related Message checkPresentSmsAlrimTalkConfig Error sendType=%s, orderNo=%s, %s', $sendType, $orderNo, $e->getMessage())
            );
            return false;
        }
    }

    /**
     * 수신자 정보 생성(member)
     *
     * @param array $orderData 주문 데이터
     * @return array 수신자 정보
     */
    protected function getReceiverInfo(array $orderData, array $presentReceiver): array
    {
        $memNo = StringUtils::strIsSet($orderData['memNo'],'0');
        if ((int)$memNo > 0) {
            $memberSmsFl = $this->memberAdmin->getMember($memNo, 'memNo', 'smsFl');
            $smsFl = StringUtils::strIsSet($memberSmsFl['smsFl'],'y');
        } else {
            $smsFl = 'y';
        }

        return [
            'memNo' => $memNo,
            'smsFl' => $smsFl,
            'memNm' => StringUtils::strIsSet($orderData['orderName'],''),
            'cellPhone' => StringUtils::strIsSet($orderData['orderCellPhone'],''),
            'scmNo' => $presentReceiver['scmNo'],
        ];
    }

    /**
     * 선물 수령자 정보 생성(recipient)
     *
     * @param array $presentReceiver 선물하기 수령자 데이터
     * @return array 선물 수령자 정보
     */
    protected function getRecipientInfo(array $presentReceiver): array
    {
        return [
            'memNo' => '0',
            'smsFl' => 'y',
            'memNm' => $presentReceiver['receiverName'],
            'cellPhone' => $presentReceiver['cellPhone'],
            'scmNo' => $presentReceiver['scmNo'],
        ];
    }

    /**
     * 치환코드 기본값 생성
     *
     * @param array $orderData 주문 데이터
     * @param array $presentReceiver 선물하기 수령자 데이터
     * @return array
     */
    protected function getReplaceArguments(array $orderData, array $presentReceiver): array
    {
        $basicInfoConfigs = $this->policy->getValue('basic.info');
        $shopUrl = $this->request->getDomainUrl($this->request->getDefaultHost(), usePort: false);
        // 기본도메인(*.godomall.com)의 경우 HTTPS front 도메인으로 URL을 고정
        // 모바일 기기 접근 시 MobileShop::mobileConnectCheck()에서 m- 도메인으로 리다이렉트 처리
        $defaultHost = $this->request->getDefaultHost();
        if (preg_match('/\.godomall\.com$/i', $defaultHost)) {
            $shopUrl = 'https://' . $defaultHost;
        }
        $presentUrl = sprintf('%s/present/present_confirm.php?token=%s', $shopUrl, $presentReceiver['confirmToken']);

        // 만료일이 없는경우 기본값 세팅 (첫 발송시)
        if($presentReceiver['expireDt'] === null) {
            $presentReceiver['expireDt'] = $this->getExpirationDatetime();
        }

        return [
            'rc_mallNm' => $basicInfoConfigs['mallNm'],
            'shopUrl' => $shopUrl,
            'receiverName' => $presentReceiver['receiverName'],
            'orderName' => $orderData['orderName'],
            'presentExpiryDate' => Carbon::parse($presentReceiver['expireDt'])->format('Y-m-d'),
            'presentUrl' => $presentUrl,
            'orderNo' => $presentReceiver['orderNo'],
            'orderGoodsNo' => $presentReceiver['orderGoodsNo'],
            'deliveryName' => StringUtils::strIsSet($presentReceiver['companyName'],''),
            'invoiceNo' => StringUtils::strIsSet($presentReceiver['invoiceNo'],''),
        ];
    }

    /**
     * sms 발송 처리
     *
     * @param string $sendType
     * @param array $receiverInfo
     * @param array $recipientInfo
     * @param array $replaceArguments
     * @return bool
     */
    private function sendSms(string $sendType, array $receiverInfo, array $recipientInfo, array $replaceArguments): bool
    {
        $this->logger->channel('presentNotification')->info('Closure sendSms.', func_get_args());
        /** @var \Bundle\Component\Sms\SmsAuto $smsAuto */
        $smsAuto = new SmsAuto();
        $smsAuto->setSmsType(SmsAutoCode::PRESENT);
        $smsAuto->setSmsAutoCodeType($sendType);
        $smsAuto->setReceiver($receiverInfo);
        $smsAuto->setRecipient($recipientInfo);
        $smsAuto->setReplaceArguments($replaceArguments);
        $result = $smsAuto->autoSend();
        return $result[0]['success'] > 0;
    }

    /**
     * 선물 수령자 정보 발송 완료 처리
     *
     * @param string $orderNo
     * @param int $orderGoodsNo
     * @throws \Throwable
     */
    protected function updateSentPresentReceiver(string $orderNo, int $orderGoodsNo, bool $isSuccess): void
    {
        $now = Carbon::now()->format('Y-m-d H:i:s');
        $this->dbManager->getConnection()->beginTransaction();
        try {
            $presentReceiverUpdateData = [
                'sendSmsFl' => $isSuccess ? 'y' : 'n',
                'sendDt' => $now,
                'modDt' => $now,
                'expireDt' => $isSuccess ? $this->getExpirationDatetime() : null,
            ];
            $this->presentReceiverInfoRepository->updateReceiverInfo($orderNo, $presentReceiverUpdateData);
            $this->dbManager->getConnection()->commit();
        } catch (\Throwable $e) {
            $this->logger->channel('presentNotification')->warning(__METHOD__ . ' 수령자 정보 저장 실패', [
                'orderNo' => $orderNo,
                'orderGoodsNo' => $orderGoodsNo,
                'error' => $e->getMessage()
            ]);
            $this->dbManager->getConnection()->rollback();
        }
    }

    /**
     * 선물 배송기한 입력 만료일
     *
     * @return string
     */
    protected function getExpirationDatetime(): string
    {
        $presentConfigs = $this->policy->getValue('goods.present');
        $expirationPeriod = StringUtils::strIsSet($presentConfigs['expirationPeriod'], "7");

        return Carbon::now()
            ->addDays((int)$expirationPeriod)
            ->setTime(23, 59, 59)
            ->format('Y-m-d H:i:s');
    }
}
