<?php
/*
 * Copyright (C) 2025 NHN COMMERCE. - All Rights Reserved
 *
 * Unauthorized copying or redistribution of this file in source and binary forms via any medium is strictly prohibited.
 */

namespace Bundle\DTO\Cart;

use Origin\DTO\AbstractDTO;

class CartRequestDTO extends AbstractDTO
{
    protected const USE_BUNDLE_GOODS_DEFAULT = 1;

    /** @var int 공급사 번호 */
    protected int $scmNo;
    
    /** @var array 상품번호 배열 */
    protected array $goodsNo;
    
    /** @var array 옵션번호 배열 */
    protected array $optionSno;
    
    /** @var array 상품수량 배열 */
    protected array $goodsCnt;
    
    /** @var int|float 총 가격 */
    protected int|float $setTotalPrice;
    
    /** @var string 배송비 선불/착불 */
    protected string $deliveryCollectFl;
    
    /** @var string 배송방법 */
    protected string $deliveryMethodFl;
    
    /** @var string 장바구니 모드 ('' = 장바구니, 'd' = 바로구매) */
    protected string $cartMode;
    
    /** @var int 번들 상품 사용 여부 */
    protected int $useBundleGoods;
    
    /** @var array|null 텍스트 옵션 배열 */
    protected ?array $optionText;
    
    /** @var array|null 추가상품번호 배열 */
    protected ?array $addGoodsNo;
    
    /** @var array|null 추가상품수량 배열 */
    protected ?array $addGoodsCnt;

    public function __construct(
        int $scmNo,
        array $goodsNo,
        array $optionSno,
        array $goodsCnt,
        int|float $setTotalPrice,
        string $deliveryCollectFl,
        string $deliveryMethodFl,
        string $cartMode = '',
        int $useBundleGoods = self::USE_BUNDLE_GOODS_DEFAULT,
        ?array $optionText = null,
        ?array $addGoodsNo = null,
        ?array $addGoodsCnt = null
    ) {
        $this->scmNo = $scmNo;
        $this->goodsNo = $goodsNo;
        $this->optionSno = $optionSno;
        $this->goodsCnt = $goodsCnt;
        $this->setTotalPrice = $setTotalPrice;
        $this->deliveryCollectFl = $deliveryCollectFl;
        $this->deliveryMethodFl = $deliveryMethodFl;
        $this->cartMode = $cartMode;
        $this->useBundleGoods = $useBundleGoods;
        $this->optionText = $optionText;
        $this->addGoodsNo = $addGoodsNo;
        $this->addGoodsCnt = $addGoodsCnt;
    }

    /**
     * @return int
     */
    public function getScmNo(): int
    {
        return $this->scmNo;
    }

    /**
     * @return array
     */
    public function getGoodsNo(): array
    {
        return $this->goodsNo;
    }

    /**
     * @return array
     */
    public function getOptionSno(): array
    {
        return $this->optionSno;
    }

    /**
     * @return array
     */
    public function getGoodsCnt(): array
    {
        return $this->goodsCnt;
    }

    /**
     * @return int|float
     */
    public function getSetTotalPrice(): int|float
    {
        return $this->setTotalPrice;
    }

    /**
     * @return string
     */
    public function getDeliveryCollectFl(): string
    {
        return $this->deliveryCollectFl;
    }

    /**
     * @return string
     */
    public function getDeliveryMethodFl(): string
    {
        return $this->deliveryMethodFl;
    }

    /**
     * @return string
     */
    public function getCartMode(): string
    {
        return $this->cartMode;
    }

    /**
     * @return int
     */
    public function getUseBundleGoods(): int
    {
        return $this->useBundleGoods;
    }

    /**
     * @return array|null
     */
    public function getOptionText(): ?array
    {
        return $this->optionText;
    }

    /**
     * @return array|null
     */
    public function getAddGoodsNo(): ?array
    {
        return $this->addGoodsNo;
    }

    /**
     * @return array|null
     */
    public function getAddGoodsCnt(): ?array
    {
        return $this->addGoodsCnt;
    }

    /**
     * 배열로 변환 (set_total_price 키명 유지)
     *
     * @return array
     */
    public function toArray(): array
    {
        $data = parent::toArray();
        
        // setTotalPrice를 set_total_price로 변환
        if (isset($data['setTotalPrice'])) {
            $data['set_total_price'] = $data['setTotalPrice'];
            unset($data['setTotalPrice']);
        }
        
        return $data;
    }
}

