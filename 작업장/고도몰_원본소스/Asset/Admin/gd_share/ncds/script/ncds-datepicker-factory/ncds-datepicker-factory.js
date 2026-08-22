/**
 * DatePicker Factory - CRM 공통 날짜 선택기 생성 유틸리티
 *
 * @param {Object} options
 * @param {string} options.containerId - DatePicker를 렌더링할 컨테이너 ID (기본값: 'datepicker-container')
 * @param {string} options.attrName - input의 name 속성 (기본값: 'regDt[]')
 * @param {string} options.size - DatePicker 크기 (기본값: 'xs')
 * @param {Array} options.buttons - 기간 버튼 설정 (null이면 기본 버튼 사용)
 * @param {number} options.maxMonthRange - 최대 검색 가능 개월 수 (기본값: 3)
 * @param {boolean} options.enableTime - 시간 선택 활성화 (기본값: false)
 * @param {boolean} options.enableSeconds - 초 선택 활성화 (기본값: false)
 * @param {Object} options.startDateOptions - 시작일 개별 옵션 (enableTime, enableSeconds 등)
 * @param {Object} options.endDateOptions - 종료일 개별 옵션 (enableTime, enableSeconds 등)
 * @param {Array|Object} options.datePickerOptions - 외부에서 직접 datePickerOptions 설정 (1개 또는 2개 가능, 기본값: null)
 * @returns {Object} DatePicker 인스턴스
 *
 * @example
 * // 기본 설정으로 DatePicker 생성
 * const datePicker = createCRMDatePicker();
 *
 * @example
 * // 시분초 포함 DatePicker 생성
 * const datePicker = createCRMDatePicker({
 *     enableTime: true,
 *     enableSeconds: true,
 * });
 *
 * @example
 * // 외부에서 datePickerOptions 직접 설정 (1개만)
 * const datePicker = createCRMDatePicker({
 *     datePickerOptions: [{
 *         element: 'single-date',
 *         attrName: 'regDt',
 *         options: {
 *             mode: 'single',
 *             dateFormat: 'Y-m-d',
 *             locale: 'ko'
 *         }
 *     }]
 * });
 *
 * @example
 * // 버튼 없이, 다른 attrName으로 생성
 * const datePicker = createCRMDatePicker({
 *     containerId: 'datepicker-custom-container',
 *     attrName: 'searchDate[]',
 *     buttons: [], // 버튼 없음
 *     maxMonthRange: 6, // 6개월까지 허용
 * });
 *
 * @example
 * // attrName을 따로 설정하기
 *
 * const datePicker = createCRMDatePicker({
 *     statAttrName: 'date1[]',
 *     endAttrName: 'date2[]
 * });
 *
 * @example
 * // 종료일에만 시분초 적용
 * const datePicker = createCRMDatePicker({
 *     endDateOptions: {
 *         enableTime: true,
 *         enableSeconds: true,
 *     }
 * });
 *
 * @example
 * // 커스텀 버튼으로 생성
 * const datePicker = createCRMDatePicker({
 *     buttons: [
 *         { text: '오늘', period: 0, unit: 'days', isCurrent: true },
 *         { text: '1주일', period: 6, unit: 'days', isCurrent: false },
 *     ]
 * });
 */
function createCRMDatePicker(options = {}) {
    const {
        containerId = 'datepicker-container',
        attrName = 'regDt[]',
        startAttrName,
        endAttrName,
        size = 'xs',
        buttons = null,
        maxMonthRange = 3,
        enableTime = false,
        enableSeconds = false,
        startDateOptions = {},
        endDateOptions = {},
        datePickerOptions = null
    } = options;

    //   시간 옵션에 따른 dateFormat 결졍
    const getDateFormat = (hasTime, hasSeconds) => {
        if (hasTime && hasSeconds) return 'Y-m-d H:i:S';
        if (hasTime) return 'Y-m-d H:i';

        return 'Y-m-d';
    };

    // 개별 날짜 옵션 생성
    const createDateOptions = (customOptions) => {
        const hasTime = customOptions.enableTime ?? enableTime;
        const hasSeconds = customOptions.enableSeconds ?? enableSeconds;

        return {
            mode: 'single',
            static: true,
            dateFormat: customOptions.dateFormat ?? getDateFormat(hasTime, hasSeconds),
            clickOpens: true,
            allowInvalidPreload: true,
            allowInput: true,
            locale: 'ko',
            ...(hasTime && {
                enableTime: true,
                time_24hr: true
            }),
            ...(hasSeconds && {enableSeconds: true}),
            ...customOptions
        };
    };

    const defaultButtons = [
        { text: '오늘', period: 0, unit: 'days', isCurrent: false },
        { text: '7일', period: 6, unit: 'days', isCurrent: true },
        { text: '15일', period: 14, unit: 'days', isCurrent: false },
        { text: '1개월', period: 29, unit: 'days', isCurrent: false },
        { text: '3개월', period: 89, unit: 'days', isCurrent: false },
    ];

    // datePickerOptions가 외부에서 제공되면 사용, 없으면 기본값 생성
    let finalDatePickerOptions;
    if (datePickerOptions) {
        // 외부에서 받은 옵션 사용 (1개 또는 2개 모두 가능)
        const rawOptions = Array.isArray(datePickerOptions)
            ? datePickerOptions
            : [datePickerOptions];

        // 각 옵션의 options에 기본값 병합
        finalDatePickerOptions = rawOptions.map(option => ({
            ...option,
            options: createDateOptions(option.options || {})
        }));
    } else {
        // 기본값: start-date와 end-date 2개 생성
        finalDatePickerOptions = [
            {
                element: 'start-date',
                attrName: startAttrName ?? attrName,
                options: createDateOptions(startDateOptions),
            },
            {
                element: 'end-date',
                attrName: endAttrName ?? attrName,
                options: createDateOptions(endDateOptions),
            },
        ];
    }

    const datePickerConfig = {
        size,
        buttons: buttons || defaultButtons,
        datePickerOptions: finalDatePickerOptions,
    };

    const wrapper = document.querySelector(`#${containerId}`);
    if (!wrapper) {
        console.error(`DatePicker container #${containerId} not found`);
        return null;
    }

    // 기존 DatePicker 인스턴스가 있으면 제거 (이 부분 추가)
    if (wrapper.datePickerInstance) {
        try {
             // destroy 호출
            if (typeof wrapper.datePickerInstance.destroy === 'function') {
                wrapper.datePickerInstance.destroy();
            }
        } catch (e) {
            console.warn('기존 DatePicker 제거 중 오류:', e);
        }
        wrapper.datePickerInstance = null;
    }

   // DOM 요소를 완전히 재생성
    const parent = wrapper.parentNode;
    const id = wrapper.id;
    const className = wrapper.className;
    const style = wrapper.getAttribute('style');

    // 새 요소 생성
    const newWrapper = document.createElement('div');
    newWrapper.id = id;
    if (className) newWrapper.className = className;
    if (style) newWrapper.setAttribute('style', style);

    // 기존 요소를 새 요소로 교체
    parent.replaceChild(newWrapper, wrapper);

    // 새 DatePicker 인스턴스 생성
    const datePicker = new ncua.DatePicker(newWrapper, datePickerConfig);
    newWrapper.datePickerInstance = datePicker;

    return datePicker;
}

/**
 * DatePicker에 날짜 범위 검증 연결
 *
 * @param {Object} datePicker - ncua.DatePicker 인스턴스
 * @param {Object} options - 검증 옵션
 * @param {string} options.containerId - DatePicker 컨테이너 ID (기본값: 'datepicker-container')
 * @param {Object} options.messages - 커스텀 에러 메시지
 * @param {string} options.messages.overRange - 범위 초과 메시지
 * @param {string} options.messages.startAfterEnd - 시작일 > 종료일 메시지
 * @param {string} options.messages.endBeforeStart - 종료일 < 시작일 메시지
 * @param {string} options.messages.sameDate - 시작일 = 종료일 메시지
 * @param {number} options.maxMonthRange - 최대 검색 가능 개월 수 (기본값: 3)
 * @param {boolean} options.allowSameStartEndDate - 시작일과 종료일이 같은 것을 허용할지 여부 (기본값: true)
 * @returns {Function} 검증 해제 함수
 *
 * @example
 * const datePicker = createCRMDatePicker();
 * const detach = attachDateValidation(datePicker);
 *
 * // 검증 해제가 필요할 때
 * detach();
 *
 * @example
 * // 6개월 범위로 검증
 * attachDateValidation(datePicker, { maxMonthRange: 6 });
 *
 * @example
 * attachDateValidation(datePicker, {
 *     maxMonthRange: 6,
 *     messages: {
 *         overRange: '최대 6개월까지 조회 가능합니다.',
 *         startAfterEnd: '시작일을 확인해주세요.',
 *         endBeforeStart: '종료일을 확인해주세요.',
 *     }
 * });
 */

function attachDateValidation(datePicker, options = {}) {
    if (!datePicker) {
        console.error('DatePicker instance는 필수 입니다.');
        return () => {};
    }

    const {
        containerId = 'datepicker-container',
        maxRecentYear,
        maxMonthRange = 3,
        allowSameStartEndDate = true,
        messages = {}
    } = options;

    const wrapper = document.querySelector(`#${containerId}`);

    if (!wrapper) {
        console.error(`DatePicker container #${containerId} not found`);
        return () => {};
    }

    let beforeDates = datePicker.getDates();

    const ERR_MESSAGE = {
        OVER_YEAR: messages.overYear ?? `최대 ${maxRecentYear}년 이전까지의 내역만 검색 가능합니다.`,
        OVER_RANGE: messages.overRange ?? `검색 가능한 범위는 최대 ${maxMonthRange}개월입니다.`,
        START_AFTER_END: messages.startAfterEnd ?? '시작일자는 종료일자보다 이후일 수 없습니다.',
        END_BEFORE_START: messages.endBeforeStart ?? '종료일자는 시작일자보다 이전일 수 없습니다.',
        SAME_DATE: messages.sameDate ?? '종료일자는 시작일자와 같을 수 없습니다.',
    };

    const handleValidationError = (message) => {
        NCDSAlert({ message, iconType: 'error' });
        datePicker.setDate(beforeDates);
    };

    const validateDateRange = (startDate, endDate, changedInputId) => {
        const isStartDateChanged = changedInputId.includes('start-date');
        const isEndDateChanged = changedInputId.includes('end-date');
        const dayDiff = endDate.diff(startDate, 'day');
        const maxDay = maxMonthRange !== null ? maxMonthRange * 30 - 1 : null;

        if (maxRecentYear) {
            const minDate = moment().subtract(maxRecentYear, 'years');

            if (startDate.isBefore(minDate) || endDate.isBefore(minDate)) {
                handleValidationError(ERR_MESSAGE.OVER_YEAR);
                return false;
            }
        }

        if (maxDay !== null && dayDiff > maxDay) {
            handleValidationError(ERR_MESSAGE.OVER_RANGE);
            return false;
        }

        if (isStartDateChanged && startDate.isAfter(endDate)) {
            handleValidationError(ERR_MESSAGE.START_AFTER_END);
            return false;
        }

        if (isEndDateChanged && endDate.isBefore(startDate)) {
            handleValidationError(ERR_MESSAGE.END_BEFORE_START);
            return false;
        }

        if (!allowSameStartEndDate && startDate.isSame(endDate, 'day')) {
            handleValidationError(ERR_MESSAGE.SAME_DATE);
            return false;
        }

        return true;
    };

    const handleChange = (e) => {
        const dateList = datePicker.getDates();
        const startDate = moment(dateList[0]);
        const endDate = moment(dateList[1]);
        const changedInputId = e.target.id;

        if (validateDateRange(startDate, endDate, changedInputId)) {
            beforeDates = datePicker.getDates();
        }
    };

    wrapper.addEventListener('change', handleChange);

        // 검증 해제 함수 반환
    return () => wrapper.removeEventListener('change', handleChange);
}

// 전역으로 노출
window.createCRMDatePicker = createCRMDatePicker;
window.attachDateValidation = attachDateValidation;
