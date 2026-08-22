<section class="ncua-card">
    <header class="ncua-card__header">
        <h3 class="ncua-card__title">기본설정</h3>
    </header>
    <section class="ncua-card__body">
        <div class="ncua-table ncua-table--vertical">
            <table>
                <tbody>
                    <tr>
                        <th><div>사용 여부</div></th>
                        <td>
                            <div>
                                <div class="ncua-switch ncua-switch--xs">
                                    <label class="ncua-switch__option ncua-switch__option--left ncua-switch__option--<?= $checked['useFl']['y'] ? 'active' : 'inactive' ?>">
                                        <input class="ncua-switch__radio ncua-switch__radio--left" type="radio" name="useFl" value="y" <?= $checked['useFl']['y'] ?> />
                                        <span class="ncua-switch__label">사용함</span>
                                    </label>
                                    <label class="ncua-switch__option ncua-switch__option--right ncua-switch__option--<?= $checked['useFl']['n'] ? 'active' : 'inactive' ?>">
                                        <input class="ncua-switch__radio ncua-switch__radio--right" type="radio"  name="useFl" value="n" <?= $checked['useFl']['n'] ?>  />
                                        <span class="ncua-switch__label">사용안함</span>
                                    </label>
                                </div>
                            </div>
                        </td>
                    </tr>

                    <tr>
                        <th><div data-tooltip-seq="001">상품후기 게시판 통합</div></th>
                        <td>
                            <div class="ncua-flex-column ncua-flex-gap">
                                <button class="ncua-btn ncua-btn--xs ncua-btn--secondary-gray js-btn-migration">
                                    <span class="ncua-btn__label">상품후기 게시판 통합</span>
                                </button>
                                <div>
                                    <?php if($migrationInfo['regDt']){?>
                                    <span class="ncua-caution-text">게시판 통합 <?=substr($migrationInfo['regDt'],0,10)?> 게시글까지 완료</span>
                                    <?php }?>
                                    <div class="ncua-caution-text">
                                        플러스리뷰는 일반 텍스트와 첨부 이미지 중심의 리뷰 기능입니다.<div> 따라서, 게시판 통합 시 첨부된 이미지 파일만 통합되고 이미지 태그로 본문에 작성된 이미지는 통합되지 않습니다.</div>
                                    </div>
                                </div>
                            </div>
                        </td>
                    </tr>

                    <tr>
                        <th><div data-tooltip-seq="002">포토리뷰 게시판 주소</div></th>
                        <td>
                            <div class="ncua-flex-column ncua-flex-gap">
                                <div>PC : <?= $data['photoReviewUri']['front'] ?>
                                    <button type="button" data-clipboard-text="<?= $data['photoReviewUri']['front'] ?>" class="ncua-clipboard ncua-copy"
                                            title="<?= $data['photoReviewUri']['front']; ?>">
                                    </button>
                                
                                    </div>
                                <div>모바일 : <?= $data['photoReviewUri']['mobile'] ?>
                                    <button type="button" data-clipboard-text="<?= $data['photoReviewUri']['mobile'] ?>" class="ncua-clipboard ncua-copy"
                                            title="<?= $data['photoReviewUri']['mobile']; ?>">
                                    </button>
                                </div>
                            </div>
                        </td>
                    </tr>

                    <tr>
                        <th>
                            <div data-tooltip-seq="003">포토리뷰 게시판 페이지당 <br/>게시물 수</div>
                        </th>
                        <td>
                            <div class="ncua-flex-gap">
                                <?php foreach ($goodsPagetCntList as $key => $value) { ?>
                                <label class="ncua-radio-field ncua-radio-field--xs has-text">
                                    <span class="ncua-radio-input ncua-radio-input--xs ncua-radio-field__input">
                                        <input type="radio" name="photoPagetCnt" value="<?= $value ?>"  <?= $selected['photoPagetCnt'][$value] ? 'checked' : '' ?>/> 
                                    </span>
                                    <span class="ncua-radio-field__text"><?=$value?>개</span>
                                </label>
                                <?php } ?>
                            </div>
                        </td>
                    </tr>

                    <tr>
                        <th><div data-tooltip-seq="004">포토리뷰 게시판 위젯</div></th>
                        <td>
                            <div class="ncua-flex-column ncua-flex-gap">
                                <div class="ncua-border-content-layout">
                                    <div class="ncua-border-content">
                                        <div class="ncua-border-content-title">레이아웃</div>
                                        <div class="ncua-border-content-form ncua-layout-size">
                                            <div class="ncua-input ncua-input--xs ncua-input-width-120">
                                                <div class="ncua-input__content">
                                                    <div class="ncua-input__field ncua-input__field--xs">
                                                        <input type="text" name="photoWidget[cols]" value="" placeholder="가로 사이즈"  class="js-number"/>
                                                    </div>
                                                </div>
                                            </div>
                                            <div>*</div> 
                                            <div class="ncua-input ncua-input--xs ncua-input-width-120">
                                                <div class="ncua-input__content">
                                                    <div class="ncua-input__field ncua-input__field--xs">
                                                        <input type="text" name="photoWidget[rows]" value="" placeholder="세로 사이즈"  class="js-number"/>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="ncua-border-content">
                                        <div class="ncua-border-content-title">썸네일 사이즈</div>
                                        <div class="ncua-border-content-form ncua-thumnail-size ncua-flex-gap">
                                            <label class="ncua-radio-field ncua-radio-field--xs has-text">
                                                <span class="ncua-radio-input ncua-radio-input--xs ncua-radio-field__input">
                                                    <input type="radio" name="photoWidget[thumSizeType]" value="auto" /> 
                                                </span>
                                                <span class="ncua-radio-field__text">페이지 자동맞춤</span>
                                            </label>
                                            <label class="ncua-radio-field ncua-radio-field--xs has-text">
                                                <span class="ncua-radio-input ncua-radio-input--xs ncua-radio-field__input">
                                                    <input type="radio" name="photoWidget[thumSizeType]" value="menual" /> 
                                                </span>
                                                <span class="ncua-radio-field__text">수동설정</span>
                                                <div class="ncua-input ncua-input--xs ncua-input-width-120">
                                                    <div class="ncua-input__content">
                                                        <div class="ncua-input__field ncua-input__field--xs">
                                                            <input type="text" name="photoWidget[thumWidth]" value="" placeholder="가로 사이즈"  class="js-number"/>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div>px</div>
                                            </label>
                                        </div>  
                                    </div>
                                </div>
                                <button class="ncua-btn ncua-btn--xs ncua-btn--secondary js-btn-widget" data-mode="photo"><span class="ncua-btn__label">위젯생성</span></button>
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <th>
                            <div data-tooltip-seq="005">전체리뷰 게시판 주소</div>
                        </th>
                        <td>
                            <div class="ncua-flex-column ncua-flex-gap">
                                <div>PC : <?= $data['allReviewUri']['front'] ?>
                                    <button type="button" data-clipboard-text="<?= $data['allReviewUri']['front'] ?>" class="ncua-clipboard ncua-copy"
                                            title="<?= $data['allReviewUri']['front']; ?>">
                                    </button>
                                </div>
                                <div>모바일 : <?= $data['allReviewUri']['mobile'] ?>
                                    <button type="button" data-clipboard-text="<?= $data['allReviewUri']['mobile'] ?>" class="ncua-clipboard ncua-copy"
                                            title="<?= $data['allReviewUri']['mobile']; ?>">
                                    </button>
                                </div>
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <th>
                            <div data-tooltip-seq="006">전체리뷰 게시판 페이지당 <br/>게시물 수</div>
                        </th>
                        <td>
                            <div>
                                <div class="ncua-input ncua-input--xs ncua-input-width-80">
                                    <div class="ncua-input__content">
                                        <div class="ncua-input__field ncua-input__field--xs">
                                            <input type="text" maxlength="3" name="articlePagetCnt" value="<?=$data['articlePagetCnt']?>" class="js-number" placeholder="개수 입력"/>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <th><div data-tooltip-seq="007">전체리뷰 게시판 위젯</div></th>
                        <td>
                            <div class="ncua-flex-column ncua-flex-gap">
                                <div class="ncua-border-content-layout ncua-preview-template-layout">
                                    <div class="ncua-border-content">
                                        <div class="ncua-border-content-title">위젯 형태</div>
                                        <div class="ncua-border-content-form">
                                            <label class="ncua-radio-field ncua-radio-field--xs has-text">
                                                <span class="ncua-radio-input ncua-radio-input--xs ncua-radio-field__input">
                                                    <input type="radio" name="articleWidget[template]" value="default" <?= gd_isset($checked['useFl']['y']) ?>> 
                                                </span>
                                                <span>
                                                    <span class="ncua-radio-field__text">기본형 
                                                    <button type="button" class="ncua-btn ncua-btn--xs ncua-btn--secondary-gray js-btn-preview-template" data-target="layerViewArticleTemplateDefault">
                                                        <span class="ncua-btn__label">미리보기</span>
                                                    </button>
                                                    </span> 
                                                </span>
                                            </label>
                                            <div class="layerViewArticleTemplateDefault ncua-preview-template">
                                                <img src="<?= PATH_ADMIN_GD_SHARE ?>image/plusreview_template_article_default.png">
                                            </div>
                                    
                                            <label class="ncua-radio-field ncua-radio-field--xs has-text">
                                                <span class="ncua-radio-input ncua-radio-input--xs ncua-radio-field__input">
                                                    <input type="radio" name="articleWidget[template]" value="simple" <?= gd_isset($checked['useFl']['n']) ?>>
                                                </span>
                                                <span>
                                                    <span class="ncua-radio-field__text">간편형 
                                                    <button type="button" class="ncua-btn ncua-btn--xs ncua-btn--secondary-gray js-btn-preview-template" data-target="layerViewArticleTemplateSimple">
                                                        <span class="ncua-btn__label">미리보기</span>
                                                    </button>
                                                    </span> 
                                                </span>
                                            </label>
                                            <div class="layerViewArticleTemplateSimple ncua-preview-template">
                                                <img src="<?= PATH_ADMIN_GD_SHARE ?>image/plusreview_template_article_simple.png">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="ncua-border-content">
                                        <div class="ncua-border-content-title">출력 리뷰 개수</div>
                                        <div class="ncua-border-content-form ncua-input ncua-input--xs ncua-input-width-80">
                                            <div class="ncua-input__content">
                                                <div class="ncua-input__field ncua-input__field--xs">
                                                    <input type="text" name="articleWidget[rows]" value="" class="js-number" placeholder="개수 입력"/>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div> 
                                <button class="ncua-btn ncua-btn--xs ncua-btn--secondary js-btn-widget" data-mode="article"><span class="ncua-btn__label">위젯생성</span></button>

                            </div>
                        </td>
                    </tr>
                    <tr>
                        <th>
                            <div data-tooltip-seq="008">상품기준 리뷰 게시판 주소</div>
                        </th>
                        <td>
                            <div class="ncua-flex-column ncua-flex-gap">
                                <div>PC : <?= $data['goodsReviewUri']['front'] ?>
                                    <button type="button" data-clipboard-text="<?= $data['goodsReviewUri']['front'] ?>" class="ncua-clipboard ncua-copy"
                                            title="<?= $data['goodsReviewUri']['front']; ?>">
                                    </button>
                                </div>
                                <div>모바일 : <?= $data['goodsReviewUri']['mobile'] ?>
                                    <button type="button" data-clipboard-text="<?= $data['goodsReviewUri']['mobile'] ?>" class="ncua-clipboard ncua-copy"
                                            title="<?= $data['goodsReviewUri']['mobile']; ?>">
                                    </button>
                                </div>
                                </div>
                        </td>
                    </tr>
                    <tr>
                        <th><div data-tooltip-seq="009">상품기준 리뷰 게시판 페이지당 <br/>게시물 수</div></th>
                        <td>
                            <div class="ncua-flex-gap">
                                <?php foreach ($goodsPagetCntList as $key => $value) { ?>
                                <label class="ncua-radio-field ncua-radio-field--xs has-text">
                                    <span class="ncua-radio-input ncua-radio-input--xs ncua-radio-field__input">
                                        <input type="radio" name="goodsPagetCnt" value="<?= $value ?>"  <?= $selected['goodsPagetCnt'][$value] ? 'checked' : '' ?>/> 
                                    </span>
                                    <span class="ncua-radio-field__text"><?=$value?>개</span>
                                </label>
                                <?php } ?>
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <th><div>쓰기권한 설정</div></th>
                        <td>
                            <div class="ncua-flex-column ncua-flex-gap">
                                <div class="ncua-auth-config ncua-flex-gap" data-combobox-id="layer_member_group_auth_write_combobox">
                                    <label class="ncua-radio-field ncua-radio-field--xs has-text">
                                        <span class="ncua-radio-input ncua-radio-input--xs ncua-radio-field__input">
                                            <input type="radio" name="authWrite" value="all" <?= $checked['authWrite']['all'] ?>/> 
                                        </span>
                                        <span class="ncua-radio-field__text">전체(회원+비회원)</span>
                                    </label>
                                    <label class="ncua-radio-field ncua-radio-field--xs has-text">
                                        <span class="ncua-radio-input ncua-radio-input--xs ncua-radio-field__input">
                                            <input type="radio" name="authWrite" value="member" <?= $checked['authWrite']['member'] ?>/> 
                                        </span>
                                        <span class="ncua-radio-field__text">회원전용(비회원제외)</span>
                                    </label>
                                    <label class="ncua-radio-field ncua-radio-field--xs has-text">
                                        <span class="ncua-radio-input ncua-radio-input--xs ncua-radio-field__input">
                                            <input type="radio" name="authWrite" value="group" <?= $checked['authWrite']['group'] ?> /> 
                                        </span>
                                        <span class="ncua-radio-field__text">특정회원등급</span>
                                    </label>
                                    <div id="layer_member_group_auth_write_combobox"></div>
                                </div>
                                <div id="member_groupLayer_auth_write" class="ncua-flex ncua-gap-4 ncua-align-items-center member-group-display-none <?= is_array($data['authWriteGroup']) ? 'active' : ''?>">
                                    <?php if (is_array($data['authWriteGroup'])) { ?>
                                        <h5>선택된 회원등급 :</h5>
                                        <?php foreach ($data['authWriteGroup'] as $k => $v) { ?>
                                        <div id="authWriteGroup_<?= $k ?>">
                                            <input type="hidden" name="authWriteGroup[]" value="<?= $k ?>"/>
                                            <span class="ncua-tag ncua-tag--sm">
                                                <span class="ncua-tag__text"><?= $v ?></span>
                                                <button type="button" class="ncua-tag__close auth-write-member-tag ncua-select-group-tag" onclick="removeMemberGroup('authWriteGroup', <?= $k ?>)">
                                                <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" fill="none" viewBox="0 0 24 24" stroke="none">
                                                    <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 6 6 18M6 6l12 12" />
                                                </svg>                                                </button>
                                            </span>
                                        </div>
                                        <?php }
                                    } ?>
                                </div>
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <th><div data-tooltip-seq="010">쓰기권한 추가 기준</div></th>
                        <td>
                            <div class="ncua-flex-column ncua-flex-gap">
                                <label class="ncua-radio-field ncua-radio-field--xs has-text">
                                    <span class="ncua-radio-input ncua-radio-input--xs ncua-radio-field__input">
                                        <input type="radio" name="authWriteExtra" value="all" <?= $checked['authWriteExtra']['all'] ?>>
                                    </span>
                                    <span class="ncua-radio-field__text">구매 여부와 상관없이 후기 작성 가능</span>
                                </label>
                                <div class="ncua-auth-write-buy-condition">
                                    <label class="ncua-radio-field ncua-radio-field--xs has-text">
                                        <span class="ncua-radio-input ncua-radio-input--xs ncua-radio-field__input">
                                            <input type="radio" name="authWriteExtra" value="buyer" <?= $checked['authWriteExtra']['buyer'] ?>>
                                        </span>
                                        <span class="ncua-radio-field__text">구매 내역이 존재하는 경우에만 후기 작성 가능</span>
                                        ( 작성 가능 시점 :
                                        <span class="ncua-select ncua-select--xs">
                                            <span class="ncua-select__content">
                                                <?= gd_select_box('authWriteStatus', 'authWriteStatus', $authWriteStatus, ' 이후', $selected['authWriteStatus'], null, null, 'ncua-select__tag'); ?>
                                            </span>
                                        </span>
                                        )
                        
                                    </label>
                                    <label class="ncua-checkbox-field ncua-checkbox-field--xs has-text">
                                        <span class="ncua-checkbox-input ncua-checkbox-input--xs ncua-checkbox-field__input">
                                            <input type="checkbox" name="authWriteStatusDurationFl" value="y" <?= $checked['authWriteStatusDurationFl']['y'] ?>/>
                                        </span>
                                        <span>
                                            <span class="ncua-checkbox-field__text">
                                                <span>작성 가능 시점으로부터</span>
                                                <span class="ncua-select ncua-select--xs">
                                                    <span class="ncua-select__content">
                                                        <?= gd_select_box('authWriteStatusDuration', 'authWriteStatusDuration', $authWriteStatusDuration, '일', $selected['authWriteStatusDuration'], null, null, 'ncua-select__tag'); ?>
                                                    </span>
                                                </span>
                                                <span>이내만 후기 작성 가능.</span>
                                            </span>
                                        </span>
                                    </label>
                                </div>
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <th><div data-tooltip-seq="011">중복작성 제한</div></th>
                        <td>
                            <div class="ncua-flex-gap">
                                <label class="ncua-radio-field ncua-radio-field--xs has-text">
                                    <span class="ncua-radio-input ncua-radio-input--xs ncua-radio-field__input">
                                        <input type="radio" name="orderDuplicateIgnoreFl" value="y" <?= $checked['orderDuplicateIgnoreFl']['y'] ?>>
                                    </span>
                                    <span class="ncua-radio-field__text">제한없음</span>
                                </label>
                                <label class="ncua-radio-field ncua-radio-field--xs has-text">
                                    <span class="ncua-radio-input ncua-radio-input--xs ncua-radio-field__input">
                                        <input type="radio" name="orderDuplicateIgnoreFl" value="n" <?= $checked['orderDuplicateIgnoreFl']['n'] ?>>
                                    </span>
                                    <span class="ncua-radio-field__text">1회만 작성 가능하도록 제한</span>
                                </label>
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <th><div>리뷰 작성 예외 상품</div></th>
                        <td>
                            <div>
                                <div class="ncua-switch ncua-switch--xs">
                                    <label class="ncua-switch__option ncua-switch__option--left ncua-switch__option--<?= $checked['exceptGoodsFl']['y'] ? 'active' : 'inactive' ?>">
                                        <input class="ncua-switch__radio ncua-switch__radio--left" type="radio" name="exceptGoodsFl" value="y" <?= $checked['exceptGoodsFl']['y'] ?> />
                                        <span class="ncua-switch__label">사용함</span>
                                    </label>
                                    <label class="ncua-switch__option ncua-switch__option--right ncua-switch__option--<?= $checked['exceptGoodsFl']['n'] ? 'active' : 'inactive' ?>">
                                        <input class="ncua-switch__radio ncua-switch__radio--right" type="radio"  name="exceptGoodsFl" value="n" <?= $checked['exceptGoodsFl']['n'] ?>  />
                                        <span class="ncua-switch__label">사용안함</span>
                                    </label>
                                </div>
                            </div>
                        </td>
                    </tr>
                    <!-- configExceptGoods 노출/미노출 스크립트 -->
                    <tr id="configExceptGoods">
                        <th><div>예외상품 설정<div></th>
                        <td>
                            <div class="ncua-search-result">
                                <div class="ncua-search-result__content">
                                    <div class="ncua-table ncua-table--horizontal ncua-table--border-bottom-radius-none ncua-except-goods-table">
                                        <table id="exceptGoodsTable">
                                            <colgroup>
                                                <col width="56px">
                                                <col width="80px">
                                                <col width="88px">
                                                <col >
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
                                                    if (is_array($data['exceptGoods']) && count($data['exceptGoods']) > 0) {
                                                        foreach ($data['exceptGoods'] as $key => $val): ?>
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
                                                                <td><div><span class="number"><?=$key + 1?></span></div></td>
                                                                <td><div><?=gd_html_goods_image($val['goodsNo'], $val['imageName'], $val['imagePath'], $val['imageStorage'], 50, $val['goodsNm'], '_blank')?></div></td>
                                                                <td><div class="ncua-left-align"><a class="ncua-link"  href="../goods/goods_register.php?goodsNo=<?=$val['goodsNo']?>" target="_blank"><?=$val['goodsNm']?></a></div></td>
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
                                            <button type="button"class="ncua-btn ncua-btn--xs ncua-btn--destructive ncua-select-delete js-delete-exceptGoods">
                                                선택 삭제
                                            </button>  
                                        </div>
                                    </div> 
                            
                                </div>

                            </div>
                        </td>
                    </tr>
                    <tr>
                        <th><div>댓글 기능</div></th>
                        <td>
                            <div>
                                <div class="ncua-switch ncua-switch--xs">
                                    <label class="ncua-switch__option ncua-switch__option--left ncua-switch__option--<?= $checked['memoFl']['y'] ? 'active' : 'inactive' ?>">
                                        <input class="ncua-switch__radio ncua-switch__radio--left" type="radio" name="memoFl" value="y" <?= $checked['memoFl']['y'] ?> />
                                        <span class="ncua-switch__label">사용함</span>
                                    </label>
                                    <label class="ncua-switch__option ncua-switch__option--right ncua-switch__option--<?= $checked['memoFl']['n'] ? 'active' : 'inactive' ?>">
                                        <input class="ncua-switch__radio ncua-switch__radio--right" type="radio"  name="memoFl" value="n" <?= $checked['memoFl']['n'] ?>  />
                                        <span class="ncua-switch__label">사용안함</span>
                                    </label>
                                </div>
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <th><div>댓글권한 설정</div></th>
                        <td>
                            <div class="ncua-flex-column ncua-flex-gap">
                                <div class="ncua-auth-config ncua-flex-gap" data-combobox-id="layer_member_group_auth_memo_combobox">
                                    <label class="ncua-radio-field ncua-radio-field--xs has-text">
                                        <span class="ncua-radio-input ncua-radio-input--xs ncua-radio-field__input">
                                            <input type="radio" name="authMemoWrite" value="all" <?= $checked['authMemoWrite']['all'] ?>/> 
                                        </span>
                                        <span class="ncua-radio-field__text">전체(회원+비회원)</span>
                                    </label>
                                    <label class="ncua-radio-field ncua-radio-field--xs has-text">
                                        <span class="ncua-radio-input ncua-radio-input--xs ncua-radio-field__input">
                                            <input type="radio" name="authMemoWrite" value="admin" <?= $checked['authMemoWrite']['admin'] ?>/> 
                                        </span>
                                        <span class="ncua-radio-field__text">관리자 전용</span>
                                    </label>
                                    <label class="ncua-radio-field ncua-radio-field--xs has-text">
                                        <span class="ncua-radio-input ncua-radio-input--xs ncua-radio-field__input">
                                            <input type="radio" name="authMemoWrite" value="member" <?= $checked['authMemoWrite']['member'] ?>/> 
                                        </span>
                                        <span class="ncua-radio-field__text">회원전용(비회원제외)</span>
                                    </label>
                                    <label class="ncua-radio-field ncua-radio-field--xs has-text">
                                        <span class="ncua-radio-input ncua-radio-input--xs ncua-radio-field__input">
                                            <input type="radio" name="authMemoWrite" value="group" <?= $checked['authMemoWrite']['group'] ?> /> 
                                        </span>
                                        <span class="ncua-radio-field__text">특정회원등급</span>
                                    </label>
                                    <div id="layer_member_group_auth_memo_combobox"></div>
                                </div>
                            <div id="member_groupLayer_auth_memo" class="ncua-flex ncua-gap-4 ncua-align-items-center member-group-display-none <?= ($data['authMemoWriteGroup']) ? 'active' : '' ?>">
                                <?php if (is_array($data['authMemoWriteGroup'])) { ?>
                                    <h5>선택된 회원등급 :</h5>
                                    <?php foreach ($data['authMemoWriteGroup'] as $k => $v) { ?>
                                    <div id="authMemoWriteGroup_<?= $k ?>" >
                                        <input type="hidden" name="authMemoWriteGroup[]" value="<?= $k ?>"/>

                                        <span class="ncua-tag ncua-tag--sm">
                                            <span class="ncua-tag__text"><?= $v ?></span>
                                            <button type="button" class="ncua-tag__close auth-memo-member-tag ncua-select-group-tag" onclick="removeMemberGroup('authMemoWriteGroup', <?= $k ?>)">
                                                <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" fill="none" viewBox="0 0 24 24" stroke="none">
                                                    <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 6 6 18M6 6l12 12" />
                                                </svg>
                                            </button>
                                        </span>
                                    </div>
                                    <?php }
                                } ?>
                            </div>

                            </div>
                        </td>
                    </tr>
                    <tr>
                        <th>
                            <div data-tooltip-seq="012">주문목록 리뷰등록 설정</div>
                        </th>
                        <td>
                            <div>
                                <div class="ncua-switch ncua-switch--xs">
                                    <label class="ncua-switch__option ncua-switch__option--left ncua-switch__option--<?= $checked['mypageFl']['y'] ? 'active' : 'inactive' ?>">
                                        <input class="ncua-switch__radio ncua-switch__radio--left" type="radio" name="mypageFl" value="y" <?= $checked['mypageFl']['y'] ?> />
                                        <span class="ncua-switch__label">노출함</span>
                                    </label>
                                    <label class="ncua-switch__option ncua-switch__option--right ncua-switch__option--<?= $checked['useFl']['n'] ? 'active' : 'inactive' ?>">
                                        <input class="ncua-switch__radio ncua-switch__radio--right" type="radio"  name="mypageFl" value="n" <?= $checked['mypageFl']['n'] ?>  />
                                        <span class="ncua-switch__label">노출안함</span>
                                    </label>
                                </div>
                            </div>
                                            </td>
                    </tr>
                    <tr>
                        <th>
                            <div>작성자 표시방법</div>
                        </th>
                        <td>
                            <div class="ncua-flex-gap">
                                <label class="ncua-radio-field ncua-radio-field--xs has-text">
                                    <span class="ncua-radio-input ncua-radio-input--xs ncua-radio-field__input">
                                        <input type="radio" name="writerDisplay" value="name" <?= $checked['writerDisplay']['name'] ?>/> 
                                    </span>
                                    <span class="ncua-radio-field__text">이름표시</span>
                                </label>
                                <label class="ncua-radio-field ncua-radio-field--xs has-text">
                                    <span class="ncua-radio-input ncua-radio-input--xs ncua-radio-field__input">
                                        <input type="radio" name="writerDisplay" value="id" <?= $checked['writerDisplay']['id'] ?>/> 
                                    </span>
                                    <span class="ncua-radio-field__text">아이디표시</span>
                                </label>
                                <label class="ncua-radio-field ncua-radio-field--xs has-text">
                                    <span class="ncua-radio-input ncua-radio-input--xs ncua-radio-field__input">
                                        <input type="radio" name="writerDisplay" value="nick" <?= $checked['writerDisplay']['nick'] ?>/> 
                                    </span>
                                    <span class="ncua-radio-field__text">닉네임표시</span>
                                </label>
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <th>
                            <div>관리자 표시방법</div>
                        </th>
                        <td>
                            <div>
                            관리자는 작성자에 '관리자'로 표시됩니다.                            </div>
                        </td>
                    </tr>
                    <tr>
                        <th>
                            <div>작성자 노출제한</div>
                        </th>
                        <td>
                            <?= $writerDisplayLimit?>
                            <div class="ncua-flex-gap">
                                <label class="ncua-radio-field ncua-radio-field--xs has-text">
                                    <span class="ncua-radio-input ncua-radio-input--xs ncua-radio-field__input">
                                        <input type="radio" name="writerDisplayLimit" value="0" <?= $selected['writerDisplayLimit'][0] === 'selected' ? 'checked' : '' ?>/> 
                                    </span>
                                    <span class="ncua-radio-field__text">전체노출</span>
                                </label>
                                <label class="ncua-radio-field ncua-radio-field--xs has-text">
                                    <span class="ncua-radio-input ncua-radio-input--xs ncua-radio-field__input">
                                        <input type="radio" name="writerDisplayLimit" value="1" <?= $selected['writerDisplayLimit'][1] === 'selected' ? 'checked' : '' ?>/> 
                                    </span>
                                    <span class="ncua-radio-field__text">1글자 노출</span>
                                </label>
                                <label class="ncua-radio-field ncua-radio-field--xs has-text">
                                    <span class="ncua-radio-input ncua-radio-input--xs ncua-radio-field__input">
                                        <input type="radio" name="writerDisplayLimit" value="2" <?= $selected['writerDisplayLimit'][2] === 'selected' ? 'checked' : '' ?>/> 
                                    </span>
                                    <span class="ncua-radio-field__text">2글자 노출</span>
                                </label>
                            </div>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </section>
</section>

