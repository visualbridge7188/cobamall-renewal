<section class="ncua-card present-config__gift-card-config">
    <header class="ncua-card__header">
        <h3 class="ncua-card__title" data-tooltip-seq="005" data-tooltip-icon-type="fill">선물카드 설정</h3>
    </header>
    <section class="ncua-card__body js-track-change">
        <div>
            <div class="ncua-caution-text">선물카드를 직접등록시, 800px*400px ,  jpg, png형식, 최대 500KB 를 권장합니다.</div>
            <div class="ncua-notice-info">가로:세로 비율이 2:1 아니거나, 가로 500px 세로 250px 이하일 경우 선물카드를 추가할 수 없습니다.</div>
            <div class="ncua-notice-info">최대 <?=$cardLimit?>개 까지 직접등록이 가능하며, 초과시 이미 등록된 선물카드를 삭제한 후에 등록할 수 있습니다.</div>
        </div>

        <div class="ncua-search-result present-config__gift-card-table">
            <div class="ncua-search-result__content">
                <div class="ncua-table ncua-table--horizontal">
                    <table>
                        <colgroup>
                            <col width="56px">
                            <col width="80px">
                            <col width="90px">
                            <col width="160px">
                            <col width="*">
                            <col width="200px">
                        </colgroup>
                        <thead>
                            <tr>
                                <th>
                                    <div data-tooltip-seq="006">
                                        <label class="ncua-checkbox-field ncua-checkbox-field--xs has-text">
                                            <span class="ncua-checkbox-input ncua-checkbox-input--xs ncua-checkbox-field__input">
                                                <input type="checkbox" class="js-checkall" data-target-name="delete-card-sno">
                                            </span>
                                        </label>
                                    </div>
                                </th>
                                <th><div data-tooltip-seq="007">순서</div></th>
                                <th><div data-tooltip-seq="008">구분</div></th>
                                <th><div data-tooltip-seq="009">이미지</div></th>
                                <th><div data-tooltip-seq="003">기본메시지</div></th>
                                <th><div data-tooltip-seq="010">노출</div></th>
                            </tr>
                        </thead>
                        <tbody>
                        <?php foreach ($cards as $index => $card): ?>
                            <tr>
                                <td>
                                    <div>
                                        <label class="ncua-checkbox-field ncua-checkbox-field--xs has-text">
                                            <span class="ncua-checkbox-input ncua-checkbox-input--xs ncua-checkbox-field__input">
                                                <input type="checkbox" class="delete-card-sno" name="delete-card-sno" value="<?= $card->getSno() ?>"
                                                    <?= $card->getUploadType() === 'default' ? 'disabled' : '' ?>>
                                            </span>
                                        </label>
                                    </div>
                                </td>
                                <td>
                                    <div class="card-sort"><?= $index + 1 ?></div>
                                    <input type="hidden" name="card[update][<?= $card->getSno() ?>][sno]" value="<?= $card->getSno() ?>">
                                </td>
                                <td><div class="upload-type"><?= $card->getUploadType() === 'default' ? '기본제공' : '직접등록' ?></div></td>
                                <td>
                                    <div><img src="<?= $card->getImageUrl() ?>" width="55" alt="선물카드 이미지" /></div>
                                </td>
                                <td class="present-config__gift-card-message">
                                    <div>
                                        <div class="ncua-input ncua-input--xs ncua-input--full-width-with-count" data-show-hint-text="true">
                                            <div class="ncua-input__content-wrap">
                                                <div class="ncua-input__content">
                                                    <div class="ncua-input__field ncua-input__field--xs present-card-message-field">
                                                        <input type="text" name="card[update][<?= $card->getSno() ?>][message]" class="present-card-message" data-charcount-key="presentCardMessage[<?= $card->getSno() ?>]"
                                                               maxlength="30" value="<?= $card->getMessage() ?>">
                                                    </div>
                                                </div>
                                                <div class="ncua-input__field-text-count" data-charcount-text="presentCardMessage[<?= $card->getSno() ?>]">
                                                    <output class="ncua-input__field-text-count-current">0</output>
                                                    <span>/30</span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <div class="ncua-flex-gap">
                                        <label class="ncua-radio-field ncua-radio-field--xs has-text">
                                            <span class="ncua-radio-input ncua-radio-input--xs ncua-radio-field__input">
                                                <input type="radio" name="card[update][<?= $card->getSno() ?>][displayFl]" value="y"
                                                <?= $card->getDisplayFl() == 'y' ? 'checked' : '' ?>/>
                                            </span>
                                            <span class="ncua-radio-field__text">노출함</span>
                                        </label>
                                        <label class="ncua-radio-field ncua-radio-field--xs has-text">
                                            <span class="ncua-radio-input ncua-radio-input--xs ncua-radio-field__input">
                                                <input type="radio" name="card[update][<?= $card->getSno() ?>][displayFl]" value="n"
                                                <?= $card->getDisplayFl() == 'n' ? 'checked' : '' ?>/>
                                            </span>
                                            <span class="ncua-radio-field__text">노출안함</span>
                                        </label>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                        </tbody>
                    </table>
                    <template id="card-row-template">
                        <tr>
                            <td>
                                <div>
                                    <label class="ncua-checkbox-field ncua-checkbox-field--xs has-text">
                                            <span class="ncua-checkbox-input ncua-checkbox-input--xs ncua-checkbox-field__input">
                                                <input type="checkbox" class="delete-card-sno" name="delete-card-sno" value="">
                                            </span>
                                    </label>
                                </div>
                            </td>
                            <td>
                                <div class="card-sort"></div>
                                <input type="hidden" data-name="card[insert][__INDEX__]" data-value="__INDEX__">
                            </td>
                            <td><div class="upload-type">직접등록</div></td>
                            <td>
                                <div><img data-src="__IMG__" width="55" alt="선물카드 이미지" /></div>
                                <input type="file" data-name="card_image[__INDEX__]" style="display: none;" class="no-filestyle">
                            </td>
                            <td class="present-config__gift-card-message">
                                <div>
                                    <div class="ncua-input ncua-input--xs ncua-input--full-width-with-count" data-show-hint-text="true">
                                        <div class="ncua-input__content-wrap">
                                            <div class="ncua-input__content">
                                                <div class="ncua-input__field ncua-input__field--xs present-card-message-field">
                                                    <input type="text" data-name="card[insert][__INDEX__][message]" class="present-card-message" data-charcount-key="presentCardMessage[insert][__INDEX__]"
                                                           maxlength="30" value="고마운 마음을 가득 담아 선물을 보내요.">
                                                </div>
                                            </div>
                                            <div class="ncua-input__field-text-count" data-charcount-text="presentCardMessage[insert][__INDEX__]">
                                                <output class="ncua-input__field-text-count-current">0</output>
                                                <span>/30</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <div class="ncua-flex-gap">
                                    <label class="ncua-radio-field ncua-radio-field--xs has-text">
                                            <span class="ncua-radio-input ncua-radio-input--xs ncua-radio-field__input">
                                                <input type="radio" data-name="card[insert][__INDEX__][displayFl]" value="y"
                                                checked/>
                                            </span>
                                        <span class="ncua-radio-field__text">노출함</span>
                                    </label>
                                    <label class="ncua-radio-field ncua-radio-field--xs has-text">
                                            <span class="ncua-radio-input ncua-radio-input--xs ncua-radio-field__input">
                                                <input type="radio" data-name="card[insert][__INDEX__][displayFl]" value="n"/>
                                            </span>
                                        <span class="ncua-radio-field__text">노출안함</span>
                                    </label>
                                </div>
                            </td>
                        </tr>
                    </template>
                </div>
                <div class="ncua-search-result__actions ncua-search-result__actions--bottom">
                    <div class="ncua-search-result__button-group">
                        <button type="button" class="ncua-btn ncua-btn--xs ncua-btn--secondary add-card-btn">
                            선물카드 추가
                        </button>
                        <button type="button" class="ncua-btn ncua-btn--xs ncua-btn--destructive ncua-select-delete delete-card-btn">
                            선택 삭제
                        </button>
                    </div>
                </div>
                <input type="file" style="display: none;" id="new-card-image" class="no-filestyle">
                <input type="hidden" class="delete-card-snos" name="card[delete]">
            </div>
        </div>
    </section>
</section>
