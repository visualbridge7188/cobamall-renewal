<?php
/*
 * Copyright (C) 2025 NHN COMMERCE. - All Rights Reserved
 *
 * Unauthorized copying or redistribution of this file in source and binary forms via any medium
 * is strictly prohibited.
 */

namespace Bundle\Controller\Admin\Crm\MessageConfig;


use Framework\Enum\ExternalUrl;

class LayerLunaKakaoTalkChannelController extends \Controller\Admin\Controller
{
    public function index()
    {
        $kakaoAlrimLunaConfig = gd_policy('kakaoAlrimLuna.config');

        gd_isset($kakaoAlrimLunaConfig['useFlag'], 'n');
        gd_isset($kakaoAlrimLunaConfig['lunaCliendId'], '');
        gd_isset($kakaoAlrimLunaConfig['lunaClientKey'], '');

        $this->setData('kakaoAlrimLunaConfig', $kakaoAlrimLunaConfig);
        $this->setData('lunaKakaoRequestUrl', ExternalUrl::GODO_KAKAO_API->getUrl('/lunaKakaoRequest.php'));

        $this->getView()->setDefine('layout', 'layout_layer.php');
    }
}
