<div class="table-title gd-help-manual">
    전체 배송회차 로그 보기
</div>
<table class="table table-rows">
    <colgroup>
        <col width="50%">
        <col/>
    </colgroup>
    <thead>
    <tr>
        <th>배송회차</th>
        <th>주문번호</th>
    </tr>
    </thead>
    <tbody>
    <?php foreach ($deliveryLog as $log) : ?>
        <tr class="text-center">
            <td><?= $log['deliveryRound'] ?></td>
            <td>
                <?php if (empty($log['orderNo'])) : ?>
                    -
                <?php else : ?>
                    <a href="#;" onclick="javascript:open_order_link('<?=$log['orderNo']?>', 'newTab', '<?=$isProvider?>')" style="color: #117efa"><?=$log['orderNo']?></a>
                    <img src="/admin/gd_share/img/icon_grid_open.png" alt="팝업창열기" class="hand mgl5" border="0" onclick="javascript:order_view_popup('<?=$log['orderNo']?>', '<?=$isProvider?>');">
                <?php endif; ?>
            </td>
        </tr>
    <?php endforeach;?>
    </tbody>
</table>
<div class="text-center">
    <button type="button" class="btn btn-lg btn-black js-layer-close">닫기</button>
</div>
<script type="text/javascript" src="<?=PATH_ADMIN_GD_SHARE?>script/orderList.js?ts=<?=time();?>"></script>
