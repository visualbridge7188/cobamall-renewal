<section class="ncua-card__body">
    <form id="frmSearch" method="post" action="" class="js-form-enter-submit">
        <div class="ncua-table ncua-table--vertical">
            <table>
                <colgroup>
                    <col width="240px" />
                    <col />
                </colgroup>
                <tbody>
                <?php if ($isKakaoAlrimAvailable): ?>
                    <tr id="kakaoAlimTalkSenderSelector">
                        <th>
                            <div>제공사</div>
                        </th>
                        <td>
                            <div class="ncua-flex-gap">
                                <label class="ncua-radio-field ncua-radio-field--xs has-text">
                                    <span class="ncua-radio-input ncua-radio-input--xs ncua-radio-field__input">
                                        <input type="radio" name="sender" value="kakaoAlrimCloud" <?= $isSelectedBizm ? '' : 'checked="checked"' ?>/>
                                    </span>
                                    <span class="ncua-radio-field__text">고도몰</span>
                                </label>
                                <label class="ncua-radio-field ncua-radio-field--xs has-text">
                                    <span class="ncua-radio-input ncua-radio-input--xs ncua-radio-field__input">
                                        <input type="radio" name="sender" value="kakaoAlrim" <?= $isSelectedBizm ? 'checked="checked"' : '' ?> />
                                    </span>
                                    <span class="ncua-radio-field__text">비즈엠</span>
                                </label>
                            </div>
                        </td>
                    </tr>
                <?php else: ?>
                    <input type="hidden" name="sender" value="kakaoAlrimCloud"/>
                <?php endif; ?>
                <tr>
                    <th>
                        <div>발송일</div>
                    </th>
                    <td>
                        <div>
                            <div id="datepicker-container"></div>
                        </div>
                    </td>
                </tr>
                <tr>
                    <th>
                        <div>발송 유형</div>
                    </th>
                    <td>
                        <div class="ncua-flex-gap js-trigger-type">
                            <label class="ncua-checkbox-field ncua-checkbox-field--xs has-text">
                                <span class="ncua-checkbox-input ncua-checkbox-input--xs ncua-checkbox-field__input">
                                    <input type="checkbox" id="triggerTypeAll" value="all" <?= $isSelectedBizm ? 'disabled="disabled"' : 'checked="checked"' ?> />
                                </span>
                                <span class="ncua-checkbox-field__text">전체</span>
                            </label>
                            <?php foreach ($triggerTypes as $triggerType): ?>
                                <label class="ncua-checkbox-field ncua-checkbox-field--xs has-text">
                                    <span class="ncua-checkbox-input ncua-checkbox-input--xs ncua-checkbox-field__input">
                                        <input type="checkbox" name="triggerTypes[]" value="<?= $triggerType->name ?>" <?= $isSelectedBizm ? 'disabled="disabled"' : 'checked="checked"' ?> />
                                    </span>
                                    <span class="ncua-checkbox-field__text"><?= $triggerType->value ?></span>
                                </label>
                            <?php endforeach; ?>
                        </div>
                    </td>
                </tr>
                <tr>
                    <th>
                        <div>발송 상태</div>
                    </th>
                    <td>
                        <div class="ncua-flex-gap js-send-status">
                            <label class="ncua-checkbox-field ncua-checkbox-field--xs has-text">
                                <span class="ncua-checkbox-input ncua-checkbox-input--xs ncua-checkbox-field__input">
                                    <input type="checkbox" value="all" checked="checked"/>
                                </span>
                                <span class="ncua-checkbox-field__text">전체</span>
                            </label>
                            <?php foreach ($sendStatuses as $sendStatus): ?>
                                <label class="ncua-checkbox-field ncua-checkbox-field--xs has-text">
                                    <span class="ncua-checkbox-input ncua-checkbox-input--xs ncua-checkbox-field__input">
                                        <input type="checkbox" name="sendStatuses[]" value="<?= $sendStatus->name ?>" checked="checked" />
                                    </span>
                                    <span class="ncua-checkbox-field__text"><?= $sendStatus->value ?></span>
                                </label>
                            <?php endforeach; ?>
                        </div>
                    </td>
                </tr>
                </tbody>
            </table>
        </div>
        <div class="ncua-btn-group">
            <ul class="mobile-history-list-info">
                <li>발송 대기 중인 예약발송건은 <a class="ncua-link" href="/crm/scheduled_send.php" target="_blank">예약 발송 관리</a>에서 확인할 수 있습니다.</li>
                <?php if ($isKakaoAlrimLunaInstalled): ?>
                    <li>블룸에이아이의 발송 내역은 <a class="ncua-link" href="<?= $blumnAiStatisticsUrl ?>" target="_blank">블룸에이아이 파트너스 센터</a>에서 확인 가능합니다.</li>
                <?php endif; ?>
            </ul>
            <span class="mobile-history-list-search-btns">
                <button type="reset" name="reset" class="ncua-btn ncua-btn--xs has-underline ncua-btn--text">초기화</button>
                <button type="submit" class="ncua-btn ncua-btn--xs ncua-btn--secondary">검색</button>
            </span>
        </div>
    </form>
    <div id="layerKakaoAlimTalkHistoryResult"></div>
</section>

<script>
    const datePicker = createCRMDatePicker({
        containerId: 'datepicker-container',
        startAttrName: 'dateFrom',
        endAttrName: 'dateTo',
    });
    attachDateValidation(datePicker, {
        maxRecentYear: 1
    });

    $(document).ready(function () {
        initializeEvents();
        loadContents(new FormData(document.getElementById('frmSearch')), true);
    });

    function initializeEvents() {
        const form = document.getElementById('frmSearch');
        form.addEventListener('submit', async (e) => {
            e.preventDefault(); // 기본 submit 막기

            const formData = new FormData(e.target);

            if (!formData.get('dateFrom') || !formData.get('dateTo')) {
                NCDSAlert({message: '발송일을 선택해 주세요.', iconType: 'error'});
                return;
            }

            loadContents(formData, true);
        });

        if (<?= json_encode($isKakaoAlrimAvailable) ?>) {
            form.querySelectorAll('input[name=sender]').forEach(radio => {
                radio.addEventListener('change', (e) => {
                    const checkboxAll = form.querySelector('#triggerTypeAll');
                    const checkboxes = form.querySelectorAll('input[name="triggerTypes[]"]');

                    if (e.target.value === 'kakaoAlrim') {
                        checkboxAll.checked = false;
                        checkboxAll.disabled = true;
                        checkboxes.forEach(checkbox => {
                            checkbox.checked = false;
                            checkbox.disabled = true;
                        });
                    } else {
                        checkboxAll.disabled = false;
                        checkboxes.forEach(checkbox => checkbox.disabled = false);
                        checkboxAll.click();
                    }
                });
            });
        }

        form.querySelector('button[type="reset"]')?.addEventListener('click', (e) => {
            setTimeout(() => {
                if (<?= json_encode($isSelectedBizm) ?>) {
                    form.querySelector('#triggerTypeAll').disabled = true;
                    form.querySelectorAll('input[name="triggerTypes[]"]').forEach(checkbox => {
                        checkbox.disabled = true;
                    });
                }

                Array.from(form.querySelectorAll('#datepicker-container .ncua-button-group__item')).find(el => el.textContent.includes('7'))?.click();

                const dateFromInput = form.querySelector('input[name="dateFrom"]');
                const dateToInput = form.querySelector('input[name="dateTo"]');

                if (!dateFromInput?.value || !dateToInput?.value) {
                    const today = new Date();
                    const sevenDaysAgo = new Date(today);
                    sevenDaysAgo.setDate(sevenDaysAgo.getDate() - 7);

                    if (dateFromInput) dateFromInput.value = sevenDaysAgo.toISOString().slice(0, 10);
                    if (dateToInput) dateToInput.value = today.toISOString().slice(0, 10);
                }

                form.querySelector('button[type="submit"]')?.click();
            }, 100);
        });

        ['.js-trigger-type', '.js-send-status'].forEach(selector => new CheckboxGroup(selector));
    }

    function loadContents(formData, isSearch = false) {
        const loadingModal = window.spinnerModal({ message: '생성 중...' });

        if (isSearch) {
            loadingModal.open();
        }

        const urlMap = {
            kakaoAlrim: '/crm/mobile_message_history/layer_member_bizm_kakao_alim_talk_history_result.php',
            default: '/crm/mobile_message_history/layer_member_cloud_kakao_alim_talk_history_result.php',
        };

        const url = urlMap[formData.get('sender')] || urlMap.default;

        formData.append('memberNo', <?= json_encode($memberNo) ?>);

        $.ajax({
            url: url,
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: function (data) {
                $('#layerKakaoAlimTalkHistoryResult').html(data);
            },
            complete: function () {
                if (isSearch) {
                    loadingModal.close();
                }
            },
            error: function () {
                NCDSAlert({ message: '일시적인 오류가 발생하였습니다.<br>다시 시도해 주세요.', iconType: 'error' });
            },
        });
    }
</script>
