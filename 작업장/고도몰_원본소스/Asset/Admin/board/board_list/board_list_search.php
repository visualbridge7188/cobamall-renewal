<form id="frmSearchBase" method="get" class="js-form-enter-submit">
    <div class="ncua-table ncua-table--vertical">
        <table>
            <colgroup>
                <col class="search-keyword-table-th-width"/>
                <col/>
            </colgroup>
            <tr>
                <th><div>검색어</div></th>
                <td>
                    <div class="ncua-gap-4">
                        <span class="ncua-select ncua-select--xs">
                            <span class="ncua-select__content">
                                <?=gd_select_box('key', 'key', array( 'bdId' => '아이디', 'bdNm' => '이름'), '', gd_isset($search['key']), null, null, 'ncua-select__tag'); ?>
                            </span>
                        </span>
                        <span class="ncua-select ncua-select--xs">
                            <span class="ncua-select__content">
                                <?= gd_select_box('searchKind', 'searchKind', $searchKindASelectBox, null, gd_isset($search['searchKind']), null, null, 'ncua-select__tag'); ?>
                            </span>
                        </span>
                        <div class="ncua-input ncua-input--xs ncua-input-width-320">
                            <div class="ncua-input__content">
                                <div class="ncua-input__field ncua-input__field--xs">
                                    <input type="text" name="keyword" value="<?=gd_isset($search['keyword']); ?>"
                                    />
                                </div>
                            </div>
                        </div>  
                    </div>
                </td>
            </tr>
            <tr>
                <th><div>유형</div></th>
                <td>
                    <div class="ncua-flex-gap">
                        <label class="ncua-checkbox-field ncua-checkbox-field--xs has-text">
                            <span class="ncua-checkbox-input ncua-checkbox-input--xs ncua-checkbox-field__input">
                                <input type="checkbox" name="boardKind[all]" value="y" <?=$checked['boardKind']['all']?>>
                            </span>
                            <span><span class="ncua-checkbox-field__text">전체</span></span>
                        </label>
                        <label class="ncua-checkbox-field ncua-checkbox-field--xs has-text">
                            <span class="ncua-checkbox-input ncua-checkbox-input--xs ncua-checkbox-field__input">
                                <input type="checkbox" name="boardKind[default]" value="y" <?=$checked['boardKind']['default']?>>
                            </span>
                            <span><span class="ncua-checkbox-field__text">일반형</span></span>
                        </label>
                        <label class="ncua-checkbox-field ncua-checkbox-field--xs has-text">
                            <span class="ncua-checkbox-input ncua-checkbox-input--xs ncua-checkbox-field__input">
                                <input type="checkbox" name="boardKind[gallery]" value="y" <?=$checked['boardKind']['gallery']?>>
                            </span>
                            <span><span class="ncua-checkbox-field__text">갤러리형</span></span>
                        </label>
                        <label class="ncua-checkbox-field ncua-checkbox-field--xs has-text">
                            <span class="ncua-checkbox-input ncua-checkbox-input--xs ncua-checkbox-field__input">
                                <input type="checkbox" name="boardKind[event]" value="y" <?=$checked['boardKind']['event']?>>
                            </span>
                            <span><span class="ncua-checkbox-field__text">이벤트형</span></span>
                        </label>
                        <label class="ncua-checkbox-field ncua-checkbox-field--xs has-text">
                            <span class="ncua-checkbox-input ncua-checkbox-input--xs ncua-checkbox-field__input">
                                <input type="checkbox" name="boardKind[qa]" value="y" <?=$checked['boardKind']['qa']?>>
                            </span>
                            <span><span class="ncua-checkbox-field__text">1:1문의형</span></span>
                        </label>
                    </div>
                </td>
            </tr>
        </table>
    </div>
    <p class="ncua-btn-group ncua-align-right search-toolbar">
        <button type="button" id="btnReset" class="ncua-btn ncua-btn--xs has-underline ncua-btn--text"><span class="ncua-btn__label">초기화</span></button>
        <button type="submit" class="ncua-btn ncua-btn--xs ncua-btn--secondary"><span class="ncua-btn__label">검색</span></button>
    </p>
</form>

