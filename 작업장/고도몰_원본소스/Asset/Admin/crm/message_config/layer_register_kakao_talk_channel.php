<link rel="stylesheet" href="<?= gd_set_browser_cache(PATH_ADMIN_GD_SHARE . 'ncds/css/crm/layer-register-kakao-channel.css') ?>">
<script defer src="<?= gd_set_browser_cache(PATH_ADMIN_GD_SHARE . 'ncds/script/common/charcount.js') ?>"></script>
<script defer src="<?= gd_set_browser_cache(PATH_ADMIN_GD_SHARE . 'ncds/script/common/ncds-jquery-validator.js') ?>"></script>

<?php foreach ($secondLevelCategories as $cateCode => $cateName): ?>
    <input type="hidden" id="cate2<?= $cateCode ?>" value="<?= $cateName ?>" />
<?php endforeach; ?>

<?php foreach ($thirdLevelCategories as $cateCode => $cateName): ?>
    <input type="hidden" id="cate3<?= $cateCode ?>" value="<?= $cateName ?>" />
<?php endforeach; ?>

<article class="ncua-content layer-register-kakao-channel">
    <!-- 통합 폼 -->
    <form id="registerKakaoChannelForm">
        <input type="hidden" name="formType" id="formType" value="">
        <input type="hidden" id="sender" name="sender" value="<?= $sender ?>">
        <div class="ncua-search-result">
            <div class="ncua-table ncua-table--vertical">
                <table>
                    <colgroup>
                        <col width="144px">
                        <col>
                    </colgroup>
                    <tr>
                        <th>
                            <div data-tooltip-seq="001" class="ncua-flex ncua-align-items-center">
                                카카오톡 채널
                            </div>
                        </th>
                        <td>
                            <div class="ncua-flex ncua-flex-column ncua-gap-4 kakao-channel-wrap">
                                <div class="ncua-input ncua-input--xs ncua-input-full-width" data-show-hint-text="true">
                                    <div class="ncua-input__content-wrap">
                                        <div class="ncua-input__field ncua-input__field--xs">
                                            <input type="text" name="plusId" id="plusId"
                                                class="kakaoChannel js-kakao-talk-channel-name"
                                                data-charcount-key="kakaoChannel" 
                                                placeholder="예) @고도몰" 
                                                maxlength="30"
                                                required>
                                        </div>
                                        <div class="ncua-input__field-text-count" data-charcount-text="kakaoChannel">
                                            <output class="ncua-input__field-text-count-current">0</output>
                                            <span>/30</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <th><div>카테고리</div></th>
                        <td>
                            <div class="ncua-flex ncua-gap-8 ncua-align-items-center category-wrap">
                                <div class="ncua-select ncua-select--xs" data-show-hint-text="true">
                                    <span class="ncua-select__content">
                                        <?= gd_select_box('layerKakaoCategory1', 'layerKakaoCategory1', $firstLevelCategories, null, null, '대분류', null, 'ncua-select__tag') ?>
                                    </span>
                                </div>
                                <div class="ncua-select ncua-select--xs">
                                    <span class="ncua-select__content">
                                        <?= gd_select_box('layerKakaoCategory2', 'layerKakaoCategory2', array(), null, null, '중분류', null, 'ncua-select__tag') ?>
                                    </span>
                                </div>
                                <div class="ncua-select ncua-select--xs">
                                    <span class="ncua-select__content">
                                        <?= gd_select_box('layerKakaoCategory3', 'layerKakaoCategory3', array(), null, null, '소분류', null, 'ncua-select__tag') ?>
                                    </span>
                                </div>
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <th><div>휴대폰 인증</div></th>
                        <td>
                            <div class="phone-verification-wrap">
                                <div class="ncua-flex ncua-gap-4">
                                    <div class="ncua-input ncua-input--xs ncua-input-width-320" data-show-hint-text="true">
                                        <div class="ncua-input__content">
                                            <div class="ncua-input__field ncua-input__field--xs">
                                                <input type="text" name="phoneNumber" id="phoneNumber" maxlength="15" placeholder="휴대폰 번호를 입력하세요" inputmode="numeric" pattern="[0-9]*" oninput="this.value = this.value.replace(/[^0-9]/g, '')"/>
                                            </div>
                                        </div>
                                    </div>
                                    <button type="button" id="sendVerificationCode" class="ncua-btn ncua-btn--xs ncua-btn--secondary-gray">
                                        <span class="ncua-btn__label">인증번호 발송</span>
                                    </button>
                                    <input type="hidden" id="sentPhoneNumber" name="sentPhoneNumber" value="">
                                </div>
                                <div id="phoneVerificationNotice" class="phone-verification-wrap__notice">
                                    카카오톡 채널 관리자센터에 등록된 휴대폰번호와 같아야 인증번호가 발송됩니다.
                                </div>
                                <div class="ncua-input ncua-input--xs ncua-input-width-320" id="verificationCodeWrap" data-show-hint-text="true" style="display: none;">
                                    <div class="ncua-input__content">
                                        <div class="ncua-input__field ncua-input__field--xs">
                                            <input type="text" name="token" id="verificationCode" placeholder="인증번호를 입력하세요"/>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </td>
                    </tr>
                </table>
            </div>
        </div>
    </form>
    
    <div class="ncua-content-footer">
        <button type="button" id="registerKakaoChannelCancel" class="ncua-btn ncua-btn--sm ncua-btn--secondary-gray">
            <span class="ncua-btn__label">취소</span>
        </button>
        <button type="button" id="registerKakaoChannelConfirm" class="ncua-btn ncua-btn--sm ncua-btn--primary">
            <span class="ncua-btn__label">등록</span>
        </button>
    </div>
</article>

<script>
    const DOM_READY_STATES = ['complete', 'interactive'];

    const charCountManager = createCharCountManager({
        targetClasses: ['js-kakao-talk-channel-name'],
    });
    charCountManager.init();
    
    /**
     * Tooltip 닫기 기능
     */
    const hideTooltips = () => {
        // GodoCosGuide의 모든 tooltip 닫기
        if (typeof window.GodoCosGuide !== 'undefined' && window.GodoCosGuide.hideAllTooltips) {
            window.GodoCosGuide.hideAllTooltips();
        }
    };
    
    // DOM 요소 캐싱
    const initDOMCache = (container) => {
        return {
            container,
            form: container.querySelector('#registerKakaoChannelForm'),
            sender: container.querySelector('#sender'),
            plusIdContainer: container.querySelector('#plusId'),
            categoryMajor: container.querySelector('#layerKakaoCategory1'),
            categoryMedium: container.querySelector('#layerKakaoCategory2'),
            categoryMinor: container.querySelector('#layerKakaoCategory3'),
            phoneNumber: container.querySelector('#phoneNumber'),
            sentPhoneNumber: container.querySelector('#sentPhoneNumber'),
            verificationCode: container.querySelector('#verificationCode'),
            formType: container.querySelector('#formType'),
            sendBtn: container.querySelector('#sendVerificationCode'),
            confirmBtn: container.querySelector('#registerKakaoChannelConfirm'),
            cancelBtn: container.querySelector('#registerKakaoChannelCancel'),
            phoneVerificationNotice: container.querySelector('#phoneVerificationNotice'),
            verificationCodeWrap: container.querySelector('#verificationCodeWrap')
        };
    };

    const onReady = () => {
        try {
            initCategorySelectBoxes();

            const container = document.querySelector('.layer-register-kakao-channel');
            if (!container) return;

            const dom = initDOMCache(container);
            
            // 카카오톡 채널 글자수 카운트 기능 초기화
            const charCountManager = createCharCountManager({
                targetClasses: ['plusId'],
            });
            charCountManager.init();

            // 취소 버튼
            dom.cancelBtn?.addEventListener('click', () => {
                hideTooltips();
                layer_close();
            });
            
            // jQuery Validator 설정
            let validatorInstance = null;
            
            // 커스텀 validation 메서드 등록
            const registerCustomMethods = () => {
                if (typeof $ === 'undefined' || typeof $.validator === 'undefined') {
                    return false;
                }

                // 커스텀 validation: @로 시작하는지 확인
                $.validator.addMethod('customKakaoChannel', function(value) {
                    // 필드가 비어있으면 required 규칙에서 처리하므로 여기서는 통과
                    if (!value || value.trim() === '') {
                        return true;
                    }
                    // @로 시작하는지 확인
                    return /^@/.test(value);
                }, '@를 채널 앞에 붙여주세요.');

                // 커스텀 validation: 카테고리 3개 중 하나라도 없으면 false
                $.validator.addMethod('checkAllCategories', function() {
                    const categoryMajor = dom.categoryMajor?.value || '';
                    const categoryMedium = dom.categoryMedium?.value || '';
                    const categoryMinor = dom.categoryMinor?.value || '';
                    
                    return categoryMajor !== '' && categoryMedium !== '' && categoryMinor !== '';
                }, '카테고리를 선택해 주세요.');

                $.validator.addMethod('checkPhoneNumber', function() {
                    const phoneNumber = dom.phoneNumber?.value || '';
                    const result = phoneNumber !== '';

                    dom.phoneVerificationNotice.toggleAttribute('hidden', !result);
                    return result;
                });

                $.validator.addMethod('checkSent', function() {
                    if (dom.formType?.value === 'register') {
                        const phoneNumber = dom.phoneNumber?.value || '';
                        const sentPhoneNumber = dom.sentPhoneNumber?.value || '';

                        if (!sentPhoneNumber || phoneNumber !== sentPhoneNumber) {
                            NCDSAlert({message: '휴대폰 인증이 완료되지 않았습니다.<br>인증번호를 발송해 주세요.', iconType: 'warning'});
                            return false;
                        }
                    }

                    return true;
                });

                $.validator.addMethod('checkToken', function() {
                    if (dom.formType?.value === 'register') {
                        const verificationCode = dom.verificationCode?.value || '';
                        return verificationCode !== '';
                    }

                    return true;
                });
                
                return true;
            };
            
            // Validator 설정 객체
            const validatorConfig = {
                ignore: ':hidden:not(#sentPhoneNumber)',
                rules: {
                    plusId: {
                        required: true,
                        customKakaoChannel: true
                    },
                    layerKakaoCategory1: {
                        required: true,
                        checkAllCategories: true
                    },
                    layerKakaoCategory2: {
                        required: true,
                        checkAllCategories: true
                    },
                    layerKakaoCategory3: {
                        required: true,
                        checkAllCategories: true
                    },
                    phoneNumber: {
                        checkPhoneNumber: true
                    },
                    sentPhoneNumber: {
                        checkSent: true
                    },
                    token: {
                        checkToken: true
                    }
                },
                messages: {
                    plusId: {
                        required: '카카오톡 채널을 입력해 주세요.',
                        customKakaoChannel: '@를 카카오톡 채널 앞에 붙여주세요.'
                    },
                    categoryMajor: {
                        required: '카테고리를 선택해 주세요.',
                        checkAllCategories: '카테고리를 선택해 주세요.'
                    },
                    categoryMedium: {
                        required: '카테고리를 선택해 주세요.',
                        checkAllCategories: '카테고리를 선택해 주세요.'
                    },
                    categoryMinor: {
                        required: '카테고리를 선택해 주세요.',
                        checkAllCategories: '카테고리를 선택해 주세요.'
                    },
                    phoneNumber: {
                        checkPhoneNumber: '휴대폰 번호를 입력해 주세요.'
                    },
                    token: {
                        checkToken: '카카오톡으로 발송된 인증번호를 입력해 주세요.'
                    }
                },
                dialog: false,
                invalidHandler: function(form, validator) {
                    // 기본 alert 방지
                    return false;
                },
                showErrors: function(errorMap, errorList) {
                    NCDSValidator.showErrors(errorMap, errorList);
                    this.defaultShowErrors();
                },
                submitHandler: function(form) {
                    const formType = dom.formType?.value;

                    if (formType === 'verification') {
                        return false;
                    }

                    const sender = dom.sender?.value;
                    const plusId = dom.plusIdContainer?.value;
                    const categoryCode = dom.categoryMinor?.value;
                    const phoneNumber = dom.sentPhoneNumber?.value;
                    const token = dom.verificationCode?.value;


                    const data = {
                        mode: 'register',
                        sender: sender,
                        plusId: plusId,
                        phoneNumber: phoneNumber,
                        token: token,
                        categoryCode: categoryCode
                    };

                    $.post('./message_config/layer_register_kakao_talk_channel_ps.php', data, function(response) {
                        if (response.error) {
                            NCDSAlert({ message: '일시적인 오류가 발생하였습니다.<br>다시 시도해 주세요.', iconType: 'error' });
                            return;
                        }

                        if (response.success) {
                            NCDSAlert({ message: response.data['alertMessage']['message'], iconType: 'success' });
                            layer_close();

                            if (sender === 'kakaoAlrimCloud') {
                                loadCloudKakaoTalkChannelLayer();
                            } else {
                                loadBizmKakaoTalkChannelLayer();
                            }
                        } else {
                            NCDSAlert({ message: response.data['alertMessage']['message'], subMessage: response.data['alertMessage']['subMessage'], iconType: 'error' });
                        }
                    }, 'json');
                }
            };
            
            const initValidator = () => {
                if (typeof $ === 'undefined' || typeof $.validator === 'undefined') {
                    console.warn('jQuery Validator가 로드되지 않았습니다.');
                    return;
                }

                // 커스텀 메서드 등록
                if (!registerCustomMethods()) {
                    return;
                }

                // Validator 인스턴스 초기화 (const로 선언)
                validatorInstance = $('#registerKakaoChannelForm').validate(validatorConfig);
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

            // 인증번호 발송/재발송 버튼 (validator 초기화 이후에 등록)
            dom.sendBtn?.addEventListener('click', (e) => {
                e.preventDefault();
                
                if (!validatorInstance) {
                    return;
                }

                // formType을 verification으로 설정 (verificationCode 필드의 required가 자동으로 false가 됨)
                dom.formType.value = 'verification';
                
                // 검증 수행 실패 체크 (submit 없이)
                if (!validatorInstance.form()) {
                    return;
                }

                // 검증 통과 시 ajax 요청
                var plusId = dom.plusIdContainer?.value ?? '';
                const categoryMinor = dom.categoryMinor?.value ?? '';
                const phoneNumber = dom.phoneNumber?.value ?? '';

                if (plusId.trim() === '') {
                    if (validatorInstance) validatorInstance.showErrors({ plusId: '카카오톡 채널을 입력해 주세요.' });
                    return;
                }

                if (phoneNumber.trim() === '') {
                    if (validatorInstance) validatorInstance.showErrors({ phoneNumber: '휴대폰 번호를 입력해 주세요.' });
                    return;
                }

                if (categoryMinor === '') {
                    if (validatorInstance) validatorInstance.showErrors({ categoryMinor: '카테고리를 선택해 주세요.' });
                    return;
                }

                const data = {
                    mode: 'getToken',
                    sender: dom.sender?.value,
                    plusId: plusId,
                    phoneNumber: phoneNumber,
                    category: categoryMinor
                };

                $.post('./message_config/layer_register_kakao_talk_channel_ps.php', data, function(response) {
                    if (response.success) {
                        e.target.querySelector('span.ncua-btn__label').textContent = '인증번호 재발송';
                        dom.verificationCodeWrap.style.display = 'flex';
                        dom.sentPhoneNumber.value = phoneNumber;
                        NCDSAlert({ message: '카카오톡으로 인증번호가 발송되었습니다.', iconType: 'success' });
                    } else {
                        if (validatorInstance) {
                            dom.phoneVerificationNotice.toggleAttribute('hidden', true);
                            validatorInstance.showErrors({ phoneNumber: '카카오톡으로 발송된 인증번호를 입력해 주세요.' });
                        }
                    }
                }, 'json').fail(function () {
                    NCDSAlert({ message: '인증번호를 요청하지 못하였습니다. 잠시 후 다시 시도해주세요.', iconType: 'error' });
                });
            });
            
            // 등록 버튼
            dom.confirmBtn?.addEventListener('click', (e) => {
                e.preventDefault();
                
                if (!validatorInstance) {
                    return;
                }

                // formType을 register로 설정
                dom.formType.value = 'register';

                // 검증 수행 후 submit
                if (!validatorInstance.form()) {
                    return;
                }
                
                // 검증 통과 시 폼 submit
                $('#registerKakaoChannelForm').submit();
            });
        } catch {
            // ignore
        }
    };

    function initCategorySelectBoxes() {
        $(document).on('change', '[id^="layerKakaoCategory"]', function(){
            var sId = this.id.replace('layerKakaoCategory', '');

            if (sId === '1') {
                // 중,소분류 초기화
                resetCategoryOptions('layerKakaoCategory2', '중분류');
                resetCategoryOptions('layerKakaoCategory3', '소분류');

                // 대분류 기본값 선택시
                if (!this.value) return false;

                // 중분류 재구성
                populateCategoryOptions('cate2', this.value, 'layerKakaoCategory2');

            } else if (sId === '2') {
                // 소분류 초기화
                resetCategoryOptions('layerKakaoCategory3', '소분류');

                // 중분류 기본값 선택시
                if (!this.value) return false;

                // 소분류 재구성
                populateCategoryOptions('cate3', this.value, 'layerKakaoCategory3');
            }
        });
    }

    function resetCategoryOptions(id, text) {
        const element = document.getElementById(id);
        if (element) element.innerHTML = `<option value=''>${text}</option>`;
    }

    function populateCategoryOptions(inputPrefix, inputSuffix, targetId) {
        const inputs = document.querySelectorAll(`[id^="${inputPrefix}${inputSuffix}"]`);
        inputs.forEach(function(input) {
            const cateCode = input.id.replace(inputPrefix, '');
            document.getElementById(targetId).insertAdjacentHTML('beforeend', `<option value="${cateCode}">${input.value}</option>`);
        });
    }

    if (DOM_READY_STATES.includes(document.readyState)) {
        onReady();
    } else {
        document.addEventListener('DOMContentLoaded', onReady);
    }
</script>

<script type="text/javascript">
    const code = '260107001';
    if (window.GodoCosGuide) {
        window.GodoCosGuide.init({
            apiUrl: <?= json_encode($cosGuideApiUrl) ?>,
            guideCode: code,
        });
    }
</script>
