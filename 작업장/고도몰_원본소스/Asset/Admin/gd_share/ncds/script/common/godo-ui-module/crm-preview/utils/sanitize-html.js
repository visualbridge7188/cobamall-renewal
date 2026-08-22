/**
 * CrmSanitizer - XSS 방지를 위한 HTML 정화 유틸리티
 * DOMParser를 사용하여 위험한 태그/속성을 제거하고 안전한 HTML만 허용
 */
const CrmSanitizer = (function () {
    'use strict';

    const DANGEROUS_TAGS = [
        'script', 'iframe', 'object', 'embed', 'form',
        'input', 'textarea', 'select', 'link', 'style',
        'meta', 'base', 'svg', 'math', 'noscript'
    ];

    const URL_ATTRS = ['href', 'src', 'action', 'formaction', 'xlink:href', 'data'];

    /**
     * HTML 문자열에서 위험 요소를 제거하고 안전한 HTML을 반환
     * @param {string} html
     * @returns {string}
     */
    function sanitizeHTML(html) {
        if (typeof html !== 'string') return '';
        if (!html.trim()) return html;

        const parser = new DOMParser();
        const doc = parser.parseFromString(html, 'text/html');

        DANGEROUS_TAGS.forEach(function (tag) {
            const elements = doc.querySelectorAll(tag);
            for (let i = 0; i < elements.length; i++) {
                elements[i].remove();
            }
        });

        const allElements = doc.querySelectorAll('*');
        for (let i = 0; i < allElements.length; i++) {
            const el = allElements[i];
            const attrs = [];
            for (let j = 0; j < el.attributes.length; j++) {
                attrs.push(el.attributes[j].name);
            }
            for (let k = 0; k < attrs.length; k++) {
                const attrName = attrs[k].toLowerCase();
                const attrValue = (el.getAttribute(attrs[k]) || '').trim().toLowerCase();

                if (attrName.indexOf('on') === 0) {
                    el.removeAttribute(attrs[k]);
                    continue;
                }
                if (URL_ATTRS.indexOf(attrName) !== -1 && attrValue.indexOf('javascript:') === 0) {
                    el.removeAttribute(attrs[k]);
                }
            }
        }

        return doc.body.innerHTML;
    }

    /**
     * HTML 특수문자 이스케이프
     * @param {string} str
     * @returns {string}
     */
    function escapeHTML(str) {
        if (typeof str !== 'string') return str;
        return str
            .replace(/&/g, '&amp;')
            .replace(/</g, '&lt;')
            .replace(/>/g, '&gt;')
            .replace(/"/g, '&quot;')
            .replace(/'/g, '&#039;');
    }

    return {
        sanitizeHTML: sanitizeHTML,
        escapeHTML: escapeHTML
    };
})();

// CommonJS/AMD 지원
if (typeof module !== 'undefined' && module.exports) {
    module.exports = CrmSanitizer;
}
