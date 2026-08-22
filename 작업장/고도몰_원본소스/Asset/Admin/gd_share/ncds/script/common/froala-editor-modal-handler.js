/**
 * Froala Editor Modal Handler
 * 
 * 레이어/모달로 열린 페이지에서 Froala Editor를 사용할 때
 * Bootstrap Modal의 focusin 이벤트를 자동으로 제어하는 유틸리티
 * 
 * @module FroalaEditorModalHandler
 */
(function (window) {
    'use strict';

    // 최상위 창 가져오기
    const topWindow = window.top || window;

    if (typeof topWindow.BsModalFocusinToggle === 'undefined') {
        return;
    }

    /**
     * 레이어/모달로 열렸는지 확인
     * @param {HTMLElement} targetElement - 확인할 대상 요소 (선택사항)
     * @returns {boolean} 레이어에 감싸져 있는지 여부
     */
    function isInLayer(targetElement) {
        // BootstrapDialog로 열린 경우
        if (targetElement) {
            const modalContainer = targetElement.closest('.bootstrap-dialog');
            if (modalContainer) {
                return true;
            }
        }

        // window.opener가 있는 경우 (팝업)
        if (window.opener) {
            return true;
        }

        return false;
    }

    /**
     * 모달 핸들러 초기화
     * @param {Object} options - 옵션 객체
     * @param {string|HTMLElement} [options.targetSelector] - 확인할 대상 요소 선택자 또는 요소
     */
    function init(options) {
        options = options || {};
        const targetSelector = options.targetSelector;
        
        // targetSelector가 빈 문자열인 경우 처리
        if (!targetSelector) {
            return;
        }

        let targetElement = null;
        if (targetSelector) {
            if (typeof targetSelector === 'string') {
                targetElement = document.querySelector(targetSelector);
            } else if (targetSelector instanceof HTMLElement) {
                targetElement = targetSelector;
            }
        }

        // 레이어에 감싸져 있으면 disable 실행
        function initModalHandler() {
            if (!targetElement && targetSelector) {
                // targetSelector가 지정되었지만 요소를 찾지 못한 경우
                return;
            }

            if (isInLayer(targetElement)) {
                if (topWindow.BsModalFocusinToggle && typeof topWindow.BsModalFocusinToggle.disable === 'function') {
                    topWindow.BsModalFocusinToggle.disable();
                }
            }
        }

        /**
         * fr-popup fr-active 클래스를 가진 요소들 모두 삭제
         */
        function removeFroalaPopups() {
            try {
                const popupElements = document.querySelectorAll('.fr-popup.fr-active');
                if (popupElements && popupElements.length > 0) {
                    popupElements.forEach(function(popupElement) {
                        if (popupElement && popupElement.parentNode) {
                            popupElement.remove();
                        }
                    });
                }
            } catch (e) {
                // 에러 발생 시 무시
            }
        }

        /**
         * restore 및 Froala 팝업 정리 (중복 로직 통합)
         */
        function cleanup() {
            try {
                if (topWindow.BsModalFocusinToggle && typeof topWindow.BsModalFocusinToggle.restore === 'function') {
                    topWindow.BsModalFocusinToggle.restore();
                }
                removeFroalaPopups();
            } catch (e) {
                // 에러 발생 시 무시
            }
        }

        // 팝업창이 사라질 때 restore 실행
        function setupRestoreHandler() {
            // beforeunload 이벤트: 창이 닫힐 때
            window?.addEventListener?.('beforeunload', cleanup);

            // BootstrapDialog가 닫힐 때 (jQuery 이벤트 리스너 사용)
            if (typeof window.$ !== 'undefined') {
                window.$(document).on('hidden.bs.modal', '.bootstrap-dialog', function(event) {
                    cleanup();
                });
            }
        }

        // DOM 로드 완료 후 실행
        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', function() {
                initModalHandler();
                setupRestoreHandler();
            });
        } else {
            initModalHandler();
            setupRestoreHandler();
        }
    }

    // 전역 노출
    window.FroalaEditorModalHandler = {
        init: init,
        isInLayer: isInLayer
    };

})(window);
