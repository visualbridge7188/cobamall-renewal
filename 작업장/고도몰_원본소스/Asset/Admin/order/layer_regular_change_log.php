<div class="table-title gd-help-manual">
    전체 변경이력 로그 보기
</div>
<table class="table table-rows">
    <thead>
    <tr>
        <th>일자</th>
        <th>처리자</th>
        <th>변경영역</th>
        <th>변경내용</th>
    </tr>
    </thead>
    <tbody>
    <?php foreach ($changeLog as $log) : ?>
        <?php if ($isProvider && $log['actionType'] === '결제카드 변경') : ?>
            <?php continue; ?>
        <?php endif; ?>
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
            <td><?= $log['actionType'] ?></td>
            <td><?= $log['actionDesc'] ?></td>
    <?php endforeach;?>
    </tbody>
</table>
<div class="text-center">
    <button type="button" class="btn btn-lg btn-black js-layer-close">닫기</button>
</div>

