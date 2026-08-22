<?php
/*
 * Copyright (C) 2026 NHN COMMERCE. - All Rights Reserved
 *
 * Unauthorized copying or redistribution of this file in source and binary forms via any medium is strictly prohibited.
 */

namespace Bundle\Controller\Admin\Crm\MobileSend;

use Framework\Utility\StringUtils;
use Origin\Enum\Crm\Message\KakaoAlimTalkTemplateType;
use Origin\Service\Crm\Message\Sms080RejectService;
use Origin\Service\Member\KakaoAlrim\KakaoAlrimCloudSettingService;

class LayerKakaoAlimtalkContentSettingController extends \Controller\Admin\Controller
{
    public function index()
    {
        $this->callMenu('crm', 'messageSend', 'mobileSend');

        /* @var KakaoAlrimCloudSettingService $kakaoAlimCloudsSettingService*/
        $kakaoAlimCloudsSettingService = \App::getInstance(KakaoAlrimCloudSettingService::class);

        $kakaoAlimTalkTemplateTypes = KakaoAlimTalkTemplateType::cases();
        $allowedKeys = array_map(fn($type) => strtolower($type->name), $kakaoAlimTalkTemplateTypes);
        $kakaoAlimtalkTemplates = array_filter(
            $kakaoAlimCloudsSettingService->getTemplates(),
            fn($key) => in_array($key, $allowedKeys),
            ARRAY_FILTER_USE_KEY
        );

        /** @var Sms080RejectService $sms080RejectService */
        $sms080RejectService = \App::getInstance(Sms080RejectService::class);
        $is080RejectAvailable = $sms080RejectService->isAvailable();

        $this->setData('is080RejectAvailable', $is080RejectAvailable);
        $this->setData('kakaoAlimTalkTemplateTypes', $kakaoAlimTalkTemplateTypes);
        $this->setData('kakaoAlimtalkTemplates', $kakaoAlimtalkTemplates);
        $this->getView()->setDefine('layout', 'layout_layer.php');
    }
}
