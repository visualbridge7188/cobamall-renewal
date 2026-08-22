<link type="text/css" href="<?= gd_set_browser_cache(PATH_ADMIN_GD_SHARE . 'ncds/css/crm/mobile-history.css') ?>" rel="stylesheet"/>
<article class="ncua-content mobile-msg">
    <div class="page-header js-affix">
        <h3><?= end($naviMenu->location) ?></h3>
    </div>

    <section class="ncua-card send-history">
        <header class="ncua-card__header">
            <span class="ncua-card__title">발송 내역 요약<span class="ncua-flex" data-tooltip-icon-type="fill" data-tooltip-seq="001"></span></span><span class="mobile-history-desc">최근 7일</span>
            <a class="ncua-btn ncua-btn--xs ncua-btn--secondary-gray" href="./mobile_history_list.php">전체 보기</a>
        </header>
        <section class="ncua-card__body">
            <div class="ncua-metrics">
                <p class="ncua-metrics__label">총 발송건 수</p>
                <b class="ncua-metrics__number"><?= number_format($messageCountLast7Days + $friendtalkCountLast7Days + $alimtalkCountLast7Days + $myappPushCountLast7Days) ?></b>
            </div>
            <div class="ncua-metrics">
                <p class="ncua-metrics__label">SMS/LMS</p>
                <b class="ncua-metrics__number"><?= number_format($messageCountLast7Days) ?></b>
            </div>
            <div class="ncua-metrics">
                <p class="ncua-metrics__label">카카오 친구톡</p>
                <b class="ncua-metrics__number"><?= number_format($friendtalkCountLast7Days) ?></b>
            </div>
            <div class="ncua-metrics">
                <p class="ncua-metrics__label">카카오 알림톡</p>
                <b class="ncua-metrics__number"><?= number_format($alimtalkCountLast7Days) ?></b>
            </div>
            <div class="ncua-metrics">
                <p class="ncua-metrics__label">앱푸시</p>
                <b class="ncua-metrics__number"><?= number_format($myappPushCountLast7Days) ?></b>
            </div>
        </section>
    </section>

    <section class="ncua-card msg-point-summary">
        <header class="ncua-card__header">
            메시지 포인트 사용 요약
            <button class="ncua-btn ncua-btn--xs ncua-btn--secondary-gray js-popup-point" type="button">포인트 충전</button>
        </header>
        <section class="ncua-card__body">
            <div class="ncua-metrics">
                <p class="ncua-metrics__label">최근 7일 사용량</p>
                <b class="ncua-metrics__number"><?=number_format($pointUsageLast7Days, 1); ?><span class="ncua-unit">p</span></b>
            </div>
            <div class="ncua-metrics msg-point-summary-average">
                <p class="ncua-metrics__label">평균 주간 사용량<span class="mobile-history-desc">최근 28일</span></p>
                <b class="ncua-metrics__number"><?=number_format($pointUsageLast28Days / 4, 1); ?><span class="ncua-unit">p</span></b>
            </div>
            <div class="ncua-metrics">
                <div>
                    <p class="ncua-metrics__label">잔여 포인트</p>
                    <b class="ncua-metrics__number"><?=number_format($smsPoint, 1); ?><span class="ncua-unit">p</span></b>
                </div>

                <dl class="msg-each-point-remaining">
                    <div>
                        <dt>sms</dt>
                        <dd><?=number_format($availableCount['sms']); ?><span class="ncua-unit">건</span></dd>
                    </div>
                    <div>
                        <dt>lms</dt>
                        <dd><?=number_format($availableCount['lms']); ?><span class="ncua-unit">건</span></dd>
                    </div>
                    <div>
                        <dt>카카오톡 알림톡</dt>
                        <dd><?=number_format($availableCount['kakaoAlrimTalk']); ?><span class="ncua-unit">건</span></dd>
                    </div>
                    <div>
                        <dt>카카오 친구톡</dt>
                        <dd><?=number_format($availableCount['kakaoFriendTalk']); ?><span class="ncua-unit">건</span></dd>
                    </div>
                </dl>
            </div>
        </section>
    </section>

    <section class="ncua-card shipping-schedule">
        <header class="ncua-card__header">
            발송 예정 현황
        </header>
        <section class="ncua-card__body">
            <div class="ncua-metrics">
                <p class="ncua-metrics__label">대기 중인 예약 발송</p>
                <b class="ncua-metrics__number"><?= number_format($crmNotificationCampaignCounts['SCHEDULED']['WAITING'] ?? 0) ?><span class="ncua-unit">건</span></b>
                <a class="ncua-btn ncua-btn--sm ncua-btn--text ncua-metrics__link" href="<?= $scheduledSendWaitingUrl ?>">상세보기</a>
            </div>
            <div class="ncua-metrics">
                <p class="ncua-metrics__label">발송 중인 반복 발송</p>
                <b class="ncua-metrics__number"><?= number_format(($crmNotificationCampaignCounts['REPEAT']['WAITING'] ?? 0) + ($crmNotificationCampaignCounts['REPEAT']['PROCESSING'] ?? 0)) ?><span class="ncua-unit">건</span></b>
                <a class="ncua-btn ncua-btn--sm ncua-btn--text ncua-metrics__link" href="<?= $scheduledSendRepeatUrl ?>">상세보기</a>
            </div>
            <div class="ncua-metrics">
                <p class="ncua-metrics__label">활성화된 자동 알림 발송</p>
                <b class="ncua-metrics__number"><?= $autoSendCounts['active'] ?><span class="ncua-unit">건</span></b>
                <a class="ncua-btn ncua-btn--sm ncua-btn--text ncua-metrics__link" href="<?= $autoSendUrl ?>">상세보기</a>
            </div>
        </section>
    </section>
</article>

<script>
    $(document).ready(function () {
        document.querySelector('.js-popup-point').addEventListener('click', () => {
            show_popup_message_config({
                url: './popup_charge_message_points.php',
                width: 1000,
                height: 800,
                onClose: function () {
                    window.location.reload();
                }
            });
        });
    });

    window.show_popup_message_config = ({url, width, height, onClose}) => {
        win = popup({
            url: url,
            target: '',
            width: width,
            height: height,
            scrollbars: 'yes',
            resizable: 'yes'
        });
        win?.focus();

        if (onClose && win) {
            const timer = setInterval(() => {
                if (win.closed) {
                    clearInterval(timer);
                    onClose();
                }
            }, 500);
        }

        return win;
    };
</script>
<script>
    const code = '251212001';
    if (window.GodoCosGuide) {
        window.GodoCosGuide.init({
            apiUrl: <?= json_encode($cosGuideApiUrl) ?>,
            guideCode: code,
             });
    }
</script>
