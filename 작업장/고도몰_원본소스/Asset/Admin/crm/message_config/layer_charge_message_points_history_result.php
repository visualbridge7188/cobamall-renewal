<!-- 내역 -->
<div class="ncua-search-result">
    <div class="ncua-table ncua-table--horizontal">
        <table>
            <colgroup>
                <col width="68px">
                <col >
                <col width="150px">
                <col width="150px">
                <col >
            </colgroup>
            <thead>
                <tr>
                    <th><div>번호</div></th>
                    <th><div>내용</div></th>
                    <th><div>결제방법</div></th>
                    <th><div>결제가격</div></th>
                    <th><div>결제일</div></th>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($smsPointChargeList)): ?>
                    <?php foreach ($smsPointChargeList as $index => $smsPointChargeInfo): ?>
                        <tr>
                            <td><div><?= $page->idx-- ?></div></td>
                            <td><div><?= htmlspecialchars($smsPointChargeInfo['description'] ?? '') ?></div></td>
                            <td><div><?= htmlspecialchars($smsPointChargeInfo['payTypeName'] ?? '') ?></div></td>
                            <td><div><?= number_format($smsPointChargeInfo['amount'] ?? 0) ?></div></td>
                            <td><div><?= $smsPointChargeInfo['paidAt'] ?? '' ?></div></td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="5"><div class="empty-charge-history">충전 내역이 없습니다.</div></td>
                    </tr>
                <?php endif; ?>

            </tbody>
        </table>
    </div>
</div>

<!-- 페이지네이션 -->
<?php if (!empty($smsPointChargeList)): ?>
<div class="ncua-pagination"><?= $page->getPage('loadContentsByPage(\'PAGELINK\')') ?>
<?php endif; ?>

<script type="text/javascript">
    function loadContentsByPage(page) {
        const pageNumber = parseInt(page.split('=')[1], 10);
        loadContents(pageNumber);
    }
</script>
