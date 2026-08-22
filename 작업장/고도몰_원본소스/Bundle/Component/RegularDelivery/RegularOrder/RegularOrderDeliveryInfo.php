<?php
/*
 * Copyright (C) 2025 NHN COMMERCE. - All Rights Reserved
 *
 * Unauthorized copying or redistribution of this file in source and binary forms via any medium
 * is strictly prohibited.
 */

namespace Bundle\Component\RegularDelivery\RegularOrder;

use DTO\RegularDelivery\RegularOrder\RegularOrderDeliveryInfoUpdateDTO;
use Framework\Log\Logger;

class RegularOrderDeliveryInfo
{
    // 성공 메시지
    const DELIVERY_CYCLE_CHANGE_SUCCESS_MESSAGE = "- 배송주기/종료회차 변경에 성공하였습니다.";
    const SHIPPING_ADDRESS_CHANGE_SUCCESS_MESSAGE = "- 배송지 주소 변경에 성공하였습니다.";
    const PAYMENT_CARD_CHANGE_SUCCESS_MESSAGE = "- 결제카드 변경에 성공하였습니다.";
    
    // 실패 메시지
    const DELIVERY_CYCLE_CHANGE_FAILED_MESSAGE = "- 배송주기/종료회차 변경에 실패하였습니다.";
    const SHIPPING_ADDRESS_CHANGE_FAILED_MESSAGE = "- 배송지 주소 변경에 실패하였습니다.";
    const PAYMENT_CARD_CHANGE_FAILED_MESSAGE = "- 결제카드 변경에 실패하였습니다.";
    
    // 확인 메시지
    const CHANGE_COMPLETED_MESSAGE = "배송정보가 변경되었습니다.";

    /** @var RegularOrderCycleChange */
    private $regularOrderCycleChange;

    /** @var RegularOrderShippingAddress */
    private $regularOrderShippingAddress;

    /** @var RegularOrderPayment */
    private $regularOrderPayment;

    /** @var Logger */
    private $logger;

    public function __construct(
        RegularOrderCycleChange $regularOrderCycleChange,
        RegularOrderShippingAddress $regularOrderShippingAddress,
        RegularOrderPayment $regularOrderPayment,
        Logger $logger
    )
    {
        $this->regularOrderCycleChange = $regularOrderCycleChange;
        $this->regularOrderShippingAddress = $regularOrderShippingAddress;
        $this->regularOrderPayment = $regularOrderPayment;
        $this->logger = $logger;
    }

    /**
     * 배송정보 변경 처리
     *
     * @param RegularOrderDeliveryInfoUpdateDTO $deliveryInfoUpdateDto
     * @return string
     */
    public function updateRegularDeliveryInfo(RegularOrderDeliveryInfoUpdateDTO $deliveryInfoUpdateDto): string
    {
        // 요청에 의해 실행된 서비스의 결과
        $results = ['success' => [], 'failed' => []];

        // 배송주기/종료회차 변경 처리
        if ($deliveryInfoUpdateDto->getChangeDeliveryCycleFl() === 'y') {
            try {
                $cycleDto = $deliveryInfoUpdateDto->getDeliveryCycleChangeDto();
                $this->regularOrderCycleChange->updateRegularDeliveryCycle($cycleDto);
                $results['success'][] = self::DELIVERY_CYCLE_CHANGE_SUCCESS_MESSAGE;
            } catch (\Throwable $e) {
                $this->logger->channel('regularDelivery')->warning(__METHOD__ . ' ' . self::DELIVERY_CYCLE_CHANGE_FAILED_MESSAGE . ' : ' . $e->getMessage());
                $results['failed'][] = self::DELIVERY_CYCLE_CHANGE_FAILED_MESSAGE;
            }
        }

        // 배송지 정보 변경 처리
        if ($deliveryInfoUpdateDto->getChangeShippingAddressFl() === 'y') {
            try {
                $shippingDto = $deliveryInfoUpdateDto->getRegularOrderShippingAddressUpdateDto();
                $this->regularOrderShippingAddress->updateRegularOrderShippingAddress($shippingDto);
                $results['success'][] = self::SHIPPING_ADDRESS_CHANGE_SUCCESS_MESSAGE;
            } catch (\Throwable $e) {
                $this->logger->channel('regularDelivery')->warning(__METHOD__ . ' ' . self::SHIPPING_ADDRESS_CHANGE_FAILED_MESSAGE . ' : ' . $e->getMessage());
                $results['failed'][] = self::SHIPPING_ADDRESS_CHANGE_FAILED_MESSAGE;
            }
        }

        // 결제카드 변경 처리
        if ($deliveryInfoUpdateDto->getChangePaymentCardFl() === 'y') {
            try {
                $paymentCardDto = $deliveryInfoUpdateDto->getRegularOrderPaymentCardUpdateDto();
                $this->regularOrderPayment->updateRegularOrderPaymentCard($paymentCardDto);
                $results['success'][] = self::PAYMENT_CARD_CHANGE_SUCCESS_MESSAGE;
            } catch (\Throwable $e) {
                $this->logger->channel('regularDelivery')->warning(__METHOD__ . ' ' . self::PAYMENT_CARD_CHANGE_FAILED_MESSAGE . ' : ' . $e->getMessage());
                $results['failed'][] = self::PAYMENT_CARD_CHANGE_FAILED_MESSAGE;
            }
        }

        return $this->formattedResultMessage($results);
    }

    /**
     * 결과 메세지 생성
     *
     * @param array $results
     * @return string
     */
    private function formattedResultMessage(array $results): string
    {
        if (!empty($results['failed'])) {
            $messages = [];

            foreach (['success' => '성공', 'failed' => '실패'] as $key => $label) {
                if (!empty($results[$key])) {
                    $messages[] = "{$label}\n" . implode("\n", $results[$key]);
                }
            }

            return implode("\n\n", $messages);
        }

        return self::CHANGE_COMPLETED_MESSAGE;
    }
}
