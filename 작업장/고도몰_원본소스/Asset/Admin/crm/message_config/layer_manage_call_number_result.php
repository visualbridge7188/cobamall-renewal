<!-- 내역 -->
<div class="ncua-search-result">
    <div class="ncua-table ncua-table--horizontal">
        <table class="popup-manage-call-number__product-table">
            <colgroup>
                <col width="79px">
                <col>
                <col>
                <col width="100px">
                <col>
                <col width="90px">
            </colgroup>
            <thead>
            <tr>
                <th><div>선택</div></th>
                <th><div>발신번호</div></th>
                <th><div>관리 명칭</div></th>
                <th><div>상태</div></th>
                <th><div>승인일</div></th>
                <th><div>관리</div></th>
            </tr>
            </thead>
            <tbody>
            <?php if (!empty($smsCallNumberList)): ?>
                <?php foreach ($smsCallNumberList as $smsCallNumber): ?>
                    <tr>
                        <td>
                            <div>
                                <?php if ($currentCallNumber !== $smsCallNumber->getCallNumber()): ?>
                                    <label class="ncua-radio-field ncua-radio-field--xs ncua-flex">
                                        <span class="ncua-radio-input ncua-radio-input--xs ncua-radio-field__input <?= $smsCallNumber->getStatus()->name !== 'AUTHENTICATED' ? 'is-disabled' : '' ?>">
                                            <input type="radio" name="callNumber" value="<?= $smsCallNumber->getCallNumber() ?>" <?= $smsCallNumber->getStatus()->name !== 'AUTHENTICATED' ? 'disabled' : '' ?>/>
                                        </span>
                                    </label>
                                <?php else: ?>
                                    <div>사용 중</div>
                                <?php endif; ?>
                            </div>
                        </td>
                        <td><div class="js-call-number" data-call-number-no="<?= $smsCallNumber->getCallNumberNo() ?>"><?= $smsCallNumber->getCallNumber() ?></div></td>
                        <td><div><?= $smsCallNumber->getTitle() ?></div></td>
                        <td><div><?= $smsCallNumber->getStatus()->value ?></div></td>
                        <td><div><?= $smsCallNumber->getApprovedAt() ?? '-' ?></div></td>
                        <td>
                            <?php if (in_array($smsCallNumber->getStatus()->name, ['REGISTER', 'AUTHENTICATED'])): ?>
                                <div>
                                    <button type="button" class="ncua-btn ncua-btn--xxs ncua-btn--secondary-gray" data-call-number-no="<?= $smsCallNumber->getCallNumberNo() ?>" data-manage-status="<?= $smsCallNumber->getStatus()->name ?>">
                                        <span class="ncua-btn__label">삭제</span>
                                    </button>
                                </div>
                            <?php elseif ($smsCallNumber->getStatus()->name === 'CANCEL'): ?>
                                <div>
                                    <input type="hidden" id="deniedReason<?= $smsCallNumber->getCallNumberNo() ?>" value="<?= $smsCallNumber->getDeniedReason() ?>" />
                                    <button type="button" class="ncua-btn ncua-btn--xxs ncua-btn--secondary-gray" data-call-number-no="<?= $smsCallNumber->getCallNumberNo() ?>" data-manage-status="<?= $smsCallNumber->getStatus()->name ?>">
                                        <span class="ncua-btn__label">반려사유</span>
                                    </button>
                                </div>
                            <?php else: ?>
                                <div>-</div>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr>
                    <td colspan="6">
                        <div class="no-data">
                            <span>등록된 발신번호가 없습니다.</span>
                        </div>
                    </td>
                </tr>
            <?php endif; ?>
            </tbody>
        </table>
    </div>
    <div class="ncua-table-bottom"></div>
    <!-- 페이지네이션 -->
    <div class="ncua-pagination"><?= $page->getPage('loadContentsByPage(\'PAGELINK\')') ?>
</div>

<script type="text/javascript">
    const callerNo = <?= $caller->getCallerNo() ?>;
    $(document).ready(function () {
        // 발신번호 상태 클릭 이벤트
        document.querySelectorAll('[data-manage-status]')?.forEach( element => {
            element.addEventListener('click', function (e) {
                const manageStatus = this.getAttribute('data-manage-status');

                switch (manageStatus) {
                    case 'REGISTER':
                    case 'AUTHENTICATED':
                        NCDSConfirm({
                            message: '발신번호를 삭제하시겠습니까?',
                            subMessage: '발신번호 삭제 시, 다시 복구되지 않습니다.<br/>쇼핑몰에 연동된 발신번호가 없을 경우 자동알림을 포함한 모바일 메시지가 발송되지 않습니다.',
                            btnText: {
                                confirmLabel: '확인',
                                cancelLabel: '취소'
                            },
                            callback: (type) => {
                                if (type) {
                                    const callNumberNo = element.getAttribute('data-call-number-no');
                                    deleteCallNumber(callNumberNo);
                                }
                            }
                        });
                        break;
                    case 'CANCEL':
                        const callNumberNo = element.getAttribute('data-call-number-no');
                        const deniedReason = document.getElementById(`deniedReason${callNumberNo}`)?.value ?? '';
                        NCDSAlert({ message: '발신번호 반려 사유', subMessage: deniedReason, iconType: 'warning' });
                        break;
                    default:
                        break;
                }
            });
        });
    });

    function deleteCallNumber(callNumberNo) {
        var callNumber = document.querySelector(`.js-call-number[data-call-number-no='${callNumberNo}']`).innerText;
        $.post('./popup_manage_call_number_ps.php', {mode: 'deleteCallNumber', callerNo: callerNo, callNumberNo: callNumberNo, callNumber: callNumber}, function (response) {
            if (response.error || (!response.success && !response.data)) {
                NCDSAlert({ message: '일시적인 오류가 발생하였습니다.<br>다시 시도해 주세요.', iconType: 'error' });
                return;
            }

            if (!response.success && response.data.type === 'conflict') {
                NCDSAlert({
                    message: '다른 쇼핑몰 어드민에서 사용되고 있어 삭제할 수 없습니다.',
                    subMessage: 'NHN 커머스 마이페이지 > SMS 발신번호 관리에서 삭제하시기 바랍니다.',
                    iconType: 'error',
                });
                return;
            }

            NCDSAlert({
                message: '발신번호 삭제가 완료되었습니다.',
                iconType: 'success',
                callback: function () {
                    window.location.reload();
                }
            });
        });
    }

    function loadContentsByPage(page) {
        const pageNumber = parseInt(page.split('=')[1], 10);
        loadContents(pageNumber);
    }
</script>
