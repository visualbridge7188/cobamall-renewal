<?php
/*
 * Copyright (C) 2025 NHN COMMERCE. - All Rights Reserved
 *
 * Unauthorized copying or redistribution of this file in source and binary forms via any medium
 * is strictly prohibited.
 */

namespace Bundle\Controller\Mobile\Mypage;

use Component\RegularDelivery\RegularGoods\RegularGift;
use DTO\RegularDelivery\RegularOrder\RegularOrderGiftConditionDTO;

class RegularDeliveryGiftChangeController extends \Controller\Mobile\Controller
{
    public function index()
    {
        $regulaGift = \App::getInstance(RegularGift::class);

        $request = \Request::request()->toArray();

        $regularOrderGiftConditionDTO = new RegularOrderGiftConditionDTO($request);
        $regularGoodsGiftData = $regulaGift->getRegularGoodsGiftPresentInfoWithPrevGiftInfo($regularOrderGiftConditionDTO);

        // 템플릿에 데이터 전달
        $this->setData('applyNo', $request['applyNo']);
        $this->setData('changeGoodsFl', $request['changeGoodsFl']);
        $this->setData('regularGoodsNo', $request['regularGoodsNo']);
        $this->setData('regularGoodsCnt', $request['regularGoodsCnt']);
        $this->setData('regularAddGoodsCnt', $request['regularAddGoodsCnt']);
        $this->setData('regularGiftPresentInfoSno', $regularGoodsGiftData['regularGiftPresentInfoSno']);
        $this->setData('conditionTitle', $regularGoodsGiftData['conditionTitle']);
        $this->setData('totalMultiGiftNum', $regularGoodsGiftData['totalMultiGiftNum']);
        $this->setData('currentMultiGiftNum', $regularGoodsGiftData['currentMultiGiftNum']);
        $this->setData('selectCount', $regularGoodsGiftData['selectCount']);
        $this->setData('giveCount', $regularGoodsGiftData['giveCount']);
        $this->setData('prevGiftData', $regularGoodsGiftData['prevGiftData']);
        $this->setData('multiGiftData', $regularGoodsGiftData['multiGiftData']);
        $this->setData('page', $regularGoodsGiftData['page']);

        // 페이지 이름 설정
        $gPageName = __("사은품 변경");
        $this->setData('gPageName', $gPageName);
    }
}
