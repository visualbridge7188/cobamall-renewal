<?php
/*
 * Copyright (C) 2025 NHN COMMERCE. - All Rights Reserved
 *
 * Unauthorized copying or redistribution of this file in source and binary forms via any medium
 * is strictly prohibited.
 */

namespace Bundle\DTO\RegularDelivery\RegularOrder;

use Bundle\Component\Member\Manager;
use Origin\DTO\AbstractDTO;

class RegularOrderSearchCondition extends AbstractDTO
{
    public $applyNoList;
    public $scmFl;
    public $scmNo;
    public $key;
    public $keyword;
    public $treatDate;
    public $applyStatus;
    public $deliveryCycleType;
    public $deliveryCycleMonth;
    public $deliveryCycleWeek;
    public $deliveryCycleDayWeek;
    public $page;
    public $pageNum;
    public $sort;
    public $isProvider;

    public function __construct(array $data)
    {
        $this->applyNoList = $data['applyNo'] ?? [];
        $this->scmFl = $data['scmFl'] ?? null;
        $this->scmNo = $data['scmNo'] ?? \Session::get('manager.scmNo');
        $this->key = $data['key'] ?? null;
        $this->keyword = $data['keyword'] ?? null;
        $this->treatDate = $data['treatDate'] ?? null;
        $this->applyStatus = $data['applyStatus'] ?? null;
        $this->deliveryCycleType = $data['deliveryCycleType'] ?? null;
        $this->deliveryCycleMonth = $data['deliveryCycleMonth'] ?? [];
        $this->deliveryCycleWeek = $data['deliveryCycleWeek'] ?? [];
        $this->deliveryCycleDayWeek = $data['deliveryCycleDayWeek'] ?? [];
        $this->page = $data['page'] ?? 1; // default page 1
        $this->pageNum = $data['pageNum'] ?? 10; // default size 10
        $this->sort = $data['sort'] ?? null;
        $this->isProvider = Manager::isProvider();
    }
}
