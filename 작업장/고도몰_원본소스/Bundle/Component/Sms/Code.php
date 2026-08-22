<?php
/**
 * This is commercial software, only users who have purchased a valid license
 * and accept to the terms of the License Agreement can install and use this
 * program.
 *
 * Do not edit or add to this file if you wish to upgrade Godomall5 to newer
 * versions in the future.
 *
 * @copyright ⓒ 2016, NHN godo: Corp.
 * @link      http://www.godo.co.kr
 */

namespace Bundle\Component\Sms;

/**
 * SMS 자동 발송 코드를 모은 인터페이스 입니다.
 *
 * @package Bundle\Component\Sms
 */
interface Code
{
    const ORDER = 'ORDER';  //주문 접수
    const INCASH = 'INCASH';    //결제 완료
    const ACCOUNT = 'ACCOUNT';  //입금 요청
    const DELIVERY = 'DELIVERY';    //상품배송 안내
    const INVOICE_CODE = 'INVOICE_CODE';    //상품배송 안내(송장번호 포함)
    const DELIVERY_COMPLETED = 'DELIVERY_COMPLETED';    //배송 완료
    const CANCEL = 'CANCEL';    //주문 취소
    const REPAY = 'REPAY';  //환불 완료
    const REPAYPART = 'REPAYPART';  //카드 부분취소
    const SOLD_OUT = 'SOLD_OUT';    //상품 품절
    const EXCHANGE = 'EXCHANGE';    //고객 교환 신청
    const BACK = 'BACK';    //고객 반품 신청
    const REFUND = 'REFUND';    //고객 환불 신청
    const ADMIN_APPROVAL = 'ADMIN_APPROVAL';    //고객 교환/반품/환불신청 승인
    const ADMIN_REJECT = 'ADMIN_REJECT';    //고객 교환/반품/환불신청 거절

    /**
     * 정기결제(배송) 관련
    */
    const REGULAR_DELIVERY_APPLIED = 'REGULAR_DELIVERY_APPLIED';    // 정기배송 신청 안내
    const REGULAR_PAYMENT_SCHEDULED = 'REGULAR_PAYMENT_SCHEDULED';    // 정기배송 결제 예정 안내
    const REGULAR_PAYMENT_FAILED = 'REGULAR_PAYMENT_FAILED';    // 정기배송 결제 실패 안내
    const REGULAR_PAYMENT_COMPLETED = 'REGULAR_PAYMENT_COMPLETED';    // 정기배송 결제 완료
    const REGULAR_DELIVERY_NOTICE = 'REGULAR_DELIVERY_NOTICE';    // 정기결제 상품 배송 안내
    const REGULAR_DELIVERY_PAUSED = 'REGULAR_DELIVERY_PAUSED';    // 정기배송 일시정지
    const REGULAR_DELIVERY_RESUMED = 'REGULAR_DELIVERY_RESUMED';    // 정기배송 일시정지 해제
    const REGULAR_DELIVERY_SKIPPED = 'REGULAR_DELIVERY_SKIPPED';    // 정기배송 회차 건너뛰기
    const REGULAR_DELIVERY_INFO_CHANGED = 'REGULAR_DELIVERY_INFO_CHANGED';    // 정기배송 일정 변경
    const REGULAR_DELIVERY_CANCELED = 'REGULAR_DELIVERY_CANCELED';    // 정기배송 해지 안내

    const JOIN = 'JOIN'; //회원가입
    const APPROVAL = 'APPROVAL'; //가입승인
    const PASS_AUTH = 'PASS_AUTH';  //비밀번호 찾기 인증번호
    const BIRTH = 'BIRTH';  //생일축하
    const SLEEP_INFO = 'SLEEP_INFO';    //휴면회원 전환 사전안내
    const SLEEP_INFO_TODAY = 'SLEEP_INFO_TODAY';    //휴면회원 전환 안내
    const SLEEP_AUTH = 'SLEEP_AUTH';    //휴면회원 해제 인증번호
    const SLEEP_WAKE = 'SLEEP_WAKE';    //일반회원 전환 안내
    const AGREEMENT2YPERIOD = 'AGREEMENT2YPERIOD';  //수신동의여부 재확인
    const GROUP_CHANGE = 'GROUP_CHANGE';    //회원등급 변경안내
    const MILEAGE_PLUS = 'MILEAGE_PLUS';    //마일리지 지급안내
    const MILEAGE_MINUS = 'MILEAGE_MINUS';  //마일리지 차감안내
    const MILEAGE_EXPIRE = 'MILEAGE_EXPIRE';    //마일리지 소멸안내
    const DEPOSIT_PLUS = 'DEPOSIT_PLUS';    //예치금 지급안내
    const DEPOSIT_MINUS = 'DEPOSIT_MINUS';  //예치금 차감안내

    const COUPON_ORDER_FIRST = 'COUPON_ORDER_FIRST';    //첫 구매 축하 쿠폰
    const COUPON_ORDER = 'COUPON_ORDER';    //구매 감사 쿠폰
    const COUPON_BIRTH = 'COUPON_BIRTH';    //생일 축하 쿠폰
    const COUPON_JOIN = 'COUPON_JOIN';  //회원가입 축하 쿠폰
    const COUPON_LOGIN = 'COUPON_LOGIN';    //출석체크 감사 쿠폰
    const COUPON_MEMBER_MODIFY = 'COUPON_MEMBER_MODIFY';    //회원 정보 이벤트 쿠폰
    const COUPON_MANUAL = 'COUPON_MANUAL';  //수동쿠폰 발급 안내
    const COUPON_WARNING = 'COUPON_WARNING';    //쿠폰만료 안내
    const COUPON_WAKE = 'COUPON_WAKE';    //휴면회원 해제 감사 쿠폰

    // 게시판은 id가 구분값
    const GOODS_REVIEW = 'goodsreview';    // 상품후기
    const GOODS_QNA = 'goodsqa';    // 상품문의
    const ONE_TO_ONE_QNA = 'qa';    // 1:1문의
    const NOTICE = 'notice';    // 공지사항
    const EVENT = 'event';    // 이벤트
    const AD_BOARD = 'cooperation';    // 광고·제휴게시판

    const SETTLE_BANK = 'SETTLE_BANK';  //무통장 입금은행 정보 변경

    const PRESENT = 'PRESENT';  // 선물 발송
    const PRESENT_REMAIN_THREE = 'PRESENT_REMAIN_THREE';    // 선물 만료 3일전 알림
    const PRESENT_REMAIN_ONE = 'PRESENT_REMAIN_ONE';    // 선물 만료 1일전 알림
    const PRESENT_ACCEPT = 'PRESENT_ACCEPT';    // 선물 수락
    const PRESENT_REJECT = 'PRESENT_REJECT';    // 선물 거절
    const PRESENT_CHANGE_DEST = 'PRESENT_CHANGE_DEST';  // 선물 배송지 변경
    const PRESENT_INVOICE_CODE = 'PRESENT_INVOICE_CODE';  // 선물 송장번호 안내
    const PRESENT_REJECT_FAIL = 'PRESENT_REJECT_FAIL';    // 선물 거절 실패 (거절불가)
}
