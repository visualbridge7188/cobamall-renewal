<?php
/*
 * Copyright (C) 2026 NHN COMMERCE. - All Rights Reserved
 *
 * Unauthorized copying or redistribution of this file in source and binary forms via any medium
 * is strictly prohibited.
 */

namespace Bundle\Component\Excel;

/**
 * 주문 엑셀 다운로드 정렬 처리
 */
class ExcelOrderSort
{
    /**
     * 주문 상태 예외 정렬 + 2,3차 정렬 처리
     *
     * @param string $sort 정렬 기준
     * @return string 변환된 ORDER BY 절
     */
    public function resolve(string $sort): string
    {
        // 주문 상태 예외 정렬 처리
        if ($sort == 'og.orderStatus asc') {
            $sort = 'case LEFT(og.orderStatus, 1) when \'o\' then \'01\' when \'p\' then \'02\' when \'g\' then \'03\' when \'d\' then \'04\' when \'s\' then \'05\' when \'e\' then \'06\' when \'b\' then \'07\' when \'r\' then \'08\' when \'c\' then \'09\' when \'f\' then \'10\' else \'11\' end';
        } elseif ($sort == 'og.orderStatus desc') {
            $sort = 'case LEFT(og.orderStatus, 1) when \'f\' then \'01\' when \'c\' then \'02\' when \'r\' then \'03\' when \'b\' then \'04\' when \'e\' then \'05\' when \'s\' then \'06\' when \'d\' then \'07\' when \'g\' then \'08\' when \'p\' then \'09\' when \'o\' then \'10\' else \'11\' end';
        }

        // 선택된 정렬 + 2,3차 정렬 처리
        if (str_contains($sort, 'packetCode') && str_contains($sort, 'desc')) {
            // 묶음배송 내림차순 정렬인 경우 주문배송테이블번호 기준 내림차순, 주문코드 기준 내림차순으로 정렬
            return $sort . ', og.orderDeliverySno desc, og.orderCd desc';
        }

        // 그 외 주문배송테이블번호 기준 오름차순, 주문코드 기준 오름차순으로 고정
        return $sort . ', og.orderDeliverySno asc, og.orderCd asc';
    }
}
