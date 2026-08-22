<?php
/*
 * Copyright (C) 2026 NHN COMMERCE. - All Rights Reserved
 *
 * Unauthorized copying or redistribution of this file in source and binary forms via any medium is strictly prohibited.
 */

namespace Bundle\Controller\Admin\Crm\MyappPush;

use Origin\DTO\ApiClient\Commerce\Notification\MessageTemplateMyappDTO;
use Origin\Enum\Crm\Message\MyappPushTemplateCategory;
use Origin\Service\Crm\Message\MyappPushTemplateService;
use Request;

class MyappPushTemplatePsController extends \Controller\Admin\Controller
{
    public function index()
    {
        $request = Request::post()->toArray();
        $mode = $request['mode'] ?? '';

        $service = \App::getInstance(MyappPushTemplateService::class);

        switch ($mode) {
            case 'save':
                $categoryValue = $request['category'] ?? '';
                $category = MyappPushTemplateCategory::tryFrom($categoryValue);
                if ($category === null) {
                    $this->json(['success' => false, 'message' => '카테고리를 선택해주세요.']);
                    return;
                }

                $templateName = trim($request['templateName'] ?? '');
                if ($templateName === '') {
                    $this->json(['success' => false, 'message' => '템플릿명을 입력해주세요.']);
                    return;
                }

                $pushContent = trim($request['pushContent'] ?? '');
                if ($pushContent === '') {
                    $this->json(['success' => false, 'message' => '푸시 내용을 입력해주세요.']);
                    return;
                }

                $pushSubject = trim($request['pushSubject'] ?? '');

                $dto = new MessageTemplateMyappDTO([
                    'pushType' => $category->value,
                    'templateName' => $templateName,
                    'pushSubject' => $pushSubject,
                    'pushContent' => $pushContent,
                    'pushImage' => $request['pushImage'],
                    'pushUrl' => $request['pushUrl'],
                    'pushWithdraw' => $request['pushWithdraw'],
                ]);

                $pushSno = $service->saveMyappTemplate($dto);

                if ($pushSno == 0) {
                    $this->json(['success' => false, 'message' => '템플릿 저장에 실패하였습니다.']);
                } else {
                    $this->json(['success' => true, 'data' => ['sno' => $pushSno], 'message' => '템플릿이 저장되었습니다.']);
                }

                return;

            default:
                $this->json(['error' => 'Invalid mode', 'data' => []]);
                return;
        }
    }
}
