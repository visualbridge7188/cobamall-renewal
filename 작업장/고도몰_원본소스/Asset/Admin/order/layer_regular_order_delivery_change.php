<table class="table table-cols">
    <tbody>
    <tr>
        <th>
            <strong>배송주기</strong>
        </th>
        <td>
            <?php if ($deliveryCycleType === 'all') : ?>
            <div class="form-inline">
                <div>
                    <label class="radio-inline">
                        <input type="radio" name="cycleType" value="month" <?= $currentDeliveryInfo['cycleType'] === 'month' ? 'checked' : '' ?>>월단위
                    </label>
                    <label class="radio-inline">
                        <input type="radio" name="cycleType" value="week" <?= $currentDeliveryInfo['cycleType'] === 'week' ? 'checked' : '' ?>>주단위
                    </label>
                </div>
                <!-- 월/주 선택에서의 월단위: 개월 -->
                <div class="form-inline month_cycle_row" style="<?= $currentDeliveryInfo['cycleType'] === 'month' ? '' : 'display: none' ?>">
                    <div class="cycle_row month_cycle" style="padding-top: 10px">
                        <select name="deliveryMonth" id="deliveryMonth">
                            <?php foreach ($deliveryCycle['deliveryCycleMonth'] as $index => $deliveryMonth) : ?>
                                <?php $checked = $currentDeliveryInfo['cycleType'] === 'month' ? $deliveryMonth == $currentDeliveryInfo['cycle'] : $index == 0; ?>
                                <option value="<?=$deliveryMonth?>" <?= $checked ? 'selected' : '' ?>>
                                    <?=$deliveryMonth?>개월
                                </option>
                            <?php endforeach; ?>
                        </select>
                        <span>마다</span>
                        <select name="deliveryMonthDay" id="deliveryMonthDay">
                            <?php foreach ($deliveryCycle['deliveryCycleMonthDay'] as $index => $deliveryMonthDay) : ?>
                                <?php $checked = $currentDeliveryInfo['cycleType'] === 'month' ? $deliveryMonthDay == $currentDeliveryInfo['cycleDay'] : $index == 0; ?>
                                <option value="<?=$deliveryMonthDay?>" <?= $checked ? 'selected' : '' ?>>
                                    <?=$deliveryMonthDay?>일
                                </option>
                            <?php endforeach; ?>
                        </select>
                        <span>에 배송</span>
                    </div>
                </div>
                <!-- 월/주 선택에서의 주단위: 주 -->
                <div class="form-inline week_cycle_row" style="<?= $currentDeliveryInfo['cycleType'] === 'week' ? '' : 'display: none' ?>">
                    <div class="cycle_row week_cycle" style="padding-top: 10px">
                        <select name="deliveryWeek" id="deliveryWeek">
                            <?php foreach ($deliveryCycle['deliveryCycleWeek'] as $index => $deliveryWeek) : ?>
                                <?php $checked = $currentDeliveryInfo['cycleType'] === 'week' ? $deliveryWeek == $currentDeliveryInfo['cycle'] : $index == 0; ?>
                                <option value="<?=$deliveryWeek?>" <?= $checked ? 'selected' : '' ?>>
                                    <?=$deliveryWeek?>주
                                </option>
                            <?php endforeach; ?>
                        </select>
                        <span>마다</span>
                        <select name="deliveryWeekDay" id="deliveryWeekDay">
                            <?php foreach ($deliveryCycle['deliveryCycleWeekDay'] as $index => $deliveryWeekDay) : ?>
                                <?php
                                $weekdayNames = ['월요일', '화요일', '수요일', '목요일', '금요일'];
                                $weekdayText = $weekdayNames[$deliveryWeekDay - 1] ?? '';
                                $checked = $currentDeliveryInfo['cycleType'] === 'week' ? $deliveryWeekDay == $currentDeliveryInfo['cycleDay'] : $index == 0;
                                ?>
                                <option value="<?=$deliveryWeekDay?>" <?= $checked ? 'selected' : '' ?>>
                                    <?=$weekdayText?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                        <span>에 배송</span>
                    </div>
                </div>
            </div>
            <?php endif; ?>
            <!-- 월단위: 개월 -->
            <?php if ($deliveryCycleType === 'month') : ?>
                <div class="form-inline">
                    <div>
                        <label class="radio-inline">
                            <input type="radio" name="cycleType" value="month" checked > 월단위
                        </label>
                    </div>
                    <div class="form-inline month_cycle_row" style="padding-top: 10px">
                        <select name="deliveryMonth" id="deliveryMonth">
                            <?php foreach ($deliveryCycle['deliveryCycleMonth'] as $index => $deliveryMonth) : ?>
                                <option value="<?=$deliveryMonth?>" <?= $deliveryMonth == $currentDeliveryInfo['cycle'] ? 'selected' : '' ?>>
                                    <?=$deliveryMonth?>개월
                                </option>
                            <?php endforeach; ?>
                        </select>
                        <span>마다</span>
                        <select name="deliveryMonthDay" id="deliveryMonthDay">
                            <?php foreach ($deliveryCycle['deliveryCycleMonthDay'] as $index => $deliveryMonthDay) : ?>
                                <option value="<?=$deliveryMonthDay?>" <?= $deliveryMonthDay == $currentDeliveryInfo['cycleDay'] ? 'selected' : '' ?>>
                                    <?=$deliveryMonthDay?>일
                                </option>
                            <?php endforeach; ?>
                        </select>
                        <span>에 배송</span>
                    </div>
                </div>
            <?php endif;?>
            <!-- 주단위: 주 -->
            <?php if ($deliveryCycleType === 'week') : ?>
            <div class="form-inline">
                <div>
                    <label class="radio-inline">
                        <input type="radio" name="cycleType" value="week" checked > 주단위
                    </label>
                </div>
                <div class="form-inline week_cycle_row" style="padding-top: 10px">
                    <select name="deliveryWeek" id="deliveryWeek">
                        <?php foreach ($deliveryCycle['deliveryCycleWeek'] as $index => $deliveryWeek) : ?>
                            <option value="<?=$deliveryWeek?>" <?= $deliveryWeek == $currentDeliveryInfo['cycle'] ? 'selected' : '' ?>>
                                <?=$deliveryWeek?>주
                            </option>
                        <?php endforeach; ?>
                    </select>
                    <span>마다</span>
                    <select name="deliveryWeekDay" id="deliveryWeekDay">
                        <?php foreach ($deliveryCycle['deliveryCycleWeekDay'] as $index => $deliveryWeekDay) : ?>
                            <?php
                            $weekdayNames = ['월요일', '화요일', '수요일', '목요일', '금요일'];
                            $weekdayText = $weekdayNames[$deliveryWeekDay - 1] ?? '';
                            ?>
                            <option value="<?=$deliveryWeekDay?>" <?= $deliveryWeekDay == $currentDeliveryInfo['cycleDay'] ? 'selected' : '' ?>>
                                <?=$weekdayText?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                    <span>에 배송</span>
                </div>
            </div>
            <?php endif;?>
        </td>
    </tr>
    <?php if ($roundDisplayType === 'all' || $roundDisplayType === 'abled') : ?>
    <tr>
        <th>
            <strong>종료회차</strong>
        </th>
        <td>
            <?php if ($roundDisplayType === 'all') : ?>
                <div class="form-inline delivery_round_abled">
                    <div class="form-inline">
                        <select name="deliveryRoundCount" id="deliveryRoundCount">
                            <option value="0" <?= (0 === $currentDeliveryInfo['maxDeliveryRounds']) ? 'selected' : '' ?>>무제한</option>
                            <?php foreach ($maxDeliveryRounds as $index => $deliveryRound) : ?>
                                <option value="<?=$deliveryRound?>" <?= $deliveryRound == $currentDeliveryInfo['maxDeliveryRounds'] ? 'selected' : '' ?>><?=$deliveryRound?>회</option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>
            <?php endif; ?>
            <?php if ($roundDisplayType === 'abled') : ?>
                <div class="form-inline">
                    <select name="deliveryRoundCount" id="deliveryRoundCount">
                        <?php foreach ($maxDeliveryRounds as $index => $deliveryRound) : ?>
                            <option value="<?=$deliveryRound?>" <?= $deliveryRound == $currentDeliveryInfo['maxDeliveryRounds'] ? 'selected' : '' ?>><?=$deliveryRound?>회</option>
                        <?php endforeach; ?>
                    </select>
                </div>
            <?php endif; ?>
        </td>
    </tr>
    <?php endif; ?>
    </tbody>
</table>
<div class="text-center">
    <button type="button" class="btn btn-lg btn-white js-layer-close">취소</button>
    <button type="submit" class="btn btn-lg btn-black js-regular-delivery-change">저장</button>
</div>
<script>
    $(document).ready(function() {
        // 배송주기 라디오 버튼
        $('input[name="cycleType"]').on('change', function() {
            if ($(this).val() === 'month') {
                $('.month_cycle_row').show();
                $('.week_cycle_row').hide();
            } else if ($(this).val() === 'week') {
                $('.week_cycle_row').show();
                $('.month_cycle_row').hide();
            }
        });

        // 종료회차 라디오버튼
        $('input[name="deliveryRound"]').on('change', function() {
            if ($(this).val() === 'abled') {
                $('.delivery_round_abled').show();
            } else if ($(this).val() === 'disabled') {
                $('.delivery_round_abled').hide();
            }
        });

        // 저장 클릭
        $('.js-regular-delivery-change').bind('click', function (e) {
            if (validateMonthCycle()) {

                const postData = {
                    mode: 'change_delivery_cycle',
                    deliveryCycleType: $('input[name="cycleType"]:checked').val(),
                    deliveryRound: $('input[name="deliveryRound"]:checked').val(),
                    applyNo: <?=$applyNo?>
                };

                // 배송주기
                if (postData.deliveryCycleType === 'month') {
                    postData.deliveryCycle = $('#deliveryMonth').val();
                    postData.deliveryCycleDay = $('#deliveryMonthDay').val();
                } else if (postData.deliveryCycleType === 'week') {
                    postData.deliveryCycle = $('#deliveryWeek').val();
                    postData.deliveryCycleDay = $('#deliveryWeekDay').val();
                }

                // 종료회차
                if (postData.deliveryRound !== 'disabled') {
                    postData.maxDeliveryRound = $('#deliveryRoundCount').val();
                }

                $.post('../order/layer_regular_order_ps.php', postData, function (data) {
                    if (data.result === 'success') {
                        dialog_alert(data.message, '배송주기 변경', setTimeout(function(){
                            location.reload(true);
                        }, 1000));
                    } else {
                        dialog_alert(data.message)
                    }
                });
            }
        });

        function validateMonthCycle() {
            const selectedCycle = $('input[name="cycleType"]:checked').val();
            const selectedDeliveryRoundCount = $('select[name="deliveryRoundCount"]').val();
            const selectedDeliveryRound = '<?= $roundDisplayType ?>';

            if (selectedCycle === 'month') {
                const month = $('#deliveryMonth').val();
                const monthDay = $('#deliveryMonthDay').val();
                if (!month || !monthDay) {
                    dialog_alert('월단위 배송 주기(개월/일)를 모두 선택해주세요.');
                    return false;
                }
            } else if (selectedCycle === 'week') {
                const week = $('#deliveryWeek').val();
                const weekDay = $('#deliveryWeekDay').val();
                if (!week || !weekDay) {
                    dialog_alert('주단위 배송 주기(주/요일)를 모두 선택해주세요.');
                    return false;
                }
            }

            if (selectedDeliveryRound !== 'disabled') {
                if (!selectedDeliveryRoundCount) {
                    dialog_alert('종료회차를 선택해주세요.');
                    return false;
                }
            }

            return true;
        }
    });
</script>
