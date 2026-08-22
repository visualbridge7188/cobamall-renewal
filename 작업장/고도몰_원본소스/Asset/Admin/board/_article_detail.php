<section class="ncua-card">
    <header class="ncua-card__header">
        <h4 class="ncua-card__title"><?=($isShow == 'y') ? '게시글 보기' : '상세보기'; ?></h4>
    </header>
    <section class="ncua-card__body">
        <div class="ncua-table ncua-table--vertical">
            <table>
                <colgroup>
                    <col width="240px">
                    <col>
                    <col width="240px">
                    <col>
                </colgroup>
                <tr>
                    <th><div>게시판</div></th>
                    <td colspan="3">
                        <div><?= $bdView['cfg']['bdNm'] ?></div>
                    </td>
                </tr>
                <?php if($listType != 'memo') { ?>
                <tr>
                    <th><div>제목</div></th>
                    <td>
                        <div class="article-detail-title">
                        <?php if ($bdView['data']['isNotice'] == 'y')
                            echo gd_isset($bdView['cfg']['ncds']['bdIconNotice']);
                        ?>
                        <?php if ($bdView['data']['isSecret'] == 'y')
                            echo gd_isset($bdView['cfg']['ncds']['bdIconSecret']);
                        ?>
                        <?php if ($bdView['data']['isNew'] == 'y')
                            echo gd_isset($bdView['cfg']['ncds']['bdIconNew']); ?>
                        <?php if ($bdView['data']['isHot'] == 'y')
                            echo gd_isset($bdView['cfg']['ncds']['bdIconHot']); ?>
                        <?php if ($bdView['data']['isFile'] == 'y')
                            echo gd_isset($bdView['cfg']['ncds']['bdIconFile']); ?>

                        <?= $bdView['data']['subject'] ?></div>
                    </td>
                    <th><div>등록시간</div></th>
                    <td>
                        <div><?= $bdView['data']['regDt']; ?></div>
                    </td>
                </tr>

                <?php if ($bdView['cfg']['bdEventFl'] == 'y') { ?>
                    <tr>
                        <th><div>부가설명</div></th>
                        <td colspan="3">
                            <div><?= $bdView['data']['subSubject'] ?></div>
                        </td>
                    </tr>
                    <tr>
                        <th><div>이벤트 기간</div></th>
                        <td colspan="3">
                            <div><?= $bdView['data']['eventStart'] ?> ~ <?= $bdView['data']['eventEnd'] ?></div>
                        </td>
                    </tr>
                <?php } ?>
                <?php if ($bdView['cfg']['bdCategoryFl'] == 'y') { ?>
                    <tr>
                        <th><div>말머리</div></th>
                        <td colspan="3">
                            <div><?= gd_isset($bdView['data']['category'], '-') ?></div>
                        </td>
                    </tr>
                <?php } ?>

                <tr>
                    <th><div>작성자</div></th>
                    <td>
                        <div>
                        <?php if ($bdView['data']['memNo'] > 0 && !gd_is_provider()) {
                            echo "<a   class='js-layer-crm hand' data-member-no='" . $bdView['data']['memNo'] . "' >";
                            $aTagClose = '</a>';
                        } ?>
                            <?= $bdView['data']['writer'] .$aTagClose ?>
                        </div>
                    </td>
                    <th><div>아이피</div></th>
                    <td>
                        <div><?= $bdView['data']['writerIp'] ?></div>
                    </td>
                </tr>

                <?php
                if ($bdView['cfg']['bdMobileFl'] == 'y') {
                    ?>
                    <tr>
                        <th><div>휴대폰</div></th>
                        <td colspan="3">
                            <div><?= gd_isset($bdView['data']['writerMobile']) ?></div>
                        </td>
                    </tr>
                    <?php
                }
                if ($bdView['cfg']['bdEmailFl'] == 'y') {
                    ?>
                    <tr>
                        <th><div>이메일</div></th>
                        <td colspan="3">
                            <div><?= gd_isset($bdView['data']['writerEmail']) ?></div>
                        </td>
                    </tr>
                    <?php
                }
                ?>
                <?php if ($bdView['cfg']['goodsType'] == 'goods' && $bdView['data']['goodsNo']) { ?>
                    <tr>
                        <th><div>상품정보</div></th>
                        <td colspan="3">
                            <div>
                                <div class="ncua-border-product-image-layout">
                                    <div class="ncua-border-product-image">
                                        <a href="<?= URI_HOME ?>goods/goods_view.php?goodsNo=<?=$bdView['data']['goodsNo']?>" target="_blank">
                                            <img src="<?= $bdView['data']['goodsData']['goodsImageSrc']; ?>" width="100">
                                        </a>
                                    </div>
                                    <div class="ncua-border-product-detail">
                                        <div class="ncua-border-product-detail-item">
                                            <div class="ncua-product-label">상품명</div>
                                            <div class="ncua-product-value ncua-product-name" onclick="goods_register_popup('<?=$bdView['data']['goodsNo']; ?>' <?php if (gd_is_provider()) { echo ",'1'"; } ?>);">
                                                <?=$bdView['data']['goodsData']['goodsNm']?>
                                            </div>
                                        </div>
                                        <div class="ncua-border-product-detail-item">
                                            <div class="ncua-product-label">판매가</div>
                                            <div class="ncua-product-value"><?=gd_currency_display($bdView['data']['goodsData']['goodsPrice'])?></div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </td>
                    </tr>
                <?php } ?>

                <?php if($bdView['cfg']['goodsType'] == 'order' && $bdView['data']['extraData']['arrOrderGoodsData']) {?>
                    <tr>
                        <th><div>주문 정보</div></th>
                        <td colspan="3">
                            <div>
                                <?php
                                foreach($bdView['data']['extraData']['arrOrderGoodsData'] as $val) {
                                    $addGoodsInfo = '';
                                    if($val['goodsType'] == 'addGoods') {
                                        $addGoodsIcon = '<span class="label label-default">추가</span>&nbsp;';
                                    }
                                ?>
                                <div class="ncua-border-product-image-layout">
                                    <div class="ncua-border-product-image">
                                        <a href="<?=URI_HOME?>goods/goods_view.php?goodsNo=<?=$val['goodsNo']?>" target="_blank">
                                            <img src="<?=$val['goodsImageSrc']?>" width="100" height="100">
                                        </a>
                                    </div>
                                    <div class="ncua-border-product-detail">
                                        <div class="ncua-border-product-detail-item">
                                            <div class="ncua-product-label">주문 정보</div>
                                            <div class="ncua-product-value">
                                                <?php if(empty($val['orderNo']) === false){ ?>
                                                <a class="ncua-product-link" href="<?= URI_ADMIN ?><?= gd_is_provider() ? 'provider/' : '' ?>order/order_view.php?orderNo=<?= $val['orderNo']; ?>" title="상품주문번호" target="_blank"><?=$val['orderNo']?></a> | <?=$val['orderGoodsRegDt']?>
                                                <?php } ?>
                                            </div>
                                        </div>
                                        <div class="ncua-border-product-detail-item">
                                            <div class="ncua-product-label">주문 상품</div>
                                            <div class="ncua-product-value">
                                                <?php if($val['goodsType'] == 'addGoods') {?>
                                                <a class="ncua-product-link" href="javascript:void(0)" onclick="addgoods_register_popup('<?=$val['goodsNo']; ?>' <?php if(gd_is_provider()) { echo ",'1'"; } ?>);">
                                                <?php } else {?>
                                                <a class="ncua-product-link" href="javascript:void(0)" onclick="goods_register_popup('<?=$val['goodsNo']; ?>' <?php if(gd_is_provider()) { echo ",'1'"; } ?>);">
                                                <?php }?>
                                                    <?=$addGoodsIcon.$val['goodsNm']?>
                                                </a>
                                                <?php if(!empty($val['optionName'])) { ?>
                                                <br><?=$val['optionName']?>
                                                <?php } ?>
                                            </div>
                                        </div>
                                        <div class="ncua-border-product-detail-item">
                                            <div class="ncua-product-label">결제 정보</div>
                                            <div class="ncua-product-value">[<?=$val['orderStatusText']?>] <?=gd_currency_display($val['totalGoodsPrice'])?></div>
                                        </div>
                                    </div>
                                </div>
                                <?php } ?>
                            </div>
                        </td>
                    </tr>
                <?php }?>
                <?php
                if ($bdView['cfg']['bdGoodsPtFl'] == 'y') {?>
                    <tr>
                        <th><div>별점</div></th>
                        <td colspan="3">
                            <div>
                                <span class="ncua-rating"><span style="width:<?=$bdView['data']['goodsPt']*20?>%;">별</span></span>
                            </div>
                        </td>
                    </tr>
                <?php }?>

                <?php if ($bdView['data']['uploadedFile']) { ?>
                    <tr>
                        <th><div>파일첨부</div></th>
                        <td colspan="3">
                            <div>
                                <ul>
                                    <?php foreach ($bdView['data']['uploadedFile'] as $val) { ?>
                                        <li><a href="../board/download.php?bdId=<?= $bdView['cfg']['bdId'] ?>&sno=<?= $bdView['data']['sno'] ?>&fid=<?= $val['fid'] ?>"><?= $val['name'] ?></a></li>
                                    <?php } ?>
                                </ul>
                            </div>
                        </td>
                    </tr>
                <?php } ?>
                <?php
                if ($bdView['cfg']['bdLinkFl'] == 'y') {
                    ?>
                    <tr>
                        <th><div>링크</div></th>
                        <td colspan="3">
                            <div>
                                <a href="<?= gd_isset($bdView['data']['urlLink']) ?>"><?= gd_isset($bdView['data']['urlLink']) ?></a>
                            </div>
                        </td>
                    </tr>
                    <?php
                }
                ?>
                <?php } ?>
                <?php if($listType == 'memo') { ?>
                <tr>
                    <th><div>작성자</div></th>
                    <td><div><?= $bdView['data']['writerNm']; ?></div></td>
                    <th><div>등록시간</div></th>
                    <td><div><?= $bdView['data']['regDt']; ?></div></td>
                </tr>
                <?php } ?>
                <tr>
                    <th><div>내용</div></th>
                    <td colspan="3">
                        <div class="article-detail-content">    
                            <?= $listType != 'memo' ? $bdView['data']['workedContents'] : $bdView['data']['memo']; ?>
                        </div>
                    </td>
                </tr>
                <?php if ($bdView['cfg']['bdMemoFl'] == 'y' && $listType == 'board' && $isShow == 'y') { ?>
                    <tr>
                        <th><div>댓글</div></th>
                        <td colspan="3">
                            <div class="ncua-memo-wrap">
                                <div class="ncua-memo-write">
                                    <form name="frmMemoWrite" action="memo_ps.php" method="post" target="ifrmProcess">
                                        <input type="hidden" name="bdId" value="<?= $bdView['cfg']['bdId'] ?>">
                                        <input type="hidden" name="bdSno" value="<?= $req['sno'] ?>">
                                        <input type="hidden" name="mode" value="write">
                                        <div>
                                            <?php echo $hiddenCheckboxInWrite?>
                                            <label class="ncua-checkbox-field ncua-checkbox-field--xs has-text">
                                                <span class="ncua-checkbox-input ncua-checkbox-input--xs ncua-checkbox-field__input">
                                                    <input type="checkbox" name="isSecretReplyInWrite" value="y" <?php echo $checkSecretReplyView?> />
                                                </span>
                                                <span><span class="ncua-checkbox-field__text">비밀 댓글로 작성</span></span>
                                            </label>
                                        </div>
                                        <div class="ncua-input ncua-input--textarea ncua-input--textarea--xs">
                                            <textarea class="ncua-input__textarea" placeholder="댓글을 입력해주세요." name="memo" required></textarea>
                                        </div>
                                        <button type="submit" class="ncua-btn ncua-btn--xs ncua-btn--secondary js-btn-memo-save pull-left">등록</button>
                                    </form>
                                </div>
                                <?php if ($bdView['cfg']['bdMemoFl'] == 'y' && $bdView['data']['memoList']) { ?>
                                <div class="ncua-memo-list">
                                    <div class="ncua-memo-count">총 댓글 수 : <?= $bdView['data']['memoCnt'] ?></div>
                                    <div class="ncua-table ncua-table--horizontal">
                                        <table>
                                            <colgroup>
                                                <col width="15%">
                                                <col>
                                                <col width="15%">
                                                <col width="180px">
                                            </colgroup>
                                            <thead>
                                                <tr>
                                                    <th><div>작성자</div></th>
                                                    <th><div>내용</div></th>
                                                    <th><div>작성일</div></th>
                                                    <th><div>편집</div></th>
                                                </tr>
                                            </thead>
                                            <?php foreach ($bdView['data']['memoList'] as $val) {
                                                if ($val['isShow'] == 'y') {
                                                ?>
                                                <tr>
                                                    <td>
                                                        <div>
                                                        <?= $val['writer'] ?>
                                                        </div>
                                                    </td>
                                                    <td>
                                                        <div>
                                                            <div class="ncua-memo-content-layout">
                                                                <form name="frmMemo<?= $val['sno'] ?>" action="memo_ps.php" method="post" target="ifrmProcess">
                                                                    <input type="hidden" name="mode">
                                                                    <input type="hidden" name="bdId" value="<?= $bdView['cfg']['bdId'] ?>">
                                                                    <input type="hidden" name="bdSno" value="<?= $req['sno'] ?>">
                                                                    <input type="hidden" name="sno" value="<?= $val['sno'] ?>">
                                                                    <input type="hidden" name="memo" value="">
                                                                    <div class="js-text-memo ncua-memo-text">
                                                                        <?php
                                                                        if ($val['isSecretReply'] == 'y') {
                                                                            echo gd_isset($bdView['cfg']['ncds']['bdIconSecret']);
                                                                        } ?>

                                                                        <?php if ($val['gapReply']) {
                                                                            echo $val['gapReply'] . $bdView['cfg']['ncds']['bdIconReply'];
                                                                        } ?>
                                                                        <?= $val['workedMemo'] ?>
                                                                    </div>
                                                                    <div class="display-none js-textarea-modify-memo form-inline">
                                                                        <div>
                                                                            <?php echo $hiddenCheckboxInModify?>
                                                                            <label class="ncua-checkbox-field ncua-checkbox-field--xs has-text">
                                                                                <span class="ncua-checkbox-input ncua-checkbox-input--xs ncua-checkbox-field__input">
                                                                                    <input type="checkbox" name="isSecretReplyInModify" value="y" <?php echo $checkSecretReplyView?> />
                                                                                </span>
                                                                                <span><span class="ncua-checkbox-field__text">비밀 댓글로 작성</span></span>
                                                                            </label>
                                                                        </div>
                                                                        <div class="ncua-input ncua-input--textarea ncua-input--textarea--xs">
                                                                            <textarea class="ncua-input__textarea" name="modifyMemo" required><?= gd_htmlspecialchars_stripslashes($val['memo']) ?></textarea>
                                                                        </div>
                                                                        <button class="ncua-btn ncua-btn--xs ncua-btn--secondary pull-left js-btn-modify">저장</button>
                                                                        <div class="clear-both"></div>
                                                                    </div>
                                                                    <div class="display-none js-textarea-reply-memo">
                                                                        <div>
                                                                            <?php echo $hiddenCheckboxInReply?>
                                                                            <label class="ncua-checkbox-field ncua-checkbox-field--xs has-text">
                                                                                <span class="ncua-checkbox-input ncua-checkbox-input--xs ncua-checkbox-field__input">
                                                                                    <input type="checkbox" name="isSecretReplyInReply" value="y" <?php echo $checkSecretReplyView?> />
                                                                                </span>
                                                                                <span><span class="ncua-checkbox-field__text">비밀 댓글로 작성</span></span>
                                                                            </label>
                                                                        </div>
                                                                        <div class="ncua-input ncua-input--textarea ncua-input--textarea--xs">
                                                                            <textarea class="ncua-input__textarea" name="replyMemo" required></textarea>
                                                                        </div>
                                                                        <button class="ncua-btn ncua-btn--xs ncua-btn--secondary pull-left js-btn-reply">저장</button>
                                                                    </div>
                                                                </form>
                                                            </div>
                                                        </div>
                                                    </td>
                                                    <td>
                                                        <div>
                                                            <?= $val['regDt'] ?>
                                                        </div>
                                                    </td>
                                                    <td>
                                                        <div class="ncua-memo-button-group">
                                                        <button class="ncua-btn ncua-btn--xxs ncua-btn--secondary-gray js-btn-memo-modify">수정</button>
                                                        <?php if (!$val['groupThread']) { ?>
                                                            <button class="ncua-btn ncua-btn--xxs ncua-btn--secondary-gray js-btn-memo-reply">답글</button>
                                                        <?php } ?>
                                                        <button class="ncua-btn ncua-btn--xxs ncua-btn--secondary-gray js-btn-delete" title="확인" data-message="정말로 삭제하시겠습니까?">삭제</button>
                                                        </div>
                                                    </td>
                                                </tr>
                                                <?php } ?>
                                            <?php } ?>
                                        </table>
                                    </div>
                                </div>
                                <?php } ?>
                            </div>
                        </td>
                    </tr>
                <?php } ?>
                <?php if($isShow == 'n') { ?>
                    <tr>
                        <th><div>신고내용</div></th>
                        <td colspan="3">
                            <div>
                                <div class="ncua-border-info-layout">
                                    <div class="ncua-border-info-item">
                                        <div class="ncua-border-info-label">신고자</div>
                                        <div class="ncua-border-info-value"><?=$reportData['memId']?></div>
                                    </div>
                                    <div class="ncua-border-info-item">
                                        <div class="ncua-border-info-label">신고일</div>
                                        <div class="ncua-border-info-value"><?=$reportData['regDt']?></div>
                                    </div>
                                    <div class="ncua-border-info-item">
                                        <div class="ncua-border-info-label">신고사유</div>
                                        <div class="ncua-border-info-value"><?=gd_htmlspecialchars_stripslashes($reportData['reportMemo'])?></div>
                                    </div>
                                    <div class="ncua-border-info-item">
                                        <div class="ncua-border-info-label">개인정보수집동의</div>
                                        <div class="ncua-border-info-value"><?=($reportData['checkCollectAgreeFl'] == 'y') ? '동의함' : '동의안함'?></div>
                                    </div>
                                </div>
                            </div>
                        </td>
                    </tr>
                <?php } ?>
            </table>
        </div>
    </section> 
</section>
