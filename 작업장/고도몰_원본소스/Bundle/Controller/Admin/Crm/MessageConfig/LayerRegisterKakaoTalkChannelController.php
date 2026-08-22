<?php
/*
 * Copyright (C) 2025 NHN COMMERCE. - All Rights Reserved
 *
 * Unauthorized copying or redistribution of this file in source and binary forms via any medium
 * is strictly prohibited.
 */

namespace Bundle\Controller\Admin\Crm\MessageConfig;

use Framework\Http\Request;
use Framework\Http\Response;
use Origin\Service\Member\KakaoAlrim\KakaoAlrimCloudRegistService;

class LayerRegisterKakaoTalkChannelController extends \Controller\Admin\Controller
{
    public function index()
    {
        /** @var Request $request */
        $request = \App::getInstance('request');
        $sender = $request->post()->get('sender');

        try {
            /** @var KakaoAlrimCloudRegistService $service */
            $service = \App::getInstance(KakaoAlrimCloudRegistService::class);
            [$firstLevelCategories, $secondLevelCategories, $thirdLevelCategories] = $service->getCategoryInfo();
        } catch (\Throwable $e) {
            $this->json(['success' => false], Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        $this->setData('sender', $sender);
        $this->setData('firstLevelCategories', gd_isset($firstLevelCategories));
        $this->setData('secondLevelCategories', gd_isset($secondLevelCategories));
        $this->setData('thirdLevelCategories', gd_isset($thirdLevelCategories));

        $this->getView()->setDefine('layout', 'layout_layer.php');
    }
}
