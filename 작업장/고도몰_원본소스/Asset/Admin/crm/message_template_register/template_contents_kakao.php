<?php
$kakaoCloud = $response->getCloud();
$kakaoBizm = $response->getBizm();
?>
<section class="ncua-card">
    <header class="ncua-card__header">
        <h4 class="ncua-card__title">템플릿 내용</h4>
    </header>
    <section class="ncua-card__body">
        <!-- 템플릿 타입 라디오 버튼 -->
        <?php
        $templateTypes = [
            ['value' => 'NONE', 'label' => '메시지형', 'tabTarget' => 'templateTypeMessage'],
            ['value' => 'TEXT', 'label' => '강조표기형', 'tabTarget' => 'templateTypeEmphasis'],
            ['value' => 'IMAGE', 'label' => '이미지형', 'tabTarget' => 'templateTypeImage'],
            ['value' => 'ITEM_LIST', 'label' => '아이템리스트형', 'tabTarget' => 'templateTypeItemList'],
        ];
        $selectedTemplateType = ($kakaoCloud?->getTemplateEmphasizeType() ?? $kakaoBizm?->getTemplateEmphasizeType()) ?: 'NONE';
        ?>
        <div class="ncua-flex ncua-flex-gap template-type-content" data-component="template-type-radio" data-show-when='{"provider": ["kakaoAlrimCloud", "cloud"]}'>
            <?php foreach ($templateTypes as $index => $type): ?>
            <label class="ncua-radio-field ncua-radio-field--xs has-text">
                <span class="ncua-radio-input ncua-radio-input--xs ncua-radio-field__input">
                    <input name="templateType" type="radio" value="<?= $type['value'] ?>" <?= $selectedTemplateType === $type['value'] ? 'checked="checked"' : '' ?> data-tab-target="<?= $type['tabTarget'] ?>" />
                </span>
                <span>
                    <span class="ncua-radio-field__text"><?= $type['label'] ?></span>
                </span>
            </label>
            <?php endforeach; ?>
        </div>

        <!-- 상단 이미지 -->
        <div class="top-image-content" data-component="top-image-content" data-show-when='{"provider": ["kakaoAlrimCloud", "cloud"], "templateType": ["IMAGE", "ITEM_LIST"]}'>
            <div class="ncua-card__body-title-wrap ncua-required">
                <p class="ncua-card__body-title--xs">상단 이미지</p>
            </div>
            <input name="templateTopImage" data-preview-image="templateTopImageInput" id="templateTopImageInput" tabindex="-1" aria-hidden="true" type="file" />
            <div id="top-image-file-container"></div>
        </div>

        <!-- 헤더 -->
        <div class="header-content" data-component="header-content" data-show-when='{"provider": ["kakaoAlrimCloud", "cloud"], "templateType": ["ITEM_LIST"]}'>
            <div class="ncua-card__body-title-wrap ncua-required">
                <p class="ncua-card__body-title--xs">헤더</p>
            </div>
            <div class="ncua-input ncua-input--xs ncua-input--full-width-with-count" data-show-hint-text="true">
                <div class="ncua-input__content-wrap">
                    <div class="ncua-input__field ncua-input__field--xs">
                        <input class="template-header-input" data-charcount-key="templateHeader" maxlength="16" name="templateHeader" type="text" value="<?= htmlspecialchars($kakaoCloud?->getTemplateHeader() ?? '') ?>" placeholder="치환코드 포함 최대 16자 이내로 작성해 주세요." />
                    </div>
                    <div class="ncua-input__field-text-count" data-charcount-text="templateHeader">
                        <output class="ncua-input__field-text-count-current"><?= mb_strlen($kakaoCloud?->getTemplateHeader() ?? '') ?></output><span>/16</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- 아이템 하이라이트 이미지 -->
        <div class="item-highlight-image-content" data-component="item-highlight-image-content" data-show-when='{"provider": ["kakaoAlrimCloud", "cloud"], "templateType": ["ITEM_LIST"]}'>
            <div class="ncua-card__body-title-wrap">
                <p class="ncua-card__body-title--xs">아이템 하이라이트 이미지</p>
            </div>
            <input name="templateItemHighlightImage" id="templateItemHighlightImageInput" class="template-item-highlight-image-input" tabindex="-1" aria-hidden="true" type="file" />
            <div id="item-highlight-image-file-container"></div>
        </div>

        <!-- 아이템 하이라이트 제목 -->
        <?php $itemHighlight = $kakaoCloud?->getTemplateItemHighlight(); ?>
        <div class="item-highlight-title-content" data-component="item-highlight-title-content" data-show-when='{"provider": ["kakaoAlrimCloud", "cloud"], "templateType": ["ITEM_LIST"]}'>
            <div class="ncua-card__body-title-wrap ncua-required">
                <p class="ncua-card__body-title--xs">아이템 하이라이트 제목</p>
            </div>
            <div class="ncua-input ncua-input--xs ncua-input--full-width-with-count" data-show-hint-text="true">
                <div class="ncua-input__content-wrap">
                    <div class="ncua-input__field ncua-input__field--xs">
                        <input class="item-highlight-title-input" data-charcount-key="templateItemHighlightTitle" maxlength="30" name="templateItemHighlightTitle" type="text" value="<?= htmlspecialchars($itemHighlight['title'] ?? '') ?>" placeholder="30자 이내, 이미지가 있을 경우 21자 이내로 작성해 주세요." />
                    </div>
                    <div class="ncua-input__field-text-count" data-charcount-text="templateItemHighlightTitle">
                        <output class="ncua-input__field-text-count-current"><?= mb_strlen($itemHighlight['title'] ?? '') ?></output><span>/30</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- 아이템 하이라이트 내용 -->
        <div class="item-highlight-description-content" data-component="item-highlight-description-content" data-show-when='{"provider": ["kakaoAlrimCloud", "cloud"], "templateType": ["ITEM_LIST"]}'>
            <div class="ncua-card__body-title-wrap ncua-required">
                <p class="ncua-card__body-title--xs">아이템 하이라이트 내용</p>
            </div>
            <div class="ncua-input ncua-input--xs ncua-input--full-width-with-count" data-show-hint-text="true">
                <div class="ncua-input__content-wrap">
                    <div class="ncua-input__content">
                        <div class="ncua-input__field ncua-input__field--xs">
                            <input class="item-highlight-description-input" data-charcount-key="templateItemHighlightDescription" maxlength="19" name="templateItemHighlightDescription" type="text" value="<?= htmlspecialchars($itemHighlight['description'] ?? '') ?>" placeholder="19자 이내, 이미지가 있을 경우 13자 이내로 작성해 주세요." />
                        </div>

                    </div>
                    <div class="ncua-input__field-text-count" data-charcount-text="templateItemHighlightDescription">
                        <output class="ncua-input__field-text-count-current"><?= mb_strlen($itemHighlight['description'] ?? '') ?></output><span>/19</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- 아이템 리스트 -->
        <div class="item-list-content" data-component="item-list-content" data-show-when='{"provider": ["kakaoAlrimCloud", "cloud"], "templateType": ["ITEM_LIST"]}'>
            <div class="ncua-card__body-title-wrap ncua-required">
                <p class="ncua-card__body-title--xs">아이템 리스트</p>
                <div>
                    <button type='button' class="ncua-btn ncua-btn--xs ncua-btn--secondary-gray add-item-btn">+ 아이템 추가</button>
                </div>
            </div>
            <div class="ncua-table ncua-table--horizontal">
                <table>
                    <colgroup>
                        <col width="80px" />
                        <col />
                        <col />
                        <col width="90px" />
                    </colgroup>
                    <thead>
                        <tr>
                            <th><div class="ncua-align-center">NO</div></th>
                            <th><div>제목</div></th>
                            <th><div>내용</div></th>
                            <th><div class="ncua-align-center">관리</div></th>
                        </tr>
                    </thead>
                    <tbody data-component="item-list-tbody">
                        <tr>
                            <td>
                                <div class="ncua-align-center ncua-text-align-center">1</div>
                            </td>
                            <td>
                                <div>
                                    <div class="ncua-input ncua-input--xs ncua-input--full-width-with-count" data-show-hint-text="true">
                                        <div class="ncua-input__content-wrap">
                                            <div class="ncua-input__content">
                                                <div class="ncua-input__field ncua-input__field--xs">
                                                    <input class="item-list-title-input" data-charcount-key="templateItemListTitle1" maxlength="6" name="templateItemListTitle1" type="text" value="" placeholder="6자 이내로 작성해 주세요." />
                                                </div>
                                            </div>
                                            <div class="ncua-input__field-text-count" data-charcount-text="templateItemListTitle1">
                                                <output class="ncua-input__field-text-count-current">0</output><span>/6</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <div>
                                    <div class="ncua-input ncua-input--xs ncua-input--full-width-with-count" data-show-hint-text="true">
                                        <div class="ncua-input__content-wrap">
                                            <div class="ncua-input__content">
                                                <div class="ncua-input__field ncua-input__field--xs">
                                                    <input class="item-list-content-input" data-charcount-key="templateItemListContent1" maxlength="23" name="templateItemListContent1" type="text" value="" placeholder="치환 코드 포함 최대 23자 이내로 작성해 주세요." />
                                                </div>
                                            </div>
                                            <div class="ncua-input__field-text-count" data-charcount-text="templateItemListContent1">
                                                <output class="ncua-input__field-text-count-current">0</output><span>/23</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <div class="ncua-align-center">
                                    <button type="button" class="ncua-btn ncua-btn--xs ncua-btn--secondary-gray remove-item-btn">- 삭제</button>
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <div class="ncua-align-center ncua-text-align-center">2</div>
                            </td>
                            <td>
                                <div>
                                    <div class="ncua-input ncua-input--xs ncua-input--full-width-with-count" data-show-hint-text="true">
                                        <div class="ncua-input__content-wrap">
                                            <div class="ncua-input__content">
                                                <div class="ncua-input__field ncua-input__field--xs">
                                                    <input class="item-list-title-input" data-charcount-key="templateItemListTitle2" maxlength="6" name="templateItemListTitle2" type="text" value="" placeholder="6자 이내로 작성해 주세요." />
                                                </div>
                                            </div>
                                            <div class="ncua-input__field-text-count" data-charcount-text="templateItemListTitle2">
                                                <output class="ncua-input__field-text-count-current">0</output><span>/6</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <div>
                                    <div class="ncua-input ncua-input--xs ncua-input--full-width-with-count" data-show-hint-text="true">
                                        <div class="ncua-input__content-wrap">
                                            <div class="ncua-input__content">
                                                <div class="ncua-input__field ncua-input__field--xs">
                                                    <input class="item-list-content-input" data-charcount-key="templateItemListContent2" maxlength="23" name="templateItemListContent2" type="text" value="" placeholder="치환 코드 포함 최대 23자 이내로 작성해 주세요." />
                                                </div>
                                            </div>
                                            <div class="ncua-input__field-text-count" data-charcount-text="templateItemListContent2">
                                                <output class="ncua-input__field-text-count-current">0</output><span>/23</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <div class="ncua-align-center">
                                    <button type="button" class="ncua-btn ncua-btn--xs ncua-btn--secondary-gray remove-item-btn">- 삭제</button>
                                </div>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- 아이템 요약 -->
        <?php $itemSummary = $kakaoCloud?->getTemplateItemSummary(); ?>
        <div class="item-summary-content" data-component="item-summary-content" data-show-when='{"provider": ["kakaoAlrimCloud", "cloud"], "templateType": ["ITEM_LIST"]}'>
            <div class="ncua-card__body-title-wrap">
                <p class="ncua-card__body-title--xs">아이템 요약</p>
            </div>
            <div class="ncua-table ncua-table--horizontal">
                <table>
                    <colgroup>
                        <col />
                        <col />
                    </colgroup>
                    <thead>
                        <tr>
                            <th><div>제목</div></th>
                            <th><div>내용</div></th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>
                                <div>
                                    <div class="ncua-input ncua-input--xs ncua-input--full-width-with-count">
                                        <div class="ncua-input__content-wrap">
                                            <div class="ncua-input__content">
                                                <div class="ncua-input__field ncua-input__field--xs">
                                                    <input class="item-summary-title-input" data-charcount-key="templateItemSummaryTitle" maxlength="6" name="templateItemSummaryTitle" type="text" value="<?= htmlspecialchars($itemSummary['title'] ?? '') ?>" placeholder="6자 이내로 작성해 주세요." />
                                                </div>
                                            </div>
                                            <div class="ncua-input__field-text-count" data-charcount-text="templateItemSummaryTitle">
                                                <output class="ncua-input__field-text-count-current"><?= mb_strlen($itemSummary['title'] ?? '') ?></output><span>/6</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <div>
                                    <div class="ncua-input ncua-input--xs ncua-input--full-width-with-count">
                                        <div class="ncua-input__content-wrap">
                                            <div class="ncua-input__content">
                                                <div class="ncua-input__field ncua-input__field--xs">
                                                    <input class="item-summary-content-input" data-charcount-key="templateItemSummaryContent" maxlength="14" name="templateItemSummaryContent" type="text" value="<?= htmlspecialchars($itemSummary['description'] ?? '') ?>" placeholder="유니코드 화폐 기호, 숫자, 쉼표, 마침표만 사용하여 14자 이내로 작성." />
                                                </div>
                                            </div>
                                            <div class="ncua-input__field-text-count" data-charcount-text="templateItemSummaryContent">
                                                <output class="ncua-input__field-text-count-current"><?= mb_strlen($itemSummary['description'] ?? '') ?></output><span>/14</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- 제목 -->
        <div class="title-content" data-component="title-content" data-show-when='{"provider": ["kakaoAlrimCloud", "cloud"], "templateType": ["TEXT"]}'>
            <div class="ncua-card__body-title-wrap ncua-required">
                <p class="ncua-card__body-title--xs">제목</p>
            </div>
            <div class="ncua-input ncua-input--xs ncua-input--full-width-with-count" data-show-hint-text="true">
                <div class="ncua-input__content-wrap">
                    <div class="ncua-input__content">
                        <div class="ncua-input__field ncua-input__field--xs">
                            <input class="template-type-title" data-charcount-key="templateTypeTitle" maxlength="23" name="templateTypeTitle" type="text" value="<?= htmlspecialchars($kakaoCloud?->getTemplateTitle() ?? '') ?>" placeholder="제목으로 강조할 내용을 작성해 주세요." />
                        </div>
                    </div>
                    <div class="ncua-input__field-text-count" data-charcount-text="templateTypeTitle">
                        <output class="ncua-input__field-text-count-current"><?= mb_strlen($kakaoCloud?->getTemplateTitle() ?? '') ?></output><span>/23</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- 부제목 -->
        <div class="subtitle-content" data-component="subtitle-content" data-show-when='{"provider": ["kakaoAlrimCloud", "cloud"], "templateType": ["TEXT"]}'>
            <div class="ncua-card__body-title-wrap ncua-required">
                <p class="ncua-card__body-title--xs">부제목</p>
            </div>
            <div class="ncua-input ncua-input--xs ncua-input--full-width-with-count" data-show-hint-text="true">
                <div class="ncua-input__content-wrap">
                    <div class="ncua-input__content">
                        <div class="ncua-input__field ncua-input__field--xs">
                            <input class="template-type-sub-title" data-charcount-key="templateTypeSubTitle" maxlength="18" name="templateTypeSubTitle" type="text" value="<?= htmlspecialchars($kakaoCloud?->getTemplateSubtitle() ?? '') ?>" placeholder="제목에 대한 부연 설명을 작성해 주세요." />
                        </div>
                    </div>
                    <div class="ncua-input__field-text-count" data-charcount-text="templateTypeSubTitle">
                        <output class="ncua-input__field-text-count-current"><?= mb_strlen($kakaoCloud?->getTemplateSubtitle() ?? '') ?></output><span>/18</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- 본문 입력 -->
        <div class="body-content" data-component="body-content" data-show-when='{"templateType": [null,"NONE", "TEXT", "IMAGE", "ITEM_LIST"]}'>
            <div class="ncua-card__body-title-wrap ncua-required">
                <p class="ncua-card__body-title--xs">본문 입력</p>
            </div>
            <div id="kakao-message-input-container" class="message-input-container"></div>
        </div>

        <div id="kakao-variable-selector-container" class="variable-selector-container" data-component="variable-selector"></div>

        <!-- 부가 정보 문구 -->
        <?php $hasExtra = !empty($kakaoCloud?->getTemplateExtra() ?? $kakaoBizm?->getTemplateExtra())
            || ($request->getMode() !== 'modify'); ?>
        <div class="message-content" data-component="message-content" data-show-when='{"provider": ["kakaoAlrimCloud", "cloud"], "templateType": ["NONE", "TEXT", "IMAGE", "ITEM_LIST"]}'>
            <div class="ncua-card__body-title-wrap ncua-card__body-title-wrap--inline">
                <div class="ncua-flex ncua-gap-8 align-items-center">
                    <p class="ncua-card__body-title--xs">부가 정보 문구</p>
                    <label class="ncua-checkbox-field ncua-checkbox-field--xs has-text">
                        <span class="ncua-checkbox-input ncua-checkbox-input--xs ncua-checkbox-field__input">
                            <input type="checkbox" id="extraInfoCheckbox" name="extraInfoCheckbox" <?= $hasExtra ? 'checked="checked"' : '' ?> />
                        </span>
                        <span class="ncua-checkbox-field__text">추가</span>
                    </label>
                </div>
            </div>
            <div class="ncua-input ncua-input--textarea ncua-input--textarea--xs">
                <textarea id="extraInfoTextarea" name="extraInfoTextarea" class="ncua-input__textarea body-content-textarea" data-charcount-key="templateExtra" placeholder="URL포함 200자 이내로 작성해 주세요." maxlength="200"><?= htmlspecialchars($kakaoCloud?->getTemplateExtra() ?? $kakaoBizm?->getTemplateExtra() ?? '') ?></textarea>
                <div class="ncua-input__text-count-wrap">
                    <div class="ncua-input__text-count">
                        <span class="ncua-input__text-count-text">
                            <span class="ncua-input__text-count-text-count" data-charcount-text="templateExtra"><?= mb_strlen($kakaoCloud?->getTemplateExtra() ?? $kakaoBizm?->getTemplateExtra() ?? '') ?></span>/200
                        </span>
                    </div>
                </div>
            </div>
        </div>

        <!-- 채널 추가 문구 -->
        <?php
        $messageType = $kakaoCloud?->getTemplateMessageType() ?? $kakaoBizm?->getTemplateMessageType();
        $hasChannelAdd = in_array($messageType, ['AD', 'MI', 'ADD_CHANNEL', 'MIX'])
            || ($request->getMode() !== 'modify');
        ?>
        <div class="channel-add-content" data-component="channel-add-content" data-show-when='{"provider": ["kakaoAlrimCloud", "cloud"], "templateType": ["NONE", "TEXT", "IMAGE", "ITEM_LIST"]}'>
            <div class="ncua-card__body-title-wrap ncua-card__body-title-wrap--inline">
                <div class="ncua-flex ncua-gap-8 align-items-center">
                    <p class="ncua-card__body-title--xs">채널 추가 문구(고정 내용)</p>
                    <label class="ncua-checkbox-field ncua-checkbox-field--xs has-text">
                        <span class="ncua-checkbox-input ncua-checkbox-input--xs ncua-checkbox-field__input">
                            <input type="checkbox" id="channelAddCheckbox" name="channelAddCheckbox" <?= $hasChannelAdd ? 'checked="checked"' : '' ?> />
                        </span>
                        <span class="ncua-checkbox-field__text">추가</span>
                    </label>
                </div>
            </div>
            <div class="ncua-input ncua-input--textarea ncua-input--textarea--xs">
                <textarea id="channelAddTextarea" name="channelAddTextarea" class="ncua-input__textarea body-content-textarea" disabled="disabled" data-charcount-key="channelExtra" placeholder="채널 추가하고 이 채널의 광고와 마케팅을 카카오톡으로 받기" maxlength="36"></textarea>
                <div class="ncua-input__text-count-wrap">
                    <div class="ncua-input__text-count">
                        <span class="ncua-input__text-count-text">
                            <span class="ncua-input__text-count-text-count" data-charcount-text="channelExtra">0</span>/36
                        </span>
                    </div>
                </div>
            </div>
        </div>

        <!-- 버튼 추가 -->
        <div class="action-buttons" data-component="action-buttons" data-show-when='{"templateType": [null, "NONE", "TEXT", "IMAGE", "ITEM_LIST"]}'>
            <div class="ncua-card__body-title-wrap">
                <p class="ncua-card__body-title--xs tooltip-align" data-tooltip-seq="005">버튼</p>
                <div class="ncua-flex ncua-gap-8">
                    <button type='button' id="addPageLinkButton" class="ncua-btn ncua-btn--xs ncua-btn--secondary-gray">+ 페이지 링크 추가</button>
                    <button type='button' id="addDeliveryButton" class="ncua-btn ncua-btn--xs ncua-btn--secondary-gray">+ 배송 조회 추가</button>
                </div>
            </div>
            <div class="ncua-table ncua-table--horizontal">
                <table>
                    <colgroup>
                        <col width="80px" />
                        <col />
                        <col />
                        <col width="90px" />
                    </colgroup>
                    <thead>
                        <tr>
                            <th><div class="ncua-align-center">타입</div></th>
                            <th class="ncua-required"><div>버튼명</div></th>
                            <th class="ncua-required"><div>URL</div></th>
                            <th><div class="ncua-align-center">관리</div></th>
                        </tr>
                    </thead>
                    <tbody id="pageLinkTableBody">
                        <tr class="empty-row">
                            <td colspan="4">
                                <div class="ncua-align-center">추가된 버튼이 없습니다.</div>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <div class="ncua-table ncua-table--horizontal">
                <table>
                    <colgroup>
                        <col width="80px" />
                        <col />
                        <col />
                        <col width="90px" />
                    </colgroup>
                    <thead>
                        <tr>
                            <th><div class="ncua-align-center">타입</div></th>
                            <th class="ncua-required"><div>버튼명</div></th>
                            <th class="ncua-required"><div>URL</div></th>
                            <th><div class="ncua-align-center">관리</div></th>
                        </tr>
                    </thead>
                    <tbody id="deliveryTableBody">
                        <tr class="empty-row">
                            <td colspan="4">
                                <div class="ncua-align-center">추가된 버튼이 없습니다.</div>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- 알림톡 템플릿 안내 -->
        <details class="ncua-accordion ncua-accordion--gray" data-component="kakao-notification-notice" open>
            <summary>알림톡 템플릿 안내</summary>
            <div class="ncua-accordion__content has-dot">
                <ul>
                    <li>알림톡 템플릿은 등록 후 카카오에서 검수 완료를 해야 사용이 가능합니다.</li>
                    <li>검수는 카카오톡에서 진행되며, 영업일 기준 1~2일 이상 시간이 소요됩니다.</li>
                </ul>
            </div>
        </details>
    </section>
</section>
