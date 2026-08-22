(function() {
    'use strict';

    /**
     * NCDS Alert 아이콘 SVG
     */
    const NCDS_ALERT_ICONS = {
    error: '<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="none"><path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v4m0 4h.01M10.615 3.892 2.39 18.098c-.456.788-.684 1.182-.65 1.506a1 1 0 0 0 .406.705c.263.191.718.191 1.629.191h16.45c.91 0 1.365 0 1.628-.191a1 1 0 0 0 .407-.705c.034-.324-.195-.718-.65-1.506L13.383 3.892c-.454-.785-.681-1.178-.978-1.31a1 1 0 0 0-.812 0c-.297.132-.524.525-.979 1.31"></path></svg>',
    success: '<svg xmlns="http://www.w3.org/2000/svg"fill="none" viewBox="0 0 24 24" stroke="none"><path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m7.5 12 3 3 6-6m5.5 3c0 5.523-4.477 10-10 10S2 17.523 2 12 6.477 2 12 2s10 4.477 10 10"></path></svg>',
    warning: '<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="none"><path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m15 9-6 6m0-6 6 6m7-3c0 5.523-4.477 10-10 10S2 17.523 2 12 6.477 2 12 2s10 4.477 10 10"></path></svg>',
    info: '<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="none"><path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 16v-4m0-4h.01M22 12c0 5.523-4.477 10-10 10S2 17.523 2 12 6.477 2 12 2s10 4.477 10 10"></path></svg>'
};

/**
 * 아이콘 색상 맵
 */
const ICON_COLOR_MAP = {
    error: 'error',
    success: 'success',
    warning: 'warning',
    info: 'neutral'
};

    /**
     * NCDS Alert
     * @param {Object} options - 옵션 객체
     * @param {string} options.message - 메시지 제목
     * @param {string} [options.subMessage] - 메시지 부제목
     * @param {Function} [options.callback] - 확인 버튼 클릭 시 실행할 콜백
     * @param {string} [options.iconType] - 아이콘 타입 ('error' | 'success' | 'warning' | 'info')
     * @param {Object} [options.btnText] - 버튼 텍스트 { confirmLabel: string }
     * @param {string} [options.size='md'] - 모달 크기 ('sm' | 'md' | 'lg' | 'xl')
     * @param {string} [options.buttonAlign='right'] - 버튼 정렬 ('stretch' | 'center' | 'right' | 'left')
     * @param {HTMLElement} [options.focusTarget] - 포커스 대상 요소
     */
    function NCDSAlert(options = {}) {
        const createButton = ((label, hierarchy, onClick, size = 'sm') => {
            if(window.createButton) { // window.createButton 가 있는 경우 사용
                return window.createButton(label, hierarchy, onClick, size);
            }

            // window.createButton 가 없는 경우 직접 버튼 생성
            const htmlTextToElement = window.htmlTextToElement || ((htmlText) => {
                const template = document.createElement('template');
                template.innerHTML = htmlText.trim();
                return template.content.firstElementChild;
            });

            const button = htmlTextToElement(`
                <button class="ncua-btn ncua-btn--${size} ncua-btn--${hierarchy}">
                    <span class="ncua-btn__label">${label}</span>
                </button>
            `);
            button.addEventListener('click', onClick);
            return button;
        });

        const {
            message,
            subMessage = '',
            callback,
            iconType,
            btnText = { confirmLabel: '확인' },
            size = 'sm',
            buttonAlign = 'right',
            zIndex = 2000,
            focusTarget = null,
        } = options;

        const modal = new window.ncua.Modal({
            size,
            className: 'ncds-alert-modal',
            zIndex,
            closeOnBackdropClick: true,
        });

        // Header 옵션 구성 (modal에는 원래 이름으로 전달)
        const headerOptions = {
            title: message,
            subtitle: subMessage,
            hideCloseButton: true,
        };

        // 아이콘이 지정된 경우 featuredIcon 추가
        if (iconType && NCDS_ALERT_ICONS[iconType]) {
            headerOptions.featuredIcon = {
                svgString: NCDS_ALERT_ICONS[iconType],
                theme: 'light-circle',
                color: ICON_COLOR_MAP[iconType] || 'neutral',
                size: 'sm'
            };
        }

        const header = new window.ncua.Modal.Header(headerOptions);
        const actions = new window.ncua.Modal.Actions('', {
            layout: 'horizontal',
            align: buttonAlign
        });

        // 버튼 생성
        const primaryBtn = createButton(
            btnText.confirmLabel,
            'primary',
            () => {
                if (typeof callback === 'function') {
                    callback();
                }
                modal.close();
            }
        );

        actions.setContent([primaryBtn]);
        header.setCloseHandler(() => modal.close());

        const modalEl = modal.getModalElement();
        header.appendTo(modalEl);
        actions.appendTo(modalEl);
        modal.open();

        // 모달 열린 후 버튼에 포커스
        const focusElement = focusTarget || primaryBtn;
        requestAnimationFrame(() => focusElement?.focus());

        return modal;
    }

    // 전역 노출
    window.NCDSAlert = NCDSAlert;
    window.NCDS_ALERT_ICONS = NCDS_ALERT_ICONS;
})();
