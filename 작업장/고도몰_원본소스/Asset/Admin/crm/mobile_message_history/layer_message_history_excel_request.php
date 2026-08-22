
<link type="text/css" href="<?= gd_set_browser_cache(PATH_ADMIN_GD_SHARE . 'ncds/css/crm/layer-history-download.css') ?>" rel="stylesheet"/>
<script defer src="<?= gd_set_browser_cache(PATH_ADMIN_GD_SHARE . 'ncds/script/common/charcount.js') ?>"></script>
<script defer src="<?= gd_set_browser_cache(PATH_ADMIN_GD_SHARE . 'ncds/script/common/password-input.js') ?>"></script>
<script defer src="<?= gd_set_browser_cache(PATH_ADMIN_GD_SHARE . 'ncds/script/common/ncds-jquery-validator.js') ?>"></script>
<section class="layer-history-download">
    <form id="frmExcelRequest" method="post">
        <input type="hidden" name="mode" value="generate" />
        <input type="hidden" name="menu" value="<?= $menu->name ?>" />
        <div class="ncua-table ncua-table--vertical">
            <table>
                <colgroup>
                    <col width="144px" />
                </colgroup>
                <tbody>
                    <tr>
                        <th>
                            <div>다운로드 범위</div>
                        </th>
                        <td>
                            <div class="ncua-flex-gap">
                                <?php foreach ($excelRequestDownloadRanges as $index => $excelRequestDownloadRange): ?>
                                    <label class="ncua-radio-field ncua-radio-field--xs has-text">
                                        <span class="ncua-radio-input ncua-radio-input--xs ncua-radio-field__input">
                                            <input type="radio" name="downloadRange" value="<?= $excelRequestDownloadRange->name ?>" <?= $index == 1 ? 'checked' : '' ?>/>
                                        </span>
                                        <span class="ncua-radio-field__text"><?= $excelRequestDownloadRange->value ?></span>
                                    </label>
                                <?php endforeach; ?>
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <th>
                            <div>양식 선택</div>
                        </th>
                        <td>
                            <div class="ncua-gap-4">
                                <span class="ncua-select ncua-select--xs">
                                    <span class="ncua-select__content">
                                        <select class="ncua-select__tag" disabled>
                                            <option value="">CRM</option>
                                        </select>
                                    </span>
                                </span>
                                <span class="ncua-select ncua-select--xs">
                                    <span class="ncua-select__content">
                                        <select class="ncua-select__tag" disabled>
                                            <option value="">모바일 메시지 발송 내역</option>
                                        </select>
                                    </span>
                                </span>
                                <span class="ncua-select ncua-select--xs">
                                    <span class="ncua-select__content">
                                        <select class="ncua-select__tag" disabled>
                                            <option value=""><?= $menu->value ?></option>
                                        </select>
                                    </span>
                                </span>
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <th>
                            <div>파일명</div>
                        </th>
                        <td>
                            <div>
                                <div class="ncua-input ncua-input--xs">
                                    <div class="ncua-input__content-wrap">
                                        <div class="ncua-input__content">
                                            <div class="ncua-input__field ncua-input__field--xs ncua-input-width-320">
                                                <input class="js-excel-name" type="text" maxlength="50" name="excelName" value="" data-charcount-key="excelName" />
                                            </div>
                                        </div>
                                        <div class="ncua-input__field-text-count" data-charcount-text="excelName">
                                            <output class="ncua-input__field-text-count-current">0</output>
                                            <span>/50</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <th>
                            <div>비밀번호 사용 여부</div>
                        </th>
                        <td>
                            <div>
                                <div class="ncua-switch ncua-switch--xs">
                                    <label class="ncua-switch__option ncua-switch__option--left ncua-switch__option--active">
                                        <input class="ncua-switch__radio ncua-switch__radio--left" type="radio" value="y" checked />
                                        <span class="ncua-switch__label">사용함</span>
                                    </label>
                                    <label class="ncua-switch__option ncua-switch__option--right ncua-switch__option--inactive">
                                        <input class="ncua-switch__radio ncua-switch__radio--right" type="radio" value="n" />
                                        <span class="ncua-switch__label">사용안함</span>
                                    </label> </div>
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <th>
                            <div>비밀번호 설정</div>
                        </th>
                        <td>
                            <div>
                                <div class="ncua-input ncua-input--xs ncua-input-password ncua-input-width-320" data-show-hint-text="true">
                                    <div class="ncua-input__content">
                                        <div class="ncua-input__field ncua-input__field--xs">
                                            <div class="ncua-input__icon-wrap">
                                                <img class="ncua-input__left-icon" src="<?= PATH_ADMIN_GD_SHARE ?>ncds/image/password-lock-01.svg" width="14" height="14" />
                                            </div>
                                            <input type="password" name="excelPassword" data-charcount-key="passwordSetting" class="js-pw" maxlength="16" placeholder="영문/숫자/특수문자 2개 포함, 10~16자" />
                                            <button type="button" class="ncua-input__icon-wrap ncua-input__right-icon ncua-input__password-icon">
                                                <img src="<?= PATH_ADMIN_GD_SHARE ?>ncds/image/password-eye-off.svg" width="14" height="14" />
                                            </button>
                                        </div>
                                        <div class="ncua-input__field-text-count" data-charcount-text="passwordSetting">
                                            <span class="ncua-input__field-text-count-current">0</span>
                                            <span>/16</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <th>
                            <div>비밀번호 확인</div>
                        </th>
                        <td>
                            <div>
                                <div class="ncua-input ncua-input--xs ncua-input-password ncua-input-width-320" data-show-hint-text="true">
                                    <div class="ncua-input__content">
                                        <div class="ncua-input__field ncua-input__field--xs">
                                            <div class="ncua-input__icon-wrap">
                                                <img class="ncua-input__left-icon" src="<?= PATH_ADMIN_GD_SHARE ?>ncds/image/password-lock-01.svg" width="14" height="14" />
                                            </div>
                                            <input type="password" name="excelPassword" data-charcount-key="passwordConfirm" class="js-pw-confirm" maxlength="16" placeholder="영문/숫자/특수문자 2개 포함, 10~16자" />
                                            <button type="button" class="ncua-input__icon-wrap ncua-input__right-icon ncua-input__password-icon">
                                                <img src="<?= PATH_ADMIN_GD_SHARE ?>ncds/image/password-eye-off.svg" width="14" height="14" />
                                            </button>
                                        </div>
                                        <div class="ncua-input__field-text-count" data-charcount-text="passwordConfirm">
                                            <span class="ncua-input__field-text-count-current">0</span>
                                            <span>/16</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
        <button type="submit" class="ncua-btn ncua-btn--xs ncua-btn--primary layer-history-download-request">요청</button>
    </form>
    <ul class="layer-history-download-info">
        <li><em>개인정보 보호를 위해 '다운로드 보안 설정'을 사용하시길 권장합니다.</em><a class="ncua-link" href="/policy/manage_security.php" target="_blank">운영 보안 설정</a></li>
        <li>검색 내역으로 다운로드 시, 리스트에서 설정한 발송 상태 조건은 적용되지 않으며, 발송일, 발송유형, 검색어 기준으로 다운로드됩니다.</li>
    </ul>

    <div id="layerExcelHistoryResult"></div>
</section>

<script type="text/javascript">
    const searchQuery = JSON.parse('<?= json_encode($searchQuery, JSON_UNESCAPED_UNICODE | JSON_HEX_TAG | JSON_HEX_APOS) ?>');
    const selectedSendKeys = JSON.parse('<?= json_encode($selectedSendKeys, JSON_UNESCAPED_UNICODE | JSON_HEX_TAG | JSON_HEX_APOS) ?>');

    const charCountManager = createCharCountManager({
        targetClasses: ['js-excel-name', 'js-pw', 'js-pw-confirm'],
    });
    charCountManager.init();

    const passwordInputManager = createPasswordInputManager();
    passwordInputManager.init();

    $(document).ready(function () {
        initializeEvents();
        loadLayerContents(new FormData(document.querySelector('#frmExcelRequest')));
    });

    function initializeEvents() {
        document.querySelector('.js-excel-name').addEventListener('input', (e) => {
            const invalidChars = /[&*()₩;'?<>:"+=/\\|]/g;
            e.target.value = e.target.value.replace(invalidChars, '');
        });

        document.querySelector('#frmExcelRequest').addEventListener('submit', async (e) => {
            e.preventDefault(); // 기본 submit 막기
            if (!validationPassword()) return;

            const formData = new FormData(e.target);

            if (formData.get('downloadRange') === 'SELECT' && selectedSendKeys.length === 0) {
                NCDSAlert({message: '선택된 데이터가 없습니다.', iconType: 'error' });
                return;
            }

            formData.append('searchQuery', JSON.stringify(searchQuery));
            formData.append('selectedSendKeys', JSON.stringify(selectedSendKeys));

            requestExcel(formData);
        });
    }

    const validationPassword = () => {
        const pwInput = document.querySelector('.js-pw');
        const pwConfirmInput = document.querySelector('.js-pw-confirm');

        const hasValue = Array.from([pwInput, pwConfirmInput]).every(input => {
            NCDSValidator.unhighlight(input);
            if (!input.value) {
                NCDSValidator.highlight(input);
                NCDSValidator.showErrors(null, [{
                    element: input,
                    message: '비밀번호를 입력해주세요.'
                }]);
                return false;
            }
            return true;
        });

        if (!hasValue) return false;

        // 비밀번호 규칙 검증: 영문 대·소문자, 숫자, 특수문자 중 2가지 이상 포함한 10~16자
        const password = pwInput.value;
        const hasUpperCase = /[A-Z]/.test(password);
        const hasLowerCase = /[a-z]/.test(password);
        const hasNumber = /[0-9]/.test(password);
        const hasSpecialChar = /[!@#$%^&*()_+\-=\[\]{};':"\\|,.<>\/?`~]/.test(password);

        const typeCount = [hasUpperCase, hasLowerCase, hasNumber, hasSpecialChar].filter(Boolean).length;

        if (password.length < 10 || password.length > 16 || typeCount < 2) {
            NCDSValidator.highlight(pwInput);
            NCDSValidator.showErrors(null, [{
                element: pwInput,
                message: '영문 대·소문자, 숫자, 특수문자 중 2가지 이상 포함한 10~16자로 설정해 주세요.'
            }]);
            return false;
        }

        if (pwInput.value !== pwConfirmInput.value) {
            NCDSValidator.highlight(pwConfirmInput);
            NCDSValidator.showErrors(null, [{
                element: pwConfirmInput,
                message: '비밀번호가 일치하지 않습니다.'
            }]);

            return false;
        }

        return true;
    };

    function requestExcel(formData) {
        $.ajax({
            url: './mobile_message_history/layer_message_history_excel_request_ps.php',
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: function (response) {
                NCDSAlert({ message: response.message, iconType: 'success' });
                loadLayerContents(new FormData(document.querySelector('#frmExcelRequest')));
            },
            error: function () {
                NCDSAlert({ message: '파일 생성에 실패했습니다.', color: 'error' });
            }
        });
    }

    function loadLayerContents(formData) {
        $.ajax({
            url: './mobile_message_history/layer_message_history_excel_request_result.php',
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: function (data) {
                $('#layerExcelHistoryResult').html(data);
            },
            error: function () {
                NCDSAlert({ message: '일시적인 오류가 발생하였습니다.<br>다시 시도해 주세요.', iconType: 'error' });
            },
        });
    }
</script>
