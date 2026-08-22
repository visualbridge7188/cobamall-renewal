<section class="ncua-card ncua-card--no-border message-point">
    <header class="ncua-card__header">
        <h4 class="ncua-card__title">메시지 포인트</h4>
        <span class="message-point-text">
            <span class="point-value"><?= number_format($smsPoint, 1) ?></span>
            <span class="unit-text">포인트</span>
        </span>
        <button type="button" class="ncua-btn ncua-btn--xs ncua-btn--secondary" onclick="chargeMessagePoints();">포인트 충전</button>
    </header> 
    <section class="ncua-card__body">
        <p class="message-point-detail">
            <span>
                <span>SMS</span>
                <span class="message-point-count">
                    <span><?php echo number_format($sendableMethodCountByPoint['sms']) ?></span>
                    <span class="unit-text">건</span>
                </span>
            </span>
            <span>
                <span>LMS</span>
                <span class="message-point-count">
                    <span><?php echo number_format($sendableMethodCountByPoint['lms']) ?></span>
                    <span class="unit-text">건</span>
                </span>
            </span>
            <span>
                <span>카카오 알림톡</span>
                <span class="message-point-count">
                    <span><?php echo number_format($sendableMethodCountByPoint['kakaoAlrimTalk']) ?></span>
                    <span class="unit-text">건</span>
                </span>
            </span>
            <span>
                <span>카카오 친구톡</span>
                <span class="message-point-count">
                    <span><?php echo number_format($sendableMethodCountByPoint['kakaoFriendTalk']) ?></span>
                    <span class="unit-text">건</span>
                </span>
            </span>
            <span>
                <span>마이앱</span>
                <span class="message-point-count">
                    <span>무료</span>
                </span>
            </span>
        </p>
    </section>
</section>
