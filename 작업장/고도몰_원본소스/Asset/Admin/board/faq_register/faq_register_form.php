<form id="frm" action="faq_ps.php" method="post">
    <input type="hidden" name="mode" id="mode" value="<?=gd_isset($mode)?>" />
    <input type="hidden" name="sno" id="sno" value="<?=gd_isset($data['sno'])?>" />
    <?php if ($gGlobal['isUse'] == 'y') { ?>
        <input type="hidden" name="mallSno" value="<?=$mallSno?>">
    <?php } ?>
    <div class="ncua-table ncua-table--vertical">
        <table>
        <tr>
            <th><div>구분</div></th>
            <td>
                <div>
                    <span class="ncua-select ncua-select--xs">
                        <span class="ncua-select__content">
                            <?=gd_select_box('category', 'category', gd_code('03001',$mallSno), null, gd_isset($data['category']), null, null, 'ncua-select__tag'); ?>
                        </span>
                    </span>
                </div>
            </td>
        </tr>
        <tr>
            <th><div>Best</div></th>
            <td>
                <div>
                    <label class="ncua-checkbox-field ncua-checkbox-field--xs has-text">
                    <span class="ncua-checkbox-input ncua-checkbox-input--xs ncua-checkbox-field__input">
                        <input type="checkbox" name="isBest" value="y" <?=gd_isset($checked['isBest']['y'])?>> 
                    </span>
                    <span><span class="ncua-checkbox-field__text">BEST FAQ 노출</span></span>
                    </label>
                </div>
            </td>
        </tr>
        <tr>
            <th class="ncua-required"><div>제목</div></th>
            <td class="faq-register-subject">
                <div>
                    <div class="ncua-input ncua-input--xs">
                        <div class="ncua-input__content">
                            <div class="ncua-input__field ncua-input__field--xs">
                                <input type="text" name="subject" value="<?=gd_isset($data['subject']);?>" />
                            </div>
                        </div>
                    </div>
                </div>
            </td>
        </tr>
        <tr>
            <th class="ncua-required"><div>내용</div></th>
            <td>
                <div>
                    <textarea data-godo-editor="basic-editor" name="contents" required="required" label="내용"><?=gd_isset($data['contents']);?></textarea>
                </div>
            </td>
        </tr>
        <tr>
            <th><div>답변</div></th>
            <td>
                <div>
                    <textarea name="answer" data-godo-editor="basic-editor" required="required" label="답변"><?=gd_isset($data['answer']);?></textarea>
                </div>
            </td>
        </tr>
        </table>
    </div>
</form>
