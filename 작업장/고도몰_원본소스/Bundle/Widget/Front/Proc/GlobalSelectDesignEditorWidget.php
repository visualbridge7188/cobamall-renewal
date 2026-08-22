<?php
/*
 * Copyright (C) 2026 NHN COMMERCE. - All Rights Reserved
 *
 * Unauthorized copying or redistribution of this file in source and binary forms via any medium
 * is strictly prohibited.
 */

namespace Bundle\Widget\Front\Proc;

/**
 * 글로벌 몰 선택 위젯 (디자인 에디터 전용)
 *
 * 다국가 몰 운영 시 사용자가 다른 몰로 이동할 수 있는 네비게이션을 제공
 * flags, dropdown-text, dropdown 3가지 레이아웃을 지원
 */
class GlobalSelectDesignEditorWidget extends \Widget\Front\Widget
{
    /**
     * 몰별 기본 아이콘 파일명 매핑
     * 1: 한국, 2: 미국, 3: 중국, 4: 일본
     */
    private const DEFAULT_ICONS = [
        1 => 'ico_kr.png',
        2 => 'ico_us.png',
        3 => 'ico_cn.png',
        4 => 'ico_jp.png',
    ];

    public function index()
    {
        // 몰 목록 조회
        $mallList = $this->getMallList();
        $mallCnt = gd_count($mallList);

        // 몰이 1개 이하면 네비게이션 불필요 (위젯 숨김 처리)
        if ($mallCnt <= 1) {
            $this->setData('mallCnt', $mallCnt);
            return;
        }

        // 도메인 설정 적용
        $mall = \App::load('Component\\Mall\\Mall');
        $mallList = $mall->globalShopDomainSetting($mallList);

        // 몰별 아이콘과 이름 정보 구성
        [$mallIcon, $mallNm] = $this->buildMallMetadata($mallList);

        // 템플릿에 전달할 데이터 설정
        $this->setData('mallList', $mallList);
        $this->setData('mallCnt', $mallCnt);
        $this->setData('mallIcon', $mallIcon);
        $this->setData('mallNm', $mallNm);
        $this->setData('nowMall', $this->getCurrentMall($mallList));
        $this->setData('uriCommon', \UserFilePath::data('commonimg')->www());
    }

    /**
     * 사용 가능한 몰 목록 조회
     *
     * @return array
     */
    private function getMallList(): array
    {
        $globals = \App::getInstance('globals');
        $mallList = $globals->get('gGlobal.useMallList');

        // 캐시에 없으면 DB에서 조회
        if (gd_count($mallList) < 1) {
            $mall = \App::load('Component\\Mall\\Mall');
            $mallList = $mall->getListByUseMall();
        }

        return $mallList ?: [];
    }

    /**
     * 몰별 메타데이터(아이콘, 이름) 구성
     *
     * @param array $mallList 몰 목록
     * @return array [아이콘 배열, 이름 배열]
     */
    private function buildMallMetadata(array $mallList): array
    {
        $mallIconPolicy = gd_policy('design.mallIconType');
        $mallIcon = [];
        $mallNm = [];

        foreach (array_keys($mallList) as $sno) {
            // 아이콘: 정책 설정 -> 기본값 -> 디폴트 순으로 폴백
            $mallIcon[$sno] = $mallIconPolicy['mallIcon'][$sno]
                ?? self::DEFAULT_ICONS[$sno]
                ?? 'ico_noimg_16.gif';

            // 몰 이름: 기본 정보에서 조회
            $basicInfo = gd_policy('basic.info', $sno);
            $mallNm[$sno] = $basicInfo['mallNm'] ?? '';
        }

        return [$mallIcon, $mallNm];
    }

    /**
     * 현재 선택된 몰 정보를 반환
     *
     * @param array $mallList 몰 목록
     * @return array|null 현재 몰 정보 (몰이 없으면 null)
     */
    private function getCurrentMall(array $mallList): ?array
    {
        if (empty($mallList)) {
            return null;
        }

        // 세션에서 현재 몰 번호 조회 (기본값: 1)
        $mallSno = gd_isset(\Component\Mall\Mall::getSession('sno'), 1);

        // 해당 몰이 목록에 있으면 반환, 없으면 첫 번째 몰 반환
        return $mallList[$mallSno] ?? reset($mallList);
    }
}
