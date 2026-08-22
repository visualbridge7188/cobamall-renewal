<?php

/*
 * Copyright (C) 2026 NHN COMMERCE. - All Rights Reserved
 *
 * Unauthorized copying or redistribution of this file in source and binary forms via any medium
 * is strictly prohibited.
 */

namespace Bundle\Component\DesignEditor;

/**
 * 디자인 에디터 라우터
 *
 * 컨트롤러가 존재하지 않는 URL 요청 시
 * 디자인 에디터 에셋 테이블에서 pageUrl 매칭을 확인하여
 * 해당 스킨 페이지로 라우팅
 */
class DesignEditorRouter
{
    /**
     * @param DesignEditorAssetRepositoryInterface $repository
     * @param DesignEditorSkinResolver $skinResolver
     */
    public function __construct(
        protected DesignEditorAssetRepositoryInterface $repository,
        protected DesignEditorSkinResolver $skinResolver
    ) {
    }

    /**
     * 요청 URI로 에셋 매칭
     *
     * @param string $requestUri 요청 URI (예: /custom/my_page.php)
     * @return array|null 에셋 정보 또는 null (매칭 실패)
     */
    public function matchByRequestUri(string $requestUri): ?array
    {
        // 디자인 에디터 스킨 사용 여부 확인
        if (!$this->skinResolver->isDesignEditorSkinEnabled()) {
            return null;
        }

        // 현재 사용중인 스킨 번호 조회
        $skinSno = $this->skinResolver->getCurrentSkinSno();
        if (is_null($skinSno)) {
            return null;
        }

        // URI 정규화
        $pageUrl = $this->normalizeUri($requestUri);

        // 에셋 조회
        return $this->repository->findByPageUrl($skinSno, $pageUrl);
    }

    /**
     * URI 정규화
     *
     * @param string $uri
     * @return string 정규화된 URI
     */
    protected function normalizeUri(string $uri): string
    {
        // 선행 슬래시 제거
        $uri = ltrim($uri, '/');

        // 쿼리스트링 제거
        $uri = parse_url($uri, PHP_URL_PATH) ?? '';

        return $uri;
    }
}
