<?php
/*
 * Copyright (C) 2025 NHN COMMERCE. - All Rights Reserved
 *
 * Unauthorized copying or redistribution of this file in source and binary forms via any medium
 * is strictly prohibited.
 */

namespace Bundle\Controller\Admin\Crm\MessageConfig;

use Origin\Service\Member\KakaoAlrim\KakaoAlrimCloudSettingService;

class LayerCloudKakaoTalkChannelController extends \Controller\Admin\Controller
{
    public function index()
    {
        /** @var KakaoAlrimCloudSettingService $kakaoAlrimCloudSettingService */
        $kakaoAlrimCloudSettingService = \App::getInstance(KakaoAlrimCloudSettingService::class);
        $kakaoAlrimCloudConfig = $kakaoAlrimCloudSettingService->getKakaoAlrimCloudData();

        // PG 설정 불러오기
        $pgConfig = gd_pgs();
        $isConnectedPg = ($pgConfig['pgAutoSetting'] === 'y' || $pgConfig['pgApprovalSetting'] === 'y') ? 'y' : 'n';

        $this->setData('isConnectedPg', $isConnectedPg);
        $this->setData('kakaoAlrimCloudConfig', $kakaoAlrimCloudConfig);

        $this->getView()->setDefine('layout', 'layout_layer.php');
    }
}
