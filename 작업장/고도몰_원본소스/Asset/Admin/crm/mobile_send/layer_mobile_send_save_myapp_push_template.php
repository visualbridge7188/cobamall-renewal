<script defer src="<?=gd_set_browser_cache(PATH_ADMIN_GD_SHARE . 'ncds/script/common/charcount.js')?>"></script>
<script defer src="<?= gd_set_browser_cache(PATH_ADMIN_GD_SHARE . 'ncds/script/common/ncds-jquery-validator.js') ?>"></script>
<link rel="stylesheet" href="<?=gd_set_browser_cache(PATH_ADMIN_GD_SHARE . 'ncds/css/crm/layer-save-as-template.css')?>">

<div class="modal-dialog__content layer-save-as-template">
    <input type="hidden" id="pushSubject" value="<?= htmlspecialchars($pushSubject, ENT_QUOTES) ?>" />
    <input type="hidden" id="pushContent" value="<?= htmlspecialchars($pushContent, ENT_QUOTES) ?>" />
    <input type="hidden" id="pushImage" value="<?= htmlspecialchars($pushImage, ENT_QUOTES) ?>" />
    <input type="hidden" id="pushUrl" value="<?= htmlspecialchars($pushUrl, ENT_QUOTES) ?>" />
    <input type="hidden" id="pushWithdraw" value="<?= htmlspecialchars($pushWithdraw, ENT_QUOTES) ?>" />
    <div class="ncua-table ncua-table--vertical">
        <table>
            <tbody>
                <tr>
                    <th class="ncua-required"><div>카테고리</div></th>
                    <td>
                        <div>
                            <div class="ncua-select ncua-select--xs">
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
                    <th class="ncua-required"><div>제목 입력</div></th>
                    <td>
                        <div>
                            <div class="ncua-input ncua-input--xs ncua-input--full-width-with-count">
                                <div class="ncua-input__content-wrap">
                                    <div class="ncua-input__content">
                                        <div class="ncua-input__field ncua-input__field--xs">
                                            <input maxlength="10" type="text" id="templateName" placeholder="제목을 입력하세요" class="layer-template-title" data-charcount-key="layerTemplateTitle" />
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

    document.getElementById('btnSaveTemplate').addEventListener('click', function() {
        const category = document.querySelector('#templateCategory');
        const templateName = document.querySelector('#templateName');
        const pushSubject = document.querySelector('#pushSubject');
        const pushContent = document.querySelector('#pushContent');
        const pushImage = document.querySelector('#pushImage');
        const pushUrl = document.querySelector('#pushUrl');
        const pushWithdraw = document.querySelector('#pushWithdraw');

        let validate = true;
        if (!category.value) {
            NCDSValidator.highlight(category);
            validate = false;
        } else {
            NCDSValidator.unhighlight(category);
        }

        if (!templateName.value.trim()) {
            NCDSValidator.highlight(templateName);
            validate = false;
        } else {
            NCDSValidator.unhighlight(templateName);
        }
        if (!validate) return;

        $.post('./myapp_push/myapp_push_template_ps.php', {
            mode: 'save',
            category: category.value,
            templateName: templateName.value.trim(),
            pushSubject: pushSubject.value ?? '',
            pushContent: pushContent.value ?? '',
            pushImage: pushImage?.value,
            pushUrl: pushUrl?.value,
            pushWithdraw: pushWithdraw?.value,
        }, function(response) {
            if (response.success) {
                NCDSToast({message: '저장되었습니다.', color: 'success'});
                document.querySelector('.js-layer-close').click();
            } else {
                NCDSAlert({
                    message: response.message || '저장에 실패했습니다.',
                    type: 'error',
                });
            }
        }, 'json');
    });
</script>
