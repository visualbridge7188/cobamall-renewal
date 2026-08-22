<div class="modal-dialog__content">
<article class="ncua-content">
    <div class="ncua-table ncua-table--vertical">
        <table>
            <tbody>
            <tr>
                <th class="ncua-required"><div>옵션 관리명</div></th>
                <td>
                    <div class="ncua-input ncua-input--xs ncua-input-full-width">
                        <div class="ncua-input__content-wrap" style="width:100%;"><div class="ncua-input__content" style="width:100%;"><div class="ncua-input__field ncua-input__field--xs"><input type="text" id="freqSaveGroupName" name="groupName" placeholder="옵션관리명을 입력하세요." maxlength="50" aria-required="true" /></div></div></div>
                    </div>
                </td>
            </tr>
            </tbody>
        </table>
    </div>
</article>
</div>

<div class="modal-dialog__footer">
    <button type="button" class="ncua-btn ncua-btn--sm ncua-btn--primary js-opt-save-freq-submit"><span class="ncua-btn__label">등록</span></button>
</div>

<script type="text/javascript">
<!--
$(function () {
    setTimeout(function () { $('#freqSaveGroupName').focus(); }, 50);

    $('#freqSaveGroupName').on('keydown', function (e) {
        if (e.key === 'Enter') { e.preventDefault(); $('.js-opt-save-freq-submit').trigger('click'); }
    });

    $('.js-opt-save-freq-submit').on('click', function () {
        var managNm = $.trim($('#freqSaveGroupName').val());
        if (!managNm) {
            NCDSAlert({ message: '옵션 관리명을 입력하세요.', iconType: 'error', callback: function () { $('#freqSaveGroupName').focus(); } });
            return;
        }
        const DIV = '<?= STR_DIVISION ?>';
        var names  = [];
        var values = [];
        $('.js-opt-name-input').each(function (i) {
            var nm = $.trim($(this).val());
            if (!nm) return;
            names.push(nm);
            var vals = [];
            $(this).closest('.js-opt-name-row').find('.js-opt-val-input').each(function () {
                vals.push($.trim($(this).val()));
            });
            values.push(vals.join(DIV));
        });
        $.ajax({
            url: './goods_ps.php', type: 'POST',
            data: {
                mode: 'option_direct_register',
                optionManageNm:  managNm,
                optionDisplayFl: $('[name="optionY[optionDisplayFl]"]:checked').val() || 's',
                optionName:      names.join(DIV),
                optionValue:     values
            },
            success: function () {
                layer_close();
                NCDSAlert({ message: '자주쓰는 옵션이 등록되었습니다.', iconType: 'success' });
            },
            error: function () {
                NCDSAlert({ message: '자주쓰는 옵션 등록에 실패했습니다.', iconType: 'error' });
            }
        });
    });


});
//-->
</script>
