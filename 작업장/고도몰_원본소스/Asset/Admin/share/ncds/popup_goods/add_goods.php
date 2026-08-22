<article class="ncua-add-goods">
    <h2 class="ncua-popup-goods__title">등록 상품 리스트</h2>

    <div class="ncua-search-result">
        <div class="ncua-search-result__content">
            <!-- action -->
            <div class="ncua-search-result__actions">
                <div class="ncua-button-group ncua-button-group--xs ncua-sequence-btns has-border">
                    <button type="button" class="ncua-button-group__item js-moverow goodsChoice_downArrowMore" data-direction="bottom">
                        맨아래
                    </button>
                    <button type="button" class="ncua-button-group__item js-moverow goodsChoice_downArrow" data-direction="down">
                        아래
                    </button>
                    <button type="button" class="ncua-button-group__item js-moverow goodsChoice_upArrow" data-direction="up">
                        위
                    </button>

                    <button type="button" class="ncua-button-group__item js-moverow goodsChoice_upArrowMore" data-direction="top">
                        맨위
                    </button>
                </div>
                <div class="ncua-add-goods__sequence-actions">
                    <span class="action-title">선택한 상품을</span> 
                    <div class="ncua-input ncua-input--xs">
                        <div class="ncua-input__content">
                            <div class="ncua-input__field ncua-input__field--xs">
                                <input type="text" name="goodsChoice_sortText" class="goodsChoice_sortText"/>
                            </div>
                        </div>
                    </div>
                     번 위치로 
                    <button type="button" class="ncua-btn ncua-btn--xs ncua-btn--secondary-gray goodsChoice_moveBtn">이동</button>
                    <?php
                    if ($relationFl != 'm') {
                    ?>
                    <button type="button" class="ncua-btn ncua-btn--xs ncua-btn--secondary-gray goodsChoice_fixBtn">고정</button>
                    <?php
                    }
                    ?>
                </div>
            </div>
            <form id="addGoodsFrm" class="ncua-add-goods-form">
                <div class="ncua-table ncua-table--horizontal">
                    <table id="tbl_add_goods_result">
                        <thead>
                        <tr id="goodsRegisteredTrArea">
                            <th>
                                <div>
                                    <label class="ncua-checkbox-field ncua-checkbox-field--xs">
                                            <span class="ncua-checkbox-input ncua-checkbox-input--xs ncua-checkbox-field__input">
                                                <input type="checkbox" id="allCheck" value="y" onclick="all_checkbox(this,'tbl_add_goods_result')"/>
                                            </span>
                                        </label>
                                    </div>
                                </th>
                            <th><div>진열순서</div></th>
                            <th><div>이미지</div></th>
                            <th><div>상품명</div></th>
                            <th><div>판매가</div></th>
                            <th><div>공급사</div></th>
                            <th><div>재고</div></th>
                            <th><div>품절여부</div></th>
                        </tr>
                        </thead>

                        <tbody contents-length="<?=is_null($setGoodsList) ? 0 : strlen(trim($setGoodsList))?>">
                        <?php if($setGoodsList) { echo $setGoodsList; } ?>
                        </tbody>
                    </table>
                </div>
            </form>
            <!-- action -->
            <div class="ncua-search-result__actions ncua-search-result__actions--bottom">
                <div class="ncua-button-group ncua-button-group--xs ncua-sequence-btns has-border">
                    <button type="button" class="ncua-button-group__item js-moverow goodsChoice_downArrowMore" data-direction="bottom">
                        맨아래
                    </button>
                    <button type="button" class="ncua-button-group__item js-moverow goodsChoice_downArrow" data-direction="down">
                        아래
                    </button>
                    <button type="button" class="ncua-button-group__item js-moverow goodsChoice_upArrow" data-direction="up">
                        위
                    </button>

                    <button type="button" class="ncua-button-group__item js-moverow goodsChoice_upArrowMore" data-direction="top">
                        맨위
                    </button>
                </div>

                <div class="ncua-add-goods__sequence-actions"><span class="action-title">선택한 상품을</span>
                    <div class="ncua-input ncua-input--xs">
                        <div class="ncua-input__content">
                            <div class="ncua-input__field ncua-input__field--xs">
                                <input type="text" name="goodsChoice_sortText" class="goodsChoice_sortText"/>
                            </div>
                        </div>
                    </div>
                     번 위치로
                    <button type="button" class="ncua-btn ncua-btn--xs ncua-btn--secondary-gray goodsChoice_moveBtn">이동</button>
                    <?php
                    if ($relationFl != 'm') {
                        ?>
                        <button type="button" class="ncua-btn ncua-btn--xs ncua-btn--secondary-gray goodsChoice_fixBtn">고정</button>
                    <?php
                    }
                    ?>
                </div>
            </div>
        </div>
    </div>

    
</article>
