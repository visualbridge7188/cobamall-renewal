<?php

/*
 * Copyright (C) 2026 NHN COMMERCE. - All Rights Reserved
 *
 * Unauthorized copying or redistribution of this file in source and binary forms via any medium
 * is strictly prohibited.
 */

namespace Bundle\Component\DesignEditor;

use Origin\Model\Design\DesignEditorSkin;

/**
 * 디자인 에디터 스킨 리졸버
 *
 * 현재 사용중인 디자인 에디터 스킨 정보를 조회
 */
class DesignEditorSkinResolver
{
    /** @var array|null 캐시된 스킨 설정 */
    protected ?array $skinConfig = null;

    /** @var bool 디자인 에디터 스킨 조회 완료 여부 */
    protected bool $designEditorSkinResolved = false;

    /** @var array|null 캐시된 디자인 에디터 스킨 정보 */
    protected ?array $designEditorSkin = null;

    /**
     * 디자인 에디터 스킨 사용 여부 확인
     *
     * 현재 활성 스킨이 디자인 에디터 스킨인지 확인
     *
     * @return bool
     */
    public function isDesignEditorSkinEnabled(): bool
    {
        return $this->doIsDesignEditorSkinEnabled();
    }

    /**
     * 현재 사용중인 디자인 에디터 스킨 번호 조회
     *
     * @return int|null 스킨 일련번호 또는 null
     */
    public function getCurrentSkinSno(): ?int
    {
        return $this->doGetCurrentSkinSno();
    }

    /**
     * 현재 사용중인 스킨명 조회
     *
     * @return string|null 스킨명 또는 null
     */
    public function getCurrentSkinName(): ?string
    {
        return $this->doGetCurrentSkinName();
    }

    /**
     * 디자인 에디터 스킨 사용 여부 확인 (구현)
     *
     * @return bool
     */
    protected function doIsDesignEditorSkinEnabled(): bool
    {
        $skin = $this->getDesignEditorSkin();
        return !is_null($skin);
    }

    /**
     * 현재 스킨 번호 조회 (구현)
     *
     * @return int|null
     */
    protected function doGetCurrentSkinSno(): ?int
    {
        $skin = $this->getDesignEditorSkin();
        return $skin['skinSno'] ?? null;
    }

    /**
     * 현재 스킨명 조회 (구현)
     *
     * @return string|null
     */
    protected function doGetCurrentSkinName(): ?string
    {
        $skin = $this->getDesignEditorSkin();
        return $skin['skinName'] ?? null;
    }

    /**
     * 현재 활성 스킨의 디자인 에디터 스킨 정보 조회
     *
     * @return array|null
     */
    protected function getDesignEditorSkin(): ?array
    {
        if ($this->designEditorSkinResolved) {
            return $this->designEditorSkin;
        }

        $this->designEditorSkinResolved = true;

        $skinConfig = $this->getSkinConfig();
        if (empty($skinConfig)) {
            return null;
        }

        // 디바이스에 따른 스킨 코드 결정
        $skinCode = $this->getCurrentSkinCode($skinConfig);
        if (empty($skinCode)) {
            return null;
        }

        // 디바이스 타입 결정
        $skinDevice = \Request::isMobile() ? 'mobile' : 'front';

        // es_designEditorSkin 테이블에서 해당 스킨 조회
        $skin = DesignEditorSkin::query()
            ->where('skinCode', $skinCode)
            ->where('skinDevice', $skinDevice)
            ->first();

        if ($skin) {
            $this->designEditorSkin = $skin->toArray();
        }

        return $this->designEditorSkin;
    }

    /**
     * 현재 활성 스킨 코드 조회
     *
     * @param array $skinConfig
     * @return string|null
     */
    protected function getCurrentSkinCode(array $skinConfig): ?string
    {
        // 모바일 여부에 따라 Live 스킨 코드 반환
        if (\Request::isMobile()) {
            return $skinConfig['mobileLive'] ?? null;
        }

        return $skinConfig['frontLive'] ?? null;
    }

    /**
     * 스킨 설정 조회
     *
     * @return array
     */
    protected function getSkinConfig(): array
    {
        if (is_null($this->skinConfig)) {
            $this->skinConfig = gd_policy('design.skin') ?: [];
        }

        return $this->skinConfig;
    }
}
