<?php
/*
 * Copyright (C) 2026 NHN COMMERCE. - All Rights Reserved
 *
 * Unauthorized copying or redistribution of this file in source and binary forms via any medium is strictly prohibited.
 */

namespace Bundle\Controller\Admin\Crm\MessageConfig;

use Bundle\Component\Member\KakaoAlrim;

class LayerBizmKakaoTalkChannelController extends \Controller\Admin\Controller
{
    public function index()
    {
        $kakaoAlrimBizmConfig = gd_policy('kakaoAlrim.config');

        gd_isset($kakaoAlrimBizmConfig['plusId'], '');
        gd_isset($kakaoAlrimBizmConfig['kakaoKey'], '');
        gd_isset($kakaoAlrimBizmConfig['phoneNumber'], '');
        gd_isset($kakaoAlrimBizmConfig['useFlag'], 'n');
        gd_isset($kakaoAlrimBizmConfig['approvalFl'], 'n');

        $oKakao = new KakaoAlrim;
        $senderProfileLookup = $oKakao->senderProfileLookup();
        $kakaoAlrimBizmConfig['status'] = $senderProfileLookup->senderProfileStatus;

        // PG 설정 불러오기
        $pgConfig = gd_pgs();
        $isConnectedPg = ($pgConfig['pgAutoSetting'] === 'y' || $pgConfig['pgApprovalSetting'] === 'y') ? 'y' : 'n';

        $this->setData('isConnectedPg', $isConnectedPg);
        $this->setData('kakaoAlrimBizmConfig', $kakaoAlrimBizmConfig);

        $this->getView()->setDefine('layout', 'layout_layer.php');
    }
}
