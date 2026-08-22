<form id="frmSearchBase" method="get" class="js-form-enter-submit">
    <div class="ncua-table ncua-table--vertical" data-component-no="b011">
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
                                        <input type="radio" name="mallSno" value="<?=$val['sno']?>" <?=$checked['mallSno'][$val['sno']]?>>
                                    </span>
                                    <span class="ncua-radio-field__text"><?=$val['mallName']?></span>
                                </label>
                            <?php }?>
                        </div>
                    </td>
                </tr>
            <?php }?>
            <tr>
                <th><div>카테고리</div></th>
                <td>
                    <div>
                        <span class="ncua-select ncua-select--xs">
                            <span class="ncua-select__content">
                        <?php
                        echo gd_select_box('category', 'category', gd_code('03001',$search['mallSno']), null, gd_isset($search['category']), '=전체=', null, 'ncua-select__tag'); ?>
                            </span>
                        </span>
                    </div>
                </td>
            </tr>
            <tr>
                <th><div>유형</div></th>
                <td>
                    <div class="ncua-flex-gap">
                        <label class="ncua-radio-field ncua-radio-field--xs has-text">
                            <span class="ncua-radio-input ncua-radio-input--xs ncua-radio-field__input">
                                <input type="radio" name="isBest" value="" <?=$checked['isBest'][''] ?> />
                            </span>
                            <span class="ncua-radio-field__text">전체</span>
                        </label>
                        <label class="ncua-radio-field ncua-radio-field--xs has-text">
                            <span class="ncua-radio-input ncua-radio-input--xs ncua-radio-field__input">
                                <input type="radio" name="isBest" value="n" <?=$checked['isBest']['n'] ?>/>
                            </span>
                            <span class="ncua-radio-field__text">일반</span>
                        </label>
                        <label class="ncua-radio-field ncua-radio-field--xs has-text">
                            <span class="ncua-radio-input ncua-radio-input--xs ncua-radio-field__input">
                                <input type="radio" name="isBest" value="y" <?=$checked['isBest']['y'] ?>/>
                            </span>
                            <span class="ncua-radio-field__text">베스트</span>
                        </label>
                    </div>
                </td>
            </tr>
            <tr>
                <th><div>등록일</div></th>
                <td>
                    <div>
                        <div id="datepicker-container"></div>
                    </div>
                </td>
            </tr>
            <tr>
                <th><div>검색</div></th>
                <td>
                    <div class="faq-list-search">
                        <span class="ncua-select ncua-select--xs">
                            <span class="ncua-select__content">
                                <?=gd_select_box('searchKey', 'searchKey', array('all' => '=통합검색=', 'subject' => '제목', 'contents' => '내용', 'answer' => '답변'), null, gd_isset($search['searchKey']), null, null, 'ncua-select__tag'); ?>
                            </span>
                        </span>
                        <div class="ncua-input ncua-input--xs">
                            <div class="ncua-input__content">
                                <div class="ncua-input__field ncua-input__field--xs ncua-input-width-320">
                                    <input type="text" placeholder="검색어를 입력해주세요." name="searchWord" value="<?=gd_isset($search['searchWord']); ?>" />
                                </div>
                            </div>
                        </div> 
                    </div>
                </td>
            </tr>
        </table>
    </div>
    <div class="ncua-btn-group ncua-align-right">
        <button type="reset" class="ncua-btn ncua-btn--xs has-underline ncua-btn--text js-faq-search-reset">초기화</button>
        <button type="submit" class="ncua-btn ncua-btn--xs ncua-btn--secondary">검색</button>
    </div>
</form>
