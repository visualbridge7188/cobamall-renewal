<div class="js-kakao-alimtalk-tab-section">
    <div class="swiper ncua-horizontal-tab ncua-horizontal-tab--md ncua-horizontal-tab--panel">
        <div class="swiper-wrapper">
            <div class="swiper-slide ncua-horizontal-tab__item">
                <button type="button" class="ncua-tab-button is-active" data-send-tab-type="MAIN">
                    알림톡 메시지 작성
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
        <!-- 메시지 내용 입력 -->
        <div class="message-content send-method-component" data-target="MAIN">
            <div class="ncua-card__body-title-wrap">
                <p class="ncua-card__body-title--xs">내용 입력</p>
            </div>
            <!-- 알림톡 템플릿 선택 -->
            <div class="select-alarm-template">
                <p class="template-option-title"><span>*</span>구분</p>
                <div class="template-radio-wrap">
                    <?php foreach ($kakaoAlimTalkTemplateTypes as $kakaoAlimTalkTemplateType): ?>
                        <label class="ncua-radio-field ncua-radio-field--xs has-text">
                            <span class="ncua-radio-input ncua-radio-input--xs ncua-radio-field__input">
                                <input name="templateType" type="radio" value="<?= $kakaoAlimTalkTemplateType->name ?>" <?= $kakaoAlimTalkTemplateType->name === 'ORDER' ? 'checked="checked"' : '' ?>/>
                            </span>
                            <span>
                                <span class="ncua-radio-field__text"><?= $kakaoAlimTalkTemplateType->value ?></span>
                            </span>
                        </label>
                    <?php endforeach; ?>
                </div>
                <p class="template-option-title"><span>*</span>템플릿 명</p>
                <span class="ncua-select ncua-select--xs">
                    <span class="ncua-select__content">
                        <select class="ncua-select__tag" name="selectKakaoAlimtalkTemplate">
                            <option value="">템플릿을 선택하세요</option>
                        </select>
                    </span>
                </span>
            </div>
            <div class="ncua-input ncua-input--textarea ncua-input--textarea--xs">
                <textarea name="messageContent" class="ncua-input__textarea" data-message-textarea placeholder="메시지 내용을 입력하세요." disabled></textarea>
            </div>
        </div>

        <div class="message-content send-method-component" data-target="ALTERNATIVE">
            <div class="ncua-card__body-title-wrap">
                <p class="ncua-card__body-title--xs">내용 입력</p>
                <button type="button" class="ncua-btn ncua-btn--xs ncua-btn--text-gray has-underline refresh-btn load-main-contents">
                    메시지 최신 상태 반영
                </button>
            </div>
            <div id="messageContentAlternativeContainer"></div>
        </div>
        <div id="replaceCodeAlternativeContainer" class="send-method-component" data-target="ALTERNATIVE"></div>

        <!-- 메시지 성과 추적 -->
        <div class="message-performance-analysis send-method-component" data-target="ALTERNATIVE">
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
    </div>
    <!-- 알림톡 안내 -->
    <details class="ncua-accordion ncua-accordion--gray send-method-component" data-target="MAIN" open>
        <summary>알림톡 안내</summary>
        <div class="ncua-accordion__content has-dot">
            <ul>
                <li>알림톡으로 사용 시 자동 SMS는 발송되지 않습니다.</li>
                <li>카카오톡 미설치 등으로 알림톡 발송 실패 시 SMS/LMS로 동일 메시지가 재발송됩니다.</li>
                <li>대체 발송의 경우 <a href="../crm/message_config.php">메시지 설정</a>에서 변경이 가능합니다.</li>
            </ul>
        </div>
    </details>
    <!-- SMS/LMS 대체 발송 안내 -->
    <details class="ncua-accordion ncua-accordion--gray send-method-component" data-target="ALTERNATIVE" open>
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
    const kakaoAlimtalkTabSection = document.querySelector('.js-kakao-alimtalk-tab-section');
    const chipContainer = document.querySelector('[data-role="chip-container"]');

    const kakaoAlimtalkTemplates = <?= json_encode($kakaoAlimtalkTemplates, JSON_UNESCAPED_UNICODE) ?>;

    $(document).ready(function () {
        initializeKakaoAlimtalkContentSettingSectionEvents();
        initializeKakaoAlimtalkContentSettingSectionElement();
    });

    function initializeKakaoAlimtalkContentSettingSectionEvents() {
        // 메인/대체 메시지 탭 버튼
        kakaoAlimtalkTabSection.querySelectorAll('.ncua-tab-button[data-send-tab-type]').forEach(button => {
            button.addEventListener('click', function () {
                const tabType = button.dataset.sendTabType;
                if (tabType === 'ALTERNATIVE') {
                    if (!enableAlternative('ALIMTALK')) {
                        NCDSAlert({
                            message: '대체 메시지 설정이 비활성화되어 있습니다.',
                            subMessage: 'CRM > 메시지 > 메시지 설정에서 기능을 활성화한 후 다시 시도해주세요.',
                            iconType: 'error'
                        });
                        return;
                    }
                }

                setPreviewTab(tabType);
                updateActiveTab(kakaoAlimtalkTabSection, tabType);
                updateTabComponent(kakaoAlimtalkTabSection, tabType);
            });
        });

        kakaoAlimtalkTabSection.querySelectorAll('input[name="templateType"]').forEach(radio => {
            radio.addEventListener('change', function () {
                resetMessageValue()
                refreshKakaoAlimtalkTemplate(this.value)
            });
        });

        // 알림톡 메세지 작성 - 템플릿 선택 이벤트
        kakaoAlimtalkTabSection.querySelector('select[name="selectKakaoAlimtalkTemplate"]').addEventListener('change', async function (e) {
            const selectedTemplate = getSelectedKakaoAlimtalkTemplate();
            if (!selectedTemplate) return;

            kakaoAlimtalkTabSection.querySelector('textarea[name="messageContent"]').value = selectedTemplate.baseContents;
            window.preview.reset();
            setTemplateData(selectedTemplate);

            if (e.target.value === '') {
                setDefaultPreview(['ALIMTALK_TEXT']);
            }
        });

        kakaoAlimtalkTabSection.querySelector('input[name="use080Reject"]').addEventListener('click', function(e) {
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

        kakaoAlimtalkTabSection.querySelector('button.load-main-contents').addEventListener('click', function() {
            const message = kakaoAlimtalkTabSection.querySelector('textarea[name="messageContent"]').value;
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

        const shortLinkInput = kakaoAlimtalkTabSection.querySelector('input[data-role="short-link-input"]');
        const addShortLinkBtn = kakaoAlimtalkTabSection.querySelector('button[data-role="add-short-link"]');

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
            addKakaoAlimtalkChipButton(linkValue);

            // 입력 필드 초기화
            shortLinkInput.value = '';

            const isImpossibleAddShortLink = document.querySelectorAll(`.chip-button[data-send-tab-type="${getActivatedSendTabType()}"]`).length > 4;
            shortLinkInput.disabled = isImpossibleAddShortLink;
            addShortLinkBtn.disabled = isImpossibleAddShortLink;
        });
    }

    // 칩 버튼 생성 함수
    function addKakaoAlimtalkChipButton(linkValue, linkKey = null) {
        if (!linkKey) linkKey = generateLinkKey();
        const chipButton = generateChipButton(linkKey, linkValue);
        const shortLinkInput = kakaoAlimtalkTabSection.querySelector('input[data-role="short-link-input"]');
        const addShortLinkBtn = kakaoAlimtalkTabSection.querySelector('button[data-role="add-short-link"]');

        chipButton.addEventListener('click', function() {
            window.alternativeMessageInput.insertText(chipButton.querySelector('.chip-button-text').textContent); // 알림톡은 대체메시지에 반영
        });

        // 닫기 버튼 이벤트
        const removeBtn = chipButton.querySelector('[data-role="remove-chip"]');
        removeBtn.addEventListener('click', function(e) {
            e.stopPropagation(); // 부모 버튼 클릭 이벤트 방지

            // chip-button-text 값을 textContent에서 모두 삭제
            const chipText = chipButton.querySelector('.chip-button-text')?.textContent;
            if (chipText) {
                const escapedText = chipText.replace(/[.*+?^${}()|[\]\\]/g, '\\$&');
                const replacedContent = alternativeMessageInput.getValue().replace(new RegExp(escapedText, 'g'), '')
                window.alternativeMessageInput.setValue(replacedContent);
                window.alternativePreview.setContent(replacedContent);
                if (replacedContent === '') {
                    setDefaultPreview(['ALTERNATIVE_TEXT']);
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

    function setTemplateData(templateData) {
        if (templateData.templateTitle) {
            window.preview.setEmTitle(templateData.templateTitle);
        }

        if (templateData.templateSubtitle) {
            window.preview.setEmSubTitle(templateData.templateSubtitle);
        }

        if (templateData.templateImageUrl) {
            window.preview.setImage(templateData.templateImageUrl);
        }

        if (templateData.templateItems.length > 0) {
            if (templateData.templateHeader) {
                window.preview.setListHeader(resolveReplaceCode(templateData.templateHeader));
            }
            if (templateData.templateItemHighlight) {
                if (templateData.templateItemHighlight.title) {
                    window.preview.setHighlightTitle(templateData.templateItemHighlight.title);
                    window.preview.setHighlightDesc(templateData.templateItemHighlight.description);
                    window.preview.setHighlightImage(templateData.templateItemHighlight.imageUrl);
                }
            }

            const itemList = templateData.templateItems.map(item => ({
                title: item.title,
                desc: resolveReplaceCode(item.description),
                image: ''
            }));

            if (templateData.templateItemSummary && templateData.templateItemSummary.title) {
                itemList.push({
                    title: templateData.templateItemSummary.title,
                    desc: resolveReplaceCode(templateData.templateItemSummary.description),
                    image: '',
                    isSummary: true
                });
            }

            window.preview.setItemList(itemList);
        }

        // 메시지 작성 영역
        window.preview.setContent(resolveReplaceCode(templateData.baseContents));

        // 추가 정보 영역
        if (templateData.templateExtra) {
            window.preview.setExtraInfo(templateData.templateExtra);
        }

        if (templateData.templateButtons.length > 0) {
            const buttons = [...templateData.templateButtons]
                .sort((a, b) => {
                    if (a.type === 'ADD_CHANNEL') return -1;
                    if (b.type === 'ADD_CHANNEL') return 1;
                    return a.order - b.order;
                })
                .map(btn => ({
                    text: btn.name,
                    url: btn.mobileUrl || btn.pcUrl || '',
                    type: btn.type === 'ADD_CHANNEL' ? 'add-channel' : 'WL'
                }));

            if (templateData.templateButtons.some(btn => btn.type === 'ADD_CHANNEL')) {
                window.preview.setChannelMessage('채널 추가하고 이 채널의 마케팅 메시지 등을 카카오톡으로 받기');
            }
            window.preview.setButtons(buttons);
        }
    }

    // 카카오 알림톡 선택된 템플릿 조회
    function getSelectedKakaoAlimtalkTemplate() {
        const select = kakaoAlimtalkTabSection.querySelector('select[name="selectKakaoAlimtalkTemplate"]');
        const option = select.options[select.selectedIndex];
        if (!option.value) return null;

        const typeTemplates = kakaoAlimtalkTemplates[option.dataset.templateType.toLowerCase()]
        return typeTemplates.find(template => template.templateCode === option.value);
    }

    function resetMessageValue() {
        // 메시지 작성 영역
        kakaoAlimtalkTabSection.querySelector('textarea[name="messageContent"]').value = '';
        window.messageInput?.setValue('');
        window.preview?.reset();
        window.alternativePreview?.reset();
    }

    function refreshKakaoAlimtalkTemplate(templateType) {
        const selectKakaoAlimtalkTemplate = kakaoAlimtalkTabSection.querySelector('select[name="selectKakaoAlimtalkTemplate"]');
        selectKakaoAlimtalkTemplate.innerHTML = `<option value="">템플릿을 선택하세요</option>`;

        const filteredTemplates = kakaoAlimtalkTemplates[templateType.toLowerCase()] ?? [];
        filteredTemplates.forEach(template => {
            const option = document.createElement('option');
            option.value = template.templateCode;
            option.textContent = template.templateName;

            option.dataset.templateType = templateType;
            selectKakaoAlimtalkTemplate.appendChild(option);
        });
    }

    function initializeKakaoAlimtalkContentSettingSectionElement() {
        // 초기 메시지 타입 선택
        kakaoAlimtalkTabSection.querySelector('button[data-send-tab-type="MAIN"]')?.dispatchEvent(new Event('click'));
        // 초기 템플릿 타입 선택
        kakaoAlimtalkTabSection.querySelector('input[name="templateType"]:checked').dispatchEvent(new Event('change'));

        // 대체 메시지 인풋
        window.alternativeMessageInput = GodoUIModule.render({
            type: 'MessageInput',
            target: '#messageContentAlternativeContainer',
            name: 'messageAlternativeContent',
            title: '내용 입력',
            maxLength: 2000,
            useBytes: true,
            allowEmoji: false,
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

        window.alternativeChipSelector = GodoUIModule.render({
            type: 'ChipSelector',
            target: '#replaceCodeAlternativeContainer',
            title: '사용 가능한 변수',
            tooltipSeq: '003',
            categories: buildReplaceCodeCategories(),
            onSelect: (variable) => {
                window.alternativeMessageInput.insertText(variable);
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
            sendType: 'ALIMTALK'
        });

        window.preview.onVariableCheckboxChange(() => {
            window.preview.setContent(resolveReplaceCode(kakaoAlimtalkTabSection.querySelector('textarea[name="messageContent"]').value));
        });

        window.alternativePreview = GodoUIModule.render({
            type: 'MessagePreview',
            target: '#previewAlternativeContainer',
            sendType: 'SMS'
        });

        window.alternativePreview.onVariableCheckboxChange(() => {
            window.alternativePreview.setContent(resolveReplaceCode(window.alternativeMessageInput.getValue(), false));
        });

        setDefaultPreview(['ALIMTALK_TEXT', 'ALTERNATIVE_TEXT']);
    }
</script>
