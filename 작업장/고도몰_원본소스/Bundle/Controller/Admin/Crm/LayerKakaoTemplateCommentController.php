<?php
/*
 * Copyright (C) 2025 NHN COMMERCE. - All Rights Reserved
 *
 * Unauthorized copying or redistribution of this file in source and binary forms via any medium
 * is strictly prohibited.
 */

namespace Bundle\Controller\Admin\Crm;

use Framework\StaticProxy\Proxy\Logger;
use Origin\Service\Member\KakaoAlrim\KakaoAlrimCloudMessageTemplateService;
use Bundle\Component\Member\KakaoAlrim;

class LayerKakaoTemplateCommentController extends \Controller\Admin\Controller
{
    public function index()
    {
        $this->getView()->setDefine('layout', 'layout_layer.php');

        $request = \App::getInstance('request');
        $requestData = $request->request()->toArray();
        $provider = $requestData['provider'] ?? "";
        $templateCode = $requestData['templateCode'] ?? "";

        if ($provider === 'bizm'){
            $kakao = new KakaoAlrim;
            $messageTemplateComments = $kakao->getTemplateComment($templateCode);
            $comments = $messageTemplateComments['comments'] ?? [];
            $comment = !empty($comments) ? end($comments)->content ?? '' : '';
            $this->setData('comment', $comment);
        } else{
            /** @var KakaoAlrimCloudMessageTemplateService $service */
            $service = \App::getInstance(KakaoAlrimCloudMessageTemplateService::class);
            $messageTemplateComments = $service->getMessageTemplateComments($templateCode);
            $this->setData('comment', $messageTemplateComments);
        }

    }
}
