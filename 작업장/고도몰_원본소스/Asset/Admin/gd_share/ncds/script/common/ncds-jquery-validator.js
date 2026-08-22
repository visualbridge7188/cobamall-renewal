/**
 * jQuery Validator NCDS 스타일 적용
 * NCDS 컴포넌트에 맞는 에러 하이라이트 처리
 */
class NCDSValidator {
    static ERROR_CLASS_NAME = {
        default: 'destructive'
    };
    static HINT_TEXT_CLASS_NAME = 'ncua-hint-text ncua-input__hint-text';

    /**
     * 힌트 텍스트 HTML 템플릿 생성
     * @returns {HTMLElement} 힌트 텍스트 요소
     */
    static createHintTextElement() {
        const hintText = document.createElement('span');
        hintText.className = this.HINT_TEXT_CLASS_NAME;
        return hintText;
    }

    /**
     * 사이즈별 아이콘 크기 매핑
     */
    static ICON_SIZE_MAP = {
        xs: 14,
        sm: 16,
        default: 16
    };

    /**
     * 에러 아이콘 HTML 템플릿 생성
     * @param {number} size - 아이콘 크기 (px)
     * @returns {string} 아이콘 HTML
     */
    static getErrorIconHTML(size = 16) {
        return `
            <div class="ncua-input__icon-wrap ncua-input__right-icon ncua-input__destructive-icon-wrap">
                <svg
                    xmlns="http://www.w3.org/2000/svg"
                    width="${size}"
                    height="${size}"
                    fill="none"
                    viewBox="0 0 24 24"
                    stroke="none"
                    class="ncua-input__destructive-icon">
                    <path
                        stroke="currentColor"
                        stroke-linecap="round"
                        stroke-linejoin="round"
                        stroke-width="2"
                        d="M12 16v-4m0-4h.01M22 12c0 5.523-4.477 10-10 10S2 17.523 2 12 6.477 2 12 2s10 4.477 10 10"></path>
                </svg>
            </div>
        `.replace(/\s+/g, ' ').trim();
    }

    /**
     * NCDS 컴포넌트별 selector 매핑
     */
    static SELECTOR_MAP = {
        SELECT: '.ncua-select',
        TEXTAREA: '.ncua-input',
        INPUT: {
            checkbox: '.ncua-checkbox-input',
            radio: '.ncua-radio-input',
            default: '.ncua-input'
        },
        IMAGE_FILE_INPUT: '.ncua-image-file-input'
    };

    /**
     * NCDS 에러 대상 요소 찾기
     * @param {HTMLElement} element - 폼 요소
     * @returns {HTMLElement|null} - NCDS 컴포넌트 wrapper 요소
     */
    static getErrorTarget(element) {
        const { tagName, type } = element;

        if (tagName === 'SELECT') {
            return element.closest(this.SELECTOR_MAP.SELECT);
        }
        
        if (tagName === 'TEXTAREA') {
            return element.closest(this.SELECTOR_MAP.TEXTAREA);
        }
        
        if (tagName === 'INPUT') {
            const selector = this.SELECTOR_MAP.INPUT[type] || this.SELECTOR_MAP.INPUT.default;
            return element.closest(selector);
        }

        if (element.matches(this.SELECTOR_MAP.IMAGE_FILE_INPUT)) {
            return element.closest(this.SELECTOR_MAP.IMAGE_FILE_INPUT);
        }

        return null;
    }

    /**
     * 요소 타입별 에러 클래스 반환
     * @param {HTMLElement} element - 폼 요소
     * @returns {string} 에러 클래스명
     */
    static getErrorClassName(element) {
        const { tagName } = element;
        return this.ERROR_CLASS_NAME.default;
    }

    /**
     * 입력 필드의 사이즈 감지
     * @param {HTMLElement} element - 폼 요소
     * @returns {string} 사이즈 ('xs', 'sm', 'default')
     */
    static detectSize(element) {
        const inputWrapper = element.closest('.ncua-input');
        if (!inputWrapper) return 'default';

        if (inputWrapper.classList.contains('ncua-input--xs')) return 'xs';
        if (inputWrapper.classList.contains('ncua-input--sm')) return 'sm';
        
        return 'default';
    }

    /**
     * 에러 아이콘 추가
     * @param {HTMLElement} element - 폼 요소
     */
    static addErrorIcon(element) {
        const { tagName, type } = element;
        
        // data-use-error-icon 속성이 false면 아이콘 추가 안 함
        if (element.hasAttribute('data-use-error-icon') && element.getAttribute('data-use-error-icon') === 'false') {
            return;
        }

        // input[type="text"], input[type="email"] 등 텍스트 계열만 아이콘 추가
        if (tagName !== 'INPUT' || type === 'checkbox' || type === 'radio' || type === 'hidden') {
            return;
        }

        const field = element.closest('.ncua-input__field');
        if (!field) return;

        // 이미 아이콘이 있으면 추가하지 않음
        if (field.querySelector('.ncua-input__destructive-icon-wrap')) return;

        // 사이즈에 따른 아이콘 크기 결정
        const size = this.detectSize(element);
        const iconSize = this.ICON_SIZE_MAP[size] || this.ICON_SIZE_MAP.default;

        element.insertAdjacentHTML('afterend', this.getErrorIconHTML(iconSize));
    }

    /**
     * 에러 아이콘 제거
     * @param {HTMLElement} element - 폼 요소
     */
    static removeErrorIcon(element) {
        const field = element.closest('.ncua-input__field');
        if (!field) return;

        const icon = field.querySelector('.ncua-input__destructive-icon-wrap');
        icon?.remove();
    }

    /**
     * 에러 표시 (힌트 텍스트 생성 및 에러 메시지 표시)
     * @param {Object} errorMap - 에러 매핑
     * @param {Array} errorList - 에러 목록
     */
    static showErrors = function(errorMap, errorList) {
        // errorList가 배열이 아닌 경우 처리
        if (!Array.isArray(errorList)) {
            return;
        }

        for (const error of errorList) {
            const { element, message } = error;
            const target = NCDSValidator.getErrorTarget(element);
            
            // NCDS 컴포넌트가 아니면 건너뛰기
            if (!target) {
                continue;
            }

            // highlight는 jQuery Validator가 자동으로 호출하므로 여기서는 호출하지 않음
            // showErrors는 힌트 텍스트만 처리

            // data-show-hint-text가 true인 경우에만 힌트 텍스트 표시
            if (target.hasAttribute('data-show-hint-text') && target.getAttribute('data-show-hint-text') === 'true') {
                // 힌트 텍스트 찾기 (기존 것이 있으면 재사용)
                let hintText = target.querySelector(`.${NCDSValidator.HINT_TEXT_CLASS_NAME.split(' ')[0]}`);
                if (!hintText) {
                    // 힌트 텍스트가 없으면 새로 생성
                    hintText = NCDSValidator.createHintTextElement();
                    target.appendChild(hintText);
                }
                
                // 원래 힌트 텍스트 저장 (최초 1회만)
                if (!hintText.hasAttribute('data-original-hint')) {
                    hintText.setAttribute('data-original-hint', hintText.textContent);
                }
                // 에러 메시지 저장 (복원 판단용)
                hintText.setAttribute('data-error-hint', message);

                // 에러 클래스 추가 및 메시지 업데이트
                hintText.classList.add(NCDSValidator.ERROR_CLASS_NAME.default);
                hintText.textContent = message;
            }
        }

        // this is jqueryValidator
        if (this.defaultShowErrors) {
            this.defaultShowErrors();
        }
    }

    /**
     * 에러 숨김 (힌트 텍스트 원래 상태로 복원)
     * @param {HTMLElement} target - NCDS 컴포넌트 wrapper 요소
     */
    static removeHintText = function(element) {
        if(!element) { 
            return;
        }

        const target = NCDSValidator.getErrorTarget(element);
        if (!target) return;

        const hintText = target.querySelector('.ncua-hint-text') || null;

        if (!hintText) return;

        hintText.classList.remove(NCDSValidator.ERROR_CLASS_NAME.default);

        if (hintText.hasAttribute('data-original-hint')) {
            // 현재 텍스트가 에러 메시지와 동일하면 → 컴포넌트가 setHint로 갱신하지 않은 것 → 원래 텍스트로 복원
            if (hintText.textContent === hintText.getAttribute('data-error-hint')) {
                hintText.textContent = hintText.getAttribute('data-original-hint');
            }
            // 현재 텍스트가 에러 메시지와 다르면 → 컴포넌트가 setHint로 이미 갱신한 것 → 복원하지 않음
            hintText.removeAttribute('data-original-hint');
            hintText.removeAttribute('data-error-hint');
        } else if (hintText.classList.contains('ncua-input__hint-text')) {
            // NCDSValidator가 생성한 요소만 제거
            hintText.remove();
        }
    }

    /**
     * 에러 하이라이트 적용
     * @param {HTMLElement|jQuery} element - 폼 요소
     * @param {string} [errorClass] - 에러 클래스 (jQuery Validator에서 전달)
     * @param {string} [validClass] - 유효 클래스 (jQuery Validator에서 전달)
     */
    static highlight = (element, errorClass, validClass) => {
        const domElement = element.jquery ? element[0] : element;
        const target = NCDSValidator.getErrorTarget(domElement);
        if (!target) return;

        // 요소별 에러 클래스 적용
        const errorClassName = NCDSValidator.getErrorClassName(domElement);
        target.classList.add(errorClassName);
        NCDSValidator.addErrorIcon(domElement);
    };

    /**
     * 에러 하이라이트 제거
     * @param {HTMLElement|jQuery} element - 폼 요소
     * @param {string} [errorClass] - 에러 클래스 (jQuery Validator에서 전달)
     * @param {string} [validClass] - 유효 클래스 (jQuery Validator에서 전달)
     */
    static unhighlight = (element, errorClass, validClass) => {
        const domElement = element.jquery ? element[0] : element;
        const target = NCDSValidator.getErrorTarget(domElement);
        if (!target) return;

        // 요소별 에러 클래스 제거
        const errorClassName = NCDSValidator.getErrorClassName(domElement);
        target.classList.remove(errorClassName);
        NCDSValidator.removeErrorIcon(domElement);
        NCDSValidator.removeHintText(domElement);
    };
    
    /**
     * jQuery Validator에 NCDS 스타일 기본값 설정
     */
    static init() {
        if (typeof $ === 'undefined' || typeof $.validator === 'undefined') {
            return;
        }

        $.validator.setDefaults({
            highlight: NCDSValidator.highlight,
            unhighlight: NCDSValidator.unhighlight,
            showErrors: NCDSValidator.showErrors,
        });
    }
}

// 전역 노출 (하위 호환성)
window.NCDSValidator = NCDSValidator;
window.getNCDSErrorTarget = (element) => NCDSValidator.getErrorTarget(element);
window.initNCDSValidatorDefaults = () => NCDSValidator.init();
window.NCDSHighlighter = NCDSValidator.highlight;
window.NCDSUnhighlighter = NCDSValidator.unhighlight;
window.showNCDSErrors = NCDSValidator.showErrors;
window.hideNCDSErrors = NCDSValidator.hideErrors; 
  
// DOM 로드 시 자동 초기화
if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', () => NCDSValidator.init());
} else {
    NCDSValidator.init();
}
