<div class="js-kakao-friendtalk-tab-section">
    <input type="hidden" name="friendtalkCampaignImageUrl" value="">
    <input type="hidden" name="friendtalkCouponNo" value="">
    <div class="swiper ncua-horizontal-tab ncua-horizontal-tab--md ncua-horizontal-tab--panel">
        <div class="swiper-wrapper">
            <div class="swiper-slide ncua-horizontal-tab__item">
                <button type="button" class="ncua-tab-button is-active" data-send-tab-type="MAIN">
                    친구톡 메시지 작성
                </button>
            </div>
            <div class="swiper-slide ncua-horizontal-tab__item">
                <button type="button" class="ncua-tab-button" data-send-tab-type="ALTERNATIVE">
                    SMS/LMS(대체 메시지)
                </button>
            </div>
        </div>
    </div>
    <div class="tab-content-wrap">
        <!-- 캠페인 명 입력 -->
        <div class="campaign-name send-method-component" data-target="MAIN">
            <div class="campaign-name-input-wrap">
                <p class="ncua-card__body-title--sm ncua-card__body-title--required">캠페인 명</p>
                <div class="ncua-input ncua-input--sm">
                    <div class="ncua-input__content">
                        <div class="ncua-input__field ncua-input__field--sm">
                            <input name="friendtalkCampaignName" type="text" value="<?= gd_htmlspecialchars($campaignName ?? '') ?>" maxlength="20" placeholder="캠페인명을 입력해주세요 (최대 20자)" />
                        </div>
                    </div>
                </div>
            </div>
            <!-- 캠페인 메시지 타입 -->
            <div class="campaign-message-radio-wrap send-method-component" data-target="MAIN">
                <?php foreach ($campaignMessageTypes as $campaignMessageType): ?>
                    <label class="ncua-radio-field ncua-radio-field--xs has-text">
                    <span class="ncua-radio-input ncua-radio-input--xs ncua-radio-field__input">
                        <input name="campaignMessageType" type="radio" value="<?= $campaignMessageType->name ?>" <?= $campaignMessageType->name === 'TEXT' ? 'checked="checked"' : '' ?>/>
                    </span>
                        <span>
                        <span class="ncua-radio-field__text"><?= $campaignMessageType->value ?></span>
                    </span>
                    </label>
                <?php endforeach; ?>
            </div>

            <!-- 와이드 아이템, 캐러셀 안내 문구 -->
            <div class="campaign-wide-item-carousel-notice send-method-component campaign-type-component" data-target="MAIN" data-campaign-type="WIDE_ITEM_LIST,CAROUSEL_FEED">
                <ul>
                    <li class="ncua-caution-text">해당 메세지형은 대체 메시지를 지원하지 않습니다.</li>
                </ul>
            </div>
        </div>

        <!-- 캠페인 캐러셀 수 -->
        <div class="campaign-carousel-count send-method-component campaign-type-component js-kakao-friendtalk-carousel" data-target="MAIN" data-campaign-type="CAROUSEL_FEED">
            <div class="swiper ncua-horizontal-tab ncua-horizontal-tab--sm ncua-horizontal-tab--button-white">
                <div class="swiper-wrapper">
                    <!-- 캐러셀 탭은 JS로 동적 렌더링 됨 -->
                </div>
            </div>
            <button type="button" class="ncua-btn ncua-btn--xs ncua-btn--tertiary-gray plus-btn js-add-carousel-btn">
                <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" fill="none" viewBox="0 0 24 24" stroke="none"><path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 5v14m-7-7h14"></path></svg>
                추가
            </button>
        </div>

        <!-- 캠페인 타이틀 -->
        <div class="campaign-title send-method-component campaign-type-component" data-target="MAIN" data-campaign-type="WIDE_ITEM_LIST">
            <p class="ncua-card__body-title--xs">타이틀</p>
            <div class="ncua-input ncua-input--xs ncua-input-full-width">
                <div class="ncua-input__content-wrap">
                    <div class="ncua-input__content">
                        <div class="ncua-input__field ncua-input__field--xs">
                            <input class="ncua-campaign-header" data-charcount-key="header" maxlength="20" name="friendtalkCampaignHeader" type="text" value="" placeholder="내용을 입력해 주세요." />
                        </div>
                    </div>
                    <div class="ncua-input__field-text-count" data-charcount-text="header">
                        <output class="ncua-input__field-text-count-current">0</output>
                        <span>/20</span>
                    </div>
                </div>
            </div>
            <?php if ($isRecipeContext): ?>
            <div class="ncua-hint-text js-wide-item-point-hint display-none"></div>
            <?php endif; ?>
        </div>

        <!-- 캠페인 이미지 -->
        <div class="campaign-image send-method-component campaign-type-component" data-target="MAIN" data-campaign-type="IMAGE,WIDE_IMAGE,CAROUSEL_FEED">
            <p class="ncua-card__body-title--xs">캠페인 이미지</p>
            <div class="ncua-table ncua-table--horizontal">
                <table>
                    <colgroup>
                        <col width="79px" />
                        <col />
                    </colgroup>
                    <thead>
                        <tr>
                            <th class="ncua-required"><div class="ncua-align-center">이미지</div></th>
                            <th class="ncua-required"><div data-tooltip-seq="008">WEB 링크</div></th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>
                                <div class="ncua-align-center">
                                    <input hidden name="campaignImage" class="no-filestyle" id="campaignImageInput" tabindex="-1" aria-hidden="true" type="file" />
                                    <div id="campaign-image-file-input-container"></div>
                                </div>
                            </td>
                            <td>
                                <div>
                                    <div class="ncua-input ncua-input--xs ncua-input-full-width">
                                        <div class="ncua-input__content">
                                            <div class="ncua-input__field ncua-input__field--xs">
                                                <input name="friendtalkCampaignImageLink" type="text" value="" placeholder="<?= trim(URI_HOME,'/') ?>" />
                                            </div>
                                        </div>
                                        <span class="ncua-hint-text ncua-input__hint-text">외부 링크 입력 시 캠페인 통계가 집계되지 않습니다.</span>
                                    </div>
                                </div>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
            <ul class="campaign-image-notice campaign-type-component" data-campaign-type="IMAGE">
                <li class="ncua-notice-info">권장 사이즈 : 600*800px (3:4 비율)</li>
                <li class="ncua-notice-info">5MB이내인 jpg, png 형식의 파일을 등록해 주세요.</li>
            </ul>
            <ul class="campaign-wide-image-notice campaign-type-component" data-campaign-type="WIDE_IMAGE">
                <li class="ncua-notice-info">권장 사이즈 : 800*600px (4:3 비율)</li>
                <li class="ncua-notice-info">5MB이내인 jpg, png 형식의 파일을 등록해 주세요.</li>
            </ul>
            <ul class="campaign-carousel-feed-notice campaign-type-component" data-campaign-type="CAROUSEL_FEED">
                <li class="ncua-notice-info">권장 사이즈 : 800*400px (2:1 비율) / 800*800px (1:1 비율) / 800*600px (4:3 비율)</li>
                <li class="ncua-notice-info">5MB이내인 jpg, png 형식의 파일을 등록해 주세요.</li>
            </ul>
        </div>

        <div class="message-content send-method-component campaign-type-component" data-target="MAIN" data-campaign-type="TEXT,IMAGE,WIDE_IMAGE,CAROUSEL_FEED">
            <div class="ncua-card__body-title-wrap">
                <p class="ncua-card__body-title--xs">내용 입력</p>
            </div>
            <div class="myapp-message-title campaign-type-component" data-campaign-type="CAROUSEL_FEED">
                <div class="ncua-input ncua-input--xs ncua-input-full-width">
                    <div class="ncua-input__content-wrap">
                        <div class="ncua-input__content">
                            <div class="ncua-input__field ncua-input__field--xs">
                                <input class="ncua-campaign-title" data-charcount-key="title" maxlength="20" name="friendtalkCampaignTitle" type="text" value="" placeholder="제목을 입력해 주세요." />
                            </div>
                        </div>
                        <div class="ncua-input__field-text-count" data-charcount-text="title">
                            <output class="ncua-input__field-text-count-current">0</output>
                            <span>/20</span>
                        </div>
                    </div>
                </div>
            </div>
            <div id="messageContentContainer"></div>
        </div>
        <div id="replaceCodeContainer" class="js-replace-code send-method-component" data-target="MAIN"></div>

        <div class="message-content send-method-component" data-target="ALTERNATIVE">
            <div class="ncua-card__body-title-wrap">
                <p class="ncua-card__body-title--xs">내용 입력</p>
                <button type="button" class="ncua-btn ncua-btn--xs ncua-btn--text-gray has-underline refresh-btn load-main-contents">
                    메시지 최신 상태 반영
                </button>
            </div>
            <div id="messageContentAlternativeContainer"></div>
        </div>
        <div id="replaceCodeAlternativeContainer" class="js-replace-code send-method-component" data-target="ALTERNATIVE"></div>

        <!-- 메시지 성과 추적 -->
        <div class="message-performance-analysis send-method-component campaign-type-component" data-target="MAIN,ALTERNATIVE" data-campaign-type="TEXT,IMAGE">
            <p class="ncua-card__body-title--xs tooltip-align" data-tooltip-seq="004">메시지 성과 추적</p>
            <div class="add-link-wrap">
                <div class="ncua-input ncua-input--xs">
                    <div class="ncua-input__content">
                        <div class="ncua-input__field ncua-input__field--xs">
                            <input name="shortLink" type="text" value="" placeholder="메시지에 삽입할 링크를 여기에 넣고 추가를 누르면 숏링크가 생성됩니다." data-role="short-link-input" />
                        </div>
                    </div>
                </div>
                <button type="button" class="ncua-btn ncua-btn--xs ncua-btn--secondary-gray plus-btn" data-role="add-short-link">
                    <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" fill="none" viewBox="0 0 24 24" stroke="none"><path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 5v14m-7-7h14"></path></svg>
                    추가
                </button>
            </div>
            <div class="chip-button-wrap" data-role="chip-container"></div>
        </div>
        <!-- 친구톡 추가 버튼 -->
        <div class="kakao-action-buttons send-method-component js-kakao-friendtalk-button-section" data-target="MAIN">
            <div class="ncua-card__body-title-wrap">
                <p class="ncua-card__body-title--xs">버튼</p>
                <div class="button-wrap">
                    <button type='button' class="ncua-btn ncua-btn--xs ncua-btn--secondary-gray" data-action="add-coupon">쿠폰 추가</button>
                    <button type='button' class="ncua-btn ncua-btn--xs ncua-btn--secondary-gray" data-action="add-link">링크 추가</button>
                </div>
            </div>

            <div class="empty-coupon-data">
                <p>추가된 버튼이 없습니다.</p>
            </div>
        </div>

        <!-- 캠페인 아이템 리스트 -->
        <div class="campaign-item-list send-method-component campaign-type-component js-kakao-friendtalk-itemlist-section" data-target="MAIN" data-campaign-type="WIDE_ITEM_LIST">
            <div class="ncua-card__body-title-wrap">
                <p class="ncua-card__body-title--xs">아이템 리스트</p>
                <button type="button" class="ncua-btn ncua-btn--xs ncua-btn--secondary-gray plus-btn" data-action="add-friendtalk-item">
                    <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" fill="none" viewBox="0 0 24 24" stroke="none"><path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 5v14m-7-7h14"></path></svg>
                    아이템 추가
                </button>
            </div>
            <div class="ncua-table ncua-table--horizontal">
                <table>
                    <colgroup>
                        <col width="79px" />
                        <col />
                        <col />
                        <col width="88px" />
                    </colgroup>
                    <thead>
                    <tr>
                        <th class="ncua-required"><div class="ncua-align-center">이미지</div></th>
                        <th class="ncua-required"><div>타이틀</div></th>
                        <th class="ncua-required"><div data-tooltip-seq="008">WEB 링크</div></th>
                        <th><div class="ncua-align-center">삭제</div></th>
                    </tr>
                    </thead>
                    <tbody>

                    </tbody>
                </table>
            </div>
            <ul class="campaign-item-list-notice">
                <li class="ncua-notice-info">첫번째 아이템 권장 사이즈 : 800*400px (2:1 비율)</li>
                <li class="ncua-notice-info">추가 아이템 권장 사이즈 : 500*500px (1:1 비율)</li>
                <li class="ncua-notice-info">5MB이내인 jpg, png 형식의 파일을 등록해 주세요.</li>
            </ul>
        </div>
    </div>
    <!-- 친구톡 포인트 정보 -->
    <div class="kakao-friend-point-info send-method-component" data-target="MAIN">
        <p class="ncua-card__body-title--sm">메시지 타입 & 사용 포인트</p>
        <div class="ncua-table ncua-table--vertical">
            <table>
                <tbody>
                <tr>
                    <th><div>메시지 타입</div></th>
                    <td>
                        <div class="ncua-gap-4" id="campaignUnitPoint">
                            텍스트 (건당 <strong>1.4</strong> 포인트)
                        </div>
                    </td>
                </tr>
                <tr>
                    <th><div>사용 포인트</div></th>
                    <td>
                        <div class="ncua-gap-4">
                            <strong id="expectMessagePoint">0</strong> 포인트
                        </div>
                    </td>
                </tr>
                </tbody>
            </table>
        </div>
        <ul class="kakao-friend-point-info-notice">
            <li class="ncua-caution-text">표시된 포인트는 1회 발송 기준입니다.</li>
            <li class="ncua-notice-info">CRM 그룹 선택 후 예약·반복 발송 시에는 대상자 변경으로 예상 포인트가 달라질 수 있습니다.</li>
        </ul>
    </div>
    <!-- 친구톡 안내 -->
    <details class="ncua-accordion ncua-accordion--gray send-method-component" data-target="MAIN" <?= !$isRecipeContext ? 'open' : '' ?>>
        <summary>친구톡 안내</summary>
        <div class="ncua-accordion__content has-dot">
            <ul>
                <li>예약 및 반복 발송 시 선택한 CRM 그룹의 예상 발송 인원은 실제 발송 시점의 인원과 상이할 수 있습니다.</li>
                <li>수신 대상 중 카카오톡 채널 친구가 아닌 회원에게는 캠페인 발송에 실패하며 발송 실패 시 차감된 포인트는 반환됩니다.</li>
                <li>수신 번호가 중복된 경우 가장 먼저 조회된 회원에게만 발송됩니다. 중복 발송 실패 시 차감된 포인트는 반환됩니다.</li>
                <li>대체 발송의 경우 <a href="../crm/message_config.php" target="_top">메시지 설정</a>에서 변경이 가능합니다.</li>
            </ul>
        </div>
    </details>
    <!-- SMS/LMS 대체 발송 안내 -->
    <details class="ncua-accordion ncua-accordion--gray send-method-component" data-target="ALTERNATIVE" <?= !$isRecipeContext ? 'open' : '' ?>>
        <summary>SMS/LMS 발송 안내</summary>
        <div class="ncua-accordion__content has-dot">
            <ul>
                <li>SMS 작성 시 90byte를 초과하면 LMS로 자동 전환되어 발송됩니다.</li>
                <li>변수에 실제 데이터가 적용된 최종 메시지가 90byte를 초과하는 경우에도 LMS로 전환될 수 있습니다.</li>
            </ul>
        </div>
    </details>
    <!-- 광고성 문구 추가 -->
    <div class="ad-phrase-add send-method-component" data-target="ALTERNATIVE">
        <p class="ncua-card__body-title--xs">광고성 문구 추가</p>
        <label class="ncua-checkbox-field ncua-checkbox-field--xs has-text">
            <span class="ncua-checkbox-input ncua-checkbox-input--xs ncua-checkbox-field__input">
                <input type="checkbox" name="use080Reject"  data-available="<?= $is080RejectAvailable ? 'y' : 'n' ?>">
            </span>
            <span>
                <span class="ncua-checkbox-field__text">광고성 문구 추가</span>
                <span class="ncua-checkbox-field__support-text">광고성 문구를 추가하려면 <a class="ncua-btn ncua-btn--xs ncua-btn--text has-underline" href="../service/service_info.php?menu=consulting_refusal_info" target="_blank">[080 수신거부 사용신청]</a>을 먼저 해주시기 바랍니다.</span>
            </span>
        </label>
    </div>
</div>

<script type="text/javascript">
    const kakaoFriendtalkTabSection = document.querySelector('.js-kakao-friendtalk-tab-section');
    const kakaoFriendtalkButtonSection = document.querySelector('.js-kakao-friendtalk-button-section');
    const kakaoFriendtalkItemListSection = document.querySelector('.js-kakao-friendtalk-itemlist-section');
    const kakaoFriendtalkCarouselSection = document.querySelector('.js-kakao-friendtalk-carousel');
    const chipContainer = document.querySelector('[data-role="chip-container"]');

    // 아이템 리스트 행별 이미지 업로더 (itemKey → ImageFileInput)
    const itemImageFileInputs = new Map();
    // 동기 루프에서 itemKey 충돌 방지용 시퀀스
    let itemRowKeySeq = 0;

    const isRecipeContext = <?= $isRecipeContext ? 'true' : 'false' ?>;
    const linkPlatformType = '<?= $linkPlatformType ?>';
    const campaignTypeUnitPoint = <?= json_encode($campaignMessageTypeUnitPoints, JSON_UNESCAPED_UNICODE) ?>;

    function friendtalkUnitPointHint(campaignMessageType) {
        return `친구톡 건당 ${parseFloat(campaignTypeUnitPoint[campaignMessageType]).toFixed(1)}포인트 차감`;
    }
    const friendtalkRequest = <?= json_encode($friendtalkRequest ?? [], JSON_UNESCAPED_UNICODE | JSON_HEX_TAG | JSON_HEX_AMP) ?>;
    const MAX_CAMPAIGN_IMAGE_FILE_SIZE = 5 * 1024 * 1024; // 5MB
    const MIN_CAMPAIGN_IMAGE_DIMENSION = {minWidth: 500, minHeight: 250};
    const MIN_ITEM_FIRST_IMAGE_DIMENSION = {minWidth: 500, minHeight: 250}; // 첫번째 아이템 2:1 비율
    const MIN_ITEM_ADDITIONAL_IMAGE_DIMENSION = {minWidth: 500, minHeight: 500}; // 추가 아이템 1:1 비율
    let campaignImageFileInput = null;

    window.carouselState = {
        MIN_CAROUSELS: 2,
        MAX_CAROUSELS: 6,
        activeIndex: 0,
        carousels: [],
        // 기본 캐러셀 데이터 구조
        createEmptyCarousel: () => ({ ...
                {
                    header: '',
                    message: '',
                    imageUrl: '',
                    imageLink: '',
                    shortLinkKeys: new Map(),
                    buttons: [{
                        name: '',
                        url: {
                            mobileUrl: messageConfig.kakaoFriendTalk.linkPlatformType === 'MOBILE_APP' ? null : '',
                            iosUrl: messageConfig.kakaoFriendTalk.linkPlatformType !== 'MOBILE_APP' ? null : '',
                            aosUrl: messageConfig.kakaoFriendTalk.linkPlatformType !== 'MOBILE_APP' ? null : ''
                        }
                    }],
                    coupon: null,
                    isDeleted: false
                }
        }),
        // 초기화 (2개의 캐러셀로 시작)
        init() {
            this.carousels = [
                this.createEmptyCarousel(),
                this.createEmptyCarousel()
            ];
            this.activeIndex = 0;
            this.renderTabs();
            this.renderCurrentCarouselForm();
        },
        // 탭 UI 렌더링
        renderTabs() {
            const container = kakaoFriendtalkCarouselSection.querySelector('.swiper-wrapper');
            container.innerHTML = '';

            let tabIndex = 1;
            this.carousels.forEach((carousel, index) => {
                if (carousel.isDeleted) return;
                const isActive = index === this.activeIndex;
                const currentTabIndex = tabIndex++;
                const showCloseBtn = currentTabIndex > this.MIN_CAROUSELS;
                const tabItem = document.createElement('div');
                tabItem.className = 'swiper-slide ncua-horizontal-tab__item';
                tabItem.style.marginRight = '8px';

                tabItem.innerHTML = `
                    <button type="button" class="ncua-tab-button ${isActive ? 'is-active' : ''}" data-carousel-index="${index}">
                        캐러셀${currentTabIndex}
                    </button>
                    ${showCloseBtn ? `
                    <button type="button" class="ncua-tab-close" data-carousel-close="${index}">
                        <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" fill="none" viewBox="0 0 24 24" stroke="none">
                            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 6 6 18M6 6l12 12"></path>
                        </svg>
                    </button>
                    ` : ''}
                `;

                container.appendChild(tabItem);
            });

            // 탭 클릭 이벤트 바인딩
            container.querySelectorAll('.ncua-tab-button[data-carousel-index]').forEach(btn => {
                btn.addEventListener('click', (e) => {
                    const idx = parseInt(e.currentTarget.dataset.carouselIndex);
                    this.setActiveIndex(idx);
                });
            });

            // 닫기 버튼 이벤트 바인딩
            container.querySelectorAll('.ncua-tab-close[data-carousel-close]').forEach(btn => {
                btn.addEventListener('click', (e) => {
                    e.stopPropagation();
                    const idx = parseInt(e.currentTarget.dataset.carouselClose);
                    this.removeCarousel(idx);
                });
            });

            // 추가 버튼 상태 업데이트
            kakaoFriendtalkCarouselSection.querySelector('.js-add-carousel-btn').disabled = this.carousels.filter(c => !c.isDeleted).length >= this.MAX_CAROUSELS;
        },
        // 현재 캐러셀 인덱스 변경
        setActiveIndex(index) {
            if (index < 0 || index >= this.carousels.length) return;
            this.saveCurrentCarouselData();
            this.activeIndex = index;
            this.renderTabs();
            window.preview.setSlideIndex(index);
            this.renderCurrentCarouselForm();
            // 슬라이드 전환 후 미리보기 이미지 복원
            if (window.preview.getSlideCount() > 0) {
                window.preview.setImage(this.carousels[index].imageUrl || ' ');
            }

            updateDefaultPreview('CAROUSEL_FEED');
        },
        // 캐러셀 추가
        addCarousel() {
            if (this.carousels.filter(c => !c.isDeleted).length >= this.MAX_CAROUSELS) {
                NCDSAlert({ message: `캐러셀은 최대 ${this.MAX_CAROUSELS}개까지 추가할 수 있습니다.`, iconType: 'error' });
                return false;
            }
            this.carousels.push(this.createEmptyCarousel());
            this.renderTabs();
            window.preview.addSlide();
            return true;
        },
        // 캐러셀 삭제
        removeCarousel(index) {
            if (this.carousels.filter(c => !c.isDeleted).length <= this.MIN_CAROUSELS) {
                NCDSAlert({ message: `캐러셀은 최소 ${this.MIN_CAROUSELS}개 이상이어야 합니다.`, iconType: 'error' });
                return false;
            }
            this.carousels[index].isDeleted = true;
            if (this.activeIndex === index) {
                const nextIndex = this.carousels.findIndex((c, i) => i > index && !c.isDeleted);
                const prevIndex = this.carousels.findLastIndex((c, i) => i < index && !c.isDeleted);
                this.activeIndex = nextIndex !== -1 ? nextIndex : prevIndex;
            }
            this.renderTabs();
            window.preview.removeSlide(index);
            this.renderCurrentCarouselForm();
            return true;
        },
        // 현재 활성 캐러셀 데이터 저장
        saveCurrentCarouselData() {
            const carousel = this.carousels[this.activeIndex];
            if (!carousel) return;

            // 타이틀 (header)
            carousel.header = kakaoFriendtalkTabSection.querySelector('input[name="friendtalkCampaignTitle"]').value;

            // 이미지 URL
            carousel.imageUrl = kakaoFriendtalkTabSection.querySelector('input[name="friendtalkCampaignImageUrl"]').value;

            // 이미지 링크
            carousel.imageLink = kakaoFriendtalkTabSection.querySelector('input[name="friendtalkCampaignImageLink"]').value;

            // 메시지 (message)
            carousel.message = window.messageInput.getValue();

            carousel.shortLinkKeys = window.shortLinkKeys;

            // 쿠폰 데이터 수집
            carousel.coupon = this.collectCouponData();

            // 버튼 데이터 수집
            carousel.buttons = this.collectButtonsData();
        },
        // 쿠폰 데이터 수집
        collectCouponData() {
            const couponTable = kakaoFriendtalkTabSection.querySelector('#kakaoFriendTalkcouponTable');
            if (!couponTable) return null;
            const couponNoInput = kakaoFriendtalkTabSection.querySelector('input[name="friendtalkCouponNo"]');
            if (!couponTable || !couponNoInput) return null;

            const couponNo = parseInt(couponNoInput.value) || null;
            if (!couponNo) return null;

            const titleInput = couponTable.querySelector('input[name="couponTitle"]');
            const descInput = couponTable.querySelector('input[name="couponDescription"]');
            const urlInput = couponTable.querySelector('input[name="couponUrl"]');

            return {
                couponNo: couponNo,
                couponName: couponNoInput.dataset.couponName,
                couponPeriod: couponNoInput.dataset.couponPeriod,
                couponUseType: couponNoInput.dataset.couponUseType,
                couponDeviceType: couponNoInput.dataset.couponDeviceType,
                title: titleInput?.value?.replace(/^\s+|\s+$/g, '') || '',
                description: descInput?.value?.replace(/^\s+|\s+$/g, '') || '',
                url: urlInput?.value?.replace(/^\s+|\s+$/g, '') || ''
            };
        },
        // 버튼 데이터 수집
        collectButtonsData() {
            if (!kakaoFriendtalkButtonSection.querySelector('#linkButtonTable')) {
                return [];
            }

            const linkButtonTableBody = kakaoFriendtalkButtonSection.querySelector('#linkButtonTableBody');
            const buttons = [];
            linkButtonTableBody.querySelectorAll('tr').forEach(row => {
                const buttonName = row.querySelector('.ncua-link-button-name').value;
                const webUrlInput = row.querySelector('input[name="linkButtonWebUrl"]')?.value;
                const iosUrlInput = row.querySelector('input[name="linkButtonIOSUrl"]')?.value;
                const aosUrlInput = row.querySelector('input[name="linkButtonAOSUrl"]')?.value;

                const linkPlatformType = messageConfig.kakaoFriendTalk.linkPlatformType;
                const isMobileApp = linkPlatformType === 'MOBILE_APP';

                buttons.push({
                    name: buttonName.replace(/^\s+|\s+$/g, ''),
                    url: {
                        mobileUrl: isMobileApp ? null : (webUrlInput?.replace(/^\s+|\s+$/g, '') || ''),
                        iosUrl: !isMobileApp ? null : (iosUrlInput?.replace(/^\s+|\s+$/g, '') || ''),
                        aosUrl: !isMobileApp ? null : (aosUrlInput?.replace(/^\s+|\s+$/g, '') || '')
                    }
                });
            });
            return buttons;
        },
        // 현재 캐러셀 폼 데이터 렌더링
        renderCurrentCarouselForm: function () {
            const carousel = this.carousels[this.activeIndex];
            if (!carousel) return;

            // 타이틀 (header)
            const campaignTitleInput = kakaoFriendtalkTabSection.querySelector('input[name="friendtalkCampaignTitle"]');
            campaignTitleInput.value = carousel.header;
            const titleCountEl = kakaoFriendtalkTabSection.querySelector('.ncua-input__field-text-count[data-charcount-text="title"] .ncua-input__field-text-count-current');
            if (titleCountEl) titleCountEl.textContent = campaignTitleInput.value.length;

            // 이미지 URL (hidden input)
            kakaoFriendtalkTabSection.querySelector('input[name="friendtalkCampaignImageUrl"]').value = carousel.imageUrl;

            // 캠페인 이미지 ImageFileInput 재생성 (캐러셀별 독립적 이미지)
            createCampaignImageFileInput(carousel.imageUrl || null);

            // 이미지 링크
            kakaoFriendtalkTabSection.querySelector('input[name="friendtalkCampaignImageLink"]').value = carousel.imageLink;

            // 메시지 (message)
            window.messageInput.setValue(carousel.message);
            <?php if ($isRecipeContext): ?>
            // 레시피: 슬라이드 전환은 setValue 라 onInput 미발생 → 포인트 힌트·안내문구 직접 반영
            window.messageInput.setHint(carousel.message ? friendtalkUnitPointHint(getCampaignMessageType()) : null);
            MobileMessageReplaceCode.updateExpireCodeGuide('#replaceCodeContainer', carousel.message);
            <?php endif; ?>

            // 버튼/쿠폰 영역 렌더링
            kakaoFriendtalkButtonSection.querySelectorAll('.ncua-table').forEach(t => t.remove());
            kakaoFriendtalkButtonSection.querySelector('.empty-coupon-data').classList.remove('display-none');

            if (carousel.coupon) {
                appendCouponRow();

                const couponData = {
                    couponName: carousel.coupon.couponName,
                    couponPeriod: carousel.coupon.couponPeriod,
                    couponUseType: carousel.coupon.couponUseType,
                    couponDeviceType: carousel.coupon.couponDeviceType,
                };

                const couponDetailContainer = kakaoFriendtalkTabSection.querySelector('.coupon-detail-container');
                couponDetailContainer.innerHTML = '';
                couponDetailContainer.appendChild(generateCouponColumn(couponData));

                couponDetailContainer.querySelector('input[name="couponTitle"]').value =carousel.coupon.title;
                couponDetailContainer.querySelector('input[name="couponDescription"]').value = carousel.coupon.description;
                couponDetailContainer.querySelector('input[name="couponUrl"]').value = carousel.coupon.url;
            }

            if (getCampaignMessageType() === 'CAROUSEL_FEED' && carousel.buttons.length > 0) {
                carousel.buttons.forEach(button => {
                    appendLinkRow();
                    const lastLinkButton = kakaoFriendtalkButtonSection.querySelector('#linkButtonTableBody tr:last-child');
                    lastLinkButton.querySelector('.ncua-link-button-name').value = button.name;
                    const webUrlInput = lastLinkButton.querySelector('input[name="linkButtonWebUrl"]');
                    const iosUrlInput = lastLinkButton.querySelector('input[name="linkButtonIOSUrl"]');
                    const aosUrlInput = lastLinkButton.querySelector('input[name="linkButtonAOSUrl"]');

                    const linkPlatformType = messageConfig.kakaoFriendTalk.linkPlatformType;
                    const isMobileApp = linkPlatformType === 'MOBILE_APP';

                    if (webUrlInput) webUrlInput.value = isMobileApp ? '' : (button.url.mobileUrl ?? '');
                    if (iosUrlInput) iosUrlInput.value = !isMobileApp ? '' : (button.url.iosUrl ?? '');
                    if (aosUrlInput) aosUrlInput.value = !isMobileApp ? '' : (button.url.aosUrl ?? '');
                    updateLinkPreview();
                });
            }

            checkTable();

            // 성과분석 숏링크 초기화
            kakaoFriendtalkTabSection.querySelector('input[name="shortLink"]').value = '';
            kakaoFriendtalkTabSection.querySelector('[data-role="chip-container"]').innerHTML = '';

            // 숏링크 추가
            window.shortLinkKeys = new Map();
            carousel.shortLinkKeys.forEach((linkValue, linkKey) => {
                addKakaoFriendtalkChipButton(linkValue.replace(/^\s+|\s+$/g, ''), linkKey);
            });

            const shortLinkInput = kakaoFriendtalkTabSection.querySelector('input[data-role="short-link-input"]');
            const addShortLinkBtn = kakaoFriendtalkTabSection.querySelector('button[data-role="add-short-link"]');
            const isImpossibleAddShortLink = carousel.shortLinkKeys.size > 4;
            shortLinkInput.disabled = isImpossibleAddShortLink;
            addShortLinkBtn.disabled = isImpossibleAddShortLink;
        },
        // 발송용 carousels 데이터 수집
        getCarouselsData() {
            // 현재 활성 캐러셀 데이터 먼저 저장
            this.saveCurrentCarouselData();

            return {
                carousels: this.carousels.filter(c => !c.isDeleted).map(carousel => {
                    return {
                        header: carousel.header || '',
                        message: carousel.message || '',
                        attachment: {
                            buttons: carousel.buttons && carousel.buttons.length > 0 ? carousel.buttons : null,
                            image: {
                                imageUrl: carousel.imageUrl.replace(/^\s+|\s+$/g, '') || '',
                                imageLink: carousel.imageLink.replace(/^\s+|\s+$/g, '') || ''
                            },
                            coupon: carousel.coupon ? {
                                title: carousel.coupon.title || '',
                                description: carousel.coupon.description || '',
                                url: carousel.coupon.url.replace(/^\s+|\s+$/g, '') || '',
                                couponNo: carousel.coupon.couponNo || null
                            } : null
                        }
                    };
                }),
                replaceKeys: extractReplaceKeys(this.carousels.filter(c => !c.isDeleted).map(c => `${c.header || ''} ${c.message || ''}`).join(' '), true),
                trackingLink: this.carousels.filter(c => !c.isDeleted).flatMap(carousel => {
                    const shortLinks = [];
                    carousel.shortLinkKeys.forEach((url, key) => {
                        if (carousel.message && carousel.message.includes(`{${key}}`)) {
                            shortLinks.push({ key, url });
                        }
                    });
                    return shortLinks;
                })
            }
        }
    }

    /**
     * 쿠폰 컬럼 HTML 생성
     * @param {Object} coupon
     * @param {string} coupon.couponName       - 쿠폰명
     * @param {string} coupon.couponUseType    - 쿠폰 유형
     * @param {string} coupon.couponPeriod     - 사용 기간
     * @param {string} coupon.couponDeviceType - 사용 범위
     * @returns {string} HTML 문자열
     */
    function generateCouponColumn(coupon) {
        const campaignMessageType = getCampaignMessageType();
        const element = document.createElement('div');
        element.className = 'coupon-detail-wrap';
        element.innerHTML = `
            <p class="coupon-detail-title">${coupon.couponName}</p>
            <dl class="coupon-detail-info">
                <dt>쿠폰 유형</dt><dd class="coupon-detail-use-type">${coupon.couponUseType}</dd>
                <dt>사용 기간</dt><dd class="coupon-detail-period">${coupon.couponPeriod}</dd>
                <dt>사용 범위</dt><dd class="coupon-detail-device-type">${coupon.couponDeviceType}</dd>
            </dl>
            <div class="coupon-detail-input-wrap">
                <div class="ncua-input ncua-input--xs">
                    <div class="ncua-input__label ncua-input__label--xs">
                        <label class="ncua-label is-required" data-tooltip-seq="006">쿠폰 타이틀</label>
                    </div>
                    <div class="ncua-input__content-wrap">
                        <div class="ncua-input__content">
                            <div class="ncua-input__field ncua-input__field--xs">
                                <input name="couponTitle" type="text" value="" placeholder="쿠폰 타이틀을 입력해주세요." />
                            </div>
                        </div>
                    </div>
                </div>
                <div class="ncua-input ncua-input--xs">
                    <div class="ncua-input__label ncua-input__label--xs">
                        <label class="ncua-label is-required" data-tooltip-seq="007">쿠폰 설명</label>
                    </div>
                    <div class="ncua-input__content-wrap">
                        <div class="ncua-input__content">
                            <div class="ncua-input__field ncua-input__field--xs">
                                <input class="ncua-coupon-description" data-charcount-key="coupon-description" maxlength="${['TEXT', 'IMAGE', 'CAROUSEL_FEED'].includes(campaignMessageType) ? 12 : 18}" name="couponDescription" type="text" value="" placeholder="쿠폰 설명을 입력해주세요." />
                            </div>
                        </div>
                        <div class="ncua-input__field-text-count" data-charcount-text="coupon-description">
                            <output class="ncua-input__field-text-count-current">0</output>
                            <span>/${['TEXT', 'IMAGE', 'CAROUSEL_FEED'].includes(campaignMessageType) ? 12 : 18}</span>
                        </div>
                    </div>
                </div>
                <div class="ncua-input ncua-input--xs ncua-input-width-400">
                    <div class="ncua-input__label ncua-input__label--xs">
                        <label class="ncua-label is-required" data-tooltip-seq="008">WEB 링크</label>
                    </div>
                    <div class="ncua-input__content-wrap">
                        <div class="ncua-input__content">
                            <div class="ncua-input__field ncua-input__field--xs">
                                <input type="text" name="couponUrl" value="" placeholder="WEB 링크를 입력해주세요." />
                            </div>
                        </div>
                    </div>
                    <span class="ncua-hint-text ncua-input__hint-text">외부 링크 입력 시 캠페인 통계가 집계되지 않습니다.</span>
                </div>
            </div>
            <button type="button" class="ncua-btn only-icon ncua-btn--xs ncua-btn--tertiary-gray remove-coupon-btn">삭제</button>
        `;

        element.querySelector('input[name="couponTitle"]').addEventListener('input', (e) => {
            const couponDescription = element.querySelector('input[name="couponDescription"]').value;
            window.preview.setCoupon({text: e.target.value, date: couponDescription});
        });

        element.querySelector('input[name="couponDescription"]').addEventListener('input', (e) => {
            const couponTitle = element.querySelector('input[name="couponTitle"]').value;
            window.preview.setCoupon({text: couponTitle, date: e.target.value});
        });

        element.querySelector('.remove-coupon-btn').addEventListener('click', function() {
            element.remove();
            const couponContainer = kakaoFriendtalkTabSection.querySelector('#kakaoFriendTalkcouponTable .coupon-detail-container');
            couponContainer.parentNode.insertBefore(generateSelectCouponButton(), couponContainer);

            kakaoFriendtalkTabSection.querySelector('input[name="friendtalkCouponNo"]').value = '';
            window.preview.setCoupon(null);
        });

        return element;
    }

    function generateSelectCouponButton() {
        const button = document.createElement('button');
        button.id = 'selectCoupon';
        button.type = 'button';
        button.className = 'ncua-btn ncua-btn--xs ncua-btn--secondary-gray';
        button.textContent = '쿠폰 선택';
        button.addEventListener('click', function () {
            $.post('./layer_select_coupon.php', null, function (data) {
                ncds_layer_popup({
                    message: data,
                    title: '쿠폰 선택',
                    size: 'wide'
                });
            });
        });
        return button;
    }

    $(document).ready(function () {
        initializeKakaoFriendtalkContentSettingSectionEvents();
        initializeKakaoFriendtalkContentSettingSectionElement();
    });

    function initializeKakaoFriendtalkContentSettingSectionEvents() {
        // 메인/대체 메시지 탭 버튼
        kakaoFriendtalkTabSection.querySelectorAll('.ncua-tab-button[data-send-tab-type]').forEach(button => {
            button.addEventListener('click', function () {
                const tabType = button.dataset.sendTabType;
                if (tabType === 'ALTERNATIVE') {
                    if (!enableAlternative('FRIENDTALK')) {
                        if (messageConfig.kakaoFriendTalk.useAlternative === 'y') {
                            NCDSAlert({
                                message: '대체 메시지 미지원',
                                subMessage: '현재 선택한 메시지 유형은 대체 메시지를 제공하지 않습니다.',
                                iconType: 'error'
                            });
                            return;
                        }

                        NCDSAlert({
                            message: '대체 메시지 설정이 비활성화되어 있습니다.',
                            subMessage: 'CRM > 메시지 > 메시지 설정에서 기능을 활성화한 후 다시 시도해주세요.',
                            iconType: 'error'
                        });
                        return;
                    }
                }

                setPreviewTab(tabType);
                updateActiveTab(kakaoFriendtalkTabSection, tabType);
                updateTabComponent(kakaoFriendtalkTabSection, tabType);
                clearChipComponent();
                if (tabType === 'MAIN') {
                    updateCampaignTypeComponent(getCampaignMessageType());
                }
            });
        });

        // 친구톡 메시지 타입 라디오 버튼
        kakaoFriendtalkTabSection.querySelectorAll('input[name="campaignMessageType"]').forEach(radio => {
            radio.addEventListener('change', function () {
                clearComponent(radio.value);
                // 슬라이드 재구성 전에 레시피 캐러셀 기본값 선반영
                preloadRecipeCarouselsIfNeeded(radio.value);
                updateTabComponent(kakaoFriendtalkTabSection, 'MAIN');
                renderMessageInput(radio.value);
                updateCampaignUnitPoint(radio.value);
                updateCampaignTypeComponent(radio.value);
                updateDefaultPreview(radio.value);
                applyFriendtalkRecipeDefaults(radio.value);
            });
        });

        // 성과분석 링크 추가 버튼
        const shortLinkInput = kakaoFriendtalkTabSection.querySelector('input[data-role="short-link-input"]');
        const addShortLinkBtn = kakaoFriendtalkTabSection.querySelector('button[data-role="add-short-link"]');
        addShortLinkBtn.addEventListener('click', function() {
            const linkValue = shortLinkInput.value.replace(/^\s+|\s+$/g, '');

            // 입력값 검증
            if (!linkValue) {
                NCDSAlert({message: '링크를 입력해주세요.', iconType: 'error'});
                return;
            }

            // URL 형식 검증
            if (!isValidUrl(linkValue)) {
                NCDSAlert({
                    message: 'http:// 또는 https://로 시작하는 URL을 입력해 주세요.',
                    iconType: 'error'
                });
                return;
            }

            // 칩 버튼 생성
            addKakaoFriendtalkChipButton(linkValue);

            // 입력 필드 초기화
            shortLinkInput.value = '';

            const isImpossibleAddShortLink = document.querySelectorAll(`.chip-button[data-send-tab-type="${getActivatedSendTabType()}"]`).length > 4;
            shortLinkInput.disabled = isImpossibleAddShortLink;
            addShortLinkBtn.disabled = isImpossibleAddShortLink;
        });

        // 쿠폰 추가 버튼
        kakaoFriendtalkButtonSection.querySelector('button[data-action="add-coupon"]').addEventListener('click', function() {
            appendCouponRow();
            checkTable();
            
            this.disabled = true;
            document.querySelector('#selectCoupon').addEventListener('click', e => {
                $.post('./layer_select_coupon.php', null, function (data) {
                    ncds_layer_popup({
                        message: data,
                        title: '쿠폰 선택<div class="modal-title-description">발급방식이 수동 발급인 쿠폰 중 발급상태가 발급 중인 쿠폰만 조회됩니다.</div>',
                        size: 'wide'
                    });
                });
            })
        });

        // 링크 추가 버튼
        kakaoFriendtalkButtonSection.querySelector('button[data-action="add-link"]').addEventListener('click', function() {
            appendLinkRow();
        });

        // 아이템 리스트 추가 버튼
        kakaoFriendtalkItemListSection.querySelector('button[data-action="add-friendtalk-item"]').addEventListener('click', function() {
            appendItemListRow();
        });


        // 캐러셀 추가 버튼
        kakaoFriendtalkCarouselSection.querySelector('.js-add-carousel-btn').addEventListener('click', function() {
            carouselState.addCarousel();
        });

        kakaoFriendtalkTabSection.querySelector('input[name="use080Reject"]').addEventListener('click', function(e) {
            if (this.dataset.available !== 'y') {
                e.preventDefault();
                return;
            }

            if (this.checked) {
                addAdText(window.alternativeMessageInput, window.alternativePreview);
            } else {
                removeAdText(window.alternativeMessageInput, window.alternativePreview);
            }
        });

        kakaoFriendtalkTabSection.querySelector('input[name="friendtalkCampaignHeader"]').addEventListener('input', function(e) {
            window.preview.setTitle(resolveReplaceCode(e.target.value));
            if (e.target.value === '') {
                setDefaultPreview(['FRIENDTALK_TITLE']);
            }
            <?php if ($isRecipeContext): ?>
            kakaoFriendtalkTabSection.querySelector('.js-wide-item-point-hint')?.classList.toggle('display-none', e.target.value === '');
            <?php endif; ?>
        });

        kakaoFriendtalkTabSection.querySelector('input[name="friendtalkCampaignTitle"]').addEventListener('input', function(e) {
            window.preview.setContentTitle(resolveReplaceCode(e.target.value));
            if (e.target.value === '') {
                setDefaultPreview(['FRIENDTALK_CAROUSEL_TITLE']);
            }
        });

        kakaoFriendtalkTabSection.querySelector('button.load-main-contents').addEventListener('click', function() {
            const message = messageInput.getValue();
            const allKeys = [
                MobileMessageReplaceCode.CRM_RECIPE,
                MobileMessageReplaceCode.MEMBER,
                MobileMessageReplaceCode.GOODS,
                MobileMessageReplaceCode.ORDER,
                MobileMessageReplaceCode.PROMOTION,
                MobileMessageReplaceCode.BOARD,
                MobileMessageReplaceCode.REGULAR,
                MobileMessageReplaceCode.PRESENT,
            ].flatMap(category => Object.keys(category));

            const convertedMessage = message.replace(/#\{([a-zA-Z_0-9]+)}/g, (match, key) => {
                return allKeys.includes(MobileMessageReplaceCode.normalizeReplaceKey(key)) ? `{${key}}` : '';
            });

            alternativeMessageInput.setValue('');
            alternativeMessageInput.insertText(convertedMessage);
            alternativePreview.setContent(resolveReplaceCode(convertedMessage, false));
            NCDSToast({message: '메시지가 최신 내용으로 업데이트 됐습니다.', color: 'success'});
        });

        // 미리보기 슬라이더 직접 스와이프 시 carouselState 동기화
        document.querySelector('#previewContainer').addEventListener('previewSlideChange', function(e) {
            const index = e.detail.index;
            if (carouselState.activeIndex === index) return;
            carouselState.saveCurrentCarouselData();
            carouselState.activeIndex = index;
            carouselState.renderTabs();
            carouselState.renderCurrentCarouselForm();
            updateDefaultPreview('CAROUSEL_FEED');
        });
    }

    function renderMessageInput(campaignMessageType) {
        window.messageInput.clear();
        window.messageInput.setHint(null);
        window.preview.reset();
        window.preview.setMessageType(campaignMessageType)

        switch (campaignMessageType) {
            case 'TEXT':
                window.messageInput.setMaxLength(1300);
                break;
            case 'IMAGE':
                window.preview.setImage('');
                window.preview.setButtons([{text: '버튼'}]);
                window.messageInput.setMaxLength(400);
                break;
            case 'WIDE_IMAGE':
                window.preview.setImage('');
                window.messageInput.setMaxLength(76);
                break;
            case 'CAROUSEL_FEED':
                window.messageInput.setMaxLength(180);
                const count = window.preview.getSlideCount();
                for (let i = 0; i < count; i++) {
                    window.preview.removeSlide();
                }

                carouselState.carousels.forEach(_ => {
                    window.preview.addSlide();
                });
                break;
        }

    }

    function updateCampaignUnitPoint(campaignType) {
        const expectTargetCount = getExpectTargetCount();
        const unitPoint = parseFloat(campaignTypeUnitPoint[campaignType]).toFixed(1);
        const campaignTypeLabel = kakaoFriendtalkTabSection.querySelector(`input[name="campaignMessageType"][value="${campaignType}"]`)
            .closest('.ncua-radio-field')
            .querySelector('.ncua-radio-field__text').textContent;

        kakaoFriendtalkTabSection.querySelector('#campaignUnitPoint').innerHTML = `${campaignTypeLabel} (건당 <strong>${unitPoint}</strong> 포인트)`;
        kakaoFriendtalkTabSection.querySelector('#expectMessagePoint').innerHTML = (Math.round(campaignTypeUnitPoint[campaignType] * expectTargetCount * 10) / 10).toFixed(1);
    }

    function clearChipComponent() {
        // 성과분석 숏링크 초기화
        kakaoFriendtalkTabSection.querySelector('input[name="shortLink"]').value = '';
        kakaoFriendtalkTabSection.querySelector('[data-role="chip-container"]').innerHTML = '';

        const activeSendTabType = document.querySelector('button[data-send-tab-type].is-active')?.dataset.sendTabType;

        const isMain = activeSendTabType === 'MAIN';
        const shortLinkKeys = isMain ? window.shortLinkKeys : window.shortLinkAlternativeKeys;
        if (isMain) {
            window.shortLinkKeys = new Map();
        } else {
            window.shortLinkAlternativeKeys = new Map();
        }
        shortLinkKeys.forEach((linkValue, linkKey) => {
            addKakaoFriendtalkChipButton(linkValue.replace(/^\s+|\s+$/g, ''), linkKey);
        });
    }

    function clearComponent(campaignMessageType) {
        // 타이틀 초기화 (캠페인 명은 캠페인 레벨 값이라 타입 전환 시 유지)
        kakaoFriendtalkTabSection.querySelector('input[name="friendtalkCampaignHeader"]').value = '';
        kakaoFriendtalkTabSection.querySelector('input[name="friendtalkCampaignTitle"]').value = '';
        // 캠페인 이미지 초기화
        kakaoFriendtalkTabSection.querySelector('input[name="friendtalkCampaignImageUrl"]').value = '';
        kakaoFriendtalkTabSection.querySelector('input[name="friendtalkCampaignImageLink"]').value = '';
        campaignImageFileInput?.clearFiles();

        // 아이템 리스트 초기화
        kakaoFriendtalkItemListSection.querySelector('tbody').innerHTML = '';
        itemImageFileInputs.clear();

        // 성과분석 숏링크 초기화
        kakaoFriendtalkTabSection.querySelector('input[name="shortLink"]').value = '';
        kakaoFriendtalkTabSection.querySelector('[data-role="chip-container"]').innerHTML = '';

        // 쿠폰, 링크 테이블 초기화
        kakaoFriendtalkButtonSection.querySelectorAll('.ncua-table').forEach(t => t.remove());
        kakaoFriendtalkButtonSection.querySelector('.empty-coupon-data').classList.remove('display-none');

        window.preview.reset();
        checkTable();

        // 아이템 리스트 테이블 row 수 초기화
        if (campaignMessageType === 'WIDE_ITEM_LIST') {
            appendItemListRow(true);
        }

        // 캐러셀 초기화
        carouselState.init();
    }

    function updateItemListPreview() {
        const itemListTableBody = kakaoFriendtalkItemListSection.querySelector('tbody');

        let itemListDatas = [];
        itemListTableBody.querySelectorAll('tr').forEach((row, idx) => {
            const title = row.querySelector('input[name="itemTitle"]').value;
            let imageUrl = row.querySelector('input[name="itemImageUrl"]')?.value ?? '';
            // 첫 아이템은 이미지가 비면 기본 placeholder 표시
            if (idx === 0 && !imageUrl) {
                imageUrl = '<?= PATH_ADMIN_GD_SHARE ?>ncds/image/temp-preview02.png';
            }
            itemListDatas.push({title: title, desc: '', image: imageUrl});
        });

        window.preview.setItemList(itemListDatas);
    }

    function appendItemListRow(isFirst = false) {
        const itemListTableBody = kakaoFriendtalkItemListSection.querySelector('tbody');
        if (itemListTableBody.querySelectorAll('tr').length >= 4) {
            NCDSAlert({ message: `아이템은 최대 4개까지 추가할 수 있습니다.`, iconType: 'error' });
            return;
        }

        const template = getItemRowTemplate(isFirst);
        itemListTableBody.insertAdjacentHTML('beforeend', template);

        const charCountManager = createCharCountManager({targetClasses: ['ncua-item-button-name']});
        charCountManager.init();

        const newRow = itemListTableBody.lastElementChild;
        const itemKey = newRow.dataset.itemKey;

        updateItemListPreview();

        // 텍스트 변경 이벤트
        newRow.querySelector('input[name="itemTitle"]').addEventListener('input', function() {
            updateItemListPreview();
        })

        // 삭제 버튼 이벤트
        newRow.querySelector('[data-action="remove-friendtalk-item"]')?.addEventListener('click', function() {
            if (itemListTableBody.querySelectorAll('tr').length > 1) {
                newRow.remove();
                itemImageFileInputs.delete(itemKey);
                updateItemListPreview();
            }
        });

        itemImageFileInputs.set(itemKey, initItemImageFileInput(`campaign-item-image-file-input-container-${itemKey}`, itemKey, isFirst));
    }

    function appendCouponRow() {
        const emptyDataElement = kakaoFriendtalkButtonSection.querySelector('.empty-coupon-data');
        emptyDataElement.insertAdjacentHTML('beforebegin', getCouponTableTemplate());
        const insertedElement = emptyDataElement.previousElementSibling;

        // 테이블이 추가되면 empty 메시지 숨기기
        const tables = kakaoFriendtalkButtonSection.querySelectorAll('.ncua-table');
        if (tables.length > 0) {
            emptyDataElement.classList.add('display-none');
        }

        // 새로 추가된 삭제 버튼에 이벤트 리스너 추가
        insertedElement.querySelector('.minus-btn').addEventListener('click', function() {
            insertedElement.remove();
            checkTable();

            window.preview.setCoupon(null)

            // 모든 테이블이 삭제되면 empty 메시지 표시
            const remainingTables = kakaoFriendtalkButtonSection.querySelectorAll('.ncua-table');
            if (remainingTables.length === 0) {
                emptyDataElement.classList.remove('display-none');
            }
        });
    }
    
    function appendLinkRow() {
        const emptyDataElement = kakaoFriendtalkButtonSection.querySelector('.empty-coupon-data');
        if (!kakaoFriendtalkButtonSection.querySelector('#linkButtonTable')) {
            emptyDataElement.insertAdjacentHTML('beforebegin', getLinkTableTemplate());
        }
        const linkButtonTableBody = kakaoFriendtalkButtonSection.querySelector('#linkButtonTableBody');
        linkButtonTableBody.insertAdjacentHTML('beforeend', getLinkTableRowTemplate());
        const insertedElement = linkButtonTableBody.lastElementChild;
        insertedElement.querySelector('.ncua-link-button-name').addEventListener('input', function() {
            updateLinkPreview();
        })

        checkTable();
        updateLinkPreview();
        if (cosData && window.GodoCosGuide) {
                window.GodoCosGuide.apply(cosData);
            }
        // 테이블이 추가되면 empty 메시지 숨기기
        const tables = kakaoFriendtalkButtonSection.querySelectorAll('.ncua-table');
        if (tables.length > 0) {
            emptyDataElement.classList.add('display-none');
        }

        // 새로 추가된 삭제 버튼에 이벤트 리스너 추가
        insertedElement.querySelector('.minus-btn').addEventListener('click', function() {
            const linkTable = kakaoFriendtalkButtonSection.querySelector('.add-link-button-table');
            const linkCount = linkTable?.querySelectorAll('#linkButtonTableBody tr')?.length ?? 0;
            if (getCampaignMessageType() === 'CAROUSEL_FEED' && linkCount <= 1) {
                return;
            }

            if (linkCount === 1) {
                linkTable.remove();
            } else {
                insertedElement.remove();
            }

            checkTable();
            updateLinkPreview();

            // 모든 테이블이 삭제되면 empty 메시지 표시
            const remainingTables = kakaoFriendtalkButtonSection.querySelectorAll('.ncua-table');
            if (remainingTables.length === 0) {
                emptyDataElement.classList.remove('display-none');
            }
        });
    }

    function updateLinkPreview() {
        if (!kakaoFriendtalkButtonSection.querySelector('#linkButtonTable')) {
            window.preview.setButtons(null);
            return;
        }

        const linkButtonTableBody = kakaoFriendtalkButtonSection.querySelector('#linkButtonTableBody');
        let buttonDatas = [];
        linkButtonTableBody.querySelectorAll('tr').forEach(row => {
            const buttonName = row.querySelector('.ncua-link-button-name').value;
            buttonDatas.push({text: buttonName, url: '', type: 'WL'});
        });

        window.preview.setButtons(buttonDatas);
    }

    function checkTable() {
        const linkTable = document.querySelector('.add-link-button-table');
        const couponTable = document.querySelector('.add-coupon-button-table');

        const couponCount = couponTable?.querySelectorAll('tbody > tr')?.length ?? 0;
        const linkCount = linkTable?.querySelectorAll('#linkButtonTableBody tr')?.length ?? 0;

        const campaignMessageType = getCampaignMessageType();

        const maxTotal = ['TEXT', 'IMAGE'].includes(campaignMessageType) ? 5 : 3;
        const maxLink = ['TEXT', 'IMAGE'].includes(campaignMessageType) ? 5 : 2;
        kakaoFriendtalkButtonSection.querySelector('button[data-action="add-coupon"]').disabled = couponCount >= 1 || couponCount + linkCount >= maxTotal;
        kakaoFriendtalkButtonSection.querySelector('button[data-action="add-link"]').disabled = linkCount >= maxLink || couponCount + linkCount >= maxTotal;
    }

    function updateCampaignTypeComponent(campaignType) {
        kakaoFriendtalkTabSection.querySelectorAll('.campaign-type-component').forEach(el => {
            el.classList.add('display-none');
            const targets = el.dataset.campaignType?.split(',') ?? [];
            if (targets.includes(campaignType)) {
                el.classList.remove('display-none');
            }
        });
    }

    function updateDefaultPreview(campaignType) {
        switch (campaignType) {
            case 'TEXT':
                setDefaultPreview(['TEXT']);
                break;
            case 'IMAGE':
                setDefaultPreview(['TEXT', 'FRIENDTALK_IMAGE']);
                break;
            case 'WIDE_IMAGE':
                setDefaultPreview(['TEXT', 'FRIENDTALK_IMAGE']);
                break;
            case 'WIDE_ITEM_LIST':
                window.preview.setContent(null);
                setDefaultPreview(['FRIENDTALK_TITLE', 'FRIENDTALK_WIDE_ITEM_LIST']);
                break;
            case 'CAROUSEL_FEED':
                const { carousels, activeIndex } = window.carouselState;
                carousels.forEach((carousel, index) => {
                    if (carousel.isDeleted) return;
                    window.preview.setSlideIndex(index);
                    if (!carousel.header) setDefaultPreview(['FRIENDTALK_CAROUSEL_TITLE']);
                    if (!carousel.message) setDefaultPreview(['TEXT']);
                    if (!carousel.imageUrl) setDefaultPreview(['FRIENDTALK_IMAGE']);
                });
                window.preview.setSlideIndex(activeIndex);
                break;
        }
    }

    // 칩 버튼 생성 함수
    function addKakaoFriendtalkChipButton(linkValue, linkKey = null) {
        if (!linkKey) linkKey = generateLinkKey();
        const chipButton = generateChipButton(linkKey, linkValue);
        const shortLinkInput = kakaoFriendtalkTabSection.querySelector('input[data-role="short-link-input"]');
        const addShortLinkBtn = kakaoFriendtalkTabSection.querySelector('button[data-role="add-short-link"]');

        chipButton.addEventListener('click', function() {
            const replaceCode = chipButton.querySelector('.chip-button-text');
            if (document.querySelector('button[data-send-tab-type].is-active')?.dataset.sendTabType === 'MAIN') {
                window.messageInput.insertText(replaceCode.textContent);
            } else {
                window.alternativeMessageInput.insertText(replaceCode.textContent);
            }
        });

        // 닫기 버튼 이벤트
        const removeBtn = chipButton.querySelector('[data-role="remove-chip"]');
        removeBtn.addEventListener('click', function(e) {
            e.stopPropagation(); // 부모 버튼 클릭 이벤트 방지

            // chip-button-text 값을 textContent에서 모두 삭제
            const chipText = chipButton.querySelector('.chip-button-text')?.textContent;
            if (chipText) {
                const escapedText = chipText.replace(/[.*+?^${}()|[\]\\]/g, '\\$&');
                if (document.querySelector('button[data-send-tab-type].is-active')?.dataset.sendTabType === 'MAIN') {
                    const replacedContent = messageInput.getValue().replace(new RegExp(escapedText, 'g'), '')
                    window.messageInput.setValue(replacedContent);
                    window.preview.setContent(replacedContent);
                    if (replacedContent === '') {
                        setDefaultPreview(['TEXT']);
                    }
                } else {
                    const replacedContent = alternativeMessageInput.getValue().replace(new RegExp(escapedText, 'g'), '')
                    window.alternativeMessageInput.setValue(replacedContent);
                    window.alternativePreview.setContent(replacedContent);
                    if (replacedContent === '') {
                        setDefaultPreview(['ALTERNATIVE_TEXT']);
                    }
                }
            }

            chipButton.remove();

            const isImpossibleAddShortLink = document.querySelectorAll(`.chip-button[data-send-tab-type="${getActivatedSendTabType()}"]`).length > 4;
            shortLinkInput.disabled = isImpossibleAddShortLink;
            addShortLinkBtn.disabled = isImpossibleAddShortLink;

            delShortLinkKey(linkKey);
        });
        chipContainer.appendChild(chipButton);
        setShortLinkKey(linkKey, linkValue);
    }

    // 쿠폰 추가 버튼 템플릿
    function getCouponTableTemplate() {
        return `
                <div class="ncua-table ncua-table--horizontal add-coupon-button-table" >
                    <table id="kakaoFriendTalkcouponTable">
                        <colgroup>
                            ${isRecipeContext ? '' : '<col width="80px"/>'}
                            <col/>
                            <col width="88px"/>
                        </colgroup>
                        <thead>
                            <tr>
                                ${isRecipeContext ? '' : '<th><div class="ncua-align-center">타입</div></th>'}
                                <th class="ncua-required"><div>쿠폰 선택</div></th>
                                <th><div class="ncua-align-center">삭제</div></th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                ${isRecipeContext ? '' : `<td>
                                    <div class="ncua-align-center">쿠폰</div>
                                </td>`}
                                <td>
                                    <div class="ncua-flex-column ncua-gap-8">
                                        <button id="selectCoupon" type="button" class="ncua-btn ncua-btn--xs ncua-btn--secondary-gray">쿠폰 선택</button>
                                        <div class="coupon-detail-container"></div>
                                    </div>
                                </td>
                                <td>
                                    <div class="ncua-align-center">
                                        <button type="button" class="ncua-btn ncua-btn--xs ncua-btn--secondary-gray minus-btn">삭제</button>
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                `;
    }

    // 링크 테이블 전체 템플릿
    function getLinkTableTemplate() {
        return `
                <div class="ncua-table ncua-table--horizontal add-link-button-table">
                    <table id="linkButtonTable">
                        <colgroup>
                            ${isRecipeContext ? '' : '<col width="80px"/>'}
                            <col/>
                            <col/>
                            <col width="88px"/>
                        </colgroup>
                        <thead>
                            <tr>
                                ${isRecipeContext ? '' : '<th><div class="ncua-align-center">타입</div></th>'}
                                <th class="ncua-required"><div>버튼명</div></th>
                                ${isAppLink() ? `<th class="ncua-required"><div>APP 링크</div></th>` : `<th class="ncua-required"><div data-tooltip-seq="008">WEB 링크</div></th>`}
                                <th><div class="ncua-align-center">삭제</div></th>
                            </tr>
                        </thead>
                        <tbody id="linkButtonTableBody">

                        </tbody>
                    </table>
                </div>
            `;
    }

    // 링크 테이블 행 템플릿 (tbody 내 tr만)
    function getLinkTableRowTemplate() {
        const campaignMessageType = getCampaignMessageType();
        const uniqueKey = `link-button-name-${Date.now()}`;
        return `
                <tr>
                    ${isRecipeContext ? '' : '<td><div class="ncua-align-center">기본</div></td>'}
                    <td>
                        <div>
                            <div class="ncua-input ncua-input--xs ncua-input-full-width button-name-input">
                                <div class="ncua-input__content-wrap">
                                    <div class="ncua-input__content">
                                        <div class="ncua-input__field ncua-input__field--xs ncua-input-full-width">
                                            <input class="ncua-link-button-name" type="text" data-charcount-key="${uniqueKey}" maxlength="${['TEXT', 'IMAGE'].includes(campaignMessageType) ? 14 : 8}" placeholder="버튼명을 입력해주세요." />
                                        </div>
                                    </div>
                                    <div class="ncua-input__field-text-count" data-charcount-text="${uniqueKey}">
                                        <output class="ncua-input__field-text-count-current">0</output>
                                        <span>/${['TEXT', 'IMAGE'].includes(campaignMessageType) ? 14 : 8}</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </td>
                    ${getLinkColumnBody()}
                    <td>
                        <div class="ncua-align-center">
                            <button type="button" class="ncua-btn ncua-btn--xs ncua-btn--secondary-gray minus-btn">삭제</button>
                        </div>
                    </td>
                </tr>`;
    }

    // 링크 컬럼 바디 템플릿
    function getLinkColumnBody() {
        return isAppLink()
            ? `<td>
                    <div class="ncua-flex-column ncua-gap-8 link-column-button-wrap">
                        <div class="ncua-input ncua-input--xs ncua-input-full-width">
                            <div class="ncua-input__content-wrap">
                                <div class="ncua-input__label ncua-input__label--xs">
                                    <label class="ncua-label">iOS 링크</label>
                                </div>
                                <div class="ncua-input__content">
                                    <div class="ncua-input__field ncua-input__field--xs">
                                        <input name="linkButtonIOSUrl" type="text" value="" maxlength="800" placeholder="${isRecipeContext ? '' : 'https://commerce.com/page/event01'}" />
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="ncua-input ncua-input--xs ncua-input-full-width">
                            <div class="ncua-input__content-wrap">
                                <div class="ncua-input__label ncua-input__label--xs">
                                    <label class="ncua-label">AOS 링크</label>
                                </div>
                                <div class="ncua-input__content">
                                    <div class="ncua-input__field ncua-input__field--xs">
                                        <input name="linkButtonAOSUrl" type="text" value="" maxlength="800" placeholder="${isRecipeContext ? '' : 'https://commerce.com/page/event01'}" />
                                    </div>
                                </div>
                            </div>
                            ${isRecipeContext ? '' : '<span class="ncua-hint-text ncua-input__hint-text">외부 링크 입력 시 캠페인 통계가 집계되지 않습니다.</span>'}
                        </div>
                    </div>
                </td>`
            : `<td>
                    <div>
                        <div class="ncua-input ncua-input--xs ncua-input-full-width">
                            <div class="ncua-input__content">
                                <div class="ncua-input__field ncua-input__field--xs">
                                    <input name="linkButtonWebUrl" type="text" value="" maxlength="800" placeholder="" />
                                </div>
                            </div>
                            ${isRecipeContext ? '' : '<span class="ncua-hint-text ncua-input__hint-text">외부 링크 입력 시 캠페인 통계가 집계되지 않습니다.</span>'}
                        </div>
                    </div>
                </td>`;
    }

    function isAppLink() {
        return linkPlatformType === 'MOBILE_APP';
    }

    function getItemRowTemplate(isFirst = true) {
        const uniqueKey = `${Date.now()}_${itemRowKeySeq++}`;

        return `
                <tr data-item-key="${uniqueKey}">
                    <td>
                        <div class="ncua-align-center">
                            <input type="hidden" name="itemImageUrl" value="" />
                            <input hidden name="campaignItemImage" class="no-filestyle" id="campaignItemImageInput-${uniqueKey}" tabindex="-1" aria-hidden="true" type="file" />
                            <div id="campaign-item-image-file-input-container-${uniqueKey}" class="campaign-item-image-file-input-container"></div>
                        </div>
                    </td>
                    <td>
                        <div>
                            <div class="ncua-input ncua-input--xs ncua-input-full-width">
                                <div class="ncua-input__content-wrap">
                                    <div class="ncua-input__content">
                                        <div class="ncua-input__field ncua-input__field--xs">
                                            <input class="ncua-item-button-name" data-charcount-key="item-button-name-${uniqueKey}" maxlength="${isFirst ? 25 : 30}" name="itemTitle" type="text" value="" placeholder="내용을 입력해주세요." />
                                        </div>
                                    </div>
                                    <div class="ncua-input__field-text-count" data-charcount-text="item-button-name-${uniqueKey}">
                                        <output class="ncua-input__field-text-count-current">0</output>
                                        <span>/${isFirst ? 25 : 30}</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </td>
                    <td>
                        <div>
                            <div class="ncua-input ncua-input--xs ncua-input-full-width">
                                <div class="ncua-input__content">
                                    <div class="ncua-input__field ncua-input__field--xs">
                                        <input name="itemWebLink" type="text" value="" placeholder="" />
                                    </div>
                                </div>
                                ${isRecipeContext ? '' : '<span class="ncua-hint-text ncua-input__hint-text">외부 링크 입력 시 캠페인 통계가 집계되지 않습니다.</span>'}
                            </div>
                        </div>
                    </td>
                    <td>
                        ${!isFirst ? `
                            <div class="ncua-align-center">
                                <button type="button" class="ncua-btn ncua-btn--xs ncua-btn--secondary-gray minus-btn" data-action="remove-friendtalk-item">삭제</button>
                            </div>
                        ` : ''}
                    </td>
                </tr>
            `;
    }

    function initializeKakaoFriendtalkContentSettingSectionElement() {
        // 초기 메시지 타입 선택
        kakaoFriendtalkTabSection.querySelector('button[data-send-tab-type="MAIN"]')?.dispatchEvent(new Event('click'));
        // 초기 캠페인 타입 선택
        kakaoFriendtalkTabSection.querySelector('input[name="campaignMessageType"]:checked')?.dispatchEvent(new Event('change'));

        // 캠페인 이미지 초기화
        createCampaignImageFileInput();

        // textarea 카운팅
        const charCountManager = createCharCountManager({
            targetClasses: ['ncua-campaign-header', 'ncua-campaign-title', 'ncua-recruit-cancel-method', 'ncua-link-button-name', 'ncua-coupon-description'],
        });
        charCountManager.init();

        // 캐러셀 피드 초기화
        carouselState.init();

        const initialTextContent = <?= json_encode($initialTextContent ?? '', JSON_UNESCAPED_UNICODE | JSON_HEX_TAG | JSON_HEX_AMP) ?>;
        const initialAlternativeMessage = <?= json_encode($initialAlternativeMessage ?? '', JSON_UNESCAPED_UNICODE | JSON_HEX_TAG | JSON_HEX_AMP) ?>;

        if (isRecipeContext) {
            const wideItemPointHint = kakaoFriendtalkTabSection.querySelector('.js-wide-item-point-hint');
            if (wideItemPointHint) wideItemPointHint.textContent = friendtalkUnitPointHint('WIDE_ITEM_LIST');
        }

        // 본문 메시지
        window.messageInput = GodoUIModule.render({
            type: 'MessageInput',
            target: '#messageContentContainer',
            name: 'messageContent',
            title: '내용 입력',
            maxLength: 1300,
            useBytes: false,
            initialValue: initialTextContent,
            onInput: function(input) {
                window.preview.setContent(resolveReplaceCode(input));
                MobileMessageReplaceCode.updateExpireCodeGuide('#replaceCodeContainer', input);
                if (input === '') {
                    window.messageInput.setHint(null);
                    setDefaultPreview(['TEXT']);
                } else if (isRecipeContext) {
                    window.messageInput.setHint(friendtalkUnitPointHint(getCampaignMessageType()));
                }
            }
        });

        // 대체 메시지
        window.alternativeMessageInput = GodoUIModule.render({
            type: 'MessageInput',
            target: '#messageContentAlternativeContainer',
            name: 'messageAlternativeContent',
            title: '내용 입력',
            maxLength: 2000,
            useBytes: true,
            allowEmoji: false,
            initialValue: initialAlternativeMessage,
            onInput: function(input) {
                window.alternativePreview.setContent(resolveReplaceCode(input, false));
                MobileMessageReplaceCode.updateExpireCodeGuide('#replaceCodeAlternativeContainer', input);
                if (input === '') {
                    window.alternativeMessageInput.setHint(null);
                    setDefaultPreview(['ALTERNATIVE_TEXT']);
                } else {
                    window.alternativeMessageInput.setHint(window.alternativeMessageInput.calculateLength(input) > 90 ? 'LMS 건당 3포인트 차감' : 'SMS 건당 1포인트 차감');
                }
            }
        });

        window.chipSelector = GodoUIModule.render({
            type: 'ChipSelector',
            target: '#replaceCodeContainer',
            title: isRecipeContext ? '추천 변수' : '사용 가능한 변수',
            tooltipSeq: '003',
            hideCategorySelector: isRecipeContext,
            categories: isRecipeContext ? buildRecipeReplaceCodeCategories('#') : buildReplaceCodeCategories('#'),
            onSelect: (variable) => {
                const resolved = MobileMessageReplaceCode.resolveRecipeExpireInsertKey(variable, isRecipeContext);
                if (getCampaignMessageType() === 'WIDE_ITEM_LIST') {
                    const headerInput = kakaoFriendtalkTabSection.querySelector('input[name="friendtalkCampaignHeader"]');
                    headerInput.value = headerInput.value + resolved;
                    headerInput.dispatchEvent(new Event('input', { bubbles: true }));
                } else {
                    window.messageInput.insertText(resolved);
                }
            },
            onCategoryChange: (category) => {
                if (category === 'MEMBER') {
                    window.chipSelector.setCautionHTML(null);
                } else {
                    window.chipSelector.setCautionHTML('<ul class="replace-code-caution"><li class="ncua-caution-text">회원 외 치환코드는 정상 적용되지 않으니 저장된 메시지 수정 시에만 이용바랍니다.</li></ul>');
                }
            }
        });

        window.alternativeChipSelector = GodoUIModule.render({
            type: 'ChipSelector',
            target: '#replaceCodeAlternativeContainer',
            title: isRecipeContext ? '추천 변수' : '사용 가능한 변수',
            tooltipSeq: '003',
            hideCategorySelector: isRecipeContext,
            categories: isRecipeContext ? buildRecipeReplaceCodeCategories() : buildReplaceCodeCategories(),
            onSelect: (variable) => {
                window.alternativeMessageInput.insertText(MobileMessageReplaceCode.resolveRecipeExpireInsertKey(variable, isRecipeContext));
            },
            onCategoryChange: (category) => {
                if (category === 'MEMBER') {
                    window.alternativeChipSelector.setCautionHTML(null);
                } else {
                    window.alternativeChipSelector.setCautionHTML('<ul class="replace-code-caution"><li class="ncua-caution-text">회원 외 치환코드는 정상 적용되지 않으니 저장된 메시지 수정 시에만 이용바랍니다.</li></ul>');
                }
            }
        });

        // 미리보기 랜더링
        document.querySelector('.js-preview-tab-section').classList.remove('display-none');
        window.preview = GodoUIModule.render({
            type: 'MessagePreview',
            target: '#previewContainer',
            sendType: 'FRIENDTALK',
            messageType: 'TEXT'
        });

        window.preview.onVariableCheckboxChange(() => {
            switch (getCampaignMessageType()) {
                case 'WIDE_ITEM_LIST':
                    window.preview.setTitle(resolveReplaceCode(kakaoFriendtalkTabSection.querySelector('input[name="friendtalkCampaignHeader"]').value));
                    break;
                case 'CAROUSEL_FEED':
                    carouselState.saveCurrentCarouselData();
                    carouselState.carousels.forEach((carousel, index) => {
                        if (carousel.isDeleted) return;
                        window.preview.setSlideIndex(index);
                        window.preview.setTitle(resolveReplaceCode(carousel.header));
                        window.preview.setContent(resolveReplaceCode(carousel.message));

                        if (carousel.header === '') {
                            setDefaultPreview(['FRIENDTALK_CAROUSEL_TITLE']);
                        }

                        if (carousel.message === '') {
                            setDefaultPreview(['TEXT']);
                        }
                    });
                    window.preview.setSlideIndex(carouselState.activeIndex);
                    break;
                default:
                    window.preview.setContent(resolveReplaceCode(window.messageInput.getValue()));
                    break;
            }
        });

        window.alternativePreview = GodoUIModule.render({
            type: 'MessagePreview',
            target: '#previewAlternativeContainer',
            sendType: 'SMS'
        });

        window.alternativePreview.onVariableCheckboxChange(() => {
            window.alternativePreview.setContent(resolveReplaceCode(window.alternativeMessageInput.getValue(), false));
        });

        setDefaultPreview(['ALTERNATIVE_TEXT', 'TEXT']);

        if (initialAlternativeMessage) {
            window.alternativePreview.setContent(resolveReplaceCode(initialAlternativeMessage, false));
            MobileMessageReplaceCode.updateExpireCodeGuide('#replaceCodeAlternativeContainer', initialAlternativeMessage);
        }

        applyFriendtalkRecipeDefaults(getCampaignMessageType());
    }

    function applyFriendtalkRecipeDefaults(campaignMessageType) {
        if (!friendtalkRequest || Object.keys(friendtalkRequest).length === 0) return;
        if (!window.messageInput) return;

        const typeKeyMap = {
            TEXT: 'text',
            IMAGE: 'image',
            WIDE_IMAGE: 'wideImage',
            WIDE_ITEM_LIST: 'wideItemList',
            CAROUSEL_FEED: 'carousel',
        };
        const data = friendtalkRequest[typeKeyMap[campaignMessageType]];
        if (!data) return;

        if (data.content) {
            window.messageInput.setValue('');
            window.messageInput.insertText(data.content);
        }

        if (data.header) {
            const headerInput = kakaoFriendtalkTabSection.querySelector('input[name="friendtalkCampaignHeader"]');
            if (headerInput) {
                headerInput.value = data.header;
                headerInput.dispatchEvent(new Event('input', { bubbles: true }));
            }
        }

        if (Array.isArray(data.buttons) && data.buttons.length > 0) {
            applyFriendtalkRecipeButtons(data.buttons);
        }

        if (Array.isArray(data.items) && data.items.length > 0) {
            applyFriendtalkRecipeItems(data.items);
        }

        if (Array.isArray(data.carousels) && data.carousels.length > 0) {
            applyFriendtalkRecipeCarousels(data.carousels);
        }
    }

    // 레시피 기본 캐러셀 응답 → carouselState.carousels 형태로 변환
    function mapRecipeCarousels(carousels) {
        if (!Array.isArray(carousels)) {
            carousels = [];
        }
        const mapped = carousels.map(function (carousel) {
            const base = carouselState.createEmptyCarousel();
            const attachment = carousel.attachment || {};
            const image = attachment.image || {};
            const buttons = Array.isArray(attachment.buttons) && attachment.buttons.length > 0
                ? attachment.buttons.map(function (btn) {
                    // url 은 nullable 이라 객체로 보정
                    let url = btn?.url;
                    if (!url || typeof url !== 'object') {
                        url = { mobileUrl: '', iosUrl: '', aosUrl: '' };
                    }
                    return {
                        name: btn?.name ?? '',
                        url: url
                    };
                })
                : base.buttons;

            return {
                ...base,
                header: carousel.header ?? '',
                message: carousel.message ?? '',
                imageUrl: image.imageUrl ?? '',
                imageLink: image.imageLink ?? '',
                buttons: buttons,
                // 응답 쿠폰엔 표시 메타가 없어 운영자 선택으로 남김
                coupon: null
            };
        });

        // 최소 캐러셀 수 보장
        while (mapped.length < carouselState.MIN_CAROUSELS) {
            mapped.push(carouselState.createEmptyCarousel());
        }
        return mapped;
    }

    // 슬라이드 재구성보다 먼저 carouselState 를 채워 빈 데이터로 덮어쓰는 경합 차단
    function preloadRecipeCarouselsIfNeeded(campaignMessageType) {
        if (campaignMessageType !== 'CAROUSEL_FEED' || !window.carouselState) return;
        const carousels = friendtalkRequest && friendtalkRequest.carousel && friendtalkRequest.carousel.carousels;
        if (!Array.isArray(carousels) || carousels.length === 0) return;
        carouselState.carousels = mapRecipeCarousels(carousels);
        carouselState.activeIndex = 0;
    }

    function applyFriendtalkRecipeCarousels(carousels) {
        if (!window.carouselState) return;
        carouselState.carousels = mapRecipeCarousels(carousels);
        carouselState.activeIndex = 0;
        carouselState.renderTabs();
        carouselState.renderCurrentCarouselForm();

        // 미리보기는 input 이벤트로만 동기화되므로 레시피 기본값은 모든 슬라이드에 직접 push 한다
        carouselState.carousels.forEach(function (carousel, index) {
            if (carousel.isDeleted) return;
            window.preview.setSlideIndex(index);

            // 제목·본문은 치환코드 치환 후 반영, 비면 기본 placeholder
            if (carousel.header) {
                window.preview.setContentTitle(resolveReplaceCode(carousel.header));
            } else {
                setDefaultPreview(['FRIENDTALK_CAROUSEL_TITLE']);
            }

            if (carousel.message) {
                window.preview.setContent(resolveReplaceCode(carousel.message));
            } else {
                setDefaultPreview(['TEXT']);
            }

            // 이미지가 없으면 기본 placeholder 표시
            if (carousel.imageUrl) {
                window.preview.setImage(carousel.imageUrl);
            } else {
                setDefaultPreview(['FRIENDTALK_IMAGE']);
            }

            const previewButtons = (carousel.buttons || [])
                .filter(function (button) { return button && button.name; })
                .map(function (button) { return { text: button.name }; });
            if (previewButtons.length > 0) {
                window.preview.setButtons(previewButtons);
            }
        });
        window.preview.setSlideIndex(carouselState.activeIndex);
    }

    function applyFriendtalkRecipeButtons(buttons) {
        const isMobileApp = linkPlatformType === 'MOBILE_APP';

        buttons.forEach(function (btn) {
            appendLinkRow();
            const row = kakaoFriendtalkButtonSection.querySelector('#linkButtonTableBody tr:last-child');
            if (!row) return;

            const nameInput = row.querySelector('.ncua-link-button-name');
            if (nameInput && btn?.name) {
                nameInput.value = btn.name;
                nameInput.dispatchEvent(new Event('input', { bubbles: true }));
            }

            const url = btn?.url;
            if (!url || typeof url !== 'object') return;

            const webUrlInput = row.querySelector('input[name="linkButtonWebUrl"]');
            const iosUrlInput = row.querySelector('input[name="linkButtonIOSUrl"]');
            const aosUrlInput = row.querySelector('input[name="linkButtonAOSUrl"]');
            if (webUrlInput) webUrlInput.value = isMobileApp ? '' : (url.mobileUrl ?? '');
            if (iosUrlInput) iosUrlInput.value = !isMobileApp ? '' : (url.iosUrl ?? '');
            if (aosUrlInput) aosUrlInput.value = !isMobileApp ? '' : (url.aosUrl ?? '');
        });

        updateLinkPreview();
    }

    // 레시피 기본 이미지를 해당 행 썸네일로 복원
    function restoreItemImagePreview(itemKey, imageUrl) {
        if (!imageUrl) return;
        const instance = itemImageFileInputs.get(itemKey);
        if (!instance) return;
        instance.setFiles([{ fileName: imageUrl.split('/').pop(), fileImageUrl: imageUrl }]);
        instance.renderImagePreviews();
    }

    function applyFriendtalkRecipeItems(items) {
        const itemListTableBody = kakaoFriendtalkItemListSection.querySelector('tbody');

        // 아이템 수만큼 행 확보 (첫 행은 기존 생성, 최대 4개)
        items.forEach(function (_, idx) {
            if (idx === 0) return;
            if (itemListTableBody.querySelectorAll('tr').length >= 4) return;
            appendItemListRow(false);
        });

        const rows = itemListTableBody.querySelectorAll('tr');
        items.forEach(function (item, idx) {
            const row = rows[idx];
            if (!row) return;

            const titleInput = row.querySelector('input[name="itemTitle"]');
            if (titleInput && item?.title) {
                titleInput.value = item.title;
                titleInput.dispatchEvent(new Event('input', { bubbles: true }));
            }

            const imageUrlInput = row.querySelector('input[name="itemImageUrl"]');
            if (imageUrlInput && item?.imageUrl) {
                imageUrlInput.value = item.imageUrl;
                restoreItemImagePreview(row.dataset.itemKey, item.imageUrl);
            }

            const webLinkInput = row.querySelector('input[name="itemWebLink"]');
            if (webLinkInput && item?.url) {
                webLinkInput.value = item.url;
                webLinkInput.dispatchEvent(new Event('input', { bubbles: true }));
            }
        });

        // 타이틀 이벤트가 먼저 미리보기를 갱신하므로 이미지 반영 후 한 번 더 갱신
        updateItemListPreview();
    }

    // 친구톡 아이템 이미지 업로드 초기화
    function initItemImageFileInput(containerId, itemKey, isFirst = false) {
        const container = document.getElementById(containerId);
        if (!container) return;

        if (container.querySelector('.ncua-image-file-input')) return;

        const itemImageDimension = isFirst ? MIN_ITEM_FIRST_IMAGE_DIMENSION : MIN_ITEM_ADDITIONAL_IMAGE_DIMENSION;

        const imageFileInput = new ncua.ImageFileInput({
            container: containerId,
            buttonLabel: '',
            size: 'xs',
            accept: 'image/jpg, image/jpeg, image/png',
            multiple: false,
            showFileInput: false,
            onChange: async (newFiles) => {
                const file = newFiles[0];

                // setFiles() 복원 호출이면 건너뛰기
                if (file && !file.size) return;

                // 하이라이트 제거
                container.querySelector('.ncua-image-file-input').classList.remove('destructive');

                if (!file) {
                    // 이미지 삭제 시 hidden input 초기화 및 미리보기 업데이트
                    const row = document.querySelector(`tr[data-item-key="${itemKey}"]`);
                    if (row) row.querySelector('input[name="itemImageUrl"]').value = '';
                    updateItemListPreview();
                    return;
                }

                if (!await validateFiles(file, null, MAX_CAMPAIGN_IMAGE_FILE_SIZE, itemImageDimension, null, 'item')) {
                    imageFileInput.clearFiles();
                    return;
                }

                // 이미지 미리보기 렌더링
                imageFileInput.renderImagePreviews();

                const formData = new FormData();
                formData.append('mode', 'uploadFriendtalkImage');
                formData.append('imageFile', file);

                const result = await new Promise((resolve) => {
                    fetch('./mobile_send/layer_send_method_setting_ps.php', {
                        method: 'POST',
                        body: formData
                    })
                        .then(response => response.json())
                        .then(response => resolve({ success: response.success, message: response.message, data: response.data }))
                        .catch(() => resolve({ success: false }));
                });

                if (!result.success) {
                    NCDSAlert({ message: result.message, iconType: 'error' });
                    imageFileInput.clearFiles();
                    imageFileInput.renderImagePreviews();
                    return;
                }

                // hidden input에 imageUrl 저장
                const row = document.querySelector(`tr[data-item-key="${itemKey}"]`);
                row.querySelector('input[name="itemImageUrl"]').value = result.data.imageUrl;

                // 미리보기 업데이트
                updateItemListPreview();
            },
        });

        return imageFileInput;
    }

    /**
     * 캠페인 이미지 ImageFileInput 생성 (캐러셀 전환 시 재생성용)
     * @param {string|null} existingImageUrl 기존 업로드된 이미지 URL (캐러셀 복원 시)
     */
    function createCampaignImageFileInput(existingImageUrl = null) {
        // 기존 컨테이너 내용 초기화
        const container = document.getElementById('campaign-image-file-input-container');
        if (container) container.innerHTML = '';

        campaignImageFileInput = new ncua.ImageFileInput({
            container: 'campaign-image-file-input-container',
            buttonLabel: '',
            size: 'xs',
            accept: 'image/jpg, image/jpeg, image/png',
            multiple: false,
            showFileInput: false,
            onChange: async (newFiles) => {
                const file = newFiles[0];

                // setFiles()로 호출된 경우 (기존 이미지 URL 복원) 건너뛰기
                if (file && !file.size) return;

                // 하이라이트 제거
                document.querySelector('#campaign-image-file-input-container .ncua-image-file-input').classList.remove('destructive');

                const isCarousel = getCampaignMessageType() === 'CAROUSEL_FEED';
                if (!file) {
                    // 이미지 삭제 시 미리보기 및 hidden input 초기화
                    window.preview.setImage('');
                    document.querySelector('input[name="friendtalkCampaignImageUrl"]').value = '';
                    if (isCarousel && carouselState.carousels[carouselState.activeIndex]) {
                        carouselState.carousels[carouselState.activeIndex].imageUrl = '';
                    }
                    return;
                }


                const ratio = isCarousel ? {min: 3/4, max: 2/1} : null;
                // 업로드 시작 시점의 캐러셀 인덱스 캡처 (비동기 업로드 중 전환 대비)
                const uploadCarouselIndex = isCarousel ? carouselState.activeIndex : -1;

                const type = isCarousel ? 'carousel' : 'campaign';
                if (!await validateFiles(file, null, MAX_CAMPAIGN_IMAGE_FILE_SIZE, MIN_CAMPAIGN_IMAGE_DIMENSION, ratio, type)) {
                    campaignImageFileInput.clearFiles();
                    return;
                }

                // 이미지 미리보기 렌더링
                campaignImageFileInput.renderImagePreviews();

                const formData = new FormData();
                formData.append('mode', 'uploadFriendtalkImage');
                formData.append('imageFile', file);

                const result = await new Promise((resolve) => {
                    fetch('./mobile_send/layer_send_method_setting_ps.php', {
                        method: 'POST',
                        body: formData
                    })
                    .then(response => response.json())
                    .then(response => resolve({ success: response.success, message: response.message, data: response.data }))
                    .catch(() => resolve({ success: false }));
                });

                if (!result.success) {
                    console.log(result);
                    NCDSAlert({ message: result.message, iconType: 'error' });
                    campaignImageFileInput.clearFiles();
                    campaignImageFileInput.renderImagePreviews();
                    return;
                }

                // 캐러셀 피드 모드일 때 업로드 시작 시점의 캐러셀에 이미지 URL 저장
                if (uploadCarouselIndex >= 0 && carouselState.carousels[uploadCarouselIndex]) {
                    carouselState.carousels[uploadCarouselIndex].imageUrl = result.data.imageUrl;
                }

                // 현재 캐러셀이 업로드 시작 시점과 같을 때만 미리보기/hidden input 업데이트
                if (uploadCarouselIndex < 0 || carouselState.activeIndex === uploadCarouselIndex) {
                    window.preview.setImage(result.data.imageUrl);
                    document.querySelector('input[name="friendtalkCampaignImageUrl"]').value = result.data.imageUrl;
                }

                // file input 초기화 (같은 파일 재선택 가능하도록)
                const fileInput = document.querySelector('#campaign-image-file-input-container input[type="file"]');
                if (fileInput) fileInput.value = '';
            },
        });

        // 기존 이미지 URL이 있으면 미리보기 복원
        if (existingImageUrl) {
            campaignImageFileInput.setFiles([{ fileName: existingImageUrl.split('/').pop(), fileImageUrl: existingImageUrl }]);
            campaignImageFileInput.renderImagePreviews();
        }
    }

    async function validateFiles(file, allowedExtensions = null, maxSize = null, imageDimensions = null, aspectRatio = null, type = null) {
        // 파일이 없는 경우
        if (!file) {
            NCDSAlert({message: '파일을 선택해 주세요.', iconType: 'error'});
            return false;
        }

        // 파일 확장자 검증
        const fileName = file.name;

        // 파일 사이즈 검증
        if (maxSize && file.size > maxSize) {
            const maxSizeMB = (maxSize / (1024 * 1024)).toFixed(1);
            const fileSizeMB = (file.size / (1024 * 1024)).toFixed(2);
            NCDSAlert({
                message: `최대 ${maxSizeMB}MB까지 업로드 가능합니다. (현재 파일: ${fileSizeMB}MB)`,
                iconType: 'error'
            });
            return false;
        }

        // 카카오 친구톡 이미지 크기 검증
        if (imageDimensions || aspectRatio) {
            try {
                const {width, height} = await getImageDimensions(file);
                const ratioError = aspectRatio && (width / height < aspectRatio.min || width / height > aspectRatio.max);
                const sizeError = imageDimensions && (width < imageDimensions.minWidth || height < imageDimensions.minHeight);

                if (type === 'carousel' && (ratioError || sizeError)) {
                    NCDSAlert({
                        message: `가로:세로 비율은 2:1 이상 또는 3:4 이하여야 합니다. 가로는 최소 500px, 세로는 최소 250px 이상이어야 합니다. (${fileName})`,
                        iconType: 'error'
                    });
                    return false;
                }

                if (type === 'item' && sizeError) {
                    const ratio = imageDimensions.minWidth === imageDimensions.minHeight ? '1:1' : '2:1';
                    NCDSAlert({
                        message: `가로:세로 비율은 ${ratio}이어야 합니다. 아이템 이미지의 가로는 최소 ${imageDimensions.minWidth}px, 세로는 최소 ${imageDimensions.minHeight}px 이상이어야 합니다. (${fileName})`,
                        iconType: 'error'
                    });
                    return false;
                }

                if (sizeError) {
                    NCDSAlert({
                        message: `가로는 최소 ${imageDimensions.minWidth}px, 세로는 최소 ${imageDimensions.minHeight}px 이상이어야 합니다. (${fileName})`,
                        iconType: 'error'
                    });
                    return false;
                }
            } catch (error) {
                NCDSAlert({message: '파일 데이터가 올바르지 않습니다.<br>다른 파일을 선택해 주세요.', iconType: 'error'});
                return false;
            }
        }

        return true;
    }

    // 이미지 크기를 가져오는 헬퍼 함수
    const getImageDimensions = (file) => {
        return new Promise((resolve, reject) => {
            const reader = new FileReader();
            reader.onload = (e) => {
                const img = new Image();
                img.onload = () => {
                    resolve({ width: img.width, height: img.height });
                };
                img.onerror = () => {
                    reject(new Error('이미지 로드 실패'));
                };
                img.src = e.target.result;
            };
            reader.onerror = () => {
                reject(new Error('파일 읽기 실패'));
            };
            reader.readAsDataURL(file);
        });
    }
</script>
