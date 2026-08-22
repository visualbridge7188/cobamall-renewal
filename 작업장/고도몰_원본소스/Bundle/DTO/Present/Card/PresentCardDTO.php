<?php
/*
 * Copyright (C) 2025 NHN COMMERCE. - All Rights Reserved
 *
 * Unauthorized copying or redistribution of this file in source and binary forms via any medium is strictly prohibited.
 */

namespace Bundle\DTO\Present\Card;

use Origin\DTO\AbstractDTO;

class PresentCardDTO extends AbstractDTO
{
    public function __construct(
        protected readonly int $sno,
        protected readonly string $uploadType,
        protected readonly string $imageUrl,
        protected readonly string $message,
        protected readonly string $displayFl,
        protected readonly string $deleteFl,
        protected readonly ?string $deleteDt,
        protected readonly string $regDt,
        protected readonly ?string $modDt
    )
    {
    }

    public function getSno(): int
    {
        return $this->sno;
    }

    public function getUploadType(): string
    {
        return $this->uploadType;
    }

    public function getImageUrl(): string
    {
        return $this->imageUrl;
    }

    public function getMessage(): string
    {
        return $this->message;
    }

    public function getDisplayFl(): string
    {
        return $this->displayFl;
    }

    public function getDeleteFl(): string
    {
        return $this->deleteFl;
    }

    public function getDeleteDt(): ?string
    {
        return $this->deleteDt;
    }

    public function getRegDt(): string
    {
        return $this->regDt;
    }

    public function getModDt(): ?string
    {
        return $this->modDt;
    }
}
