/**
 * @module NumericInputManager
 * @description 
 * NCDS 표준 숫자 입력 제어 모듈입니다. 
 * 이벤트 위임(Event Delegation)을 통해 동적으로 생성된 요소에도 자동 적용됩니다.
 * * @example
 * * <input type="text" class="js-number">
 * * * <input type="text" class="js-number" data-number-type="comma">
 * * * <input type="text" class="js-number" data-number-type="decimal">
 */
const createNumericInputManager = () => {
    const STRATEGIES = {
        // 기본: 순수 숫자만 (ex: 12345)
        default: (val) => val.replace(/[^0-9]/g, ''),
        // 천 단위 콤마 추가 (ex: 1,234)
        comma: (val) => {
            const num = val.replace(/[^0-9]/g, '');
            return num.replace(/\B(?=(\d{3})+(?!\d))/g, ',');
        },
        // 소수점 허용 (ex: 12.34)
        decimal: (val) => val.replace(/[^0-9.]/g, '').replace(/(\..*)\./g, '$1')
    };

    /**
     * 실제 입력값 정제 함수
     */
    const transform = (inputElement) => {
        const originalValue = inputElement.value;
        const type = inputElement.dataset.numberType || 'default';
        const strategy = STRATEGIES[type] || STRATEGIES.default;
        
        const transformedValue = strategy(originalValue);

        if (originalValue !== transformedValue) {
            inputElement.value = transformedValue;
        }
    };

    const handler = (event) => {
        if (event.target.classList.contains('js-number')) {
            transform(event.target);
        }
    };

    return {
        init() {
            document.addEventListener('input', handler, true);
            document.querySelectorAll('.js-number').forEach(transform);
            return this;
        },
        destroy() {
            document.removeEventListener('input', handler, true);
        },
        update(inputElement) {
            if (inputElement) transform(inputElement);
        }
    };
};

document.addEventListener('DOMContentLoaded', () => {
    window.numericManager = createNumericInputManager().init();
});