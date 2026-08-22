/*
 * Copyright (C) 2025 NHN COMMERCE. - All Rights Reserved
 *
 * Unauthorized copying or redistribution of this file in source and binary forms via any medium
 * is strictly prohibited.
 */

(function(global) {
    global.GodoTutorial = global.GodoTutorial || {};

    global.GodoTutorial.TutorialHandler = class TutorialHandler {
        static STEP_GUIDE_SCRIPT = 'https://fe-sdk.cdn-nhncommerce.com/@ncds/step-guide/1.0/step-guide.min.js';
        static STEP_GUIDE_STYLE = 'https://fe-sdk.cdn-nhncommerce.com/@ncds/step-guide/1.0/step-guide.min.css';

        constructor(config) {
            const {
                category = '',
                code = '',
                mode = 'false',
                linkModalUrl = '',
                linkModalSize = {width: 760, height: 725},
                stepGuideSteps = [],
                stepGuideButtonLabel = {
                    prev: '이전',
                    next: '다음',
                    done: '저장 하기',
                    skip: '나중에 하기'
                },
                stepGuideLinks = {},
                handleStepChange = null,
                allowedOrigin = '*',
            } = config;

            /**
             * 튜토리얼 기본 설정
             * - category: 카테고리 (예: PAYMENT_TUTORIAL)
             * - code: 코드 (예: GD_SUBSCRIPTION_SETTING_01)
             * - mode: 페이지 진입 시 스텝가이드 즉시 실행 여부
             */
            this.category = category;
            this.code = code;
            this.mode = mode;

            /**
             * 튜토리얼 연계 모달
             * - linkModalInstance : 모달 인스턴스
             * - linkModalUrl : 모달 url
             * - linkModalSize : 모달 사이즈
             */
            this.linkModalInstance = null;
            this.linkModalUrl = linkModalUrl;
            this.linkModalSize = linkModalSize;

            /**
             * 튜토리얼 스텝가이드
             * - stepGuideInstance : 스텝가이드 인스턴스
             * - stepGuideSteps : 단계별 출력 정보
             * - stepGuideButtonLabel : 버튼 라벨
             * - stepGuideLinks : 이동 경로 정보
             * - handleStepChange : 스텝 변경 시 콜백 함수
             */
            this.stepGuideInstance = null;
            this.stepGuideSteps = stepGuideSteps;
            this.stepGuideButtonLabel = stepGuideButtonLabel;
            this.stepGuideLinks = stepGuideLinks;
            this.handleStepChange = handleStepChange;

            // 허용된 Origin 여부
            this.allowedOrigin = allowedOrigin;

            // 리로드 여부 확인 후 mode를 false로 설정
            this.mode =
                performance.getEntriesByType?.('navigation')?.[0]?.type === 'reload' ||
                performance.navigation?.type === performance.navigation.TYPE_RELOAD
                    ? 'false'
                    : this.mode;

            // 튜토리얼 부트업
            this.bootUp();
        }

        // 튜토리얼 부트업
        async bootUp() {
            try {
                this.insertStyle();
                await this.loadExternalResources();
            } catch (error) {
                // 스텝가이드 관련 외부 리소스 로딩이 실패할 경우 튜토리얼 진행 불가
                return;
            }

            this.setupTutorial();
            this.listenMessage();
        }

        // 내부 스타일 삽입
        insertStyle() {
            const style = document.createElement('style');
            style.textContent = `
                .tutorial-step-badge { position: absolute; top: 16px; left: 16px; display: inline-block; font-size: 10px; font-weight: 700; background-color: #ec1d31; color: #fff; padding: 0 6px; border-radius: 8px; vertical-align: middle; }
                .tutorial-step-text { display: inline-block; margin-left: 52px; vertical-align: middle; }
            `;
            document.head.appendChild(style);
        }

        // 외부 리소스 로딩 : 스텝가이드 스크립트 및 스타일
        async loadExternalResources() {
            const loadResource = (tag, attributes) =>
                new Promise((resolve, reject) => {
                    const element = Object.assign(document.createElement(tag), attributes);
                    element.onload = resolve;
                    element.onerror = reject;
                    document.head.appendChild(element);
                });

            await Promise.all([
                loadResource('script', {src: TutorialHandler.STEP_GUIDE_SCRIPT, async: true}),
                loadResource('link', {href: TutorialHandler.STEP_GUIDE_STYLE, rel: 'stylesheet'}),
            ]);
        }

        // 부트업 과정에서 튜토리얼 모드에 따라 실행
        setupTutorial() {
            if (this.mode === 'true') {
                // 튜토리얼 스텝가이드 바로 보기 모드가 활성화된 경우 스텝가이드 실행
                this.showTutorialStepGuide();
            } else if (this.mode === 'false' && this.linkModalUrl) {
                // 튜토리얼 스텝가이드 바로 보기 모드가 비활성화되고 튜토리얼 연계 모달 url 이 있는 경우 튜토리얼 연계 모달 실행
                this.showTutorialLinkModal(this.linkModalUrl, this.linkModalSize);
            }
        }

        // 튜토리얼 쿠키 가져오기
        getTutorialCookie(name) {
            const value = `; ${document.cookie}`;
            const parts = value.split(`; ${name}=`);
            return parts.length === 2 ? parts.pop().split(';').shift() : null;
        }

        // postMessage 수신 이벤트 등록
        listenMessage() {
            window.addEventListener('message', this.handleMessage.bind(this));
        }

        // postMessage 유효성 체크
        isValidMessage(message) {
            // 메시지는 object 타입이며 category, type 이 필수로 존재하고 요청된 튜토리얼 카테고리와 동일해야함
            return (
                message &&
                typeof message === 'object' &&
                'category' in message &&
                'type' in message &&
                this.category === message.category
            );
        }

        // postMessage 타입에 따른 처리
        handleMessage(event) {
            const {origin, data: message} = event;

            // origin 체크
            if (this.allowedOrigin !== '*' && origin !== this.allowedOrigin) {
                return;
            }

            // 메시지 유효성 체크
            if (!this.isValidMessage(message)) {
                return;
            }

            switch (message.type) {
                case 'MODAL_CLOSE':
                    // 튜토리얼 연계 모달 닫기
                    this.closeTutorialLinkModal();
                    break;
                case 'MODAL_HIDE_TODAY':
                    // 오늘 하루 다시 보지 않기
                    this.closeTutorialLinkModal();
                    this.hideTodayTutorialLinkModal();
                    break;
                case 'NAVIGATE_TO_TUTORIAL':
                    // 튜토리얼 스텝가이드 보기
                    if (this.code === message.payload.code) {
                        // 현재 페이지의 튜토리얼 코드와 요청된 코드가 동일한 경우 스텝가이드 실행
                        this.closeTutorialLinkModal();
                        this.showTutorialStepGuide();
                    } else {
                        // 요청된 코드가 현재 페이지의 코드와 다른 경우, 해당 챕터의 페이지로 이동 후 스텝가이드 실행
                        const matchingCode = this.stepGuideLinks[message.payload.code] || null;
                        if (matchingCode && typeof matchingCode === 'object') {
                            document.location.href = matchingCode.settingLink;
                        }
                    }
                    break;
                default:
                    break;
            }
        }

        // Dom 요소 생성 처리
        createElementWithStyles(tag, styles) {
            const element = document.createElement(tag);
            Object.assign(element.style, styles);
            return element;
        }

        // 튜토리얼 연계 모달 실행
        showTutorialLinkModal(url, size) {
            // 오늘 하루 다시 보지 않기가 활성화 일 경우 튜토리얼 연계 모달 실행 안함
            if (this.getTutorialCookie(`${this.category}_HIDE_TODAY`) === 'true') {
                return;
            }

            // 이미 생성된 튜토리얼 연계 모달이 있을 경우 중복 실행 방지
            if (this.linkModalInstance) {
                return;
            }

            document.body.style.overflow = 'hidden';

            // 딤드 처리를 위한 오버레이 레이어 생성
            const overlayLayer = this.createElementWithStyles('div', {
                display: 'none',
                position: 'fixed',
                left: '0',
                top: '0',
                width: '100%',
                height: '100%',
                zIndex: '3000',
                backgroundColor: 'rgba(0, 0, 0, 0.5)',
            });
            document.body.appendChild(overlayLayer);

            // 모달 콘텐츠 출력을 위한 iframe 생성
            const iframe = this.createElementWithStyles('iframe', {
                width: '100%',
                height: '100%',
                border: 'none',
                borderRadius: '12px',
            });
            iframe.src = url;
            iframe.title = 'Tutorial Modal';

            // iframe을 포함하는 중앙 정렬된 모달 컨테이너 생성
            this.linkModalInstance = this.createElementWithStyles('div', {
                display: 'none',
                position: 'fixed',
                left: '50%',
                top: '50%',
                transform: 'translate(-50%, -50%)',
                width: `${size.width}px`,
                height: `${size.height}px`,
                borderRadius: '12px',
                boxShadow: '0 0 10px rgba(0, 0, 0, 0.5)',
                zIndex: '3010',
                backgroundColor: 'white',
            });
            this.linkModalInstance.appendChild(iframe);
            document.body.appendChild(this.linkModalInstance);

            // 모달과 오버레이를 디스플레이 처리
            setTimeout(() => {
                overlayLayer.style.display = 'block';
                this.linkModalInstance.style.display = 'block';
                this.linkModalInstance.focus();
            }, 1000);
        }

        // 튜토리얼 연계 모달 닫기
        closeTutorialLinkModal() {
            if (this.linkModalInstance) {
                this.linkModalInstance.style.display = 'none';
                document.body.style.overflow = '';

                const element = document.querySelector('div[style*="z-index: 3000"]');
                if (element && element.parentNode) {
                    element.parentNode.removeChild(element);
                }

                if (this.linkModalInstance.parentNode) {
                    this.linkModalInstance.parentNode.removeChild(this.linkModalInstance);
                }

                this.linkModalInstance = null;
            }
        }

        // 튜토리얼 연계 모달 하루 동안 안보기
        hideTodayTutorialLinkModal() {
            const date = new Date();
            date.setTime(date.getTime() + (24 * 60 * 60 * 1000));
            const expires = "; expires=" + date.toUTCString();

            document.cookie = `${this.category}_HIDE_TODAY=true; path=/; expires=${expires};`;
        }

        // 튜토리얼 스텝가이드 실행
        showTutorialStepGuide() {
            if (typeof stepGuide === 'function') {
                this.stepGuideInstance = stepGuide({
                    el: document.body,
                    options: {
                        hideSkip: false,
                        hideStepCount: false,
                        exitOnOverlayClick: false,
                        buttonLabel: this.stepGuideButtonLabel,
                        steps: this.stepGuideSteps,
                    },
                });
                if (typeof this.stepGuideInstance === 'object') {
                    this.stepGuideInstance.onChange((currentStep) => {
                        const isLastStep = currentStep.element.id === 'tutorial-step-guide-done';

                        // 스텝가이드의 마지막 스텝일 경우 스크롤 제어
                        document.body.style.overflow = isLastStep ? 'hidden' : 'auto';

                        // 스텝가이드의 마지막 스텝일 경우 스킵 버튼 숨김 처리
                        const skipButton = document.getElementById('step-guide-skip');
                        if (skipButton) {
                            skipButton.style.display = isLastStep ? 'none' : 'inline-block';
                        }

                        // 스텝가이드 콜백 함수 실행
                        if (typeof this.handleStepChange === 'function') {
                            this.handleStepChange(currentStep.element.id);
                        }
                    });
                    this.stepGuideInstance.onExit(() => {
                        document.body.style.overflow = 'auto';
                    });
                }
            }
        }
    };
})(window);
