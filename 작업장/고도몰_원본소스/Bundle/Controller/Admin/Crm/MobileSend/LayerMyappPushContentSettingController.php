<?php
/*
 * Copyright (C) 2026 NHN COMMERCE. - All Rights Reserved
 *
 * Unauthorized copying or redistribution of this file in source and binary forms via any medium
 * is strictly prohibited.
 */

namespace Bundle\Controller\Admin\Crm\MobileSend;

use Bundle\Util\Crm\RecipeContextResolverTrait;
use Framework\Utility\StringUtils;
use Origin\Enum\Crm\Message\MyappPushNotificationType;
use Origin\Enum\Crm\Message\MyappPushSendCondition;
use Origin\Enum\Crm\Message\MyappPushSendPlatform;
use Origin\Service\Crm\Message\MyappPushTemplateService;
use Origin\Service\Crm\Message\Sms080RejectService;

class LayerMyappPushContentSettingController extends \Controller\Admin\Controller
{
    use RecipeContextResolverTrait;

    public function index()
    {
        $this->callMenu('crm', 'messageSend', 'mobileSend');

        /** @var Sms080RejectService $sms080RejectService */
        $sms080RejectService = \App::getInstance(Sms080RejectService::class);
        $is080RejectAvailable = $sms080RejectService->isAvailable();

        /** @var MyappPushTemplateService $myappPushTemplateService */
        $myappPushTemplateService = \App::getInstance(MyappPushTemplateService::class);
        $myappPushUrl = $myappPushTemplateService->getMyappDomainUrl();

        $recipeContext = $this->resolveRecipeContext();
        $isRecipeContext = $this->hasRecipeContext();
        $myappRequest = $recipeContext['defaultCampaignRequest']['myappRequest'] ?? [];
        $initialTitle = $myappRequest['title'] ?? '';
        $initialMessage = $myappRequest['content'] ?? '';
        $initialAlternativeMessage = $recipeContext['defaultCampaignRequest']['smsLmsRequest']['content'] ?? '';

        $this->setData('is080RejectAvailable', $is080RejectAvailable);
        $this->setData('myappPushSendConditions', MyappPushSendCondition::cases());
        $this->setData('myappPushNotificationTypes', MyappPushNotificationType::cases());
        $this->setData('myappPushSendPlatforms', MyappPushSendPlatform::cases());
        $this->setData('myappPushUrl', $myappPushUrl);
        $this->setData('isRecipeContext', $isRecipeContext);
        $this->setData('initialTitle', $initialTitle);
        $this->setData('initialMessage', $initialMessage);
        $this->setData('initialAlternativeMessage', $initialAlternativeMessage);
        $this->getView()->setDefine('layout', 'layout_layer.php');
    }
}
