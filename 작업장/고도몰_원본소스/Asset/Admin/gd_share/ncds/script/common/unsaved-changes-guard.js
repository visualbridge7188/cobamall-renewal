/**
 * 저장하지 않은 변경사항 이탈 방지 모듈 (ES6)
 * 폼 내용 변경 후 링크 클릭 시 콜백 실행
 * 뒤로가기/앞으로가기는 브라우저 기본 동작
 * 
 * @usage
 * const guard = new UnsavedChangesGuard('#frmMessageConfig', (proceed) => {
 *     NCDSConfirm({
 *         message: '페이지를 이동하시겠습니까?',
 *         subMessage: '저장하지 않은 내용이 있습니다.',
 *         callback: (result) => {
 *             if (result) proceed();
 *         }
 *     });
 * });
 */
class UnsavedChangesGuard {
    constructor(formSelector, onLeave) {
        this._form = document.querySelector(formSelector);
        
        if (!this._form) {
            console.warn(`UnsavedChangesGuard: 폼을 찾을 수 없습니다 - ${formSelector}`);
            return;
        }

        if (typeof onLeave !== 'function') {
            console.warn('UnsavedChangesGuard: onLeave 콜백이 필요합니다');
            return;
        }

        this._onLeave = onLeave;
        this._initialState = new FormData(this._form);
        this._abortController = new AbortController();
        this._isNavigating = false;
        
        this._init();
    }

    _init() {
        this._initLinkGuard();
        this._initBeforeUnload();
    }

    /**
     * FormData 직렬화 (비교용)
     */
    _serializeFormData(formData) {
        const entries = [...formData.entries()];
        entries.sort((a, b) => a[0].localeCompare(b[0]));
        return JSON.stringify(entries);
    }

    /**
     * 링크 클릭 감지
     */
    _initLinkGuard() {
        const { signal } = this._abortController;

        document.addEventListener('click', (e) => {
            const anchor = e.target.closest('a[href]');
            
            if (!anchor) return;

            const href = anchor.getAttribute('href');
            
            // 무시할 링크 패턴
            const skipConditions = [
                !href,
                href === '#',
                href.startsWith('#'),
                href.startsWith('javascript:'),
                anchor.hasAttribute('download'),
                anchor.getAttribute('target') === '_blank',
            ];
            
            if (skipConditions.some(Boolean)) {
                return;
            }
            
            if (this.isChanged() && !this._isNavigating) {
                e.preventDefault();
                e.stopPropagation();
                
                this._onLeave(() => {
                    this._isNavigating = true;
                    this.reset();
                    window.location.href = href;
                });
            }
        }, { signal, capture: true });
    }

    /**
     * 새로고침/탭 닫기 감지 (브라우저 기본 경고)
     */
    _initBeforeUnload() {
        const { signal } = this._abortController;

        window.addEventListener('beforeunload', (e) => {
            if (this.isChanged() && !this._isNavigating) {
                e.preventDefault();
                return '';
            }
        }, { signal });
    }

    /**
     * 폼 변경 여부 확인
     */
    isChanged() {
        if (!this._form) return false;
        
        const currentState = new FormData(this._form);
        return this._serializeFormData(currentState) !== this._serializeFormData(this._initialState);
    }

    /**
     * 초기 상태 리셋 (저장 후 호출)
     */
    reset() {
        if (this._form) {
            this._initialState = new FormData(this._form);
        }
    }

    /**
     * 가드 해제
     */
    destroy() {
        this._abortController?.abort();
        this._form = null;
        this._onLeave = null;
    }

    /**
     * jQuery 호환 - 폼 요소 반환
     */
    get $form() {
        return this._form ? $(this._form) : $();
    }
}

// 전역 등록
window.UnsavedChangesGuard = UnsavedChangesGuard;
