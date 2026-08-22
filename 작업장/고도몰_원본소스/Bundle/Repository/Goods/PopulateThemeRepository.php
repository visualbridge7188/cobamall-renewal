<?php
/*
 * Copyright (C) 2025 NHN COMMERCE. - All Rights Reserved
 *
 * Unauthorized copying or redistribution of this file in source and binary forms via any medium
 * is strictly prohibited.
 */

namespace Bundle\Repository\Goods;

use Origin\Model\Goods\PopulateTheme;

class PopulateThemeRepository
{
    /**
     * displayField 또는 mobileDisplayField에 'regularPrice'가 포함된 모든 PopulateTheme 데이터 반환
     *
     * @return \Illuminate\Database\Eloquent\Collection : 조건에 맞는 PopulateTheme 객체 리스트
     *
     * SELECT `sno`, `same`, `displayField`, `mobileDisplayField`
     * FROM `es_populateTheme`
     * WHERE `displayField` LIKE '%regularPrice%' OR `mobileDisplayField` LIKE '%regularPrice%';
     *
     * @return array
     */
    public function findPopulateThemeHaveRegularPrice(): array
    {
        return PopulateTheme::where(function ($query) {
            $query->where('displayField', 'LIKE', '%regularPrice%')
                ->orWhere('mobileDisplayField', 'LIKE', '%regularPrice%');
        })
            ->select('sno', 'same', 'displayField', 'mobileDisplayField')
            ->get()
            ->toArray();
    }

    /**
     * sno를 기준으로 displayField와 mobileDisplayField 값 업데이트
     *
     * @param $sno : 업데이트할 대상의 sno
     * @param $displayField : 변경된 displayField 값
     * @param $mobileDisplayField : 변경된 mobileDisplayField 값
     * @return void
     *
     * UPDATE `es_populateTheme`
     * SET `displayField` = ?, `mobileDisplayField` = ?
     * WHERE `sno` = ?;
     */
    public function updateDisplayFieldAndMobileDisplayFieldBySno($sno, $displayField, $mobileDisplayField)
    {
        PopulateTheme::where('sno', $sno)
            ->update([
                'displayField' => $displayField,
                'mobileDisplayField' => $mobileDisplayField
            ]);
    }
}
