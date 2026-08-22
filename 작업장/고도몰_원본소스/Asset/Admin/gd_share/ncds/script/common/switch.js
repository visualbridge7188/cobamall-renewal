/**
 * NCDS Switch 컴포넌트 상태 관리
 */

(function() {
    'use strict';

    const SELECTORS = {
        SWITCH: '.ncua-switch',
        OPTION: '.ncua-switch__option',
        RADIO: 'input[type="radio"]',
        CHECKED_RADIO: 'input[type="radio"]:checked'
    };

    const CLASSES = {
        ACTIVE: 'ncua-switch__option--active',
        INACTIVE: 'ncua-switch__option--inactive'
    };

    /**
     * 스위치 옵션의 활성 상태를 업데이트
     * @param {HTMLElement} switchContainer - 스위치 컨테이너 요소
     * @param {HTMLInputElement} activeRadio - 활성화할 라디오 버튼
     */
    const updateSwitchState = (switchContainer, activeRadio) => {
        const allOptions = switchContainer.querySelectorAll(SELECTORS.OPTION);
        
        // 모든 옵션을 inactive로 초기화
        allOptions.forEach(option => {
            option.classList.remove(CLASSES.ACTIVE);
            option.classList.add(CLASSES.INACTIVE);
        });
        
        if (!activeRadio) {
            return;
        }

        const activeOption = activeRadio.closest(SELECTORS.OPTION);

        if (!activeOption) {
            return;
        }
        
        activeOption.classList.remove(CLASSES.INACTIVE);
        activeOption.classList.add(CLASSES.ACTIVE);
    };

    /**
     * 단일 스위치 컴포넌트의 초기 상태 설정
     * @param {HTMLElement} switchElement - 스위치 요소
     */
    const setInitialSwitchState = (switchElement) => {
        // checked 속성 또는 실제 checked 상태인 라디오 버튼 찾기
        const checkedRadio = switchElement.querySelector(SELECTORS.CHECKED_RADIO) ||
                            switchElement.querySelector(`${SELECTORS.RADIO}[checked]`);
        
        // 초기 상태 설정
        updateSwitchState(switchElement, checkedRadio);
        switchElement.addEventListener('change', handleDelegatedRadioChange);
    };

    /**
     * 라디오 버튼 change 이벤트 핸들러 (이벤트 위임)
     * @param {Event} event - change 이벤트
     */
    const handleDelegatedRadioChange = (event) => {
        const target = event.target;
        
        // 라디오 버튼이 아니면 무시
        if (target.type !== 'radio') return;
        
        const switchContainer = target.closest(SELECTORS.SWITCH);
        
        // 스위치 컨테이너 내부의 라디오 버튼인 경우만 처리
        if (switchContainer) {
            updateSwitchState(switchContainer, target);
        }
    };

    /**
     * 모든 스위치 컴포넌트 초기화
     */
    const initializeAllSwitches = () => {
        const switches = document.querySelectorAll(SELECTORS.SWITCH);
        
        if (switches.length === 0) {
            return;
        }
        
        // 각 스위치의 초기 상태 설정
        switches.forEach(setInitialSwitchState);
    };

    /**
     * Switch 컴포넌트 초기화 진입점
     * DOM이 준비되면 자동으로 초기화
     */
    const initSwitch = () => {
        const isDomReady = document.readyState === 'complete' || document.readyState === 'interactive';
        
        if (isDomReady) {
            initializeAllSwitches();
        } else {
            document.addEventListener('DOMContentLoaded', initializeAllSwitches);
        }
    };

    // 전역으로 노출
    window.initSwitch = initSwitch;

    // DOM이 준비되면 자동 초기화
    initSwitch();
})();
