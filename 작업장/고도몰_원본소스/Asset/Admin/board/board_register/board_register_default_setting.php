<section class="ncua-card">
    <header class="ncua-card__header">
        <h4 class="ncua-card__title">기본설정</h4>
        <p class="ncua-card__sub-info"><strong>*는 필수 입력</strong> 항목입니다.</p>
    </header>
    <section class="ncua-card__body board-template-list">
        <div class="ncua-table ncua-table--vertical">
            <table>
                <tr>
                    <th class="ncua-required" ><div data-tooltip-seq="002">PC쇼핑몰 사용여부</div></th>
                    <td>
                        <div>
                            <div class="ncua-switch ncua-switch--xs" >
                                <label class="ncua-switch__option ncua-switch__option--left ncua-switch__option--active">
                                    <input class="ncua-switch__radio ncua-switch__radio--left" type="radio" value="y" name="bdUsePcFl" <?= $checked['bdUsePcFl']['y'] ?>/>
                                    <span class="ncua-switch__label">사용함</span>
                                </label>
                                <label class="ncua-switch__option ncua-switch__option--right ncua-switch__option--inactive">
                                    <input class="ncua-switch__radio ncua-switch__radio--right" type="radio" value="n" name="bdUsePcFl" <?= $checked['bdUsePcFl']['n'] ?>/>
                                    <span class="ncua-switch__label">사용안함</span>
                                </label>
                            </div>
                        </div>
                    </td>
                </tr>
                <tr>
                    <th class="ncua-required" ><div data-tooltip-seq="003">모바일쇼핑몰 사용여부</div></th>
                    <td>
                        <div>
                            <div class="ncua-switch ncua-switch--xs">
                                <label class="ncua-switch__option ncua-switch__option--left ncua-switch__option--active">
                                    <input class="ncua-switch__radio ncua-switch__radio--left" type="radio" value="y" name="bdUseMobileFl" <?= $checked['bdUseMobileFl']['y'] ?>/>
                                    <span class="ncua-switch__label">사용함</span>
                                </label>
                                <label class="ncua-switch__option ncua-switch__option--right ncua-switch__option--inactive">
                                    <input class="ncua-switch__radio ncua-switch__radio--right" type="radio" value="n" name="bdUseMobileFl" <?= $checked['bdUseMobileFl']['n'] ?>/>
                                    <span class="ncua-switch__label">사용안함</span>
                                </label>
                            </div>
                        </div>
                    </td>
                </tr>
                <tr>
                    <th class="ncua-required" ><div data-tooltip-seq="004">유형</div></th>
                    <td>
                        <div>    
                            <div class="ncua-image-radio-group">
                                <?php
                                if ($data['bdKind'] == 'qa') {
                                    unset($bdKindList['default'], $bdKindList['gallery'], $bdKindList['event']);
                                } elseif ($mode == 'modify') {
                                    unset($bdKindList['qa']);
                                }

                                foreach ($bdKindList as $key => $val) {
                                    ?>
                                    <label class="ncua-radio-field ncua-radio-field--xs has-text has-image">
                                        <div class="ncua-radio-field__header">
                                            <span class="ncua-radio-input ncua-radio-input--xs ncua-radio-field__input">
                                                <input type="radio" name="bdKind" value="<?= $key ?>" <?= $checked['bdKind'][$key] ?> />
                                            </span>
                                            <span class="ncua-radio-field__text"><?= $val ?></span>
                                        </div>
                                        <div class="ncua-radio-field__image">
                                            <img src="<?= PATH_ADMIN_GD_SHARE ?>img/board/type_<?= $key ?>.png">
                                        </div>
                                    </label>
                                <?php } ?>
                            </div>
                        </div>
                    </td>
                </tr>
                <tr>
                    <th class="ncua-required"><div>아이디</div></th>
                    <td>
                        <div class="ncua-gap-4">
                            <input type="hidden" name="chkbdId" id="chkbdId" value="<?= gd_isset($data['bdId']); ?>"/>
                            <?php if ($mode == 'regist') { ?>
                                <div class="ncua-input ncua-input--xs ncua-input-width-320">
                                    <div class="ncua-input__content">
                                        <div class="ncua-input__field ncua-input__field--xs"><input type="text" name="bdId" id="bdId" placeholder="2~30자 영문 또는 숫자로 생성 가능합니다"/></div>
                                    </div>
                                </div>
                                <button type="button" id="overlap_bdId" class="ncua-btn ncua-btn--xs ncua-btn--secondary-gray"><span class="ncua-btn__label">중복확인</span></button>
                            <?php } else { ?>
                                <b><?= gd_isset($data['bdId']); ?></b>
                            <?php } ?>
                        </div>
                    </td>
                </tr>
                <tr>
                    <th class="ncua-required"><div>게시판명</div></th>
                    <td>
                        <div>
                            <div class="ncua-input ncua-input--xs ncua-input-width-320">
                                <div class="ncua-input__content">
                                    <div class="ncua-input__field ncua-input__field--xs"><input type="text" name="bdNm" id="bdNm" value="<?= gd_isset($data['bdNm']); ?>" placeholder="게시판명을 입력하세요"/></div>
                                </div>
                            </div>
                        </div>
                    </td>
                </tr>
                <?php if ($mode == 'modify') { ?>
                    <tr>
                        <th><div>PC게시판 주소</div></th>
                        <td>
                            <div class="ncua-gap-4">
                                (쇼핑몰 주소) <?= $data['pageUrl'] ?>
                                <button type="button" data-clipboard-text="<?= $data['pageUrl'] ?>" class="ncua-clipboard ncua-btn ncua-copy" title="<?= $data['bdNm']; ?>"></button>
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <th><div>모바일게시판 주소</div></th>
                        <td>
                            <div class="ncua-gap-4">
                                (쇼핑몰 주소) <?= $data['pageMobileUrl'] ?>
                                <button type="button" data-clipboard-text="<?= $data['pageMobileUrl'] ?>" class="ncua-clipboard ncua-btn ncua-copy" title="<?= $data['bdNm']; ?>"></button>
                            </div>
                        </td>
                    </tr>
                <?php } ?>
                <tr>
                    <th class="ncua-required"><div data-tooltip-seq="005">게시판 스킨</div></th>
                    <td>
                        <div class="ncua-flex-column board-skin-table ncua-align-items-flex-start ncua-flex-gap">
                            <div class="ncua-table ncua-table--vertical">
                                <table>
                                    <colgroup>
                                        <col width="10%">
                                        <col>
                                        <col>
                                    </colgroup>
                                    <tr>
                                        <th class="th-border right-border"><div>구분</div></th>
                                        <th class="th-border right-border"><div>사용중인 디자인 스킨</div></th>
                                        <th class="th-border "><div>게시판 디자인 스킨 선택</div></th>
                                    </tr>
                                    <?php if (Globals::get('gSkin.frontSkinName')) { ?>
                                        <?php if (\Globals::get('gGlobal.isUse')) { ?>

                                            <?php foreach (\Globals::get('gGlobal.useMallList') as $key => $val) {
                                                $domainPostfix = $val['domainFl'] == 'kr' ? '' : ucfirst($val['domainFl']);
                                                ?>
                                                <tr>
                                                    <?php if ($val['sno'] == 1) { ?>
                                                        <th rowspan="<?= gd_count(\Globals::get('gGlobal.useMallList')) ?>"><div>PC 쇼핑몰</div></th>
                                                    <?php } ?>
                                                    <td><div class="ncua-gap-4"><span class="flag flag-16 flag-<?= $val['domainFl'] ?>"></span> <?= $val['skin']['frontLive'] ?></div></td>
                                                    <td>
                                                        <div>
                                                            <span class="ncua-select ncua-select--xs board-register-select-box-width-240">
                                                                <span class="ncua-select__content">
                                                                    <select class="ncua-select__tag" name="theme<?= $domainPostfix ?>Sno" id="theme<?= $domainPostfix ?>Sno">
                                                                        <option value="">선택해주세요</option>
                                                                        <?php //if ($mode == 'modify') {
                                                                        foreach ($selected['frontThemeList'][$val['domainFl']] as $row) {
                                                                            ?>
                                                                            <option value="<?= $row['sno'] ?>" data-basic="<?= $row['bdBasicFl'] ?>" <?php if ($row['sno'] == $data['theme' . $domainPostfix . 'Sno']) echo 'selected' ?>><?= $row['themeNm'] ?> (<?= $row['themeId'] ?>)</option>
                                                                        <?php }
                                                                        //  } ?>
                                                                    </select>
                                                                </span>
                                                            </span>
                                                        </div>
                                                    </td>
                                                </tr>
                                            <?php } ?>
                                        <?php } else { ?>
                                            <tr>
                                                <th><div>PC 쇼핑몰</div></th>
                                                <td>
                                                    <div><?= Globals::get('gSkin.frontSkinName') ?></div>
                                                </td>
                                                <td>
                                                    <div>
                                                        <span class="ncua-select ncua-select--xs board-register-select-box-width-240">
                                                            <span class="ncua-select__content">
                                                                <select class="ncua-select__tag" name="themeSno" id="themeSno">
                                                                    <option value="">선택해주세요</option>
                                                                    <?php //if ($mode == 'modify') {
                                                                    foreach ($selected['frontThemeList'] as $row) { ?>
                                                                        <option data-basic="<?= $row['bdBasicFl'] ?>" value="<?= $row['sno'] ?>" <?php if ($row['sno'] == $data['themeSno']) echo 'selected' ?>><?= $row['themeNm'] ?> (<?= $row['themeId'] ?>)</option>
                                                                    <?php }
                                                                    //    } ?>
                                                                </select>
                                                            </span>
                                                        </span>
                                                    </div>
                                                </td>
                                            </tr>
                                        <?php } ?>
                                    <?php } ?>
                                    <?php if (Globals::get('gSkin.mobileSkinName')) { ?>
                                        <?php if (\Globals::get('gGlobal.isUse')) { ?>

                                            <?php foreach (\Globals::get('gGlobal.useMallList') as $key => $val) {
                                                $domainPostfix = $val['domainFl'] == 'kr' ? '' : ucfirst($val['domainFl']);
                                                ?>
                                                <tr>
                                                    <?php if ($val['sno'] == 1) { ?>
                                                        <th rowspan="<?= gd_count(\Globals::get('gGlobal.useMallList')) ?>"><div>모바일 쇼핑몰</div></th>
                                                    <?php } ?>
                                                    <td><div class="ncua-gap-4"><span class="flag flag-16 flag-<?= $val['domainFl'] ?>"></span> <?= $val['skin']['mobileLive'] ?></div></td>
                                                    <td>
                                                        <div>
                                                            <span class="ncua-select ncua-select--xs board-register-select-box-width-240">
                                                                <span class="ncua-select__content">
                                                                    <select class="ncua-select__tag" name="mobileTheme<?= $domainPostfix ?>Sno" id="mobileTheme<?= $domainPostfix ?>Sno">
                                                                        <option value="">선택해주세요</option>
                                                                        <?php //if ($mode == 'modify') {
                                                                        foreach ($selected['mobileThemeList'][$val['domainFl']] as $row) { ?>
                                                                            <option value="<?= $row['sno'] ?>" <?php if ($row['sno'] == $data['mobileTheme' . $domainPostfix . 'Sno']) echo 'selected' ?>><?= $row['themeNm'] ?> (<?= $row['themeId'] ?>)</option>
                                                                        <?php }
                                                                        //   } ?>
                                                                    </select>
                                                                </span>
                                                            </span>
                                                        </div>
                                                    </td>
                                                </tr>
                                            <?php } ?>


                                        <?php } else { ?>
                                            <tr>
                                                <th><div>모바일 쇼핑몰</div></th>
                                                <td>
                                                    <div><?= Globals::get('gSkin.mobileSkinName') ?></div>
                                                </td>
                                                <td>
                                                    <div>
                                                        <span class="ncua-select ncua-select--xs board-register-select-box-width-240">
                                                            <span class="ncua-select__content">
                                                                <select class="ncua-select__tag" name="mobileThemeSno" id="mobileThemeSno">
                                                                    <option value="">선택해주세요</option>
                                                                    <?php //if ($mode == 'modify') {
                                                                    foreach ($selected['mobileThemeList'] as $row) { ?>
                                                                        <option value="<?= $row['sno'] ?>" <?php if ($row['sno'] == $data['mobileThemeSno']) echo 'selected' ?>><?= $row['themeNm'] ?> (<?= $row['themeId'] ?>)</option>
                                                                    <?php }
                                                                    //  } ?>
                                                                </select>
                                                            </span>
                                                        </span>
                                                    </div>
                                                </td>
                                            </tr>
                                        <?php } ?>
                                    <?php } ?>
                                </table>
                            </div> 
                        
                            <button type="button" onclick="addSkin()" class="ncua-btn ncua-btn--xs ncua-btn--secondary-gray">
                                <span class="ncua-btn__label">게시판 스킨등록</span>
                            </button>
                        </div>
                    </td>
                </tr>

                <tr>
                    <th><div>리스트권한 설정</div></th>
                    <td>
                        <div class="ncua-flex-column ncua-align-items-flex-start ncua-flex-gap">
                            <div class="ncua-flex-gap ncua-flex" data-combobox-id="layer_member_group_list_combobox">
                                <label class="ncua-radio-field ncua-radio-field--xs has-text ncua-align-items-center">
                                    <span class="ncua-radio-input ncua-radio-input--xs ncua-radio-field__input"><input type="radio" name="bdAuthList" value="all" <?= $checked['bdAuthList']['all'] ?> /></span>
                                    <span>
                                        <span class="ncua-radio-field__text">전체(회원+비회원)</span>
                                    </span>
                                </label>
                                <label class="ncua-radio-field ncua-radio-field--xs has-text ncua-align-items-center">
                                    <span class="ncua-radio-input ncua-radio-input--xs ncua-radio-field__input"><input type="radio" name="bdAuthList" value="admin" <?= $checked['bdAuthList']['admin'] ?> /></span>
                                    <span>
                                        <span class="ncua-radio-field__text">관리자 전용</span>
                                    </span>
                                </label>
                                <label class="ncua-radio-field ncua-radio-field--xs has-text ncua-align-items-center">
                                    <span class="ncua-radio-input ncua-radio-input--xs ncua-radio-field__input"><input type="radio" name="bdAuthList" value="member" <?= $checked['bdAuthList']['member'] ?> /></span>
                                    <span>
                                        <span class="ncua-radio-field__text">회원전용(비회원제외)</span>
                                    </span>
                                </label>
                                <label class="ncua-radio-field ncua-radio-field--xs has-text ncua-align-items-center" >
                                    <span class="ncua-radio-input ncua-radio-input--xs ncua-radio-field__input"><input type="radio" name="bdAuthList" value="group" <?= $checked['bdAuthList']['group'] ?>/></span>
                                    <span class="ncua-radio-field__text">특정회원등급</span>
                                </label>
                                <div id="layer_member_group_list_combobox"></div>
                            </div>
                            <div id="member_groupLayer_list" class="ncua-flex ncua-flex-wrap ncua-gap-4 ncua-align-items-center member-group-display-none <?= is_array($data['bdAuthListGroup']) ? 'active' : '' ?>">
                                <?php if (is_array($data['bdAuthListGroup'])) { ?>
                                    <h5>선택된 회원등급 : </h5>
                                    <?php foreach ($data['bdAuthListGroup'] as $k => $v) { ?>
                                        <div id="bdAuthListGroup_<?= $k ?>">
                                            <input type="hidden" name="bdAuthListGroup[]" value="<?= $k ?>"/>
                                            <span class="ncua-tag ncua-tag--sm">
                                                <span class="ncua-tag__text"><?= $v ?></span>
                                                <button type="button" class="ncua-tag__close board-register-member-select-group-tag" onclick="removeMemberGroup('bdAuthListGroup', <?= $k ?>)">
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
                    <th><div>읽기권한 설정</div></th>
                    <td>
                        <div class="ncua-flex-column ncua-align-items-flex-start ncua-flex-gap">
                            <div class="ncua-flex-gap ncua-flex" data-combobox-id="layer_member_group_read_combobox">
                                <label class="ncua-radio-field ncua-radio-field--xs has-text ncua-align-items-center">
                                    <span class="ncua-radio-input ncua-radio-input--xs ncua-radio-field__input"><input type="radio" name="bdAuthRead" value="all" <?= $checked['bdAuthRead']['all'] ?> /></span>
                                    <span>
                                        <span class="ncua-radio-field__text">전체(회원+비회원)</span>
                                    </span>
                                </label>
                                <label class="ncua-radio-field ncua-radio-field--xs has-text ncua-align-items-center">
                                    <span class="ncua-radio-input ncua-radio-input--xs ncua-radio-field__input"><input type="radio" name="bdAuthRead" value="admin" <?= $checked['bdAuthRead']['admin'] ?> /></span>
                                    <span>
                                        <span class="ncua-radio-field__text">관리자 전용</span>
                                    </span>
                                </label>
                                <label class="ncua-radio-field ncua-radio-field--xs has-text ncua-align-items-center">
                                    <span class="ncua-radio-input ncua-radio-input--xs ncua-radio-field__input"><input type="radio" name="bdAuthRead" value="member" <?= $checked['bdAuthRead']['member'] ?> /></span>
                                    <span>
                                        <span class="ncua-radio-field__text">회원전용(비회원제외)</span>
                                    </span>
                                </label>
                                <label class="ncua-radio-field ncua-radio-field--xs has-text ncua-align-items-center" >
                                    <span class="ncua-radio-input ncua-radio-input--xs ncua-radio-field__input"><input type="radio" name="bdAuthRead" value="group" <?= $checked['bdAuthRead']['group'] ?>/></span>
                                    <span class="ncua-radio-field__text">특정회원등급</span>
                                </label>
                                <div id="layer_member_group_read_combobox"></div>
                            </div>
                            <div id="member_groupLayer_read" class="ncua-flex ncua-flex-wrap ncua-gap-4 ncua-align-items-center member-group-display-none <?= is_array($data['bdAuthReadGroup']) ? 'active' : '' ?>">
                                <?php if (is_array($data['bdAuthReadGroup'])) { ?>
                                    <h5>선택된 회원등급 : </h5>
                                    <?php foreach ($data['bdAuthReadGroup'] as $k => $v) { ?>
                                        <div id="bdAuthReadGroup_<?= $k ?>">
                                            <input type="hidden" name="bdAuthReadGroup[]" value="<?= $k ?>"/>
                                            <span class="ncua-tag ncua-tag--sm">
                                                <span class="ncua-tag__text"><?= $v ?></span>
                                                <button type="button" class="ncua-tag__close board-register-member-select-group-tag" onclick="removeMemberGroup('bdAuthReadGroup', <?= $k ?>)">
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
                    <th><div>쓰기권한 설정</div></th>
                    <td>
                        <div class="ncua-flex-column ncua-align-items-flex-start ncua-flex-gap">
                            <div class="ncua-flex-gap ncua-flex" data-combobox-id="layer_member_group_write_combobox">
                                <label class="ncua-radio-field ncua-radio-field--xs has-text ncua-align-items-center">
                                    <span class="ncua-radio-input ncua-radio-input--xs ncua-radio-field__input"><input type="radio" name="bdAuthWrite" value="all" <?= $checked['bdAuthWrite']['all'] ?> class="if-is-event-disabled"/></span>
                                    <span>
                                        <span class="ncua-radio-field__text">전체(회원+비회원)</span>
                                    </span>
                                </label>
                                <label class="ncua-radio-field ncua-radio-field--xs has-text ncua-align-items-center">
                                    <span class="ncua-radio-input ncua-radio-input--xs ncua-radio-field__input"><input type="radio" name="bdAuthWrite" value="admin" <?= $checked['bdAuthWrite']['admin'] ?> class="if-is-event-checked"/></span>
                                    <span>
                                        <span class="ncua-radio-field__text">관리자 전용</span>
                                    </span>
                                </label>
                                <label class="ncua-radio-field ncua-radio-field--xs has-text ncua-align-items-center">
                                    <span class="ncua-radio-input ncua-radio-input--xs ncua-radio-field__input"><input type="radio" name="bdAuthWrite" value="member" <?= $checked['bdAuthWrite']['member'] ?> class="if-is-event-disabled"/></span>
                                    <span>
                                        <span class="ncua-radio-field__text">회원전용(비회원제외)</span>
                                    </span>
                                </label>
                                <label class="ncua-radio-field ncua-radio-field--xs has-text ncua-align-items-center">
                                    <span class="ncua-radio-input ncua-radio-input--xs ncua-radio-field__input"><input type="radio" name="bdAuthWrite" value="group" <?= $checked['bdAuthWrite']['group'] ?> class="if-is-event-disabled"/></span>
                                    <span class="ncua-radio-field__text">특정회원등급</span>
                                </label>
                                <div id="layer_member_group_write_combobox"></div>
                            </div>
                            <div id="member_groupLayer_write" class="ncua-flex ncua-flex-wrap ncua-gap-4 ncua-align-items-center member-group-display-none <?= is_array($data['bdAuthWriteGroup']) ? 'active' : '' ?>">
                                <?php if (is_array($data['bdAuthWriteGroup'])) { ?>
                                    <h5>선택된 회원등급 : </h5>
                                    <?php foreach ($data['bdAuthWriteGroup'] as $k => $v) { ?>
                                        <div id="bdAuthWriteGroup_<?= $k ?>">
                                            <input type="hidden" name="bdAuthWriteGroup[]" value="<?= $k ?>"/>
                                            <span class="ncua-tag ncua-tag--sm">
                                                <span class="ncua-tag__text"><?= $v ?></span>
                                                <button type="button" class="ncua-tag__close board-register-member-select-group-tag" onclick="removeMemberGroup('bdAuthWriteGroup', <?= $k ?>)">
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
                <?php if($isGoodsReview) {?>
                    <tr>
                        <th><div>쓰기권한 추가 기준</div></th>
                        <td>
                            <div class="ncua-flex-column ncua-flex-gap ncua-align-items-flex-start">
                                <div>
                                    <label class="ncua-radio-field ncua-radio-field--xs has-text">
                                        <span class="ncua-radio-input ncua-radio-input--xs ncua-radio-field__input"><input type="radio" name="bdReviewAuthWrite" value="all" <?= $checked['bdReviewAuthWrite']['all'] ?> /></span>
                                        <span>
                                            <span class="ncua-radio-field__text">구매 여부와 상관없이 후기 작성 가능</span>
                                        </span>
                                    </label>
                                </div>
                                <div class="ncua-flex-column ncua-gap-8">
                                    <div class="write-permission-setting">
                                        <label class="ncua-radio-field ncua-radio-field--xs has-text">
                                            <span class="ncua-radio-input ncua-radio-input--xs ncua-radio-field__input">
                                                <input type="radio" name="bdReviewAuthWrite" value="buyer" <?= $checked['bdReviewAuthWrite']['buyer'] ?> />
                                            </span>
                                            <span><span class="ncua-radio-field__text">구매 내역이 존재하는 경우에만 후기 작성 가능</span></span>
                                        </label>
                                        
                                        <div class="ncua-flex ncua-gap-8 ncua-align-items-center">
                                            <span>( 작성 가능 시점 :</span>
                                            <span class="ncua-select ncua-select--xs ncua-input-width-120">
                                                <span class="ncua-select__content">
                                                    <select name="bdReviewOrderStatus" class="ncua-select__tag ">
                                                        <option value="p1" <?=$selected['bdReviewOrderStatus']['p1']?>>결제 완료 이후</option>
                                                        <option value="d1" <?=$selected['bdReviewOrderStatus']['d1']?>>배송중 이후</option>
                                                        <option value="d2" <?=$selected['bdReviewOrderStatus']['d2']?>>배송완료 이후</option>
                                                        <option value="s1" <?=$selected['bdReviewOrderStatus']['s1']?>>구매확정 이후</option>
                                                    </select>
                                                </span>
                                            </span>
                                            <span>)</span>
                                        </div>
                                    </div>
                                    <div class="write-permission-setting-add-option">
                                        <label class="ncua-checkbox-field ncua-checkbox-field--xs has-text">
                                            <span class="ncua-checkbox-input ncua-checkbox-input--xs ncua-checkbox-field__input">
                                                <input type="checkbox" name="bdReviewPeriodFl" value="y" <?=$checked['bdReviewPeriodFl']?> />
                                            </span>
                                            <span><span class="ncua-checkbox-field__text">작성 가능 시점으로부터</span></span>
                                        </label>
                                        
                                        <div class="ncua-flex ncua-gap-8 ncua-align-items-center">
                                            <span class="ncua-select ncua-select--xs ncua-input-width-80">
                                                <span class="ncua-select__content">
                                                    <select name="bdReviewPeriod" class="ncua-select__tag">
                                                        <?php foreach($periodDay as $val) {?>
                                                            <option value="<?=$val?>" <?=$selected['bdReviewPeriod'][$val]?>><?=$val?>일</option>
                                                        <?php }?>
                                                    </select>
                                                </span>
                                            </span>
                                            <span>이내만 후기 작성 가능.</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </td>
                    </tr>
                <?php } ?>
                <tr class="if-is-event-hide">
                    <th><div data-tooltip-seq="006">답변 기능</div></th>
                    <td>
                        <div class="ncua-flex-column ncua-align-items-flex-start ncua-flex-gap">
                            <div class="ncua-switch ncua-switch--xs" >
                                <label class="ncua-switch__option ncua-switch__option--left ncua-switch__option--active">
                                    <input class="if-is-event-disabled ncua-switch__radio ncua-switch__radio--left" type="radio" value="y" name="bdReplyFl" <?= gd_isset($checked['bdReplyFl']['y']) ?> />
                                    <span class="ncua-switch__label">사용함</span>
                                </label>
                                <label class="ncua-switch__option ncua-switch__option--right ncua-switch__option--inactive">
                                    <input class="if-is-event-checked ncua-switch__radio ncua-switch__radio--right" type="radio" value="n" name="bdReplyFl" <?= gd_isset($checked['bdReplyFl']['n']) ?> />
                                    <span class="ncua-switch__label">사용안함</span>
                                </label>
                            </div>
                            
                            <div>
                                <label class="ncua-checkbox-field ncua-checkbox-field--xs has-text reply-function">
                                    <span class="ncua-checkbox-input ncua-checkbox-input--xs ncua-checkbox-field__input">
                                        <input type="checkbox" name="bdAnswerStatusFl" value="y" <?=$checked['bdAnswerStatusFl']?> />
                                    </span>
                                    <span><span class="ncua-checkbox-field__text">답변관리 기능 사용</span></span>
                                </label>
                            </div>
                        </div>
                    </td>
                </tr>
                <tr class="if-is-event-hide">
                    <th><div>답변권한 설정</div></th>
                    <td>
                        <div class="ncua-flex-column ncua-align-items-flex-start ncua-flex-gap">
                            <div class="ncua-flex-gap ncua-flex" data-combobox-id="layer_member_group_reply_combobox">
                                <label class="ncua-radio-field ncua-radio-field--xs has-text ncua-align-items-center">
                                    <span class="ncua-radio-input ncua-radio-input--xs ncua-radio-field__input"><input type="radio" name="bdAuthReply" value="all" <?= $checked['bdAuthReply']['all'] ?> /></span>
                                    <span>
                                        <span class="ncua-radio-field__text">전체(회원+비회원)</span>
                                    </span>
                                </label>
                                <label class="ncua-radio-field ncua-radio-field--xs has-text ncua-align-items-center">
                                    <span class="ncua-radio-input ncua-radio-input--xs ncua-radio-field__input"><input type="radio" name="bdAuthReply" value="admin" <?= $checked['bdAuthReply']['admin'] ?> /></span>
                                    <span>
                                        <span class="ncua-radio-field__text">관리자 전용</span>
                                    </span>
                                </label>
                                <label class="ncua-radio-field ncua-radio-field--xs has-text ncua-align-items-center">
                                    <span class="ncua-radio-input ncua-radio-input--xs ncua-radio-field__input"><input type="radio" name="bdAuthReply" value="member" <?= $checked['bdAuthReply']['member'] ?> /></span>
                                    <span>
                                        <span class="ncua-radio-field__text">회원전용(비회원제외)</span>
                                    </span>
                                </label>
                                <label class="ncua-radio-field ncua-radio-field--xs has-text ncua-align-items-center">
                                    <span class="ncua-radio-input ncua-radio-input--xs ncua-radio-field__input"><input type="radio" name="bdAuthReply" value="group" <?= $checked['bdAuthReply']['group'] ?>/></span>
                                    <span class="ncua-radio-field__text">특정회원등급</span>
                                </label>
                                <div id="layer_member_group_reply_combobox"></div>
                            </div>
                            <div id="member_groupLayer_reply" class="ncua-flex ncua-flex-wrap ncua-gap-4 ncua-align-items-center member-group-display-none <?= is_array($data['bdAuthReplyGroup']) ? 'active' : '' ?>">
                                <?php if (is_array($data['bdAuthReplyGroup'])) { ?>
                                    <h5>선택된 회원등급 : </h5>
                                    <?php foreach ($data['bdAuthReplyGroup'] as $k => $v) { ?>
                                        <div id="bdAuthReplyGroup_<?= $k ?>">
                                            <input type="hidden" name="bdAuthReplyGroup[]" value="<?= $k ?>"/>
                                            <span class="ncua-tag ncua-tag--sm">
                                                <span class="ncua-tag__text"><?= $v ?></span>
                                                <button type="button" class="ncua-tag__close board-register-member-select-group-tag" onclick="removeMemberGroup('bdAuthReplyGroup', <?= $k ?>)">
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
                <tr class="if-is-qa-hide">
                    <th><div>댓글 기능</div></th>
                    <td>
                        <div>
                            <div class="ncua-switch ncua-switch--xs" >
                                <label class="ncua-switch__option ncua-switch__option--left ncua-switch__option--active">
                                    <input class="ncua-switch__radio ncua-switch__radio--left" type="radio" value="y" name="bdMemoFl" <?= gd_isset($checked['bdMemoFl']['y']) ?> />
                                    <span class="ncua-switch__label">사용함</span>
                                </label>
                                <label class="ncua-switch__option ncua-switch__option--right ncua-switch__option--inactive">
                                    <input class="ncua-switch__radio ncua-switch__radio--right" type="radio" value="n" name="bdMemoFl" <?= gd_isset($checked['bdMemoFl']['n']) ?> />
                                    <span class="ncua-switch__label">사용안함</span>
                                </label>
                            </div>
                        </div>
                    </td>
                </tr>
                <tr class="if-is-qa-hide">
                    <th><div>댓글권한 설정</div></th>
                    <td>
                        <div class="ncua-flex-column ncua-align-items-flex-start ncua-flex-gap">
                            <div class="ncua-flex-gap ncua-flex" data-combobox-id="layer_member_group_memo_combobox">
                                <label class="ncua-radio-field ncua-radio-field--xs has-text ncua-align-items-center">
                                    <span class="ncua-radio-input ncua-radio-input--xs ncua-radio-field__input"><input type="radio" name="bdAuthMemo" value="all" <?= $checked['bdAuthMemo']['all'] ?> /></span>
                                    <span>
                                        <span class="ncua-radio-field__text">전체(회원+비회원)</span>
                                    </span>
                                </label>
                                <label class="ncua-radio-field ncua-radio-field--xs has-text ncua-align-items-center">
                                    <span class="ncua-radio-input ncua-radio-input--xs ncua-radio-field__input"><input type="radio" name="bdAuthMemo" value="admin" <?= $checked['bdAuthMemo']['admin'] ?> /></span>
                                    <span>
                                        <span class="ncua-radio-field__text">관리자 전용</span>
                                    </span>
                                </label>
                                <label class="ncua-radio-field ncua-radio-field--xs has-text ncua-align-items-center">
                                    <span class="ncua-radio-input ncua-radio-input--xs ncua-radio-field__input"><input type="radio" name="bdAuthMemo" value="member" <?= $checked['bdAuthMemo']['member'] ?> /></span>
                                    <span>
                                        <span class="ncua-radio-field__text">회원전용(비회원제외)</span>
                                    </span>
                                </label>
                                <label class="ncua-radio-field ncua-radio-field--xs has-text ncua-align-items-center">
                                    <span class="ncua-radio-input ncua-radio-input--xs ncua-radio-field__input"><input type="radio" name="bdAuthMemo" value="group" <?= $checked['bdAuthMemo']['group'] ?>/></span>
                                    <span class="ncua-radio-field__text">특정회원등급</span>
                                </label>
                                <div id="layer_member_group_memo_combobox"></div>
                            </div>
                            <div id="member_groupLayer_memo" class="ncua-flex ncua-flex-wrap ncua-gap-4 ncua-align-items-center member-group-display-none <?= is_array($data['bdAuthMemoGroup']) ? 'active' : '' ?>">
                                <?php if (is_array($data['bdAuthMemoGroup'])) { ?>
                                    <h5>선택된 회원등급 : </h5>
                                    <?php foreach ($data['bdAuthMemoGroup'] as $k => $v) { ?>
                                        <div id="bdAuthMemoGroup_<?= $k ?>">
                                            <input type="hidden" name="bdAuthMemoGroup[]" value="<?= $k ?>"/>
                                            <span class="ncua-tag ncua-tag--sm">
                                                <span class="ncua-tag__text"><?= $v ?></span>
                                                <button type="button" class="ncua-tag__close board-register-member-select-group-tag" onclick="removeMemberGroup('bdAuthMemoGroup', <?= $k ?>)">
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
                        <div>작성자 표시방법</div>
                    </th>
                    <td>
                        <div class="ncua-flex-gap">
                            <label class="ncua-radio-field ncua-radio-field--xs has-text">
                                <span class="ncua-radio-input ncua-radio-input--xs ncua-radio-field__input">
                                    <input type="radio" name="bdUserDsp"
                                    value="name" <?= gd_isset($checked['bdUserDsp']['name']) ?> />
                                </span>
                                <span>
                                    <span class="ncua-radio-field__text">이름표시</span>
                                </span>
                            </label>
                            <label class="ncua-radio-field ncua-radio-field--xs has-text">
                                <span class="ncua-radio-input ncua-radio-input--xs ncua-radio-field__input">
                                    <input type="radio" type="radio" name="bdUserDsp"
                                    value="nick" <?= gd_isset($checked['bdUserDsp']['nick']) ?> />
                                </span>
                                <span>
                                    <span class="ncua-radio-field__text">닉네임표시</span>
                                </span>
                            </label>
                            <label class="ncua-radio-field ncua-radio-field--xs has-text">
                                <span class="ncua-radio-input ncua-radio-input--xs ncua-radio-field__input">
                                    <input type="radio" name="bdUserDsp"
                                    value="id" <?= gd_isset($checked['bdUserDsp']['id']) ?> />
                                </span>
                                <span>
                                    <span class="ncua-radio-field__text">아이디표시</span>
                                </span>
                            </label>
                        </div>
                    </td>
                </tr>

                <tr>
                    <th>
                        <div>작성자 노출제한</div>
                    </th>
                    <td>
                        <div>
                            <span class="ncua-select ncua-select--xs board-register-select-box-width-240">
                                <span class="ncua-select__content">
                                    <select class="ncua-select__tag" name="bdUserLimitDsp">
                                        <option value="0" <?= gd_isset($selected['bdUserLimitDsp'][0]) ?>>전체노출</option>
                                        <option value="1" <?= gd_isset($selected['bdUserLimitDsp'][1]) ?>>1글자 노출</option>
                                        <option value="2" <?= gd_isset($selected['bdUserLimitDsp'][2]) ?>>2글자 노출</option>
                                    </select>
                                </span>
                            </span>
                        </div>
                    </td>
                </tr>

                <tr>
                    <th><div>운영자 표시방법</div></th>
                    <td>
                        <div class="ncua-flex-gap">
                            <label class="ncua-radio-field ncua-radio-field--xs has-text">
                                <span class="ncua-radio-input ncua-radio-input--xs ncua-radio-field__input">
                                    <input type="radio" name="bdAdminDsp"
                                    value="nick" <?= gd_isset($checked['bdAdminDsp']['nick']) ?> />
                                </span>
                                <span>
                                    <span class="ncua-radio-field__text">닉네임표시</span>
                                </span>
                            </label>
                            <label class="ncua-radio-field ncua-radio-field--xs has-text">
                                <span class="ncua-radio-input ncua-radio-input--xs ncua-radio-field__input">
                                    <input type="radio" name="bdAdminDsp"
                                    value="image" <?= gd_isset($checked['bdAdminDsp']['image']) ?> />
                                </span>
                                <span>
                                    <span class="ncua-radio-field__text">이미지표시</span>
                                </span>
                            </label>
                        </div>
                    </td>
                </tr>
                <?php if (gd_use_provider() === true) { ?>
                    <tr>
                        <th><div>공급사 표시방법</div></th>
                        <td>
                            <div class="ncua-flex-gap">
                                <label class="ncua-radio-field ncua-radio-field--xs has-text">
                                    <span class="ncua-radio-input ncua-radio-input--xs ncua-radio-field__input">
                                        <input type="radio" name="bdSupplyDsp"
                                        value="nick" <?= gd_isset($checked['bdSupplyDsp']['nick']) ?> />
                                    </span>
                                    <span>
                                        <span class="ncua-radio-field__text">닉네임표시</span>
                                    </span>
                                </label>
                                <label class="ncua-radio-field ncua-radio-field--xs has-text">
                                    <span class="ncua-radio-input ncua-radio-input--xs ncua-radio-field__input">
                                        <input type="radio" name="bdSupplyDsp"
                                        value="image" <?= gd_isset($checked['bdSupplyDsp']['image']) ?> />
                                    </span>
                                    <span>
                                        <span class="ncua-radio-field__text">이미지표시</span>
                                    </span>
                                </label>
                            </div>
                        </td>
                    </tr>
                <?php } ?>
                <tr>
                    <th><div data-tooltip-seq="007">저장 위치</div></th>
                    <td>
                        <div class="save-location-wrapper">
                            <span class="ncua-select ncua-select--xs board-register-select-box-width-240">
                                <span class="ncua-select__content">
                                    <select class="ncua-select__tag" name="bdUploadStorage" id="bdUploadStorage">
                                        <?php
                                        foreach ($storageBox as $key => $val) {
                                            $selected = '';
                                            if ($key == gd_isset($data['bdUploadStorage'])) {
                                                $selected = ' selected="selected" ';
                                            }
                                            echo '<option value="' . $key . '" ' . $selected . '>' . $val . '</option>';
                                        }
                                        ?>
                                    </select>
                                </span>
                            </span>
                            <div class="save-location-text">
                                <span>파일 저장 위치 :</span>
                                <span id="spanFileStorage">
                                </span>
                                <div class="ncua-input ncua-input--xs ncua-input-width-320">
                                    <div class="ncua-input__content">
                                        <div class="ncua-input__field ncua-input__field--xs">
                                            <input type="text" name="bdUploadPath" id="bdUploadPath" value="<?= gd_isset($data['bdUploadPath']) ?>" readonly="readonly" size="30"/>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="save-location-text">
                                <span>썸네일 저장 위치 :</span>
                                <span id="spanFileThumbStorage">
                                </span>
                                <div class="ncua-input ncua-input--xs ncua-input-width-320">
                                    <div class="ncua-input__content">
                                        <div class="ncua-input__field ncua-input__field--xs">
                                            <input type="text" name="bdUploadThumbPath" id="bdUploadThumbPath" value="<?= gd_isset($data['bdUploadThumbPath']) ?>" readonly="readonly" size="30"/>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </td>
                </tr>

                <tr class="bdReplyFl-area <?php if(gd_isset($checked['bdReplyFl']['n']) || $data['bdKind'] === 'qa'){ echo 'display-none'; } ?>">
                    <th><div data-tooltip-seq="008">게시글 삭제 설정</div></th>
                    <td>
                        <div class="ncua-flex-column ncua-flex-gap ncua-align-items-flex-start">
                            <label class="ncua-radio-field ncua-radio-field--xs has-text">
                                <span class="ncua-radio-input ncua-radio-input--xs ncua-radio-field__input">
                                    <input type="radio" name="bdReplyDelFl"
                                    value="applicable" <?= gd_isset($checked['bdReplyDelFl']['applicable']) ?> />
                                </span>
                                <span>
                                    <span class="ncua-radio-field__text">답변글이 있는 게시글 삭제시, 해당 글만 삭제</span>
                                </span>
                            </label>
                            <label class="ncua-radio-field ncua-radio-field--xs has-text">
                                <span class="ncua-radio-input ncua-radio-input--xs ncua-radio-field__input">
                                    <input type="radio" name="bdReplyDelFl"
                                    value="reply" <?= gd_isset($checked['bdReplyDelFl']['reply']) ?> />
                                </span>
                                <span>
                                    <span class="ncua-radio-field__text">답변글이 있는 게시글 삭제시, 답변글도 함께 삭제</span>
                                </span>
                            </label>
                        </div>
                    </td>
                </tr>

                <tr>
                    <th>
                        <div data-tooltip-seq="011">게시글 3년 경과<br>
                        자동 삭제 설정</div>
                    </th>
                    <td>
                        <div>
                            <div class="ncua-switch ncua-switch--xs">
                                <label class="ncua-switch__option ncua-switch__option--left ncua-switch__option--active">
                                    <input class="ncua-switch__radio ncua-switch__radio--left" name="bdAutoDelFl" type="radio"
                                    value='y' <?= gd_isset($checked['bdAutoDelFl']['y']) ?>  />
                                    <span class="ncua-switch__label">사용함</span>
                                </label>
                                <label class="ncua-switch__option ncua-switch__option--right ncua-switch__option--inactive">
                                    <input class="ncua-switch__radio ncua-switch__radio--right" name="bdAutoDelFl" type="radio"
                                    value='n' <?= gd_isset($checked['bdAutoDelFl']['n']) ?> />
                                    <span class="ncua-switch__label">사용안함</span>
                                </label>
                            </div>
                        </div>
                    </td>
                </tr>

                <tr>
                    <th><div>마일리지 사용유무</div></th>
                    <td>
                        <div>
                            <div class="ncua-switch ncua-switch--xs">
                                <label class="ncua-switch__option ncua-switch__option--left ncua-switch__option--active">
                                    <input class="ncua-switch__radio ncua-switch__radio--left" name="bdMileageFl" type="radio"
                                    value='y' <?= gd_isset($checked['bdMileageFl']['y']) ?>  />
                                    <span class="ncua-switch__label">사용함</span>
                                </label>
                                <label class="ncua-switch__option ncua-switch__option--right ncua-switch__option--inactive">
                                    <input class="ncua-switch__radio ncua-switch__radio--right" name="bdMileageFl" type="radio"
                                    value='n' <?= gd_isset($checked['bdMileageFl']['n']) ?> />
                                    <span class="ncua-switch__label">사용안함</span>
                                </label>
                            </div>
                        </div>
                    </td>
                </tr>

                <tr class="bdMileageFl-area">
                    <th><div data-tooltip-seq="010">마일리지 지급</div></th>
                    <td>
                        <div class="ncua-flex-column ncua-gap-8 ncua-align-items-flex-start" >
                            <div class="ncua-flex ncua-align-items-center ncua-gap-4">
                                게시글 작성 시
                                <div class="ncua-input ncua-input--xs ncua-input-width-120">
                                    <div class="ncua-input__content">
                                        <div class="ncua-input__field ncua-input__field--xs">
                                            <input type="text" name="bdMileageAmount"  placeholder="금액 입력"
                                                value="<?= gd_isset($data['bdMileageAmount']) ?>" maxlength="8"/>
                                        </div>
                                    </div>
                                </div>
                                원 지급
                            </div>
                            <div class="bdReplyMileageFl-area <?php if(gd_isset($checked['bdReplyFl']['n']) || $data['bdKind'] === 'event' || $data['bdKind'] === 'qa'){ echo 'display-none'; } ?>">
                                <label class="ncua-checkbox-field ncua-checkbox-field--xs has-text">
                                    <span class="ncua-checkbox-input ncua-checkbox-input--xs ncua-checkbox-field__input">
                                        <input type="checkbox" name="bdReplyMileageFl" value='y' <?= gd_isset($checked['bdReplyMileageFl']['y']) ?> />
                                    </span>
                                    <span>
                                        <span class="ncua-checkbox-field__text">답변글 작성 시에도 마일리지 지급</span>
                                    </span>
                                </label>
                            </div>
                        </div>
                    </td>
                </tr>
                <tr class="bdMileageFl-area">
                    <th><div data-tooltip-seq="009">게시글 삭제 시 <br/> 마일리지 차감</div></th>
                    <td>
                        <div>
                            <div class="ncua-switch ncua-switch--xs">
                                <label class="ncua-switch__option ncua-switch__option--left ncua-switch__option--active">
                                    <input class="ncua-switch__radio ncua-switch__radio--left" name="bdMileageDeleteFl" type="radio"
                                    value='y' <?= gd_isset($checked['bdMileageDeleteFl']['y']) ?> />
                                    <span class="ncua-switch__label">사용함</span>
                                </label>
                                <label class="ncua-switch__option ncua-switch__option--right ncua-switch__option--inactive">
                                    <input class="ncua-switch__radio ncua-switch__radio--right" name="bdMileageDeleteFl" type="radio"
                                    value='n' <?= gd_isset($checked['bdMileageDeleteFl']['n']) ?> />
                                    <span class="ncua-switch__label">사용안함</span>
                                </label>
                            </div>
                        </div>
                    </td>
                </tr>
                <tr class="bdMileageFl-area">
                    <th><div>차감 마일리지 <br/>부족 시 처리방법</div></th>
                    <td>
                        <div class="ncua-flex-gap">
                            <label class="ncua-radio-field ncua-radio-field--xs has-text">
                                <span class="ncua-radio-input ncua-radio-input--xs ncua-radio-field__input"><input type="radio" name="bdMileageLackAction" value="delete" <?= gd_isset($checked['bdMileageLackAction']['delete']) ?> /></span>
                                <span>
                                    <span class="ncua-radio-field__text">마이너스 차감 후 게시글 삭제</span>
                                </span>
                            </label>
                            <label class="ncua-radio-field ncua-radio-field--xs has-text">
                                <span class="ncua-radio-input ncua-radio-input--xs ncua-radio-field__input"><input type="radio" name="bdMileageLackAction" value="nodelete" <?= gd_isset($checked['bdMileageLackAction']['nodelete']) ?> /></span>
                                <span>
                                    <span class="ncua-radio-field__text">게시글 삭제 불가</span>
                                </span>
                            </label>
                        </div>
                    </td>
                </tr>
            </table>
        </div>
    </section>

</section>
