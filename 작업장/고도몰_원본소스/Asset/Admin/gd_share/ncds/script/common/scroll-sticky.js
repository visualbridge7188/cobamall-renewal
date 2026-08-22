(function() {
    'use strict';

    const DEFAULTS = {
        panelSelector: '.ncua-panel',
        splitLayoutSelector: '.ncua-split-layout',
        sectionContainerSelector: '.section-container',
        pageHeaderSelector: '.page-header',
        splitLayoutMarginTop: 24,
    };

    /**
     * 스크롤 스티키 플러그인
     * @param {Object} [options]
     * @param {string} [options.panelSelector='.ncua-panel'] - 스티키 대상 패널 셀렉터
     * @param {string} [options.splitLayoutSelector='.ncua-split-layout'] - 분할 레이아웃 컨테이너 셀렉터
     * @param {string} [options.sectionContainerSelector='.section-container'] - 섹션 컨테이너 셀렉터
     * @param {string} [options.pageHeaderSelector='.page-header'] - 페이지 헤더 셀렉터
     * @param {number} [options.splitLayoutMarginTop=24] - splitLayout의 margin-block-start 값(px) 또는 헤더의 margin-bottom 값(px)
     * @returns {{ init: Function, destroy: Function }}
     */
    function createScrollSticky(options = {}) {
        const config = { ...DEFAULTS, ...options };

        const panel = document.querySelector(config.panelSelector);
        const splitLayout = document.querySelector(config.splitLayoutSelector);
        const section = document.querySelector(config.sectionContainerSelector);
        const header = document.querySelector(config.pageHeaderSelector);

        if (!panel || !splitLayout || !header) {
            return { init() {}, destroy() {} };
        }

        let panelWidth = panel.getBoundingClientRect().width;
        let isPanelFixed = false;
        let isPanelAbsolute = false;
        let lastAppliedTop = null;
        let lastAppliedRight = null;
        let rafId = null;
        let abortController = null;
        let mutationObserver = null;

        // 유틸
        const getStickyTop = () => {
            return header.offsetHeight + config.splitLayoutMarginTop;
        } 

        const calcPanelRight = () => {
            return document.documentElement.clientWidth - splitLayout.getBoundingClientRect().right;
        };

        const isStickyAvailable = () => {
            // fixed 상태에서는 패널이 flow에서 빠지므로 section 높이로 비교
            const containerHeight = (isPanelFixed && section)
                ? section.getBoundingClientRect().height
                : splitLayout.getBoundingClientRect().height;
            return containerHeight > panel.getBoundingClientRect().height;
        };

        const lockSectionWidth = () => {
            if (!section) return;
            // 고정폭(flex-shrink:0) 섹션은 패널이 flow 에서 빠져도 늘어나지 않으므로 잠금 불필요
            if (getComputedStyle(section).flexShrink === '0') return;
            const gap = parseInt(getComputedStyle(splitLayout).gap, 10) || 0;
            section.style.width = `${splitLayout.getBoundingClientRect().width - panelWidth - gap}px`;
        };

        const unlockSectionWidth = () => {
            if (!section) return;
            section.style.width = '';
            section.style.maxWidth = '';
        };

        /**
         * 패널/섹션의 인라인 스타일을 모두 초기화하여 flex 레이아웃으로 복원
         * resize 시 정확한 치수를 재측정하기 위한 전처리 단계
         */
        const releaseToFlex = () => {
            panel.style.cssText = '';
            if (section) {
                section.style.width = '';
                section.style.maxWidth = '';
            }
            isPanelFixed = false;
            isPanelAbsolute = false;
            lastAppliedTop = null;
            lastAppliedRight = null;
        };

        // 상태 전환 (relative -> fixed -> absolute)
        const setPanelRelative = () => {
            if (!isPanelFixed && !isPanelAbsolute) return;

            isPanelFixed = false;
            isPanelAbsolute = false;
            lastAppliedTop = null;
            lastAppliedRight = null;

            Object.assign(panel.style, { position: 'relative', top: '', right: '', bottom: '', width: '' });
            unlockSectionWidth();
        };

        const setPanelFixed = (top) => {
            const snappedTop = Math.round(top);
            const panelRight = Math.round(calcPanelRight());

            if (isPanelFixed && !isPanelAbsolute
                && lastAppliedTop === snappedTop
                && lastAppliedRight === panelRight) return;

            isPanelFixed = true;
            isPanelAbsolute = false;
            lastAppliedTop = snappedTop;
            lastAppliedRight = panelRight;

            Object.assign(panel.style, {
                position: 'fixed',
                top: `${snappedTop}px`,
                right: `${panelRight}px`,
                bottom: '',
                width: `${panelWidth}px`,
            });
            lockSectionWidth();
        };

        /**
         * 패널을 컨테이너 하단에 고정 (absolute + bottom: 0)
         * .ncua-content에 position: relative가 있어야 기준점으로 동작
         */
        const setPanelAbsoluteBottom = () => {
            if (isPanelAbsolute) return;

            isPanelFixed = false;
            isPanelAbsolute = true;
            lastAppliedTop = null;

            Object.assign(panel.style, {
                position: 'absolute',
                top: 'initial',
                right: '0',
                bottom: '0',
                width: `${panelWidth}px`,
            });
            lockSectionWidth();
        };

        const update = () => {
            if (!isStickyAvailable()) return;

            const layoutRect = splitLayout.getBoundingClientRect();
            const stickyTop = getStickyTop();
            const panelHeight = Math.round(panel.getBoundingClientRect().height);
            const clampTop = Math.round(layoutRect.bottom - panelHeight);
            const fixedTop = Math.min(clampTop, stickyTop);

            if (layoutRect.top > stickyTop) {
                setPanelRelative();
            } else if (clampTop < 0) {
                setPanelAbsoluteBottom();
            } else {
                setPanelFixed(fixedTop);
            }
        };

        const scheduleUpdate = () => {
            if (rafId) cancelAnimationFrame(rafId);
            rafId = requestAnimationFrame(update);
        };

        const handleResize = () => {
            releaseToFlex();
            panelWidth = panel.getBoundingClientRect().width;
            update();
        };

        return {
            init() {
                abortController = new AbortController();
                const { signal } = abortController;

                window.addEventListener('scroll', scheduleUpdate, { signal });
                window.addEventListener('resize', handleResize, { signal });

                if (section) {
                    mutationObserver = new MutationObserver(scheduleUpdate);
                    mutationObserver.observe(section, {
                        childList: true,
                        subtree: true,
                        attributes: true,
                        attributeFilter: ['style', 'class'],
                    });
                }

                update();
            },

            destroy() {
                if (rafId) { cancelAnimationFrame(rafId); rafId = null; }
                if (mutationObserver) { mutationObserver.disconnect(); mutationObserver = null; }
                if (abortController) { abortController.abort(); abortController = null; }
                setPanelRelative();
            },
        };
    }

    window.createScrollSticky = createScrollSticky;

    const autoInit = () => {
        const instance = createScrollSticky();
        instance.init();
    };

    const DOM_READY_STATES = ['complete', 'interactive'];

    if (DOM_READY_STATES.includes(document.readyState)) {
        autoInit();
    } else {
        document.addEventListener('DOMContentLoaded', autoInit);
    }
})();
