<?php

/*
 * Copyright (C) 2026 NHN COMMERCE. - All Rights Reserved
 *
 * Unauthorized copying or redistribution of this file in source and binary forms via any medium
 * is strictly prohibited.
 */

namespace Bundle\Component\DesignEditor;

/**
 * 디자인 에디터 에셋 저장소 인터페이스
 *
 * DB 기반 라우팅을 위한 에셋 조회 인터페이스
 * 실제 DB 구현체와 테스트용 Stub 구현체를 교체할 수 있도록 인터페이스로 분리
 *
 * @author NHN godo
 */
interface DesignEditorAssetRepositoryInterface
{
    /**
     * pageUrl로 에셋 조회
     *
     * @param int $skinSno 스킨 일련번호
     * @param string $pageUrl 페이지 URL (예: custom/my_page.php)
     * @return array|null 에셋 정보 배열 또는 null
     */
    public function findByPageUrl(int $skinSno, string $pageUrl): ?array;

    /**
     * 에셋 코드로 조회
     *
     * @param int $skinSno 스킨 일련번호
     * @param string $assetCode 에셋 코드 (예: custom--my_page)
     * @return array|null 에셋 정보 배열 또는 null
     */
    public function findByAssetCode(int $skinSno, string $assetCode): ?array;

    /**
     * 스킨의 모든 에셋 목록 조회
     *
     * @param int $skinSno 스킨 일련번호
     * @return array 에셋 목록
     */
    public function findAllBySkin(int $skinSno): array;
}
