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
use Component\Member\KakaoAlrimLuna;
use Framework\Enum\ExternalUrl;
use Framework\Utility\ComponentUtils;
use Framework\Utility\GodoUtils;
use Origin\Enum\ManagerTutorialType;
use Origin\Service\Admin\Member\ManagerTutorial\HelperPopupExposureAvailabilityService;

/**
 * 카카오 알림톡 설정
 *
 */
class KakaoAlrimLunaSettingController extends \Controller\Admin\Controller
{
    /**
     * index
     *
     */
    public function index()
    {
        if ($this->isDirectAccess()) {
            // URL 직접 입력 시 신규 페이지 리다이렉트
            $this->redirect('/crm/auto_send.php');
        }

        header('Access-Control-Allow-Origin:*');

        $request = \App::getInstance('request');
        // --- 메뉴 설정
        $this->callMenu('member', 'kakaoAlrim', 'kakaoAlrimSetting');

        // 휴면 기능 설정값
        $memberSleepSetting = gd_policy('member.sleep');

        $kakaoSetting = gd_policy('kakaoAlrimLuna.config');

        if (!$kakaoSetting) {
            $kakaoSetting['lunaCliendId'] = '';
            $kakaoSetting['lunaClientKey'] = '';
            $kakaoSetting['useFlag'] = 'n';
        }


        // 카카오알림 component
        $oKakao = new KakaoAlrimLuna;
        $smsAutoData = $oKakao->getSmsAutoData();
        $template = $oKakao->getKakaoTemplateList();
        $checked['useFlag'][$kakaoSetting['useFlag']] = 'checked="checked"';

        // 카카오 알림톡에 맞게 기본값들 재세팅
        $smsAutoList = Sms::SMS_AUTO_RECEIVE_LIST;
        unset($smsAutoList['promotion']);

        $bizKakaoSetting = gd_policy('kakaoAlrim.config');
        $this->setData('bizKakaoUseFlag', gd_isset($bizKakaoSetting['useFlag'],'n'));
        $memberJoin = ComponentUtils::getPolicy('member.join');
        $this->setData('kakaoSetting', $kakaoSetting);
        $this->setData('smsAutoList', $smsAutoList);
        $this->setData('smsAutoOrderPeriod', Sms::SMS_AUTO_ORDER_PERIOD);
        $this->setData('smsAutoOrderAfterPeriod', Sms::SMS_ORDER_AFTER_PERIOD);
        $this->setData('smsAutoReSendTime', Sms::SMS_RE_SEND_TIME);
        $this->setData('smsAutoCouponLimitPeriod', Sms::SMS_COUPON_LIMIT_PERIOD);
        $this->setData('smsAutoReservationTime', $oKakao->getKakaoAlrimReservationTime());
        $this->setData('smsAutoData', gd_htmlspecialchars($smsAutoData));
        $this->setData('template', gd_htmlspecialchars($template));
        $this->setData('checked', gd_isset($checked));
        $this->setData('useApprovalFlag', ($memberJoin['appUseFl'] != 'n' || $memberJoin['under14Fl'] == 'y'));
        $this->setData('sleepUseFl', $memberSleepSetting['useFl'] === 'y');
        if ($request->get()->get('popupMode', '') === 'yes') {
            $this->getView()->setDefine('layout', 'layout_blank.php');
        }

        // 20181227 이후 설치솔루션은 카드 부분 취소 미노출(설치일기준)
        $globals = \App::getInstance('globals');
        $gLicense = $globals->get('gLicense');
        $this->setData('mallSettingDate', $gLicense['sdate']);

        // 배포 날짜
        $date = GodoUtils::getFunctionInstallDateFromYml('kakao-alirm-cloud');
        // 상점 설치 날짜보다 배포일이 크면 true 반환
        // 상점 설치 날짜가 더 큰걸 찾아야 되니까 역반환
        $isNewMall = !GodoUtils::getInstalledDate($date);
        $this->setData('isNewMall', $isNewMall);
        $this->setData('blumnAiUrl', ExternalUrl::BIZMSG_BLUMN_AI->getUrl());

        /**
         * 블룸에이아이 콜백이 리얼 도메인으로만 들어와 parent 팝업이 닫히지 않는 이슈가 있음
         * 알파 도메인은 외부 통신이 불가해 사용 불가 → 중계 서버는 리얼로만 콜백 수신
         * 리얼 환경에서 콜백 도메인을 기준으로 예외 처리하는 방식으로 대응하는 것으로 웹개와 협의
         */
        $this->setData('lunaKakaoRequestUrl', 'https://kakaoapi.godo.co.kr/lunaKakaoRequest.php');

        // 튜토리얼 연계 모달 경로 조회
        $helperPopupExposureAvailabilityService = \App::getInstance(HelperPopupExposureAvailabilityService::class);
        $tutorialLinkModalUrl = $helperPopupExposureAvailabilityService->getTutorialUrl(ManagerTutorialType::SUBSCRIPTION_KAKAO_ALIM_TALK->value);
        $this->setData('tutorialLinkModalUrl', $tutorialLinkModalUrl);
        $this->setData('tutorialCode', ManagerTutorialType::SUBSCRIPTION_KAKAO_ALIM_TALK->value);

        // 튜토리얼 스텝가이드 즉시 실행 여부
        $tutorialMode = \Request::get()->get('tutorialMode', 'false');
        $this->setData('tutorialMode', $tutorialMode);

        // 튜토리얼 링크 모달 사이즈 및 스텝가이드 단계 설정
        $tutorialConfig = \App::getConfig('tutorial.regularDelivery')->toArray()[ManagerTutorialType::SUBSCRIPTION_KAKAO_ALIM_TALK->value . '_LUNA'];
        $this->setData('tutorialLinkModalSize', $tutorialConfig['linkModalSize']);
        $this->setData('tutorialStepGuideSteps', $tutorialConfig['stepGuideSteps']);

        // 튜토리얼 진행 현황 조회
        $tutorialStepGuideLinks = $helperPopupExposureAvailabilityService->getTutorialProgress(ManagerTutorialType::SUBSCRIPTION_KAKAO_ALIM_TALK->value);
        $this->setData('tutorialStepGuideLinks', $tutorialStepGuideLinks);
    }
}
