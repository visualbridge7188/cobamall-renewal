<?php
/*
 * Copyright (C) 2025 NHN COMMERCE. - All Rights Reserved
 *
 * Unauthorized copying or redistribution of this file in source and binary forms via any medium is strictly prohibited.
 */

namespace Bundle\Component\Present\Admin;

use Component\Present\Card\PresentCard;
use Component\Present\Category\PresentCategory;
use Component\Present\Config\PresentConfig;
use Component\Present\Exception\PresentCardDisplayException;
use Component\Present\Exception\PresentCardMessageLengthException;
use Component\Present\Exception\PresentCardValidationException;
use Component\Present\Exception\PresentGoodsLimitOverException;
use Component\Present\Goods\PresentApplyGoods;
use Component\Present\Log\PresentAdminLog;
use Factory\Present\PresentApplyTypeFactory;
use Framework\Log\Logger;
use Illuminate\Database\Capsule\Manager;

class PresentAdmin
{
    public function __construct(
        protected readonly Logger $logger,
        protected readonly Manager $dbManager,
        protected readonly PresentConfig $presentConfig,
        protected readonly PresentCard $presentCard,
        protected readonly PresentCategory $presentCategory,
        protected readonly PresentApplyGoods $presentApplyGoods,
        protected readonly PresentAdminLog $presentAdminLog,
    )
    {
    }

    /**
     * 선물하기 관리자 저장
     *
     * @throws \Throwable
     */
    public function save(array $postValue, array $files): bool
    {
        $conn = $this->dbManager->getConnection();
        $conn->beginTransaction();

        try {
            // es_config 저장
            $this->presentConfig->savePresentConfig($postValue);

            // 선물하기 카드 저장
            $this->presentCard->save($postValue['card'], $files['card_image']);

            // 선택된 설정에 따른 저장
            if ($postValue['applyType'] === 'all') {
                $this->presentCategory->clearExclusiveConfig();
                $this->presentApplyGoods->clearExclusiveConfig();
            } else {
                $saveData = $postValue['applyType'] === 'category' ? $postValue : $postValue['goodsNo'];
                $presentComponent = PresentApplyTypeFactory::create($postValue['applyType']);
                $presentComponent->save($saveData);
                $presentComponent->clearExclusiveConfig();
            }

            // 로그 저장
            $this->presentAdminLog->save($postValue);

            $conn->commit();
        } catch (PresentGoodsLimitOverException
                | PresentCardDisplayException
                | PresentCardMessageLengthException
                | PresentCardValidationException $presentException) {
            $this->logger->channel('presentGoods')->warning(__METHOD__, [
                'exceptionClass' => get_class($presentException),
                'error' => $presentException->getMessage(),
                'file' => $presentException->getFile(),
                'line' => $presentException->getLine(),
            ]);
            $conn->rollBack();
            throw $presentException;
        } catch (\Throwable $throwable) {
            $this->logger->channel('presentGoods')->warning(__METHOD__, [
                'error' => $throwable->getMessage(),
                'trace' => $throwable->getTrace(),
            ]);
            $conn->rollBack();
            return false;
        }

        return true;
    }
}
