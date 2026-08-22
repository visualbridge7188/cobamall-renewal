<link rel="stylesheet" href="<?= gd_set_browser_cache(PATH_ADMIN_GD_SHARE . 'ncds/css/crm/popup-charge-message-points.css') ?>">

<article class="ncua-content popup-charge-message-points">   
    <header class="page-header ncua-page-header">
        <h3 class="ncua-help-manual">
            <?= end($naviMenu->location) ?>
        </h3>
    </header>

    <article class="ncua-content popup-charge-message-points__content">
        <section class="ncua-flex ncua-gap-8 ncua-flex-column">
            <header>
                <h4>메시지 포인트</h4>
            </header>
            <section>
                <div class="ncua-table ncua-table--vertical ncua-info-table">
                <table>
                        <colgroup>
                            <col width="144px">
                            <col >
                        </colgroup>
                    <tr>
                        <th><div>잔여 포인트</div></th>
                        <td>
                            <div>
                                <span id="remainingPoints" class="popup-charge-message-points__remaining-points"><?= number_format($smsPoint, 1) ?></span> &nbsp;포인트
                            </div>
                        </td>
                    </tr>
                </table>
            </div>
            </section>
        </section>
        <ul class="popup-charge-message-points__point-info">
            <li class="ncua-notice-info">SMS&nbsp;<span class="point-info-item-value"><?= number_format($availableCount['sms']) ?></span>&nbsp;건 | LMS&nbsp;<span class="point-info-item-value"><?= number_format($availableCount['lms']) ?></span>&nbsp;건</li>
            <li class="ncua-notice-info">카카오 알림톡:&nbsp;<span class="point-info-item-value"><?= number_format($availableCount['kakaoAlrimTalk']) ?></span>&nbsp;건</li>
            <li class="ncua-notice-info">카카오 친구톡:&nbsp;<span class="point-info-item-value"><?= number_format($availableCount['kakaoFriendTalk']) ?></span>&nbsp;건</li>
            <li class="ncua-notice-info">마이앱(앱푸시): 무료</li>
        </ul>

    <!-- 충전 상품 선택 섹션 -->
        <form name="frmSms" method="post" action="<?= $messagePointPayUrl ?>" onsubmit="return popupPay()">
            <input type="hidden" name="sno" value="<?= $shopSno ?>">
            <input type="hidden" name="mode" value="sms">
            <section class="ncua-flex ncua-gap-8 ncua-flex-column product-section">
                <header>
                    <h4 class="ncua-flex" data-tooltip-seq="001" data-tooltip-icon-type="fill">충전 상품 선택</h4>
                </header>
                <section>
                    <div class="ncua-search-result">
                        <div class="ncua-table ncua-table--horizontal">
                            <table class="popup-charge-message-points__product-table">
                                        <colgroup>
                                            <col width="79px">
                                            <col width="134px">
                                            <col width="116px">
                                            <col>
                                            <col>
                                            <col>
                                            <col>
                                        </colgroup>
                                <thead>
                                    <tr>
                                        <th><div>결제선택</div></th>
                                        <th><div>발송 건/포인트</div></th>
                                        <th><div>사용요금</div></th>
                                        <th><div>SMS<br/>(건당 1포인트)</div></th>
                                        <th><div>LMS<br/>(건당 <?= $lmsPoint ?>포인트)</div></th>
                                        <th><div>알림톡<br/>(건당 <?= $kakaoPoint ?>포인트)</div></th>
                                        <th><div>친구톡<br/>(텍스트 기준 건당 <?= $kakaoFriendTalkPoint ?>포인트)</div></th>
                                    </tr>
                                </thead>
                                <tbody>
                                <?php
                                    $idx = 0;
                                    if (empty($smsPriceList) === false):
                                        foreach ($smsPriceList as $key => $value):
                                ?>
                                    <tr>
                                        <td>
                                            <div>
                                                <label class="ncua-radio-field ncua-radio-field--xs ncua-flex">
                                                    <span class="ncua-radio-input ncua-radio-input--xs ncua-radio-field__input">
                                                        <input type="radio" name="idx" value="<?= $idx++ ?>" <?php if ($idx === 1) echo 'checked' ?>/>
                                                    </span>
                                                </label>
                                            </div>
                                        </td>
                                        <td><div><?= number_format($key) ?>포인트</div></td>
                                        <td><div><?= number_format($value['useFee']) ?>원</div></td>
                                        <td><div><?= rtrim(rtrim(number_format($value['unit'], 1), '0'), '.') ?>원/1건</div></td>
                                        <td><div><?= ($value['unit'] * $lmsPoint) ?>원/1건</div></td>
                                        <td><div><?= round($value['unit'] * $kakaoPoint, 1) ?>원/1건</div></td>
                                        <td><div><?= round($value['unit'] * $kakaoFriendTalkPoint, 1) ?>원/1건</div></td>
                                    </tr>
                                <?php
                                        endforeach;
                                    endif;
                                ?>
                                </tbody>
                            </table>
                        </div>
                        <div class="ncua-table-bottom"></div>
                    </div>
                </section>
            </section>
        </form>

    <!-- FAQ 아코디언 -->
        <details class="ncua-accordion ncua-accordion--gray" open>
            <summary>메시지 포인트가 무엇인가요?</summary>
            <ul class="ncua-accordion__content">
                <li class="ncua-notice-info">메시지 발송에는 메시지 포인트가 필요합니다. (앱푸시는 제외)</li>
                <li class="ncua-notice-info">SMS (90byte 이하) 1 포인트 / LMS (90~2000byte) <?= $lmsPoint ?>포인트</li>
                <li class="ncua-notice-info">카카오 알림톡 <?= $kakaoPoint ?> 포인트</li>
                <li class="ncua-notice-info">카카오 친구톡: 텍스트 형 <?= $kakaoFriendTalkPoint ?> 포인트 / 이미지형·와이드 이미지형 <?= $kakaoFriendTalkImagePoint ?> 포인트 / 와이드 아이템 리스트형·캐러셀형 <?= $kakaoFriendTalkWideItemPoint ?> 포인트</li>
            </ul>
        </details>
    </article>
    <!-- 메시지 포인트 섹션 -->

    <!-- 하단 버튼 -->
    <div class="ncua-content-footer">
        <button type="button" id="chargeMessagePointsHistory" class="ncua-btn ncua-btn--sm ncua-btn--secondary-gray">
            <span class="ncua-btn__label">포인트 충전 내역</span>
        </button>
        <button type="button" id="chargeMessagePointsSubmit" class="ncua-btn ncua-btn--sm ncua-btn--primary">
            <span class="ncua-btn__label">포인트 충전</span>
        </button>
    </div>
</article>

<script type="text/javascript">
    // ncds 경로 빠지면서 body에 ncds 틀어지는 현상 수정
    document?.querySelector('body')?.classList?.add('ncds');

    document.addEventListener('DOMContentLoaded', () => {
        const productTable = document.querySelector('.popup-charge-message-points__product-table tbody');
        const historyBtn = document.getElementById('chargeMessagePointsHistory');
        const submitBtn = document.getElementById('chargeMessagePointsSubmit');

        /**
         * 테이블 행 클릭 시 라디오 버튼 선택
         */
        productTable?.querySelectorAll('tr').forEach(row => {
            row.addEventListener('click', (e) => {
                if (e.target.type !== 'radio') {
                    const radio = row.querySelector('input[type="radio"]');
                    if (radio) radio.checked = true;
                }
            });
        });

        /**
         * 포인트 충전 내역 버튼
         */
        historyBtn?.addEventListener('click', () => {
            $.post('./message_config/layer_charge_message_points_history.php', null, (data) => {
                ncds_layer_popup({message: data, title: '메시지 포인트 충전 내역', size: 'wide-sm'});
            });
        });

        /**
         * 포인트 충전 버튼
         */
        submitBtn?.addEventListener('click', () => {
            const form = $("form[name=frmSms]");
            form.submit();
        });
    });

    function popupPay() {
        const selectedRadio = document.querySelector('input[name="idx"]:checked');
        if (!selectedRadio) {
            NCDSAlert({
                message: '충전 상품을 선택해주세요.',
                iconType: 'error'
            });
            return;
        }

        const form = $("form[name=frmSms]");
        var popup_window = window.open("", "popupPay", "width=500,height=450");
        form.attr("target", "popupPay");
        var timer = setInterval(function () {
            if (popup_window.closed) {
                clearInterval(timer);
                window.location.reload();
            }
        }, 500);
    }
</script>

<!-- 툴팁 스크립트 -->
<script type="text/javascript">
    const code = '251210002';
    if (window.GodoCosGuide) {
        window.GodoCosGuide.init({
            apiUrl: <?= json_encode($cosGuideApiUrl) ?>,
            guideCode: code,
        });
    }
</script>
