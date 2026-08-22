/**
 * CRM Preview Library
 * 메시지 미리보기를 관리하는 메인 클래스 (Facade Pattern)
 */
class CrmPreview {
    /**
     * @param {Object} options - 초기화 옵션
     * @param {string|HTMLElement} options.container - 미리보기가 렌더링될 컨테이너 셀렉터 또는 요소
     * @param {string} options.noticeText - notice 영역 초기 텍스트 (기본: '미리보기와 실제가 다를 수 있습니다.')
     * @param {string} options.checkboxText - checkbox 라벨 텍스트 (기본: '변수로 변환해서 보기')
     * @param {boolean} options.showAdLabel - 마이앱 제목에 (광고) 표시 여부 (기본: false)
     */
    constructor(options) {
        if (!options || !options.container) {
            throw new Error('CrmPreview: container 옵션이 필요합니다.');
        }

        // 문자열이면 querySelector, 아니면 그대로 사용
        this.container = typeof options.container === 'string' 
            ? document.querySelector(options.container)
            : options.container;
            
        if (!this.container) {
            throw new Error('CrmPreview: container를 찾을 수 없습니다.');
        }

        this.noticeText = options.noticeText !== undefined ? options.noticeText : '<li class="ncua-notice-info">미리보기와 실제가 다를 수 있습니다.</li>';
        this.checkboxText = options.checkboxText || '변수로 변환해서 보기';
        this.showAdLabel = options.showAdLabel !== undefined ? options.showAdLabel : false;
        
        // wrapper 구조 생성
        this.initWrapperStructure();

        this.currentState = null;
        this.currentSendType = null;
        this.variableCheckboxCallback = null;
        
        this.data = {
            title: '',
            content: '',
            image: '',
            buttons: [],
            coupon: null,
            itemList: [],
            slideIndex: 0,
            variableMode: false
        };
    }

    /**
     * Wrapper 구조 초기화 (notice, checkbox 포함)
     */
    initWrapperStructure() {
        // container 비우기
        this.container.innerHTML = '';

        // preview-content-wrap 생성
        this.contentWrap = document.createElement('div');
        this.contentWrap.className = 'preview-content-wrap';
        this.container.appendChild(this.contentWrap);

        // notice 생성
        this.noticeElement = document.createElement('ul');
        this.noticeElement.className = 'ncua-notice-list';
        if (this.noticeText) {
            this.noticeElement.innerHTML = CrmSanitizer.sanitizeHTML(this.noticeText);
        } else {
            this.noticeElement.style.display = 'none';
        }
        this.container.appendChild(this.noticeElement);

        // checkbox 영역 생성
        const checkboxDiv = document.createElement('div');
        checkboxDiv.className = 'preview-variable-checkbox';

        const label = document.createElement('label');
        label.className = 'ncua-checkbox-field ncua-checkbox-field--xs has-text';

        const inputSpan = document.createElement('span');
        inputSpan.className = 'ncua-checkbox-input ncua-checkbox-input--xs ncua-checkbox-field__input';

        this.checkboxElement = document.createElement('input');
        this.checkboxElement.type = 'checkbox';
        inputSpan.appendChild(this.checkboxElement);

        const textSpan = document.createElement('span');
        textSpan.className = 'ncua-checkbox-field__text';
        textSpan.textContent = this.checkboxText;

        label.appendChild(inputSpan);
        label.appendChild(textSpan);
        checkboxDiv.appendChild(label);

        this.container.appendChild(checkboxDiv);

        // 체크박스 이벤트 리스너 (destroy 시 제거용으로 참조 저장)
        this._onCheckboxChange = (e) => {
            const isChecked = e.target.checked;
            this.data.variableMode = isChecked;

            if (typeof this.variableCheckboxCallback === 'function') {
                this.variableCheckboxCallback(isChecked);
            }
        };
        this.checkboxElement.addEventListener('change', this._onCheckboxChange);
    }

    /**
     * 발송 타입 설정
     * @param {string} type - 'SMS' | 'FRIENDTALK' | 'ALIMTALK' | 'MYAPP'
     * @returns {CrmPreview} 체이닝을 위한 this 반환
     */
    setSendType(type) {
        if (typeof CRM_SEND_METHOD_TYPE === 'undefined') {
            console.error('CrmPreview: constants.js가 로드되지 않았습니다.');
            return this;
        }

        if (!CRM_SEND_METHOD_TYPE[type]) {
            console.error(`CrmPreview: 잘못된 발송 타입입니다. (${type})`);
            return this;
        }

        // 이미 같은 타입이면 무시
        if (this.currentSendType === type) {
            return this;
        }

        // 기존 state 정리
        if (this.currentState) {
            try {
                this.currentState.destroy();
            } catch (e) {
                console.error('CrmPreview: 기존 state destroy 실패', e);
            }
            this.currentState = null;
        }

        this.currentSendType = type;

        // 새 state 생성 (모든 State는 동일한 (container, data, options) 인터페이스)
        const stateOptions = { showAdLabel: this.showAdLabel };

        switch (type) {
            case CRM_SEND_METHOD_TYPE.SMS:
                this.currentState = new SmsState(this.contentWrap, this.data, stateOptions);
                break;
            case CRM_SEND_METHOD_TYPE.FRIENDTALK:
                this.currentState = new FriendtalkState(this.contentWrap, this.data, stateOptions);
                break;
            case CRM_SEND_METHOD_TYPE.ALIMTALK:
                this.currentState = new AlimtalkState(this.contentWrap, this.data, stateOptions);
                break;
            case CRM_SEND_METHOD_TYPE.MYAPP:
                this.currentState = new MyappState(this.contentWrap, this.data, stateOptions);
                break;
        }

        return this;
    }

    /**
     * 메시지 타입 설정 (FRIENDTALK 전용)
     * @param {string} type - 'TEXT' | 'IMAGE' | 'WIDE_IMAGE' | 'WIDE_ITEM_LIST' | 'CAROUSEL_FEED'
     * @returns {CrmPreview}
     */
    setMessageType(type) {
        if (this.currentState && typeof this.currentState.setMessageType === 'function') {
            this.currentState.setMessageType(type);
        }
        return this;
    }

    /**
     * UI 렌더링 (타입 변경 시 호출)
     * @returns {CrmPreview}
     */
    render() {
        if (this.currentState) {
            this.currentState.render();
        }
        return this;
    }

    /**
     * 제목 설정
     * @param {string} value
     * @returns {CrmPreview}
     */
    setTitle(value) {
        this.data.title = value;
        if (this.currentState) {
            this.currentState.setTitle(value);
        }
        return this;
    }

    /**
     * 내용 설정
     * @param {string} value
     * @returns {CrmPreview}
     */
    setContent(value) {
        this.data.content = value;
        if (this.currentState) {
            this.currentState.setContent(value);
        }
        return this;
    }

    /**
     * 이미지 설정
     * @param {string} src - 이미지 URL
     * @returns {CrmPreview}
     */
    setImage(src) {
        this.data.image = src;
        if (this.currentState) {
            this.currentState.setImage(src);
        }
        return this;
    }

    /**
     * 버튼 목록 설정
     * @param {Array} buttons - [{text: '버튼명', url: 'URL', type: 'WL'}, ...]
     * @returns {CrmPreview}
     */
    setButtons(buttons) {
        this.data.buttons = buttons || [];
        if (this.currentState) {
            this.currentState.setButtons(this.data.buttons);
        }
        return this;
    }

    /**
     * 쿠폰 설정 (FRIENDTALK 전용)
     * @param {Object|null} coupon - {text: '쿠폰명', date: '사용기한'}
     * @returns {CrmPreview}
     */
    setCoupon(coupon) {
        this.data.coupon = coupon;
        if (this.currentState && typeof this.currentState.setCoupon === 'function') {
            this.currentState.setCoupon(coupon);
        }
        return this;
    }

    /**
     * 아이템 리스트 설정
     * @param {Array} items - [{title: '', desc: '', image: ''}, ...]
     * @returns {CrmPreview}
     */
    setItemList(items) {
        this.data.itemList = items || [];
        if (this.currentState && typeof this.currentState.setItemList === 'function') {
            this.currentState.setItemList(this.data.itemList);
        }
        return this;
    }

    /**
     * 내용 제목 설정 (FRIENDTALK 전용)
     * @param {string} value
     * @returns {CrmPreview}
     */
    setContentTitle(value) {
        if (this.currentState && typeof this.currentState.setContentTitle === 'function') {
            this.currentState.setContentTitle(value);
        }
        return this;
    }

    /**
     * 강조 제목 설정 (ALIMTALK 전용)
     * @param {string} value
     * @returns {CrmPreview}
     */
    setEmTitle(value) {
        if (this.currentState && typeof this.currentState.setEmTitle === 'function') {
            this.currentState.setEmTitle(value);
        }
        return this;
    }

    /**
     * 강조 부제목 설정 (ALIMTALK 전용)
     * @param {string} value
     * @returns {CrmPreview}
     */
    setEmSubTitle(value) {
        if (this.currentState && typeof this.currentState.setEmSubTitle === 'function') {
            this.currentState.setEmSubTitle(value);
        }
        return this;
    }

    /**
     * 리스트 헤더 설정 (ALIMTALK 전용)
     * @param {string} value
     * @returns {CrmPreview}
     */
    setListHeader(value) {
        if (this.currentState && typeof this.currentState.setListHeader === 'function') {
            this.currentState.setListHeader(value);
        }
        return this;
    }

    /**
     * 추가 정보 설정 (ALIMTALK 전용)
     * @param {string} value
     * @returns {CrmPreview}
     */
    setExtraInfo(value) {
        if (this.currentState && typeof this.currentState.setExtraInfo === 'function') {
            this.currentState.setExtraInfo(value);
        }
        return this;
    }

    /**
     * 채널 메시지 설정 (ALIMTALK 전용)
     * @param {string} value
     * @returns {CrmPreview}
     */
    setChannelMessage(value) {
        if (this.currentState && typeof this.currentState.setChannelMessage === 'function') {
            this.currentState.setChannelMessage(value);
        }
        return this;
    }

    /**
     * 하이라이트 제목 설정 (ALIMTALK 전용)
     * @param {string} value
     * @returns {CrmPreview}
     */
    setHighlightTitle(value) {
        if (this.currentState && typeof this.currentState.setHighlightTitle === 'function') {
            this.currentState.setHighlightTitle(value);
        }
        return this;
    }

    /**
     * 하이라이트 설명 설정 (ALIMTALK 전용)
     * @param {string} value
     * @returns {CrmPreview}
     */
    setHighlightDesc(value) {
        if (this.currentState && typeof this.currentState.setHighlightDesc === 'function') {
            this.currentState.setHighlightDesc(value);
        }
        return this;
    }

    /**
     * 하이라이트 이미지 설정 (ALIMTALK 전용)
     * @param {string} src
     * @returns {CrmPreview}
     */
    setHighlightImage(src) {
        if (this.currentState && typeof this.currentState.setHighlightImage === 'function') {
            this.currentState.setHighlightImage(src);
        }
        return this;
    }

    /**
     * 변수 모드 설정 (변수 그대로 표시 vs 샘플 데이터로 치환)
     * @param {boolean} enabled
     * @returns {CrmPreview}
     */
    setVariableMode(enabled) {
        this.data.variableMode = enabled;
        if (this.currentState && typeof this.currentState.setVariableMode === 'function') {
            this.currentState.setVariableMode(enabled);
        }
        return this;
    }

    /**
     * 슬라이드 인덱스 설정 (FRIENDTALK 전용)
     * @param {number} index
     * @returns {CrmPreview}
     */
    setSlideIndex(index) {
        this.data.slideIndex = index;
        if (this.currentState && typeof this.currentState.setSlideIndex === 'function') {
            this.currentState.setSlideIndex(index);
        }
        return this;
    }

    /**
     * 슬라이드 추가 (FRIENDTALK 전용)
     * @returns {CrmPreview}
     */
    addSlide() {
        if (this.currentState && typeof this.currentState.addSlide === 'function') {
            this.currentState.addSlide();
        }
        return this;
    }

    /**
     * 슬라이드 삭제 (FRIENDTALK 전용)
     * @param {number} index
     * @returns {CrmPreview}
     */
    removeSlide(index) {
        if (this.currentState && typeof this.currentState.removeSlide === 'function') {
            this.currentState.removeSlide(index);
        }
        return this;
    }

    /**
     * 현재 슬라이드 인덱스 반환 (FRIENDTALK 전용)
     * @returns {number}
     */
    getCurrentSlideIndex() {
        if (this.currentState && typeof this.currentState.getCurrentSlideIndex === 'function') {
            return this.currentState.getCurrentSlideIndex();
        }
        return 0;
    }

    /**
     * 슬라이드 개수 반환 (FRIENDTALK 전용)
     * @returns {number}
     */
    getSlideCount() {
        if (this.currentState && typeof this.currentState.getSlideCount === 'function') {
            return this.currentState.getSlideCount();
        }
        return 0;
    }

    /**
     * 현재 발송 타입 반환
     * @returns {string|null}
     */
    getSendType() {
        return this.currentSendType;
    }

    /**
     * 현재 메시지 타입 반환 (FRIENDTALK 전용)
     * @returns {string|null}
     */
    getMessageType() {
        if (this.currentState && typeof this.currentState.getMessageType === 'function') {
            return this.currentState.getMessageType();
        }
        return null;
    }

    /**
     * 이미지가 설정되어 있는지 확인
     * @returns {boolean}
     */
    hasImage() {
        return !!this.data.image;
    }

    /**
     * 현재 데이터 반환
     * @returns {Object}
     */
    getData() {
        return { ...this.data };
    }

    /**
     * Notice 영역 설정 (텍스트 또는 HTML 엘리먼트)
     * @param {string|HTMLElement} content - notice 텍스트 또는 HTML 엘리먼트
     * @returns {CrmPreview}
     */
    setNoticeText(content) {
        if (!this.noticeElement) {
            return this;
        }

        // 기존 내용 비우기
        this.noticeElement.innerHTML = '';

        if (typeof content === 'string') {
            // 문자열인 경우 innerHTML로 삽입 (HTML 태그 포함 가능)
            this.noticeElement.innerHTML = content;
            this.noticeText = content;
        } else if (content instanceof HTMLElement) {
            // HTMLElement인 경우 appendChild로 삽입
            this.noticeElement.appendChild(content);
            this.noticeText = content.outerHTML;
        } else {
            console.error('CrmPreview: setNoticeText는 문자열 또는 HTMLElement를 받아야 합니다.');
        }

        return this;
    }

    /**
     * 체크박스 라벨 텍스트 설정
     * @param {string} text - 체크박스 라벨 텍스트
     * @returns {CrmPreview}
     */
    setCheckboxText(text) {
        this.checkboxText = text;
        if (this.checkboxElement) {
            const textSpan = this.checkboxElement.parentElement.parentElement.querySelector('.ncua-checkbox-field__text');
            if (textSpan) {
                textSpan.textContent = text;
            }
        }
        return this;
    }

    /**
     * 변수 체크박스 변경 시 콜백 함수 등록
     * @param {Function} callback - 체크박스 변경 시 실행할 콜백 (isChecked 인자를 받음)
     * @returns {CrmPreview}
     */
    onVariableCheckboxChange(callback) {
        if (typeof callback !== 'function') {
            console.error('CrmPreview: onVariableCheckboxChange에 함수를 전달해야 합니다.');
            return this;
        }
        this.variableCheckboxCallback = callback;
        return this;
    }

    /**
     * 체크박스 체크 상태 설정
     * @param {boolean} checked - 체크 여부
     * @returns {CrmPreview}
     */
    setCheckboxChecked(checked) {
        if (this.checkboxElement) {
            this.checkboxElement.checked = !!checked;
            this.data.variableMode = !!checked;
        }
        return this;
    }

    /**
     * 체크박스 체크 상태 반환
     * @returns {boolean}
     */
    isCheckboxChecked() {
        return this.checkboxElement ? this.checkboxElement.checked : false;
    }

    /**
     * 수신거부 방법 설정 (Myapp 전용)
     * @param {string} value - 수신거부 방법 텍스트
     * @returns {CrmPreview}
     */
    setWithdrawalMethod(value) {
        if (this.currentState && typeof this.currentState.setWithdrawalMethod === 'function') {
            this.currentState.setWithdrawalMethod(value);
        }
        return this;
    }

    /**
     * 광고 라벨 표시 여부 설정 (Myapp 전용)
     * @param {boolean} show
     * @returns {CrmPreview}
     */
    setShowAdLabel(show) {
        this.showAdLabel = show;
        if (this.currentState && typeof this.currentState.setShowAdLabel === 'function') {
            this.currentState.setShowAdLabel(show);
        }
        return this;
    }

    /**
     * 리셋 (초기화)
     * @returns {CrmPreview}
     */
    reset() {
        // 데이터 초기화
        this.data = {
            title: '',
            content: '',
            image: '',
            buttons: [],
            coupon: null,
            itemList: [],
            slideIndex: 0,
            variableMode: false
        };

        // State의 reset 메서드 호출 (각 State가 자신의 UI 초기화)
        if (this.currentState && typeof this.currentState.reset === 'function') {
            this.currentState.reset();
        }
        
        // 체크박스 초기화
        if (this.checkboxElement) {
            this.checkboxElement.checked = false;
        }
        
        return this;
    }

    /**
     * 리소스 정리
     */
    destroy() {
        // 현재 state 정리
        if (this.currentState) {
            this.currentState.destroy();
            this.currentState = null;
        }

        // 체크박스 이벤트 리스너 제거
        if (this.checkboxElement && this._onCheckboxChange) {
            this.checkboxElement.removeEventListener('change', this._onCheckboxChange);
            this._onCheckboxChange = null;
        }

        // DOM 정리
        if (this.container) {
            this.container.innerHTML = '';
        }

        this.contentWrap = null;
        this.noticeElement = null;
        this.checkboxElement = null;
        this.currentSendType = null;
        this.variableCheckboxCallback = null;
    }
}

// Window 전역 객체로 노출
window.CrmPreview = CrmPreview;
