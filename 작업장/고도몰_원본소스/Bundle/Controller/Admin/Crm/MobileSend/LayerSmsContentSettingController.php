<?php
/*
 * Copyright (C) 2026 NHN COMMERCE. - All Rights Reserved
 *
 * Unauthorized copying or redistribution of this file in source and binary forms via any medium
 * is strictly prohibited.
 */

namespace Bundle\Controller\Admin\Crm\MobileSend;

use Bundle\Util\Crm\RecipeContextResolverTrait;
use Origin\Service\Crm\Message\Sms080RejectService;

class LayerSmsContentSettingController extends \Controller\Admin\Controller
{
    use RecipeContextResolverTrait;

    public function index()
    {
        $this->callMenu('crm', 'messageSend', 'mobileSend');

        /** @var Sms080RejectService $sms080RejectService */
        $sms080RejectService = \App::getInstance(Sms080RejectService::class);
        $is080RejectAvailable = $sms080RejectService->isAvailable();

        $recipeContext = $this->resolveRecipeContext();
        $isRecipeContext = $this->hasRecipeContext();
        $initialMessage = $recipeContext['defaultCampaignRequest']['smsLmsRequest']['content'] ?? '';

        $this->setData('is080RejectAvailable', $is080RejectAvailable);
        $this->setData('isRecipeContext', $isRecipeContext);
        $this->setData('initialMessage', $initialMessage);
        $this->getView()->setDefine('layout', 'layout_layer.php');
    }
}
