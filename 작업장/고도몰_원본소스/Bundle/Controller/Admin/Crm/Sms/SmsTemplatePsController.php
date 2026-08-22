<?php
/*
 * Copyright (C) 2026 NHN COMMERCE. - All Rights Reserved
 *
 * Unauthorized copying or redistribution of this file in source and binary forms via any medium
 * is strictly prohibited.
 */

namespace Bundle\Controller\Admin\Crm\Sms;

use Origin\DTO\ApiClient\Commerce\Notification\MessageTemplateSmsDTO;
use Origin\Enum\Crm\Message\SmsTemplateCategory;
use Origin\Repository\Member\Sms\SmsContentsRepository;
use Origin\Service\Crm\Message\SmsTemplateService;
use Request;

class SmsTemplatePsController extends \Controller\Admin\Controller
{
    public function index()
    {
        $request = Request::post()->toArray();
        $mode = $request['mode'] ?? '';

        $service = \App::getInstance(SmsTemplateService::class);

        switch ($mode) {
            case 'save':
                $categoryValue = $request['category'] ?? '';
                $category = SmsTemplateCategory::tryFrom($categoryValue);
                if ($category === null) {
                    $this->json(['success' => false, 'message' => '카테고리를 선택해주세요.']);
                    return;
                }

                $subject = trim($request['subject'] ?? '');
                if ($subject === '') {
                    $this->json(['success' => false, 'message' => '제목을 입력해주세요.']);
                    return;
                }

                $contents = trim($request['contents'] ?? '');
                if ($contents === '') {
                    $this->json(['success' => false, 'message' => '내용을 입력해주세요.']);
                    return;
                }

                $templateDto = MessageTemplateSmsDTO::fromRegisterRequest([
                    'templateCategory' => $category->value,
                    'templateTitle' => $subject,
                    'templateMessageContent' => $contents,
                ]);

                $sno = $service->setSmsContents($templateDto);

                if ($sno == 0) {
                    $this->json(['success' => false, 'message' => '템플릿 저장에 실패하였습니다.']);
                } else {
                    $this->json(['success' => true, 'data' => ['sno' => $sno], 'message' => '템플릿이 저장되었습니다.']);
                }

                return;

            default:
                $this->json(['error' => 'Invalid mode', 'data' => []]);
                return;
        }
    }
}
