<?php
/*
 * Copyright (C) 2025 NHN COMMERCE. - All Rights Reserved
 *
 * Unauthorized copying or redistribution of this file in source and binary forms via any medium
 * is strictly prohibited.
 */

namespace Bundle\DTO\RegularDelivery\RegularOrder;

use Origin\DTO\AbstractDTO;

/**
 * @property-read int memNo
 * @property-read array cartSno
 * @property-read array regularGoodsPrice
 * @property-read array regularGoodsPriceOption
 * @property-read array regularGoodsPriceOptionText
 * @property-read array regularAddGoodsPrice
 * @property-read array giftInfo
 * @property-read array applierInfo
 * @property-read int totalDeliveryCharge
 * @property-read int shippingSno
 * @property-read int settlePrice
 * @property-read string encryptCardNo
 * @property-read string regDt
 */
class RegularOrderDTO extends AbstractDTO
{
    private $memNo;
    private $cartSno;
    private $regularGoodsPrice;
    private $regularGoodsPriceOption;
    private $regularGoodsPriceOptionText;
    private $regularAddGoodsPrice;
    private $regularGoodsDiscountUseFl;
    private $giftInfo;
    private $applierInfo;
    private $totalDeliveryCharge;
    private $shippingSno;
    private $settlePrice;
    private $encryptCardNo;
    private $regDt;

    public function __construct(array $data)
    {
        $this->memNo = \Session::get('member.memNo');
        $this->cartSno = $data['cartSno']; // 카트 정보
        $this->regularGoodsPrice = $data['regularGoodsPrice']; // 정기상품 가격
        $this->regularGoodsPriceOption = $data['regularGoodsPriceOption']; // 정기상품 옵션 가격
        $this->regularGoodsPriceOptionText = $data['regularGoodsPriceOptionTextTotal'] ?? []; // 정기상품 텍스트 옵션 가격
        $this->regularAddGoodsPrice = $data['regularAddGoodsPrice'] ?? []; // 정기상품 텍스트 옵션 가격
        $this->regularGoodsDiscountUseFl = $data['regularGoodsDiscountUseFl'] ?? []; // 정기상품 할인 사용 여부
        $this->giftInfo = $data['gift'] ?? []; // 사은품 정보
        $this->applierInfo = [
            'applierName' => $data['orderName'], // 신청자 정보
            'applierPhone' => $data['orderPhone'], // 신청자 전화번호
            'applierCellPhone' => $data['orderCellPhone'], // 신청자 휴대폰번호
            'applierEmail' => $data['orderEmail'], // 신청자 이메일
        ];
        $this->totalDeliveryCharge = $data['totalDeliveryCharge'];
        $this->shippingSno = $data['shippingSno']; // 수령자 배송지 주소 번호
        $this->settlePrice = $data['settlePrice']; // 결제 금액
        $this->encryptCardNo = $data['encryptCardNo'];
        $this->regDt = date('Y-m-d H:i:s');
    }

    /**
     * @return int
     */
    public function getMemNo(): int
    {
        return $this->memNo;
    }
    /**
     * @return array
     */
    public function getCartSno(): array
    {
        return $this->cartSno;
    }

    /**
     * @return array
     */
    public function getRegularGoodsPrice(): array
    {
        return $this->regularGoodsPrice;
    }

    /**
     * @return array
     */
    public function getRegularGoodsPriceOption(): array
    {
        return $this->regularGoodsPriceOption;
    }

    /**
     * @return array
     */
    public function getRegularGoodsPriceOptionText(): array
    {
        return $this->regularGoodsPriceOptionText;
    }

    /**
     * @return array
     */
    public function getRegularAddGoodsPrice(): array
    {
        return $this->regularAddGoodsPrice;
    }

    /**
     * @return array
     */
    public function getRegularGoodsDiscountUseFl(): array
    {
        return $this->regularGoodsDiscountUseFl;
    }

    /**
     * @return array
     */
    public function getGiftInfo(): array
    {
        return $this->giftInfo;
    }

    /**
     * @return array
     */
    public function getApplierInfo(): array
    {
        return $this->applierInfo;
    }

    /**
     * @return int
     */
    public function getTotalDeliveryCharge(): int
    {
        return $this->totalDeliveryCharge;
    }
    /**
     * @return int
     */
    public function getShippingSno(): int
    {
        return $this->shippingSno;
    }

    /**
     * @return int
     */
    public function getSettlePrice(): int
    {
        return $this->settlePrice;
    }

    /**
     * @return string
     */
    public function getEncryptCardNo(): string
    {
        return $this->encryptCardNo;
    }

    /**
     * @return string
     */
    public function getRegDt(): string
    {
        return $this->regDt;
    }
}
