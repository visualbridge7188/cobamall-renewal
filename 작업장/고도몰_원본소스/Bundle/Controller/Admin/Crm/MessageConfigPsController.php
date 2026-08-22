<?php
/*
 * Copyright (C) 2025 NHN COMMERCE. - All Rights Reserved
 *
 * Unauthorized copying or redistribution of this file in source and binary forms via any medium
 * is strictly prohibited.
 */

namespace Bundle\Controller\Admin\Crm;

use Framework\Http\Request;
use Origin\DTO\Crm\Message\InsufficMessagePointAlertConfigDTO;
use Origin\DTO\Crm\Message\KakaoAlimTalkConfigDTO;
use Origin\DTO\Crm\Message\KakaoFriendTalkConfigDTO;
use Origin\DTO\Crm\Message\MessagePointConfigDTO;
use Origin\DTO\Crm\Message\MyappPushConfigDTO;
use Origin\DTO\Crm\Message\SmsAutoConfigDTO;
use Origin\DTO\Crm\Message\SmsRejectConfigDTO;
use Origin\Service\Crm\Message\MessageConfigService;
use Origin\Service\Crm\Message\SmsCallerService;

class MessageConfigPsController extends \Controller\Admin\Controller
{
    public function index()
    {
        $this->callMenu('crm', 'messageConfig', 'messageConfig');
        $this->setMenuCode('crm', 'messageConfig', 'messageConfig');

        /** @var Request $request */
        $request = \App::getInstance('request');
        $mode = $request->request()->get('mode');

        switch ($mode) {
            case 'save':
                $postValue = $request->post()->toArray();

                try {
                    $messagePointConfig = new MessagePointConfigDTO($postValue['messagePointConfig']);
                    $smsAutoConfig = new SmsAutoConfigDTO($postValue['smsAutoConfig']);
                    $smsRejectConfig = $postValue['smsRejectUse'] ? new SmsRejectConfigDTO(['use' => $postValue['smsRejectUse']]) : null;
                    $kakaoFriendTalkConfig = new KakaoFriendTalkConfigDTO($postValue['kakaoFriendTalkConfig']);
                    $kakaoAlimTalkConfig = new KakaoAlimTalkConfigDTO($postValue['kakaoAlimTalkConfig']);
                    $myappPushConfig = new MyappPushConfigDTO($postValue['myappPushConfig']);

                    /** @var MessageConfigService $messageConfigService */
                    $messageConfigService = \App::getInstance(MessageConfigService::class);
                    $messageConfigService->save(
                        $messagePointConfig,
                        $smsAutoConfig,
                        $smsRejectConfig,
                        $kakaoFriendTalkConfig,
                        $kakaoAlimTalkConfig,
                        $myappPushConfig
                    );

                    $this->json(['success' => true, 'message' => '메시지 설정이 저장되었습니다.']);
                } catch (\Throwable $e) {
                    \Logger::channel('mobileMessage')->warning($e->getMessage(), ['requestData' => $postValue]);
                    $this->json(['success' => false, 'message' => $e->getMessage()]);
                }
                break;
            case 'checkCaller':
                try {
                    /** @var SmsCallerService $smsCallerService */
                    $smsCallerService = \App::getInstance(SmsCallerService::class);

                    // 발신 관리책임자 목록 조회
                    $smsCallerList = $smsCallerService->getSmsCallerList()->getSmsCallerList();
                    $smsCallerCount = count($smsCallerList);

                    // 해당 검증은 발신 관리책임자가 연동되어 있지 않을 경우에만 발동되므로, 방어코드 추가
                    $isSelectedExists = !empty(array_values(array_filter($smsCallerList, fn($item) => $item->isSelected())));

                    if ($isSelectedExists) {
                        $this->json(['success' => true, 'message' => '발신 관리책임자가 단일로 등록되어 있습니다.', 'data' => ['type' => 'connected_caller', 'caller' => $smsCallerList[0]->toArray()]]);
                    }

                    switch ($smsCallerCount) {
                        case 0: // 발신 관리책임자 없음
                            $this->json(['success' => true, 'message' => '등록된 발신관리 책임자가 없습니다.', 'data' => ['type' => 'no_caller']]);
                            break;
                        case 1:
                            $this->json(['success' => true, 'message' => '발신 관리책임자가 단일로 등록되어 있습니다.', 'data' => ['type' => 'single_caller', 'caller' => $smsCallerList[0]->toArray()]]);
                            break;
                        default:
                            $this->json(['success' => true, 'message' => '발신 관리책임자가 복수로 등록되어 있습니다.', 'data' => ['type' => 'multiple_callers']]);
                    }
                } catch (\Exception $e) {
                    \Logger::channel('sms')->warning($e->getMessage(), [__METHOD__, $e->getTraceAsString()]);
                    $this->json(['success' => false, 'message' => $e->getMessage()]);
                }
                break;
            case 'connectCaller':
                try {
                    $callerNo = $request->post()->get('callerNo');

                    /** @var SmsCallerService $smsCallerService */
                    $smsCallerService = \App::getInstance(SmsCallerService::class);
                    $result = $smsCallerService->connectSmsCaller($callerNo);
                    if ($result) {
                        $this->json(['success' => true, 'message' => '발신 관리책임자가 정상적으로 연동되었습니다.']);
                    } else {
                        $this->json(['success' => false, 'message' => '발신 관리책임자 연동에 실패하였습니다.']);
                    }
                } catch (\Exception $e) {
                    \Logger::channel('sms')->warning($e->getMessage(), [__METHOD__, $e->getTraceAsString()]);
                    $this->json(['success' => false, 'message' => $e->getMessage()]);
                }
                break;
            default:
                throw new \InvalidArgumentException('잘못된 접근입니다.');
        }
    }
}
