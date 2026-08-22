<?php
/*
 * Copyright (C) 2025 NHN COMMERCE. - All Rights Reserved
 *
 * Unauthorized copying or redistribution of this file in source and binary forms via any medium is strictly prohibited.
 */

namespace Bundle\Component\Present\Goods;

use Carbon\Carbon;
use Component\Goods\Goods;
use Component\Present\Exception\PresentGoodsLimitOverException;
use Component\Present\PresentGoodsInterface;
use Framework\Log\Logger;
use Repository\Present\Goods\PresentGoodsRepositoryInterface;

class BasePresentGoods implements PresentGoodsInterface
{
   const GOODS_LIMIT = 1000;

    public function __construct(
        protected Logger $logger,
        protected Goods $goods,
        protected PresentGoodsRepositoryInterface $presentGoodsRepository,
    )
    {
    }

    /**
     * 상품 저장 및 삭제
     *
     * @param array $datas
     * @return bool
     */
    public function save(array $datas): bool
    {
        $goodsNos = array_filter($datas);

        // 요청된 상품 번호 중 존재하지 않는 것만 저장
        $existGoodsNos = $this->presentGoodsRepository->findAllGoodsNo();

        // 인서트 데이터
        $insertGoodsNos = array_diff($goodsNos, $existGoodsNos);

        // 삭제 데이터
        $deleteGoodsNos = array_diff($existGoodsNos, $goodsNos);

        $this->validateGoodsCount($existGoodsNos, $insertGoodsNos, $deleteGoodsNos);

        // 인서트 데이터 생성
        $insertData = array_map(
            fn($goodsNo) => ['goodsNo' => $goodsNo, 'regDt' => Carbon::now()],
            $insertGoodsNos
        );

        $this->remove($deleteGoodsNos);
        $this->presentGoodsRepository->insert($insertData);
        return true;
    }

    /**
     * 상품 제한 수 검증
     *
     * @param array $existGoodsNos
     * @param array $insertGoodsNos
     * @param array $deleteGoodsNos
     * @throws PresentGoodsLimitOverException
     * @return void
     */
    protected function validateGoodsCount(array $existGoodsNos, array $insertGoodsNos, array $deleteGoodsNos): void
    {
        $calculatedFinalGoodsCount = count($existGoodsNos) + count($insertGoodsNos) - count($deleteGoodsNos);

        if ($calculatedFinalGoodsCount > self::GOODS_LIMIT) {
            throw new PresentGoodsLimitOverException('limit over');
        }
    }

    /**
     * 상품 정보 추가
     *
     * @param array $goodsItem
     * @return array
     */
    protected function addGoodsInfo(array $goodsItem): array
    {
        // 공통 이미지 태그 생성
        $goodsItem['goodsImageTag'] = gd_html_goods_image(
            $goodsItem['goodsNo'],
            $goodsItem['imageName'],
            $goodsItem['imagePath'],
            $goodsItem['imageStorage'],
            30,
            $goodsItem['goodsNm'],
            '_blank'
        );
        [
            $goodsItem['totalStock'],
            $goodsItem['stockText']
        ] = gd_is_goods_state($goodsItem['stockFl'], $goodsItem['totalStock'], $goodsItem['soldOutFl']);
        $goodsItem['goodsPrice'] = number_format($goodsItem['goodsPrice']) . '원';

        return $goodsItem;
    }

    /**
     * 상품 조회 - 페이징 데이터, 팝업 유지 데이터(모든 데이터)
     *
     * @return array
     */
    public function getAll(): array
    {
        $presentGoodsNo = $this->presentGoodsRepository->findAllGoodsNo();

        return $this->getGoodsDataDisplay($presentGoodsNo);
    }

    /**
     * 상품 번호를 통한 상품 데이터 조회
     *
     * @param array $goodsNos
     * @return array[]
     */
    protected function getGoodsDataDisplay(array $goodsNos): array
    {
        $goodsDisplay = [];
        if (!empty($goodsNos)) {
            $allGoodsDatas = $this->goods->getGoodsDataDisplay(implode(INT_DIVISION, $goodsNos)) ?: [];
            $goodsDisplay = array_map(
                fn($goodsData) => $this->addGoodsInfo($goodsData),
                $allGoodsDatas
            );
        }

        return $goodsDisplay;
    }

    /**
     * 상품 번호를 통한 상품 삭제
     *
     * @param array $deleteGoodsNos
     * @return void
     */
    public function remove(array $deleteGoodsNos): void
    {
        if (!empty($deleteGoodsNos)) {
            $this->presentGoodsRepository->deleteByGoodsNo($deleteGoodsNos);
        }
    }
}
