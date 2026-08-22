<?php
/*
 * Copyright (C) 2025 NHN COMMERCE. - All Rights Reserved
 *
 * Unauthorized copying or redistribution of this file in source and binary forms via any medium
 * is strictly prohibited.
 */

namespace Bundle\DTO\RegularDelivery\RegularOrder;

use Origin\DTO\AbstractDTO;
use DTO\RegularDelivery\RegularOrder\DeliveryCycleChangeDTO;
use DTO\RegularDelivery\RegularOrder\RegularOrderPaymentCardUpdateDTO;
use DTO\RegularDelivery\RegularOrder\RegularOrderShippingAddressUpdateDTO;

/**
 * @property-read string $changeDeliveryCycleFl
 * @property-read string $changeShippingAddressFl
 * @property-read string $changePaymentCardFl
 * @property-read DeliveryCycleChangeDTO $deliveryCycleChangeDto
 * @property-read RegularOrderPaymentCardUpdateDTO $regularOrderPaymentCardUpdateDto
 * @property-read RegularOrderShippingAddressUpdateDTO $regularOrderShippingAddressUpdateDto
 */
class RegularOrderDeliveryInfoUpdateDTO extends AbstractDTO
{
    /** @var string */
    private $changeDeliveryCycleFl;

    /** @var string */
    private $changeShippingAddressFl;

    /** @var string */
    private $changePaymentCardFl;

    /** @var DeliveryCycleChangeDTO */
    private $deliveryCycleChangeDto;

    /** @var RegularOrderPaymentCardUpdateDTO */
    private $regularOrderPaymentCardUpdateDto;

    /** @var RegularOrderShippingAddressUpdateDTO */
    private $regularOrderShippingAddressUpdateDto;

    public function __construct(array $data)
    {
        $this->changeDeliveryCycleFl = $data['changeDeliveryCycleFl'] ?? 'n';
        $this->changeShippingAddressFl = $data['changeShippingAddressFl'] ?? 'n';
        $this->changePaymentCardFl = $data['changePaymentCardFl'] ?? 'n';
        $this->deliveryCycleChangeDto = new DeliveryCycleChangeDTO($data);
        $this->regularOrderPaymentCardUpdateDto = new RegularOrderPaymentCardUpdateDTO($data);
        $this->regularOrderShippingAddressUpdateDto = new RegularOrderShippingAddressUpdateDTO($data);
    }

    /**
     * @return string
     */
    public function getChangeDeliveryCycleFl(): string
    {
        return $this->changeDeliveryCycleFl;
    }

    /**
     * @return string
     */
    public function getChangeShippingAddressFl(): string
    {
        return $this->changeShippingAddressFl;
    }

    /**
     * @return string
     */
    public function getChangePaymentCardFl(): string
    {
        return $this->changePaymentCardFl;
    }

    /**
     * @return DeliveryCycleChangeDTO
     */
    public function getDeliveryCycleChangeDto(): DeliveryCycleChangeDTO
    {
        return $this->deliveryCycleChangeDto;
    }

    /**
     * @return RegularOrderPaymentCardUpdateDTO
     */
    public function getRegularOrderPaymentCardUpdateDto(): RegularOrderPaymentCardUpdateDTO
    {
        return $this->regularOrderPaymentCardUpdateDto;
    }

    /**
     * @return RegularOrderShippingAddressUpdateDTO
     */
    public function getRegularOrderShippingAddressUpdateDto(): RegularOrderShippingAddressUpdateDTO
    {
        return $this->regularOrderShippingAddressUpdateDto;
    }
}
