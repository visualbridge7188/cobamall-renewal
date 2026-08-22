/*
 * Copyright (C) 2025 NHN COMMERCE. - All Rights Reserved
 *
 * Unauthorized copying or redistribution of this file in source and binary forms via any medium
 * is strictly prohibited.
 */

(function (global) {
    global.GodoCoach = global.GodoCoach || {};

    global.GodoCoach.CoachModalHandler = class CoachModalHandler {
        static NAMESPACE = 'godo-coach';
        static COACH_MODAL_STYLE = `
            .godo-coach-modal {display: flex; flex-direction: column; max-height: 90vh; overflow: hidden; width: 100%; max-width: 1640px; border-radius: 12px; padding: 0; border: 0; box-shadow: 0px 20px 24px -4px rgba(16, 24, 40, 0.08), 0px 8px 8px -4px rgba(16, 24, 40, 0.03); position: fixed; left: 50%; top: 50%; transform: translate(-50%, -50%);}
            .godo-coach-modal__title {flex-shrink: 0; margin: 0; padding: 16px 20px; font-weight: 700; font-size: 14px; line-height: 22px; background: #fff;}
            .godo-coach-modal__content {flex-shrink: 1; min-height: 0; overflow-y: auto; padding: 20px; background-color: #f6f7f9; display: flex; flex-direction: column; justify-content: center;}
            .godo-coach-modal__image {margin: 0; overflow: hidden; border-radius: 12px; flex: 1 1 auto; display: flex; align-items: center; justify-content: center;}
            .godo-coach-modal__image img {width: 100%; height: 100%; object-fit: cover; vertical-align: top; border-radius: 12px; display: block;}
            .godo-coach-modal__footer {flex-shrink: 0; display: flex; justify-content: flex-end; gap: 8px; margin: 0; padding-block: 16px; padding-inline: 20px; background: #fff;}
            .godo-coach-modal__footer button {font-size: 14px; font-weight: 700; line-height: 22px; border: 1px solid; border-radius: 6px; height: 36px; padding-inline: 14px;}
            .godo-coach-modal__footer button:first-child {color: #171818; background-color: #fff; border-color: #d3d4d8;}
            .godo-coach-modal__footer button:last-child {color: #fff; background-color: #f7444e; border-color: #ec1d31;}
            .godo-coach-modal__close {position: absolute; top: 16px; right: 16px; width: 36px; height: 36px; background: transparent; border: none; padding: 0; cursor: pointer;}
            .godo-coach-backdrop::backdrop {background-color: rgba(0, 0, 0, 0.5);}
        `;

        constructor(config) {
            const {
                coachName = 'MODAL',
                imageUrl = '',
                modalTitle = '안내 팝업',
            } = config;

            // coachName을 대문자로 변경하며, 하이픈을 언더스코어로 변환 처리
            const normalizedCoachName = coachName.replace(/-/g, '_').toUpperCase();

            this.coachName = normalizedCoachName;
            this.coachHideToday = `COACH_${coachName}_HIDE_TODAY`;
            this.coachHideForever = `COACH_${coachName}_HIDE_FOREVER`;
            this.imageUrl = imageUrl;
            this.modalTitle = modalTitle;
            this.modal = null;

            this.injectStyle();
            this.init();
        }

        // 코치 모달에 사용되는 스타일 삽입
        injectStyle() {
            // 이미 스타일이 존재하면 추가 삽입하지 않음
            const styleId = `${CoachModalHandler.NAMESPACE}-style-${this.coachName}`;
            if (document.getElementById(styleId)) {
                return;
            }

            // 스타일 태그 생성 및 삽입
            const style = document.createElement('style');
            style.id = styleId;
            style.textContent = CoachModalHandler.COACH_MODAL_STYLE;
            document.head.appendChild(style);
        }

        // 모달 생성 및 이벤트 등록
        init() {
            // 쿠키가 존재하면 모달을 생성하지 않음
            if (this._hasCookie(this.coachHideForever) || this._hasCookie(this.coachHideToday)) {
                return;
            }
            // 모달 생성, 표시 및 이벤트 등록
            this.createModal();
            this.showModal();
            this.registerEvents();
        }

        // 모달 DOM 생성
        createModal() {
            // 이미 모달이 존재하면 생성하지 않음
            if (this.modal) {
                return;
            }

            // 코치 모달 다이얼로그 생성
            const dialog = document.createElement('dialog');
            dialog.className = 'godo-coach-modal godo-coach-backdrop';
            dialog.id = `${CoachModalHandler.NAMESPACE}-modal`;

            // 정중앙 배치를 위한 스타일 추가
            dialog.style.position = 'fixed';
            dialog.style.left = '50%';
            dialog.style.top = '50%';
            dialog.style.transform = 'translate(-50%, -50%)';

            // 모달 내부 HTML 구성
            dialog.innerHTML = `
                <h1 class="godo-coach-modal__title">${this.modalTitle}</h1>
                <section class="godo-coach-modal__content">
                    <figure class="godo-coach-modal__image">
                        <img src="${this.imageUrl}" alt="" width="1600" height="638" />
                    </figure>
                </section>
                <p class="godo-coach-modal__footer">
                    <button type="button" class="js-hide-coach">다시 보지 않기</button>
                    <button type="button" class="js-close-coach">닫기</button>
                </p>
                <button type="button" class="godo-coach-modal__close js-close-coach">
                    <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" viewBox="0 0 12 12" fill="none">
                        <path d="M11 1L1 11M1 1L11 11" stroke="#757678" stroke-width="1.66667" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                </button>
            `;
            document.body.appendChild(dialog);
            this.modal = dialog;
        }

        // 모달 보기
        showModal() {
            this.modal.showModal();
        }

        // 이벤트 등록
        registerEvents() {
            const closeBtns = this.modal.querySelectorAll('.js-close-coach');
            const hideBtn = this.modal.querySelector('.js-hide-coach');

            // 닫기 버튼: 하루동안 안보기
            closeBtns.forEach((btn) =>
                btn.addEventListener('click', () => {
                    // 하루동안 안보기 쿠키 설정
                    const expires = new Date();
                    expires.setTime(expires.getTime() + 24 * 60 * 60 * 1000);
                    this._setCookie(this.coachHideToday, expires);

                    // 모달 닫기
                    this.modal.close();
                    this.modal.remove();
                })
            );

            // 다시 보지 않기: 영구 안보기
            hideBtn.addEventListener('click', () => {
                // 100년동안 안보기 쿠키 설정
                const expires = new Date();
                expires.setFullYear(expires.getFullYear() + 100);
                this._setCookie(this.coachHideForever, expires);

                // 모달 닫기
                this.modal.close();
                this.modal.remove();
            });
        }

        // 쿠키 설정
        _setCookie(name, expiresTime) {
            // 이미 쿠키가 존재하면 설정하지 않음
            if (this._hasCookie(name)) {
                return;
            }
            // 쿠키 설정
            document.cookie = `${name}=true; path="/"; expires=${expiresTime.toUTCString()}`;
        }

        // 쿠키 확인
        _hasCookie(key) {
            const value = `; ${document.cookie}`;
            const parts = value.split(`; ${key}=`);
            return parts.length === 2 && parts.pop().split(';').shift() === 'true';
        }
    };
})(window);
