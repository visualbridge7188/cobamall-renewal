(function () {
    // Popup 인스턴스 전역 관리 Map 초기화 (디자인에디터 전용)
    if (!window.__NDE_POPUP__) {
        window.__NDE_POPUP__ = new Map();
    }

    // ============================================
    // 설정 상수
    // ============================================

    /**
     * 섹션(팝업 컨테이너) Props 기본값
     */
    const DEFAULT_SECTION_PROPS = {
        popupPositionDesktop: 'position-desktop-left-bottom',
        popupWidthModeDesktop: 400,
        popupCloseToday: 'true',
        backgroundOverlay: 'true',
        backgroundOverlayOpacity: 20,
        slideTransitionSpeed: 3,
        pagingButtons: 'true',
        arrowButtons: 'true',
    };

    /**
     * 위젯(팝업 이미지) Props 기본값
     */
    const DEFAULT_WIDGET_PROPS = {
        popupImageTitle: '팝업 이미지',
    };

    /**
     * CSS 셀렉터
     */
    const SELECTORS = {
        closeBtn: '.nde-c-popup__button--close',
        closeTodayBtn: '.nde-c-popup__button--close-today',
        overlay: '.nde-c-popup__overlay',
        swiper: '.nde-o-swiper',
        swiperPrev: '.swiper-button-prev',
        swiperNext: '.swiper-button-next',
        swiperPagination: '.swiper-pagination',
    };

    // ============================================
    // 렌더링 헬퍼 함수
    // ============================================

    /**
     * hidden 값 체크 (boolean 또는 문자열 'true' 모두 처리)
     */
    const isHidden = (value) => value === true || value === 'true';

    /**
     * truthy 값 체크 (Props의 boolean 문자열 처리)
     */
    const isTruthy = (value) => value === 'true' || value === true || (value && value !== 'false');

    /**
     * 포지션 클래스 추출
     * 예: 'position-desktop-left-top' -> 'left-top'
     */
    const extractPositionClass = (positionDesktop) => {
        if (!positionDesktop) return 'center';
        const match = positionDesktop.match(/position-desktop-(.+)/);
        return match ? match[1] : 'center';
    };

    // ============================================
    // 렌더링 함수 (템플릿)
    // ============================================

    /**
     * 위젯(팝업 이미지) HTML 생성
     *
     * Props:
     * - imageSrc: 이미지 URL
     * - imageSameMediaOnMobile: 모바일에서 같은 이미지 사용 여부
     * - imageSrcMobile: 모바일 전용 이미지 URL
     * - exposureStartDate: 노출 시작일시 (YYYY-MM-DD 또는 YYYY-MM-DD HH:MM)
     * - exposureEndDate: 노출 종료일시 (YYYY-MM-DD 또는 YYYY-MM-DD HH:MM)
     * - popupImageTitle: 이미지 제목 (alt 텍스트)
     * - popupImageLink: 클릭 시 이동할 링크
     * - popupImageOpenInNewTab: 새 탭에서 열기 여부
     */
    const renderWidgetHtml = (widget) => {
        const props = widget.props || {};
        const imageSrc = props.imageSrc || '';
        const imageSrcMobile = props.imageSrcMobile || '';
        const sameOnMobile = props.imageSameMediaOnMobile === 'true';
        const popupImageTitle = props.popupImageTitle || DEFAULT_WIDGET_PROPS.popupImageTitle;
        const popupImageLink = props.popupImageLink || '';
        const openInNewTab = props.popupImageOpenInNewTab === true || props.popupImageOpenInNewTab === 'true';

        const imageTag = (!sameOnMobile && imageSrcMobile)
            ? `<picture><source media="(max-width: 767px)" srcSet="${imageSrcMobile}"/><img src="${imageSrc}" alt="${popupImageTitle}"/></picture>`
            : `<img src="${imageSrc}" alt="${popupImageTitle}" />`;

        const imageContent = popupImageLink
            ? `<a href="${popupImageLink}" target="${openInNewTab ? '_blank' : '_self'}"${openInNewTab ? ' rel="noopener noreferrer"' : ''}>${imageTag}</a>`
            : `<span>${imageTag}</span>`;

        return `
<div class="swiper-slide">
    <div
        nde-widget-id="${widget.id}"
        nde-widget-type="${widget.type}"
        nde-name="팝업 이미지"
        class="nde-c-widget"
        style="--nde-width-desktop: auto; --nde-width-mobile: auto; --nde-height: auto;"
    >
        <div class="nde-c-popup-image">
            <div class="nde-c-popup-image__content">
                ${imageContent}
            </div>
        </div>
    </div>
</div>`;
    };

    /**
     * 위젯 목록 HTML 생성
     */
    const renderWidgets = (widgets) => {
        return filterVisibleWidgets(widgets)
            .map(renderWidgetHtml)
            .join('');
    };

    /**
     * 섹션(팝업 컨테이너) HTML 생성
     */
    const renderSectionHtml = (section) => {
        const props = section.props || {};
        const d = DEFAULT_SECTION_PROPS;

        // Props 기본값 적용
        const popupPositionDesktop = props.popupPositionDesktop || d.popupPositionDesktop;
        const popupWidthModeDesktop = props.popupWidthModeDesktop !== undefined ? props.popupWidthModeDesktop : d.popupWidthModeDesktop;
        const popupCloseToday = props.popupCloseToday !== undefined ? props.popupCloseToday : d.popupCloseToday;
        const backgroundOverlay = props.backgroundOverlay !== undefined ? props.backgroundOverlay : d.backgroundOverlay;
        const backgroundOverlayOpacity = props.backgroundOverlayOpacity !== undefined ? props.backgroundOverlayOpacity : d.backgroundOverlayOpacity;
        const slideTransitionSpeed = props.slideTransitionSpeed !== undefined ? props.slideTransitionSpeed : d.slideTransitionSpeed;
        const pagingButtons = props.pagingButtons !== undefined ? props.pagingButtons : d.pagingButtons;
        const arrowButtons = props.arrowButtons !== undefined ? props.arrowButtons : d.arrowButtons;

        const positionClass = extractPositionClass(popupPositionDesktop);
        const overlayOpacityValue = backgroundOverlayOpacity / 100;
        const widgetsHtml = renderWidgets(section.widgets);

        const overlayHtml = isTruthy(backgroundOverlay)
            ? `<div class="nde-c-popup__overlay nde-c-popup__overlay--active" style="opacity:${overlayOpacityValue}"></div>`
            : `<div class="nde-c-popup__overlay"></div>`;

        const closeTodayBtnHtml = isTruthy(popupCloseToday)
            ? `<button type="button" class="nde-c-popup__button nde-c-popup__button--close-today">오늘 그만보기</button>`
            : '';

        const paginationHtml = isTruthy(pagingButtons)
            ? `<div class="swiper-pagination"></div>`
            : '';

        const arrowButtonsHtml = isTruthy(arrowButtons)
            ? `<div class="swiper-button-prev">
                        <svg xmlns="http://www.w3.org/2000/svg" width="19" height="34" viewBox="0 0 19 34" fill="none">
                            <title>이전</title>
                            <path d="M17.9135 0.94295L1.88574 16.9707L17.9135 32.9985" stroke="currentColor" stroke-width="2.66667" fill="none"></path>
                        </svg>
                    </div>
                    <div class="swiper-button-next">
                        <svg xmlns="http://www.w3.org/2000/svg" width="19" height="34" viewBox="0 0 19 34" aria-hidden="true" fill="none">
                            <title>다음</title>
                            <path d="M0.94295 0.94295L16.9707 16.9707L0.94295 32.9985" stroke="currentColor" stroke-width="2.66667" fill="none"></path>
                        </svg>
                    </div>`
            : '';

        return `
<section nde-section-id="${section.id}" nde-name="${section.title || '팝업'}" class="nde-o-section">
    <div class="nde-o-section--container">
        ${overlayHtml}
        <div class="nde-c-popup nde-c-popup--${positionClass}" style="--popup-width-desktop:${popupWidthModeDesktop}px">
            <div class="nde-c-popup__content">
                <div
                    class="nde-o-swiper swiper"
                    data-slide-transition-speed="${slideTransitionSpeed}"
                    data-paging-buttons="${pagingButtons}"
                    data-arrow-buttons="${arrowButtons}"
                    data-swiper-loop="true"
                    data-slides-per-view="1"
                    data-slides-per-view-mobile="1"
                    data-slides-per-group="1"
                    data-slides-per-group-mobile="1"
                    data-space-between="0"
                    data-space-between-mobile="0"
                    data-autoplay="true"
                    data-auto-height="true"
                >
                    <div class="swiper-wrapper">
                        ${widgetsHtml}
                    </div>
                    ${paginationHtml}
                    ${arrowButtonsHtml}
                </div>
            </div>
            <div class="nde-c-popup__button-group">
                ${closeTodayBtnHtml}
                <button type="button" class="nde-c-popup__button nde-c-popup__button--close">닫기</button>
            </div>
        </div>
    </div>
</section>`;
    };

    // ============================================
    // 유틸리티 함수
    // ============================================

    /**
     * 쿠키 설정
     */
    const setCookie = (name, value, days) => {
        let expires = '';
        if (days) {
            const date = new Date();
            date.setTime(date.getTime() + days * 24 * 60 * 60 * 1000);
            expires = '; expires=' + date.toUTCString();
        }
        document.cookie = name + '=' + encodeURIComponent(value) + expires + '; path=/';
    };

    /**
     * 쿠키 읽기
     */
    const getCookie = (name) => {
        const nameEQ = name + '=';
        const cookies = document.cookie.split(';');
        for (let i = 0; i < cookies.length; i++) {
            let cookie = cookies[i].trim();
            if (cookie.indexOf(nameEQ) === 0) {
                return decodeURIComponent(cookie.substring(nameEQ.length));
            }
        }
        return null;
    };

    /**
     * 날짜 문자열을 Date 객체로 파싱
     * @param {string} dateStr - 날짜 문자열 (YYYY-MM-DD 또는 YYYY-MM-DD HH:MM)
     * @returns {Date|null} - Date 객체 또는 null
     */
    const parseDate = (dateStr) => {
        if (!dateStr || typeof dateStr !== 'string') return null;

        const trimmed = dateStr.trim();
        if (!trimmed) return null;

        // YYYY-MM-DD HH:MM 형식 처리
        const dateTimeMatch = trimmed.match(/^(\d{4})-(\d{2})-(\d{2})\s+(\d{2}):(\d{2})$/);
        if (dateTimeMatch) {
            const [, year, month, day, hour, minute] = dateTimeMatch;
            return new Date(
                parseInt(year, 10),
                parseInt(month, 10) - 1,
                parseInt(day, 10),
                parseInt(hour, 10),
                parseInt(minute, 10)
            );
        }

        // YYYY-MM-DD 형식 처리
        const dateMatch = trimmed.match(/^(\d{4})-(\d{2})-(\d{2})$/);
        if (dateMatch) {
            const [, year, month, day] = dateMatch;
            return new Date(parseInt(year, 10), parseInt(month, 10) - 1, parseInt(day, 10));
        }

        return null;
    };

    /**
     * 노출 기간 체크
     * @param {string} startDate - 노출 시작일시 (YYYY-MM-DD 또는 YYYY-MM-DD HH:MM, 빈값일 경우 상시 노출)
     * @param {string} endDate - 노출 종료일시 (YYYY-MM-DD 또는 YYYY-MM-DD HH:MM, 빈값일 경우 상시 노출)
     * @returns {boolean} - 현재 노출 가능 여부
     */
    const isWithinExposurePeriod = (startDate, endDate) => {
        const now = new Date();

        // 시작일시 체크
        if (startDate) {
            const start = parseDate(startDate);
            if (start && now < start) return false;
        }

        // 종료일시 체크
        if (endDate) {
            const end = parseDate(endDate);
            if (end) {
                // 시간이 지정되지 않은 경우 (YYYY-MM-DD), 해당 날짜의 23:59:59까지 포함
                const hasTime = endDate.trim().includes(' ');
                if (!hasTime) {
                    end.setHours(23, 59, 59, 999);
                }
                if (now > end) return false;
            }
        }

        return true;
    };

    /**
     * 노출 가능한 위젯 필터링
     */
    const filterVisibleWidgets = (widgets) => {
        if (!widgets || !Array.isArray(widgets)) return [];

        return widgets.filter((widget) => {
            if (isHidden(widget.hidden)) return false;
            const props = widget.props || {};
            return isWithinExposurePeriod(props.exposureStartDate, props.exposureEndDate);
        });
    };

    // ============================================
    // 팝업 초기화 함수
    // ============================================

    const initPopup = (section) => {
        // section 유효성 검사
        if (!section || !section.id) return null;

        // 숨김 상태면 렌더링하지 않음
        if (isHidden(section.hidden)) return null;

        // 노출 가능한 위젯이 없으면 팝업을 표시하지 않음
        const visibleWidgets = filterVisibleWidgets(section.widgets);
        if (visibleWidgets.length === 0) return null;

        const sectionId = section.id;
        const cookieName = 'nde_popup_close_' + sectionId;

        // 오늘 하루 보지 않기 쿠키 체크
        if (getCookie(cookieName)) return null;

        // 기존 인스턴스 정리
        if (window.__NDE_POPUP__.has(sectionId)) {
            const existing = window.__NDE_POPUP__.get(sectionId);
            if (existing.container) existing.container.remove();
            if (existing.swiper && typeof existing.swiper.destroy === 'function') {
                existing.swiper.destroy(true, true);
            }
            window.__NDE_POPUP__.delete(sectionId);
        }

        // HTML 생성
        const html = renderSectionHtml(section);

        // 컨테이너 생성 및 DOM 추가
        const container = document.createElement('div');
        container.innerHTML = html;
        document.body.appendChild(container);

        const sectionEl = container.querySelector('[nde-section-id="' + sectionId + '"]');
        if (!sectionEl) {
            container.remove();
            return null;
        }

        // ESC 키 이벤트 핸들러
        let handleKeyDown = null;

        // ============================================
        // 팝업 인스턴스 객체 생성
        // ============================================
        const popupInstance = {
            sectionId,
            container,
            sectionEl,
            swiper: null,

            close() {
                if (!this.container) return; // 중복 호출 방어

                if (handleKeyDown) {
                    document.removeEventListener('keydown', handleKeyDown);
                    handleKeyDown = null;
                }
                if (this.swiper && typeof this.swiper.destroy === 'function') {
                    this.swiper.destroy(true, true);
                    this.swiper = null;
                }
                this.container.remove();
                this.container = null;
                window.__NDE_POPUP__.delete(this.sectionId);

            },

            closeToday() {
                setCookie(cookieName, '1', 1);
                this.close();
            },
        };

        // ============================================
        // 이벤트 바인딩
        // ============================================

        // touchend: iOS 고스트 클릭 방지 (합성 click 차단 후 직접 실행)
        // click: PC(마우스) 폴백
        const bindClose = (el, fn) => {
            el.addEventListener('touchend', (e) => { e.preventDefault(); fn(); });
            el.addEventListener('click', (e) => { e.preventDefault(); e.stopPropagation(); fn(); });
        };

        const closeBtn = sectionEl.querySelector(SELECTORS.closeBtn);
        if (closeBtn) bindClose(closeBtn, () => popupInstance.close());

        const closeTodayBtn = sectionEl.querySelector(SELECTORS.closeTodayBtn);
        if (closeTodayBtn) bindClose(closeTodayBtn, () => popupInstance.closeToday());

        const overlay = sectionEl.querySelector(SELECTORS.overlay);
        if (overlay) bindClose(overlay, () => popupInstance.close());

        // ESC 키로 팝업 닫기 (접근성)
        handleKeyDown = (e) => {
            if (e.key === 'Escape') popupInstance.close();
        };
        document.addEventListener('keydown', handleKeyDown);

        // ============================================
        // Swiper 초기화
        // ============================================
        const swiperEl = sectionEl.querySelector(SELECTORS.swiper);
        const slideCount = visibleWidgets.length;

        if (swiperEl && typeof Swiper !== 'undefined') {
            // globals.css의 --nde-slide-pagination-type 변수에 따라 페이징 모양 설정
            const paginationType = getComputedStyle(document.documentElement)
                .getPropertyValue('--nde-slide-pagination-type')
                .trim();
            if (paginationType) {
                swiperEl.setAttribute('data-pagination-type', paginationType);
            }

            const slideTransitionSpeed = Number.parseFloat(swiperEl.getAttribute('data-slide-transition-speed') || '3');
            const pagingButtons = swiperEl.getAttribute('data-paging-buttons') === 'true';
            const arrowButtons = swiperEl.getAttribute('data-arrow-buttons') === 'true';
            const loop = swiperEl.getAttribute('data-swiper-loop') === 'true';
            const autoplay = swiperEl.getAttribute('data-autoplay') !== 'false';

            // 활성 슬라이드 높이에 맞춰 자동 조정 여부 (기본값: false)
            const autoHeight = swiperEl.getAttribute('data-auto-height') === 'true';

            const isSingleSlide = slideCount <= 1;

            const swiperConfig = {
                slidesPerView: 1,
                slidesPerGroup: 1,
                spaceBetween: 0,
                loop: isSingleSlide ? false : loop,
                centeredSlides: false,
                autoHeight,
            };

            // autoplay 설정 (슬라이드 1개일 때는 비활성화)
            if (autoplay && !isSingleSlide) {
                swiperConfig.autoplay = {
                    delay: slideTransitionSpeed * 1000,
                    disableOnInteraction: false,
                    pauseOnMouseEnter: true,
                };
            }

            // 페이지네이션 설정
            if (pagingButtons && !isSingleSlide) {
                swiperConfig.pagination = {
                    el: swiperEl.querySelector(SELECTORS.swiperPagination),
                    clickable: true,
                };
            }

            // 네비게이션 버튼 설정
            if (arrowButtons && !isSingleSlide) {
                swiperConfig.navigation = {
                    nextEl: swiperEl.querySelector(SELECTORS.swiperNext),
                    prevEl: swiperEl.querySelector(SELECTORS.swiperPrev),
                };
            }

            // 슬라이드 1개일 때 네비게이션 버튼 숨김
            if (isSingleSlide) {
                const prevBtn = swiperEl.querySelector(SELECTORS.swiperPrev);
                const nextBtn = swiperEl.querySelector(SELECTORS.swiperNext);
                const pagination = swiperEl.querySelector(SELECTORS.swiperPagination);
                if (prevBtn) prevBtn.style.display = 'none';
                if (nextBtn) nextBtn.style.display = 'none';
                if (pagination) pagination.style.display = 'none';
            }

            try {
                popupInstance.swiper = new Swiper(swiperEl, swiperConfig);
            } catch (error) {
                console.error('Failed to initialize Popup Swiper:', error);
            }
        }

        // 인스턴스 저장
        window.__NDE_POPUP__.set(sectionId, popupInstance);

        return popupInstance;
    };

    // ============================================
    // 메인 초기화 함수
    // ============================================

    const initPopups = (sections) => {
        if (!Array.isArray(sections)) {
            console.warn('NDE Popup: sections must be an array');
            return;
        }

        // 기존 인스턴스 정리
        window.__NDE_POPUP__.forEach((popup) => {
            if (popup && typeof popup.close === 'function') popup.close();
        });
        window.__NDE_POPUP__.clear();

        // 팝업 타입 섹션만 필터링하여 초기화
        sections
            .filter((section) => section.type && section.type.includes('__popup'))
            .forEach((section) => initPopup(section));
    };

    /**
     * 모든 팝업 닫기
     */
    const closeAll = () => {
        window.__NDE_POPUP__.forEach((popup) => {
            if (popup && typeof popup.close === 'function') popup.close();
        });
        window.__NDE_POPUP__.clear();
    };

    /**
     * 특정 팝업 가져오기
     */
    const getPopup = (sectionId) => {
        return window.__NDE_POPUP__.get(sectionId) || null;
    };

    // ============================================
    // 전역 API 노출
    // ============================================
    window.__NDE_POPUP__.init = initPopups;
    window.__NDE_POPUP__.initSingle = initPopup;
    window.__NDE_POPUP__.closeAll = closeAll;
    window.__NDE_POPUP__.get = getPopup;
})();
