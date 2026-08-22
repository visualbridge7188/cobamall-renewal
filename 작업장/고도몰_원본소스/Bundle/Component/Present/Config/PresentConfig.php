<?php
/*
 * Copyright (C) 2025 NHN COMMERCE. - All Rights Reserved
 *
 * Unauthorized copying or redistribution of this file in source and binary forms via any medium is strictly prohibited.
 */

namespace Bundle\Component\Present\Config;

use Component\Policy\Policy;
use Framework\Log\Logger;

class PresentConfig
{
    const DEFAULT_USE = 'n';
    const DEFAULT_EXPIRATION_PERIOD = '7';
    const DEFAULT_APPLY_TYPE = 'all';

    protected string $mode = 'presentConfig';
    protected array $expirationPeriod = ['7', '10', '15', '30'];
    protected array $applyType = ['all', 'category', 'applyGoods'];
    protected array $configKeys = ['useFl', 'expirationPeriod', 'applyType'];

    public function __construct(
        private readonly Logger $logger,
        private readonly Policy $policy
    ) {
    }

    public function getMode(): string
    {
        return $this->mode;
    }

    public function getExpirationPeriod(): array
    {
        return $this->expirationPeriod;
    }

    public function getApplyType(): array
    {
        return $this->applyType;
    }

    public function getConfig(): array
    {
        return $this->policy->getValue('goods.present');
    }

    /**
     * POST를 가공하여 es_config 저장
     *
     * @param array $postValue POST 데이터
     * @return array
     */
    public function savePresentConfig(array $postValue): array
    {
        $configData = array_intersect_key($postValue, array_flip($this->configKeys));

        if ($this->hasConfigChanged($configData)) {
            $this->policy->setValue('goods.present', $configData);
            $this->logger->channel('presentGoods')->info(__METHOD__, ['data' => $configData]);
            return $configData;
        }

        return [];
    }

    /**
     * 설정값 변경 여부 확인
     *
     * @param array $newConfig
     * @return bool
     */
    protected function hasConfigChanged(array $newConfig): bool
    {
        $currentConfig = $this->getConfig();

        foreach ($this->configKeys as $key) {
            $currentValue = $currentConfig[$key] ?? null;
            $newValue = $newConfig[$key] ?? null;

            if ($currentValue !== $newValue) {
                return true;
            }
        }

        return false;
    }

    /**
     * 선물하기 기능 사용 여부
     *
     * @return bool
     */
    public function isUsePresent(): bool
    {
        return $this->policy->getValue('goods.present')['useFl'] === 'y';
    }

    /**
     * 현재 설정된 선물하기 적용 기준 반환
     *
     * @return string
     */
    public function getCurrentApplyType(): string
    {
        return $this->policy->getValue('goods.present')['applyType'] ?? self::DEFAULT_APPLY_TYPE;
    }
}
