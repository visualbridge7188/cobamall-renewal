/**
 * NCDS 테이블 체크박스 동기화 유틸리티
 * 
 * ncua-table 클래스 안에 있는 table에서:
 * - 전체 체크박스: th 안에 있는 input checkbox
 * - 개별 체크박스: 첫번째 td 안에 있는 input checkbox
 * 
 * 전체 체크박스가 체크되어 있고 개별 체크박스를 체크해제하면,
 * 전체 체크박스도 자동으로 해제됩니다.
 * 
 * 사용법:
 * initTableCheckboxSync(containerSelector);
 * 
 * 예시:
 * initTableCheckboxSync('#exceptGoodsTable');
 * initTableCheckboxSync('.ncua-table');
 */

(function() {
    'use strict';

    /**
     * 테이블 체크박스 동기화 초기화
     * @param {string|HTMLElement} container - 컨테이너 셀렉터 또는 요소
     */
    const initTableCheckboxSync = (container) => {
        // 컨테이너 요소 찾기
        const containerElement = typeof container === 'string' 
            ? document.querySelector(container) 
            : container;

        if (!containerElement) {
            return;
        }

        // ncua-table 클래스를 가진 테이블 찾기
        const table = containerElement.querySelector('.ncua-table table') || 
                     containerElement.querySelector('table');

        if (!table) {
            return;
        }

        // 전체 체크박스 찾기 (th 안에 있는 input checkbox)
        const allCheckbox = table.querySelector('th input[type="checkbox"]');

        if (!allCheckbox) {
            return;
        }

        // 개별 체크박스 찾기 (첫번째 td 안에 있는 input checkbox)
        const individualCheckboxes = table.querySelectorAll('tbody tr td:first-child input[type="checkbox"]');

        if (individualCheckboxes.length === 0) {
            return;
        }

        // 각 개별 체크박스에 이벤트 리스너 추가
        individualCheckboxes.forEach(function(checkbox) {
            checkbox.addEventListener('change', function() {
                // 개별 체크박스가 체크해제되고, 전체 체크박스가 체크되어 있는 경우
                if (!this.checked && allCheckbox.checked) {
                    allCheckbox.checked = false;
                    // change 이벤트 트리거 (다른 리스너가 있을 수 있음)
                    allCheckbox.dispatchEvent(new Event('change', { bubbles: true }));
                }
            });
        });
    };

    /**
     * 여러 컨테이너에 대해 테이블 체크박스 동기화 초기화
     * @param {string|HTMLElement|Array} containers - 컨테이너 셀렉터, 요소, 또는 배열
     */
    const initTableCheckboxSyncMultiple = (containers) => {
        if (Array.isArray(containers)) {
            containers.forEach(container => initTableCheckboxSync(container));
        } else if (typeof containers === 'string') {
            // 셀렉터로 여러 요소 찾기
            const elements = document.querySelectorAll(containers);
            elements.forEach(element => initTableCheckboxSync(element));
        } else {
            initTableCheckboxSync(containers);
        }
    };

    /**
     * 페이지 내 모든 ncua-table에 대해 자동 초기화
     */
    const initAllTableCheckboxSync = () => {
        const tables = document.querySelectorAll('.ncua-table');

        if (!tables.length) return;
        
        tables.forEach(table => {
            // 이미 초기화된 테이블인지 확인 (data-checkbox-sync 속성으로 체크)
            if (!table.hasAttribute('data-checkbox-sync-initialized')) {
                initTableCheckboxSync(table);
                table.setAttribute('data-checkbox-sync-initialized', 'true');
            }
        });
    };

    // 전역으로 노출
    window.initNcdsTableCheckboxSync = initTableCheckboxSync;
    window.initNcdsTableCheckboxSyncMultiple = initTableCheckboxSyncMultiple;
    window.initNcdsAllTableCheckboxSync = initAllTableCheckboxSync;

    // DOM이 준비되면 자동 초기화
    const isDomReady = document.readyState === 'complete' || 
                      document.readyState === 'interactive';
    
    if (isDomReady) {
        initAllTableCheckboxSync();
    } else {
        document.addEventListener('DOMContentLoaded', initAllTableCheckboxSync);
    }
})();

