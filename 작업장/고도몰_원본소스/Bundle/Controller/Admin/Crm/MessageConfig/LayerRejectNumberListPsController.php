<?php
/*
 * Copyright (C) 2025 NHN COMMERCE. - All Rights Reserved
 *
 * Unauthorized copying or redistribution of this file in source and binary forms via any medium
 * is strictly prohibited.
 */

namespace Bundle\Controller\Admin\Crm\MessageConfig;

use Bundle\Component\Sms\Sms080;
use Framework\Debug\Exception\AlertCloseException;
use Framework\Http\Request;
use Framework\Utility\ComponentUtils;
use Framework\Utility\StringUtils;

class LayerRejectNumberListPsController extends \Controller\Admin\Controller
{
    public function index()
    {
        /** @var Request $request */
        $request = \App::getInstance('request');
        $requestData = $request->request();
        $mode = $requestData->get('mode');

        switch ($mode) {
            case 'checkActivation':
                $policy = ComponentUtils::getPolicy('sms.sms080');
                StringUtils::strIsSet($policy['status'], '');
                $isOpened = $policy['status'] == 'O';

                $this->json(['success' => $isOpened]);
                break;
            case 'manualSync':
                try {
                    /** @var Sms080 $sms080 */
                    $sms080 = \App::load('Component\\Sms\\Sms080');
                    $sms080->syncListByManual();
                    $this->json(['success' => true, 'message' => "동기화가 완료되었습니다."]);
                } catch (\Throwable $e) {
                    \Logger::channel('sms')->warning($e->getMessage(), [__METHOD__, $e->getTraceAsString()]);
                    $this->json(['success' => false, 'message' => "회원 정보와 동기화 중 오류가 발생했습니다."]);
                }
                break;
            default:
                throw new AlertCloseException(__("잘못된 접근입니다."));
        }
    }
}
