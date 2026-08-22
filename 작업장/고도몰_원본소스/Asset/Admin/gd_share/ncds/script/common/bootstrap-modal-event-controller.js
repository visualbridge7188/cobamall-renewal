/**
 * Bootstrap Modal Event Controller
 * 
 * Bootstrap Modal의 focusin 이벤트를 제어하는 유틸리티
 * 
 * @module BsModalFocusinToggle
 */
(function (window) {
    'use strict';

    // 최상위 창 가져오기
    const topWindow = window.top || window;

    // 최상위 창의 jQuery 확인
    if (typeof topWindow.$ === 'undefined') {
        return;
    }

    const BsModalFocusinToggle = (() => {
        let saved = null;

        function snapshot() {
            if (saved) return;
            // 최상위 창의 document에서 이벤트 확인
            const list = topWindow.$._data(topWindow.document, 'events')?.focusin || [];
            saved = list.filter(h => h.namespace === 'bs.modal')
                        .map(h => ({ handler: h.handler, selector: h.selector }));
        }

        function disable() {
            // 모달 실행시 이벤트 재실행되어 모달실행 이후 100ms 대기후 이벤트 제거
            setTimeout(() => {
                snapshot();
                // 최상위 창의 document에서 이벤트 제거
                topWindow.$(topWindow.document).off('focusin.bs.modal');
            }, 100);
        }

        function restore() {
            if (!saved) return;
            // selector가 있으면 delegate 형태로 복구
            saved.forEach(h => {
                if (h.selector) {
                    topWindow.$(topWindow.document).on('focusin.bs.modal', h.selector, h.handler);
                } else {
                    topWindow.$(topWindow.document).on('focusin.bs.modal', h.handler);
                }
            });
        }

        return { disable, restore };
    })();

    // 최상위 창에 전역 노출
    topWindow.BsModalFocusinToggle = BsModalFocusinToggle;

})(window);
