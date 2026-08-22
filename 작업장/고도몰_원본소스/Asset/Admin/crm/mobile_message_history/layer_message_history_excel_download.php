<link type="text/css"
      href="<?= gd_set_browser_cache(PATH_ADMIN_GD_SHARE . 'ncds/css/crm/layer-history-download.css') ?>"
      rel="stylesheet"/>
<section class="layer-history-download-reason">
    <form id="frmExcelDownload" method="post">
        <div class="layer-history-download-reason-content">
            <div class="ncua-table ncua-table--vertical">
                <table>
                    <colgroup>
                        <col width="144px"/>
                    </colgroup>
                    <tbody>
                    <tr>
                        <th>
                            <div>사유 선택</div>
                        </th>
                        <td>
                            <div>
                                <span class="ncua-select ncua-select--xs" data-show-hint-text="true">
                                    <span class="ncua-select__content">
                                        <select class="ncua-select__tag" name="reason">
                                            <option value="">사유 선택</option>
                                            <?php foreach ($menu as $excelDownloadReason): ?>
                                            <option value="<?= $excelDownloadReason; ?>"><?= $excelDownloadReason; ?></option>
                                            <?php endforeach; ?>
                                        </select>
                                    </span>
                                </span>
                            </div>
                        </td>
                    </tr>
                    </tbody>
                </table>
            </div>
            <p class="layer-history-download-reason-caution ncua-notice-info"><em>개인정보의 안전성 확보조치 기준(고시)에 의거하여 개인정보를 다운로드한 경우 사유 확인이
                    필요합니다.</em></p>
        </div>
        <div class="layer-history-download-reason-btns text-right">
            <button type="submit" class="ncua-btn ncua-btn--sm ncua-btn--primary">확인</button>
        </div>
    </form>
</section>


<script type="text/javascript">
    $(document).ready(function () {
        document.querySelector('#frmExcelDownload').addEventListener('submit', async (e) => {
            e.preventDefault(); // 기본 submit 막기

            if (!validationReason()) return;

            fetch('./mobile_message_history/layer_message_history_excel_request_ps.php', {
                method: 'POST',
                body: new URLSearchParams({
                    mode: 'download',
                    excelNo: <?= $excelNo ?>,
                    reason: e.target.reason.selectedOptions[0].text,
                })
            }).then(async response => {
                if (!response.ok) {
                    NCDSToast({message: '파일을 다운로드할 수 없습니다.<br>자세한 내용은 NHN 커머스 1:1문의로 확인해 주세요.', color: 'error'});
                    return;
                }
                const blob = await response.blob();
                const url = URL.createObjectURL(blob);
                const a = document.createElement('a');
                a.href = url;
                a.download = decodeURIComponent((response.headers.get('Content-Disposition')?.match(/filename\*=UTF-8''([^,;]+)/)?.[1] || 'download.zip'));
                document.body.appendChild(a);
                a.click();
                a.remove();
                URL.revokeObjectURL(url);
            }).catch(() => {
                NCDSToast({message: '파일을 다운로드할 수 없습니다.<br>자세한 내용은 NHN 커머스 1:1문의로 확인해 주세요.', color: 'error'});
            }).finally(() => {
                BootstrapDialog.dialogs[Object.keys(BootstrapDialog.dialogs).pop()]?.close();
            });
        });
    });

    const validationReason = () => {
        const reasonSelectBox = document.querySelector('select[name="reason"]');

        if (!reasonSelectBox.value) {
            NCDSValidator.highlight(reasonSelectBox);
            NCDSValidator.showErrors(null, [{
                element: reasonSelectBox,
                message: '사유를 선택해 주세요.'
            }]);
            return false;
        }

        return true;
    };
</script>
