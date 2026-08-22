// 필요 전역: $, NCDSToast
window.CrmGroupStatus = (function () {
    'use strict';

    const STATUS = {
        REQUEST: 'REQUEST',
        PROCESS: 'PROCESS',
        DONE: 'DONE',
        FAIL: 'FAIL',
        CANCEL: 'CANCEL',
    };

    // CRM 그룹 최대 등록 개수
    const MAX_COUNT = 20;

    function getInfo(crmGroupNo) {
        return $.ajax({
            url: './mobile_send/layer_recipient_setting_ps.php',
            type: 'GET',
            data: { mode: 'getCrmGroupStatus', crmGroupNo: crmGroupNo },
        });
    }

    // 현재 등록된 CRM 그룹 총개수 조회 (실패 시 reject)
    function getCount() {
        return $.ajax({
            url: './mobile_send/layer_select_crm_groups_ps.php',
            type: 'GET',
            data: { mode: 'getCrmGroups' },
            dataType: 'json',
        }).then(function (response) {
            return response?.data?.totalCount ?? 0;
        });
    }

    function errorToast() {
        return { message: '일시적인 오류가 발생하였습니다. 다시 시도해 주세요.', color: 'error' };
    }

    function toast(status) {
        if (status === STATUS.DONE) {
            return { message: '데이터 추출이 완료되었습니다.', color: 'success' };
        }
        if (status === STATUS.FAIL || status === STATUS.CANCEL) {
            return errorToast();
        }
        return { message: '데이터 추출이 진행 중입니다. 잠시만 기다려주세요.', color: 'error' };
    }

    function toggleView(sectionEl, status) {
        const isDone = status === STATUS.DONE;
        sectionEl.querySelector('.js-crm-group-extraction').classList.toggle('display-none', isDone);
        sectionEl.querySelector('.js-recipient-count-text').classList.toggle('display-none', !isDone);
    }

    return { STATUS, MAX_COUNT, getInfo, getCount, toast, errorToast, toggleView };
})();
