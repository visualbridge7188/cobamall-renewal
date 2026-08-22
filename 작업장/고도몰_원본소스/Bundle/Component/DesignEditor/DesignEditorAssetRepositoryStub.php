<?php

/*
 * Copyright (C) 2026 NHN COMMERCE. - All Rights Reserved
 *
 * Unauthorized copying or redistribution of this file in source and binary forms via any medium
 * is strictly prohibited.
 */

namespace Bundle\Component\DesignEditor;

/**
 * 디자인 에디터 에셋 저장소 Stub (테스트용)
 *
 * 단위 테스트를 위한 인메모리 구현체
 * 실제 DB 스키마(es_designEditorAsset, es_designEditorFolder)와 동일한 필드 구조 사용
 *
 * @author NHN godo
 */
class DesignEditorAssetRepositoryStub implements DesignEditorAssetRepositoryInterface
{
    /** @var array 임시 에셋 데이터 */
    protected array $assets = [];

    public function __construct()
    {
        $this->initializeStubData();
    }

    /**
     * 임시 에셋 데이터 초기화
     */
    protected function initializeStubData(): void
    {
        $this->assets = [
            [
                'assetSno' => 1,
                'folderSno' => 1,
                'skinSno' => 1,  // JOIN 결과로 포함
                'folderSlug' => 'custom',  // JOIN 결과로 포함
                'assetCode' => 'custom--test_page',
                'assetTitle' => '테스트 페이지',
                'assetType' => 'html',
                'assetIcon' => null,
                'assetContents' => json_encode([
                    'version' => '1.0.0',
                    'page' => [
                        'id' => 'custom-test-page',
                        'title' => '테스트 페이지',
                        'seo' => [
                            'title' => '테스트 페이지 SEO 타이틀',
                            'description' => '테스트 페이지 설명입니다.',
                            'keywords' => '테스트, 페이지',
                        ],
                        'sections' => [],
                    ],
                ]),
                'assetPath' => 'custom/test_page.html',
                'pageUrl' => 'custom/test_page.php',
                'deleteFl' => 'n',
                'regDt' => date('Y-m-d H:i:s'),
                'modDt' => null,
            ],
            [
                'assetSno' => 2,
                'folderSno' => 1,
                'skinSno' => 1,
                'folderSlug' => 'custom',
                'assetCode' => 'custom--event_page',
                'assetTitle' => '이벤트 페이지',
                'assetType' => 'html',
                'assetIcon' => null,
                'assetContents' => null,
                'assetPath' => 'custom/event_page.html',
                'pageUrl' => 'custom/event_page.php',
                'deleteFl' => 'n',
                'regDt' => date('Y-m-d H:i:s'),
                'modDt' => null,
            ],
        ];
    }

    /**
     * 커스텀 에셋 추가 (테스트용)
     *
     * @param array $asset
     */
    public function addAsset(array $asset): void
    {
        $this->assets[] = $asset;
    }

    /**
     * {@inheritdoc}
     */
    public function findByPageUrl(int $skinSno, string $pageUrl): ?array
    {
        foreach ($this->assets as $asset) {
            if ($asset['skinSno'] === $skinSno
                && $asset['pageUrl'] === $pageUrl
                && $asset['deleteFl'] === 'n') {
                return $asset;
            }
        }
        return null;
    }

    /**
     * {@inheritdoc}
     */
    public function findByAssetCode(int $skinSno, string $assetCode): ?array
    {
        foreach ($this->assets as $asset) {
            if ($asset['skinSno'] === $skinSno
                && $asset['assetCode'] === $assetCode
                && $asset['deleteFl'] === 'n') {
                return $asset;
            }
        }
        return null;
    }

    /**
     * {@inheritdoc}
     */
    public function findAllBySkin(int $skinSno): array
    {
        return array_filter($this->assets, function ($asset) use ($skinSno) {
            return $asset['skinSno'] === $skinSno && $asset['deleteFl'] === 'n';
        });
    }
}
