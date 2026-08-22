<?php
/*
 * Copyright (C) 2026 NHN COMMERCE. - All Rights Reserved
 *
 * Unauthorized copying or redistribution of this file in source and binary forms via any medium
 * is strictly prohibited.
 */

namespace Bundle\Controller\Admin\Crm\MobileSend;

use Component\Crm\MobileMessage;
use Bundle\Component\Excel\Enum\ExcelSampleType;
use Bundle\Component\Excel\ExcelSample;
use Bundle\Component\Excel\ExcelSmsConvert;
use Bundle\Component\Sms\SmsExcelLog;
use Framework\Debug\Exception\AlertCloseException;
use Framework\Http\Request;
use Framework\Http\Response;
use Origin\DTO\ApiClient\Commerce\Crm\TargetsRepeatRulesRequestDTO;
use Origin\DTO\ApiClient\Commerce\Crm\TargetsRepeatRuleStatusRequestDTO;
use Origin\Service\ApiClient\Commerce\Crm\CrmAdminApiClientService;

class LayerRecipientSettingPsController extends \Controller\Admin\Controller
{
    public function index()
    {
        /** @var Request $request */
        $request = \App::getInstance('request');
        $mode = $request->request()->get('mode');

        switch ($mode) {
            case 'getCountAll':
            case 'getCountMembers':
                try {
                    $getData = $request->get()->toArray();
                    $memberNos = json_decode($getData['selectedMemberNos']);

                    /* @var MobileMessage $mobileMessage*/
                    $mobileMessage = \App::getInstance(MobileMessage::class);

                    $counts = $mobileMessage->getReceiverCounts($memberNos, null);
                    $this->json(['success' => true, 'data' => $counts], Response::HTTP_OK);
                } catch (\Throwable $e) {
                    \Logger::channel('mobileMessage')->error($e->getMessage());
                    $this->json(['success' => false], Response::HTTP_INTERNAL_SERVER_ERROR);
                }
                break;
            case 'getCountGroupMembers':
                try {
                    $getData = $request->get()->toArray();
                    $groupSnos = json_decode($getData['selectedMemberGroups']);

                    /* @var MobileMessage $mobileMessage*/
                    $mobileMessage = \App::getInstance(MobileMessage::class);

                    $counts = $mobileMessage->getReceiverCounts(null, $groupSnos);
                    $this->json(['success' => true, 'data' => $counts], Response::HTTP_OK);
                } catch (\Throwable $e) {
                    \Logger::channel('mobileMessage')->error($e->getMessage());
                    $this->json(['success' => false], Response::HTTP_INTERNAL_SERVER_ERROR);
                }
                break;
            case 'getCrmGroupStatus':
                try {
                    $getData = $request->get()->toArray();
                    $requestDTO = new TargetsRepeatRuleStatusRequestDTO($getData);

                    /** @var $crmAdminApiClient CrmAdminApiClientService */
                    $crmAdminApiClient = \App::getInstance(CrmAdminApiClientService::class);
                    $targetsRepeatRuleStatusResponseDTO = $crmAdminApiClient->getNotificationTargetsRepeatRuleStatus($requestDTO, $getData['crmGroupNo']);

                    $this->json([
                        'success' => true,
                        'message' => 'CRN 그룹 개수 조회 성공',
                        'data' => $targetsRepeatRuleStatusResponseDTO->toRecursiveArray()
                    ]);
                } catch (\Throwable $e) {
                    \Logger::channel('mobileMessage')->error($e->getMessage(), ['requestDTO' => $requestDTO->toArray()]);
                    $this->json(['success' => false], Response::HTTP_INTERNAL_SERVER_ERROR);
                }
                break;
            case 'downloadExcelSample':
                (new ExcelSample(ExcelSampleType::SMS))->outputSample();
                break;
            case 'uploadExcelTarget':
                $requestFiles = $request->files()->toArray();
                $excelFile = $requestFiles['excel'] ?? null;
                if (!$excelFile) {
                    $this->json(['success' => false, 'message' => '엑셀 파일이 없습니다.'], Response::HTTP_BAD_REQUEST);
                    break;
                }

                set_time_limit(RUN_TIME_LIMIT);
                /* @var ExcelSmsConvert $excelSmsConvert */
                $excelSmsConvert = \App::load('Component\\Excel\\ExcelSmsConvert');
                /* @var SmsExcelLog $smsExcelLog */
                $smsExcelLog = \App::load('Component\\Sms\\SmsExcelLog');
                try {
                    $excelSmsConvert->upload();
                    $uploadKey = $excelSmsConvert->getUploadKey();
                    $counts = $smsExcelLog->countValidationLogByUploadKey($uploadKey);
                    $this->json(['success' => true, 'data' => ['uploadedExcelKey' => $uploadKey, 'successTargetCount' => $counts]], Response::HTTP_OK);
                } catch (\Throwable $e) {
                    Logger::channel('mobileMessage')->error(__CLASS__ . ' 엑셀 업로드 처리 중 에러 발생 : ', ['error' => $e->getMessage()]);
                    $this->json(['success' => false, 'message' => '엑셀 파일 처리에 실패했습니다.'], Response::HTTP_INTERNAL_SERVER_ERROR);
                }
                break;
            default:
                throw new AlertCloseException(__("잘못된 접근입니다."));
        }
    }
}
