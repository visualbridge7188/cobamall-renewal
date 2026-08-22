<link rel="stylesheet" href="<?= gd_set_browser_cache(PATH_ADMIN_GD_SHARE . 'ncds/css/crm/layer-charge-message-points-history.css') ?>">

<article class="ncua-content layer-charge-message-points-history">
    <!-- 충전 내역 테이블 -->
    <div id="layerChargeMessagePointsHistoryResult"></div>
</article>

<script type="text/javascript">
    $(document).ready(function () {
        loadContents();
    });
    function loadContents(page = 1) {
        $.ajax({
            url: './message_config/layer_charge_message_points_history_result.php',
            type: 'POST',
            data: { page: page },
            success: function (data) {
                $('#layerChargeMessagePointsHistoryResult').html(data);
            }
        });
    }
</script>
