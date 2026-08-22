/**
 * MessageInput - 메시지 입력 및 글자수 카운트 UI 모듈
 * textarea 렌더링, 힌트 표시, 글자수/바이트 카운트, 텍스트 삽입 기능을 제공합니다.
 */
class MessageInput {
    /**
     * @param {Object} options - 초기화 옵션
     * @param {string|HTMLElement} options.container - 렌더링될 컨테이너 셀렉터 또는 요소
     * @param {string} options.name - textarea name 속성 (기본: 'messageContent')
     * @param {string} options.placeholder - placeholder 텍스트
     * @param {string} options.hintText - 힌트 텍스트 (선택사항)
     * @param {number} options.maxLength - 최대 길이 (기본: 2000)
     * @param {boolean} options.useBytes - bytes 모드 사용 여부 (기본: false)
     * @param {number} options.bytesThreshold - bytes 임계값 (기본: 90)
     * @param {boolean} options.allowEmoji - 이모티콘 허용 여부 (기본: true)
     * @param {string} options.initialValue - 초기 값 (선택사항)
     * @param {Function} options.onInput - input 이벤트 콜백
     * @param {Function} options.onChange - change 이벤트 콜백
     */
    constructor(options) {
        if (!options || !options.container) {
            throw new Error('MessageInput: container 옵션이 필요합니다.');
        }

        // 문자열이면 querySelector, 아니면 그대로 사용
        this.container = typeof options.container === 'string' 
            ? document.querySelector(options.container)
            : options.container;
            
        if (!this.container) {
            throw new Error('MessageInput: container를 찾을 수 없습니다.');
        }

        // 옵션 설정
        this.name = options.name || 'messageContent';
        this.placeholder = options.placeholder || '메시지 내용을 입력하세요.';
        this.hintText = options.hintText || '';
        this.maxLength = options.maxLength || 2000;
        this.useBytes = options.useBytes !== undefined ? options.useBytes : false;
        this.bytesThreshold = options.bytesThreshold || 90;
        this.isAllowEmoji = options.allowEmoji !== undefined ? options.allowEmoji : true;
        this.initialValue = options.initialValue || '';
        this.onInputCallback = options.onInput || null;
        this.onChangeCallback = options.onChange || null;

        // DOM 요소 참조
        this.textarea = null;
        this.hintElement = null;
        this.currentCountElement = null;
        this.maxCountElement = null;

        // 마지막 커서 위치 저장 (focus를 잃었을 때 사용)
        this.lastCursorPosition = null;

        // document 이벤트 리스너 참조 (destroy 시 제거용)
        this._onDocumentClick = null;

        // 상수
        this.MULTIBYTE_CHAR_THRESHOLD = 127;
        this.CARRIAGE_RETURN_CODE = 13;  // \r (CR) - SMS 카운트에서 제외
        
        // 이모티콘 정규식 패턴
        this.EMOJI_PATTERN = /[\u{1F1E6}-\u{1F1FF}]{2}|\p{Extended_Pictographic}(?:[\u{FE0F}\u{1F3FB}-\u{1F3FF}]|\u{200D}\p{Extended_Pictographic})*/gu;

        // 초기 렌더링
        this.render();
    }

    /**
     * 전체 UI 렌더링
     */
    render() {
        const html = this.buildHTML();
        this.container.innerHTML = html;
        this.cacheElements();
        this.bindEvents();
        this.updateCount();
        return this;
    }

    /**
     * HTML 생성
     */
    buildHTML() {
        const maxDisplay = this.useBytes 
            ? (this.bytesThreshold > 0 ? this.bytesThreshold : this.maxLength)
            : this.maxLength;

        const bytesText = this.useBytes ? ' bytes' : '';

        return `
            <div class="ncua-input ncua-input--textarea ncua-input--textarea--xs" data-component="message-input">
                <textarea 
                    name="${this.escapeHTML(this.name)}" 
                    class="ncua-input__textarea" 
                    data-message-textarea 
                    placeholder="${this.escapeHTML(this.placeholder)}"
                    maxlength="${this.maxLength}"
                >${this.escapeHTML(this.initialValue)}</textarea>
                <div class="ncua-input__text-count-wrap">
                    <div class="ncua-hint-text" data-message-hint>${this.escapeHTML(this.hintText)}</div>
                    <div class="ncua-input__text-count">
                        <span class="ncua-input__text-count-text">
                            <span class="ncua-input__text-count-text-count" data-message-current>0</span>
                            <span>/</span>
                            <span class="ncua-input__text-count-text-max" data-message-max-length>${maxDisplay}</span>${bytesText}
                        </span>
                    </div>
                </div>
            </div>
        `;
    }

    /**
     * DOM 요소 캐싱
     */
    cacheElements() {
        this.textarea = this.container.querySelector('[data-message-textarea]');
        this.hintElement = this.container.querySelector('[data-message-hint]');
        this.currentCountElement = this.container.querySelector('[data-message-current]');
        this.maxCountElement = this.container.querySelector('[data-message-max-length]');
    }

    /**
     * 이벤트 바인딩
     */
    bindEvents() {
        if (!this.textarea) return;

        this.textarea.addEventListener('input', (e) => {
            this.handleInput(e);
        });

        this.textarea.addEventListener('change', (e) => {
            if (typeof this.onChangeCallback === 'function') {
                this.onChangeCallback(e.target.value, e);
            }
        });

        this.textarea.addEventListener('blur', () => {
            this.lastCursorPosition = {
                start: this.textarea.selectionStart,
                end: this.textarea.selectionEnd
            };
        });

        // textarea, 칩버튼 외 영역 클릭 시 저장된 커서 위치 초기화
        // 기존 리스너가 있으면 제거 (render 재호출 시 중복 방지)
        if (this._onDocumentClick) {
            document.removeEventListener('click', this._onDocumentClick);
        }
        this._onDocumentClick = (e) => {
            const isTextarea = e.target === this.textarea || this.container.contains(e.target);
            const isChipButton = !!e.target.closest('.chip-button');
            if (!isTextarea && !isChipButton) {
                this.lastCursorPosition = null;
            }
        };
        document.addEventListener('click', this._onDocumentClick);
    }

    /**
     * Input 이벤트 핸들러
     */
    handleInput(event) {
        if (!this.textarea) return;
        let value = this.textarea.value;
        let cursorPos = this.textarea.selectionStart;
        
        // 이모티콘 제거 (allowEmoji가 false일 때)
        if (!this.isAllowEmoji) {
            const result = this.removeEmoji(value, cursorPos);
            if (result.value !== value) {
                this.textarea.value = result.value;
                this.textarea.setSelectionRange(result.cursorPos, result.cursorPos);
                value = result.value;
            }
        }
        
        // 길이 검증 및 자르기
        const validatedValue = this.validateAndTruncate(value);
        if (validatedValue !== value) {
            this.textarea.value = validatedValue;
        }

        // 카운트 업데이트
        this.updateCount();

        // 콜백 실행
        if (typeof this.onInputCallback === 'function') {
            this.onInputCallback(this.textarea.value, event);
        }
    }

    /**
     * 문자열의 바이트 크기 계산 (SMS 기준: 한글 2byte, 영문/숫자 1byte)
     */
    getByteLength(str) {
        let cnt = 0;
        for (let i = 0; i < str.length; i++) {
            const charCode = str.charCodeAt(i);
            if (charCode > this.MULTIBYTE_CHAR_THRESHOLD) {
                cnt += 2;
            } else if (charCode !== this.CARRIAGE_RETURN_CODE) {
                cnt++;
            }
        }
        return cnt;
    }

    /**
     * 이모티콘 제거 및 커서 위치 보정
     * @param {string} str - 원본 문자열
     * @param {number} cursorPos - 현재 커서 위치
     * @returns {Object} { value: 제거된 문자열, cursorPos: 보정된 커서 위치 }
     */
    removeEmoji(str, cursorPos = 0) {
        if (!str) return { value: str, cursorPos: 0 };
        
        // 커서 이전에 제거된 문자 길이 계산
        let removedBeforeCursor = 0;
        const matches = [...str.matchAll(this.EMOJI_PATTERN)];
        
        for (const match of matches) {
            if (match.index < cursorPos) {
                removedBeforeCursor += match[0].length;
            }
        }
        
        // 이모티콘 제거
        const newStr = str.replace(this.EMOJI_PATTERN, '');
        const newCursorPos = Math.max(0, cursorPos - removedBeforeCursor);
        
        return {
            value: newStr,
            cursorPos: newCursorPos
        };
    }

    /**
     * 현재 길이 계산 (bytes 또는 chars)
     */
    calculateLength(value) {
        return this.useBytes ? this.getByteLength(value) : value.length;
    }

    /**
     * bytes 모드에서 값 자르기
     */
    truncateBytes(value, maxLength) {
        let truncated = value;
        while (this.getByteLength(truncated) > maxLength && truncated.length > 0) {
            truncated = truncated.slice(0, -1);
        }
        return truncated;
    }

    /**
     * 값 검증 및 자르기
     */
    validateAndTruncate(value) {
        if (!value) return value;

        if (this.useBytes) {
            const currentByteLength = this.getByteLength(value);
            if (currentByteLength > this.maxLength) {
                return this.truncateBytes(value, this.maxLength);
            }
        } else {
            if (value.length > this.maxLength) {
                return value.slice(0, this.maxLength);
            }
        }
        
        return value;
    }

    /**
     * 카운트 표시 업데이트
     */
    updateCount() {
        if (!this.textarea || !this.currentCountElement) return;

        const value = this.textarea.value;
        const currentLength = this.calculateLength(value);

        // 현재 카운트 업데이트
        this.currentCountElement.textContent = currentLength;

        // bytes 모드에서 threshold 처리
        if (this.useBytes && this.bytesThreshold > 0 && this.maxCountElement) {
            if (currentLength <= this.bytesThreshold) {
                this.maxCountElement.textContent = this.bytesThreshold;
            } else {
                this.maxCountElement.textContent = this.maxLength;
            }
        }
    }

    /**
     * 커서 위치에 텍스트 삽입
     */
    insertText(text) {
        if (!this.textarea) return this;

        // textarea에 포커스가 있는지 확인
        const hasFocus = document.activeElement === this.textarea;

        let start, end;
        if (hasFocus) {
            // 포커스가 있으면 현재 커서 위치 사용
            start = this.textarea.selectionStart;
            end = this.textarea.selectionEnd;
        } else if (this.lastCursorPosition) {
            // 포커스가 없지만 마지막 커서 위치가 저장되어 있으면 해당 위치 사용
            start = this.lastCursorPosition.start;
            end = this.lastCursorPosition.end;
        } else {
            // 커서 위치 정보가 없으면 맨 뒤에 삽입
            start = this.textarea.value.length;
            end = start;
        }
        
        const currentValue = this.textarea.value;

        // 커서 위치에 텍스트 삽입
        const newValue = currentValue.substring(0, start) + text + currentValue.substring(end);
        
        // 검증 후 설정
        this.textarea.value = this.validateAndTruncate(newValue);

        // 커서를 삽입된 텍스트 뒤로 이동
        const newCursorPos = start + text.length;
        this.textarea.setSelectionRange(newCursorPos, newCursorPos);

        // textarea에 포커스
        this.textarea.focus();

        // 카운트 업데이트
        this.updateCount();

        // input 이벤트 콜백 실행 (일관성을 위해 event는 null 전달)
        if (typeof this.onInputCallback === 'function') {
            this.onInputCallback(this.textarea.value, null);
        }

        return this;
    }

    /**
     * 값 가져오기
     */
    getValue() {
        return this.textarea ? this.textarea.value : '';
    }

    /**
     * 값 설정하기
     */
    setValue(value) {
        if (!this.textarea) return this;

        let processedValue = value || '';
        
        // 이모티콘 제거 (allowEmoji가 false일 때)
        if (!this.isAllowEmoji && processedValue) {
            const result = this.removeEmoji(processedValue, 0);
            processedValue = result.value;
        }
        
        // 길이 검증
        const validatedValue = this.validateAndTruncate(processedValue);
        this.textarea.value = validatedValue;
        this.updateCount();

        return this;
    }

    /**
     * 힌트 텍스트 설정
     */
    setHint(text) {
        this.hintText = text || '';
        if (this.hintElement) {
            this.hintElement.textContent = this.hintText;
        }
        return this;
    }

    /**
     * 힌트 텍스트 가져오기
     */
    getHint() {
        return this.hintText;
    }

    /**
     * placeholder 설정
     */
    setPlaceholder(text) {
        this.placeholder = text || '';
        if (this.textarea) {
            this.textarea.placeholder = this.placeholder;
        }
        return this;
    }

    /**
     * 최대 길이 설정
     */
    setMaxLength(length) {
        this.maxLength = length || 2000;
        if (this.textarea) {
            this.textarea.maxLength = this.maxLength;
        }
        if (this.maxCountElement) {
            this.maxCountElement.textContent = this.maxLength;
        }
        this.updateCount();
        return this;
    }

    /**
     * 이모티콘 허용 여부 설정
     * @param {boolean} allow - 허용 여부
     */
    setAllowEmoji(allow) {
        this.isAllowEmoji = !!allow;
        
        // 이모티콘을 차단하는 경우, 현재 값에서도 이모티콘 제거
        if (!this.isAllowEmoji && this.textarea) {
            const value = this.textarea.value;
            const cursorPos = this.textarea.selectionStart;
            const result = this.removeEmoji(value, cursorPos);
            
            if (result.value !== value) {
                this.textarea.value = result.value;
                this.textarea.setSelectionRange(result.cursorPos, result.cursorPos);
                this.updateCount();
            }
        }
        
        return this;
    }

    /**
     * 이모티콘 허용 여부 가져오기
     * @returns {boolean} 이모티콘 허용 여부
     */
    getAllowEmoji() {
        return this.isAllowEmoji;
    }

    /**
     * bytes 모드 활성화
     */
    enableBytesMode(options = {}) {
        this.useBytes = true;
        this.maxLength = options.maxLength || this.maxLength;
        this.bytesThreshold = options.bytesThreshold || this.bytesThreshold;

        if (this.textarea) {
            this.textarea.maxLength = this.maxLength;
        }

        // bytes 텍스트 추가
        this.updateBytesText(true);
        this.updateCount();

        return this;
    }

    /**
     * char 모드 활성화
     */
    enableCharMode(options = {}) {
        this.useBytes = false;
        this.maxLength = options.maxLength || this.maxLength;

        if (this.textarea) {
            this.textarea.maxLength = this.maxLength;
        }

        // bytes 텍스트 제거
        this.updateBytesText(false);
        this.updateCount();

        return this;
    }

    /**
     * bytes 텍스트 표시 업데이트
     */
    updateBytesText(show) {
        if (!this.currentCountElement) return;

        const textCountText = this.currentCountElement.closest('.ncua-input__text-count-text');
        if (!textCountText) return;

        const childNodes = Array.from(textCountText.childNodes);
        const lastTextNode = childNodes.filter(node => node.nodeType === Node.TEXT_NODE).pop();
        const hasBytes = lastTextNode && lastTextNode.textContent.includes('bytes');

        if (show && !hasBytes) {
            textCountText.appendChild(document.createTextNode(' bytes'));
        } else if (!show && hasBytes) {
            lastTextNode.textContent = lastTextNode.textContent.replace(/\s*bytes\s*/, '');
        }
    }

    /**
     * 현재 커서 위치 가져오기
     */
    getCursorPosition() {
        if (!this.textarea) return { start: 0, end: 0 };
        return {
            start: this.textarea.selectionStart,
            end: this.textarea.selectionEnd
        };
    }

    getTextareaContainer() {
        return this.textarea;
    }

    /**
     * 커서 위치 설정
     */
    setCursorPosition(position) {
        if (!this.textarea) return this;
        this.textarea.setSelectionRange(position, position);
        return this;
    }

    /**
     * textarea에 포커스
     */
    focus() {
        if (this.textarea) {
            this.textarea.focus();
        }
        return this;
    }

    /**
     * 초기화 (값 비우기)
     */
    clear() {
        this.setValue('');
        return this;
    }

    /**
     * HTML 이스케이프
     */
    escapeHTML(str) {
        if (typeof str !== 'string') return str;
        return str
            .replace(/&/g, '&amp;')
            .replace(/</g, '&lt;')
            .replace(/>/g, '&gt;')
            .replace(/"/g, '&quot;')
            .replace(/'/g, '&#039;');
    }

    /**
     * 컨테이너 초기화 (파괴)
     */
    destroy() {
        // document 이벤트 리스너 제거
        if (this._onDocumentClick) {
            document.removeEventListener('click', this._onDocumentClick);
            this._onDocumentClick = null;
        }

        if (this.container) {
            this.container.innerHTML = '';
        }
        this.textarea = null;
        this.hintElement = null;
        this.currentCountElement = null;
        this.maxCountElement = null;
    }
}

// CommonJS/AMD 지원
if (typeof module !== 'undefined' && module.exports) {
    module.exports = MessageInput;
}
