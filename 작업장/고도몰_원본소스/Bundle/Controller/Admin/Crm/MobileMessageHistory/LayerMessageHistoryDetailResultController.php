<?php
/*
 * Copyright (C) 2026 NHN COMMERCE. - All Rights Reserved
 *
 * Unauthorized copying or redistribution of this file in source and binary forms via any medium
 * is strictly prohibited.
 */

namespace Bundle\Controller\Admin\Crm\MobileMessageHistory;

use Framework\Http\Request;
use Framework\Http\Response;
use Origin\DTO\ApiClient\Commerce\Notification\MessageHistoryDetailRequestDTO;
use Origin\DTO\ApiClient\Commerce\Notification\MessageHistoryDetailResponseDTO;
use Origin\Enum\ApiClient\Crm\WorkingType;
use Origin\Enum\ApiClient\Notification\MessageAccessLocationType;
use Origin\Service\Crm\Message\MessageHistoryService;

class LayerMessageHistoryDetailResultController extends \Controller\Admin\Controller
{
    public function index()
    {
        try {
            $this->callMenu('crm', 'messageHistory', 'mobileHistoryList');
            $this->setMenuCode('crm', 'messageHistory', 'mobileHistoryList');

            /** @var Request $request */
            $request = \App::getInstance('request');
            $postData = $request->post()->toArray();

            $requestDTO = (new MessageHistoryDetailRequestDTO($postData))
            ->setAccessLocation(MessageAccessLocationType::HISTORY)
            ->setWorkingType(WorkingType::tryFromName($postData['workingType'] ?? null) ?? WorkingType::VIEW);
            try {
                /** @var MessageHistoryService $messageHistoryService */
                $messageHistoryService = \App::getInstance(MessageHistoryService::class);
                $messageHistoryDetailResponseDTO = $messageHistoryService->getMessageHistoryDetail($requestDTO);
            } catch (\Throwable $e) {
                \Logger::channel('mobileMessage')->error($e->getMessage(), ['requestDTO' => $requestDTO]);
                $messageHistoryDetailResponseDTO = new MessageHistoryDetailResponseDTO(['totalCount' => 0, 'contents' => []], $requestDTO->getPage(), $requestDTO->getPageSize());
            }
            $page = $messageHistoryDetailResponseDTO->getPage();

            $this->setData('postData', $postData);
            $this->setData('messageHistoryDetailList', $messageHistoryDetailResponseDTO->getMessageHistoryDetailList());
            $this->setData('page', $page);
            $this->getView()->setDefine('layout', 'layout_layer.php');
        } catch (\Throwable $e) {
            $this->json(['success' => false, 'message' => $e->getMessage()], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
