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

use Origin\Enum\AutoSend\AutoSendOptionVisibilityPolicy;
use Origin\Enum\AutoSend\AutoSendRecipient;
use Origin\Enum\AutoSend\AutoSendRecipientPolicy;

/**
 * SMS 자동발송 코드별 기본 설정 값 초기화하는 클래스이며 기존 Sms::setSmsAutoCode 함수를 대체하는 클래스
 * 2017-02-17 yjwee 생성자에서 실제 Sms 발송을 하지 않더라도 BoardAdmin 클래스를 생성하여 불필요한 로직이 호출되던 부분을 수정함.
 *
 * @package Bundle\Component\Sms
 * @author  yjwee
 */
class SmsAutoCode
{
    const ORDER = 'order';
    const REGULAR_DELIVERY = 'regular';
    const MEMBER = 'member';
    const PROMOTION = 'promotion';
    const ADMIN = 'admin';
    const BOARD = 'board';
    const PRESENT = 'present';

    const ORDER_CATEGORY_NAME = '주문/배송';
    const REGULAR_DELIVERY_CATEGORY_NAME = '정기결제(배송)';
    const MEMBER_CATEGORY_NAME = '회원';
    const PROMOTION_CATEGORY_NAME = '쿠폰/프로모션';
    const BOARD_CATEGORY_NAME = '게시판';
    const PRESENT_CATEGORY_NAME = '선물하기';

    protected $codes = [];
    protected $boardAdmin;

    /**
     * 자동 sms 관련 설정 값 초기화
     *
     */
    protected function initialize()
    {
        if (empty($this->codes)) {
            $this->initOrder();
            $this->initRegularDelivery();
            $this->initMember();
            $this->initPromotion();
            $this->initBoard();
            $this->initAdmin();
            $this->initPresent();
        }
    }

    /**
     * 주문 관련 설정 값 초기화
     *
     */
    protected function initOrder()
    {
        //@formatter:off
        $this->codes[self::ORDER] = [
            [
                'code'       => Code::ORDER,
                'text'       => __('주문 접수'),
                ...self::buildPolicyFlags(Code::ORDER),
                'agreeCheck' => 'n',
                'desc'       => __('무통장 및 가상계좌 주문 건의 주문 접수 시 발송'),
                'category'   => self::ORDER_CATEGORY_NAME,
            ],
            [
                'code'       => Code::INCASH,
                'text'       => __('결제 완료'),
                ...self::buildPolicyFlags(Code::INCASH),
                'agreeCheck' => 'n',
                'desc'       => __('주문 상태가 \'결제 완료\'로 변경 시 발송 (무통장 및 가상계좌는 운영자가 직접 \'결제완료\'로 처리 시)'),
                'category'   => self::ORDER_CATEGORY_NAME,
            ],
            [
                'code'       => Code::ACCOUNT,
                'text'       => __('입금 요청'),
                ...self::buildPolicyFlags(Code::ACCOUNT),
                'agreeCheck' => 'n',
                'desc'       => __('무통장 및 가상계좌 주문 건의 주문 접수 시 입금 정보를 포함하여 발송'),
                'category'   => self::ORDER_CATEGORY_NAME,
            ],
            [
                'code'       => Code::DELIVERY,
                'text'       => __('상품배송 안내'),
                ...self::buildPolicyFlags(Code::DELIVERY),
                'agreeCheck' => 'n',
                'desc'       => __('주문 상태가 \'배송 중\'으로 변경 시 발송'),
                'category'   => self::ORDER_CATEGORY_NAME,
            ],
            [
                'code'       => Code::INVOICE_CODE,
                'text'       => __('상품배송 안내(송장번호 포함)'),
                ...self::buildPolicyFlags(Code::INVOICE_CODE),
                'agreeCheck' => 'n',
                'desc'       => __('주문 상태가 \'배송 중\'으로 변경 시, 송장번호를 포함하여 발송'),
                'category'   => self::ORDER_CATEGORY_NAME,
            ],
            [
                'code'       => Code::DELIVERY_COMPLETED,
                'text'       => __('배송 완료'),
                ...self::buildPolicyFlags(Code::DELIVERY_COMPLETED),
                'agreeCheck' => 'n',
                'desc'       => __('주문 상태가 \'배송 완료\'로 변경 시 발송'),
                'category'   => self::ORDER_CATEGORY_NAME,
            ],
            [
                'code'       => Code::CANCEL,
                'text'       => __('주문 취소'),
                ...self::buildPolicyFlags(Code::CANCEL),
                'agreeCheck' => 'n',
                'desc'       => __('주문 상태가 \'취소\'로 변경 시 발송'),
                'category'   => self::ORDER_CATEGORY_NAME,
            ],
            [
                'code'       => Code::REPAY,
                'text'       => __('환불 완료'),
                ...self::buildPolicyFlags(Code::REPAY),
                'agreeCheck' => 'n',
                'desc'       => __('주문 상태가 \'환불 완료\'로 변경 시 발송'),
                'category'   => self::ORDER_CATEGORY_NAME,
            ],
            [
                'code'       => Code::REPAYPART,
                'text'       => __('카드 부분취소'),
                ...self::buildExceptionPolicyFlags(Code::REPAYPART),
                'agreeCheck' => 'n',
                'desc'       => __('카드 부분취소 시'),
                'category'   => self::ORDER_CATEGORY_NAME,
            ],
            [
                'code'       => Code::SOLD_OUT,
                'text'       => __('상품 품절'),
                ...self::buildPolicyFlags(Code::SOLD_OUT),
                'agreeCheck' => 'n',
                'desc'       => __('고객 주문에 의한 상품 품절 발생 시 발송'),
                'category'   => self::ORDER_CATEGORY_NAME,
            ],
            [
                'code'       => Code::EXCHANGE,
                'text'       => __('고객 교환 신청'),
                ...self::buildPolicyFlags(Code::EXCHANGE),
                'agreeCheck' => 'n',
                'desc'       => __('고객이 마이페이지에서 교환 신청 시 발송'),
                'category'   => self::ORDER_CATEGORY_NAME,
            ],
            [
                'code'       => Code::BACK,
                'text'       => __('고객 반품 신청'),
                ...self::buildPolicyFlags(Code::BACK),
                'agreeCheck' => 'n',
                'desc'       => __('고객이 마이페이지에서 반품 신청 시 발송'),
                'category'   => self::ORDER_CATEGORY_NAME,
            ],
            [
                'code'       => Code::REFUND,
                'text'       => __('고객 환불 신청'),
                ...self::buildPolicyFlags(Code::REFUND),
                'agreeCheck' => 'n',
                'desc'       => __('고객이 마이페이지에서 환불 신청 시 발송'),
                'category'   => self::ORDER_CATEGORY_NAME,
            ],
            [
                'code'       => Code::ADMIN_APPROVAL,
                'text'       => __('고객 교환/반품/환불신청 승인'),
                ...self::buildPolicyFlags(Code::ADMIN_APPROVAL),
                'agreeCheck' => 'n',
                'desc'       => __('운영자가 신청 건을 승인 처리했을 때 발송'),
                'category'   => self::ORDER_CATEGORY_NAME,
            ],
            [
                'code'       => Code::ADMIN_REJECT,
                'text'       => __('고객 교환/반품/환불신청 거절'),
                ...self::buildPolicyFlags(Code::ADMIN_REJECT),
                'agreeCheck' => 'n',
                'desc'       => __('운영자가 신청 건을 거절 처리했을 때 발송'),
                'category'   => self::ORDER_CATEGORY_NAME,
            ],
        ];
        //@formatter:on
    }

    /**
     * 정기결제(배송) 관련 설정 값 초기화
     *
     */
    protected function initRegularDelivery()
    {
        //@formatter:off
        $this->codes[self::REGULAR_DELIVERY] = [
            [
                'code'       => Code::REGULAR_DELIVERY_APPLIED,
                'text'       => __('정기배송 신청 안내'),
                ...self::buildPolicyFlags(Code::REGULAR_DELIVERY_APPLIED),
                'agreeCheck' => 'n',
                'desc'       => __('정기배송 신청 완료 시 발송'),
                'category'   => self::REGULAR_DELIVERY_CATEGORY_NAME,
            ],
            [
                'code'       => Code::REGULAR_PAYMENT_SCHEDULED,
                'text'       => __('정기배송 결제 예정 안내'),
                ...self::buildPolicyFlags(Code::REGULAR_PAYMENT_SCHEDULED),
                'agreeCheck' => 'n',
                'desc'       => __('정기배송 예정일 기준 (주말 제외) 3일 전 발송'),
                'category'   => self::REGULAR_DELIVERY_CATEGORY_NAME,
            ],
            [
                'code'       => Code::REGULAR_PAYMENT_FAILED,
                'text'       => __('정기배송 결제 실패 안내'),
                ...self::buildPolicyFlags(Code::REGULAR_PAYMENT_FAILED),
                'agreeCheck' => 'n',
                'desc'       => __('정기배송 예정일 기준(주말 제외) 2일 전 결제 실패 또는 주문서 생성 실패 시 발송'),
                'category'   => self::REGULAR_DELIVERY_CATEGORY_NAME,
            ],
            [
                'code'       => Code::REGULAR_PAYMENT_COMPLETED,
                'text'       => __('정기배송 결제 완료'),
                ...self::buildPolicyFlags(Code::REGULAR_PAYMENT_COMPLETED),
                'agreeCheck' => 'n',
                'desc'       => __('정기배송 예정일 기준(주말 제외) 2일 전 결제가 정상 완료될 시 발송'),
                'category'   => self::REGULAR_DELIVERY_CATEGORY_NAME,
            ],
            [
                'code'       => Code::REGULAR_DELIVERY_NOTICE,
                'text'       => __('정기결제 상품 배송 안내'),
                ...self::buildPolicyFlags(Code::REGULAR_DELIVERY_NOTICE),
                'agreeCheck' => 'n',
                'desc'       => __('정기배송 주문 상태가 \'배송 중\'으로 변경 시 발송'),
                'category'   => self::REGULAR_DELIVERY_CATEGORY_NAME,
            ],
            [
                'code'       => Code::REGULAR_DELIVERY_PAUSED,
                'text'       => __('정기배송 일시정지'),
                ...self::buildPolicyFlags(Code::REGULAR_DELIVERY_PAUSED),
                'agreeCheck' => 'n',
                'desc'       => __('고객·운영자·시스템에 의해 정기배송이 일시정지될 시 발송'),
                'category'   => self::REGULAR_DELIVERY_CATEGORY_NAME,
            ],
            [
                'code'       => Code::REGULAR_DELIVERY_RESUMED,
                'text'       => __('정기배송 일시정지 해제'),
                ...self::buildPolicyFlags(Code::REGULAR_DELIVERY_RESUMED),
                'agreeCheck' => 'n',
                'desc'       => __('고객 또는 운영자가 정기배송 일시정지를 해제할 시 발송'),
                'category'   => self::REGULAR_DELIVERY_CATEGORY_NAME,
            ],
            [
                'code'       => Code::REGULAR_DELIVERY_SKIPPED,
                'text'       => __('정기배송 회차 건너뛰기'),
                ...self::buildPolicyFlags(Code::REGULAR_DELIVERY_SKIPPED),
                'agreeCheck' => 'n',
                'desc'       => __('고객 또는 운영자가 회차를 건너뛰기로 처리할 시 발송'),
                'category'   => self::REGULAR_DELIVERY_CATEGORY_NAME,
            ],
            [
                'code'       => Code::REGULAR_DELIVERY_INFO_CHANGED,
                'text'       => __('정기배송 일정 변경'),
                ...self::buildPolicyFlags(Code::REGULAR_DELIVERY_INFO_CHANGED),
                'agreeCheck' => 'n',
                'desc'       => __('고객 또는 운영자가 정기배송 주기를 변경할 시 발송'),
                'category'   => self::REGULAR_DELIVERY_CATEGORY_NAME,
            ],
            [
                'code'       => Code::REGULAR_DELIVERY_CANCELED,
                'text'       => __('정기배송 해지 안내'),
                ...self::buildPolicyFlags(Code::REGULAR_DELIVERY_CANCELED),
                'agreeCheck' => 'n',
                'desc'       => __('고객·운영자·시스템에 의해 정기배송이 해지되거나 회차가 종료될 시 발송'),
                'category'   => self::REGULAR_DELIVERY_CATEGORY_NAME,
            ]
        ];
    }

    /**
     * 회원 관련 설정 값 초기화
     *
     */
    protected function initMember()
    {
        //@formatter:off
        $this->codes[self::MEMBER] = [
            [
                'code'       => Code::JOIN,
                'text'       => __('회원가입'),
                ...self::buildPolicyFlags(Code::JOIN),
                'agreeCheck' => 'n',
                'desc'       => __('신규 회원의 가입 완료 시 발송'),
                'category'   => self::MEMBER_CATEGORY_NAME,
            ],
            [
                'code'       => Code::APPROVAL,
                'text'       => __('가입승인'),
                ...self::buildPolicyFlags(Code::APPROVAL),
                'agreeCheck' => 'n',
                'desc'       => __('운영자가 회원가입 승인 처리 시 발송'),
                'category'   => self::MEMBER_CATEGORY_NAME,
            ],
            [
                'code'       => Code::PASS_AUTH,
                'text'       => __('비밀번호 찾기 인증번호'),
                ...self::buildPolicyFlags(Code::PASS_AUTH),
                'agreeCheck' => 'n',
                'desc'       => __('회원이 비밀번호 찾기를 시도할 때 인증번호 안내 시 발송'),
                'category'   => self::MEMBER_CATEGORY_NAME,
            ],
            [
                'code'       => Code::SLEEP_INFO,
                'text'       => __('휴면회원 전환 사전안내'),
                ...self::buildPolicyFlags(Code::SLEEP_INFO),
                'agreeCheck' => 'n',
                'desc'       => __('회원이 휴면상태로 전환되기 30일 전 발송'),
                'reserveHour'=> '10',
                'category'   => self::MEMBER_CATEGORY_NAME,
            ],
            [
                'code'       => Code::SLEEP_INFO_TODAY,
                'text'       => __('휴면회원 전환 안내'),
                ...self::buildPolicyFlags(Code::SLEEP_INFO_TODAY),
                'agreeCheck' => 'n',
                'desc'       => __('회원이 휴면상태로 전환될 시 발송'),
                'reserveHour'=> '10',
                'category'   => self::MEMBER_CATEGORY_NAME,
            ],
            [
                'code'       => Code::SLEEP_AUTH,
                'text'       => __('휴면회원 해제 인증번호'),
                ...self::buildPolicyFlags(Code::SLEEP_AUTH),
                'agreeCheck' => 'n',
                'desc'       => __('휴면회원이 휴대폰 인증으로 휴면 해제 시도 시 발송'),
                'category'   => self::MEMBER_CATEGORY_NAME,
            ],
            [
                'code'       => Code::SLEEP_WAKE,
                'text'       => __('일반회원 전환 안내'),
                ...self::buildPolicyFlags(Code::SLEEP_WAKE),
                'agreeCheck' => 'n',
                'desc'       => __('휴면상태 해제로 일반회원으로 전환될 시 발송'),
                'category'   => self::MEMBER_CATEGORY_NAME,
            ],
            [
                'code'       => Code::AGREEMENT2YPERIOD,
                'text'       => __('수신동의여부 재확인'),
                ...self::buildPolicyFlags(Code::AGREEMENT2YPERIOD),
                'agreeCheck' => 'n',
                'desc'       => __('회원 수신동의일 기준 2년 경과 전 발송 (이메일이 등록된 회원은 메일로 발송)'),
                'reserveHour'=> '8',
                'category'   => self::MEMBER_CATEGORY_NAME,
            ],
            [
                'code'       => Code::GROUP_CHANGE,
                'text'       => __('회원등급 변경안내'),
                ...self::buildPolicyFlags(Code::GROUP_CHANGE),
                'agreeCheck' => 'n',
                'desc'       => __('회원등급 변경 시 발송'),
                'reserveHour'=> '8',
                'category'   => self::MEMBER_CATEGORY_NAME,
            ],
            [
                'code'       => Code::MILEAGE_PLUS,
                'text'       => __('마일리지 지급안내'),
                ...self::buildPolicyFlags(Code::MILEAGE_PLUS),
                'agreeCheck' => 'n',
                'desc'       => __('마일리지 지급 완료 시 발송 (자동·수동 지급 포함)'),
                'category'   => self::MEMBER_CATEGORY_NAME,
            ],
            [
                'code'       => Code::MILEAGE_MINUS,
                'text'       => __('마일리지 차감안내'),
                ...self::buildPolicyFlags(Code::MILEAGE_MINUS),
                'agreeCheck' => 'n',
                'desc'       => __('마일리지 차감 완료 시 발송'),
                'category'   => self::MEMBER_CATEGORY_NAME,
            ],
            [
                'code'       => Code::MILEAGE_EXPIRE,
                'text'       => __('마일리지 소멸안내'),
                ...self::buildPolicyFlags(Code::MILEAGE_EXPIRE),
                'agreeCheck' => 'n',
                'desc'       => __('마일리지 소멸 예정일(마일리지 기본설정 기준) 도래 전 발송'),
                'reserveHour'=> '9',
                'category'   => self::MEMBER_CATEGORY_NAME,
            ],
            [
                'code'       => Code::DEPOSIT_PLUS,
                'text'       => __('예치금 지급안내'),
                ...self::buildPolicyFlags(Code::DEPOSIT_PLUS),
                'agreeCheck' => 'n',
                'desc'       => __('예치금 지급 완료 시 발송 (환불 처리·직접 지급 포함)'),
                'category'   => self::MEMBER_CATEGORY_NAME,
            ],
            [
                'code'       => Code::DEPOSIT_MINUS,
                'text'       => __('예치금 차감안내'),
                ...self::buildPolicyFlags(Code::DEPOSIT_MINUS),
                'agreeCheck' => 'n',
                'desc'       => __('예치금 차감 완료 시 발송 (회원 사용·운영자 차감 포함)'),
                'category'   => self::MEMBER_CATEGORY_NAME,
            ],
        ];
        //@formatter:on
    }

    /**
     * 프로모션 관련 설정 값 초기화
     *
     */
    protected function initPromotion()
    {
        //@formatter:off
        $this->codes[self::PROMOTION] = [
            [
                'code'       => Code::COUPON_ORDER_FIRST,
                'text'       => __('첫 구매 축하 쿠폰'),
                ...self::buildPolicyFlags(Code::COUPON_ORDER_FIRST),
                'agreeCheck' => 'y',
                'desc'       => __('첫 구매한 회원에게 쿠폰 자동 발급 시 발송 (SMS 수신동의 회원에게만 발송)'),
                'category'   => self::PROMOTION_CATEGORY_NAME,
            ],
            [
                'code'       => Code::COUPON_ORDER,
                'text'       => __('구매 감사 쿠폰'),
                ...self::buildPolicyFlags(Code::COUPON_ORDER),
                'agreeCheck' => 'y',
                'desc'       => __('상품 구매한 회원에게 감사 쿠폰 자동 발급 시 발송 (SMS 수신동의 회원에게만 발송)'),
                'category'   => self::PROMOTION_CATEGORY_NAME,
            ],
            [
                'code'       => Code::COUPON_BIRTH,
                'text'       => __('생일 축하 쿠폰'),
                ...self::buildPolicyFlags(Code::COUPON_BIRTH),
                'agreeCheck' => 'y',
                'desc'       => __('생일인 회원에게 생일 축하 쿠폰 자동 발급 시 발송 (SMS 수신동의 회원에게만 발송)'),
                'reserveHour'=> '8',
                'category'   => self::PROMOTION_CATEGORY_NAME,
            ],
            [
                'code'       => Code::COUPON_JOIN,
                'text'       => __('회원가입 축하 쿠폰'),
                ...self::buildPolicyFlags(Code::COUPON_JOIN),
                'agreeCheck' => 'y',
                'desc'       => __('신규 가입한 회원에게 쿠폰 자동 발급 시 발송 (SMS 수신동의 회원에게만 발송)'),
                'category'   => self::PROMOTION_CATEGORY_NAME,
            ],
            [
                'code'       => Code::COUPON_LOGIN,
                'text'       => __('출석체크 감사 쿠폰'),
                ...self::buildPolicyFlags(Code::COUPON_LOGIN),
                'agreeCheck' => 'y',
                'desc'       => __('출석체크 이벤트 조건을 달성한 회원에게 쿠폰 발급 시 발송 (SMS 수신동의 회원에게만 발송)'),
                'category'   => self::PROMOTION_CATEGORY_NAME,
            ],
            [
                'code'       => Code::COUPON_MEMBER_MODIFY,
                'text'       => __('회원 정보 이벤트 쿠폰'),
                ...self::buildPolicyFlags(Code::COUPON_MEMBER_MODIFY),
                'agreeCheck' => 'y',
                'desc'       => __('회원정보 이벤트 조건을 충족한 회원에게 쿠폰 발급 시 발송 (SMS 수신동의 회원에게만 발송)'),
                'category'   => self::PROMOTION_CATEGORY_NAME,
            ],
            // RecipientPolicy 미정의 — sendType 하드코딩 유지
            [
                'code'       => Code::COUPON_WAKE,
                'text'       => __('휴면회원 해제 감사 쿠폰'),
                ...self::buildPolicyFlags(Code::COUPON_WAKE),
                'sendType'   => 'member',
                'agreeCheck' => 'y',
                'desc'       => '',
                'category'   => self::PROMOTION_CATEGORY_NAME,
            ],
            [
                'code'       => Code::COUPON_MANUAL,
                'text'       => __('수동쿠폰 발급 안내'),
                ...self::buildPolicyFlags(Code::COUPON_MANUAL),
                'agreeCheck' => 'y',
                'desc'       => __('운영자가 직접 쿠폰 발급 시 발송 (SMS 수신동의 회원에게만 발송)'),
                'category'   => self::PROMOTION_CATEGORY_NAME,
            ],
            [
                'code'       => Code::COUPON_WARNING,
                'text'       => __('쿠폰만료 안내'),
                ...self::buildPolicyFlags(Code::COUPON_WARNING),
                'agreeCheck' => 'y',
                'desc'       => __('회원이 보유한 쿠폰의 사용기간 만료 전 발송 (SMS 수신동의 회원에게만 발송)'),
                'reserveHour'=> '11',
                'category'   => self::PROMOTION_CATEGORY_NAME,
            ],
            [
                'code'       => Code::BIRTH,
                'text'       => __('생일축하'),
                ...self::buildPolicyFlags(Code::BIRTH),
                'agreeCheck' => 'y',
                'desc'       => __('회원 생일 당일 축하 메시지 발송 (SMS 수신동의 회원에게만 발송)'),
                'reserveHour'=> '10',
                'category'   => self::PROMOTION_CATEGORY_NAME,
            ],
        ];
        //@formatter:on
    }

    /**
     * 관리자 관련 설정 값 초기화
     *
     */
    protected function initAdmin()
    {
        //@formatter:off
        $this->codes[self::ADMIN] = [
            [
                'code'       => Code::SETTLE_BANK,
                'text'       => __('무통장 입금은행 정보 변경'),
                ...self::buildExceptionPolicyFlags(Code::SETTLE_BANK),
                'agreeCheck' => 'n',
                'desc'       => '',
            ],
        ];
        //@formatter:on
    }

    /**
     * 게시판 관련 설정 값 초기화
     */
    protected function initBoard()
    {
        $boardDescMap = [
            'goodsreview' => __('상품후기가 등록되면 운영자에게, 운영자가 답변을 등록하면 후기 작성자에게 발송'),
            'goodsqa'     => __('상품문의가 등록되면 운영자에게, 운영자가 답변을 등록하면 작성자에게 발송'),
            'qa'          => __('1:1문의가 등록되면 운영자에게, 운영자가 답변을 등록하면 작성자에게 발송'),
            'notice'      => __('공지사항이 등록되면 운영자에게, 운영자가 답변을 등록하면 작성자에게 발송'),
            'event'       => __('이벤트 게시글이 등록되면 운영자에게, 운영자가 답변을 등록하면 작성자에게 발송'),
            'cooperation' => __('광고·제휴 게시글이 등록되면 운영자에게, 운영자가 답변을 등록하면 작성자에게 발송'),
        ];

        $db = \App::load('DB');
        $boardList = $db->query_fetch('SELECT bdId, bdNm, bdGoodsFl FROM ' . DB_BOARD . ' ORDER BY sno ASC');
        $this->codes[self::BOARD] = [];
        foreach ($boardList as $row) {
            $_provider = '';
            if ($row['bdGoodsFl'] == 'y') { //상품연동된것만
                $_provider = '_provider';
            }
            $boardDesc = $boardDescMap[$row['bdId']] ?? '게시글이 등록되면 운영자에게, 운영자가 답변을 등록하면 작성자에게 발송';
            //@formatter:off
            $this->codes[self::BOARD][$row['bdId']] = [
                'code'             => $row['bdId'],
                'text'             => $row['bdNm'],
                // 신규 게시판일 경우 기본 템플릿 미제공
                'memberContents'   => '',
                'adminContents'    => '',
                'providerContents' => '',
                ...self::buildExceptionPolicyFlags($row['bdId'], 'member_admin' . $_provider),
                'agreeCheck'       => 'n',
                'smsType'          => 'board',
                'desc'             => $boardDesc,
                'category'         => self::BOARD_CATEGORY_NAME,
            ];
            //@formatter:on
        }
    }

    /**
     * 선물하기 관련 설정 값 초기화
     *
     */
    protected function initPresent()
    {
        //@formatter:off
        $this->codes[self::PRESENT] = [
            [
                'code'       => Code::PRESENT,
                'text'       => __('선물메시지'),
                ...self::buildPolicyFlags(Code::PRESENT),
                'desc'       => __('선물 주문 상태가 \'결제 완료\'로 변경 시 발송 (무통장 및 가상계좌는 운영자가 직접 \'결제완료\'로 처리 시)'),
                'category'   => self::PRESENT_CATEGORY_NAME,
            ],
            [
                'code'       => Code::PRESENT_REMAIN_THREE,
                'text'       => __('선물 취소 3일 전'),
                ...self::buildPolicyFlags(Code::PRESENT_REMAIN_THREE),
                'desc'       => __('선물 수락기간 만료되기 3일 전 발송'),
                'category'   => self::PRESENT_CATEGORY_NAME,
            ],
            [
                'code'       => Code::PRESENT_REMAIN_ONE,
                'text'       => __('선물 취소 1일 전'),
                ...self::buildPolicyFlags(Code::PRESENT_REMAIN_ONE),
                'desc'       => __('선물 수락기간 만료되기 1일 전 발송'),
                'category'   => self::PRESENT_CATEGORY_NAME,
            ],
            [
                'code'       => Code::PRESENT_REJECT,
                'text'       => __('선물 거절'),
                ...self::buildPolicyFlags(Code::PRESENT_REJECT),
                'agreeCheck' => 'y',
                'desc'       => __('선물 거절이 가능한 경우, \'거절하기\' 버튼 클릭 시 또는 선물 수락 기간 만료 시 발송'),
                'category'   => self::PRESENT_CATEGORY_NAME,
            ],
            [
                'code'       => Code::PRESENT_ACCEPT,
                'text'       => __('선물 수락'),
                ...self::buildPolicyFlags(Code::PRESENT_ACCEPT),
                'agreeCheck' => 'y',
                'desc'       => __('선물메시지의 \'수락하기\' 버튼을 클릭 시 발송'),
                'category'   => self::PRESENT_CATEGORY_NAME,
            ],
            [
                'code'       => Code::PRESENT_CHANGE_DEST,
                'text'       => __('선물 배송지 변경'),
                ...self::buildPolicyFlags(Code::PRESENT_CHANGE_DEST),
                'agreeCheck' => 'y',
                'desc'       => __('선물 배송지 변경될 시 발송'),
                'category'   => self::PRESENT_CATEGORY_NAME,
            ],
            [
                'code'       => Code::PRESENT_INVOICE_CODE,
                'text'       => __('선물배송안내 (송장번호 포함)'),
                ...self::buildPolicyFlags(Code::PRESENT_INVOICE_CODE),
                'agreeCheck' => 'y',
                'desc'       => __('선물 주문 상태가 \'배송 중\'으로 변경 시, 송장번호를 포함하여 발송'),
                'category'   => self::PRESENT_CATEGORY_NAME,
            ],
            [
                'code'       => Code::PRESENT_REJECT_FAIL,
                'text'       => __('선물 거절 실패'),
                ...self::buildPolicyFlags(Code::PRESENT_REJECT_FAIL),
                'agreeCheck' => 'y',
                'desc'       => __('선물 거절이 불가능한 경우, 선물 수락 기간 만료 시 발송'),
                'category'   => self::PRESENT_CATEGORY_NAME,
            ],
        ];
        //@formatter:off
    }

    /**
     * Policy에 미정의된 코드의 옵션 플래그를 반환한다.
     * - REPAYPART, SETTLE_BANK, 게시판 코드 등 Policy에 등록되지 않은 코드 전용
     *
     * @param string $code
     * @param ?string $sendType 동적으로 결정되는 sendType (게시판 등)
     * @return array
     */
    private static function buildExceptionPolicyFlags(string $code, ?string $sendType = null): array
    {
        return match ($code) {
            Code::REPAYPART => [
                'sendType'         => 'member_admin_provider',
                'orderCheck'       => 'y',
                'nightCheck'       => 'y',
                'couponCheck'      => 'n',
                'deliveryCheck'    => 'n',
                'disapprovalCheck' => 'n',
            ],
            Code::SETTLE_BANK => [
                'sendType'         => 'admin',
                'orderCheck'       => 'n',
                'nightCheck'       => 'n',
                'couponCheck'      => 'n',
                'deliveryCheck'    => 'n',
                'disapprovalCheck' => 'n',
            ],
            default => [
                'sendType'         => $sendType ?? 'member_admin',
                'orderCheck'       => 'n',
                'nightCheck'       => 'y',
                'couponCheck'      => 'n',
                'deliveryCheck'    => 'n',
                'disapprovalCheck' => 'n',
            ],
        };
    }

    /**
     * AutoSendOptionVisibilityPolicy와 AutoSendRecipientPolicy를 기반으로
     * 코드별 옵션 플래그와 sendType을 반환한다.
     *
     * - targetOrderScope       → orderCheck
     * - isNightSendEnabled     → nightCheck
     * - sendTriggerType        → couponCheck
     * - sendUnit               → deliveryCheck
     * - includeApprovalPendingMember → disapprovalCheck
     * - AutoSendRecipientPolicy → sendType
     *
     * Policy에 미정의된 코드는 빈 배열을 반환한다.
     *
     * @param string $code
     * @return array
     */
    private static function buildPolicyFlags(string $code): array
    {
        $flags = [];

        $recipients = AutoSendRecipientPolicy::getCodeWithRecipients($code);
        if (!empty($recipients)) {
            $flags['sendType'] = self::recipientsToSendType($recipients);
        }

        $visibility = AutoSendOptionVisibilityPolicy::getOptionVisibilityByCode($code);
        if ($visibility !== null) {
            $flags['orderCheck'] = $visibility->targetOrderScope ? 'y' : 'n';
            $flags['nightCheck'] = $visibility->isNightSendEnabled ? 'y' : 'n';
            $flags['couponCheck'] = $visibility->sendTriggerType ? 'y' : 'n';
            $flags['deliveryCheck'] = $visibility->sendUnit ? 'y' : 'n';
            $flags['disapprovalCheck'] = $visibility->includeApprovalPendingMember ? 'y' : 'n';
        }

        return $flags;
    }

    /**
     * AutoSendRecipient 배열을 sendType 문자열로 변환
     * 정렬 순서: recipient(0) → member(1) → admin(2) → provider(3)
     *
     * @param AutoSendRecipient[] $recipients
     * @return string
     */
    private static function recipientsToSendType(array $recipients): string
    {
        $order = [
            AutoSendRecipient::RECIPIENT->name => 0,
            AutoSendRecipient::MEMBER->name    => 1,
            AutoSendRecipient::ADMIN->name     => 2,
            AutoSendRecipient::PROVIDER->name  => 3,
        ];

        usort($recipients, fn(AutoSendRecipient $a, AutoSendRecipient $b) =>
            ($order[$a->name] ?? 99) <=> ($order[$b->name] ?? 99)
        );

        return implode('_', array_map(
            fn(AutoSendRecipient $r) => strtolower($r->name),
            $recipients
        ));
    }

    /**
     * 설정 값 기본 데이터 반환 함수
     *
     * @param        $code
     * @param        $text
     * @param        $sendType
     * @param string $desc
     * @param array  $check
     *
     * @return array
     */
    protected function getDefaultCode($code, $text, $sendType, $desc = '', array $check = [], $category = null)
    {
        $defaultCheck = [
            'orderCheck'       => 'n',
            'nightCheck'       => 'n',
            'agreeCheck'       => 'n',
            'couponCheck'      => 'n',
            'disapprovalCheck' => 'n',
        ];
        foreach ($defaultCheck as $index => $item) {
            if (gd_array_key_exists($index, $check)) {
                $defaultCheck[$index] = $check[$index];
            }
        }

        if ($category != null) {
            $defaultCheck['category'] = $category;
        }

        $default = [
            'code'     => $code,
            'text'     => $text,
            'sendType' => $sendType,
            'desc'     => $desc,
        ];
        $result = gd_array_merge($default, $defaultCheck);

        return $result;
    }

    /**
     * SMS 자동발송 설정을 반환하는 함수
     * @deprecated 기존 Sms::setSmsAutoCode 를 사용하던 곳에서 쓰기위해 만든 함수
     * @uses       SmsAutoCode::getCodes
     * @static
     * @return array
     */
    public static function getSmsAutoCode()
    {
        $smsAutoCode = new SmsAutoCode();

        return $smsAutoCode->getCodes();
    }

    /**
     * SMS 자동발송 설정을 반환하는 함수
     *
     * @return array
     */
    public function getCodes()
    {
        $this->initialize();

        return $this->codes;
    }


    /**
     * @param string $code
     * @return array|null
     */
    public function getByCode(string $code): ?array
    {
        $this->initialize();

        foreach ($this->codes as $group => $items) {
            foreach ($items as $item) {
                if (($item['code'] ?? null) === $code) {
                    return $item;
                }
            }
        }

        return null;
    }

    /**
     * @param string $code
     * @return string|null
     */
    public function getGroupByCode(string $code): ?string
    {
        $this->initialize();

        foreach ($this->codes as $group => $items) {

            $foundCode = array_filter($items, fn($item) => ($item['code'] ?? null) === $code);

            if (!empty($foundCode)) {
                return $group;
            }
        }

        return null;
    }

    /**
     * @param string $group
     * @return string|null
     */
    static function getCategoryNameByGroup(string $group): ?string
    {
        return match ($group) {
            self::ORDER             => self::ORDER_CATEGORY_NAME,
            self::REGULAR_DELIVERY  => self::REGULAR_DELIVERY_CATEGORY_NAME,
            self::MEMBER            => self::MEMBER_CATEGORY_NAME,
            self::PROMOTION         => self::PROMOTION_CATEGORY_NAME,
            self::BOARD             => self::BOARD_CATEGORY_NAME,
            self::PRESENT           => self::PRESENT_CATEGORY_NAME,
            default => null,
        };
    }

}
