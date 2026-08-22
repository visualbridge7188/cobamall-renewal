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
use Origin\Enum\ApiClient\Excel\ExcelDownloadRange;
use Origin\Enum\ApiClient\Excel\ExcelGenerateMenu;
use Origin\Service\Admin\Excel\CommerceExcelService;
use Respect\Validation\Exceptions\ValidationException;
use Respect\Validation\Validator;

class LayerMessageHistoryExcelRequestPsController extends \Controller\Admin\Controller
{
    public function index()
    {
        $this->callMenu('crm', 'messageHistory', 'mobileHistoryList');
        $this->setMenuCode('crm', 'messageHistory', 'mobileHistoryList');

        /** @var Request $request */
        $request = \App::getInstance('request');
        $postData = $request->post()->toArray();
        $mode = $postData['mode'];

        switch ($mode) {
            case 'generate':
                try {
                    Validator::arrayVal()
                        ->key('menu', Validator::stringType()->notEmpty()->in(array_map(fn($case) => $case->name, ExcelGenerateMenu::cases())))
                        ->key('downloadRange', Validator::stringType()->notEmpty()->in(array_map(fn($case) => $case->name, ExcelDownloadRange::cases())))
                        ->key('excelPassword', Validator::stringType()->notEmpty())
                        ->assert($postData);

                    /** @var CommerceExcelService $commerceExcelService */
                    $commerceExcelService = \App::getInstance(CommerceExcelService::class);
                    $requestDTO = $commerceExcelService->prepareCommerceExcelGenerateRequestDTO($postData);
                    $commerceExcelService->requestExcelGenerate($requestDTO);

                    $this->json(['success' => true, 'message' => '다운로드할 엑셀 파일 생성이 완료되었습니다.<br />파일을 선택하여 다운로드하시기 바랍니다.'], Response::HTTP_OK);
                } catch (ValidationException $e) {
                    \Logger::channel('excel')->error("엑셀 생성 요청 파라미터가 잘못 되었습니다.", [$e->getMessage(), __METHOD__]);
                    $this->json(['success' => false, 'message' => $e->getMessage()], Response::HTTP_BAD_REQUEST);
                } catch (\Throwable $e) {
                    \Logger::channel('excel')->error("엑셀 생성 요청에 실패 하였습니다.", [$e->getMessage(), __METHOD__]);
                    $this->json(['success' => false, 'message' => $e->getMessage()], Response::HTTP_INTERNAL_SERVER_ERROR);
                }
                break;
            case 'download':
                try {
                    Validator::arrayVal()
                        ->key('reason', Validator::stringType()->notEmpty())
                        ->key('excelNo', Validator::stringType()->notEmpty())
                        ->assert($postData);

                    $naviMenu = $this->getData('naviMenu');

                    /** @var CommerceExcelService $commerceExcelService */
                    $commerceExcelService = \App::getInstance(CommerceExcelService::class);
                    $requestDTO = $commerceExcelService->prepareCommerceExcelDownloadRequestDTO($postData['reason'], $naviMenu->location[0], $naviMenu->location[2]);
                    $commerceExcelService->downloadExcel($requestDTO, $postData['excelNo']);
                } catch (ValidationException $e) {
                    \Logger::channel('excel')->error("엑셀 다운로드 파라미터가 잘못 되었습니다.", [$e->getMessage(), __METHOD__]);
                    $this->json(['success' => false, 'message' => $e->getMessage()], Response::HTTP_BAD_REQUEST);
                } catch (\Throwable $e) {
                    \Logger::channel('excel')->error("엑셀 다운로드에 실패 하였습니다.", [$e->getMessage(), __METHOD__]);
                    $this->json(['success' => false, 'message' => $e->getMessage()], Response::HTTP_INTERNAL_SERVER_ERROR);
                }
                break;
            default:
                throw new \InvalidArgumentException('잘못된 접근입니다.');
        }
    }
}
