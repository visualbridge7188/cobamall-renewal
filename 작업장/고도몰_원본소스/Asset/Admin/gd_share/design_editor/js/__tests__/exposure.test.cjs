/**
 * gd_swiper.js 노출기간 날짜 술어 단위 테스트
 *
 * 슬라이드 노출기간 만료/개시는 gd_swiper.js 의 런타임 필터(filterExposedSlides)가 담당하며,
 * 그 판정 핵심은 순수 함수 isWithinExposurePeriod(+parseExposureDate) 이다. PHPUnit 은 host 렌더의
 * data-exposure-* emit 계약만 검증하므로, JS 날짜 술어 회귀를 막을 가드가 없었다.
 *
 * 본 테스트는 프레임워크 없이 node 단독 실행(`node exposure.test.cjs`)되며, gd_swiper.js 의 실코드를
 * 그대로 require 하여(드리프트 0) 7 케이스를 검증한다. 실패 시 assert 가 throw → 프로세스 비정상 종료.
 */
const assert = require('node:assert');
const { parseExposureDate, isWithinExposurePeriod } = require('../gd_swiper.js');

// now 기준 상대 날짜 문자열 생성 (Date() 모킹 없이 결정적 검증)
const pad = (n) => String(n).padStart(2, '0');
const fmt = (d, dateOnly) => {
    const base = `${d.getFullYear()}-${pad(d.getMonth() + 1)}-${pad(d.getDate())}`;
    return dateOnly ? base : `${base} ${pad(d.getHours())}:${pad(d.getMinutes())}`;
};
const now = new Date();
const DAY = 24 * 60 * 60 * 1000;
const past = new Date(now.getTime() - 2 * DAY);
const future = new Date(now.getTime() + 2 * DAY);
const yesterday = new Date(now.getTime() - DAY);

let passed = 0;
const check = (name, fn) => {
    fn();
    passed += 1;
    console.log(`  ✓ ${name}`);
};

// --- parseExposureDate ---
check('parseExposureDate: YYYY-MM-DD HH:MM 파싱', () => {
    const d = parseExposureDate('2026-06-16 09:30');
    assert.ok(d instanceof Date && !Number.isNaN(d.getTime()));
    assert.strictEqual(d.getFullYear(), 2026);
    assert.strictEqual(d.getMonth(), 5); // 0-base
    assert.strictEqual(d.getDate(), 16);
    assert.strictEqual(d.getHours(), 9);
    assert.strictEqual(d.getMinutes(), 30);
});
check('parseExposureDate: 빈/형식오류 → null', () => {
    assert.strictEqual(parseExposureDate(''), null);
    assert.strictEqual(parseExposureDate('garbage'), null);
    assert.strictEqual(parseExposureDate(null), null);
});

// --- isWithinExposurePeriod ---
check('미설정(null,null) → 상시 노출(true)', () => {
    assert.strictEqual(isWithinExposurePeriod('', ''), true);
});
check('in-window(과거 시작·미래 종료) → true', () => {
    assert.strictEqual(isWithinExposurePeriod(fmt(past), fmt(future)), true);
});
check('만료(과거 종료) → false', () => {
    assert.strictEqual(isWithinExposurePeriod('', fmt(past)), false);
});
check('미시작(미래 시작) → false', () => {
    assert.strictEqual(isWithinExposurePeriod(fmt(future), ''), false);
});
check('시작만(과거) → true / 종료만(미래) → true', () => {
    assert.strictEqual(isWithinExposurePeriod(fmt(past), ''), true);
    assert.strictEqual(isWithinExposurePeriod('', fmt(future)), true);
});
check('date-only 종료일=오늘 → 23:59:59 포함(true)', () => {
    assert.strictEqual(isWithinExposurePeriod('', fmt(now, true)), true);
});
check('date-only 종료일=어제 → 어제 23:59:59 경과(false)', () => {
    assert.strictEqual(isWithinExposurePeriod('', fmt(yesterday, true)), false);
});

console.log(`\n${passed} passed`);
