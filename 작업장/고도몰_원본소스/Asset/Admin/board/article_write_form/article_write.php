<section class="ncua-card">
    <header class="ncua-card__header">
        <h4 class="ncua-card__title">게시글 <?= $mode ?></h4>
        <p class="ncua-card__sub-info"><strong>*는 필수 입력</strong> 항목입니다.</p>
    </header>
    <section class="ncua-card__body">
        <input type="hidden" name="sno" value="<?= $req['sno'] ?>">
        <input type="hidden" name="mode" value="<?= $req['mode'] ?>">

        <div class="ncua-table ncua-table--vertical">
            <table id="board-table">
                <colgroup>
                    <col/>
                    <col/>
                </colgroup>
                <tr>
                    <th><div>게시판</div></th>
                    <td>
                        <div>
                            <?php
                            if($req['mode'] == 'write' && gd_is_provider() === false) {?>
                                <span class="ncua-select ncua-select--xs ncua-input-width-320">
                                    <span class="ncua-select__content">
                                        <select name="bdId" id="bdId" class="ncua-select__tag">
                                            <?php foreach ($boardList as $data) { ?>
                                                <option value="<?= $data['bdId'] ?>" <?php if ($req['bdId'] == $data['bdId']) echo 'selected' ?>><?= $data['bdNm'] ?>(<?= $data['bdId'] ?>)</option>
                                            <?php } ?>
                                        </select>
                                    </span>
                                </span>
                            <?php }
                            else {?>
                                <?= $bdWrite['cfg']['bdNm'].'('.$bdWrite['cfg']['bdId'].')' ?>
                                <input type="hidden" name="bdId" value="<?= $bdWrite['cfg']['bdId'] ?>">
                            <?php }?>
                        </div>
                    </td>
                </tr>
                <?php if($bdWrite['cfg']['bdAnswerStatusFl'] == 'y' || $bdWrite['cfg']['bdReplyStatusFl'] == 'y'){?>
                <tr>
                    <th><div>답변 상태</div></th>
                    <td>
                        <div>
                            <span class="ncua-select ncua-select--xs ncua-input-width-80">
                                <span class="ncua-select__content">
                                    <select name="replyStatus" class="ncua-select__tag">
                                        <?php
                                        foreach ($listReplyStatus as $key => $val) { ?>
                                            <option
                                                    value="<?= $key ?>" <?php if ($bdWrite['data']['replyStatus'] == $key) echo 'selected' ?>><?= $val ?></option>
                                        <?php } ?>
                                    </select>
                                </span>
                            </span>
                        </div>
                    </td>
                </tr>
                <?php }?>
                <tr>
                    <th class="ncua-required"><div>제목</div></th>
                    <td>
                        <div>
                            <div class="ncua-input ncua-input--xs ncua-input-full-width">
                                <div class="ncua-input__field ncua-input__field--xs">
                                    <input type="text" name="subject" id="subject" value="<?= gd_isset($bdWrite['data']['subject']) ?>">
                                </div>
                            </div>
                        </div>
                    </td>
                </tr>
                <?php if(gd_is_provider() === false) {?>
                <tr> 
                    <th><div>게시글 양식</div></th>
                    <td>
                        <div class="ncua-gap-4">
                            <span class="ncua-select ncua-select--xs">
                                <span class="ncua-select__content">
                                    <?= gd_select_box('bdTemplateSno', 'bdTemplateSno', $templateList, null, null, null, null, 'ncua-select__tag') ?>
                                </span>
                            </span>
                            <button type="button" class="ncua-btn ncua-btn--xs ncua-btn--secondary-gray js-template-register">게시글 양식 등록</button>
                        </div>
                    </td>
                </tr>
                <?php }?>
                <?php if ($bdWrite['data']['canWriteGoodsSelect'] == 'y') { ?>
                    <tr>
                        <th><div>상품 선택</div></th>
                        <td>
                            <div class="ncua-flex-column ncua-flex-gap ncua-product-select-layout">
                                <?php if ($bdWrite['data']['goodsNo']) { ?>
                                <div id="selectGoods">
                                    <div class="ncua-border-product-image-layout">
                                        <div class="ncua-border-product-image">
                                            <input type="hidden" name="goodsNo[]" value="<?= $bdWrite['data']['goodsNo'] ?>">
                                            <a href="<?= URI_HOME ?>goods/goods_view.php?goodsNo=<?= $bdWrite['data']['goodsNo'] ?>" target="_blank">
                                                <img src="<?= $bdWrite['data']['goodsData']['goodsImageSrc']; ?>" width="100">
                                            </a>
                                        </div>
                                        <div class="ncua-border-product-detail">
                                            <div class="ncua-border-product-detail-item">
                                                <div class="ncua-product-label">상품명</div>
                                                <div class="ncua-product-value">
                                                    <button type="button" class="ncua-product-link" onclick="goods_register_popup('<?= $bdWrite['data']['goodsNo'] ?>' <?php if(gd_is_provider()) { echo ",'1'"; } ?>);">
                                                        <?= $bdWrite['data']['goodsData']['goodsNm'] ?>
                                                    </button>
                                                </div>
                                            </div>
                                            <div class="ncua-border-product-detail-item">
                                                <div class="ncua-product-label">판매가</div>
                                                <div class="ncua-product-value"><?= gd_currency_display($bdWrite['data']['goodsData']['goodsPrice']) ?></div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <?php } else { ?>
                                <div id="selectGoods" style="display: none;"></div>
                                <?php } ?>
                                <div class="ncua-button-group">
                                    <button type="button" class="ncua-btn ncua-btn--xs ncua-btn--secondary-gray js-add-goods">상품선택</button>
                                    <button type="button" class="ncua-btn ncua-btn--xs ncua-btn--destructive js-select-remove" <?= !$bdWrite['data']['goodsNo'] ? 'style="display: none;"' : '' ?>>삭제</button>
                                </div>
                            </div>
                        </td>
                    </tr>
                <?php } ?>
                <?php if ($bdWrite['data']['canWriteOrderSelect'] == 'y') { ?>
                    <tr>
                        <th><div>주문 선택</div></th>
                        <td>
                            <div class="ncua-flex-column ncua-flex-gap ncua-product-select-layout">
                                <?php if ($bdWrite['data']['extraData']['arrOrderGoodsData']) {
                                    foreach ($bdWrite['data']['extraData']['arrOrderGoodsData'] as $val) {
                                        ?>
                                <div id="selectOrder">
                                    <div class="ncua-border-product-image-layout">
                                        <div class="ncua-border-product-image">
                                            <input type="hidden" name="orderGoodsNo[]" value="<?= $val['sno'] ?>">
                                            <a href="<?= URI_HOME ?>goods/goods_view.php?goodsNo=<?= $val['goodsNo'] ?>" target="_blank">
                                                <img src="<?= $val['goodsImageSrc'] ?>" width="100" height="100">
                                            </a>
                                        </div>
                                        <div class="ncua-border-product-detail">
                                            <div class="ncua-border-product-detail-item">
                                                <div class="ncua-product-label">주문 정보</div>
                                                <div class="ncua-product-value">
                                                    <a class="ncua-product-link" href="<?= URI_ADMIN ?><?= gd_is_provider() ? 'provider/' : '' ?>order/order_view.php?orderNo=<?= $val['orderNo']; ?>" title="상품주문번호" target="_blank"><?=$val['orderNo']?></a> | <?= $val['orderGoodsRegDt'] ?><br>
                                                </div>
                                            </div>
                                            <div class="ncua-border-product-detail-item">
                                                <div class="ncua-product-label">주문 상품</div>
                                                <div class="ncua-product-value">
                                                    <?php if($val['goodsType'] == 'addGoods') {?>
                                                    <a class="ncua-product-link" href="javascript:void(0)" onclick="addgoods_register_popup('<?=$val['goodsNo'] ?>' <?php if(gd_is_provider()) { echo ",'1'"; } ?>);">
                                                    <?php } else {?>
                                                    <a class="ncua-product-link" href="javascript:void(0)" onclick="goods_register_popup('<?=$val['goodsNo'] ?>' <?php if(gd_is_provider()) { echo ",'1'"; } ?>);">
                                                    <?php }?>
                                                        <?= $val['goodsNm'] ?>
                                                    </a>
                                                    <br><?= $val['optionName'] ?>
                                                </div>
                                            </div>
                                            <div class="ncua-border-product-detail-item">
                                                <div class="ncua-product-label">결제 정보</div>
                                                <div class="ncua-product-value">
                                                    [<?= $val['orderStatusText'] ?>] <?= gd_currency_display($val['totalGoodsPrice']) ?>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <?php }
                                } else { ?>
                                <div id="selectOrder" style="display: none;"></div>
                                <?php } ?>
                                <div class="ncua-button-group">
                                    <button type="button" class="ncua-btn ncua-btn--xs ncua-btn--secondary-gray js-add-order">주문내역 선택</button>
                                    <button type="button" class="ncua-btn ncua-btn--xs ncua-btn--primary js-select-remove" <?= !$bdWrite['data']['extraData']['arrOrderGoodsData'] ? 'style="display: none;"' : '' ?>>삭제</button>
                                </div>
                            </div>
                        </td>
                    </tr>
                <?php } ?>
                <?php
                if ($bdWrite['cfg']['bdGoodsPtFl'] == 'y') { ?>
                <tr>
                    <th><div>별점</div></th>
                    <td>
                        <div class="ncua-flex-gap">
                            <?php for ($i = 5; $i >= 0; $i--) { ?>
                                <label for="rating<?= $i ?>" class="ncua-radio-field ncua-radio-field--xs has-text">
                                    <span class="ncua-radio-input ncua-radio-input--xs ncua-radio-field__input">
                                        <input type="radio" name="goodsPt" value="<?= $i ?>" class="radio" id="rating<?= $i ?>" <?php if ($i == $bdWrite['data']['goodsPt']) echo 'checked' ?> />
                                    </span>
                                    <span class="ncua-rating"><span class="ncua-radio-field__text" style="width:<?= $i * 20 ?>%;">별<?= $i ?></span></span>
                                </label>
                            <?php } ?>
                        </div>
                    </td>
                </tr>
                <?php } ?>
                <?php
                if ($bdWrite['cfg']['bdMobileFl'] == 'y') {
                    ?>
                    <tr>
                        <th><div>휴대폰</div></th>
                        <td>
                            <div>
                                <div class="ncua-input ncua-input--xs ncua-input-width-320">
                                    <div class="ncua-input__field ncua-input__field--xs">
                                        <input type="text" name="writerMobile" value="<?= gd_isset($bdWrite['data']['writerMobile']) ?>" />
                                    </div>
                                </div>
                            </div>
                        </td>
                    </tr>
                    <?php
                }
                if ($bdWrite['cfg']['bdEmailFl'] == 'y') {
                    ?>
                    <tr>
                        <th><div>이메일</div></th>
                        <td>
                            <div>
                                <div class="ncua-input ncua-input--xs ncua-input-width-320">
                                    <div class="ncua-input__field ncua-input__field--xs">
                                        <input type="text" name="writerEmail" value="<?= gd_isset($bdWrite['data']['writerEmail']) ?>" />
                                    </div>
                                </div>
                            </div>
                        </td>
                    </tr>
                    <?php
                }
                ?>
                <?php if ($bdWrite['cfg']['bdCategoryFl'] == 'y') { ?>
                    <tr>
                        <th><div>말머리</div></th>
                        <td>
                            <div>
                                <span class="ncua-select ncua-select--xs">
                                    <span class="ncua-select__content">
                                        <?= $bdWrite['categoryBox']; ?>
                                    </span>
                                </span>
                            </div>
                        </td>
                    </tr>
                <?php } ?>
                <?php if ($bdWrite['cfg']['bdFl'] == 'y') { ?>
                    <tr>
                        <th><div>점수</div></th>
                        <td>
                            <div>
                                <span class="ncua-select ncua-select--xs ncua-input-width-80">
                                    <span class="ncua-select__content">
                                        <select id="score" name="score" class="ncua-select__tag">
                                        <?php for ($i = 0; $i < 6; $i++) { ?>
                                        <option
                                            value="<?= $i ?>" <?php if ($bdWrite['data'][''] == $i) echo 'selected' ?>><?= $i ?></option>
                                        <?php } ?>
                                        </select>
                                    </span>
                                </span>
                            </div>
                        </td>
                    </tr>
                <?php } ?>
                <?php if ($req['mode'] != 'write') { ?>
                    <tr>
                        <th><div>게시판 이동</div></th>
                        <td>
                            <div class="ncua-flex-gap">
                                <?php if($bdWrite['data']['parentSno'] == 0) {?>
                                    <label class="ncua-checkbox-field ncua-checkbox-field--xs has-text">
                                        <span class="ncua-checkbox-input ncua-checkbox-input--xs ncua-checkbox-field__input">
                                            <input type="checkbox" id="isMove" name="isMove" value="y"/>
                                        </span>
                                        <span><span class="ncua-checkbox-field__text"><?php echo $bdWrite['cfg']['bdNm']; ?></span></span>
                                    </label>
                                    <span>-></span>
                                    <span class="ncua-select ncua-select--xs ncua-input-width-320">
                                        <span class="ncua-select__content">
                                            <select id="moveBdId" name="moveBdId" class="ncua-select__tag">
                                                <?php
                                                if (isset($moveBoardList) && is_array($moveBoardList)) {
                                                    foreach ($moveBoardList as $val) {
                                                        ?>
                                                        <option
                                                            value="<?= $val['bdId'] ?>" <?php if ($val['bdId'] == $bdWrite['cfg']['bdId'])
                                                            echo "selected='selected'" ?>><?= $val['bdNm'] . '(' . $val['bdId'] . ')' ?></option>
                                                        <?php
                                                    }
                                                }
                                                ?>
                                            </select>
                                        </span>
                                    </span>
                                    <?php }
                                    else {?>
                                답변글은 이동할 수 없습니다. 부모글을 이동하면 답변글도 자동으로 이동됩니다.
                                <?php }?>
                            </div>
                        </td>
                    </tr>
                <?php } ?>
                <?php
                if ($bdWrite['cfg']['bdEventFl'] == 'y') { ?>
                    <?php if ($bdWrite['cfg']['bdSubSubjectFl'] == 'y') { ?>
                        <tr>
                            <th><div>부가설명</div></th>
                            <td>
                                <div>
                                    <div class="ncua-input ncua-input--xs">
                                        <div class="ncua-input__field ncua-input__field--xs">
                                            <input type="text" name="subSubject" value="<?= gd_isset($bdWrite['data']['subSubject']) ?>" />
                                        </div>
                                    </div>
                                </div>
                            </td>
                        </tr>
                    <?php } ?>
                    <tr>
                        <th class="ncua-required"><div>이벤트 기간</div></th>
                        <td>
                            <div>
                                <div id="datepicker-container"></div>
                            </div>
                        </td>
                    </tr>
                <?php } ?>
                
                <?php
                if ($bdWrite['cfg']['bdUploadFl'] == 'y') {
                    ?>
                    <tr>
                        <th><div>파일첨부</div></th>
                        <td>
                            <div class="file-input-content">
                                <div id="fileInputContainer"></div>
                                <div id="fileTagContainer" class="ncua-file-tags"></div>
                            </div>
                        </td>
                    </tr>
                    <?php
                }
                ?>
                <?php
                if ($bdWrite['cfg']['bdLinkFl'] == 'y') {
                    ?>
                    <tr>
                        <th><div>링크</div></th>
                        <td>
                            <div>
                                <div class="ncua-input ncua-input--xs ncua-input-full-width">
                                    <div class="ncua-input__field ncua-input__field--xs">
                                        <input type="text" name="urlLink" value="<?= gd_isset($bdWrite['data']['urlLink']) ?>" />
                                    </div>
                                </div>
                            </div>
                        </td>
                    </tr>
                    <?php
                }
                ?>
                <tr>
                    <th><div>게시글 옵션 설정</div></th>
                    <td>
                        <div class="ncua-flex-gap">
                            <?php
                                if (gd_isset($bdWrite['data']['groupThread']) == '' && $req['mode'] != 'reply') {
                                    ?>
                                    <label for="w_isNotice" class="ncua-checkbox-field ncua-checkbox-field--xs has-text">
                                    <span class="ncua-checkbox-input ncua-checkbox-input--xs ncua-checkbox-field__input">
                                        <input type="checkbox" name="isNotice" id="w_isNotice" value="y" <?php if (gd_isset($bdWrite['data']['isNotice']) == 'y') echo 'checked="checked"' ?>
                                        <?php if (gd_isset($bdWrite['cfg']['bdKind']) == 'qa' && $bdWrite['data']['memNo'] >= 0) echo 'placeholder disabled'?>/>
                                    </span>
                                    <span><span class="ncua-checkbox-field__text">공지사항</span></span>
                                    </label>
                                    <?php
                                }
                            ?>
                            <label class="ncua-checkbox-field ncua-checkbox-field--xs has-text">
                                <span class="ncua-checkbox-input ncua-checkbox-input--xs ncua-checkbox-field__input">
                                    <input type="checkbox" name="isSecret" id="w_isSecret" value="y" <?=$bdWrite['data']['checked']['isSecret']?>
                                    <?php
                                    if($mode != '답변')
                                    if(($bdWrite['cfg']['bdSecretFl'] == 2 && gd_isset($bdWrite['data']['checked']['isSecret']) != "checked='checked'") || (gd_isset($bdWrite['data']['isNotice']) != 'y' && $bdWrite['cfg']['bdSecretFl'] == 3 && gd_isset($bdWrite['data']['checked']['isSecret']) == "checked='checked'")) echo 'placeholder disabled' ?> />
                                <?php if($mode != '답변') { ?>
                                <input type="hidden" name="isSecret" id="w_isSecret" <?=$bdWrite['data']['checked']['isSecret']?>/>
                                <?php } ?>
                                </span>
                                <span><span class="ncua-checkbox-field__text">비밀글</span></span>
                            </label>
                        </div>
                    </td>
                </tr>
                <tr>
                    <th class="ncua-required"><div>내용</div></th>
                    <td>
                        <div>
                            <textarea
                            data-godo-editor="article-write-editor"
                            data-height-min="412"
                            data-height-max="600"
                            data-height-resize="true"
                            name="contents" id="article-write-editor" rows="10"><?= gd_isset($bdWrite['data']['contents']); ?></textarea>
                        </div>
                    </td>
                </tr>
            </table>
        </div>
    </section>
</section>

<script type="text/javascript">
    const categoryElement = document.getElementById('category')
    if (categoryElement) {
        categoryElement.classList.add('ncua-select__tag')
        categoryElement.classList.remove('form-control')
    }
</script>
