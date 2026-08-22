<link type="text/css" href="<?= gd_set_browser_cache(PATH_ADMIN_GD_SHARE . 'ncds/css/crm/mobile-history-list.css') ?>" rel="stylesheet"/>
<script type="text/javascript" src="<?= gd_set_browser_cache(PATH_ADMIN_GD_SHARE . 'ncds/script/ncds-datepicker-factory/ncds-datepicker-factory.js') ?>"></script>
<article class="ncua-content mobile-history-list js-mobile-history-list">
    <section class="ncua-card">
        <div id="layerMobileMessageHistorySearch"></div>
    </section>
</article>
<script type="text/javascript" src="<?= gd_set_browser_cache(PATH_ADMIN_GD_SHARE . 'ncds/script/ncds-checkbox-group/ncds-checkbox-group.js') ?>"></script>
<script defer src="<?= gd_set_browser_cache(PATH_ADMIN_GD_SHARE . 'ncds/script/common/spinner-modal.js') ?>"></script>
<script>
    $(document).ready(function () {
        loadMobileMessageHistorySearchLayer();
    });

    function loadMobileMessageHistorySearchLayer() {
        const navTabs = {
            sms: '/crm/mobile_message_history/layer_member_sms_history_search.php',
            friendTalk: '/crm/mobile_message_history/layer_member_kakao_friend_talk_history_search.php',
            alimTalk: '/crm/mobile_message_history/layer_member_kakao_alim_talk_history_search.php',
        };

        const params = new URLSearchParams(window.location.search);
        const tab = params.get('navTabs');
        const memNo = params.get('memNo');
        const url = navTabs[tab];

        $.post(url, { memberNo: memNo }, function (data) {
            $('#layerMobileMessageHistorySearch').html(data);
        });
    }

    $('.btn-register').click(function () {
        member_sms($('#memNo').val());
    });
</script>
