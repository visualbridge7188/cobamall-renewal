/**
 * 입력 글자수 카운트 기능
 * @module CharCountManager
 * @param {Object} options - 설정 옵션
 * @param {string[]} options.targetClasses - 감시할 input/textarea 클래스 목록
 * @param {string} [options.charCountWrapSelector='.ncua-input__field-text-count'] - input용 카운트 래퍼 셀렉터
 * @param {string} [options.currentSelector='.ncua-input__field-text-count-current'] - input용 현재 글자수 셀렉터
 * @param {string} [options.textareaCountWrapSelector='.ncua-input__text-count'] - textarea용 카운트 래퍼 셀렉터
 * @param {string} [options.textareaCurrentSelector='.ncua-input__text-count-text-count'] - textarea용 현재 글자수 셀렉터
 * @param {boolean} [options.useBytes=false] - bytes 단위 사용 여부
 * @param {number} [options.bytesThreshold=0] - bytes 임계값 (초과 시 최대값 표시 변경)
 * @param {string} [options.bytesMaxSelector='.ncua-input__text-count-text-max'] - bytes 최대값 표시 셀렉터
 * @returns {Object} CharCountManager 인스턴스
 * 
 * @example
 * // 기본 사용 (글자수 기준)
 * const manager = createCharCountManager({
 *     targetClasses: ['input-title', 'input-name'],
 * });
 * manager.init();
 * 
 * @example
 * // bytes 모드 사용 (SMS/LMS)
 * const bytesManager = createCharCountManager({
 *     targetClasses: ['textarea-content'],
 *     useBytes: true,
 *     bytesThreshold: 90,     // 90bytes 초과 시 최대값 표시 변경
 *     bytesMaxLength: 3000,   // bytes 모드 최대 길이 (useBytes: true일 때만 사용)
 * });
 * bytesManager.init();
 * 
 * @example
 * // useBytes 메서드 사용
 * bytesManager.useBytes({ bytesMaxLength: 3000 });
 * 
 * @example
 * // useChars 메서드 사용
 * charManager.useChars({ charMaxLength: 300 });
 * // useChars
 *
 * @example
 * // 인스턴스 정리
 * manager.destroy();
 */
function createCharCountManager({
    targetClasses = [],
    charCountWrapSelector = '.ncua-input__field-text-count',
    currentSelector = '.ncua-input__field-text-count-current',
    textareaCountWrapSelector = '.ncua-input__text-count',
    textareaTextCountTextSelector = '.ncua-input__text-count-text',
    textareaCurrentSelector = '.ncua-input__text-count-text-count',
    useBytes = false,
    bytesThreshold = 0,
    bytesMaxSelector = '.ncua-input__text-count-text-max',
    bytesMaxLength = 3000,   // bytes 모드 최대 길이 (useBytes: true일 때만 사용)
} = {}) {
    // 동적으로 변경 가능한 useBytes 변수
    let currentUseBytes = useBytes;
    
    // 초기 maxLength 저장 (char 모드로 복원할 때 사용)
    const originalMaxLengths = new Map();

    // 바이트 계산 상수
    const MULTIBYTE_CHAR_THRESHOLD = 127;
    const LINE_FEED_CHAR_CODE = 13;
    const CHAR_MAX_LENGTH = 90;
    const BYTES_MAX_LENGTH = 2000;

    // 카운트 엘리먼트 기본값
    const DEFAULT_ELEMENT = { currentEl: null, maxEl: null, parentWrap: null };

    /**
     * 문자열의 바이트 크기 계산 (SMS 기준: 한글 2byte, 영문/숫자 1byte)
     * PHP StringUtils::strLength()와 동일한 로직
     * @param {string} str
     * @returns {number}
     */
    const getByteLength = (str) => {
        let cnt = 0;
        for (let i = 0; i < str.length; i++) {
            const charCode = str.charCodeAt(i);
            // ord($ch) > 127인 경우 2바이트 (한글 등 멀티바이트 문자)
            if (charCode > MULTIBYTE_CHAR_THRESHOLD) {
                cnt += 2;
            } else if (charCode !== LINE_FEED_CHAR_CODE) {
                // 개행중 10은 CR(Carriage Return), 13은 LF(Line Feed), 13은 무시함
                cnt++;
            }
        }
        return cnt;
    };

    /**
     * textarea의 카운트 엘리먼트 찾기
     */
    const findTextareaCountElements = (element) => {
        const parent = element.closest('.ncua-input, .ncua-input--textarea');
        if (!parent) {
            return DEFAULT_ELEMENT;
        }
        
        const countWrap = parent.querySelector(textareaCountWrapSelector);
        if (!countWrap) {
            return DEFAULT_ELEMENT;
        }
        
        return {
            currentEl: countWrap.querySelector(textareaCurrentSelector),
            maxEl: countWrap.querySelector(bytesMaxSelector),
            parentWrap: countWrap,
        };
    };

    /**
     * input의 카운트 엘리먼트 찾기
     */
    const findInputCountElements = (element) => {
        const key = element.getAttribute('data-charcount-key');
        if (!key) {
            return DEFAULT_ELEMENT;
        }
        
        const charCountWrap = document.querySelector(
            `${charCountWrapSelector}[data-charcount-text='${key}']`
        );
        if (!charCountWrap) {
            return DEFAULT_ELEMENT;
        }
        
        return {
            currentEl: charCountWrap.querySelector(currentSelector),
            maxEl: charCountWrap.querySelector(bytesMaxSelector),
            parentWrap: charCountWrap,
        };
    };

    /**
     * 카운트 표시 엘리먼트들 찾기
     */
    const findCountElements = (element) => {
        if (element.tagName === 'TEXTAREA') {
            return findTextareaCountElements(element);
        }
        return findInputCountElements(element);
    };

    /**
     * bytes 모드 사용 여부 결정
     */
    const shouldUseBytesMode = (element) => {
        const elementUseBytes = element.getAttribute('data-charcount-bytes') === 'true';
        return elementUseBytes || currentUseBytes;
    };

    /**
     * 문자열 길이 계산
     */
    const calculateLength = (value, useBytesMode) => {
        return useBytesMode ? getByteLength(value) : value.length;
    };

    /**
     * bytes 모드에서 값 자르기
     */
    const truncateBytes = (value, maxLength) => {
        let truncated = value;
        while (getByteLength(truncated) > maxLength && truncated.length > 0) {
            truncated = truncated.slice(0, -1);
        }
        return truncated;
    };

    /**
     * char 모드에서 값 자르기
     */
    const truncateChars = (value, maxLength) => {
        return value.slice(0, maxLength);
    };

    /**
     * 최대 길이를 초과하는 값 자르기
     */
    const truncateValue = ({ value, maxLength, useBytesMode = false }) => {
        return useBytesMode ? truncateBytes(value, maxLength) : truncateChars(value, maxLength);
    };

    /**
     * bytes 모드 검증 및 자르기
     * bytesThreshold 이상 입력 가능하지만 maxLength까지만
     */
    const validateBytesMode = (value, maxLength) => {
        if (!value) {
            return value;
        }
        
        const currentByteLength = getByteLength(value);
        if (currentByteLength > maxLength) {
            return truncateBytes(value, maxLength);
        }
        // bytesThreshold 이상이어도 입력 가능하므로 그대로 반환
        return value;
    };

    /**
     * char 모드 검증 및 자르기
     * maxLength까지만 입력 가능
     */
    const validateCharMode = (value, maxLength) => {
        if (!value) {
            return value;
        }
        
        if (value.length > maxLength) {
            return truncateChars(value, maxLength);
        }
        return value;
    };

    /**
     * 모드별 입력 제한 검증 및 자르기
     */
    const validateAndTruncate = ({ value, maxLength, useBytesMode = false }) => {
        return useBytesMode 
            ? validateBytesMode(value, maxLength)
            : validateCharMode(value, maxLength);
    };

    /**
     * bytes 모드 최대값 표시 업데이트
     */
    const updateBytesMaxDisplay = (maxEl, currentLength, maxLength) => {
        if (bytesThreshold > 0 && currentLength <= bytesThreshold) {
            maxEl.textContent = bytesThreshold;
        } else {
            maxEl.textContent = maxLength;
        }
    };

    /**
     * char 모드 최대값 표시 업데이트
     */
    const updateCharMaxDisplay = (maxEl, maxLength) => {
        maxEl.textContent = maxLength;
    };

    /**
     * 최대값 표시 업데이트 (bytesThreshold 기능)
     */
    const updateMaxDisplay = ({ maxEl, currentLength, maxLength, useBytesMode = false }) => {
        if (!maxEl) {
            return;
        }
        
        if (useBytesMode) {
            updateBytesMaxDisplay(maxEl, currentLength, maxLength);
        } else {
            updateCharMaxDisplay(maxEl, maxLength);
        }
    };

    /**
     * 모드에 따른 maxLength 결정
     */
    const getMaxLength = (element, useBytesMode) => {
        if (useBytesMode) {
            return bytesMaxLength;
        }
        return element.maxLength > 0 ? element.maxLength : 0;
    };

    /**
     * 요소의 maxLength 설정
     */
    const setElementMaxLength = (element, useBytesMode) => {
        if (useBytesMode) {
            element.maxLength = bytesMaxLength;
        }
    };

    /**
     * 요소 값 검증 및 업데이트
     */
    const validateAndUpdateElementValue = ({ element, maxLength, useBytesMode = false }) => {
        let value = element.value || '';
        const validatedValue = validateAndTruncate({ 
            value, 
            maxLength, 
            useBytesMode 
        });
        
        if (validatedValue !== value) {
            element.value = validatedValue;
            return validatedValue;
        }
        
        return value;
    };

    /**
     * 카운트 표시 업데이트
     */
    const updateCountDisplay = ({ currentEl, maxEl, value, maxLength, useBytesMode = false }) => {
        const currentLength = calculateLength(value, useBytesMode);
        currentEl.textContent = currentLength;
        updateMaxDisplay({ 
            maxEl, 
            currentLength, 
            maxLength, 
            useBytesMode 
        });
    };

    /**
     * textarea의 'bytes' 텍스트 업데이트
     */
    const updateTextareaBytesText = ({ element, useBytesMode }) => {
        if (element.tagName !== 'TEXTAREA') return;

        const { parentWrap } = findTextareaCountElements(element);
        if (!parentWrap) return;

        const textCountTextEl = parentWrap.querySelector(textareaTextCountTextSelector);
        if (!textCountTextEl) return;

        // 마지막 텍스트 노드 찾기
        const childNodes = Array.from(textCountTextEl.childNodes);
        const lastTextNode = childNodes.filter(node => node.nodeType === Node.TEXT_NODE).pop();
        const hasBytes = lastTextNode && lastTextNode.textContent.includes('bytes');
        
        if (useBytesMode && !hasBytes) {
            textCountTextEl.appendChild(document.createTextNode(' bytes'));
        } else if (!useBytesMode && hasBytes) {
            lastTextNode.textContent = lastTextNode.textContent.replace(/\s*bytes\s*/, '');
        }
    };

    /**
     * 모든 타겟 요소 순회
     */
    const forEachTargetElement = (callback) => {
        targetClasses.forEach((targetClass) => {
            const elements = document.querySelectorAll(`.${targetClass}`);
            elements.forEach((element) => {
                if (element instanceof HTMLInputElement || element instanceof HTMLTextAreaElement) {
                    callback(element);
                }
            });
        });
    };

    /**
     * 인스턴스별 이벤트 핸들러
     * @param {Event} event
     */
    const handler = (event) => {
        if (!targetClasses.some((cls) => event.target.classList.contains(cls))) {
            return;
        }
        instance.update(event.target);
    };

    const instance = {
        /**
         * 이벤트 바인딩 및 초기화
         */
        init() {
            document.addEventListener('input', handler);
            
            // 초기 maxLength 저장 및 설정
            forEachTargetElement((element) => {
                const initialMaxLength = element.maxLength || 0;
                originalMaxLengths.set(element, initialMaxLength);
                
                if (currentUseBytes) {
                    element.maxLength = bytesMaxLength;
                }
            });
            
            // 초기 카운트 업데이트
            forEachTargetElement((element) => {
                instance.update(element);
            });
        },

        /**
         * 이벤트 해제
         */
        destroy() {
            document.removeEventListener('input', handler);
        },

        /**
         * 수동 글자수 갱신
         * @param {HTMLInputElement|HTMLTextAreaElement} element
         */
        update(element) {
            const { currentEl, maxEl } = findCountElements(element);
            if (!currentEl) {
                return;
            }

            const useBytesMode = shouldUseBytesMode(element);
            const maxLength = getMaxLength(element, useBytesMode);
            
            if (!maxLength) {
                return;
            }
            
            setElementMaxLength(element, useBytesMode);
            const validatedValue = validateAndUpdateElementValue({ 
                element, 
                maxLength, 
                useBytesMode 
            });
            updateCountDisplay({ 
                currentEl, 
                maxEl, 
                value: validatedValue, 
                maxLength, 
                useBytesMode 
            });
            updateTextareaBytesText({ 
                element, 
                useBytesMode 
            });
        },

        /**
         * bytes 모드로 변경
         * @param {Object} options - 설정 옵션
         * @param {number} [options.bytesMaxLength=BYTES_MAX_LENGTH] - 최대 바이트수 (기본값: 2000)
         */
        useBytes({ bytesMaxLength = BYTES_MAX_LENGTH } = {}) {
            currentUseBytes = true;
            forEachTargetElement((element) => {
                element.maxLength = bytesMaxLength;
                instance.update(element);
            });
        },

        /**
         * char 모드로 변경
         * @param {Object} options - 설정 옵션
         * @param {number} [options.charMaxLength=CHAR_MAX_LENGTH] - 최대 글자수 (기본값: 90)
         */
        useChars({ charMaxLength = CHAR_MAX_LENGTH } = {}) {
            currentUseBytes = false;
            forEachTargetElement((element) => {
                element.maxLength = charMaxLength;
                const validatedValue = validateAndTruncate({ 
                    value: element.value || '', 
                    maxLength: charMaxLength 
                });
                
                if (validatedValue !== element.value) {
                    element.value = validatedValue;
                }
                
                instance.update(element);
            });
        },
    };
    
    return instance;
}
