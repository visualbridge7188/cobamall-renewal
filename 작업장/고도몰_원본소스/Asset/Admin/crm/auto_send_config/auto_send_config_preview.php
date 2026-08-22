<section class="ncua-card" id="preview-section">
    <header class="ncua-card__header">
        <h4 class="ncua-card__title"><span id="preview-recipient-title">회원 </span>알림 미리보기</h4>
        <div class="ncua-horizontal-tab ncua-horizontal-tab--button-white">
            <div class="swiper swiper-initialized swiper-horizontal">
                <div class="swiper-wrapper">
                    <div class="swiper-slide ncua-horizontal-tab__item">
                        <button type="button" class="ncua-tab-button is-active" data-preview-tab="sms">SMS</button>
                    </div>
                    <div class="swiper-slide ncua-horizontal-tab__item">
                        <button type="button" class="ncua-tab-button" data-preview-tab="kakao">카카오 알림톡</button>
                    </div>
                    <div class="swiper-slide ncua-horizontal-tab__item">
                        <button type="button" class="ncua-tab-button" data-preview-tab="app">마이앱(앱푸시)</button>
                    </div>
                </div>
            </div>
        </div>
    </header>

    <section class="ncua-card__body">
        <div class="preview-content-wrap">
            <!-- CrmPreview 컨테이너 -->
            <div class="crm-preview-container"></div>

            <!-- 수신 대상 선택 안한 경우 미리보기 -->
            <section class="mobile-preview mobile-preview--none" data-preview-content="none" style="display: none;">
                <div class="ncua-prev--none">수신 대상을 선택해 주세요.</div>
            </section>
        </div>
    </section>
</section>
