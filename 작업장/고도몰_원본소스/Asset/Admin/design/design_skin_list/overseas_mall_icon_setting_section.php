<section class="ncua-card overseas-mall-icon-setting-section max-width-center">
    <header class="ncua-card__header">
        <h4 class="ncua-card__title">해외몰 홈아이콘 관리</h4>

        <div class="ncua-card__header--right">
            <button type="button" class="ncua-btn ncua-btn--xxs ncua-btn--tertiary-gray view-all-button">
                <i class="view-all-icon chevron-down"></i>
            </button>
        </div>
    </header>
    
    <section class="ncua-card__body ncua-flex ncua-flex-column ncua-flex-gap">
        <form id="frmMallIcon" name="frmMallIcon" action="design_skin_list_ps.php" method="post" target="ifrmProcess" enctype="multipart/form-data">
            <input type="hidden" name="mode" value="mallIconConfig"/>

            <div class="ncua-table ncua-table--vertical">
                <table>
                    <colgroup>
                        <col width="240px"/>
                        <col/>
                    </colgroup>
                    <tbody>
                        <tr>
                            <th><div>홈아이콘 관리</div></th>
                            <td>
                                <div class="ncua-flex ncua-flex-column ncua-flex-gap overseas-mall-icon--table-container">
                                    <div class="ncua-table ncua-table--horizontal">
                                        <table>
                                            <thead>
                                                <tr>
                                                    <th><div>아이콘 종류</div></th>
                                                    <th><div>현재 적용 이미지</div></th>
                                                    <th width="240px"><div>이미지</div></th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php foreach ($mallListAll as $key => $mall) { ?>
                                                    <tr>
                                                        <td><div><?= $mall['mallName'] ?></div></td>
                                                        <td>
                                                            <div>
                                                                <img src="<?= $uriCommon . '/' . $mallIcon[$key] ?>" />
                                                            </div>
                                                        </td>
                                                        <td>
                                                            <div class="mall-icon" id="mall_icon_<?= $mall['domainFl'] ?>" data-mall-key="<?= $key ?>"></div>
                                                            <input type="file" name="mallIcon[<?= $key ?>]" class="no-filestyle display-none" />
                                                            <input type="hidden" name="mallDomainFl[<?= $key ?>]" value="<?= $mall['domainFl'] ?>" />
                                                            <div id="mall_icon_<?= $mall['domainFl'] ?>_container" class="mall-icon-tag-container is-display-none"></div>
                                                        </td>
                                                    </tr>
                                                <?php } ?>
                                            </tbody>
                                        </table>
                                    </div>
                                    <div>
                                        <ul>
                                            <li class="ncua-notice-info">권장 이미지 사이즈 : 22 px x 17 px</li>
                                            <li class="ncua-notice-info">권장 이미지 용량 : 500kb이하</li>
                                            <li class="ncua-notice-info">권장 확장자 : png</li>
                                            <li class="ncua-notice-info">기존 제공되는 아이콘은 삭제 시 복원되지 않습니다.</li>
                                        </ul>
                                    </div>
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <th><div>PC 스킨 홈아이콘 유형</div></th>
                            <td>
                                <div class="ncua-flex-gap">
                                    <div class="ncua-flex ncua-flex-column overseas-mall-icon--type--item">
                                        <label class="ncua-radio-field ncua-radio-field--xs has-text">
                                            <span class="ncua-radio-input ncua-radio-input--xs ncua-radio-field__input">
                                                <input type="radio" name="iconType" value="check" <?= $checked['iconType']['check'] ?>/>
                                            </span>
                                            <span class="ncua-radio-field__text">아이콘만 나란히 노출</span>
                                        </label>
                                        <img src="<?= PATH_ADMIN_GD_SHARE ?>ncds/image/language_image_01.svg" />
                                    </div>

                                    <div class="ncua-flex ncua-flex-column overseas-mall-icon--type--item">
                                        <label class="ncua-radio-field ncua-radio-field--xs has-text">
                                            <span class="ncua-radio-input ncua-radio-input--xs ncua-radio-field__input">
                                                <input type="radio" name="iconType" value="select_flag" <?= $checked['iconType']['select_flag'] ?>>
                                            </span>
                                            <span class="ncua-radio-field__text">아이콘만 드롭박스로 노출</span>
                                        </label>
                                        <img src="<?= PATH_ADMIN_GD_SHARE ?>ncds/image/language_image_02.svg" />
                                    </div>

                                    <div class="ncua-flex ncua-flex-column overseas-mall-icon--type--item">
                                        <label class="ncua-radio-field ncua-radio-field--xs has-text">
                                            <span class="ncua-radio-input ncua-radio-input--xs ncua-radio-field__input">
                                            <input type="radio" name="iconType" value="select_language" <?= $checked['iconType']['select_language'] ?>>
                                            </span>
                                            <span class="ncua-radio-field__text">아이콘+텍스트를 드롭박스로 노출</span>
                                        </label>
                                        <img src="<?= PATH_ADMIN_GD_SHARE ?>ncds/image/language_image_03.svg" />
                                    </div>
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <th><div>모바일 스킨 홈아이콘 유형</div></th>
                            <td>
                                <div class="ncua-flex-gap">
                                    <div class="ncua-flex ncua-flex-column overseas-mall-icon--type--item">
                                        <label class="ncua-radio-field ncua-radio-field--xs has-text">
                                            <span class="ncua-radio-input ncua-radio-input--xs ncua-radio-field__input">
                                                <input type="radio" name="iconTypeMobile" value="check" <?= $checked['iconTypeMobile']['check'] ?>/>
                                            </span>
                                            <span class="ncua-radio-field__text">아이콘만 나란히 노출</span>
                                        </label>
                                        <img src="<?= PATH_ADMIN_GD_SHARE ?>ncds/image/language_image_01.svg" />
                                    </div>

                                    <div class="ncua-flex ncua-flex-column overseas-mall-icon--type--item">
                                        <label class="ncua-radio-field ncua-radio-field--xs has-text">
                                            <span class="ncua-radio-input ncua-radio-input--xs ncua-radio-field__input">
                                                <input type="radio" name="iconTypeMobile" value="select_flag" <?= $checked['iconTypeMobile']['select_flag'] ?>/>
                                            </span>
                                            <span class="ncua-radio-field__text">아이콘만 드롭박스로 노출</span>
                                        </label>
                                        <img src="<?= PATH_ADMIN_GD_SHARE ?>ncds/image/language_image_02.svg" />
                                    </div>

                                    <div class="ncua-flex ncua-flex-column overseas-mall-icon--type--item">
                                        <label class="ncua-radio-field ncua-radio-field--xs has-text">
                                            <span class="ncua-radio-input ncua-radio-input--xs ncua-radio-field__input">
                                                <input type="radio" name="iconTypeMobile" value="select_language" <?= $checked['iconTypeMobile']['select_language'] ?>/>
                                            </span>
                                            <span class="ncua-radio-field__text">아이콘+텍스트를 드롭박스로 노출</span>
                                        </label>
                                        <img src="<?= PATH_ADMIN_GD_SHARE ?>ncds/image/language_image_03.svg" />
                                    </div>
                                </div>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </form>
        <div class="ncua-notice-info notice--error">
            반응형 스킨의 해외몰 홈아이콘 유형은 디자인 에디터로 편집하기를 통해 변경하실 수 있습니다.
        </div>
    </section>
</section>
