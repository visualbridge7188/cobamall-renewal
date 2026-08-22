<form name="frmSearch" id="frmSearch" action="plus_review_list.php" class="frmSearch js-form-enter-submit">
    <input type="hidden" id="isShow" name="isShow" value="<?=$isShow?>"/>
    <input type="hidden" id="listType" name="listType" value="<?=$listType?>"/>
    <input type="hidden" id="sort" name="sort" value="<?=gd_isset($req['sort'])?>"/>
    <input type="hidden" id="pageNum" name="pageNum" value="<?=gd_isset(Request::get()->get('pageNum', 10))?>"/>
    
    <div class="ncua-table ncua-table--vertical">
        <table>
            <colgroup>
                <col>
                <col>
                <col width="240px">
                <col>
            </colgroup>
            <tbody>
                <tr>
                    <th><div>게시판</div></th>
                    <td >
                        <div>
                            <b>플러스리뷰 게시판</b>
                        </div>
                    </td>
                </tr>
                <tr>
                    <th>
                        <div><?php if ($isShow == 'n') echo '신고'; ?>기간 설정</div>
                    </th>
                    <td colspan="3">
                        <div class="ncua-flex-gap">
                            <?php if($isShow == 'n') { ?>
                            <input type="hidden" name="searchDateFl" value="reportDt" />
                            <?php } else { ?>
                            <div class="ncua-radio-field-group">
                                <label class="ncua-radio-field ncua-radio-field--xs has-text">
                                    <span class="ncua-radio-input ncua-radio-input--xs ncua-radio-field__input">
                                        <input type="radio" id="searchDateRegDt" name="searchDateFl" value="regDt" <?php if ($req['searchDateFl'] == 'regDt' || !isset($req['searchDateFl'])) echo 'checked' ?>>
                                    </span>
                                    <span class="ncua-radio-field__text">등록일</span>
                                </label>
                                <label class="ncua-radio-field ncua-radio-field--xs has-text">
                                    <span class="ncua-radio-input ncua-radio-input--xs ncua-radio-field__input">
                                        <input type="radio" id="searchDateModDt" name="searchDateFl" value="modDt" <?php if ($req['searchDateFl'] == 'modDt') echo 'checked' ?>>
                                    </span>
                                    <span class="ncua-radio-field__text">수정일</span>
                                </label>
                            </div>
                            <?php } ?>

                            <div id="datepicker-container"></div>
                        </div>
                    </td>
                </tr>
                <?php if($isShow != 'n') { ?>
                <tr>
                    <th><div>검색어</div></th>
                    <td colspan="3">
                        <div class="ncua-flex-gap">
                            <div class="ncua-select ncua-select--xs">
                                <div class="ncua-select__content ">
                                    <select id="searchField" class="ncua-select__tag" name="searchField">
                                        <option value="goodsNm" <?=$req['searchField'] == 'goodsNm' ? 'selected' : ''?>>상품명</option>
                                        <option value="contents" <?=$req['searchField'] == 'contents' ? 'selected' : ''?>>내용</option>
                                        <option value="writerNm" <?=$req['searchField'] == 'writerNm' ? 'selected' : ''?>>이름</option>
                                        <option value="writerNick" <?=$req['searchField'] == 'writerNick' ? 'selected' : ''?>>닉네임</option>
                                        <option value="writerId" <?=$req['searchField'] == 'writerId' ? 'selected' : ''?>>아이디</option>
                                    </select>
                                </div>
                            </div>
                            <div class="ncua-select ncua-select--xs search-kind">
                                <div class="ncua-select__content">
                                    <?= gd_select_box('searchKind', 'searchKind', $searchKindASelectBox, null, gd_isset($req['searchKind']), null, null, 'ncua-select__tag'); ?>
                                </div>
                            </div>
                            <div class="ncua-input ncua-input--xs ncua-input-width-320">
                                <div class="ncua-input__content">
                                    <div class="ncua-input__field ncua-input__field--xs">
                                        <input name="searchWord" type="text" value="<?= gd_isset($req['searchWord']) ?>" />
                                    </div>
                                </div>
                            </div>
                        </div>
                    </td>
                </tr>
                <tr>
                    <?php if($isShow != 'n') { ?>
                    <th><div>속성</div></th>
                    <td>
                        <div class="ncua-flex-gap">
                            <label class="ncua-radio-field ncua-radio-field--xs has-text">
                                <span class="ncua-radio-input ncua-radio-input--xs ncua-radio-field__input">
                                    <input type="radio" name="reviewType" value="" <?php if ($req['reviewType'] == '') echo 'checked' ?>>
                                </span>
                                <span class="ncua-radio-field__text">전체</span>
                            </label>
                            <label class="ncua-radio-field ncua-radio-field--xs has-text">
                                <span class="ncua-radio-input ncua-radio-input--xs ncua-radio-field__input">
                                    <input type="radio" name="reviewType" value="photo" <?php if ($req['reviewType'] == 'photo') echo 'checked' ?>>
                                </span>
                                <span class="ncua-radio-field__text">포토리뷰</span>
                            </label>
                            <label class="ncua-radio-field ncua-radio-field--xs has-text">
                                <span class="ncua-radio-input ncua-radio-input--xs ncua-radio-field__input">
                                    <input type="radio" name="reviewType" value="text" <?php if ($req['reviewType'] == 'text') echo 'checked' ?>>
                                </span>
                                <span class="ncua-radio-field__text">일반리뷰</span>
                            </label>
                        </div>
                    </td>
                    <?php } ?>
                    <th><div>댓글여부</div></th>
                    <td>
                        <div class="ncua-flex-gap">
                            <label class="ncua-radio-field ncua-radio-field--xs has-text">
                                <span class="ncua-radio-input ncua-radio-input--xs ncua-radio-field__input">
                                    <input type="radio" name="isMemo" value="" <?php if ($req['isMemo'] == '') echo 'checked' ?>>
                                </span>
                                <span class="ncua-radio-field__text">전체</span>
                            </label>
                            <label class="ncua-radio-field ncua-radio-field--xs has-text">
                                <span class="ncua-radio-input ncua-radio-input--xs ncua-radio-field__input">
                                    <input type="radio" name="isMemo" value="y" <?php if ($req['isMemo'] == 'y') echo 'checked' ?>>
                                </span>
                                <span class="ncua-radio-field__text">댓글있음</span>
                            </label>
                            <label class="ncua-radio-field ncua-radio-field--xs has-text">
                                <span class="ncua-radio-input ncua-radio-input--xs ncua-radio-field__input">
                                    <input type="radio" name="isMemo" value="n" <?php if ($req['isMemo'] == 'n') echo 'checked' ?>>
                                </span>
                                <span class="ncua-radio-field__text">댓글없음</span>
                            </label>
                        </div>
                    </td>
            
                </tr>
                <tr>
                    <th><div>마일리지 지급</div></th>
                    <td>
                        <div class="ncua-flex-gap">
                            <label class="ncua-radio-field ncua-radio-field--xs has-text">
                                <span class="ncua-radio-input ncua-radio-input--xs ncua-radio-field__input">
                                    <input type="radio" name="mileage" value="" <?php if ($req['mileage'] == '') echo 'checked' ?>>
                                </span>
                                <span class="ncua-radio-field__text">전체</span>
                            </label>
                            <label class="ncua-radio-field ncua-radio-field--xs has-text">
                                <span class="ncua-radio-input ncua-radio-input--xs ncua-radio-field__input">
                                    <input type="radio" name="mileage" value="y" <?php if ($req['mileage'] == 'y') echo 'checked' ?>>
                                </span>
                                <span class="ncua-radio-field__text">지급완료</span>
                            </label>
                            <label class="ncua-radio-field ncua-radio-field--xs has-text">
                                <span class="ncua-radio-input ncua-radio-input--xs ncua-radio-field__input">
                                    <input type="radio" name="mileage" value="w" <?php if ($req['mileage'] == 'w') echo 'checked' ?>>
                                </span>
                                <span class="ncua-radio-field__text">지급예정</span>
                            </label>
                            <label class="ncua-radio-field ncua-radio-field--xs has-text">
                                <span class="ncua-radio-input ncua-radio-input--xs ncua-radio-field__input">
                                    <input type="radio" name="mileage" value="n" <?php if ($req['mileage'] == 'n') echo 'checked' ?>>
                                </span>
                                <span class="ncua-radio-field__text">미지급</span>
                            </label>
                            <label class="ncua-radio-field ncua-radio-field--xs has-text">
                                <span class="ncua-radio-input ncua-radio-input--xs ncua-radio-field__input">
                                    <input type="radio" name="mileage" value="i" <?php if ($req['mileage'] == 'i') echo 'checked' ?>>
                                </span>
                                <span class="ncua-radio-field__text">지급불가</span>
                            </label>
                        </div>
                    </td>
                    <th class="ncua-border-radius-none"><div>승인여부</div></th>
                    <td>
                        <div class="ncua-flex-gap">
                            <label class="ncua-radio-field ncua-radio-field--xs has-text">
                                <span class="ncua-radio-input ncua-radio-input--xs ncua-radio-field__input">
                                    <input type="radio" name="applyFl" value="" <?php if ($req['applyFl'] == '') echo 'checked' ?>>
                                </span>
                                <span class="ncua-radio-field__text">전체</span>
                            </label>
                            <label class="ncua-radio-field ncua-radio-field--xs has-text">
                                <span class="ncua-radio-input ncua-radio-input--xs ncua-radio-field__input">
                                    <input type="radio" name="applyFl" value="y" <?php if ($req['applyFl'] == 'y') echo 'checked' ?>>
                                </span>
                                <span class="ncua-radio-field__text">승인</span>
                            </label>
                            <label class="ncua-radio-field ncua-radio-field--xs has-text">
                                <span class="ncua-radio-input ncua-radio-input--xs ncua-radio-field__input">
                                    <input type="radio" name="applyFl" value="n" <?php if ($req['applyFl'] == 'n') echo 'checked' ?>>
                                </span>
                                <span class="ncua-radio-field__text">미승인</span>
                            </label>
                        </div>
                    </td>
                </tr>
                <?php } ?>
        </tbody>
        </table>
    </div>

    <div class="ncua-btn-group ncua-align-right">
        <button type="reset" name="reset" class="ncua-btn ncua-btn--xs has-underline ncua-btn--text">초기화</button>
        <button type="submit" class="ncua-btn ncua-btn--xs ncua-btn--secondary">검색</button>
    </div>
</form>

