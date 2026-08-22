<?php
/**
 * This is commercial software, only users who have purchased a valid license
 * and accept to the terms of the License Agreement can install and use this
 * program.
 *
 * Do not edit or add to this file if you wish to upgrade Godomall to newer
 * versions in the future.
 *
 * @copyright ⓒ 2024, NHN COMMERCE Corp.
 */

namespace Bundle\Component\Excel;

class ExcelDeliveryConvert extends \Component\Excel\ExcelDataConvert
{
    /**
     * 지역별 추가배송비 엑셀파일등록
     *
     * @param array $data
     * @return array
     */
    public function setDeliveryExcelCodeUp(array $data): array
    {
        // 엑셀데이터 추출
        $request = \App::getInstance('request');
        $excel = $request->files()->get('excel');

        if ($this->hasError() || !$this->read()) {
            throw new Exception(__('엑셀 파일이 존재하지 않습니다. 다운로드 받으신 엑셀파일을 이용해서 "Excel 통합 문서(xlsx)" 또는 "Excel 97-2003 통합문서" 로 저장이 된 엑셀 파일만 가능합니다.'));
        }

        $this->excelReader->setReadDataOnly(true);
        $this->sheet = $this->excelReader->setReadEmptyCells(false)->load($excel['tmp_name'])->getActiveSheet();
        $arrSheet = $this->sheet->toArray();
        unset($arrSheet[0]);

        $arrExcelData = [];
        foreach($arrSheet as $sheetKey => $sheetVal){
            $arrData = array_filter($sheetVal);
            if (!empty($arrData)) {
                $arrExcelData[] = $arrData;
            }
        }

        // 1번째 줄은 설명, 2번째 줄부터 데이터
        if (sizeof($arrExcelData) < 1) {
            $failMsg = __('엑셀 파일을 확인해 주세요. 엑셀 데이터가 존재하지 않습니다. 데이터는 2번째 줄부터 작성을 하셔야 합니다.');
            throw new Exception($failMsg);
        }

        // 1,000건 넘길 시 예외 처리
        if (sizeof($arrExcelData) > 1000) {
            throw new Exception(__('지역별 배송비 입력데이터는 1,000건 이상 처리할 수 없습니다.'));
        }

        // 엑셀 데이터를 추출해서 데이터 설정 (상품주문번호가 없는 경우 제외)
        $add = [];
        for ($i = 0; $i < sizeof($arrExcelData); $i++) {
            if (isset($arrExcelData[$i][1])) {
                $add['scmNo'][$i] = $data['scmNo'];
                $add['basicKey'][$i] = $data['sno'];
                $add['addAreaCode'][$i] = 0;
                $add['addArea'][$i] = trim($arrExcelData[$i][0]);
                $add['addPrice'][$i] = trim($arrExcelData[$i][1]);
                $add['addRegDt'][$i] = '';
            }
        }

        return $add;
    }
}
