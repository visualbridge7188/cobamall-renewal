<form name="frmSearch" id="frmSearch" action="template_list.php" class="frmSearch js-form-enter-submit">
    <div class="ncua-table ncua-table--vertical">
        <table>
            <tbody>
                <tr>
                    <th><div>검색어</div></th>
                    <td>
                        <div class="ncua-search-keyword">
                            <span class="ncua-select ncua-select--xs">
                                <span class="ncua-select__content">
                                    <select class="ncua-select__tag" name="searchField" id="searchField">
                                        <option value="all" <?= $req['searchField'] === 'all' ? 'selected' : '' ?>>
                                            =통합검색=
                                        </option>
                                        <option value="subject" <?= $req['searchField'] === 'subject' ? 'selected' : '' ?>>
                                            제목
                                        </option>
                                        <option value="contents" <?= $req['searchField'] === 'contents' ? 'selected' : '' ?>>내용
                                        </option>
                                    </select>
                                </span>
                            </span>
                            <div class="ncua-input ncua-input--xs">
                                <div class="ncua-input__content">
                                    <div class="ncua-input__field ncua-input__field--xs ncua-input-width-320">
                                        <input name="searchWord" value="<?= gd_isset($req['searchWord']) ?>" type="text" />
                                    </div>
                                </div>
                            </div> 
                        </div>
                    </td>
                </tr>
                <tr>
                    <th><div>분류</div></th>
                    <td><div class="ncua-flex-gap">
                        <label class="ncua-radio-field ncua-radio-field--xs has-text">
                            <span class="ncua-radio-input ncua-radio-input--xs ncua-radio-field__input">
                                <input type="radio" name="templateType" value="" <?php if ($req['templateType'] == '') echo 'checked' ?>>
                            </span>
                            <span class="ncua-radio-field__text">전체</span>
                        </label>
                        <label class="ncua-radio-field ncua-radio-field--xs has-text">
                            <span class="ncua-radio-input ncua-radio-input--xs ncua-radio-field__input">
                                <input type="radio" name="templateType" value="front" <?php if ($req['templateType'] == 'front') echo 'checked' ?>>
                            </span>
                            <span class="ncua-radio-field__text">쇼핑몰 게시글 양식</span>
                        </label>
                        <label class="ncua-radio-field ncua-radio-field--xs has-text">
                            <span class="ncua-radio-input ncua-radio-input--xs ncua-radio-field__input">
                                <input type="radio" name="templateType" value="admin" <?php if ($req['templateType'] == 'admin') echo 'checked' ?>>
                            </span>
                            <span class="ncua-radio-field__text">관리자 게시글 양식</span>
                        </label>
                    <label class="radio-inline">
                    </label>
                    <label class="radio-inline">
                    </label>
                    </div></td>
                </tr>
            </tbody>
        </table>
    </div>
    <p class="ncua-btn-group ncua-align-right">
        <button type="reset" class="ncua-btn ncua-btn--xs has-underline ncua-btn--text">초기화</button>
        <input type="submit" value="검색" class="ncua-btn ncua-btn--xs ncua-btn--secondary">
    </p>
</form>
