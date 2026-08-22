/**
 * textarea 글자수 카운트 기능
 */
/**
 * textarea의 입력 이벤트를 처리하는 함수
 * @param {HTMLTextAreaElement} textarea - textarea 엘리먼트
 * @param {HTMLElement} countElement - 글자수 표시 엘리먼트
 * @param {number} maxLength - 최대 글자수
 */
const handleInput = (textarea, countElement, maxLength) => {
    const currentLength = textarea.value.length;
    
    if (countElement) {
        countElement.innerText = currentLength;
    }

    // maxLength 체크
    if (maxLength > 0 && currentLength > maxLength) {
        // 초과된 텍스트 제거
        textarea.value = textarea.value.slice(0, maxLength);
        // 카운트 업데이트
        if (countElement) {
            countElement.innerText = maxLength;
        }
    }
};

/**
 * textarea의 붙여넣기 이벤트를 처리하는 함수
 * @param {ClipboardEvent} e - 클립보드 이벤트
 * @param {HTMLTextAreaElement} textarea - textarea 엘리먼트
 * @param {HTMLElement} countElement - 글자수 표시 엘리먼트
 * @param {number} maxLength - 최대 글자수
 */
const handlePaste = (e, textarea, countElement, maxLength) => {
    if(!maxLength) return;
  
    const pastedText = (e.clipboardData || window.clipboardData).getData('text');
    const currentLength = textarea.value.length;
    const remainingLength = maxLength - currentLength;
    
    // 붙여넣기 할 텍스트가 제한을 초과하는 경우
    if (pastedText.length > remainingLength) {
        e.preventDefault();
        const truncatedText = pastedText.slice(0, remainingLength);
        
        // 수동으로 텍스트 삽입
        const start = textarea.selectionStart;
        const end = textarea.selectionEnd;
        const textBeforeCursor = textarea.value.slice(0, start);
        const textAfterCursor = textarea.value.slice(end);
        
        textarea.value = textBeforeCursor + truncatedText + textAfterCursor;
        
        // 커서 위치 조정
        textarea.selectionStart = textarea.selectionEnd = start + truncatedText.length;
        
        // 카운트 업데이트
        if (countElement) {
            countElement.innerText = maxLength;
        }
    }
    
};

/**
 * textarea 초기화 함수
 */
const initTextarea = () => {
    const textareas = document.querySelectorAll('.ncua-input__textarea');

    textareas.forEach(function (textarea) {
        const countElement = textarea.closest('.ncua-input--textarea').querySelector('.ncua-input__text-count-text-count');
        const maxLength = parseInt(textarea.getAttribute('maxlength')) || 0;

        // 초기 글자수 표시
        if (countElement) {
            countElement.innerText = textarea.value.length;
        }

        // 이벤트 리스너 등록
        textarea.addEventListener('input', () => handleInput(textarea, countElement, maxLength));
        textarea.addEventListener('paste', (e) => handlePaste(e, textarea, countElement, maxLength));
    });
}

// 전역 함수로 노출
window.initTextarea = initTextarea;

// DOM 로드 완료 시 자동 실행
if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', initTextarea);
} else {
    // DOM이 이미 로드된 경우 즉시 실행
    initTextarea();
}
