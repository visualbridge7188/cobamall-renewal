<div class="js-myapp-push-tab-section">
    <input type="hidden" name="myappImageUrl" value="">
    <div class="swiper ncua-horizontal-tab ncua-horizontal-tab--md ncua-horizontal-tab--panel">
        <div class="swiper-wrapper">
            <div class="swiper-slide ncua-horizontal-tab__item">
                <button type="button" class="ncua-tab-button is-active" data-send-tab-type="MAIN">
                    마이앱 메시지 작성
                </button>
            </div>
            <div class="swiper-slide ncua-horizontal-tab__item">
                <button type="button" class="ncua-tab-button" data-send-tab-type="ALTERNATIVE">
                    SMS/LMS(대체 메시지)
                </button>
            </div>
        </div>
    </div>
    <div class="tab-content-wrap">
        <!-- 마이앱 조건 설정 -->
        <div class="myapp-condition-setting send-method-component" data-target="MAIN">
            <p class="ncua-card__body-title--xs">조건 설정</p>
            <ul class="myapp-condition-setting-notice">
                <?php if ($isRecipeContext): ?>
                    <li class="ncua-notice-info">선택된 회원의 계정이 로그인 되어있는 모든 앱에 푸시가 발송됩니다.<br />다만, 발송 OS, 앱 설치, 알림 수신 동의 여부에 따라 수신 대상에서 제외될 수 있습니다.</li>
                <?php else: ?>
                    <li class="ncua-notice-info">앱 설치자로 발송 시 로그인 상태와 상관없이 앱을 설치한 모든 유저에게 푸시가 발송됩니다.</li>
                    <li class="ncua-notice-info">가입 회원에게 발송 시 선택된 회원의 계정이 로그인 되어 있는 모든 앱에 푸시가 발송됩니다.<br />다만, 발송 OS, 앱 설치 여부, 앱 알림 수신 동의 여부에 따라 수신 대상에서 제외될 수 있습니다.</li>
                <?php endif; ?>
            </ul>
            <div class="ncua-table ncua-table--vertical">
                <table>
                    <tbody>
                    <tr class="js-myapp-push-send-condition-row display-none">
                        <th><div>발송 조건</div></th>
                        <td>
                            <div class="ncua-flex ncua-flex-gap">
                                <?php foreach($myappPushSendConditions as $myappPushSendCondition): ?>
                                    <label class="ncua-radio-field ncua-radio-field--xs has-text">
                                        <span class="ncua-radio-input ncua-radio-input--xs ncua-radio-field__input">
                                            <input name="myappSendCondition" type="radio" value="<?= $myappPushSendCondition->name ?>" <?= $myappPushSendCondition->name === 'MEMBER_ONLY' ? 'checked="checked"' : '' ?>/>
                                        </span>
                                        <span>
                                            <span class="ncua-radio-field__text"><?= $myappPushSendCondition->value ?></span>
                                        </span>
                                    </label>
                                <?php endforeach; ?>
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <th><div>발송 목적</div></th>
                        <td>
                            <div class="ncua-flex ncua-flex-gap">
                                <?php foreach($myappPushNotificationTypes as $myappPushNotificationType): ?>
                                    <label class="ncua-radio-field ncua-radio-field--xs has-text">
                                        <span class="ncua-radio-input ncua-radio-input--xs ncua-radio-field__input">
                                            <input name="myappNotificationType" type="radio" value="<?= $myappPushNotificationType->name ?>" <?= $myappPushNotificationType->name === 'AD' ? 'checked="checked"' : '' ?>/>
                                        </span>
                                        <span>
                                            <span class="ncua-radio-field__text"><?= $myappPushNotificationType->value ?></span>
                                        </span>
                                    </label>
                                <?php endforeach; ?>
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <th><div>발송 OS</div></th>
                        <td>
                            <div class="ncua-flex ncua-flex-gap">
                                <?php foreach($myappPushSendPlatforms as $myappPushSendPlatform): ?>
                                    <label class="ncua-radio-field ncua-radio-field--xs has-text">
                                        <span class="ncua-radio-input ncua-radio-input--xs ncua-radio-field__input">
                                            <input name="myappPlatform" type="radio" value="<?= $myappPushSendPlatform->name ?>" <?= $myappPushSendPlatform->name === 'ALL' ? 'checked="checked"' : '' ?>/>
                                        </span>
                                        <span>
                                            <span class="ncua-radio-field__text"><?= $myappPushSendPlatform->value ?></span>
                                        </span>
                                    </label>
                                <?php endforeach; ?>
                            </div>
                        </td>
                    </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <div class="message-content send-method-component" data-target="MAIN">
            <div class="ncua-card__body-title-wrap">
                <p class="ncua-card__body-title--xs">내용 입력</p>
            </div>
            <div class="myapp-message-title">
                <div class="ncua-input ncua-input--xs ncua-input-full-width">
                    <div class="ncua-input__content-wrap">
                        <div class="ncua-input__content">
                            <div class="ncua-input__field ncua-input__field--xs">
                                <input class="ncua-myapp-message-title" data-charcount-key="myapp-title" maxlength="40" name="myappTitle" type="text" value="<?= gd_htmlspecialchars($initialTitle ?? '') ?>" placeholder="맛집 BEST 3" />
                            </div>
                        </div>
                        <div class="ncua-input__field-text-count" data-charcount-text="myapp-title">
                            <output class="ncua-input__field-text-count-current">0</output>
                            <span>/40</span>
                        </div>
                    </div>
                </div>
            </div>
            <div id="messageContentContainer"></div>
        </div>
        <div id="replaceCodeContainer" class="send-method-component" data-target="MAIN"></div>

        <div class="message-content send-method-component" data-target="ALTERNATIVE">
            <div class="ncua-card__body-title-wrap">
                <p class="ncua-card__body-title--xs">내용 입력</p>
                <button type="button" class="ncua-btn ncua-btn--xs ncua-btn--text-gray has-underline refresh-btn load-main-contents">
                    메시지 최신 상태 반영
                </button>
            </div>
            <div id="messageContentAlternativeContainer"></div>
        </div>
        <div id="replaceCodeAlternativeContainer" class="send-method-component" data-target="ALTERNATIVE"></div>

        <!-- 마이앱 조건 설정 테이블 -->
        <div class="myapp-condition-additional-setting send-method-component" data-target="MAIN">
            <p class="ncua-card__body-title--xs">세부 설정</p>
            <div class="ncua-table ncua-table--vertical">
                <table>
                    <tbody>
                        <tr class="js-recruit-cancel-method">
                            <th><div>수신 동의 철회 방법</div></th>
                            <td>
                                <div>
                                    <div class="ncua-input ncua-input--xs ncua-input-full-width">
                                        <div class="ncua-input__content-wrap">
                                            <div class="ncua-input__content">
                                                <div class="ncua-input__field ncua-input__field--xs">
                                                    <input class="ncua-recruit-cancel-method" data-charcount-key="recruit-cancel-method" maxlength="30" name="myappUnsubscribeGuide" type="text" value="" placeholder="수신거부 설정 > 알림 OFF" />
                                                </div>
                                            </div>
                                            <div class="ncua-input__field-text-count" data-charcount-text="recruit-cancel-method">
                                                <output class="ncua-input__field-text-count-current">0</output>
                                                <span>/30</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <th><div>이미지</div></th>
                            <td>
                                <div class="my-app-image-container">
                                    <div id="myappImageFileInput" class="excel-file-input"></div>
                                    <div id="myappImageFileTagContainer"></div>
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <th><div>URL</div></th>
                            <td>
                                <div class="ncua-flex-column ncua-gap-8">
                                    <div class="url-input-wrap">
                                        <span id="mobileUrl"><?= $myappPushUrl ?></span>
                                        <div class="ncua-input ncua-input--xs">
                                            <div class="ncua-input__content">
                                                <div class="ncua-input__field ncua-input__field--xs ">
                                                    <input name="myappPushPath" type="text" value="" placeholder="/event/12345" />
                                                </div>
                                            </div>
                                        </div>
                                        <input type="hidden" name="validatePushUrl" value="n"/>
                                        <button type="button" class="ncua-btn ncua-btn--xs ncua-btn--secondary-gray" data-click-target="myappPushUrlCheck" disabled>검증</button>
                                    </div>
                                    <ul>
                                        <li class="ncua-notice-info">입력되지 않은 푸시는 메인페이지로 이동됩니다.</li>
                                    </ul>
                                </div>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
        <!-- 템플릿 버튼 -->
        <?php if (!$isRecipeContext): ?>
        <div class="template-control-wrap send-method-component" data-target="MAIN">
            <button data-click-target="loadTemplate" type='button' class="ncua-btn ncua-btn--sm ncua-btn--secondary">템플릿 불러오기</button>
            <button data-click-target="saveAsTemplate" type='button' class="ncua-btn ncua-btn--sm ncua-btn--secondary-gray file-btn">
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 16 16" fill="none"><path d="M9.33317 1.51294V4.26663C9.33317 4.64 9.33317 4.82669 9.40583 4.96929C9.46975 5.09473 9.57174 5.19672 9.69718 5.26064C9.83978 5.3333 10.0265 5.3333 10.3998 5.3333H13.1535M7.99984 11.9999V7.99992M5.99984 9.99992H9.99984M9.33317 1.33325H5.8665C4.7464 1.33325 4.18635 1.33325 3.75852 1.55124C3.3822 1.74299 3.07624 2.04895 2.88449 2.42527C2.6665 2.85309 2.6665 3.41315 2.6665 4.53325V11.4666C2.6665 12.5867 2.6665 13.1467 2.88449 13.5746C3.07624 13.9509 3.3822 14.2569 3.75852 14.4486C4.18635 14.6666 4.7464 14.6666 5.8665 14.6666H10.1332C11.2533 14.6666 11.8133 14.6666 12.2412 14.4486C12.6175 14.2569 12.9234 13.9509 13.1152 13.5746C13.3332 13.1467 13.3332 12.5867 13.3332 11.4666V5.33325L9.33317 1.33325Z" stroke="black" stroke-width="1.33" stroke-linecap="round" stroke-linejoin="round"/></svg>
                템플릿으로 저장하기
            </button>
        </div>
        <?php endif; ?>
        <!-- 메시지 성과 추적 -->
        <div class="message-performance-analysis send-method-component" data-target="ALTERNATIVE">
            <p class="ncua-card__body-title--xs tooltip-align" data-tooltip-seq="004">메시지 성과 추적</p>
            <div class="add-link-wrap">
                <div class="ncua-input ncua-input--xs">
                    <div class="ncua-input__content">
                        <div class="ncua-input__field ncua-input__field--xs">
                            <input name="shortLink" type="text" value="" placeholder="메시지에 삽입할 링크를 여기에 넣고 추가를 누르면 숏링크가 생성됩니다." data-role="short-link-input" />
                        </div>
                    </div>
                </div>
                <button type="button" class="ncua-btn ncua-btn--xs ncua-btn--secondary-gray plus-btn" data-role="add-short-link">
                    <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" fill="none" viewBox="0 0 24 24" stroke="none"><path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 5v14m-7-7h14"></path></svg>
                    추가
                </button>
            </div>
            <div class="chip-button-wrap" data-role="chip-container"></div>
        </div>
    </div>
    <!-- 마이앱 안내 -->
    <details class="ncua-accordion ncua-accordion--gray send-method-component" data-target="MAIN" <?= !$isRecipeContext ? 'open' : '' ?>>
        <summary>마이앱 안내</summary>
        <div class="ncua-accordion__content has-dot">
            <ul>
                <li>광고성 정보를 발송하는 경우 <button id="myappSendGuide" type="button" class="ncua-btn ncua-btn--xs ncua-btn--text has-underline">법적 준수 사항</button>을 지켜서 발송해야 합니다.</li>
                <li>발송 목적을 '광고성'으로 선택한 경우 (광고) 머리말이 추가되서 발송됩니다.<span>(광고) 문구 입력 시 '(광고)' 문구가 중복되므로 유의</span>해 주세요.</li>
                <li>발송 목적이 '광고성' 인 경우 대체 메시지에서는 직접 광고성 문구 추가를 체크해주셔야 합니다.</li>
            </ul>
        </div>
    </details>
    <!-- SMS/LMS 대체 발송 안내 -->
    <details class="ncua-accordion ncua-accordion--gray send-method-component" data-target="ALTERNATIVE" <?= !$isRecipeContext ? 'open' : '' ?>>
        <summary>SMS/LMS 발송 안내</summary>
        <div class="ncua-accordion__content has-dot">
            <ul>
                <li>SMS 작성 시 90byte를 초과하면 LMS로 자동 전환되어 발송됩니다.</li>
                <li>변수에 실제 데이터가 적용된 최종 메시지가 90byte를 초과하는 경우에도 LMS로 전환될 수 있습니다.</li>
            </ul>
        </div>
    </details>
    <!-- 광고성 문구 추가 -->
    <div class="ad-phrase-add send-method-component" data-target="ALTERNATIVE">
        <p class="ncua-card__body-title--xs">광고성 문구 추가</p>
        <label class="ncua-checkbox-field ncua-checkbox-field--xs has-text">
            <span class="ncua-checkbox-input ncua-checkbox-input--xs ncua-checkbox-field__input">
                <input type="checkbox" name="use080Reject"  data-available="<?= $is080RejectAvailable ? 'y' : 'n' ?>">
            </span>
            <span>
                <span class="ncua-checkbox-field__text">광고성 문구 추가</span>
                <span class="ncua-checkbox-field__support-text">광고성 문구를 추가하려면 <a class="ncua-btn ncua-btn--xs ncua-btn--text has-underline" href="../service/service_info.php?menu=consulting_refusal_info" target="_blank">[080 수신거부 사용신청]</a>을 먼저 해주시기 바랍니다.</span>
            </span>
        </label>
    </div>
</div>

<script type="text/javascript">
    const myappPushTabSection = document.querySelector('.js-myapp-push-tab-section');
    const chipContainer = document.querySelector('[data-role="chip-container"]');
    const isMyappInstalled = <?= json_encode($isMyappInstalled) ?>;
    const isRecipeContext = <?= $isRecipeContext ? 'true' : 'false' ?>;

    let myappImageFileInput;
    let isLoadingTemplate = false;
    const myappImageAllowedExtensions = ['jpg', 'png'];
    const MAX_MY_APP_IMAGE_FILE_SIZE = 3 * 1024 * 1024; // 3MB

    $(document).ready(function () {
        initializeMyappPushContentSettingSectionEvents();
        initializeMyappPushContentSettingSectionElement();
    });

    function initializeMyappPushContentSettingSectionEvents() {
        // 메인/대체 메시지 탭 버튼
        myappPushTabSection.querySelectorAll('.ncua-tab-button[data-send-tab-type]').forEach(button => {
            button.addEventListener('click', function () {
                const tabType = button.dataset.sendTabType;
                if (tabType === 'ALTERNATIVE') {
                    if (!enableAlternative('MYAPP')) {
                        // 조건 만족 시 alert
                        const recipientType = document.querySelector('input[name="recipientType"]:checked')?.value ?? 'ALL'
                        const isOnlyAgreed = document.querySelector('input[name="isOnlyAgreed"]')?.checked;
                        const myappSendCondition = document.querySelector('input[name="myappSendCondition"]:checked').value;

                        if (recipientType === 'ALL' && !isOnlyAgreed && myappSendCondition === 'APP_INSTALLED') {
                            NCDSAlert({
                                message: '대체 메시지 미지원',
                                subMessage: '현재 선택한 발송 조건은 대체 메시지를 제공하지 않습니다.',
                                iconType: 'error'
                            });
                            return;
                        }

                        NCDSAlert({
                            message: '대체 메시지 설정이 비활성화되어 있습니다.',
                            subMessage: 'CRM > 메시지 > 메시지 설정에서 기능을 활성화한 후 다시 시도해주세요.',
                            iconType: 'error'
                        });
                        return;
                    }
                }

                setPreviewTab(tabType);
                updateActiveTab(myappPushTabSection, tabType);
                updateTabComponent(myappPushTabSection, tabType);
            });
        });
        myappPushTabSection.querySelectorAll('input[name="myappNotificationType"]').forEach(radio => {
            radio.addEventListener('change', function () {
                myappPushTabSection.querySelector('.js-recruit-cancel-method').classList.toggle('display-none', this.value === 'NOTIFICATION');
                if (this.value === 'NOTIFICATION') {
                    myappPushTabSection.querySelector('input[name="myappUnsubscribeGuide"]').value = '';
                    window.preview.setWithdrawalMethod('');
                }
                window.preview.setShowAdLabel(radio.value === 'AD');
                updateAdMessage(radio.value);
            })
        });

        myappPushTabSection.querySelector('button[data-click-target="loadTemplate"]')?.addEventListener('click', function() {
            $.post('./mobile_send/layer_mobile_send_myapp_push_template.php', null, function (data) {
                ncds_layer_popup({message: data, title: '템플릿 불러오기', size: 'wide-sm'});
            });
        });

        myappPushTabSection.querySelector('button[data-click-target="saveAsTemplate"]')?.addEventListener('click', function() {
            const message = messageInput.getValue();
            if (message === '') {
                NCDSAlert({
                    message: '내용을 입력해야 템플릿으로 저장할 수 있습니다.',
                    iconType: 'error'
                });
                return;
            }
            const myappData = {
                pushSubject: myappPushTabSection.querySelector('input[name="myappTitle"]')?.value ?? '',
                pushContent: message,
                pushImage: myappPushTabSection.querySelector('input[name="myappImageUrl"]')?.value ?? '',
                pushUrl: myappPushTabSection.querySelector('input[name="myappPushPath"]')?.value ?? '',
                pushWithdraw: myappPushTabSection.querySelector('input[name="myappUnsubscribeGuide"]')?.value ?? '',
            };
            $.post('./mobile_send/layer_mobile_send_save_myapp_push_template.php', myappData, function (data) {
                ncds_layer_popup({message: data, title: '템플릿으로 저장<div class="modal-title-description">작성한 내용을 저장할 카테고리와 제목을 선택하여 주시기 바랍니다.</div>', size: 'wide-sm'});
            });
        });

        myappPushTabSection.querySelector('input[name="use080Reject"]').addEventListener('click', function(e) {
            if (this.dataset.available !== 'y') {
                e.preventDefault();
                return;
            }

            if (this.checked) {
                addAdText(window.alternativeMessageInput, window.alternativePreview);
            } else {
                removeAdText(window.alternativeMessageInput, window.alternativePreview);
            }
        });

        myappPushTabSection.querySelector('input[name="myappTitle"]').addEventListener('input', function(e) {
            syncMyappTitlePreview(e.target.value);
        });

        myappPushTabSection.querySelector('input[name="myappUnsubscribeGuide"]').addEventListener('input', function(e) {
            window.preview.setWithdrawalMethod(e.target.value);
            if (e.target.value === '') {
                setDefaultPreview(['MYAPP_UNSUBSCRIBE_GUIDE']);
            }
        });

        myappPushTabSection.querySelector('button[data-click-target="myappPushUrlCheck"]').addEventListener('click', function() {
            const mobileUrl = myappPushTabSection.querySelector('#mobileUrl').innerText;
            const path = myappPushTabSection.querySelector('input[name="myappPushPath"]')?.value;
            window.open(mobileUrl + path, "_blank", 'width=360,height=700,scrollbars=yes');
            myappPushTabSection.querySelector('input[name="validatePushUrl"]').value = 'y';
        });

        myappPushTabSection.querySelector('input[name="myappPushPath"]').addEventListener('input', function(e) {
            myappPushTabSection.querySelector('input[name="validatePushUrl"]').value = 'n';
            myappPushTabSection.querySelector('button[data-click-target="myappPushUrlCheck"]').disabled = !e.target.value.replace(/^\s+|\s+$/g, '');
        });

        myappPushTabSection.querySelector('button.load-main-contents').addEventListener('click', function() {
            const message = messageInput.getValue();
            const allKeys = [
                MobileMessageReplaceCode.CRM_RECIPE,
                MobileMessageReplaceCode.MEMBER,
                MobileMessageReplaceCode.GOODS,
                MobileMessageReplaceCode.ORDER,
                MobileMessageReplaceCode.PROMOTION,
                MobileMessageReplaceCode.BOARD,
                MobileMessageReplaceCode.REGULAR,
                MobileMessageReplaceCode.PRESENT,
            ].flatMap(category => Object.keys(category));

            const convertedMessage = message.replace(/#\{([a-zA-Z_0-9]+)}/g, (match, key) => {
                return allKeys.includes(MobileMessageReplaceCode.normalizeReplaceKey(key)) ? `{${key}}` : '';
            });

            alternativeMessageInput.setValue('');
            alternativeMessageInput.insertText(convertedMessage);
            alternativePreview.setContent(resolveReplaceCode(convertedMessage, false));
            NCDSToast({message: '메시지가 최신 내용으로 업데이트 됐습니다.', color: 'success'});
        });

        const shortLinkInput = myappPushTabSection.querySelector('input[data-role="short-link-input"]');
        const addShortLinkBtn = myappPushTabSection.querySelector('button[data-role="add-short-link"]');

        addShortLinkBtn.addEventListener('click', function() {
            const linkValue = shortLinkInput.value.replace(/^\s+|\s+$/g, '');

            // 입력값 검증
            if (!linkValue) {
                NCDSAlert({message: '링크를 입력해주세요.', iconType: 'error'});
                return;
            }

            // URL 형식 검증
            if (!isValidUrl(linkValue)) {
                NCDSAlert({
                    message: 'http:// 또는 https://로 시작하는 URL을 입력해 주세요.',
                    iconType: 'error'
                });
                return;
            }

            // 칩 버튼 생성
            addMyappChipButton(linkValue);

            // 입력 필드 초기화
            shortLinkInput.value = '';

            const isImpossibleAddShortLink = document.querySelectorAll(`.chip-button[data-send-tab-type="${getActivatedSendTabType()}"]`).length > 4;
            shortLinkInput.disabled = isImpossibleAddShortLink;
            addShortLinkBtn.disabled = isImpossibleAddShortLink;
        });
    }

    // 칩 버튼 생성 함수
    function addMyappChipButton(linkValue, linkKey = null) {
        if (!linkKey) linkKey = generateLinkKey();
        const chipButton = generateChipButton(linkKey, linkValue);
        const shortLinkInput = myappPushTabSection.querySelector('input[data-role="short-link-input"]');
        const addShortLinkBtn = myappPushTabSection.querySelector('button[data-role="add-short-link"]');

        chipButton.addEventListener('click', function() {
            window.alternativeMessageInput.insertText(chipButton.querySelector('.chip-button-text').textContent); // 알림톡은 대체메시지에 반영
        });

        // 닫기 버튼 이벤트
        const removeBtn = chipButton.querySelector('[data-role="remove-chip"]');
        removeBtn.addEventListener('click', function(e) {
            e.stopPropagation(); // 부모 버튼 클릭 이벤트 방지

            // chip-button-text 값을 textContent에서 모두 삭제
            const chipText = chipButton.querySelector('.chip-button-text')?.textContent;
            if (chipText) {
                const escapedText = chipText.replace(/[.*+?^${}()|[\]\\]/g, '\\$&');
                const replacedContent = alternativeMessageInput.getValue().replace(new RegExp(escapedText, 'g'), '')
                window.alternativeMessageInput.setValue(replacedContent);
                window.alternativePreview.setContent(replacedContent);
                if (replacedContent === '') {
                    setDefaultPreview(['ALTERNATIVE_TEXT']);
                }
            }

            chipButton.remove();

            const isImpossibleAddShortLink = document.querySelectorAll(`.chip-button[data-send-tab-type="${getActivatedSendTabType()}"]`).length > 4;
            shortLinkInput.disabled = isImpossibleAddShortLink;
            addShortLinkBtn.disabled = isImpossibleAddShortLink;

            delShortLinkKey(linkKey);
        });
        chipContainer.appendChild(chipButton);
        setShortLinkKey(linkKey, linkValue);
    }

    function updateAdMessage(notificationType) {
        const message = window.messageInput.getValue();
        let convertedMessage = message.replace(/^\(광고\)\s?/, '');;

        window.messageInput.setValue(convertedMessage);
        window.preview.setContent(resolveReplaceCode(convertedMessage));
    }

    function initializeMyappPushContentSettingSectionElement() {
        // 초기 메시지 타입 선택
        myappPushTabSection.querySelector('button[data-send-tab-type="MAIN"]')?.dispatchEvent(new Event('click'));

        // 전체회원 + SMS 수신동의한 회원 발송 여부에 따른 layout 변경
        const isRecipientAllType = (document.querySelector('input[name="recipientType"]:checked')?.value ?? 'ALL') === 'ALL';
        const isOnlyAgreed = document.querySelector('input[name="isOnlyAgreed"]')?.checked ?? false;
        setMyappSendConditionAvailability(isRecipientAllType, isOnlyAgreed);

        const myappImageFileTagContainer = myappPushTabSection.querySelector('#myappImageFileTagContainer');
        createMyappImageFileInput();

        // textarea 카운팅
        const charCountManager = createCharCountManager({
            targetClasses: ['ncua-myapp-message-title', 'ncua-recruit-cancel-method'],
        });
        charCountManager.init();

        const initialMyappMessage = <?= json_encode($initialMessage ?? '', JSON_UNESCAPED_UNICODE | JSON_HEX_TAG | JSON_HEX_AMP) ?>;
        const initialMyappTitle = <?= json_encode($initialTitle ?? '', JSON_UNESCAPED_UNICODE | JSON_HEX_TAG | JSON_HEX_AMP) ?>;
        const initialAlternativeMessage = <?= json_encode($initialAlternativeMessage ?? '', JSON_UNESCAPED_UNICODE | JSON_HEX_TAG | JSON_HEX_AMP) ?>;

        // 본문 메시지
        window.messageInput = GodoUIModule.render({
            type: 'MessageInput',
            target: '#messageContentContainer',
            name: 'messageContent',
            maxLength: 300,
            useBytes: false,
            initialValue: initialMyappMessage,
            onInput: syncMyappMessagePreview,
        });

        // 대체 메시지
        window.alternativeMessageInput = GodoUIModule.render({
            type: 'MessageInput',
            target: '#messageContentAlternativeContainer',
            name: 'messageAlternativeContent',
            title: '내용 입력',
            maxLength: 2000,
            useBytes: true,
            allowEmoji: false,
            initialValue: initialAlternativeMessage,
            onInput: function(input) {
                window.alternativePreview.setContent(resolveReplaceCode(input, false));
                MobileMessageReplaceCode.updateExpireCodeGuide('#replaceCodeAlternativeContainer', input);
                if (input === '') {
                    window.alternativeMessageInput.setHint(null);
                    setDefaultPreview(['ALTERNATIVE_TEXT']);
                } else {
                    window.alternativeMessageInput.setHint(window.alternativeMessageInput.calculateLength(input) > 90 ? 'LMS 건당 3포인트 차감' : 'SMS 건당 1포인트 차감');
                }
            }
        });

        window.chipSelector = GodoUIModule.render({
            type: 'ChipSelector',
            target: '#replaceCodeContainer',
            title: isRecipeContext ? '추천 변수' : '사용 가능한 변수',
            tooltipSeq: '003',
            hideCategorySelector: isRecipeContext,
            cautionHTML: `
                <ul class="replace-code-caution">
                    <li id="nonMemberReplaceCodeCaution" class="ncua-caution-text" hidden>회원 외 치환코드는 정상 적용되지 않으니 저장된 메시지 수정 시에만 이용바랍니다.</li>
                    <li id="useTemplateCaution" class="ncua-caution-text" hidden>템플릿 사용시에 미지원 치환코드가 포함된 경우, 해당 값은 공란으로 발송되므로 미리보기를 확인해주시기 바랍니다.</li>
                </ul>
            `,
            categories: isRecipeContext ? buildRecipeReplaceCodeCategories() : buildReplaceCodeCategories(),
            onSelect: (variable) => {
                messageInput.insertText(MobileMessageReplaceCode.resolveRecipeExpireInsertKey(variable, isRecipeContext));
            },
            onCategoryChange: (category) => {
                myappPushTabSection.querySelector('#nonMemberReplaceCodeCaution').hidden = category === 'MEMBER';
            }
        });

        window.alternativeChipSelector = GodoUIModule.render({
            type: 'ChipSelector',
            target: '#replaceCodeAlternativeContainer',
            title: isRecipeContext ? '추천 변수' : '사용 가능한 변수',
            tooltipSeq: '003',
            hideCategorySelector: isRecipeContext,
            categories: isRecipeContext ? buildRecipeReplaceCodeCategories() : buildReplaceCodeCategories(),
            onSelect: (variable) => {
                window.alternativeMessageInput.insertText(MobileMessageReplaceCode.resolveRecipeExpireInsertKey(variable, isRecipeContext));
            },
            onCategoryChange: (category) => {
                if (category === 'MEMBER') {
                    window.alternativeChipSelector.setCautionHTML(null);
                } else {
                    window.alternativeChipSelector.setCautionHTML('<ul class="replace-code-caution"><li class="ncua-caution-text">회원 외 치환코드는 정상 적용되지 않으니 저장된 메시지 수정 시에만 이용바랍니다.</li></ul>');
                }
            }
        });

        // 미리보기 랜더링
        document.querySelector('.js-preview-tab-section').classList.remove('display-none');
        window.preview = GodoUIModule.render({
            type: 'MessagePreview',
            target: '#previewContainer',
            sendType: 'MYAPP',
            showAdLabel: true,
            noticeText: `<li class="ncua-notice-info">앱 푸시 메시지는 위와 같이 발송됩니다.</li>
            <li class="ncua-caution-text">사용자의 디바이스에 따라 다소 차이가 있을 수 있습니다.</li>`
        });

        window.preview.onVariableCheckboxChange(() => {
            window.preview.setContent(resolveReplaceCode(window.messageInput.getValue()));
        });

        window.alternativePreview = GodoUIModule.render({
            type: 'MessagePreview',
            target: '#previewAlternativeContainer',
            sendType: 'SMS'
        });

        window.alternativePreview.onVariableCheckboxChange(() => {
            window.alternativePreview.setContent(resolveReplaceCode(window.alternativeMessageInput.getValue(), false));
        });

        const notificationType = myappPushTabSection.querySelector('input[type="radio"][name="myappNotificationType"]:checked').value ?? '';
        updateAdMessage(notificationType);

        setDefaultPreview(['ALTERNATIVE_TEXT', 'MYAPP_UNSUBSCRIBE_GUIDE']);
        if (initialAlternativeMessage) {
            window.alternativePreview.setContent(resolveReplaceCode(initialAlternativeMessage, false));
            MobileMessageReplaceCode.updateExpireCodeGuide('#replaceCodeAlternativeContainer', initialAlternativeMessage);
        }
        syncMyappTitlePreview(initialMyappTitle);
        syncMyappMessagePreview(initialMyappMessage);
    }

    function syncMyappTitlePreview(title) {
        window.preview.setTitle(title);
        if (title === '') {
            setDefaultPreview(['MYAPP_TITLE']);
        }
    }

    function syncMyappMessagePreview(input) {
        window.preview.setContent(resolveReplaceCode(input));
        MobileMessageReplaceCode.updateExpireCodeGuide('#replaceCodeContainer', input);
        if (input === '') {
            myappPushTabSection.querySelector('#useTemplateCaution').hidden = true;
            setDefaultPreview(['MYAPP_TEXT']);
        }
    }

    function createHiddenFileInput(fileName, file, options) {
        const { index = null, container, fileInputName } = options;
        const hiddenUpfilesInput = document.createElement('input');
        hiddenUpfilesInput.hidden = true;
        hiddenUpfilesInput.type = 'file';
        hiddenUpfilesInput.tabIndex = -1;
        hiddenUpfilesInput.setAttribute('aria-hidden', 'true');
        hiddenUpfilesInput.className = 'no-filestyle';
        hiddenUpfilesInput.accept = '';
        hiddenUpfilesInput.name = fileInputName;
        const dataTransfer = new DataTransfer();
        dataTransfer.items.add(file);
        hiddenUpfilesInput.files = dataTransfer.files;

        const wrapperDiv = document.createElement('div');
        wrapperDiv.className = 'ncua-file-hidden-input';
        wrapperDiv.appendChild(hiddenUpfilesInput);
        container.appendChild(wrapperDiv);
    }

    function createMyappImageFileInput(existingImageUrl = null) {
        const container = document.getElementById('myappImageFileInput');
        if (container) container.innerHTML = '';
        const myappImageFileTagContainer = myappPushTabSection.querySelector('#myappImageFileTagContainer');

        // 레시피 모달에서는 이미지 미리보기 슬롯(+) 없이 파일 찾기 버튼만 노출 (board/article_write와 동일한 FileInput 사용)
        const FileInputComponent = isRecipeContext ? ncua.FileInput : ncua.ImageFileInput;
        myappImageFileInput = new FileInputComponent({
            container: 'myappImageFileInput',
            fileInputName: 'myappImageFile',
            hintItems: [
                '권장 사이즈: 640*320px','3MB 이내인 jpg, png 형식의 파일을 등록해 주세요.'
            ],
            accept: 'image/jpg, image/jpeg, image/png',
            multiple: false,
            showFileInput: false,
            onChange: async (newFiles) => {
                // setFiles()로 추가된 파일은 .size가 없음, 실제 선택 파일 우선 탐색
                const file = newFiles.find(f => f.size) || newFiles[0];

                // setFiles()로만 호출된 경우 건너뛰기
                if (file && !file.size) return;

                if (!file) {
                    // 이미지가 등록된 상태에서 삭제 시 확인
                    const currentImageUrl = myappPushTabSection.querySelector('input[name="myappImageUrl"]').value;
                    if (currentImageUrl) {
                        NCDSConfirm({
                            message: '첨부파일을 삭제하시겠습니까?',
                            btnText: {
                                cancelLabel: '취소',
                                confirmLabel: '확인',
                            },
                            callback: (result) => {
                                if (result) {
                                    window.preview.setImage(null);
                                    myappPushTabSection.querySelector('input[name="myappImageUrl"]').value = '';
                                    myappImageFileTagContainer.innerHTML = '';
                                    createMyappImageFileInput();
                                } else {
                                    createMyappImageFileInput(currentImageUrl);
                                }
                            }
                        });
                    }
                    return;
                }

                // 파일 유효성 검사
                if (!await validateMyappFiles(file, myappImageAllowedExtensions, MAX_MY_APP_IMAGE_FILE_SIZE)) {
                    const currentImageUrl = myappPushTabSection.querySelector('input[name="myappImageUrl"]').value;
                    createMyappImageFileInput(currentImageUrl || null);
                    return;
                }

                // 서버에 이미지 업로드
                const formData = new FormData();
                formData.append('mode', 'uploadMyappImage');
                formData.append('imageFile', file);

                const result = await new Promise((resolve) => {
                    $.ajax({
                        url: './mobile_send/layer_send_method_setting_ps.php',
                        type: 'POST',
                        data: formData,
                        processData: false,
                        contentType: false,
                        dataType: 'json',
                        success: (response) => resolve({ success: response.success, message: response.message, data: response.data }),
                        error: () => resolve({ success: false })
                    });
                });

                if (!result.success) {
                    NCDSAlert({ message: result.message, iconType: 'error' });
                    const currentImageUrl = myappPushTabSection.querySelector('input[name="myappImageUrl"]').value;
                    createMyappImageFileInput(currentImageUrl || null);
                    return;
                }

                myappImageFileTagContainer.innerHTML = '';
                createHiddenFileInput(file.name, file, {
                    container: myappImageFileTagContainer,
                    fileInputName: 'myappImageFile',
                });

                // 미리보기 업데이트
                window.preview.setImage(result.data.imageUrl);

                // hidden input에 imageUrl 저장
                myappPushTabSection.querySelector('input[name="myappImageUrl"]').value = result.data.imageUrl;

                // 컴포넌트 재생성 (항상 1개만 표시)
                createMyappImageFileInput(result.data.imageUrl);
            }
        });

        // FileInput(레시피)에는 프리뷰 API가 없으므로 기존 이미지 썸네일 표시는 ImageFileInput에서만
        if (existingImageUrl && !isRecipeContext) {
            myappImageFileInput.setFiles([{ fileName: existingImageUrl.split('/').pop(), fileImageUrl: existingImageUrl }]);
            myappImageFileInput.renderImagePreviews();
        }
    }

    async function validateMyappFiles(file, allowedExtensions = null, maxSize = null) {
        // 파일 확장자 검증
        const fileName = file.name;
        const fileExtension = fileName.split('.').pop().toLowerCase();

        if (allowedExtensions && !allowedExtensions.includes(fileExtension)) {
            NCDSAlert({message: '지원되지 않는 파일 형식입니다.<br>JPG, PNG 파일만 업로드할 수 있습니다.', iconType: 'error'});
            return false;
        }

        // 파일 사이즈 검증
        if (maxSize && file.size > maxSize) {
            const maxSizeMB = (maxSize / (1024 * 1024)).toFixed(1);
            const fileSizeMB = (file.size / (1024 * 1024)).toFixed(2);
            NCDSAlert({
                message: `최대 ${maxSizeMB}MB까지 업로드 가능합니다. (현재 파일: ${fileSizeMB}MB)`,
                iconType: 'error'
            });
            return false;
        }
        return true;
    }

    function setTemplate(templateData) {
        // 푸시 제목 적용
        myappPushTabSection.querySelector('input[name="myappTitle"]').value = templateData.title;
        window.preview.setTitle(templateData.title)

        // 푸시 메시지 적용
        window.messageInput.setValue('');
        window.messageInput.insertText(templateData.message);

        // 라디오버튼 초기화
        ['myappSendCondition', 'myappNotificationType', 'myappPlatform'].forEach(name => {
            const firstRadio = myappPushTabSection.querySelector(`input[name="${name}"]`);
            if (firstRadio) firstRadio.checked = true;
        });

        // 템플릿 불러오기 안내문구
        myappPushTabSection.querySelector('#useTemplateCaution').hidden = false;

        // 초기화
        createMyappImageFileInput();
        myappPushTabSection.querySelector('input[name="myappImageUrl"]').value = '';
        window.preview.setImage(null);

        // 푸시 이미지 적용
        if (templateData.imageUrl) {
            setTimeout(() => {
                createMyappImageFileInput(templateData.imageUrl);
                myappPushTabSection.querySelector('input[name="myappImageUrl"]').value = templateData.imageUrl;
                window.preview.setImage(templateData.imageUrl);
            }, 0);
        }
        myappPushTabSection.querySelector('input[name="myappPushPath"]').value = '';
        myappPushTabSection.querySelector('button[data-click-target="myappPushUrlCheck"]').disabled = true;
        myappPushTabSection.querySelector('input[name="validatePushUrl"]').value = 'n';
        myappPushTabSection.querySelector('input[name="myappUnsubscribeGuide"]').value = '';
        window.preview.setWithdrawalMethod('');

        // 푸시 url 확인
        const mobileUrl = myappPushTabSection.querySelector('#mobileUrl').innerText;
        const pushPathInput = myappPushTabSection.querySelector('input[name="myappPushPath"]');
        pushPathInput.value = templateData.pushUrl.startsWith(mobileUrl) ? templateData.pushUrl.slice(mobileUrl.length) : templateData.pushUrl;
        myappPushTabSection.querySelector('button[data-click-target="myappPushUrlCheck"]').disabled = !pushPathInput.value.replace(/^\s+|\s+$/g, '');
        myappPushTabSection.querySelector('input[name="validatePushUrl"]').value = pushPathInput.value.replace(/^\s+|\s+$/g, '') ? 'y' : 'n';

        if (templateData.pushWithdraw) {
            myappPushTabSection.querySelector('input[name="myappUnsubscribeGuide"]').value = templateData.pushWithdraw;
            window.preview.setWithdrawalMethod(templateData.pushWithdraw);
        }
    }

    $('#myappSendGuide').click(function (e) {
        $.post('./layer_my_app_send_guide.php', null, function (data) {
            ncds_layer_popup({
                message: data,
                title: '광고성 메시지 전송 시 유의사항',
                size: 'normal'
            });
        });
    });
</script>
