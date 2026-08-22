<?php

/*
 * Copyright (C) 2026 NHN COMMERCE. - All Rights Reserved
 *
 * Unauthorized copying or redistribution of this file in source and binary forms via any medium
 * is strictly prohibited.
 */

namespace Bundle\Component\DesignEditor;

/**
 * 디자인 에디터 스킨 리졸버 Stub (테스트용)
 *
 * 단위 테스트를 위한 설정 가능한 구현체
 */
class DesignEditorSkinResolverStub extends DesignEditorSkinResolver
{
    /**
     * @param bool $enabled 디자인 에디터 스킨 활성화 여부
     * @param int|null $skinSno 스킨 번호
     * @param string|null $skinName 스킨명
     */
    public function __construct(
        protected bool $enabled = true,
        protected ?int $skinSno = 1,
        protected ?string $skinName = 'test_skin'
    ) {
    }

    /**
     * 활성화 여부 설정
     *
     * @param bool $enabled
     */
    public function setEnabled(bool $enabled): void
    {
        $this->enabled = $enabled;
    }

    /**
     * 스킨 번호 설정
     *
     * @param int|null $skinSno
     */
    public function setSkinSno(?int $skinSno): void
    {
        $this->skinSno = $skinSno;
    }

    /**
     * 스킨명 설정
     *
     * @param string|null $skinName
     */
    public function setSkinName(?string $skinName): void
    {
        $this->skinName = $skinName;
    }

    /**
     * {@inheritdoc}
     */
    protected function doIsDesignEditorSkinEnabled(): bool
    {
        return $this->enabled;
    }

    /**
     * {@inheritdoc}
     */
    protected function doGetCurrentSkinSno(): ?int
    {
        return $this->skinSno;
    }

    /**
     * {@inheritdoc}
     */
    protected function doGetCurrentSkinName(): ?string
    {
        return $this->skinName;
    }
}
