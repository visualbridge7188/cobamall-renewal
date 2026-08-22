<link type="text/css" href="<?= gd_set_browser_cache(PATH_ADMIN_GD_SHARE . 'ncds/css/crm/crm-common.css') ?>" rel="stylesheet"/>
<link type="text/css" href="<?= gd_set_browser_cache(PATH_ADMIN_GD_SHARE . 'ncds/css/crm/message-template-register.css') ?>" rel="stylesheet"/>
<link type="text/css" href="<?= gd_set_browser_cache(PATH_ADMIN_GD_SHARE . 'ncds/css/crm/layer-preview.css') ?>" rel="stylesheet"/>
<link type="text/css" href="<?= gd_set_browser_cache(PATH_ADMIN_GD_SHARE . 'script/slider/slick/slick.css') ?>" rel="stylesheet"/>
<script src="<?= gd_set_browser_cache(PATH_ADMIN_GD_SHARE . 'script/slider/slick/slick.min.js') ?>"></script>
<script defer src="<?= gd_set_browser_cache(PATH_ADMIN_GD_SHARE . 'ncds/script/common/switch.js') ?>"></script>
<script defer src="<?= gd_set_browser_cache(PATH_ADMIN_GD_SHARE . 'ncds/script/common/charcount.js') ?>"></script>
<script defer src="<?= gd_set_browser_cache(PATH_ADMIN_GD_SHARE . 'ncds/script/ncds-multi-select/ncds-multi-select.js') ?>"></script>
<script defer src="<?= gd_set_browser_cache(PATH_ADMIN_GD_SHARE . 'ncds/script/common/ncds-jquery-validator.js') ?>"></script>
<script src="<?= gd_set_browser_cache(PATH_ADMIN_GD_SHARE . 'ncds/script/common/unsaved-changes-guard.js') ?>"></script>
<script defer src="<?= gd_set_browser_cache(PATH_ADMIN_GD_SHARE . 'ncds/script/common/scroll-sticky.js') ?>"></script>
<script src="<?= gd_set_browser_cache(PATH_ADMIN_GD_SHARE . 'script/crm/mobile_message_replace_code.js') ?>"></script>
<script defer src="<?= gd_set_browser_cache(PATH_ADMIN_GD_SHARE . 'ncds/script/common/godo-ui-module/godo-ui-module.js') ?>"></script>
<script defer src="<?= gd_set_browser_cache(PATH_ADMIN_GD_SHARE . 'ncds/script/common/godo-ui-module/crm-preview/crm-preview-loader.js') ?>"></script>
<script defer src="<?= gd_set_browser_cache(PATH_ADMIN_GD_SHARE . 'ncds/script/common/godo-ui-module/message-input/message-input.js') ?>"></script>
<script defer src="<?= gd_set_browser_cache(PATH_ADMIN_GD_SHARE . 'ncds/script/common/godo-ui-module/chip-selector/chip-selector.js') ?>"></script>

<article class="ncua-content message-template-register">
    <form name="templateRegisterForm" id="templateRegisterForm" action="" method="post">
        <input type="hidden" name="mode" value="<?= $request->getMode() ?? 'register' ?>"/>
        <input type="hidden" name="templateCode" value="<?= $request->getTemplateCode() ?? '' ?>"/>
        <header class="ncua-page-header page-header js-affix affix-top" style="width: 1017px;">
            <h3>
                <button type="button"
                        class="ncua-btn ncua-btn--md only-icon ncua-btn--secondary-gray ncua-history-back js-btn-back">
                    뒤로가기
                </button>
                템플릿 <?= ($request->getMode() === 'modify') ? '수정' : '등록'; ?>
            </h3>
            <div class="ncua-page-header__actions">
                <button type="button" class="ncua-btn ncua-btn--md ncua-btn--secondary-gray js-btn-list">목록</button>
                <button type="submit" class="ncua-btn ncua-btn--md ncua-btn--primary">저장</button>
            </div>
        </header>

        <?php include $basicConfig; ?>

        <div class="ncua-split-layout">
            <div class="section-container">
                <!-- 템플릿 내용: 발송수단별 분리 -->
                <div data-show-when='{"sendMethod": ["sms"]}'
                     style="<?= $request->getSendMethod() !== 'sms' ? 'display:none' : '' ?>">
                    <?php include $templateContentsSms; ?>
                </div>
                <div data-show-when='{"sendMethod": ["kakao"]}'
                     style="<?= $request->getSendMethod() !== 'kakao' ? 'display:none' : '' ?>">
                    <?php include $templateContentsKakao; ?>
                </div>
                <div data-show-when='{"sendMethod": ["myapp"]}'
                     style="<?= $request->getSendMethod() !== 'myapp' ? 'display:none' : '' ?>">
                    <?php include $templateContentsMyapp; ?>
                </div>
            </div>
            <aside class="ncua-panel">
                <!-- 알림 메시지 미리보기 -->
                <?php include $previewMessage; ?>
            </aside>
        </div>
    </form>
</article>

<script>
    // 초기 데이터 (수정/복제 모드용)
    const initialData = {
        mode: <?= json_encode($request->getMode() ?? 'register') ?>,
        templateCode: <?= json_encode($request->getTemplateCode() ?? '') ?>,
        sendMethod: <?= json_encode($request->getSendMethod() ?? 'sms') ?>,
        // SMS
        smsContent: <?= json_encode($response->getSms()?->getContents() ?? '') ?>,
        // Cloud
        cloudItems: <?= json_encode($response->getCloud()?->getTemplateItems() ?? []) ?>,
        cloudButtons: <?= json_encode($response->getCloud()?->getTemplateButtons() ?? []) ?>,
        cloudImageUrl: <?= json_encode($response->getCloud()?->getTemplateImageUrl() ?? null) ?>,
        cloudImageName: <?= json_encode($response->getCloud()?->getTemplateImageName() ?? null) ?>,
        cloudItemHighlightImageUrl: <?= json_encode(($response->getCloud()?->getTemplateItemHighlight() ?? [])['imageUrl'] ?? null) ?>,
        cloudContent: <?= json_encode($response->getCloud()?->getTemplateContent() ?? $response->getBizm()?->getTemplateContent() ?? '') ?>,
        // Bizm
        bizmButtons: <?= json_encode(json_decode($response->getBizm()?->getTemplateButton() ?? '[]', true) ?: []) ?>,
        // MyApp
        myappImage: <?= json_encode($response->getMyapp()?->getPushImage() ?? null) ?>,
        myappUrl: <?= json_encode($response->getMyapp()?->getPushUrl() ?? null) ?>,
        myappContent: <?= json_encode($response->getMyapp()?->getPushContent() ?? '') ?>,
    };
    
    // 치환코드 카테고리 (MobileMessageReplaceCode 기반)
    const replaceCodeCategories = [
        { value: 'MEMBER', label: '회원' },
        { value: 'GOODS', label: '상품' },
        { value: 'ORDER', label: '주문' },
        { value: 'PROMOTION', label: '프로모션' },
        { value: 'BOARD', label: '게시판' },
        { value: 'REGULAR', label: '정기결제(배송)' },
        { value: 'PRESENT', label: '선물하기' },
    ];

    const buildReplaceCodeCategories = (prefix) => {
        return replaceCodeCategories.map(cat => ({
            value: cat.value,
            label: cat.label,
            variables: Object.entries(MobileMessageReplaceCode[cat.value]).map(([key, obj]) => ({
                key: `${prefix}{${key}}`,
                label: obj.name
            }))
        }));
    };

    const getProvider = () =>
        document.querySelector('input[name="provider"]:checked')?.value
        ?? document.querySelector('input[type="hidden"][name="provider"]')?.value
        ?? 'cloud';

    const onReady = () => {
        try {
            /**
             * 사용 가능한 변수 접기/펼치기 토글
             */
            document.querySelectorAll('.js-variable-toggle').forEach(btn => {
                btn.addEventListener('click', function () {
                    const target = this.dataset.target;
                    const wrapper = this.closest('.variable-selector-wrap');
                    const chipSelectors = wrapper.querySelectorAll('.chip-selector');
                    const isHidden = chipSelectors[0]?.style.display === 'none';

                    chipSelectors.forEach(selector => {
                        // 기존 display 상태를 고려하여 토글
                        if (isHidden) {
                            // 펼치기: 원래 display 값 복원
                            const config = selector.dataset.variableConfig;
                            if (config === 'sms' || config === 'myapp') {
                                selector.style.display = 'flex';
                            } else if (config === 'kakao_cloud' || config === 'kakao_bizm') {
                                // provider에 따라 표시 여부 결정
                                const provider = getProvider();
                                if ((config === 'kakao_cloud' && provider === 'cloud') ||
                                    (config === 'kakao_bizm' && provider === 'bizm')) {
                                    selector.style.display = 'flex';
                                }
                            }
                        } else {
                            selector.style.display = 'none';
                        }
                    });

                    this.textContent = isHidden ? '▲' : '▼';
                });
            });

            /**
             * 조건부 컴포넌트 표시 관리자
             */
            const createConditionalDisplayManager = () => {
                // 현재 값 가져오기 (제공사, 발송 수단, 템플릿 타입)
                const getCurrentValues = () => {
                    const provider = getProvider();
                    const sendMethod = document.querySelector('input[name="sendMethod"]:checked')?.value;
                    if (sendMethod === 'sms') return {provider, sendMethod, templateType: null};

                    const container = document.querySelector('[data-component="template-type-radio"]');
                    const radio = document.querySelector('input[name="templateType"]:checked');
                    const isVisible = container && window.getComputedStyle(container).display !== 'none';
                    const templateType = (isVisible && radio) ? radio.value : null;
                    return {provider, sendMethod, templateType};
                };

                // 조건부 표시 조건 파싱
                const parseCondition = (str) => {
                    if (!str) return null;
                    try {
                        return JSON.parse(str);
                    } catch {
                        return null;
                    }
                };

                // 조건부 표시 여부 확인
                const shouldShow = (condition, {provider, sendMethod, templateType}) => {
                    if (!condition) return true;

                    const {
                        provider: condProvider,
                        sendMethod: condSendMethod,
                        templateType: condTemplateType
                    } = condition;
                    const currentType = templateType ?? null;

                    if (condProvider && !condProvider.includes(provider)) return false;
                    if (condSendMethod && !condSendMethod.includes(sendMethod)) return false;
                    if (!condTemplateType) return true;

                    const hasNull = condTemplateType.includes(null);
                    if (hasNull && currentType === null) return true;
                    if (currentType !== null && condTemplateType.includes(currentType)) return true;
                    return false;
                };

                // 조건부 표시 업데이트
                const updateVisibility = () => {
                    const currentValues = getCurrentValues();
                    document.querySelectorAll('[data-show-when]').forEach(element => {
                        const condition = parseCondition(element.getAttribute('data-show-when'));
                        const isVisible = shouldShow(condition, currentValues);

                        element.style.display = isVisible ? '' : 'none';

                        // 숨겨진 요소의 input/textarea/select를 disabled 처리
                        element.querySelectorAll('input, textarea, select').forEach(field => {
                            // 체크박스로 제어되는 textarea는 제외
                            if (field.id === 'channelAddTextarea' || field.id === 'extraInfoTextarea') return;
                            // 수정/복제 모드에서 발송수단, 제공사는 disabled 유지
                            if (field.name === 'sendMethod' || field.name === 'provider') return;
                            field.disabled = !isVisible;
                        });
                    });

                    // data-hide-when: 조건 매칭 시 숨김
                    document.querySelectorAll('[data-hide-when]').forEach(element => {
                        const condition = parseCondition(element.getAttribute('data-hide-when'));
                        element.style.display = shouldShow(condition, currentValues) ? 'none' : '';
                    });

                    // 같은 name을 가진 라디오가 여러 행에 걸쳐 있을 때, 보이는 행에 checked가 없으면 PHP가 렌더링한 checked 속성 기준으로 복원
                    ['templateCategory', 'templateBasicType'].forEach(name => {
                        const visibleRadios = Array.from(document.querySelectorAll(`input[name="${name}"]`))
                            .filter(r => !r.disabled && r.closest('tr')?.style.display !== 'none');
                        if (visibleRadios.length > 0 && !visibleRadios.some(r => r.checked)) {
                            const htmlChecked = visibleRadios.find(r => r.hasAttribute('checked'));
                            (htmlChecked || visibleRadios[0]).checked = true;
                        }
                    });
                };

                // 변수 설정 전환 함수
                const updateVariableConfig = () => {
                    const sendMethod = document.querySelector('input[name="sendMethod"]:checked')?.value || 'sms';
                    const provider = getProvider();

                    let configKey = sendMethod;
                    if (sendMethod === 'kakao') {
                        configKey = 'kakao_' + provider;
                    }

                    // 모든 변수 설정 숨기고 해당 설정만 표시
                    document.querySelectorAll('[data-variable-config]').forEach(el => {
                        if (el.dataset.variableConfig === configKey) {
                            el.style.display = 'flex';
                            // 첫 번째 카테고리 표시 초기화
                            const select = el.querySelector('.js-variable-category-select');
                            if (select) {
                                const initialValue = select.value;
                                el.querySelectorAll('.chip-button-wrap').forEach(wrap => {
                                    wrap.style.display = wrap.dataset.categoryGroup === initialValue ? 'flex' : 'none';
                                });
                            }
                        } else {
                            el.style.display = 'none';
                        }
                    });
                };

                // 구분(templateBasicType) provider별 필터링
                const updateBasicTypeVisibility = () => {
                    const provider = getProvider();
                    const container = document.querySelector('.js-template-basic-types');
                    if (!container) return;

                    let firstVisible = null;
                    container.querySelectorAll('label[data-providers]').forEach(label => {
                        const providers = JSON.parse(label.dataset.providers || '[]');
                        const isVisible = providers.includes(provider);
                        label.style.display = isVisible ? '' : 'none';
                        if (isVisible && !firstVisible) {
                            firstVisible = label;
                        }
                    });

                    // 현재 선택된 라디오가 숨겨진 경우 첫 번째 보이는 항목 선택
                    const checkedRadio = container.querySelector('input[name="templateBasicType"]:checked');
                    if (checkedRadio && checkedRadio.closest('label').style.display === 'none' && firstVisible) {
                        firstVisible.querySelector('input[type="radio"]').checked = true;
                    }
                };

                updateVisibility();
                updateVariableConfig();
                updateBasicTypeVisibility();
                document.addEventListener('change', (e) => {
                    if (e.target.matches('input[name="sendMethod"]') || e.target.matches('input[name="provider"]')) {
                        updateVisibility();
                        updateVariableConfig();
                        updateBasicTypeVisibility();
                        // Validator 에러 초기화
                        if (typeof validatorInstance !== 'undefined' && validatorInstance) {
                            validatorInstance.resetForm();
                        }
                    }
                });

                return {updateVisibility};
            };

            const conditionalDisplayManager = createConditionalDisplayManager();

            /**
             * 글자수 카운트 기능 초기화: char 모드만 사용
             */
            const charCountManager = createCharCountManager({
                targetClasses: [
                    'ncua-template__title',
                    'template-type-title',
                    'template-type-sub-title',
                    'template-content-myapp-title',
                    'body-content-textarea',
                    'body-content-textarea-myapp',
                    'item-list-title-input',
                    'item-list-content-input',
                    'item-summary-title-input',
                    'item-summary-content-input',
                    'template-header-input',
                    'item-highlight-title-input',
                    'item-highlight-description-input',
                    'template-button-name',
                    'delivery-button-name',
                    'withdrawal-button-name',
                    'page-link-button-input',
                    'delivery-button-name-input',
                ],
            });
            charCountManager.init();

            /**
             * 글자 수 카운트 기능 초기화: bytes & char 모드 사용
             */
            const charCountBytesManager = createCharCountManager({
                targetClasses: ['message-content-textarea'],
                useBytes: true,           // bytes 모드 활성화
                bytesThreshold: 90,       // 90bytes 기준으로 SMS/LMS 구분
                bytesMaxLength: 2000,      // bytes 모드 최대 길이
            });
            charCountBytesManager.init();

            /**
             * SMS 포인트 차감
             */
            const SMS_BYTES_THRESHOLD = 90;
            const smsByteModeHint = document.querySelector('.sms-byte-mode-hint');
            const smsByteCountEl = document.querySelector(
                '.ncua-input__text-count-text-count[data-charcount-text="templateMessageContent"]'
            );
            const updateSmsPointHint = () => {
                if (!smsByteModeHint || !smsByteCountEl) return;

                const currentBytes = parseInt((smsByteCountEl.textContent || '0').trim(), 10);
                const isLms = Number.isFinite(currentBytes) && currentBytes > SMS_BYTES_THRESHOLD;

                smsByteModeHint.textContent = isLms
                    ? 'LMS 건당 3포인트 차감'
                    : 'SMS 건당 1포인트 차감';
            };

            // 초기 렌더 시 0bytes 기준 문구 반영
            updateSmsPointHint();

            // charcount.js가 input 이벤트에서 카운트를 먼저 갱신하므로,
            // 동일 이벤트에 문구 업데이트를 뒤에 연결해 최신 카운트를 반영한다.
            document.addEventListener('input', (event) => {
                const target = event.target;
                if (!(target instanceof HTMLTextAreaElement)) return;
                if (!target.classList.contains('message-content-textarea')) return;
                updateSmsPointHint();
            });

            /**
             * 발송 수단별 필드 값 저장소
             */
            const fieldValueStore = {
                sms: {},
                kakao: {},
                myapp: {}
            };

            // 발송 수단별 저장할 필드 셀렉터
            const fieldSelectors = {
                sms: [
                    'textarea[name="smsMessageInput"]'
                ],
                kakao: [
                    'textarea[name="kakaoMessageInput"]',
                    '#extraInfoTextarea',
                    'input[name="templateTypeTitle"]',
                    'input[name="templateTypeSubTitle"]',
                    'input[name="templateHeader"]',
                    'input[name="templateItemHighlightTitle"]',
                    'input[name="templateItemHighlightDescription"]',
                    'input[name="templateItemSummaryTitle"]',
                    'input[name="templateItemSummaryContent"]'
                ],
                myapp: [
                    'input[name="templateContentMyappTitle"]',
                    'textarea[name="myappMessageInput"]',
                    'input[name="templateWithdrawalMethod"]',
                    'input[name="templateUrl"]'
                ]
            };

            // 발송 수단별 라디오 선택 값 저장소
            const radioValueStore = { sms: {}, kakao_cloud: {}, kakao_bizm: {}, myapp: {} };

            const getRadioStoreKey = (sendMethod) => {
                if (sendMethod === 'kakao') {
                    return 'kakao_' + getProvider();
                }
                return sendMethod;
            };

            // 필드 값 저장
            const saveFieldValues = (sendMethod) => {
                const selectors = fieldSelectors[sendMethod] || [];
                const values = {};
                selectors.forEach(selector => {
                    const el = document.querySelector(selector);
                    if (el) values[selector] = el.value;
                });
                fieldValueStore[sendMethod] = values;

                // 라디오 선택 값 저장
                const storeKey = getRadioStoreKey(sendMethod);
                if (sendMethod === 'kakao') {
                    const checkedBasicType = document.querySelector('input[name="templateBasicType"]:checked');
                    if (checkedBasicType) radioValueStore[storeKey].templateBasicType = checkedBasicType.value;
                } else {
                    const checkedCategory = document.querySelector(`tr[data-show-when*='"${sendMethod}"'] input[name="templateCategory"]:checked`);
                    if (checkedCategory) radioValueStore[storeKey].templateCategory = checkedCategory.value;
                }
            };

            // 필드 값 복원
            const restoreFieldValues = (sendMethod) => {
                const values = fieldValueStore[sendMethod] || {};
                Object.entries(values).forEach(([selector, value]) => {
                    const el = document.querySelector(selector);
                    if (el && value !== undefined) {
                        el.value = value;
                        el.dispatchEvent(new Event('input', {bubbles: true}));
                    }
                });

                // 라디오 선택 값 복원
                const storeKey = getRadioStoreKey(sendMethod);
                const saved = radioValueStore[storeKey] || {};
                if (sendMethod === 'kakao' && saved.templateBasicType) {
                    const radio = document.querySelector(`input[name="templateBasicType"][value="${saved.templateBasicType}"]`);
                    if (radio && !radio.disabled) radio.checked = true;
                } else if (saved.templateCategory) {
                    const radio = document.querySelector(`tr[data-show-when*='"${sendMethod}"'] input[name="templateCategory"][value="${saved.templateCategory}"]`);
                    if (radio) radio.checked = true;
                }
            };

            let previousSendMethod = document.querySelector('input[name="sendMethod"]:checked')?.value || 'sms';

            const updateTemplateTitleLimit = (sendMethod, provider) => {
                const input = document.querySelector('.js-template-title');
                if (!input) return;

                const maxLengthMap = {sms: 10, myapp: 10, kakao: provider === 'bizm' ? 30 : null};
                const max = maxLengthMap[sendMethod] ?? null;
                const label = document.querySelector('.js-template-title-max-length');

                if (!max) {
                    input.removeAttribute('maxlength');
                    if (label) label.textContent = '';
                    return;
                }

                input.setAttribute('maxlength', max);
                if (label) label.textContent = '/' + max;

                if (input.value.length > max) {
                    input.value = input.value.substring(0, max);
                    const charcount = document.querySelector('[data-charcount-text="templateTitle"] output');
                    if (charcount) charcount.textContent = max;
                }
            };

            /**
             * 발송 수단 라디오 버튼 변경 시 글자 수 카운트 모드 변경 및 조건부 표시 업데이트
             */
            const sendMethodRadios = document.querySelectorAll('input[name="sendMethod"]');
            sendMethodRadios.forEach((radio) => {
                radio.addEventListener('change', (event) => {
                    const selectedValue = event.target.value;

                    // 이전 발송 수단의 필드 값 저장
                    if (previousSendMethod) {
                        saveFieldValues(previousSendMethod);
                    }

                    // 현재 발송 수단 업데이트
                    previousSendMethod = selectedValue;

                    // 글자 수 카운트 모드 변경 (먼저 호출하여 maxLength 설정)
                    if (selectedValue === 'sms') {
                        charCountBytesManager.useBytes({bytesMaxLength: 2000});
                    } else {
                        charCountBytesManager.useChars({charMaxLength: 300});
                    }

                    // 조건부 표시 업데이트 (필드가 보이도록 먼저 처리)
                    if (conditionalDisplayManager) {
                        conditionalDisplayManager.updateVisibility();
                    }

                    // 템플릿 제목 maxlength 동적 변경
                    updateTemplateTitleLimit(selectedValue, getProvider());

                    // 새 발송 수단의 저장된 필드 값 복원
                    restoreFieldValues(selectedValue);
                    
                    // 미리보기 업데이트
                    if (templatePreview) {
                        const sendTypeMap = {
                            'sms': 'SMS',
                            'kakao': 'ALIMTALK',
                            'myapp': 'MYAPP'
                        };
                        templatePreview.reset();
                        templatePreview.setSendType(sendTypeMap[selectedValue] || 'SMS').render();

                        if (initialData.mode === 'register') {
                            // 등록 모드: 새 발송수단에 맞는 샘플 데이터 표시
                            applySampleData(selectedValue);
                        } else {
                            // 수정/복제 모드: DOM에 남아있는 입력값을 미리보기에 재반영
                            previewManager.refreshAll();
                            refreshMessageInputPreviews();
                        }
                    }

                    // SMS MessageInput hintText 재설정
                    if (selectedValue === 'sms') {
                        updateSmsMessageHint(smsMessageInput.getValue());
                    }
                });
            });

            /**
             * 제공사 라디오 버튼 변경 시 조건부 표시 업데이트
             */
            const providerRadios = document.querySelectorAll('input[name="provider"]');
            providerRadios.forEach((radio) => {
                radio.addEventListener('change', (event) => {
                    // 조건부 표시 업데이트
                    if (conditionalDisplayManager) {
                        conditionalDisplayManager.updateVisibility();
                    }
                    // 템플릿 제목 maxlength 동적 변경
                    updateTemplateTitleLimit(previousSendMethod, event.target.value);
                });
            });

            /**
             * 폼 초기화 함수
             */
            const resetTemplateForm = () => {
                const form = document.getElementById('templateRegisterForm');
                if (!form) return;

                // 초기화 제외 대상: sendMethod가 오직 'sms' 또는 'myapp'만 포함하는 요소
                const excludeContainers = Array.from(form.querySelectorAll('[data-show-when]')).filter(el => {
                    try {
                        const {sendMethod} = JSON.parse(el.getAttribute('data-show-when'));
                        return sendMethod?.length === 1 && (sendMethod[0] === 'sms' || sendMethod[0] === 'myapp');
                    } catch {
                        return false;
                    }
                });

                // 요소가 제외 컨테이너 안에 있는지 확인
                const isInExcludeContainer = (element) => {
                    return excludeContainers.some(container => container.contains(element));
                }

                // 텍스트 입력 필드 초기화 (템플릿 제목 제외, SMS/MyApp 탭 요소 제외)
                form.querySelectorAll('input[type="text"]:not([name="templateTitle"]), textarea').forEach(input => {
                    if (input.id !== 'channelAddTextarea' && !isInExcludeContainer(input)) {
                        input.value = '';
                        input.dispatchEvent(new Event('input', {bubbles: true}));
                    }
                });

                // 체크박스 초기화 (SMS/MyApp 탭 요소 제외)
                form.querySelectorAll('input[type="checkbox"]').forEach(checkbox => {
                    if (!checkbox.disabled && !isInExcludeContainer(checkbox)) {
                        // 기본값이 checked인 체크박스 목록
                        const defaultChecked = ['extraInfoCheckbox', 'channelAddCheckbox'];
                        checkbox.checked = defaultChecked.includes(checkbox.id);
                        checkbox.dispatchEvent(new Event('change', {bubbles: true}));
                    }
                });

                // 이미지 파일 입력 초기화 (myapp 탭 요소 제외)
                [topImageFileInput, itemHighlightImageFileInput].forEach(input => {
                    if (typeof input !== 'undefined' && input) input.clearFiles();
                });

                // 테이블 초기화 헬퍼
                const clearTable = (tableId) => {
                    const tbody = document.getElementById(tableId);
                    if (!tbody) return;

                    tbody.querySelectorAll('tr:not(.empty-row)').forEach(row => {
                        if (typeof $ !== 'undefined') {
                            row.querySelectorAll('input').forEach(input => $(input).rules('remove'));
                        }
                        row.remove();
                    });

                    const emptyRow = tbody.querySelector('.empty-row');
                    if (emptyRow) emptyRow.style.display = '';
                };

                // 버튼 테이블 초기화 (페이지 링크, 배송 조회)
                clearTable('pageLinkTableBody');
                clearTable('deliveryTableBody');

                // 추가 버튼 활성화
                ['addPageLinkButton', 'addDeliveryButton'].forEach(id => {
                    const btn = document.getElementById(id);
                    if (btn) btn.disabled = false;
                });

                // 아이템 리스트 초기화 (2개 행만 남기고 나머지 삭제, 입력값 초기화)
                const itemListTbody = document.querySelector('[data-component="item-list-tbody"]');
                if (!itemListTbody) return;
                itemListTbody.querySelectorAll('tr').forEach((row, index) => {
                    if (index < 2) {
                        row.querySelectorAll('input[type="text"]').forEach(input => {
                            input.value = '';
                            input.dispatchEvent(new Event('input', {bubbles: true}));
                        });
                    } else {
                        row.remove();
                    }
                });
            };

            /**
             * SMS 바이트 수에 따라 SMS/LMS 포인트 차감 힌트 텍스트 변경
             */
            const updateSmsMessageHint = (value) => {
                const currentBytes = smsMessageInput.calculateLength(value);
                if (currentBytes > 90) {
                    smsMessageInput.setHint('LMS 건당 3포인트 차감');
                } else if (currentBytes > 0) {
                    smsMessageInput.setHint('SMS 건당 1포인트 차감');
                } else {
                    smsMessageInput.setHint('');
                }
            };

            /**
             * MessageInput / ChipSelector / CrmPreview 초기화
             */
            const smsMessageInput = GodoUIModule.render({
                type: 'MessageInput',
                target: '#sms-message-input-container',
                name: 'smsMessageInput',
                placeholder: '메시지 내용을 작성해주세요.',
                maxLength: 2000,
                useBytes: true,
                bytesThreshold: 90,
                hintText: 'SMS 건당 1포인트 차감',
                initialValue: initialData.smsContent || '',
                allowEmoji: false,
                onInput: function(value) {
                    if (templatePreview) {
                        let displayValue = value || '';
                        if (isVariableConvertEnabled() && displayValue) {
                            displayValue = convertVariablesToDefaults(displayValue);
                        }
                        templatePreview.setContent(displayValue);
                    }
                    
                    updateSmsMessageHint(value);
                }
            });

            // 수정/복제 모드: initialValue 기준으로 SMS/LMS 힌트 초기화
            updateSmsMessageHint(initialData.smsContent || '');

            const smsVariableSelector = GodoUIModule.render({
                type: 'ChipSelector',
                target: '#sms-variable-selector-container',
                title: '사용 가능한 변수',
                name: 'smsVariableSelector',
                tooltipSeq: '004',
                categories: buildReplaceCodeCategories(''),
                onSelect: function(variable) {
                    smsMessageInput.insertText(variable);
                }
            });

            const kakaoVariableSelector = GodoUIModule.render({
                type: 'ChipSelector',
                target: '#kakao-variable-selector-container',
                title: '사용 가능한 변수',
                name: 'kakaoVariableSelector',
                tooltipSeq: '004',
                categories: buildReplaceCodeCategories('#'),
                onSelect: function(variable) {
                    kakaoMessageInput.insertText(variable);
                }
            });

            const myappVariableSelector = GodoUIModule.render({
                type: 'ChipSelector',
                target: '#myapp-variable-selector-container',
                title: '사용 가능한 변수',
                name: 'myappVariableSelector',
                tooltipSeq: '004',
                categories: buildReplaceCodeCategories(''),
                onSelect: function(variable) {
                    myappMessageInput.insertText(variable);
                }
            });

            const kakaoMessageInput = GodoUIModule.render({
                type: 'MessageInput',
                target: '#kakao-message-input-container',
                name: 'kakaoMessageInput',
                placeholder: '템플릿 본문을 작성해주세요.',
                maxLength: 1000,
                useBytes: false,
                initialValue: initialData.cloudContent || '',
                onInput: function(value) {
                    if (templatePreview) {
                        let displayValue = value || '';
                        if (isVariableConvertEnabled() && displayValue) {
                            displayValue = convertVariablesToDefaults(displayValue);
                        }
                        templatePreview.setContent(displayValue);
                    }
                }
            });

            const myappMessageInput = GodoUIModule.render({
                type: 'MessageInput',
                target: '#myapp-message-input-container',
                name: 'myappMessageInput',
                placeholder: '요즘 핫한 파인다이닝',
                maxLength: 300,
                useBytes: false,
                initialValue: initialData.myappContent || '',
                onInput: function(value) {
                    if (templatePreview) {
                        let displayValue = value || '';
                        if (isVariableConvertEnabled() && displayValue) {
                            displayValue = convertVariablesToDefaults(displayValue);
                        }
                        templatePreview.setContent(displayValue);
                    }
                }
            });

            // MessageInput wrapper에 data-show-hint-text 속성 추가 (jQuery Validator 힌트 표시용)
            document.querySelectorAll('#sms-message-input-container .ncua-input, #kakao-message-input-container .ncua-input, #myapp-message-input-container .ncua-input')
                .forEach(el => el.setAttribute('data-show-hint-text', 'true'));

            let templatePreview = null;
            window.addEventListener('crmPreviewLoaded', function() {
                const currentSendMethod = document.querySelector('input[name="sendMethod"]:checked')?.value || 'sms';
                const sendTypeMap = { 'sms': 'SMS', 'kakao': 'ALIMTALK', 'myapp': 'MYAPP' };

                templatePreview = GodoUIModule.render({
                    type: 'MessagePreview',
                    target: '#template-preview-container',
                    sendType: sendTypeMap[currentSendMethod] || 'SMS',
                    checkboxText: '변수로 변환해서 보기',
                    hintText: ''
                });

                if (initialData.mode === 'register') {
                    // 등록 모드: 샘플 데이터 표시
                    applySampleData(currentSendMethod);
                } else {
                    // 수정/복제 모드: 초기 데이터 미리보기 반영
                    previewManager.refreshAll();
                    refreshMessageInputPreviews();

                    // 초기 이미지 미리보기 반영
                    if (initialData.cloudImageUrl) {
                        previewManager.setImagePreview('templateTopImageInput', initialData.cloudImageUrl);
                    }
                    if (initialData.cloudItemHighlightImageUrl) {
                        previewManager.setImagePreview('templateItemHighlightImageInput', initialData.cloudItemHighlightImageUrl);
                    }
                    if (initialData.myappImage) {
                        previewManager.setImagePreview('templateWithdrawalImageInput', initialData.myappImage);
                    }
                }

                // 등록/수정 모두: 체크박스-미리보기 연동 초기화 (templatePreview 초기화 이후 실행)
                initCheckboxControl('extraInfoCheckbox', 'extraInfoTextarea', 'setExtraInfo');
                initCheckboxControl('channelAddCheckbox', null, 'setChannelMessage', '채널 추가하고 이 채널의 광고와 마케팅 메시지 등을 카카오톡으로 받기');

                // 채널 추가 체크박스 변경 시 버튼 미리보기 동기화 (채널 추가 버튼 표시/숨김)
                document.getElementById('channelAddCheckbox')?.addEventListener('change', () => previewManager.syncButtons());
            });

            /**
             * 등록 모드 미리보기 샘플 데이터
             */
            const sampleAlimTalkImage = '<?= PATH_ADMIN_GD_SHARE ?>ncds/image/img-alim-talk.png';

            const sampleDataMap = {
                sms: {
                    content: '메시지 내용을 작성해 주세요.',
                },
                kakao: {
                    NONE: {
                        content: '템플릿 본문을 작성해주세요.',
                        channelMessage: '채널 추가하고 이 채널의 마케팅 메시지 등을 카카오톡으로 받기',
                        buttons: [{ text: '채널 추가', type: 'add-channel' }],
                    },
                    TEXT: {
                        emTitle: '강조표기형 title',
                        emSubTitle: '강조표기형 subTitle',
                        content: '템플릿 본문을 작성해주세요.',
                        channelMessage: '채널 추가하고 이 채널의 마케팅 메시지 등을 카카오톡으로 받기',
                        buttons: [{ text: '채널 추가', type: 'add-channel' }],
                    },
                    IMAGE: {
                        image: sampleAlimTalkImage,
                        content: '템플릿 본문을 작성해주세요.',
                        channelMessage: '채널 추가하고 이 채널의 마케팅 메시지 등을 카카오톡으로 받기',
                        buttons: [{ text: '채널 추가', type: 'add-channel' }],
                    },
                    ITEM_LIST: {
                        image: sampleAlimTalkImage,
                        listHeader: '헤더',
                        highlightImage: sampleAlimTalkImage,
                        highlightTitle: '아이템 하이라이트 제목',
                        highlightDesc: '아이템 하이라이트 내용',
                        content: '템플릿 본문을 작성해주세요.',
                        channelMessage: '채널 추가하고 이 채널의 마케팅 메시지 등을 카카오톡으로 받기',
                        buttons: [{ text: '채널 추가', type: 'add-channel' }],
                    },
                },
                myapp: {
                    title: '맛집 BEST 3',
                    content: '(광고) 요즘 핫한 파인다이닝',
                    withdrawalMethod: '수신거부: 설정 > 알림 OFF',
                },
            };

            const applySampleData = (sendMethod, templateType) => {
                if (!templatePreview || initialData.mode !== 'register') return;

                templatePreview.reset();

                if (sendMethod === 'sms') {
                    const sample = sampleDataMap.sms;
                    templatePreview.setContent(sample.content);
                } else if (sendMethod === 'kakao') {
                    const type = templateType || document.querySelector('input[name="templateType"]:checked')?.value || 'NONE';
                    const sample = sampleDataMap.kakao[type] || sampleDataMap.kakao.NONE;

                    if (sample.image) templatePreview.setImage(sample.image);
                    if (sample.emTitle) templatePreview.setEmTitle(sample.emTitle);
                    if (sample.emSubTitle) templatePreview.setEmSubTitle(sample.emSubTitle);
                    if (sample.listHeader) templatePreview.setListHeader(sample.listHeader);
                    if (sample.highlightImage) templatePreview.setHighlightImage(sample.highlightImage);
                    if (sample.highlightTitle) templatePreview.setHighlightTitle(sample.highlightTitle);
                    if (sample.highlightDesc) templatePreview.setHighlightDesc(sample.highlightDesc);
                    if (sample.content) templatePreview.setContent(sample.content);
                    if (sample.channelMessage) templatePreview.setChannelMessage(sample.channelMessage);
                    if (sample.buttons) templatePreview.setButtons(sample.buttons);
                } else if (sendMethod === 'myapp') {
                    const sample = sampleDataMap.myapp;
                    templatePreview.setTitle(sample.title);
                    templatePreview.setContent(sample.content);
                    templatePreview.setWithdrawalMethod(sample.withdrawalMethod);
                }
            };

            /**
             * 변수 기본값 매핑 (미리보기 변환용)
             */
            const variableDefaults = {};
            [
                MobileMessageReplaceCode.MEMBER,
                MobileMessageReplaceCode.GOODS,
                MobileMessageReplaceCode.ORDER,
                MobileMessageReplaceCode.PROMOTION,
                MobileMessageReplaceCode.BOARD,
                MobileMessageReplaceCode.REGULAR,
                MobileMessageReplaceCode.PRESENT,
            ].forEach(category => {
                Object.entries(category).forEach(([key, obj]) => {
                    if (variableDefaults[`{${key}}`] === undefined) {
                        variableDefaults[`{${key}}`] = obj.default;
                    }
                });
            });
            // MobileMessageReplaceCode에 없는 키 추가
            Object.assign(variableDefaults, {
                '{name}': '김고도',
                '{orderGoodsNo}': 'ORD20251113001',
                '{short_link_M{linkNum}}': '쇼핑몰도메인/_s/a1B2c3D',
                '{short_link_alt{linkNum}}': '쇼핑몰도메인/_s/a1B2c3D',
            });

            // 카카오용 #{} 형식 기본값 생성
            const kakaoVariableDefaults = {};
            Object.keys(variableDefaults).forEach(key => {
                const kakaoKey = key.replace('{', '#{');
                kakaoVariableDefaults[kakaoKey] = variableDefaults[key];
            });

            // 변수를 기본값으로 변환하는 함수
            const convertVariablesToDefaults = (text) => {
                if (!text) return text;
                let result = text;
                // 카카오 변수 (#{}) 먼저 변환
                Object.keys(kakaoVariableDefaults).forEach(variable => {
                    result = result.split(variable).join(kakaoVariableDefaults[variable]);
                });
                // SMS/Myapp 변수 ({}) 변환
                Object.keys(variableDefaults).forEach(variable => {
                    result = result.split(variable).join(variableDefaults[variable]);
                });
                return result;
            };

            // 변수 변환 체크박스 상태 확인 함수 (체크 해제 시 치환, 체크 시 변수 그대로)
            const isVariableConvertEnabled = () => {
                return templatePreview ? !templatePreview.isCheckboxChecked() : false;
            };

            /**
             * 미리보기 & 변수 삽입 매니저
             */
            const createPreviewManager = () => {
                // 공통 유틸리티
                const createUtils = () => {
                    let tempDiv = null;
                    const eventFlags = {input: false, click: false, image: false};

                    const escapeHtml = (str) => {
                        if (!str) return '';
                        if (!tempDiv) tempDiv = document.createElement('div');
                        tempDiv.textContent = str;
                        return tempDiv.innerHTML;
                    };

                    const getSourceKey = (el) => el.dataset.charcountKey || el.name || el.dataset.previewSource;

                    const updateImageSrc = (images, getSrc) => {
                        images.forEach(img => {
                            const src = getSrc(img);
                            if (src !== undefined) img.src = src;
                        });
                    };

                    return {eventFlags, escapeHtml, getSourceKey, updateImageSrc};
                };

                const utils = createUtils();

                // 텍스트 미리보기 모듈
                const createTextPreviewModule = () => {
                    const cache = new Map();

                    const buildCache = () => {
                        cache.clear();
                        document.querySelectorAll('[data-preview-target]').forEach(el => {
                            const key = el.dataset.previewTarget;
                            if (!cache.has(key)) cache.set(key, []);
                            cache.get(key).push(el);
                        });
                    };

                    // data-preview-target key → templatePreview 메서드 매핑
                    const previewMethodMap = {
                        templateMessageContent: 'setContent',
                        kakaoMessageInput: 'setContent',
                        templateContentMyappContent: 'setContent',
                        templateTypeTitle: 'setEmTitle',
                        templateTypeSubTitle: 'setEmSubTitle',
                        templateHeader: 'setListHeader',
                        templateItemHighlightTitle: 'setHighlightTitle',
                        templateItemHighlightDescription: 'setHighlightDesc',
                        templateExtra: 'setExtraInfo',
                        // channelExtra는 고정 텍스트(textarea value 항상 빈값)이므로 refreshAll()에서 제외
                        // 채널 문구 표시/숨김은 initCheckboxControl의 toggle()에서 전담
                        templateContentMyappTitle: 'setTitle',
                        templateWithdrawalMethod: 'setWithdrawalMethod',
                    };

                    const skipConvertKeys = new Set([
                        'templateContentMyappTitle',
                        'templateItemHighlightTitle',
                        'templateItemHighlightDescription',
                    ]);

                    const update = (key, value) => {
                        // CrmPreview 동기화
                        if (templatePreview) {
                            const method = previewMethodMap[key];
                            if (method && typeof templatePreview[method] === 'function') {
                                let displayValue = value || '';
                                if (isVariableConvertEnabled() && displayValue && !skipConvertKeys.has(key)) {
                                    displayValue = convertVariablesToDefaults(displayValue);
                                }
                                templatePreview[method](displayValue);
                            }
                        }
                    };

                    const applyInitialValues = () => {
                        document.querySelectorAll('[data-charcount-key], [name]').forEach(input => {
                            if (!input.value) return;
                            if (input.closest('[data-show-when]')?.style.display === 'none') return;
                            const key = utils.getSourceKey(input);
                            if (key && (cache.has(key) || previewMethodMap[key])) update(key, input.value);
                        });
                    };

                    const init = () => {
                        if (utils.eventFlags.input) return;
                        buildCache();
                        document.addEventListener('input', (e) => {
                            if (!['TEXTAREA', 'INPUT'].includes(e.target.tagName)) return;
                            const key = utils.getSourceKey(e.target);
                            if (key) update(key, e.target.value);
                        });
                        applyInitialValues();
                        utils.eventFlags.input = true;
                    };

                    // 모든 미리보기 새로고침 (체크박스 변경 시 사용)
                    const refreshAll = () => {
                        document.querySelectorAll('[data-charcount-key], [name]').forEach(input => {
                            // 숨겨진 섹션의 input은 제외
                            if (input.closest('[data-show-when]')?.style.display === 'none') return;
                            const key = utils.getSourceKey(input);
                            if (key && (cache.has(key) || previewMethodMap[key])) update(key, input.value);
                        });
                    };

                    return {init, update, buildCache, refreshAll};
                };

                const textPreviewModule = createTextPreviewModule();

                // 이미지 미리보기 모듈
                const createImagePreviewModule = () => {
                    // inputId → templatePreview 메서드 매핑
                    const imageMethodMap = {
                        templateTopImageInput: 'setImage',
                        templateItemHighlightImageInput: 'setHighlightImage',
                        templateWithdrawalImageInput: 'setImage',
                    };

                    const update = (inputId, file) => {
                        if (!templatePreview) return;
                        const method = imageMethodMap[inputId];
                        if (!method || typeof templatePreview[method] !== 'function') return;

                        if (!file || !file.type) {
                            templatePreview[method]('');
                            return;
                        }

                        const reader = new FileReader();
                        reader.onload = (e) => templatePreview[method](e.target.result);
                        reader.readAsDataURL(file);
                    };

                    const setUrl = (inputId, url) => {
                        if (!templatePreview) return;
                        const method = imageMethodMap[inputId];
                        if (method && typeof templatePreview[method] === 'function') {
                            templatePreview[method](url || '');
                        }
                    };

                    const reset = (inputId) => {
                        if (!templatePreview) return;
                        const method = imageMethodMap[inputId];
                        if (method && typeof templatePreview[method] === 'function') {
                            templatePreview[method]('');
                        }
                    };

                    const init = () => {
                        if (utils.eventFlags.image) return;
                        document.addEventListener('change', (e) => {
                            if (e.target.type === 'file' && e.target.files?.length && e.target.id) {
                                update(e.target.id, e.target.files[0]);
                            }
                        });
                        utils.eventFlags.image = true;
                    };

                    return {init, update, setUrl, reset};
                };

                const imagePreviewModule = createImagePreviewModule();

                // 변수 삽입 모듈
                const createVariableInsertModule = () => {
                    const getTargetTextarea = () => {
                        const selectors = [
                            '[data-charcount-key="templateMessageContent"]',
                            '[data-charcount-key="templateContentMyappContent"]',
                            '[data-charcount-key="templateBodyContent"]'
                        ];
                        for (const selector of selectors) {
                            const el = document.querySelector(selector);
                            if (el && el.offsetParent !== null) return el;
                        }
                        return null;
                    };
                    const insertAtCursor = (el, text) => {
                        if (!el || el.disabled) return;
                        const {selectionStart: start, selectionEnd: end, value} = el;
                        el.value = value.substring(0, start) + text + value.substring(end);
                        el.setSelectionRange(start + text.length, start + text.length);
                        el.dispatchEvent(new Event('input', {bubbles: true}));
                        el.focus();
                    };

                    const init = () => {
                        if (utils.eventFlags.click) return;
                        document.addEventListener('click', (e) => {
                            const btn = e.target.closest('.js-variable-button');
                            if (!btn?.dataset.variableName) return;
                            e.preventDefault();
                            const targetTextarea = getTargetTextarea();
                            if (targetTextarea) insertAtCursor(targetTextarea, btn.dataset.variableName);
                        });
                        utils.eventFlags.click = true;
                    };

                    return {init, insertAtCursor};
                };

                const variableInsertModule = createVariableInsertModule();

                // 버튼 미리보기 모듈
                const createButtonPreviewModule = () => {
                    // 현재 버튼 테이블에서 전체 버튼 수집 → templatePreview.setButtons()
                    const syncButtons = () => {
                        if (!templatePreview) return;
                        const buttons = [];

                        // 채널 추가 버튼 (체크박스 활성화 시 맨 앞에 추가)
                        const channelAddCheckbox = document.getElementById('channelAddCheckbox');
                        if (channelAddCheckbox?.checked) {
                            buttons.push({ text: '채널 추가', type: 'add-channel' });
                        }

                        // 페이지 링크 버튼 수집
                        document.querySelectorAll('#pageLinkTableBody tr[data-row-id]').forEach(row => {
                            const name = row.querySelector('.page-link-button-input')?.value?.trim();
                            const url = row.querySelector('.button-url-input')?.value?.trim();
                            if (name) {
                                buttons.push({ text: name, url: url || '' });
                            }
                        });

                        // 배송 조회 버튼 수집
                        document.querySelectorAll('#deliveryTableBody tr[data-row-id]').forEach(row => {
                            const name = row.querySelector('.delivery-button-name-input')?.value?.trim();
                            if (name) {
                                buttons.push({ text: name });
                            }
                        });

                        templatePreview.setButtons(buttons);
                    };

                    const init = () => {
                        // 페이지 링크
                        document.getElementById('pageLinkTableBody')?.addEventListener('input', syncButtons);

                        // 배송 조회
                        document.getElementById('deliveryTableBody')?.addEventListener('input', syncButtons);

                        // 삭제 처리 (DOM 변경 감지)
                        document.addEventListener('click', (e) => {
                            const row = e.target.closest('.delete-row-btn')?.closest('tr[data-row-id]');
                            if (row) {
                                // DOM 제거 후 다음 프레임에서 동기화
                                requestAnimationFrame(syncButtons);
                            }
                        });

                        // 행 추가 시에도 동기화 (MutationObserver)
                        const observer = new MutationObserver(() => requestAnimationFrame(syncButtons));
                        const pageLinkTbody = document.getElementById('pageLinkTableBody');
                        const deliveryTbody = document.getElementById('deliveryTableBody');
                        if (pageLinkTbody) observer.observe(pageLinkTbody, { childList: true });
                        if (deliveryTbody) observer.observe(deliveryTbody, { childList: true });
                    };

                    return {init, syncButtons};
                };

                const buttonPreviewModule = createButtonPreviewModule();

                // 아이템 리스트 미리보기 모듈
                const createItemListPreviewModule = () => {
                    // 아이템 리스트를 입력한 경우 본문 입력은 700자로 변경
                    const updateCharLimit = () => {
                        const textarea = document.querySelector('textarea[name="kakaoMessageInput"]');
                        if (!textarea) return;

                        const hasItemList = [...document.querySelectorAll('[data-component="item-list-tbody"] tr')]
                            .some(row => row.querySelector('.item-list-title-input')?.value?.trim()
                                || row.querySelector('.item-list-content-input')?.value?.trim());

                        const maxLength = hasItemList ? 700 : 1000;
                        textarea.maxLength = maxLength;

                        const maxCountText = textarea.closest('.ncua-input')?.querySelector('.max-count-text');
                        if (maxCountText) maxCountText.textContent = maxLength;

                        charCountManager.update(textarea);
                    };

                    const syncItemList = () => {
                        if (!templatePreview) return;
                        const items = [];
                        const convert = isVariableConvertEnabled() ? convertVariablesToDefaults : (v) => v;

                        // 아이템 행 수집
                        document.querySelectorAll('[data-component="item-list-tbody"] tr').forEach(row => {
                            const title = row.querySelector('.item-list-title-input')?.value?.trim();
                            const content = row.querySelector('.item-list-content-input')?.value?.trim();
                            if (title && content) {
                                items.push({ title: convert(title), desc: convert(content) });
                            }
                        });

                        // 요약 행 수집 (요약은 치환하지 않음)
                        const summaryTitle = document.querySelector('.item-summary-title-input')?.value?.trim();
                        const summaryContent = document.querySelector('.item-summary-content-input')?.value?.trim();
                        if (summaryTitle && summaryContent) {
                            items.push({ title: summaryTitle, desc: summaryContent, isSummary: true });
                        }

                        templatePreview.setItemList(items);
                    };

                    const update = () => {
                        syncItemList();
                        updateCharLimit();
                    };

                    const init = () => {
                        update();
                        document.addEventListener('input', (e) => {
                            if (e.target.matches('.item-list-title-input, .item-list-content-input, .item-summary-title-input, .item-summary-content-input')) {
                                update();
                            }
                        });

                        const tbody = document.querySelector('[data-component="item-list-tbody"]');
                        if (tbody) new MutationObserver(() => update()).observe(tbody, {childList: true});
                    };

                    return {init, update};
                };

                const itemListPreviewModule = createItemListPreviewModule();

                // 초기화
                const init = () => {
                    textPreviewModule.init();
                    imagePreviewModule.init();
                    variableInsertModule.init();
                    buttonPreviewModule.init();
                    itemListPreviewModule.init();
                };

                return {
                    init,
                    updatePreview: (key, value) => textPreviewModule.update(key, value),
                    refreshCache: () => textPreviewModule.buildCache(),
                    refreshAll: () => {
                        textPreviewModule.refreshAll();
                        itemListPreviewModule.update();
                        buttonPreviewModule.syncButtons();

                        // 채널 추가 문구 재적용 (고정 텍스트라 textarea value가 항상 빈값이므로 체크박스 기반으로 복원)
                        if (templatePreview && typeof templatePreview.setChannelMessage === 'function') {
                            const channelCheckbox = document.getElementById('channelAddCheckbox');
                            templatePreview.setChannelMessage(
                                channelCheckbox?.checked ? '채널 추가하고 이 채널의 광고와 마케팅 메시지 등을 카카오톡으로 받기' : ''
                            );
                        }
                    },
                    syncButtons: () => buttonPreviewModule.syncButtons(),
                    insertTextAtCursor: (el, text) => variableInsertModule.insertAtCursor(el, text),
                    updateImagePreview: (id, file) => imagePreviewModule.update(id, file),
                    setImagePreview: (id, url) => imagePreviewModule.setUrl(id, url),
                    resetImagePreview: (id) => imagePreviewModule.reset(id)
                };
            };

            const previewManager = createPreviewManager();
            previewManager.init();

            // MessageInput 값을 변수 변환하여 미리보기에 반영하는 함수
            const refreshMessageInputPreviews = () => {
                if (!templatePreview) return;
                const sendMethod = document.querySelector('input[name="sendMethod"]:checked')?.value || 'sms';
                const inputMap = {
                    sms: smsMessageInput,
                    kakao: kakaoMessageInput,
                    myapp: myappMessageInput
                };
                const activeInput = inputMap[sendMethod];
                if (activeInput && activeInput.getValue) {
                    const value = activeInput.getValue();
                    let displayValue = value || '';
                    if (isVariableConvertEnabled() && displayValue) {
                        displayValue = convertVariablesToDefaults(displayValue);
                    }
                    templatePreview.setContent(displayValue);
                }
            };

            // 변수 변환 체크박스 이벤트 리스너 (CrmPreview 체크박스 사용)
            if (templatePreview) {
                templatePreview.onVariableCheckboxChange(function() {
                    previewManager.refreshAll();
                    // MessageInput의 현재 값도 변수 변환 반영
                    refreshMessageInputPreviews();
                });
            } else {
                // templatePreview가 아직 로드되지 않은 경우, 로드 후 등록
                window.addEventListener('crmPreviewLoaded', function registerCheckboxHandler() {
                    if (templatePreview) {
                        templatePreview.onVariableCheckboxChange(function() {
                            previewManager.refreshAll();
                            refreshMessageInputPreviews();
                        });
                    }
                    window.removeEventListener('crmPreviewLoaded', registerCheckboxHandler);
                });
            }

            // 이미지 상태 추적용 hidden input (변경 감지 기준점)
            const imageStateMap = {
                templateTopImage: initialData.cloudImageUrl || '',
                templateItemHighlightImage: initialData.cloudItemHighlightImageUrl || '',
                templateWithdrawalImage: initialData.myappImage || '',
            };
            const form = document.getElementById('templateRegisterForm');
            Object.entries(imageStateMap).forEach(([name, value]) => {
                const input = document.createElement('input');
                input.type = 'hidden';
                input.name = `_imageState_${name}`;
                input.value = value;
                form.appendChild(input);
            });

            /**
             * 체크박스 연동 제어 (텍스트영역 활성화 + 미리보기 연동)
             * @param {string} checkboxId - 체크박스 요소 ID
             * @param {string|null} textareaId - 텍스트영역 요소 ID (없으면 null)
             * @param {string} previewMethod - templatePreview 메서드명 (예: 'setExtraInfo')
             * @param {string} [fixedValue] - 고정값 (textarea 없이 고정 텍스트를 표시할 때 사용)
             */
            const initCheckboxControl = (checkboxId, textareaId, previewMethod, fixedValue) => {
                const checkbox = document.getElementById(checkboxId);
                const textarea = textareaId ? document.getElementById(textareaId) : null;

                if (!checkbox) return;

                const toggle = () => {
                    if (textarea) textarea.disabled = !checkbox.checked;
                    if (templatePreview && previewMethod && typeof templatePreview[previewMethod] === 'function') {
                        if (checkbox.checked) {
                            const value = fixedValue || (textarea ? textarea.value : '') || '';
                            templatePreview[previewMethod](value);
                        } else {
                            templatePreview[previewMethod]('');
                        }
                    }
                };
                toggle();
                checkbox.addEventListener('change', toggle);
            };

            /**
             * 버튼(페이지 링크 추가, 배송 조회 추가) 행 추가/삭제 관리
             */
            const initButtonRowManager = () => {
                const addPageLinkBtn = document.getElementById('addPageLinkButton');
                const addDeliveryBtn = document.getElementById('addDeliveryButton');
                const pageLinkTableBody = document.getElementById('pageLinkTableBody');
                const deliveryTableBody = document.getElementById('deliveryTableBody');

                if (!addPageLinkBtn || !addDeliveryBtn || !pageLinkTableBody || !deliveryTableBody) return;

                let rowCounter = 0;
                const MAX_BUTTONS = 5;

                // 전체 버튼 개수 계산 (페이지 링크 + 배송 조회만 카운트)
                const getTotalButtonCount = () => {
                    const pageLinkRows = pageLinkTableBody.querySelectorAll('tr:not(.empty-row)').length;
                    const deliveryRows = deliveryTableBody.querySelectorAll('tr:not(.empty-row)').length;
                    return pageLinkRows + deliveryRows;
                };

                // 추가 버튼 활성화/비활성화
                const updateAddButtons = () => {
                    const count = getTotalButtonCount();
                    const disabled = count >= MAX_BUTTONS;
                    addPageLinkBtn.disabled = disabled;
                    addDeliveryBtn.disabled = disabled;
                };

                // 빈 행 표시/숨김
                const updateEmptyRow = (tbody) => {
                    const emptyRow = tbody.querySelector('.empty-row');
                    const dataRows = tbody.querySelectorAll('tr:not(.empty-row)');
                    if (emptyRow) {
                        emptyRow.style.display = dataRows.length > 0 ? 'none' : '';
                    }
                };

                // 페이지 링크 행 생성
                const createPageLinkRow = () => {
                    const rowId = `pageLink_${++rowCounter}`;
                    const tr = document.createElement('tr');
                    tr.dataset.rowId = rowId;
                    tr.innerHTML = `
                        <td><div class="ncua-align-center ncua-text-align-center">페이지<br>링크</div></td>
                        <td>
                            <div>
                                <div class="ncua-input ncua-input--xs ncua-input--full-width-with-count" data-show-hint-text="true">
                                    <div class="ncua-input__content-wrap">
                                        <div class="ncua-input__content">
                                            <div class="ncua-input__field ncua-input__field--xs">
                                                <input class="page-link-button-input" name="templateButtonName${rowId}" data-charcount-key="templateButtonName${rowId}" type="text" maxlength="14" placeholder="버튼에 표기할 문구를 넣어 주세요." />
                                            </div>
                                        </div>
                                        <div class="ncua-input__field-text-count" data-charcount-text="templateButtonName${rowId}">
                                            <output class="ncua-input__field-text-count-current">0</output><span>/14</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </td>
                        <td>
                            <div>
                                <div class="ncua-input ncua-input--xs ncua-input-full-width" data-show-hint-text="true">
                                    <div class="ncua-input__content">
                                        <div class="ncua-input__field ncua-input__field--xs">
                                            <input class="button-url-input" type="text" name="templateButtonUrl${rowId}" maxlength="1000" placeholder="모바일 쇼핑몰 URL을 넣어 주세요." />
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </td>
                        <td>
                            <div class="ncua-align-center">
                                <button type="button" class="delete-row-btn ncua-btn ncua-btn--xs ncua-btn--secondary-gray">- 삭제</button>
                            </div>
                        </td>
                    `;

                    const deleteBtn = tr.querySelector('.delete-row-btn');

                    // 삭제 버튼
                    deleteBtn.addEventListener('click', () => {
                        // jQuery Validator rules 제거
                        if (typeof $ !== 'undefined') {
                            const nameInput = tr.querySelector('.page-link-button-input');
                            const urlInput = tr.querySelector('.button-url-input');
                            if (nameInput) $(nameInput).rules('remove');
                            if (urlInput) $(urlInput).rules('remove');
                        }
                        tr.remove();
                        updateEmptyRow(pageLinkTableBody);
                        updateAddButtons();
                    });

                    return tr;
                };

                // 배송 조회 행 생성
                const createDeliveryRow = () => {
                    const rowId = `delivery_${++rowCounter}`;
                    const tr = document.createElement('tr');
                    tr.dataset.rowId = rowId;
                    tr.innerHTML = `
                        <td><div class="ncua-align-center">배송<br>조회</div></td>
                        <td>
                            <div>
                                <div class="ncua-input ncua-input--xs ncua-input--full-width-with-count" data-show-hint-text="true">
                                    <div class="ncua-input__content-wrap">
                                        <div class="ncua-input__content">
                                            <div class="ncua-input__field ncua-input__field--xs">
                                                <input class="delivery-button-name-input" name="templateDeliveryButtonName${rowId}" data-charcount-key="templateDeliveryButtonName${rowId}" type="text" maxlength="14" placeholder="버튼에 표기할 문구를 넣어 주세요." />
                                            </div>
                                        </div>
                                        <div class="ncua-input__field-text-count" data-charcount-text="templateDeliveryButtonName${rowId}">
                                            <output class="ncua-input__field-text-count-current">0</output><span>/14</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </td>
                        <td>
                            <div class="info-text">본문에 택배사명과 송장번호가 포함되면 버튼이 카카오 배송조회<br>페이지로 연결됩니다. 단, 미지원 택배사는 조회되지 않습니다.</div>
                        </td>
                        <td>
                            <div class="ncua-align-center">
                                <button type="button" class="delete-row-btn ncua-btn ncua-btn--xs ncua-btn--secondary-gray">- 삭제</button>
                            </div>
                        </td>
                    `;

                    const deleteBtn = tr.querySelector('.delete-row-btn');

                    // 삭제 버튼
                    deleteBtn.addEventListener('click', () => {
                        // jQuery Validator rules 제거
                        if (typeof $ !== 'undefined') {
                            const nameInput = tr.querySelector('.delivery-button-name-input');
                            if (nameInput) $(nameInput).rules('remove');
                        }
                        tr.remove();
                        updateEmptyRow(deliveryTableBody);
                        updateAddButtons();
                    });

                    return tr;
                };

                // jQuery Validator rules 추가 헬퍼
                const addValidationRules = (row, rules) => {
                    if (typeof $ === 'undefined') return;
                    const $form = $(row).closest('form');
                    // validator가 초기화되지 않았으면 스킵
                    if (!$form.length || !$form.data('validator')) return;
                    rules.forEach(({selector, message}) => {
                        const input = row.querySelector(selector);
                        if (input) $(input).rules('add', {required: true, messages: {required: message}});
                    });
                };

                // 행 추가 공통 함수
                const addRow = (createFn, tbody, rules) => {
                    if (getTotalButtonCount() >= MAX_BUTTONS) return null;
                    const row = createFn();
                    tbody.insertBefore(row, tbody.querySelector('.empty-row'));
                    addValidationRules(row, rules);
                    updateEmptyRow(tbody);
                    updateAddButtons();
                    return row;
                };

                // 페이지 링크 추가 버튼
                addPageLinkBtn.addEventListener('click', () => {
                    addRow(createPageLinkRow, pageLinkTableBody, [
                        {selector: '.page-link-button-input', message: '버튼명을 입력해 주세요.'},
                        {selector: '.button-url-input', message: 'URL을 입력해 주세요.'}
                    ]);
                });

                // 배송 조회 추가 버튼
                addDeliveryBtn.addEventListener('click', () => {
                    addRow(createDeliveryRow, deliveryTableBody, [
                        {selector: '.delivery-button-name-input', message: '버튼명을 입력해 주세요.'}
                    ]);
                });

                // 초기 상태 설정
                updateAddButtons();

                // 초기 데이터로 버튼 렌더링 (수정/복제 모드)
                if (initialData.cloudButtons && initialData.cloudButtons.length > 0) {
                    initialData.cloudButtons.forEach(button => {
                        // cloud: type, bizm: linkType
                        const buttonType = button.type || button.linkType;
                        // cloud: WEB_LINK, bizm: WL
                        if (buttonType === 'WL' || buttonType === 'WEB_LINK') {
                            // addRow 함수로 페이지 링크 버튼 추가
                            const row = addRow(createPageLinkRow, pageLinkTableBody, [
                                {selector: '.page-link-button-input', message: '버튼명을 입력해 주세요.'},
                                {selector: '.button-url-input', message: 'URL을 입력해 주세요.'}
                            ]);
                            if (row) {
                                const nameInput = row.querySelector('.page-link-button-input');
                                const urlInput = row.querySelector('.button-url-input');
                                const nameValue = button.name || '';
                                const urlValue = button.mobileUrl || button.pcUrl || button.linkMo || button.linkPc || '';
                                // 값 먼저 설정 (미리보기에서 name && url 체크하므로)
                                if (nameInput) nameInput.value = nameValue;
                                if (urlInput) urlInput.value = urlValue;
                                // 글자수 카운트 수동 업데이트
                                const nameCount = row.querySelector('.ncua-input__field-text-count-current');
                                if (nameCount) nameCount.textContent = nameValue.length;
                                // 값 설정 후 미리보기 업데이트를 위해 이벤트 발생
                                if (nameInput) nameInput.dispatchEvent(new Event('input', {bubbles: true}));
                            }
                            // cloud: DELIVERY_SEARCH, bizm: DS
                        } else if (buttonType === 'DS' || buttonType === 'DELIVERY_SEARCH') {
                            // addRow 함수로 배송 조회 버튼 추가
                            const row = addRow(createDeliveryRow, deliveryTableBody, [
                                {selector: '.delivery-button-name-input', message: '버튼명을 입력해 주세요.'}
                            ]);
                            if (row) {
                                const nameInput = row.querySelector('.delivery-button-name-input');
                                const nameValue = button.name || '';
                                if (nameInput) {
                                    nameInput.value = nameValue;
                                    // 글자수 카운트 수동 업데이트
                                    const nameCount = row.querySelector('.ncua-input__field-text-count-current');
                                    if (nameCount) nameCount.textContent = nameValue.length;
                                    // 미리보기 업데이트를 위해 이벤트 발생
                                    nameInput.dispatchEvent(new Event('input', {bubbles: true}));
                                }
                            }
                        }
                    });
                }
            };

            initButtonRowManager();
            // 버튼 행 렌더링 후 미리보기 동기화 (수정/복제 모드)
            if (initialData.mode !== 'register') {
                previewManager.syncButtons();
            }

            const getImageDimensions = (file) => {
                return new Promise((resolve, reject) => {
                    const reader = new FileReader();

                    reader.onload = (e) => {
                        const img = new Image();
                        img.onload = () => {
                            resolve({
                                width: img.naturalWidth,
                                height: img.naturalHeight
                            });
                        };
                        img.onerror = reject;
                        img.src = e.target.result;
                    };

                    reader.onerror = reject;
                    reader.readAsDataURL(file);
                });
            }

            /**
             * 이미지 파일 크기 및 비율 검증 상수
             */
            const MAX_UPLOAD_COUNT = 1;
            const MAX_UPLOAD_IMAGE_SIZE = 5;
            const MAX_UPLOAD_FILE_SIZE = 3;
            const TOP_IMAGE_RATIO = 2;
            const TOP_IMAGE_MIN_WIDTH = 500;
            const TOP_IMAGE_MIN_HEIGHT = 250;
            const ITEM_HIGHLIGHT_IMAGE_RATIO = 1;
            const ITEM_HIGHLIGHT_IMAGE_MIN_WIDTH = 108;
            const ITEM_HIGHLIGHT_IMAGE_MIN_HEIGHT = 108;
            const WITHDRAWAL_IMAGE_ALLOWED_EXTENSIONS = ['jpg', 'png'];

            const validateImageFiles = async ({newFiles, maxSize, imageRatio}) => {
                // 실제 파일 데이터가 없는 경우 검증 생략 (setFiles 초기 데이터)
                if (!newFiles.some(file => file?.size > 0)) return { valid: true };

                const maxSizeKB = maxSize * 100;
                const maxSizeBytes = maxSizeKB * 1024;

                // 파일 크기 검증 (500KB = 500 * 1024 bytes)
                const oversizedFile = newFiles.find(file => file?.size > maxSizeBytes);
                if (oversizedFile) {
                    return {
                        valid: false,
                        message: `이미지 용량은 ${maxSizeKB}KB 이하여야 합니다.`
                    };
                }

                // 2:1 비율이 아닌 이미지를 첨부한 경우
                if (imageRatio === TOP_IMAGE_RATIO) {
                    for (const file of newFiles) {
                        const dimensions = await new Promise((resolve, reject) => {
                            const img = new Image();
                            img.onload = () => resolve({w: img.naturalWidth, h: img.naturalHeight});
                            img.onerror = reject;
                            img.src = URL.createObjectURL(file);
                        });
                        const isInvalidRatio = dimensions.w !== dimensions.h * imageRatio;
                        const isBelowMinSize = dimensions.w < TOP_IMAGE_MIN_WIDTH || dimensions.h < TOP_IMAGE_MIN_HEIGHT;

                        if (isInvalidRatio || isBelowMinSize) {
                            return {
                                valid: false,
                                message: `이미지 비율은 ${imageRatio}:1이어야 하며, 가로 ${TOP_IMAGE_MIN_WIDTH}px · 세로 ${TOP_IMAGE_MIN_HEIGHT}px 이상이어야 합니다.`
                            };
                        }
                    }
                }

                // 1:1 비율이 아닌 이미지를 첨부한 경우
                if (imageRatio === ITEM_HIGHLIGHT_IMAGE_RATIO) {
                    for (const file of newFiles) {
                        const dimensions = await new Promise((resolve, reject) => {
                            const img = new Image();
                            img.onload = () => resolve({w: img.naturalWidth, h: img.naturalHeight});
                            img.onerror = reject;
                            img.src = URL.createObjectURL(file);
                        });
                        const isInvalidRatio = dimensions.w !== dimensions.h * imageRatio;
                        const isBelowMinSize = dimensions.w < ITEM_HIGHLIGHT_IMAGE_MIN_WIDTH || dimensions.h < ITEM_HIGHLIGHT_IMAGE_MIN_HEIGHT;

                        if (isInvalidRatio || isBelowMinSize) {
                            return {
                                valid: false,
                                message: `이미지 비율은 ${imageRatio}:1이어야 하며, 가로 ${ITEM_HIGHLIGHT_IMAGE_MIN_WIDTH}px · 세로 ${ITEM_HIGHLIGHT_IMAGE_MIN_HEIGHT}px 이상이어야 합니다.`
                            };
                        }
                    }
                }

                return {
                    valid: true,
                };
            }

            // 파일 크기 검증
            const validateFiles = (newFiles, allowedExtensions, maxSize) => {
                // 파일이 없는 경우
                if (!newFiles) {
                   return {
                        valid: false,
                        message: '파일을 선택해 주세요.'
                    };
                }

                // 파일 확장자 검증
                const fileName = newFiles[0]?.name;
                const fileExtension = fileName?.split('.').pop().toLowerCase();
                if (fileName?.length > 0 && allowedExtensions && !allowedExtensions.includes(fileExtension)) {
                    return {
                        valid: false,
                        message: '지원되지 않는 파일입니다.\njpg, png 형식의 파일을 등록해 주세요.'
                    };
                }

                // 파일 크기 검증 (3MB = 3 * 1024 * 1024 bytes)
                const maxSizeBytes = maxSize * 1024 * 1024;
                const oversizedFile = newFiles.find(file => file?.size > maxSizeBytes);

                if (oversizedFile) {
                    return {
                        valid: false,
                        message: '이미지 업로드에 실패했습니다.',
                        subMessage: '권장사이즈에 맞춰 이미지를 업로드 해주시길 바랍니다.'
                    };
                }

                return {
                    valid: true,
                };
            }

            // 파일 선택 시 FileInput 설정
            const setInputFile = (files, inputName) => {
                const fileInput = document.querySelector(`input[name="${inputName}"]`);
                if (!fileInput) return;

                const dataTransfer = new DataTransfer();

                if (files && files.length > 0) {
                    dataTransfer.items.add(files[0]);
                }

                fileInput.files = dataTransfer.files;
            };

            // 이미지 미리보기 삭제 핸들러
            const deleteImagePreviewHandler = ({imageInput, fileInputId, inputName}) => {
                if (imageInput?.clearFiles) imageInput.clearFiles();
                if (imageInput?.renderImagePreviews) imageInput.renderImagePreviews();
                if (fileInputId) previewManager.updateImagePreview(fileInputId, null);

                if (inputName) {
                    setInputFile([], inputName);
                    const stateInput = form.querySelector(`input[name="_imageState_${inputName}"]`);
                    if (stateInput) stateInput.value = '';
                }

                // 기존 이미지 삭제 시 initialData 초기화
                if (inputName === 'templateTopImage') {
                    initialData.cloudImageUrl = null;
                } else if (inputName === 'templateItemHighlightImage') {
                    initialData.cloudItemHighlightImageUrl = null;
                } else if (inputName === 'templateWithdrawalImage') {
                    initialData.myappImage = null;
                }

                // 등록 모드: 이미지 삭제 후 해당 샘플 이미지만 복원
                const sampleImageRestoreMap = {
                    templateTopImage: () => templatePreview.setImage(sampleAlimTalkImage),
                    templateItemHighlightImage: () => templatePreview.setHighlightImage(sampleAlimTalkImage),
                };
                if (initialData.mode === 'register') sampleImageRestoreMap[inputName]?.();
            }

            // 이미지 미리보기 삭제 전 알럿 노출
            const deleteImagePreviewAlert = ({imageInput, fileInputId, inputName}) => {
                NCDSConfirm({
                    message: '첨부파일을 삭제하시겠습니까?',
                    btnText: {
                        confirmLabel: '확인',
                        cancelLabel: '취소'
                    },
                    callback: (result) => {
                        if (result) {
                            deleteImagePreviewHandler({imageInput, fileInputId, inputName});
                        }
                    },
                });
            }

            // 이미지 미리보기 삭제 이벤트 메타 데이터
            const imageFileInputMetaList = [
                {
                    containerId: 'top-image-file-container',
                    imageInput: () => topImageFileInput,
                    fileInputId: 'templateTopImageInput',
                    inputName: 'templateTopImage',
                },
                {
                    containerId: 'item-highlight-image-file-container',
                    imageInput: () => itemHighlightImageFileInput,
                    fileInputId: 'templateItemHighlightImageInput',
                    inputName: 'templateItemHighlightImage',
                },
                {
                    containerId: 'withdrawal-image-file-container',
                    imageInput: () => withdrawalImageFileInput,
                    fileInputId: 'templateWithdrawalImageInput',
                    inputName: 'templateWithdrawalImage',
                },
            ];

            // 이미지 미리보기 삭제 이벤트(삭제 버튼이 먼저 DOM을 삭제하기 전에 선점 처리(캡처 단계))
            document.addEventListener('click', (e) => {
                const deleteBtn = e.target?.closest?.('.ncua-image-file-input__preview-remove-button');
                if (!deleteBtn) return;

                const meta = imageFileInputMetaList.find(({containerId}) => deleteBtn.closest(`#${containerId}`));
                if (!meta) return;

                e.preventDefault();
                e.stopPropagation();
                e.stopImmediatePropagation();

                deleteImagePreviewAlert({
                    imageInput: meta.imageInput(),
                    fileInputId: meta.fileInputId,
                    inputName: meta.inputName,
                });
            }, {capture: true});

            // 상단 이미지 ImageFileInput
            const topImageFileInput = new ncua.ImageFileInput({
                container: 'top-image-file-container',
                buttonLabel: '파일 찾기',
                maxFileCount: MAX_UPLOAD_COUNT,
                accept: '.jpg,.jpeg,.png',
                hintItems: ['가로 800px, 세로 400px,', '용량 500KB 이하 / jpg, png만 가능'],
                onChange: async (newFiles) => {
                    // 파일 검증
                    const validation = await validateImageFiles({
                        newFiles,
                        maxSize: MAX_UPLOAD_IMAGE_SIZE,
                        imageRatio: TOP_IMAGE_RATIO
                    });

                    if (!validation.valid) {
                        NCDSAlert({message: validation.message, ...(validation.subMessage && {subMessage: validation.subMessage}), iconType: 'error'});
                        return;
                    }

                    // 이미지 미리보기 렌더링
                    topImageFileInput.renderImagePreviews();

                    // 이미지 업로드 성공 시 에러 하이라이트 제거
                    const topImageBtn = document.querySelector('#top-image-file-container .ncua-image-file-input');
                    if (topImageBtn) {
                        NCDSValidator.unhighlight(topImageBtn);
                    }

                    // 알림톡(이미지형, 아이템 리스트형) 미리보기: 상단 이미지 업데이트
                    if (newFiles.length > 0 && newFiles[0].size > 0) {
                        previewManager.updateImagePreview('templateTopImageInput', newFiles[0]);
                    }

                    setInputFile(newFiles, 'templateTopImage');
                    const stateInput = form.querySelector('input[name="_imageState_templateTopImage"]');
                    if (stateInput) stateInput.value = newFiles.length > 0 ? 'modified' : '';
                },
            });

            // 기존 상단 이미지 미리보기 설정 (수정/복제 모드)
            if (initialData.cloudImageUrl) {
                topImageFileInput.setFiles([{ fileName: initialData.cloudImageUrl.split('/').pop(), fileImageUrl: initialData.cloudImageUrl }]);
            }

            // 아이템 하이라이트 이미지 ImageFileInput
            const itemHighlightImageFileInput = new ncua.ImageFileInput({
                container: 'item-highlight-image-file-container',
                buttonLabel: '파일 찾기',
                maxFileCount: MAX_UPLOAD_COUNT,
                accept: '.jpg,.jpeg,.png',
                hintItems: ['1:1 비율,', '용량 500KB 이하 / jpg, png만 가능'],
                onChange: async (newFiles) => {
                    // 파일 검증
                    const validation = await validateImageFiles({
                        newFiles,
                        maxSize: MAX_UPLOAD_IMAGE_SIZE,
                        imageRatio: ITEM_HIGHLIGHT_IMAGE_RATIO
                    });

                    if (!validation.valid) {
                        NCDSAlert({message: validation.message, ...(validation.subMessage && {subMessage: validation.subMessage}), iconType: 'error'});
                        return;
                    }

                    // 이미지 미리보기 렌더링
                    itemHighlightImageFileInput.renderImagePreviews();

                    const hasImage = newFiles.length > 0 && newFiles[0].size > 0;

                    // 알림톡(아이템 리스트형) 미리보기: 아이템 하이라이트 이미지 업데이트
                    if (hasImage) {
                        previewManager.updateImagePreview('templateItemHighlightImageInput', newFiles[0]);
                    }

                    // 이미지 첨부/삭제 시 아이템 하이라이트 글자 수 제한 적용 및 복원
                    validateAndUpdateHighlightFields(hasImage);

                    setInputFile(newFiles, 'templateItemHighlightImage');
                    const stateInput = form.querySelector('input[name="_imageState_templateItemHighlightImage"]');
                    if (stateInput) stateInput.value = newFiles.length > 0 ? 'modified' : '';
                },
            });

            // 기존 아이템 하이라이트 이미지 미리보기 설정 (수정/복제 모드)
            if (initialData.cloudItemHighlightImageUrl) {
                itemHighlightImageFileInput.setFiles([{ fileName: initialData.cloudItemHighlightImageUrl.split('/').pop(), fileImageUrl: initialData.cloudItemHighlightImageUrl }]);
            }

            // 세부 입력 이미지
            const withdrawalImageFileInput = new ncua.ImageFileInput({
                container: 'withdrawal-image-file-container',
                buttonLabel: '파일 찾기',
                maxFileCount: MAX_UPLOAD_COUNT,
                hintItems: ['권장 사이즈: 640*320px', '3MB 이내인 jpg, png 형식의 파일을 등록해 주세요.'],
                fileInputName: 'uploadFiles[]',
                onChange: (newFiles) => {
                    // 파일 검증
                    const validation = validateFiles(newFiles, WITHDRAWAL_IMAGE_ALLOWED_EXTENSIONS, MAX_UPLOAD_FILE_SIZE);
                    if (!validation.valid) {
                        NCDSAlert({message: validation.message, ...(validation.subMessage && {subMessage: validation.subMessage}), iconType: 'error'});
                        return;
                    }

                    // 이미지 미리보기 렌더링
                    withdrawalImageFileInput.renderImagePreviews();

                    // 마이앱 미리보기 이미지 업데이트
                    if (newFiles.length > 0 && newFiles[0].size > 0) {
                        previewManager.updateImagePreview('templateWithdrawalImageInput', newFiles[0]);
                    }

                    setInputFile(newFiles, 'templateWithdrawalImage');
                    const stateInput = form.querySelector('input[name="_imageState_templateWithdrawalImage"]');
                    if (stateInput) stateInput.value = newFiles.length > 0 ? 'modified' : '';
                }
            });

            // 기존 마이앱 이미지 미리보기 설정 (수정/복제 모드)
            if (initialData.myappImage) {
                withdrawalImageFileInput.setFiles([{ fileName: initialData.myappImage.split('/').pop(), fileImageUrl: initialData.myappImage }]);
            }

            /**
             * 템플릿 타입 변경 시 컨펌 알럿 출력 (메시지형, 강조표기형, 이미지형, 아이템리스트형)
             */
            const templateTypeRadioContainer = document.querySelector('[data-component="template-type-radio"]');
            if (templateTypeRadioContainer) {
                let previousCheckedRadio = templateTypeRadioContainer.querySelector('input[name="templateType"]:checked');

                templateTypeRadioContainer.addEventListener('click', (event) => {
                    const target = event.target.closest('input[type="radio"][name="templateType"]');
                    if (!target) return;

                    // 현재 체크된 라디오와 동일한 경우 처리하지 않음
                    if (target === previousCheckedRadio) return;

                    // 기본 동작 방지
                    event.preventDefault();

                    // 이전에 선택된 라디오 버튼 저장
                    const clickedRadio = target;

                    NCDSConfirm({
                        message: '템플릿 유형을 변경하면 기존 내용이 사라집니다.\n변경하시겠습니까?',
                        callback: (result) => {
                            if (result) {
                                // 확인 시 라디오 버튼 체크
                                clickedRadio.checked = true;
                                previousCheckedRadio = clickedRadio;

                                // 폼 초기화
                                resetTemplateForm();

                                // jQuery Validator 초기화
                                if (validatorInstance) {
                                    validatorInstance.resetForm();
                                }

                                // resetForm 내부의 form.reset()이 라디오를 기본값으로 되돌릴 수 있으므로 재설정
                                clickedRadio.checked = true;

                                // 조건부 표시 업데이트
                                if (conditionalDisplayManager) {
                                    conditionalDisplayManager.updateVisibility();
                                }

                                // 미리보기 초기화 후 재렌더링
                                if (templatePreview) {
                                    templatePreview.reset();
                                    templatePreview.setSendType('ALIMTALK').render();
                                }

                                // 등록 모드: 새 템플릿 유형에 맞는 샘플 데이터 표시
                                applySampleData('kakao', clickedRadio.value);
                            } else {
                                // 취소 시 이전 상태 유지 (이미 preventDefault로 막혀있음)
                                if (previousCheckedRadio) {
                                    previousCheckedRadio.checked = true;
                                }
                            }
                        }
                    });
                });
            }

            /**
             * 아이템 하이라이트 이미지 등록 여부에 따른 글자 수 제한 적용
             * 글자 수 제한, 자르기 처리, maxlength 속성 및 카운터 표시 업데이트
             */
            const truncateAndUpdateCounter = ({ input, maxLength, counterElement }) => {
                if (!input || !counterElement) return;

                const prevValue = input.value;
                input.value = input.value.substring(0, maxLength);
                input.setAttribute('maxlength', maxLength);

                const outputElement = counterElement.querySelector('output');
                if (outputElement) {
                    outputElement.textContent = input.value.length;
                }

                const maxLengthSpan = counterElement.querySelector('span');
                if (maxLengthSpan) {
                    maxLengthSpan.textContent = `/${maxLength}`;
                }

                // 값이 변경된 경우 미리보기 동기화를 위해 input 이벤트 발생
                if (prevValue !== input.value) {
                    input.dispatchEvent(new Event('input', { bubbles: true }));
                }
            };

            // 아이템 하이라이트 이미지 첨부/삭제 시 글자 수 제한 적용 및 복원
            const HIGHLIGHT_FIELDS = [
                {selector: '.item-highlight-title-input', counterAttr: 'templateItemHighlightTitle', restrictedMaxLength: 21, originalMaxLength: 30, fieldName: '제목'},
                {selector: '.item-highlight-description-input', counterAttr: 'templateItemHighlightDescription', restrictedMaxLength: 13, originalMaxLength: 19, fieldName: '내용'},
            ];

            const validateAndUpdateHighlightFields = (hasImage) => {
                const exceededFields = [];

                for (const {selector, restrictedMaxLength, originalMaxLength, fieldName} of HIGHLIGHT_FIELDS) {
                    const input = document.querySelector(selector);
                    const maxLength = hasImage ? restrictedMaxLength : originalMaxLength;

                    if (hasImage && input && input.value.trim().length > maxLength) {
                        exceededFields.push(`${fieldName}은 ${maxLength}자`);
                    }
                }

                if (exceededFields.length > 0) {
                    NCDSAlert({message: `이미지 첨부 시 아이템 하이라이트 ${exceededFields.join(', ')}로 제한되며, 초과한 내용은 삭제됩니다.`, iconType: 'error'});
                }

                for (const {selector, counterAttr, restrictedMaxLength, originalMaxLength} of HIGHLIGHT_FIELDS) {
                    truncateAndUpdateCounter({
                        input: document.querySelector(selector),
                        maxLength: hasImage ? restrictedMaxLength : originalMaxLength,
                        counterElement: document.querySelector(`[data-charcount-text="${counterAttr}"]`)
                    });
                }

                return exceededFields.length === 0;
            };

            /**
             * 아이템 요약과 아이템 리스트 유효성 검사
             */
            const validateItemSummaryAndList = () => {
                // 입력 여부 확인 (공통 함수)
                const hasInput = (titleSelector, contentSelector) => {
                    const titleInput = document.querySelector(titleSelector);
                    const contentInput = document.querySelector(contentSelector);

                    const hasTitle = titleInput && titleInput.value.trim().length > 0;
                    const hasContent = contentInput && contentInput.value.trim().length > 0;

                    return hasTitle || hasContent;
                };

                // 아이템 리스트 입력 여부 확인
                const hasItemListInput = () => {
                    return hasInput('.item-list-title-input', '.item-list-content-input');
                };

                // 아이템 요약 입력 여부 확인
                const hasItemSummaryInput = () => {
                    return hasInput('.item-summary-title-input', '.item-summary-content-input');
                };

                // 빈 input 필드 찾기
                const findEmptyInput = (inputs) => {
                    for (const input of inputs) {
                        if (!input.value || input.value.trim().length === 0) {
                            return input;
                        }
                    }
                    return null;
                };

                // 이미지 첨부 여부 확인
                const hasImageAttached = () => {
                    return itemHighlightImageFileInput.getFiles().length > 0 || !!initialData.cloudItemHighlightImageUrl;
                };


                // 아이템 리스트 유효성 검사
                const validateItemList = () => {
                    // 아이템 요약 제목, 내용 중 1개라도 입력한 상태, 아이템 리스트 제목 또는 내용 미입력 상태
                    if (hasItemSummaryInput()) {
                        const itemListTitleInputs = document.querySelectorAll('.item-list-title-input');
                        const itemListContentInputs = document.querySelectorAll('.item-list-content-input');

                        const hasIncompletePair = Array.from(itemListTitleInputs).some((titleInput, index) => {
                            const title = titleInput.value.trim();
                            const content = itemListContentInputs[index]?.value.trim();
                            return (title || content) && !(title && content);
                        });

                        const hasCompleteItemList = Array.from(itemListTitleInputs).some((titleInput, index) => {
                            const title = titleInput.value.trim();
                            const content = itemListContentInputs[index]?.value.trim();
                            return title && content;
                        });

                        if (hasIncompletePair || !hasCompleteItemList) {
                            showValidationError('아이템 리스트를 입력해 주세요.', '아이템 요약을 입력한 경우, 아이템 리스트 입력은 필수입니다.');
                            return false;
                        }
                    }

                    // DOM 요소 선택
                    const itemSummaryContainer = document.querySelector('[data-component="item-summary-content"]');
                    const itemSummary = {
                        container: itemSummaryContainer,
                        titleInput: itemSummaryContainer?.querySelector('.item-summary-title-input'),
                        contentInput: itemSummaryContainer?.querySelector('.item-summary-content-input')
                    };

                    // 아이템 요약 제목 미입력한 상태, 아이템 요약 내용 입력 상태
                    if (itemSummary.titleInput && itemSummary.contentInput) {
                        const titleLength = itemSummary.titleInput.value.trim().length;
                        const contentLength = itemSummary.contentInput.value.trim().length;

                        if (titleLength === 0 && contentLength > 0) {
                            showValidationError('아이템 요약 제목을 입력해 주세요.', '아이템 요약 내용을 입력한 경우, 요약 제목 입력은 필수입니다.');
                            return false;
                        }

                        // 아이템 요약 제목 입력한 상태, 아이템 요약 내용 미입력 상태
                        if (titleLength > 0 && contentLength === 0) {
                            showValidationError('아이템 요약 내용을 입력해 주세요.', '아이템 요약 제목을 입력한 경우, 요약 내용 입력은 필수입니다.');
                            return false;
                        }
                    }

                    // 아이템 하이라이트 글자 수 제한 검증
                    if (!validateAndUpdateHighlightFields(hasImageAttached())) {
                        return false;
                    }

                    return true;
                };

                return validateItemList();
            };

            /**
             * 아이템 리스트 추가 및 삭제
             */
            const updateRowNumber = (row, num) => {
                row.querySelector('.ncua-align-center.ncua-text-align-center').textContent = num;
                ['Title', 'Content'].forEach(type => {
                    const prefix = `templateItemList${type}`;
                    const input = row.querySelector(`.item-list-${type.toLowerCase()}-input`);
                    if (input) {
                        input.setAttribute('data-charcount-key', `${prefix}${num}`);
                        input.setAttribute('name', `${prefix}${num}`);
                    }
                    row.querySelector(`[data-charcount-text*="${prefix}"]`)?.setAttribute('data-charcount-text', `${prefix}${num}`);
                });
            };

            // 아이템 리스트 번호 재정렬
            const reorderItemList = (tbody) => {
                tbody.querySelectorAll('tr').forEach((row, index) => updateRowNumber(row, index + 1));
            };

            // 페이지 로드 시 템플릿 캐싱
            const itemListTbody = document.querySelector('[data-component="item-list-tbody"]');
            const itemListRowTemplate = itemListTbody?.querySelector('tr')?.cloneNode(true);

            // 아이템 리스트 추가
            const addItemListBtnClick = () => {
                const tbody = document.querySelector('[data-component="item-list-tbody"]');
                if (!tbody || !itemListRowTemplate) return;

                const nextNum = tbody.querySelectorAll('tr').length + 1;
                if (nextNum > 10) return NCDSAlert({message: '아이템 리스트는 최대 10개까지 추가할 수 있습니다.', iconType: 'error'});

                // 미리 저장한 템플릿 사용
                const row = itemListRowTemplate.cloneNode(true);
                row.querySelectorAll('input[type="text"]').forEach(input => {
                    input.value = '';
                    input.disabled = false;
                });

                updateRowNumber(row, nextNum);
                tbody.appendChild(row);
            };

            const addItemListBtn = document.querySelector('.add-item-btn');
            if (addItemListBtn) {
                addItemListBtn.addEventListener('click', addItemListBtnClick);
            }

            // 아이템 리스트 삭제
            const canDeleteItem = (tbody) => {
                if (tbody.querySelectorAll('tr').length <= 2) {
                    NCDSAlert({message: '아이템 리스트는 2개 이상 입력해야 합니다.', iconType: 'error'});
                    return false;
                }
                return true;
            };

            if (itemListTbody) {
                itemListTbody.addEventListener('click', (event) => {
                    const deleteBtn = event.target.closest('.ncua-btn--secondary-gray');
                    if (!deleteBtn) return;

                    const tbody = event.currentTarget;
                    const targetRow = event.target.closest('tr');

                    if (canDeleteItem(tbody)) {
                        deleteItemRow(targetRow, tbody);
                    }
                });
            }

            const deleteItemRow = (row, tbody) => {
                row?.remove();
                reorderItemList(tbody);
            };

            // 초기 데이터로 아이템 리스트 렌더링 (수정/복제 모드, ITEM_LIST 타입만)
            const currentTemplateType = document.querySelector('input[name="templateType"]:checked')?.value;
            if (currentTemplateType === 'ITEM_LIST' && initialData.cloudItems && initialData.cloudItems.length > 0 && itemListTbody && itemListRowTemplate) {
                // 기존 행 삭제
                itemListTbody.innerHTML = '';

                initialData.cloudItems.forEach((item, index) => {
                    const row = itemListRowTemplate.cloneNode(true);
                    row.querySelectorAll('input[type="text"]').forEach(input => {
                        input.value = '';
                        input.disabled = false;
                    });

                    updateRowNumber(row, index + 1);

                    const titleInput = row.querySelector('.item-list-title-input');
                    const contentInput = row.querySelector('.item-list-content-input');
                    if (titleInput) titleInput.value = item.title || '';
                    if (contentInput) contentInput.value = item.description || '';

                    // 글자수 카운트 업데이트
                    const titleCount = row.querySelector('[data-charcount-text*="Title"] .ncua-input__field-text-count-current');
                    const contentCount = row.querySelector('[data-charcount-text*="Content"] .ncua-input__field-text-count-current');
                    if (titleCount) titleCount.textContent = (item.title || '').length;
                    if (contentCount) contentCount.textContent = (item.description || '').length;

                    itemListTbody.appendChild(row);
                });
            }

            /**
             * 마이앱 세부입력 URL 검증 버튼 활성/비활성 처리
             */
            const urlInput = document.querySelector('input[name="templateUrl"]');
            const urlCheckButton = document.querySelector('.url-check-button');

            if (urlInput && urlCheckButton) {
                const updateButtonState = () => {
                    const hasValue = urlInput.value.trim().length > 0;
                    urlCheckButton.disabled = !hasValue;
                };

                urlInput.addEventListener('input', updateButtonState);
                updateButtonState();
            }

            /**
             * 필수 입력 필드 유효성 검사
             */
            const validateRequiredFields = () => {
                // 상단 이미지 체크 (ImageFileInput 컴포넌트에서 직접 확인)
                const hasTopImage = (topImageFileInput?.getFiles()?.length > 0) || !!initialData.cloudImageUrl;

                // 단일 필드 체크
                const hasHeader = !!document.querySelector('input[name="templateHeader"]')?.value?.trim();

                // 아이템 하이라이트 쌍 체크 (제목과 내용이 모두 입력되어야 유효)
                const highlightTitle = document.querySelector('input[name="templateItemHighlightTitle"]')?.value?.trim();
                const highlightDesc = document.querySelector('input[name="templateItemHighlightDescription"]')?.value?.trim();
                const hasHighlightInput = !!highlightTitle && !!highlightDesc;

                const hasSingleFieldInput = hasTopImage || hasHeader || hasHighlightInput;

                // 아이템 리스트 필드 체크 (여러 행 포함, 제목과 내용이 쌍으로 입력되어야 함)
                const itemListTitles = document.querySelectorAll('input[name^="templateItemListTitle"]');
                const itemListContents = document.querySelectorAll('input[name^="templateItemListContent"]');
                const hasItemListInput = Array.from(itemListTitles).some((titleInput, index) => {
                    const title = titleInput.value.trim();
                    const content = itemListContents[index]?.value.trim() || '';
                    return title.length > 0 && content.length > 0;
                });

                // 아이템 리스트 제목/내용 쌍 체크 (한쪽만 입력된 경우)
                const hasIncompleteItemList = Array.from(itemListTitles).some((titleInput, index) => {
                    const title = titleInput.value.trim();
                    const content = itemListContents[index]?.value.trim() || '';
                    return (title.length > 0) !== (content.length > 0);
                });

                const itemSummaryTitleValue = document.querySelector('input[name="templateItemSummaryTitle"]')?.value.trim() || '';
                const itemSummaryContentValue = document.querySelector('input[name="templateItemSummaryContent"]')?.value.trim() || '';
                const hasNoItemSummary = itemSummaryTitleValue.length === 0 && itemSummaryContentValue.length === 0;

                if (hasIncompleteItemList && hasNoItemSummary) {
                    const incompleteRow = Array.from(itemListTitles).find((titleInput, index) => {
                        const title = titleInput.value.trim();
                        const content = itemListContents[index]?.value.trim() || '';
                        return title.length > 0 && content.length === 0;
                    });
                    if (incompleteRow) {
                        const index = Array.from(itemListTitles).indexOf(incompleteRow);
                        itemListContents[index]?.focus();
                        NCDSAlert({message: '아이템 리스트 내용을 입력해 주세요.', iconType: 'error'});
                    } else {
                        const emptyTitleIndex = Array.from(itemListTitles).findIndex((titleInput, index) => {
                            const title = titleInput.value.trim();
                            const content = itemListContents[index]?.value.trim() || '';
                            return title.length === 0 && content.length > 0;
                        });
                        itemListTitles[emptyTitleIndex]?.focus();
                        NCDSAlert({message: '아이템 리스트 제목을 입력해 주세요.', iconType: 'error'});
                    }
                    return false;
                }

                // 아이템 요약 제목 또는 내용이 입력된 경우, 아이템 리스트 제목/내용 쌍 체크
                if (hasIncompleteItemList && !hasNoItemSummary) {
                    const incompleteRow = Array.from(itemListTitles).find((titleInput, index) => {
                        const title = titleInput.value.trim();
                        const content = itemListContents[index]?.value.trim() || '';
                        return title.length > 0 && content.length === 0;
                    });
                    if (incompleteRow) {
                        const index = Array.from(itemListTitles).indexOf(incompleteRow);
                        itemListContents[index]?.focus();
                        NCDSAlert({message: '아이템 리스트 내용을 입력해 주세요.', iconType: 'error'});
                    } else {
                        const emptyTitleIndex = Array.from(itemListTitles).findIndex((titleInput, index) => {
                            const title = titleInput.value.trim();
                            const content = itemListContents[index]?.value.trim() || '';
                            return title.length === 0 && content.length > 0;
                        });
                        itemListTitles[emptyTitleIndex]?.focus();
                        NCDSAlert({message: '아이템 리스트 제목을 입력해 주세요.', iconType: 'error'});
                    }
                    return false;
                }

                // 아이템 리스트 개수 체크 (0개 또는 2개 이상, 1개만 있으면 안됨)
                const itemListCount = Array.from(itemListTitles).filter((titleInput, index) => {
                    const title = titleInput.value.trim();
                    const content = itemListContents[index]?.value.trim() || '';
                    return title.length > 0 && content.length > 0;
                }).length;

                if (itemListCount === 1) {
                    const firstFilledTitle = Array.from(itemListTitles).find((titleInput, index) => {
                        const title = titleInput.value.trim();
                        const content = itemListContents[index]?.value.trim() || '';
                        return title.length > 0 && content.length > 0;
                    });
                    firstFilledTitle?.focus();
                    NCDSAlert({
                        message: '아이템 리스트는 2개 이상 입력해야 합니다.',
                        iconType: 'error'
                    });
                    return false;
                }

                // 아이템 요약 필드 체크 (제목과 내용이 쌍으로 입력되어야 함)
                const itemSummaryTitle = document.querySelector('input[name="templateItemSummaryTitle"]');
                const itemSummaryContent = document.querySelector('input[name="templateItemSummaryContent"]');
                const hasItemSummaryInput = itemSummaryTitle?.value.trim().length > 0 && itemSummaryContent?.value.trim().length > 0;

                // 아이템 요약 제목/내용 쌍 체크 (한쪽만 입력된 경우)
                const summaryTitleFilled = itemSummaryTitle?.value.trim().length > 0;
                const summaryContentFilled = itemSummaryContent?.value.trim().length > 0;
                if (summaryTitleFilled !== summaryContentFilled) {
                    if (summaryTitleFilled && !summaryContentFilled) {
                        itemSummaryContent?.focus();
                        NCDSAlert({message: '아이템 요약 내용을 입력해 주세요.', subMessage:'아이템 요약 제목을 입력한 경우, 요약 내용 입력은 필수 입니다.', iconType: 'error'});
                    } else {
                        itemSummaryTitle?.focus();
                        NCDSAlert({message: '아이템 요약 제목을 입력해 주세요.', subMessage:'아이템 요약 내용을 입력한 경우, 요약 제목 입력은 필수 입니다.', iconType: 'error'});
                    }
                    return false;
                }

                const hasAnyInput = hasSingleFieldInput || hasItemListInput || hasItemSummaryInput;

                if (!hasAnyInput) {
                    NCDSAlert({
                        message: '상단 이미지, 헤더, 아이템 하이라이트, 아이템 리스트 중 1개 이상은 필수로 입력해야 합니다.',
                        iconType: 'error'
                    });
                    return false;
                }

                const templateItemSummaryContent = document.querySelector('input[name="templateItemSummaryContent"]')?.value || '';
                if (templateItemSummaryContent) {
                    // 숫자와 통화기호, 통화단위 및 일부 특수기호(`,`, `.`)만 입력 가능 (숫자 최소 1개 필수)
                    const isValidSummaryContent = /^[\p{Sc}원,.]*[0-9][\p{Sc}0-9,.원]*$/u.test(templateItemSummaryContent);

                    if (!isValidSummaryContent) {
                        document.querySelector('input[name="templateItemSummaryContent"]')?.focus();
                        NCDSAlert({
                            message: '아이템 요약정보 내용은 숫자와 화폐 기호(₩, $, 원 등)를 반드시 포함해야 하며, 숫자, 쉼표, 마침표만 입력 가능합니다.',
                            iconType: 'error'
                        });
                        return false;
                    }
                }

                return true;
            };

            /**
             * 검증 에러 표시 (공통)
             */
            const showValidationError = (message, subMessage = null, focusTarget = null) => {
                NCDSAlert({
                    message,
                    iconType: 'error',
                    ...(subMessage && {subMessage}),
                    ...(focusTarget && {callback: () => focusTarget.focus()}),
                });
            };

            /**
             * 알림톡 버튼 URL http:// 또는 https:// 프로토콜 검증
             * @returns {HTMLInputElement|null} 프로토콜이 잘못된 첫 번째 input 요소, 없으면 null
             */
            const findInvalidProtocolInput = () => {
                return Array.from(document.querySelectorAll('.button-url-input'))
                    .find(input => {
                        const url = input.value?.trim();
                        return url && !/^https?:\/\//.test(url);
                    }) || null;
            };

            /**
             * 아이템 리스트형 필드 에러 표시 및 검증
             * validateRequiredFields, validateItemSummaryAndList 검증 후 에러 UI 표시
             * @returns {boolean} 검증 통과 여부
             */
            // 아이템 리스트형 수동 에러 표시 대상 필드
            const itemListErrorFields = [
                {selector: 'input[name="templateHeader"]', message: '헤더를 입력해 주세요.', group: 'header'},
                {selector: 'input[name="templateItemHighlightTitle"]', message: '아이템 하이라이트 제목을 입력해 주세요.', group: 'highlight'},
                {selector: 'input[name="templateItemHighlightDescription"]', message: '아이템 하이라이트 내용을 입력해 주세요.', group: 'highlight'},
                {selector: 'input[name^="templateItemListTitle"]', message: '아이템 리스트 제목을 입력해 주세요.', group: 'itemList'},
                {selector: 'input[name^="templateItemListContent"]', message: '아이템 리스트 내용을 입력해 주세요.', group: 'itemList'},
                {selector: 'textarea[name="kakaoMessageInput"]', message: '본문을 입력해 주세요.', group: 'body'},
            ];

            // 그룹별 입력 여부 확인 함수 맵 (맵에 없는 그룹은 항상 검증 대상)
            const fieldGroupChecks = {
                header: () => !!document.querySelector('input[name="templateHeader"]')?.value?.trim(),
                highlight: () => {
                    const title = document.querySelector('input[name="templateItemHighlightTitle"]')?.value?.trim();
                    const desc = document.querySelector('input[name="templateItemHighlightDescription"]')?.value?.trim();
                    return !!title && !!desc;
                },
                itemList: () => {
                    const titles = document.querySelectorAll('input[name^="templateItemListTitle"]');
                    const contents = document.querySelectorAll('input[name^="templateItemListContent"]');
                    return Array.from(titles).some((t, i) => t.value.trim() || contents[i]?.value.trim());
                },
            };

            // 아이템 리스트형 수동 에러 표시 필드의 에러 상태 초기화
            const clearItemListFieldErrors = (validator) => {
                const fieldNames = new Set();
                const form = document.getElementById('templateRegisterForm');

                itemListErrorFields.forEach(field => {
                    document.querySelectorAll(field.selector).forEach(el => {
                        NCDSValidator.unhighlight(el);
                        const name = el.id || el.name;
                        if (!name) return;
                        fieldNames.add(name);
                        form?.querySelector('label.error[for="' + name + '"]')?.remove();
                    });
                });

                if (!validator) return;
                validator.errorList = validator.errorList.filter(e => !fieldNames.has(e.element?.id || e.element?.name));
                fieldNames.forEach(name => { delete validator.errorMap[name]; delete validator.invalid[name]; delete validator.submitted[name]; });
            };

            // 그룹 입력 여부 조회 (결과 캐싱, 맵에 없는 그룹은 항상 true)
            const getGroupInputStatus = () => {
                const cache = {};
                return (group) => {
                    if (!(group in cache)) {
                        const checker = fieldGroupChecks[group];
                        cache[group] = checker ? checker() : true;
                    }
                    return cache[group];
                };
            };

            // 아이템 리스트형 빈 필드에 에러 추가
            const addEmptyFieldErrors = (validator) => {
                if (!validator) return;

                const hasGroupInput = getGroupInputStatus();
                const hasTopImage = (topImageFileInput?.getFiles()?.length > 0) || !!initialData.cloudImageUrl;
                const hasAnyInput = hasTopImage || Object.keys(fieldGroupChecks).some(hasGroupInput);

                const shouldValidate = (group) => !hasAnyInput || hasGroupInput(group);

                for (const field of itemListErrorFields) {
                    if (!shouldValidate(field.group)) continue;

                    for (const el of document.querySelectorAll(field.selector)) {
                        if (el.value?.trim()) continue;
                        validator.errorList.push({message: field.message, element: el});
                        validator.errorMap[el.name] = field.message;
                        validator.submitted[el.name] = field.message;
                    }
                }

                if (validator.settings.showErrors) {
                    validator.settings.showErrors.call(validator, validator.errorMap, validator.errorList);
                } else {
                    validator.defaultShowErrors();
                }
            };

            // 아이템리스트형 검증: 필수 필드 → 요약/리스트 정합성 → 본문
            const showItemListFieldErrors = (validator) => {
                clearItemListFieldErrors(validator);

                const bodyContentEl = document.querySelector('textarea[name="kakaoMessageInput"]');
                const validations = [
                    {
                        check: () => validateRequiredFields(),
                        onFail: () => addEmptyFieldErrors(validator),
                    },
                    {
                        check: () => validateItemSummaryAndList(),
                    },
                    {
                        check: () => !!bodyContentEl?.value?.trim(),
                        onFail: () => {
                            addEmptyFieldErrors(validator);
                            bodyContentEl?.focus();
                        },
                    },
                ];

                for (const {check, onFail} of validations) {
                    if (check()) continue;
                    onFail?.();
                    return false;
                }

                return true;
            };

            /**
             * jQuery Validator 초기화
             */
            let validatorInstance = null;
            const initValidator = () => {
                if (typeof $ === 'undefined' || typeof $.validator === 'undefined') {
                    return;
                }

                $.validator.addMethod('requireTopImage', function (value, element, param) {
                    if (!param) return true;
                    return (topImageFileInput?.getFiles()?.length > 0) || !!initialData.cloudImageUrl;
                });

                const validatorConfig = {
                    dialog: false,
                    ignore: ':disabled',
                    rules: {
                        templateTitle: "required",
                        templateTopImage: {
                            requireTopImage: function () {
                                const sendMethod = document.querySelector('input[name="sendMethod"]:checked')?.value || initialData.sendMethod;
                                if (sendMethod !== 'kakao') return false;
                                const templateType = document.querySelector('input[name="templateType"]:checked')?.value || 'NONE';
                                return templateType === 'IMAGE';
                            }
                        },
                        templateCategory1: {
                            required: function (element) {
                                const sendMethod = document.querySelector('input[name="sendMethod"]:checked')?.value || initialData.sendMethod;
                                return sendMethod === 'kakao';
                            }
                        },
                        templateTypeTitle: {
                            required: function (element) {
                                const sendMethod = document.querySelector('input[name="sendMethod"]:checked')?.value || initialData.sendMethod;
                                if (sendMethod !== 'kakao') return false;
                                const templateType = document.querySelector('input[name="templateType"]:checked')?.value || 'NONE';
                                return templateType === 'TEXT';
                            }
                        },
                        templateTypeSubTitle: {
                            required: function (element) {
                                const sendMethod = document.querySelector('input[name="sendMethod"]:checked')?.value || initialData.sendMethod;
                                if (sendMethod !== 'kakao') return false;
                                const templateType = document.querySelector('input[name="templateType"]:checked')?.value || 'NONE';
                                return templateType === 'TEXT';
                            }
                        },
                        smsMessageInput: {
                            required: function (element) {
                                const sendMethod = document.querySelector('input[name="sendMethod"]:checked')?.value || initialData.sendMethod;
                                return sendMethod === 'sms';
                            }
                        },
                        templateContentMyappTitle: {
                            required: function (element) {
                                const sendMethod = document.querySelector('input[name="sendMethod"]:checked')?.value || initialData.sendMethod;
                                return sendMethod === 'myapp';
                            }
                        },
                        myappMessageInput: {
                            required: function (element) {
                                const sendMethod = document.querySelector('input[name="sendMethod"]:checked')?.value || initialData.sendMethod;
                                return sendMethod === 'myapp';
                            }
                        },
                        templateButtonName: "required",
                        templateButtonUrl: "required",
                        templateDeliveryButtonName: "required",
                        kakaoMessageInput: {
                            required: function (element) {
                                const sendMethod = document.querySelector('input[name="sendMethod"]:checked')?.value || initialData.sendMethod;
                                if (sendMethod !== 'kakao') return false;
                                const templateType = document.querySelector('input[name="templateType"]:checked')?.value || 'NONE';
                                return templateType !== 'ITEM_LIST';
                            }
                        }
                    },
                    messages: {
                        templateCategory1: {
                            required: "카테고리를 선택해 주세요."
                        },
                        templateTitle: {
                            required: "템플릿 제목을 입력해 주세요."
                        },
                        templateTypeTitle: {
                            required: "제목을 입력해 주세요."
                        },
                        templateTypeSubTitle: {
                            required: "부제목을 입력해 주세요."
                        },
                        smsMessageInput: {
                            required: "내용을 입력해 주세요."
                        },
                        kakaoMessageInput: {
                            required: "본문을 입력해 주세요."
                        },
                        templateContentMyappTitle: {
                            required: "푸시 제목을 입력해 주세요."
                        },
                        myappMessageInput: {
                            required: "푸시 내용을 입력해 주세요."
                        },
                        templateButtonName: {
                            required: "버튼명을 입력해 주세요."
                        },
                        templateButtonUrl: {
                            required: "URL을 입력해 주세요."
                        },
                        templateDeliveryButtonName: {
                            required: "버튼명을 입력해 주세요."
                        },
                    },
                    invalidHandler: function (event, validator) {
                        const templateTypeRadio = document.querySelector('input[name="templateType"]:checked');

                        // IMAGE 타입: 상단 이미지 미등록 시 highlight + alert (ITEM_LIST는 submitHandler에서 별도 검증)
                        if (validator.errorMap.templateTopImage && templateTypeRadio?.value !== 'ITEM_LIST') {
                            const topImageBtn = document.querySelector('#top-image-file-container .ncua-image-file-input');
                            if (topImageBtn) {
                                NCDSValidator.highlight(topImageBtn);
                            }
                            NCDSAlert({message: '상단 이미지를 등록해 주세요.', iconType: 'error'});
                        }

                        if (!templateTypeRadio || templateTypeRadio.value !== 'ITEM_LIST') {
                            return false;
                        }

                        // standard 검증 실패 시 필드 하이라이트만 추가 (NCDSAlert는 submitHandler에서 처리)
                        clearItemListFieldErrors(validator);
                        addEmptyFieldErrors(validator);

                        // 기본 alert 방지
                        return false;
                    },
                    submitHandler: function (form) {
                        const submitBtn = form.querySelector('button[type="submit"]');
                        try {
                            if (submitBtn.disabled) return false;
                            submitBtn.disabled = true;

                            const sendMethod = document.querySelector('input[name="sendMethod"]:checked')?.value || initialData.sendMethod;
                            const templateTypeRadio = document.querySelector('input[name="templateType"]:checked');

                            // ITEM_LIST 타입일 때 추가 검증
                            if (templateTypeRadio && templateTypeRadio.value === 'ITEM_LIST') {
                                if (!showItemListFieldErrors(validatorInstance)) {
                                    submitBtn.disabled = false;
                                    return false;
                                }
                            }

                            // 알림톡: 버튼 URL http:// 또는 https:// 프로토콜 검증
                            if (sendMethod === 'kakao') {
                                const invalidInput = findInvalidProtocolInput();
                                if (invalidInput) {
                                    submitBtn.disabled = false;
                                    showValidationError('http:// 또는 https://로 시작하는 URL을 입력해 주세요.', null, invalidInput);
                                    return false;
                                }
                            }

                            const mode = initialData.mode || 'register';
                            const templateCode = initialData.templateCode || '';
                            const formData = new FormData();

                            // 공통 필드 (mode, templateCode)
                            formData.append('mode', mode);
                            if (templateCode) {
                                formData.append('templateCode', templateCode);
                            }

                            // 공통 성공/실패 핸들러
                            const handleSuccess = (responseData) => {
                                sessionStorage.setItem('pendingToast', JSON.stringify({message: '템플릿이 저장되었습니다.', color: 'success'}));
                                window.unsavedGuard?.reset();
                                const provider = getProvider();
                                const redirectForm = document.createElement('form');
                                redirectForm.method = 'POST';
                                redirectForm.action = '/crm/message_template.php';
                                redirectForm.appendChild(createHiddenInput('sendMethod', sendMethod));
                                redirectForm.appendChild(createHiddenInput('provider', provider));
                                document.body.appendChild(redirectForm);
                                redirectForm.submit();
                            };

                            const handleError = (message) => {
                                submitBtn.disabled = false;
                                NCDSAlert({
                                    message: message || '저장에 실패했습니다.',
                                    iconType: 'error'
                                });
                            };

                            const escapeLineBreaks = (str) => str ? str.replace(/\r?\n/g, '\\r\\n') : str;

                            const submitForm = () => {
                                fetch('/crm/message_template_register_ps.php', {
                                    method: 'POST',
                                    body: formData
                                })
                                    .then(response => response.json())
                                    .then(data => {
                                        if (data.success) {
                                            handleSuccess(data);
                                        } else {
                                            handleError(data.message);
                                        }
                                    })
                                    .catch(error => {
                                        console.error('Error:', error);
                                        handleError('저장 중 오류가 발생했습니다.');
                                    });
                            };

                            if (sendMethod === 'sms') {
                                // SMS/LMS 템플릿 저장
                                formData.append('sendMethod', sendMethod);
                                formData.append('templateCategory', document.querySelector('input[name="templateCategory"]:checked')?.value || '');
                                formData.append('templateTitle', document.querySelector('input[name="templateTitle"]')?.value || '');
                                formData.append('templateMessageContent', escapeLineBreaks(document.querySelector('textarea[name="smsMessageInput"]')?.value || ''));

                                submitForm();
                            } else if (sendMethod === 'kakao') {
                                // 카카오 알림톡 템플릿 저장
                                const provider = getProvider();
                                const templateType = document.querySelector('input[name="templateType"]:checked')?.value || 'NONE';

                                formData.append('sendMethod', sendMethod);
                                formData.append('provider', provider);
                                formData.append('templateBasicType', document.querySelector('input[name="templateBasicType"]:checked')?.value || '');
                                formData.append('templateCategory1', document.querySelector('select[name="templateCategory1"]')?.value || '');
                                formData.append('templateCategory2', document.querySelector('select[name="templateCategory2"]')?.value || '');
                                formData.append('templateTitle', document.querySelector('input[name="templateTitle"]')?.value || '');
                                formData.append('securityTemplate', document.querySelector('input[name="securityTemplate"]:checked')?.value || 'n');
                                formData.append('templateType', templateType);
                                formData.append('templateBodyContent', escapeLineBreaks(document.querySelector('textarea[name="kakaoMessageInput"]')?.value || ''));
                                const hasExtraInfo = document.getElementById('extraInfoCheckbox')?.checked;
                                formData.append('templateExtra', hasExtraInfo ? escapeLineBreaks(document.getElementById('extraInfoTextarea')?.value || '') : '');
                                formData.append('hasChannelAdd', document.getElementById('channelAddCheckbox')?.checked ? 'y' : 'n');

                                // 템플릿 타입별 추가 필드
                                if (templateType === 'TEXT') {
                                    formData.append('templateTypeTitle', document.querySelector('input[name="templateTypeTitle"]')?.value || '');
                                    formData.append('templateTypeSubTitle', document.querySelector('input[name="templateTypeSubTitle"]')?.value || '');
                                }

                                if (templateType === 'IMAGE' || templateType === 'ITEM_LIST') {
                                    // 상단 이미지 파일 전달
                                    const topImageFile = topImageFileInput.getFiles()[0];
                                    if (topImageFile) {
                                        formData.append('templateTopImageFile', topImageFile);
                                    } else if (initialData.cloudImageUrl) {
                                        // 새 파일이 없으면 기존 이미지 URL 전달
                                        formData.append('templateImageUrl', initialData.cloudImageUrl);
                                        formData.append('templateImageName', initialData.cloudImageName || '');
                                    }
                                }

                                if (templateType === 'ITEM_LIST') {
                                    formData.append('templateHeader', document.querySelector('input[name="templateHeader"]')?.value || '');

                                    // 아이템 하이라이트 이미지 파일 전달
                                    const highlightImageFile = itemHighlightImageFileInput.getFiles()[0];
                                    if (highlightImageFile) {
                                        formData.append('templateItemHighlightImageFile', highlightImageFile);
                                    } else if (initialData.cloudItemHighlightImageUrl) {
                                        // 새 파일이 없으면 기존 이미지 URL 전달
                                        formData.append('templateItemHighlightImageUrl', initialData.cloudItemHighlightImageUrl);
                                    }

                                    formData.append('templateItemHighlightTitle', document.querySelector('input[name="templateItemHighlightTitle"]')?.value || '');
                                    formData.append('templateItemHighlightDescription', document.querySelector('input[name="templateItemHighlightDescription"]')?.value || '');
                                    formData.append('templateItemSummaryTitle', document.querySelector('input[name="templateItemSummaryTitle"]')?.value || '');
                                    formData.append('templateItemSummaryContent', document.querySelector('input[name="templateItemSummaryContent"]')?.value || '');

                                    // 아이템 리스트 수집
                                    const itemListRows = document.querySelectorAll('[data-component="item-list-tbody"] tr');
                                    const itemList = [];
                                    itemListRows.forEach((row) => {
                                        const title = row.querySelector('.item-list-title-input')?.value || '';
                                        const content = row.querySelector('.item-list-content-input')?.value || '';
                                        if (title || content) {
                                            itemList.push({title, content});
                                        }
                                    });
                                    formData.append('itemList', JSON.stringify(itemList));
                                }

                                // 버튼 수집
                                const pageLinkButtons = [];
                                document.querySelectorAll('#pageLinkTableBody tr[data-row-id]').forEach(row => {
                                    const name = row.querySelector('.page-link-button-input')?.value || '';
                                    const url = row.querySelector('.button-url-input')?.value || '';
                                    if (name && url) {
                                        pageLinkButtons.push({type: 'WL', name, url});
                                    }
                                });
                                formData.append('pageLinkButtons', JSON.stringify(pageLinkButtons));

                                const deliveryButtons = [];
                                document.querySelectorAll('#deliveryTableBody tr[data-row-id]').forEach(row => {
                                    const name = row.querySelector('.delivery-button-name-input')?.value || '';
                                    if (name) {
                                        deliveryButtons.push({type: 'DS', name});
                                    }
                                });
                                formData.append('deliveryButtons', JSON.stringify(deliveryButtons));

                                submitForm();
                            } else if (sendMethod === 'myapp') {
                                // 마이앱(앱푸시) 템플릿 저장
                                formData.append('sendMethod', sendMethod);
                                formData.append('templateCategory', document.querySelector('input[name="templateCategory"]:checked')?.value || '');
                                formData.append('templateTitle', document.querySelector('input[name="templateTitle"]')?.value || '');
                                formData.append('templateContentMyappTitle', document.querySelector('input[name="templateContentMyappTitle"]')?.value || '');
                                formData.append('templateContentMyappContent', escapeLineBreaks(document.querySelector('textarea[name="myappMessageInput"]')?.value || ''));
                                formData.append('templateWithdrawalMethod', document.querySelector('input[name="templateWithdrawalMethod"]')?.value || '');

                                const withdrawalImageFile = document.querySelector('input[name="templateWithdrawalImage"]')?.files?.[0];
                                if (withdrawalImageFile && withdrawalImageFile.size > 0) {
                                    formData.append('templateWithdrawalImage', withdrawalImageFile);
                                } else if (initialData.myappImage) {
                                    // 새 파일이 없으면 기존 이미지 URL 전송
                                    formData.append('pushImageUrl', initialData.myappImage);
                                }

                                // URL 값 전달 (입력값이 있으면 검증 필수)
                                const templateUrlInput = document.getElementById('templateUrlInput');
                                const templateUrlValue = templateUrlInput?.value?.trim() || '';

                                if (templateUrlValue && (!templateUrlInput.getValidatedUrl || templateUrlInput.getValidatedUrl() !== templateUrlValue)) {
                                    NCDSAlert({
                                        message: '입력한 푸시 URL을 검증해주세요.',
                                        iconType: 'warning'
                                    });
                                    submitBtn.disabled = false;
                                    return;
                                }

                                formData.append('templateUrl', templateUrlValue);

                                submitForm();
                            }
                        } catch (error) {
                            if (submitBtn) submitBtn.disabled = false;
                            NCDSAlert({
                                message: '저장 처리 중 오류가 발생했습니다.',
                                iconType: 'error'
                            });
                        }

                        // 기본 form submit 방지
                        return false;
                    },
                    showErrors: function (errorMap, errorList) {
                        if (typeof NCDSValidator !== 'undefined' && NCDSValidator.showErrors) {
                            NCDSValidator.showErrors(errorMap, errorList);
                        }
                        this.defaultShowErrors();
                    },
                    highlight: function (element) {
                        if (typeof NCDSValidator !== 'undefined' && NCDSValidator.highlight) {
                            NCDSValidator.highlight(element);
                        }
                    },
                    unhighlight: function (element) {
                        if (typeof NCDSValidator !== 'undefined' && NCDSValidator.unhighlight) {
                            NCDSValidator.unhighlight(element);
                        }
                    }
                };

                // Validator 인스턴스 초기화
                validatorInstance = $('#templateRegisterForm').validate(validatorConfig);
            };

            // jQuery가 로드될 때까지 대기
            if (typeof $ !== 'undefined') {
                initValidator();
            } else {
                // jQuery가 아직 로드되지 않은 경우 대기
                const checkJQuery = setInterval(() => {
                    if (typeof $ !== 'undefined') {
                        clearInterval(checkJQuery);
                        initValidator();
                    }
                }, 100);

                // 최대 5초 대기
                setTimeout(() => {
                    clearInterval(checkJQuery);
                }, 5000);
            }

        } catch (error) {
            console.error(error);
        }

        // 초기화 완료 후 폼 상태를 변경 감지 기준점으로 갱신
        window.unsavedGuard?.reset();
    };


    const DOM_READY_STATES = ['complete', 'interactive'];

    if (DOM_READY_STATES.includes(document.readyState)) {
        onReady();
    } else {
        document.addEventListener('DOMContentLoaded', onReady);
    }

    /**
     * 저장하지 않은 변경사항 이탈 방지
     */
    try {
        window.unsavedGuard = new UnsavedChangesGuard('#templateRegisterForm', (proceed) => {
            NCDSConfirm({
                message: '페이지를 이동하시겠습니까?',
                subMessage: '저장하지 않은 내용이 있습니다.<br/>페이지를 이동하면 설정한 내용이 모두 사라집니다.',
                btnText: {
                    confirmLabel: '이동',
                    cancelLabel: '취소'
                },
                callback: (result) => {
                    if (result) proceed();
                }
            });
        });

        // 폼 데이터 직렬화 비교로 실제 값 변경만 감지
        const guardForm = document.querySelector('#templateRegisterForm');
        if (guardForm) {
            const serializeForm = () => JSON.stringify(
                [...new FormData(guardForm).entries()]
                    .map(([k, v]) => [k, v instanceof File ? v.name + v.size : v])
                    .sort((a, b) => a[0].localeCompare(b[0]))
            );
            const originalReset = window.unsavedGuard.reset.bind(window.unsavedGuard);

            window.unsavedGuard.isChanged = () => serializeForm() !== window.unsavedGuard._snapshotState;
            window.unsavedGuard.reset = () => { window.unsavedGuard._snapshotState = serializeForm(); originalReset(); };
            window.unsavedGuard.reset();
        }
    } catch {
        // ignore
    }
</script>

<script type="text/javascript">
    const code = '251219002';
    if (window.GodoCosGuide) {
        window.GodoCosGuide.init({
            apiUrl: <?= json_encode($cosGuideApiUrl ?? null) ?>,
            guideCode: code,
        });
    }
    const requestData = JSON.parse('<?= json_encode($request->toArray(), JSON_UNESCAPED_UNICODE) ?>');

    // hidden input 생성
    function createHiddenInput(name, value) {
        const input = document.createElement('input');
        input.type = 'hidden';
        input.name = name;
        input.value = value;
        return input;
    }

    function update_template() {
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = './message_template.php';
        const searchData = JSON.parse('<?= json_encode($request->getSearch(), JSON_UNESCAPED_UNICODE) ?>');
        if (searchData) {
            Object.entries(searchData).forEach(([key, value]) => {
                if (Array.isArray(value)) {
                    value.forEach(item => form.appendChild(createHiddenInput(`${key}[]`, item)));
                } else {
                    form.appendChild(createHiddenInput(key, value));
                }
            });
        }
        document.body.appendChild(form);
        form.submit();
    }

    function go_to_list() {
        const sendMethod = document.querySelector('input[name="sendMethod"]:checked')?.value || 'sms';
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = './message_template.php';
        form.appendChild(createHiddenInput('sendMethod', sendMethod));
        document.body.appendChild(form);
        form.submit();
    }

    document.querySelector('.js-btn-back')?.addEventListener('click', function () {
        update_template();
    });

    document.querySelector('.js-btn-list')?.addEventListener('click', function () {
        go_to_list();
    });

    // 파라미터와 함께 페이지 리다이렉트
    function redirectWithParams(key, value, isAll = true) {
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = '';

        if (isAll) {
            Object.entries(requestData).forEach(([k, v]) => {
                if (k === 'search') {
                    form.appendChild(createHiddenInput(k, JSON.stringify(v)));
                } else if (Array.isArray(v)) {
                    v.forEach(item => form.appendChild(createHiddenInput(`${k}[]`, item)));
                } else {
                    form.appendChild(createHiddenInput(k, v));
                }
            });
        }

        form.appendChild(createHiddenInput(key, value));
        document.body.appendChild(form);
        form.submit();
    }

    document.addEventListener('DOMContentLoaded', function () {
        // SMS 내용 입력 시 "SMS 건당 1포인트 차감" 문구 표시/숨김
        const smsTextarea = document.querySelector('textarea[name="templateMessageContent"]');
        const smsByteHint = document.querySelector('.sms-byte-mode-hint');
        if (smsTextarea && smsByteHint) {
            smsTextarea.addEventListener('input', function () {
                smsByteHint.style.display = this.value.trim() ? '' : 'none';
            });
        }

        document.querySelectorAll('.js-variable-category-select').forEach(function (select) {
            // 초기 로드 시 선택된 카테고리 표시
            const initialValue = select.value;
            const chipSelector = select.closest('.chip-selector');
            chipSelector.querySelectorAll('.chip-button-wrap').forEach(function (wrap) {
                if (wrap.dataset.categoryGroup === initialValue) {
                    wrap.style.display = 'flex';
                } else {
                    wrap.style.display = 'none';
                }
            });

            // change 이벤트 리스너
            select.addEventListener('change', function (e) {
                const selectedValue = e.target.value;
                const chipSelector = e.target.closest('.chip-selector');

                chipSelector.querySelectorAll('.chip-button-wrap').forEach(function (wrap) {
                    if (wrap.dataset.categoryGroup === selectedValue) {
                        wrap.style.display = 'flex';
                    } else {
                        wrap.style.display = 'none';
                    }
                });
            });
        });

        // 마이앱 URL 검증 및 미리보기 버튼
        const validateUrlBtn = document.getElementById('validateUrlBtn');
        const templateUrlInput = document.getElementById('templateUrlInput');

        if (validateUrlBtn && templateUrlInput) {
            let validatedUrl = null; // 검증된 URL 저장

            // 초기 상태: 입력값 없으면 비활성화
            validateUrlBtn.disabled = !templateUrlInput.value.trim();

            // 입력값 변경 시 검증 상태 확인
            templateUrlInput.addEventListener('input', function () {
                const currentValue = this.value.trim();
                validateUrlBtn.disabled = !currentValue;

                // 검증된 URL과 다르면 검증 상태 초기화
                if (validatedUrl && currentValue !== validatedUrl) {
                    validatedUrl = null;
                }
            });

            // 검증된 URL 가져오기 (폼 제출 시 사용)
            templateUrlInput.getValidatedUrl = function () {
                const currentValue = this.value.trim();
                if (validatedUrl && currentValue === validatedUrl) {
                    return validatedUrl;
                }
                return null;
            };

            validateUrlBtn.addEventListener('click', async function () {
                const path = templateUrlInput.value.trim();

                if (!path) {
                    NCDSAlert({message: 'URL 경로를 입력해주세요.', iconType: 'warning'});
                    return;
                }

                // URL 생성
                const mallDomain = '<?= rtrim(URI_MOBILE, '/') ?>';
                const url = path.startsWith('http') ? path : mallDomain + path;

                validateUrlBtn.disabled = true;
                const originalText = validateUrlBtn.textContent;
                validateUrlBtn.textContent = '확인 중...';

                try {
                    const response = await fetch(url, {method: 'HEAD'});
                    if (response.ok) {
                        validatedUrl = path; // 검증 성공 시 저장
                        window.open(url, 'myappUrlPreview', 'width=430,height=800,scrollbars=yes,resizable=yes');
                    } else {
                        validatedUrl = null;
                        NCDSAlert({
                            message: 'URL에 접근할 수 없습니다.\n경로를 확인해주세요.\n\n' + url,
                            iconType: 'error'
                        });
                    }
                } catch (error) {
                    // CORS 에러일 경우 검증 성공으로 처리
                    validatedUrl = path;
                    window.open(url, 'myappUrlPreview', 'width=430,height=800,scrollbars=yes,resizable=yes');
                } finally {
                    validateUrlBtn.disabled = false;
                    validateUrlBtn.textContent = originalText;
                }
            });
        }
    });
</script>
