<?php if(!gd_is_provider()) { ?>
    <div class="swiper ncua-horizontal-tab ncua-horizontal-tab--underline-fill">
        <div class="swiper-wrapper">
            <div class="swiper-slide ncua-horizontal-tab__item">
                <a href="../board/plus_review_list.php?isShow=y&listType=board" class="ncua-tab-button <?=$isShow == 'y' && $listType == 'board' ? 'is-active' : ''; ?>" data-html="true" data-content="일반 게시물" data-placement="top">
                    일반 게시물
                </a>
            </div>
            <div class="swiper-slide ncua-horizontal-tab__item">
                <a href="../board/plus_review_list.php?isShow=n&listType=board" class="ncua-tab-button <?=$isShow == 'n' && $listType == 'board' ? 'is-active' : ''; ?>" data-html="true" data-content="신고 게시물" data-placement="top">
                    신고 게시물
                </a>
            </div>
            <div class="swiper-slide ncua-horizontal-tab__item">
                <a href="../board/plus_review_list.php?isShow=n&listType=memo" class="ncua-tab-button <?=$isShow == 'n' && $listType == 'memo' ? 'is-active' : ''; ?>" data-html="true" data-content="신고 댓글" data-placement="top">
                    신고 댓글
                </a>
            </div>
        </div>
    </div>
<?php } ?>
<div class="ncua-search-result">
    <!-- summary -->
    <div class="ncua-search-result__summary">
        <p class="ncua-search-result__summary-count">검색 <strong><?= number_format($list['cnt']['search']) ?></strong>개 / 전체 <strong><?= number_format($list['cnt']['total']) ?></strong>개</p> 
        <div class="ncua-search-result__summary-button">
            <?php if($isShow == 'y') { ?>
            <button type="button" class="ncua-btn ncua-btn--xs ncua-btn--secondary-gray js-excel-download" data-target-form="frmSearch" data-target-list-form="frmList" data-target-list-sno="sno" data-search-count="<?=$list['cnt']['search'];?>" data-total-count="<?=$list['cnt']['total'];?>">
                <img src="<?= PATH_ADMIN_GD_SHARE ?>ncds/image/ico_excel_download.svg" alt="엑셀 다운로드">엑셀 다운로드
            </button>
            <?php } ?>
        </div>
    </div>
    <!-- // summary -->
    
    <!-- 검색 결과 액션 -->
    <div class="ncua-search-result__content">
        <div class="ncua-search-result__actions">
            <div class="ncua-search-result__actions-button">
                <div class="ncua-search-result__actions-text">
                    <button type="button" class="ncua-btn ncua-btn--xs ncua-btn--destructive ncua-select-delete js-btn-delete">선택 삭제</button>
                    <?php if($isShow != 'n') { ?>
                        <button type="button" class="ncua-btn ncua-btn--xs ncua-btn--secondary js-btn-apply" data-value="y">승인</button>
                        <button type="button" class="ncua-btn ncua-btn--xs ncua-btn--secondary-gray js-btn-apply" data-value="n">미승인</button>
                        <button type="button" class="ncua-btn ncua-btn--xs ncua-btn--secondary-gray js-btn-milage">마일리지 지급</button>
                        <a href="<?php echo URI_ADMIN; ?>member/member_batch_mileage_list.php" class="ncua-btn ncua-btn--xs ncua-btn--secondary-gray">마일리지 지급 내역 보기</a>
                    <?php } else { ?>
                        <button type="button" class="ncua-btn ncua-btn--xs ncua-btn--secondary-gray js-btn-report">신고해제</button>
                    <?php } ?>
                </div>
            </div>
            <div class="ncua-search-result__actions-select">
                <?php if($isShow != 'n') { ?>
                <span class="ncua-select ncua-select--xs">
                    <span class="ncua-select__content">
                        <?= gd_select_box('sort', 'sort', $list['sort'], null, $req['sort'], null, null, 'ncua-select__tag'); ?>
                    </span>
                </span>
                <?php } ?>
                <span class="ncua-select ncua-select--xs">
                    <span class="ncua-select__content">
                        <?= gd_select_box_by_page_view_count(Request::get()->get('pageNum', 10), null, null, 'ncua-select__tag js-page-number'); ?>
                    </span>
                </span>
            </div>
        </div>
        <!-- // 검색 결과 액션 -->

        <form name="frmList" id="frmList" action="plus_review_ps.php" method="post" target="ifrmProcess">
            <input type="hidden" name="mode" value="delete">
            <input type="hidden" name="bdId" value="plusReview">
            <input type="hidden" id="listType" name="listType" value="<?=$listType?>"/>
            <div class="ncua-table ncua-table--horizontal ncua-border-radius-none plus-review-list-table">
                <table id="listTbl">
                    <colgroup>
                        <col width="56px">
                        <col width="70px">
                        <col width="180px">
                        <?php if($isShow != 'n') { ?>
                        <col width="60px">
                        <col width="60px">
                        <col width="60px">
                        <col width="130px">
                        <col width="100px">
                        <col width="80px">
                        <col width="130px">
                        <col width="100px">
                        <col width="100px">
                        <col width="60px">
                        <col width="120px">
                        <col width="80px">
                        <col width="80px">
                        <?php } else { ?>
                        <col width="120px">
                        <col/>
                        <col width="130px">
                        <?php } ?>
                    </colgroup>
                    <thead>
                        <tr>
                            <th>
                                <div>
                                    <label class="ncua-checkbox-field ncua-checkbox-field--xs">
                                        <span class="ncua-checkbox-input ncua-checkbox-input--xs ncua-checkbox-field__input">
                                            <input type="checkbox" class="js-checkall" data-target-name="sno">
                                        </span>
                                    </label>
                                </div>
                            </th>
                            <th><div>번호</div></th>
                            <th><div>내용</div></th>
                            <?php if($isShow != 'n') { ?>
                            <th><div>속성</div></th>
                            <th><div>댓글</div></th>
                            <th><div>평가</div></th>
                            <th><div class="ncua-text-align-center">주문 실 결제금액<br>(상품 실구매 금액)</div></th>
                            <th><div>주문일</div></th>
                            <th><div>처리상태</div></th>
                            <th><div>작성자</div></th>
                            <th><div>작성일</div></th>
                            <th><div>발급일</div></th>
                            <th><div>추천</div></th>
                            <th><div>마일리지</div></th>
                            <th><div>승인</div></th>
                            <th><div>수정</div></th>
                            <?php } else { ?>
                            <th><div>신고일</div></th>
                            <th><div>신고내용</div></th>
                            <th><div>관리</div></th>
                            <?php } ?>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        if (gd_array_is_empty($list['list']) === false) {
                            foreach ($list['list'] as $val) {
                        ?>
                        <tr>
                            <td>
                                <div>
                                    <label class="ncua-checkbox-field ncua-checkbox-field--xs">
                                        <span class="ncua-checkbox-input ncua-checkbox-input--xs ncua-checkbox-field__input">
                                            <input name="sno[]" type="checkbox" value="<?= $val['sno'] ?>">
                                        </span>
                                    </label>
                                    <input name="goodsNoArry[<?= $val['sno'] ?>]" type="hidden" value="<?= $val['goodsNo'] ?>">
                                </div>
                            </td>
                            <td>
                                <div>
                                <?php
                                if ($listType == 'memo') {
                                    echo $page->idx--;
                                    echo '<input type="hidden" name="bdSno['.$val['sno'].']" value="'.$val['articleSno'].'">';
                                } else {
                                    echo $val['no'];
                                }?>
                                </div>
                            </td>
                            <?php if ($listType == 'memo') { ?>
                            <td>
                                <div>
                                    <a href="javascript:view(<?=$val['sno']?>)" class="js-contents-short ncua-link"><?=$val['memo']?></a>
                                </div>
                            </td>
                            <?php } else { ?>
                            <td>
                                <div class="js-preview ncua-left-align" data-sno="<?= $val['sno'] ?>" data-preview-id="preview-<?= $val['sno'] ?>">
                                    <a href="javascript:view(<?=$val['sno']?>)" class="js-contents-short ncua-link">
                                        <?= $val['listContents'] ?>
                                        <?php if($val['isFile'] == 'y') {?>
                                            <img src="<?=PATH_ADMIN_GD_SHARE?>ncds/image/ico_attachment.svg" />
                                        <?php }?>
                                        <?php if($val['isNew'] == 'y') {?>
                                            <img src="<?=PATH_ADMIN_GD_SHARE?>ncds/image/ico_new.svg" />
                                        <?php }?>
                                    </a>
                                    <?php if($val['orderChannelFl'] == 'naverpay') {?>
                                    <img src="<?=\UserFilePath::adminSkin('gd_share', 'img', 'channel_icon', 'naverpay.gif')->www()?>">
                                    <?php }?>
                                </div>
                            </td>
                            <?php } ?>
                            <?php if($isShow != 'n') { ?>
                            <td>
                                <div>
                                    <?= $val['reviewTypeText'] ?>
                                </div>
                            </td>
                            <td>
                                <div>
                                    <?= $val['memoCnt'] ?>
                                </div>
                            </td>
                            <td>
                                <div>
                                    <?= $val['goodsPt'] ?>
                                </div>
                            </td>
                            <td>
                                <div>
                                    <?= $val['orderPrice']?>
                                </div>
                            </td>
                            <td>
                                <div>
                                    <?= $val['buyGoodsRegDt']?>
                                </div>
                            </td>
                            <td>
                                <div>
                                    <?= $val['orderStatus']?>
                                </div>
                            </td>
                            <td>
                                <div class="ncua-text-align-center">
                                    <?php if($val['memNo'] > 0 && gd_is_provider() == false) {?>
                                        <a href="javascript:void(0)" class='js-layer-crm hand' data-member-no="<?=$val['memNo']?>"><?= $val['writer'] ?></a>
                                    <?php } else {?>
                                        <?= $val['writer'] ?>
                                    <?php }?>
                                </div>
                            </td>
                            <td>
                                <div>
                                    <?= $val['regDate'] ?>
                                </div>
                            </td>
                            <td>
                                <div>
                                    <?= $val['mileageGiveDt'] ?>
                                </div>
                            </td>
                            <td>
                                <div>
                                    <?= $val['recommend'] ?>
                                </div>
                            </td>
                            <td class="js-apply-milage-<?= $val['sno'] ?>">
                                <div>
                                    <?php if($val['channel']  == 'naverpay' || $val['memNo']  == 0 ) {?>
                                        <span class="text-gray">지급불가</span>
                                    <?php }else if($val['mileage']  > 0) {?>
                                        지급완료
                                    <?php }else if($val['mileage'] == 0 && $val['mileageGiveDt'] != '0000-00-00' && empty($val['mileagePolicy']) === false) {?>
                                        지급예정
                                    <?php }else{?>
                                        <button type="button" class="ncua-btn ncua-btn--xxs ncua-btn--secondary-gray" onclick="milageAdd(<?= $val['sno'] ?>)">마일리지</button>
                                    <?php }?>
                                </div>
                            </td>
                            <td class="js-apply-button-<?= $val['sno'] ?>">
                                <div>
                                    <?php if($val['applyFl'] != 'y'){?>
                                        <button type="button" class="ncua-btn ncua-btn--xxs ncua-btn--secondary" onclick="applySet(<?= $val['sno'] ?>,<?= $val['goodsNo'] ?>)">승인</button>
                                    <?php }else{?>
                                        승인완료
                                    <?php }?>
                                </div>
                            </td>
                            <td>
                                <div>
                                    <?php if($val['channel']  != 'naverpay') {?>
                                        <button type="button" class="ncua-btn ncua-btn--xxs ncua-btn--secondary-gray" onclick="modify(<?= $val['sno'] ?>)">수정</button>
                                    <?php }?>
                                </div>
                            </td>
                            <?php } else { ?>
                            <td>
                                <div>
                                    <?=gd_date_format('Y-m-d', $val['reportDt']);?>
                                </div>
                            </td>
                            <td>
                                <div class="align-left">
                                    <?= gd_html_cut(gd_string_nl2br($val['reportMemo']), 96, '..'); ?>
                                </div>
                            </td>
                            <td>
                                <div>
                                    <button type="button" class="ncua-btn ncua-btn--xxs ncua-btn--secondary-gray" onclick="view(<?=$val['sno']?>);">상세보기</button>
                                </div>
                            </td>
                            <?php } ?>
                        </tr>
                        <?php }
                        } else {
                        ?>
                        <tr>
                            <td colspan="15" height="50" class="no-data"><div>검색된 정보가 없습니다.</div></td>
                        </tr>
                        <?php } ?>
                    </tbody>
                </table>
            </div>
        </form>
        <div class="ncua-table-bottom"></div>
    </div>
    <?php if($isShow == 'n') { ?>
        <div class="ncua-caution-text">신고 된 게시물의 경우 PC 및 모바일쇼핑몰에서 노출되지 않으니 신속히 확인하시어 대응하는 것을 권장 드립니다.</div>
    <?php } ?>
    <div class="ncua-pagination"><?= $list['pagination'] ?></div>
</div>
