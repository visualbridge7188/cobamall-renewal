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
use Origin\Traits\Member\KakaoAlrim\KakaoAlrimCloudAvailable;

/**
 * 카카오 알림톡 설정
 *
 */
class KakaoAlrimLunaTemplateController extends \Controller\Admin\Controller
{
    use KakaoAlrimCloudAvailable;
    /**
     * index
     *
     */
    public function index()
    {
        if ($this->isDirectAccess()) {
            // URL 직접 입력 시 신규 페이지 리다이렉트
            $this->redirect('/crm/message_template.php');
        }

        $request = \App::getInstance('request');
        // --- 메뉴 설정
        $this->callMenu('member', 'kakaoAlrim', 'kakaoAlrimTemplate');

        $kakaoSetting = gd_policy('kakaoAlrimLuna.config');

        if($kakaoSetting['useFlag'] == 'y' && !empty($kakaoSetting['lunaCliendId']) && !empty($kakaoSetting['lunaClientKey'])){
            $lunaUse = 'y';
        }else{
            $lunaUse = 'n';
        }

        // 신규 몰인지
        $isNewMall = $this->isNewMall();
        $this->setData('isNewMall', $isNewMall);
        $this->setData('lunaUse', $lunaUse);
        $this->setData('blumnAiTemplateUrl', ExternalUrl::BIZMSG_BLUMN_AI->getUrl('/template'));
    }
}
