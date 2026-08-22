/*
 * Copyright (C) 2026 NHN COMMERCE. - All Rights Reserved
 *
 * Unauthorized copying or redistribution of this file in source and binary forms via any medium
 * is strictly prohibited.
 */

/**
 * 모바일 메시지용 치환코드 정의
 */
const MobileMessageReplaceCode = (function() {
    // SendMethod Enum
    const SendMethod = Object.freeze({
        ALL: 'ALL',
        MANUAL: 'MANUAL',
        AUTO: 'AUTO'
    });

    const MEMBER = {
        rc_mallNm: { name: '쇼핑몰 명/상점명', default: '고도쇼핑몰', sendMethod: SendMethod.ALL },
        memId: { name: '회원 아이디', default: 'godouser01', sendMethod: SendMethod.ALL },
        memNm: { name: '회원명', default: '김고도', sendMethod: SendMethod.ALL },
        sleepScheduleDt: { name: '휴면회원전환예정일', default: '2025-12-31', sendMethod: SendMethod.ALL },
        rc_scheduleDt: { name: '일반회원전환일', default: '2025-11-15', sendMethod: SendMethod.ALL },
        smsAgreementDt: { name: 'SMS 수신동의일', default: '2024-06-10', sendMethod: SendMethod.ALL },
        mailAgreementDt: { name: '메일 수신동의일', default: '2024-06-10', sendMethod: SendMethod.ALL },
        groupNm: { name: '회원등급', default: '일반회원', sendMethod: SendMethod.ALL },
        mileage: { name: '보유한 마일리지', default: '1,400', sendMethod: SendMethod.ALL },
        deposit: { name: '보유한 예치금', default: '14,000', sendMethod: SendMethod.ALL },
        rc_mileage: { name: '지급/차감/소멸 예정 마일리지', default: '500', sendMethod: SendMethod.AUTO },
        rc_deleteScheduleDt: { name: '마일리지 소멸 예정일시', default: '2025-12-31', sendMethod: SendMethod.AUTO },
        rc_deposit: { name: '지급/차감 예치금', default: '5,000', sendMethod: SendMethod.AUTO },
        rc_certificationCode: { name: '비밀번호 찾기/휴면 해제 인증번호', default: '392014', sendMethod: SendMethod.AUTO },
        smsAgreementFl: { name: 'SMS 수신동의 여부', default: '동의함', sendMethod: SendMethod.AUTO },
        mailAgreementFl: { name: '메일 수신동의 여부', default: '미동의', sendMethod: SendMethod.AUTO },
        shopUrl: { name: '도메인', default: 'https://www.testgodomall.com', sendMethod: SendMethod.AUTO },
        passJoin: { name: '회원가입 경과일', default: '1,240', sendMethod: SendMethod.MANUAL }
    };

    const GOODS = {
        rc_mallNm: { name: '쇼핑몰 명/상점명', default: '고도쇼핑몰', sendMethod: SendMethod.MANUAL },
        restockName: { name: '(재입고 알림) 신청자명', default: '김고도', sendMethod: SendMethod.MANUAL },
        restockGoodsNm: { name: '(재입고 알림) 상품명', default: '코튼 셔츠', sendMethod: SendMethod.MANUAL },
        restockOptionName: { name: '(재입고 알림) 상품옵션', default: '화이트 / M', sendMethod: SendMethod.MANUAL },
        restockGoodsUrl: { name: '(재입고 알림) 상품 URL', default: 'https://goods.godomall.com/12345', sendMethod: SendMethod.MANUAL }
    };

    const ORDER = {
        rc_mallNm: { name: '쇼핑몰 명/상점명', default: '고도쇼핑몰', sendMethod: SendMethod.ALL },
        orderName: { name: '주문자 이름', default: '김고도', sendMethod: SendMethod.ALL },
        orderNo: { name: '주문번호', default: 'ORD20251113001', sendMethod: SendMethod.ALL },
        settlePrice: { name: '총 주문 금액', default: '59,000', sendMethod: SendMethod.ALL },
        cancelPrice: { name: '취소금액', default: '12,000', sendMethod: SendMethod.ALL },
        gbRefundPrice: { name: '실 환불금액', default: '12,000', sendMethod: SendMethod.ALL },
        depositNm: { name: '입금자명', default: '고도상점', sendMethod: SendMethod.ALL },
        bankAccount: { name: '입금계좌번호', default: 'NHN은행 123-456789-101112', sendMethod: SendMethod.ALL },
        expirationDate: { name: '입금만료일', default: '2025-11-15', sendMethod: SendMethod.ALL },
        bankInfo: { name: '은행명/계좌번호/예금주', default: '예금주: 고도상점', sendMethod: SendMethod.MANUAL },
        orderDate: { name: '주문일', default: '2025-11-12', sendMethod: SendMethod.MANUAL },
        invoiceNo: { name: '송장번호', default: '5678901234', sendMethod: SendMethod.AUTO },
        goodsNm: { name: '주문 상품명', default: '코튼 셔츠', sendMethod: SendMethod.AUTO },
        userExchangeStatus: { name: '클레임 상태', default: '교환 완료', sendMethod: SendMethod.AUTO },
        deliveryName: { name: '배송업체명', default: '고도택배', sendMethod: SendMethod.AUTO },
        shopUrl: { name: '도메인', default: 'https://www.testgodomall.com', sendMethod: SendMethod.AUTO },
    };

    const PROMOTION = {
        rc_mallNm: { name: '쇼핑몰 명/상점명', default: '고도쇼핑몰', sendMethod: SendMethod.ALL },
        memNm: { name: '회원명 (없는 경우 공백으로 치환)', default: '김고도', sendMethod: SendMethod.ALL },
        eventNm: { name: '(기획전 홍보) 기획전명', default: '아우터 페스티벌', sendMethod: SendMethod.MANUAL },
        eventDt: { name: '(기획전 홍보) 기획전 기간', default: '2025.11.15 ~ 2025.11.30', sendMethod: SendMethod.MANUAL },
        eventUrl: { name: '(기획전 홍보) 기획전 url', default: 'https://www.testgodomall.com/event/outer2025', sendMethod: SendMethod.MANUAL },
        eventType: { name: '이벤트 종류', default: '신규회원 전용 이벤트', sendMethod: SendMethod.AUTO },
        CouponName: { name: '쿠폰명', default: '11월 웰컴 할인쿠폰', sendMethod: SendMethod.AUTO },
        CouponDate: { name: '쿠폰 기간', default: '2025.11.01 ~ 2025.11.30', sendMethod: SendMethod.AUTO },
        rc_memid: { name: '회원 아이디', default: 'godouser01', sendMethod: SendMethod.AUTO },
    };

    const BOARD = {
        rc_mallNm: { name: '쇼핑몰 명/상점명', default: '고도쇼핑몰', sendMethod: SendMethod.ALL },
        memNm: { name: '회원명', default: '김고도', sendMethod: SendMethod.MANUAL },
        shopName: { name: '쇼핑몰 명/상점명', default: '고도쇼핑몰', sendMethod: SendMethod.AUTO },
        shopUrl: { name: '도메인', default: 'https://www.testgodomall.com', sendMethod: SendMethod.ALL },
        wriNm: { name: '작성자명', default: '김고도', sendMethod: SendMethod.ALL }
    };

    const REGULAR = {
        rc_mallNm: { name: '쇼핑몰 명/상점명', default: '고도쇼핑몰', sendMethod: SendMethod.ALL },
        subNo: { name: '신청 번호', default: 'SUB2025111301', sendMethod: SendMethod.ALL },
        settlePrice: { name: '총 주문 금액', default: '59,000', sendMethod: SendMethod.ALL },
        subGoodsNm: { name: '정기배송 상품명', default: '양말 정기배송', sendMethod: SendMethod.ALL },
        subDeliveryCycle: { name: '정기배송 주기', default: '매월 1회', sendMethod: SendMethod.ALL },
        subDeliveryDate: { name: '정기배송 예정일', default: '2025-12-01', sendMethod: SendMethod.ALL },
        subAddress: { name: '정기배송지', default: '서울시 강남구 테헤란로 123', sendMethod: SendMethod.ALL },
        cardName: { name: '카드사명', default: '고도카드', sendMethod: SendMethod.ALL },
        cardNo: { name: '카드번호', default: '2423', sendMethod: SendMethod.AUTO },
        subName: { name: '신청자명', default: '김고도', sendMethod: SendMethod.ALL },
        autoPayDate: { name: '자동결제일', default: '2025-11-25', sendMethod: SendMethod.ALL },
        subPayRound: { name: '자동결제회차(배송회차)', default: '3', sendMethod: SendMethod.ALL },
        cancelReason: { name: '해지사유', default: '서비스 이용 중단', sendMethod: SendMethod.ALL },
        endPayRound: { name: '종료회차', default: '4', sendMethod: SendMethod.ALL },
        deliveryName: { name: '배송업체명', default: '고도택배', sendMethod: SendMethod.AUTO },
        invoiceNo: { name: '송장번호', default: '567890123456', sendMethod: SendMethod.AUTO }
    };

    const PRESENT = {
        rc_mallNm: { name: '쇼핑몰 명/상점명', default: '고도쇼핑몰', sendMethod: SendMethod.ALL },
        receiverName: { name: '수령자명', default: '김고도', sendMethod: SendMethod.ALL },
        orderName: { name: '주문자명', default: '김고도', sendMethod: SendMethod.ALL },
        presentExpiryDate: { name: '수락 기간 만료일', default: '2025-12-31', sendMethod: SendMethod.ALL },
        presentUrl: { name: '선물확인 URL', default: 'https://www.godomall.com/gift/confirm/12345', sendMethod: SendMethod.ALL },
        shopUrl: { name: '상점 URL', default: 'https://www.testgodomall.com', sendMethod: SendMethod.ALL },
        orderNo: { name: '주문번호', default: 'ORD20251113001', sendMethod: SendMethod.ALL },
        deliveryName: { name: '배송업체명', default: '고도택배', sendMethod: SendMethod.AUTO },
        invoiceNo: { name: '송장번호', default: '567890123456', sendMethod: SendMethod.AUTO }
    };

    const CRM_RECIPE = {
        rc_mallNm: { name: '쇼핑몰 명/상점명', default: '고도쇼핑몰', sendMethod: SendMethod.MANUAL },
        memId: { name: '회원 아이디', default: 'godouser01', sendMethod: SendMethod.MANUAL },
        memNm: { name: '회원명', default: '김고도', sendMethod: SendMethod.MANUAL },
        smsAgreementDt: { name: 'SMS 수신동의일', default: '2024-06-10', sendMethod: SendMethod.MANUAL },
        groupNm: { name: '회원등급', default: '일반회원', sendMethod: SendMethod.MANUAL },
        mileage: { name: '보유한 마일리지', default: '1,400', sendMethod: SendMethod.MANUAL },
        deposit: { name: '보유한 예치금', default: '14,000', sendMethod: SendMethod.MANUAL },
        passJoin: { name: '회원가입 경과일', default: '1,240', sendMethod: SendMethod.MANUAL },
        useCouponCnt: { name: '사용 가능 쿠폰 수', default: '7', sendMethod: SendMethod.MANUAL },
        expireCoupon_Nd: { name: 'N일 내 만료 예정 쿠폰명', default: '웰컴 쿠폰', defaultDay: '7', sendMethod: SendMethod.MANUAL },
        expireMileage_Nd: { name: 'N일 내 만료 예정 마일리지', default: '700', defaultDay: '7', sendMethod: SendMethod.MANUAL }
    };

    /**
     * 대상 객체에서 sendMethod로 필터링하여 키 배열 반환
     * @param {object} target - MEMBER | ORDER
     * @param {string} sendMethod - SendMethod.ALL | SendMethod.MANUAL | SendMethod.AUTO
     * @returns {string[]}
     */
    const getKeysByMethod = (target, sendMethod = SendMethod.ALL) => {
        return Object.entries(target)
            .filter(([, obj]) => obj.sendMethod === sendMethod || obj.sendMethod === SendMethod.ALL)
            .map(([key]) => key);
    };

    /**
     * 대상 객체에서 sendMethod로 필터링하여 {key, name, default} 배열 반환
     * @param {object} target - MEMBER | ORDER
     * @param {string} sendMethod - SendMethod.ALL | SendMethod.MANUAL | SendMethod.AUTO
     * @returns {Array<{key: string, name: string, default: string}>}
     */
    const getItemsByMethod = (target, sendMethod) => {
        return Object.entries(target)
            .filter(([, obj]) => obj.sendMethod === sendMethod || obj.sendMethod === SendMethod.ALL)
            .map(([key, obj]) => ({ key, name: obj.name, default: obj.default, defaultDay: obj.defaultDay }));
    };

    /**
     * 대상 객체의 모든 키 배열 반환
     * @param {object} target - MEMBER | ORDER
     * @returns {string[]}
     */
    const getAllKeys = (target) => {
        return Object.keys(target);
    };

    /**
     * 동적 키(_Nd 패턴)를 카탈로그 키로 정규화 (예: expireCoupon_2d → expireCoupon_Nd)
     * 카탈로그에 등록된 prefix(expireCoupon, expireMileage)만 변환 — 임의 키(custom_7d 등) 오변환 방지
     * @param {string} key
     * @returns {string}
     */
    const normalizeReplaceKey = (key) => key.replace(/^(expireCoupon|expireMileage)_\d+d$/, '$1_Nd');

    /**
     * recipeType 값을 케밥 소문자로 정규화 (예: DORMANT_COUPON_WAKEUP → dormant-coupon-wakeup)
     * @param {string} value
     * @returns {string}
     */
    const normalizeRecipeType = (value) => (value ?? '').toLowerCase().replace(/_/g, '-');

    /**
     * 미리보기용 치환 — 카탈로그 디폴트값 + 숏링크로 본문 내 치환코드를 채운다
     * expireCoupon_Nd/expireMileage_Nd 의 N이 0 이상 정수가 아니면(음수·소수) 빈값 치환
     * @param {string} content
     * @param {object} [options]
     * @param {boolean} [options.includeRecipe=true] - CRM_RECIPE 카테고리 포함 여부
     * @param {Map|null} [options.shortLinkKeys=null] - 성과추적 숏링크 키 Map
     * @returns {string}
     */
    const resolvePreviewContent = (content, { includeRecipe = true, shortLinkKeys = null } = {}) => {
        const forceDefaultKeys = ['shopUrl'];
        const replaceMap = {};
        [
            ...(includeRecipe ? [CRM_RECIPE] : []),
            MEMBER,
            GOODS,
            ORDER,
            PROMOTION,
            BOARD,
            REGULAR,
            PRESENT,
        ].forEach(category => {
            Object.entries(category).forEach(([key, obj]) => {
                if (replaceMap[key] !== undefined) return;
                replaceMap[key] = (obj.sendMethod !== SendMethod.AUTO || forceDefaultKeys.includes(key)) ? obj.default : '';
            });
        });

        if (shortLinkKeys) {
            shortLinkKeys.forEach((_, key) => {
                replaceMap[key] = '쇼핑몰도메인/_s/a1B2c3D';
            });
        }

        return content.replace(/[#$]?\{([a-zA-Z0-9_.\-]+)}/g, (match, key) => {
            const dynamicExpire = key.match(/^(expireCoupon|expireMileage)_(.+)d$/);
            if (dynamicExpire && !/^\d+$/.test(dynamicExpire[2])) return '';
            const normalizedKey = normalizeReplaceKey(key);
            return replaceMap[normalizedKey] !== undefined ? replaceMap[normalizedKey] : match;
        });
    };

    /**
     * 본문에 만료 치환코드(expireCoupon_Nd/expireMileage_Nd)가 있으면 N 입력 안내 문구 생성
     * @param {string} content
     * @returns {string}
     */
    const buildExpireCodeGuide = (content) => {
        if (!content) return '';
        const codes = [];
        if (/[#$]?\{expireCoupon_[^}]*d}/.test(content)) codes.push('{expireCoupon_Nd}');
        if (/[#$]?\{expireMileage_[^}]*d}/.test(content)) codes.push('{expireMileage_Nd}');
        return codes.length ? `${codes.join(', ')}의 N은 0 이상의 정수로 입력해주세요.` : '';
    };

    /**
     * 치환코드 셀렉터(variable-selector-wrap) 하단에 만료코드 안내 문구를 렌더/갱신/숨김
     * @param {string} chipContainerSelector - 대상 컨테이너 셀렉터
     * @param {string} content - 본문
     */
    const updateExpireCodeGuide = (chipContainerSelector, content) => {
        const wrap = document.querySelector(`${chipContainerSelector} .variable-selector-wrap`);
        if (!wrap) return;
        const message = buildExpireCodeGuide(content);
        let guide = wrap.querySelector('.js-expire-code-guide');
        if (!message) {
            if (guide) guide.hidden = true;
            return;
        }
        if (!guide) {
            guide = document.createElement('p');
            guide.className = 'ncua-caution-text js-expire-code-guide';
            wrap.querySelector('.ncua-card__body-title-wrap').insertAdjacentElement('afterend', guide);
        }
        guide.textContent = message;
        guide.hidden = false;
    };

    /**
     * 레시피 만료 치환코드 삽입 시 조건값(N일)을 실제 일자로 바인딩
     * (dormant-coupon-wakeup → expireCoupon, dormant-mileage-wakeup → expireMileage)
     * @param {string} variable - 삽입할 치환코드
     * @param {boolean} [isRecipeContext=true] - 레시피 컨텍스트 여부
     * @returns {string}
     */
    const resolveRecipeExpireInsertKey = (variable, isRecipeContext = true) => {
        if (!isRecipeContext) return variable;

        const recipeType = normalizeRecipeType(document.querySelector('input[name="recipeType"]')?.value);
        const boundPrefix = { 'dormant-coupon-wakeup': 'expireCoupon', 'dormant-mileage-wakeup': 'expireMileage' }[recipeType];
        if (!boundPrefix) return variable;

        const day = parseInt(document.querySelector('select.js-condition-value-select')?.value ?? '', 10);
        if (!Number.isInteger(day) || day < 0) return variable;

        return variable.replace(new RegExp(`\\{${boundPrefix}_\\d+d}`, 'g'), `{${boundPrefix}_${day}d}`);
    };

    // Public API
    return {
        SendMethod: SendMethod,
        MEMBER: MEMBER,
        GOODS: GOODS,
        ORDER: ORDER,
        PROMOTION: PROMOTION,
        BOARD: BOARD,
        REGULAR: REGULAR,
        PRESENT: PRESENT,
        CRM_RECIPE: CRM_RECIPE,
        getKeysByMethod: getKeysByMethod,
        getAllKeys: getAllKeys,
        getItemsByMethod: getItemsByMethod,
        normalizeReplaceKey: normalizeReplaceKey,
        normalizeRecipeType: normalizeRecipeType,
        resolvePreviewContent: resolvePreviewContent,
        buildExpireCodeGuide: buildExpireCodeGuide,
        updateExpireCodeGuide: updateExpireCodeGuide,
        resolveRecipeExpireInsertKey: resolveRecipeExpireInsertKey
    };
})();
