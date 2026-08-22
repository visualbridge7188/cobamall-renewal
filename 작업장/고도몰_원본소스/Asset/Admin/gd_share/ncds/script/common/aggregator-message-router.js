/**
 * iframe postMessage 라우터
 *
 * TargetRoute에 정의된 target으로 postMessage가 오면 해당 URL로 자동 이동합니다.
 * RELOAD 타입은 별도 설정 없이 항상 동작합니다.
 *
 * @example
 * // 기본 사용 - 설정 없이 모든 target 자동 라우팅
 * IframeMessageRouter.init();
 *
 * // MODAL_CLOSE 핸들링이 필요한 경우만 콜백 전달
 * IframeMessageRouter.init({
 *     onModalClose: function(payload) { opener.selectCreatedCrmGroup(payload.no, payload.title); window.close(); }
 * });
 *
 * // LAYER_OPEN/CLOSE 를 호스트가 원하는 방식으로 열고 닫기 (반드시 등록 — 미설정 시 LAYER_OPEN 은 무시됨)
 * IframeMessageRouter.init({
 *     onLayerOpen: function(url, payload) { ... url 을 원하는 레이어로 표시 ... },
 *     onLayerClose: function(payload) { ... 레이어 닫기 ... }
 * });
 */
(function () {
    'use strict';

    // 모듈이 한 문서에서 두 번 평가돼도 라우터·리스너를 중복 생성하지 않도록 가드.
    if (window.IframeMessageRouter) return;

    const MessageType = {
        NAVIGATION: 'NAVIGATION',
        NAVIGATION_NEW_TAB: 'NAVIGATION_NEW_TAB',
        RELOAD: 'RELOAD',
        MODAL_OPEN: 'MODAL_OPEN',
        MODAL_CLOSE: 'MODAL_CLOSE',
        LAYER_OPEN: 'LAYER_OPEN',
        LAYER_CLOSE: 'LAYER_CLOSE',
        PERMISSION_CHECK: 'PERMISSION_CHECK'
    };

    const TargetRoute = {
        // CRM
        CRM_GROUP_RESULT: '/crm/crm_group_result.php',
        CRM_GROUP_CREATE: '/crm/create_crm_group.php',
        CRM_GROUP_CREATE_POPUP: '/crm/popup_create_crm_group.php',
        CRM_GROUP_EDIT: '/crm/crm_group_edit.php',
        CRM_GROUP_LIST: '/crm/crm_group.php',
        RESERVATION_SHIPMENT_DETAIL: '/crm/scheduled_send_reservation_detail.php',
        REPEAT_SHIPMENT_DETAIL: '/crm/scheduled_send_repeat_detail.php',
        SCHEDULED_SEND_LIST: '/crm/scheduled_send.php',
        MESSAGE_PERFORMANCE_LIST: '/crm/mobile_message_performance_insight.php',
        MESSAGE_PERFORMANCE_DETAIL: '/crm/popup_mobile_message_performance_detail.php',
        MESSAGE_PERFORMANCE_INSIGHT: '/crm/mobile_message_performance.php',
        MOBILE_HISTORY: '/crm/mobile_history_list.php',
        MOBILE_SEND: '/crm/mobile_send.php',
        // CRM 레시피 등록/재등록 모달
        CRM_RECIPE_SEND: '/crm/crm_recipe_send.php',
        // PROMOTION
        COUPON_LIST: '/promotion/coupon_list.php',
        COUPON_REGISTER: '/promotion/coupon_regist.php',
        // GOODS
        PRODUCT_REGISTER: '/goods/goods_register.php',
        // SHARE
        MEMBER_CRM: '/share/member_crm.php',
    };

    window.IframeMessageRouter = {
        _config: {},

        init: function (config) {
            if (config) this._config = config;
            // 리스너는 한 번만 바인딩 — 호스트가 콜백(onModalClose/onLayerOpen 등) 설정 위해 init 을 다시 호출해도 안전
            if (this._bound) return;
            this._bound = true;
            window.addEventListener('message', this._handleMessage.bind(this));
        },

        _handleMessage: function (event) {
            const type = event.data && event.data.type;
            const payload = event.data && event.data.payload || {};

            if (event.data.category === MessageType.RELOAD) {
                window.location.reload();
                return;
            }

            switch (type) {
                case MessageType.NAVIGATION:
                    this._handleNavigation(payload);
                    break;
                case MessageType.NAVIGATION_NEW_TAB:
                    this._handleNavigationNewTab(payload);
                    break;
                case MessageType.MODAL_OPEN:
                    this._handleModalOpen(payload);
                    break;
                case MessageType.MODAL_CLOSE:
                    this._handleModalClose(payload);
                    break;
                case MessageType.LAYER_OPEN:
                    this._handleLayerOpen(payload);
                    break;
                case MessageType.LAYER_CLOSE:
                    this._handleLayerClose(payload);
                    break;
                case MessageType.PERMISSION_CHECK:
                    this._handlePermissionCheck(event);
                    break;
            }
        },

        _handleNavigation: function (payload) {
            const url = TargetRoute[payload.target];
            if (!url) return;

            window.location.href = this._appendQuery(url, payload.query);
        },

        _handleNavigationNewTab: function (payload) {
            const url = TargetRoute[payload.target];
            if (!url) return;

            window.open(this._appendQuery(url, payload.query), '_blank');
        },

        _handleModalOpen: function (payload) {
            const url = TargetRoute[payload.target] || payload.url || '';

            this._openPopup({ url: this._appendQuery(url, payload.query), width: payload.width || 1920, height: payload.height || 1000 });
        },

        _handleModalClose: function (payload) {
            const onModalClose = this._config.onModalClose;
            if (typeof onModalClose === 'function') {
                onModalClose(payload);
            }
        },

        // 레이어(페이지 내 오버레이) — 팝업창(MODAL_OPEN)과 달리 현재 페이지 위에 띄움.
        // 열기: { type:'LAYER_OPEN', payload:{ target|url, query, ... } } / 닫기: { type:'LAYER_CLOSE' }
        // 모달 스타일·여닫는 방식은 전적으로 호스트가 정의 — init({ onLayerOpen(url, payload), onLayerClose(payload) }) 로 등록.
        // 라우터는 url 만 만들어 그 메서드를 실행할 뿐, DOM/스타일에는 관여하지 않음. 미등록이면 LAYER_OPEN 은 경고 후 무시.
        _handleLayerOpen: function (payload) {
            if (typeof this._config.onLayerOpen !== 'function') {
                console.warn('IframeMessageRouter: LAYER_OPEN 수신 — init({ onLayerOpen }) 미등록으로 무시됨');
                return;
            }
            const url = this._appendQuery(TargetRoute[payload.target] || payload.url || '', payload.query);
            this._config.onLayerOpen(url, payload);
        },

        _handleLayerClose: function (payload) {
            if (typeof this._config.onLayerClose === 'function') {
                this._config.onLayerClose(payload);
            }
        },

        // CRM 레시피(hub) 권한체크 요청 수신 → 권한 판단 후 결과 회신
        // 동작별 권한 판단 → 거부 사유(없으면 null = 통과)는 호스트 콜백(onPermissionCheck)이 수행
        // 요청: { category:'CRM_RECIPE', type:'PERMISSION_CHECK', payload:{ action, requestId } }
        // 응답: { category:'CRM_RECIPE', type:'PERMISSION_CHECK', payload:{ requestId, granted, reason } }
        _handlePermissionCheck: function (event) {
            if (typeof this._config.onPermissionCheck === 'function') {
                this._config.onPermissionCheck(event);
            }
        },

        _appendQuery: function (url, query) {
            if (!query) return url;
            const qs = new URLSearchParams(query).toString();
            if (!qs) return url;
            return url + (url.indexOf('?') === -1 ? '?' : '&') + qs;
        },

        _openPopup: function (config) {
            const win = popup({
                url: config.url,
                target: '',
                width: config.width,
                height: config.height,
                scrollbars: 'yes',
                resizable: 'yes'
            });

            if (win) win.focus();
            return win;
        }
    };

    IframeMessageRouter.init();
})();
