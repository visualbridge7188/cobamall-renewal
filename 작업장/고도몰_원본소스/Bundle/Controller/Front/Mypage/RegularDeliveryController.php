<?php

namespace Bundle\Controller\Front\Mypage;

use App;
use Component\RegularDelivery\RegularOrder\RegularOrderPayment;
use Component\Member\Util\MemberUtil;
use Component\Mypage\RegularDelivery;
use Framework\Debug\Exception\AlertBackException;
use Framework\Utility\DateTimeUtils;

class RegularDeliveryController extends \Controller\Front\Controller
{
    public function index()
    {
        $regularDelivery = \App::getInstance(RegularDelivery::class);
        // 정기결제(배송) 기능 사용 여부 체크
        if (!$regularDelivery->isUseRegularDelivery()) {
            throw new AlertBackException(__('정기결제(배송) 기능이 사용중 상태가 아닙니다.'));
        }

        if (!MemberUtil::isLogin()) {
            $this->js("alert('" . __('로그인을 하셔야 이용하실 수 있습니다.') . "'); top.location.href = '../member/login.php';");
        }

        // datetimepicker 관련 css 및 script
        $locale = \Globals::get('gGlobal.locale');
        $this->addCss([
            'plugins/bootstrap-datetimepicker.min.css',
            'plugins/bootstrap-datetimepicker-standalone.css',
        ]);
        $this->addScript([
            'moment/moment.js',
            'moment/locale/' . $locale . '.js',
            'jquery/datetimepicker/bootstrap-datetimepicker.min.js',
        ]);

        // 검색 기간 조회
        $searchCondition = \Request::get()->get('searchDate');
        $searchDate = $regularDelivery->getSearchDateCondition($searchCondition);

        // 1년 이상 검색 제한 검증
        if (DateTimeUtils::intervalDay($searchDate[0], $searchDate[1]) > 365) {
            throw new AlertBackException(__('1년이상 기간으로 검색하실 수 없습니다.'));
        }

        // 정기배송 신청 목록 조회
        $pageNum = (int) \Request::get()->get('page', 1);
        $regularDeliveryList = $regularDelivery->getRegularDeliveryApplyList($searchDate, $pageNum);

        // pg 정보
        $pgConf = gd_policy('autoPg.pgName');
        $this->setData('pgName', $pgConf['pgName']);

        // 정기결제 카드 목록 조회
        $memNo = \Session::get('member.memNo');
        $regularPaymentView = \App::getInstance(RegularOrderPayment::class);
        $regularPaymentCardList = $regularPaymentView->getCardListWithUsedStatus($memNo);

        // 주문 리스트 정보
        $this->setData('regularDeliveryList', $regularDeliveryList ?? []);
        $this->setData('startDate', $searchDate[0] ?? '');
        $this->setData('endDate', $searchDate[1] ?? '');
        $this->setData('searchDate', $searchDate ?? []);

        // 결제카드 리스트 정보
        $this->setData('regularPaymentCardList', $regularPaymentCardList ?? []);

        // 페이징 처리
        $this->setData('page', $regularDeliveryList['page']);
        $this->setData('total', $regularDeliveryList['page']->recode['total'] ?? 0);
    }
}
