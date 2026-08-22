/**
 * SMS State
 * SMS 발송 타입의 미리보기 처리
 * HTML을 동적으로 생성하는 방식
 * 순수 바닐라 자바스크립트
 */
class SmsState {
    constructor(container, data, options = {}) {
        this.container = container;
        this.data = data;
        this.section = null;
    }

    /**
     * UI 렌더링 - SMS 미리보기 HTML 동적 생성
     */
    render() {
        // 컨테이너 비우기
        this.container.innerHTML = '';
        
        // SMS 섹션 생성
        this.section = this.createSmsSection();
        
        // 컨테이너에 추가
        this.container.appendChild(this.section);
        
        // 데이터 적용
        this.applyData();
    }

    /**
     * SMS 섹션 HTML 생성
     * @returns {HTMLElement}
     */
    createSmsSection() {
        const section = document.createElement('section');
        section.setAttribute('data-component', 'sms-message-preview');
        section.className = 'mobile-preview mobile-preview--sms';
        
        const contentDiv = document.createElement('div');
        contentDiv.className = 'ncua-prev sms-contents-side-preview';
        contentDiv.style.whiteSpace = 'pre-wrap';
        
        section.appendChild(contentDiv);
        
        return section;
    }

    /**
     * 저장된 데이터를 UI에 적용
     */
    applyData() {
        if (this.data.content) {
            this.setContent(this.data.content);
        }
    }

    /**
     * 내용 설정
     * @param {string} value
     */
    setContent(value) {
        if (!this.section) return;
        
        const element = this.section.querySelector('.sms-contents-side-preview');
        if (element) {
            element.innerHTML = CrmSanitizer.sanitizeHTML(value);
        }
    }

    /**
     * 제목 설정 (SMS는 제목이 없으므로 무시)
     */
    setTitle(value) {
        // SMS는 제목이 없음
    }

    /**
     * 이미지 설정 (SMS는 이미지가 없으므로 무시)
     */
    setImage(src) {
        // SMS는 이미지가 없음
    }

    /**
     * 버튼 설정 (SMS는 버튼이 없으므로 무시)
     */
    setButtons(buttons) {
        // SMS는 버튼이 없음
    }

    /**
     * 리셋 (필드 초기화)
     */
    reset() {
        this.setTitle('');
        this.setContent('');
    }

    /**
     * State 정리
     * SMS State는 정리할 리소스가 없으므로 빈 구현
     */
    destroy() {
        // No cleanup needed
    }
}

// Window 전역 객체로 노출
window.SmsState = SmsState;
