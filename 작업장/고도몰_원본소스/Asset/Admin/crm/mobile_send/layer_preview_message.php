<section class="ncua-card js-preview-message-section">
    <header class="ncua-card__header">
        <h4 class="ncua-card__title">메시지 미리보기</h4>
        <div class="swiper ncua-horizontal-tab ncua-horizontal-tab--sm ncua-horizontal-tab--button-white js-preview-tab-section display-none">
            <div class="swiper-wrapper">
                <div class="swiper-slide ncua-horizontal-tab__item">
                    <button type="button" class="ncua-tab-button is-active" data-preview-message-type="MAIN">
                        메인 메시지
                    </button>
                </div>
                <div class="swiper-slide ncua-horizontal-tab__item">
                    <button type="button" class="ncua-tab-button" data-preview-message-type="ALTERNATIVE">
                        대체 메시지
                    </button>
                </div>
            </div>
        </div>
    </header>
    <section class="ncua-card__body">
        <div id="previewContainer" class="preview-component" data-target="MAIN"></div>
        <div id="previewAlternativeContainer" class="preview-component" data-target="ALTERNATIVE"></div>
    </section>
</section>


<script type="text/javascript">
    const previewMessageSection = document.querySelector('.js-preview-message-section');

    $(document).ready(function () {
        initializePreviewMessageSectionEvents();
        initializePreviewMessageSectionElement();
    });

    function initializePreviewMessageSectionEvents() {
        previewMessageSection.querySelectorAll('.ncua-tab-button[data-preview-message-type]').forEach(button => {
            button.addEventListener('click', function () {
                const tabType = button.dataset.previewMessageType;
                if (tabType === 'ALTERNATIVE') {
                    const sendMethod = document.querySelector('input[name="sendMethod"]:checked')?.value ?? 'SMS';
                    if (sendMethod !== 'SMS' && !enableAlternative(sendMethod)) {
                        NCDSAlert({
                            message: '대체 메시지 설정이 비활성화되어 있습니다.',
                            subMessage: 'CRM > 메시지 > 메시지 설정에서 기능을 활성화한 후 다시 시도해주세요.',
                            iconType: 'error'
                        });
                        return;
                    }
                }
                updatePreviewActiveTab(tabType);
                updatePreviewTabComponent(tabType);
                if (typeof setSendMethodTab === 'function') {
                    setSendMethodTab(tabType);
                }
            });
        });
    }

    function initializePreviewMessageSectionElement() {
        previewMessageSection.querySelector('button[data-preview-message-type="MAIN"]')?.dispatchEvent(new Event('click'));
    }

    function updatePreviewTabComponent(tabType) {
        previewMessageSection.querySelectorAll('.preview-component').forEach(el => {
            el.classList.add('display-none');
            const targets = el.dataset.target?.split(',') ?? [];
            if (targets.includes(tabType)) {
                el.classList.remove('display-none');
            }
        });
    }

    function updatePreviewActiveTab(tabType) {
        previewMessageSection.querySelectorAll('button[data-preview-message-type]').forEach(btn => {
            btn.classList.toggle('is-active', btn.dataset.previewMessageType === tabType);
        });
    }

    function setPreviewTab(tabType) {
        previewMessageSection.querySelector(`button[data-preview-message-type="${tabType}"]`).click();
    }
</script>
