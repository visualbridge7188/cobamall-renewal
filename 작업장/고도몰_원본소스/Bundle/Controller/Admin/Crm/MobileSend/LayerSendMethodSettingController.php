<?php
/*
 * Copyright (C) 2026 NHN COMMERCE. - All Rights Reserved
 *
 * Unauthorized copying or redistribution of this file in source and binary forms via any medium
 * is strictly prohibited.
 */

namespace Bundle\Controller\Admin\Crm\MobileSend;

use Bundle\Util\Crm\RecipeContextResolverTrait;
use Origin\Enum\Crm\Message\SendMethod;
use Origin\Service\Crm\Message\Sms080RejectService;

class LayerSendMethodSettingController extends \Controller\Admin\Controller
{
    use RecipeContextResolverTrait;

    public function index()
    {
        $this->callMenu('crm', 'messageSend', 'mobileSend');

        /** @var Sms080RejectService $sms080RejectService */
        $sms080RejectService = \App::getInstance(Sms080RejectService::class);
        $smsRejectPolicy = $sms080RejectService->getConfig();
        $smsRejectText = ($smsRejectPolicy['rejectNumber'] != '' && $smsRejectPolicy['status'] == 'O' && $smsRejectPolicy['use'] == 'y') ? "수신거부 {$smsRejectPolicy['rejectNumber']}" : '';

        $recipeContext = $this->resolveRecipeContext();
        $isRecipeContext = $this->hasRecipeContext();
        $messageSendGuide = $recipeContext['guideMessages']['messageSendGuide'] ?? '';

        $this->setData('sendMethods', SendMethod::cases());
        $this->setData('smsRejectText', $smsRejectText);
        $this->setData('isRecipeContext', $isRecipeContext);
        $this->setData('messageSendGuide', $messageSendGuide);
        $this->getView()->setDefine('layout', 'layout_layer.php');
    }
}
