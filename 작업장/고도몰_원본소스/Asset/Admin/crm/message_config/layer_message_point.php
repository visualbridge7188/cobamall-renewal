<?php if ($existsChargeHistory): ?>
    <!-- 한번이상 포인트 충전한 상태일 때 -->
    <div class="ncua-flex ncua-flex-gap ncua-align-items-center">
        <div class="ncua-flex ncua-gap-8 ncua-flex-column">
            <span class="message-config-point-display"><strong id="remainingPoints" class="message-config-point__red_strong"><?= number_format($smsPoint, 1) ?></strong>&nbsp;포인트</span>
            <div class="message-config-noti-info">발송 가능 건수: SMS <?= number_format($availableCount['sms']) ?>건, LMS <?= number_format($availableCount['lms']) ?>건, 카카오 알림톡 <?= number_format($availableCount['kakaoAlrimTalk']) ?>건, 카카오 친구톡 <?= number_format($availableCount['kakaoFriendTalk']) ?>건, 마이앱 무료</div>
            <div class="ncua-flex ncua-gap-4">
                <button type="button" id="chargeMessagePointsHistory" class="ncua-btn ncua-btn--xs ncua-btn--secondary-gray">
                    <span class="ncua-btn__label">충전 내역</span>
                </button>
                <button type="button" id="chargeMessagePoints" class="ncua-btn ncua-btn--xs ncua-btn--secondary">
                    <span class="ncua-btn__label">포인트 충전</span>
                </button>
            </div>
        </div>
    </div>
<?php else: ?>
    <!-- 한번도 포인트 충전하지 않은 상태일 때 -->
    <div class="ncua-flex ncua-flex-gap ncua-align-items-center">
        <div class="ncua-flex ncua-gap-8 ncua-align-items-center">
            <span>메시지 발송을 위해 포인트를 충전하세요.</span>
            <button type="button" id="chargeMessagePoints" class="ncua-btn ncua-btn--xs ncua-btn--secondary-gray">
                <span class="ncua-btn__label">포인트 충전</span>
            </button>
        </div>
    </div>
<?php endif; ?>

<script type="text/javascript">
    $(document).ready(function () {
        // 메시지 포인트 충전 내역
        $('#chargeMessagePointsHistory').click(function () {
            $.post('./message_config/layer_charge_message_points_history.php', null, (data) => {
                ncds_layer_popup({message: data, title: '메시지 포인트 충전 내역', size: 'wide-sm'});
            });
        });

        // 메시지 포인트 충전
        $('#chargeMessagePoints').click(function () {
            show_popup_message_config({
                url: './popup_charge_message_points.php',
                width: 1000,
                height: 800,
                onClose: function () {
                    loadMessagePointLayer();
                }
            });
        });
    });
</script>
