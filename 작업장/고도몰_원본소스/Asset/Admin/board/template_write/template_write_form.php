<form id="ncds-board-template-write-form" action="template_ps.php" method="post" class="board-template-write">
    <input type="hidden" name="mode" value="<?=gd_isset($data['mode']) ?>"/>
    <input type="hidden" name="sno" value="<?=gd_isset($data['sno']) ?>"/>
    <?php if($req['mode'] != 'popup') {?>
    <div class="modal-dialog__content">
    <?php }?>
        <div class="ncua-table ncua-table--vertical <?php if($req['mode'] == 'popup') {?>board-template-write-form-table<?php }?>">
            <table>
                <tbody>
                    <tr>
                        <th><div>분류</div></th>
                        <td>
                            <div class="ncua-flex-gap">
                                <label class="ncua-radio-field ncua-radio-field--xs has-text">
                                    <span class="ncua-radio-input ncua-radio-input--xs ncua-radio-field__input">
                                        <input type="radio" name="templateType" value="front" <?php if($data['templateType'] == 'front') echo 'checked'?> <?php if($req['templateType'] == 'admin') echo 'disabled';?>>
                                    </span>
                                    <span><span class="ncua-radio-field__text">쇼핑몰 게시글 양식</span></span>
                                </label>
                                <label class="ncua-radio-field ncua-radio-field--xs has-text">
                                    <span class="ncua-radio-input ncua-radio-input--xs ncua-radio-field__input">
                                        <input type="radio" name="templateType" value="admin" <?php if($data['templateType'] == 'admin') echo 'checked'?> <?php if($req['templateType'] == 'front') echo 'disabled';?>>
                                    </span>
                                    <span><span class="ncua-radio-field__text">관리자 게시글 양식</span></span>
                                </label>
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <th class="ncua-required"><div>제목</div></th>
                        <td class="board-template-write-subject">
                            <div>
                                <div class="ncua-input ncua-input--xs">
                                    <div class="ncua-input__content">
                                        <div class="ncua-input__field ncua-input__field--xs">
                                            <input name="subject" value="<?=gd_htmlspecialchars(gd_isset($data['subject'])) ?>" type="text" />
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
                                <div class="editor-container">
                                    <textarea name="contents" data-godo-editor="basic-editor"  id="editor">
                                        <?=gd_isset($data['contents']); ?>
                                    </textarea>
                                </div>
                            </div>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div> 
    <?php if($req['mode'] != 'popup') {?>
    </div>
    <?php }?>
    <div class="board-template-write-footer <?php if($req['mode'] != 'popup') {?>modal-dialog__footer<?php }?>">
        <button type="button" class="ncua-btn ncua-btn--sm ncua-btn--secondary-gray js-template-write-cancel">취소</button>
        <input type="submit" value="저장" class="ncua-btn ncua-btn--sm ncua-btn--primary"/>
    </div>
</form>
