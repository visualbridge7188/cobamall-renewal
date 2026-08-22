<?php
/*
 * Copyright (C) 2025 NHN COMMERCE. - All Rights Reserved
 *
 * Unauthorized copying or redistribution of this file in source and binary forms via any medium
 * is strictly prohibited.
 */

namespace Bundle\Controller\Admin\Share;

use Bundle\Controller\Admin\Controller;
use Component\RegularDelivery\RegularGoods\RegularGoods;

class LayerRegularGoodsOptionController extends Controller
{
    public function index()
    {
        $goodsNo = \Request::get()->get('goodsNo');
        $applyNo = \Request::get()->get('applyNo');

        try {
            if (empty($goodsNo)) {
                throw new \Exception('상품번호가 존재하지 않습니다.');
            }

            // 상품 정보 조회
            $goods = \App::load('\\Component\\Goods\\GoodsAdmin');
            $goodsView = $goods->getGoodsView($goodsNo);

            $scmAdmin = \App::load('\\Component\\Scm\\ScmAdmin');
            $tmpData = $scmAdmin->getScmInfo($goodsView['scmNo'], 'companyNm');
            $goodsView['scmNm'] = $tmpData['companyNm'];

            // 정기배송 상품 데이터 추가
            $regularGoods = \App::getInstance(RegularGoods::class);
            $regularGoodsData = $regularGoods->getRegularGoodsData($goodsNo);
            $this->setData('regularGoodsData', $regularGoodsData);
            $this->setData('applyNo', $applyNo);

            // 정기배송 상품이 정률 할인일 경우 옵션 배열에 할인가 반영
            if ($regularGoodsData['discountUseFl'] === 'y' && $regularGoodsData['discountType'] === 'percent') {
                $discountRate = (float) $regularGoodsData['discountRate'];

                // 선택 옵션 정기배송 할인가 반영 처리
                if (!empty($goodsView['option'])) {
                    $goodsView['option'] = $regularGoods->applyRegularGoodsPercentDiscount($goodsView['option'], 'optionPrice', $discountRate);
                }

                // 문구 옵션 정기배송 할인가 반영 처리
                if (!empty($goodsView['optionText'])) {
                    $goodsView['optionText'] = $regularGoods->applyRegularGoodsPercentDiscount($goodsView['optionText'], 'addPrice', $discountRate);
                }
            }

            // default 구매 최소 수량
            $goodsView['defaultGoodsCnt'] = 1;
            if($goodsView['fixedOrderCnt'] == 'option') {
                $goodsView['defaultGoodsCnt'] = $goodsView['minOrderCnt'];
            }
            if($goodsView['fixedSales'] != 'goods' && ($goodsView['salesUnit'] > $goodsView['defaultGoodsCnt'])) {
                $goodsView['defaultGoodsCnt'] = $goodsView['salesUnit'];
            }

            $this->getView()->setDefine('layout', 'layout_layer.php');

            $this->setData('goodsView', $goodsView);
            $this->setData('currency', \Globals::get('gCurrency'));

            $mileage = $goodsView['mileageConf']['info'];
            $this->setData('mileageData', gd_isset($mileage));

            //상품 품절 설정 코드 불러오기
            $code = \App::load('\\Component\\Code\\Code');
            $optionSoldOutCode = $code->getGroupItems('05002');
            $optionSoldOutCode['n'] = $optionSoldOutCode['05002002'];
            $this->setData('optionSoldOutCode', $optionSoldOutCode);

            //상품 배송지연 설정 코드 불러오기
            $code = \App::load('\\Component\\Code\\Code');
            $optionDeliveryDelayCode = $code->getGroupItems('05003');
            $this->setData('optionDeliveryDelayCode', $optionDeliveryDelayCode);

            // 공급사와 동일한 페이지 사용
            $this->getView()->setPageName('share/layer_reegular_goods_option.php');

        } catch (\Exception $e) {
            \Logger::channel('regularGoods')->warning('정기결제 상품 레이어 옵션 조회 실패', [$e->getMessage(), $e->getTrace()]);
            throw $e;
        }

    }
}
