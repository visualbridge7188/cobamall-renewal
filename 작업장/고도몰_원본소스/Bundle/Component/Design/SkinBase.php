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

namespace Bundle\Component\Design;

use Component\Validator\Validator;
use Component\Policy\Policy;
use Component\Mall\Mall;
use Framework\File\FileHandler;
use Globals;
use Message;
use Origin\Enum\Design\DesignEditorSkinType;
use Origin\Service\Design\DesignEditorAssetService;
use Origin\Service\Design\DesignEditorSkinService;
use UserFilePath;

/**
 * 스킨 관리 클래스
 * @author Shin Donggyu <artherot@godo.co.kr>
 */
class SkinBase
{
    // 디비 접속
    protected $db;

    // 스킨명
    protected $skinName;

    // 제외 폴더 배열
    protected $exceptDir = ['.', '..', 'img', 'image', 'images', '.svn', '__gd__history', '__gd__preview',];

    // 제외 화일 배열
    protected $exceptFile = ['.', '..', '.bash_logout', '.bash_profile', '.bashrc', '.emacs', '.bash_history', '.bash_logout.rpmnew', '.bash_profile.rpmnew', '.bashrc.rpmnew',];

    // 허용되는 화일 확장자
    protected $useFileExt = ['htm', 'html', 'css', 'js', 'txt',];

    // 스킨 설정 확장자
    public $skinConfExt = ['.json', '.txt', '.jpg',];

    // 디바이스 종류 (PC - front, 모바일 - mobile)
    // __('모바일')
    public $deviceType = ['front' => 'PC', 'mobile' => '모바일',];

    // 스킨 종류 (PC용 스킨(front) , 모바일용 스킨(mobile))
    public $skinType;

    // 스킨 관련 경로
    public $skinRootPath; // 스킨 루트 폴더 경로
    public $skinPath; // 스킨 폴더 경로
    public $skinImgPath; // 스킨 img 경로
    public $skinConfigDir; // 스킨 설정 경로
    public FileHandler $fileHandler;
    public DesignEditorSkinService $designEditorSkin;
    public DesignEditorAssetService $designEditorAsset;

    /**
     * 생성자
     * @param null $skinType
     * @throws \Exception
     */
    public function __construct($skinType = null)
    {
        $this->designEditorSkin = \App::getInstance(DesignEditorSkinService::class);
        $this->designEditorAsset = \App::getInstance(DesignEditorAssetService::class);

        // 스킨 타입
        if (empty($skinType) === true) {
            $skinType = 'front';
        }
        $this->skinType = $skinType;

        // 스킨 루트 경로
        if ($this->skinType === 'front' || $this->designEditorSkin->isResponsive($this->skinType)) {
            $this->skinRootPath = UserFilePath::frontSkin();
        } else if ($this->skinType === 'mobile') {
            $this->skinRootPath = UserFilePath::mobileSkin();
        } else {
            throw new \Exception(__('잘못된 skinType입니다.'));
        }

        // 스킨 기본 설정 정보 경로
        $this->skinConfigDir = UserFilePath::data('conf', $this->designEditorSkin->getSkinConfigDir($this->skinType)) . DS;

        if (!is_object($this->db)) {
            $this->db = \App::load('DB');
        }
        $this->fileHandler = new FileHandler();
    }

    /**
     * 스킨 설정 반환
     *
     * @param  string $skinName 스킨이름(폴더명)
     * @throws \Exception
     */
    public function setSkin($skinName)
    {
        // Validation
        if (Validator::designSkinName($skinName, true) === false) {
            throw new \Exception('스킨정보가 없습니다.');
        }
        $skinDevice = $this->designEditorSkin->getDesignEditorSkinDevice($this->skinType);
        if ($skinDevice === 'front') {
            if (!UserFilePath::frontSkin($skinName)->isDir()) {
                throw new \Exception(sprintf(__('%s 스킨이 존재하지 않습니다. 스킨 설정을 다시 확인해 주시기 바랍니다.'), $skinName));
            }

            // 스킨 경로
            $this->skinPath = UserFilePath::frontSkin($skinName);
        } else if ($skinDevice === 'mobile') {
            if (!UserFilePath::mobileSkin($skinName)->isDir()) {
                throw new \Exception(sprintf(__('%s 스킨이 존재하지 않습니다. 스킨 설정을 다시 확인해 주시기 바랍니다.'), $skinName));
            }

            // 스킨 경로
            $this->skinPath = UserFilePath::mobileSkin($skinName);
        } else {
            throw new \Exception(__('오류가 발생 하였습니다.'), 'skinName');
        }


        // Set path
        $this->skinName = $skinName;
        $this->skinImgPath = $this->skinPath->add('img');
    }

    /**
     * 스킨 설정 정보 반환.
     * @param string $skinName 스킨명
     * @return array 스킨 설정 정보
     * @throws \Exception
     * @internal param string $designPagePath 디자인 페이지 경로
     */
    public function getSkinConfig($skinName = null)
    {
        // Set skin name
        if($skinName === null ) {
            $skinDevice = $this->designEditorSkin->getDesignEditorSkinDevice($this->skinType);
            $skinName = Globals::get('gSkin.' . $skinDevice . 'SkinWork');
            echo $skinName;
        }

        // 페이지 정보
        $skinDesignPageConfig = $this->skinConfigDir . $skinName . '.json';
        if (is_file($skinDesignPageConfig)) {
            $getData = json_decode(file_get_contents($skinDesignPageConfig), true);
        } else {
            throw new \Exception(sprintf(__('%s 파일이 없습니다.'), $skinDesignPageConfig));
        }

        return $getData;
    }

    /**
     * 스킨 설정 json_encode
     * @param array $setData json_encode 처리할 배열 정보
     * @return string json_encode 처리된 스킨 설정 정보
     */
    protected function setEncode($setData)
    {
        // json_encode 처리
        $setData = json_encode($setData, JSON_UNESCAPED_UNICODE);

        // json_encode 데이타를 보기 편하게
        $setData = str_replace('{"', '{' . PHP_EOL . '"', $setData);
        $setData = str_replace('"},"', '"' . PHP_EOL . '},' . PHP_EOL . PHP_EOL . '"', $setData);
        $setData = str_replace('":{', '":' . PHP_EOL . '{', $setData);
        $setData = str_replace('","', '",' . PHP_EOL . '"', $setData);
        $setData = str_replace('}}', PHP_EOL . '}' . PHP_EOL . '}', $setData);

        return $setData;
    }

    /**
     * 스킨정보
     * @param string $skinName 스킨이름(폴더명)
     * @return array  스킨정보
     * @throws \Exception
     */
    public function getSkinInfo($skinName)
    {
        // Design Editor 스킨 정보 조회
        $this->designEditorSkin->hasSkinType($this->skinType);
        $setData = $this->designEditorSkin->getSkinInfo($this->skinType, $skinName);
        if(empty($setData)) {
            $setData['skin_name'] = $skinName;
            $setData['skin_code'] = $skinName;
        }

        if ($this->fileHandler->isExists($this->skinConfigDir . $skinName . '.jpg') === true) {
            $setData['skin_cover'] = UserFilePath::data('conf', 'skin_' . $this->skinType, $skinName . '.jpg')->www();
        } else {
            $setData['skin_cover'] = PATH_ADMIN_GD_SHARE . 'img/skin_noimg.jpg';
        }

        // 사용가능 국가 정보
        if (empty($setData['skin_country']) === false) {
            $countryCode = explode('_', $setData['skin_country'])[0];
        } else {
            $countryCode = 'kr';
        }

        // 스킨 언어
        $setData['skin_language'] = '';
        foreach (Globals::get('gGlobal.mallList') as $mVal) {
            if ($countryCode === $mVal['domainFl']) {
                $setData['skin_language'] = $mVal['languageFl'];
            }
        }

        return $setData;
    }

    /**
     * 스킨 정보에 대한 키 데이터 반환
     * @param  string $skinName 스킨이름(폴더명)
     * @param  string $dataKey 원하는 정보의 키
     * @return string 스킨 데이터
     */
    public function getSkinData($skinName, $dataKey)
    {
        if (empty($dataKey) === true) {
            return false;
        }

        // 스킨 정보
        $skinInfo = $this->getSkinInfo($skinName);

        return $skinInfo[$dataKey];
    }

    /**
     * 스킨 정보 목록 반환
     * @return array 스킨
     * @throws \Exception
     */
    public function getSkinList()
    {
        if (!$this->skinRootPath->isDir()) {
            throw new \Exception(sprintf(__('%s 폴더가 없습니다.'), 'SkinRoot'));
        }
        $skins = [];
        if ($handle = opendir($this->skinRootPath)) {
            while (false !== ($skinName = readdir($handle))) {
                if ($this->skinRootPath->add($skinName)->isDir() === true && gd_in_array($skinName, $this->exceptDir) === false) {
                    $data = $this->getSkinInfo($skinName);
                    array_push($skins, $data);
                }
            }
            closedir($handle);
        }
        $sortkey = [];
        foreach ($skins as $k => $v) {
            $sortkey[$k] = $v['skin_name'];
        }
        array_multisort($sortkey, $skins);

        return $skins;
    }

    /**
     * 스킨 정보 목록 반환 (front 와 mobile 의 스킨코드와 스킨명을)
     * @return array 스킨
     * @throws \Exception
     */
    public function getSkinListArray()
    {
        $skinRootPath['front'] = UserFilePath::frontSkin();
        $skinRootPath['mobile'] = UserFilePath::mobileSkin();

        $setData = [];
        foreach ($skinRootPath as $tKey => $tVal) {
            if ($handle = opendir($tVal)) {
                $no = 0;
                $tmpData = [];
                while (false !== ($skinName = readdir($handle))) {
                    if ($tVal->add($skinName)->isDir() === true && gd_in_array($skinName, $this->exceptDir) === false) {
                        // Skin Info
                        $skinInfoFile = UserFilePath::data('conf', 'skin_' . $tKey) . DS . $skinName . '.txt';
                        $getData = $this->fileHandler->read($skinInfoFile);
                        if (empty($getData) === false) {
                            $getData = explode(PHP_EOL, $getData);
                            foreach ($getData as $iVal) {
                                if (empty($iVal) === false) {
                                    $tmp = explode(':', $iVal);
                                    if (isset($tmp[1]) === true) {
                                        if (trim($tmp[0]) === 'skin_code') {
                                            $tmp[1] = $skinName;
                                        }
                                        $tmpData[$no][trim($tmp[0])] = trim($tmp[1]);
                                    }
                                }
                            }
                            $no++;
                        }
                    }
                }
                foreach ($tmpData as $value) {
                    $setData[$tKey][] = [
                        'skin_name' => $value['﻿skin_name'],
                        'skin_code' => $value['skin_code'],
                    ];
                }
                closedir($handle);
            }
        }

        return $setData;
    }

    /**
     * 스킨 정보 목록 반환 (front 와 mobile 의 스킨코드 반환)
     * @return array skinCode
     * @throws \Exception
     */
    public function getSkinListByType()
    {
        $skinRootPath['front'] = UserFilePath::frontSkin();
        $skinRootPath['mobile'] = UserFilePath::mobileSkin();

        $setData = [];
        foreach ($skinRootPath as $skinTypeKey => $skinRootPathVal) {
            if (!is_dir($skinRootPathVal)) {
                continue;
            }

            $directory = new \DirectoryIterator($skinRootPathVal);
            foreach ($directory as $entry) {
                if ($entry->isDot() || !$entry->isDir()) {
                    continue;
                }

                $skinName = $entry->getFilename();
                if (in_array($skinName, $this->exceptDir, true)) {
                    continue;
                }

                // 폴더명 저장
                $setData[$skinTypeKey][] = $skinName;
            }
        }
        return $setData;
    }


    /**
     * 스킨 정보 목록 반환 (key => 영문 스킨명, val => 한글 스킨명 (영문 스킨명))
     * @param $deviceDiv boolean 디바이스별 구분 여부
     * @param $deviceFl boolean 디바이스명 출력 여부
     * @return array 스킨
     * @throws \Exception
     */
    public function getSkinSimpleList($deviceDiv = false, $deviceFl = true)
    {
        // 스킨 정보 목록
        $tmpData = $this->getSkinListArray();

        // 스킨 사용 내역
        $tmpStstus = Globals::get('gSkin');

        // 스킨 목록을 변환
        $setData = [];
        foreach ($tmpData as $tKey => $tVal) {
            foreach ($tVal as $sKey => $sVal) {
                $skinString = [];

                // 디바이스명 출력 여부
                if ($deviceFl === true) {
                    $skinString[] = $this->deviceType[$tKey] . ' : ';
                }
                $skinString[] = $sVal['skin_name'] . ' ( ' . $sVal['skin_code'] . ' )';

                // 사용스킨 작업스킨 출력 여부
                $live = $work = false;
                $tmpCodeL = $tKey . 'SkinLive';
                $tmpCodeW = $tKey . 'SkinWork';

                if ($tmpStstus[$tmpCodeL] === $sVal['skin_code']) {
                    $live = true;
                }
                if ($tmpStstus[$tmpCodeW] === $sVal['skin_code']) {
                    $work = true;
                }

                // 디바이스별 구분 여부
                if ($deviceDiv === true) {
                    $setData[$tKey][$sVal['skin_code']] = [
                        'skinLive' => $live,
                        'skinWork' => $work,
                        'skinDesc' => gd_implode('', $skinString),
                    ];
                } else {
                    $setData[$tKey . STR_DIVISION . $sVal['skin_code']] = gd_implode('', $skinString);
                }
            }
        }
        return $setData;
    }

    /**
     * 스킨정보 목록 가져오기
     * 해외상점 스캔해서 사용여부 체크 포함
     *
     * @param $deviceDiv boolean 디바이스별 구분 여부
     * @param $deviceFl boolean 디바이스명 출력 여부
     * @return array 스킨
     * @author Jong-tae Ahn <qnibus@godo.co.kr>
     */
    public function getSkinSimpleOverseasList($deviceDiv = false, $deviceFl = true)
    {
        // 스킨 정보 목록
        $tmpData = $this->getSkinListArray();

        // 해외몰 스킨 사용 내역
        $mall = new Mall();
        $mallListAll = $mall->getList();
        foreach ($mallListAll as $mallInfo) {
            $skinAll[$mallInfo['sno']] = gd_policy('design.skin', $mallInfo['sno']);
            $skinAll[$mallInfo['sno']]['mallName'] = $mallInfo['mallName'];
        }

        // 스킨 목록을 변환
        $setData = [];
        foreach ($tmpData as $tKey => $tVal) {
            foreach ($tVal as $sKey => $sVal) {
                $skinString = [];

                // 디바이스명 출력 여부
                if ($deviceFl === true) {
                    $skinString[] = $this->deviceType[$tKey] . ' : ';
                }
                $skinString[] = $sVal['skin_name'] . ' ( ' . $sVal['skin_code'] . ' )';

                // 사용스킨 작업스킨 출력 여부
                $live = $work = false;
                $kind = [];
                $tmpCodeL = $tKey . 'Live';
                $tmpCodeW = $tKey . 'Work';

                // 해외상점의 사용여부 및 사용상점 분류 작업
                foreach ($skinAll as $skinValue) {
                    if ($skinValue[$tmpCodeL] == $sVal['skin_code']) {
                        $live = true;
                        $kind[] = $skinValue['mallName'];
                    }
                    if ($skinValue[$tmpCodeW] == $sVal['skin_code']) {
                        $work = true;
                        $kind[] = $skinValue['mallName'];
                    }
                }

                // 디바이스별 구분 여부
                if ($deviceDiv === true) {
                    $setData[$tKey][$sVal['skin_code']] = [
                        'skinLive' => $live,
                        'skinWork' => $work,
                        'skinDesc' => gd_implode('', $skinString),
                        'skinKind' => gd_implode(',', gd_array_unique($kind)),
                    ];
                } else {
                    $setData[$tKey . STR_DIVISION . $sVal['skin_code']] = gd_implode('', $skinString);
                }
            }
        }

        return $setData;
    }

    /**
     * 현재 사용중인 스킨정보
     * @param string $skinType 스킨타입
     * @param string $useType 스킨유형
     * @return string
     */
    public function getUseSkinInfo($skinType = 'front', $useType = 'Live')
    {
        $tmpStstus = Globals::get('gSkin');
        $useSkin = $tmpStstus[$skinType . 'Skin' . $useType];
        return $skinType . STR_DIVISION . $useSkin;
    }

    /**
     * 적응형(PC, Mobile), 반응형 스킨 정보 일괄 조회
     * @return array
     * @throws \Exception
     */
    public function getSkinAllList(): array
    {
        $skinInfos = $this->designEditorSkin->getSkinInfos();

        // 썸네일 경로 추가
        foreach ($skinInfos as &$skinInfo) {
            $skinCode = $skinInfo["skin_code"];
            $skinDevice = $skinInfo['skin_device'];
            $skinInfo['skin_cover'] = $this->getSkinThumbnailPath($skinDevice, $skinCode);

            // 스킨 타입이 반응형(responsive)인 경우 모바일 썸네일 추가
            if ($skinInfo['skin_type'] == 'responsive') {
                $skinInfo['skin_cover_front'] = $this->getSkinThumbnailPath($skinDevice, $skinCode . "_front");
                $skinInfo['skin_cover_mobile'] = $this->getSkinThumbnailPath($skinDevice, $skinCode . "_mobile");
            }
        }

        return $skinInfos;
    }

    /**
     * 스킨 타입에 따른 최근 편집 날짜 조회
     * - 반응형(responsive): es_designEditorAsset 테이블에서 해당 skin_sno의 가장 최근 수정일자
     * - 적응형(adaptive): /data/conf/skin_{skin_device}/{skin_code}.json 파일의 수정 일자
     *
     * @param array $skinInfo
     * @return string|null
     */
    public function getSkinLastEditDate(array $skinInfo): ?string
    {
        $skinType = $skinInfo['skin_type'] ?? 'adaptive';

        if ($skinType === 'responsive') {
            return $this->getResponsiveSkinLastEditDate($skinInfo);
        }

        return $this->getAdaptiveSkinLastEditDate($skinInfo);
    }

    /**
     * 반응형 스킨의 최근 편집 날짜 조회 (es_designEditorAsset에서 최신 수정일자)
     *
     * @param array $skinInfo
     * @return string|null
     */
    protected function getResponsiveSkinLastEditDate(array $skinInfo): ?string
    {
        if (!isset($skinInfo['skin_sno'])) {
            return null;
        }

        return $this->designEditorAsset->findLatestModifiedDateBySkinSno($skinInfo['skin_sno']);
    }

    /**
     * 적응형 스킨의 최근 편집 날짜 조회 (json 파일의 수정 일자)
     *
     * @param array $skinInfo
     * @return string|null
     */
    protected function getAdaptiveSkinLastEditDate(array $skinInfo): ?string
    {
        $skinCode = $skinInfo['skin_code'] ?? '';
        $skinDevice = $skinInfo['skin_device'] ?? 'front';

        if (empty($skinCode)) {
            return null;
        }

        $jsonFilePath = UserFilePath::data('conf', 'skin_' . $skinDevice) . DS . $skinCode . '.json';

        if (!$this->fileHandler->isExists($jsonFilePath)) {
            return null;
        }

        $mtime = filemtime($jsonFilePath);
        if ($mtime === false) {
            return null;
        }

        return date('Y-m-d H:i:s', $mtime);
    }

    protected function getSkinThumbnailPath(string $skinDevice, string $fileName): string
    {
        if ($this->fileHandler->isExists(UserFilePath::data('conf', 'skin_' . $skinDevice) . DS . $fileName . ".jpg") === true) {
            return UserFilePath::data('conf', 'skin_' . $skinDevice, $fileName . '.jpg')->www();
        }

        return PATH_ADMIN_GD_SHARE . 'img/skin_noimg.jpg';
    }

    protected function getSkinDevice(): string
    {
        return $this->skinType === 'mobile' ? 'mobile' : 'front';
    }

    /**
     * 반응형 스킨이 적용된 몰 목록 조회
     * @return array 반응형 스킨이 적용된 몰 이름 배열
     */
    public static function getResponsiveSkinMallNames(): array
    {
        $mall = new Mall();
        $mallList = $mall->getList();
        $responsiveMallNames = [];

        foreach ($mallList as $mallInfo) {
            $skinConfig = gd_policy('design.skin', $mallInfo['sno']);
            $skinType = $skinConfig['skinType'] ?? DesignEditorSkinType::ADAPTIVE->value;

            if ($skinType === DesignEditorSkinType::RESPONSIVE->value) {
                $responsiveMallNames[] = $mallInfo['mallName'];
            }
        }

        return $responsiveMallNames;
    }

    /**
     * 반응형 스킨 경고 메시지 생성
     * @param array $mallNames 반응형 스킨이 적용된 몰 이름 배열
     * @return string 경고 메시지
     */
    public static function getResponsiveSkinWarningMessage(array $mallNames): string
    {
        $mallNamesStr = implode(', ', $mallNames);
        return "현재 {$mallNamesStr}은 반응형 스킨이 적용되어 있어, 본 메뉴의 일부 기능이 지원되지 않습니다.<br>해당 몰의 디자인 수정은 디자인 에디터를 이용해 주세요.<br>다른 몰 설정은 계속 이용하실 수 있습니다.";
    }

    /**
     * 반응형 스킨 경고 얼럿 메시지 반환
     * @return string|null 반응형 스킨이 적용된 몰이 있으면 경고 메시지, 없으면 null
     */
    public static function getResponsiveSkinAlertMessage(): ?string
    {
        $responsiveMallNames = self::getResponsiveSkinMallNames();
        if (empty($responsiveMallNames)) {
            return null;
        }
        return self::getResponsiveSkinWarningMessage($responsiveMallNames);
    }
}
