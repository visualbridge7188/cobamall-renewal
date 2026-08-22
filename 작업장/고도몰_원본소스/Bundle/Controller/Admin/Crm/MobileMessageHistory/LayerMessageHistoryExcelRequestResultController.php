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
use Origin\DTO\Excel\CommerceExcelGenerateHistoryListResponseDTO;
use Origin\DTO\Excel\CommerceExcelGenerateHistoryRequestDTO;
use Origin\Enum\ApiClient\Excel\ExcelGenerateStatus;
use Origin\Service\Admin\Excel\CommerceExcelService;

class LayerMessageHistoryExcelRequestResultController extends \Controller\Admin\Controller
{
    public function index()
    {
        try {
            $this->callMenu('crm', 'messageHistory', 'mobileHistoryList');
            $this->setMenuCode('crm', 'messageHistory', 'mobileHistoryList');

            /** @var Request $request */
            $request = \App::getInstance('request');
            $postData = $request->post()->toArray();

            $requestData = [
                'excelMenu' => $postData['menu'],
                'page' => $postData['page'] ?? CommerceExcelGenerateHistoryRequestDTO::DEFAULT_PAGE,
                'pageSize' => CommerceExcelGenerateHistoryRequestDTO::DEFAULT_PAGE_SIZE
            ];
            $requestDTO = new CommerceExcelGenerateHistoryRequestDTO($requestData);

            try {
                /** @var CommerceExcelService $commerceExcelService */
                $commerceExcelService = \App::getInstance(CommerceExcelService::class);
                $excelGenerateHistoryListResponseDTO = $commerceExcelService->getExcelGenerateHistoryList($requestDTO);
            } catch (\Throwable $e) {
                \Logger::channel('excel')->error($e->getMessage(), ['requestDTO' => $requestDTO]);
                $excelGenerateHistoryListResponseDTO = new CommerceExcelGenerateHistoryListResponseDTO(
                    ['totalCount' => 0, 'contents' => []], $requestDTO->getPage(), $requestDTO->getPageSize()
                );
            }
            $page = $excelGenerateHistoryListResponseDTO->getPage();

            $this->setData('postData', $postData);
            $this->setData('excelGenerateHistoryList', $excelGenerateHistoryListResponseDTO->getExcelGenerateHistoryList());
            $this->setData('downloadableStatuses', ExcelGenerateStatus::getDownloadableStatuses());
            $this->setData('page', $page);
            $this->getView()->setDefine('layout', 'layout_layer.php');
        } catch (\Throwable $e) {
            $this->json(['success' => false, 'message' => $e->getMessage()], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
