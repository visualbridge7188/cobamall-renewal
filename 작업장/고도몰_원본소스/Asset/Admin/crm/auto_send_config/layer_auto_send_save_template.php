<script defer src="<?=gd_set_browser_cache(PATH_ADMIN_GD_SHARE . 'ncds/script/common/charcount.js')?>"></script>
<link rel="stylesheet" href="<?=gd_set_browser_cache(PATH_ADMIN_GD_SHARE . 'ncds/css/crm/layer-save-as-template.css')?>">

<div class="modal-dialog__content layer-save-as-template">
    <input type="hidden" id="templateChannel" value="<?= $channel ?? 'SMS' ?>" />
    <input type="hidden" id="templateTitle" value="<?= htmlspecialchars($title, ENT_QUOTES) ?>" />
    <input type="hidden" id="templateContents" value="<?= htmlspecialchars($contents, ENT_QUOTES) ?>" />
    <input type="hidden" id="templateUrl" value="<?= htmlspecialchars($url ?? '', ENT_QUOTES) ?>" />
    <input type="hidden" id="templateImage" value="<?= htmlspecialchars($image ?? '', ENT_QUOTES) ?>" />
    <div class="ncua-table ncua-table--vertical">
        <table>
            <tbody>
                <tr>
                    <th class="ncua-required"><div>카테고리</div></th>
                    <td>
                        <div>
                            <div class="ncua-select ncua-select--xs" data-show-hint-text="true">
                                <div class="ncua-select__content">
                                    <select class="ncua-select__tag" id="templateCategory">
                                        <option value="">카테고리 선택</option>
                                        <?php foreach ($categories as $category): ?>
                                            <option value="<?= $category['value'] ?>"><?= $category['name'] ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </td>
                </tr>
                <tr>
                    <th class="ncua-required"><div>제목</div></th>
                    <td>
                        <div>
                            <div class="ncua-input ncua-input--xs ncua-input--full-width-with-count" data-show-hint-text="true">
                                <div class="ncua-input__content-wrap">
                                    <div class="ncua-input__content">
                                        <div class="ncua-input__field ncua-input__field--xs">
                                            <input maxlength="10" type="text" id="templateSubject" placeholder="제목을 입력하세요" class="layer-template-title" data-charcount-key="layerTemplateTitle" />
                                        </div>
                                    </div>
                                    <div class="ncua-input__field-text-count" data-charcount-text="layerTemplateTitle">
                                        <output class="ncua-input__field-text-count-current">0</output>
                                        <span>/10</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
</div>
<div class="modal-dialog__footer">
    <button type="button" class="ncua-btn ncua-btn--sm ncua-btn--secondary-gray js-layer-close">
        <span class="ncua-btn__label">취소</span>
    </button>
    <button type="button" class="ncua-btn ncua-btn--sm ncua-btn--primary" id="btnSaveTemplate">
        <span class="ncua-btn__label">저장</span>
    </button>
</div>

<script type="text/javascript">
    const charCountManager = createCharCountManager({
        targetClasses: ['layer-template-title'],
    });
    charCountManager.init();

    // 폼 요소들
    const categoryElement = document.getElementById('templateCategory');
    const subjectElement = document.getElementById('templateSubject');
    const titleElement = document.getElementById('templateTitle');

    /**
     * NCDSValidator.showErrors를 활용한 에러 표시
     * @param {Array} errorList - 에러 목록 [{element, message}, ...]
     */
    const showValidationErrors = (errorList) => {
        if (!Array.isArray(errorList) || errorList.length === 0) return;

        // 각 에러에 대해 highlight 적용
        errorList.forEach(error => {
            NCDSValidator.highlight(error.element);
        });

        // showErrors 함수를 통해 힌트 텍스트 표시
        const errorMap = {};
        errorList.forEach(error => {
            if (error.element.name || error.element.id) {
                errorMap[error.element.name || error.element.id] = error.message;
            }
        });

        NCDSValidator.showErrors.call({ defaultShowErrors: () => {} }, errorMap, errorList);

        // 첫 번째 에러 필드에 포커스
        if (errorList[0]?.element) {
            errorList[0].element.focus();
        }
    };

    /**
     * NCDSValidator.unhighlight를 활용한 에러 제거
     * @param {HTMLElement} element - 폼 요소
     */
    const clearValidationError = (element) => {
        if (!element) return;
        NCDSValidator.unhighlight(element);
    };

    /**
     * 모든 필드의 에러 상태 초기화
     */
    const clearAllErrors = () => {
        [categoryElement, subjectElement].forEach(element => {
            if (element) {
                clearValidationError(element);
            }
        });
    };

    // 입력 시 에러 상태 제거
    categoryElement.addEventListener('change', function() {
        if (this.value) {
            clearValidationError(this);
        }
    });
    
    subjectElement.addEventListener('input', function() {
        if (this.value.trim()) {
            clearValidationError(this);
        }
    });

    document.getElementById('btnSaveTemplate').addEventListener('click', function() {
        const channel = document.getElementById('templateChannel').value;
        const category = categoryElement.value;
        const subject = subjectElement.value.trim();
        const contents = document.getElementById('templateContents').value;
        const title = document.getElementById('templateTitle').value;

        // 모든 에러 상태 초기화
        clearAllErrors();

        // 유효성 검사
        const errors = [];

        if (!category) {
            errors.push({
                element: categoryElement,
                message: '카테고리를 선택해주세요.'
            });
        }

        if (!subject) {
            errors.push({
                element: subjectElement,
                message: '제목을 입력해주세요.'
            });
        }

        // 에러가 있으면 표시하고 중단
        if (errors.length > 0) {
            showValidationErrors(errors);
            return;
        }

        if (!contents) {
            NCDSAlert({
                message: '저장할 내용이 없습니다.',
                iconType: 'error'
            });
            return;
        }

        const url = channel === 'MYAPP_PUSH'
            ? './myapp/myapp_push_template_ps.php'
            : './sms/sms_template_ps.php';

        const templateUrl = document.getElementById('templateUrl').value;
        const templateImage = document.getElementById('templateImage').value;

        const formData = new FormData();
        formData.append('mode', 'save');
        formData.append('category', category);
        formData.append('subject', subject);
        formData.append('contents', contents);
        formData.append('title', title);
        formData.append('url', templateUrl);
        formData.append('image', templateImage);

        if (window._templateImageFile) {
            formData.append('imageFile', window._templateImageFile, window._templateImageFile.name);
        }

        $.ajax({
            url: url,
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    NCDSToast({
                        message: '저장되었습니다.',
                        color: 'success'
                    });
                    document.querySelector('.js-layer-close').click();
                } else {
                    NCDSAlert({
                        message: response.message || '저장에 실패했습니다.',
                        iconType: 'error'
                    });
                }
            },
            error: function() {
                NCDSAlert({
                    message: '저장에 실패했습니다.',
                    iconType: 'error'
                });
            }
        });
    });
</script>
