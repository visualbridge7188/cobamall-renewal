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

namespace Bundle\Controller\Admin\Design;

use Framework\Debug\Exception\AlertOnlyException;
use Framework\Enum\ExternalUrl;
use Framework\Utility\StringUtils;
use Origin\Service\Design\DesignEditorSkinService;
use UserFilePath;
use Origin\Service\Aggregator\GodomallAggregatorService;
use Origin\Service\MyApp\MyappSettingService;
use Origin\Enum\ManagerTutorialType;
use Origin\Service\Admin\Member\ManagerTutorial\HelperPopupExposureAvailabilityService;



/**
 * 디자인 스킨 선택
 * @author Shin Donggyu <artherot@godo.co.kr>
 */
class DesignSkinListController extends \Controller\Admin\Controller
{
    private const SKIN_PREVIEW_BASE_URL = "../design/design_skin_preview_ps.php";

    /**
     * index
     *
     * @throws AlertOnlyException
     */
    public function index()
    {
        //--- 메뉴 설정
        $this->callMenu('design', 'designSkin', 'skinList');
        $this->setMenuCode('design', 'designSkin', 'skinList');
        $request = \App::getInstance('request');
        //--- 페이지 데이터
        try {
            $getValue = $request->get()->all();
            $mall = \App::load('Component\\Mall\\Mall');
            $mallSelect = [];

            try {
                $designEditorSkinService = \App::getInstance(DesignEditorSkinService::class);

                // 스테이징(개발) 상점 한정 — responsive 자체 마이그를 adaptive 보다 먼저 실행
                // 순서 중요: adaptive 마이그는 파일만으론 반응형을 구분하지 못하고 DB 의 responsive 목록에 의존해
                // front 목록에서 제외하므로, responsive 가 DB 에 선 등록돼야 같은 코드가 adaptive 로 중복 등록되지 않음
                if (\Globals::get('gLicense.isDevShop')) {
                    $designEditorSkinService->migrateResponsiveSkinInfo();
                }
                $designEditorSkinService->migrateAdaptiveSkinInfo();
            } catch (\Throwable $e) {
                \Logger::channel('designEditor')->warning(__METHOD__ . ', ' . $e->getFile() . '[' . $e->getLine() . '], ' . $e->getMessage(), $e->getTrace());
            }

            //--- skinBase 정의
            $skinBase = \App::load('Component\\Design\\SkinBase');
            $skinList = $skinBase->getSkinAllList();

            //--- freeSkinBanner 정의
            $freeSkinBanner = '';
            /** @var \Bundle\Component\Godo\GodoPanelApi $godoApi */
            $godoApi = \App::load('Component\\Godo\\GodoPanelApi');
            $setData = $godoApi->getGodoSectionData(\App::getConfig('cosSectionCodeList')->toArray()['designBanner']);

            if (isset($setData['posts'][0]['postBodyText'])) {
                $freeSkinBanner = $setData['posts'][0]['postBodyText'];
            }

            if ($mall->isUsableMall() === true) {
                $mallIconType = gd_policy('design.mallIconType');
                $mallIconType['iconType'] = gd_isset($mallIconType['iconType'], 'check');
                $mallIconType['iconTypeMobile'] = gd_isset($mallIconType['iconTypeMobile'], 'check');

                $mallList = $mall->getListByUseMall();

                foreach ($mallList as $mallInfo) {
                    $mallSelect[$mallInfo['sno']] = $mallInfo['mallName'];
                }
            } else {
                $mallSelect[1] = '기준몰';
            }

            $mallListAll = $mall->getList();
            foreach ($mallListAll as $mallInfo) {
                if (is_array($mallIconType['mallIcon'])) {
                    $mallIconType['mallIcon'][$mallInfo['sno']] = gd_isset($mallIconType['mallIcon'][$mallInfo['sno']], 'ico_' . $mallInfo['domainFl'] . '.png');
                } else {
                    $mallIconType['mallIcon'] = [$mallInfo['sno'] => 'ico_' . $mallInfo['domainFl'] . '.png'];
                }
            }

            // 멀티상점용 스킨 load
            $mallSno = gd_isset($getValue['mallSno'], 1);
            $session = \App::getInstance('session');
            $session->set('mallSno', $mallSno);
            $pageCacheConfig = \App::getConfig('app.cache.page')->toArray();
            $widgetCacheConfig = \App::getConfig('app.cache.widget')->toArray();
            if (count(array_merge($pageCacheConfig['front'] ?? [], $pageCacheConfig['mobile'] ?? [], $widgetCacheConfig)) > 0) {
                $this->setData('cacheUrl', '../design/design_skin_list_ps.php?mode=clearCache&mallSno=' . $mallSno);
            }

            $checked['iconType'][$mallIconType['iconType']] = 'checked="checked"';
            $checked['iconTypeMobile'][$mallIconType['iconTypeMobile']] = 'checked="checked"';
        } catch (\Exception $e) {
            throw new AlertOnlyException($e->getMessage());
        }

        // 무료스킨추가 연결 도메인
        $designCenterUrl = ExternalUrl::DESIGN_CENTER->getUrl('freeSkin?shopNo=' . \Globals::get('gLicense.godosno'));
        $this->setData('designCenterUrl', $designCenterUrl);

        // 반응형 무료스킨 다운로드 URL
        $godoWwwConfig = \App::getSystemConfig('internal')['godo-www'];
        if (empty($godoWwwConfig['url']) || empty($godoWwwConfig['path']['free-skin']) || empty(\Globals::get('gLicense.godosno'))) {
            throw new \Exception('무료스킨 설정이 올바르지 않습니다.');
        }

        $freeSkinUrl = $godoWwwConfig['url']
            . $godoWwwConfig['path']['free-skin']
            . '?shopNo=' . \Globals::get('gLicense.godosno');
        $this->setData('freeSkinUrl', $freeSkinUrl);

        // NCDS 스타일 적용을 위한 body 클래스 추가
        $this->setData('adminBodySubClass', 'ncds');

        //--- 관리자 디자인 템플릿
        $this->getView()->setDefine('layoutMenu', 'menu_design.php');

        $designSkinListUrl = '../mobile/design_skin_list.php';

        // 스킨 미리보기 URL
        $skinPreviewBaseUrl = '../design/design_skin_preview_ps.php?skinPreviewCode=' . $mallSno . STR_DIVISION;

        // 모든 몰에서 사용 중인 스킨 코드 수집
        $usedSkinCodes = [];
        foreach ($mallListAll as $mallInfo) {
            $mallSkinConfig = gd_policy('design.skin', $mallInfo['sno']);
            if (!empty($mallSkinConfig['frontLive'])) {
                $usedSkinCodes['front'][$mallSkinConfig['frontLive']] = $mallInfo['sno'];
            }
            if (!empty($mallSkinConfig['mobileLive'])) {
                $usedSkinCodes['mobile'][$mallSkinConfig['mobileLive']] = $mallInfo['sno'];
            }
        }

        // 스킨 설정 정보, (사용 중 아닌) 스킨 목록 설정
        $designSkinConfig = gd_policy('design.skin', $mallSno);
        $usingSkinType = $designSkinConfig['skinType'] ?? 'adaptive';

        // 모바일샵 설정 정보
        $mobileConfig = gd_policy('mobile.config');
        $mobileShopFl = $mobileConfig['mobileShopFl'] ?? 'n';

        $skinConf = ['skin_type' => $usingSkinType];
        $unusedSkinList = [];

        foreach ($skinList as $skin) {
            $skinType = $skin['skin_type'] ?? 'adaptive';
            $skinDevice = $skin['skin_device'];
            $skinCode = $skin['skin_code'];

            // 적응형 스킨에만 default_design_page_id 추가
            if ($skinType === 'adaptive') {
                $skin['default_design_page_id'] = $this->getDefaultDesignPageId($skinCode, $skinDevice);
            }

            // 다른 몰에서 사용 중인 스킨은 목록에서 제외
            if (isset($usedSkinCodes[$skinDevice][$skinCode])) {
                $usedByMallSno = $usedSkinCodes[$skinDevice][$skinCode];

                // 현재 몰에서 사용 중인 스킨 정보 설정
                if ($usedByMallSno == $mallSno && $skinType == $usingSkinType) {
                    $lastEditDate = $skinBase->getSkinLastEditDate($skin);
                    $skin['skin_last_edit_date'] = $this->getRelativeTimeString($lastEditDate ?? '', '편집됨');
                    $skinConf[$skinDevice]['liveInfo'] = $skin;
                    $skinConf[$skinDevice]['previewUrl'] = $skinPreviewBaseUrl . $skinDevice . STR_DIVISION . $skinCode;
                }
                continue;
            }

            $skin['registered_date'] = $this->getRelativeTimeString($skin['registered_date'] ?? '', '생성됨');
            $unusedSkinList[] = $skin;
        }

        // 인기 급상승 스킨 에그리게이터 경로
        $godomallAggregatorService = \App::getInstance(GodomallAggregatorService::class);
        $aggregatorUrl = $godomallAggregatorService->getAggregatorUrl('top-trending-skins');

        try {
            $myappSettingService = \App::getInstance(MyappSettingService::class);
            $myappStatus = $myappSettingService->getMyappStatus();
            $isMyappInstalled = $myappStatus->isInstalled();
            $isMyappReleased = $myappStatus->isReleased();
        } catch (\Throwable $e) {
            \Logger::channel('designEditor')->error($e->getMessage());
            $isMyappInstalled = false;
            $isMyappReleased = false;
        }

        $this->setData('isMyappInstalled', $isMyappInstalled);
        $this->setData('isMyappReleased', $isMyappReleased);

        $this->setData('mallSno', $mallSno);
        $this->setData('mallCnt', gd_count($mallList));
        $this->setData('mallList', $mallList);
        $this->setData('mallListAll', $mallListAll);
        $this->setData('mallSelect', $mallSelect);
        $this->setData('freeSkinBanner', $freeSkinBanner);
        $this->setData('skinList', $unusedSkinList);
        $this->setData('skinConf', $skinConf);
        $this->setData('mobileShopFl', $mobileShopFl);
        $this->setData('skinPreviewUrl', $skinPreviewBaseUrl);
        $this->setData('uriCommon', \UserFilePath::data('commonimg')->www());
        $this->setData('checked', $checked);
        $this->setData('mallIcon', $mallIconType['mallIcon']);
        $this->setData('gReferrer', true);
        $this->setData('designSkinListUrl', $designSkinListUrl);
        $this->setData('aggregatorUrl', $aggregatorUrl);
        $this->setData('skinTabData', $this->getSkinTabData($unusedSkinList, $usingSkinType));
        $this->setData('hasResponsiveSkin', $designEditorSkinService->hasResponsiveSkin());

        // 튜토리얼 연계 모달 경로 조회
        $helperPopupExposureAvailabilityService = \App::getInstance(HelperPopupExposureAvailabilityService::class);
        $tutorialLinkModalUrl = $helperPopupExposureAvailabilityService->getTutorialUrl(ManagerTutorialType::SUBSCRIPTION_SKIN_PATCH->value);
        $this->setData('tutorialLinkModalUrl', $tutorialLinkModalUrl);
        $this->setData('tutorialCode', ManagerTutorialType::SUBSCRIPTION_SKIN_PATCH->value);

        // 튜토리얼 스텝가이드 즉시 실행 여부
        $tutorialMode = \Request::get()->get('tutorialMode', 'false');
        $this->setData('tutorialMode', $tutorialMode);

        // 튜토리얼 링크 모달 사이즈 및 스텝가이드 단계 설정
        $tutorialConfig = \App::getConfig('tutorial.regularDelivery')->toArray()[ManagerTutorialType::SUBSCRIPTION_SKIN_PATCH->value];
        $this->setData('tutorialLinkModalSize', $tutorialConfig['linkModalSize']);
        $this->setData('tutorialStepGuideSteps', $tutorialConfig['stepGuideSteps']);

        // 튜토리얼 진행 현황 조회
        $tutorialStepGuideLinks = $helperPopupExposureAvailabilityService->getTutorialProgress(ManagerTutorialType::SUBSCRIPTION_SKIN_PATCH->value);
        $this->setData('tutorialStepGuideLinks', $tutorialStepGuideLinks);

        $this->getView()->setDefine('activeDesignSkinSection', 'design/design_skin_list/active_design_skin_section.php');
        $this->getView()->setDefine('activeDesignSkinResponsiveSection', 'design/design_skin_list/active_design_skin_responsive_section.php');
        $this->getView()->setDefine('ownedSkinListSection', 'design/design_skin_list/owned_skin_list_section.php');
        $this->getView()->setDefine('overseasMallIconSettingSection', 'design/design_skin_list/overseas_mall_icon_setting_section.php');
        $this->getView()->setDefine('topTrendingSkinsSection', 'design/design_skin_list/top_trending_skins_section.php');
    }

    /**
     * 스킨의 main 폴더에서 첫 번째 HTML 파일명을 가져옴
     *
     * @param string $skinCode 스킨 코드
     * @param string $skinDevice 스킨 디바이스 (front/mobile)
     * @return string 기본 디자인 페이지 ID (main/파일명 형식, 없으면 'default')
     */
    private function getDefaultDesignPageId(string $skinCode, string $skinDevice): string
    {
        $skinPath = $skinDevice === 'mobile'
            ? UserFilePath::mobileSkin($skinCode)
            : UserFilePath::frontSkin($skinCode);

        $mainFolderPath = $skinPath->add('main');

        // main 폴더가 없는 경우
        if (!$mainFolderPath->isDir()) {
            return 'default';
        }

        $mainFolderRealPath = $mainFolderPath->getRealPath();
        if (!is_dir($mainFolderRealPath)) {
            return 'default';
        }

        $files = scandir($mainFolderRealPath);

        foreach ($files as $file) {
            if ($file === '.' || $file === '..') {
                continue;
            }

            $filePath = $mainFolderRealPath . DIRECTORY_SEPARATOR . $file;
            if (!is_file($filePath)) {
                continue;
            }

            $extension = strtolower(pathinfo($file, PATHINFO_EXTENSION));
            if ($extension === 'html' || $extension === 'htm') {
                return 'main/' . $file;
            }
        }

        return 'default';
    }

    /**
     * 상대 시간 문자열 반환
     *
     * @param string $dateString 날짜 문자열
     * @param string $suffix 접미사 (편집됨/생성됨)
     * @return string 상대 시간 문자열
     */
    private function getRelativeTimeString(string $dateString, string $suffix): string
    {
        if (empty($dateString)) {
            return '';
        }

        $timestamp = strtotime($dateString);
        if ($timestamp === false) {
            return '';
        }

        $now = time();
        $diff = $now - $timestamp;
        $diffHours = floor($diff / 3600);

        // 1시간 이내
        if ($diffHours < 1) {
            return '방금 전에 ' . $suffix;
        }

        // 1시간 이후 24시간 이내
        if ($diffHours < 24) {
            return $diffHours . '시간 전에 ' . $suffix;
        }

        // 24시간 이후
        $skinYear = (int)date('Y', $timestamp);
        $currentYear = (int)date('Y', $now);
        $month = (int)date('n', $timestamp);
        $day = (int)date('j', $timestamp);

        // 올해가 아닌 경우
        if ($skinYear !== $currentYear) {
            return $skinYear . '년 ' . $month . '월 ' . $day . '일에 ' . $suffix;
        }

        return $month . '월 ' . $day . '일에 ' . $suffix;
    }

    /**
     * 스킨 타입별 개수 및 탭 active 상태 계산
     *
     * @param array $skinList 스킨 목록
     * @param string $usingSkinType 현재 사용 중인 스킨 타입
     * @return array
     */
    private function getSkinTabData(array $skinList, string $usingSkinType): array
    {
        $responsiveSkinCount = 0;
        $adaptiveSkinCount = 0;

        foreach ($skinList as $skin) {
            if (($skin['skin_type'] ?? 'adaptive') === 'responsive') {
                $responsiveSkinCount++;
            } else {
                $adaptiveSkinCount++;
            }
        }

        // 기본 탭 결정: 둘 다 있으면 사용 중인 타입, 아니면 보유한 타입
        if ($responsiveSkinCount > 0 && $adaptiveSkinCount > 0) {
            $defaultTab = $usingSkinType;
        } elseif ($responsiveSkinCount > 0) {
            $defaultTab = 'responsive';
        } else {
            $defaultTab = 'adaptive';
        }

        return [
            'responsiveSkinCount' => $responsiveSkinCount,
            'adaptiveSkinCount' => $adaptiveSkinCount,
            'isResponsiveActive' => $usingSkinType === 'responsive',
            'isAdaptiveActive' => $usingSkinType !== 'responsive',
            'defaultTab' => $defaultTab,
        ];
    }
}
