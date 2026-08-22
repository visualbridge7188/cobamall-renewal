/**
 * 스피너 모달 공통 유틸리티
 * 
 * @param {Object} options - 모달 옵션
 * @param {string} options.message - 로딩 메시지 (기본값: '로딩 중...')
 * @param {string} options.modalSize - 모달 크기 (기본값: 'sm') - 'sm' | 'md' | 'lg' | 'xl'
 * @param {string} options.spinnerSize - 스피너 크기 (기본값: 'xs') - 'xs' | 'sm' | 'md' | 'lg'
 * @param {string} options.className - 모달 클래스명 (기본값: 'ncds-spinner-modal')
 * @param {number} options.zIndex - z-index 값 (기본값: 2000)
 * @param {boolean} options.closeOnBackdropClick - 백드롭 클릭 시 닫기 여부 (기본값: true)
 * @returns {Object} 모달 인스턴스
 */
window.spinnerModal = function(options = {}) {
    const defaultOptions = {
        message: '로딩 중...',
        modalSize: 'sm',
        spinnerSize: 'sm',
        className: 'ncds-spinner-modal',
        zIndex: 2000,
        closeOnBackdropClick: true
    };
    
    const config = Object.assign({}, defaultOptions, options);
    
    const modal = new window.ncua.Modal({
        size: config.modalSize,
        className: config.className,
        zIndex: config.zIndex,
        closeOnBackdropClick: config.closeOnBackdropClick,
    });
    
    const modalContent = new window.ncua.Modal.Content(`<div class="ncua-spinner ncua-spinner--${config.spinnerSize}">
        <div class="ncua-spinner__content"><p class="ncua-spinner__text">${config.message}</p></div>
    </div>`);
    
    modalContent.appendTo(modal.getModalElement());
    
    return modal;
};

