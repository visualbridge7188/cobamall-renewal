<section class="ncua-card board-register-function-setting">
    <header class="ncua-card__header">
        <h4 class="ncua-card__title">기능설정</h4>
        <p class="ncua-card__sub-info"><strong>*는 필수 입력</strong> 항목입니다.</p>
    </header>
    <section class="ncua-card__body board-template-list">
        <div class="ncua-table ncua-table--vertical">
            <table>
                <tr>
                    <th><div data-tooltip-seq="012">상품 연동</div></th>
                    <td>
                        <div class="ncua-flex-column ncua-flex-gap ncua-align-items-flex-start">
                            <div class="ncua-switch ncua-switch--xs <?= ($goodsBoard === 'y') ? 'ncua-switch--disabled' : '' ?>">
                                <label class="ncua-switch__option ncua-switch__option--left <?= gd_isset($checked['bdGoodsFl']['y']) ? 'ncua-switch__option--active' : 'ncua-switch__option--inactive' ?>">
                                    <input class="ncua-switch__radio ncua-switch__radio--left" type="radio" name="bdGoodsFl" value="y" <?= gd_isset($checked['bdGoodsFl']['y']) ? 'checked' : '' ?> <?= ($goodsBoard === 'y') ? 'disabled' : '' ?> />
                                    <span class="ncua-switch__label">사용</span>
                                </label>
                                <label class="ncua-switch__option ncua-switch__option--right <?= gd_isset($checked['bdGoodsFl']['n']) ? 'ncua-switch__option--active' : 'ncua-switch__option--inactive' ?>">
                                    <input class="ncua-switch__radio ncua-switch__radio--right" type="radio" name="bdGoodsFl" value="n" <?= gd_isset($checked['bdGoodsFl']['n']) ? 'checked' : '' ?> <?= ($goodsBoard === 'y') ? 'disabled' : '' ?> />
                                    <span class="ncua-switch__label">사용안함</span>
                                </label>
                            </div>
                            <div class="ncua-flex ncua-flex-gap ncua-align-items-flex-start">
                                상품/주문연동: 
                                <label class="ncua-radio-field ncua-radio-field--xs has-text">
                                    <span class="ncua-radio-input ncua-radio-input--xs ncua-radio-field__input">
                                        <input type="radio" name="bdGoodsType" value="goods" <?= gd_isset($checked['bdGoodsType']['goods']) ?> <?=$disabled['bdGoodsType']['goods']?> />
                                    </span>
                                    <span><span class="ncua-radio-field__text">상품</span></span>
                                </label>
                                <?php if(!$isGoodsQa) {?>
                                    <label class="ncua-radio-field ncua-radio-field--xs has-text">
                                        <span class="ncua-radio-input ncua-radio-input--xs ncua-radio-field__input">
                                            <input type="radio" name="bdGoodsType" value="order" <?= gd_isset($checked['bdGoodsType']['order']) ?> <?= gd_isset($disabled['bdGoodsType']['order']) ?> />
                                        </span>
                                        <span><span class="ncua-radio-field__text">주문상품</span></span>
                                    </label>
                                <?php }?>
                                
                                <?php if($isGoodsReview === false) {?>
                                    <label class="ncua-checkbox-field ncua-checkbox-field--xs has-text js-bdGoodsTypeOrderDuplication display-none">
                                        <span class="ncua-checkbox-input ncua-checkbox-input--xs ncua-checkbox-field__input">
                                            <input type="checkbox" name="bdGoodsTypeOrderDuplication" value="y" <?= gd_isset($checked['bdGoodsType']['orderDuplication']) ?> <?= gd_isset($disabled['bdGoodsTypeOrderDuplication']) ?> />
                                        </span>
                                        <span><span class="ncua-checkbox-field__text">주문내역 중복 허용</span></span>
                                    </label>
                                <?php }?>
                            </div>
                        </div>
                    </td>
                </tr>
                <?php if($isGoodsReview) {?>
                <tr>
                    <th><div data-tooltip-seq="026">중복작성 제한</div></th>
                    <td>
                        <div class="ncua-flex-gap">
                            <label class="ncua-radio-field ncua-radio-field--xs has-text">
                                <span class="ncua-radio-input ncua-radio-input--xs ncua-radio-field__input">
                                    <input type="radio" name="bdReviewDuplicateLimit" value="free" <?= $checked['bdReviewDuplicateLimit']['free'] ?> />
                                </span>
                                <span><span class="ncua-radio-field__text">제한없음</span></span>
                            </label>
                            <label class="ncua-radio-field ncua-radio-field--xs has-text">
                                <span class="ncua-radio-input ncua-radio-input--xs ncua-radio-field__input">
                                    <input type="radio" name="bdReviewDuplicateLimit" value="one" <?= $checked['bdReviewDuplicateLimit']['one'] ?> />
                                </span>
                                <span><span class="ncua-radio-field__text">1회만 작성 가능하도록 제한</span></span>
                            </label>
                        </div>
                    </td>
                </tr>
                <tr>
                    <th><div>후기작성 예외상품</div></th>
                    <td>
                        <div>
                            <div id="presentFlExcept_goods">
                                <div class="ncua-switch ncua-switch--xs">
                                    <label class="ncua-switch__option ncua-switch__option--left <?=!empty($data['bdReviewExceptGoodsData']) ? 'ncua-switch__option--active' : 'ncua-switch__option--inactive'?>">
                                        <input class="ncua-switch__radio ncua-switch__radio--left" type="radio" value="goods" name="presentExceptFl" <?=!empty($checked['bdReviewExceptGoodsData']) ? 'checked' : ''?> data-except-type="goods"/>
                                        <span class="ncua-switch__label">사용함</span>
                                    </label>
                                    <label class="ncua-switch__option ncua-switch__option--right <?=empty($data['bdReviewExceptGoodsData']) ? 'ncua-switch__option--active' : 'ncua-switch__option--inactive'?>">
                                        <input class="ncua-switch__radio ncua-switch__radio--right" type="radio" value="" name="presentExceptFl" <?=empty($checked['bdReviewExceptGoodsData']) ? 'checked' : ''?> data-except-type="goods"/>
                                        <span class="ncua-switch__label">사용안함</span>
                                    </label>
                                </div>
                            </div>
                        </div>
                    </td>
                </tr>
                
                <tr id="presentFlExcept_goods_tbl" <?php if(empty($checked['bdReviewExceptGoodsData'])){?> style="display:none" <?php }?>>
                    <th><div>예외상품 설정</div></th>
                    <td>
                        <div class="ncua-search-result">
                            <div class="ncua-search-result__content">
                                <div class="ncua-table ncua-table--horizontal ncua-table--border-bottom-radius-none ncua-except-goods-table">
                                    <table id="exceptGoodsTable">
                                        <colgroup>
                                            <col width="56px">
                                            <col width="80px">
                                            <col width="88px">
                                            <col>
                                        </colgroup>
                                        <thead>
                                            <tr>
                                                <th>
                                                    <div>
                                                        <label class="ncua-checkbox-field ncua-checkbox-field--xs has-text">
                                                            <span class="ncua-checkbox-input ncua-checkbox-input--xs ncua-checkbox-field__input">
                                                                <input type="checkbox" class="js-checkall" data-target-name="exceptGoodsChk">
                                                            </span>
                                                        </label>
                                                    </div>
                                                </th>
                                                <th><div class="ncua-align-center">번호</div></th>
                                                <th><div class="ncua-align-center">이미지</div></th>
                                                <th><div>상품명</div></th>
                                            </tr>
                                        </thead>
                                        <tbody id="exceptGoods">
                                            <?php
                                            if (is_array($data['bdReviewExceptGoodsData']) && count($data['bdReviewExceptGoodsData']) > 0) {
                                                foreach ($data['bdReviewExceptGoodsData'] as $key => $val): ?>
                                                    <tr id="idExceptGoods_<?=$val['goodsNo']?>">
                                                        <td>
                                                            <div>
                                                                <label class="ncua-checkbox-field ncua-checkbox-field--xs has-text">
                                                                    <span class="ncua-checkbox-input ncua-checkbox-input--xs ncua-checkbox-field__input">
                                                                        <input type="checkbox" name="exceptGoodsChk"/>
                                                                        <input type="hidden" name="exceptGoods[]" value="<?=$val['goodsNo']?>" />
                                                                    </span>
                                                                </label>
                                                            </div>
                                                        </td>
                                                        <td><div class="ncua-align-center"><?=$key + 1?></div></td>
                                                        <td><div class="ncua-align-center"><?=gd_html_goods_image($val['goodsNo'], $val['imageName'], $val['imagePath'], $val['imageStorage'], 50, $val['goodsNm'], '_blank')?></div></td>
                                                        <td><div><a class="ncua-link" href="../goods/goods_register.php?goodsNo=<?=$val['goodsNo']?>" target="_blank"><?=$val['goodsNm']?></a></div></td>
                                                    </tr>
                                                <?php endforeach;
                                            } else { ?>
                                                <tr class="tr-no-data">
                                                    <td colspan="4" class="no-data"><div>추가된 예외상품이 없습니다.</div></td>
                                                </tr>
                                            <?php } ?>
                                        </tbody>
                                    </table>
                                </div>
                                <div class="ncua-search-result__actions ncua-search-result__actions--bottom">
                                    <div class="ncua-search-result__button-group">
                                        <button type="button" class="ncua-btn ncua-btn--xs ncua-btn--secondary" onclick="layer_except_register('goods','except');">
                                            상품 추가
                                        </button>
                                        <button type="button" class="ncua-btn ncua-btn--xs ncua-btn--destructive ncua-select-delete js-delete-exceptGoods">
                                            선택 삭제
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </td>
                </tr>
                <?php }?>
                
                <tr class="if-is-gallery_default-show">
                    <th><div>게시글 작성 시 별점</div></th>
                    <td>
                        <div>
                            <div>
                                <div class="ncua-switch ncua-switch--xs">
                                    <label class="ncua-switch__option ncua-switch__option--left <?= gd_isset($checked['bdGoodsPtFl']['y']) ? 'ncua-switch__option--active' : 'ncua-switch__option--inactive' ?>">
                                        <input class="ncua-switch__radio ncua-switch__radio--left" type="radio" name="bdGoodsPtFl" value="y" <?= gd_isset($checked['bdGoodsPtFl']['y']) ?> />
                                        <span class="ncua-switch__label">사용</span>
                                    </label>
                                    <label class="ncua-switch__option ncua-switch__option--right <?= gd_isset($checked['bdGoodsPtFl']['n']) ? 'ncua-switch__option--active' : 'ncua-switch__option--inactive' ?>">
                                        <input class="ncua-switch__radio ncua-switch__radio--right" type="radio" name="bdGoodsPtFl" value="n" <?= gd_isset($checked['bdGoodsPtFl']['n']) ?> />
                                        <span class="ncua-switch__label">사용안함</span>
                                    </label>
                                </div>
                            </div>
                        </div>
                    </td>
                </tr>
                
                <tr>
                    <th><div>게시글 추천</div></th>
                    <td>
                        <div>
                            <span>
                                <div class="ncua-switch ncua-switch--xs">
                                    <label class="ncua-switch__option ncua-switch__option--left <?= gd_isset($checked['bdRecommendFl']['y']) ? 'ncua-switch__option--active' : 'ncua-switch__option--inactive' ?>">
                                        <input class="ncua-switch__radio ncua-switch__radio--left" type="radio" name="bdRecommendFl" value="y" <?= gd_isset($checked['bdRecommendFl']['y']) ?> />
                                        <span class="ncua-switch__label">사용</span>
                                    </label>
                                    <label class="ncua-switch__option ncua-switch__option--right <?= gd_isset($checked['bdRecommendFl']['n']) ? 'ncua-switch__option--active' : 'ncua-switch__option--inactive' ?>">
                                        <input class="ncua-switch__radio ncua-switch__radio--right" type="radio" name="bdRecommendFl" value="n" <?= gd_isset($checked['bdRecommendFl']['n']) ?> />
                                        <span class="ncua-switch__label">사용안함</span>
                                    </label>
                                </div>
                            </span>
                        </div>
                    </td>
                </tr>

                <tr>
                    <th><div data-tooltip-seq="013">기본 게시글 양식 설정</div></th>
                    <td>
                        <div class="ncua-gap-4">
                            <span class="ncua-select ncua-select--xs board-register-select-box-width-240">
                                <span class="ncua-select__content">
                                    <?= gd_select_box('bdTemplateSno', 'bdTemplateSno', $templateList, null, $data['bdTemplateSno'], null, null, 'ncua-select__tag'); ?>
                                </span>
                            </span>
                            <button type="button" class="ncua-btn ncua-btn--xs ncua-btn--secondary-gray js-template-register"><span class="ncua-btn__label">게시글 양식 등록</span></button>
                        </div>
                    </td>
                </tr>

                <tr>
                    <th><div data-tooltip-seq="014">말머리 기능</div></th>
                    <td>
                        <div class="ncua-flex-column ncua-align-items-flex-start">
                            <div>
                                <div class="ncua-switch ncua-switch--xs">
                                    <label class="ncua-switch__option ncua-switch__option--left <?= gd_isset($checked['bdCategoryFl']) ? 'ncua-switch__option--active' : 'ncua-switch__option--inactive' ?>">
                                        <input class="ncua-switch__radio ncua-switch__radio--left" type="radio" name="bdCategoryFl" value="y" <?= gd_isset($checked['bdCategoryFl']) ?> />
                                        <span class="ncua-switch__label">사용</span>
                                    </label>
                                    <label class="ncua-switch__option ncua-switch__option--right <?= !gd_isset($checked['bdCategoryFl']) ? 'ncua-switch__option--active' : 'ncua-switch__option--inactive' ?>">
                                        <input class="ncua-switch__radio ncua-switch__radio--right" type="radio" name="bdCategoryFl" value="n" <?= !gd_isset($checked['bdCategoryFl']) ? 'checked' : '' ?> />
                                        <span class="ncua-switch__label">사용안함</span>
                                    </label>
                                </div>
                                <div class="ncua-gap-4 category-write-info">
                                    * 글작성시 제목앞에 특정단어를 넣는 기능입니다
                                </div>
                            </div>
                            <div class="category-write" style="display:none">
                                <div class="ncua-table ncua-table--vertical ">
                                    <table>
                                        <colgroup>
                                            <col/>
                                            <col/>
                                        </colgroup>
                                        <tr>
                                            <th class="heading-function-th"><div>말머리 타이틀</div></th>
                                            <td>
                                                <div class="ncua-input ncua-input--xs">
                                                    <div class="ncua-input__content ncua-input-full-width">
                                                        <div class="ncua-input__field ncua-input__field--xs">
                                                            <input type="text" name="bdCategoryTitle" value="<?= gd_isset($data['bdCategoryTitle']) ?>" placeholder="말머리 타이틀을 입력하세요."/>
                                                        </div>
                                                    </div>
                                                </div>
                                            </td>
                                        </tr>
                                        <tr>
                                            <th class="heading-function-th"><div>말머리 입력</div></th>
                                            <td>
                                                <div class="heading-function-category-input">
                                                <?php
                                                        $bdCategory = explode(STR_DIVISION, gd_isset($data['bdCategory']));
                                                        $bdCategoryTemplateSno = explode(STR_DIVISION, gd_isset($data['bdCategoryTemplateSno']));
                                                        for ($i = 0; $i < $data['bdCategoryCount']; $i++) {
                                                            ?>
                                                            <div class="ncua-gap-8 ncua-flex heading-function-category-item">
                                                                <div class="ncua-input ncua-input--xs heading-function-select-flex-1 ncua-input-width-320">
                                                                    <div class="ncua-input__content">
                                                                        <div class="ncua-input__field ncua-input__field--xs">
                                                                            <input type="text" name="bdCategory[]" class="js-add-field-category" value="<?= $bdCategory[$i] ?>" placeholder="말머리를 입력하세요."/>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                                <span class="ncua-select ncua-select--xs board-register-select-box-width-240">
                                                                    <span class="ncua-select__content">
                                                                        <?= gd_select_box('bdCategoryTemplateSno[]', 'bdCategoryTemplateSno[]', $templateList, null, $bdCategoryTemplateSno[$i], null, null, 'ncua-select__tag'); ?>
                                                                    </span>
                                                                </span>
                                                                <?php if ($i > 0) { ?>
                                                                <div onclick="remove_category(this);" class="ncua-remove-icon-button"></div>
                                                                <?php } ?>
                                                            </div>
                                                        <?php } ?>
                                                        <button type="button" class="ncua-btn ncua-btn--xs ncua-btn--secondary-gray add-category-button" onclick="add_category();"><div class="ncua-plus-icon"></div>추가</button>
                                                </div>
                                            </td>
                                        </tr>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </td>
                </tr>
                
                <tr>
                    <th><div data-tooltip-seq="015">조회수 표시 설정</div></th>
                    <td>
                        <div class="ncua-flex-gap">
                            <label class="ncua-checkbox-field ncua-checkbox-field--xs has-text">
                                <span class="ncua-checkbox-input ncua-checkbox-input--xs ncua-checkbox-field__input">
                                    <input type="checkbox" name="bdPcHitFl" value="y" <?= gd_isset($checked['bdPcHitFl']['y']) ?> />
                                </span>
                                <span><span class="ncua-checkbox-field__text">PC쇼핑몰</span></span>
                            </label>
                            <label class="ncua-checkbox-field ncua-checkbox-field--xs has-text">
                                <span class="ncua-checkbox-input ncua-checkbox-input--xs ncua-checkbox-field__input">
                                    <input type="checkbox" name="bdMobileHitFl" value="y" <?= gd_isset($checked['bdMobileHitFl']['y']) ?> <?=gd_isset($disabled['bdMobileShopFl']['y'])?> />
                                </span>
                                <span><span class="ncua-checkbox-field__text">모바일쇼핑몰</span></span>
                            </label>
                        </div>
                    </td>
                </tr>
                
                <tr>
                    <th class="ncua-required"><div>조회당 Hit증가수</div></th>
                    <td >
                        <div class="ncua-gap-8">
                            <div class="ncua-input ncua-input--xs ncua-input-width-120">
                                <div class="ncua-input__content">
                                    <div class="ncua-input__field ncua-input__field--xs">
                                        <input type="text" name="bdHitPerCnt" value="<?= gd_isset($data['bdHitPerCnt']) ?>" class="js-number"/>
                                    </div>
                                </div>
                            </div>
                            개                            
                            <label class="ncua-checkbox-field ncua-checkbox-field--xs has-text">
                                <span class="ncua-checkbox-input ncua-checkbox-input--xs ncua-checkbox-field__input">
                                    <input type="checkbox" name="bdHitIPCheck" value="y" <?= gd_isset($checked['bdHitIPCheck']['y']) ?> />
                                </span>
                                <span><span class="ncua-checkbox-field__text">IP 중복제한</span></span>
                            </label>
                        </div>
                    </td>
                </tr>
                
                <tr>
                    <th><div>비밀글 설정</div></th>
                    <td >
                        <div class="ncua-flex-gap">
                            <label class="ncua-radio-field ncua-radio-field--xs has-text ">
                                <span class="ncua-radio-input ncua-radio-input--xs ncua-radio-field__input">
                                    <input type="radio" name="bdSecretFl" value="0" <?= gd_isset($checked['bdSecretFl'][0]) ?> />
                                </span>
                                <span><span class="ncua-radio-field__text">작성시 기본 일반글</span></span>
                            </label>
                            <label class="ncua-radio-field ncua-radio-field--xs has-text">
                                <span class="ncua-radio-input ncua-radio-input--xs ncua-radio-field__input">
                                    <input type="radio" name="bdSecretFl" value="1" <?= gd_isset($checked['bdSecretFl'][1]) ?> />
                                </span>
                                <span><span class="ncua-radio-field__text">작성시 기본 비밀글</span></span>
                            </label>
                            <label class="ncua-radio-field ncua-radio-field--xs has-text">
                                <span class="ncua-radio-input ncua-radio-input--xs ncua-radio-field__input">
                                    <input type="radio" name="bdSecretFl" value="2" <?= gd_isset($checked['bdSecretFl'][2]) ?> />
                                </span>
                                <span><span class="ncua-radio-field__text">무조건 일반글</span></span>
                            </label>
                            <label class="ncua-radio-field ncua-radio-field--xs has-text">
                                <span class="ncua-radio-input ncua-radio-input--xs ncua-radio-field__input">
                                    <input type="radio" name="bdSecretFl" value="3" <?= gd_isset($checked['bdSecretFl'][3]) ?> />
                                </span>
                                <span><span class="ncua-radio-field__text">무조건 비밀글</span></span>
                            </label>
                        </div>
                    </td>
                </tr>
                
                <tr>
                    <th class="ncua-required"><div>비밀글 제목설정</div></th>
                    <td >
                        <div class="ncua-flex-gap">
                            <label class="ncua-radio-field ncua-radio-field--xs has-text">
                                <span class="ncua-radio-input ncua-radio-input--xs ncua-radio-field__input">
                                    <input type="radio" name="bdSecretTitleFl" value="0" <?= gd_isset($checked['bdSecretTitleFl'][0]) ?> />
                                </span>
                                <span><span class="ncua-radio-field__text">제목 노출</span></span>
                            </label>
                            <label class="ncua-radio-field ncua-radio-field--xs has-text">
                                <span class="ncua-radio-input ncua-radio-input--xs ncua-radio-field__input">
                                    <input type="radio" name="bdSecretTitleFl" value="1" <?= gd_isset($checked['bdSecretTitleFl'][1]) ?> />
                                </span>
                                <span><span class="ncua-radio-field__text">제목 지정</span></span>
                            </label>
                            <div class="ncua-input ncua-input--xs ncua-input-width-320">
                                <div class="ncua-input__content">
                                    <div class="ncua-input__field ncua-input__field--xs">
                                        <input type="text" name="bdSecretTitleTxt" value="<?= gd_isset($data['bdSecretTitleTxt']) ?>"/>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </td>
                </tr>
                
                <tr class="bdSecretReply if-is-qa-hide">
                    <th class="ncua-required"><div>비밀댓글 설정</div></th>
                    <td>
                        <div class="ncua-flex-gap">
                            <label class="ncua-radio-field ncua-radio-field--xs has-text">
                                <span class="ncua-radio-input ncua-radio-input--xs ncua-radio-field__input">
                                    <input type="radio" name="bdSecretReplyFl" value="0" <?= gd_isset($checked['bdSecretReplyFl'][0]) ?> />
                                </span>
                                <span><span class="ncua-radio-field__text">작성시 기본 일반댓글</span></span>
                            </label>
                            <label class="ncua-radio-field ncua-radio-field--xs has-text">
                                <span class="ncua-radio-input ncua-radio-input--xs ncua-radio-field__input">
                                    <input type="radio" name="bdSecretReplyFl" value="1" <?= gd_isset($checked['bdSecretReplyFl'][1]) ?> />
                                </span>
                                <span><span class="ncua-radio-field__text">작성시 기본 비밀댓글</span></span>
                            </label>
                            <label class="ncua-radio-field ncua-radio-field--xs has-text">
                                <span class="ncua-radio-input ncua-radio-input--xs ncua-radio-field__input">
                                    <input type="radio" name="bdSecretReplyFl" value="2" <?= gd_isset($checked['bdSecretReplyFl'][2]) ?> />
                                </span>
                                <span><span class="ncua-radio-field__text">무조건 일반댓글</span></span>
                            </label>
                            <label class="ncua-radio-field ncua-radio-field--xs has-text">
                                <span class="ncua-radio-input ncua-radio-input--xs ncua-radio-field__input">
                                    <input type="radio" name="bdSecretReplyFl" value="3" <?= gd_isset($checked['bdSecretReplyFl'][3]) ?> />
                                </span>
                                <span><span class="ncua-radio-field__text">무조건 비밀댓글</span></span>
                            </label>
                        </div>
                    </td>
                </tr>
                
                <tr class="bdSecretReplyTitle if-is-qa-hide">
                    <th class="ncua-required"><div>비밀댓글 제목설정</div></th>
                    <td >
                        <div class="ncua-flex-gap">
                            <label class="ncua-radio-field ncua-radio-field--xs has-text">
                                <span class="ncua-radio-input ncua-radio-input--xs ncua-radio-field__input">
                                    <input type="radio" name="bdSecretReplyTitleFl" value="0" <?= gd_isset($checked['bdSecretReplyTitleFl'][0]) ?> />
                                </span>
                                <span><span class="ncua-radio-field__text">제목 노출</span></span>
                            </label>
                            <label class="ncua-radio-field ncua-radio-field--xs has-text">
                                <span class="ncua-radio-input ncua-radio-input--xs ncua-radio-field__input">
                                    <input type="radio" name="bdSecretReplyTitleFl" value="1" <?= gd_isset($checked['bdSecretReplyTitleFl'][1]) ?> />
                                </span>
                                <span><span class="ncua-radio-field__text">제목 지정</span></span>
                            </label>
                            <div class="ncua-input ncua-input--xs ncua-input-width-320">
                                <div class="ncua-input__content">
                                    <div class="ncua-input__field ncua-input__field--xs">
                                        <input type="text" name="bdSecretReplyTitle" value="<?= gd_isset($data['bdSecretReplyTitle']) ?>"/>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </td>
                </tr>
                
                <tr>
                    <th class="ncua-required"><div>게시물 시작번호</div></th>
                    <td >
                        <div class="ncua-gap-8">
                            <div class="ncua-input ncua-input--xs ncua-input-width-120">
                                <div class="ncua-input__content">
                                    <div class="ncua-input__field ncua-input__field--xs">
                                        <input type="text" name="bdStartNum" value="<?= gd_isset($data['bdStartNum']) ?>" class="js-number"/>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </td>
                </tr>
                
                <tr>
                    <th class="ncua-required"><div>NEW아이콘 효력</div></th>
                    <td >
                        <div class="ncua-gap-8">
                            <div class="ncua-input ncua-input--xs ncua-input-width-120">
                                <div class="ncua-input__content">
                                    <div class="ncua-input__field ncua-input__field--xs">
                                        <input type="text" name="bdNewFl" id="bdNewFl" value="<?= gd_isset($data['bdNewFl']) ?>" class="js-number"/>
                                    </div>
                                </div>
                            </div>
                            시간
                        </div>
                    </td>
                </tr>
                
                <tr>
                    <th class="ncua-required"><div>HOT아이콘 조건</div></th>
                    <td>
                        <div class="ncua-gap-8">
                            조회수 
                            <div class="ncua-input ncua-input--xs ncua-input-width-120">
                                <div class="ncua-input__content">
                                    <div class="ncua-input__field ncua-input__field--xs">
                                        <input type="text" name="bdHotFl" id="bdHotFl" value="<?= gd_isset($data['bdHotFl']) ?>" class="js-number"/>
                                    </div>
                                </div>
                            </div>
                            회 이상 게시글
                        </div>
                    </td>
                </tr>
            </table>
        </div>
    </section>
</section>
