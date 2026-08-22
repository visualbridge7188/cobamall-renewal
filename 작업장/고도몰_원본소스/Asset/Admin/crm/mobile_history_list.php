<link type="text/css" href="<?= gd_set_browser_cache(PATH_ADMIN_GD_SHARE . 'ncds/css/crm/mobile-history-list.css') ?>" rel="stylesheet"/>
<script type="text/javascript" src="<?= gd_set_browser_cache(PATH_ADMIN_GD_SHARE . 'ncds/script/ncds-datepicker-factory/ncds-datepicker-factory.js') ?>"></script>
<article class="ncua-content mobile-history-list js-mobile-history-list">
    <div class="page-header js-affix">
        <h3 class="ncua-help-manual">
        <button type="button" class="ncua-btn ncua-btn--md only-icon ncua-btn--secondary-gray ncua-history-back js-btn-back" onclick="history.back()">뒤로가기</button>
        <?= end($naviMenu->location) ?>
        </h3>
    </div>
    <div class="swiper ncua-horizontal-tab ncua-horizontal-tab--md ncua-horizontal-tab--underline-fill">
        <div class="swiper-wrapper">
            <div class="swiper-slide ncua-horizontal-tab__item">
                <button class="ncua-tab-button" data-target="SMS" data-url="./mobile_message_history/layer_sms_history_search.php">
                    SMS
                </button>
            </div>
            <div class="swiper-slide ncua-horizontal-tab__item">
                <button class="ncua-tab-button" data-target="FRIENDTALK" data-url="./mobile_message_history/layer_kakao_friend_talk_history_search.php">
                    카카오 친구톡
                </button>
            </div>
            <div class="swiper-slide ncua-horizontal-tab__item">
                <button class="ncua-tab-button" data-target="ALIMTALK" data-url="./mobile_message_history/layer_kakao_alim_talk_history_search.php">
                    카카오 알림톡
                </button>
            </div>
            <div class="swiper-slide ncua-horizontal-tab__item">
                <button class="ncua-tab-button" data-target="MYAPP" data-url="./mobile_message_history/layer_myapp_push_history_search.php">
                    마이앱(앱푸시)
                </button>
            </div>
        </div>
    </div>
    <section class="ncua-card">
        <div id="layerMobileMessageHistorySearch"></div>
    </section>
</article>
<script type="text/javascript" src="<?= gd_set_browser_cache(PATH_ADMIN_GD_SHARE . 'ncds/script/ncds-checkbox-group/ncds-checkbox-group.js') ?>"></script>
<script defer src="<?= gd_set_browser_cache(PATH_ADMIN_GD_SHARE . 'ncds/script/common/spinner-modal.js') ?>"></script>
<script>
    const sendMethod = <?= json_encode($sendMethod ?? '', JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT) ?>;

    $(document).ready(function () {
        initializeTabs();
    });

    function initializeTabs() {
        const tabElements = document.querySelectorAll('.ncua-horizontal-tab__item > .ncua-tab-button');
        tabElements.forEach(tab => {
            tab.addEventListener('click', function () {
                loadMobileMessageHistorySearchLayer(this);
                tabElements.forEach(t => t.classList.remove('is-active'));
                this.classList.add('is-active');
            });
        });

        const targetTab = sendMethod ? document.querySelector(`.ncua-tab-button[data-target="${sendMethod}"]`) : tabElements[0];
        targetTab.click();
    }

    function loadMobileMessageHistorySearchLayer(tab) {
        $.post(tab.dataset.url, null, function (data) {
            $('#layerMobileMessageHistorySearch').html(data);
        });
    }
</script>
