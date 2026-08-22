<?php
/*
 * Copyright (C) 2026 NHN COMMERCE. - All Rights Reserved
 *
 * Unauthorized copying or redistribution of this file in source and binary forms via any medium
 * is strictly prohibited.
 */

namespace Bundle\Component\Goods;

use Framework\Log\Logger;
use Framework\Utility\StringUtils;
use Request;
use Session;

class SimpleGoods
{
    /** 과세/면세 폴백 기본값 (DBTableField::tableGoods 의 폴백과 동일하게 유지) */
    private const DEFAULT_TAX_FREE_FL = 't';
    private const DEFAULT_TAX_PERCENT = 10;

    public function __construct(
        private readonly SimpleGoodsDAO $dao,
        private readonly Logger $logger
    ) {
    }

    public function registerSimpleGoods(int $goodsNo): void
    {
        try {
            $this->dao->insertSimpleGoods($goodsNo);
        } catch (\Throwable $e) {
            $this->logger->channel('simpleGoods')->warning(__METHOD__ . ' 간편등록 테이블 INSERT 실패 goodsNo: ' . $goodsNo, ['exception' => $e->getMessage()]);
        }
    }

    /**
     * 간편상품등록 전용 기본값 세팅
     *
     * 간편등록 폼은 UI에 없는 필드를 미전송하므로, 일반상품등록 신규 폼과 동일한
     * 기본값을 POST 데이터에 채워 저장 로직(saveInfoGoodsAndUpdateRegularGoods)에 넘긴다.
     *
     * @param array $postValue 간편등록 폼 POST 데이터
     * @return array 기본값이 병합된 POST 데이터
     */
    public function prepareRegisterPostValue(array $postValue): array
    {
        $goods = \App::load('\\Component\\Goods\\GoodsAdmin');

        // 상품이미지 사이즈
        $simpleImageConf = gd_policy('goods.image');
        $simpleImageType = gd_isset($simpleImageConf['imageType']);
        $postValue['imageSize'] = [];
        foreach ($simpleImageConf as $imgKey => $imgVal) {
            if (is_array($imgVal) === false || isset($imgVal['size1']) === false || $imgVal['size1'] === '') {
                continue;
            }
            if ($simpleImageType === 'fixed') {
                $postValue['imageSize'][$imgKey] = $imgVal['size1'] . INT_DIVISION . gd_isset($imgVal['hsize1']);
            } else {
                $postValue['imageSize'][$imgKey] = $imgVal['size1'];
            }
        }

        // 이미지 리사이즈: 간편상품등록은 항상 자동 리사이즈로 저장 (original + 각 이미지 종류로 fan-out)
        $postValue['imageResize'] = ['original' => 'y'];
        foreach ($simpleImageConf as $imgKey => $imgVal) {
            if (is_array($imgVal) === false) {
                continue;
            }
            $postValue['imageResize'][$imgKey] = 'y';
        }

        // 간편상품등록 기본값 세팅
        if (empty($postValue['goodsNmFl'])) {
            $postValue['goodsNmFl'] = 'd';
        }
        if (empty($postValue['commission'])) {
            $postValue['commission'] = '0';
        }

        // 본사가 공급사 선택시 수수료 처리
        if (!empty($postValue['scmNo']) && $postValue['scmNo'] != DEFAULT_CODE_SCMNO) {
            $scmNo = (int)$postValue['scmNo'];
            if ($scmNo > 0) {
                $scm = \App::load('\\Component\\Scm\\ScmAdmin');
                $scmInfo = $scm->getScmInfo($scmNo, 'scmCommission');
                if (!empty($scmInfo)) {
                    $postValue['commission'] = $scmInfo['scmCommission'];
                }
            }
        }

        // 메인상품 진열 / 인기상품 포함 기본값 (본사만 처리 — 일반상품등록과 동일)
        if (!Session::get('manager.isProvider')) {
            $mainDisplayTheme = $goods->getAdminMainDisplayTheme();
            if (is_array($mainDisplayTheme)) {
                foreach ($mainDisplayTheme as $theme) {
                    if ($theme['sortAutoFl'] == 'y') {
                        $postValue['displayThemeSno'][] = $theme['sno'];
                    }
                }
            }

            $populate = \App::load('\\Component\\Goods\\Populate');
            $populateInfo = $populate->getPopulateData();
            if (is_array($populateInfo)) {
                foreach ($populateInfo as $pInfo) {
                    if ($pInfo['range'] == 'all') {
                        $postValue['populateListSno'][] = $pInfo['sno'];
                    }
                }
            }
        }

        // 성인인증/접근권한 노출 기본값
        if (empty($postValue['onlyAdultDisplayFl'])) {
            $postValue['onlyAdultDisplayFl'] = 'y';
        }
        if (empty($postValue['goodsAccessDisplayFl'])) {
            $postValue['goodsAccessDisplayFl'] = 'y';
        }

        // 네이버페이 사용/적립 기본값
        if (empty($postValue['naverNpayAble'])) {
            $postValue['naverNpayAble'] = ['pc', 'mobile'];
        }
        if (empty($postValue['naverNpayAcumAble'])) {
            $postValue['naverNpayAcumAble'] = ['pc', 'mobile'];
        }

        // 네이버 연령대 기본값 (간편등록은 UI가 없어 미전송 → 일반등록 신규 폼과 동일: 전체 'a')
        if (empty($postValue['naverAgeGroup'])) {
            $postValue['naverAgeGroup'] = 'a';
        }

        // 페이스북 제품 피드 사용 기본값 (일반등록 신규 폼과 동일: 설정안함 'n')
        if (empty($postValue['fbUseFl'])) {
            $postValue['fbUseFl'] = 'n';
        }

        // 구글 쇼핑 상품 피드 사용 기본값 (일반등록 신규 폼과 동일: 설정함 'y')
        if (empty($postValue['googleUseFl'])) {
            $postValue['googleUseFl'] = 'y';
        }

        // SEO 태그 기본값 (일반등록 신규 폼과 동일: 사용안함 'n'.
        // seoTagFl 세팅 시 저장 로직이 SEO 레코드를 생성하므로 seoTag는 빈 배열로 전달)
        if (empty($postValue['seoTagFl'])) {
            $postValue['seoTagFl'] = 'n';
        }
        if (empty($postValue['seoTag']) || !is_array($postValue['seoTag'])) {
            $postValue['seoTag'] = [];
        }

        // HS코드 기본값 (간편등록은 UI가 없어 미전송 → 저장 로직의 array_map(trim, hscode)가
        // PHP8에서 TypeError 발생. 일반등록 신규와 동일하게 빈 배열로 세팅 → 최종 저장값 "")
        if (empty($postValue['hscode']) || !is_array($postValue['hscode'])) {
            $postValue['hscode'] = [];
        }
        if (empty($postValue['hscodeNation']) || !is_array($postValue['hscodeNation'])) {
            $postValue['hscodeNation'] = [];
        }

        // 옵션 미사용 시 optionN 기본값 (간편등록은 UI가 없어 미전송 → 일반등록 신규 폼의 hidden과 동일
        if (empty($postValue['optionN']) || !is_array($postValue['optionN'])) {
            $postValue['optionN'] = ['sno' => [''], 'optionNo' => ['']];
        }

        // KC인증 기본값 (간편등록은 UI가 없어 미전송 → saveInfoGoods의 kcmarkInfo foreach가
        // null 순회로 Warning 발생 및 "null" 저장. 일반등록 미사용('n') 제출과 동일하게 세팅)
        if (empty($postValue['kcmarkInfo']['kcmarkFl'])) {
            $postValue['kcmarkInfo']['kcmarkFl'] = 'n';
        }
        if (empty($postValue['kcmarkInfo']['kcmarkNo']) || !is_array($postValue['kcmarkInfo']['kcmarkNo'])) {
            $postValue['kcmarkInfo']['kcmarkNo'] = [''];
        }
        if (empty($postValue['kcmarkInfo']['kcmarkDivFl']) || !is_array($postValue['kcmarkInfo']['kcmarkDivFl'])) {
            $postValue['kcmarkInfo']['kcmarkDivFl'] = [''];
        }
        if (empty($postValue['kcmarkDt']) || !is_array($postValue['kcmarkDt'])) {
            $postValue['kcmarkDt'] = [''];
        }

        // 배송/AS/반품/교환 이용안내 기본값 (간편등록은 UI가 없어 미전송 →
        // 일반등록 신규 폼과 동일하게 몰에 설정된 기본 이용안내를 자동 적용.
        $buyerInform = \App::load('\\Component\\Agreement\\BuyerInform');
        $goodsInfoDetail = $buyerInform->getGoodsInfoCode('register', $postValue['scmNo'] ?? null);
        foreach (['detailInfoDelivery', 'detailInfoAS', 'detailInfoRefund', 'detailInfoExchange'] as $detailInfoKey) {
            if (!empty($postValue[$detailInfoKey . 'Fl'])) {
                continue;
            }
            if (!empty($goodsInfoDetail['default'][$detailInfoKey])) {
                $postValue[$detailInfoKey] = $goodsInfoDetail['default'][$detailInfoKey];
                $postValue[$detailInfoKey . 'Fl'] = 'selection';
            } else {
                $postValue[$detailInfoKey . 'Fl'] = 'no';
            }
        }

        // 과세/면세 정책 기본값 반영
        $taxConf = gd_policy('goods.tax');
        if (empty($postValue['taxFreeFl'])) {
            $postValue['taxFreeFl'] = empty($taxConf['taxFreeFl']) ? self::DEFAULT_TAX_FREE_FL : $taxConf['taxFreeFl'];
        }
        if (empty($postValue['taxPercent'])) {
            $postValue['taxPercent'] = empty($taxConf['taxPercent']) ? self::DEFAULT_TAX_PERCENT : $taxConf['taxPercent'];
        }

        // 간편상품등록: optionYIcon POST 데이터 초기화 (기존 상품등록은 옵션 팝업에서 설정)
        $postValue['optionYIcon']['optionImageAddUrl'] = '';
        $postValue['optionYIcon']['goodsImage'][0] = $postValue['optionYIcon']['goodsImage'][0] ?? [];

        // 옵션 이미지를 일반상품등록과 동일한 임시세션(temp) 경로로 브릿지
        $postValue = $this->prepareOptionImageTempSession($postValue);

        // 상세설명 XSS 대응
        foreach (['goodsDescription', 'goodsDescriptionMobile'] as $descKey) {
            if (!empty($postValue[$descKey]) && is_string($postValue[$descKey])) {
                $postValue[$descKey] = StringUtils::xssClean($postValue[$descKey]);
            }
        }

        return $postValue;
    }

    /**
     * 간편상품등록 옵션 이미지 → 일반상품등록(temp세션) 경로로 브릿지
     *
     * 간편등록 폼은 옵션 팝업을 쓰지 않고 옵션 이미지를 인라인으로 전송한다.
     *  - optionYIcon[goodsImage][0][]     : 신규 업로드 파일 ($_FILES)
     *  - optionYIcon[goodsImageText][0][] : URL 직접입력
     *  - sgrExistingImage[0][]            : 기존상품에서 불러온 이미지 파일명 (이미 option_temp/에 복사됨)
     *
     * 이를 일반상품등록의 옵션 팝업(GoodsAdmin::goodsOptionTempRegister)과 동일하게 option_temp/ 폴더 +
     * DB_GOODS_OPTION_ICON_TEMP 임시테이블에 세션 단위로 적재하고 optionTempSession 을 세팅한다.
     * 이후 GoodsAdmin 은 팝업 등록과 동일한 정상 경로(임시테이블 조회)로 옵션 이미지를 처리하므로
     * GoodsAdmin 에 간편등록 전용 분기가 필요없다.
     *
     * 옵션 이미지는 대표(첫번째) 옵션 그룹에만 부여되므로 그룹 인덱스 0 만 처리한다.
     *
     * @param array $postValue 간편등록 폼 POST 데이터
     * @return array optionTempSession 이 세팅된 POST 데이터
     */
    public function prepareOptionImageTempSession(array $postValue): array
    {
        // 옵션 미사용이면 옵션 이미지 처리 불필요
        if (($postValue['optionFl'] ?? '') !== 'y') {
            return $postValue;
        }

        $optionFiles    = Request::files()->get('optionYIcon') ?? [];       // $_FILES[optionYIcon]
        $uploadNames    = $optionFiles['name']['goodsImage'][0] ?? [];      // [vKey => 업로드 파일명]
        $urlImages      = $postValue['optionYIcon']['goodsImageText'][0] ?? [];
        $existingImages = $postValue['sgrExistingImage'][0] ?? [];
        $optionValues   = $postValue['optionY']['optionValue'][0] ?? [];
        $isUrlMode      = ($postValue['optionImageAddUrl'] ?? '') === 'y';

        // 대표 옵션 그룹(0)에서 처리할 옵션값 인덱스(vKey) 집합
        $valueKeys = array_unique(array_merge(
            array_keys($uploadNames),
            array_keys($urlImages),
            array_keys($existingImages)
        ));

        if (empty($valueKeys)) {
            return $postValue;
        }

        $optionTempPath = \App::getUserBasePath() . '/data/goods/option_temp';
        $session        = $this->dao->generateOptionIconTempSession();
        $iconTempRows   = [];

        foreach ($valueKeys as $vKey) {
            $goodsImage = null;

            if ($isUrlMode && !empty($urlImages[$vKey])) {
                // URL 직접입력
                $goodsImage = $urlImages[$vKey];
            } elseif (gd_file_uploadable($optionFiles, 'image', 'goodsImage', 0, $vKey)) {
                // 신규 업로드 파일 → option_temp/ 로 이동 (팝업 등록과 동일한 세션 기반 파일명)
                if (!file_exists($optionTempPath)) {
                    mkdir($optionTempPath);
                }
                $fileExt  = pathinfo($uploadNames[$vKey], PATHINFO_EXTENSION);
                $fileName = $session . '_' . $vKey . '.' . $fileExt;
                if (move_uploaded_file($optionFiles['tmp_name']['goodsImage'][0][$vKey], $optionTempPath . DIRECTORY_SEPARATOR . $fileName)) {
                    $goodsImage = $fileName;
                }
            }

            // 신규 업로드/URL 이 없으면 기존상품에서 불러온 이미지 사용 (이미 option_temp/에 복사되어 있음)
            if ($goodsImage === null && !empty($existingImages[$vKey])) {
                $goodsImage = $existingImages[$vKey];
            }

            if ($goodsImage === null) {
                continue;
            }

            $iconTempRows[] = [
                'session'     => $session,
                'optionNo'    => $vKey,
                'optionValue' => (string) ($optionValues[$vKey] ?? ''),
                'goodsImage'  => $goodsImage,
                'isUpdated'   => 'y',
            ];
        }

        if (!empty($iconTempRows)) {
            $this->dao->insertOptionIconTempRows($iconTempRows);
            $postValue['optionTempSession'] = $session;
        }

        return $postValue;
    }
}
