<?php if(!gd_is_provider()) { ?>
<div class="swiper ncua-horizontal-tab ncua-horizontal-tab--sm ncua-horizontal-tab--underline-fill">
    <div class="swiper-wrapper">
        <div class="swiper-slide ncua-horizontal-tab__item">
            <a href="../board/article_list.php?isShow=y&bdId=<?=$req['bdId']?>&listType=board" class="ncua-tab-button <?=$isShow == 'y' && $listType == 'board' ? 'is-active' : ''; ?>" data-html="true" data-content="일반 게시물" data-placement="top">
                일반 게시물
            </a>
        </div>
        <div class="swiper-slide ncua-horizontal-tab__item">
            <a href="../board/article_list.php?isShow=n&bdId=<?=$req['bdId']?>&listType=board" class="ncua-tab-button <?=$isShow == 'n' && $listType == 'board' ? 'is-active' : ''; ?>" data-html="true" data-content="신고 게시물" data-placement="top">
                신고 게시물
            </a>
        </div>
        <div class="swiper-slide ncua-horizontal-tab__item">
            <a href="../board/article_list.php?isShow=n&bdId=<?=$req['bdId']?>&listType=memo" class="ncua-tab-button <?=$isShow == 'n' && $listType == 'memo' ? 'is-active' : ''; ?>" data-html="true" data-content="신고 댓글" data-placement="top">
                신고 댓글
            </a>
        </div>
    </div>
</div>
<?php } ?>
<form name="frmList" id="frmList" action="article_ps.php" method="post">
    <div class="ncua-search-result">
        <!-- summary -->
        <div class="ncua-search-result__summary">
            <p class="ncua-search-result__summary-count">검색 <strong><?= number_format($bdList['cnt']['search']) ?></strong>개 / 전체 <strong><?= number_format($bdList['cnt']['total']) ?></strong>개</p> 
            <div class="ncua-search-result__summary-button">
                <button type="button" class="ncua-btn ncua-btn--xs ncua-btn--secondary-gray js-excel-download" data-target-form="frmSearch" data-target-list-form="frmList" data-target-list-sno="sno" data-search-count="<?=$bdList['cnt']['search']?>" data-total-count="<?=$bdList['cnt']['total']?>">
                <img src="<?= PATH_ADMIN_GD_SHARE ?>ncds/image/ico_excel_download.svg" alt="엑셀 다운로드">
                    엑셀 다운로드
                </button>
            </div>
        </div>
        <!-- // summary -->
        <div class="ncua-search-result__content">
            <!-- 검색 결과 액션 -->
            <div class="ncua-search-result__actions">
                <div class="ncua-search-result__button-group">
                    <button type="button" class="ncua-btn ncua-btn--xs ncua-btn--destructive ncua-select-delete js-btn-delete">
                        선택 삭제
                    </button>
                    <?php if($isShow == 'n') { ?>
                        <button type="button" class="ncua-btn ncua-btn--xs ncua-btn--secondary js-btn-report">신고해제</button>
                    <?php } ?>
                </div>
                <div class="ncua-search-result__actions-select">
                    <?php if($isShow != 'n') { ?>
                        <span class="ncua-select ncua-select--xs ncua-input-width-120">
                            <span class="ncua-select__content">
                                <?= gd_select_box('sort', 'sort', $bdList['sort'], null, $req['sort'], null, null, 'ncua-select__tag') ?>
                            </span>
                        </span>
                    <?php } ?>
                    <span class="ncua-select ncua-select--xs ncua-input-width-120">
                        <span class="ncua-select__content">
                            <?= gd_select_box_by_page_view_count(Request::get()->get('pageNum',10), null, null, 'ncua-select__tag') ?>
                        </span>
                    </span>
                </div>
            </div>
            <!-- // 검색 결과 액션 -->
            
            <input type="hidden" name="bdId" value="<?= $bdList['cfg']['bdId'] ?>">
            <input type="hidden" name="mode" value="delete">
            <input type="hidden" name="bdListDel" value="y">
            <input type="hidden" id="listType" name="listType" value="<?=$listType?>"/>
            <div class="ncua-table ncua-table--horizontal">
                <table id="listTbl">
                    <colgroup>
                        <col width="56px">
                        <col width="70px"/>
                        <?php if ($bdList['cfg']['bdGoodsFl'] === 'y' && $bdList['cfg']['bdGoodsType'] === 'goods' && ($listType != 'memo')) { ?>
                            <col width="100px"/>
                        <?php } ?>
                        <col/>
                        <?php if($isShow == 'n') { ?>
                            <col width="100px"/>
                            <col/>
                            <col width="100px"/>
                        <?php } else { ?>
                            <col width="150px"/>
                            <col width="100px"/>
                            <col width="70px"/>
                            <?php  if ($bdList['cfg']['bdAnswerStatusFl'] == 'y' || $bdList['cfg']['bdReplyStatusFl'] == 'y') { ?>
                                <col width="100px"/>
                            <?php } ?>
                            <?php if ($bdList['cfg']['bdRecommendFl'] == 'y') { ?>
                                <col width="70px"/>
                            <?php } ?>
                            <?php if ($bdList['cfg']['bdGoodsPtFl'] == 'y') { ?>
                                <col width="70px"/>
                            <?php } ?>
                            <?php  if ($bdList['cfg']['bdAnswerStatusFl'] == 'y' || $bdList['cfg']['bdReplyStatusFl'] == 'y') { ?>
                                <col width="100px"/>
                            <?php } ?>
                            <col width="150px"/>
                        <?php } ?>
                    </colgroup>
                    <thead>
                        <tr>
                            <th>
                                <div class="ncua-align-center">
                                    <label class="ncua-checkbox-field ncua-checkbox-field--xs">
                                        <span class="ncua-checkbox-input ncua-checkbox-input--xs ncua-checkbox-field__input">
                                            <input type="checkbox" class="ncua-checkall" data-checkbox-name="sno[]">
                                        </span>
                                    </label>
                                </div>
                            </th>
                            <th><div class="ncua-align-center">번호</div></th>
                            <?php if ($bdList['cfg']['bdGoodsFl'] === 'y' && $bdList['cfg']['bdGoodsType'] === 'goods' && ($listType != 'memo')) { ?>
                                <th><div class="ncua-align-center">상품이미지</div></th>
                            <?php } ?>
                            <th><div class="ncua-align-center">제목</div></th>
                            <?php if($isShow == 'n') { ?>
                                <th><div class="ncua-align-center">신고일</div></th>
                                <th><div class="ncua-align-center">신고내용</div></th>
                                <th><div class="ncua-align-center">관리</div></th>
                            <?php } else { ?>
                            <th><div class="ncua-align-center">작성자</div></th>
                            <th><div class="ncua-align-center">작성일</div></th>
                            <th><div class="ncua-align-center">조회</div></th>
                            <?php  if ($bdList['cfg']['bdAnswerStatusFl'] == 'y' || $bdList['cfg']['bdReplyStatusFl'] == 'y') { ?>
                                <th><div class="ncua-align-center">답변상태</div></th>
                            <?php } ?>
                            <?php if ($bdList['cfg']['bdRecommendFl'] == 'y') { ?>
                                <th><div class="ncua-align-center"> 추천</div></th>
                            <?php } ?>
                            <?php if ($bdList['cfg']['bdGoodsPtFl'] == 'y') { ?>
                                <th><div class="ncua-align-center">평점</div></th>
                            <?php } ?>
                            <?php  if ($bdList['cfg']['bdAnswerStatusFl'] == 'y' || $bdList['cfg']['bdReplyStatusFl'] == 'y') { ?>
                                <th><div class="ncua-align-center">답변일</div></th>
                            <?php } ?>
                            <th><div class="ncua-align-center">수정/답변</div></th>
                            <?php } ?>
                        </tr>
                    </thead>
                    <?php
                    if (gd_array_is_empty($bdList['list']) === false) {
                        foreach ($bdList['list'] as $val) {
                            if ($bdList['cfg']['bdGoodsFl'] === 'y' && $bdList['cfg']['bdGoodsType'] === 'goods') {
                                //게시글 관리에서 노출되는 상품이미지 항목의 노이미지 노출을 위해 imageStorage가 없는 경우 local 셋팅
                                if(!gd_isset($val['imageStorage'])){
                                    $val['imageStorage'] = 'local';
                                }
                            }
                            ?>
                            <tr>
                                <td>
                                    <div class="ncua-align-center">
                                        <label class="ncua-checkbox-field ncua-checkbox-field--xs">
                                            <span class="ncua-checkbox-input ncua-checkbox-input--xs ncua-checkbox-field__input">
                                                <input name="sno[]" type="checkbox" value="<?= $val['sno'] ?>" <?php if($val['auth']['delete'] != 'y' && $listType != 'memo') echo 'disabled'?>>
                                            </span>
                                        </label>
                                    </div>
                                </td>
                                <td>
                                    <div class="ncua-align-center">
                                    <?php
                                    if ($listType == 'memo') {
                                        echo $page->idx--;
                                        echo '<input type="hidden" name="bdSno['.$val['sno'].']" value="'.$val['bdSno'].'">';
                                    } else {
                                        if ($val['isNotice'] == 'y') {
                                            echo gd_isset($bdList['cfg']['ncds']['bdIconNotice']);
                                        } else {
                                            echo $val['articleListNo'];
                                        }
                                    }
                                    if ($bdList['cfg']['bdId'] == 'goodsreview') {
                                        echo '<input type="hidden" name="goodsNo['.$val['sno'].']" value="'.$val['goodsNo'].'">';
                                    }
                                    ?>
                                    </div>
                                </td>
                                <?php if ($bdList['cfg']['bdGoodsFl'] === 'y' && $bdList['cfg']['bdGoodsType'] === 'goods' && ($listType != 'memo')) { ?>
                                    <td>
                                        <div class="ncua-align-center">
                                            <?=gd_html_goods_image($val['goodsNo'], $val['imageName'], $val['imagePath'], $val['imageStorage'], 40, $val['goodsNm'], '_blank'); ?>
                                        </div>
                                    </td>
                                <?php } ?>
                                <td>
                                    <div class="table-title-content">
                                        <?= $val['gapReply'] ?><?php if ($val['groupThread'] != '')
                                            echo gd_isset($bdList['cfg']['ncds']['bdIconReply']); ?>
                                        <?php if ($val['isSecret'] == 'y') {
                                            echo gd_isset($bdList['cfg']['ncds']['bdIconSecret']);
                                        } ?>
                                        <a class="<?php if ($val['isNotice'] == 'y') {
                                            echo 'notice';
                                        } ?>"
                                        href="javascript:btnView('<?= $bdList['cfg']['bdId'] ?>',<?= $val['sno'] ?>);">
                                            <?php
                                            if ($val['category']) {
                                                echo '[' . $val['category'] . ']';
                                            } ?>
                                            <?= $listType == 'memo' ? $val['memo'] : $val['subject']; ?>
                                        </a>
                                        <?php if ($bdList['cfg']['bdMemoFl'] == 'y' && $val['memoCnt']) {
                                            echo '&nbsp;<span class="memoCnt">[' . gd_isset($val['memoCnt']) . ']</span>';
                                        } ?>
                                        <?php if ($val['isNew'] == 'y')
                                            echo gd_isset($bdList['cfg']['ncds']['bdIconNew']); ?>
                                        <?php if ($val['isHot'] == 'y')
                                            echo gd_isset($bdList['cfg']['ncds']['bdIconHot']); ?>
                                        <?php if ($val['isFile'] == 'y')
                                            echo gd_isset($bdList['cfg']['ncds']['bdIconFile']); ?>
                                        <?php if ($listType == 'board') { ?>
                                            <button type="button" class="popup-open ncua-btn ncua-btn--xxs only-icon" onclick="articleViewPopup('<?= $val['sno'] ?>');">
                                                <img src="<?= PATH_ADMIN_GD_SHARE ?>ncds/image/ico_link_external.svg" alt="팝업창열기">
                                            </button>
                                        <?php } ?>
                                    </div>
                                </td>
                                <?php if($isShow == 'n') { ?>
                                <td>
                                    <div class="ncua-align-center">
                                        <?=gd_date_format('Y-m-d', $val['reportDt']);?>
                                    </div>
                                </td>
                                <td>
                                    <div class="article-list-report-memo">
                                        <?= gd_html_cut(gd_string_nl2br($val['reportMemo']), 96, '..'); ?>
                                    </div>
                                </td>
                                <td>
                                    <div class="ncua-align-center">
                                    <button type="button" onclick="btnView('<?= $req['bdId'] ?>', <?= $val['sno'] ?>);"
                                    class="ncua-btn ncua-btn--xs ncua-btn--secondary-gray">상세보기</button>
                                    </div>
                                </td>
                                <?php } else { ?>
                                <td>
                                    <div class="ncua-align-center">
                                        <?php if ($val['memNo'] > 0 && !gd_is_provider()) {
                                            echo "<a   class='js-layer-crm hand' data-member-no='" . $val['memNo'] . "' >";
                                            $aTagClose = '</a>';
                                        } ?>
                                        <?= $val['writer'] . $aTagClose ?>
                                    </div>
                                </td>
                                <td>
                                    <div class="ncua-align-center">
                                        <?= $val['regDate'] ?>
                                    </div>
                                </td>
                                <td>
                                    <div class="ncua-align-center">
                                        <?= number_format($val['hit']) ?>
                                    </div>
                                </td>
                                <?php  if ($bdList['cfg']['bdAnswerStatusFl'] == 'y' || $bdList['cfg']['bdReplyStatusFl'] == 'y') { ?>
                                    <td>
                                        <div class="ncua-align-center">
                                            <?= $val['replyStatusText'] ?>
                                        </div>
                                    </td>
                                <?php } ?>
                                <?php if ($bdList['cfg']['bdRecommendFl'] == 'y') { ?>
                                    <td> 
                                        <div class="ncua-align-center">
                                            <?= gd_isset($val['recommend'], 0) ?>
                                        </div>
                                    </td>
                                <?php } ?>

                                <?php if ($bdList['cfg']['bdGoodsPtFl'] == 'y') { ?>
                                    <td>
                                        <div class="ncua-align-center">
                                            <?= gd_isset($val['goodsPt'], 0) ?>
                                        </div>
                                    </td>
                                <?php } ?>
                                <?php  if ($bdList['cfg']['bdAnswerStatusFl'] == 'y' || $bdList['cfg']['bdReplyStatusFl'] == 'y') { ?>
                                    <?php  if ($val['replyStatus'] == '3') { ?>
                                        <td>
                                            <div class="ncua-align-center">
                                                <?= $val['answerModDate'] ?>
                                            </div>
                                        </td>
                                    <?php } else { ?>
                                        <td>
                                            <div class="ncua-align-center">
                                                <?= '-' ?>
                                            </div>
                                        </td>
                                    <?php } ?>
                                <?php } ?>
                                <td>
                                    <div class="ncua-align-center ncua-gap-4">
                                        <?php if($val['auth']['modify'] == 'y') {?>
                                            <button type="button" 
                                                    class="ncua-btn ncua-btn--xxs ncua-btn--secondary-gray js-btn-modify"
                                                    data-bd-id="<?= $req['bdId'] ?>"
                                                    data-sno="<?= $val['sno'] ?>">수정</button>
                                        <?php }?>
                                        <?php if(!$val['adminFl'] && $val['auth']['reply'] == 'y') {?>
                                            <button type="button"
                                                    class="ncua-btn ncua-btn--xxs ncua-btn--secondary-gray js-btn-reply"
                                                    data-bd-id="<?= $req['bdId'] ?>"
                                                    data-sno="<?= $val['sno'] ?>">답변</button>
                                        <?php }?>
                                    </div>
                                </td>
                            </tr>
                            <?php
                            }
                        }
                    } else {
                        ?>
                        <tr>
                            <td colspan="7" height="50" class="no-data"><div>게시물이 없습니다.</div></td>
                        </tr>
                    <?php } ?>
                </table>
            </div>
            <div class="ncua-table-bottom"></div>
            <div class="modal fade bs-example-modal-sm" tabindex="-1" role="dialog"
                aria-labelledby="mySmallModalLabel">
                <div class="modal-dialog modal-sm">
                    <div class="modal-content">
                        <div class="modal-header">
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                                    aria-hidden="true">&times;</span></button>
                            <h4 class="modal-title">엑셀 다운로드</h4>
                        </div>

                        <div class="modal-body">
                            <p> 다운받을 항목을 선택해주세요.</p>
                            <select id="excelDownloadType" name="excelDownloadType" class="form-control">
                                <option value="1">게시글 전체 다운로드</option>
                                <option value="2">선택한 게시글다운로드</option>
                                <option value="3">댓글 전체 다운로드</option>
                                <option value="4">선택한 댓글 다운로드</option>
                            </select>
                            <div data-target="excelDownloadType"></div>
                        </div>

                        <div class="modal-footer">
                            <button type="button" class="ncua-btn ncua-btn--md ncua-btn--primary" onclick="excelDownload(this.form)">확인
                            </button>
                            <button type="button" class="ncua-btn ncua-btn--md ncua-btn--secondary-gray" data-dismiss="modal">취소</button>
                        </div>
                    </div>
                </div>
            </div>
            <script>
                function writeArticle(sno) {
                    frame_popup("article_write.php?bdId=<?=$bdList['cfg']['bdId']?>&mode=write&sno=" + ((sno) ? sno : ""), "<?=$bdList['cfg']['bdNm']?> 게시판", 'wide-lg');
                }

                function replyArticle(sno) {
                    frame_popup("article_write.php?bdId=<?=$bdList['cfg']['bdId']?>&mode=reply&sno=" + ((sno) ? sno : ""), "<?=$bdList['cfg']['bdNm']?> 게시판", 'wide-xlg');
                }

                function modifyArticle(sno, hasParent) {
                    if (hasParent) {
                        frame_popup("article_write.php?bdId=<?=$bdList['cfg']['bdId']?>&mode=modify&sno=" + ((sno) ? sno : ""), "<?=$bdList['cfg']['bdNm']?> 게시판", 'wide-xlg');
                    }
                    else {
                        frame_popup("article_write.php?bdId=<?=$bdList['cfg']['bdId']?>&mode=modify&sno=" + ((sno) ? sno : ""), "<?=$bdList['cfg']['bdNm']?> 게시판", 'wide-lg');
                    }
                }

                function view(bdId, sno) {
                    location.href = "article_view.php?bdId=" + bdId + "&sno=" + sno;
                }
                function articleViewPopup(sno) {
                    window.open("../board/article_view.php?bdId=<?=$bdList['cfg']['bdId']?>&popupMode=yes&mode=reply&sno=" + sno, "<?=$bdList['cfg']['bdNm']?> 게시판", 'width=1200,height=750,scrollbars=yes,resizable=yes');
                }

                $(document).ready(function () {
                    $('.no-data').attr('colspan', $('#listTbl thead th').length);

                    $('select[name=\'pageNum\']').change(function () {
                        $('#frmSearch').submit();
                    });

                    $('select[name=\'sort\']').change(function () {
                        $('#frmSearch').submit();
                    });

                    $('select[name=bdId]').bind('change',function(){
                        location.href='article_list.php?bdId='+$(this).val()+'&isShow='+$('#isShow').val()+'&listType='+$('#listType').val();
                    })

                    $('.js-btn-delete').click(function() {
                        $('#frmList input[name="mode"]').val('delete');
                        $('#frmList').submit();
                    });
                    $('.js-btn-report').click(function() {
                        $('#frmList input[name="mode"]').val('report');
                        $('#frmList').submit();
                    });

                    // 모던한 이벤트 리스너 (이벤트 위임 사용)
                    document.addEventListener('click', (e) => {
                        const modifyBtn = e.target.closest('.js-btn-modify');
                        const replyBtn = e.target.closest('.js-btn-reply');
                        
                        if (modifyBtn) {
                            e.preventDefault();
                            const { bdId, sno } = modifyBtn.dataset;
                            if (typeof btnModifyWrite === 'function') {
                                btnModifyWrite(bdId, parseInt(sno, 10));
                            }
                            return;
                        }
                        
                        if (replyBtn) {
                            e.preventDefault();
                            const { bdId, sno } = replyBtn.dataset;
                            if (typeof btnReplyWrite === 'function') {
                                btnReplyWrite(bdId, parseInt(sno, 10));
                            }
                        }
                    });

                    $('#frmList').validate({
                        ignore: ':hidden',
                        dialog: false,
                        submitHandler: function (form) {
                            var mode = form.mode.value;
                            <?php if($listType == 'memo') { ?>
                            form.action = 'memo_ps.php'
                            <?php } ?>
                            var msg = '';
                            if (mode == 'delete') {
                                var bdReplyDelFl = '<?=$bdList['cfg']['bdReplyDelFl']?>';
                                var confirmMsg = '';
                                if (bdReplyDelFl == 'reply') {
                                    confirmMsg = '<br> 해당 글의 답변글도 함께 삭제되며\n\r';
                                }
                                msg = '선택한 글을 삭제하시겠습니까?<br/> ' + confirmMsg + '영구 삭제되어 복원 불가능합니다.';
                            } else if (mode == 'report') {
                                msg = '선택한 게시물을 신고해제 하시겠습니까?<br/>신고해제 시, 기존 신고내역은 확인 불가합니다.';
                            }
                            form.target = 'ifrmProcess';
                            NCDSConfirm({message: msg}).then(function (result) {
                                if (result) {
                                    form.submit();
                                }
                            }).catch(function (error) {
                                NCDSAlert({message: error.message});
                            });
                        },
                        invalidHandler: function(event, validator) {
                            if (validator.errorList.length > 0) {
                                NCDSAlert({
                                    message: validator.errorList[0].message,
                                    iconType: 'error'
                                });
                            }
                        },
                        rules: {
                            'sno[]': {
                                required: true
                            }
                        },
                        messages: {
                            'sno[]': {
                                required: '선택하신 글이 없습니다.'
                            },

                        },
                    });

                    //검색어 변경 될 때 placeHolder 교체 및 검색 종류 변환 및 검색 종류 변환
                    const searchKeyword = $('#frmSearch input[name="searchWord"]');
                    const searchKind = $('#frmSearch #searchKind');
                    const arrSearchKey = ['writerNick', 'writerNm', 'writerId'];
                    const strSearchKey = $('select[name="searchField"]').val();

                    setKeywordPlaceholder(searchKeyword, searchKind, strSearchKey, arrSearchKey);
                    searchKind.change(function (e) {
                        setKeywordPlaceholder(searchKeyword, searchKind, $('select[name="searchField"]').val(), arrSearchKey);
                    });

                    $('select[name="searchField"]').change(function (e) {
                        setKeywordPlaceholder(searchKeyword, searchKind, $(this).val(), arrSearchKey);
                    });
                });

                function excelDownload(frm) {
                    const bdId = '<?=$bdList['cfg']['bdId']?>';
                    const downloadtype = frm.excelDownloadType.value;
                    const sno = [];
                    $("input[name='sno[]']:checked").each(function () {
                        sno.push($(this).val());
                    });

                    const snos = sno.join('-');
                    if (downloadtype == '1' || downloadtype == '2') {
                        location.href = './board_excel.php?downloadtype='+downloadtype+'&bdId=' + bdId + '&snos=' + encodeURI(snos);
                    }
                    else if (downloadtype == '3' || downloadtype == '4') {
                        location.href = './memo_excel.php?downloadtype='+downloadtype+'&bdId=' + bdId + '&snos=' + encodeURI(snos);
                    }
                }

            </script>
        </div>
        <div class="ncua-pagination"><?= $bdList['pagination'] ?></div>
        <?php if($isShow == 'n') { ?>
            <p class="ncua-caution-text">신고 된 게시물의 경우 PC 및 모바일쇼핑몰에서 노출되지 않으니 신속히 확인하시어 대응하는 것을 권장 드립니다.</p>
        <?php } ?>
    </div>
</form>
