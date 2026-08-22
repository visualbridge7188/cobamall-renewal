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
 * @link http://www.godo.co.kr
 */

namespace Bundle\Component\Agreement;

use Framework\Database\DB;
use Framework\Utility\HttpUtils;
use Component\Validator\Validator;
use App;
use Globals;
use Session;
use Component\Database\DBTableField;

/**
 * 법정 필수 사항 점검 Class
 *
 * @author minji lee <mj2@godo.co.kr>
 */
class LegalRequirements
{
    public static $LEGAL_REQUIREMENTS_LIST = [];
    public $db;

    /**
     * 생성자
     *
     */
    public function __construct()
    {
        $this->db = App::load('DB');
        self::initializeLegalRequirementsList();
    }

    /**
     * 법정 필수 사항 설정 목록 초기화
     *
     * 이 메서드는 법정 필수 사항에 대한 설정 리스트를 초기화합니다.
     * 각 항목에는 작업 제목(title), 설명(desc), 연결될 URL(url)이 포함됩니다.
     *
     * - 'start' 단계는 시작 관련 작업을 포함합니다.
     * - 'selling' 단계는 판매 준비 관련 작업을 포함합니다.
     * - 'promotion' 단계는 홍보 준비 관련 작업을 포함합니다.
     * - 'security' 단계는 보안 강화 관련 작업을 포함합니다.
     *
     * @return void
     */
    public static function initializeLegalRequirementsList(): void
    {
        self::$LEGAL_REQUIREMENTS_LIST = [
            'start' => [
                'base_check' => ['title' => '내 쇼핑몰 확인하기', 'desc' => '기본 설정된 내 쇼핑몰을 확인해 보세요. 디자인 수정이나 더 많은 스킨 설치는 디자인 메뉴에서 가능해요', 'url' => URI_HOME],
                'base_info' => ['title' => '쇼핑몰 정보 입력하기', 'desc' => '상품 판매를 위한 쇼핑몰 기본 정보 입력이 잘 되었는지 확인 후 수정해 보세요', 'url' => '/policy/base_info.php'],
                'private' => ['title' => '약관 입력하기', 'desc' => '쇼핑몰 운영과 이용에 필요한 사항을 입력하고 이용자 개인정보 보호지침 안내를 설정하세요', 'url' => '/policy/base_agreement_with_private.php?mallSno=1&mode=private'],
            ],
            'selling' => [
                'pg_info' => ['title' => '결제 서비스(PG) 연결하기', 'desc' => '온라인 쇼핑몰에서 고객이 상품을 결제할 수 있도록 카드, 간편결제 등 결제수단을 연결하세요', 'url' => '/service/service_info.php?menu=pg_info'],
                'goods_category' => ['title' => '상품 카테고리 만들기', 'desc' => '쇼핑몰에서 보여질 상품 카테고리를 만들어 보세요', 'url' => '/goods/category_tree.php'],
                'goods_register' => ['title' => '상품 등록하기', 'desc' => '판매의 시작! 쇼핑몰에서 판매할 상품을 등록하세요 상품 이름만 있어도 등록이 가능해요', 'url' => '/goods/goods_list.php'],
                'goods_must_info_list' => ['title' => '상품정보제공 고시 설정하기', 'desc' => '상품 카테고리별 상품정보제공 고시를 설정하세요', 'url' => '/goods/goods_must_info_list.php'],
            ],
            'promotion' => [
                'sms_auto' => ['title' => 'sms 발신번호 등록', 'desc' => '회원에게 홍보 SMS를 발송하기 위한 발신번호를 등록하세요', 'url' => '/member/sms_auto.php'],
                'sms080_config' => ['title' => '080 수신거부 설정', 'desc' => '광고성 정보 전송 시, 수신자가 수신거부의사를 표시할 수 있도록 설정하세요.', 'url' => '/member/sms080_config.php'],
            ],
            'security' => [
                'manage_security' => ['title' => '운영 보안 설정하기', 'desc' => '회원 개인정보의 안전을 위해 관리자 보안을 설정하세요', 'url' => '/policy/manage_security.php'],
                'ssl_admin_setting' => ['title' => '보안 서버 설치하기', 'desc' => '고객이 찾을 수 있는 안전한 쇼핑몰이 되도록 보안 서버를 설치하세요', 'url' => '/policy/ssl_admin_setting.php'],
                'sms_auto' => ['title' => '광고 수신거부 처리 결과 통보', 'desc' => '수신거부 의사 표시 받은 날로 부터 14일 이내 처리 결과 통보를 설정해 주세요', 'url' => '/member/sms_auto.php'],
            ]
        ];
    }

    /**
     * 필수 설정 체크 값 가져오기
     *
     * @return array
     */
    public function getLegalRequirements()
    {
        $list = self::$LEGAL_REQUIREMENTS_LIST;
        $data['list'] = $list;
        //$policy = gd_policy('basic.legalRequirements'); // db에 저장된 체크 값
        $data['data'] = gd_policy('basic.legalRequirements'); // db에 저장된 체크 값
        /*
        foreach($list as $key => $val) {
            foreach($val as $name => $tmp) {
                if($policy[$key][$name] == 'true') {
                    unset($data['list'][$key][$name]);
                    $data['list'][$key][$name] = $tmp;
                }
            }
        }
        */
        $data['config'] = $this->getLegalRequirementsConfig(); // 레이어 show hide 여부
        return $data;
    }

    /**
     * 필수 설정 체크 값 저장
     *
     * @param $arrData
     * @return bool
     */
    public function saveLegalRequirements($arrData)
    {
        $data = gd_policy('basic.legalRequirements');
        $data[$arrData['name']][$arrData['key']] = $arrData['val'];
        gd_set_policy('basic.legalRequirements', $data);

        $logData['managerId'] = \Session::get('manager.managerId');
        $logData['menu'] = self::$LEGAL_REQUIREMENTS_LIST[$arrData['name']][$arrData['key']]['title'];
        $logData['checked'] = ($arrData['val'] == 'true') ? 'y' : 'n';
        $arrBind = $this->db->get_binding(DBTableField::tableLogLegalRequirements(), $logData, 'insert', gd_array_keys($logData));
        $this->db->set_insert_db(DB_LOG_LEGAL_REQUIREMENTS, $arrBind['param'], $arrBind['bind'], 'y');
        return true;
    }

    /**
     * 레이아웃 show hide 쿠키 값 가져오기
     *
     * @return array
     */
    public function getLegalRequirementsConfig()
    {
        $cookie = \App::getInstance('cookie');

        // 20241008 포함한 이후에 신설된 상점에 한해서 초기 접속시 디폴트 열림 상태 노출
        if (Globals::get('gLicense.sdate') >= '20241008' && !gd_policy('basic.legalRequirements') && $cookie->get('legalRequirements_displayFl') !== 'false') {
            $cookie->set('legalRequirements_displayFl', 'true', 3600 * 24 * 7);
        }
        $data['displayFl'] = gd_isset($cookie->get('legalRequirements_displayFl'), 'false');
        $data['checkedFl'] = gd_isset($cookie->get('legalRequirements_checkedFl'), 'false');
        return $data;
    }
}
