<section class="ncua-card owned-skin-list-section max-width-center" data-default-tab="<?= $skinTabData['defaultTab'] ?>">
    <div>
        <header class="ncua-card__header">
            <h4 class="ncua-card__title">보유 스킨 리스트</h4>
        </header>
        <!-- 탭 영역 -->
        <?php if ($skinTabData['responsiveSkinCount'] > 0 && $skinTabData['adaptiveSkinCount'] > 0): ?>
        <div class="swiper ncua-horizontal-tab ncua-horizontal-tab--sm ncua-horizontal-tab--underline-fill">
            <div class="swiper-wrapper ncua-gap-8">
                <div class="swiper-slide ncua-horizontal-tab__item">
                    <a href="javascript:void(0);" class="ncua-tab-button js-tab-button <?= $skinTabData['isResponsiveActive'] ? 'is-active' : '' ?>" data-tab="responsive">
                        반응형
                    </a>
                </div>
                <div class="swiper-slide ncua-horizontal-tab__item">
                    <a href="javascript:void(0);" class="ncua-tab-button js-tab-button <?= $skinTabData['isAdaptiveActive'] ? 'is-active' : '' ?>" data-tab="adaptive">
                        PC/모바일
                    </a>
                </div>
            </div>
        </div>
        <?php endif; ?>
    </div>
    <section class="ncua-card__body">

        <!-- 리스트 영역 -->
        <div class="owned-skin-list-container">
            <div class="ncua-search-result__summary">
                <p class="ncua-search-result__summary-count">
                    전체 <strong class="js-total-count">0</strong>개
                </p>
                <!-- TODO: 반응형 스킨이 있는 경우 is-visible-hidden 클래스 추가 -->
                <div class="ncua-file-input ncua-file-input--xs">
                    <button type="button" class="ncua-btn ncua-btn--xs ncua-btn--secondary-gray js-skin-upload <?= $skinTabData['defaultTab'] === 'responsive' ? 'is-visible-hidden' : '' ?>">
                        <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="none" class="ncua-btn__icon">
                            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12v4.2c0 1.68 0 2.52-.327 3.162a3 3 0 0 1-1.311 1.311C18.72 21 17.88 21 16.2 21H7.8c-1.68 0-2.52 0-3.162-.327a3 3 0 0 1-1.311-1.311C3 18.72 3 17.88 3 16.2V12m13-5-4-4m0 0L8 7m4-4v12"></path>
                        </svg>
                        <span class="ncua-btn__label">스킨 업로드</span>
                    </button>
                </div>
            </div>

            <div class="owned-skin-list"></div>

            <div class="owned-skin-list--view-all" style="display: none;">
                <button class="ncua-btn ncua-btn--xs ncua-btn--tertiary-gray ncua-input-full-width js-view-all">
                    <span class="ncua-btn__label">
                        모두 보기
                    </span>
                    <i class="view-all-icon chevron-down"></i>
                </button>
            </div>
        </div>
    </section>
</section>
