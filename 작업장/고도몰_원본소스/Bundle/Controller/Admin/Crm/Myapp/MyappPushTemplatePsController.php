<?php
/*
 * Copyright (C) 2026 NHN COMMERCE. - All Rights Reserved
 *
 * Unauthorized copying or redistribution of this file in source and binary forms via any medium
 * is strictly prohibited.
 */

namespace Bundle\Controller\Admin\Crm\Myapp;

use Origin\DTO\ApiClient\Commerce\Notification\MessageTemplateMyappDTO;
use Origin\Enum\Crm\Message\MyappPushTemplateCategory;
use Origin\Repository\Member\Myapp\MyappPushRepository;
use Origin\Service\Crm\Message\MyappPushTemplateService;
use Request;

class MyappPushTemplatePsController extends \Controller\Admin\Controller
{
    public function index()
    {
        $request = Request::post()->toArray();
        $mode = $request['mode'] ?? '';

        if ($mode === 'save') {
            $this->save($request);
        } else {
            $this->json(['result' => true, 'message' => '잘못된 요청입니다.']);
        }
    }

    private function save(array $request): void
    {
        $categoryValue = $request['category'] ?? '';
        $subject = $request['subject'] ?? '';
        $contents = $request['contents'] ?? '';
        $title = $request['title'] ?? '';
        $url = $request['url'] ?? '';
        $image = $request['image'] ?? '';

        $category = MyappPushTemplateCategory::tryFrom($categoryValue);

        if ($category === null) {
            $this->json(['success' => false, 'message' => '카테고리를 선택해주세요.']);
        }

        if (empty($subject)) {
            $this->json(['success' => false, 'message' => '제목을 입력해주세요.']);
        }

        if (empty($contents)) {
            $this->json(['success' => false, 'message' => '내용을 입력해주세요.']);
        }

        try {
            $repository = \App::getInstance(MyappPushRepository::class);
            $sno = $repository->saveByGodomallTemplate($category, $subject, $contents, $title, $url, $image);

            // 파일 업로드된 이미지가 있으면 서버에 저장
            $files = Request::files()->toArray();
            if (!empty($files['imageFile']['tmp_name']) && (int)$files['imageFile']['error'] === UPLOAD_ERR_OK) {
                /** @var MyappPushTemplateService $templateService */
                $templateService = \App::getInstance(MyappPushTemplateService::class);
                $files['imageFile']['pushImage'] = $files['imageFile']['tmp_name'];
                $templateService->processAndSaveImage(
                    $sno,
                    new MessageTemplateMyappDTO(['pushSno' => $sno]),
                    $files['imageFile']
                );
            }

            $this->json(['success' => true, 'message' => '템플릿이 저장되었습니다.']);
        } catch (\Exception $e) {
            $this->json(['success' => false, 'message' => '저장에 실패했습니다.']);
        }
    }
}
