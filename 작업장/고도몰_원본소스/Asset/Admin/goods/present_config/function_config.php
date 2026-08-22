<section class="ncua-card">
    <header class="ncua-card__header">
        <h3 class="ncua-card__title ncua-flex ncua-align-items-center" data-tooltip-seq="001" data-tooltip-icon-type="fill">기능 설정</h3>
    </header>
    <section class="ncua-card__body js-track-change">
        <div class="ncua-table ncua-table--vertical">
            <table>
                <tbody>
                    <tr>
                        <th class="ncua-required"><div data-tooltip-seq="004">선물하기 기능</div></th>
                        <td>
                            <div>
                                <div class="ncua-switch ncua-switch--xs">
                                    <label class="ncua-switch__option ncua-switch__option--left ncua-switch__option--<?= $checked['useFl']['y'] ? 'active' : 'inactive' ?>">
                                        <input class="ncua-switch__radio ncua-switch__radio--left" type="radio" name="useFl" value="y" <?= $checked['useFl']['y'] ?: '' ?> />
                                        <span class="ncua-switch__label">사용함</span>
                                    </label>
                                    <label class="ncua-switch__option ncua-switch__option--right ncua-switch__option--<?= $checked['useFl']['n'] ? 'active' : 'inactive' ?>">
                                        <input class="ncua-switch__radio ncua-switch__radio--right" type="radio"  name="useFl" value="n" <?= $checked['useFl']['n'] ?: '' ?> />
                                        <span class="ncua-switch__label">사용안함</span>
                                    </label>
                                </div>
                            </div>
                        </td>
                    </tr>
                    <tr class="present-config__auto-cancel">
                        <th class="ncua-required"><div data-tooltip-seq="002">자동취소일</div></th>
                        <td>
                            <div class="ncua-flex-gap">
                                <label class="ncua-radio-field ncua-radio-field--xs has-text">
                                    <span class="ncua-radio-input ncua-radio-input--xs ncua-radio-field__input">
                                        <input type="radio" name="expirationPeriod" value="7"
                                        <?= $checked['expirationPeriod']['7'] ?: '' ?> />
                                    </span>
                                    <span class="ncua-radio-field__text">7일</span>
                                </label>
                                <label class="ncua-radio-field ncua-radio-field--xs has-text">
                                    <span class="ncua-radio-input ncua-radio-input--xs ncua-radio-field__input">
                                        <input type="radio" name="expirationPeriod" value="10"
                                        <?= $checked['expirationPeriod']['10'] ?: '' ?> />
                                    </span>
                                    <span class="ncua-radio-field__text">10일</span>
                                </label>
                                <label class="ncua-radio-field ncua-radio-field--xs has-text">
                                    <span class="ncua-radio-input ncua-radio-input--xs ncua-radio-field__input">
                                        <input type="radio" name="expirationPeriod" value="15"
                                        <?= $checked['expirationPeriod']['15'] ?: '' ?> />
                                    </span>
                                    <span class="ncua-radio-field__text">15일</span>
                                </label>
                                <label class="ncua-radio-field ncua-radio-field--xs has-text">
                                    <span class="ncua-radio-input ncua-radio-input--xs ncua-radio-field__input">
                                        <input type="radio" name="expirationPeriod" value="30"
                                        <?= $checked['expirationPeriod']['30'] ?: '' ?>/>
                                    </span>
                                    <span class="ncua-radio-field__text">30일</span>
                                </label>
                            </div>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </section>
</section>
