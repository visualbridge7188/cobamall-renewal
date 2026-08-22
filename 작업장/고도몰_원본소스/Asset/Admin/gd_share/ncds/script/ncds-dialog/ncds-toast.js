(function() {
    'use strict';

    /**
     * Toast Container ID
     */
    const TOAST_CONTAINER_ID = 'ncds-toast-container';

    /**
     * Container 가져오기 또는 생성
     * @returns {HTMLElement}
     */
    const getOrCreateContainer = () => {
        const htmlTextToElement = window.htmlTextToElement || ((htmlText) => {
            const template = document.createElement('template');
            template.innerHTML = htmlText.trim();
            return template.content.firstElementChild;
        });

        let container = document.getElementById(TOAST_CONTAINER_ID);
        if (!container) {
            container = htmlTextToElement(`<div id="${TOAST_CONTAINER_ID}"></div>`);
            document.body.appendChild(container);
        }
        return container;
    };

    /**
     * Toast 엘리먼트를 컨테이너로 이동
     * @param {HTMLElement} element - Toast 엘리먼트
     * @param {HTMLElement} container - 컨테이너 엘리먼트
     */
    const moveToContainer = (element, container) => {
        if (!element || element.parentNode === container) return;

        // body에서 제거
        element.parentNode?.removeChild(element);

        // container 맨 앞에 추가 (새 Toast가 위로)
        container.insertBefore(element, container.firstChild);
    };

    /**
     * NCDS Toast
     * @param {Object} options - 옵션 객체
     * @param {string} options.message - 메시지 제목
     * @param {string} [options.subMessage] - 메시지 부제목
     * @param {Object|Array} [options.actions] - 액션 버튼 설정
     * @param {Function} [options.onClose] - 닫기 시 실행할 콜백
     * @param {string} [options.color='warning'] - 토스트 색상 ('warning' | 'success' | 'error' | 'info')
     * @param {number} [options.autoClose=3000] - 자동 닫기 시간 (밀리초, 0이면 자동 닫기 없음)
     * @returns {Object} Notification 인스턴스
     */
    function NCDSToast(options = {}) {
        const {
            message,
            subMessage = '',
            actions = [],
            onClose = null,
            color = 'warning',
            autoClose = 3000,
            zIndex = 2000,
        } = options;

        const container = getOrCreateContainer();

        // NCDS Notification 생성 (원래 이름으로 전달)
        const notification = new window.ncua.Notification({
            type: 'floating',
            title: message,
            supportingText: subMessage,
            color,
            actions,
            className: 'ncua-content',
            onClose,
            autoClose,
            zIndex,
        });

        // show() 호출하여 notification 초기화
        notification.show();

        // show()가 body에 추가한 element를 container로 이동
        const element = notification.getElement();
        moveToContainer(element, container);

        return notification;
    }

    // 전역 노출
    window.NCDSToast = NCDSToast;
})();
