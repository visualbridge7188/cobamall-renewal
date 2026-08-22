/**
 * 비밀번호 입력 필드 관리 기능
 * @module PasswordInputManager
 * @param {Object} options - 설정 옵션
 * @param {string} [options.targetSelector='.ncua-input-password'] - 대상 셀렉터
 * @param {string} [options.inputSelector='input[type="password"], input[type="text"]'] - input 셀렉터
 * @param {string} [options.toggleButtonSelector='button.ncua-input__password-icon'] - 토글 버튼 셀렉터
 * @param {string} [options.leftIconSelector='.ncua-input__left-icon'] - 왼쪽 아이콘 셀렉터
 * @param {string} [options.lockIconEmpty='password-lock-01.svg'] - 비어있을 때 아이콘
 * @param {string} [options.lockIconFilled='password-lock-02.svg'] - 값이 있을 때 아이콘
 * @param {string} [options.eyeIconOff='password-eye-off.svg'] - 눈 아이콘 (off)
 * @param {string} [options.eyeIconOn='password-eye.svg'] - 눈 아이콘 (on)
 * @param {string} [options.imageBasePath] - 이미지 기본 경로 (없으면 상대 경로 사용)
 * @returns {Object} PasswordInputManager 인스턴스
 */

const PATH_ADMIN_GD_SHARE = '/admin/gd_share/ncds/image/';

function createPasswordInputManager({
    targetSelector = '.ncua-input-password',
    inputSelector = 'input[type="password"], input[type="text"]',
    toggleButtonSelector = 'button.ncua-input__password-icon',
    leftIconSelector = '.ncua-input__left-icon',
    lockIconEmpty = 'password-lock-01.svg',
    lockIconFilled = 'password-lock-02.svg',
    eyeIconOff = 'password-eye-off.svg',
    eyeIconOn = 'password-eye.svg',
    imageBasePath = null,
} = {}) {
    /**
     * 이미지 경로 반환
     * @param {string} filename - 파일명
     * @returns {string} 이미지 경로
     */
    const getImagePath = (filename) => {
        if (imageBasePath) {
            return `${imageBasePath}${filename}`;
        }
        // 기본 경로는 현재 스크립트 위치 기준으로 설정
        // 실제 사용 시 PATH_ADMIN_GD_SHARE 상수 사용
        return `${PATH_ADMIN_GD_SHARE}${filename}`;
    };

    /**
     * 비밀번호 필드 초기화
     * @param {HTMLElement} container - ncua-input-password 컨테이너
     */
    const initPasswordField = (container) => {
        // 모달 레이어가 닫혔다가 다시 열릴때 새로 초기화 되지 않고 이벤트가 중복으로 등록 되어
        // 눈 아이콘이 토글되지 않아 초기화 되었으면 다시 이벤트 등록 안되도록 수정
        if (container._passwordInitialized) {
            return;
        }

        const input = container.querySelector(inputSelector);
        const toggleButton = container.querySelector(toggleButtonSelector);
        const leftIcon = container.querySelector(leftIconSelector);

        if (!input || !toggleButton || !leftIcon) {
            return;
        }

        // 토글 버튼 클릭 이벤트
        const handleToggleClick = () => {
            const currentType = input.type;
            const newType = currentType === 'password' ? 'text' : 'password';
            input.type = newType;

            // 버튼 내 이미지 변경
            const buttonImg = toggleButton.querySelector('img');
            if (buttonImg) {
                const newSrc = newType === 'password' 
                    ? getImagePath(eyeIconOff) 
                    : getImagePath(eyeIconOn);
                buttonImg.src = newSrc;
            }
        };

        // input 값 변경 이벤트
        const handleInputChange = () => {
            const hasValue = input.value && input.value.length > 0;
            // leftIcon이 img 요소인지 확인
            const leftIconImg = leftIcon.tagName === 'IMG' ? leftIcon : leftIcon.querySelector('img');
            
            if (leftIconImg) {
                const newSrc = hasValue 
                    ? getImagePath(lockIconFilled) 
                    : getImagePath(lockIconEmpty);
                leftIconImg.src = newSrc;
            }
        };

        // 이벤트 리스너 제거 (중복 방지)
        toggleButton.removeEventListener('click', handleToggleClick);
        input.removeEventListener('input', handleInputChange);
        input.removeEventListener('change', handleInputChange);

        // 이벤트 리스너 추가
        toggleButton.addEventListener('click', handleToggleClick);
        input.addEventListener('input', handleInputChange);
        input.addEventListener('change', handleInputChange);

        container._passwordInitialized = true;

        // 초기 상태 설정
        handleInputChange();
    };

    const instance = {
        /**
         * 이벤트 바인딩
         */
        init() {
            // 기존 요소들 초기화
            const containers = document.querySelectorAll(targetSelector);
            containers.forEach(container => {
                initPasswordField(container);
            });

            // 동적으로 추가되는 요소를 위한 MutationObserver
            const observer = new MutationObserver((mutations) => {
                mutations.forEach((mutation) => {
                    mutation.addedNodes.forEach((node) => {
                        if (node.nodeType === 1) { // Element node
                            // 추가된 노드가 targetSelector인 경우
                            if (node.matches && node.matches(targetSelector)) {
                                initPasswordField(node);
                            }
                            // 추가된 노드의 자손 중 targetSelector가 있는 경우
                            const newContainers = node.querySelectorAll 
                                ? node.querySelectorAll(targetSelector) 
                                : [];
                            newContainers.forEach(container => {
                                initPasswordField(container);
                            });
                        }
                    });
                });
            });

            observer.observe(document.body, {
                childList: true,
                subtree: true
            });

            // 인스턴스에 observer 저장 (destroy 시 사용)
            instance._observer = observer;
        },

        /**
         * 이벤트 해제
         */
        destroy() {
            if (instance._observer) {
                instance._observer.disconnect();
                instance._observer = null;
            }

            // 모든 컨테이너의 이벤트 리스너 제거
            const containers = document.querySelectorAll(targetSelector);
            containers.forEach(container => {
                const input = container.querySelector(inputSelector);
                const toggleButton = container.querySelector(toggleButtonSelector);
                
                if (toggleButton) {
                    // 이벤트 리스너를 제거하기 위해 새로운 버튼으로 교체
                    const newButton = toggleButton.cloneNode(true);
                    toggleButton.parentNode.replaceChild(newButton, toggleButton);
                }
            });
        },

        /**
         * 수동으로 특정 필드 초기화
         * @param {HTMLElement|string} container - ncua-input-password 컨테이너 또는 셀렉터
         */
        initField(container) {
            const element = typeof container === 'string' 
                ? document.querySelector(container) 
                : container;
            
            if (element && element.matches && element.matches(targetSelector)) {
                initPasswordField(element);
            }
        },
    };

    return instance;
}
