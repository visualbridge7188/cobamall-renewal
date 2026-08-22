<script defer src="<?=gd_set_browser_cache(PATH_ADMIN_GD_SHARE . 'ncds/script/common/charcount.js')?>"></script>
<link rel="stylesheet" href="<?=gd_set_browser_cache(PATH_ADMIN_GD_SHARE . 'ncds/css/crm/layer-save-as-template.css')?>">

<div class="modal-dialog__content layer-save-as-template">
    <div class="ncua-table ncua-table--vertical">
        <table>
            <tbody>
                <tr>
                    <th><div>카테고리</div></th>
                    <td>
                        <div>
                            <div class="ncua-select ncua-select--xs">
                                <div class="ncua-select__content">
                                    <select class="ncua-select__tag">
                                        <option value="">카테고리 선택</option>
                                        <option value="1">Option 1</option>
                                        <option value="2">Option 2</option>
                                        <option value="3">Option 3</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </td>
                </tr>
                <tr>
                    <th><div>제목</div></th>
                    <td>
                        <div>
                            <div class="ncua-input ncua-input--xs ncua-input--full-width-with-count">
                                <div class="ncua-input__content-wrap">
                                    <div class="ncua-input__content">
                                        <div class="ncua-input__field ncua-input__field--xs">
                                            <input maxlength="10" type="text" placeholder="제목을 입력하세요" class="layer-template-title" data-charcount-key="layerTemplateTitle" />
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
    <button type="button" class="ncua-btn ncua-btn--sm ncua-btn--primary js-layer-close">
        <span class="ncua-btn__label">저장</span>
    </button>
</div>

<script type="text/javascript">
    const charCountManager = createCharCountManager({
        targetClasses: ['ncua-title'],
    });
    charCountManager.init();
</script>
