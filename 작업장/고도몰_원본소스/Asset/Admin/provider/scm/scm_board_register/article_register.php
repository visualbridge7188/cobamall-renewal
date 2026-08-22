<section class="ncua-card">
    <header class="ncua-card__header">
        <h4 class="ncua-card__title">게시글 <?=$mode?></h4>
        <p class="ncua-card__sub-info"><strong>*는 필수 입력</strong> 항목입니다.</p>
    </header>
    <section class="ncua-card__body">
        <?php if ($req['mode'] == 'modify' || $req['mode'] == 'reply') { ?>
            <input type="hidden" name="sno" value="<?= $req['sno'] ?>"/>
        <?php } ?>
        <input type="hidden" name="mode" value="<?= $req['mode'] ?>"/>
        <div class="ncua-table ncua-table--vertical">
            <table>
                <colgroup>
                    <col/>
                    <col/>
                </colgroup>

                <?php if (!gd_is_provider() && $req['mode'] != 'reply') { ?>
                    <tr>
                        <th><div>대상</div></th>
                        <td>
                            <div>
                                <div class="radio-btn-group">
                                    <label class="ncua-radio-field ncua-radio-field--xs has-text">
                                        <span class="ncua-radio-input ncua-radio-input--xs ncua-radio-field__input">
                                            <input type="radio" name="scmFl" value="all" <?= gd_isset($data['checked']['scmFl']['all']); ?> onclick="$('#scmLayer').html('');"/>
                                        </span>
                                        <span><span class="ncua-radio-field__text">전체</span></span>
                                    </label>
                                    <label class="ncua-radio-field ncua-radio-field--xs has-text">
                                        <span class="ncua-radio-input ncua-radio-input--xs ncua-radio-field__input">
                                            <input type="radio" name="scmFl" value="n" <?= gd_isset($data['checked']['scmFl']['n']); ?> onclick="$('#scmLayer').html('')" ;/>
                                        </span>
                                        <span><span class="ncua-radio-field__text">본사</span></span>
                                    </label>
                                    <label class="ncua-radio-field ncua-radio-field--xs has-text">
                                        <span class="ncua-radio-input ncua-radio-input--xs ncua-radio-field__input">
                                            <input type="radio" name="scmFl" value="y" <?= gd_isset($data['checked']['scmFl']['y']); ?> onclick="layer_register('scm','checkbox')"/>
                                        </span>
                                        <span><span class="ncua-radio-field__text">공급사</span></span>
                                    </label>
                                    <button type="button" class="ncua-btn ncua-btn--xs ncua-btn--secondary-gray" onclick="layer_register('scm','checkbox')">공급사 선택</button>
                                </div>

                                <div id="scmLayer" class="width100p">
                                    <?php if ($data['scmFl'] == 'y') {
                                        //TODO:수정
                                        if ($data['scmBoardGroup']) {
                                            foreach ($data['scmBoardGroup'] as $k => $v) { ?>
                                                <span id="info_scm_<?= $v['scmNo'] ?>" class="btn-group btn-group-xs">
                                        <input type="hidden" name="scmNo[]" value="<?= $v['scmNo'] ?>"/>
                                        <input type="hidden" name="scmNoNm[]" value="<?= $v['companyNm'] ?>"/>
                                        <button type="button" class="ncua-btn ncua-btn--xs ncua-btn--secondary-gray" name="scmNoNm"><?= $v['companyNm'] ?></button>
                                        <button type="button" class="ncua-btn ncua-btn--xs ncua-btn--primary" data-toggle="delete" data-target="#info_scm_<?= $v['scmNo'] ?>">삭제</button>
                                        </span>
                                            <?php }
                                        }
                                    } ?>
                                </div>
                            </div>
                        </td>
                    </tr>
                <?php } ?>
                    <th class="ncua-required"><div>제목</div></th>
                    <td>
                        <div class="ncua-flex-gap">
                            <div class="ncua-input ncua-input--xs ncua-input-full-width">
                                <div class="ncua-input__content">
                                    <div class="ncua-input__field ncua-input__field--xs"><input type="text" name="subject" value="<?= gd_isset($data['subject']) ?>"/></div>
                                </div>
                            </div>
                            <?php if (!gd_is_provider() && !$data['groupThread']) { ?>
                                <label class="ncua-checkbox-field ncua-checkbox-field--xs has-text">
                                    <span class="ncua-checkbox-input ncua-checkbox-input--xs ncua-checkbox-field__input">
                                        <input type="checkbox" name="isNotice" value="y" <?= $data['checked']['isNotice']['y'] ?>/>
                                        <span class="ncua-checkbox-input__ico">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" viewBox="0 0 12 12" fill="none">
                                            <path
                                            d="M10 3L4.5 8.5L2 6"
                                            stroke="#171818"
                                            stroke-width="1.6666"
                                            stroke-linecap="round"
                                            stroke-linejoin="round"></path>
                                        </svg>
                                        </span>
                                    </span>
                                    <span><span class="ncua-checkbox-field__text">공지사항</span></span>
                                </label>
                            <?php } ?>
                        </div>
                    </td>
                </tr>
                <tr>
                    <th><div>분류</div></th>
                    <td>
                        <div>
                            <span class="ncua-select ncua-select--xs">
                                <span class="ncua-select__content">
                                    <?= gd_select_box('category', 'category', $category, null, gd_isset($data['category']), '=전체=', null, 'ncua-select__tag' ); ?>
                                </span>
                            </span>
                        </div>
                    </td>
                </tr>
                <tr>
                    <th><div>작성자</div></th>
                    <td>
                        <div>
                            <?= $data['writer'] ?>
                        </div>
                    </td>
                </tr>
                <tr>
                    <th><div>파일첨부</div></th>
                    <td>
                        <div class="file-input-content">
                            <div id="fileInputContainer"></div>
                            <div id="fileTagContainer" class="ncua-file-tags"></div>
                        </div>
                    </td>
                </tr>
                <tr>
                    <th class="ncua-required"><div>내용</div></th>
                    <td>
                        <div>
                            <textarea 
                            data-godo-editor="editor"
                            data-height-min="412"
                            data-height-max="600"
                            data-height-resize="true"
                            name="contents" id="editor" rows="10" style="width:98%; height:412px; "><?= gd_isset($data['contents']); ?></textarea>
                        </div>
                    </td>
                </tr>
            </table>
        </div>
    </section>
</section>
