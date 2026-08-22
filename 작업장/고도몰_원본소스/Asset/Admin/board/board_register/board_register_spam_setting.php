<section class="ncua-card">
    <header class="ncua-card__header">
        <h4 class="ncua-card__title">스팸방지 설정</h4>
    </header>
    <section class="ncua-card__body board-template-list">
        <div class="ncua-table ncua-table--vertical">
            <table>
                <tr>
                    <th><div>허용 태그</div></th>
                    <td>
                        <div class="ncua-flex-gap">
                            <label class="ncua-checkbox-field ncua-checkbox-field--xs has-text">
                                <span class="ncua-checkbox-input ncua-checkbox-input--xs ncua-checkbox-field__input">
                                    <input type="checkbox" name="bdAllowTags[]" value="iframe" <?= gd_isset($checked['bdAllowTags']['iframe']) ?> />
                                </span>
                                <span><span class="ncua-checkbox-field__text">iframe</span></span>
                            </label>
                            <label class="ncua-checkbox-field ncua-checkbox-field--xs has-text">
                                <span class="ncua-checkbox-input ncua-checkbox-input--xs ncua-checkbox-field__input">
                                    <input type="checkbox" name="bdAllowTags[]" value="embed" <?= gd_isset($checked['bdAllowTags']['embed']) ?> />
                                </span>
                                <span><span class="ncua-checkbox-field__text">embed</span></span>
                            </label>
                        </div>
                    </td>
                </tr>
            <tr>
                <th><div data-tooltip-seq="016">허용 도메인</div></th>
                <td>
                    <div class="ncua-flex-column ncua-align-items-flex-start ncua-gap-4">
                        <ul id="domain-allow-box" class="ncua-gap-4 ncua-flex-column ncua-flex">
                            <?php
                            for ($i = 0; $i < $data['bdAllowDomainCount']; $i++) {
                                ?>
                                <?php if ($i % 2 == 0) { ?>
                                    <li class="ncua-gap-8 ncua-flex">
                                        <div class="ncua-input ncua-input--xs ncua-input-width-320">
                                            <div class="ncua-input__content">
                                                <div class="ncua-input__field ncua-input__field--xs">
                                                    <input type="text" name="bdAllowDomain[]" placeholder="youtube.com" maxlength="60" value="<?= gd_isset($data['arrBdAllowDomain'][$i]) ?>">
                                                </div>
                                            </div>
                                        </div>
                                <?php } else if ($i % 2 == 1) { ?>
                                        <div class="ncua-input ncua-input--xs ncua-input-width-320">
                                            <div class="ncua-input__content">
                                                <div class="ncua-input__field ncua-input__field--xs">
                                                    <input type="text" name="bdAllowDomain[]" placeholder="naver.com" value="<?= gd_isset($data['arrBdAllowDomain'][$i]) ?>">
                                                </div>
                                            </div>
                                        </div>
                                    </li>
                                <?php }
                            }
                            ?>
                            <?php if ($data['bdAllowDomainCount'] % 2 == 1) { ?>
                                <div class="ncua-input ncua-input--xs ncua-input-width-320">
                                    <div class="ncua-input__content">
                                        <div class="ncua-input__field ncua-input__field--xs">
                                            <input type="text" name="bdAllowDomain[]" placeholder="youtube.com" value="">
                                        </div>
                                    </div>
                                </div>
                            <?php } ?>
                        </ul>
                        <button type="button" class="ncua-btn ncua-btn--xs ncua-btn--secondary-gray js-allow-domain-add">키워드 추가</button>
                    </div>
                </td>
            </tr>

                <tr class="if-is-qa-hide">
                    <th><div>댓글 스팸방지</div></th>
                    <td>
                        <div class="ncua-flex-gap">
                            <label class="ncua-checkbox-field ncua-checkbox-field--xs has-text">
                                <span class="ncua-checkbox-input ncua-checkbox-input--xs ncua-checkbox-field__input">
                                    <input type="checkbox" name="bdSpamMemoFl[]" value="1" <?= gd_isset($checked['bdSpamMemoFl'][1]) ?> />
                                </span>
                                <span><span class="ncua-checkbox-field__text">외부유입차단</span></span>
                            </label>
                            <label class="ncua-checkbox-field ncua-checkbox-field--xs has-text">
                                <span class="ncua-checkbox-input ncua-checkbox-input--xs ncua-checkbox-field__input">
                                    <input type="checkbox" name="bdSpamMemoFl[]" value="2" <?= gd_isset($checked['bdSpamMemoFl'][2]) ?> />
                                </span>
                                <span><span class="ncua-checkbox-field__text">자동등록방지</span></span>
                            </label>
                            <label class="ncua-checkbox-field ncua-checkbox-field--xs has-text">
                                <span class="ncua-checkbox-input ncua-checkbox-input--xs ncua-checkbox-field__input">
                                    <input type="checkbox" name="bdPasswordMemoFl" value="y" <?= gd_isset($checked['bdPasswordMemoFl']['y']) ?> />
                                </span>
                                <span><span class="ncua-checkbox-field__text">비밀댓글 암호보안</span></span>
                            </label>
                        </div>
                    </td>
                </tr>
                <tr>
                    <th><div>게시글 스팸방지</div></th>
                    <td>
                        <div class="ncua-flex-gap">
                            <label class="ncua-checkbox-field ncua-checkbox-field--xs has-text">
                                <span class="ncua-checkbox-input ncua-checkbox-input--xs ncua-checkbox-field__input">
                                    <input type="checkbox" name="bdSpamBoardFl[]" value="1" <?= gd_isset($checked['bdSpamBoardFl'][1]) ?> />
                                </span>
                                <span><span class="ncua-checkbox-field__text">외부유입차단</span></span>
                            </label>
                            <label class="ncua-checkbox-field ncua-checkbox-field--xs has-text">
                                <span class="ncua-checkbox-input ncua-checkbox-input--xs ncua-checkbox-field__input">
                                    <input type="checkbox" name="bdSpamBoardFl[]" value="2" <?= gd_isset($checked['bdSpamBoardFl'][2]) ?> />
                                </span>
                                <span><span class="ncua-checkbox-field__text">자동등록방지</span></span>
                            </label>
                        </div>
                    </td>
                </tr>
            </table>
        </div>
    </section>
</section>

