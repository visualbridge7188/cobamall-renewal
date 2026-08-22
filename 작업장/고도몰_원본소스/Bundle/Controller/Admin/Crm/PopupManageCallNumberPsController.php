<?php
/*
 * Copyright (C) 2026 NHN COMMERCE. - All Rights Reserved
 *
 * Unauthorized copying or redistribution of this file in source and binary forms via any medium is strictly prohibited.
 */

namespace Bundle\Controller\Admin\Crm;

use Bundle\Component\Sms\SmsAdmin;
use Framework\Debug\Exception\AlertCloseException;
use Framework\Http\Request;
use Origin\Exception\Internal\ApiException;
use Origin\Service\Crm\Message\SmsCallerService;

class PopupManageCallNumberPsController extends \Controller\Admin\Controller
{
    public function index()
    {
        $this->callMenu('crm', 'messageConfig', 'manageCallNumber');
        $this->setMenuCode('crm', 'messageConfig', 'manageCallNumber');

        /** @var Request $request */
        $request = \App::getInstance('request');
        $mode = $request->request()->get('mode');

        switch ($mode) {
            case 'deleteCallNumber':
                $callerNo = $request->request()->get('callerNo');
                $callNumberNo = $request->request()->get('callNumberNo');
                $callNumber = $request->request()->get('callNumber');

                /** @var \Bundle\Component\Sms\SmsAdmin $smsAdmin */
                $smsAdmin = \App::load('Component\\Sms\\SmsAdmin');
                $smsAutoData = $smsAdmin->getSmsAutoData();
                $mallCallNumber = $smsAutoData['smsCallNum'] ?? '';
                $isCurrentCallNumber = $mallCallNumber === $callNumber;

                try {
                    /** @var SmsCallerService $smsCallerService */
                    $smsCallerService = \App::getInstance(SmsCallerService::class);
                    $result = $smsCallerService->deleteSmsCallNumber($callerNo, $callNumberNo);

                    if ($result && $isCurrentCallNumber) {
                        gd_set_policy('sms.config', ['smsCallNum' => '']);
                    }

                    $this->json(['success' => $result, 'message' => $result ? '발신번호가 삭제되었습니다.' : '발신번호 삭제에 실패하였습니다.']);
                } catch (ApiException $e) {
                    $data = ($e->getCode() == ApiException::CONFLICT) ? ['type' => 'conflict'] : null;
                    $this->json(['success' => false, 'message' => $e->getMessage(), 'data' => $data]);
                } catch (\Throwable $e) {
                    $this->json(['success' => false, 'message' => $e->getMessage()], ['mallCallNumber' => $mallCallNumber, 'callNumber' => $callNumber]);
                }
                break;
            case 'setCallNumber':
                try {
                    $callNumber = $request->request()->get('callNumber');

                    if (empty($callNumber)) {
                        throw new AlertCloseException('발신번호를 선택해 주세요.');
                    }

                    $smsAdmin = \App::load(SmsAdmin::class);
                    $result = $smsAdmin->saveCallNum($callNumber);
                    if ($result) {
                        $this->json(['success' => true, 'message' => '발신번호 설정에 성공하였습니다.']);
                    } else {
                        $this->json(['success' => false, 'message' => '발신번호 설정에 실패하였습니다.']);
                    }
                } catch (\Throwable $e) {
                    throw new AlertCloseException($e->getMessage());
                }
                break;
            default:
                throw new AlertCloseException('잘못된 접근입니다.');
        }
    }
}
