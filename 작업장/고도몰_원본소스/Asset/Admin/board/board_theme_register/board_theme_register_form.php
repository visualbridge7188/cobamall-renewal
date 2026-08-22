<section class="ncua-card">
    <header class="ncua-card__header">
        <h4 class="ncua-card__title">기본 정보</h4>
        <p class="ncua-card__sub-info"><strong>*는 필수 입력</strong> 항목입니다.</p>
    </header>

    <section class="ncua-card__body">
        <div class="ncua-table ncua-table--vertical">
            <table>
                <?php if ($gGlobal['isUse']) { ?>
                    <tr>
                        <th class="ncua-required"><div data-tooltip-seq="002">적용 디자인스킨</div></th>
                        <td>
                            <div class="ncua-flex-gap">
                            <?php if ($data['mode'] == 'theme_modify') { ?>
                                <b><?= $data['liveSkinName'] ?>(<?= $data['liveSkin'] ?>)</b>
                            <?php } else { ?>
                                <?php foreach ($gGlobal['useMallList'] as $val) {
                                    ?>
                                    <label class="ncua-radio-field ncua-radio-field--xs has-text">
                                        <span class="ncua-radio-input ncua-radio-input--xs ncua-radio-field__input">
                                            <input type="radio" name="liveSkin"
                                                                    value="<?= $val['skin']['frontLive'] . STR_DIVISION . $val['skin']['mobileLive'] ?>">
                                        </span>
                                        <span class="ncua-radio-field__text"><?= $val['mallName'] ?></span>
                                    </label>
                                <?php } ?>
                            <?php } ?>
                            </div>
                        </td>
                    </tr>
                <?php } ?>
                <tr>
                    <th><div>구분</div></th>
                    <td>
                        <div class="ncua-flex-gap">
                        <?php if ($data['mode'] == 'theme_register') { ?>
                            <label class="ncua-radio-field ncua-radio-field--xs has-text">
                                <span class="ncua-radio-input ncua-radio-input--xs ncua-radio-field__input">
                                    <input type="radio" name="bdMobileFl" value="n" <?= $checked['bdMobileFl']['n'] ?>>
                                </span>
                                <span class="ncua-radio-field__text">PC쇼핑몰</span>
                            </label>
                            <label class="ncua-radio-field ncua-radio-field--xs has-text">
                                <span class="ncua-radio-input ncua-radio-input--xs ncua-radio-field__input">
                                    <input type="radio" name="bdMobileFl" value="y" <?= $checked['bdMobileFl']['y'] ?>>
                                </span>
                                <span class="ncua-radio-field__text">모바일쇼핑몰</span>
                            </label>
                        <?php } else { ?>
                            <b> <?= $data['deviceTypeText'] ?></b>
                            <input type="hidden" name="bdMobileFl" value="<?= $data['bdMobileFl'] ?>">
                        <?php } ?>
                        </div>
                    </td>
                </tr>
                <tr>
                    <th><div>유형</div></th>
                    <td>
                        <div class="ncua-item-align-start ncua-flex-column">
                        <?php
                        if ($data['mode'] == 'theme_register') {
                            $tmpChecked = 'checked="checked"'; ?>
                            <div class="ncua-flex-gap board-theme-register-select-category-list">
                            <?php foreach ($bdKind as $k => $v) {
                                ?>
                                <label class="ncua-radio-field ncua-radio-field--xs has-text ncua-radio-img">
                                    <span class="ncua-radio-input ncua-radio-input--xs ncua-radio-field__input">
                                        <input type="radio" name="bdKind" value="<?= $k; ?>" <?= $tmpChecked ?> />
                                    </span>
                                    <span class="ncua-radio-field__text"><?= $v ?></span>
                                    <img src="<?= PATH_ADMIN_GD_SHARE ?>img/board/type_<?= $k ?>.png">
                                </label>
                                <?php
                                $tmpChecked = '';
                            } ?>
                            </div>
                            <?php } else {
                            ?>
                            <input type="hidden" name="bdKind" value="<?= gd_isset($data['bdKind']); ?>"/>
                            <strong><?= gd_isset($bdKind[$data['bdKind']]); ?></strong>
                        <?php } ?>

                            <span id="bdKind_msg" class="input_error_msg"></span>
                        </div>
                    </td>
                </tr>
                <tr>
                    <th class="ncua-required"><div data-tooltip-seq="001">스킨코드</div></th>
                    <td>
                        <div class="ncua-flex-column ncua-item-align-start">
                            <input type="hidden" name="chkThemeId" id="chkThemeId" value="<?= $data['themeId']; ?>"/>
                            <?php if ($data['mode'] == 'theme_register') { ?>
                                <div class="board-theme-register-skin-code-field">
                                    <div class="ncua-input ncua-input--xs ncua-input-width-520">
                                        <div class="ncua-input__field ncua-input__field--xs">
                                            <input type="text" name="themeId" id="themeId" value="" style="ime-mode:disabled; text-transform:lowercase;" placeholder="영문,숫자로 2~30자 입력하세요." />
                                        </div>
                                    </div>
                                    <button type="button" class="ncua-btn ncua-btn--xs ncua-btn--secondary" id="overlap_themeId">중복확인</button>
                                </div>
                                <span id="themeId_msg" class="input_error_msg"></span>
                            <?php } else { ?>
                                <strong><?= gd_isset($data['themeId']); ?></strong>
                                <input type="hidden" name="themeId" value="<?= $data['themeId'] ?>">
                            <?php } ?>
                        </div>
                    </td>
                </tr>
                <tr>
                    <th class="ncua-required"><div>게시판 스킨명</div></th>
                    <td>
                        <div class="ncua-flex-column ncua-item-align-start">
                            <div class="ncua-input ncua-input--xs ncua-input-width-520">
                                <div class="ncua-input__field ncua-input__field--xs">
                                    <input type="text" name="themeNm" value="<?= $data['themeNm']; ?>" placeholder="한글, 영문 대소문자, 숫자 최대 100자 입력 가능" />
                                </div>
                            </div>
                            <span id="themeNm_msg" class="input_error_msg"></span>
                        </div>
                    </td>
                </tr>
                <tr class="js-mobile-hide">
                    <th><div>게시판 위치</div></th>
                    <td>
                        <div class="ncua-flex-gap">
                            <label class="ncua-radio-field ncua-radio-field--xs has-text ncua-radio-img">
                                <span class="ncua-radio-input ncua-radio-input--xs ncua-radio-field__input">
                                    <input type="radio" name="bdAlign" value="left" <?= $checked['bdAlign']['left'] ?>>
                                </span>
                                <span class="ncua-radio-field__text">좌측 정렬</span>
                                <img src="<?= PATH_ADMIN_GD_SHARE ?>img/board/align_left.png">
                            </label>
                            <label class="ncua-radio-field ncua-radio-field--xs has-text ncua-radio-img">
                                <span class="ncua-radio-input ncua-radio-input--xs ncua-radio-field__input">
                                    <input type="radio" name="bdAlign" value="center" <?= $checked['bdAlign']['center'] ?>>
                                </span>
                                <span class="ncua-radio-field__text">센터 정렬</span>
                                <img src="<?= PATH_ADMIN_GD_SHARE ?>img/board/align_center.png">
                            </label>
                            <label class="ncua-radio-field ncua-radio-field--xs has-text ncua-radio-img">
                                <span class="ncua-radio-input ncua-radio-input--xs ncua-radio-field__input">
                                    <input type="radio" name="bdAlign" value="right" <?= $checked['bdAlign']['right'] ?>>
                                </span>
                                <span class="ncua-radio-field__text">우측 정렬</span>
                                <img src="<?= PATH_ADMIN_GD_SHARE ?>img/board/align_right.png">
                            </label>
                        </div>
                    </td>
                </tr>
                <tr class="js-mobile-hide">
                    <th class="ncua-required"><div>게시판 넓이</div></th>
                    <td>
                        <div class="ncua-flex-gap">
                            <div class="board-theme-register-bd-width-field">
                                <div class="ncua-input ncua-input--xs ncua-input-width-120">
                                    <div class="ncua-input__field ncua-input__field--xs">
                                        <input type="text" name="bdWidth" value="<?= gd_isset($data['bdWidth']) ?>" class="js-number" />
                                    </div>
                                </div>
                                <span class="ncua-select ncua-select--xs">
                                    <span class="ncua-select__content">
                                    <?= gd_select_box('bdWidthUnit', 'bdWidthUnit', array('%' => '%', 'px' => 'px'), null, gd_isset($data['bdWidthUnit']), null, null, 'ncua-select__tag'); ?>
                                    </span>
                                </span>
                            </div>
                            <img src="<?= PATH_ADMIN_GD_SHARE ?>/img/board/board-width.png">
                        </div>
                    </td>
                </tr>
                <tr class="js-pc-show" style="display:none">
                    <th><div>pc아이콘 관리</div></th>
                    <td>
                        <div>
                            <div class="ncua-table ncua-table--horizontal board-theme-register-icon-table">
                                <table>
                                    <colgroup>
                                        <col style="width: 10%;" />
                                        <col style="width: 10%;" />
                                        <col style="width: 25%;" />
                                        <col/>
                                    </colgroup>
                                    <thead>
                                        <tr>
                                            <th><div>아이콘 종류</div></th>
                                            <th><div>아이콘 미리보기</div></th>
                                            <th><div>아이콘 등록</div></th>
                                            <th><div>아이콘 예시 화면</div></th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                    <?php
                                    $i = 0;
                                    foreach ($iconTypeList as $key => $val) {
                                        ?>
                                        <tr>
                                            <td><div><?= $key ?></div></td>
                                            <td>
                                                <div class="board-theme-icon-preview ncua-flex-column">
                                                    <img src='<?= $data['iconImage'][$val]['url'] ?>'/>
                                                <?php if ($data['mode'] == 'theme_modify' && $data['iconImage'][$val]['userModify'] == true) { ?>
                                                    <button type="button" class="ncua-btn ncua-btn--xs ncua-btn--secondary-gray js-ncua-icon-modify-delete" onclick="deleteIcon('<?= $data['themeId'] ?>','<?= $val ?>','pc')">
                                                        삭제
                                                    </button>
                                                <?php } ?>
                                                </div>
                                            </td>
                                            <td>
                                                <div class="board-theme-register-icon-upload-wrapper">
                                                    <input type="file" hidden name="boardIcon[<?= $val ?>]" value="">
                                                    <div id="boardIcon<?= $val ?>"></div>
                                                </div>
                                            </td>
                                            <?php if ($i == 0) { ?>
                                                <td rowspan="7">
                                                    <div>
                                                        <img src="<?= PATH_ADMIN_GD_SHARE ?>img/board/board_sample.png" />
                                                    </div>
                                                </td>
                                            <?php } ?>
                                        </tr>
                                        <?php
                                        $i++;
                                    } ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </td>
                </tr>
                <tr class="js-mobile-show" style="display:none">
                    <th><div>모바일아이콘 관리</div></th>
                    <td>
                        <div>
                            <div class="ncua-table ncua-table--horizontal board-theme-register-icon-table">
                                <table>
                                    <colgroup>
                                        <col style="width: 10%;" />
                                        <col />
                                        <col style="width: 25%;" />
                                        <col/>
                                    </colgroup>
                                    <thead>
                                        <tr>
                                            <th><div>아이콘 종류</div></th>
                                            <th><div>아이콘 미리보기</div></th>
                                            <th><div>아이콘 등록</div></th>
                                            <th><div>아이콘 예시 화면</div></th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                    <?php
                                    $i = 0;
                                    foreach ($iconTypeList as $key => $val) {
                                        ?>
                                        <tr>
                                            <td><div><?= $key ?></div></td>
                                            <td>
                                                <div class="board-theme-icon-preview ncua-flex-column">
                                                    <img src='<?= $data['mobileIconImage'][$val]['url'] ?>'/>
                                                    <?php if ($data['mode'] == 'theme_modify' && $data['mobileIconImage'][$val]['userModify'] == true) { ?>
                                                        <button type="button" class="ncua-btn ncua-btn--xxs ncua-btn--secondary-gray"
                                                                onclick="deleteIcon('<?= $data['themeId'] ?>','<?= $val ?>','mobile')">
                                                            삭제
                                                        </button>
                                                    <?php } ?>
                                                </div>
                                            </td>
                                            <td>
                                                <div class="board-theme-register-icon-upload-wrapper">
                                                    <input type="file" hidden name="boardIconMobile[<?= $val ?>]" value="">
                                                    <div id="boardIconMobile<?= $val ?>"></div>
                                                    <div class="pull-left mgl10">
                                                    </div>
                                                </div>
                                            </td>
                                            <?php if ($i == 0) { ?>
                                                <td rowspan="7">
                                                    <div>
                                                        <img src="<?= PATH_ADMIN_GD_SHARE ?>img/board/board_sample.png" />
                                                    </div>
                                                </td>
                                            <?php } ?>
                                        </tr>
                                        <?php
                                        $i++;
                                    } ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </td>
                </tr>
                <tr class="js-mobile-hide">
                    <th class="ncua-required"><div>게시글 줄높이</div></th>
                    <td>
                        <div class="board-theme-register-line-height-field ncua-flex-gap">
                            <div class="ncua-input ncua-input--xs">
                                <div class="ncua-input__field ncua-input__field--xs ncua-input-width-120">
                                    <input type="text" name="bdListLineSpacing" value="<?= gd_isset($data['bdListLineSpacing']) ?>" class="js-number"/>
                                </div>
                                px
                            </div>
                            <img src="<?= PATH_ADMIN_GD_SHARE ?>img/board/board_line-height.png" />
                        </div>
                    </td>
                </tr>
                <?php if ($data['mode'] == 'theme_modify' && !$gGlobal['isUse']) { ?>
                    <tr>
                        <th><div>디자인 수정</div></th>
                        <td class="board-theme-register-design-modify-table">
                            <div class="ncua-flex-column ncua-item-align-start">
                                <div class="ncua-table ncua-table--horizontal">
                                    <table>
                                        <thead>
                                        <tr>
                                            <th><div class="ncua-align-center">목록 화면</div></th>
                                            <th><div class="ncua-align-center">상세보기 화면</div></th>
                                            <th><div class="ncua-align-center">작성 화면</div></th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                        <tr>
                                            <td>
                                                <div class="ncua-flex-column">
                                                    <p>게시판{board}/skin/{스킨코드}/list.html</p>
                                                    <button type="button" class="ncua-btn ncua-btn--xs ncua-btn--secondary-gray" onclick="skin_modify_link('list')">바로가기</button>
                                                </div>
                                            </td>
                                            <td>
                                                <div class="ncua-flex-column">
                                                    <p>게시판{board}/skin/{스킨코드}/view.html</p>
                                                    <button type="button" class="ncua-btn ncua-btn--xs ncua-btn--secondary-gray" onclick="skin_modify_link('view')">바로가기</button>
                                                </div>
                                            </td>
                                            <td>
                                                <div class="ncua-flex-column">
                                                    <p>게시판{board}/skin/{스킨코드}/write.html</p>
                                                    <button type="button" class="ncua-btn ncua-btn--xs ncua-btn--secondary-gray" onclick="skin_modify_link('write')">바로가기</button>
                                                </div>
                                            </td>
                                        </tr>
                                        </tbody>
                                    </table>
                                </div>
                                <p class="notice-danger">※ 현재 작업 중인 쇼핑몰 디자인 스킨에 게시판(board)/skin/(스킨코드)/ 안에 목록(list.htm), 상세보기(view.htm), 작성(write.htm) 화면으로
                                    이동하여 개별 디자인 수정이 가능합니다.
                                </p>
                            </div>
                        </td>
                    </tr>
                <?php } ?>
            </table>
        </div>
    </section>
</section>

