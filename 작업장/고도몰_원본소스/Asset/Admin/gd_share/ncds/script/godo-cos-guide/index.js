/**
 * COS Guide API Manager
 * 
 * COS(Commerce Operation System) API를 통해 Help, Tooltip 가이드 콘텐츠를
 * 페이지에 렌더링하는 모듈입니다.
 * 
 * @module GodoCosGuide
 * @version 1.1.0
 * @author NHN Commerce
 * 
 * @example
 * // 기본 사용법
 * GodoCosGuide.init({
 *   guideCode: '251023001'
 * });
 */
(function (window) {
    'use strict';

    // 상수 가져오기
    const Constants = window.GodoCosGuideConstants || {};
    const GUIDE_TYPES = Constants.GUIDE_TYPES || { HELP: 'HELP', TOOLTIP: 'TOOLTIP' };
    const TOOLTIP_TYPES = Constants.TOOLTIP_TYPES || { LONG: 'long', SHORT: 'short' };
    const DOM_READY_STATES = Constants.DOM_READY_STATES || ['complete', 'interactive'];
    const DEFAULT_OPTIONS = Constants.DEFAULT_OPTIONS || {
        apiUrl: 'https://admin-api.nhn-commerce.com/cos/shop/guides/contents',
        guideCode: null,
        tooltipOptions: {
            zIndex: 2000,
        },
        selectors: {
            helpMount: '#content .col-xs-12',
            tooltipTargets: '[data-tooltip-seq]'
        },
        sanitizeHtml: (html) => {
            return html;
        },
        headers: { version: '1.0' },
        timeout: 5000,
        onSuccess: () => {}
    };

    /**
     * COS Guide 메인 객체
     */
    const GodoCosGuide = {
        _opts: null,
        _isInitialized: false,
        _tooltipInstances: [],

        /**
         * 기본 옵션
         */
        defaultOptions: DEFAULT_OPTIONS,

        /**
         * 초기화
         * 
         * @param {Object} options - 사용자 정의 옵션
         * @returns {Promise} 초기화 완료 Promise
         */
        init(options = {}) {
            // 중복 초기화 방지

            // 옵션 병합
            this._opts = { ...this.defaultOptions, ...options, tooltipOptions: { ...this.defaultOptions.tooltipOptions, ...options.tooltipOptions } };

            // 선택자 병합 (deep merge)
            if (options.selectors) {
                this._opts.selectors = {
                    ...this.defaultOptions.selectors,
                    ...options.selectors
                };
            }


            this._removeExistingInfo();

            return this.fetch()
                .then((response) => {
                    this.apply(response);
                    this._opts.onSuccess();
                    return response;
                });
        },

        /**
         * DOM ready 상태 보장
         * 
         * @param {Function} fn - 실행할 함수
         */
        onReady(fn) {
            if (!fn || typeof fn !== 'function') {
                return;
            }

            if (DOM_READY_STATES.includes(document.readyState)) {
                fn();
            } else {
                document.addEventListener('DOMContentLoaded', fn);
            }
        },

        /**
         * API 데이터 가져오기
         * 
         * @returns {Promise<Array>} 가이드 데이터 배열
         */
        fetch() {
            const { apiUrl, guideCode, headers, timeout } = this._opts;
            
            if (!guideCode) {
                return Promise.resolve([]);
            }
            
            const url = `${apiUrl}?guideCode=${encodeURIComponent(guideCode)}`;

            // AbortController로 타임아웃 구현
            const controller = new AbortController();
            const timeoutId = setTimeout(() => controller.abort(), timeout);

            return fetch(url, {
                method: 'GET',
                headers: {
                    'Content-Type': 'application/json',
                    ...headers
                },
                credentials: 'include',
                signal: controller.signal
            })
            .then(response => {
                clearTimeout(timeoutId);

                if (!response.ok) {
                    throw {
                        status: response.status,
                        statusText: response.statusText,
                        message: this._getErrorMessage(response.status)
                    };
                }

                return response.json();
            })
            .catch(err => {
                clearTimeout(timeoutId);

                if (err.name === 'AbortError') {
                    throw {
                        status: 0,
                        statusText: 'Timeout',
                        message: '요청 시간이 초과되었습니다.'
                    };
                }

                throw err;
            });
        },

        /**
         * HTTP 상태 코드에 따른 에러 메시지
         * 
         * @private
         * @param {number} status - HTTP 상태 코드
         * @returns {string} 에러 메시지
         */
        _getErrorMessage(status) {
            const messages = {
                0: '네트워크 연결을 확인해주세요.',
                400: '잘못된 요청입니다.',
                401: '인증이 필요합니다.',
                403: '접근 권한이 없습니다.',
                404: '가이드 데이터를 찾을 수 없습니다.',
                500: '서버 오류가 발생했습니다.',
                503: '서비스를 일시적으로 사용할 수 없습니다.'
            };
            return messages[status] || `알 수 없는 오류 (${status})`;
        },

        /**
         * 응답 데이터 적용
         * 
         * @param {Array} response - API 응답 데이터
         */
        apply(response) {
            if (!Array.isArray(response) || response.length === 0) {
                return;
            }

            const { help, tooltips } = this._categorizeData(response);

            this.onReady(() => {
                if (help) this.renderHelp(help);
                if (tooltips.length > 0) this.renderTooltips(tooltips);
            });
        },

        /**
         * 데이터 타입별 분류
         * 
         * @private
         * @param {Array} data - 가이드 데이터
         * @returns {Object} 분류된 데이터
         */
        _categorizeData(data) {
            const help = data.find((item) => item.guideContentType === GUIDE_TYPES.HELP);
            const tooltips = data
                .filter((item) => item.guideContentType === GUIDE_TYPES.TOOLTIP)
                .map((item) => ({
                    ...item,
                    guideSeq: this._extractSeq(item.guideContentCode)
                }))
                .filter((item) => item.guideSeq !== null); // seq 없는 항목 제외

            return { help, tooltips };
        },

        /**
         * guideContentCode에서 seq 추출
         * 
         * @private
         * @param {string} code - 가이드 콘텐츠 코드 (예: "BOARD_2")
         * @returns {string|null} 추출된 seq
         */
        _extractSeq(code) {
            if (!code || typeof code !== 'string') {
                return null;
            }

            const parts = code.split('_');
            const last = parts[parts.length - 1];

            return /^\d+$/.test(last) ? last : null;
        },

        /**
         * HELP 렌더링
         * 
         * @param {Object} item - Help 데이터
         */
        renderHelp(item) {
            const { selectors } = this._opts;
            const mount = document.querySelector(selectors.helpMount);
            if (!mount) {
                return;
            }

            const existingHelp = mount.querySelector('[data-help-initialized="true"]');
            if (existingHelp) {
                return;
            }

            const sanitizedContent = this._opts.sanitizeHtml(item.guideBodyText || '');
            
            const html = `<div class="ncua-information" data-help-initialized="true">
                <h4>안내</h4>
                <div class="content">
                    ${sanitizedContent}
                </div>
            </div>`.replace(/>\s+</g, '><');

            mount.insertAdjacentHTML('beforeend', html);
        },

        /**
         * TOOLTIP 렌더링
         * 
         * @param {Array} items - Tooltip 데이터 배열
         */
        renderTooltips(items) {
            const { selectors } = this._opts;
            const targets = document.querySelectorAll(selectors.tooltipTargets);
            if(!targets) return;

            const notInitializedTargets = Array.from(targets).filter(target => target.getAttribute('data-tooltip-initialized') !== 'true');
            if (notInitializedTargets.length === 0 || !window.ncua?.Tooltip) {
                return;
            }

            // form submit 시 툴팁 닫기 기능
            this.bindAllForms();

            // seq → item 맵 생성
            const bySeq = new Map();
            items.forEach((item) => {
                if (item.guideSeq) {
                    bySeq.set(String(item.guideSeq), item);
                }
            });

            notInitializedTargets.forEach((element) => {
                
                const seq = String(element.getAttribute('data-tooltip-seq') || '');
                const iconType = element.getAttribute('data-tooltip-icon-type') || 'stroke';

                if (!seq || !bySeq.has(seq)) {
                    return;
                }

                const data = bySeq.get(seq);
                const { title, content } = this._extractTitleAndContent(data.guideBodyText);

                try {
                    const tooltipInstance = window.ncua.Tooltip.create({
                        type: TOOLTIP_TYPES[data.tooltipType] || TOOLTIP_TYPES.LONG,
                        tooltipType: 'white',
                        position: 'auto',
                        title,
                        content,
                        iconType: iconType,
                        zIndex: this._opts.tooltipOptions.zIndex,
                    });

                    element.appendChild(tooltipInstance.getElement());
                    
                    element.setAttribute('data-tooltip-initialized', 'true');
                    // 인스턴스 저장 (나중에 일괄 닫기용)
                    this._tooltipInstances.push(tooltipInstance);
                } catch (err) {
                    // 에러 무시
                }
            });
        },

        /**
         * HTML 콘텐츠에서 제목과 본문 추출
         * 
         * @private
         * @param {string} html - HTML 콘텐츠
         * @returns {Object} { title, content }
         */
        _extractTitleAndContent(html) {
            if (!html) {
                return { title: null, content: '' };
            }

            const temp = document.createElement('div');
            temp.innerHTML = html;

            const firstChild = temp.children[0];
            let title = null;

            if (firstChild && firstChild.tagName === 'H1') {
                title = firstChild.textContent;
                firstChild.remove();
            }

            const content = this._opts.sanitizeHtml(temp.innerHTML);

            return { title, content };
        },

        _removeExistingInfo() {
            this.onReady(() => {
                const { selectors } = this._opts;
                const mount = document.querySelector(selectors.helpMount);
                if (!mount) {
                    return;
                }
                const existingInfo = mount.querySelector('.information');
                if (existingInfo) {
                    existingInfo.remove();
                }
            });
        },

        /**
         * 모든 툴팁 닫기
         */
        hideAllTooltips() {
            this._tooltipInstances.forEach(instance => {
                try { 
                    if(!instance.hideTooltip) {
                        return;
                    }
                    instance.hideTooltip();
                } catch (err) {
                    // 에러 무시
                }
            });
        },

        /**
         * 폼 서브밋 시 툴팁 닫기 리스너 등록
         * 
         * @param {string|HTMLFormElement} formSelector - 폼 선택자 또는 폼 요소
         */
        bindFormSubmit(formSelector) {
            const form = typeof formSelector === 'string' 
                ? document.querySelector(formSelector) 
                : formSelector;

            if (!form) return;

            form.addEventListener('submit', () => {
                this.hideAllTooltips();
            });
        },

        /**
         * 모든 폼에 서브밋 리스너 자동 등록
         */
        bindAllForms() {
            this.onReady(() => {
                document.querySelectorAll('form').forEach(form => {
                    this.bindFormSubmit(form);
                });
            });
        },

        /**
         * 초기화 상태 리셋 (디버깅/테스트용)
         */
        reset() {
            this._isInitialized = false;
            this._opts = null;
            this._tooltipInstances = [];
        }
    };

    // 전역 노출
    window.GodoCosGuide = GodoCosGuide;

})(window);
