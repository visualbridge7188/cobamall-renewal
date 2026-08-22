<!-- 내역 -->
<div class="ncua-search-result">
    <div class="ncua-search-result__summary">
        <p class="ncua-search-result__summary-count">검색 <strong><?= $page->getTotal() ?></strong>개 / 전체 <strong class="text-danger"><?= $page->getAmount() ?></strong>개</p>
    </div>
    <div class="ncua-table ncua-table--horizontal">
        <table>
            <colgroup>
                <col width="100px">
                <col>
                <col>
            </colgroup>
            <thead>
            <tr>
                <th><div>번호</div></th>
                <th><div>수신거부 번호</div></th>
                <th><div>등록일</div></th>
            </tr>
            </thead>
            <tbody>
            <?php if (!empty($sms080RejectList)): ?>
                <?php foreach ($sms080RejectList as $sms080Reject): ?>
                    <tr>
                        <td><div><?= $page->idx-- ?></div></td>
                        <td><div><?= gd_number_to_cell_phone($sms080Reject['rejectCellPhone']) ?></div></td>
                        <td><div><?= strtotime($sms080Reject['rejectDt']) ? date('Y-m-d', strtotime($sms080Reject['rejectDt'])) : '-' ?></div></td>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr>
                    <td colspan="3">
                        <div class="no-data">
                            <span>검색 결과가 없습니다.</span>
                        </div>
                    </td>
                </tr>
            <?php endif; ?>
            </tbody>
        </table>
    </div>

    <!-- 페이지네이션 -->
    <div class="ncua-pagination"><?= $page->getPage('loadContentsByPage(\'PAGELINK\')') ?></div>
</div>

<script type="text/javascript">
    const smsRejectNumberkeyword = '<?= $keyword ?? '' ?>';
    function loadContentsByPage(page) {
        const pageNumber = parseInt(page.split('=')[1], 10);
        loadContents(pageNumber, smsRejectNumberkeyword);
    }
</script>
