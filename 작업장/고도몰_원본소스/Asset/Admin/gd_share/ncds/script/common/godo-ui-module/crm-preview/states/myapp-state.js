/**
 * Myapp State
 * 마이앱 발송 타입의 미리보기 처리
 * HTML을 동적으로 생성하는 방식
 * 순수 바닐라 자바스크립트
 */
class MyappState {
    constructor(container, data, options = {}) {
        this.container = container;
        this.data = data;
        this.section = null;
        this.showAdLabel = options.showAdLabel !== undefined ? options.showAdLabel : false;
    }

    /**
     * UI 렌더링 - MYAPP 미리보기 HTML 동적 생성
     */
    render() {
        // 컨테이너 비우기
        this.container.innerHTML = '';
        
        // MYAPP 섹션 생성
        this.section = this.createMyappSection();
        
        // 컨테이너에 추가
        this.container.appendChild(this.section);
        
        // 데이터 적용
        this.applyData();
    }

    /**
     * MYAPP 섹션 HTML 생성
     * @returns {HTMLElement}
     */
    createMyappSection() {
        const section = document.createElement('section');
        section.setAttribute('data-component', 'myapp-message-preview');
        section.className = 'mobile-preview mobile-preview--myapp';
        
        section.innerHTML = `
            <span class="ncua-profile ncua-profile--float ncua-profile--myapp"></span>
            <figure class="ncua-prev-myapp">
                <figcaption class="ncua-prev-myapp__caption">
                    <span class="myapp-title"></span><br />
                    <span class="myapp-content" style="white-space: pre-wrap;"></span><br />
                    <span class="myapp-withdrawal-method"></span>
                </figcaption>
                <span class="ncua-prev-myapp__media" hidden>
                    <img id="myappImagePreview" src="" alt="">
                </span>
            </figure>
        `;

        return section;
    }

    /**
     * 저장된 데이터를 UI에 적용
     */
    applyData() {
        if (this.data.title) this.setTitle(this.data.title);
        if (this.data.content) this.setContent(this.data.content);
        if (this.data.image) this.setImage(this.data.image);
        if (this.data.withdrawalMethod) this.setWithdrawalMethod(this.data.withdrawalMethod);
    }

    /**
     * 제목 설정
     * @param {string} value
     */
    setTitle(value) {
        if (!this.section) return;
        
        const element = this.section.querySelector('.myapp-title');
        if (element) {
            const prefix = this.showAdLabel ? '(광고) ' : '';
            element.textContent = prefix + value;
        }
    }

    /**
     * 내용 설정
     * @param {string} value
     */
    setContent(value) {
        if (!this.section) return;
        
        const element = this.section.querySelector('.myapp-content');
        if (element) {
            element.innerHTML = CrmSanitizer.sanitizeHTML(value);
        }
    }

    /**
     * 이미지 설정
     * @param {string} src
     */
    setImage(src) {
        if (!this.section) return;
        
        const element = this.section.querySelector('#myappImagePreview');
        if (element) {
            element.src = src;
            
            // 이미지가 있으면 표시, 없으면 숨김
            const media = element.closest('.ncua-prev-myapp__media');
            if (media) {
                media.hidden = !src;
            }
        }
    }

    /**
     * 수신거부 방법 설정
     * @param {string} value
     */
    setWithdrawalMethod(value) {
        if (!this.section) return;
        
        const element = this.section.querySelector('.myapp-withdrawal-method');
        if (element) {
            element.textContent = value;
        }
    }

    /**
     * 광고 라벨 표시 여부 설정
     * @param {boolean} show
     */
    setShowAdLabel(show) {
        this.showAdLabel = show;
        this.setTitle(this.data.title);
    }

    /**
     * 버튼 설정 (MYAPP은 버튼이 없으므로 무시)
     */
    setButtons(buttons) {
        // MYAPP은 버튼이 없음
    }

    /**
     * 리셋 (필드 초기화)
     */
    reset() {
        this.setTitle('');
        this.setContent('');
        this.setImage('');
        this.setWithdrawalMethod('');
    }

    /**
     * State 정리
     * Myapp State는 정리할 리소스가 없으므로 빈 구현
     */
    destroy() {
        // No cleanup needed
    }
}

// Window 전역 객체로 노출
window.MyappState = MyappState;
