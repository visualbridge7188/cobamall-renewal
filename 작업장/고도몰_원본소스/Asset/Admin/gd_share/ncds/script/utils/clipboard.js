/**
 * NCDS Clipboard Utility
 * 
 * 클립보드 복사 기능을 제공하는 유틸리티
 * Clipboard.js 라이브러리 사용
 */

(function() {
    'use strict';

    /**
     * NCDS 클립보드 기능 초기화
     * .ncua-clipboard 클래스를 가진 요소들에 클립보드 복사 기능을 추가
     * 중복 초기화 방지: 이미 초기화된 경우 기존 인스턴스를 제거하고 새로 생성
     */
    let clipboardInstance = null;
    let isInitialized = false;

    /**
     * 클립보드 기능 초기화
     */
    const initClipboard = () => {
        // Clipboard.js가 로드되었는지 확인
        if (typeof Clipboard === 'undefined') {
            return;
        }

        // .ncua-clipboard 클래스를 가진 모든 요소에 클립보드 기능 추가
        const clipboardElements = document.querySelectorAll('.ncua-clipboard');
        
        if (!clipboardElements.length) return;

        // 기존 인스턴스가 있으면 제거 (중복 이벤트 방지)
        if (clipboardInstance && typeof clipboardInstance.destroy === 'function') {
            clipboardInstance.destroy();
            clipboardInstance = null;
            isInitialized = false;
        }

        // 이미 초기화되었고 인스턴스가 있으면 중복 초기화 방지
        if (isInitialized && clipboardInstance) {
            return;
        }

        clipboardInstance = new Clipboard('.ncua-clipboard');
        
        clipboardInstance.on('success', (e) => {
            const title = `복사되었습니다.`;
            
            typeof window.NCDSAlert === 'function' 
                ? window.NCDSToast({message: title, color: 'success'})
                : alert(title);
                
            e.clearSelection();
        });
        
        clipboardInstance.on('error', (e) => {
            console.error('클립보드 복사 실패:', e.action, e.trigger);
            
            const title = '복사에 실패했습니다.';
            typeof window.NCDSAlert === 'function' 
                ? window.NCDSToast({message: title, color: 'error'})
                : alert(title);
        });

        isInitialized = true;
    };

    // 전역으로 노출
    window.initClipboard = initClipboard;

    /**
     * 클립보드 기능 초기화 해제 ( 별도 초기화 필요 시 사용 )
     */
    const destroyClipboard = () => {
        if (clipboardInstance && typeof clipboardInstance.destroy === 'function') {
            clipboardInstance.destroy();
            clipboardInstance = null;
            isInitialized = false;
        }
    };

    window.destroyClipboard = destroyClipboard;

    // DOM이 로드되면 자동으로 초기화
    const initializeOnReady = () => {
        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', initClipboard);
        } else {
            initClipboard();
        }
    };

    initializeOnReady();
})();
