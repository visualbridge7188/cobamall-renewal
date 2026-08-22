<?php
/*
 * Copyright (C) 2025 NHN COMMERCE. - All Rights Reserved
 *
 * Unauthorized copying or redistribution of this file in source and binary forms via any medium
 * is strictly prohibited.
 */

namespace Bundle\Controller\Admin\Crm;

use Framework\Debug\Exception\AlertCloseException;
use Framework\Http\Request;
use GuzzleHttp\Exception\RequestException;
use Origin\Enum\ApiClient\Crm\RecipeType;
use Origin\Service\Crm\Message\MessageSendService;
use Origin\Service\Crm\Message\SmsPasswordService;
use Component\Crm\MobileMessage;
use Framework\Http\Response;

class MobileSendPsController extends \Controller\Admin\Controller
{
    public function index()
    {
        $this->callMenu('crm', 'messageSend', 'mobileSend');
        $this->setMenuCode('crm', 'messageSend', 'mobileSend');

        /** @var Request $request */
        $request = \App::getInstance('request');
        $postData = $request->post()->toArray();
        $mode = $postData['mode'];

        switch ($mode) {
            case 'send':
                try {
                    $requestData = json_decode($postData['payload'], true);

                    // CRM 레시피 발송은 레시피 메뉴 권한으로 인가
                    if (!empty($requestData['recipeType'])) {
                        if (RecipeType::tryFromSlug($requestData['recipeType']) === null) {
                            throw new AlertCloseException(__('잘못된 접근입니다.'));
                        }
                        $this->setMenuCode('crm', 'crm', 'crmRecipe');
                    }

                    // 수신대상 - 엑셀업로드, 업로드된 정보로 수신자 설정
                    if (($requestData['targetInfo']['targetType'] ?? null) === 'EXCEL') {
                        /* @var \Bundle\Component\Crm\MobileMessage $mobileMessage*/
                        $mobileMessage = \App::getInstance(MobileMessage::class);

                        $requestData = $mobileMessage->setExcelTargets($requestData);
                    }

                    /* @var MessageSendService $messageSendService*/
                    $messageSendService = \App::getInstance(MessageSendService::class);
                    $messageSendService->send($requestData);
                    $this->json(['success' => true]);
                } catch (RequestException $e) {
                    if ($e->hasResponse()) {
                        $response = $e->getResponse();
                        if ($response->getStatusCode() === Response::HTTP_BAD_REQUEST) {
                            $contents = json_decode($response->getBody()->getContents(), true);
                            $this->json(['success' => false, 'message' => $contents['message']], Response::HTTP_BAD_REQUEST);
                        }
                    }
                    throw $e;
                } catch (\Throwable $e) {
                    $this->json(['success' => false, 'message' => $e->getMessage()], Response::HTTP_INTERNAL_SERVER_ERROR);
                }
                break;
            case 'validateSendPassword':
                try {
                    /** @var SmsPasswordService $smsPasswordService */
                    $smsPasswordService = \App::getInstance(SmsPasswordService::class);
                    $smsPassword = $smsPasswordService->getSmsPassword(true);

                    if ($postData['sendPassword'] === $smsPassword) {
                        $this->json(['success' => true]);
                    } else {
                        $this->json(['success' => false, 'message' => '설정 메뉴에 저장된 메시지 인증번호와 일치하지 않습니다.', 'data' => ['subMessage' => '설정 페이지에서 메시지 인증번호를 먼저 확인해주세요.']]);
                    }
                } catch (\Throwable $e) {
                    $this->json(['success' => false, 'message' => $e->getMessage()], Response::HTTP_INTERNAL_SERVER_ERROR);
                }
                break;
            default:
                throw new AlertCloseException(__("잘못된 접근입니다."));
        }
    }
}
