<?php

/**
 * This is commercial software, only users who have purchased a valid license
 * and accept to the terms of the License Agreement can install and use this
 * program.
 *
 * Do not edit or add to this file if you wish to upgrade Godomall5 to newer
 * versions in the future.
 *
 * @copyright ⓒ 2016, NHN godo: Corp.
 * @link      http://www.godo.co.kr
 */

namespace Bundle\Controller\Admin\Member;

use Component\Sms\Sms;
use Component\Sms\SmsLog;
use Component\Member\KakaoAlrim;
use Framework\Utility\GodoUtils;
use Origin\Traits\Member\KakaoAlrim\KakaoAlrimCloudAvailable;

/**
 * 카카오 알림톡 발송 내역 보기
 *
 */
class KakaoAlrimLogController extends \Controller\Admin\Controller
{
    use KakaoAlrimCloudAvailable;

    public function index()
    {
        if ($this->isDirectAccess()) {
            // URL 직접 입력 시 신규 페이지 리다이렉트
            $this->redirect('/crm/mobile_history_list.php?sendMethod=ALIMTALK');
        }

        // 신규 몰인지
        $isNewMall = $this->isNewMall();

        if($isNewMall) {
            $returnUrl = URI_ADMIN . 'member/kakao_alrim_cloud_log.php';
            $this->redirect($returnUrl);
        }

        $this->callMenu('member', 'kakaoAlrim', 'kakaoAlrimLog');

        $smsLog = new SmsLog();

        $request = \App::getInstance('request');
        $arrParam = $request->get()->toArray();

        //
        $oKakao = new KakaoAlrim;
        $oKakao->updateKakaoSendLogDb();

        if (empty($arrParam['sendFl']) === true || $arrParam['sendFl'] == '') {
            $arrParam['sendFl'] = 'kakao';
        }
        $logData = $smsLog->getSmsLogList($arrParam);

        //예약문자 결과수신 예외처리
        if (empty($logData) === false) {
            $nowDate = date('Y-m-d H:i:s');

            for ($l = 0; $l < gd_count($logData['data']); $l++) {
                if ($logData['data'][$l]['sendStatus'] == 'r' && $logData['data'][$l]['reserveDt'] > $nowDate) {
                    $logData['data'][$l]['sendStatus'] = 's';
                }
            }
        }

        $this->setData('memGroupNm', gd_htmlspecialchars(gd_member_groups()));
        $this->setData('logData', gd_isset($logData['data']));
        $this->setData('selected', gd_isset($logData['selected']));
        $this->setData('checked', gd_isset($logData['checked']));
        $this->setData('search', gd_isset($logData['search']));
        $this->setData('page', gd_isset($logData['page']));
        $this->setData('listCnt', gd_isset($listCnt));
        $this->setData('kakaoPoint', Sms::KAKAO_POINT);
        $this->setData('smsSendType', Sms::SMS_SEND_TYPE);
        $this->setData('smsSendStatus', Sms::SMS_SEND_STATUS);
        $this->addScript(['member.js']);
    }
}
