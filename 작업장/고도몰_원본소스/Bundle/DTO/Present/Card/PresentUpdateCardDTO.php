<?php
/*
 * Copyright (C) 2025 NHN COMMERCE. - All Rights Reserved
 *
 * Unauthorized copying or redistribution of this file in source and binary forms via any medium is strictly prohibited.
 */

namespace Bundle\DTO\Present\Card;

use Origin\DTO\AbstractDTO;

class PresentUpdateCardDTO extends AbstractDTO
{
    public function __construct(
        protected readonly int $sno,
        protected readonly string $message,
        protected readonly string $displayFl,
        protected ?string $modDt
    )
    {
    }

    public function getSno(): int
    {
        return $this->sno;
    }

    public function getMessage(): string
    {
        return $this->message;
    }

    public function getDisplayFl(): string
    {
        return $this->displayFl;
    }

    public function getModDt(): ?string
    {
        return $this->modDt;
    }

    public function setModDt(string $modDt): void
    {
        $this->modDt = $modDt;
    }
}
