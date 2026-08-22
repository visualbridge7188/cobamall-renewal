<link rel="stylesheet" href="<?= gd_set_browser_cache(PATH_ADMIN_GD_SHARE . 'ncds/css/crm/layer-caller-connector.css') ?>">
<script defer src="<?= gd_set_browser_cache(PATH_ADMIN_GD_SHARE . 'ncds/script/common/ncds-jquery-validator.js') ?>"></script>
<article class="ncua-content layer-caller-connector">
    <form id="registerSendManagerForm">
        <div class="layer-caller-connector__content">
            <details class="ncua-accordion ncua-accordion--blue" open>
                <summary>발신번호 관리 책임자를 먼저 연동해주세요.</summary>
                <div class="ncua-accordion__content">
                    <p>발신번호 위조를 통한 스팸·금융사기 방지를 위해 「전기통신사업법」 제84조의2에 따라</p>
                    <p>발신번호의 실제 소유자 또는 관리책임자를 확인 후에만 SMS 발송이 가능합니다.</p>
                    <p>안전한 메시지 발송을 위해 관리책임자를 먼저 설정하시기 바랍니다.</p>
                </div>
            </details>

            <div class="ncua-table ncua-table--vertical">
                <table>
                    <colgroup>
                        <col width="144px">
                        <col>
                    </colgroup>
                    <tr>
                        <th><div >관리책임자</div></th>
                        <td>
                            <?php if (count($smsCallerList) > 1): ?>
                                <!-- 관리책임자 복수 -->
                                <div class="ncua-select ncua-select--xs" data-manager-select-wrapper>
                                    <span class="ncua-select__content">
                                        <select name="caller" id="caller" class="ncua-select__tag" data-caller-select>
                                            (caller.isAgent ? ' (대리인)' : ' (대표자 본인)')
                                            <option value="">관리책임자를 선택하세요</option>
                                            <?php foreach ($smsCallerList as $smsCaller): ?>
                                            <option value="<?= $smsCaller->getCallerNo() ?>"><?= $smsCaller->getCallerName() ?> (<?= $smsCaller->isAgent() ? '대리인' : '대표자 본인' ?>)</option>
                                            <?php endforeach; ?>
                                        </select>
                                    </span>
                                </div>
                            <?php else: ?>
                                <div class="ncua-flex">
                                    <span class="ncua-flex">
                                        <input type="hidden" name="caller" id="caller" value="<?= $smsCallerList[0]->getCallerNo() ?>" />
                                        <div>
                                            <?= $smsCallerList[0]->getCallerName() ?> (<?= $smsCallerList[0]->isAgent() ? '대리인' : '대표자 본인' ?>)
                                        </div>
                                    </span>
                                </div>
                            <?php endif; ?>
                        </td>
                    </tr>
                </table>
            </div>
        </div>
    </form>
    
    <div class="ncua-content-footer">
        <button type="button" id="registerCallerCancel" class="ncua-btn ncua-btn--sm ncua-btn--secondary-gray">
            <span class="ncua-btn__label">취소</span>
        </button>
        <button type="button" id="registerCallerConfirm" class="ncua-btn ncua-btn--sm ncua-btn--primary">
            <span class="ncua-btn__label">연동</span>
        </button>
    </div>
</article>

<script>

    const onReady = () =>  {
        try {
            // 취소 버튼
            document.getElementById('registerCallerCancel')?.addEventListener('click', () => {
                layer_close();
            });
            
            // 연동 버튼
            document.getElementById('registerCallerConfirm')?.addEventListener('click', () => {
                const callerNo = document.getElementById('caller')?.value;

                if (!callerNo) {
                    NCDSValidator.highlight(document.querySelector('[data-caller-select]'));
                    return;
                }

                connectCaller(callerNo);
            });
        } catch (e) {
            NCDSAlert({ message: e.message, iconType: 'error' });
        }
    };

    function connectCaller(callerNo) {
        $.post('./message_config_ps.php', {mode: 'connectCaller', callerNo: callerNo}, function (response) {
            if (response.error || !response.success) {
                NCDSAlert({ message: '일시적인 오류가 발생하였습니다.<br>다시 시도해 주세요.', 'iconType': 'error' });
                return;
            }

            if (response.success) {
                layer_close();
                $('#manageCallNumber').click();
            }
        });
    }

    const DOM_READY_STATES = ['complete', 'interactive'];

    if (DOM_READY_STATES.includes(document.readyState)) {
        onReady();
    } else {
        document.addEventListener('DOMContentLoaded', onReady);
    }
</script>
