<?php
/*
 * Copyright (C) 2026 NHN COMMERCE. - All Rights Reserved
 *
 * Unauthorized copying or redistribution of this file in source and binary forms via any medium
 * is strictly prohibited.
 */

namespace Bundle\Controller\Admin\Crm\MobileSend;

use Bundle\Util\Crm\RecipeContextResolverTrait;
use Origin\Enum\Crm\Message\CampaignMessageType;
use Origin\Service\Crm\Message\KakaoFriendTalkSettingService;
use Origin\Service\Crm\Message\Sms080RejectService;

class LayerKakaoFriendtalkContentSettingController extends \Controller\Admin\Controller
{
    use RecipeContextResolverTrait;

    public function index()
    {
        $this->callMenu('crm', 'messageSend', 'mobileSend');

        /** @var KakaoFriendTalkSettingService $kakaoFriendTalkSettingService */
        $kakaoFriendTalkSettingService = \App::getInstance(KakaoFriendTalkSettingService::class);
        $kakaoFriendTalkConfig = $kakaoFriendTalkSettingService->getKakaoFriendTalkConfig();

        /** @var Sms080RejectService $sms080RejectService */
        $sms080RejectService = \App::getInstance(Sms080RejectService::class);
        $is080RejectAvailable = $sms080RejectService->isAvailable();

        $campaignMessageTypeUnitPoint = [];
        foreach (CampaignMessageType::cases() as $type) {
            $campaignMessageTypeUnitPoint[$type->name] = $type->unitPoint();
        }

        $recipeContext = $this->resolveRecipeContext();
        $isRecipeContext = $this->hasRecipeContext();
        $defaultCampaignRequest = $recipeContext['defaultCampaignRequest'] ?? [];
        $campaignName = $recipeContext['recipeTitle'] ?? '';
        $friendtalkRequest = $defaultCampaignRequest['kakaoFriendtalkRequest'] ?? [];
        $initialTextContent = $friendtalkRequest['text']['content'] ?? '';
        $initialAlternativeMessage = $defaultCampaignRequest['smsLmsRequest']['content'] ?? '';

        $this->setData('is080RejectAvailable', $is080RejectAvailable);
        $this->setData('campaignMessageTypes', CampaignMessageType::cases());
        $this->setData('campaignMessageTypeUnitPoints', $campaignMessageTypeUnitPoint);
        $this->setData('useSmsAlternativeSendFlag', $kakaoFriendTalkConfig->getSmsAlternativeSendFlag());
        $this->setData('linkPlatformType', $kakaoFriendTalkConfig->getLinkPlatformType()->name);
        $this->setData('isRecipeContext', $isRecipeContext);
        $this->setData('campaignName', $campaignName);
        $this->setData('friendtalkRequest', $friendtalkRequest);
        $this->setData('initialTextContent', $initialTextContent);
        $this->setData('initialAlternativeMessage', $initialAlternativeMessage);
        $this->getView()->setDefine('layout', 'layout_layer.php');
    }
}
