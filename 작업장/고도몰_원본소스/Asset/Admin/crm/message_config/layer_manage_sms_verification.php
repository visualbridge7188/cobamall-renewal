<link rel="stylesheet" href="<?= gd_set_browser_cache(PATH_ADMIN_GD_SHARE . 'ncds/css/crm/layer-manage-sms-verification.css') ?>">
<script defer src="<?= gd_set_browser_cache(PATH_ADMIN_GD_SHARE . 'ncds/script/common/charcount.js') ?>"></script>
<script defer src="<?= gd_set_browser_cache(PATH_ADMIN_GD_SHARE . 'ncds/script/common/ncds-jquery-validator.js') ?>"></script>

<article class="ncua-content layer-manage-sms-verification">
    <form id="frmSmsPassword">
        <div class="ncua-search-result">    
            <div class="ncua-table ncua-table--vertical">
                <table>
                    <tr>
                        <th><div>메세지 인증번호</div></th>
                        <td>
                            <div>
                                <div class="ncua-input ncua-input--xs ncua-input-full-width" data-show-hint-text="true">
                                    <div class="ncua-input__content-wrap">
                                        <div class="ncua-input__field ncua-input__field--xs">
                                            <input type="password" name="smsPassword" id="smsPassword" class="sms-password" data-charcount-key="smsPassword" placeholder="10~16자의 영대/소문자, 숫자, 특수문자 중 2가지 이상의 조합을 사용해 주세요." maxlength="16" required/>
                                        </div>
                                        <div class="ncua-input__field-text-count" data-charcount-text="smsPassword">
                                            <output class="ncua-input__field-text-count-current">0</output>
                                            <span>/16</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </td>
                    </tr>
                    <?php if ($displayInfo['useCaptcha'] === true): ?>
                        <tr>
                            <th><div>자동등록방지</div></th>
                            <td>
                                <div class="auto-registration-prevention">
                                    <div class="ncua-flex ncua-flex-column ncua-gap-4">
                                        <img width="160" src="../../base/captcha.php" id="captchaImage"/>
                                        <div class="info-wrap">
                                            <span>보이는 순서대로 숫자 및 문자를 모두 입력해 주세요.</span>
                                            <span class="ncua-flex" >
                                                <button type="button" id="refreshImageBtn" class="ncua-btn ncua-btn--xxs has-underline ncua-btn--text-gray">
                                                    <img width="12" height="12" src="<?= gd_set_browser_cache(PATH_ADMIN_GD_SHARE . 'ncds/image/refresh-cw-02.svg') ?>" alt="이미지 새로고침">
                                                    <span class="ncua-btn__label">이미지 새로고침</span>
                                                </button>
                                            </span>
                                        </div>
                                    </div>
                                    <div class="ncua-input ncua-input--xs ncua-input-width-520" data-show-hint-text="true">
                                        <div class="ncua-input__content-wrap">
                                            <div class="ncua-input__content">
                                                <div class="ncua-input__field ncua-input__field--xs">
                                                    <input type="text" name="captchaNumber" id="captchaNumber" placeholder="문자를 입력해주세요." required >
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </td>
                        </tr>
                    <?php endif; ?>
                </table>
            </div>
        </div>
    </form>
    <div class="layer-manage-sms-verification__actions">
        <button type="button" id="manageSmsPasswordCancel" class="ncua-btn ncua-btn--sm ncua-btn--secondary-gray">
            <span class="ncua-btn__label">취소</span>
        </button>
        <button type="button" id="manageSmsPasswordConfirm" class="ncua-btn ncua-btn--sm ncua-btn--primary">
            <span class="ncua-btn__label">확인</span>
        </button>
    </div>
</article>

<script>
    let validatorInstance = null;
    const restrictedKeywords = <?= json_encode($restrictedKeywords) ?>;

    const onReady = () =>  {
        try {
            // 인증번호 글자수 카운트 기능 초기화
            const charCountManager = createCharCountManager({
                targetClasses: ['sms-password'],
            });
            charCountManager.init();

            var useCaptcha = document.querySelectorAll('#captchaImage').length > 0;

            if (useCaptcha) {
                document.getElementById('captchaNumber').addEventListener('keyup', (e) => {
                    e.target.value = e.target.value.toUpperCase();
                });

                // 자동등록방지 이미지 새로고침 버튼
                var refreshButton = document.getElementById('refreshImageBtn');
                refreshButton.addEventListener('click', (e) => {
                    e.preventDefault();
                    var captchaImage = document.getElementById('captchaImage');
                    captchaImage.removeAttribute('src');
                    setTimeout(function () {
                        var someDate = (new Date()).getTime();
                        captchaImage.setAttribute('src', '../../base/captcha.php?ch=' + someDate);
                    }, 1);
                });

                refreshButton.click();
            }

            // 인증번호 설정/관리 버튼
            document.getElementById('manageSmsPasswordCancel').addEventListener('click', (e) => {
                layer_close();
            });

            // jQuery Validator 초기화

            
            const initValidator = () => {
                if (typeof $ === 'undefined' || typeof $.validator === 'undefined') {
                    console.warn('jQuery Validator가 로드되지 않았습니다.');
                    return;
                }

                // 제한문자: ><,?:;\
                $.validator.addMethod('checkRestrictedCharacters', function(value, _) {
                    if (!value || value.trim() === '') {
                        return true;
                    }
                    const restrictedChars = /[><,?:;\\]/;
                    return !restrictedChars.test(value);
                }, '인증번호에 ><,?:;\\는 사용할 수 없습니다.');

                // 제한키워드 체크
                $.validator.addMethod('checkRestrictedKeywords', function(value, _) {
                    if (!value || value.trim() === '') {
                        return true;
                    }

                    const lowerValue = value.toLowerCase();
                    return !restrictedKeywords.some(keyword => lowerValue.includes(keyword.toLowerCase()));
                }, '인증번호에 사용할 수 없는 단어가 포함되었습니다.');

                // 커스텀 validation 메서드 등록
                $.validator.addMethod('customSmsVerification', function(value, _) {
                    // 필드가 비어있으면 required 규칙에서 처리하므로 여기서는 통과
                    if (!value || value.trim() === '') {
                        return true;
                    }
                    
                    const trimmedValue = value.trim();
                    
                    // 길이 체크 (10~16자)
                    if (trimmedValue.length < 10 || trimmedValue.length > 16) {
                        return false;
                    }
                    
                    // 영대/소문자, 숫자, 특수문자 중 2가지 이상 조합 체크
                    let typeCount = 0;
                    if (/[a-z]/.test(trimmedValue)) typeCount++; // 소문자
                    if (/[A-Z]/.test(trimmedValue)) typeCount++; // 대문자
                    if (/[0-9]/.test(trimmedValue)) typeCount++; // 숫자
                    if (/[^a-zA-Z0-9]/.test(trimmedValue)) typeCount++; // 특수문자
                    
                    return typeCount >= 2;
                }, '10~16자의 영대/소문자, 숫자, 특수문자 중 2가지 이상의 조합을 사용해 주세요.');

                // 캡챠 검증 (실제로는 서버에서 검증해야 하지만, 클라이언트에서도 기본 체크)
                $.validator.addMethod('checkCaptcha', function(value, _) {
                    return !value || value.trim() !== '';
                }, '입력한 내용이 이미지의 내용과 일치하지 않습니다.');

                // Validator 설정 객체
                const validatorConfig = {
                    rules: {
                        smsPassword: {
                            required: true,
                            checkRestrictedCharacters: true,
                            checkRestrictedKeywords: true,
                            customSmsVerification: true
                        },
                        captchaNumber: {
                            required: true,
                            checkCaptcha: true
                        }
                    },
                    messages: {
                        smsPassword: {
                            required: '메시지 인증번호를 입력해 주세요.',
                            checkRestrictedCharacters: '인증번호에 ><,?:;\\는 사용할 수 없습니다.',
                            checkRestrictedKeywords: '인증번호에 사용할 수 없는 단어가 포함되었습니다.',
                            customSmsVerification: '10~16자의 영대/소문자, 숫자, 특수문자 중 2가지 이상의 조합을 사용해 주세요.'
                        },
                        captchaNumber: {
                            required: '자동등록방지 문자를 입력해 주세요.',
                            checkCaptcha: '입력한 내용이 이미지의 내용과 일치하지 않습니다.'
                        }
                    },
                    dialog: false,
                    invalidHandler: function() {
                        return false;
                    },
                    submitHandler: function() {
                        let smsPassword = document.getElementById('smsPassword').value;
                        let captchaNumber = useCaptcha ? document.getElementById('captchaNumber').value : null;
                        changeSmsPassword(useCaptcha, smsPassword, captchaNumber);
                        return false;
                    },
                    showErrors: function(errorMap, errorList) {
                        // NCDS Validator의 showErrors 호출
                        if (typeof NCDSValidator !== 'undefined' && NCDSValidator.showErrors) {
                            NCDSValidator.showErrors(errorMap, errorList);
                        }
                        this.defaultShowErrors();
                    },
                    highlight: function(element) {
                        if (typeof NCDSValidator !== 'undefined' && NCDSValidator.highlight) {
                            NCDSValidator.highlight(element);
                        }
                    },
                    unhighlight: function(element) {
                        if (typeof NCDSValidator !== 'undefined' && NCDSValidator.unhighlight) {
                            NCDSValidator.unhighlight(element);
                        }
                    },
                };

                // Validator 인스턴스 초기화
                validatorInstance = $('#frmSmsPassword').validate(validatorConfig);
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

            document.getElementById('manageSmsPasswordConfirm').addEventListener('click', (e) => {
                e.preventDefault();

                if (!validatorInstance) {
                    console.warn('Validator가 초기화되지 않았습니다.');
                    return;
                }

                // Validation 수행
                if (validatorInstance.form()) {
                    // Validation 통과 시 form submit
                    $('#frmSmsPassword').submit();
                }
            });

        } catch (e) {
            NCDSAlert({ message: e.message, iconType: 'error' });
        }
    };

    function changeSmsPassword(useCaptcha, smsPassword, captchaNumber) {
        $.post('./message_config/layer_manage_sms_verification_ps.php', {mode: useCaptcha ? 'changePassword' : 'validPassword', password: smsPassword, captcha: captchaNumber}, function (response) {
            if (response.error) {
                NCDSAlert({ message: '일시적인 오류가 발생하였습니다.<br>다시 시도해 주세요.', iconType: 'error' });
                return;
            }

            if (response.success) {
                if (typeof window.NCDSAlert !== 'undefined') {
                    window.NCDSToast({ message: '메시지 인증번호 등록이 완료되었습니다.', color: 'success' });
                    layer_close();
                } else {
                    NCDSAlert({
                        message: '메시지 인증번호 등록이 완료되었습니다.',
                        iconType: 'success',
                        callback: () => {
                            layer_close();
                        }
                    });
                }
            } else {
                if (response.data.type === 'invalid_captcha') {
                    document.getElementById('captchaNumber')?.focus();
                    if (validatorInstance) validatorInstance.showErrors({ captchaNumber: '입력한 내용이 이미지의 내용과 일치하지 않습니다.' });
                } else if (response.data.type === 'invalid_format') {
                    document.getElementById('smsPassword')?.focus();
                    if (validatorInstance) validatorInstance.showErrors({ smsPassword: '10~16자의 영대/소문자, 숫자, 특수문자 중 2가지 이상의 조합을 사용해 주세요.' });
                }
            }
        });
    }

    const DOM_READY_STATES = ['complete', 'interactive'];
    
    if (DOM_READY_STATES.includes(document.readyState)) {
        onReady();
    } else {
        document.addEventListener('DOMContentLoaded', onReady);
    }
</script>
