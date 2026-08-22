<?php
/*
 * Copyright (C) 2025 NHN COMMERCE. - All Rights Reserved
 *
 * Unauthorized copying or redistribution of this file in source and binary forms via any medium is strictly prohibited.
 */

namespace Bundle\Controller\Admin\Goods;

use Component\Present\Card\PresentCard;
use Component\Present\Category\PresentCategory;
use Component\Present\Config\PresentConfig;
use Component\Present\Exception\PresentCardLimitOverException;
use Component\Present\Goods\BasePresentGoods;
use DTO\Present\Card\PresentCardDTO;
use Factory\Present\PresentGoodsFactory;

class PresentConfigController extends \Controller\Admin\Controller
{
    public function index()
    {
        // --- 메뉴 설정
        $this->callMenu('goods', 'goods', 'presentGoodsConfig');

        // --- 선물하기 config 불러오기
        $presentConfig = \App::getInstance(PresentConfig::class);
        $config = $presentConfig->getConfig();

        // --- 기본값 설정
        if (empty($config['useFl'])) {
            $config['useFl'] = 'n';
        }

        $checked = [
            'useFl' => [
                ($config['useFl'] ?: $presentConfig::DEFAULT_USE) => 'checked'
            ],
            'expirationPeriod' => [
                ($config['expirationPeriod'] ?: $presentConfig::DEFAULT_EXPIRATION_PERIOD) => 'checked'
            ],
            'applyType' => [
                ($config['applyType'] ?: $presentConfig::DEFAULT_APPLY_TYPE) => 'checked'
            ],
        ];

        $display = array_fill_keys($presentConfig->getApplyType(), 'style="display:none;"');
        $display['useFl'] = ($config['useFl'] === 'y') ? '' : 'style="display:none;"';
        $display[$config['applyType']] = '';

        $presentCategory = \App::getInstance(PresentCategory::class);
        $categories = $presentCategory->getCategoryListWithChecked();

        $presentCard = \App::getInstance(PresentCard::class);
        $presentCards = $presentCard->getAll();
        $cards = array_map(fn($card) => PresentCardDTO::fromArray($card), $presentCards);

        $goodsType = match($presentConfig->getConfig()['applyType']) {
            'category' => 'exceptGoods',
            default => 'applyGoods'
        };

        $presentGoods = PresentGoodsFactory::create($goodsType);
        $presentGoodsDatas = $presentGoods->getAll();

        $session = \App::getInstance('session');
        $showPresentBanner = $session->get('showPresentBanner');

        // --- 관리자 디자인 템플릿
        $this->setData('showPresentBanner', $showPresentBanner);
        $this->setData('goodsLimit', BasePresentGoods::GOODS_LIMIT);
        $this->setData('data', $config);
        $this->setData('checked', $checked);
        $this->setData('display', $display);
        $this->setData('categories', $categories);
        $this->setData('cardLimit', PresentCard::UPLOAD_CARD_LIMIT);
        $this->setData('minDisplayCardCount', PresentCard::MIN_DISPLAY_CARD_COUNT);
        $this->setData('cards', $cards);
        $this->setData('goodsType', $goodsType);
        $this->setData('presentGoodsDatas', $presentGoodsDatas);

        $this->addScript([
            'jquery/jquery.multi_select_box.js',
        ]);

        $this->getView()->setDefine('functionConfig', 'goods/present_config/function_config.php');
        $this->getView()->setDefine('giftCardConfig', 'goods/present_config/gift_card_config.php');
        $this->getView()->setDefine('goodsConfig', 'goods/present_config/goods_config.php');

        $this->setData('adminBodySubClass', 'ncds');
    }
}
