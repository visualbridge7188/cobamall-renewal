<div class="table-title gd-help-manual">
    전체 이용상태 로그 보기
</div>
<table class="table table-rows">
    <thead>
    <tr>
        <th>일자</th>
        <th>처리자</th>
        <th>이용상태</th>
        <th>상세사유</th>
    </tr>
    </thead>
    <tbody>
    <?php foreach ($statusLog as $log) : ?>
        <tr class="text-center">
            <td><?= gd_date_format('Y-m-d H:i', $log['regDt']) ?></td>
            <td>
                <?php if (empty($log['managerId'])) : ?>
                    <div class="text-muted">
                        <?=$log['modifierIP']?>
                    </div>
                <?php else: ?>
                    <?=$log['managerId'];?><br>
                    <div class="text-muted">
                        <?=$log['modifierIP']?>
                    </div>
                <?php endif; ?>
            </td>
            <td><?= $log['status'] ?></td>
            <td><?= $log['description'] ?></td>
        </tr>
    <?php endforeach;?>
    </tbody>
</table>
<div class="text-center">
    <button type="button" class="btn btn-lg btn-black js-layer-close">닫기</button>
</div>

