<?php
/*
 * Copyright (C) 2025 NHN COMMERCE. - All Rights Reserved
 *
 * Unauthorized copying or redistribution of this file in source and binary forms via any medium
 * is strictly prohibited.
 */

namespace Bundle\Controller\Admin\Crm\MessageConfig;

use Bundle\Component\Captcha\Captcha;
use Bundle\Component\Sms\Exception\PasswordException;
use Component\Sms\SmsSender;
use Framework\Http\Request;
use Framework\Security\Encryptor;
use Framework\Utility\ComponentUtils;

class LayerManageSmsVerificationPsController extends \Controller\Admin\Controller
{
    public function index()
    {
        /** @var Request $request */
        $request = \App::getInstance('request');

        $isChangePassword = $request->post()->get('mode', 'changePassword') === 'changePassword';
        if ($isChangePassword) {
            $captchaNumber = $request->post()->get('captcha');
            $captcha = new Captcha();
            $result = $captcha->verify($captchaNumber, 1);
            if ($result['code'] !== '0000') {
                $this->json(['success' => false, 'message' => '자동등록 방지문자가 맞지 않습니다.', 'data' => ['type' => 'invalid_captcha']]);
            }
        }

        $smsPassword = $request->post()->get('password');

        /** @var \Bundle\Component\Sms\SmsSender $smsSender */
        $smsSender = \App::load(SmsSender::class);
        try {
            $smsSender->validPassword($smsPassword, !$isChangePassword);

            /** @var Encryptor $encryptor */
            $encryptor = \App::getInstance('encryptor');
            $policy = ComponentUtils::getPolicy('sms.config');
            if ($isChangePassword) {
                $policy['authentication'] = [
                    'failLog'  => [],
                    'failCnt'  => 0,
                    'password' => $encryptor->encrypt($smsPassword),
                ];
            } else {
                $policy['authentication']['failLog'] = [];
                $policy['authentication']['failCnt'] = 0;
            }

            ComponentUtils::setPolicy('sms.config', $policy);

            $this->json(['success' => true, 'message' => '메시지 인증번호 등록이 완료되었습니다.']);
        } catch (PasswordException $e) {
            $message = $e->getMessage();
            if ($isChangePassword) {
                $message = '메시지 인증번호는 영문대문자/영문소문자/숫자/특수문자 중 2가지 이상 조합, 10~16자리로만 설정할 수 있습니다.';
                $message .= '<br/>[마이페이지 > 쇼핑몰 관리]에서 메시지 인증번호를 수정 후 다시 시도해주세요.';
            }

            $this->json(['success' => false, 'message' => $message, 'data' => ['type' => 'invalid_format']]);
        }
    }
}
