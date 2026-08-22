<link rel="stylesheet" href="<?= gd_set_browser_cache(PATH_ADMIN_GD_SHARE . 'ncds/css/crm/layer-reject-number-list.css') ?>">
<script defer src="<?= gd_set_browser_cache(PATH_ADMIN_GD_SHARE . 'ncds/script/common/spinner-modal.js') ?>"></script>

<article class="ncua-content layer-reject-number-list">
    <!-- 검색 영역 -->
    <section class="layer-reject-number-list__wrapper">
        <section class="layer-reject-number-list__content">
            <div class="ncua-table ncua-table--vertical">
                <table>
                    <colgroup>
                        <col width="144px">
                        <col >
                    </colgroup>
                    <tr>
                        <th><div>수신거부 번호</div></th>
                        <td>
                            <div class="ncua-flex ncua-gap-4">
                                <div class="ncua-input ncua-input--xs ncua-input-width-320">
                                    <div class="ncua-input__content">
                                        <div class="ncua-input__field ncua-input__field--xs">
                                            <input type="text" name="keyword" placeholder="검색하려는 번호를 입력하세요">
                                        </div>
                                    </div>
                                </div>
                                <button type="button" class="ncua-btn ncua-btn--xs ncua-btn--secondary" data-click-target="search">
                                    <span class="ncua-btn__label">검색</span>
                                </button>
                            </div>
                        </td>
                    </tr>
                </table>
            </div>
            <!-- 테이블 영역 -->
            <div id="layerRejectNumberListResult"></div>
        </section>

        <!-- 하단 버튼 -->
        <div class="ncua-content-footer">
            <button type="button" class="ncua-btn ncua-btn--sm ncua-btn--primary" data-click-target="sync">
                <span class="ncua-btn__label">회원 정보와 동기화</span>
            </button>
        </div>
    </section>

</article>

<script type="text/javascript">
    const DOM_READY_STATES = ['complete', 'interactive'];
    
    const onReady = () => {
        try {
            initializeEvents();

            loadContents();
        } catch (error) {
            console.error('초기화 실패:', error);
        }
    };

    function initializeEvents() {
        const layerSection = document.querySelector('.layer-reject-number-list');

        // 검색 버튼
        layerSection.querySelector('button[data-click-target=search]')?.addEventListener('click', () => {
            const searchValue = layerSection.querySelector('input[name=keyword]')?.value;
            loadContents(1, searchValue);
        });

        // 회원 정보와 동기화 버튼
        layerSection.querySelector('button[data-click-target=sync]')?.addEventListener('click', () => {
            checkActivation();
        });
    }

    function loadContents(page = 1, keyword = '') {
        $.ajax({
            url: './message_config/layer_reject_number_list_result.php',
            type: 'POST',
            data: { page: page, keyword: keyword },
            success: function (data) {
                $('#layerRejectNumberListResult').html(data);
            }
        });
    }

    function checkActivation() {
        $.post('./message_config/layer_reject_number_list_ps.php', {mode: 'checkActivation'}, function(response) {
            if (response.error) {
                NCDSAlert({ message: '일시적인 오류가 발생하였습니다.<br>다시 시도해 주세요.', 'iconType': 'error' });
                return;
            }

            if (!response.success) {
                NCDSAlert({ message: '080 수신거부 번호를 먼저 개통 완료한 후<br>회원정보와 동기화해 주세요.', 'iconType': 'error' });
                return;
            }

            manualSync();
        }, 'json');
    }

    function manualSync() {
        const loadingModal = window.spinnerModal({ message: '동기화 중...' });
        loadingModal.open();

        $.post('./message_config/layer_reject_number_list_ps.php', {mode: 'manualSync'}, function(response) {
            loadingModal.close();

            if (response.error) {
                NCDSAlert({ message: '일시적인 오류가 발생하였습니다.<br>다시 시도해 주세요.', 'iconType': 'error' });
                return;
            }

            if (!response.success) {
                NCDSAlert({ message: '회원 정보 동기화에 실패하였습니다.<br>다시 시도해 주세요.', 'iconType': 'error' });
                return;
            }

            if (window.NCDSToast) {
                window.NCDSToast({message: '동기화가 완료되었습니다.', color: 'success'});
            }
            layer_close();
        }, 'json');
    }
    
    if (DOM_READY_STATES.includes(document.readyState)) {
        onReady();
    } else {
        document.addEventListener('DOMContentLoaded', onReady);
    }
</script>
