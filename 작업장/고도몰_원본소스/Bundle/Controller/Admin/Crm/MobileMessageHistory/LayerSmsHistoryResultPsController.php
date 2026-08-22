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
use Origin\DTO\ApiClient\Commerce\Notification\RemoveReservedSmsSendRequestDTO;
use Origin\Service\Crm\Message\SmsHistoryService;
use Respect\Validation\Exceptions\ValidationException;
use Respect\Validation\Validator;

class LayerSmsHistoryResultPsController extends \Controller\Admin\Controller
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
            case 'cancel':
                try {
                    Validator::arrayVal()
                        ->key('sendGroupKey', Validator::stringType()->notEmpty())
                        ->key('type', Validator::stringType()->notEmpty())
                        ->key('cancelMode', Validator::stringType()->notEmpty())
                        ->when(
                            Validator::key('cancelMode', Validator::equals('SELECT')),
                            Validator::key('ids', Validator::stringType()->notEmpty()),
                            Validator::alwaysValid()
                        )
                        ->assert($postData);

                    $requestDTO = new RemoveReservedSmsSendRequestDTO($postData);

                    /** @var SmsHistoryService $smsHistoryService */
                    $smsHistoryService = \App::getInstance(SmsHistoryService::class);
                    $smsHistoryService->removeReservedSmsSend($postData['sendGroupKey'], $requestDTO);

                    $this->json(['success' => true, 'message' => '예약 발송이 취소되었습니다.'], Response::HTTP_OK);
                } catch (ValidationException $e) {
                    \Logger::channel('mobileMessage')->error("예약 취소 요청 파라미터가 잘못 되었습니다.", [$e->getMessage(), __METHOD__]);
                    $this->json(['success' => false, 'message' => $e->getMessage(), 'data' => $postData], Response::HTTP_BAD_REQUEST);
                } catch (\Throwable $e) {
                    \Logger::channel('mobileMessage')->error("예약 취소 요청에 실패 하였습니다.", [$e->getMessage(), __METHOD__]);
                    $this->json(['success' => false, 'message' => $e->getMessage(), 'data' => $postData], Response::HTTP_INTERNAL_SERVER_ERROR);
                }
                break;
            default:
                $this->json(['success' => false, 'message' => '잘못된 접근입니다.'], Response::HTTP_BAD_REQUEST);
        }
    }
}
