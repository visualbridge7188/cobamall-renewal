<?php
/*
 * Copyright (C) 2026 NHN COMMERCE. - All Rights Reserved
 *
 * Unauthorized copying or redistribution of this file in source and binary forms via any medium is strictly prohibited.
 */

namespace Bundle\Controller\Admin\Crm;

use Bundle\Component\Member\Manager;
use Bundle\Component\Sms\SmsUtil;
use Bundle\Component\Sms\SmsAdmin;
use Bundle\Component\Sms\Sms;
use Core\Base\Interceptor\AdminCertification;
use Framework\Debug\Exception\AlertRedirectException;

class PopupChargeMessagePointsController extends \Controller\Admin\Controller
{
    public function index()
    {
        $request = \App::getInstance('request');
        $session = \App::getInstance('session');
        $globals = \App::getInstance('globals');

        $this->callMenu('crm', 'messageConfig', 'chargeMessagePoints');

        if ($request->get()->get('popupMode', '') === 'yes') {
            $this->getView()->setDefine('layout', 'layout_blank.php');
        } elseif ($session->has(Manager::SESSION_TEMP_MANAGER)
            && $session->has(AdminCertification::SESSION_TEMP_CERTIFICATION)) {
            if (strpos($request->getReferer(), '/base/login') === false) {
                $session->del(Manager::SESSION_TEMP_MANAGER);
                $session->del(AdminCertification::SESSION_TEMP_CERTIFICATION);
                throw new AlertRedirectException('잘못된 경로로 접근되었습니다.', 403, null, '/base/login.php', 'top');
            }
            $this->getView()->setDefine('layout', 'layout_layer.php');
            $this->setData('guestCharge', true);
        }

        $shopNo = $globals->get('gLicense.godosno');

        // SMS 포인트 Sync
        Sms::saveSmsPoint();

        // 현재 메시지 포인트
        $nowSmsPoint = (float) Sms::getPoint();
        $availableCount = [
            'sms' => SmsUtil::getAvailableCount('sms', $nowSmsPoint),
            'lms' => SmsUtil::getAvailableCount('lms', $nowSmsPoint),
            'kakaoAlrimTalk' => SmsUtil::getAvailableCount('kakaoAlrimTalk', $nowSmsPoint),
            'kakaoFriendTalk' => SmsUtil::getAvailableCount('kakaoFriendTalk', $nowSmsPoint),
        ];

        $smsAdmin = \App::load(SmsAdmin::class);

        $webConfig = \App::getSystemConfig('internal')['commerce-web'];
        $messagePointPayUrl = $webConfig['url'] . '/' . $webConfig['path']['message-point-pay-popup'];

        $this->setData('lmsPoint', Sms::LMS_POINT);
        $this->setData('kakaoPoint', Sms::KAKAO_POINT);
        $this->setData('kakaoFriendTalkPoint', Sms::KAKAO_FRIEND_TALK_POINT);
        $this->setData('kakaoFriendTalkImagePoint', Sms::KAKAO_FRIEND_TALK_IMAGE_POINT ?? 2.2);
        $this->setData('kakaoFriendTalkWideItemPoint', Sms::KAKAO_FRIEND_TALK_WIDE_ITEM_POINT ?? 2.7);
        $this->setData('availableCount', $availableCount);
        $this->setData('smsPriceList', $smsAdmin->getSmsPriceList());
        $this->setData('shopSno', $shopNo);
        $this->setData('messagePointPayUrl', $messagePointPayUrl);

        $this->getView()->setDefine('layout', 'layout_blank.php');
    }
}
