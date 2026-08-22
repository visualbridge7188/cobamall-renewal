<section class="ncua-card">
    <header class="ncua-card__header">
        <h3 class="ncua-card__title">상품상세 페이지 설정</h3>
    </header>
    <section class="ncua-card__body"> 
        <div class="ncua-table ncua-table--vertical">
            <table>
                <tbody>
                    <tr>
                        <th><div data-tooltip-seq="001">노출설정</div></th>
                        <td>
                            <div class="ncua-flex-column">
                                <div class="ncua-switch ncua-switch--xs">
                                    <label class="ncua-switch__option ncua-switch__option--left ncua-switch__option--<?= $checked['goodsPageReviewFl']['y'] ? 'active' : 'inactive' ?>">
                                        <input class="ncua-switch__radio ncua-switch__radio--left" type="radio" name="goodsPageReviewFl" value="y" <?= $checked['goodsPageReviewFl']['y'] ?> />
                                        <span class="ncua-switch__label">노출함</span>
                                    </label>
                                    <label class="ncua-switch__option ncua-switch__option--right ncua-switch__option--<?= $checked['goodsPageReviewFl']['n'] ? 'active' : 'inactive' ?>">
                                        <input class="ncua-switch__radio ncua-switch__radio--right" type="radio"  name="goodsPageReviewFl" value="n" <?= $checked['goodsPageReviewFl']['n'] ?>  />
                                        <span class="ncua-switch__label">노출안함</span>
                                    </label>
                                </div>
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <th><div data-tooltip-seq="002">플러스리뷰 노출 개수 설정</div></th>
                        <td>
                            <div class="ncua-flex-column ncua-flex-gap">
                                <div class="ncua-input ncua-input--xs ncua-text-input-unit">
                                    <div class="ncua-input-text">PC</div>
                                    <div class="ncua-input__content">
                                        <div class="ncua-input__field ncua-input__field--xs">
                                            <input type="text" name="goodsViewPageNum[front]" class="ncua-number js-number" value="<?= $data['goodsViewPageNum']['front'] ?>" maxlength="3"> 
                                        </div>
                                    </div>
                                    <div class="ncua-input-text">개</div>
                                </div>

                                <div class="ncua-input ncua-input--xs ncua-text-input-unit">
                                    <div class="ncua-input-text">모바일</div>
                                    <div class="ncua-input__content">
                                        <div class="ncua-input__field ncua-input__field--xs">
                                            <input type="text" name="goodsViewPageNum[mobile]" class="ncua-number js-number" value="<?= $data['goodsViewPageNum']['mobile'] ?>" maxlength="3">
                                        </div>
                                    </div>
                                    <div class="ncua-input-text">개</div>
                                </div>
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <th><div>디스플레이 유형 설정</div></th>
                        <td>
                            <div>
                                <div class="ncua-border-content-layout ncua-preview-template-layout">
                                    <div class="ncua-border-content">
                                        <div class="ncua-border-content-title">상품상세 페이지</div>
                                        <div class="ncua-border-content-form">
                                            <label class="ncua-radio-field ncua-radio-field--xs has-text">
                                                <span class="ncua-radio-input ncua-radio-input--xs ncua-radio-field__input">
                                                    <input type="radio" name="goodsPageTemplate" value="01" <?= $checked['goodsPageTemplate']['01'] ?>>
                                                </span>
                                                <span>
                                                    <span class="ncua-radio-field__text">타입1 
                                                    <button type="button" class="ncua-btn ncua-btn--xs ncua-btn--secondary-gray js-btn-preview-template" data-target="goodsTemplate">
                                                        <span class="ncua-btn__label">미리보기</span>
                                                    </button>
                                                </span>
                                            </label>
                                            <div class="goodsTemplate ncua-preview-template">
                                                <img src="<?= PATH_ADMIN_GD_SHARE ?>image/plusreview_goods_template_01.png">
                                            </div>  
                                        </div>   
                                    </div>

                                    <div class="ncua-border-content">
                                        <div class="ncua-border-content-title">포토리뷰 상세(PC)</div>
                                        <div class="ncua-border-content-form">
                                            <label class="ncua-radio-field ncua-radio-field--xs has-text">
                                                <span class="ncua-radio-input ncua-radio-input--xs ncua-radio-field__input">
                                                    <input type="radio" name="layerViewTemplate" value="01" <?= $checked['layerViewTemplate']['01'] ?>> 
                                                </span>
                                                <span>
                                                    <span class="ncua-radio-field__text">타입1 
                                                    <button type="button" class="ncua-btn ncua-btn--xs ncua-btn--secondary-gray js-btn-preview-template" data-target="layerViewTemplate1">
                                                        <span class="ncua-btn__label">미리보기</span>
                                                    </button>
                                                    </span> 
                                                </span>
                                            </label>
                                            <div class="layerViewTemplate1 ncua-preview-template">
                                                <img src="<?= PATH_ADMIN_GD_SHARE ?>image/plusreview_template_01.png">
                                            </div>
                                    
                                            <label class="ncua-radio-field ncua-radio-field--xs has-text">
                                                <span class="ncua-radio-input ncua-radio-input--xs ncua-radio-field__input">
                                                    <input type="radio" name="layerViewTemplate" value="02" <?= $checked['layerViewTemplate']['02'] ?>>
                                                </span>
                                                <span>
                                                    <span class="ncua-radio-field__text">타입2 
                                                    <button type="button" class="ncua-btn ncua-btn--xs ncua-btn--secondary-gray js-btn-preview-template" data-target="layerViewTemplate2">
                                                        <span class="ncua-btn__label">미리보기</span>
                                                    </button>
                                                    </span> 
                                                </span>
                                            </label>
                                            <div class="layerViewTemplate2 ncua-preview-template">
                                                <img src="<?= PATH_ADMIN_GD_SHARE ?>image/plusreview_template_02.png">
                                            </div>
                                        </div>
                                    </div>
                                </div>                                           
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <th><div>포토리뷰 모아보기</div></th>
                        <td> 
                            <div class="ncua-flex-column ncua-flex-gap">
                                <div class="ncua-switch ncua-switch--xs">
                                    <label class="ncua-switch__option ncua-switch__option--left ncua-switch__option--<?= $checked['photoReviewCollectorFl']['y'] ? 'active' : 'inactive' ?>">
                                        <input class="ncua-switch__radio ncua-switch__radio--left" type="radio" name="photoReviewCollectorFl" value="y" <?= $checked['photoReviewCollectorFl']['y'] ?> />
                                        <span class="ncua-switch__label">노출함</span>
                                    </label>
                                    <label class="ncua-switch__option ncua-switch__option--right ncua-switch__option--<?= $checked['photoReviewCollectorFl']['n'] ? 'active' : 'inactive' ?>">
                                        <input class="ncua-switch__radio ncua-switch__radio--right" type="radio"  name="photoReviewCollectorFl" value="n" <?= $checked['photoReviewCollectorFl']['n'] ?>  />
                                        <span class="ncua-switch__label">노출안함</span>
                                    </label>
                                </div>
                                <div class="ncua-input ncua-input--textarea ncua-input--textarea--xs">
                                    <textarea class="ncua-input__textarea" name="photoReviewInfo" rows="5" placeholder="포토리뷰가 없을 경우 대신 출력될 텍스트 문구를 설정할 수 있습니다."><?= $data['photoReviewInfo'] ?></textarea>
                                </div>
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <th><div data-tooltip-seq="003">노출정보 설정</div></th>
                        <td>
                            <div class="ncua-flex-gap">
                                <label class="ncua-checkbox-field ncua-checkbox-field--xs has-text">
                                    <span class="ncua-checkbox-input ncua-checkbox-input--xs ncua-checkbox-field__input">
                                        <input type="checkbox" name="showWriterFl" value="y" <?= $checked['showWriterFl']['y'] ?>/>
                                    </span>
                                    <span><span class="ncua-checkbox-field__text">작성자</span></span>
                                </label>
                                <label class="ncua-checkbox-field ncua-checkbox-field--xs has-text">
                                    <span class="ncua-checkbox-input ncua-checkbox-input--xs ncua-checkbox-field__input">
                                        <input type="checkbox" name="showRegDtFl" value="y" <?= $checked['showRegDtFl']['y'] ?> />
                                    </span>
                                    <span><span class="ncua-checkbox-field__text">작성일</span></span>
                                </label>
                            </div>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </section>
</section>
