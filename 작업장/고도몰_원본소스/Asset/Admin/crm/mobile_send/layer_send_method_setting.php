<section class="js-send-method-setting-section">
    <header class="ncua-card__header">
        <h4 class="ncua-card__title">
            <?= $isRecipeContext ? '메시지 발송' : '발송수단 설정' ?>
        </h4>
        <?php if ($isRecipeContext && $messageSendGuide !== ''): ?>
            <p class="ncua-notice-info js-message-send-guide"><?= nl2br(gd_htmlspecialchars($messageSendGuide)) ?></p>
        <?php endif; ?>
    </header>
    <section class="ncua-card__body">
        <!-- 발송수단 라디오 버튼 -->
        <div class="ncua-border-group-box">
            <?php foreach ($sendMethods as $sendMethod): ?>
                <label class="ncua-radio-field ncua-radio-field--xs has-text">
                    <span class="ncua-radio-input ncua-radio-input--xs ncua-radio-field__input">
                        <input name="sendMethod" type="radio" value="<?= $sendMethod->name ?>" <?= $sendMethod->name === 'SMS' ? 'checked="checked"' : ''?> />
                    </span>
                    <span>
                        <span class="ncua-radio-field__text"><?= $sendMethod->value ?></span>
                    </span>
                </label>
            <?php endforeach; ?>
        </div>
        <div id="layerSendMethodContent" class="ncua-horizontal-tab"></div>
    </section>
</section>

<script type="text/javascript">
    const sendMethodSettingSection = document.querySelector('.js-send-method-setting-section');
    const smsRejectText = '<?= $smsRejectText ?>';
    let linkKeyCounters = {};
    window.shortLinkKeys = new Map();
    window.shortLinkAlternativeKeys = new Map();

    $(document).ready(function () {
        initializeSendMethodSettingSectionEvents();
        initializeSendMethodSettingSectionElement();
    });

    function initializeSendMethodSettingSectionEvents() {
        sendMethodSettingSection.querySelectorAll(`input[name="sendMethod"]`).forEach(radio => {
            radio.addEventListener('change', function() {
                initializeData();
                loadSendMethodContentSettingLayer(radio.value);
                setRecipientTypeAvailability(radio.value !== 'ALIMTALK' && radio.value !== 'MYAPP');
                setPreviewTab('MAIN');
            });

            radio.addEventListener('click', function (e) {
                if (['ALIMTALK', 'MYAPP'].includes(this.value)) {
                    if (!document.querySelector('.js-recipient-setting-section')) {
                        e.preventDefault();
                        return;
                    }
                }

                const validateResult = validateSendMethodUseFlag(this.value);
                if (!validateResult.result) {
                    e.preventDefault();
                    NCDSAlert({
                        message: validateResult.message,
                        subMessage: validateResult.subMessage,
                        iconType: 'error'
                    });
                    return;
                }

                if (isEditing()) {
                    e.preventDefault();
                    NCDSConfirm({
                        message: '발송수단을 변경하시겠습니까?',
                        subMessage: '발송 수단을 변경하면 지금까지 작성한 내용이 모두 삭제됩니다.<br />계속 진행하시겠습니까?',
                        callback: function (result) {
                            if (result) {
                                radio.checked = true;
                                e.target.dispatchEvent(new Event('change', { bubbles: true }))
                            }
                        },
                    });
                }
            });
        });
    }

    function validateSendMethodUseFlag(sendMethod) {
        switch (sendMethod) {
            case 'FRIENDTALK':
                if (messageConfig.kakaoFriendTalk.useFlag === 'y') {
                    return { result: true };
                } else {
                    return {
                        result: false,
                        message: '발송 수단이 준비되지 않았습니다.',
                        subMessage: '메시지를 발송하려면 발송 수단 인증 및 설치가 필요합니다.<br />통합 설정 페이지에서 먼저 완료해 주세요.'
                    };
                }
            case 'ALIMTALK':
                if (messageConfig.kakaoAlimTalk.useFlag === 'y') {
                    if (messageConfig.kakaoAlimTalk.sender !== 'kakaoAlrimCloud') {
                        return {
                            result: false,
                            message: '비즈엠/블룸에이아이 발송 불가 안내',
                            subMessage: '비즈엠, 블룸에이아이 채널은 알림톡으로 메시지를 발송할 수 없습니다.<br />CRM > 메시지 > 메시지 설정에서 제공사를 고도몰로 변경해 주세요.'
                        };
                    }

                    return {result: true};
                } else {
                    return {
                        result: false,
                        message: '발송 수단이 준비되지 않았습니다.',
                        subMessage: '메시지를 발송하려면 발송 수단 인증 및 설치가 필요합니다.<br />통합 설정 페이지에서 먼저 완료해 주세요.'
                    };
                }
            case 'MYAPP':
                if (messageConfig.myappPush.useFlag === 'y') {
                    return { result: true };
                } else {
                    return {
                        result: false,
                        message: '발송 수단이 준비되지 않았습니다.',
                        subMessage: '메시지를 발송하려면 발송 수단 인증 및 설치가 필요합니다.<br />통합 설정 페이지에서 먼저 완료해 주세요.'
                    };
                }
            default:
                return { result: true };
        }
    }

    function initializeData() {
        linkKeyCounters = {};
        window.shortLinkKeys = new Map();
        window.shortLinkAlternativeKeys = new Map();
    }

    function setShortLinkKey(linkKey, linkValue) {
        const activeSendTabType = document.querySelector('button[data-send-tab-type].is-active')?.dataset.sendTabType;
        if (activeSendTabType === 'MAIN') {
            window.shortLinkKeys.set(linkKey, linkValue);
        } else {
            window.shortLinkAlternativeKeys.set(linkKey, linkValue);
        }
    }

    function delShortLinkKey(linkKey) {
        const activeSendTabType = document.querySelector('button[data-send-tab-type].is-active')?.dataset.sendTabType;
        if (activeSendTabType === 'MAIN') {
            window.shortLinkKeys.delete(linkKey);
        } else {
            window.shortLinkAlternativeKeys.delete(linkKey);
        }
    }

    function loadSendMethodContentSettingLayer(sendMethod) {
        const urlMap = {
            SMS: './mobile_send/layer_sms_content_setting.php',
            FRIENDTALK: './mobile_send/layer_kakao_friendtalk_content_setting.php',
            ALIMTALK: './mobile_send/layer_kakao_alimtalk_content_setting.php',
            MYAPP: './mobile_send/layer_myapp_push_content_setting.php',
        };

        const postData = (typeof recipeDefault !== 'undefined' && recipeDefault)
            ? { recipeContext: JSON.stringify(recipeDefault) }
            : null;

        $.post(urlMap[sendMethod], postData, function (data) {
            $('#layerSendMethodContent').html(data);
            if (cosData && window.GodoCosGuide) {
                window.GodoCosGuide.apply(cosData);
            }
        });
    }

    function getActivatedSendTabType() {
        return document.querySelector('.ncua-tab-button[data-send-tab-type].is-active')?.dataset.sendTabType ?? null;
    }

    function getCampaignMessageType() {
        return document.querySelector('input[name="campaignMessageType"]:checked')?.value;
    }

    function getLinkType() {
        const sendTabType = getActivatedSendTabType();
        switch (sendTabType) {
            case 'MAIN':
                if (getActivatedSendMethod() === 'FRIENDTALK' && getCampaignMessageType() === 'CAROUSEL_FEED') {
                    return `C${carouselState.activeIndex + 1}_`;
                } else {
                    return 'M';
                }
            default:
                return 'alt';
        }
    }

    function generateLinkKey() {
        let linkType = getLinkType();
        if (!linkKeyCounters[linkType]) {
            linkKeyCounters[linkType] = 1;
        }

        return `short_link_${linkType}${linkKeyCounters[linkType]++}`;
    }

    function getActivatedSendMethod() {
        return document.querySelector('input[name="sendMethod"]:checked')?.value ?? "SMS";
    }

    function useSharpReplaceCode() {
        const sharpReplaceCodeSendMethods = ['FRIENDTALK', 'ALIMTALK'];
        const sendMethod = getActivatedSendMethod();
        return sharpReplaceCodeSendMethods.includes(sendMethod) && getActivatedSendTabType() === 'MAIN'
    }

    function generateChipButton(linkKey, linkValue) {
        const currentTabType = getActivatedSendTabType();
        const chipButton = document.createElement('button');
        chipButton.className = 'chip-button';
        chipButton.type = 'button';
        chipButton.setAttribute('data-link-value', linkValue);
        chipButton.setAttribute('data-send-tab-type', currentTabType);
        const buttonText = useSharpReplaceCode() ? `#{${linkKey}}` : `{${linkKey}}`
        chipButton.innerHTML = `
           <span class="chip-button-text">${buttonText}</span>
           <span class="remove-button" data-role="remove-chip">닫기</span>
       `;
        return chipButton;
    }

    function isValidUrl(string) {
        return /^https?:\/\//.test(string);
    }

    function addAdText(messageContainer, previewContainer) {
        const content = '(광고)\n' + messageContainer.getValue() + '\n' + smsRejectText;
        messageContainer.setValue(content);
        previewContainer.setContent(resolveReplaceCode(content));
    }

    function removeAdText(messageContainer, previewContainer) {
        let content = messageContainer.getValue();
        content = content.replace(/^\(광고\)\n?/, '');
        const escapedRejectText = smsRejectText.replace(/[.*+?^${}()|[\]\\]/g, '\\$&');
        if (escapedRejectText) {
            content = content.replace(new RegExp('\\n?' + escapedRejectText, 'g'), '');
        }
        messageContainer.setValue(content);
        previewContainer.setContent(resolveReplaceCode(content));
    }

    /**
     * 대체메시지 사용 가능여부 검증
     */
    function enableAlternative(activatedSendMethod) {
        switch (activatedSendMethod) {
            case 'SMS':
                return false;
            case 'FRIENDTALK':
                if (messageConfig.kakaoFriendTalk.useAlternative !== 'y') {
                    return false;
                }

                const campaignMessageType = document.querySelector('input[name="campaignMessageType"]:checked')?.value;
                if (['CAROUSEL_FEED', 'WIDE_ITEM_LIST'].includes(campaignMessageType)) {
                    return false;
                }
                break;
            case 'ALIMTALK':
                if (messageConfig.kakaoAlimTalk.useAlternative !== 'y') {
                    return false;
                }
                break;
            case 'MYAPP':
                if (messageConfig.myappPush.useAlternative !== 'y') {
                    return false;
                }

                const recipientType = document.querySelector('input[name="recipientType"]:checked')?.value ?? 'ALL'
                const isOnlyAgreed = document.querySelector('input[name="isOnlyAgreed"]')?.checked;
                const myappSendCondition = document.querySelector('input[name="myappSendCondition"]:checked').value;

                if (recipientType === 'ALL' && !isOnlyAgreed && myappSendCondition === 'APP_INSTALLED') {
                    return false;
                }
                break;
        }

        return true;
    }

    function setDefaultPreview(types) {
        types.forEach(function (type) {
            switch (type) {
                case 'TEXT':
                    window.preview.setContent('메시지 내용을 입력하세요.');
                    break;
                case 'FRIENDTALK_IMAGE':
                    window.preview.setImage('<?= PATH_ADMIN_GD_SHARE ?>ncds/image/temp-preview.png');
                    break;
                case 'FRIENDTALK_WIDE_ITEM_LIST':
                    window.preview.setItemList([{ image: '<?= PATH_ADMIN_GD_SHARE ?>ncds/image/temp-preview02.png' }]);
                    break;
                case 'FRIENDTALK_TITLE':
                    window.preview.setTitle('타이틀을 입력해주세요.');
                    break;
                case 'FRIENDTALK_CAROUSEL_TITLE':
                    window.preview.setContentTitle('제목을 입력해주세요.');
                    break;
                case 'ALTERNATIVE_TEXT':
                    window.alternativePreview.setContent('메시지 내용을 입력하세요.');
                    break;
                case 'ALIMTALK_TEXT':
                    window.preview.setContent('알림톡은 메시지를 직접 입력할 수 없습니다.<br>템플릿을 선택해서 불러오세요.');
                    break;
                case 'MYAPP_TITLE':
                    window.preview.setTitle('맛집 BEST');
                    break;
                case 'MYAPP_TEXT':
                    window.preview.setContent('요즘 핫한 파인다이닝');
                    break;
                case 'MYAPP_UNSUBSCRIBE_GUIDE':
                    window.preview.setWithdrawalMethod('수신 거부: 설정> 알림 OFF');
                    break;
            }
        });
    }

    function updateActiveTab(container, tabType) {
        container.querySelectorAll('button[data-send-tab-type]').forEach(btn => {
            btn.classList.toggle('is-active', btn.dataset.sendTabType === tabType);
        });
    }

    function setSendMethodTab(tabType) {
        document.querySelector(`button[data-send-tab-type="${tabType}"]`)?.click();
    }

    function updateTabComponent(container, tabType) {
        container.querySelectorAll('.send-method-component').forEach(el => {
            el.classList.add('display-none');
            const isReplaceCodeComponent = el.classList.contains('js-replace-code');
            const recipientType = document.querySelector('input[name="recipientType"]:checked')?.value;
            const shouldHideReplaceCode = isReplaceCodeComponent && ['EXCEL_UPLOAD', 'DIRECT'].includes(recipientType);

            const targets = el.dataset.target?.split(',') ?? [];
            if (!shouldHideReplaceCode && targets.includes(tabType)) {
                el.classList.remove('display-none');
            }
        });
    }

    function initializeSendMethodSettingSectionElement() {
        document.querySelector('input[name="sendMethod"]:checked')?.dispatchEvent(new Event('change'));
    }

    function isEditing() {
        // 메시지 내용 체크
        const messageContent = document.querySelector('textarea[name="messageContent"]');
        if (messageContent && messageContent.value !== '') return true;

        // 메인/대체 메시지 체크
        const mainContents = document.querySelector('input[name="mainContents"]');
        const alternativeContents = document.querySelector('input[name="alternativeContents"]');
        if (mainContents && mainContents.value !== '') return true;
        if (alternativeContents && alternativeContents.value !== '') return true;

        // 친구톡 캠페인명 체크
        const campaignName = document.querySelector('input[name="friendtalkCampaignName"]');
        if (campaignName && campaignName.value !== '') return true;

        // 마이앱 타이틀 체크
        const myappTitle = document.querySelector('input[name="myappTitle"]');
        if (myappTitle && myappTitle.value !== '') return true;
    }

    // 치환코드 카테고리 목록을 칩 드로워용 구조로 변환한다. (일반/레시피 공용)
    // insertKey 는 _Nd 평탄화(defaultDay)용 — 일반 코드는 defaultDay 가 없어 key 와 동일해지므로 항상 포함해도 안전.
    function buildReplaceCodeCategoryList(categories, prefix = '') {
        return categories.map(category => ({
            ...category,
            variables: MobileMessageReplaceCode.getItemsByMethod(
                MobileMessageReplaceCode[category.value],
                MobileMessageReplaceCode.SendMethod.MANUAL
            ).map(item => ({
                key: `${prefix}{${item.key}}`,
                insertKey: `${prefix}{${item.defaultDay ? item.key.replace(/_Nd$/, `_${item.defaultDay}d`) : item.key}}`,
                label: `${item.name}`
            }))
        }));
    }

    function buildReplaceCodeCategories(prefix = '') {
        return buildReplaceCodeCategoryList([
            { value: 'MEMBER', label: '회원' },
            { value: 'GOODS', label: '상품' },
            { value: 'ORDER', label: '주문' },
            { value: 'PROMOTION', label: '프로모션' },
            { value: 'BOARD', label: '게시판' },
            { value: 'REGULAR', label: '정기결제(배송)' },
            { value: 'PRESENT', label: '선물하기' },
        ], prefix);
    }

    function buildRecipeReplaceCodeCategories(prefix = '') {
        return buildReplaceCodeCategoryList([
            { value: 'CRM_RECIPE', label: '레시피' },
        ], prefix);
    }

    function resolveReplaceCode(content, isMain = true) {
        if (content === null) return content;
        const previewObject = isMain ? window.preview : window.alternativePreview;

        if (previewObject.isCheckboxChecked()) return content;

        return MobileMessageReplaceCode.resolvePreviewContent(content, {
            includeRecipe: true,
            shortLinkKeys: isMain ? window.shortLinkKeys : window.shortLinkAlternativeKeys,
        });
    }
</script>

<!-- 툴팁 스크립트 -->
<script defer type="text/javascript">
    const code = 251219003;
    if (window.GodoCosGuide) {
        window.GodoCosGuide.init({
            apiUrl: <?= json_encode($cosGuideApiUrl) ?>,
            guideCode: code,
        }).then((data) => {
            window.cosData = data;
        });
    }
</script>
