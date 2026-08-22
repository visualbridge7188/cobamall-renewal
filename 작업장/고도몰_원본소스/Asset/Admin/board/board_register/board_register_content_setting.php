<section class="ncua-card">
    <header class="ncua-card__header">
        <h4 class="ncua-card__title">게시글 내용 화면설정</h4>
    </header>
    <section class="ncua-card__body board-template-list">
        <div class="ncua-table ncua-table--vertical">
            <table>
            <tr>
                <th><div data-tooltip-seq="022">첨부파일 이미지 표시</div></th>
                <td>
                    <div>
                        <div class="ncua-switch ncua-switch--xs">
                            <label class="ncua-switch__option ncua-switch__option--left <?= gd_isset($checked['bdAttachImageDisplayFl']['y']) ? 'ncua-switch__option--active' : 'ncua-switch__option--inactive' ?>">
                                <input class="ncua-switch__radio ncua-switch__radio--left" type="radio" name="bdAttachImageDisplayFl" value="y" <?= gd_isset($checked['bdAttachImageDisplayFl']['y']) ?> />
                                <span class="ncua-switch__label">사용함</span>
                            </label>
                            <label class="ncua-switch__option ncua-switch__option--right <?= gd_isset($checked['bdAttachImageDisplayFl']['n']) ? 'ncua-switch__option--active' : 'ncua-switch__option--inactive' ?>">
                                <input class="ncua-switch__radio ncua-switch__radio--right" type="radio" name="bdAttachImageDisplayFl" value="n" <?= gd_isset($checked['bdAttachImageDisplayFl']['n']) ?> />
                                <span class="ncua-switch__label">사용안함</span>
                            </label>
                        </div>
                    </div>
                </td>
            </tr>
            <tr class="bdAttachImageRow">
                <th><div data-tooltip-seq="023">이미지 리사이즈</div></th>
                <td>
                    <div class="ncua-gap-4">
                        <div class="ncua-input ncua-input--xs ncua-input-width-80">
                            <div class="ncua-input__content">
                                <div class="ncua-input__field ncua-input__field--xs">
                                    <input type="text" class="js-number" name="bdAttachImageMaxSize" value="<?= $data['bdAttachImageMaxSize'] ?>">
                                </div>
                            </div>
                        </div>
                        px
                    </div>
                </td>
            </tr>
            <tr class="bdAttachImageRow">
                <th><div>노출 위치</div></th>
                <td>
                    <div class="ncua-flex-gap">
                        <label class="ncua-radio-field ncua-radio-field--xs has-text">
                            <span class="ncua-radio-input ncua-radio-input--xs ncua-radio-field__input">
                                <input type="radio" name="bdAttachImagePosition" value="top" <?= gd_isset($checked['bdAttachImagePosition']['top']) ?> />
                            </span>
                            <span><span class="ncua-radio-field__text">본문상단</span></span>
                        </label>
                        <label class="ncua-radio-field ncua-radio-field--xs has-text">
                            <span class="ncua-radio-input ncua-radio-input--xs ncua-radio-field__input">
                                <input type="radio" name="bdAttachImagePosition" value="bottom" <?= gd_isset($checked['bdAttachImagePosition']['bottom']) ?> />
                            </span>
                            <span><span class="ncua-radio-field__text">본문하단</span></span>
                        </label>
                    </div>
                </td>
            </tr>
            <tr>
                <th><div>리스트화면 노출</div></th>
                <td>
                    <div>
                        <div class="ncua-switch ncua-switch--xs">
                            <label class="ncua-switch__option ncua-switch__option--left <?= gd_isset($checked['bdListInView']['y']) ? 'ncua-switch__option--active' : 'ncua-switch__option--inactive' ?>">
                                <input class="ncua-switch__radio ncua-switch__radio--left" type="radio" name="bdListInView" value="y" <?= gd_isset($checked['bdListInView']['y']) ?> />
                                <span class="ncua-switch__label">사용함</span>
                            </label>
                            <label class="ncua-switch__option ncua-switch__option--right <?= gd_isset($checked['bdListInView']['n']) ? 'ncua-switch__option--active' : 'ncua-switch__option--inactive' ?>">
                                <input class="ncua-switch__radio ncua-switch__radio--right" type="radio" name="bdListInView" value="n" <?= gd_isset($checked['bdListInView']['n']) ?> />
                                <span class="ncua-switch__label">사용안함</span>
                            </label>
                        </div>
                    </div>
                </td>
            </tr>
            <tr>
                <th><div>IP 노출</div></th>
                <td>
                    <div class="ncua-flex-column ncua-align-items-flex-start ncua-flex-gap">
                        <div >
                            <div class="ncua-switch ncua-switch--xs js-bdIpFl-switch">
                                <label class="ncua-switch__option ncua-switch__option--left <?= gd_isset($checked['bdIpFl']) ? 'ncua-switch__option--active' : 'ncua-switch__option--inactive' ?>">
                                    <input class="ncua-switch__radio ncua-switch__radio--left" type="radio" name="bdIpFl" value="y" <?= gd_isset($checked['bdIpFl']) ?> />
                                    <span class="ncua-switch__label">사용함</span>
                                </label>
                                <label class="ncua-switch__option ncua-switch__option--right <?= !gd_isset($checked['bdIpFl']) ? 'ncua-switch__option--active' : 'ncua-switch__option--inactive' ?>">
                                    <input class="ncua-switch__radio ncua-switch__radio--right" type="radio" name="bdIpFl" value="n" <?= !gd_isset($checked['bdIpFl']) ? 'checked' : '' ?> />
                                    <span class="ncua-switch__label">사용안함</span>
                                </label>
                            </div>
                        </div>
                        <label class="ncua-checkbox-field ncua-checkbox-field--xs has-text">
                            <span class="ncua-checkbox-input ncua-checkbox-input--xs ncua-checkbox-field__input">
                                <input type="checkbox" name="bdIpFilterFl" id="bdIpFilterFl" value="y" <?= gd_isset($checked['bdIpFilterFl']) ?> <?= gd_isset($disabled['bdIpFilterFl']) ?> />
                            </span>
                            <span><span class="ncua-checkbox-field__text">IP 끝자리 암호화표기</span></span>
                        </label>
                    </div>
                </td>
            </tr>
            </table>
        </div>
    </section>
</section>

