<section class="ncua-card">
    <header class="ncua-card__header">
        <h3 class="ncua-card__title">리뷰작성 설정</h3>
    </header>
    <section class="ncua-card__body">
        <div class="ncua-table ncua-table--vertical">
            <table>
                <tbody>
                    <tr>
                        <th><div>최소 글자수 제한</div></th>
                        <td>
                            <div class="ncua-flex-column ncua-flex-gap">
                                <label class="ncua-radio-field ncua-radio-field--xs has-text">
                                    <span class="ncua-radio-input ncua-radio-input--xs ncua-radio-field__input">
                                        <input type="radio" name="minLimitLengthFl" value="n" <?= $checked['minLimitLengthFl']['n'] ?>>
                                    </span>
                                    <span class="ncua-radio-field__text">제한없음</span>
                                </label>
                                <div class="ncua-min-text-limit">
                                    <label class="ncua-radio-field ncua-radio-field--xs has-text">
                                        <span class="ncua-radio-input ncua-radio-input--xs ncua-radio-field__input">
                                            <input type="radio" name="minLimitLengthFl" value="y" <?= $checked['minLimitLengthFl']['y'] ?>>
                                        </span>
                                        <div class="ncua-radio-field__text">
                                            <span>리뷰 작성 최소 글자수</span>
                                        </div>
                                    </label>
                                    <div class="ncua-input ncua-input--xs ncua-input-width-80">
                                        <div class="ncua-input__content">
                                            <div class="ncua-input__field ncua-input__field--xs">
                                                <input type="text" name="minContentsLength" value="<?=$data['minContentsLength']?>" class="js-number">
                                            </div>
                                        </div>
                                    </div>
                                    <span>자</span>
                                    <span>(</span>
                                    <label class="ncua-checkbox-field ncua-checkbox-field--xs has-text ncua-mileage-max-limit">
                                        <span class="ncua-checkbox-input ncua-checkbox-input--xs ncua-checkbox-field__input">
                                            <input type="checkbox" name="unMinLimitLengthFl" value="y" <?= $checked['unMinLimitLengthFl']['y'] ?>/>
                                        </span>
                                        <div>
                                            <div class="ncua-checkbox-field__text">
                                                <span>최소 글자수만 표기하고 등록 가능하도록 설정</span>
                                            </div>
                                        </div>
                                    </label>
                                    <span>)</span>
                                </div>
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <th><div data-tooltip-seq="022">플러스리뷰 금칙어 설정 사용여부</div></th>
                        <td>
                            <div>
                                <div class="ncua-switch ncua-switch--xs">
                                    <label class="ncua-switch__option ncua-switch__option--left ncua-switch__option--<?= $checked['prohibitedWordsFl']['y'] ? 'active' : 'inactive' ?>">
                                        <input class="ncua-switch__radio ncua-switch__radio--left" type="radio" name="prohibitedWordsFl" value="y" <?= $checked['prohibitedWordsFl']['y'] ?> />
                                        <span class="ncua-switch__label">사용함</span>
                                    </label>
                                    <label class="ncua-switch__option ncua-switch__option--right ncua-switch__option--<?= $checked['prohibitedWordsFl']['n'] ? 'active' : 'inactive' ?>">
                                        <input class="ncua-switch__radio ncua-switch__radio--right" type="radio" name="prohibitedWordsFl" value="n" <?= $checked['prohibitedWordsFl']['n'] ?> />
                                        <span class="ncua-switch__label">사용안함</span>
                                    </label>
                                </div>
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <th><div>첨부이미지 최대 크기</div></th>
                        <td>
                            <div class="ncua-gap-8">
                                <div class="ncua-input ncua-input--xs ncua-input-width-80">
                                    <div class="ncua-input__content">
                                        <div class="ncua-input__field ncua-input__field--xs">
                                            <input type="text" name="uploadMaxSize" value="<?=$data['uploadMaxSize']?>" class="js-number">
                                        </div>
                                    </div>
                                </div>
                                <span>Mbyte(s)</span>
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <th><div>첨부이미지 등록 설정</div></th>
                        <td>
                            <div class="ncua-flex-gap">
                                <label class="ncua-radio-field ncua-radio-field--xs has-text">
                                    <span class="ncua-radio-input ncua-radio-input--xs ncua-radio-field__input">
                                        <input type="radio" name="uploadRequiredFl" value="y" <?= $checked['uploadRequiredFl']['y'] ?>>
                                    </span>
                                    <span class="ncua-radio-field__text">필수</span>
                                </label>
                                <label class="ncua-radio-field ncua-radio-field--xs has-text">
                                    <span class="ncua-radio-input ncua-radio-input--xs ncua-radio-field__input">
                                        <input type="radio" name="uploadRequiredFl" value="n" <?= $checked['uploadRequiredFl']['n'] ?>>
                                    </span>
                                    <span class="ncua-radio-field__text">선택</span>
                                </label>
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <th><div>첨부이미지 파일 개수</div></th>
                        <td>
                            <div class="ncua-flex-gap">
                                <?php for ($i = 4; $i >= 1; $i--) { ?>
                                <label class="ncua-radio-field ncua-radio-field--xs has-text">
                                    <span class="ncua-radio-input ncua-radio-input--xs ncua-radio-field__input">
                                        <input type="radio" name="uploadMaxCount" value="<?= $i ?>" <?= $selected['uploadMaxCount'][$i]  ? 'checked' : '' ?>>
                                    </span>
                                    <span class="ncua-radio-field__text"><?= $i ?>개</span>
                                </label>
                                <?php } ?>
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <th><div data-tooltip-seq="023">리뷰 승인 설정</div></th>
                        <td>
                            <div class="ncua-flex-gap">
                                <label class="ncua-radio-field ncua-radio-field--xs has-text">
                                    <span class="ncua-radio-input ncua-radio-input--xs ncua-radio-field__input">
                                        <input type="radio" name="viewPermissionFl" value="a" <?= $checked['viewPermissionFl']['a'] ?>>
                                    </span>
                                    <span class="ncua-radio-field__text">등록 시 자동 승인</span>
                                </label>
                                <label class="ncua-radio-field ncua-radio-field--xs has-text">
                                    <span class="ncua-radio-input ncua-radio-input--xs ncua-radio-field__input">
                                        <input type="radio" name="viewPermissionFl" value="d" <?= $checked['viewPermissionFl']['d'] ?>>
                                    </span>
                                    <span class="ncua-radio-field__text">직접 승인</span>
                                </label>
                            </div>
                        </td>
                    </tr>
                    <!-- js-review-view-tr 노출/미노출 스크립트 -->
                    <tr class="js-review-view-tr">
                        <th><div>미승인 리뷰 노출 설정</div></th>
                        <td>
                            <div class="ncua-flex-gap">
                                <label class="ncua-radio-field ncua-radio-field--xs has-text">
                                    <span class="ncua-radio-input ncua-radio-input--xs ncua-radio-field__input">
                                        <input type="radio" name="viewAllowFl" value="y" <?= $checked['viewAllowFl']['y'] ?>>
                                    </span>
                                    <span class="ncua-radio-field__text">미승인 시 작성자에게만 노출</span>
                                </label>
                                <label class="ncua-radio-field ncua-radio-field--xs has-text">
                                    <span class="ncua-radio-input ncua-radio-input--xs ncua-radio-field__input">
                                        <input type="radio" name="viewAllowFl" value="n" <?= $checked['viewAllowFl']['n'] ?>>
                                    </span>
                                    <span class="ncua-radio-field__text">미승인 시 노출안함</span>
                                </label>
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <th><div>리뷰 수정/삭제 제한</div></th>
                        <td>
                            <div class="ncua-flex-gap">
                                <label class="ncua-radio-field ncua-radio-field--xs has-text">
                                    <span class="ncua-radio-input ncua-radio-input--xs ncua-radio-field__input">
                                        <input type="radio" name="modifyDelPermissionFl" value="n" <?= $checked['modifyDelPermissionFl']['n'] ?>>
                                    </span>
                                    <span class="ncua-radio-field__text">제한없음</span>
                                </label>
                                <label class="ncua-radio-field ncua-radio-field--xs has-text">
                                    <span class="ncua-radio-input ncua-radio-input--xs ncua-radio-field__input">
                                        <input type="radio" name="modifyDelPermissionFl" value="y" <?= $checked['modifyDelPermissionFl']['y'] ?>>
                                    </span>
                                    <span class="ncua-radio-field__text">수정/삭제 제한</span>
                                </label>
                                <label class="ncua-radio-field ncua-radio-field--xs has-text">
                                    <span class="ncua-radio-input ncua-radio-input--xs ncua-radio-field__input">
                                        <input type="radio" name="modifyDelPermissionFl" value="ma" <?= $checked['modifyDelPermissionFl']['ma'] ?>>
                                    </span>
                                    <span class="ncua-radio-field__text">혜택 지급 시 수정/삭제 제한</span>
                                </label>
                            </div>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </section>
</section>
