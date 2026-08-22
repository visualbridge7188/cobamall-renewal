/**
 * GodoCosGuide 상수 정의
 */

(function() {
    'use strict';

    /**
     * 가이드 콘텐츠 타입
     */
    const GUIDE_TYPES = {
        HELP: 'HELP',
        TOOLTIP: 'TOOLTIP'
    };

    /**
     * 툴팁 타입
     */
    const TOOLTIP_TYPES = {
        LONG: 'long',
        SHORT: 'short'
    };

    /**
     * DOM Ready 상태
     */
    const DOM_READY_STATES = ['complete', 'interactive'];

    const DEFAULT_OPTIONS = {
        // API 설정
        apiUrl: 'https://admin-api.nhn-commerce.com/cos/shop/guides/contents',
        guideCode: null,
        
        // DOM 선택자
        selectors: {
            helpMount: '#content .col-xs-12',
            tooltipTargets: '[data-tooltip-seq]'
        },
        
        // HTML 정제 함수
        sanitizeHtml: (html) => {
            return html;
        },
        
        // HTTP 설정
        headers: { version: '1.0' },
        timeout: 5000, // 5초
        
        // 콜백 함수
        onSuccess: () => {}
    };

    // 전역 노출
    window.GodoCosGuideConstants = {
        GUIDE_TYPES,
        TOOLTIP_TYPES,
        DOM_READY_STATES,
        DEFAULT_OPTIONS
    };
})();
