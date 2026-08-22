<section class="ncua-card">
    <header class="ncua-card__header">
        <h3 class="ncua-card__title">리뷰작성 혜택 설정</h3>
    </header>
    <section class="ncua-card__body">
        <div class="ncua-table ncua-table--vertical">
            <table>
                <tbody>
                    <tr>
                        <th>
                            <div>마일리지 사용유무</div>
                        </th>
                        <td>
                            <div>
                                <div class="ncua-switch ncua-switch--xs">
                                    <label class="ncua-switch__option ncua-switch__option--left ncua-switch__option--<?= $checked['mileageFl']['y'] ? 'active' : 'inactive' ?>">
                                        <input class="ncua-switch__radio ncua-switch__radio--left" type="radio" name="mileageFl" value="y" <?= $checked['mileageFl']['y'] ?> />
                                        <span class="ncua-switch__label">사용함</span>
                                    </label>
                                    <label class="ncua-switch__option ncua-switch__option--right ncua-switch__option--<?= $checked['mileageFl']['n'] ? 'active' : 'inactive' ?>">
                                        <input class="ncua-switch__radio ncua-switch__radio--right" type="radio" name="mileageFl" value="n" <?= $checked['mileageFl']['n'] ?> />
                                        <span class="ncua-switch__label">사용안함</span>
                                    </label>
                                </div>
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <th>
                            <div data-tooltip-seq="013">리뷰혜택 안내문구</div>
                        </th>
                        <td>
                            <div>
                                <div class="ncua-input ncua-input--textarea ncua-input--textarea--xs">
                                    <textarea class="ncua-input__textarea" name="reviewBenefitInfo" rows="3" maxlength="250" placeholder="(예시) 리뷰 작성 시, 10마일리지 혜택을 드립니다."><?= $data['reviewBenefitInfo'] ?></textarea>
                                    <div class="ncua-input__text-count-wrap">
                                        <div class="ncua-hint-text"></div>
                                        <div class="ncua-input__text-count">
                                        <span class="ncua-input__text-count-text">
                                            <!-- 텍스트 count 값을 스크립트에서 체크 -->
                                            <span class="ncua-input__text-count-text-count"></span>
                                            /250
                                        </span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </td>
                    </tr>
                    <tr class="js-miliage-use-tr">
                        <th>
                            <div data-tooltip-seq="014">마일리지 혜택 안내문구</div>
                        </th>
                        <td>
                            <div>
                                <div class="ncua-input ncua-input--xs ncua-input-width-520">
                                    <div class="ncua-input__content">
                                        <div class="ncua-input__field ncua-input__field--xs">
                                        <input type="text" name="mileageAddGuid" value="<?= $data['mileageAddGuid'] ?>" placeholder="(예시) 리뷰를 등록하시겠습니까? 00자 이상 등록하시면 마일리지 혜택을 드립니다.">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </td>
                    </tr>
                    <tr class="js-miliage-use-tr">
                        <th>
                            <div data-tooltip-seq="015">마일리지 지급 방법</div>
                        </th>
                        <td>
                            <div class="ncua-flex-gap">
                                <label class="ncua-radio-field ncua-radio-field--xs has-text">
                                    <span class="ncua-radio-input ncua-radio-input--xs ncua-radio-field__input">
                                        <input name="mileageAddFl" type="radio" value='a' <?= $checked['mileageAddFl']['a'] ?> />
                                    </span>
                                    <span class="ncua-radio-field__text">리뷰 등록 시 지급</span>
                                    <span>( 지급 시점 설정 : </span>
                                    <span class="ncua-select ncua-select--xs">
                                        <span class="ncua-select__content">
                                            <?= gd_select_box('mileage-add-duration', 'mileageAddDuration', $mileageAddDuration, '일', $selected['mileageAddDuration'], '작성 즉시', null, 'ncua-select__tag'); ?>
                                        </span>
                                    </span>
                                    <span>이후 지급 )</span>
                                </label>
                                <label class="ncua-radio-field ncua-radio-field--xs has-text">
                                    <span class="ncua-radio-input ncua-radio-input--xs ncua-radio-field__input">
                                        <input name="mileageAddFl" type="radio" value='m' <?= $checked['mileageAddFl']['m'] ?> />
                                    </span>
                                    <span class="ncua-radio-field__text">수동지급</span>
                                </label>
                            </div>
                        </td>
                    </tr>
                    <tr class="js-miliage-use-tr">
                        <th>
                            <div data-tooltip-seq="016">마일리지 지급 예외 설정</div>
                        </th>
                        <td>
                            <div class="ncua-flex-column ncua-flex-gap">
                                <div class="ncua-mileage-limit">
                                    <span>리뷰 작성 글자수 최소</span>
                                    <div class="ncua-input ncua-input--xs ncua-input-width-120">
                                        <div class="ncua-input__content">
                                            <div class="ncua-input__field ncua-input__field--xs">
                                                <input type="text" name="mileageAddminLimit" value="<?= $data['mileageAddminLimit'] ?>" maxlength="3" class="js-number">
                                            </div>
                                        </div>
                                    </div>
                                    <span>자 이상 입력 시에만 지급</span>
                                </div>
                                <div class="ncua-mileage-limit ncua-mileage-limit-price">
                                    <span>구매 상품 가격</span>
                                    <div class="ncua-input ncua-input--xs ncua-input-width-120">
                                        <div class="ncua-input__content">
                                            <div class="ncua-input__field ncua-input__field--xs">
                                                <input type="text" name="mileageAddLimitGoodsPrice" value="<?= $data['mileageAddLimitGoodsPrice'] ?>" class="js-number">
                                            </div>
                                        </div>
                                    </div>
                                    <span>원 이상인 상품 리뷰 등록 시 마일리지 지급</span>
                                </div>
                            </div>
                        </td>
                    </tr>
                    <tr class="js-miliage-use-tr">
                        <th>
                            <div data-tooltip-seq="017">마일리지 지급 설정</div>
                        </th>
                        <td>
                            <div class="ncua-flex-column ncua-flex-gap">
                                <div class="ncua-mileage-condition-config">
                                    <div class="ncua-mileage-condition-config-limit">
                                        <span>리뷰 등록 시 ( 지급 시점 설정 :</span>
                                        <div class="ncua-input ncua-input--xs ncua-input-width-120">
                                            <div class="ncua-input__content">
                                                <div class="ncua-input__field ncua-input__field--xs">
                                                    <input type="text" name="mileageAmount[review]" value="<?= $data['mileageAmount']['review'] ?>" required class="js-number">
                                                </div>
                                            </div>
                                        </div>
                                        <span class="ncua-select ncua-select--xs">
                                            <span class="ncua-select__content">
                                                <select id="mileage-unit" name="mileageUnit[review]" class="ncua-select__tag">
                                                    <option value="default" <?= $selected['mileageUnit']['review']['default'] ?>>원</option>
                                                    <option value="percent" <?= $selected['mileageUnit']['review']['percent'] ?>>%</option>
                                                </select>   
                                            </span>
                                        </span>
                                        <span>지급)</span>
                                    </div>
                                    <!-- mileage_unit_percent : 기존 스크립트 동작에 필요한 클래스 -->
                                    <div class="mileage_unit_percent ncua-mileage-max-limit-wrap">
                                        <label class="ncua-checkbox-field ncua-checkbox-field--xs has-text">
                                            <span class="ncua-checkbox-input ncua-checkbox-input--xs ncua-checkbox-field__input">
                                                <input type="checkbox" name="mileageAmountLimitFl" value="y" <?= $checked['mileageAmountLimitFl']['y'] ?>/>
                                            </span>
                                            <div class="ncua-checkbox-field__text">
                                                <span>최대 지급 마일리지 제한</span>
                                            </div>
                                        </label>
                                        <div class="ncua-mileage-max-limit">
                                            <div class="ncua-input ncua-input--xs ncua-input-width-120 is-disabled">
                                                <div class="ncua-input__content">
                                                    <div class="ncua-input__field ncua-input__field--xs">
                                                        <input type="text" name="mileageAmountLimit[review]" value="<?= $data['mileageAmountLimit']['review'] ?>" readonly class="js-number">
                                                    </div>
                                                </div>
                                            </div>
                                            <span>원</span>
                                        </div>
                                    </div>
                                </div>
                                <div class="ncua-mileage-config">
                                    <span>포토리뷰 작성 시</span>
                                    <div class="ncua-input ncua-input--xs ncua-input-width-120">
                                        <div class="ncua-input__content">
                                            <div class="ncua-input__field ncua-input__field--xs">
                                                <input type="text" name="mileageAmount[photo]" value="<?= $data['mileageAmount']['photo'] ?>" required class="js-number">
                                            </div>
                                        </div>
                                    </div>
                                    <span>원 추가지급</span>
                                </div>
                                <div class="ncua-mileage-config">
                                    <span>상품별 첫 리뷰 작성 시</span>
                                    <div class="ncua-input ncua-input--xs ncua-input-width-120">
                                        <div class="ncua-input__content">
                                            <div class="ncua-input__field ncua-input__field--xs">
                                                <input type="text" name="mileageAmount[first]" value="<?= $data['mileageAmount']['first'] ?>" required class="js-number">
                                            </div>
                                        </div>
                                    </div>
                                    <span>원 추가지급</span>
                                </div>
                            </div>
                        </td>
                    </tr>
                    <tr class="js-miliage-use-tr">
                        <th>
                            <div>마일리지 중복 지급 제한</div>
                        </th>
                        <td>
                            <div class="ncua-flex-column ncua-flex-gap">
                                <div class="ncua-mileage-duplicate-limit ncua-flex-gap">
                                    <label class="ncua-radio-field ncua-radio-field--xs has-text">
                                        <span class="ncua-radio-input ncua-radio-input--xs ncua-radio-field__input">
                                            <input name="mileageDuplicateIgnoreFl" type="radio" value='y' <?= $checked['mileageDuplicateIgnoreFl']['y'] ?> />
                                        </span>
                                        <span class="ncua-radio-field__text">제한없음</span>
                                    </label>
                                    <label class="ncua-radio-field ncua-radio-field--xs has-text">
                                        <span class="ncua-radio-input ncua-radio-input--xs ncua-radio-field__input">
                                            <input name="mileageDuplicateIgnoreFl" type="radio" value='n' <?= $checked['mileageDuplicateIgnoreFl']['n'] ?> />
                                        </span>
                                        <span class="ncua-radio-field__text">최초 1회만 마일리지 지급</span>
                                    </label>
                                </div>
                                <div class="ncua-notice-info">설정에 따른 마일리지 지급 여부는 하단 안내 영역을 참고해주세요.</div>
                            </div>
                        </td>
                    </tr>
                    <tr class="js-miliage-use-tr">
                        <th>
                            <div>게시글 삭제 시 마일리지 차감</div>
                        </th>
                        <td>
                            <div>
                                <div class="ncua-switch ncua-switch--xs">
                                    <label class="ncua-switch__option ncua-switch__option--left ncua-switch__option--<?= $checked['mileageDeleteFl']['y'] ? 'active' : 'inactive' ?>">
                                        <input class="ncua-switch__radio ncua-switch__radio--left" type="radio" name="mileageDeleteFl" value="y" <?= $checked['mileageDeleteFl']['y'] ?> />
                                        <span class="ncua-switch__label">사용함</span>
                                    </label>
                                    <label class="ncua-switch__option ncua-switch__option--right ncua-switch__option--<?= $checked['mileageDeleteFl']['n'] ? 'active' : 'inactive' ?>">
                                        <input class="ncua-switch__radio ncua-switch__radio--right" type="radio" name="mileageDeleteFl" value="n" <?= $checked['mileageDeleteFl']['n'] ?> />
                                        <span class="ncua-switch__label">사용안함</span>
                                    </label>
                                </div>
                            </div>
                        </td>
                    </tr>
                    <tr class="js-miliage-use-tr">
                        <th>
                            <div>차감 마일리지 부족 시 처리방법</div>
                        </th>
                        <td>
                            <div class="ncua-flex-gap">
                                <label class="ncua-radio-field ncua-radio-field--xs has-text">
                                    <span class="ncua-radio-input ncua-radio-input--xs ncua-radio-field__input">
                                        <input name="mileageLackAction" type="radio" value='delete' <?= $checked['mileageLackAction']['delete'] ?> />
                                    </span>
                                    <span class="ncua-radio-field__text">마이너스 차감 후 게시글 삭제</span>
                                </label>
                                <label class="ncua-radio-field ncua-radio-field--xs has-text">
                                    <span class="ncua-radio-input ncua-radio-input--xs ncua-radio-field__input">
                                        <input name="mileageLackAction" type="radio" value='noDelete' <?= $checked['mileageLackAction']['noDelete'] ?> />
                                    </span>
                                    <span class="ncua-radio-field__text">게시글 삭제 불가</span>
                                </label>
                            </div>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </section>
</section>

