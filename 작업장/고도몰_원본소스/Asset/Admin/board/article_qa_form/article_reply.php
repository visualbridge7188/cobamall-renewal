<section class="ncua-card">
    <header class="ncua-card__header">
        <h4 class="ncua-card__title">게시글 답변하기</h4>
        <p class="ncua-card__sub-info"><strong>*는 필수 입력</strong> 항목입니다.</p>
    </header>
    <section class="ncua-card__body">      
        <input type="hidden" name="bdId" value="<?=$req['bdId']?>" >
        <input type="hidden" name="mode" value="replyQa"/>
        <input type="hidden" name="queryString" value=""/>
        <input type="hidden" name="sno" value="<?=$req['sno']?>"/>
        <div class="ncua-table ncua-table--vertical">
            <table>
                <colgroup>
                    <col/>
                    <col/>
                </colgroup>
                <tr>
                    <th><div>답변 작성자</div></th>
                    <td>
                        <div>
                            <?= $writer['writer'] ?>
                        </div>
                    </td>
                </tr>
                <tr>
                    <th><div>답변 상태</div></th>
                    <td>
                        <div>
                            <span class="ncua-select ncua-select--xs ncua-input-width-80">
                                <span class="ncua-select__content">
                                    <select id="replyStatus" name="replyStatus" class="ncua-select__tag">   
                                        <?php
                                        foreach ($listReplyStatus as $key => $val) { ?>
                                            <option
                                                value="<?= $key ?>" <?php if ($bdView['data']['replyStatus'] == $key) echo 'selected' ?>><?= $val ?></option>
                                        <?php } ?>
                                    </select>
                                </span>
                            </span>
                        </div>
                    </td>
                </tr>
                <?php if(gd_is_provider() === false) {?>
                    <tr>
                        <th><div>게시글 양식</div></th>
                        <td>
                            <div class="ncua-flex-gap">
                                <span class="ncua-select ncua-select--xs ncua-input-width-120">
                                    <span class="ncua-select__content">
                                        <?= gd_select_box('bdTemplateSno', 'bdTemplateSno', $templateList, null, null, null, null, 'ncua-select__tag'); ?>
                                    </span>
                                </span>
                                <button type="button" class="ncua-btn ncua-btn--xs ncua-btn--secondary-gray js-template-register">게시글 양식 등록</button>
                            </div>
                        </td>
                    </tr>
                <tr>
                <?php }?>
                    <th><div>답변 제목</div></th>
                    <td>
                        <div>
                            <div class="ncua-input ncua-input--xs ncua-input-full-width">
                                <div class="ncua-input__content">
                                    <div class="ncua-input__field ncua-input__field--xs">
                                        <input type="text" name="answerSubject" value="<?= gd_isset($bdView['data']['answerSubject']) ?>"/>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </td>
                </tr>
                <tr>
                    <th><div>답변 내용</div></th>
                    <td>
                        <div>
                            <textarea 
                                data-godo-editor="article-reply-editor"
                                data-height-min="450"
                                data-height-max="600"
                                data-height-resize="true"
                                class="form-control" name="answerContents" id="article-reply-editor" label="내용"><?= gd_isset($bdView['data']['answerContents']); ?>
                            </textarea>
                        </div>
                    </td>
                </tr>
            </table>
        </div>
    </section>
</section>
