<div>
    <table class="table-cols no-title-line" style="width: 100%;">
        <colgroup>
            <col class="width-sm"/>
            <col/>
        </colgroup>
        <tr>
            <th>
                배송주기
            </th>
            <td>
                <?php if ($deliveryCycleType === 'month') : ?>
                    월 단위
                <?php elseif ($deliveryCycleType === 'week') : ?>
                    주 단위
                <?php endif; ?>
            </td>
        </tr>
        <tr>
            <th>
                상세주기
            </th>
            <td>
                <?php if ($deliveryCycleType === 'month') : ?>
                    <?= $monthCycle ?>
                <?php elseif ($deliveryCycleType === 'week') : ?>
                    <?= $weekCycle ?>
                    <br><?= $weekDayCycle ?>
                <?php endif; ?>
            </td>
        </tr>
    </table>
    <br>
    <div class="text-center"><input type="button" value="확인" class="btn btn-sm btn-white js-layer-close"/></div>
</div>
