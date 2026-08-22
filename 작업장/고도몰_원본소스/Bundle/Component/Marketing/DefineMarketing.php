<?php


namespace Bundle\Component\Marketing;


/**
 * Marketing 관련 define 정보 담은 클래스
 *
 * @package Bundle\Component\Marketing
 * @author  yido <sf2000@godo.co.kr>
 */
class DefineMarketing
{
    public $naverGrade = [];
    public $naverGradeMaxCount = [];
    public $naverGradeSafetyCount = [];

    public function __construct()
    {
        // 네이버쇼핑 EP 최대 상품수 (2026-06-02 시행 — 판매자 등급 폐지 → 수수료 기반 3단계 통합)
        $this->naverGrade = [
            '1' => __('10,000개'),
            '2' => __('50,000개'),
            '3' => __('100,000개'),
        ];

        // 등급에 따른 생성 상품 최대수
        $this->naverGradeMaxCount = [
            '1' => __('10000'),
            '2' => __('50000'),
            '3' => __('100000'),
        ];

        // 생성 상품 안전 수 (네이버 측 cut-off 회피용 자체 여백)
        $this->naverGradeSafetyCount = [
            '1' => __('100'),
            '2' => __('100'),
            '3' => __('1000'),
        ];

    }


    public function getNaverGrade()
    {
        return $this->naverGrade;
    }

    public function getNaverGradeMaxCount()
    {
        return $this->naverGradeMaxCount;
    }

    public function getNaverGradeSafetyCount()
    {
        return $this->naverGradeSafetyCount;
    }

}
