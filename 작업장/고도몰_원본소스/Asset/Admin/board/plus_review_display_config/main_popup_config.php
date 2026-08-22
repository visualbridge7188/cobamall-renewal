<section class="ncua-card">
    <header class="ncua-card__header">
        <h3 class="ncua-card__title">메인 플러스리뷰 작성 팝업 설정</h3>
    </header>
    <section class="ncua-card__body">
        <div class="ncua-table ncua-table--vertical">
            <table>
                <tbody>
                    <tr>
                        <th><div data-tooltip-seq="004">노출설정</div></th>
                        <td>
                            <div>
                                <div class="ncua-switch ncua-switch--xs">
                                    <label class="ncua-switch__option ncua-switch__option--left ncua-switch__option--<?= $checked['popupFl']['y'] ? 'active' : 'inactive' ?>">
                                        <input class="ncua-switch__radio ncua-switch__radio--left" type="radio" name="popupFl" value="y" <?= $checked['popupFl']['y'] ?> />
                                        <span class="ncua-switch__label">노출함</span>
                                    </label>
                                    <label class="ncua-switch__option ncua-switch__option--right ncua-switch__option--<?= $checked['popupFl']['n'] ? 'active' : 'inactive' ?>">
                                        <input class="ncua-switch__radio ncua-switch__radio--right" type="radio" name="popupFl" value="n" <?= $checked['popupFl']['n'] ?>  />
                                        <span class="ncua-switch__label">노출안함</span>
                                    </label>
                                </div>
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <th><div data-tooltip-seq="005">노출 시점</div></th>
                        <td>
                            <div class="ncua-flex-gap">
                                <?php foreach ($popupStatus as $key => $value) { ?>
                                    <label class="ncua-radio-field ncua-radio-field--xs has-text">
                                        <span class="ncua-radio-input ncua-radio-input--xs ncua-radio-field__input">
                                            <input type="radio" name="popupStatus" value="<?= $key ?>"  <?= $selected['popupStatus'] == $key ? 'checked' : '' ?>/>
                                        </span>
                                        <span class="ncua-radio-field__text"><?= $value ?> 이후</span>
                                    </label>
                                <?php } ?>
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <th><div>템플릿 설정</div></th>
                        <td>
                            <div class="ncua-preview-template-layout">
                                <label class="ncua-radio-field ncua-radio-field--xs has-text">
                                    <span class="ncua-radio-input ncua-radio-input--xs ncua-radio-field__input">
                                        <input type="radio" name="popupTemplate" value="01" <?= $checked['popupTemplate']['01'] ?>>
                                    </span>
                                    <span>
                                        <span class="ncua-radio-field__text">메인팝업 템플릿 
                                        <button type="button" class="ncua-btn ncua-btn--xs ncua-btn--secondary-gray js-btn-preview-template" data-target="popupTemplate">
                                            <span class="ncua-btn__label">미리보기</span>
                                        </button>
                                    </span>
                                </label>
                                <div class="popupTemplate ncua-preview-template-2">
                                    <img src="<?= PATH_ADMIN_GD_SHARE ?>image/plusreview_popup_template_01.png">
                                </div>  
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <th><div data-tooltip-seq="006">메인 팝업 노출기간 설정</div></th>
                        <td>
                            <div class="ncua-popup-period-config">
                                <span><span id="authWriteStatusText"><?= $popupStatus[$selected['popupStatus']] ?></span> 이후 시점으로부터</span>
                                <span class="ncua-select ncua-select--xs">
                                    <span class="ncua-select__content">
                                        <select class="ncua-select__tag" name="popupPeriod">
                                            <?php for ($i = 1; $i <= 15; $i++) { ?>
                                                <option value="<?= $i ?>" <?= $selected['popupPeriod'][$i] ?>><?= $i ?>일</option>
                                            <?php } ?>
                                        </select>
                                    </span>
                                </span>
                                <span>이내만 리뷰 작성 팝업을 출력</span>
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <th><div data-tooltip-seq="007">리뷰등록 완료 안내문구</div></th>
                        <td>
                            <div>
                                <div class="ncua-input ncua-input--xs ncua-input-full-width">
                                    <div class="ncua-input__content">
                                        <div class="ncua-input__field ncua-input__field--xs">
                                        <input type="text" name="popupReviewCompleteAlert" value="<?= $data['popupReviewCompleteAlert'] ?>"  placeholder="회원님의 소중한 리뷰가 등록되었습니다.">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <th><div>창위치</div></th>
                        <td>
                            <div class="ncua-popup-position-config">
                                <div class="ncua-input ncua-input--xs ncua-text-input-unit">
                                    <div class="ncua-input-text">상단에서 :</div>
                                    <div class="ncua-input__content">
                                        <div class="ncua-input__field ncua-input__field--xs">
                                            <input type="text" name="popupPosition[top]" class="ncua-number js-number" required value="<?= $data['popupPosition']['top'] ?>"> 
                                        </div>
                                    </div>
                                    <div class="ncua-input-text">px</div>
                                </div>
                                <div class="ncua-input ncua-input--xs ncua-text-input-unit">
                                    <div class="ncua-input-text">좌측에서 : </div>
                                    <div class="ncua-input__content">
                                        <div class="ncua-input__field ncua-input__field--xs">
                                            <input type="text" name="popupPosition[left]" class="ncua-number js-number" required value="<?= $data['popupPosition']['left'] ?>"> 
                                        </div>
                                    </div>
                                    <div class="ncua-input-text">px</div>
                                </div>
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <th><div>오늘하루 보이지 않음</div></th>
                        <td>
                            <div>
                                <div class="ncua-switch ncua-switch--xs">
                                    <label class="ncua-switch__option ncua-switch__option--left ncua-switch__option--<?= $checked['popupTodayCloseFl']['y'] ? 'active' : 'inactive' ?>">
                                        <input class="ncua-switch__radio ncua-switch__radio--left" type="radio" name="popupTodayCloseFl" value="y" <?= $checked['popupTodayCloseFl']['y'] ?> />
                                        <span class="ncua-switch__label">사용함</span>
                                    </label>
                                    <label class="ncua-switch__option ncua-switch__option--right ncua-switch__option--<?= $checked['popupTodayCloseFl']['n'] ? 'active' : 'inactive' ?>">
                                        <input class="ncua-switch__radio ncua-switch__radio--right" type="radio" name="popupTodayCloseFl" value="n" <?= $checked['popupTodayCloseFl']['n'] ?>  />
                                        <span class="ncua-switch__label">사용안함</span>
                                    </label>
                                </div>         
                            </div>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </section>
</section>
