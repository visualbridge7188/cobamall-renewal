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

namespace Bundle\Controller\Admin\Share;

use Component\Godo\GodoPanelApi;
use Component\Godo\GodoGongjiServerApi;
use Component\Admin\AdminPanel;
use Request;

/**
 * 관리자 페이지내 고도 Panel / API
 * @author Shin Donggyu <artherot@godo.co.kr>
 */
class AdminPanelApiController extends \Controller\Admin\Controller
{
    public const NOTICE_2DEPTH_CATEGORY_NO = ['36', '42']; // cos 게시판-게시판관리-소식-공지사항- 2차 카테고리 no 값(공통, 고도몰)
    public const PATCH_2DEPTH_CATEGORY_NO = '39'; // cos 게시판-게시판관리-소식-업데이트- 2차 카테고리 no 값 (고도몰)
    public const SELLERTIP_BOARD_CODE = 'COMMERCE_CLASS'; // cos 게시판-게시판관리-셀러팁 boardCode
    public const SELLERTIP_CATEGORY_NO = '80'; // cos 게시판-게시판관리-셀러팁- 고도몰 카테고리 no 값
    public const DEFAULT_BOARD_SIZE = 8; // cos 게시판 보드당 조회 개수

    /**
     * Cos Panel section Code
     *
     * @var string
     */
    private string $loginPanelSectionCode;
    private string $bannerPanelMainTopSectionCode;
    private string $bannerPanelMainMiddleSectionCode;
    private string $bannerPanelMainSideSectionCode;
    private array $popupPanelLayerSectionCode;
    private array $popupPanelModalSectionCode;

    private AdminPanel $adminPanel;

    public function index()
    {
        // cos 서버 모듈 호출
        $cosApi = new GodoPanelApi();

        // cos 섹션 코드 init
        $this->initializePanelSectionCodes();

        // 고도 공지서버 모듈 호출
        $godoApi = new GodoGongjiServerApi();

        // 페이지 코드 (메뉴얼의 메뉴 코드를 이용, get / post로 받아옴)
        $pageCode = Request::request()->toArray();

        // 관리자 패널 클래스 호출
        $this->adminPanel = new AdminPanel();

        // 관리자 로그인 페이지
        if ($this->isLoginPanel($pageCode)) {
            $this->addLoginPanel($cosApi);
        }

        // 관리자 메인 페이지
        if ($this->isAdminMain($pageCode)) {
            // 배너
            $this->addBannerPanels($cosApi);
            // 보드
            $this->addBoardPanels($godoApi, $cosApi);
            // 링크
            $this->addLinkPanels($godoApi);
            // 고객센터
            $this->addCustomerPanel($godoApi);
            // 팝업(cos)
            $this->addPopupPanelByCos($cosApi, 'main');
        }

        // 페이지 패널
        if ($this->isPagePanel($pageCode)) {
            // 팝업(gongji)
            $this->addPopupPanel($godoApi, $pageCode);
        }

        // 회원 리스트 팝업
        if ($this->isMemberListPanel($pageCode)) {
            $this->addPopupPanelByCos($cosApi, 'memberList');
        }

        // CRM 그룹 팝업
        if ($this->isCrmGroupPanel($pageCode)) {
            $this->addPopupPanelByCos($cosApi, 'crmGroup');
        }

        // 디자인 스킨 리스트 팝업
        if ($this->isDesignSkinListPanel($pageCode)) {
            $this->addPopupPanelByCos($cosApi, 'designSkinList');
        }

        // 모바일 디자인 스킨 리스트 팝업
        if ($this->isMobileDesignSkinListPanel($pageCode)) {
            $this->addPopupPanelByCos($cosApi, 'mobileDesignSkinList');
        }

        // 카카오 알림톡 설정
        if ($this->isKakaoAlrimPanel($pageCode)) {
            $this->addKakaoAlrimNoticePanel($cosApi, 'kakaoAlrim');
        }

        // 뱅크다 서비스 종료 안내 팝업
        if ($this->isBankdaServicePanel($pageCode)) {
            $this->addPopupPanelByCos($cosApi, 'bankdaService');
        }

        $panel = $this->adminPanel->getPanel();

        // json data
        $panelData = '{}';
        if (empty($panel) === false) {
            $panelData = json_encode($panel, JSON_UNESCAPED_UNICODE);
        }

        echo $panelData;
        exit();
    }

    /**
     * Initialize cos section code
     * popupPanel은 팝업이 필요한 구좌에 따라서 데이터가 늘어날 수 있으므로, 배열처리 했습니다!
     * @return void
     */
    private function initializePanelSectionCodes(): void
    {
        $cosSectionCodeList = \App::getConfig('cosSectionCodeList')->toArray();

        $this->loginPanelSectionCode = $cosSectionCodeList['loginBanner'];
        $this->bannerPanelMainTopSectionCode = $cosSectionCodeList['mainTopBanner'];
        $this->bannerPanelMainMiddleSectionCode = $cosSectionCodeList['mainMiddleBanner'];
        $this->bannerPanelMainSideSectionCode = $cosSectionCodeList['mainSideBanner'];
        $this->popupPanelLayerSectionCode = [
            'main' => $cosSectionCodeList['layerPopup']['main']
        ];
        $this->popupPanelModalSectionCode = [
            'main' => $cosSectionCodeList['modalPopup']['main'],
            'kakaoAlrim' => $cosSectionCodeList['kakaoAlrim'],
            'memberList' => $cosSectionCodeList['modalPopup']['memberList'],
            'crmGroup' => $cosSectionCodeList['modalPopup']['crmGroup'],
            'designSkinList' => $cosSectionCodeList['modalPopup']['designSkinList'],
            'mobileDesignSkinList' => $cosSectionCodeList['modalPopup']['mobileDesignSkinList'],
            'bankdaService' => $cosSectionCodeList['modalPopup']['bankdaService'],
        ];
    }

    /**
     * @param array $pageCode
     * @return bool
     */
    private function isLoginPanel(array $pageCode): bool
    {
        return $pageCode['menuCode'] === 'base' && empty($pageCode['menuKey']) && $pageCode['menuFile'] === 'login';
    }

    /**
     * @param array $pageCode
     * @return bool
     */
    private function isAdminMain(array $pageCode): bool
    {
        return $pageCode['menuCode'] === 'base' && $pageCode['menuKey'] === 'index' && $pageCode['menuFile'] === 'index';
    }

    /**
     * @param array $pageCode
     * @return bool
     */
    private function isPagePanel(array $pageCode): bool
    {
        return empty($pageCode['menuCode']) === false && empty($pageCode['menuFile']) === false;
    }

    /**
     * @param array $pageCode
     * @return bool
     */
    private function isMemberListPanel(array $pageCode): bool
    {
        return $pageCode['menuCode'] === 'member' && $pageCode['menuFile'] === 'member_list';
    }

    /**
     * CRM 그룹 페이지
     * - CRM > CRM 관리 > CRM 그룹 (crm/crm/crmGroup)
     * @param array $pageCode
     * @return bool
     */
    private function isCrmGroupPanel(array $pageCode): bool
    {
        return $pageCode['menuCode'] === 'crm' && $pageCode['menuFile'] === 'crm_group';
    }

    /**
     * @param array $pageCode
     * @return bool
     */
    private function isDesignSkinListPanel(array $pageCode): bool
    {
        return $pageCode['menuCode'] === 'design' && $pageCode['menuFile'] === 'design_skin_list';
    }

    /**
     * @param array $pageCode
     * @return bool
     */
    private function isMobileDesignSkinListPanel(array $pageCode): bool
    {
        return $pageCode['menuCode'] === 'mobile' && $pageCode['menuFile'] === 'design_skin_list';
    }

    /**
     * @param array $pageCode
     * @return bool
     */
    private function isKakaoAlrimPanel(array $pageCode): bool
    {
        return $pageCode['menuCode'] === 'member' && $pageCode['menuFile'] === 'kakao_alrim_setting';
    }

    /**
     * @param array $pageCode
     * @return bool
     */
    private function isBankdaServicePanel(array $pageCode): bool
    {
        return $pageCode['menuCode'] === 'order' && $pageCode['menuFile'] === 'bankda_service';
    }

    /**
     * @param GodoPanelApi $cosApi
     * @return void
     */
    private function addLoginPanel(GodoPanelApi $cosApi): void
    {
        $setData = $cosApi->getGodoSectionData($this->loginPanelSectionCode);
        if (empty($setData) === false && empty($setData['posts'][0]['postBodyText']) === false) {
            $this->adminPanel->setPanel('banner', 'loginPanel', $setData);
        }
    }

    /**
     * @param GodoPanelApi $cosApi
     * @return void
     */
    private function addBannerPanels(GodoPanelApi $cosApi): void
    {
        $indexBannerPanelKey = [
            'mainTop' => $this->bannerPanelMainTopSectionCode,
            'mainMiddle' => $this->bannerPanelMainMiddleSectionCode,
            'mainSide' => $this->bannerPanelMainSideSectionCode
        ];

        foreach ($indexBannerPanelKey as $panelKey => $sectionCode) {
            $setData = $cosApi->getGodoSectionData($sectionCode);
            if (empty($setData) === false && empty($setData['posts'][0]['postBodyText']) === false) {
                $this->adminPanel->setPanel('banner', $panelKey, $setData);
            }
        }
    }

    /**
     * @param GodoGongjiServerApi $godoApi
     * @param GodoPanelApi $cosApi
     * @return void
     */
    private function addBoardPanels(GodoGongjiServerApi $godoApi, GodoPanelApi $cosApi): void
    {
        $indexBoardPanelKey = [
            'noticeAPI',
            'patchAPI',
            'sellerTipAPI',
            'betterAPI',
        ];

        // cos 카테고리 값 map
        $cosCategoryMap = [
            'noticeAPI' => self::NOTICE_2DEPTH_CATEGORY_NO,
            'patchAPI' => self::PATCH_2DEPTH_CATEGORY_NO,
            'sellerTipAPI' => self::SELLERTIP_CATEGORY_NO,
        ];

        // cos boardCode override (default: COMMERCE_NEWS)
        $cosBoardCodeMap = [
            'sellerTipAPI' => self::SELLERTIP_BOARD_CODE,
        ];

        foreach ($indexBoardPanelKey as $panelKey) {
            if (array_key_exists($panelKey, $cosCategoryMap)) {
                $boardCode = $cosBoardCodeMap[$panelKey] ?? 'COMMERCE_NEWS';
                $setData = $cosApi->getBoardData($cosCategoryMap[$panelKey], self::DEFAULT_BOARD_SIZE, $boardCode);
            } else {
                $setData = $godoApi->getGodoServerData($panelKey);
            }

            if (!empty($setData)) {
                $this->adminPanel->setPanel('board', $panelKey, $setData);
            }
        }
    }

    /**
     * @param GodoGongjiServerApi $godoApi
     * @return void
     */
    private function addLinkPanels(GodoGongjiServerApi $godoApi)
    {
        $arrLink = [
            'allLink',
            'noticeLink',
            'patchLink',
            'sellerTipLink',
            'betterLink',
            'customerLink',
        ];
        $setData = $godoApi->getGodoServerBoardUrl();

        foreach ($arrLink as $panelKey) {
            $this->adminPanel->setPanel('link', $panelKey, $setData[$panelKey]);
        }
    }

    /**
     * @param GodoGongjiServerApi $godoApi
     * @return void
     */
    private function addCustomerPanel(GodoGongjiServerApi $godoApi): void
    {
        // 고객센터 정보
        $setData = $godoApi->getGodoServerCustomerCenter();
        $this->adminPanel->setPanel('customer', 'customerAPI', $setData);
    }

    /**
     * @param GodoGongjiServerApi $godoApi
     * @param array $pageCode
     * @return void
     */
    private function addPopupPanel(GodoGongjiServerApi $godoApi, array $pageCode): void
    {
        // 페이지 카테고리
        $pageCategory = $pageCode['menuCode'] . '/' . $pageCode['menuFile'];
        $setData = $godoApi->getGodoServerData('popupPanel', null, $pageCategory);

        if (empty($setData) === false) {
            $this->adminPanel->setPanel('popup', 'popupPanel', $setData);
        }
    }

    /**
     * @param GodoPanelApi $cosApi
     * @param string $popupAreaCode
     * @return void
     */
    private function addKakaoAlrimNoticePanel(GodoPanelApi $cosApi, string $popupAreaCode): void
    {
        $setData = $cosApi->getGodoSectionData($this->popupPanelModalSectionCode[$popupAreaCode]);
        if (empty($setData) === false && empty($setData['posts'][0]['postBodyText']) === false) {
            $this->adminPanel->setPanel('kakaoAlrim', 'modal', $setData);
        }
    }

    /**
     * @param GodoPanelApi $cosApi
     * @param string $popupAreaCode
     * @return void
     */
    private function addPopupPanelByCos(GodoPanelApi $cosApi, string $popupAreaCode): void
    {
        if (empty($this->popupPanelModalSectionCode[$popupAreaCode]) === false) {
            $setData = $cosApi->getGodoSectionData($this->popupPanelModalSectionCode[$popupAreaCode]);
            if (empty($setData) === false && empty($setData['posts'][0]['postBodyText']) === false) {
                $this->adminPanel->setPanel('popupCos', 'modal', $setData);
            }
        }

        if (empty($this->popupPanelLayerSectionCode[$popupAreaCode]) === false) {
            $setData = $cosApi->getGodoSectionData($this->popupPanelLayerSectionCode[$popupAreaCode]);
            if (empty($setData) === false && empty($setData['posts'][0]['postBodyText']) === false) {
                $this->adminPanel->setPanel('popupCos', 'layer', $setData);
            }
        }
    }
}
