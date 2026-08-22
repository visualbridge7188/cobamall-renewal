/*
 * Copyright (C) 2026 NHN COMMERCE. - All Rights Reserved
 *
 * Unauthorized copying or redistribution of this file in source and binary forms via any medium
 * is strictly prohibited.
 */

/**
 * CRM 그룹 조건 설명 텍스트 생성 유틸
 */
const CrmGroupCondition = (function() {
    // 날짜/기간 패널 타입 상수
    const DATE_PERIOD_PANEL_TYPE = {
        JOIN_DATE: { text: '회원가입일이' },
        LAST_LOGIN_DATE: { text: '마지막 로그인일이' },
        CART_ADDED_DATE: { text: '장바구니 담은 날짜가' },
        ORDER_DATE: { text: '주문 날짜가' },
        PAY_DONE_DATE: { text: '결제 완료일이' },
        DELIVERY_DONE_DATE: { text: '배송 완료일이' },
        BUY_CONFIRM_DATE: { text: '구매 확정일이' },
        USABLE_COUPON_ISSUE_DATE: { text: '쿠폰 발급일이' },
        USABLE_COUPON_EXPIRATION_DATE: { text: '쿠폰 만료일이' },
        USED_COUPON_DATE: { text: '쿠폰 사용일이' },
        EXPIRING_ACCUMULATION_DATE: { text: '적립금 소멸 예정일이' }
    };

    // 횟수/금액 패널 타입 상수
    const COUNT_PANEL_TYPE = {
        LOGIN_COUNT: { text: '로그인 횟수가', unit: '회' },
        USABLE_ACCUMULATION_COUNT: { text: '사용 가능 적립금이', unit: '원' },
        STOCK_COUNT: { text: '장바구니 수량이', unit: '개' },
        ORDER_COUNT: { text: '주문 횟수가', unit: '회' },
        ORDER_AMOUNT: { text: '주문 금액이', unit: '원' },
        PAY_DONE_COUNT: { text: '결제 완료 횟수가', unit: '회' },
        DELIVERY_DONE_COUNT: { text: '배송 완료 횟수가', unit: '회' },
        BUY_CONFIRM_COUNT: { text: '구매 확정 횟수가', unit: '회' },
        USABLE_COUPON_COUNT: { text: '보유 쿠폰 수가', unit: '개' },
        USED_COUPON_COUNT: { text: '사용한 쿠폰 수가', unit: '개' },
        EXPIRING_ACCUMULATION_COUNT: { text: '소멸 예정 적립금이', unit: '원' }
    };

    // 기간 타입 텍스트
    const dayTypeText = {
        DAY: '일',
        WEEK: '주',
        MONTH: '개월'
    };

    /**
     * 날짜/기간 텍스트 생성
     * @param {Object} datePeriod - 날짜 조건 객체
     * @param {string} text - 조건 설명 텍스트
     * @param {boolean} isAfter - 이후 여부 (소멸 예정 등)
     * @returns {string|undefined}
     */
    function getDatePeriodText(datePeriod, text, isAfter) {
        if (!datePeriod) return undefined;
        
        isAfter = isAfter || false;
        const relative = datePeriod.relative;
        const absolute = datePeriod.absolute;

        if (relative) {
            const periodTextFrom = dayTypeText[relative.from.period] || '';
            const periodTextTo = dayTypeText[relative.to.period] || '';
            const fromText = isAfter ? '후부터' : '전부터';
            const toText = isAfter ? '후까지' : '전까지';

            return `${text} ${relative.from.value}${periodTextFrom} ${fromText} ${relative.to.value}${periodTextTo} ${toText}`;
        }

        if (absolute) {
            return `${text} ${absolute.from}부터 ${absolute.to}까지`;
        }

        return undefined;
    }

    /**
     * 회원 조건 설명 생성
     */
    function getMemberDescription(member) {
        const conditionTextList = [];

        if (member.joinDate) {
            const datePeriodText = getDatePeriodText(member.joinDate, DATE_PERIOD_PANEL_TYPE.JOIN_DATE.text);
            if (datePeriodText) conditionTextList.push(datePeriodText);
        }

        if (member.lastLoginDate) {
            const datePeriodText = getDatePeriodText(member.lastLoginDate, DATE_PERIOD_PANEL_TYPE.LAST_LOGIN_DATE.text);
            if (datePeriodText) conditionTextList.push(datePeriodText);
        }

        if (member.loginCount) {
            const config = COUNT_PANEL_TYPE.LOGIN_COUNT;
            conditionTextList.push(
                `${config.text} ${member.loginCount.fromCount}${config.unit} 이상 ${member.loginCount.toCount}${config.unit} 이하`
            );
        }

        return conditionTextList;
    }

    /**
     * 사용 가능 적립금 조건 설명 생성
     */
    function getUsableAccumulationDescription(usableAccumulation) {
        const config = COUNT_PANEL_TYPE.USABLE_ACCUMULATION_COUNT;
        return `${config.text} ${usableAccumulation.amount.fromCount}원 이상 ${usableAccumulation.amount.toCount}원 이하`;
    }

    /**
     * 장바구니 조건 설명 생성
     */
    function getCartDescription(cart) {
        const conditionTextList = [];

        if (cart.stockCount) {
            const config = COUNT_PANEL_TYPE.STOCK_COUNT;
            conditionTextList.push(`${config.text} ${cart.stockCount.fromCount}${config.unit} 이상 ${cart.stockCount.toCount}${config.unit} 이하`);
        }

        if (cart.addedDate) {
            const datePeriodText = getDatePeriodText(cart.addedDate, DATE_PERIOD_PANEL_TYPE.CART_ADDED_DATE.text);
            if (datePeriodText) conditionTextList.push(datePeriodText);
        }

        if (cart.products && cart.products.length > 0) {
            const productNames = cart.products.map(function(product) { return product.name; }).join(', ');
            conditionTextList.push('장바구니 담은 상품: ' + productNames);
        }

        return conditionTextList;
    }

    /**
     * 주문 조건 설명 생성
     */
    function getOrderDescription(order) {
        const conditionTextList = [];

        if (order.orderDate) {
            const datePeriodText = getDatePeriodText(order.orderDate, DATE_PERIOD_PANEL_TYPE.ORDER_DATE.text);
            if (datePeriodText) conditionTextList.push(datePeriodText);
        }

        if (order.orderCount) {
            const config = COUNT_PANEL_TYPE.ORDER_COUNT;
            conditionTextList.push(`${config.text} ${order.orderCount.fromCount}${config.unit} 이상 ${order.orderCount.toCount}${config.unit} 이하`);
        }

        if (order.amount) {
            const config = COUNT_PANEL_TYPE.ORDER_AMOUNT;
            conditionTextList.push(`${config.text} ${order.amount.fromCount}${config.unit} 이상 ${order.amount.toCount}${config.unit} 이하`);
        }

        return conditionTextList;
    }

    /**
     * 결제 완료 상품 조건 설명 생성
     */
    function getOrderOptionPayDoneDescription(orderOptionPayDone) {
        const conditionTextList = [];

        if (orderOptionPayDone.orderDate) {
            const datePeriodText = getDatePeriodText(orderOptionPayDone.orderDate, '결제 완료 상품: ' + DATE_PERIOD_PANEL_TYPE.PAY_DONE_DATE.text);
            if (datePeriodText) conditionTextList.push(datePeriodText);
        }

        if (orderOptionPayDone.count) {
            const config = COUNT_PANEL_TYPE.PAY_DONE_COUNT;
            conditionTextList.push(`결제 완료 상품: ${config.text} ${orderOptionPayDone.count.fromCount}${config.unit} 이상 ${orderOptionPayDone.count.toCount}${config.unit} 이하`);
        }

        if (orderOptionPayDone.products && orderOptionPayDone.products.length > 0) {
            const productNames = orderOptionPayDone.products.map(function(product) { return product.name; }).join(', ');
            conditionTextList.push('결제 완료 상품: ' + productNames);
        }

        return conditionTextList;
    }

    /**
     * 배송 완료 상품 조건 설명 생성
     */
    function getOrderOptionDeliveryDoneDescription(orderOptionDeliveryDone) {
        const conditionTextList = [];

        if (orderOptionDeliveryDone.orderDate) {
            const datePeriodText = getDatePeriodText(orderOptionDeliveryDone.orderDate, '배송 완료 상품: ' + DATE_PERIOD_PANEL_TYPE.DELIVERY_DONE_DATE.text);
            if (datePeriodText) conditionTextList.push(datePeriodText);
        }

        if (orderOptionDeliveryDone.count) {
            const config = COUNT_PANEL_TYPE.DELIVERY_DONE_COUNT;
            conditionTextList.push(`배송 완료 상품: ${config.text} ${orderOptionDeliveryDone.count.fromCount}${config.unit} 이상 ${orderOptionDeliveryDone.count.toCount}${config.unit} 이하`);
        }

        if (orderOptionDeliveryDone.products && orderOptionDeliveryDone.products.length > 0) {
            const productNames = orderOptionDeliveryDone.products.map(function(product) { return product.name; }).join(', ');
            conditionTextList.push('배송 완료 상품: ' + productNames);
        }

        return conditionTextList;
    }

    /**
     * 구매 확정 상품 조건 설명 생성
     */
    function getOrderOptionBuyConfirmDescription(orderOptionBuyConfirm) {
        const conditionTextList = [];

        if (orderOptionBuyConfirm.orderDate) {
            const datePeriodText = getDatePeriodText(orderOptionBuyConfirm.orderDate, '구매 확정 상품: ' + DATE_PERIOD_PANEL_TYPE.BUY_CONFIRM_DATE.text);
            if (datePeriodText) conditionTextList.push(datePeriodText);
        }

        if (orderOptionBuyConfirm.count) {
            const config = COUNT_PANEL_TYPE.BUY_CONFIRM_COUNT;
            conditionTextList.push(`구매 확정 상품: ${config.text} ${orderOptionBuyConfirm.count.fromCount}${config.unit} 이상 ${orderOptionBuyConfirm.count.toCount}${config.unit} 이하`);
        }

        if (orderOptionBuyConfirm.products && orderOptionBuyConfirm.products.length > 0) {
            const productNames = orderOptionBuyConfirm.products.map(function(product) { return product.name; }).join(', ');
            conditionTextList.push('구매 확정 상품: ' + productNames);
        }

        return conditionTextList;
    }

    /**
     * 보유 쿠폰 조건 설명 생성
     */
    function getUsableCouponDescription(usableCoupon) {
        const conditionTextList = [];

        if (usableCoupon.issuedDate) {
            const datePeriodText = getDatePeriodText(usableCoupon.issuedDate, DATE_PERIOD_PANEL_TYPE.USABLE_COUPON_ISSUE_DATE.text);
            if (datePeriodText) conditionTextList.push(datePeriodText);
        }

        if (usableCoupon.expireDate) {
            const datePeriodText = getDatePeriodText(usableCoupon.expireDate, DATE_PERIOD_PANEL_TYPE.USABLE_COUPON_EXPIRATION_DATE.text, true);
            if (datePeriodText) conditionTextList.push(datePeriodText);
        }

        if (usableCoupon.count) {
            const config = COUNT_PANEL_TYPE.USABLE_COUPON_COUNT;
            conditionTextList.push(`${config.text} ${usableCoupon.count.fromCount}${config.unit} 이상 ${usableCoupon.count.toCount}${config.unit} 이하`);
        }

        if (usableCoupon.coupons && usableCoupon.coupons.length > 0) {
            const couponNames = usableCoupon.coupons.map(function(coupon) { return coupon.name; }).join(', ');
            conditionTextList.push('보유한 쿠폰: ' + couponNames);
        }

        return conditionTextList;
    }

    /**
     * 사용한 쿠폰 조건 설명 생성
     */
    function getUsedCouponDescription(usedCoupon) {
        const conditionTextList = [];

        if (usedCoupon.usedDate) {
            const datePeriodText = getDatePeriodText(usedCoupon.usedDate, DATE_PERIOD_PANEL_TYPE.USED_COUPON_DATE.text);
            if (datePeriodText) conditionTextList.push(datePeriodText);
        }

        if (usedCoupon.count) {
            const config = COUNT_PANEL_TYPE.USED_COUPON_COUNT;
            conditionTextList.push(`${config.text} ${usedCoupon.count.fromCount}${config.unit} 이상 ${usedCoupon.count.toCount}${config.unit} 이하`);
        }

        if (usedCoupon.coupons && usedCoupon.coupons.length > 0) {
            const couponNames = usedCoupon.coupons.map(function(coupon) { return coupon.name; }).join(', ');
            conditionTextList.push('사용한 쿠폰: ' + couponNames);
        }

        return conditionTextList;
    }

    /**
     * 소멸 예정 적립금 조건 설명 생성
     */
    function getExpireAccumulationDescription(expireAccumulation) {
        const conditionTextList = [];

        if (expireAccumulation.expireDate) {
            const datePeriodText = getDatePeriodText(expireAccumulation.expireDate, DATE_PERIOD_PANEL_TYPE.EXPIRING_ACCUMULATION_DATE.text);
            if (datePeriodText) conditionTextList.push(datePeriodText);
        }

        if (expireAccumulation.amount) {
            const config = COUNT_PANEL_TYPE.EXPIRING_ACCUMULATION_COUNT;
            conditionTextList.push(`${config.text} ${expireAccumulation.amount.fromCount}${config.unit} 이상 ${expireAccumulation.amount.toCount}${config.unit} 이하`);
        }

        return conditionTextList;
    }

    /**
     * CRM 그룹 조건 설명 텍스트 배열 생성
     * @param {Object} condition - extractCondition 객체
     * @returns {string[]} 조건 설명 텍스트 배열
     */
    function getConditionDescription(condition) {
        if (!condition) return [];

        let conditionTextList = [];

        if (condition.member) {
            conditionTextList = conditionTextList.concat(getMemberDescription(condition.member));
        }

        if (condition.usableAccumulation) {
            conditionTextList.push(getUsableAccumulationDescription(condition.usableAccumulation));
        }

        if (condition.cart) {
            conditionTextList = conditionTextList.concat(getCartDescription(condition.cart));
        }

        if (condition.order) {
            conditionTextList = conditionTextList.concat(getOrderDescription(condition.order));
        }

        if (condition.orderOptionPayDone) {
            conditionTextList = conditionTextList.concat(getOrderOptionPayDoneDescription(condition.orderOptionPayDone));
        }

        if (condition.orderOptionDeliveryDone) {
            conditionTextList = conditionTextList.concat(getOrderOptionDeliveryDoneDescription(condition.orderOptionDeliveryDone));
        }

        if (condition.orderOptionBuyConfirm) {
            conditionTextList = conditionTextList.concat(getOrderOptionBuyConfirmDescription(condition.orderOptionBuyConfirm));
        }

        if (condition.usableCoupon) {
            conditionTextList = conditionTextList.concat(getUsableCouponDescription(condition.usableCoupon));
        }

        if (condition.usedCoupon) {
            conditionTextList = conditionTextList.concat(getUsedCouponDescription(condition.usedCoupon));
        }

        if (condition.expireAccumulation) {
            conditionTextList = conditionTextList.concat(getExpireAccumulationDescription(condition.expireAccumulation));
        }

        return conditionTextList;
    }

    // Public API
    return {
        getConditionDescription: getConditionDescription
    };
})();
