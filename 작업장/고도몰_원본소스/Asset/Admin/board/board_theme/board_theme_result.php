<div class="ncua-search-result">
    <form action="board_theme_ps.php" id="frmList" name="frmList" method="post" action="board_theme_ps.php">
        <input type="hidden" name="mode" value="theme_delete"/>
            <!-- summary -->
            <div class="ncua-search-result__summary">
                <p class="ncua-search-result__summary-count">
                    검색
                    <strong><?= number_format($page->recode['total']); ?></strong>개 /
                    전체 <strong><?= number_format($page->recode['amount']); ?></strong>건
                </p>
            </div>
            <!-- // summary -->
            <div class="ncua-search-result__content">
                <!-- actions -->
                <div class="ncua-search-result__actions">
                    <div>
                        <button type="submit" class="ncua-btn ncua-btn--xs ncua-btn--destructive ncua-select-delete">
                            선택 삭제
                        </button>
                    </div>
                </div>
                <!-- // actions -->

                <!-- table -->
                <div class="ncua-table ncua-table--horizontal board-theme-table">
                    <table>
                        <colgroup>
                            <col width="56px" />
                            <col width="80px" />
                            <col width="120px" />
                            <col width="" />
                            <col width="80px" />
                            <col width="" />
                            <col width="80px" />
                            <col width="100px" />
                            <col width="80px" />
                            <col width="120px" />
                            <col width="80px" />
                            <col width="80px" />
                        </colgroup>
                        <thead>
                            <tr>
                                <th>
                                    <div>
                                        <label class="ncua-checkbox-field ncua-checkbox-field--xs has-text">
                                            <span class="ncua-checkbox-input ncua-checkbox-input--xs ncua-checkbox-field__input">
                                                <input type="checkbox" class="js-checkall" data-target-name="sno">
                                            </span>
                                        </label>
                                    </div>
                                </th>
                                <th><div>번호</div></th>
                                <th><div>구분</div></th>
                                <th><div>적용 디자인스킨</div></th>
                                <th><div>스킨코드</div></th>
                                <th><div>스킨명</div></th>
                                <th><div>적용개수</div></th>
                                <th><div>정렬</div></th>
                                <th><div>넓이</div></th>
                                <th><div>등록일</div></th>
                                <th><div>수정</div></th>
                                <th><div>삭제</div></th>
                            </tr>
                        </thead>
                        <tbody>
                        <?php
                        if (is_array(gd_isset($data))) {
                            foreach ($data as $key => $val) {
                                ?>
                                <tr>
                                    <td>
                                        <div>
                                            <label class="ncua-checkbox-field ncua-checkbox-field--xs has-text">
                                                <span class="ncua-checkbox-input ncua-checkbox-input--xs ncua-checkbox-field__input">
                                                    <input name="sno[]" type="checkbox" value="<?= $val['sno'] ?>" <?php if ($val['bdBasicFl'] == 'y') echo 'disabled' ?>>
                                                </span>
                                            </label>
                                        </div>
                                    </td>
                                    <td><div><?= number_format($page->idx--); ?></div></td>
                                    <td><div><?=$val['deviceTypeText']?></div></div></td>
                                    <td><div><?=$val['liveSkin']?></div></td>
                                    <td>
                                        <div>
                                            <a href="./board_theme_register.php?sno=<?= $val['sno']; ?>"><?= $val['themeId']; ?></a>
                                        </div>
                                    </td>
                                    <td>
                                        <div>
                                            <a href="./board_theme_register.php?sno=<?= $val['sno']; ?>"><?= $val['themeNm']; ?></a>
                                        </div>
                                    </td>
                                    <td>
                                        <div><span class="font-num"><?= number_format($val['applyThemeCount']); ?></span> 개</div>
                                    </td>
                                    <td><div><?= $val['bdAlignText']; ?></div></td>
                                    <td><div><?= $val['bdWidthText']?></div></td>
                                    <td><div><?= gd_date_format('Y-m-d', $val['regDt']); ?></div></td>
                                    <td>
                                        <div>
                                            <a class="ncua-btn ncua-btn--xxs ncua-btn--secondary-gray"
                                            href="./board_theme_register.php?sno=<?= $val['sno']; ?>">수정</a>
                                        </div>
                                    </td>
                                    <td>
                                        <div>
                                            <?php if ($val['bdBasicFl'] == 'n') { ?>
                                            <button type="button" class="ncua-btn ncua-btn--xxs ncua-btn--secondary-gray js-row-delete">삭제</button><?php } ?>
                                        </div>
                                    </td>
                                </tr>
                                <?php }
                        } else { ?>
                            <tr>
                                <td class="no-data" colspan="12">검색된 정보가 없습니다.</td>
                            </tr>
                        <?php } ?>
                        </tbody>
                    </table>
                </div>
                <!-- // table -->
                <div class="ncua-table-bottom"></div>
            </div>
    </form>
</div>
<div class="ncua-pagination"><?= $page->getPage(); ?></div>
