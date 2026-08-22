<?php
/*
 * Copyright (C) 2025 NHN COMMERCE. - All Rights Reserved
 *
 * Unauthorized copying or redistribution of this file in source and binary forms via any medium
 * is strictly prohibited.
 */

namespace Bundle\Component\RegularDelivery\RegularOrder;

use Repository\RegularDelivery\RegularOrder\RegularOrderLogRepository;
use Repository\RegularDelivery\RegularOrder\RegularOrderStatusLogRepository;
use DTO\RegularDelivery\RegularOrder\RegularOrderLogDTO;

class RegularOrderLog
{
    /**
     * @var RegularOrderLogRepository
     */
    private $regularOrderLogRepository;
    /**
     * @var RegularOrderStatusLogRepository
     */
    private $regularOrderStatusLogRepository;

    public function __construct(
        RegularOrderLogRepository $regularOrderLogRepository,
        RegularOrderStatusLogRepository $regularOrderStatusLogRepository
    )
    {
        $this->regularOrderLogRepository = $regularOrderLogRepository;
        $this->regularOrderStatusLogRepository = $regularOrderStatusLogRepository;
    }

    /**
     * 정기결제 로그 저장
     *
     * @param RegularOrderLogDTO $logDto
     * @return void
     */
    public function insertRegularOrderLog(RegularOrderLogDTO $logDto)
    {
        $this->regularOrderLogRepository->insertRegularOrderLog($logDto);
    }

    /**
     * 정기결제 상태변경 로그 bulk 저장
     *
     * @param array $bulkInsertDTO
     * @return void
     */
    public function bulkInsertRegularOrderStatusLog(array $bulkInsertDTO)
    {
        $this->regularOrderStatusLogRepository->bulkInsertRegularOrderStatusLog($bulkInsertDTO);
    }
}
