<script src="<?= gd_set_browser_cache(PATH_ADMIN_GD_SHARE . 'ncds/script/common/aggregator-message-router.js') ?>"></script>
<iframe name="popupCreateCrmGroup" src="<?= htmlspecialchars($pageUrl, ENT_QUOTES, 'UTF-8') ?>" frameborder="0" marginwidth="0" marginheight="0" width="100%" style="height: 100vh;"></iframe>
<script>
    // aggregator가 그룹 생성 성공 시 MODAL_CLOSE 전송 — iframe은 창을 못 닫으므로 여기서 opener 반영 후 닫는다.
    IframeMessageRouter.init({
        onModalClose: function (payload) {
            const { no, title } = payload ?? {};
            if (no) {
                opener?.selectCreatedCrmGroup?.(no, title);
            }
            window.close();
        },
    });
</script>
