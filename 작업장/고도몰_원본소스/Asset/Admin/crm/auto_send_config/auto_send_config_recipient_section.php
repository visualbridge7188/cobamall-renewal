<?php $isKakaoVendorSupported = $autoSendConfig['KAKAO_ALRIM_TALK']['isKakaoVendorSupported'] ?? true; ?>
<section class="ncua-card ncua-card--no-border" data-target-type="<?= $recipient ?>" data-kakao-alternative-by-sms="<?= $isKakaoAlternativeBySms ? 'true' : 'false' ?>" data-myapp-alternative-by-sms="<?= $isMyappAlternativeBySms ? 'true' : 'false' ?>" data-kakao-vendor-supported="<?= $isKakaoVendorSupported ? 'true' : 'false' ?>" id="<?= $recipient ?>-send-config" style="display: none;">
    <header class="ncua-card__header">
        <h4 class="ncua-card__title" data-tooltip-seq="006" data-tooltip-icon-type="fill"><?= $recipientTitles[$recipient] ?> 알림 발송수단 설정</h4>
    </header>

    <section class="ncua-card__body">
        <div class="ncua-border-group-box">
            <?php
            // MYAPP_PUSH는 member인 경우에만 표시
            $filteredChannels = array_filter($channels, fn($channel) => $channel->name !== 'MYAPP_PUSH' || in_array($recipient, $myappSupportRecipients));
            $isSingleChannel = count($filteredChannels) === 1;
            foreach ($filteredChannels as $channel) {
                if ($isSingleChannel) {
                    $isChecked = true;
                    $isDisabled = true;
                } elseif ($channel->name === 'SMS' && !$validAutoSendSms) {
                    $isChecked = false;
                    $isDisabled = true;
                } elseif ($channel->name === 'KAKAO_ALRIM_TALK' && (!$kakaoCrmUseFlag || !$isKakaoVendorSupported)) {
                    $isChecked = false;
                    $isDisabled = true;
                } elseif ($channel->name === 'MYAPP_PUSH' && !$myappCrmUseFlag) {
                    $isChecked = false;
                    $isDisabled = true;
                } else {
                    $isChecked = $autoSendConfig[$channel->name]['isAutoSend'] === 'y';
                    $isDisabled = false;
                }
            ?>
                <label class="ncua-checkbox-field ncua-checkbox-field--xs has-text">
            <span class="ncua-checkbox-input ncua-checkbox-input--xs ncua-checkbox-field__input">
                <input type="hidden" name="recipients[<?= $recipient ?>][<?= $channel->name ?>][isEnabled]" value="<?= $isSingleChannel ? 'y' : 'n' ?>" />
                <input type="checkbox" name="recipients[<?= $recipient ?>][<?= $channel->name ?>][isEnabled]" value="y" <?= $isChecked ? 'checked' : '' ?> <?= $isDisabled ? 'disabled' : '' ?> />
            </span>
                    <span class="ncua-checkbox-field__text"><?= $channel->getTitle() ?></span>
                </label>
            <?php } ?>
        </div>
        <div class="channel-hint" hidden>
            <span class="ncua-hint-text destructive ncua-input__hint-text">발송수단을 선택해 주세요.</span>
        </div>

        <div class="ncua-horizontal-tab ncua-horizontal-tab--panel">
            <div class="swiper swiper-initialized swiper-horizontal">
                <div class="swiper-wrapper">
                    <?php foreach ($filteredChannels as $channel) { ?>
                        <div class="swiper-slide ncua-horizontal-tab__item">
                            <button type="button" class="ncua-tab-button is-active" data-tab-target="<?= $channel->name ?>"><?= $channel->getTitle() ?></button>
                        </div>
                    <?php } ?>
                </div>
            </div>
        </div>

        <div class="tab-content-wrap">
            <?php if (!empty($autoSendConfig['SMS'])): ?>
                <!-- SMS/LMS  -->
                <div class="sms-lms-content tab-content-item" data-tab="SMS" style="display: none;">
                    <input type="hidden" name="recipients[<?= $recipient ?>][SMS][templateSno]" value="<?= $autoSendConfig['SMS']['sno'] ?? '' ?>" />

                    <?php if (!$validAutoSendSms): ?>
                        <div class="no-ready-send-type">
                            <p class="ncua-caution-text">
                                발송 수단이 준비되지 않았습니다.
                                <a class="ncua-link" href="./message_config.php" target="_blank">메시지 설정</a>
                                에서 필수 항목을 먼저 완료해 주세요.
                            </p>
                        </div>
                    <?php endif; ?>

                    <div class="ncua-card__body-block-section-wrap">
                        <section class="ncua-card__body-block-section">
                            <div class="ncua-card__body-title--xs">내용 입력</div>
                            <div id="sms-message-input-container-<?= $recipient ?>"
                                 data-initial-value="<?= htmlspecialchars($autoSendConfig['SMS']['templateContents'] ?? '', ENT_QUOTES) ?>">
                            </div>
                        </section>
                        <section class="ncua-card__body-block-section">
                            <div id="sms-chip-selector-container-<?= $recipient ?>"></div>
                        </section>
                    </div>
                    <div class="ncua-card__body-block-section-actions">
                        <button type="button" class="ncua-btn ncua-btn--sm ncua-btn--secondary" onclick="load_as_template('<?= $recipient ?>', 'SMS')">템플릿 불러오기</button>
                        <button type="button" class="ncua-btn ncua-btn--sm ncua-btn--secondary-gray file-btn" onclick="save_as_template('<?= $recipient ?>', 'SMS')">템플릿으로 저장하기</button>
                    </div>
                </div>
            <?php endif; ?>

            <?php if (!empty($autoSendConfig['KAKAO_ALRIM_TALK'])): ?>
                <!-- 카카오 알림톡 -->
                <div class="tab-content-item" data-tab="KAKAO_ALRIM_TALK" style="display: none;">
                    <script type="application/json" class="kakao-templates-data"><?= json_encode($kakaoTemplates, JSON_UNESCAPED_UNICODE) ?></script>
                    <?php if (!$kakaoCrmUseFlag): ?>
                        <div class="no-ready-send-type">
                            <p class="ncua-caution-text">
                                발송 수단이 준비되지 않았습니다.
                                <a class="ncua-link" href="./message_config.php" target="_blank">메시지 설정</a>
                                에서 필수 항목을 먼저 완료해 주세요.
                            </p>
                        </div>
                    <?php elseif (!$isKakaoVendorSupported): ?>
                        <div class="no-ready-send-type">
                            <p class="ncua-caution-text">
                                <?= $kakaoVendorLabel ?> 알림톡은 지원하지 않는 알림입니다. 알림톡으로 해당 알림을 발송하려면 <a class="ncua-link" href="./message_config.php" target="_blank">메시지 설정</a>에서 알림톡 제공사를 변경해 주세요.
                            </p>
                        </div>
                    <?php endif; ?>

                    <?php if ($shouldChangeKakaoTemplate): ?>
                        <section class="ncua-card__body-block-section">
                            <div class="ncua-card__body-title--xs">내용 입력</div>
                            <div class="ncua-border-content-layout">
                                <div class="ncua-border-content">
                                    <div class="ncua-border-content-title required">구분</div>
                                    <div class="ncua-border-content-form ncua-flex ncua-flex-gap">
                                        <label class="ncua-radio-field ncua-radio-field--xs has-text">
                                            <span class="ncua-radio-input ncua-radio-input--xs ncua-radio-field__input">
                                                 <input type="radio" name="recipients[<?= $recipient ?>][KAKAO_ALRIM_TALK][category]" value="order" <?= empty($autoSendConfig['KAKAO_ALRIM_TALK']['category']) || $autoSendConfig['KAKAO_ALRIM_TALK']['category'] === 'order' ? 'checked' : '' ?> />
                                            </span>
                                            <span class="ncua-radio-field__text">주문/배송</span>
                                        </label>
                                        <label class="ncua-radio-field ncua-radio-field--xs has-text">
                                            <span class="ncua-radio-input ncua-radio-input--xs ncua-radio-field__input">
                                                <input type="radio" name="recipients[<?= $recipient ?>][KAKAO_ALRIM_TALK][category]" value="regular" <?= $autoSendConfig['KAKAO_ALRIM_TALK']['category'] === 'regular' ? 'checked' : '' ?> />
                                            </span>
                                            <span class="ncua-radio-field__text">정기결제(배송)</span>
                                        </label>
                                        <label class="ncua-radio-field ncua-radio-field--xs has-text">
                                            <span class="ncua-radio-input ncua-radio-input--xs ncua-radio-field__input">
                                                <input type="radio" name="recipients[<?= $recipient ?>][KAKAO_ALRIM_TALK][category]" value="present" <?= $autoSendConfig['KAKAO_ALRIM_TALK']['category'] === 'present' ? 'checked' : '' ?> />
                                            </span>
                                            <span class="ncua-radio-field__text">선물하기</span>
                                        </label>
                                        <label class="ncua-radio-field ncua-radio-field--xs has-text">
                                            <span class="ncua-radio-input ncua-radio-input--xs ncua-radio-field__input">
                                                <input type="radio" name="recipients[<?= $recipient ?>][KAKAO_ALRIM_TALK][category]" value="member" <?= $autoSendConfig['KAKAO_ALRIM_TALK']['category'] === 'member' ? 'checked' : '' ?> />
                                            </span>
                                            <span class="ncua-radio-field__text">회원</span>
                                        </label>
                                        <label class="ncua-radio-field ncua-radio-field--xs has-text">
                                            <span class="ncua-radio-input ncua-radio-input--xs ncua-radio-field__input">
                                                <input type="radio" name="recipients[<?= $recipient ?>][KAKAO_ALRIM_TALK][category]" value="board" <?= $autoSendConfig['KAKAO_ALRIM_TALK']['category'] === 'board' ? 'checked' : '' ?> />
                                            </span>
                                            <span class="ncua-radio-field__text">게시판</span>
                                        </label>
                                    </div>
                                </div>
                                <div class="ncua-border-content">
                                    <div class="ncua-border-content-title required">템플릿 명</div>
                                    <div class="ncua-flex ncua-flex-column ncua-gap-8">
                                        <div class="ncua-border-content-form ncua-flex ncua-flex-gap">
                                            <label class="ncua-radio-field ncua-radio-field--xs has-text">
                                        <span class="ncua-radio-input ncua-radio-input--xs ncua-radio-field__input">
                                            <input type="radio" class="kakao-template-type-radio" name="recipients[<?= $recipient ?>][KAKAO_ALRIM_TALK][templateType]" value="godomall" data-recipient="<?= $recipient ?>" <?= $autoSendConfig['KAKAO_ALRIM_TALK']['isGodomallTemplate'] ? 'checked' : '' ?> />
                                        </span>
                                                <span class="ncua-radio-field__text">고도몰 템플릿</span>
                                            </label>
                                            <label class="ncua-radio-field ncua-radio-field--xs has-text">
                                        <span class="ncua-radio-input ncua-radio-input--xs ncua-radio-field__input">
                                            <input type="radio" class="kakao-template-type-radio" name="recipients[<?= $recipient ?>][KAKAO_ALRIM_TALK][templateType]" value="user" data-recipient="<?= $recipient ?>" <?= !$autoSendConfig['KAKAO_ALRIM_TALK']['isGodomallTemplate'] ? 'checked' : '' ?> />
                                        </span>
                                                <span class="ncua-radio-field__text">사용자 템플릿</span>
                                            </label>
                                        </div>
                                        <div class="ncua-border-content-form ncua-flex ncua-gap-4">
                                            <?php
                                            $currentTemplateType = $autoSendConfig['KAKAO_ALRIM_TALK']['isGodomallTemplate'] ? 'godomall' : 'user';
                                            $currentTemplateCode = $autoSendConfig['KAKAO_ALRIM_TALK']['templateCode'] ?? '';
                                            $currentCategory = $autoSendConfig['KAKAO_ALRIM_TALK']['category'] ?? '';
                                            $godomallTemplateCode = $currentTemplateType === 'godomall' ? $currentTemplateCode : '';
                                            $userTemplateCode = $currentTemplateType === 'user' ? $currentTemplateCode : '';
                                            ?>
                                            <input type="hidden" class="kakao-selected-template-godomall" data-recipient="<?= $recipient ?>" value="<?= htmlspecialchars($godomallTemplateCode) ?>" />
                                            <input type="hidden" class="kakao-selected-template-user" data-recipient="<?= $recipient ?>" value="<?= htmlspecialchars($userTemplateCode) ?>" />
                                            <div class="ncua-select ncua-select--xs kakao-template-select">
                                                <div class="ncua-select__content">
                                                    <select class="ncua-select__tag kakao-template-dropdown" name="recipients[<?= $recipient ?>][KAKAO_ALRIM_TALK][templateCode]" data-recipient="<?= $recipient ?>">
                                                        <option value="">템플릿을 선택하세요</option>
                                                        <?php
                                                        // 현재 카테고리의 템플릿 가져오기
                                                        $categoryTemplates = $kakaoTemplates[$currentCategory] ?? [];
                                                        foreach ($categoryTemplates as $template):
                                                            // 고도몰/사용자 템플릿 필터링
                                                            $isGodomall = \Origin\Enum\AutoSend\AutoSendSupport::isGodomallTemplateByKakaoAlrim($template['templateName'] ?? '');
                                                            if (($currentTemplateType === 'godomall' && !$isGodomall) || ($currentTemplateType === 'user' && $isGodomall)) continue;
                                                        ?>
                                                            <option value="<?= htmlspecialchars($template['templateCode']) ?>" <?= $template['templateCode'] === $currentTemplateCode ? 'selected' : '' ?>><?= htmlspecialchars($template['templateName']) ?></option>
                                                        <?php endforeach; ?>
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </section>
                    <?php endif; ?>

                    <div class="ncua-input ncua-input--textarea ncua-input--textarea--xs kakao-template-preview">
                        <textarea class="ncua-input__textarea" readonly><?= $autoSendConfig['KAKAO_ALRIM_TALK']['templateContents'] ?></textarea>
                    </div>

                    <!-- 안내문구 -->
                    <?php if ($shouldChangeKakaoTemplate): ?>
                        <div class="ncua-accordion ncua-accordion--gray notice-accordion ncua-flex-gap">
                            <div class="ncua-accordion__item">
                                <div class="ncua-accordion__summary">SMS/LMS 대체 발송 안내</div>
                                <ul>
                                    <li class="ncua-notice-info">알림톡, 앱푸시 발송이 실패할 경우 SMS/LMS로 대체 발송됩니다.</li>
                                    <li class="ncua-notice-info">SMS/LMS의 대체 발송은 <a class="ncua-link" href="./message_config.php" target="_blank">메시지 설정</a>에서 변경이 가능합니다.</li>
                                    <li class="ncua-notice-info"><a class="ncua-link" href="./message_config.php" target="_blank">메시지 설정</a>에서 SMS 대체발송 옵션이 '90byte까지만 SMS 발송' 선택이 되어있으면 메시지 내용이 잘려서 최대 90bytes까지 노출됩니다.</li>
                                </ul>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>
            <?php endif; ?>

            <?php if (!empty($autoSendConfig['MYAPP_PUSH']) && in_array($recipient, $myappSupportRecipients)): ?>
                <!-- 마이앱(앱푸시) -->
                <div class="tab-content-item" data-tab="MYAPP_PUSH" style="display: none;">
                    <input type="hidden" name="recipients[<?= $recipient ?>][MYAPP_PUSH][templateSno]" value="<?= $autoSendConfig['MYAPP_PUSH']['sno'] ?? '' ?>" />
                    <?php if (!$myappCrmUseFlag): ?>
                        <div class="no-ready-send-type">
                            <p class="ncua-caution-text">
                                발송 수단이 준비되지 않았습니다.
                                <a class="ncua-link" href="./message_config.php" target="_blank">메시지 설정</a>
                                에서 필수 항목을 먼저 완료해 주세요.
                            </p>                        </div>
                    <?php endif; ?>
                    <div class="ncua-card__body-block-section-wrap">
                        <!-- 내용입력 -->
                        <section class="ncua-card__body-block-section">
                            <div class="ncua-card__body-title--xs">내용 입력</div>
                            <div class="ncua-input ncua-input--xs ncua-input--full-width-with-count">
                                <div class="ncua-input__content-wrap">
                                    <div class="ncua-input__content">
                                        <div class="ncua-input__field ncua-input__field--xs">
                                            <input maxlength="40" type="text" name="recipients[<?= $recipient ?>][MYAPP_PUSH][title]" class="myapp-template-title" data-charcount-key="<?= $recipient ?>MyappTemplateTitle" value="<?= $autoSendConfig['MYAPP_PUSH']['title'] ?? '' ?>" placeholder="메시지 내용을 입력하세요." />
                                        </div>
                                    </div>
                                    <div class="ncua-input__field-text-count" data-charcount-text="<?= $recipient ?>MyappTemplateTitle">
                                        <output class="ncua-input__field-text-count-current">0</output>
                                        <span>/40</span>
                                    </div>
                                </div>
                            </div>
                            <div id="myapp-message-input-container-<?= $recipient ?>"
                                 data-initial-value="<?= htmlspecialchars($autoSendConfig['MYAPP_PUSH']['templateContents'] ?? '', ENT_QUOTES) ?>">
                            </div>
                        </section>
                        <!-- 사용 가능한 변수 -->
                        <section class="ncua-card__body-block-section">
                            <div id="myapp-chip-selector-container-<?= $recipient ?>"></div>
                        </section>
                        <!-- 세부입력 -->
                        <section class="ncua-card__body-block-section">
                            <div class="ncua-card__body-title--xs">세부 입력</div>
                            <div class="ncua-table ncua-table--vertical">
                                <table>
                                    <tbody>
                                    <tr>
                                        <th><div>이미지</div></th>
                                        <td>
                                            <div class="ncua-gap-8">
                                                <input type="hidden" class="myapp-existing-image" name="recipients[<?= $recipient ?>][MYAPP_PUSH][existingImage]" value="<?= $autoSendConfig['MYAPP_PUSH']['image'] ?? '' ?>" />
                                                <div id="myapp-image-file-input-container-<?= $recipient ?>"></div>
                                            </div>
                                        </td>
                                    </tr>
                                    <tr class="myapp-detail-url-row">
                                        <th><div>URL</div></th>
                                        <td>
                                            <div class="ncua-flex-column ncua-gap-4">
                                                <div>
                                                    <span><?= $myappDomain ?></span>
                                                    <div class="ncua-input ncua-input--xs">
                                                        <div class="ncua-input__content-wrap">
                                                            <div class="ncua-input__content">
                                                                <div class="ncua-input__field ncua-input__field--xs">
                                                                    <label>
                                                                        <input type="text" class="myapp-url-input" name="recipients[<?= $recipient ?>][MYAPP_PUSH][url]" placeholder="/event/123455" value="<?= $autoSendConfig['MYAPP_PUSH']['url'] ?? '' ?>" <?= !empty($autoSendConfig['MYAPP_PUSH']['url']) ? 'data-verified="true"' : '' ?> />
                                                                    </label>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <?php $hasUrl = !empty($autoSendConfig['MYAPP_PUSH']['url']); ?>
                                                    <button type="button" class="ncua-btn ncua-btn--xs ncua-btn--secondary-gray myapp-url-verify-btn <?= $hasUrl ? '' : 'is-disable' ?>" <?= $hasUrl ? '' : 'disabled' ?>>
                                                        <span class="ncua-btn__label">검증</span>
                                                    </button>
                                                </div>
                                                <div class="ncua-notice-info">입력되지 않은 푸시는 메인페이지로 이동됩니다.</div>
                                            </div>
                                        </td>
                                    </tr>
                                    </tbody>
                                </table>
                            </div>
                        </section>
                    </div>

                    <div class="ncua-card__body-block-section-actions">
                        <button type="button" class="ncua-btn ncua-btn--sm ncua-btn--secondary" onclick="load_as_template('<?= $recipient ?>', 'MYAPP_PUSH')">템플릿 불러오기</button>
                        <button type="button" class="ncua-btn ncua-btn--sm ncua-btn--secondary-gray file-btn" onclick="save_as_template('<?= $recipient ?>', 'MYAPP_PUSH')">템플릿으로 저장하기</button>
                    </div>

                    <div class="ncua-accordion ncua-accordion--gray notice-accordion">
                        <div class="ncua-accordion__summary">SMS/LMS 대체 발송 안내</div>
                        <ul class="ncua-accordion__content">
                            <li class="ncua-notice-info">알림톡, 앱푸시 발송이 실패할 경우 SMS/LMS로 대체 발송됩니다.</li>
                            <li class="ncua-notice-info">SMS/LMS의 대체 발송은 <a class="ncua-link" href="./message_config.php" target="_blank">메시지 설정</a>에서 변경이 가능합니다.</li>
                            <li class="ncua-notice-info"><a class="ncua-link" href="./message_config.php" target="_blank">메시지 설정</a>에서 SMS 대체발송 옵션이 '90byte까지만 SMS 발송' 선택이 되어있으면 메시지 내용이 잘려서 최대 90bytes까지 노출됩니다.</li>
                        </ul>
                    </div>
                </div>
            <?php endif; ?>
        </div>


    </section>
</section>
