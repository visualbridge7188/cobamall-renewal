<section class="ncua-card">
    <header class="ncua-card__header">
        <h4 class="ncua-card__title">작성자 화면 설정</h4>
        <p class="ncua-card__sub-info"><strong>*는 필수 입력</strong> 항목입니다.</p>
    </header>
    <section class="ncua-card__body board-template-list">
        <div class="ncua-table ncua-table--vertical">
            <table class="writer-setting-table">
                <tr class="if-is-event-show" data-table-top-shape>
                    <th><div>부가설명</div></th>
                    <td>
                        <div>
                            <div class="ncua-switch ncua-switch--xs">
                                <label class="ncua-switch__option ncua-switch__option--left <?= gd_isset($checked['bdSubSubjectFl']['y']) ? 'ncua-switch__option--active' : 'ncua-switch__option--inactive' ?>">
                                    <input class="ncua-switch__radio ncua-switch__radio--left" name="bdSubSubjectFl" type="radio" value='y' <?= gd_isset($checked['bdSubSubjectFl']['y']) ?> />
                                    <span class="ncua-switch__label">사용함</span>
                                </label>
                                <label class="ncua-switch__option ncua-switch__option--right <?= gd_isset($checked['bdSubSubjectFl']['n']) ? 'ncua-switch__option--active' : 'ncua-switch__option--inactive' ?>">
                                    <input class="ncua-switch__radio ncua-switch__radio--right" name="bdSubSubjectFl" type="radio" value='n' <?= gd_isset($checked['bdSubSubjectFl']['n']) ?> />
                                    <span class="ncua-switch__label">사용안함</span>
                                </label>
                            </div>
                        </div>
                    </td>
                </tr>

                <tr>
                    <th><div>에디터 사용</div></th>
                    <td>
                        <div>
                            <div class="ncua-switch ncua-switch--xs">
                                <label class="ncua-switch__option ncua-switch__option--left <?= gd_isset($checked['bdEditorFl']['y']) ? 'ncua-switch__option--active' : 'ncua-switch__option--inactive' ?>">
                                    <input class="ncua-switch__radio ncua-switch__radio--left" name="bdEditorFl" type="radio" value='y' <?= gd_isset($checked['bdEditorFl']['y']) ?> />
                                    <span class="ncua-switch__label">사용함</span>
                                </label>
                                <label class="ncua-switch__option ncua-switch__option--right <?= gd_isset($checked['bdEditorFl']['n']) ? 'ncua-switch__option--active' : 'ncua-switch__option--inactive' ?>">
                                    <input class="ncua-switch__radio ncua-switch__radio--right" name="bdEditorFl" type="radio" value='n' <?= gd_isset($checked['bdEditorFl']['n']) ?> />
                                    <span class="ncua-switch__label">사용안함</span>
                                </label>
                            </div>
                        </div>
                    </td>
                </tr>

                <tr>
                    <th><div>휴대폰 작성</div></th>
                    <td>
                        <div>
                            <div class="ncua-switch ncua-switch--xs">
                                <label class="ncua-switch__option ncua-switch__option--left <?= gd_isset($checked['bdMobileFl']['y']) ? 'ncua-switch__option--active' : 'ncua-switch__option--inactive' ?>">
                                    <input class="ncua-switch__radio ncua-switch__radio--left" type="radio" name="bdMobileFl" value="y" <?= gd_isset($checked['bdMobileFl']['y']) ?> />
                                    <span class="ncua-switch__label">사용함</span>
                                </label>
                                <label class="ncua-switch__option ncua-switch__option--right <?= gd_isset($checked['bdMobileFl']['n']) ? 'ncua-switch__option--active' : 'ncua-switch__option--inactive' ?>">
                                    <input class="ncua-switch__radio ncua-switch__radio--right" type="radio" name="bdMobileFl" value="n" <?= gd_isset($checked['bdMobileFl']['n']) ?> />
                                    <span class="ncua-switch__label">사용안함</span>
                                </label>
                            </div>
                        </div>
                    </td>
                </tr>

                <tr>
                    <th><div>이메일 작성</div></th>
                    <td>
                        <div>
                            <div class="ncua-switch ncua-switch--xs">
                                <label class="ncua-switch__option ncua-switch__option--left <?= gd_isset($checked['bdEmailFl']['y']) ? 'ncua-switch__option--active' : 'ncua-switch__option--inactive' ?>">
                                    <input class="ncua-switch__radio ncua-switch__radio--left" type="radio" name="bdEmailFl" value="y" <?= gd_isset($checked['bdEmailFl']['y']) ?> />
                                    <span class="ncua-switch__label">사용함</span>
                                </label>
                                <label class="ncua-switch__option ncua-switch__option--right <?= gd_isset($checked['bdEmailFl']['n']) ? 'ncua-switch__option--active' : 'ncua-switch__option--inactive' ?>">
                                    <input class="ncua-switch__radio ncua-switch__radio--right" type="radio" name="bdEmailFl" value="n" <?= gd_isset($checked['bdEmailFl']['n']) ?> />
                                    <span class="ncua-switch__label">사용안함</span>
                                </label>
                            </div>
                        </div>
                    </td>
                </tr>

                <tr>
                    <th><div>업로드 파일 사용</div></th>
                    <td>
                        <div>
                            <div class="ncua-switch ncua-switch--xs">
                                <label class="ncua-switch__option ncua-switch__option--left <?= gd_isset($checked['bdUploadFl']['y']) ? 'ncua-switch__option--active' : 'ncua-switch__option--inactive' ?>">
                                    <input class="ncua-switch__radio ncua-switch__radio--left" name="bdUploadFl" type="radio" value='y' <?= gd_isset($checked['bdUploadFl']['y']) ?> />
                                    <span class="ncua-switch__label">사용함</span>
                                </label>
                                <label class="ncua-switch__option ncua-switch__option--right <?= gd_isset($checked['bdUploadFl']['n']) ? 'ncua-switch__option--active' : 'ncua-switch__option--inactive' ?>">
                                    <input class="ncua-switch__radio ncua-switch__radio--right" name="bdUploadFl" type="radio" value='n' <?= gd_isset($checked['bdUploadFl']['n']) ?> />
                                    <span class="ncua-switch__label">사용안함</span>
                                </label>
                            </div>
                        </div>
                    </td>
                </tr>

                <tr id="bdUploadMaxSize_tr">
                    <th class="ncua-required"><div>업로드파일 최대크기</div></th>
                    <td>
                        <div class="ncua-gap-4">
                            <div class="ncua-input ncua-input--xs ncua-input-width-80">
                                <div class="ncua-input__content">
                                    <div class="ncua-input__field ncua-input__field--xs">
                                        <input type="text" name="bdUploadMaxSize" id="bdUploadMaxSize" class="js-number" value="<?= gd_isset($data['bdUploadMaxSize']) ?>"/>                                        
                                    </div>
                                </div>
                            </div>
                            MByte(s)
                        </div>
                    </td>
                </tr>

                <tr>
                    <th><div>링크</div></th>
                    <td>
                        <div>
                            <div class="ncua-switch ncua-switch--xs">
                                <label class="ncua-switch__option ncua-switch__option--left <?= gd_isset($checked['bdLinkFl']['y']) ? 'ncua-switch__option--active' : 'ncua-switch__option--inactive' ?>">
                                    <input class="ncua-switch__radio ncua-switch__radio--left" type="radio" name="bdLinkFl" value="y" <?= gd_isset($checked['bdLinkFl']['y']) ?> />
                                    <span class="ncua-switch__label">사용함</span>
                                </label>
                                <label class="ncua-switch__option ncua-switch__option--right <?= gd_isset($checked['bdLinkFl']['n']) ? 'ncua-switch__option--active' : 'ncua-switch__option--inactive' ?>">
                                    <input class="ncua-switch__radio ncua-switch__radio--right" type="radio" name="bdLinkFl" value="n" <?= gd_isset($checked['bdLinkFl']['n']) ?> />
                                    <span class="ncua-switch__label">사용안함</span>
                                </label>
                            </div>
                        </div>
                    </td>
                </tr>
            </table>
        </div>
    </section>
</section>
