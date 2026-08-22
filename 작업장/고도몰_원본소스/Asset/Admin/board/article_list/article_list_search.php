<form name="frmSearch" id="frmSearch" action="article_list.php" class="frmSearch js-form-enter-submit">
    <input type="hidden" id="boardListSort" name="boardListSort" value="<?=$boardListSort?>"/>
    <input type="hidden" id="isShow" name="isShow" value="<?=$isShow?>"/>
    <input type="hidden" id="listType" name="listType" value="<?=$listType?>"/>
    <input type="hidden" id="sort" name="sort" value="<?= $req['sort'] ?>"/>
    <input type="hidden" id="pageNum" name="pageNum" value="<?= $req['pageNum'] ?>"/>
    <div class="ncua-table ncua-table--vertical">
        <table>
            <tr>
                <th><div>게시판</div></th>
                <td>
                    <div>
                        <?php if (!gd_is_provider()) { ?>
                            <span class="ncua-select ncua-select--xs subject">
                                <span class="ncua-select__content">
                                    <select name="bdId" id="bdId" class="ncua-select__tag">
                                        <?php
                                        if (isset($boards) && is_array($boards)) {
                                            foreach ($boards as $val) {
                                                ?>
                                                <option
                                                    value="<?= $val['bdId'] ?>" <?php if ($val['bdId'] == $bdList['cfg']['bdId'])
                                                    echo "selected='selected'" ?> data-bdReplyStatusFl="<?=$val['bdReplyStatusFl']?>" data-bdEventFl="<?=$val['bdEventFl']?>" data-bdGoodsPtFl="<?=$val['bdGoodsPtFl']?>" data-bdGoodsFl="<?=$val['bdGoodsFl']?>"><?= $val['bdNm'] . '(' . $val['bdId'] . ')' ?></option>
                                                <?php
                                            }
                                        }
                                        ?>
                                    </select>
                                </span>
                            </span>
                        <?php } else { ?>
                            <?= $bdList['cfg']['bdNm'] ?> (<?= $bdList['cfg']['bdId'] ?>)
                            <input type="hidden" name="bdId" value="<?= $bdList['cfg']['bdId'] ?>"/>
                        <?php } ?>
                    </div>
                </td>
            </tr>
            <tr>
                <?php if($isShow != 'n') { ?>
                <th><div>말머리</div></th>
                <td>
                    <div>
                    <?php if (gd_isset($bdList['categoryBox'])) { ?>
                        <span class="ncua-select ncua-select--xs preface">
                            <span class="ncua-select__content">
                                <?= gd_isset($bdList['categoryBox']); ?>
                            </span>
                        </span>
                    <?php } else { ?>
                        <span>-</span>
                    <?php } ?>
                    </div>
                </td>
                <?php } ?>
            </tr>
            <tr>
                <th>
                    <div><?php echo ($isShow == 'n') ? '신고일자' : '기간 설정'; ?></div>
                </th>
                <td>
                    <div class="ncua-flex-gap">
                        <?php if($isShow == 'n') { ?>
                        <input type="hidden" name="searchDateFl" value="reportDt" />
                        <?php } else { ?>
                            <div class="ncua-flex-gap ncua-flex">
                                <label class="ncua-radio-field ncua-radio-field--xs has-text">
                                    <span class="ncua-radio-input ncua-radio-input--xs ncua-radio-field__input"><input type="radio" name="searchDateFl" value="regDt" 
                                        <?php if ($req['searchDateFl'] == 'regDt' || empty($req['searchDateFl'])) echo 'checked' ?> 
                                    /></span>
                                    <span>
                                        <span class="ncua-radio-field__text">등록일 기준</span>
                                    </span>
                                </label>
                                <label class="ncua-radio-field ncua-radio-field--xs has-text">
                                    <span class="ncua-radio-input ncua-radio-input--xs ncua-radio-field__input"><input type="radio" name="searchDateFl" value="modDt" <?php if ($req['searchDateFl'] == 'modDt') echo 'checked' ?> /></span>
                                    <span>
                                        <span class="ncua-radio-field__text">수정일 기준</span>
                                    </span>
                                </label>
                            </div>
                        <?php } ?>
                        <div id="datepicker-container"></div>
                    </div>
                </td>
            </tr>
            <?php if($isShow != 'n') { ?>
            <tr class="js-if-bdGoodsPtFl">
                <?php  if ($bdList['cfg']['bdAnswerStatusFl'] == 'y' || $bdList['cfg']['bdReplyStatusFl'] == 'y') { ?>
                    <th><div>답변상태</div></th>
                    <td>
                        <div>
                            <span class="ncua-select ncua-select--xs reply-status">
                                <span class="ncua-select__content">
                                    <select id="replyStatus" name="replyStatus" class="ncua-select__tag">
                                        <option value="">=전체=</option>
                                        <?php foreach ($board::REPLY_STATUS_LIST as $key => $val) { ?>
                                            <option value="<?= $key ?>" <?php if ($req['replyStatus'] == $key) echo 'selected' ?>><?= $val ?></option>
                                        <?php } ?>
                                    </select>
                                </span>
                            </span>
                        </div>
                    </td>
                <?php } ?>
            </tr>
            <?php if ($bdList['cfg']['bdGoodsPtFl'] == 'y') { ?>
                <tr>
                    <th><div>평점</div></th>
                    <td>
                        <div>
                            <span class="ncua-select ncua-select--xs ncua-input-width-120">
                                <span class="ncua-select__content">
                                    <select name="goodsPt" class="ncua-select__tag">
                                        <option value="">=전체=</option>
                                        <?php
                                        for ($i = 1; $i < 6; $i++) { ?>
                                            <option
                                                    value="<?= $i ?>" <?php if ((string)$i == (string)$req['goodsPt']) echo 'selected' ?>><?= $i ?></option>
                                        <?php } ?>
                                    </select>
                                </span>
                            </span>
                        </div>
                    </td>
                </tr>
            <?php } ?>
            <?php if ($bdList['cfg']['bdEventFl'] == 'y') { ?>
                <tr class="js-if-bdEventFl">
                    <th><div>이벤트 기간</div></th>
                    <td>
                        <div>
                            <div id="datepicker-container02"></div>
                        </div>
                    </td>
                </tr>
            <?php } ?>
            <tr>
                <th><div>검색어</div></th>
                <td>
                    <div class="ncua-gap-4">
                        <span class="ncua-select ncua-select--xs ncua-input-width-120">
                            <span class="ncua-select__content">
                                <select id="searchField" class="ncua-select__tag" name="searchField">
                                    <option value="subject" <?php if ($req['searchField'] == 'subject') echo 'selected' ?>>
                                        제목
                                    </option>
                                    <option
                                        value="writerNick" <?php if ($req['searchField'] == 'writerNick') echo 'selected' ?>>닉네임
                                    </option>
                                    <option
                                        value="writerNm" <?php if ($req['searchField'] == 'writerNm') echo 'selected' ?>>이름
                                    </option>
                                    <option
                                        value="writerId" <?php if ($req['searchField'] == 'writerId') echo 'selected' ?>>아이디
                                    </option>
                                    <option
                                        value="contents" <?php if ($req['searchField'] == 'contents') echo 'selected' ?>>내용
                                    </option>
                                    <option
                                        value="subject_contents" <?php if ($req['searchField'] == 'subject_contents') echo 'selected' ?>>
                                        제목+내용
                                    </option>
                                    <option class="js-if-bdGoodsFl"
                                        value="goodsNm" <?php if ($req['searchField'] == 'goodsNm') echo 'selected' ?>>
                                        상품명
                                    </option>
                                    <option class="js-if-bdGoodsFl"
                                        value="goodsNo" <?php if ($req['searchField'] == 'goodsNo') echo 'selected' ?>>
                                        상품코드
                                    </option>
                                    <option class="js-if-bdGoodsFl"
                                        value="goodsCd" <?php if ($req['searchField'] == 'goodsCd') echo 'selected' ?>>
                                        자체상품코드
                                    </option>

                                </select>
                            </span>
                        </span>
                        <span class="ncua-select ncua-select--xs search-kind ncua-input-width-120">
                            <span class="ncua-select__content">
                                <?= gd_select_box('searchKind', 'searchKind', $searchKindASelectBox, null, gd_isset($req['searchKind']), null, null, 'ncua-select__tag'); ?>
                            </span>
                        </span>
                        <div class="ncua-input ncua-input--xs ncua-input-width-320">
                            <div class="ncua-input__content">
                                <div class="ncua-input__field ncua-input__field--xs"><input name="searchWord" type="text" value="<?= gd_isset($req['searchWord']) ?>" /></div>
                            </div>
                        </div>
                    </div>
                </td>
            </tr>
            <?php } ?>
        </table>
    </div>

    <div class="ncua-btn-group ncua-align-right">
        <button type="button" class="ncua-btn ncua-btn--xs has-underline ncua-btn--text js-btn-reset">초기화</button>
        <button type="submit" class="ncua-btn ncua-btn--xs ncua-btn--secondary">검색</button>
    </div>
</form>
