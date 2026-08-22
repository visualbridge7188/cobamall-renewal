(function() {
    'use strict';

    /**
     * Confirm 모달 생성 및 설정
     * @param {Object} params - 파라미터 객체
     * @param {Function} params.resolve - Promise resolve 함수
     * @param {string} params.message - 메시지 제목
     * @param {string} params.subMessage - 메시지 부제목
     * @param {Function} [params.callback] - 콜백 함수
     * @param {Object} params.btnText - 버튼 텍스트
     * @param {string} params.size - 모달 크기
     * @param {Function} params.createButton - 버튼 생성 함수
     * @param {HTMLElement} [params.focusTarget] - 포커스 대상 요소
     */
    function createConfirmModal({ resolve, message, subMessage, callback, btnText, size, createButton, zIndex, focusTarget, confirmHierarchy }) {
        const modal = new window.ncua.Modal({
            size,
            className: 'ncds-alert-modal',
            zIndex,
            closeOnBackdropClick: true,
        });

        const header = new window.ncua.Modal.Header({
            title: message,
            subtitle: subMessage,
            hideCloseButton: true
        });

        const actions = new window.ncua.Modal.Actions('', {
            layout: 'horizontal',
            align: 'center',
            fullWidth: true
        });

        const handleResult = (result) => {
            resolve(result);
            
            if (typeof callback === 'function') {
                callback(result);
            }
            
            modal.close();
        };

        // 버튼 생성
        const secondaryBtn = createButton(
            btnText.cancelLabel,
            'secondary-gray',
            () => handleResult(false)
        );

        const primaryBtn = createButton(
            btnText.confirmLabel,
            confirmHierarchy || 'primary',
            () => handleResult(true)
        );

        actions.setContent([secondaryBtn, primaryBtn]);
        header.setCloseHandler(() => handleResult(false));

        const modalEl = modal.getModalElement();
        header.appendTo(modalEl);
        actions.appendTo(modalEl);
        modal.open();

        // 모달 열린 후 버튼에 포커스
        const focusElement = focusTarget || primaryBtn;
        requestAnimationFrame(() => focusElement?.focus());
    }

    /**
     * NCDS Confirm
     * @param {Object} options - 옵션 객체
     * @param {string} options.message - 메시지 제목
     * @param {string} [options.subMessage] - 메시지 부제목
     * @param {Function} [options.callback] - 버튼 클릭 시 실행할 콜백 (result: true=확인, false=취소)
     * @param {Object} [options.btnText] - 버튼 텍스트 { confirmLabel: string, cancelLabel: string }
     * @param {string} [options.size='md'] - 모달 크기 ('sm' | 'md' | 'lg' | 'xl')
     * @param {HTMLElement} [options.focusTarget] - 포커스 대상 요소
     * @returns {Promise<boolean>} Promise 객체 (true=확인, false=취소)
     */
    function NCDSConfirm(options = {}) {
        const createButton = ((label, hierarchy, onClick, size = 'sm') => {
            if(window.createButton) { // window.createButton 가 있는 경우 사용
                return window.createButton(label, hierarchy, onClick, size);
            }

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
            btnText = {
                confirmLabel: '확인',
                cancelLabel: '취소'
            },
            size = 'sm',
            zIndex = 2000,
            focusTarget = null,
            confirmHierarchy = 'primary',
        } = options;

        return new Promise((resolve) => {
            createConfirmModal({
                resolve,
                message,
                subMessage,
                callback,
                btnText,
                size,
                createButton,
                zIndex,
                focusTarget,
                confirmHierarchy,
            });
        });
    }

    // 전역 노출
    window.NCDSConfirm = NCDSConfirm;
})();
