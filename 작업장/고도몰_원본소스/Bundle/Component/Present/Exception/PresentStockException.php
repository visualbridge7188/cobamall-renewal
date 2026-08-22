<?php
/*
 * Copyright (C) 2025 NHN COMMERCE. - All Rights Reserved
 *
 * Unauthorized copying or redistribution of this file in source and binary forms via any medium is strictly prohibited.
 */

namespace Bundle\Component\Present\Exception;

use Exception;

class PresentStockException extends Exception
{
    private const DEFAULT_MESSAGE = '재고 부족으로 구매가 불가능합니다.';

    public function __construct(string $message = '', int $code = 0, \Throwable $previous = null)
    {
        if (empty($message)) {
            $message = __(self::DEFAULT_MESSAGE);
        }
        parent::__construct($message, $code, $previous);
    }
}
