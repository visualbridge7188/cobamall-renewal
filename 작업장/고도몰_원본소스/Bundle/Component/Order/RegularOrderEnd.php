<?php
/*
 * Copyright (C) 2025 NHN COMMERCE. - All Rights Reserved
 *
 * Unauthorized copying or redistribution of this file in source and binary forms via any medium
 * is strictly prohibited.
 */

namespace Bundle\Component\Order;

use Framework\Security\Encryptor;
use Repository\RegularDelivery\RegularOrder\RegularOrderAddGoodsRepository;
use Repository\RegularDelivery\RegularOrder\RegularOrderGoodsRepository;
use Origin\Repository\Payment\PgCardInfoRepositoryInterface;
use Repository\RegularDelivery\RegularOrder\RegularOrderRepository;

class RegularOrderEnd
{
    const CARD_MASKING = '****-****-****-';

    /**
     * @var RegularOrderRepository
     */
    private $regularOrderRepository;
    /**
     * @var RegularOrderGoodsRepository
     */
    private $regularOrderGoodsRepository;
    /**
     * @var PgCardInfoRepositoryInterface
     */
    private $cardInfoRepository;
    /**
     * @var Encryptor
     */
    private $encryptor;
    /**
     * @var RegularOrderAddGoodsRepository
     */
    private $regularOrderAddGoodsRepository;

    public function __construct(
        RegularOrderRepository $regularOrderRepository,
        RegularOrderGoodsRepository $regularOrderGoodsRepository,
        PgCardInfoRepositoryInterface $cardInfoRepository,
        RegularOrderAddGoodsRepository $regularOrderAddGoodsRepository,
        Encryptor $encryptor
    )
    {
        $this->regularOrderRepository = $regularOrderRepository;
        $this->regularOrderGoodsRepository = $regularOrderGoodsRepository;
        $this->cardInfoRepository = $cardInfoRepository;
        $this->regularOrderAddGoodsRepository = $regularOrderAddGoodsRepository;
        $this->encryptor = $encryptor;

    }

    /**
     * 신청 완료 페이지에 나올 정보
     * @param string $applyGroupNo
     * @return array
     * @throws \Exception
     */
    public function getOrderEndInfo(string $applyGroupNo): array
    {
        $memNo = \Session::get('member.memNo');
        $regularOrderSummary = $this->regularOrderRepository->findOrderSummaryByApplyGroupNo($applyGroupNo);
        if (empty($regularOrderSummary)) {
            return [];
        }
        if ($regularOrderSummary[0]['memNo'] != $memNo) {
            throw new \Exception('신청한 회원만 조회 가능합니다.');
        }

        // 카드는 동일 카드로 결제 됨으로 첫 index 카드로 조회
        $cardInfo = $this->cardInfoRepository->findUsedCardByCardNoAndMemNo($regularOrderSummary[0]['cardNo'], $memNo);
        if (empty($cardInfo)) {
            throw new \Exception('카드정보가 없습니다.');
        }
        $cardName = $cardInfo['cardNm'];
        $cardNo = $this->encryptor->decrypt($cardInfo['cardNo']);

        // 추가상품 조회
        $orderGoodsName = [];
        foreach ($regularOrderSummary as $orderSummary) {
            $applyNo = $orderSummary['applyNo'];
            $regularOrderAddGoods = $this->regularOrderAddGoodsRepository->findRegularOrderAddGoodsByApplyNo($applyNo);
            $addGoodsNameSuffix = '';
            if (!empty($regularOrderAddGoods)) {
                $addGoodsNameSuffix = count($regularOrderAddGoods) > 0 ? ' 외 ' . count($regularOrderAddGoods) . '건' : '';
            }
            $orderGoodsName[$applyNo] = $orderSummary['goodsNm'] . $addGoodsNameSuffix;
        }

        return [
            'applyNoList' => array_column($regularOrderSummary, 'applyNo'),
            'orderInfo' => $regularOrderSummary[0],
            'orderGoodsName' => $orderGoodsName,
            'cardName'=> $cardName,
            'cardNo'=> self::CARD_MASKING.$cardNo
        ];
    }
}
