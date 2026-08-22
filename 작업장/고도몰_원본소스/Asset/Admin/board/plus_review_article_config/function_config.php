<section class="ncua-card">
    <header class="ncua-card__header">
        <h3 class="ncua-card__title">기능설정</h3>
    </header>
    <section class="ncua-card__body">
        <div class="ncua-table ncua-table--vertical">
            <table>
                <tbody>
                    <tr>
                        <th>
                            <div data-tooltip-seq="018">추가정보 입력</div>
                        </th>
                        <td>
                            <div>
                                <div class="ncua-switch ncua-switch--xs">
                                    <label class="ncua-switch__option ncua-switch__option--left ncua-switch__option--<?= $checked['addFormFl']['y'] ? 'active' : 'inactive' ?>">
                                        <input class="ncua-switch__radio ncua-switch__radio--left" type="radio" name="addFormFl" value="y" <?= $checked['addFormFl']['y'] ?> />
                                        <span class="ncua-switch__label">사용함</span>
                                    </label>
                                    <label class="ncua-switch__option ncua-switch__option--right ncua-switch__option--<?= $checked['addFormFl']['n'] ? 'active' : 'inactive' ?>">
                                        <input class="ncua-switch__radio ncua-switch__radio--right" type="radio" name="addFormFl" value="n" <?= $checked['addFormFl']['n'] ?> />
                                        <span class="ncua-switch__label">사용안함</span>
                                    </label>
                                </div>
                            </div>
                        </td>
                    </tr>
                     <!-- js-add-form-tr 노출/미노출 -->
                    <tr class="js-add-form-tr">
                        <th>
                            <div>추가정보 양식 설정</div>
                        </th>
                        <td>
                            <div class="ncua-search-result ncua-flex-column ncua-flex-gap">
                                <div class="add-info-order-buttons"></div>
                                <div class="ncua-search-result__content">
                                    <div class="ncua-table ncua-table--horizontal ncua-table--border-bottom-radius-none">
                                        <table id="addFormTable">
                                            <colgroup>
                                                <col width="73px">
                                                <col width="232px">
                                                <col width="129px">
                                                <col >
                                                <col width="80px">
                                                <col width="80px">
                                            </colgroup>
                                            <thead>
                                                <tr>
                                                    <th>
                                                        <div class="ncua-sort-item__checkbox">
                                                            <div class="ncua-sort-item__checkbox-drag-icon"></div>
                                                            <label class="ncua-checkbox-field ncua-checkbox-field--xs has-text">
                                                                <span class="ncua-checkbox-input ncua-checkbox-input--xs ncua-checkbox-field__input">
                                                                    <input type="checkbox" class="js-checkall" data-target-name="addFormNo">
                                                                </span>
                                                            </label>
                                                        </div>
                                                    </th>
                                                    <th><div class="align-left">항목명</div></th>
                                                    <th><div class="align-left">입력형태</div></th>
                                                    <th><div class="align-left">입력값</div></th>
                                                    <th><div>사용</div></th>
                                                    <th><div>필수</div></th>
                                                </tr>
                                            </thead>
                                            <input type="hidden" name="labelMaxNum" value="<?=$data['labelMaxNum']?>">
                                            <tbody>
                                            <?php
                                            $addForm = $data['addForm'];
                                            foreach ($addForm['labelName'] as $k => $v) { ?>
                                                <tr data-row="<?= $k ?>" class="ncua-sort-item">
                                                    <!-- 체크박스 -->
                                                    <td>
                                                        <div class="ncua-sort-item__checkbox">
                                                            <div class="ncua-sort-item__checkbox-drag-icon"></div>
                                                            <label class="ncua-checkbox-field ncua-checkbox-field--xs has-text">
                                                                <span class="ncua-checkbox-input ncua-checkbox-input--xs ncua-checkbox-field__input">
                                                                    <input type="checkbox" name="addFormNo[]">
                                                                </span>
                                                            </label>
                                                        </div>
                                                    </td>
                                                    <!-- 항목명 -->
                                                    <td>
                                                        <div>
                                                            <input type="hidden" name="addForm[labelNumber][<?= $k ?>]" value="<?= $addForm['labelNumber'][$k] ?>">
                                                            <div class="ncua-input ncua-input--xs ncua-input-full-width">
                                                                <div class="ncua-input__content">
                                                                    <div class="ncua-input__field ncua-input__field--xs">
                                                                        <input type="text" name="addForm[labelName][<?= $k ?>]" value="<?= $addForm['labelName'][$k] ?>">
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </td>
                                                    <!-- 입력형태 -->
                                                    <td>
                                                        <div class="ncua-select ncua-select--xs">
                                                            <span class="ncua-select__content">
                                                                <select class="ncua-select__tag" name="addForm[inputType][<?= $k ?>]">
                                                                    <option value="text" <?php if ($addForm['inputType'][$k] == 'text') echo 'selected' ?>>텍스트 입력</option>
                                                                    <option value="select" <?php if ($addForm['inputType'][$k] == 'select') echo 'selected' ?>>셀렉트 박스</option>
                                                                </select>
                                                            </span>
                                                        </div>
                                                    </td>
                                                    <!-- 입력값 -->
                                                    <td>
                                                        <div class="ncua-flex-column ncua-flex-gap">
                                                            <ul class="js-label-value-list ncua-add-info-input-value">
                                                                <!-- 입력형태 : 셀렉트 박스 -->
                                                                <?php if ($addForm['inputType'][$k] == 'select') {
                                                                    foreach ($addForm['labelValue'][$k] as $_k => $_v) {
                                                                        ?>
                                                                        <li class="ncua-add-info-input-value__item">
                                                                            <div class="ncua-input ncua-input--xs ncua-input-full-width">
                                                                                <div class="ncua-input__content">
                                                                                    <div class="ncua-input__field ncua-input__field--xs">
                                                                                        <input type="text" name="addForm[labelValue][<?= $k ?>][]" value="<?= $_v ?>">
                                                                                    </div>
                                                                                </div>
                                                                            </div>
                                                                        <?php if ($_k !== 0) { ?>
                                                                            <div class="js-btn-labal-remove-li ncua-remove-icon-button"></div>
                                                                        </li>
                                                                            <?php
                                                                        } ?>
                                                                        <?php
                                                                    }
                                                                    
                                                                } else { ?>
                                                                <!-- 입력형태 : 텍스트 입력 -->
                                                                    <li>
                                                                        <div class="ncua-input ncua-input--xs ncua-input-full-width">
                                                                            <div class="ncua-input__content">
                                                                                <div class="ncua-input__field ncua-input__field--xs">
                                                                                    <input type="text" name="addForm[labelValue][<?= $k ?>][]" value="<?= $addForm['labelValue'][$k][0] ?>">
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                    </li>
                                                                <?php } ?>
                                                            </ul>
                                                            <?php if ($addForm['inputType'][$k] == 'select') { ?>
                                                                <button type="button" class="ncua-btn ncua-btn--xs  ncua-btn--secondary-gray js-btn-labal-value-add">
                                                                    <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" fill="none" viewBox="0 0 24 24" stroke="none"><path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 5v14m-7-7h14"></path></svg>
                                                                    추가
                                                                </button>
                                                            <?php } ?>
                                                        </div>
                                                    </td>
                                                    <!-- 사용 -->
                                                    <td>
                                                        <div>
                                                            <label class="ncua-checkbox-field ncua-checkbox-field--xs has-text">
                                                                <span class="ncua-checkbox-input ncua-checkbox-input--xs ncua-checkbox-field__input">
                                                                    <input type="checkbox" name="addForm[useFl][<?= $k ?>]" value="y" <?php if ($addForm['useFl'][$k] == 'y') echo 'checked' ?>>
                                                                </span>
                                                            </label>
                                                        </div>
                                                    </td>
                                                    <!-- 필수 -->
                                                    <td>
                                                        <div>
                                                            <label class="ncua-checkbox-field ncua-checkbox-field--xs has-text">
                                                                <span class="ncua-checkbox-input ncua-checkbox-input--xs ncua-checkbox-field__input">
                                                                    <input type="checkbox" name="addForm[requireFl][<?= $k ?>]" value="y" <?php if ($addForm['requireFl'][$k] == 'y') echo 'checked' ?>>
                                                                </span>
                                                            </label>
                                                        </div>
                                                    </td>
                                                </tr>
                                            <?php } ?>
                                            </tbody>
                                        </table>
                                    </div>
                                    <div class="ncua-search-result__actions ncua-search-result__actions--bottom">
                                        <div class="ncua-search-result__button-group">
                                            <button type="button" class="ncua-btn ncua-btn--xs ncua-btn--secondary js-btn-form-add">
                                                추가
                                            </button>
                                            <button type="button"class="ncua-btn ncua-btn--xs ncua-btn--destructive ncua-select-delete js-btn-remove-tr">
                                                선택 삭제
                                            </button>  
                                        </div>
                                    </div>
                            
                                </div>
                                <ul>
                                    <li class="ncua-notice-info">추가정보 양식 설정 후 추가 시 구매자가 리뷰 작성을 할 때 아래와 같이 노출됩니다.</li>
                                    <li class="ncua-notice-info">최대 10까지 등록 가능합니다.</li>
                                    <li class="ncua-notice-info">추가정보 항목은 마우스 드래그 앤 드롭으로 순서 조정이 가능합니다.</li>
                                </ul>
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <th><div data-tooltip-seq="019">추가정보 검색 기능</div></th>
                        <td>
                            <div>
                                <div class="ncua-switch ncua-switch--xs">
                                    <label class="ncua-switch__option ncua-switch__option--left ncua-switch__option--<?= $checked['addFormSearchFl']['y'] ? 'active' : 'inactive' ?>">
                                        <input class="ncua-switch__radio ncua-switch__radio--left" type="radio" name="addFormSearchFl" value="y" <?= $checked['addFormSearchFl']['y'] ?> />
                                        <span class="ncua-switch__label">사용함</span>
                                    </label>
                                    <label class="ncua-switch__option ncua-switch__option--right ncua-switch__option--<?= $checked['addFormSearchFl']['n'] ? 'active' : 'inactive' ?>">
                                        <input class="ncua-switch__radio ncua-switch__radio--right" type="radio" name="addFormSearchFl" value="n" <?= $checked['addFormSearchFl']['n'] ?> />
                                        <span class="ncua-switch__label">사용안함</span>
                                    </label>
                                </div>
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <th><div data-tooltip-seq="020">추가정보 검색 기능 타이틀 설정</div></th>
                        <td>
                            <div>
                                <div class="ncua-input ncua-input--xs">
                                    <div class="ncua-input__content-wrap">
                                        <div class="ncua-input__content">
                                            <div class="ncua-input__field ncua-input__field--xs ncua-input-width-320">
                                                <input type="text" maxlength="20" name="addFormSearchTitle" value="<?= $data['addFormSearchTitle'] ?>" placeholder="(예시) 나와 비슷한 회원 리뷰 보기" class="add_form_search_title" data-charcount-key="addFormSearchTitle"/>
                                            </div>
                                        </div>
                                        <div class="ncua-input__field-text-count" data-charcount-text="addFormSearchTitle">
                                            <output class="ncua-input__field-text-count-current">0</output>
                                            <span>/20</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <th><div data-tooltip-seq="021">리뷰작성 안내 문구 설정</div></th>
                        <td>
                            <div>
                            <div class="ncua-input ncua-input--textarea ncua-input--textarea--xs">
                                    <textarea class="ncua-input__textarea" name="reviewPlaceHolder" rows="5" maxlength="1000" placeholder="(예시) 여러분의 리뷰는 많은 회원에게 도움이됩니다."><?= $data['reviewPlaceHolder'] ?></textarea>
                                    <div class="ncua-input__text-count-wrap">
                                        <div class="ncua-hint-text"></div>
                                        <div class="ncua-input__text-count">
                                        <span class="ncua-input__text-count-text">
                                            <!-- 텍스트 count 값을 스크립트에서 체크 -->
                                            <span class="ncua-input__text-count-text-count"></span>
                                            /1000
                                        </span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <th><div>구매한 상품 옵션 노출</div></th>
                        <td>
                            <div>
                                <div class="ncua-switch ncua-switch--xs">
                                    <label class="ncua-switch__option ncua-switch__option--left ncua-switch__option--<?= $checked['displayOptionFl']['y'] ? 'active' : 'inactive' ?>">
                                        <input class="ncua-switch__radio ncua-switch__radio--left" type="radio" name="displayOptionFl" value="y" <?= $checked['displayOptionFl']['y'] ?> />
                                        <span class="ncua-switch__label">사용함</span>
                                    </label>
                                    <label class="ncua-switch__option ncua-switch__option--right ncua-switch__option--<?= $checked['displayOptionFl']['n'] ? 'active' : 'inactive' ?>">
                                        <input class="ncua-switch__radio ncua-switch__radio--right" type="radio" name="displayOptionFl" value="n" <?= $checked['displayOptionFl']['n'] ?> />
                                        <span class="ncua-switch__label">사용안함</span>
                                    </label>
                                </div>
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <th><div>게시글 작성 시 평가</div></th>
                        <td>
                            <div>플러스리뷰는 사용자 평가 기반의 기능이므로 평가 항목은 필수입니다.</div>
                        </td>
                    </tr>
                    <tr>
                        <th><div>게시글 추천</div></th>
                        <td>
                            <div>
                                <div class="ncua-switch ncua-switch--xs">
                                    <label class="ncua-switch__option ncua-switch__option--left ncua-switch__option--<?= $checked['recommendFl']['y'] ? 'active' : 'inactive' ?>">
                                        <input class="ncua-switch__radio ncua-switch__radio--left" type="radio" name="recommendFl" value="y" <?= $checked['recommendFl']['y'] ?> />
                                        <span class="ncua-switch__label">사용함</span>
                                    </label>
                                    <label class="ncua-switch__option ncua-switch__option--right ncua-switch__option--<?= $checked['recommendFl']['n'] ? 'active' : 'inactive' ?>">
                                        <input class="ncua-switch__radio ncua-switch__radio--right" type="radio" name="recommendFl" value="n" <?= $checked['recommendFl']['n'] ?> />
                                        <span class="ncua-switch__label">사용안함</span>
                                    </label>
                                </div>
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <th><div>NEW아이콘 효력</div></th>
                        <td>
                            <div>
                                <div class="ncua-input ncua-input--xs ncua-text-input-unit">
                                    <div class="ncua-input__content">
                                        <div class="ncua-input__field ncua-input__field--xs ncua-input-width-80">
                                            <input type="text" name="bdNewFl" class="js-number" size="5" value="<?= $data['bdNewFl'] ?>" /> 
                                        </div>
                                    </div>
                                    <div class="ncua-input-text">시간</div>
                                </div>
                            </div>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </section>
</section>
