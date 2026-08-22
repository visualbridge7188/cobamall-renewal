/**
 * NCDS 체크박스 전체 선택 기능
 * 
 * 사용법:
 * <input type="checkbox" class="ncua-checkall" data-checkbox-name="sno[]">
 * <input type="checkbox" name="sno[]" value="1">
 * <input type="checkbox" name="sno[]" value="2" data-ignore-checkbox="true">
 */

(function() {
    'use strict';

    const SELECTOR = {
        CHECKALL: '.ncua-checkall',
        CHECKBOX: 'input[type="checkbox"]'
    };

    const ATTRIBUTE = {
        CHECKBOX_NAME: 'data-checkbox-name',
        IGNORE_CHECKBOX: 'data-ignore-checkbox'
    };

    /**
     * 체크박스 전체 선택/해제 처리
     * @param {HTMLInputElement} checkAllElement - 전체 선택 체크박스 요소
     */
    const handleCheckAll = (checkAllElement) => {
        // ncua-checkall이 checkbox가 아니면 무시
        if (checkAllElement.type !== 'checkbox') {
            return;
        }

        // data-checkbox-name 속성값 가져오기
        const checkboxName = checkAllElement.getAttribute(ATTRIBUTE.CHECKBOX_NAME);
        
        if (!checkboxName) {
            return;
        }

        // 해당 name을 가진 모든 체크박스 찾기
        const targetCheckboxes = document.querySelectorAll(
            `${SELECTOR.CHECKBOX}[name="${checkboxName}"]`
        );

        if (targetCheckboxes.length === 0) {
            return;
        }

        // 전체 선택 체크박스의 현재 상태
        const isChecked = checkAllElement.checked;

        // 각 체크박스에 대해 처리
        targetCheckboxes.forEach(checkbox => {
            // data-ignore-checkbox 속성이 있으면 무시
            if (checkbox.hasAttribute(ATTRIBUTE.IGNORE_CHECKBOX)) {
                return;
            }

            // disabled 상태인 체크박스는 무시
            if (checkbox.disabled) {
                return;
            }

            // 전체 선택 체크박스 자신은 제외
            if (checkbox === checkAllElement) {
                return;
            }

            // 체크 상태 업데이트
            checkbox.checked = isChecked;

            // change 이벤트 트리거 (다른 이벤트 리스너가 있을 수 있음)
            checkbox.dispatchEvent(new Event('change', { bubbles: true }));
        });
    };

    /**
     * 개별 체크박스 변경 시 전체 선택 체크박스 상태 업데이트
     * @param {HTMLInputElement} changedCheckbox - 변경된 체크박스 요소
     */
    const updateCheckAllState = (changedCheckbox) => {
        const checkboxName = changedCheckbox.name;
        
        if (!checkboxName) {
            return;
        }

        // 같은 name을 가진 전체 선택 체크박스 찾기
        const checkAllElement = document.querySelector(
            `${SELECTOR.CHECKALL}[${ATTRIBUTE.CHECKBOX_NAME}="${checkboxName}"]`
        );

        if (!checkAllElement || checkAllElement.type !== 'checkbox') {
            return;
        }

        // data-ignore-checkbox가 있는 체크박스는 제외하고 모든 체크박스 찾기
        const allCheckboxes = Array.from(
            document.querySelectorAll(`${SELECTOR.CHECKBOX}[name="${checkboxName}"]`)
        ).filter(checkbox => {
            // 전체 선택 체크박스 자신 제외
            if (checkbox === checkAllElement) {
                return false;
            }
            // data-ignore-checkbox 속성이 있으면 제외
            if (checkbox.hasAttribute(ATTRIBUTE.IGNORE_CHECKBOX)) {
                return false;
            }
            // disabled 상태인 체크박스는 제외
            if (checkbox.disabled) {
                return false;
            }
            return true;
        });

        if (allCheckboxes.length === 0) {
            return;
        }

        // 모든 체크박스가 선택되어 있는지 확인
        const allChecked = allCheckboxes.every(checkbox => checkbox.checked);
        
        // 전체 선택 체크박스 상태 업데이트 (이벤트 무한 루프 방지를 위해 직접 설정)
        if (checkAllElement.checked !== allChecked) {
            checkAllElement.checked = allChecked;
        }
    };

    /**
     * 전체 선택 체크박스에 이벤트 리스너 추가
     * @param {HTMLInputElement} checkAllElement - 전체 선택 체크박스 요소
     */
    const bindCheckAllEvent = (checkAllElement) => {
        checkAllElement.addEventListener('click', (event) => {
            handleCheckAll(event.target);
        });
    };

    /**
     * 개별 체크박스에 이벤트 리스너 추가 (이벤트 위임)
     */
    const bindIndividualCheckboxEvents = () => {
        document.addEventListener('change', (event) => {
            const target = event.target;
            
            // 체크박스가 아니면 무시
            if (target.type !== 'checkbox') {
                return;
            }

            // 전체 선택 체크박스가 아니면 개별 체크박스로 간주
            if (!target.classList.contains('ncua-checkall')) {
                updateCheckAllState(target);
            }
        });
    };

    /**
     * 모든 전체 선택 체크박스 초기화
     */
    const initializeAllCheckAlls = () => {
        const checkAllElements = document.querySelectorAll(SELECTOR.CHECKALL);
        
        if (checkAllElements.length === 0) {
            return;
        }

        checkAllElements.forEach(allCheckbox => {
            const attrName = allCheckbox.getAttribute(ATTRIBUTE.CHECKBOX_NAME);

            document.querySelectorAll(`[name="${attrName}"]`).forEach(checkbox => {
                checkbox.checked = allCheckbox.checked;
            });
        });

        // 각 전체 선택 체크박스에 이벤트 바인딩
        checkAllElements.forEach(bindCheckAllEvent);
    };

    /**
     * 체크박스 전체 선택 기능 초기화
     */
    const initCheckboxAll = () => {
        const isDomReady = document.readyState === 'complete' || 
                          document.readyState === 'interactive';
        
        if (isDomReady) {
            initializeAllCheckAlls();
            bindIndividualCheckboxEvents();
        } else {
            document.addEventListener('DOMContentLoaded', () => {
                initializeAllCheckAlls();
                bindIndividualCheckboxEvents();
            });
        }
    };

    // 전역으로 노출
    window.initCheckboxAll = initCheckboxAll;

    // DOM이 준비되면 자동 초기화
    initCheckboxAll();
})();
