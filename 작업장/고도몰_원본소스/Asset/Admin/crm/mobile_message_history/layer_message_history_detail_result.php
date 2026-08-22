<link type="text/css" href="<?= gd_set_browser_cache(PATH_ADMIN_GD_SHARE . 'ncds/css/crm/layer-history-download.css') ?>" rel="stylesheet"/>
<section class="ncua-search-result js-sms-history-detail-result">
    <div class="ncua-search-result__summary">
        <p class="ncua-search-result__summary-count">
            검색 <strong><?= $page->getTotal() ?></strong> 개
        </p>
    </div>

    <div class="ncua-search-result__content">
        <div class="ncua-search-result__actions">
            <div class="ncua-search-result__actions-select">
                <span class="ncua-select ncua-select--xs">
                    <span class="ncua-select__content">
                        <?= gd_select_box('pageSize', 'pageSize', gd_array_change_key_value([10, 20, 30, 40, 50, 60, 70, 80, 90, 100]), '개씩 보기', $postData['pageSize'], null, null, 'ncua-select__tag'); ?>
                    </span>
                </span>
            </div>
        </div>

        <div class="ncua-table ncua-table--horizontal">
            <table>
                <colgroup>
                    <col width="60px" />
                    <col />
                </colgroup>
                <thead>
                    <tr>
                        <th>
                            <div>No</div>
                        </th>
                        <th>
                            <div>수신 일시</div>
                        </th>
                        <th>
                            <div>수신 번호</div>
                        </th>
                        <th>
                            <div>발송 결과</div>
                        </th>
                        <th>
                            <div>실패 사유</div>
                        </th>
                    </tr>
                </thead>
                <tbody>
                <?php if (!empty($messageHistoryDetailList)): ?>
                    <?php foreach ($messageHistoryDetailList as $messageHistoryDetail): ?>
                        <tr>
                            <td>
                                <div><?= $page->idx-- ?></div>
                            </td>
                            <td>
                                <div><?= $messageHistoryDetail->getSentAt() ?></div>
                            </td>
                            <td>
                                <div>
                                    <?= $messageHistoryDetail->getPhoneNumber() ?>
                                </div>
                            </td>
                            <td>
                                <div><?= $messageHistoryDetail->getResultType()->value ?></div>
                            </td>
                            <td>
                                <div><?= $messageHistoryDetail->getResultType()->name === 'FAIL' ? $messageHistoryDetail->getResultMessage() : '-' ?></div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="5" class="no-data">수신 대상이 없습니다.</td>
                    </tr>
                <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
    <div class="ncua-pagination"><?= $page->getPage('loadLayerContentsByPage(\'PAGELINK\')') ?></div>
</section>
<?php if ($postData['type'] !== 'SMS'): ?>
<ul class="layer-history-detail-info">
    <li class="ncua-notice-info"><a href="./message_config.php" class="ncua-link">메시지 설정</a>에서 SMS 대체발송 설정이 활성화된 경우, <?= $postData['type'] === 'FRIENDTALK' ? '친구톡' : '알림톡' ?> 발송 실패 시 SMS/LMS로 자동 재발송됩니다.</li>
    <li class="ncua-notice-info">재발송 내역은 모바일 메시지 발송 내역 > SMS 탭에서 확인하세요.</li>
</ul>
<?php endif; ?>

<script type="text/javascript">
    const layerPostData = JSON.parse('<?= json_encode($postData, JSON_UNESCAPED_UNICODE | JSON_HEX_TAG | JSON_HEX_APOS) ?>');

    $(document).ready(function() {
        const container = document.querySelector('.js-sms-history-detail-result');
        container.querySelector('#pageSize').addEventListener('change', function() {
            loadLayerContentsByPage('page=1', $(this).val());
        });
    });

    function loadLayerContentsByPage(page, pageSize = null) {
        const formData = new FormData();
        Object.entries(layerPostData).forEach(([key, value]) => {
            if (Array.isArray(value)) {
                value.forEach(v => formData.append(`${key}[]`, v));
            } else {
                formData.append(key, value);
            }
        });

        const [key, value] = page.split('=');
        formData.append(key, value);

        if (pageSize) formData.append('pageSize', pageSize);

        loadLayerContents(formData);
    }
</script>
