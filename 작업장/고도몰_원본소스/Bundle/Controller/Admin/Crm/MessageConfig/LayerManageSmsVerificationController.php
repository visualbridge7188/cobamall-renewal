<?php
/*
 * Copyright (C) 2025 NHN COMMERCE. - All Rights Reserved
 *
 * Unauthorized copying or redistribution of this file in source and binary forms via any medium
 * is strictly prohibited.
 */

namespace Bundle\Controller\Admin\Crm\MessageConfig;

use Framework\Http\Request;
use Origin\Service\Crm\Message\SmsPasswordService;

class LayerManageSmsVerificationController extends \Controller\Admin\Controller
{
    public function index()
    {
        /** @var Request $request */
        $request = \App::getInstance('request');
        $session = \App::getInstance('session');
        $displayInfo = [
            'title'      => 'SMS 인증번호',
            'useCaptcha' => false,
            'retry'      => $session->get('captchaRetry', 1),
        ];

        if ($request->request()->get('mode', 'input') === 'change') {
            $displayInfo['useCaptcha'] = true;
        }

        $restrictedKeywords = SmsPasswordService::RESTRICTED_KEYWORDS;

        $this->setData('displayInfo', $displayInfo);
        $this->setData('restrictedKeywords', $restrictedKeywords);
        $this->getView()->setDefine('layout', 'layout_layer.php');
    }
}
