<link rel="stylesheet" href="<?= PATH_ADMIN_GD_SHARE ?>ncds/css/duplicate-login-ncds.css"/>
<form id="frmDuplicateLogin" action="login_ps.php" method="post">
    <input type="hidden" name="mode" value="concurrentLoginConfirm"/>
    <input type="hidden" name="returnUrl" value=""/>
    <input type="hidden" name="keepExisting" id="keepExistingInput" value="n"/>
</form>

<script>
(() => {
    const init = () => {
        if (typeof window.ncua === 'undefined' || typeof window.ncua.Modal === 'undefined') {
            setTimeout(init, 50);
            return;
        }

        const makeBtn = (label, hierarchy, onClick) => {
            if (window.createButton) { return window.createButton(label, hierarchy, onClick, 'sm'); }
            const tpl = document.createElement('template');
            tpl.innerHTML = `<button type="button" class="ncua-btn ncua-btn--sm ncua-btn--${hierarchy}"><span class="ncua-btn__label">${label}</span></button>`;
            const btn = tpl.content.firstElementChild;
            btn.addEventListener('click', onClick);
            return btn;
        };

        const cancel = () => {
            location.href = '/base/login_ps.php?mode=logout';
        };

        const modal = new window.ncua.Modal({
            size: 'lg',
            className: 'duplicate-login-ncds-modal',
            closeOnBackdropClick: false,
            onClose: cancel,
        });

        const header = new window.ncua.Modal.Header({
            title: '고객님의 계정이 이미 사용중입니다.',
        });
        header.setCloseHandler(cancel);

        const contentHtml = `
            <div class="duplicate-login-ncds-content">
                <p class="duplicate-login-ncds-desc">
                    • 고객님의 계정이 다른 IP에서 로그인되었습니다. 아래 버튼을 선택하여 로그인을 진행해주세요.<br>
                    • 안전하게 이용하려면 이전 로그인을 종료하고 새로 로그인해주세요.
                </p>
                <div class="ncua-table ncua-table--vertical">
                    <table>
                        <colgroup><col width="50%"><col width="50%"></colgroup>
                        <tbody>
                            <tr>
                                <th><div>현재 로그인한 IP</div></th>
                                <td><div><?= gd_htmlspecialchars($currentLoginIp) ?></div></td>
                            </tr>
                            <tr>
                                <th><div>이전 로그인한 IP</div></th>
                                <td><div><?= gd_htmlspecialchars($lastLoginIp) ?></div></td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                <div class="duplicate-login-ncds-radio-group">
                    <label class="ncua-radio-field ncua-radio-field--xs has-text">
                        <span class="ncua-radio-input ncua-radio-input--xs ncua-radio-field__input">
                            <input type="radio" name="keepExistingModal" value="y" checked />
                        </span>
                        <span><span class="ncua-radio-field__text">이전 로그인을 그대로 유지한 상태에서 로그인 (동시 로그인 허용)</span></span>
                    </label>
                    <label class="ncua-radio-field ncua-radio-field--xs has-text">
                        <span class="ncua-radio-input ncua-radio-input--xs ncua-radio-field__input">
                            <input type="radio" name="keepExistingModal" value="n" />
                        </span>
                        <span><span class="ncua-radio-field__text">이전 로그인은 종료하고, 새로 로그인</span></span>
                    </label>
                </div>
            </div>
        `;
        const content = new window.ncua.Modal.Content(contentHtml);

        const actions = new window.ncua.Modal.Actions('', { layout: 'horizontal', align: 'right' });
        const confirmBtn = makeBtn('확인', 'primary', () => {
            const checked = modal.getModalElement().querySelector('input[name="keepExistingModal"]:checked');
            const keepExisting = checked?.value ?? 'n';
            const standalone = keepExisting !== 'y';

            fetch('/share/duplicate_login_ps.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest',
                },
                body: JSON.stringify({ standalone: standalone }),
            }).then((response) => {
                if (!response.ok) {
                    throw new Error('request failed');
                }
                location.reload();
            }).catch(() => {
                alert('요청하신 작업을 완료하지 못했습니다.\n잠시 후 다시 시도해 주세요.');
            });
        });
        const cancelBtn = makeBtn('취소', 'secondary-gray', cancel);
        actions.setContent([cancelBtn, confirmBtn]);

        const modalEl = modal.getModalElement();
        header.appendTo(modalEl);
        content.appendTo(modalEl);
        actions.appendTo(modalEl);
        modal.open();
    };

    init();
})();
</script>
