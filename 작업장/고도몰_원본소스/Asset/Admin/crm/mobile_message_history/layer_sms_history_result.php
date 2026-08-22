<section class="ncua-search-result js-sms-history-result">
    <div class="ncua-search-result__summary">
        <p class="ncua-search-result__summary-count">
            검색 <strong><?= $page->getTotal() ?></strong> 개
        </p>
        <div class="ncua-search-result__summary-button">
            <button type="button" class="ncua-btn ncua-btn--xs ncua-btn--secondary-gray" data-type="<?= $excelMenu->name ?>" data-click-target="excelDownload">
                <img src="/admin/gd_share/ncds/image/ico_excel_download.svg" alt="엑셀 다운로드" />
                엑셀 다운로드
            </button>
        </div>
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
                    <col width="48px" />
                    <col width="120px" />
                    <col width="120px" />
                    <col width="120px" />
                    <col />
                    <col width="180px" />
                    <col width="180px" />
                    <col width="200px" />
                    <col width="180px" />
                </colgroup>
                <thead>
                    <tr>
                        <th>
                            <div>
                                <label class="ncua-checkbox-field ncua-checkbox-field--xs has-text">
                                    <span class="ncua-checkbox-input ncua-checkbox-input--xs ncua-checkbox-field__input">
                                        <input type="checkbox" value="all" />
                                    </span>
                                </label>
                            </div>
                        </th>
                        <th>
                            <div>구분</div>
                        </th>
                        <th>
                            <div>차감 포인트</div>
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
                            <div class="ncua-flex-column">발송 건수 <span>(발송성공/발송실패)</span></div>
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
                                <div>
                                    <label class="ncua-checkbox-field ncua-checkbox-field--xs has-text">
                                        <span class="ncua-checkbox-input ncua-checkbox-input--xs ncua-checkbox-field__input">
                                            <input type="checkbox" name="sendKeys[]" value="<?= $smsHistory->getSendKey() ?>" />
                                        </span>
                                    </label>
                                </div>
                            </td>
                            <td>
                                <div><?= $smsHistory->getType()->name ?></div>
                            </td>
                            <td>
                                <div><?= $smsHistory->getSendStatus()->name === 'PENDING' || empty($smsHistory->getUsedPoint()) ? '-' : $smsHistory->getUsedPoint() ?></div>
                            </td>
                            <td>
                                <div class="ncua-flex-column">
                                    <?= $smsHistory->getTriggerType()->value ?? '-' ?>
                                    <?php if ($smsHistory->getTriggerType()->name == 'REPEATED'): ?>
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
                                <div class="ncua-flex-column"><?= "{$smsHistory->getTotalCount()} <span>({$smsHistory->getSuccessCount()}/{$smsHistory->getFailCount()})</span>" ?></div>
                            </td>
                            <td>
                                <div class="ncua-flex-column">
                                    <span><?= $smsHistory->getSendStatus()->value ?></span>
                                    <div class="ncua-flex-gap">
                                        <button type="button" class="ncua-btn ncua-btn--xxs ncua-btn--secondary-gray js-show-history-detail" data-send-key="<?= $smsHistory->getSendKey() ?>">상세</button>
                                        <?php if ($smsHistory->getSendStatus()->name == 'RESERVED'): ?>
                                            <button type="button" class="ncua-btn ncua-btn--xxs ncua-btn--secondary" data-click-target="cancelReservedSend" data-send-key="<?= $smsHistory->getSendKey() ?>" data-reserved-at="<?= $smsHistory->getReservedAt() ?>" data-sms-type="<?= $smsHistory->getType()->name ?>">예약 취소</button>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="9">
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
    const excelSearchQuery = JSON.parse('<?= json_encode($excelSearchQuery, JSON_UNESCAPED_UNICODE | JSON_HEX_TAG | JSON_HEX_APOS) ?>');

    $(document).ready(function () {
        initializeEvents();
    });

    function initializeEvents() {
        const container = document.querySelector('.js-sms-history-result');
        // 엑셀 다운로드
        container.querySelector('button[data-click-target=excelDownload]').addEventListener('click', (e) => {
            const title = 'SMS 발송내역 다운로드';
            const type = e.target.dataset.type;
            const checkedSendKeys = Array.from(container.querySelectorAll('input[name="sendKeys[]"]:checked')).map(input => input.value);

            $.post('./mobile_message_history/layer_message_history_excel_request.php', {
                type: type,
                searchQuery: excelSearchQuery,
                selectedSendKeys: checkedSendKeys
            }, function (data) {
                ncds_layer_popup({title: title, message: data, size: 'wide'});
            }).fail(function (xhr) {
                NCDSAlert({
                    message: xhr.status === 403 ? xhr.responseJSON.message : '일시적인 오류가 발생하였습니다.<br>다시 시도해 주세요.',
                    iconType: 'error'
                });
            });
        });

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
                return;
            }

            const detailBtn = e.target.closest('.js-show-history-detail');
            if (detailBtn) {
                showHistoryDetail(detailBtn.dataset.sendKey);
            }

            const cancelBtn = e.target.closest('[data-click-target="cancelReservedSend"]');
            if (cancelBtn) {
                const reservedAt = new Date(cancelBtn.dataset.reservedAt);
                const now = new Date();
                const diffMinutes = (reservedAt - now) / (1000 * 60);

                if (diffMinutes > 0 && diffMinutes <= 5) {
                    NCDSAlert({ message: '발송시각이 임박하여 예약 취소를 할 수 없습니다.<br>발송 시각 5분 전까지 취소 가능합니다.', iconType: 'error' });
                    return;
                }

                NCDSConfirm({
                    message: '예약 발송을 취소하시겠습니까?<br>취소된 메시지는 리스트에서 삭제되어 확인이 불가합니다.'
                }).then(function (result) {
                    if (result) {
                        cancelReservedSend(cancelBtn.dataset.sendKey, cancelBtn.dataset.smsType);
                    }
                });
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
        $.post('./mobile_message_history/layer_sms_contents.php', {sendGroupKey: sendKey}, function (data) {
            ncds_layer_popup({ message:data, title:'발송 내용' });
        }).fail(function () {
            NCDSAlert({ message: '발송 내용을 불러오는 중 오류가 발생했습니다.', iconType: 'error' });
        });
    }

    function showHistoryDetail(sendKey) {
        $.post('./mobile_message_history/layer_message_history_detail.php', {sendGroupKey: sendKey, type: 'SMS'}, function (data) {
            ncds_layer_popup({ message: data, title: '발송 내역 상세', size: 'wide' });
        }).fail(function () {
            NCDSAlert({ message: '발송 내역을 불러오는 중 오류가 발생했습니다.', iconType: 'error' });
        });
    }

    function cancelReservedSend(sendKey, smsType) {
        $.ajax({
            url: './mobile_message_history/layer_sms_history_result_ps.php',
            type: 'POST',
            data: { mode: 'cancel', sendGroupKey: sendKey, type: smsType, cancelMode: 'ALL' },
            dataType: 'json',
            success: () => {
                NCDSToast({
                    message: '예약 발송이 취소되었습니다.',
                    color: 'success',
                });

                loadContentsByPage(`page=${searchFormData.page ?? 1}`);
            },
            error: (xhr) => {
                if (xhr.status === 400) {
                    const data = xhr.responseJSON;
                    NCDSAlert({ message: data?.message || '일시적인 오류가 발생하였습니다.<br>다시 시도해 주세요.', iconType: 'error' });
                } else {
                    NCDSToast({
                        message: '일시적인 오류가 발생했습니다.',
                        color: 'error',
                        autoClose: 5000
                    });
                }
            }
        });
    }
</script>
