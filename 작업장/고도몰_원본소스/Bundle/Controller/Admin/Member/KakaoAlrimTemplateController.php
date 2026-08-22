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

use Component\Member\KakaoAlrim;
use Origin\Traits\Member\KakaoAlrim\KakaoAlrimCloudAvailable;

/**
 * 카카오 알림톡 템플릿 설정
 *
 */
class KakaoAlrimTemplateController extends \Controller\Admin\Controller
{
    use KakaoAlrimCloudAvailable;

    public function index()
    {
        if ($this->isDirectAccess()) {
            // URL 직접 입력 시 신규 페이지 리다이렉트
            $this->redirect('/crm/message_template.php');
        }

        $this->callMenu('member', 'kakaoAlrim', 'kakaoAlrimTemplate');

        // 신규 몰인지
        $isNewMall = $this->isNewMall();

        if($isNewMall) {
            $returnUrl = URI_ADMIN . 'member/kakao_alrim_cloud_template.php';
            $this->redirect($returnUrl);
        }

        // 카카오알림 component
        $oKakao = new KakaoAlrim;
        $request = \App::getInstance('request');
        $arrParam = gd_array_merge($request->get()->toArray(), $request->post()->toArray());
        $mode = $arrParam['mode'];

        $oKakao->updateAllTemplateDb();
        $oKakao->updateGroupTemplateDb();
        $data = $oKakao->getTemplateList();

        // 카카오 알림 셋팅 정보
        $kakaoSetting = gd_policy('kakaoAlrim.config');

        $this->setData('isNewMall', $isNewMall);
        $this->setData('kakaoSetting', gd_isset($kakaoSetting));
        $this->setData('templateData', gd_isset($data['data']));
        $this->setData('selected', gd_isset($data['selected']));
        $this->setData('checked', gd_isset($data['checked']));
        $this->setData('search', gd_isset($data['search']));
        $this->setData('page', gd_isset($data['page']));
        $this->setData('mode', gd_isset($mode));
        $this->setData('kakaoButtonType', $oKakao->getTemplateButtonType());
        $this->addScript(['member.js']);
    }
}
