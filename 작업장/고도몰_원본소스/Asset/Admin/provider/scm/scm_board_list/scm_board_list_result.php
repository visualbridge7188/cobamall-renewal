<div class="ncua-search-result">
    <div class="ncua-search-result__summary">
        <p class="ncua-search-result__summary-count">검색 <strong><?= number_format($data['searchCnt']); ?></strong>개 / 전체 <strong><?= number_format($data['totalCnt']); ?></strong>개</p>
    </div>

    <div class="ncua-search-result__content">
        <div class="ncua-search-result__actions">
            <div class="ncua-search-result__actions-button">
                <button type="button" class="ncua-btn ncua-btn--xs ncua-btn--destructive js-btn-delete">
                    선택 삭제
                </button>
            </div>
        </div>

        <form id="frmList" action="scm_board_ps.php" method="get" target="ifrmProcess">
            <input type="hidden" name="mode" value="delete">
            <div class="ncua-table ncua-table--horizontal">
                <table>
                    <colgroup>
                        <col width="56px"/>
                        <col width="60px"/>
                        <col width="160px"/>
                        <col width="104px"/>
                        <col/>
                        <col width="150px"/>
                        <col width="110px"/>
                        <col width="80px"/>
                        <col width="80px"/>
                    </colgroup>
                    <thead>
                        <tr>
                            <th>
                                <div>
                                    <label class="ncua-checkbox-field ncua-checkbox-field--xs">
                                        <span class="ncua-checkbox-input ncua-checkbox-input--xs ncua-checkbox-field__input">
                                            <input type="checkbox" class="js-checkall" data-target-name="sno"/>
                                        </span>
                                    </label>
                                </div>
                            </th>
                            <th><div>번호</div></th>
                            <th><div>공급사명</div></th>
                            <th><div>카테고리</div></th>
                            <th><div>제목</div></th>
                            <th><div>작성자</div></th>
                            <th><div>작성일</div></th>
                            <th><div>답변</div></th>
                            <th><div>수정</div></th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php
                    if (is_array(gd_isset($data['noticeList']))) {
                        foreach ($data['noticeList'] as $key => $val) {
                            ?>
                            <tr>
                                <td>
                                    <div>
                                        <label class="ncua-checkbox-field ncua-checkbox-field--xs">
                                            <span class="ncua-checkbox-input ncua-checkbox-input--xs ncua-checkbox-field__input">
                                                <input type="checkbox" name="sno[]" value="<?= $val['sno']; ?>" <?= $val['auth'] != 'y' ? 'disabled' : '' ?>/>
                                            </span>
                                        </label>
                                    </div>
                                </td>
                                <td>
                                    <div><?=$val['ncds']['iconNotice']; ?></div>
                                </td>
                                <td>
                                    <div><?= $val['companyNm'] ? $val['companyNm'] : '본사' ?></div>
                                </td>
                                <td>
                                    <div><?= $val['categoryText'] ?></div>
                                </td>
                                <td>
                                    <div class="scm-board-list-subject">
                                        <a href="scm_board_view.php?sno=<?= $val['sno'] ?>&<?= $queryString ?>">
                                            <?= $val['subject']; ?>
                                        </a>
                                    </div>
                                </td>
                                <td>
                                    <div><?= $val['managerId'] ?>(<?= $val['managerNm'] ?>)<?= $val['deleteText'] ?></div>
                                </td>
                                <td>
                                    <div><?= gd_date_format('Y-m-d', $val['regDt']); ?></div>
                                </td>
                                <td>
                                    <div>
                                        <?php if ($val['isNotice'] == 'n') { ?>
                                            <a href="scm_board_register.php?mode=reply&sno=<?= $val['sno'] ?>" class="ncua-btn ncua-btn--xxs ncua-btn--secondary-gray">답변</a>
                                        <?php } ?>
                                    </div>
                                </td>
                                <td>
                                    <div>
                                        <?php if ($val['auth'] == 'y') { ?>
                                            <a href="scm_board_register.php?sno=<?= $val['sno'] ?>&mode=modify" class="ncua-btn ncua-btn--xxs ncua-btn--secondary-gray">수정</a>
                                        <?php } ?>
                                    </div>
                                </td>
                            </tr>
                            <?php
                        }
                    }
                    ?>


                    <?php
                    if (gd_isset($data['list'])) {
                        foreach ($data['list'] as $key => $val) {
                            ?>
                            <tr>
                                <td>
                                    <div>
                                        <?php if ($val['auth'] == 'y') { ?>
                                            <label class="ncua-checkbox-field ncua-checkbox-field--xs">
                                                <span class="ncua-checkbox-input ncua-checkbox-input--xs ncua-checkbox-field__input">
                                                    <input type="checkbox" name="sno[]" value="<?= $val['sno']; ?>"/>
                                                </span>
                                            </label>
                                        <?php } ?>
                                    </div>
                                </td>
                                <td><div><?= number_format($val['listNo']); ?></div></td>
                                <td><div><?= $val['companyNm'] ? $val['companyNm'] : '본사' ?></div></td>
                                <td><div><?= $val['categoryText'] ?></div></td>
                                <td>
                                    <div class="scm-board-list-subject">
                                        <?= $val['ncds']['iconNotice'] ?>
                                        <?= $val['ncds']['iconReply'] ?>
                                        <a href="scm_board_view.php?sno=<?= $val['sno'] ?>&<?= $queryString ?>">
                                            <?= $val['subject']; ?>
                                        </a>
                                        <?= $val['ncds']['iconFile'] ?>
                                    </div>
                                </td>
                                <td><div><?= $val['managerId'] ?>(<?= $val['managerNm'] ?>)</div></td>
                                <td><div><?= gd_date_format('Y-m-d', $val['regDt']); ?></div></td>
                                <td>
                                    <div>
                                        <?php if ($val['isNotice'] == 'n') { ?>
                                            <a href="scm_board_register.php?mode=reply&sno=<?= $val['sno'] ?>" class="ncua-btn ncua-btn--xxs ncua-btn--secondary-gray">답변</a>
                                        <?php } ?>
                                    </div>
                                </td>
                                <td>
                                    <div>
                                        <?php if ($val['auth'] == 'y') { ?>
                                            <a href="scm_board_register.php?sno=<?= $val['sno'] ?>&mode=modify" class="ncua-btn ncua-btn--xxs ncua-btn--secondary-gray">수정</a>
                                        <?php } ?>
                                    </div>
                                </td>
                            </tr>
                            <?php
                        }
                    } else {
                        ?>
                        <tr>
                            <td colspan="9" height="50" class="no-data"><div>게시물이 없습니다.</div></td>
                        </tr>
                        <?php
                    }
                    ?>
                    </tbody>
                </table>
            </div>
        </form>
    </div>
    <div class="ncua-pagination"><?= $data['pagination'] ?></div>
</div>
