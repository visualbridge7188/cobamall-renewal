<?php
/*
 * Copyright (C) 2025 NHN COMMERCE. - All Rights Reserved
 *
 * Unauthorized copying or redistribution of this file in source and binary forms via any medium
 * is strictly prohibited.
 */

namespace Bundle\Repository\Goods;

use Origin\Model\Goods\DisplayThemeConfig;

class DisplayThemeConfigRepository
{

    /**
     * displayField에 'regularPrice'가 포함된 모든 데이터 반환
     *
     * @return \Illuminate\Database\Eloquent\Collection : 조건에 맞는 DisplayThemeConfig 객체 리스트
     *
     * SELECT `themeCd`, `displayField`
     * FROM `es_displayThemeConfig`
     * WHERE `displayField` LIKE '%regularPrice%';
     *
     * @return array
     */
    public function findDisplayThemeConfigsHaveRegularPrice(): array
    {
        return DisplayThemeConfig::where('displayField', 'LIKE', '%regularPrice%')
            ->select('themeCd', 'displayField')
            ->get()
            ->toArray();
    }

    /**
     * themeCd를 기준으로 변경된 displayField값 업데이트
     *
     * @param $themeCd : pk인 themeCd
     * @param $displayField : 변경된 displayField 값
     * @return void
     *
     * UPDATE `es_displayThemeConfig`
     * SET `displayField` = ?
     * WHERE `themeCd` = ?;
     */
    public function updateDisplayFieldByThemeCd($themeCd, $displayField)
    {
        DisplayThemeConfig::where('themeCd', $themeCd)
            ->update(['displayField' => $displayField]);
    }
}
