(function () {
    // ============================================
    // 노출기간(exposureStartDate/EndDate) 런타임 필터 — gd_popup.js 와 동일 로직(view 시점 평가).
    // 슬라이드는 render 시점에 거르지 않고(정적 게시 스냅샷 한계) 여기서 swiper init 전에 만료/미시작
    // 슬라이드를 제거 → 게시 후에도 자동 만료/개시. data-exposure-* 는 host 렌더가 emit.
    // ============================================
    const parseExposureDate = (dateStr) => {
        if (!dateStr) return null;
        const trimmed = String(dateStr).trim();
        if (!trimmed) return null;
        // YYYY-MM-DD HH:MM
        const dt = trimmed.match(/^(\d{4})-(\d{2})-(\d{2})\s+(\d{2}):(\d{2})$/);
        if (dt) {
            return new Date(+dt[1], +dt[2] - 1, +dt[3], +dt[4], +dt[5]);
        }
        // YYYY-MM-DD
        const dm = trimmed.match(/^(\d{4})-(\d{2})-(\d{2})$/);
        if (dm) {
            return new Date(+dm[1], +dm[2] - 1, +dm[3]);
        }
        return null;
    };

    const isWithinExposurePeriod = (startDate, endDate) => {
        const now = new Date();
        if (startDate) {
            const start = parseExposureDate(startDate);
            if (start && now < start) return false;
        }
        if (endDate) {
            const end = parseExposureDate(endDate);
            if (end) {
                // 시간 미지정(YYYY-MM-DD)이면 해당일 23:59:59 까지 포함 — gd_popup.js 와 동일
                if (!endDate.trim().includes(' ')) {
                    end.setHours(23, 59, 59, 999);
                }
                if (now > end) return false;
            }
        }
        return true;
    };

    // 노출기간 만료·미시작 슬라이드를 DOM 에서 제거 (slideCount/loop 계산 전에 호출 → 자동 정합)
    const filterExposedSlides = (swiperEl) => {
        swiperEl.querySelectorAll('.swiper-slide').forEach((slideEl) => {
            const target = slideEl.querySelector('[data-exposure-start],[data-exposure-end]');
            if (!target) return; // 노출기간 미설정 = 상시 노출
            const start = target.getAttribute('data-exposure-start') || '';
            const end = target.getAttribute('data-exposure-end') || '';
            if (!isWithinExposurePeriod(start, end)) {
                slideEl.remove();
            }
        });
    };

    const initSwiper = () => {
        if (typeof Swiper === 'undefined') {
            console.error('Swiper library is not loaded');
            return;
        }

        const swiperElements = document.querySelectorAll('.nde-o-swiper');

        swiperElements.forEach((swiperEl) => {
            // 이미 초기화된 경우 스킵
            if (swiperEl.swiper) {
                return;
            }

            // 노출기간 만료·미시작 슬라이드 제거 (slideCount/loop 계산 전 → 자동 정합).
            filterExposedSlides(swiperEl);

            // ============================================
            // 속성 읽기
            // ============================================
            // 슬라이드 전환 속도 (초 단위, 기본값: 3초)
            const slideTransitionSpeed = Number.parseFloat(swiperEl.getAttribute('data-slide-transition-speed') || '3');

            // 페이징 버튼 표시 여부
            const pagingButtons = swiperEl.getAttribute('data-paging-buttons') === 'true';

            // 좌우 화살표 버튼 표시 여부
            const arrowButtons = swiperEl.getAttribute('data-arrow-buttons') === 'true';

            // 무한 루프 재생 여부
            const loopAttr = swiperEl.getAttribute('data-swiper-loop') === 'true';

            // 공통 스타일에서 페이징 타입 CSS 변수 읽기
            const paginationType = getComputedStyle(document.documentElement)
                .getPropertyValue('--nde-slide-pagination-type')
                .trim();

            // data-pagination-type 속성 설정
            if (paginationType) {
                swiperEl.setAttribute('data-pagination-type', paginationType);
            }

            // 자동 재생 여부 (기본값: true)
            const autoplay = swiperEl.getAttribute('data-autoplay') !== 'false';

            // 한 번에 보여줄 슬라이드 개수 (기본값: 1)
            const slidesPerView = parseInt(swiperEl.getAttribute('data-slides-per-view') || '1', 10);
            const slidesPerViewMobile = parseInt(swiperEl.getAttribute('data-slides-per-view-mobile') || '1', 10);

            // 한 번에 이동할 슬라이드 개수 (기본값: slidesPerView와 동일)
            const slidesPerGroup = parseInt(swiperEl.getAttribute('data-slides-per-group') || slidesPerView, 10);
            const slidesPerGroupMobile = parseInt(
                swiperEl.getAttribute('data-slides-per-group-mobile') || slidesPerViewMobile,
                10
            );

            // 슬라이드 간 간격 (px 단위, 기본값: 0)
            const spaceBetween = parseInt(swiperEl.getAttribute('data-space-between') || '0', 10);
            const spaceBetweenMobile = parseInt(swiperEl.getAttribute('data-space-between-mobile') || '0', 10);

            // 초기 슬라이드 인덱스 (상태 복원용, 기본값: 0)
            const initialSlide = parseInt(swiperEl.getAttribute('data-initial-slide') || '0', 10);

            // 활성 슬라이드 높이에 맞춰 자동 조정 여부 (기본값: false)
            const autoHeight = swiperEl.getAttribute('data-auto-height') === 'true';

            // ============================================
            // Swiper 설정 생성
            // ============================================
            // 슬라이드 수가 slidesPerView 이하이면 loop 비활성화 (Swiper Loop Warning 방지)
            const slideCount = swiperEl.querySelectorAll('.swiper-slide').length;
            const loop = loopAttr && slideCount > slidesPerView;

            const swiperConfig = {
                slidesPerView,
                slidesPerGroup,
                spaceBetween,
                loop,
                centeredSlides: false,
                autoHeight,
                breakpointsBase: 'container',
                initialSlide,
            };

            // 모바일 breakpoint 설정
            const hasBreakpointDifference =
                slidesPerView !== slidesPerViewMobile ||
                spaceBetween !== spaceBetweenMobile ||
                slidesPerGroup !== slidesPerGroupMobile;

            if (hasBreakpointDifference) {
                swiperConfig.breakpoints = {
                    0: {
                        slidesPerView: slidesPerViewMobile,
                        slidesPerGroup: slidesPerGroupMobile,
                        spaceBetween: spaceBetweenMobile,
                        centeredSlides: false,
                    },
                    768: {
                        slidesPerView,
                        slidesPerGroup,
                        spaceBetween,
                        centeredSlides: false,
                    },
                };
            }

            // autoplay 설정
            if (autoplay) {
                swiperConfig.autoplay = {
                    delay: slideTransitionSpeed * 1000,
                    disableOnInteraction: false,
                    pauseOnMouseEnter: true,
                };
            }

            // 페이지네이션 설정
            if (pagingButtons) {
                swiperConfig.pagination = {
                    el: swiperEl.querySelector('.swiper-pagination'),
                    clickable: true,
                };
            }

            // 네비게이션 버튼 설정
            if (arrowButtons) {
                swiperConfig.navigation = {
                    nextEl: swiperEl.querySelector('.swiper-button-next'),
                    prevEl: swiperEl.querySelector('.swiper-button-prev'),
                };
            }

            // ============================================
            // Swiper 초기화
            // ============================================
            try {
                // autoHeight 활성화 시: 슬라이드 이미지가 미로드 상태이면 로드 완료 후 높이 재계산
                // - Swiper는 updateAutoHeight() 호출 시점에 이미지가 로드되지 않으면 height=0으로 측정함
                // - 초기화 시점과 슬라이드 전환 후 모두 동일한 문제가 발생하므로 공통 헬퍼로 처리
                const scheduleAutoHeightOnImageLoad = (swiper, slide) => {
                    if (!autoHeight || !slide) {
                        return;
                    }
                    slide.querySelectorAll('img').forEach((img) => {
                        if (!img.complete) {
                            const updateHeight = () => {
                                if (!swiper.destroyed) {
                                    swiper.updateAutoHeight(0);
                                }
                            };
                            img.addEventListener('load', updateHeight, { once: true });
                            img.addEventListener('error', updateHeight, { once: true });
                        }
                    });
                };

                swiperConfig.on = {
                    slideChangeTransitionEnd() {
                        // 전환된 슬라이드의 이미지가 미로드 상태이면 로드 후 높이 재계산
                        scheduleAutoHeightOnImageLoad(this, this.slides[this.activeIndex]);
                    },
                };

                const swiperInstance = new Swiper(swiperEl, swiperConfig);

                // 초기화 시점의 active slide에 대해서도 동일하게 처리
                scheduleAutoHeightOnImageLoad(swiperInstance, swiperInstance.slides[swiperInstance.activeIndex]);
            } catch (error) {
                console.error('Failed to initialize Swiper:', error);
            }
        });
    };

    // 브라우저에서만 자동 부트스트랩 (node 단위 테스트 require 시 document 부재로 스킵).
    if (typeof document !== 'undefined') {
        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', initSwiper);
        } else {
            initSwiper();
        }
    }

    // 노출기간 날짜 술어 단위 테스트용 export — 브라우저에선 module 미정의로 스킵.
    if (typeof module !== 'undefined' && module.exports) {
        module.exports = { parseExposureDate, isWithinExposurePeriod };
    }
})();
