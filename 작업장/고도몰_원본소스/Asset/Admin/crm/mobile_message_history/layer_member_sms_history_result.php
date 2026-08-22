<section class="ncua-search-result js-sms-history-result">
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
                        <?= gd_select_box('sort', 'sort', $sortType, null, $searchFormData['sort'], null, null, 'ncua-select__tag'); ?>
                    </span>
                </span>
                <span class="ncua-select ncua-select--xs">
                    <span class="ncua-select__content">
                        <?= gd_select_box('pageSize', 'pageSize', gd_array_change_key_value([10, 20, 30, 40, 50, 60, 70, 80, 90, 100, 200, 300, 500]), '개씩 보기', $searchFormData['pageSize'], null, null, 'ncua-select__tag'); ?>
                    </span>
                </span>
            </div>
        </div>
        <div class="ncua-table ncua-table--horizontal">
            <table class="js-result-table">
                <colgroup>
                    <col width="120px" />
                    <col width="120px" />
                    <col />
                    <col width="180px" />
                    <col width="180px" />
                    <col width="180px" />
                </colgroup>
                <thead>
                    <tr>
                        <th>
                            <div>구분</div>
                        </th>
                        <th>
                            <div>발송 유형</div>
                        </th>
                        <th>
                            <div>발송 내용</div>
                        </th>
                        <th>
                            <div>발송자</div>
                        </th>
                        <th>
                            <div>발송 일시</div>
                        </th>
                        <th>
                            <div>발송 상태</div>
                        </th>
                    </tr>
                </thead>
                <tbody>
                <?php if (!empty($smsHistoryList)): ?>
                    <?php foreach ($smsHistoryList as $smsHistory): ?>
                        <tr>
                            <td>
                                <div><?= $smsHistory->getType()->name ?></div>
                            </td>
                            <td>
                                <div class="ncua-flex-column">
                                    <?= $smsHistory->getTriggerType()->value ?? '-' ?>
                                    <?php if ($smsHistory->isRepeated()): ?>
                                        <span class="ncua-count"><?= $smsHistory->getRepeatRound() ?: 1 ?>회차</span>
                                    <?php endif; ?>
                                </div>
                            </td>
                            <td>
                                <div class="ncua-align-items-start"><button class="ncua-left-align mobile-history-list-message-content-btn js-show-message-contents" data-send-key="<?= $smsHistory->getSendKey() ?>" type="button"><?= $smsHistory->getContent() ?></button></div>
                            </td>
                            <td>
                                <div class="ncua-flex-column"><?= $smsHistory->getSenderName() ?> <span><?= $smsHistory->getFrom() ?></span></div>
                            </td>
                            <td>
                                <div><?= $smsHistory->getReservedAt() ?></div>
                            </td>
                            <td>
                                <div class="ncua-flex-column">
                                    <span><?= $smsHistory->getSendStatus()->value ?></span>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="6">
                            <div class="no-data">발송 내역이 없습니다.</div>
                        </td>
                    </tr>
                <?php endif; ?>
                </tbody>
            </table>
        </div>
        <div class="ncua-table-bottom"></div>
    </div>
    <div class="ncua-pagination"><?= $page->getPage('loadContentsByPage(\'PAGELINK\')') ?></div>
</section>

<script type="text/javascript">
    const searchFormData = JSON.parse('<?= json_encode($searchFormData, JSON_UNESCAPED_UNICODE | JSON_HEX_TAG | JSON_HEX_APOS) ?>');

    $(document).ready(function () {
        initializeEvents();
    });

    function initializeEvents() {
        const container = document.querySelector('.js-sms-history-result');

        container.querySelector('#sort').addEventListener('change', function () {
            loadContentsByPage('page=1', this.value, null);
        });

        container.querySelector('#pageSize').addEventListener('change', function () {
            loadContentsByPage('page=1', null, this.value);
        });

        container.addEventListener('click', (e) => {
            const messageBtn = e.target.closest('.js-show-message-contents');
            if (messageBtn) {
                showMessageContents(messageBtn.dataset.sendKey);
            }
        });

        new CheckboxGroup('.js-result-table');
    }

    function loadContentsByPage(page, sort = null, pageSize = null) {
        const formData = new FormData();
        Object.entries(searchFormData).forEach(([key, value]) => {
            if (Array.isArray(value)) {
                value.forEach(v => formData.append(`${key}[]`, v));
            } else {
                formData.append(key, value);
            }
        });

        const [key, value] = page.split('=');
        formData.append(key, value);

        if (sort) formData.append('sort', sort);
        if (pageSize) formData.append('pageSize', pageSize);

        loadContents(formData);
    }

    function showMessageContents(sendKey) {
        $.post('/crm/mobile_message_history/layer_sms_contents.php', {sendGroupKey: sendKey}, function (data) {
            ncds_layer_popup({ message:data, title:'발송 내용' });
        }).fail(function () {
            NCDSAlert({ message: '발송 내용을 불러오는 중 오류가 발생했습니다.', iconType: 'error' });
        });
    }
</script>
