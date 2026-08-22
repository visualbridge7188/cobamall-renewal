<section class="ncua-card">
    <header class="ncua-card__header">
        <h3 class="ncua-card__title">플러스리뷰 게시글 수정</h3>
    </header>
    <section class="ncua-card__body"> 
        <div class="ncua-table ncua-table--vertical">
            <table>
                <tbody>
                    <tr>
                        <th><div>승인</div></th>
                        <td>
                            <div class="ncua-flex-gap">
                                <label class="ncua-radio-field ncua-radio-field--xs has-text">
                                    <span class="ncua-radio-input ncua-radio-input--xs ncua-radio-field__input">
                                        <input type="radio" name="applyFl" value="y" <?= $data['applyFl'] == 'y' ? 'checked':'' ?>>
                                    </span>
                                    <span class="ncua-radio-field__text">승인</span>
                                </label>
                                <label class="ncua-radio-field ncua-radio-field--xs has-text">
                                    <span class="ncua-radio-input ncua-radio-input--xs ncua-radio-field__input">
                                        <input type="radio" name="applyFl" value="n" <?= $data['applyFl'] == 'n' ? 'checked':'' ?>>
                                    </span>
                                    <span class="ncua-radio-field__text">미승인</span>
                                </label>
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <th><div>상품정보</div></th>
                        <td>
                            <div>
                                <div class="ncua-border-product-image-layout">
                                    <div class="ncua-border-product-image">
                                        <a href="<?= URI_HOME ?>goods/goods_view.php?goodsNo=<?= $data['goodsNo'] ?>" target="_blank">
                                            <img src="<?= $data['goodsImageSrc']; ?>" width="100">
                                        </a>
                                    </div>
                                    <div class="ncua-border-product-detail">
                                        <div class="ncua-border-product-detail-item">
                                            <div class="ncua-product-label">상품명</div>
                                            <div class="ncua-product-value ncua-product-name" onclick="goods_register_popup('<?= $data['goodsNo']; ?>' <?php if (gd_is_provider()) {
                                                echo ",'1'";
                                            } ?>);">
                                                <?= $data['goodsNm'] ?>
                                            </div>
                                        </div>
                                        <div class="ncua-border-product-detail-item">
                                            <div class="ncua-product-label">판매가</div>
                                            <div class="ncua-product-value"><?= gd_currency_display($data['goodsPrice']) ?></div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </td>
                    </tr>
                    <?php if ($config['addFormFl'] == 'y' || $config['displayOptionFl'] == 'y') { ?>
                    <tr>
                        <th><div>추가정보/옵션</div></th>
                        <td>
                            <div>
                                <div class="ncua-border-content-layout ncua-preview-template-layout">
                                <?php foreach ($config['serviceAddForm'] as $val) { ?>
                                    <div class="ncua-border-content">
                                        <div class="ncua-border-content-title"><?= $val['labelName'] ?></div>
                                        <input type="hidden" name="addFormLabel[]" value="<?= $val['labelName'] ?>">
                                        <div class="ncua-border-content-form">
                                        <?php if ($val['inputType'] == 'select') { ?>
                                            <div class="ncua-select ncua-select--xs">
                                                <span class="ncua-select__content">
                                                    <select class="ncua-select__tag" name="addFormValue[]" <?php if ($val['requireFl'] == 'y') echo 'required' ?>>
                                                    <?php foreach ($val['labelValue'] as $opt) { ?>
                                                        <option value="<?= $opt ?>" <?php if ($opt == $data['addFormData'][$val['labelName']]) echo 'selected' ?>><?= $opt ?></option>
                                                    <?php } ?>
                                                    </select>
                                                </span>
                                            </div>
                                        <?php } else { ?>
                                            <div class="ncua-input ncua-input--xs ncua-input-width-320">
                                                <div class="ncua-input__content">
                                                    <div class="ncua-input__field ncua-input__field--xs">
                                                        <input type="text" name="addFormValue[]" placeholder="<?= $val['labelValue'][0] ?>" value="<?= $data['addFormData'][$val['labelName']] ?>" <?php if ($val['requireFl'] == 'y') echo 'required' ?>/>
                                                    </div>
                                                </div>
                                            </div>
                                        <?php } ?>
                                        </div>
                                    </div>
                                <?php } ?>
                                </div>
                            </div>
                        </td>
                    </tr>
                    <?php } ?>
                    <?php if ($config['pointFl'] == 'y') { ?>
                    <tr>
                        <th><div>평가</div></th>
                        <td>
                            <div class="ncua-flex-gap">
                            <?php for ($i = 5; $i >= 0; $i--) { ?>
                                <label for="rating<?= $i ?>" class="ncua-radio-field ncua-radio-field--xs has-text">
                                    <span class="ncua-radio-input ncua-radio-input--xs ncua-radio-field__input">
                                        <input type="radio" name="goodsPt" value="<?= $i ?>" class="radio" id="rating<?= $i ?>" <?php if ($i == $data['goodsPt']) echo 'checked' ?> name="rating"> 
                                    </span>
                                    <span>
                                        <span class="ncua-rating"><span style="width:<?= $i * 20 ?>%;">별<?= $i ?></span></span>
                                    </span>
                                </label>
                            <?php } ?>
                            </div>
                        </td>
                    </tr>
                    <?php } ?>
                    <tr>
                        <th><div>파일첨부</div></th>
                        <td>
                            <div class="ncua-flex-column ncua-flex-gap">
                                <div class="file-input-content">
                                    <div id="fileInputContainer"></div>
                                    <div id="fileTagContainer" class="ncua-file-tags">
                                    </div>
                                </div>
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <th><div>내용</div></th>
                        <td>
                            <div>
                                <div class="ncua-input ncua-input--textarea ncua-input--textarea--xs">
                                    <textarea class="ncua-input__textarea" name="contents" rows="10"> <?= $data['contents'] ?></textarea>
                                </div>
                            </div>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </section>
</section>
  