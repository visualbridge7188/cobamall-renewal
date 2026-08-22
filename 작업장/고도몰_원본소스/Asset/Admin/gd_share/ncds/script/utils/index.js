/**
 * NCDS Utils Bundle
 * 해당 폴더에 있는 파일들은 head.php에 의해서 전체 로딩
 * 
 * 모든 유틸리티 스크립트를 동적으로 로드하는 파일
 * 
 * 일반 JavaScript에서 export/import 없이 여러 파일을 묶는 방법:
 * 1. 동적 script 태그 생성 (현재 방식)
 * 2. 순차 로드로 의존성 순서 보장
 * 3. 각 파일은 독립적으로 유지
 * 
 * 포함된 유틸리티:
 * - dom.js: DOM 조작 유틸리티
 * - button.js: 버튼 생성 유틸리티 (dom.js 의존)
 * - clipboard.js: 클립보드 복사 유틸리티
 * - checkbox-all.js: 체크박스 전체 선택 유틸리티
 * - table-checkbox-sync.js: 테이블 체크박스 동기화 유틸리티
 * - scroll.js: 스크롤 이동 유틸리티
 */

(function() {
    'use strict';

    /**
     * 스크립트 파일을 동적으로 로드 (Promise 반환)
     * @param {string} src - 스크립트 파일 경로
     * @returns {Promise} 로드 완료 Promise
     */
    const loadScript = (src) => {
        return new Promise((resolve, reject) => {
            // 이미 로드된 스크립트인지 확인
            const existingScript = document.querySelector(`script[src="${src}"]`);
            if (existingScript) {
                resolve();
                return;
            }

            const script = document.createElement('script');
            script.type = 'text/javascript';
            script.src = src;
            
            script.onload = () => resolve();
            script.onerror = () => {
                reject(new Error(`Failed to load script: ${src}`));
            };

            // head에 추가
            const head = document.head || document.getElementsByTagName('head')[0];
            head.appendChild(script);
        });
    };

    /**
     * 기본 경로 가져오기
     * @returns {string} 기본 경로
     */
    const getBasePath = () => {
        // data-base-path 속성에서 경로 가져오기 (우선순위 1)
        const scriptTag = document.querySelector('script[src*="utils/index.js"]');
        if (scriptTag && scriptTag.dataset.basePath) {
            return scriptTag.dataset.basePath;
        }
        
        // 현재 스크립트의 경로를 기준으로 상대 경로 계산 (우선순위 2)
        const currentScript = document.currentScript || 
                             (() => {
                                 const scripts = document.getElementsByTagName('script');
                                 return scripts[scripts.length - 1];
                             })();
        
        if (currentScript && currentScript.src) {
            return currentScript.src.substring(0, currentScript.src.lastIndexOf('/') + 1);
        }
        
        // 최종 폴백
        return '/admin/gd_share/ncds/script/utils/';
    };

    /**
     * 스크립트 파일들을 순차적으로 로드
     * @param {string} basePath - 기본 경로
     * @param {string[]} files - 파일명 배열
     * @returns {Promise} 모든 파일 로드 완료 Promise
     */
    const loadScriptsSequentially = async (basePath, files) => {
        for (const file of files) {
            await loadScript(basePath + file);
        }
    };

    /**
     * 스크립트 파일들을 병렬로 로드
     * @param {string} basePath - 기본 경로
     * @param {string[]} files - 파일명 배열
     * @returns {Promise} 모든 파일 로드 완료 Promise
     */
    const loadScriptsInParallel = async (basePath, files) => {
        return Promise.all(files.map(file => loadScript(basePath + file)));
    };

    /**
     * 모든 유틸리티 스크립트를 로드
     * 배열로 묶인 것들은 순차 로드, 개별 파일들은 병렬 로드
     */
    const loadUtils = () => {
        const basePath = getBasePath();
        
        // 순서가 필요한 스크립트들 (순차 로드)
        loadScriptsSequentially(basePath, ['dom.js', 'button.js'])
            .catch(error => console.error('순차 로드 오류:', error));
        // 추가로 순서가 필요한 스크립트가 있다면 아래 기입
        // loadScriptsSequentially(basePath, ['1.js', '2.js']).catch(...);

        // 독립적인 스크립트들 (병렬 로드)
        loadScriptsInParallel(basePath, ['clipboard.js', 'table-checkbox-sync.js', 'scroll.js'])
            .catch(error => console.error('병렬 로드 오류:', error));
    };

    // DOM이 준비되면 스크립트 로드 시작
    const isDomReady = document.readyState === 'complete' || 
                      document.readyState === 'interactive';
    
    if (isDomReady) {
        loadUtils();
    } else {
        document.addEventListener('DOMContentLoaded', loadUtils);
    }
})();
