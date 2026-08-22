<form id="frmSearchOrder" method="get">
    <input type="hidden" name="detailSearch" value="<?= $search['detailSearch']; ?>"/>
    <input type="hidden" name="checkType" value="<?= $checkType; ?>"/>
    <input type="hidden" id="sort" name="sort" value=""/>
    <input type="hidden" id="pageNum" name="pageNum" value=""/>

    <div class="popup-order-title">
        주문 내역 선택
    </div>
    <div class="search-table-wrap">
        <div class="ncua-table ncua-table--vertical">
            <table>
                <colgroup>
                    <col width="144px"/>
                    <col/>
                    <col/>
                    <col/>
                </colgroup>
                <tbody>
                    <?php if(gd_use_provider() === true) { ?>
                    <?php if (!isset($isProvider) && $isProvider != true) { ?>
                    <tr>
                        <th><div>공급사 구분</div></th>
                        <td colspan="3">
                            <div class="radio-combobox-wrap">
                                <div class="supply-radio-group">
                                    <label class="ncua-radio-field ncua-radio-field--xs has-text">
                                        <span class="ncua-radio-input ncua-radio-input--xs ncua-radio-field__input">
                                            <input type="radio" name="scmFl" value="all" <?= gd_isset($checked['scmFl']['all']); ?>/>
                                        </span>
                                        <span>
                                            <span class="ncua-radio-field__text">전체</span>
                                        </span>
                                    </label>
                                    <label class="ncua-radio-field ncua-radio-field--xs has-text">
                                        <span class="ncua-radio-input ncua-radio-input--xs ncua-radio-field__input">
                                            <input type="radio" name="scmFl" value="0" <?= gd_isset($checked['scmFl']['0']); ?>/>
                                        </span>
                                        <span>
                                            <span class="ncua-radio-field__text">본사</span>
                                        </span>
                                    </label>
                                    <label class="ncua-radio-field ncua-radio-field--xs has-text">
                                        <span class="ncua-radio-input ncua-radio-input--xs ncua-radio-field__input">
                                            <input type="radio" name="scmFl" value="1" <?= gd_isset($checked['scmFl']['1']); ?> data-type="scm" data-mode="checkbox"/>
                                        </span>
                                        <span>
                                            <span class="ncua-radio-field__text">공급사</span>
                                        </span>
                                    </label>
                                    <div id="ncua-combo-box-layer"></div>
                                </div>
                                <div id="scmLayer" class="ncua-flex ncua-gap-4 selected-tag-list">
                                    <span>선택된 공급사 : </span>
                                    <?php if (($search['scmFl'] == 'y') || ($search['scmFl'] == '1') && empty($search['scmNo']) === false) { ?>
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
                    <?php } ?>
                    <?php } ?>
                    <tr>
                        <th><div>검색어</div></th>
                        <td colspan="3">
                            <div class="ncua-gap-8">
                                <span class="ncua-select ncua-select--xs">
                                    <span class="ncua-select__content">
                                        <?= gd_select_box('key', 'key', $search['combineSearch'], null, $search['key'], null, null, 'ncua-select__tag'); ?>
                                    </span>
                                </span>
                                <div class="ncua-input ncua-input--xs ncua-input-width-520">
                                    <div class="ncua-input__field ncua-input__field--xs">
                                        <input type="text" name="keyword" value="<?= $search['keyword']; ?>"/>
                                    </div>
                                </div>
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <th><div>기간 설정</div></th>
                        <td colspan="3">
                            <div class="ncua-gap-8">
                                <span class="ncua-select ncua-select--xs">
                                    <span class="ncua-select__content">
                                        <?= gd_select_box('treatDateFl', 'treatDateFl', $search['combineTreatDate'], null, $search['treatDateFl'], null, null, 'ncua-select__tag'); ?>
                                    </span>
                                </span>
                                <div id="datepicker-container"></div>
                            </div>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
    <div class="ncua-btn-group ncua-align-right">
        <button type="button" class="ncua-btn ncua-btn--xs ncua-btn--text has-underline ncua-btn--text js-btn-reset">초기화</button>
        <button type="submit" class="ncua-btn ncua-btn--xs ncua-btn--secondary">검색</button>
    </div>
</form>
<!-- // 검색을 위한 form -->
