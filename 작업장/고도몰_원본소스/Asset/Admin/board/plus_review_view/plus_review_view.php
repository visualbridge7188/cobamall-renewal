<section class="ncua-card">
    <header class="ncua-card__header">
        <h3 class="ncua-card__title"><?=($isShow == 'y') ? '플러스리뷰 게시글 보기' : '상세보기'; ?></h3>
    </header>
    <form name="frmDelete" action="plus_review_ps.php" method="post" target="ifrmProcess">
        <input type="hidden" name="mode" value="<?= $mode ?>">
        <input type="hidden" name="sno" value="<?= $req['sno'] ?>">
        <input type="hidden" name="popupMode" value="<?= $req['popupMode'] ?>">
        <input type="hidden" name="queryString" value="<?= $queryString?>">
        <?php if($isShow != 'y') { ?><input type="hidden" name="goodsNo" value="<?= $data['goodsNo'] ?>"><?php } ?>
    </form>
    <section class="ncua-card__body">
        <div class="ncua-table ncua-table--vertical">
            <table>
                <colgroup>
                    <col width="240px">
                    <col >
                    <col width="240px">
                    <col >
                </colgroup>
                <tbody>
                    <?php if($listType == 'board') { ?>
                    <tr>
                        <th><div>작성자</div></th>
                        <td><div><?= $data['writer'] ?></div></td>
                        <th class="ncua-border-radius-none"><div>아이피</div></th>
                        <td><div><?= $data['writerIp'] ?></div></td>
                    </tr>

                    <tr>
                        <th><div>작성일</div></th>
                        <td><div><?=$data['regDt']?></div></td>
                        <th><div>추천</div></th>
                        <td><div><?=$data['recommend']?></div></td>
                    </tr>
                    <tr>
                        <th><div>승인</div></th>
                        <td colspan="3"><div><?= $data['applyFl'] == 'y' ? '승인':'미승인'?></div></td>
                    </tr>
                    <tr>
                        <th><div>상품정보</div></th>
                        <td colspan="3">
                            <div>
                                <div class="ncua-border-product-image-layout">
                                    <div class="ncua-border-product-image">
                                        <a href="<?= URI_HOME ?>goods/goods_view.php?goodsNo=<?= $data['goodsNo'] ?>" target="_blank">
                                            <img src="<?= $data['goodsImageSrc']; ?>" width="100">
                                        </a>
                                    </div>
                                    <div class="ncua-border-product-detail">
                                        <div class="ncua-border-product-detail-item">
                                            <div class="ncua-product-label ">상품명</div>
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
                    <?php if ($config['pointFl'] == 'y') { ?>
                        <tr>
                            <th><div>평가</div></th>
                            <td colspan="3"><div>
                                <span class="ncua-rating"><span style="width:<?= $data['goodsPt'] * 20 ?>%;">별</span></span>
                            </div></td>
                        </tr>
                    <?php } ?>
                    <tr>
                        <th><div>파일첨부</div></th>
                        <td colspan="3">
                            <div>
                            <?php
                            if ($data['uploadedFile']) {
                                ?>
                                <ul style="padding:0px">
                                    <?php foreach ($data['uploadedFile'] as $val) { ?>
                                        <li class="mgb5">
                                            <a href="/board/download.php?type=plusReview&sno=<?= $data['sno'] ?>&fid=<?= $val['fid'] ?>">
                                            <img src="<?=$val['thumSrc']?>" width="100" >
                                        <?= $val['uploadFileNm'] ?></a></li>
                                    <?php } ?>
                                </ul>
                            <?php } else { ?>
                                -
                            <?php } ?>
                            </div>
                        </td>
                    </tr>
                        <tr>
                            <th><div>추가정보/옵션</div></th>
                            <td colspan="3">
                                <div class="ncua-flex-column">
                                    <?php foreach($data['addFormData'] as $key=>$val) {?>
                                    <div><?=$key?> : <?=$val?></div>
                                    <?php }?>
                                    <span style="color:#329cff">
                                    <?php foreach($data['option'] as $val) {?>
                                    <div><?=$val['name']?> : <?=$val['value']?></div>
                                    <?php }?>
                                    </span>
                                </div>
                            </td>
                        </tr>
                    <tr>
                        <th><div>내용</div></th>
                        <td colspan="3"><div><?= $data['viewContents'] ?></div></td>
                    </tr>
                    <?php if ($config['memoFl'] == 'y' && $isShow == 'y') { ?>
                        <tr>
                            <th><div>댓글</div></th>
                            <td colspan="3">
                                <div class="ncua-memo-layout">
                                    <div class="ncua-memo-write-layout">
                                        <form name="frmMemoWrite" action="plus_review_ps.php" method="post" target="ifrmProcess">
                                            <input type="hidden" name="articleSno" value="<?= $req['sno'] ?>">
                                            <input type="hidden" name="mode" value="addMemo">
                                            <div class="ncua-input ncua-input--textarea ncua-input--textarea--xs">
                                                <textarea class="ncua-input__textarea" placeholder="댓글을 입력해주세요." name="memo" required></textarea>
                                            </div>
                                            <button type="submit" name="contentsButton" class="ncua-btn ncua-btn--xs ncua-btn--secondary">
                                                <span class="ncua-btn__label">등록</span>
                                            </button>
                                        </form>
                                    </div>
                                    <?php if ($data['memoCnt'] > 0) { ?>
                                    <div class="ncua-memo-list-layout">
                                        <div class="ncua-memo-count">총 댓글 수 : <?= $data['memoCnt'] ?></div>
                                            <div class="ncua-table ncua-table--horizontal">
                                                <table>
                                                    <colgroup>
                                                        <col width="15%">
                                                        <col>
                                                        <col width="140px">
                                                        <col width="140px">
                                                    </colgroup>
                                                    <thead>
                                                        <tr>
                                                            <th><div>작성자</div></th>
                                                            <th><div>내용</div></th>
                                                            <th><div>작성일</div></th>
                                                            <th><div>편집</div></th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        <?php foreach ($data['memoList'] as $val) {
                                                            if ($val['isShow'] == 'y') {
                                                            ?>
                                                            <tr>
                                                                <td>
                                                                    <div><?= $val['writer'] ?></div>
                                                                </td>
                                                                <td>
                                                                    <div>
                                                                        <div class="ncua-memo-content-layout">
                                                                            <form name="frmMemo<?= $val['sno'] ?>" action="plus_review_ps.php" method="post" target="ifrmProcess">
                                                                                <input type="hidden" name="mode" value="modifyMemo">
                                                                                <input type="hidden" name="articleSno" value="<?= $req['sno'] ?>">
                                                                                <input type="hidden" name="sno" value="<?= $val['sno'] ?>">
                                                                                <div class="js-text-memo">
                                                                                    <?= $val['viewMemo'] ?>
                                                                                </div>
                                                                                <div class="js-textarea-modify-memo">
                                                                                    <div class="ncua-input ncua-input--textarea ncua-input--textarea--xs">
                                                                                        <textarea class="ncua-input__textarea" name="memo" required><?= ($val['memo']) ?></textarea>
                                                                                    </div> 
                                                                                </div>
                                                                            </form>
                                                                        </div>
                                                                    </div>
                                                                </td>
                                                                <td>
                                                                    <div class="ncua-text-align-center"><?= $val['regDt'] ?></div>
                                                                </td>
                                                                <td>
                                                                    <div class="ncua-memo-button-group">
                                                                        <button type="submit" class="ncua-btn ncua-btn--xxs ncua-btn--secondary js-btn-memo-submit">
                                                                            등록
                                                                        </button>
                                                                        <button type="button" class="ncua-btn ncua-btn--xxs ncua-btn--secondary-gray js-btn-memo-modify">
                                                                            수정
                                                                        </button>
                                                                        <button type="button" class="ncua-btn ncua-btn--xxs ncua-btn--secondary-gray js-btn-memo-delete">
                                                                            삭제
                                                                        </button>
                                                                    </div>
                                                                </td>
                                                            </tr>
                                                        <?php }
                                                        }?>
                                                    </tbody>
                                                </table>
                                            </div>
                                            
                                    </div>
                                    <?php } ?>
                                </div>
                            </td>
                        </tr> 
                    <?php } ?>
                    <?php } else { ?>
                        <tr>
                            <th><div>작성자</div></th>
                            <td><div><?= $data['writerNm']; ?></div></td>
                            <th class="ncua-border-radius-none"><div>등록시간</div></th>
                            <td><div><?= $data['regDt']; ?></div></td>
                        </tr>
                        <tr>
                            <th><div>내용</div></th>
                            <td colspan="3"><div><?= $data['memo']; ?></div></td>
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
                </tbody>    
            </table>
        </div>
    </section>
</section>
<div class="ncua-card-button-group">
    <?php if($data['channel']!='naverpay' &&  $isShow == 'y') {?>
        <button type="button" class="ncua-btn ncua-btn--sm ncua-btn--secondary-gray js-btn-modify">수정</button>
    <?php }?>
        <button type="button" class="ncua-btn ncua-btn--sm ncua-btn--destructive js-btn-remove">삭제</button>
    <?php if($isShow == 'n'){?>
        <button type="button" class="ncua-btn ncua-btn--sm ncua-btn--secondary-gray js-btn-report">신고해제</button>
    <?php }?>
</div>
