<link rel="stylesheet" href="<?=gd_set_browser_cache(PATH_ADMIN_GD_SHARE . 'ncds/css/board/plus-review-widget-preview.css')?>">
<div class="modal-dialog__content">
    <div class="plus-review-widget">

        <!-- 위젯 소스코드 -->
        <div class="ncua-content-box">
            <div class="ncua-content-box__title"><?= $title; ?> 게시판 위젯 소스코드</div>
            <div class="plus-review-widget-code ncua-content-box__content">
                <code><?= $code ?></code>
                <input type="text" id="clipboard_target" value="<?= $code ?>" style="position:absolute;top:-9999em;"/>
            </div>
        </div>
        <!-- 위젯 미리보기 -->
        <div class="ncua-content-box">
            <div class="ncua-content-box__title">
                <div><?= $title; ?> 게시판 위젯 미리보기 </div>
                <div class="ncua-notice-info">설정된 <?= $title; ?> 게시판 위젯은 아래와 같이 페이지에 삽입됩니다.</div>
            </div>
            <div class="plus-review-widget-preview ncua-content-box__content">
                <?php
                if ($req['mode'] == 'article') {
                    // 기본형
                    if ($req['template'] == 'default') {
                        if (empty($data) === false) { ?>
                            <div class="board_zone_list">
                                <div class="board_list_plus_review">
                                    <div class="plus_review_view">
                                        <?php foreach ($data as $row) { ?>
                                            <div class="plus_review_view_list js_widget_row" data-sno="<?= $row['sno']; ?>">
                                                <div class="star_day_name">
                                                    <em class="goods_photo"><a href="<?= URI_HOME; ?>goods/goods_view.php?goodsNo=<?= $row['goodsNo']; ?>" target="_blank"><img src="<?= $row['goodsImageSrc']; ?>" class="image"></a></em>
                                                    <?php if ($row['goodsPt'] > 0) { ?>
                                                    <strong><?php foreach (range(1, $row['goodsPt']) as $item) { ?>★<?php } ?></strong>
                                                    <?php } ?>
                                                    <span><?= $row['regDate']; ?></span>
                                                </div>
                                                <div class="option_text_file_box">
                                                    <strong class="goods_tit"><?= $row['goodsNm']; ?></strong>
                                                    <?php if (($config['addFormFl'] == 'y' || $config['displayOptionFl'] == 'y') && (gd_count($row['addFormData']) > 0 || gd_count($row['option']) > 0)) { ?>
                                                        <div class="option_list_cont">
                                                            <?php foreach ($row['addFormData'] as $aKey => $aVal) { ?>
                                                                <dl>
                                                                    <dt><?= $aKey; ?></dt>
                                                                    <dd><?= $aVal; ?></dd>
                                                                </dl>
                                                            <?php }
                                                            foreach ($row['option'] as $oKey => $oVal) { ?>
                                                                <dl>
                                                                    <dt><?= $oVal['name']; ?></dt>
                                                                    <dd><?= $oVal['value']; ?></dd>
                                                                </dl>
                                                            <?php } ?>
                                                        </div>
                                                    <?php } ?>
                                                    <div class="text_file_cont">
                                                        <div class="review_text js_widget_contents_short">
                                                            <?= $row['listContents']; ?>
                                                            <?php if ($row['isMore'] == 'y') { ?>
                                                                <a href="javascript:void(0);" class="btn_review_text_more js_widget_btn_contents_more" data-type="open"><strong>더보기</strong></a>
                                                            <?php } ?>
                                                        </div>
                                                        <div class="review_text js_widget_contents_desc" style="display:none">
                                                            <?= $row['viewContents']; ?>
                                                            <a href="javascript:void(0);" class="btn_review_text_more js_widget_btn_contents_more" data-type="close"><strong>닫기</strong></a>
                                                        </div>
                                                        <div class="file_upload_list">
                                                            <ul>
                                                                <?php foreach ($row['uploadedFile'] as $uKey => $uVal) { ?>
                                                                    <li>
                                                                        <img src="<?= $uVal['thumSquareSrc']; ?>" class="file js_widget_thum_img js_view_layer_btn" data-sno="<?= $row['sno']; ?>">
                                                                        <img src="<?= $uVal['src']; ?>" class="img_pr_zoom js_widget_zoom_img" style="display:none;">
                                                                    </li>
                                                                <?php } ?>
                                                            </ul>
                                                        </div>
                                                    </div>
                                                    <div class="review_comment_box">
                                                        <div class="review_comment_top">
                                                            <?php if ($config['memoFl'] == 'y') { ?>
                                                                <span class="comment_num">
                                                                <a href="javascript:void(0)" class="js_widget_btn_comment"><strong class="js_widget_comment_cnt"><?= $row['memoCnt']; ?></strong>개의 댓글이 있습니다.</a>
                                                                </span>
                                                            <?php } ?>
                                                            <?php if ($config['recommendFl'] == 'y') { ?>
                                                                <span class="comment_best_num">
                                                                추천 : <strong class="js_widget_recommend_cnt"><?= $row['recommend']; ?></strong><a href="javascript:void(0)" class="btn_comment_best js_widget_btn_recommend"><em>추천하기</em></a>
                                                                </span>
                                                            <?php } ?>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        <?php } ?>
                                    </div>
                                </div>
                            </div>
                        <?php } else { ?>
                            <div class="no_data">
                                작성된 리뷰가 없습니다.
                            </div>
                        <?php }
                    } else {
                        if (empty($data) === false) { ?>
                            <div class="plus_review_article simple">
                                <div class="plus_review_list">
                                    <ul class="plus_review_view">
                                        <?php foreach ($data as $row) { ?>
                                            <li>
                                                <div class="list_left">
                                                    <div class="goods_nm"><?= $row['goodsNm']; ?></div>
                                                    <div class="plus_reivew_contents_box">
                                                        <div class="plus_reivew_contents">
                                                            <?= $row['listContents']; ?>
                                                            <a href="javascript:void(0)" class="more">더보기</a>
                                                        </div>
                                                    </div>
                                                    <div>
                                                        <div class="star_day_name">
                                                            <strong>
                                                                <?php foreach (range(1, $row['goodsPt']) as $stars) { ?>★<?php } ?>
                                                            </strong>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="list_right">
                                                    <span class="plus_reivew_contents_box">
                                                        <?php if ($row['uploadHeadImage']['thumSquareSrc']) { ?>
                                                            <div class="plus_reivew_contents_img">
                                                                <img src="<?= $row['uploadHeadImage']['thumSquareSrc']; ?>"/>
                                                            </div>
                                                        <?php } ?>
                                                    </span>
                                                </div>
                                            </li>
                                        <?php } ?>
                                    </ul>
                                </div>
                            </div>
                        <?php } else { ?>
                            <div class="no_data">작성된 리뷰가 없습니다.</div>
                        <?php }
                    }
                } else {
                    $i = 1;
                    foreach ($data as $row) { ?>
                        <div class="plus-review-widget-preview-photo">
                            <img src="<?= $row['uploadHeadImage']['thumSquareSrc'] ?>" style="display:none">
                        </div>
                        <?php
                        $i++;
                    }
                } ?>
            </div>
        </div>
        
    </div>
</div>
<div class="modal-dialog__footer">
    <button class="ncua-btn ncua-btn--sm ncua-btn--primary js-btn-copy" data-clipboard-text="<?= $code ?>" title="위젯 소스코드">
        <span class="ncua-btn__label">소스복사</span>
    </button>
</div>


<script>
    var thumSizeType = <?= json_encode($req['thumSizeType'] ?? '') ?>;
    $(document).ready(function () {
        <?php if ($req['mode'] != 'article') { ?>
        setTimeout(function () {
            if (thumSizeType == 'menual') {
                $('.plus-review-widget-preview-photo img').css('width', '<?= intval($req['thumWidth'] ?? 0) ?>px').css('height', '<?= intval($req['thumWidth'] ?? 0) ?>px').show();
            } else {
                var containerWidth = ($('.plus-review-widget').width());
                var autoSize = Math.floor(containerWidth /<?= max(1, intval($req['cols'] ?? 1)) ?>) - 10;
                $('.plus-review-widget-preview-photo').css('width', autoSize + 'px').css('height', autoSize + 'px');
                $('.plus-review-widget-preview-photo img').css('width', '100%').css('height', '100%');
                $('.plus-review-widget-preview-photo img').show();
            }
        }, 1);
        <?php } else { ?>
        $(document).on('mouseenter', '.js_widget_thum_img', function () {
            $('.js_widget_zoom_img').hide();
            $(this).next('.js_widget_zoom_img').show();
        });
        $(document).on('mouseleave', '.js_widget_thum_img', function () {
            $('.js_widget_zoom_img').hide();
        });
        <?php } ?>

        var agent = navigator.userAgent.toLowerCase();
        if ((navigator.appName == 'Netscape' && agent.indexOf('trident') != -1) || (agent.indexOf("msie") != -1)) {
            $('.js-btn-copy').bind('click', function () {
                window.clipboardData.setData('Text', $(this).data('clipboard-text'));
                NCDSToast({message: '복사되었습니다.', color: 'success'})
            })
        } else { // 크롬에서 동작
            $('.js-btn-copy').on('click', function () {
                $('#clipboard_target').select();
                if (document.execCommand('copy')) {
                    NCDSToast({message: '복사되었습니다.',color: 'success'})

                }
            });
        }
    });
</script>
