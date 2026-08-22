/**
 * NCDS 버튼 생성 유틸리티
 */

(function() {
    'use strict';

    /**
     * NCDS 버튼 생성
     * @param {string} label - 버튼 라벨
     * @param {string} hierarchy - 버튼 계층 ('primary' | 'secondary')
     * @param {Function} onClick - 클릭 핸들러
     * @param {string} [size='md'] - 버튼 크기
     * @returns {HTMLButtonElement}
     */
    const createButton = (label, hierarchy, onClick, size = 'md') => {
        const htmlTextToElement = window.htmlTextToElement || ((htmlText) => {
            const template = document.createElement('template');
            template.innerHTML = htmlText.trim();
            return template.content.firstElementChild;
        });

        const button = htmlTextToElement(`
            <button class="ncua-btn ncua-btn--${size} ncua-btn--${hierarchy}">
                <span class="ncua-btn__label">${label}</span>
            </button>
        `);
        
        button.addEventListener('click', onClick);
        return button;
    };

    /**
     * ncua-select-delete 버튼의 활성/비활성 상태를 업데이트
     * @param {HTMLElement} button - 업데이트할 버튼 요소
     */
    const updateSelectDeleteButtonState = (button) => {
        // 버튼의 부모인 ncua-search-result__actions 찾기
        const actionsContainer = button.closest('.ncua-search-result__actions');
        if (!actionsContainer) return;

        // actions의 부모 요소에서 형제인 ncua-table 찾기
        const parentContainer = actionsContainer.parentElement;
        if (!parentContainer) return;

        const tableContainer = parentContainer.querySelector('.ncua-table');
        if (!tableContainer) return;

        // 테이블 내의 첫 번째 td에 있는 체크박스 찾기
        const checkboxes = tableContainer.querySelectorAll('table tr td:first-child input[type="checkbox"]');
        
        // 체크된 체크박스가 있는지 확인
        let hasChecked = false;
        checkboxes.forEach(checkbox => {
            if (checkbox.checked) {
                hasChecked = true;
            }
        });

        // 체크된 체크박스가 없으면 disabled 추가 및 is-disable 클래스 추가
        if (!hasChecked) {
            button.disabled = true;
            button.classList.add('is-disable');
        } else {
            button.disabled = false;
            button.classList.remove('is-disable');
        }
    };

    // 이미 초기화된 버튼을 추적하기 위한 Set
    const initializedButtons = new WeakSet();
    // 테이블 컨테이너별 이벤트 핸들러를 저장
    const tableEventHandlers = new WeakMap();

    /**
     * 체크박스 변경 이벤트 핸들러
     * @param {HTMLElement} parentContainer - 부모 컨테이너 요소
     * @returns {Function} 이벤트 핸들러 함수
     */
    const createCheckboxChangeHandler = (parentContainer) => {
        return (e) => {
            const checkbox = e.target;
            if (checkbox && checkbox.type === 'checkbox') {
                const td = checkbox.closest('td');
                const th = checkbox.closest('th');
                const tr = checkbox.closest('tr');
                
                // th에 있는 전체 선택 체크박스인지 확인
                const isHeaderCheckbox = th && tr && th === tr.querySelector('th:first-child');
                
                // 첫 번째 td의 체크박스인지 확인
                const isRowCheckbox = td && tr && td === tr.querySelector('td:first-child');
                
                if (isHeaderCheckbox || isRowCheckbox) {
                    // 해당 테이블과 연결된 모든 버튼의 상태 업데이트
                    const relatedButtons = parentContainer.querySelectorAll('.ncua-select-delete');
                    relatedButtons.forEach(btn => {
                        // 약간의 지연을 두어 다른 스크립트의 이벤트 처리 후 실행
                        setTimeout(() => {
                            updateSelectDeleteButtonState(btn);
                        }, 10);
                    });
                }
            }
        };
    };

    /**
     * ncua-select-delete 버튼들에 대한 체크박스 상태 감지 초기화
     */
    const initSelectDeleteButtons = () => {
        const selectDeleteButtons = document.querySelectorAll('.ncua-select-delete');

        if (!selectDeleteButtons.length) return;

        selectDeleteButtons.forEach(button => {
            // 이미 초기화된 버튼은 스킵
            if (initializedButtons.has(button)) {
                // 상태만 업데이트
                setTimeout(() => {
                    updateSelectDeleteButtonState(button);
                }, 100);
                return;
            }

            // 버튼의 부모인 ncua-search-result__actions 찾기
            const actionsContainer = button.closest('.ncua-search-result__actions');
            if (!actionsContainer) return;

            // actions의 부모 요소에서 형제인 ncua-table 찾기
            const parentContainer = actionsContainer.parentElement;
            if (!parentContainer) return;

            const tableContainer = parentContainer.querySelector('.ncua-table');
            if (!tableContainer) return;

            // 초기 상태 설정 (약간의 지연을 두어 DOM이 완전히 로드된 후 실행)
            setTimeout(() => {
                updateSelectDeleteButtonState(button);
            }, 100);

            // 이벤트 위임을 사용하여 동적으로 추가되는 체크박스도 감지
            // change 이벤트와 click 이벤트 모두 감지 (다른 스크립트가 이벤트를 가로챌 수 있음)
            let handleCheckboxChange = tableEventHandlers.get(tableContainer);
            if (!!handleCheckboxChange) {
                initializedButtons.add(button);
                
                return;
            }
            
            // 테이블 컨테이너당 하나의 이벤트 핸들러만 등록
            handleCheckboxChange = createCheckboxChangeHandler(parentContainer);

            // 이벤트 캡처 단계에서도 감지하여 다른 스크립트가 이벤트를 막아도 처리 가능하도록
            tableContainer.addEventListener('change', handleCheckboxChange, true);
            tableContainer.addEventListener('click', handleCheckboxChange, true);
            
            tableEventHandlers.set(tableContainer, handleCheckboxChange);
            

            // 버튼을 초기화된 것으로 표시
            initializedButtons.add(button);
        });
    };

    // DOMContentLoaded 시 초기화
    const initOnReady = () => {
        initSelectDeleteButtons();
        
        // 동적으로 표시되는 요소를 감지하기 위해 MutationObserver 사용
        const observer = new MutationObserver((mutations) => {
            mutations.forEach((mutation) => {
                // 자식 노드가 추가될 때 (동적으로 행이 추가될 때)
                if (mutation.addedNodes.length > 0) {
                    mutation.addedNodes.forEach((node) => {
                        const IS_ELEMENT_NODE = node.nodeType === 1;
                        if (IS_ELEMENT_NODE && node.matches && 
                            (node.matches('tr') || node.querySelector('tr'))) {
                            setTimeout(() => {
                                initSelectDeleteButtons();
                            }, 100);
                        }
                    });
                }
            });
        });


        // 테이블 컨테이너도 관찰하여 동적으로 추가되는 행 감지
        document.querySelectorAll('.ncua-table').forEach((table) => {
            observer.observe(table, {
                childList: true,
                subtree: true
            });
        });
    };

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initOnReady);
    } else {
        initOnReady();
    }

    // 전역 노출
    window.createButton = createButton;
    window.updateSelectDeleteButtonState = updateSelectDeleteButtonState;
    window.initSelectDeleteButtons = initSelectDeleteButtons;
})();

