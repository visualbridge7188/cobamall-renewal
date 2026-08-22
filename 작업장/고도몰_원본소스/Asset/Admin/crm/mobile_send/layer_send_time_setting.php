<section class="js-send-time-setting-section">
    <header class="ncua-card__header">
        <h4 class="ncua-card__title">
            발송 설정
        </h4>
    </header>
    <section class="ncua-card__body">
        <!-- 발송 설정 라디오 버튼 -->
        <div class="ncua-border-group-box">
            <?php foreach ($messageSendTypes as $messageSendType): ?>
                <label class="ncua-radio-field ncua-radio-field--xs has-text">
                    <span class="ncua-radio-input ncua-radio-input--xs ncua-radio-field__input">
                        <input name="sendSetting" type="radio" value="<?= $messageSendType->name ?>" <?= $messageSendType->name === 'IMMEDIATE' ? 'checked="checked"' : '' ?>/>
                    </span>
                    <span>
                        <span class="ncua-radio-field__text"><?= $messageSendType->value ?></span>
                    </span>
                </label>
            <?php endforeach; ?>
        </div>
        <div class="send-time-setting-content display-none js-send-time-component" data-target="SCHEDULED,REPEAT">
            <!-- 예약 발송 설정 -->
            <div class="send-setting-reserved display-none js-send-time-component" data-target="SCHEDULED">
                <div class="send-setting-reserved-date">
                    <div id="datepicker-reserved-send-container"></div>
                    <span class="ncua-select ncua-select--xs">
                        <span class="ncua-select__content">
                            <select name="reserveHour" class="ncua-select__tag">
                                <?php for ($i = 8; $i <= 20; $i++): ?>
                                    <option value="<?= sprintf('%02d', $i) ?>">
                                        <?= sprintf('%02d', $i) ?>
                                    </option>
                                <?php endfor; ?>
                            </select>
                        </span>
                    </span>
                    <span>시</span>
                    <span class="ncua-select ncua-select--xs">
                        <span class="ncua-select__content">
                            <select name="reserveMinutes" class="ncua-select__tag">
                                <?php for ($i = 0; $i <= 59; $i++): ?>
                                    <option value="<?= sprintf('%02d', $i) ?>">
                                        <?= sprintf('%02d', $i) ?>
                                    </option>
                                <?php endfor; ?>
                            </select>
                        </span>
                    </span>
                    <span>분 부터 순차발송</span>
                </div>
                <ul class="send-setting-repeat-notice">
                    <li class="ncua-notice-info">예약 발송은 현재 시간으로부터 10분 이후로만 예약이 가능합니다.</li>
                </ul>
            </div>

            <!-- 반복 발송 설정 -->
            <div class="send-setting-repeat display-none js-send-time-component" data-target="REPEAT">
                <div class="send-setting-repeat-date">
                    <div id="datepicker-repeat-send-container"></div>
                    <label class="ncua-checkbox-field ncua-checkbox-field--xs has-text">
                        <span class="ncua-checkbox-input ncua-checkbox-input--xs ncua-checkbox-field__input">
                            <input name="hasNoEndDate" type="checkbox" />
                        </span>
                        <span class="ncua-checkbox-field__text">종료일 없음</span>
                    </label>
                </div>
                <ul class="send-setting-repeat-notice">
                    <li class="ncua-notice-info">반복 발송은 현재 시간으로부터 10분 이후로만 설정이 가능합니다.</li>
                </ul>
                <p class="ncua-card__body-title--sm tooltip-align" data-tooltip-seq="005">발송 주기</p>
                <div class="send-setting-repeat-cycle">
                    <div class="send-setting-repeat-cycle-select">
                        <span class="ncua-select ncua-select--xs">
                            <span class="ncua-select__content">
                                <select id="sendCycle" class="ncua-select__tag">
                                    <?php foreach ($messageRepeatCycles as $messageRepeatCycle): ?>
                                        <option value="<?= $messageRepeatCycle->name ?>"><?= $messageRepeatCycle->value ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </span>
                        </span>
                        <span class="ncua-select ncua-select--xs">
                            <span class="ncua-select__content">
                                <select name="repeatHour" class="ncua-select__tag">
                                    <?php for ($i = 8; $i <= 20; $i++): ?>
                                        <option value="<?= sprintf('%02d', $i) ?>">
                                            <?= sprintf('%02d', $i) ?>
                                        </option>
                                    <?php endfor; ?>
                                </select>
                            </span>
                        </span>
                            <span>시</span>
                            <span class="ncua-select ncua-select--xs">
                            <span class="ncua-select__content">
                                <select name="repeatMinutes" class="ncua-select__tag">
                                    <?php for ($i = 0; $i <= 50; $i += 10): ?>
                                        <option value="<?= sprintf('%02d', $i) ?>">
                                            <?= sprintf('%02d', $i) ?>
                                        </option>
                                    <?php endfor; ?>
                                </select>
                            </span>
                        </span>
                        <span>분에 반복 발송</span>
                    </div>

                    <!-- 매주 발송 설정 -->
                    <div class="send-cycle-weekly js-repeat-cycle-component js-weekly-day" data-target="WEEKLY">
                        <label class="ncua-checkbox-field ncua-checkbox-field--xs has-text">
                            <span class="ncua-checkbox-input ncua-checkbox-input--xs ncua-checkbox-field__input">
                                <input type="checkbox" value="all" />
                            </span>
                            <span class="ncua-checkbox-field__text">전체</span>
                        </label>
                        <?php foreach ($weekDays as $weekDay): ?>
                            <label class="ncua-checkbox-field ncua-checkbox-field--xs has-text">
                                <span class="ncua-checkbox-input ncua-checkbox-input--xs ncua-checkbox-field__input">
                                    <input type="checkbox" value="<?= $weekDay->value ?>" name="repeatDays[]" />
                                </span>
                                <span class="ncua-checkbox-field__text"><?= $weekDay->label() ?></span>
                            </label>
                        <?php endforeach; ?>
                    </div>

                    <!-- 매월 발송 설정 -->
                    <div class="send-cycle-monthly js-repeat-cycle-component" data-target="MONTHLY">
                        <div class="ncua-input ncua-input--xs ncua-input-width-320">
                            <div class="ncua-input__content">
                                <div class="ncua-input__field ncua-input__field--xs ">
                                    <input name="monthly-repeat-date" type="text" inputmode="numeric" pattern="[0-9]*" value="" placeholder="일자(1~31) 숫자 입력" />
                                </div>
                            </div>
                        </div>
                        <span class="send-cycle-monthly-notice">5개까지 생성 가능합니다 (Enter로 구분)</span>
                    </div>
                </div>
                <div class="send-tag-wrap" data-component="send-cycle-monthly">
                    <div id="repeatDatesContainer" class="send-tag-content" data-role="tag-container"></div>
                    <button type="button" data-click-target="deleteMonthlyDate" class="ncua-btn ncua-btn--xxs ncua-btn--text-gray has-underline">전체삭제</button>
                </div>
            </div>
        </div>
    </section>
</section>

<script type="text/javascript">
    const sendTimeSettingSection = document.querySelector('.js-send-time-setting-section');

    $(document).ready(function () {
        initializeSendTimeSettingSectionEvents();
        initializeSendTimeSettingSectionElement();
    });

    function initializeSendTimeSettingSectionEvents() {
        const reserveHourSelect = sendTimeSettingSection.querySelector('select[name="reserveHour"]');
        const reserveMinutesSelect = sendTimeSettingSection.querySelector('select[name="reserveMinutes"]');
        const repeatHourSelect = document.querySelector('select[name="repeatHour"]');
        const repeatMinutesSelect = document.querySelector('select[name="repeatMinutes"]');
        const monthlyInput = sendTimeSettingSection.querySelector('input[name="monthly-repeat-date"]');
        const tagContainer = sendTimeSettingSection.querySelector('#repeatDatesContainer');

        sendTimeSettingSection.querySelectorAll('input[name="sendSetting"]').forEach(radio => {
            radio.addEventListener('change', function() {
                toggleComponentBySendType(radio.value);

                switch (radio.value) {
                    case 'SCHEDULED':
                        reserveHourSelect.selectedIndex = 0;
                        reserveMinutesSelect.selectedIndex = 0;
                        break;
                    case 'REPEAT':
                        const sendCycleSelect = sendTimeSettingSection.querySelector('#sendCycle');
                        sendCycleSelect.dispatchEvent(new Event('change'));
                        break;
                }
            });
        });

        reserveHourSelect.addEventListener('change', function() {
            updateMinutesOptions(reserveHourSelect, reserveMinutesSelect)
        });

        repeatHourSelect.addEventListener('change', function() {
            updateMinutesOptions(repeatHourSelect, repeatMinutesSelect)
        });

        sendTimeSettingSection.querySelector('#sendCycle').addEventListener('change', function() {
            toggleComponentBySendCycle(this.value);
            sendTimeSettingSection.querySelector('#repeatDatesContainer').innerHTML = '';

            switch (this.value) {
                case 'WEEKLY':
                    sendTimeSettingSection.querySelectorAll('.js-weekly-day input[type="checkbox"]').forEach(el => el.checked = false);
                    break;
                case 'MONTHLY':
                    monthlyInput.value = '';
                    break;
            }
        });

        // 2. Enter 키로 태그 생성
        monthlyInput.addEventListener('keydown', function(e) {
            if (e.key === 'Enter') {
                e.preventDefault();

                const date = parseInt(this.value);

                // 유효성 검사: 1~31 범위
                if (!date || date < 1 || date > 31) {
                    NCDSAlert({message: '1~31 사이의 숫자를 입력해주세요.', iconType: 'error'});
                    return;
                }

                // 최대 개수 체크
                const currentTags = tagContainer.querySelectorAll('.ncua-tag');
                if (currentTags.length >= 5) {
                    NCDSAlert({message: `최대 5개까지만 생성 가능합니다.`, iconType: 'error'});
                    return;
                }

                // 3. 중복 체크
                const existingTag = tagContainer.querySelector(`[data-day="${date}"]`);
                if (existingTag) {
                    NCDSAlert({message: '이미 등록된 날짜입니다.', iconType: 'error'});
                    return;
                }

                // 태그 생성
                const tag = generateMonthlytag(date);
                tagContainer.appendChild(tag);
                this.value = ''; // 입력 필드 초기화
            }
        });

        sendTimeSettingSection.querySelector('button[data-click-target="deleteMonthlyDate"]').addEventListener('click', function() {
            const tags = tagContainer.querySelectorAll('.ncua-tag');
            tags.forEach(tag => tag.remove());
        });

        // 반복발송 종료일 없음 체크박스 이벤트
        sendTimeSettingSection.querySelector('input[name="hasNoEndDate"]').addEventListener('change', function() {
            const endDateInput = document.querySelectorAll('input[name="repeatDate[]"]')[1];

            if (this.checked) {
                if (endDateInput) {
                    endDateInput.disabled = true;
                    endDateInput.value = '';
                }
            } else {
                if (endDateInput) {
                    endDateInput.disabled = false;
                }
            }
        });
    }

    // 태그 생성 함수
    function generateMonthlytag(date) {
        const tag = document.createElement('span');
        tag.className = 'ncua-tag ncua-tag--sm';
        tag.setAttribute('data-day', date);
        tag.innerHTML = `
                    <span class="ncua-tag__text">${date}일</span>
                    <button type="button" class="ncua-tag__close" data-role="tag-close"></button>
                `;

        // 4. 개별 삭제 버튼 이벤트
        const closeBtn = tag.querySelector('[data-role="tag-close"]');
        closeBtn.addEventListener('click', function() {
            tag.remove();
        });
        return tag;
    }

    function toggleComponentBySendType(sendType) {
        sendTimeSettingSection.querySelectorAll('.js-send-time-component').forEach(el => {
            el.classList.add('display-none');
            const targets = el.dataset.target?.split(',') ?? [];
            if (targets.includes(sendType)) {
                el.classList.remove('display-none');
            }
        });
    }

    function toggleComponentBySendCycle(sendCycle) {
        sendTimeSettingSection.querySelectorAll('.js-repeat-cycle-component').forEach(el => {
            el.classList.add('display-none');
            const targets = el.dataset.target?.split(',') ?? [];
            if (targets.includes(sendCycle)) {
                el.classList.remove('display-none');
            }
        });
    }

    function updateMinutesOptions(hourSelect, minutesSelect) {
        const isHour20 = hourSelect.value === '20';
        minutesSelect.querySelectorAll('option').forEach(option => {
            const value = parseInt(option.value, 10);
            option.hidden = isHour20 && value >= 31;
        });

        if (isHour20 && parseInt(minutesSelect.value, 10) >= 31) {
            minutesSelect.value = '00';
        }
    }

    function initializeSendTimeSettingSectionElement() {
        document.querySelector('input[name="sendMethod"]:checked')?.dispatchEvent(new Event('change'));

        initReservedSendDatePicker(false);

        initRepeatSendDatePicker(false);

        ['.js-weekly-day'].forEach(selector => new CheckboxGroup(selector));
    }

    function initReservedSendDatePicker(isRestricted) {
        const reservedSendDatePicker = createCRMDatePicker({
            containerId: 'datepicker-reserved-send-container',
            buttons: [],
            datePickerOptions: [{
                element: 'start-date',
                attrName: 'reserveDate',
                options: {
                    defaultDate: moment().format(),
                    minDate: moment().format(),
                    maxDate: isRestricted ? moment().add(364, 'days').format() : null,
                    mode: 'single',
                    dateFormat: 'Y-m-d',
                    locale: 'ko'
                },
            }],
        });
    }

    function initRepeatSendDatePicker(isRestricted) {
        const startDateOptions = {
            defaultDate: moment().format(),
            minDate: moment().format(),
            maxDate: isRestricted ? moment().add(364, 'days').format() : null,
        };
        const endDateOptions = {
            defaultDate: moment().add(5, 'days').format(),
            minDate: moment().format(),
            maxDate: isRestricted ? moment().add(364, 'days').format() : null,
        };

        const repeatSendDatePicker = createCRMDatePicker({
            containerId: 'datepicker-repeat-send-container',
            attrName: 'repeatDate[]',
            buttons: [],
            startDateOptions,
            endDateOptions,
        });
        attachDateValidation(repeatSendDatePicker, {maxMonthRange: null, containerId: 'datepicker-repeat-send-container'});

        const noEndDateInput = sendTimeSettingSection.querySelector('input[name="hasNoEndDate"]');
        if (isRestricted) noEndDateInput.checked = false;
        noEndDateInput.disabled = isRestricted;
    }
</script>
<!-- 툴팁 스크립트 -->
<script defer type="text/javascript">
    const code = 251219003;
    if (window.GodoCosGuide) {
        window.GodoCosGuide.init({
            apiUrl: <?= json_encode($cosGuideApiUrl) ?>,
            guideCode: code,
        }).then((data) => {
            window.cosData = data;
        });
    }
</script>
