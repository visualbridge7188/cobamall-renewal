<?php
/*
 * Copyright (C) 2026 NHN COMMERCE. - All Rights Reserved
 *
 * Unauthorized copying or redistribution of this file in source and binary forms via any medium
 * is strictly prohibited.
 */

namespace Bundle\Controller\Admin\Crm\MobileMessageHistory;

use Framework\Http\Request;
use Framework\Http\Response;
use Origin\Enum\ApiClient\Apps\KakaoFriendTalkType;
use Origin\Service\Crm\Message\KakaoFriendTalkHistoryService;

class LayerKakaoFriendTalkContentsController extends \Controller\Admin\Controller
{
    public function index()
    {
        $this->callMenu('crm', 'messageHistory', 'mobileHistoryList');
        $this->setMenuCode('crm', 'messageHistory', 'mobileHistoryList');

        /** @var Request $request */
        $request = \App::getInstance('request');
        $sendHistoryNo = $request->post()->get('sendHistoryNo');

        try {
            /** @var KakaoFriendTalkHistoryService $kakaoFriendTalkHistoryService */
            $kakaoFriendTalkHistoryService = \App::getInstance(KakaoFriendTalkHistoryService::class);
            $kakaoFriendTalkHistoryContentDTO = $kakaoFriendTalkHistoryService->getKakaoFriendTalkHistoryContent($sendHistoryNo);
        } catch (\Throwable $e) {
            $this->json(['success' => false], Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        \Logger::channel('mobileMessage')->info('content', $kakaoFriendTalkHistoryContentDTO->getContent());

        $this->setData('type', $kakaoFriendTalkHistoryContentDTO->getType());
        $this->setData('isText', $kakaoFriendTalkHistoryContentDTO->getType() === KakaoFriendTalkType::TEXT);
        $this->setData('isCarousel', $kakaoFriendTalkHistoryContentDTO->getType() === KakaoFriendTalkType::CAROUSEL);
        $this->setData('isWideItemList', $kakaoFriendTalkHistoryContentDTO->getType() === KakaoFriendTalkType::WIDE_ITEM_LIST);
        $this->setData('wideTypes', KakaoFriendTalkType::wideTypes());
        $this->setData('availableImageTypes', KakaoFriendTalkType::availableImageTypes());
        $this->setData('singleButtonTypes', KakaoFriendTalkType::singleButtonTypes());
        $this->setData('doubleButtonTypes', KakaoFriendTalkType::doubleButtonTypes());
        $this->setData('header', $kakaoFriendTalkHistoryContentDTO->getHeader());
        $this->setData('imageUrl', $kakaoFriendTalkHistoryContentDTO->getImageUrl());
        $this->setData('imageLink', $kakaoFriendTalkHistoryContentDTO->getImageLink());
        $this->setData('items', $kakaoFriendTalkHistoryContentDTO->getItems());
        $this->setData('content', $kakaoFriendTalkHistoryContentDTO->getContent());
        $this->setData('buttons', $kakaoFriendTalkHistoryContentDTO->getButtons());
        $this->setData('coupon', $kakaoFriendTalkHistoryContentDTO->getCoupon());
        $this->setData('carousels', $kakaoFriendTalkHistoryContentDTO->getCarousels());

        $this->getView()->setDefine('layout', 'layout_layer.php');
    }
}
