<form id="frmSearch" method="get" class="js-form-enter-submit">
    <div class="ncua-table ncua-table--vertical">
        <table>
            <?php if($gGlobal['isUse']){?>
            <tr>
                <th><div>상점</div></th>
                <td>
                    <div class="ncua-flex-gap">
                    <?php foreach($gGlobal['useMallList'] as $val) {
                        ?>
                        <label class="ncua-radio-field ncua-radio-field--xs has-text">
                            <span class="ncua-radio-input ncua-radio-input--xs ncua-radio-field__input">
                                <input type="radio" name="domainFl" value="<?=$val['domainFl']?>" <?=$checked['domainFl'][$val['domainFl']]?>>
                            </span>
                            <span class="ncua-radio-field__text"><?=$val['mallName']?></span>
                        </label>
                    <?php }?>
                    </div>
                </td>
            </tr>
            <?php }?>
            <tr>
                <th><div>검색어</div></th>
                <td>
                    <div class="board-theme-search-keyword">
                        <span class="ncua-select ncua-select--xs">
                            <span class="ncua-select__content">
                            <?=gd_select_box('searchField', 'searchField', array( 'themeId' => '스킨코드', 'themeNm' => '이름'), '', gd_isset($req['searchField']), null, null, 'ncua-select__tag'); ?>
                            </span>
                        </span>
                        <div class="ncua-input ncua-input--xs ncua-input-width-320">
                            <div class="ncua-input__content">
                                <div class="ncua-input__field ncua-input__field--xs">
                                    <input type="text" name="keyword" value="<?=gd_isset($req['keyword']); ?>" />
                                </div>
                            </div>
                        </div>
                    </div>
                </td>
            </tr>
            <tr>
                <th><div>구분</div></th>
                <td>
                    <div class="ncua-flex-gap">
                        <label class="ncua-radio-field ncua-radio-field--xs has-text">
                            <span class="ncua-radio-input ncua-radio-input--xs ncua-radio-field__input">
                                <input type="radio" name="deviceType" value="all" <?=$checked['deviceType']['all']?>>
                            </span>
                            <span class="ncua-radio-field__text">전체</span>
                        </label>
                        <label class="ncua-radio-field ncua-radio-field--xs has-text">
                            <span class="ncua-radio-input ncua-radio-input--xs ncua-radio-field__input">
                                <input type="radio" name="deviceType" value="pc" <?=$checked['deviceType']['pc']?>>
                            </span>
                            <span class="ncua-radio-field__text">PC쇼핑몰</span>
                        </label>
                        <label class="ncua-radio-field ncua-radio-field--xs has-text">
                            <span class="ncua-radio-input ncua-radio-input--xs ncua-radio-field__input">
                                <input type="radio" name="deviceType" value="mobile" <?=$checked['deviceType']['mobile']?>>
                            </span>
                            <span class="ncua-radio-field__text">모바일쇼핑몰</span>
                        </label>
                    </div>
                </td>
            </tr>
            <?php if($gGlobal['isUse']){?>
            <tr>
                <th><div>적용 디자인 스킨</div></th>
                <td>
                    <div>
                        <span class="ncua-select ncua-select--xs">
                            <span class="ncua-select__content"> 
                                <select name="liveSkin" id="liveSkin" class="ncua-select__tag">
                                    <option value="">=디자인 스킨 검색=</option>
                                    <?php foreach($applySkinList as $key=>$val) {?>
                                        <option data-role="<?=$val['device']?>"  value="<?=$val['device'].STR_DIVISION.$val['skinCode']?>" <?=$selected['liveSkin'][$val['skinValue']]?>><?=$val['skinTitle']?></option>
                                    <?php }?>
                                </select>
                            </span>
                        </span>
                        <!-- <div data-target="liveSkin"></div> -->
                    </div>
                </td>
            </tr>
            <?php }?>
            <tr>
                <th><div>유형</div></th>
                <td>
                    <div class="ncua-flex-gap">
                        <label class="ncua-checkbox-field ncua-checkbox-field--xs has-text">
                            <span class="ncua-checkbox-input ncua-checkbox-input--xs ncua-checkbox-field__input">
                                <input type="checkbox" name="boardKind[all]" value="y" <?=$checked['boardKind']['all']?>>
                            </span>
                            <span class="ncua-checkbox-field__text">전체</span>
                        </label>
                        <label class="ncua-checkbox-field ncua-checkbox-field--xs has-text">
                            <span class="ncua-checkbox-input ncua-checkbox-input--xs ncua-checkbox-field__input">
                                <input type="checkbox" name="boardKind[default]" value="y" <?=$checked['boardKind']['default']?>>
                            </span>
                            <span class="ncua-checkbox-field__text">일반형</span>
                        </label>
                        <label class="ncua-checkbox-field ncua-checkbox-field--xs has-text">
                            <span class="ncua-checkbox-input ncua-checkbox-input--xs ncua-checkbox-field__input">
                                <input type="checkbox" name="boardKind[gallery]" value="y" <?=$checked['boardKind']['gallery']?>>
                            </span>
                            <span class="ncua-checkbox-field__text">갤러리형</span>
                        </label>
                        <label class="ncua-checkbox-field ncua-checkbox-field--xs has-text">
                            <span class="ncua-checkbox-input ncua-checkbox-input--xs ncua-checkbox-field__input">
                                <input type="checkbox" name="boardKind[event]" value="y" <?=$checked['boardKind']['event']?>>
                            </span>
                            <span class="ncua-checkbox-field__text">이벤트형</span>
                        </label>
                        <label class="ncua-checkbox-field ncua-checkbox-field--xs has-text">
                            <span class="ncua-checkbox-input ncua-checkbox-input--xs ncua-checkbox-field__input">
                                <input type="checkbox" name="boardKind[qa]" value="y" <?=$checked['boardKind']['qa']?>>
                            </span>
                            <span class="ncua-checkbox-field__text">1:1문의형</span>
                        </label>
                    </div>
                </td>
            </tr>
        </table>
    </div>
    <div class="ncua-btn-group ncua-align-right">
        <button type="reset" class="ncua-btn ncua-btn--xs has-underline ncua-btn--text">초기화</button>
        <button type="submit" class="ncua-btn ncua-btn--xs ncua-btn--secondary">검색</button>
    </div>
</form>

