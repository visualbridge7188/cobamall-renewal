/**
 * GodoUIModule - 동적 UI 모듈 렌더링 역할
 */
const GodoUIModule = (function () {
    'use strict';

    /**
     * config 유효성 검증 (target 필수)
     * @param {Object} config
     * @returns {boolean} 유효하면 true, 아니면 false
     */
    function validateConfig(config) {
        if (!config || !config.target) {
            console.error('GodoUIModule: target is required');
            return false;
        }
        return true;
    }

    // ========================================
    // ChipSelector UI 모듈 (ChipSelector 어댑터)
    // ========================================

    /**
     * ChipSelector UI 모듈 렌더링
     * ChipSelector 인스턴스를 생성하여 반환합니다.
     * 
     * @param {Object} config
     * @param {string} config.target - 렌더링 대상 셀렉터
     * @param {string} config.title - 섹션 제목
     * @param {string} config.tooltipSeq - 툴팁 시퀀스 (optional)
     * @param {string} config.cautionHTML - 안내 텍스트 HTML (optional)
     * @param {Array} config.categories - 카테고리 목록
     * @param {Function} config.onSelect - chip 선택 시 콜백
     * @param {Function} config.onCategoryChange - 카테고리 변경 시 콜백 (optional)
     * @returns {ChipSelector} ChipSelector 인스턴스
     */
    function renderChipSelector(config) {
        if (!validateConfig(config)) return null;

        // ChipSelector 클래스 존재 확인
        if (typeof ChipSelector === 'undefined') {
            console.error('GodoUIModule: ChipSelector is not defined. Please load chip-selector.js first.');
            return null;
        }

        // ChipSelector 인스턴스 생성
        const chipSelector = new ChipSelector({
            container: config.target,
            title: config.title,
            tooltipSeq: config.tooltipSeq,
            cautionHTML: config.cautionHTML,
            categories: config.categories,
            onSelect: config.onSelect,
            onCategoryChange: config.onCategoryChange,
            hideCategorySelector: config.hideCategorySelector
        });

        // ChipSelector 인스턴스를 그대로 반환
        // 이후 모든 메서드는 ChipSelector API를 직접 사용
        return chipSelector;
    }

    // ========================================
    // MessageInput UI 모듈 (MessageInput 어댑터)
    // ========================================

    /**
     * MessageInput UI 모듈 렌더링
     * MessageInput 인스턴스를 생성하여 반환합니다.
     * 
     * @param {Object} config
     * @param {string} config.target - 렌더링 대상 셀렉터
     * @param {string} config.name - textarea name 속성 (기본: 'messageContent')
     * @param {string} config.placeholder - placeholder 텍스트
     * @param {string} config.hintText - 힌트 텍스트 (선택사항)
     * @param {number} config.maxLength - 최대 길이 (기본: 2000)
     * @param {boolean} config.useBytes - bytes 모드 사용 여부 (기본: false)
     * @param {number} config.bytesThreshold - bytes 임계값 (기본: 90)
     * @param {string} config.initialValue - 초기 값 (선택사항)
     * @param {Function} config.onInput - input 이벤트 콜백
     * @param {Function} config.onChange - change 이벤트 콜백
     * @returns {MessageInput} MessageInput 인스턴스
     */
    function renderMessageInput(config) {
        if (!validateConfig(config)) return null;

        // MessageInput 클래스 존재 확인
        if (typeof MessageInput === 'undefined') {
            console.error('GodoUIModule: MessageInput is not defined. Please load message-input.js first.');
            return null;
        }

        // MessageInput 인스턴스 생성
        const messageInput = new MessageInput({
            container: config.target,
            name: config.name,
            placeholder: config.placeholder,
            hintText: config.hintText,
            maxLength: config.maxLength,
            useBytes: config.useBytes,
            bytesThreshold: config.bytesThreshold,
            allowEmoji: config.allowEmoji,
            initialValue: config.initialValue,
            onInput: config.onInput,
            onChange: config.onChange
        });

        // MessageInput 인스턴스를 그대로 반환
        // 이후 모든 메서드는 MessageInput API를 직접 사용
        return messageInput;
    }

    // ========================================
    // MessagePreview UI 모듈 (CrmPreview 어댑터)
    // ========================================

    /**
     * MessagePreview UI 모듈 렌더링
     * CrmPreview 인스턴스를 생성하여 반환합니다.
     * 
     * @param {Object} config
     * @param {string} config.target - 렌더링 대상 셀렉터
     * @param {string} config.sendType - 발송 타입 ('SMS' | 'FRIENDTALK' | 'ALIMTALK' | 'MYAPP')
     * @param {string} config.messageType - 친구톡 메시지 타입 (optional, FRIENDTALK일 때만 사용)
     * @param {string} config.channelName - 채널명 (사용 안 함, 호환성 유지)
     * @param {Object} config.data - 미리보기 초기 데이터 (optional)
     * @param {string} config.noticeText - Notice 텍스트 (optional)
     * @param {string} config.checkboxText - Checkbox 라벨 텍스트 (optional)
     * @param {boolean} config.showAdLabel - 마이앱 제목에 (광고) 표시 여부 (기본: false)
     * @param {boolean} config.showVariableToggle - 변수 변환 체크박스 표시 여부 (사용 안 함, 항상 표시)
     * @param {Function} config.onVariableToggle - 변수 변환 토글 콜백
     * @returns {CrmPreview} CrmPreview 인스턴스
     */
    function renderMessagePreview(config) {
        if (!validateConfig(config)) return null;

        // CrmPreview 클래스 존재 확인
        if (typeof CrmPreview === 'undefined') {
            console.error('GodoUIModule: CrmPreview is not defined. Please load crm-preview.js first.');
            return null;
        }

        // CrmPreview 인스턴스 생성
        const crmPreview = new CrmPreview({
            container: config.target,
            noticeText: config.noticeText,
            checkboxText: config.checkboxText,
            showAdLabel: config.showAdLabel
        });

        // 초기 발송 타입 설정 및 렌더링
        if (config.sendType) {
            crmPreview.setSendType(config.sendType).render();
            
            // 친구톡인 경우 메시지 타입 설정
            if (config.sendType === 'FRIENDTALK' && config.messageType) {
                crmPreview.setMessageType(config.messageType).render();
            }
        }

        // 초기 데이터 적용
        if (config.data) {
            if (config.data.title) crmPreview.setTitle(config.data.title);
            if (config.data.content) crmPreview.setContent(config.data.content);
            if (config.data.image) crmPreview.setImage(config.data.image);
            if (config.data.buttons) crmPreview.setButtons(config.data.buttons);
            if (config.data.coupon) crmPreview.setCoupon(config.data.coupon);
            if (config.data.itemList) crmPreview.setItemList(config.data.itemList);
        }

        // 변수 토글 콜백 등록
        if (typeof config.onVariableToggle === 'function') {
            crmPreview.onVariableCheckboxChange(config.onVariableToggle);
        }

        // CrmPreview 인스턴스를 그대로 반환
        // 이후 모든 메서드는 CrmPreview API를 직접 사용
        return crmPreview;
    }

    /**
     * 메인 render 함수
     */
    function render(config) {
        if (!config || !config.type) {
            console.error('GodoUIModule: type is required');
            return null;
        }

        switch (config.type) {
            case 'ChipSelector':
                return renderChipSelector(config);
            case 'MessageInput':
                return renderMessageInput(config);
            case 'MessagePreview':
                return renderMessagePreview(config);
            default:
                console.error('GodoUIModule: unknown type -', config.type);
                return null;
        }
    }

    // Public API
    return {
        render
    };
})();

// CommonJS/AMD 지원
if (typeof module !== 'undefined' && module.exports) {
    module.exports = GodoUIModule;
}
