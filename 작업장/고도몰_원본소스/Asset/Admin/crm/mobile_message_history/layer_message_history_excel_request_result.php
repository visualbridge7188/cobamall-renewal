<link type="text/css" href="<?= gd_set_browser_cache(PATH_ADMIN_GD_SHARE . 'ncds/css/crm/layer-history-download.css') ?>" rel="stylesheet"/>
<div class="js-download-table ncua-search-result">
    <div class="ncua-search-result__content">
        <div class="ncua-search-result__actions">
            <button class="ncua-btn ncua-btn--xs ncua-btn--secondary-gray crm-excel-download-refresh js-excel-download-refresh">
                상태 새로고침
            </button>
        </div>
        <div class="ncua-table ncua-table--horizontal">
            <table>
                <colgroup>
                    <col width="80px" />
                    <col width="200px" />
                    <col />
                    <col width="120px" />
                    <col width="120px" />
                    <col width="120px" />
                    <col width="120px" />
                </colgroup>
                <thead>
                    <tr>
                        <th>
                            <div>번호</div>
                        </th>
                        <th>
                            <div>다운로드 양식명</div>
                        </th>
                        <th>
                            <div>파일명</div>
                        </th>
                        <th>
                            <div>파일상태</div>
                        </th>
                        <th>
                            <div>요청자</div>
                        </th>
                        <th>
                            <div>다운로드 기간</div>
                        </th>
                        <th>
                            <div>다운로드</div>
                        </th>
                    </tr>
                </thead>
                <tbody>
                <?php if (!empty($excelGenerateHistoryList)): ?>
                    <?php foreach ($excelGenerateHistoryList as $excelGenerateHistory): ?>
                        <tr>
                            <td>
                                <div><?= $page->getTotal() - ($page->idx--) + 1 ?></div>
                            </td>
                            <td>
                                <div><?= $excelGenerateHistory->getMenuType()->value ?></div>
                            </td>
                            <td>
                                <div><?= $excelGenerateHistory->getFileName() ?></div>
                            </td>
                            <td>
                                <div><?= $excelGenerateHistory->getStatus()->displayLabel() ?></div>
                            </td>
                            <td>
                                <div><?= $excelGenerateHistory->getAdminName() ?>
                                <?php if (!empty($excelGenerateHistory->getAdminId())): ?>
                                    <br />(<?= $excelGenerateHistory->getAdminId() ?>)</div>
                                <?php endif; ?>
                            </td>
                            <td>
                                <?php if (in_array($excelGenerateHistory->getStatus(), $downloadableStatuses)): ?>
                                    <div>~<?= date('Y-m-d', strtotime($excelGenerateHistory->getExpiredAt())) ?></div>
                                <?php endif; ?>
    
                            </td>
                            <td>
                                <?php if (in_array($excelGenerateHistory->getStatus(), $downloadableStatuses)): ?>
                                    <div>
                                        <button type="button" class="ncua-btn ncua-btn--xxs ncua-btn--secondary layer-history-download-btn" data-click-target="excelDownload" data-no="<?= $excelGenerateHistory->getNo() ?>">다운로드</button>
                                    </div>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="7" class="no-data">요청한 파일이 없습니다.</td>
                    </tr>
                <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
    <div class="ncua-pagination"><?= $page->getPage('loadLayerContentsByPage(\'PAGELINK\')') ?></div>
</div>

<script type="text/javascript">
    const postData = JSON.parse('<?= json_encode($postData, JSON_UNESCAPED_UNICODE | JSON_HEX_TAG | JSON_HEX_APOS) ?>');

    $(document).ready(function() {
        initializeEvents();
    });

    function initializeEvents() {
        const container = document.querySelector('.js-download-table');
        // 엑셀 다운로드
        container.querySelectorAll('button[data-click-target=excelDownload]')?.forEach((element) => {
            element.addEventListener('click', (e) => {
                const excelNo = e.currentTarget.dataset.no;
                $.post('./mobile_message_history/layer_message_history_excel_download.php', {menu: postData.menu, excelNo: excelNo}, function (data) {
                    ncds_layer_popup({message: data, title: '엑셀 다운로드 사유', size: 'wide'});
                }).fail(function (xhr) {
                    const response = xhr.responseJSON;
                    NCDSAlert({
                        message: response?.message || '일시적인 오류가 발생하였습니다.<br>다시 시도해 주세요.',
                        iconType: 'error'
                    });
                });
            });
        });

        container.querySelector('.js-excel-download-refresh').addEventListener('click', (e) => {
            loadLayerContentsByPage();
        });
    }

    function loadLayerContentsByPage(page) {
        const formData = new FormData();
        Object.entries(postData).forEach(([key, value]) => {
            if (Array.isArray(value)) {
                value.forEach(v => formData.append(`${key}[]`, v));
            } else {
                formData.append(key, value);
            }
        });

        if (page) {
            const [key, value] = page.split('=');
            formData.append(key, value);
        }

        loadLayerContents(formData);
    }
</script>
