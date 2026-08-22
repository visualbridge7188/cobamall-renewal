<?php

namespace Bundle\Controller\Mobile\Mypage;

use Component\RegularDelivery\RegularOrder\RegularOrderPayment;
use Component\Member\Util\MemberUtil;
use Component\Mypage\RegularDelivery;
use Exception;
use Request;

class RegularDeliveryController extends \Controller\Mobile\Controller
{
    public function index()
    {
        try {
            $regularDelivery = \App::getInstance(RegularDelivery::class);
            // 정기결제(배송) 기능 사용 여부 체크
            if (!$regularDelivery->isUseRegularDelivery()) {
                throw new AlertBackException(__('정기결제(배송) 기능이 사용중 상태가 아닙니다.'));
            }

            if (!MemberUtil::isLogin()) {
                $this->js("alert('" . __('로그인을 하셔야 이용하실 수 있습니다.') . "'); top.location.href = '../member/login.php';");
            }
            
            // 조회 기간 기준
            $searchDate = [
                '1'   => __('오늘'),
                '7'   => __('최근 7일'),
                '15'  => __('최근 15일'),
                '30'  => __('최근 1개월'),
                '90'  => __('최근 3개월'),
                '180' => __('최근 6개월'),
                '365' => __('최근 1년'),
            ];
            $this->setData('searchDate', $searchDate);


            // 검색 기간 파라미터
            $searchPeriod = Request::get()->get('searchPeriod');

            // 날짜 조건 가져오기
            $searchDates = $regularDelivery->getSearchDateCondition($searchPeriod);

            // 선택된 기간 설정
            $this->setData('selectDate', is_numeric($searchPeriod) ? $searchPeriod : 7);

            $pageNum = (int) \Request::get()->get('page', 1);
            $regularDeliveryList = $regularDelivery->getRegularDeliveryApplyList($searchDates, $pageNum);

            $this->setData('regularDeliveryList', $regularDeliveryList ?? []);

            // 정기결제 카드 목록 조회
            $memNo = \Session::get('member.memNo');
            $regularOrderPayment = \App::getInstance(RegularOrderPayment::class);
            $regularPaymentCardList = $regularOrderPayment->getCardListWithUsedStatus($memNo);

            $this->setData('regularPaymentCardList', $regularPaymentCardList);

            // pg 정보
            $pgConf = gd_policy('autoPg.pgName');
            $this->setData('pgName', $pgConf['pgName']);

            // 페이지 이름 설정
            $gPageName = __("정기배송 관리");
            $this->setData('gPageName', $gPageName);

            // 페이징 처리
            $this->setData('page', (int) $regularDeliveryList['page']);
            $this->setData('total', $regularDeliveryList['page']->recode['total'] ?? 0);

        } catch (Exception $e) {
            throw new AlertRedirectException($e->getMessage(), null, null, URI_HOME);
        }
    }
}
