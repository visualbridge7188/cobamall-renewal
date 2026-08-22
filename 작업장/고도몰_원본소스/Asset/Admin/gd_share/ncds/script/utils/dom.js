/**
 * DOM 조작 유틸리티
 */

(function() {
    'use strict';

    /**
     * HTML 문자열을 DOM 엘리먼트로 변환
     * @param {string} html - HTML 문자열
     * @returns {HTMLElement}
     */
    const htmlTextToElement = (htmlText) => {
        const template = document.createElement('template');
        template.innerHTML = htmlText.trim();
        return template.content.firstElementChild;
    };

    // 전역 노출
    window.htmlTextToElement = htmlTextToElement;
})();

