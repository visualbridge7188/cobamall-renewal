/**
 * CRM Preview Library Loader
 * 모든 필요한 스크립트를 자동으로 로드하는 헬퍼
 * 
 * 사용법 1 - 모든 State 로드:
 * <script src="gd_share/ncds/script/common/crm-preview/crm-preview-loader.js"></script>
 * 
 * 사용법 2 - 특정 State 제외:
 * <script src="crm-preview-loader.js" data-exclude="friendtalk"></script>
 * <script src="crm-preview-loader.js" data-exclude="friendtalk,myapp"></script>
 * 
 * 또는 수동 로드:
 * <script src="utils/sanitize-html.js"></script>
 * <script src="constants.js"></script>
 * <script src="states/sms-state.js"></script>
 * <script src="states/friendtalk-state.js"></script>
 * <script src="states/alimtalk-state.js"></script>
 * <script src="states/myapp-state.js"></script>
 * <script src="crm-preview.js"></script>
 */

(function() {
    'use strict';
    
    // 현재 스크립트의 경로를 가져옴
    const currentScript = document.currentScript || document.querySelector('script[src*="crm-preview-loader"]');
    if (!currentScript) {
        console.error('CrmPreviewLoader: 현재 스크립트를 찾을 수 없습니다.');
        return;
    }
    
    const scriptPath = currentScript.src;
    const basePath = scriptPath.substring(0, scriptPath.lastIndexOf('/') + 1);
    
    // data-exclude 속성 읽기
    const excludeAttr = currentScript.getAttribute('data-exclude') || '';
    const excludeList = excludeAttr.split(',').map(s => s.trim().toLowerCase());
    
    // 모든 스크립트 정의
    const allScripts = {
        'sanitizer': 'utils/sanitize-html.js',
        'constants': 'constants.js',
        'sms': 'states/sms-state.js',
        'friendtalk': 'states/friendtalk-state.js',
        'alimtalk': 'states/alimtalk-state.js',
        'myapp': 'states/myapp-state.js',
        'core': 'crm-preview.js'
    };

    // 제외할 것 빼고 스크립트 목록 생성 (sanitizer, constants, core는 항상 포함)
    const scripts = Object.entries(allScripts)
        .filter(([key]) => !excludeList.includes(key) || key === 'sanitizer' || key === 'constants' || key === 'core')
        .map(([, path]) => path);
    
    const LOAD_TIMEOUT = 10000; // 10초
    const loadErrors = [];

    /**
     * 스크립트 로드
     */
    function loadScript(src, callback) {
        const script = document.createElement('script');
        script.src = src;

        let handled = false;
        const timer = setTimeout(function() {
            if (handled) return;
            handled = true;
            var msg = 'CrmPreviewLoader: 스크립트 로드 타임아웃 (' + LOAD_TIMEOUT + 'ms) - ' + src;
            console.error(msg);
            loadErrors.push({ src: src, error: 'timeout' });
            callback();
        }, LOAD_TIMEOUT);

        script.onload = function() {
            if (handled) return;
            handled = true;
            clearTimeout(timer);
            callback();
        };
        script.onerror = function() {
            if (handled) return;
            handled = true;
            clearTimeout(timer);
            console.error('CrmPreviewLoader: 스크립트 로드 실패 -', src);
            loadErrors.push({ src: src, error: 'load_failed' });
            callback();
        };
        document.head.appendChild(script);
    }
    
    /**
     * 순차적으로 스크립트 로드
     */
    function loadNext(index) {
        if (index >= scripts.length) {
            
            // 로드 완료 이벤트 발생 (에러 정보 포함)
            if (typeof window.CustomEvent === 'function') {
                const event = new CustomEvent('crmPreviewLoaded', {
                    detail: {
                        CrmPreview: window.CrmPreview,
                        errors: loadErrors.length > 0 ? loadErrors : null
                    }
                });
                window.dispatchEvent(event);
            }
            return;
        }
        
        const scriptSrc = basePath + scripts[index];
        loadScript(scriptSrc, function() {
            loadNext(index + 1);
        });
    }
    
    // 로드 시작
    loadNext(0);
    
})();
