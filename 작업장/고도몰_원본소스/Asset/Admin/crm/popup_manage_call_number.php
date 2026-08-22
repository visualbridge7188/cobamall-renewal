<link rel="stylesheet" href="<?= gd_set_browser_cache(PATH_ADMIN_GD_SHARE . 'ncds/css/crm/popup-manage-call-number.css') ?>">
<article class="ncua-content popup-manage-call-number">   
    <header class="page-header ncua-page-header">
        <h3>
            <?= end($naviMenu->location) ?>
        </h3>
    </header>
    <article class="ncua-content popup-manage-call-number__content">
        <!-- 안내사항 아코디언 -->
        <details class="ncua-accordion ncua-accordion--blue" open>
            <summary>안내사항</summary>
            <div class="ncua-accordion__content">
                <li class="ncua-notice-info">발신번호 등록은 <a href="https://www.nhn-commerce.com/mygodo/sms/dashboard.gd" target="_blank">NHN커머스 > 마이페이지 > SMS발신번호 관리</a>에서 가능합니다.</li>
                <li class="ncua-notice-info">발신번호 등록 단계: 등록 → 심사중 → 인증 또는 반려</li>
                <li class="ncua-notice-info">반려가 된 발신번호는 ‘반려 사유’를 클릭해 반려된 사유를 확인하시기 바랍니다.</li>
                <li class="ncua-notice-info">발신번호 관리 담당자가 변경된 경우, <a href="https://www.nhn-commerce.com/mygodo/sms/dashboard.gd" target="_blank">NHN커머스 > 마이페이지 > SMS발신번호 관리</a>에서 관리책임자 해제 후 발신번호를 다시 등록해주시기 바랍니다.</li>
            </div>
        </details>
        <div class="ncua-table ncua-table--vertical ncua-info-table">
            <table>
                <colgroup>
                    <col width="144px">
                    <col >
                    <col width="144px">
                    <col >
                </colgroup>
                <tr>
                    <th><div>관리책임자</div></th>
                    <td>
                        <div>
                            <span><?= $caller->getCallerName() ?></span>
                        </div>
                    </td>
                    <th><div>인증 구분</div></th>
                    <td>
                        <div>
                            <span><?= $caller->isAgent() ? '대리인' : '대표자 본인' ?></span>
                        </div>
                    </td>
                </tr>
            </table>
        </div>

        <!-- 발신번호 리스트 섹션 -->
        <section class="send-number-section">
            <header>
                <h4 class="ncua-flex">발신번호 리스트</h4>
            </header>
            <div id="layerManageCallNumberResult"></div>
        </section>
    </article>

    <!-- 하단 버튼 -->
    <div class="ncua-content-footer">
        <button type="button" id="registerSendNumber" class="ncua-btn ncua-btn--sm ncua-btn--secondary-gray">
            <span class="ncua-btn__label">추가 등록</span>
        </button>
        <button type="button" id="saveSendNumber" class="ncua-btn ncua-btn--sm ncua-btn--primary">
            <span class="ncua-btn__label">발신 번호 설정</span>
        </button>
    </div>
</article>
<script>

    function saveCallNumber(callNumber) {
        $.post('./popup_manage_call_number_ps.php', {mode: 'setCallNumber', callNumber: callNumber}, function (response) {
            if (response.error) {
                NCDSAlert({ message: response.error.message, iconType: 'error' });
                return;
            }

            if (response.success) {
                if (window.opener && window.opener.NCDSToast) {
                    window.opener.NCDSToast({message: '발신번호 설정이 완료되었습니다.', color: 'success'});
                }

                window.close();
            }
        });
    }

    function initPopupManageCallNumber() {
        loadContents();

        // 추가 등록 버튼
        document.querySelector('#registerSendNumber')?.addEventListener('click', function (e) {
            e.preventDefault();
            window.open('https://www.nhn-commerce.com/mygodo/sms/dashboard.gd', '_blank');
        });

        // 발신 번호 설정 버튼
        document.querySelector('#saveSendNumber').addEventListener('click', function (e) {
            e.preventDefault();
            const selectedCallNumber = document.querySelector('input[name="callNumber"]:checked');
            if(!selectedCallNumber) {
                NCDSAlert({ message: '발신번호를 선택해 주세요.', iconType: 'error'});
                return;
            }
            saveCallNumber(selectedCallNumber.value);
        });
   }

    function loadContents(page = 1) {
        $.ajax({
            url: './message_config/layer_manage_call_number_result.php',
            type: 'POST',
            data: { page: page },
            success: function (data) {
                $('#layerManageCallNumberResult').html(data);
            }
        });
    }


    const DOM_READY_STATES = ['complete', 'interactive'];
    
    const onReady = () => {
        try {
            initPopupManageCallNumber();
        } catch (error) {
            NCDSAlert({ message: e.message, iconType: 'error' });
        }
    };
    
    if (DOM_READY_STATES.includes(document.readyState)) {
        onReady();
    } else {
        document.addEventListener('DOMContentLoaded', onReady);
    }
</script>
