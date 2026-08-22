/**
 * 스크롤 유틸리티
 * HTML 교체 후 특정 요소로 스크롤 이동하는 기능 제공
 */

(() => {
    'use strict';

    /**
     * HTML 교체 후 특정 요소로 스크롤 이동
     * @param {jQuery|HTMLElement|string} container - 컨테이너 요소 (jQuery 객체, DOM 요소, 또는 선택자)
     * @param {jQuery|HTMLElement|string} targetElement - 스크롤할 대상 요소 (jQuery 객체, DOM 요소, 또는 선택자)
     * @param {Object} options - 옵션 객체
     * @param {number} options.offset - 상단 여백 (기본값: 20)
     * @param {string} options.scrollContainerSelector - 스크롤 컨테이너 선택자 (기본값: '.modal-dialog__content, .bootstrap-dialog-body, .modal-body')
     * @param {boolean} options.useAnimation - 애니메이션 사용 여부 (기본값: false, 즉시 이동)
     */
    window.scrollToElementAfterHtmlReplace = (container, targetElement, options = {}) => {
        const {
            offset = 20,
            scrollContainerSelector = '.modal-dialog__content, .bootstrap-dialog-body, .modal-body',
            useAnimation = false
        } = options;

        // jQuery 객체로 변환
        const $container = $(container);
        const $targetElement = typeof targetElement === 'string' ? $container.find(targetElement) : $(targetElement);

        if ($targetElement.length === 0) {
            return;
        }

        // 스크롤 컨테이너 찾기
        let $scrollContainer = $container.closest(scrollContainerSelector);

        // HTML 교체 직후 스크롤 위치 설정 (최상단 이동 방지)
        requestAnimationFrame(() => {
            requestAnimationFrame(() => {
                // 스크롤 컨테이너를 다시 찾기 (HTML 교체 후)
                if ($scrollContainer.length === 0) {
                    $scrollContainer = $container.closest(scrollContainerSelector);
                }

                if ($scrollContainer.length > 0 && $scrollContainer[0].scrollHeight > $scrollContainer[0].clientHeight) {
                    // 스크롤 가능한 컨테이너가 있는 경우
                    const containerRect = $scrollContainer[0].getBoundingClientRect();
                    const resultRect = $targetElement[0].getBoundingClientRect();
                    const scrollTop = $scrollContainer.scrollTop() + (resultRect.top - containerRect.top) - offset;

                    if (useAnimation) {
                        $scrollContainer.animate({
                            scrollTop: Math.max(0, scrollTop)
                        }, 300);
                    } else {
                        $scrollContainer.scrollTop(Math.max(0, scrollTop));
                    }
                } else {
                    // 스크롤 컨테이너를 찾지 못한 경우, scrollIntoView 사용
                    $targetElement[0].scrollIntoView({ 
                        behavior: useAnimation ? 'smooth' : 'instant', 
                        block: 'start' 
                    });
                }
            });
        });
    };
})();

