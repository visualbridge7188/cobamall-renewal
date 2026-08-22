<div class="js-sms-setting-section">
    <div class="swiper ncua-horizontal-tab ncua-horizontal-tab--md ncua-horizontal-tab--panel">
        <div class="swiper-wrapper">
            <div class="swiper-slide ncua-horizontal-tab__item">
                <button type="button" class="ncua-tab-button is-active" data-send-tab-type="MAIN">
                    SMS/LMS 메시지 작성
                </button>
            </div>
        </div>
    </div>
    <div class="tab-content-wrap">
        <div class="message-content">
            <div class="ncua-card__body-title-wrap">
                <p class="ncua-card__body-title--xs">내용 입력</p>
            </div>
            <div id="messageContentContainer"></div>
        </div>
        <div id="replaceCodeContainer" class="js-replace-code"></div>

        <!-- 메시지 성과 추적 -->
        <div class="message-performance-analysis">
            <p class="ncua-card__body-title--xs tooltip-align" data-tooltip-seq="004">메시지 성과 추적</p>
            <div class="add-link-wrap">
                <div class="ncua-input ncua-input--xs">
                    <div class="ncua-input__content">
                        <div class="ncua-input__field ncua-input__field--xs">
                            <input name="" type="text" value="" placeholder="메시지에 삽입할 링크를 여기에 넣고 추가를 누르면 숏링크가 생성됩니다." data-role="short-link-input" />
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

        <!-- 템플릿 버튼 -->
        <?php if (!$isRecipeContext): ?>
        <div class="template-control-wrap">
            <button type="button" data-click-target="loadTemplate" class="ncua-btn ncua-btn--sm ncua-btn--secondary">템플릿 불러오기</button>
            <button type="button" data-click-target="saveAsTemplate" class="ncua-btn ncua-btn--sm ncua-btn--secondary-gray file-btn">
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 16 16" fill="none"><path d="M9.33317 1.51294V4.26663C9.33317 4.64 9.33317 4.82669 9.40583 4.96929C9.46975 5.09473 9.57174 5.19672 9.69718 5.26064C9.83978 5.3333 10.0265 5.3333 10.3998 5.3333H13.1535M7.99984 11.9999V7.99992M5.99984 9.99992H9.99984M9.33317 1.33325H5.8665C4.7464 1.33325 4.18635 1.33325 3.75852 1.55124C3.3822 1.74299 3.07624 2.04895 2.88449 2.42527C2.6665 2.85309 2.6665 3.41315 2.6665 4.53325V11.4666C2.6665 12.5867 2.6665 13.1467 2.88449 13.5746C3.07624 13.9509 3.3822 14.2569 3.75852 14.4486C4.18635 14.6666 4.7464 14.6666 5.8665 14.6666H10.1332C11.2533 14.6666 11.8133 14.6666 12.2412 14.4486C12.6175 14.2569 12.9234 13.9509 13.1152 13.5746C13.3332 13.1467 13.3332 12.5867 13.3332 11.4666V5.33325L9.33317 1.33325Z" stroke="black" stroke-width="1.33" stroke-linecap="round" stroke-linejoin="round"/></svg>
                템플릿으로 저장하기
            </button>
        </div>
        <?php endif; ?>
    </div>
    <!-- SMS/LMS 대체 발송 안내 -->
    <details class="ncua-accordion ncua-accordion--gray" <?= !$isRecipeContext ? 'open' : '' ?>>
        <summary>SMS/LMS 발송 안내</summary>
        <div class="ncua-accordion__content has-dot">
            <ul>
                <li>SMS 작성 시 90byte를 초과하면 LMS로 자동 전환되어 발송됩니다.</li>
                <li>변수에 실제 데이터가 적용된 최종 메시지가 90byte를 초과하는 경우에도 LMS로 전환될 수 있습니다.</li>
            </ul>
        </div>
    </details>
    <!-- 광고성 문구 추가 -->
    <div class="ad-phrase-add">
        <p class="ncua-card__body-title--xs">광고성 문구 추가</p>
        <label class="ncua-checkbox-field ncua-checkbox-field--xs has-text">
            <span class="ncua-checkbox-input ncua-checkbox-input--xs ncua-checkbox-field__input">
                <input type="checkbox" name="use080Reject" data-available="<?= $is080RejectAvailable ? 'y' : 'n' ?>">
            </span>
            <span>
                <span class="ncua-checkbox-field__text">광고성 문구 추가</span>
                <span class="ncua-checkbox-field__support-text">광고성 문구를 추가하려면 <a class="ncua-btn ncua-btn--xs ncua-btn--text has-underline" href="../service/service_info.php?menu=consulting_refusal_info" target="_blank">[080 수신거부 사용신청]</a>을 먼저 해주시기 바랍니다.</span>
            </span>
        </label>
    </div>
</div>

<script type="text/javascript">
    const smsSettingSection = document.querySelector('.js-sms-setting-section');
    const messageContentTextarea = document.querySelector('textarea[name="messageContent"]');
    const chipContainer = document.querySelector('[data-role="chip-container"]');

    $(document).ready(function () {
        initializeSmsContentSettingSectionEvents();
        initializeSmsContentSettingSectionElement();
    });

    function initializeSmsContentSettingSectionEvents() {
        const shortLinkInput = smsSettingSection.querySelector('input[data-role="short-link-input"]');
        const addShortLinkBtn = smsSettingSection.querySelector('button[data-role="add-short-link"]');
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
            addSmsChipButton(linkValue);

            // 입력 필드 초기화
            shortLinkInput.value = '';

            const isImpossibleAddShortLink = document.querySelectorAll(`.chip-button[data-send-tab-type="${getActivatedSendTabType()}"]`).length > 4;
            shortLinkInput.disabled = isImpossibleAddShortLink;
            addShortLinkBtn.disabled = isImpossibleAddShortLink;
        });

        smsSettingSection.querySelector('button[data-click-target="loadTemplate"]')?.addEventListener('click', function() {
            $.post('./mobile_send/layer_mobile_send_sms_template.php', null, function (data) {
                ncds_layer_popup({message: data, title: '템플릿 불러오기', size: 'wide-sm'});
            });
        });

        smsSettingSection.querySelector('button[data-click-target="saveAsTemplate"]')?.addEventListener('click', function() {
            const message = messageInput.getValue();
            if (message === '') {
                NCDSAlert({
                    message: '내용을 입력해야 템플릿으로 저장할 수 있습니다.',
                    iconType: 'error'
                });
                return;
            }
            const smsData = [{ name: "contents", value: message }];
            $.post('./mobile_send/layer_mobile_send_save_sms_template.php', smsData, function (data) {
                ncds_layer_popup({message: data, title: '템플릿으로 저장<div class="modal-title-description">작성한 내용을 저장할 카테고리와 제목을 선택하여 주시기 바랍니다.</div>', size: 'wide-sm'});
            });
        });

        smsSettingSection.querySelector('input[name="use080Reject"]').addEventListener('click', function(e) {
            if (this.dataset.available !== 'y') {
                e.preventDefault();
                return;
            }

            if (this.checked) {
                addAdText(window.messageInput, window.preview);
            } else {
                removeAdText(window.messageInput, window.preview);
            }
        });
    }

    // 칩 버튼 생성 함수
    function addSmsChipButton(linkValue) {
        const linkKey = generateLinkKey();
        const chipButton = generateChipButton(linkKey, linkValue);
        const shortLinkInput = smsSettingSection.querySelector('input[data-role="short-link-input"]');
        const addShortLinkBtn = smsSettingSection.querySelector('button[data-role="add-short-link"]');

        chipButton.addEventListener('click', function() {
            const replaceCode = chipButton.querySelector('.chip-button-text');
            window.messageInput.insertText(replaceCode.textContent);
        });

        // 닫기 버튼 이벤트
        const removeBtn = chipButton.querySelector('[data-role="remove-chip"]');
        removeBtn.addEventListener('click', function(e) {
            e.stopPropagation(); // 부모 버튼 클릭 이벤트 방지

            // chip-button-text 값을 textContent에서 모두 삭제
            const chipText = chipButton.querySelector('.chip-button-text')?.textContent;
            if (chipText) {
                const escapedText = chipText.replace(/[.*+?^${}()|[\]\\]/g, '\\$&');
                const replacedContent = messageInput.getValue().replace(new RegExp(escapedText, 'g'), '')
                window.messageInput.setValue(replacedContent);
                window.preview.setContent(replacedContent);
                if (replacedContent === '') {
                    setDefaultPreview(['TEXT']);
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

    function initializeSmsContentSettingSectionElement() {
        const initialMessage = <?= json_encode($initialMessage ?? '', JSON_UNESCAPED_UNICODE | JSON_HEX_TAG | JSON_HEX_AMP) ?>;

        // MessageInput 생성
        window.messageInput = GodoUIModule.render({
            type: 'MessageInput',
            target: '#messageContentContainer',
            name: 'messageContent',
            maxLength: 2000,
            useBytes: true,
            allowEmoji: false,
            initialValue: initialMessage,
            onInput: syncSmsPreviewAndHint,
        });

        const isRecipeContext = <?= $isRecipeContext ? 'true' : 'false' ?>;
        window.chipSelector = GodoUIModule.render({
            type: 'ChipSelector',
            target: '#replaceCodeContainer',
            title: isRecipeContext ? '추천 변수' : '사용 가능한 변수',
            tooltipSeq: '003',
            hideCategorySelector: isRecipeContext,
            cautionHTML: `
                <ul class="replace-code-caution">
                    <li id="nonMemberReplaceCodeCaution" class="ncua-caution-text" hidden>회원 외 치환코드는 정상 적용되지 않으니 저장된 메시지 수정 시에만 이용바랍니다.</li>
                    <li id="useTemplateCaution" class="ncua-caution-text" hidden>템플릿 사용시에 미지원 치환코드가 포함된 경우, 해당 값은 공란으로 발송되므로 미리보기를 확인해주시기 바랍니다.</li>
                </ul>
            `,
            categories: isRecipeContext ? buildRecipeReplaceCodeCategories() : buildReplaceCodeCategories(),
            onSelect: (variable) => {
                messageInput.insertText(MobileMessageReplaceCode.resolveRecipeExpireInsertKey(variable, isRecipeContext));
            },
            onCategoryChange: (category) => {
                smsSettingSection.querySelector('#nonMemberReplaceCodeCaution').hidden = category === 'MEMBER';
            }
        });

        // 미리보기 랜더링
        document.querySelector('.js-preview-tab-section').classList.add('display-none');
        window.preview = GodoUIModule.render({
            type: 'MessagePreview',
            target: '#previewContainer',
            sendType: 'SMS'
        });

        window.preview.onVariableCheckboxChange(() => {
            window.preview.setContent(resolveReplaceCode(window.messageInput.getValue()));
        });

        syncSmsPreviewAndHint(initialMessage);

        const recipientType = document.querySelector('input[name="recipientType"]:checked')?.value;
        if (['EXCEL_UPLOAD', 'DIRECT'].includes(recipientType)) {
            smsSettingSection.querySelector('.js-replace-code').classList.add('display-none');
        }
    }

    function syncSmsPreviewAndHint(input) {
        window.preview.setContent(resolveReplaceCode(input));
        MobileMessageReplaceCode.updateExpireCodeGuide('#replaceCodeContainer', input);
        if (input === '') {
            window.messageInput.setHint(null);
            smsSettingSection.querySelector('#useTemplateCaution').hidden = true;
            setDefaultPreview(['TEXT']);
        } else {
            window.messageInput.setHint(window.messageInput.calculateLength(input) > 90 ? 'LMS 건당 3포인트 차감' : 'SMS 건당 1포인트 차감');
        }
    }

    function setTemplate(message) {
        smsSettingSection.querySelector('input[name="use080Reject"]').checked = false;

        // 메시지 적용
        window.messageInput.setValue('');
        window.messageInput.insertText(message);

        // 템플릿 불러오기 안내문구
        smsSettingSection.querySelector('#useTemplateCaution').hidden = false;
    }

    function clearSmsComponent() {
        initializeData();
        const shortLinkInput = smsSettingSection.querySelector('input[data-role="short-link-input"]');
        const addShortLinkBtn = smsSettingSection.querySelector('button[data-role="add-short-link"]');

        shortLinkInput.value = '';
        smsSettingSection.querySelector('[data-role="chip-container"]').innerHTML = '';
        window.messageInput.setValue('');
        window.preview.reset();

        const isImpossibleAddShortLink = document.querySelectorAll(`.chip-button[data-send-tab-type="${getActivatedSendTabType()}"]`).length > 4;
        shortLinkInput.disabled = isImpossibleAddShortLink;
        addShortLinkBtn.disabled = isImpossibleAddShortLink;
    }
</script>
