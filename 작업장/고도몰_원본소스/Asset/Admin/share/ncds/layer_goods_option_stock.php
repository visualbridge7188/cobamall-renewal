<style>
.bootstrap-dialog.ncds-modal:has(#layerShowGoodsOptionStock) .modal-dialog {
    top: 50% !important;
    transform: translateY(-50%) !important;
}
</style>
<div class="modal-dialog__content">
    <div class="ncua-table ncua-table--horizontal ncua-table--rounded">
    <table>
        <colgroup>
            <col width="60px">
            <?php
            foreach($optionName as $v){
                ?><col width="*"><?php
            };
            ?>
            <col width="80px">
            <col width="80px">
            <col width="80px">
            <col width="80px">
            <col width="120px">
        </colgroup>
        <thead>
        <tr>
            <th><div>번호</div></th>
            <?php
            foreach($optionName as $v){
                ?><th><div><?=gd_htmlspecialchars($v)?></div></th><?php
            };
            ?>
            <th><div>옵션가</div></th>
            <th><div>재고량</div></th>
            <th><div>노출상태</div></th>
            <th><div>품절상태</div></th>
            <th><div>자체옵션코드</div></th>
        </tr>
        </thead>
        <tbody>
<?php
    if (gd_isset($data) && is_array($data)) {
        foreach ($data as $key => $val) {

            $optionDisplay = '노출함';
            if ($val['optionViewFl'] != 'y') $optionDisplay = '노출안함';
            if($val['optionSellFl'] == 't'){
                $optionSell = $stockReason[$val['optionSellCode']];
            }else{
                $optionSell = $stockReason[$val['optionSellFl']];
            }
?>
        <tr>
            <td><div><?php echo number_format($index + 1);?></div></td>
            <?php
            for($i=1;$i<=5;$i++){
                if(empty($val['optionValue'.$i])) continue;
                $tmpOption[] = $val['optionValue'.$i];
            }
            foreach($tmpOption as $v){
                ?><td><div><?=gd_htmlspecialchars($v)?></div></td><?php
            }
            unset ($tmpOption);
            ?>
            <td><div><?php echo number_format($val['optionPrice']);?> 원</div></td>
            <td><div><?php echo number_format($val['stockCnt']);?></div></td>
            <td><div><?=$optionDisplay?></div></td>
            <td><div><?=$optionSell?></div></td>
            <td><div><?php echo gd_htmlspecialchars($val['optionCode']); ?></div></td>
        </tr>
<?php
            $index++;
        }
    } else {
?>
        <tr>
            <td colspan="8"><div>검색을 이용해 주세요.</div></td>
        </tr>
<?php
    }
?>
        </tbody>
    </table>
    </div>
</div>

