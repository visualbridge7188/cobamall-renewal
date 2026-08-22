<div class="ncua-search-result">
<!-- table -->
    <form id="frmList" action="template_ps.php" method="post">
        <input type="hidden" name="mode" value="delete"/>

        <!-- summary -->
        <div class="ncua-search-result__summary">
            <p class="ncua-search-result__summary-count">검색 <strong><?= number_format($data['totalCnt']) ?></strong>개 / 전체 <strong><?= number_format($data['amountCnt']) ?></strong>개</p> 
        </div>
        <!-- // summary -->
        <div class="ncua-search-result__content">
            <!-- 검색 결과 액션 -->
            <div class="ncua-search-result__actions">
                <button class="ncua-btn ncua-btn--xs ncua-btn--destructive ncua-select-delete">
                    선택 삭제
                </button>
                <div class="ncua-search-result__actions-select">
                    <span class="ncua-select ncua-select--xs">
                        <span class="ncua-select__content">
                            <?= gd_select_box('sort', 'sort', $data['sort'], null, $req['sort'], null, null, 'ncua-select__tag'); ?>
                        </span>
                    </span>
                    <span class="ncua-select ncua-select--xs">
                        <span class="ncua-select__content">
                            <?= gd_select_box_by_page_view_count(Request::get()->get('pageNum', 10), null, null, 'ncua-select__tag'); ?>
                        </span>
                    </span>
                </div> 
            </div>
            <!-- // 검색 결과 액션 -->
            <!-- table -->
            <div class="ncua-table ncua-table--horizontal">
                <table>
                    <colgroup>
                        <col width="56px">
                        <col width="80px">
                        <col width="140px">
                        <col width="*">
                        <col width="124px">
                        <col width="120px">
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
                            <th><div>분류</div></th>
                            <th><div>제목</div></th>
                            <th><div>등록일 / 수정일</div></th>
                            <th><div>수정</div></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        if (gd_array_is_empty($data['data']) === false) {
                            foreach ($data['data'] as $row) {
                                ?>
                                <tr>
                                    <td>
                                        <div>
                                            <label class="ncua-checkbox-field ncua-checkbox-field--xs has-text">
                                                <span class="ncua-checkbox-input ncua-checkbox-input--xs ncua-checkbox-field__input">
                                                    <input type="checkbox" name="sno[]" value="<?= $row['sno'] ?>"/>
                                                </span>
                                            </label> 
                                        </div>
                                    <td><div><?= number_format($pager->idx--) ?></div></td>
                                    <td><div><?= $row['templateTypeText'] ?></div></td>
                                    <td>
                                        <div class="board-template-list-title">
                                            <a class="js-btn-modify hand" data-sno="<?= $row['sno'] ?>"><?= $row['subject'] ?></a>
                                        </div>
                                    </td>
                                    <td><div><?=$row['regDtDate']?><br><?=$row['modDtDate']?></div></td>
                                    <td><div><button type="button" class="js-btn-modify ncua-btn ncua-btn--xxs ncua-btn--secondary-gray" data-sno="<?= $row['sno'] ?>">수정</button></div></td>
                                </tr>
                                <?php
                            }
                        } else {
                            ?>
                            <tr>
                                <td colspan="6" class="no-data"><div>템플릿이 없습니다.</div></td>
                            </tr>
                            <?php
                        }
                        ?>
                    </tbody>
                </table>
            </div>
            <!-- // table -->
            <div class="ncua-table-bottom"></div>
        </div>
    </form>
</div>
