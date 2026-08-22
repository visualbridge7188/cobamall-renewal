<?php
/*
 * Copyright (C) 2025 NHN COMMERCE. - All Rights Reserved
 *
 * Unauthorized copying or redistribution of this file in source and binary forms via any medium is strictly prohibited.
 */

namespace Bundle\Component\Present\Log;

use Carbon\Carbon;
use Component\Member\Manager;
use DTO\Present\Log\PresentLogDTO;
use Framework\Http\Request;
use Framework\Http\Session\SessionManager;
use Framework\Log\Logger;
use Repository\Present\Log\PresentAdminLogRepository;

class PresentAdminLog
{

    public function __construct(
        protected readonly SessionManager $session,
        protected readonly Request $request,
        protected readonly Logger $logger,
        protected readonly PresentAdminLogRepository $presentAdminLogRepository,
    )
    {
    }

    /**
     * 로그 저장
     *
     * @param array $postValue
     * @return void
     */
    public function save(array $postValue): void
    {
        $ip = $this->request->getRemoteAddress();
        $managerSession = $this->session->get(Manager::SESSION_MANAGER_LOGIN);
        $managerSno = $managerSession['sno'];
        $applyType = $postValue['applyType'];

        $saveData = match ($applyType) {
            'all' => [],
            'category' => [
                'cateCds' => array_values($postValue['cateCd'] ?: []),
                'goodsNos' => $postValue['goodsNo']
            ],
            'applyGoods' => $postValue['goodsNo'],
        };

        $saveDatas = [
            'useFl' => $postValue['useFl'],
            'expirationPeriod' => $postValue['expirationPeriod'],
            'applyType' => $applyType,
            'managerSno' => $managerSno,
            'logData' => json_encode($saveData),
            'ip' => $ip,
            'regDt' => Carbon::now(),
        ];
        $this->presentAdminLogRepository->insert($saveDatas);
        $this->logger->channel('presentGoods')->info(__METHOD__ . ' SAVE SUCCESS.', ['saveData' => $saveDatas]);
    }
}
