/**
 * NCDS Notification 관련 유틸리티 함수
 */
const NCDSNotification = {
    /**
     * 알림 닫기 기능
     * @param {string} selector - 알림 요소의 클래스명 selector (예: '.ncua-full-width-notification')
     */
    close: (selector) => {
        if (selector) {
            const notificationElement = document.querySelector(selector);
            if (notificationElement) {
                notificationElement.style.display = 'none';
            }
        }
    }
};

// 전역 함수로 노출
window.NCDSNotification = NCDSNotification;

