<form id="frmSearch" name="frmSearch" method="get" class="js-form-enter-submit">
    <div class="ncua-table ncua-table--vertical">
        <table>
            <colgroup>
                <col/>
                <col/>
            </colgroup>
        <tbody>
            <?php if(gd_is_provider() === false) {?>
            <tr>
                <th><div>공급사 구분</div></th>
                <td>
                    <div class="radio-combobox-wrap">
                        <div class="supply-radio-group">
                            <label class="ncua-radio-field ncua-radio-field--xs has-text">
                                <span class="ncua-radio-input ncua-radio-input--xs ncua-radio-field__input">
                                    <input type="radio" name="scmFl" value="all" <?= gd_isset($search['checked']['scmFl']['all']); ?>/>
                                </span>
                                <span>
                                    <span class="ncua-radio-field__text">전체</span>
                                </span>
                            </label>
                            <label class="ncua-radio-field ncua-radio-field--xs has-text">
                                <span class="ncua-radio-input ncua-radio-input--xs ncua-radio-field__input">
                                    <input type="radio" name="scmFl" value="n" <?= gd_isset($search['checked']['scmFl']['n']); ?>/>
                                </span>
                                <span>
                                    <span class="ncua-radio-field__text">본사</span>
                                </span>
                            </label>
                            <label class="ncua-radio-field ncua-radio-field--xs has-text">
                                <span class="ncua-radio-input ncua-radio-input--xs ncua-radio-field__input">
                                    <input type="radio" name="scmFl" value="y" <?= gd_isset($search['checked']['scmFl']['y']); ?> data-type="scm" data-mode="checkbox"/>
                                </span>
                                <span>
                                    <span class="ncua-radio-field__text">공급사</span>
                                </span>
                            </label>
                            <div id="ncua-combo-box-layer"></div>
                        </div>
                        <div id="scmLayer" class="ncua-flex ncua-gap-4 selected-tag-list">
                            <span>선택된 공급사 : </span>
                            <?php if (($search['scmFl'] == 'y') && empty($search['scmNo']) === false) { ?>
                                <?php foreach ($search['scmNo'] as $k => $v) { ?>
                                    <span class="ncua-tag ncua-tag--sm" id="info_scm_<?= $v ?>">
                                        <input type="hidden" name="scmNo[]" value="<?= $v ?>"/>
                                        <input type="hidden" name="scmNoNm[]" value="<?= $search['scmNoNm'][$k] ?>"/>
                                        <span class="ncua-tag__text" name="scmNoNm[]"><?= $search['scmNoNm'][$k] ?></span>
                                        <button type="button" class="ncua-tag__close" data-toggle="delete" data-target="#info_scm_<?= $v ?>">
                                            <span class="scm-tag ncua-select-group-tag"></span>
                                        </button>
                                    </span>
                                <?php } ?>    
                            <?php } ?>
                        </div>
                    </div>
                </td>
            </tr>
            <?php }?>
            <tr>
                <th><div>분류</div></th>
                <td>
                    <div>
                        <span class="ncua-select ncua-select--xs">
                            <span class="ncua-select__content">
                                <?= gd_select_box('category', 'category', $category, null, gd_isset($search['category']), '=전체=', null, 'ncua-select__tag'); ?>
                            </span>
                        </span>
                    </div>
                </td>  
            </tr>
            <tr>
                <th><div>검색어</div></th>
                <td>
                    <div class="ncua-gap-4">
                        <span class="ncua-select ncua-select--xs">
                            <span class="ncua-select__content">
                                <?= gd_select_box('searchField', 'searchField', $search['searchSelectField'], null, $search['searchField'], '=통합검색=', null, 'ncua-select__tag'); ?>
                            </span>
                        </span>
                        <span class="ncua-select ncua-select--xs search-kind">
                            <span class="ncua-select__content">
                                <?= gd_select_box('searchKind', 'searchKind', $searchKindASelectBox, null, gd_isset($search['searchKind']), null, null, 'ncua-select__tag'); ?>
                            </span>
                        </span>
                        <div class="ncua-input ncua-input--xs ncua-input-width-320">
                            <div class="ncua-input__field ncua-input__field--xs">
                                <input name="keyword" type="text" value="<?= $search['keyword']; ?>" />
                            </div>
                        </div>
                    </div>
                </td>
            </tr>
            <tr>
                <th><div>기간 설정</div></th>
                <td>
                    <div>
                        <div id="datepicker-container"></div>
                    </div>
                </td>
            </tr>
            </tbody>
        </table>
    </div>

    <div class="ncua-btn-group ncua-align-right">
    <button type="button" class="ncua-btn ncua-btn--xs has-underline ncua-btn--text js-btn-reset">초기화</button>
        <button type="submit" class="ncua-btn ncua-btn--xs ncua-btn--secondary">검색</button>
    </div>
</form>
