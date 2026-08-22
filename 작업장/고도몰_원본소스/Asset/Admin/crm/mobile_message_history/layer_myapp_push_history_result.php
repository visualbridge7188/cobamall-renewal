<section class="ncua-search-result js-myapp-push-history-result">
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
                        <?= gd_select_box('sort', 'sortType', $sort, null, $searchFormData['sort'], null, null, 'ncua-select__tag'); ?>
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
            <table>
                <colgroup>
                    <col />
                    <col />
                    <col width="120px" />
                    <col width="120px" />
                    <col width="220px" />
                    <col width="220px" />
                    <col width="180px" />
                </colgroup>
                <thead>
                    <tr>
                        <th>
                            <div>푸시 제목</div>
                        </th>
                        <th>
                            <div>발송 내용</div>
                        </th>
                        <th>
                            <div>발송 목적</div>
                        </th>
                        <th>
                            <div>발송 유형</div>
                        </th>
                        <th>
                            <div>발송 일시</div>
                        </th>
                        <th class="myapp-layout-shift">
                            <div class="ncua-flex-column">
                                <span>
                                    <span class="ncua-flex ncua-align-items-center">발송 건수
                                        <span class="ncua-flex" data-tooltip-icon-type="fill" data-tooltip-seq="008"></span>
                                    </span>
                                </span>(발송성공/발송실패)
                            </div>
                        </th>
                        <th class="myapp-layout-shift">
                            <div>발송 상태 <span class="ncua-flex" data-tooltip-icon-type="fill" data-tooltip-seq="015"></span></div>
                        </th>
                    </tr>
                </thead>
                <tbody>
                <?php if (!empty($myappPushHistoryList)): ?>
                    <?php foreach ($myappPushHistoryList as $myappPushHistory): ?>
                        <tr>
                            <td>
                                <div><?= $myappPushHistory->getTitle() ?></div>
                            </td>
                            <td>
                                <div><button class="ncua-left-align mobile-history-list-message-content-btn" onclick="showMessageContents(<?= $myappPushHistory->getPushNo() ?>)" type="button"><?= $myappPushHistory->getContent() ?></button></div>
                            </td>
                            <td>
                                <div><?= $myappPushHistory->getNotificationType()->value ?></div>
                            </td>
                            <td>
                                <div class="ncua-flex-column">
                                    <?= $myappPushHistory->getScheduleType()->value ?>
                                    <?php if ($myappPushHistory->getScheduleType()->name === 'REPEAT'): ?>
                                        <span class="ncua-count"><?= $myappPushHistory->getIteration() ?: 1 ?>회차</span>
                                    <?php endif; ?>
                                </div>
                            </td>
                            <td>
                                <div><?= $myappPushHistory->getSendDateTime() ?></div>
                            </td>
                            <td>
                                <div class="ncua-flex-column"><?= "{$myappPushHistory->getSendTotalCount()} <span>({$myappPushHistory->getSendSuccessCount()}/{$myappPushHistory->getSendFailCount()})</span>" ?></div>
                            </td>
                            <td>
                                <div>
                                    <span><?= $myappPushHistory->getSendStatus()->value ?></span>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="7" class="no-data">
                            <div>
                                <p>발송 내역이 없습니다.
                                <?php if (!$isEnabled): ?>
                                    <br />이 발송수단은 현재 '사용 안함'으로 설정되어 있습니다.<br />
                                    발송을 위해 <a class="ncua-link" href="./message_config.php">메시지 설정</a>에서 사용을 활성화해 주세요.
                                <?php endif; ?>
                                </p>
                            </div>
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

    $(document).ready(function() {
        document.querySelector('.js-mobile-history-list').classList.add('mobile-history-list-hidden-arrow');
        const container = document.querySelector('.js-myapp-push-history-result');

        container.querySelector('#sort').addEventListener('change', function() {
            loadContentsByPage('page=1', $(this).val(), null);
        });

        container.querySelector('#pageSize').addEventListener('change', function() {
            loadContentsByPage('page=1', null, $(this).val());
        });
    });

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

    function showMessageContents(pushNo) {
        $.post('./mobile_message_history/layer_myapp_push_contents.php', {pushNo: pushNo}, function (data) {
            if (!data) {
                NCDSAlert({ message: '발송 내용을 불러오는 중 오류가 발생했습니다.', iconType: 'error' });
                return;
            }

            ncds_layer_popup({ message: data, title: '발송 내용' });
        });
    }
</script>
<script type="text/javascript">
    const code = '251210001';
    if (window.GodoCosGuide) {
        window.GodoCosGuide.init({
            apiUrl: <?= json_encode($cosGuideApiUrl) ?>,
            guideCode: code,
        });
    }
</script>
