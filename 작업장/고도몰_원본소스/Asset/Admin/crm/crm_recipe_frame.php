<link type="text/css" href="<?= gd_set_browser_cache(PATH_ADMIN_GD_SHARE . 'ncds/css/crm/crm-recipe.css') ?>" rel="stylesheet"/>
<script src="<?= gd_set_browser_cache(PATH_ADMIN_GD_SHARE . 'ncds/script/common/aggregator-message-router.js') ?>"></script>
<iframe name="crmRecipe" src="<?= gd_htmlspecialchars($pageUrl) ?>" frameborder="0" marginwidth="0" marginheight="0" width="100%" style="height: 100vh;"></iframe>
<script>
(() => {
    'use strict';

    const DRAWER_ID = 'crm-recipe-drawer';

    const PENDING_TOAST_KEY = 'crmRecipePendingToast';
    (() => {
        try {
            const raw = sessionStorage.getItem(PENDING_TOAST_KEY);
            if (!raw) return;

            sessionStorage.removeItem(PENDING_TOAST_KEY);
            const saved = JSON.parse(raw);
            if (saved?.toast && window.NCDSToast && Date.now() - saved.at < 10000) {
                window.NCDSToast(saved.toast);
            }
        } catch (e) {/* sessionStorage 접근 불가 환경 무시 */}
    })();

    const onKeydown = (event) => {
        if (event.key === 'Escape') closeDrawer();
    };

    const closeDrawer = () => {
        document.getElementById(DRAWER_ID)?.remove();
        // 어드민은 window 스크롤이라 html/body 둘 다 해제
        document.documentElement.classList.remove('crm-recipe-drawer-open');
        document.body.classList.remove('crm-recipe-drawer-open');
        document.removeEventListener('keydown', onKeydown);
    };

    const openDrawer = (url) => {
        closeDrawer(); // 중복 오픈 방지

        const iframe = Object.assign(document.createElement('iframe'), {
            className: 'crm-recipe-drawer__iframe',
            src: url,
        });

        const panel = document.createElement('div');
        panel.className = 'crm-recipe-drawer__panel';
        panel.append(iframe);

        const backdrop = document.createElement('div');
        backdrop.id = DRAWER_ID;
        backdrop.className = 'crm-recipe-drawer';
        backdrop.append(panel);

        document.body.append(backdrop);
        // 부모 스크롤 잠금 + 스크롤바 제거
        document.documentElement.classList.add('crm-recipe-drawer-open');
        document.body.classList.add('crm-recipe-drawer-open');
        document.addEventListener('keydown', onKeydown);
    };

    window.addEventListener('message', ({ data }) => {
        if (data?.type !== 'CLOSE_CRM_RECIPE_DRAWER') return;

        closeDrawer();

        if (data.toast) {
            try {
                sessionStorage.setItem(PENDING_TOAST_KEY, JSON.stringify({ toast: data.toast, at: Date.now() }));
            } catch (e) {/* sessionStorage 접근 불가 환경 무시 */}
        }

        if (data.reloadList) {
            const listFrame = document.querySelector('iframe[name="crmRecipe"]');
            if (listFrame) listFrame.src = listFrame.src;
        }
    });

    // 공통 상품선택 팝업의 콜백을 드로어 iframe 으로 전달
    window.setAddGoods = function (resultJson) {
        document.querySelector('.crm-recipe-drawer__iframe')?.contentWindow?.setAddGoods?.(resultJson);
    };

    // PERMISSION_CHECK 응답용 action 별 거부 사유 (null = 허용)
    const PERMISSION_DENIAL_REASONS = <?= json_encode($permissionDenialReasons, JSON_HEX_TAG) ?>;

    IframeMessageRouter.init({
        onLayerOpen: openDrawer,
        onLayerClose: closeDrawer,
        onPermissionCheck: (event) => {
            if (event.data?.category !== 'CRM_RECIPE') return;

            const { action, requestId } = event.data.payload || {};
            const known = Object.prototype.hasOwnProperty.call(PERMISSION_DENIAL_REASONS, action);
            const reason = known ? PERMISSION_DENIAL_REASONS[action] : null;

            const payload = { requestId, granted: known && !reason };
            if (reason) payload.reason = reason;

            event.source?.postMessage({ category: 'CRM_RECIPE', type: 'PERMISSION_CHECK', payload }, event.origin || '*');
        },
    });
})();
</script>
