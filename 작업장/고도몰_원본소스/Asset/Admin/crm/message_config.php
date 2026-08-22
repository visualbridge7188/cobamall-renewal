<link rel="stylesheet" href="<?= gd_set_browser_cache(PATH_ADMIN_GD_SHARE . 'ncds/css/crm/message-config.css') ?>">
<script defer src="<?= gd_set_browser_cache(PATH_ADMIN_GD_SHARE . 'ncds/script/common/switch.js') ?>"></script>
<script defer src="<?= gd_set_browser_cache(PATH_ADMIN_GD_SHARE . 'ncds/script/common/unsaved-changes-guard.js') ?>"></script>
<script defer src="<?= gd_set_browser_cache(PATH_ADMIN_GD_SHARE . 'ncds/script/common/ncds-jquery-validator.js') ?>"></script>
<article class="ncua-content message-config-wrapper">
    <form id="frmMessageConfig" action="message_config_ps.php" method="post">
        <input type="hidden" name="mode" value="save"/>
        <header class="page-header ncua-page-header js-affix">
            <h3 class="ncua-help-manual">
                <?= end($naviMenu->location) ?>
            </h3>
            <span class="ncua-page-header__actions">
                <button type="submit" class="ncua-btn ncua-btn--md ncua-btn--primary js-btn-save">저장</button>
            </span>
        </header>

        <!-- 기본 설정 섹션 -->
        <section class="ncua-card default-setting">
            <header class="ncua-card__header">
                <h4 class="ncua-card__title">기본 설정</h4>
            </header>
            <section class="ncua-card__body">
                <div class="ncua-table ncua-table--vertical">
                    <table>
                        <tr>
                            <th><div data-tooltip-seq="001">메시지 포인트</div></th>
                            <td>
                                <div id="layerMessagePoint"></div>
                            </td>
                        </tr>
                        <tr>
                            <th><div>발신번호</div></th>
                            <td>
                                <div id="layerCallNumber"></div>
                            </td>
                        </tr>
                        <tr>
                            <th><div data-tooltip-seq="002">메시지 인증번호</div></th>
                            <td>
                                <div id="layerSmsVerification"></div>
                            </td>
                        </tr>
                        <tr class="insuffic-message-point-alert-setting-row">
                            <th><div data-tooltip-seq="003">메시지 포인트 부족 알림</div></th>
                            <td>
                                <div class="ncua-flex ncua-gap-10 ncua-align-items-center">
                                    <div class="ncua-switch ncua-switch--xs">
                                        <label class="ncua-switch__option ncua-switch__option--left ncua-switch__option--active">
                                            <input class="ncua-switch__radio ncua-switch__radio--left" type="radio" value="y" name="messagePointConfig[UsePointLackNotify]" <?= $messagePointConfig->getUsePointLackNotify() ? 'checked' : '' ?>/>
                                            <span class="ncua-switch__label">사용함</span>
                                        </label>
                                        <label class="ncua-switch__option ncua-switch__option--right ncua-switch__option--inactive">
                                            <input class="ncua-switch__radio ncua-switch__radio--right" type="radio" value="n" name="messagePointConfig[UsePointLackNotify]" <?= !$messagePointConfig->getUsePointLackNotify() ? 'checked' : '' ?>/>
                                            <span class="ncua-switch__label">사용안함</span>
                                        </label>
                                    </div>
                                    <div class="ncua-border-content-layout message-config-insuffic-message-point-alert-setting <?= !$messagePointConfig->getUsePointLackNotify() ? 'display-none' : '' ?>">
                                        <div class="ncua-border-content-title">포인트 부족 알림 기준 설정</div>
                                        <div class="ncua-border-content">
                                            <div class="ncua-border-content-form">
                                                <label class="ncua-checkbox-field ncua-checkbox-field--xs has-text ncua-align-items-center point-reach-alarm-setting">
                                                <div>
                                                    <span class="ncua-checkbox-input ncua-checkbox-input--xs ncua-checkbox-field__input">
                                                        <input type="checkbox" name="messagePointConfig[notifyOnThreshold]" id="notifyOnThreshold" value="true" <?= $messagePointConfig->getNotifyOnThreshold() ? 'checked' : '' ?>/>
                                                    </span>
                                                </div>
                                                <span class="ncua-checkbox-field__text ncua-align-items-center ncua-flex ncua-gap-8">
                                                    <span class="ncua-input ncua-input--xs" data-show-hint-text="true">
                                                        <div class="ncua-input__content-wrap message-point-input">
                                                            <div class="ncua-input__field ncua-input__field--xs">
                                                                <input class="point-reach-alarm-input" type="text" name="messagePointConfig[pointLackNotifyThreshold]" id="pointLackNotifyThreshold" data-maxlength="6" maxlength="6" value="<?= $messagePointConfig->getPointLackNotifyThreshold() ?>" oninput="this.value = this.value.replace(/[^0-9]/g, '')"/>
                                                            </div>
                                                            <span class="point-reach-alarm-text">포인트 도달 시</span>
                                                        </div>
                                                    </span>
                                                </span>
                                                </label>
                                                 <div class="ncua-checkbox-field__support-text insuffic-message-point-alert-support-text insuffic-message-point-threshold-alert-support-text no-padding display-none">알림을 받은 포인트를 입력해 주세요.</div>
                                            </div>
                                            <div class="ncua-border-content-form">
                                                <label class="ncua-checkbox-field ncua-checkbox-field--xs has-text ncua-align-items-center flex-start">
                                                    <span class="ncua-checkbox-input ncua-checkbox-input--xs ncua-checkbox-field__input">
                                                        <input type="checkbox" name="messagePointConfig[notifyOnSendFail]" id="notifyOnSendFail" value="true" <?= $messagePointConfig->getNotifyOnSendFail() ? 'checked' : '' ?>/>
                                                    </span>
                                                    <span>
                                                        <span class="ncua-checkbox-field__text ncua-align-items-center ncua-flex ncua-gap-8">
                                                            <span>발송 실패 시</span>
                                                        </span>
                                                        <span class="ncua-checkbox-field__support-text">발송 실패(부분/전체)가 발생</span>
                                                    </span>
                                                </label>
                                            </div>
                                        </div>
                                        <div class="ncua-checkbox-field__support-text insuffic-message-point-alert-support-text insuffic-message-point-send-failed-alert-support-text no-padding display-none">알림 기준을 선택해 주세요.</div>
                                    </div>
                                </div>
                            </td>
                        </tr>
                        <tr class="cloud-kakao-talk-channel-setting-row">
                            <th><div<?= $isKakaoAlrimAvailable || $isKakaoAlrimLunaInstalled ? ' data-tooltip-seq="004"' : '' ?>>카카오톡 채널</div></th>
                            <td>
                                <div id="layerCloudKakaoTalkChannel"></div>
                            </td>
                        </tr>
                    </table>
                </div>
            </section>
        </section>

        <!-- SMS 섹션 -->
        <section class="ncua-card sms-setting">
            <header class="ncua-card__header">
                <h4 class="ncua-card__title">SMS</h4>
            </header>
            <section class="ncua-card__body ">
                <div class="ncua-table ncua-table--vertical">
                    <table>
                        <tr>
                            <th><div data-tooltip-seq="018">자동알림 90byte 초과시 전송 방법</div>
                            </th>
                            <td>
                                <div class="ncua-radio-group ncua-flex ncua-flex-gap">
                                    <?php foreach ($smsAutoSendOverTypes as $smsAutoSendOverType): ?>
                                        <label class="ncua-radio-field ncua-radio-field--xs has-text">
                                            <span class="ncua-radio-input ncua-radio-input--xs ncua-radio-field__input">
                                                <input type="radio" name="smsAutoConfig[smsAutoSendOver]" value="<?= $smsAutoSendOverType->value ?>" <?= $smsAutoConfig->getSmsAutoSendOver() === $smsAutoSendOverType ? 'checked' : '' ?>/>
                                            </span>
                                            <span>
                                                <span class="ncua-radio-field__text"><?= $smsAutoSendOverType->description() ?></span>
                                                <span class="ncua-checkbox-field__support-text"><?= $smsAutoSendOverType->subDescription() ?></span>
                                            </span>
                                        </label>
                                    <?php endforeach; ?>
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <?php if ($smsRejectPolicy['rejectNumber'] != '' && $smsRejectPolicy['status'] == 'O'): ?>
                                <th><div data-tooltip-seq="005">080 수신거부 번호</div></th>
                                <td>
                                    <div class="ncua-flex ncua-flex-gap">
                                        <div class="ncua-switch ncua-switch--xs">
                                            <label class="ncua-switch__option ncua-switch__option--left ncua-switch__option--active">
                                                <input class="ncua-switch__radio ncua-switch__radio--left" type="radio" value="y" name="smsRejectUse" <?= $smsRejectPolicy['use'] == 'y' ? 'checked' : '' ?>/>
                                                <span class="ncua-switch__label">사용함</span>
                                            </label>
                                            <label class="ncua-switch__option ncua-switch__option--right ncua-switch__option--inactive">
                                                <input class="ncua-switch__radio ncua-switch__radio--right" type="radio" value="n" name="smsRejectUse" <?= $smsRejectPolicy['use'] != 'y' ? 'checked' : '' ?>/>
                                                <span class="ncua-switch__label">사용안함</span>
                                            </label>
                                        </div>

                                        <div class="ncua-border-content-layout">
                                            <div class="ncua-border-content sms-reject-number-info">
                                                <div class="ncua-border-content-title">수신거부 번호 정보</div>
                                                <div class="sms-reject-number"><?= $smsRejectPolicy['rejectNumber'] ?></div>
                                                <div class="sms-reject-number-status">
                                                    <span>개통완료</span>
                                                    <span>(개통일 : <?= $smsRejectPolicy['date'] ?>)</span>
                                                </div>
                                            </div>
                                            <div class="ncua-divider ncua-divider--text ncua-divider--single-line">
                                                <div class="ncua-divider__line"></div>
                                            </div>
                                            <div class="ncua-flex ncua-gap-8 ncua-align-items-center">
                                                <button type="button" id="viewRejectNumberList" class="ncua-btn ncua-btn--xs ncua-btn--secondary-gray">
                                                    <span class="ncua-btn__label">수신거부한 번호 리스트</span>
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </td>
                            <?php else: ?>
                                <th><div data-tooltip-seq="016">080 수신거부 번호</div></th>
                                <td>
                                    <div class="ncua-flex ncua-gap-8 ncua-align-items-center">
                                        <span>광고성 SMS 발송을 위해 080 수신거부 번호를 신청하세요.</span>
                                        <button type="button" id="manageSms080Reject" class="ncua-btn ncua-btn--xs ncua-btn--secondary-gray">
                                            <span class="ncua-btn__label">080 수신거부 사용 신청</span>
                                        </button>
                                    </div>
                                </td>
                            <?php endif; ?>
                        </tr>
                    </table>
                </div>
            </section>
            <?php if ($smsRejectPolicy['rejectNumber'] != '' && $smsRejectPolicy['status'] == 'O' && $smsRejectPolicy['use'] == 'y'): ?>
                <section class="ncua-card__body accordion-section">
                    <details class="ncua-accordion ncua-accordion--gray" open>
                        <summary>080 수신거부 번호 안내</summary>
                        <div class="ncua-flex ncua-gap-8 ncua-flex-column">
                            <ul class="ncua-accordion__content">
                                <li class="ncua-notice-info">080 수신거부 번호 사용기간 만료 시, 신규 신청 방법</li>
                                <li class="ncua-indent-1">1. <a href="https://apps.nhn-commerce.com/apps/419" target="_blank">080 수신거부 사용 신청 페이지</a> 접속</li>
                                <li class="ncua-indent-1">2. [신청하기] 버튼 클릭 후 신청 및 결제</li>
                            </ul>
                            <ul class="ncua-accordion__content">
                                <li class="ncua-notice-info">080 수신거부 기간연장(추가 결제) 방법</li>
                                <li class="ncua-indent-1">1. NHN 커머스 홈페이지 > 마이페이지 > 쇼핑몰 관리 > 부가서비스 신청 관리 > 080 수신거부 페이지 접속</li>
                                <li class="ncua-indent-1">2. [관리] 버튼 클릭</li>
                                <li class="ncua-indent-1">3. [결제조회 및 납부] 버튼 클릭 후 연장 및 결제</li>
                            </ul>
                        </div>
                    </details>
                </section>
            <?php endif; ?>
        </section>

        <!-- 카카오 친구톡 섹션 -->
        <section class="ncua-card kakao-friend-talk-setting">
            <header class="ncua-card__header">
                <h4 class="ncua-card__title">카카오 친구톡</h4>
            </header>
            <section class="ncua-card__body">
                <div class="ncua-table ncua-table--vertical">
                    <table>
                        <tr>
                            <th><div>사용 설정</div></th>
                            <td>
                                <div class="ncua-flex ncua-gap-8 ncua-align-items-center">
                                    <div class="ncua-switch ncua-switch--xs">
                                        <label class="ncua-switch__option ncua-switch__option--left ncua-switch__option--active">
                                            <input class="ncua-switch__radio ncua-switch__radio--left" type="radio" value="y" name="kakaoFriendTalkConfig[useFlag]" <?= $kakaoFriendTalkConfig->getUseFlag() === 'y' ? 'checked' : '' ?>/>
                                            <span class="ncua-switch__label">사용함</span>
                                        </label>
                                        <label class="ncua-switch__option ncua-switch__option--right ncua-switch__option--inactive">
                                            <input class="ncua-switch__radio ncua-switch__radio--right" type="radio" value="n" name="kakaoFriendTalkConfig[useFlag]" <?= $kakaoFriendTalkConfig->getUseFlag() === 'n' ? 'checked' : '' ?>/>
                                            <span class="ncua-switch__label">사용안함</span>
                                        </label>
                                    </div>
                                </div>
                            </td>
                        </tr>
                        <tr class="setting-data message-link-move-setting <?= $kakaoFriendTalkConfig->getUseFlag() != 'y' ? 'display-none' : '' ?>">
                            <th>
                                <div data-tooltip-seq="006">메시지 링크 이동 설정</div>
                            </th>
                            <td>
                                <div class="ncua-flex ncua-gap-8 ncua-flex-column">
                                    <div class="ncua-flex ncua-flex-gap">
                                        <?php foreach ($linkPlatformTypes as $linkPlatformType): ?>
                                            <label class="ncua-radio-field ncua-radio-field--xs has-text">
                                                <span class="ncua-radio-input ncua-radio-input--xs ncua-radio-field__input">
                                                    <input type="radio" name="kakaoFriendTalkConfig[linkPlatformType]" value="<?= $linkPlatformType->name ?>" <?= $kakaoFriendTalkConfig->getLinkPlatformType() === $linkPlatformType ? 'checked' : '' ?>/>
                                                </span>
                                                <span class="ncua-radio-field__text"><?= $linkPlatformType->description() ?></span>
                                            </label>
                                        <?php endforeach; ?>
                                    </div>
                                    <div class="ncua-flex ncua-gap-8 ncua-align-items-center">
                                        <div data-click-target="view-message-link-type" class="ncua-link ncua-link--xs">WEB APP 링크 차이 보기</div>
                                    </div>
                                </div>
                            </td>
                        </tr>
                        <tr class="setting-data kakao-friend-talk-sms-alternative-send-setting <?= $kakaoFriendTalkConfig->getUseFlag() !== 'y' ? 'display-none' : '' ?>">
                            <th><div data-tooltip-seq="007">발송 실패 시, SMS 대체 발송</div></th>
                            <td>
                                <div class="ncua-flex ncua-gap-8 ncua-align-items-center">
                                    <div class="ncua-switch ncua-switch--xs">
                                        <label class="ncua-switch__option ncua-switch__option--left ncua-switch__option--active">
                                            <input class="ncua-switch__radio ncua-switch__radio--left" type="radio" value="y" name="kakaoFriendTalkConfig[smsAlternativeSendFlag]" <?= $kakaoFriendTalkConfig->getSmsAlternativeSendFlag() === 'y' ? 'checked' : '' ?>/>
                                            <span class="ncua-switch__label">사용함</span>
                                        </label>
                                        <label class="ncua-switch__option ncua-switch__option--right ncua-switch__option--inactive">
                                            <input class="ncua-switch__radio ncua-switch__radio--right" type="radio" value="n" name="kakaoFriendTalkConfig[smsAlternativeSendFlag]" <?= $kakaoFriendTalkConfig->getSmsAlternativeSendFlag() === 'n' ? 'checked' : '' ?>/>
                                            <span class="ncua-switch__label">사용안함</span>
                                        </label>
                                    </div>
                                </div>
                            </td>
                        </tr>
                    </table>
                </div>
            </section>
            <section class="ncua-card__body accordion-section setting-data<?= $kakaoFriendTalkConfig->getUseFlag() !== 'y' ? ' display-none' : '' ?>">
                <details class="ncua-accordion ncua-accordion--gray" open>
                    <summary>친구톡 발송 전 확인사항</summary>
                    <ul class="ncua-accordion__content">
                        <li class="ncua-notice-info">'기본설정 > 기본 정보 설정'에 올바른 쇼핑몰 도메인이 등록되었는지 확인해 주세요.</li>
                        <li class="ncua-notice-info">발신 프로필키가 발급되고 1시간이 지난 후 발송하세요.</li>
                        <li class="ncua-notice-info">친구톡 1건당 1.4 포인트가 필요합니다. 메시지 포인트가 충분한지 확인해 주세요.</li>
                    </ul>
                </details>
            </section>
        </section>

        <!-- 카카오 알림톡 섹션-->
        <section class="ncua-card kakao-alrim-talk-setting">
            <header class="ncua-card__header">
                <h4 class="ncua-card__title">카카오 알림톡</h4>
            </header>
            <section class="ncua-card__body">
                <div class="ncua-table ncua-table--vertical">
                    <table>
                        <tr>
                            <th><div>사용 설정</div></th>
                            <td>
                                <div class="ncua-flex ncua-gap-8 ncua-align-items-center">
                                    <div class="ncua-switch ncua-switch--xs">
                                        <label class="ncua-switch__option ncua-switch__option--left ncua-switch__option--active">
                                            <input class="ncua-switch__radio ncua-switch__radio--left" type="radio" value="y" name="kakaoAlimTalkConfig[useFlag]" <?= $kakaoAlimTalkConfig->getUseFlag() === 'y' ? 'checked' : '' ?>/>
                                            <span class="ncua-switch__label">사용함</span>
                                        </label>
                                        <label class="ncua-switch__option ncua-switch__option--right ncua-switch__option--inactive">
                                            <input class="ncua-switch__radio ncua-switch__radio--right" type="radio" value="n" name="kakaoAlimTalkConfig[useFlag]" <?= $kakaoAlimTalkConfig->getUseFlag() === 'n' ? 'checked' : '' ?>/>
                                            <span class="ncua-switch__label">사용안함</span>
                                        </label>
                                    </div>
                                </div>
                            </td>
                        </tr>
                        <tr class="setting-data kakao-alim-talk-sender <?= (!$isKakaoAlrimAvailable && !$isKakaoAlrimLunaInstalled) || $kakaoAlimTalkConfig->getUseFlag() !== 'y' ? 'display-none' : '' ?>">
                            <th><div>알림톡 발송 채널</div></th>
                            <td>
                                <div class="ncua-flex ncua-gap-8 ncua-flex-column sender-setting">
                                    <div class="ncua-flex ncua-flex-gap">
                                        <?php foreach ($kakaoAlimTalkSenders as $kakaoAlimTalkSender): ?>
                                            <label class="ncua-radio-field ncua-radio-field--xs has-text <?= ($kakaoAlimTalkSender->value === 'kakaoAlrim' && !$isKakaoAlrimAvailable) || ($kakaoAlimTalkSender->value === 'kakaoAlrimLuna' && !$isKakaoAlrimLunaInstalled) ? 'display-none' : '' ?>">
                                                <span class="ncua-radio-input ncua-radio-input--xs ncua-radio-field__input">
                                                    <input type="radio" name="kakaoAlimTalkConfig[sender]" value="<?= $kakaoAlimTalkSender->value ?>" <?= $kakaoAlimTalkConfig->getSender()->value === $kakaoAlimTalkSender->value ? 'checked' : '' ?>/>
                                                </span>
                                                <span class="ncua-radio-field__text"><?= $kakaoAlimTalkSender->description() ?></span>
                                            </label>
                                        <?php endforeach; ?>
                                    </div>
                                    <div class="ncua-flex ncua-gap-8 ncua-align-items-center <?= $kakaoAlimTalkConfig->getSender()->value !== 'kakaoAlrimCloud' ? 'display-none' : '' ?>" data-kakao-alim-talk-sender="kakaoAlrimCloud">
                                        <span class="js-cloud-kakao-talk-msg">기본 설정에서 카카오톡 채널을 등록하세요.</span>
                                    </div>
                                    <div class="ncua-flex ncua-gap-8 ncua-flex-column bizm-channel-setting <?= $kakaoAlimTalkConfig->getSender()->value !== 'kakaoAlrim' ? 'display-none' : '' ?>" data-kakao-alim-talk-sender="kakaoAlrim">
                                        <div id="layerBizmKakaoTalkChannel"></div>
                                    </div>
                                    <div class="ncua-flex ncua-gap-8 ncua-align-items-center bloomai-channel-setting <?= $kakaoAlimTalkConfig->getSender()->value !== 'kakaoAlrimLuna' ? 'display-none' : '' ?>" data-kakao-alim-talk-sender="kakaoAlrimLuna">
                                        <div id="layerLunaKakaoTalkChannel"></div>
                                    </div>
                                </div>
                            </td>
                        </tr>
                        <tr class="setting-data kakao-alim-talk-auto-sms-alternative-send-setting <?= $kakaoAlimTalkConfig->getUseFlag() !== 'y' || $kakaoAlimTalkConfig->getSender()->value === 'kakaoAlrimLuna' ? 'display-none' : '' ?>">
                            <th>
                                <div data-tooltip-seq="010">발송 실패 시, SMS 대체 발송</div>
                            </th>
                            <td>
                                <div class="ncua-flex ncua-flex-gap">
                                    <div class="ncua-flex ncua-gap-8 ncua-align-items-center kakao-alim-talk-manual-sms-alternative-send-setting <?= $kakaoAlimTalkConfig->getSender()->value === 'kakaoAlrim' ? 'display-none' : '' ?>">
                                        모바일 메시지 발송
                                        <div class="ncua-switch ncua-switch--xs">
                                            <label class="ncua-switch__option ncua-switch__option--left ncua-switch__option--active">
                                                <input class="ncua-switch__radio ncua-switch__radio--left" type="radio" value="y" name="kakaoAlimTalkConfig[manualSmsAlternativeSendFlag]" <?= $kakaoAlimTalkConfig->getManualSmsAlternativeSendFlag() === 'y' ? 'checked' : '' ?>/>
                                                <span class="ncua-switch__label">사용함</span>
                                            </label>
                                            <label class="ncua-switch__option ncua-switch__option--right ncua-switch__option--inactive">
                                                <input class="ncua-switch__radio ncua-switch__radio--right" type="radio" value="n" name="kakaoAlimTalkConfig[manualSmsAlternativeSendFlag]" <?= $kakaoAlimTalkConfig->getManualSmsAlternativeSendFlag() === 'n' ? 'checked' : '' ?>/>
                                                <span class="ncua-switch__label">사용안함</span>
                                            </label>
                                        </div>
                                    </div>
                                    <div class="ncua-divider ncua-divider--text ncua-divider--single-line kakao-alim-talk-manual-sms-alternative-send-setting <?= $kakaoAlimTalkConfig->getSender()->value === 'kakaoAlrim' ? 'display-none' : '' ?>">
                                        <div class="ncua-divider__line"></div>
                                    </div>
                                    <div class="ncua-flex ncua-gap-8 ncua-align-items-center">
                                        자동 알림 발송
                                        <div class="ncua-switch ncua-switch--xs">
                                            <label class="ncua-switch__option ncua-switch__option--left ncua-switch__option--active">
                                                <input class="ncua-switch__radio ncua-switch__radio--left" type="radio" value="y" name="kakaoAlimTalkConfig[autoSmsAlternativeSendFlag]" <?= $kakaoAlimTalkConfig->getAutoSmsAlternativeSendFlag() === 'y' ? 'checked' : '' ?>>
                                                <span class="ncua-switch__label">사용함</span>
                                            </label>
                                            <label class="ncua-switch__option ncua-switch__option--right ncua-switch__option--inactive">
                                                <input class="ncua-switch__radio ncua-switch__radio--right" type="radio" value="n" name="kakaoAlimTalkConfig[autoSmsAlternativeSendFlag]" <?= $kakaoAlimTalkConfig->getAutoSmsAlternativeSendFlag() === 'n' ? 'checked' : '' ?>>
                                                <span class="ncua-switch__label">사용안함</span>
                                            </label>
                                        </div>
                                    </div>
                                    <div class="ncua-border-content-layout kakao-alim-talk-alternative-sms-setting <?= $kakaoAlimTalkConfig->getUseFlag() !== 'y' || $kakaoAlimTalkConfig->getAutoSmsAlternativeSendFlag() !== 'y' ? 'display-none' : '' ?>" data-kakao-alim-talk-auto-sms-alternative-send-flag="y">
                                        <div class="ncua-flex ncua-gap-4 ncua-flex-column">
                                            <div class="ncua-flex ncua-align-items-center" data-tooltip-seq="017">대체발송 처리 방법</div>
                                            <div class="ncua-radio-group ncua-flex ncua-flex-gap">
                                                <?php foreach ($autoSmsAlternativeSendTypes as $autoSmsAlternativeSendType): ?>
                                                    <label class="ncua-radio-field ncua-radio-field--xs has-text">
                                                        <span class="ncua-radio-input ncua-radio-input--xs ncua-radio-field__input">
                                                            <input type="radio" name="kakaoAlimTalkConfig[autoSmsAlternativeSendType]" value="<?= $autoSmsAlternativeSendType->name ?>" <?= $kakaoAlimTalkConfig->getAutoSmsAlternativeSendType() === $autoSmsAlternativeSendType ? 'checked' : '' ?>/>
                                                        </span>
                                                        <span class="ncua-radio-field__text"><?= $autoSmsAlternativeSendType->description() ?></span>
                                                    </label>
                                                <?php endforeach; ?>
                                            </div>
                                        </div>
                                        <div class="ncua-flex ncua-gap-4 ncua-flex-column <?= $kakaoAlimTalkConfig->getUseFlag() !== 'y' || $kakaoAlimTalkConfig->getAutoSmsAlternativeSendFlag() !== 'y' || $kakaoAlimTalkConfig->getAutoSmsAlternativeSendType()->name !== 'FAILED_MESSAGE' ? 'display-none' : '' ?>" data-kakao-alim-talk-auto-sms-alternative-send-type="FAILED_MESSAGE">
                                            <div class="ncua-flex ncua-align-items-center" data-tooltip-seq="014">90 byte 초과 시 메시지 전송 방법</div>
                                            <div class="ncua-radio-group ncua-flex ncua-flex-gap">
                                                <?php foreach ($failedMessageSendTypes as $failedMessageSendType): ?>
                                                    <label class="ncua-radio-field ncua-radio-field--xs has-text">
                                                        <span class="ncua-radio-input ncua-radio-input--xs ncua-radio-field__input">
                                                            <input type="radio" name="kakaoAlimTalkConfig[failedMessageSendType]" value="<?= $failedMessageSendType->name ?>" <?= $kakaoAlimTalkConfig->getFailedMessageSendType() === $failedMessageSendType ? 'checked' : '' ?>/>
                                                        </span>
                                                        <span>
                                                            <span class="ncua-radio-field__text"><?= $failedMessageSendType->description() ?></span>
                                                            <span class="ncua-checkbox-field__support-text"><?= $failedMessageSendType->subDescription() ?></span>
                                                        </span>
                                                    </label>
                                                <?php endforeach; ?>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </td>
                        </tr>
                    </table>
                </div>
            </section>
            <section class="ncua-card__body accordion-section setting-data<?= $kakaoAlimTalkConfig->getUseFlag() !== 'y' ? ' display-none' : '' ?>">
                <details class="ncua-accordion ncua-accordion--gray" open>
                    <summary>알림톡 발송 전 확인사항</summary>
                    <div class="ncua-flex ncua-flex-gap ncua-flex-column">
                        <ul class="ncua-accordion__content">
                            <li class="ncua-notice-info">'기본설정 > 기본 정보 설정'에 올바른 쇼핑몰 도메인이 등록되었는지 확인해 주세요.</li>
                            <li class="ncua-notice-info">발신 프로필키가 발급되고 1시간이 지난 후 발송하세요.</li>
                            <li class="ncua-notice-info">알림톡 1건당 0.6 포인트가 필요합니다. 메시지 포인트가 충분한지 확인해 주세요.</li>
                        </ul>
                        <?php if ($isKakaoAlrimAvailable || $isKakaoAlrimLunaInstalled): ?>
                            <div class="ncua-flex ncua-gap-8 ncua-flex-column">
                                <div class="ncua-accordion__content ncua-accordion__summary-style">알림톡 제공사별 이용 안내</div>
                                <ul class="ncua-accordion__content">
                                    <li class="ncua-notice-info">카카오 알림톡은 고도몰, 비즈엠, 블룸에이아이 중 한 곳만 선택하여 사용할 수 있습니다.</li>
                                    <li class="ncua-notice-info">고도몰·비즈엠 알림톡: 카카오톡 채널 등록 후 사용 가능, 비용은 고도몰 SMS 발송 건수에서 차감됩니다.</li>
                                    <?php if ($isKakaoAlrimLunaInstalled): ?>
                                        <li class="ncua-notice-info">블룸에이아이 알림톡: 블룸에이아이 회원가입 후 사용 가능하며, 비용은 블룸에이아이에서 청구됩니다.</li>
                                        <li class="ncua-notice-info">블룸에이아이 알림톡</li>
                                        <li class="ncua-notice-info ncua-indent-1">블룸에이아이 회원가입 후 사용 가능하며, 비용은 블룸에이아이에서 청구됩니다.</li>
                                        <li class="ncua-notice-info ncua-indent-1"><a href="https://bizmsg.blumn.ai/" target="_blank">블룸에이아이 알림톡 발신번호</a>와 고도몰 발신번호가 동일해야 알림톡이 발송됩니다.</li>
                                    <?php endif; ?>
                                </ul>
                            </div>
                        <?php endif; ?>
                    </div>
                </details>
            </section>
        </section>

        <!-- 앱푸시 섹션 -->
        <section class="ncua-card mobile-app-push-setting">
            <header class="ncua-card__header">
                <h4 class="ncua-card__title">앱푸시</h4>
            </header>
            <section class="ncua-card__body">
                <div class="ncua-table ncua-table--vertical">
                    <table>
                        <tr>
                            <th><div>사용 설정</div></th>
                            <td>
                                <div class="ncua-flex ncua-gap-8 ncua-align-items-center">
                                    <div class="ncua-switch ncua-switch--xs">
                                        <label class="ncua-switch__option ncua-switch__option--left ncua-switch__option--active">
                                            <input class="ncua-switch__radio ncua-switch__radio--left" type="radio" value="y" name="myappPushConfig[useFlag]" <?= $myappPushConfig->getUseFlag() === 'y' ? 'checked' : '' ?>/>
                                            <span class="ncua-switch__label">사용함</span>
                                        </label>
                                        <label class="ncua-switch__option ncua-switch__option--right ncua-switch__option--inactive">
                                            <input class="ncua-switch__radio ncua-switch__radio--right" type="radio" value="n" name="myappPushConfig[useFlag]" <?= $myappPushConfig->getUseFlag() === 'n' ? 'checked' : '' ?>/>
                                            <span class="ncua-switch__label">사용안함</span>
                                        </label>
                                    </div>
                                </div>
                            </td>
                        </tr>
                        <tr class="setting-data <?= $myappPushConfig->getUseFlag() !== 'y' ? 'display-none' : '' ?>">
                            <th>
                                <div data-tooltip-seq="012">발송 실패 시, SMS 대체 발송</div>
                            </th>
                            <td>
                                <div class="ncua-flex ncua-flex-gap">
                                    <div class="ncua-flex ncua-gap-8 ncua-align-items-center">
                                        모바일 메시지 발송
                                        <div class="ncua-switch ncua-switch--xs">
                                            <label class="ncua-switch__option ncua-switch__option--left ncua-switch__option--active">
                                                <input class="ncua-switch__radio ncua-switch__radio--left" type="radio" value="y" name="myappPushConfig[manualSmsAlternativeSendFlag]" <?= $myappPushConfig->getManualSmsAlternativeSendFlag() === 'y' ? 'checked' : '' ?>/>
                                                <span class="ncua-switch__label">사용함</span>
                                            </label>
                                            <label class="ncua-switch__option ncua-switch__option--right ncua-switch__option--inactive">
                                                <input class="ncua-switch__radio ncua-switch__radio--right" type="radio" value="n" name="myappPushConfig[manualSmsAlternativeSendFlag]" <?= $myappPushConfig->getManualSmsAlternativeSendFlag() === 'n' ? 'checked' : '' ?>/>
                                                <span class="ncua-switch__label">사용안함</span>
                                            </label>
                                        </div>
                                    </div>
                                    <div class="ncua-divider ncua-divider--text ncua-divider--single-line">
                                        <div class="ncua-divider__line"></div>
                                    </div>
                                    <div class="ncua-flex ncua-gap-8 ncua-align-items-center">
                                        자동 알림 발송
                                        <div class="ncua-switch ncua-switch--xs">
                                            <label class="ncua-switch__option ncua-switch__option--left ncua-switch__option--active">
                                                <input class="ncua-switch__radio ncua-switch__radio--left" type="radio" value="y" name="myappPushConfig[autoSmsAlternativeSendFlag]" <?= $myappPushConfig->getAutoSmsAlternativeSendFlag() === 'y' ? 'checked' : '' ?>/>
                                                <span class="ncua-switch__label">사용함</span>
                                            </label>
                                            <label class="ncua-switch__option ncua-switch__option--right ncua-switch__option--inactive">
                                                <input class="ncua-switch__radio ncua-switch__radio--right" type="radio" value="n" name="myappPushConfig[autoSmsAlternativeSendFlag]" <?= $myappPushConfig->getAutoSmsAlternativeSendFlag() === 'n' ? 'checked' : '' ?>/>
                                                <span class="ncua-switch__label">사용안함</span>
                                            </label>
                                        </div>
                                    </div>
                                    <div class="ncua-border-content-layout myapp-push-alternative-sms-setting <?= $myappPushConfig->getUseFlag() !== 'y' || $myappPushConfig->getAutoSmsAlternativeSendFlag() !== 'y' ? 'display-none' : '' ?>" data-myapp-push-auto-sms-alternative-send-flag="y">
                                        <div class="ncua-flex ncua-gap-4 ncua-flex-column">
                                            <div class="ncua-flex ncua-align-items-center" data-tooltip-seq="013">대체발송 처리 방법</div>
                                            <div class="ncua-radio-group ncua-flex ncua-flex-gap">
                                                <?php foreach ($autoSmsAlternativeSendTypes as $autoSmsAlternativeSendType): ?>
                                                    <label class="ncua-radio-field ncua-radio-field--xs has-text">
                                                    <span class="ncua-radio-input ncua-radio-input--xs ncua-radio-field__input">
                                                        <input type="radio" name="myappPushConfig[autoSmsAlternativeSendType]" value="<?= $autoSmsAlternativeSendType->name ?>" <?= $myappPushConfig->getAutoSmsAlternativeSendType() === $autoSmsAlternativeSendType ? 'checked' : '' ?>/>
                                                    </span>
                                                        <span class="ncua-radio-field__text"><?= $autoSmsAlternativeSendType->description() ?></span>
                                                    </label>
                                                <?php endforeach; ?>
                                            </div>
                                        </div>
                                        <div class="ncua-flex ncua-gap-4 ncua-flex-column <?= $myappPushConfig->getUseFlag() !== 'y' || $myappPushConfig->getAutoSmsAlternativeSendFlag() !== 'y' || $myappPushConfig->getAutoSmsAlternativeSendType()->name !== 'FAILED_MESSAGE' ? 'display-none' : '' ?>" data-myapp-push-auto-sms-alternative-send-type="FAILED_MESSAGE">
                                            <div class="ncua-flex ncua-align-items-center" data-tooltip-seq="014">90 byte 초과 시 메시지 전송 방법</div>
                                            <div class="ncua-radio-group ncua-flex ncua-flex-gap">
                                                <?php foreach ($failedMessageSendTypes as $failedMessageSendType): ?>
                                                    <label class="ncua-radio-field ncua-radio-field--xs has-text">
                                                        <span class="ncua-radio-input ncua-radio-input--xs ncua-radio-field__input">
                                                        <input type="radio" name="myappPushConfig[failedMessageSendType]" value="<?= $failedMessageSendType->name ?>" <?= $myappPushConfig->getFailedMessageSendType() == $failedMessageSendType ? 'checked' : '' ?>/>
                                                    </span>
                                                        <span>
                                                            <span class="ncua-radio-field__text"><?= $failedMessageSendType->description() ?></span>
                                                            <span class="ncua-checkbox-field__support-text"><?= $failedMessageSendType->subDescription() ?></span>
                                                        </span>
                                                    </label>
                                                <?php endforeach; ?>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </td>
                        </tr>
                    </table>
                </div>
            </section>
        </section>
    </form>
</article>

<script type="text/javascript">
    const isKakaoAlrimAvailable = <?= json_encode($isKakaoAlrimAvailable) ?>;
    const isKakaoAlrimLunaInstalled = <?= json_encode($isKakaoAlrimLunaInstalled) ?>;
    const isMyappInstalled = <?= json_encode($isMyappInstalled) ?>;
    const isMyappReleased = <?= json_encode($isMyappReleased) ?>;
    /**
     * 기본 설정 영역
     */
    function defaultSetting() {
        loadMessagePointLayer();
        loadCallNumberLayer();
        loadSmsVerificationLayer();
        loadCloudKakaoTalkChannelLayer();

        if (isKakaoAlrimAvailable) {
            loadBizmKakaoTalkChannelLayer();
        }

        if (isKakaoAlrimLunaInstalled) {
            loadLunaKakaoTalkChannelLayer();
        }

        const settingSection = document.querySelector('.default-setting');

        // 포인트 부족 알림 설정 사용함/사용안함에 따른 설정 영역 노출 처리
        settingSection.querySelectorAll('input[name="messagePointConfig[UsePointLackNotify]"]')?.forEach?.((input) => {
            input.addEventListener('change', function (e) {
                e.preventDefault();
                settingSection.querySelector('.message-config-insuffic-message-point-alert-setting').classList.toggle('display-none', input.value !== 'y');
            });

            input.addEventListener('click', function (e) {
                if (input.value !== 'y') {
                    e.preventDefault();
                    NCDSConfirm({
                        message: '메시지 발송 실패 시 알림을 받지 않겠습니까?',
                        subMessage: '메시지 포인트가 부족하면 자동 알림, 반복발송 등이 발송되지 않습니다.',
                        btnText: {
                            confirmLabel: '확인',
                            cancelLabel: '취소'
                        },
                        callback: (result) => {
                            input.checked = true;
                            if (result) e.target.dispatchEvent(new Event('change', { bubbles: true }));
                        }
                    });
                } else {
                    e.target.dispatchEvent(new Event('change', { bubbles: true }));
                }
            });
        });
    }

    /**
     * SMS 설정 영역
     */
    function smsSetting() {
        // SMS > 080 수신거부 관리
        document.querySelector('#manageSms080Reject')?.addEventListener('click', function (e) {
            e.preventDefault();
            window.open('../service/service_info.php?menu=consulting_refusal_info', '_blank');
        });

        // SMS > 수신거부한 번호 리스트 보기
        document.querySelector('#viewRejectNumberList')?.addEventListener('click', function (e) {
            e.preventDefault();
            $.post('./message_config/layer_reject_number_list.php', null, function (data) {
                ncds_layer_popup({ message: data, title: '수신거부한 번호 리스트', size: 'wide-sm' });
            });
        });
    }

    /**
     * 카카오 친구톡 사용 설정 영역
     */
    function kakaoFriendTalkSetting() {
        const settingSection = document.querySelector('.kakao-friend-talk-setting');
        if (!settingSection) return;

        settingSection.querySelectorAll('input[name="kakaoFriendTalkConfig[useFlag]"]').forEach(input => {
            input.addEventListener('change', function (e) {
                e.preventDefault();
                settingSection.querySelectorAll('.setting-data').forEach(row => {
                    row.classList.toggle('display-none', input.value !== 'y');
                });
            });

            input.addEventListener('click', function (e) {
                if (input.value === 'y') {
                    if (!document.getElementById('registeredCloudKakaoTalkChannelCard')) {
                        NCDSAlert({ message: '친구톡을 사용하려면 카카오톡 채널이 필요합니다.<br>상단에서 카카오톡 채널을 먼저 등록해 주세요.', iconType: 'error' });
                        e.preventDefault();
                    } else {
                        e.target.dispatchEvent(new Event('change', { bubbles: true }));
                    }
                } else {
                    e.preventDefault();
                    NCDSConfirm({
                        message: '카카오 친구톡을 사용 안함으로 변경하시겠습니까?',
                        subMessage: '카카오 친구톡으로 등록한 예약/반복발송이 발송되지 않습니다.<br>※ 발송일시가 5분 이내인 발송건은 그대로 발송됩니다.',
                        btnText: {
                            confirmLabel: '확인',
                            cancelLabel: '취소'
                        },
                        callback: (result) => {
                            input.checked = true;
                            if (result) e.target.dispatchEvent(new Event('change', { bubbles: true }));
                        }
                    });
                }
            });
        });

        settingSection.querySelector('input[name="kakaoFriendTalkConfig[smsAlternativeSendFlag]"][value=y]').addEventListener('click', function (e) {
            if (!document.getElementById('registeredCallNumberCard')) {
                NCDSAlert({message: '발신번호 설정 후 대체발송을 사용할 수 있습니다.', iconType: 'error' });
                e.preventDefault();
                return;
            }

            if (document.getElementById('mismatchedSmsPasswordCard')) {
                NCDSAlert({ message: '등록된 인증번호 정보가 일치하지 않아 대체발송을 사용할 수 없습니다.', subMessage: '어드민과 NHN 커머스(마이페이지)의 인증번호를 동일하게 설정한 뒤 다시 시도해 주세요.', iconType: 'error' });
                e.preventDefault();
            } else if (!document.getElementById('registeredSmsPasswordCard')) {
                NCDSAlert({ message: '메시지 인증번호 설정 후 대체발송을 사용할 수 있습니다.', iconType: 'error' });
                e.preventDefault();
            }
        });


        // WEB APP 링크 차이 보기 링크
        settingSection.querySelector('[data-click-target="view-message-link-type"]')?.addEventListener('click', function(e) {
            e.preventDefault();
            $.post('./layer_view_message_link_type.php', null, function (data) {
                ncds_layer_popup({message: data, title: '메시지 링크 유형', size: 'wide-sm'});
            });
        });
    }

    /**
     * 카카오 알림톡 사용 설정 영역
     */
    function kakaoAlimTalkSetting() {
        const settingSection = document.querySelector('.kakao-alrim-talk-setting');
        if (!settingSection) return;

        settingSection.querySelectorAll('input[name="kakaoAlimTalkConfig[useFlag]"]').forEach(input => {
            input.addEventListener('change', function (e) {
                e.preventDefault();
                settingSection.querySelectorAll('.setting-data').forEach(row => {
                    row.classList.toggle('display-none', input.value !== 'y');

                    if (row.classList.contains('kakao-alim-talk-sender')) {
                        row.classList.toggle('display-none', (!isKakaoAlrimAvailable && !isKakaoAlrimLunaInstalled) || input.value !== 'y');
                    }
                });

                const autoSmsAlternativeSendFlag = settingSection.querySelector('input[name="kakaoAlimTalkConfig[autoSmsAlternativeSendFlag]"]:checked')?.value === 'y';
                if (autoSmsAlternativeSendFlag) {
                    toggleLayoutKakaoAlimTalkAutoSmsAlternativeSendFlag(settingSection, input, 'y', function() {
                        showLayoutKakaoAlimTalkAutoSmsAlternativeSendType(settingSection, 'FAILED_MESSAGE');
                    });
                } else {
                    settingSection.querySelector('.kakao-alim-talk-alternative-sms-setting').classList.add('display-none');
                }
            });

            input.addEventListener('click', function (e) {
                if (input.value === 'y') {
                    if (!isKakaoAlrimAvailable && !isKakaoAlrimLunaInstalled && !document.getElementById('registeredCloudKakaoTalkChannelCard')) {
                        NCDSAlert({ message: '알림톡을 사용하려면 카카오톡 채널이 필요합니다.<br>상단에서 카카오톡 채널을 먼저 등록해 주세요.', iconType: 'error' });
                        e.preventDefault();
                        return;
                    }

                    e.target.dispatchEvent(new Event('change', { bubbles: true }));
                } else {
                    e.preventDefault();
                    NCDSConfirm({
                        message: '카카오 알림톡을 사용 안함으로 변경하시겠습니까?',
                        subMessage: '카카오 알림톡으로 등록한 예약/반복발송이 발송되지 않습니다.<br>※ 발송일시가 5분 이내인 발송건은 그대로 발송됩니다.',
                        btnText: {
                            confirmLabel: '확인',
                            cancelLabel: '취소'
                        },
                        callback: (result) => {
                            input.checked = true;
                            if (result) e.target.dispatchEvent(new Event('change', { bubbles: true }));
                        }
                    });
                }
            });
        });

        settingSection.querySelector('input[name="kakaoAlimTalkConfig[manualSmsAlternativeSendFlag]"][value=y]').addEventListener('click', function (e) {
            if (!document.getElementById('registeredCallNumberCard')) {
                NCDSAlert({message: '발신번호 설정 후 대체발송을 사용할 수 있습니다.', iconType: 'error' });
                e.preventDefault();
                return;
            }

            if (document.getElementById('mismatchedSmsPasswordCard')) {
                NCDSAlert({ message: '등록된 인증번호 정보가 일치하지 않아 대체발송을 사용할 수 없습니다.', subMessage: '어드민과 NHN 커머스(마이페이지)의 인증번호를 동일하게 설정한 뒤 다시 시도해 주세요.', iconType: 'error' });
                e.preventDefault();
            } else if (!document.getElementById('registeredSmsPasswordCard')) {
                NCDSAlert({ message: '메시지 인증번호 설정 후 대체발송을 사용할 수 있습니다.', iconType: 'error' });
                e.preventDefault();
            }
        });

        settingSection.querySelector('input[name="kakaoAlimTalkConfig[autoSmsAlternativeSendFlag]"][value=y]').addEventListener('click', function (e) {
            if (!document.getElementById('registeredCallNumberCard')) {
                NCDSAlert({message: '발신번호 설정 후 대체발송을 사용할 수 있습니다.', iconType: 'error' });
                e.preventDefault();
                return;
            }

            if (document.getElementById('mismatchedSmsPasswordCard')) {
                NCDSAlert({ message: '등록된 인증번호 정보가 일치하지 않아 대체발송을 사용할 수 없습니다.', subMessage: '어드민과 NHN 커머스(마이페이지)의 인증번호를 동일하게 설정한 뒤 다시 시도해 주세요.', iconType: 'error' });
                e.preventDefault();
            } else if (!document.getElementById('registeredSmsPasswordCard')) {
                NCDSAlert({ message: '메시지 인증번호 설정 후 대체발송을 사용할 수 있습니다.', iconType: 'error' });
                e.preventDefault();
            }
        });

        // 자동알림 대체발송 사용함 일때 view 설정
        settingSection.querySelectorAll('input[name="kakaoAlimTalkConfig[autoSmsAlternativeSendFlag]"]').forEach(input => {
            input.addEventListener('change', function (e) {
                e.preventDefault();

                toggleLayoutKakaoAlimTalkAutoSmsAlternativeSendFlag(settingSection, input, 'y', function() {
                    showLayoutKakaoAlimTalkAutoSmsAlternativeSendType(settingSection, 'FAILED_MESSAGE');
                });
            });
        });

        // 90 byte 초과 시 메시지 전송 방법 view 설정
        settingSection.querySelectorAll('input[name="kakaoAlimTalkConfig[autoSmsAlternativeSendType]"]').forEach(input => {
            input.addEventListener('change', function (e) {
                e.preventDefault();
                const alternativeSmsSetting = settingSection.querySelector('[data-kakao-alim-talk-auto-sms-alternative-send-type="FAILED_MESSAGE"]');
                if (alternativeSmsSetting) {
                    alternativeSmsSetting.classList.toggle('display-none', input.value !== 'FAILED_MESSAGE');
                }
            });
        });

        // 알림톡 발송 채널
        settingSection.querySelectorAll('.kakao-alrim-talk-setting input[name="kakaoAlimTalkConfig[sender]"]').forEach(input => {
            input.addEventListener('change', function (e) {
                e.preventDefault();
                const inputValue = input.value;

                settingSection.querySelectorAll('[data-kakao-alim-talk-sender]').forEach(target => target.classList.add('display-none'));

                const targetChannel = settingSection.querySelector(`[data-kakao-alim-talk-sender="${inputValue}"]`);
                if (targetChannel) targetChannel.classList.remove('display-none');

                const autoSmsSettingElement = settingSection.querySelectorAll('.kakao-alim-talk-auto-sms-alternative-send-setting');
                const manualSmsSettingElement = settingSection.querySelectorAll('.kakao-alim-talk-manual-sms-alternative-send-setting');
                if (input.value === 'kakaoAlrimCloud') {
                    autoSmsSettingElement.forEach(el => el.classList.remove('display-none'));
                    manualSmsSettingElement.forEach(el => el.classList.remove('display-none'));
                } else if (input.value === 'kakaoAlrim') {
                    autoSmsSettingElement.forEach(el => el.classList.remove('display-none'));
                    manualSmsSettingElement.forEach(el => el.classList.add('display-none'));
                } else {
                    autoSmsSettingElement.forEach(el => el.classList.add('display-none'));
                    manualSmsSettingElement.forEach(el => el.classList.add('display-none'));
                }
            });
        });
    }

    function toggleLayoutKakaoAlimTalkAutoSmsAlternativeSendFlag(parent, input, sendFlag, callback) {
        const selectedValue = parent.querySelector(`.kakao-alim-talk-alternative-sms-setting[data-kakao-alim-talk-auto-sms-alternative-send-flag="${sendFlag}"]`);
        if (!selectedValue) return;

        selectedValue.classList.toggle('display-none', input.value !== sendFlag);
        if (input.value === sendFlag && typeof callback === 'function') {
            callback();
        }
    }

    function showLayoutKakaoAlimTalkAutoSmsAlternativeSendType(parent, sendType) {
        const checkedValue = parent.querySelector('input[name="kakaoAlimTalkConfig[autoSmsAlternativeSendType]"]:checked')?.value;
        if (checkedValue === sendType) {
            parent.querySelector(`[data-kakao-alim-talk-auto-sms-alternative-send-type="${sendType}"]`).classList.remove('display-none');
        }
    }


    /**
     * 앱푸시 설정 영역
     */
    function mobileAppSetting() {
        const settingSection = document.querySelector('.mobile-app-push-setting');
        if (!settingSection) return;

        // 사용 설정 이벤트
        settingSection.querySelectorAll('input[name="myappPushConfig[useFlag]"]').forEach((input) => {
            input.addEventListener('change', function (e) {
                e.preventDefault();
                const settingRows = settingSection.querySelectorAll('.setting-data');

                settingRows.forEach((row) => {
                    row.classList.toggle('display-none', input.value !== 'y');
                });

                const autoSmsAlternativeSendFlag = settingSection.querySelector('input[name="myappPushConfig[autoSmsAlternativeSendFlag]"]:checked')?.value === 'y';
                if (autoSmsAlternativeSendFlag) {
                    const alternativeSmsFlagY = settingSection.querySelector('.myapp-push-alternative-sms-setting[data-myapp-push-auto-sms-alternative-send-flag="y"]');
                    if (!alternativeSmsFlagY) return;

                    alternativeSmsFlagY.classList.remove('display-none');
                    showLayoutMyappPushAutoSmsAlternativeSendType(settingSection, 'FAILED_MESSAGE');
                } else {
                    settingSection.querySelector('.myapp-push-alternative-sms-setting').classList.add('display-none');
                }
            });

            input.addEventListener('click', function (e) {
                if (input.value === 'y') {
                    if (!isMyappInstalled && !isMyappReleased) {
                        NCDSAlert({ message: '앱푸시를 사용하려면 쇼핑몰 앱이 필요합니다.<br>앱스토어에서 마이앱을 신청해 먼저 쇼핑몰 앱을 만들어주세요.', iconType: 'error' });
                        e.preventDefault();
                        return;
                    } else if (isMyappInstalled && !isMyappReleased) {
                        NCDSAlert({ message: '앱푸시를 사용하려면 쇼핑몰 앱이 필요합니다.<br>마이앱에서 앱 출시를 먼저 완료해 주세요.', iconType: 'error' });
                        e.preventDefault();
                        return;
                    }
                    e.target.dispatchEvent(new Event('change', { bubbles: true }));
                } else {
                    e.preventDefault();
                    NCDSConfirm({
                        message: '앱푸시를 사용 안함으로 변경하시겠습니까?',
                        subMessage: '앱푸시로 등록한 예약/반복발송이 발송되지 않습니다.<br>※ 발송일시가 5분 이내인 발송건은 그대로 발송됩니다.',
                        btnText: {
                            confirmLabel: '확인',
                            cancelLabel: '취소'
                        },
                        callback: (result) => {
                            input.checked = true;
                            if (result) e.target.dispatchEvent(new Event('change', { bubbles: true }));
                        }
                    });
                }
            });
        });

        settingSection.querySelector('input[name="myappPushConfig[manualSmsAlternativeSendFlag]"][value=y]').addEventListener('click', function (e) {
            if (!document.getElementById('registeredCallNumberCard')) {
                NCDSAlert({message: '발신번호 설정 후 대체발송을 사용할 수 있습니다.', iconType: 'error' });
                e.preventDefault();
                return;
            }

            if (document.getElementById('mismatchedSmsPasswordCard')) {
                NCDSAlert({ message: '등록된 인증번호 정보가 일치하지 않아 대체발송을 사용할 수 없습니다.', subMessage: '어드민과 NHN 커머스(마이페이지)의 인증번호를 동일하게 설정한 뒤 다시 시도해 주세요.', iconType: 'error' });
                e.preventDefault();
            } else if (!document.getElementById('registeredSmsPasswordCard')) {
                NCDSAlert({ message: '메시지 인증번호 설정 후 대체발송을 사용할 수 있습니다.', iconType: 'error' });
                e.preventDefault();
            }
        });

        settingSection.querySelector('input[name="myappPushConfig[autoSmsAlternativeSendFlag]"][value=y]').addEventListener('click', function (e) {
            if (!document.getElementById('registeredCallNumberCard')) {
                NCDSAlert({message: '발신번호 설정 후 대체발송을 사용할 수 있습니다.', iconType: 'error' });
                e.preventDefault();
                return;
            }

            if (document.getElementById('mismatchedSmsPasswordCard')) {
                NCDSAlert({ message: '등록된 인증번호 정보가 일치하지 않아 대체발송을 사용할 수 없습니다.', subMessage: '어드민과 NHN 커머스(마이페이지)의 인증번호를 동일하게 설정한 뒤 다시 시도해 주세요.', iconType: 'error' });
                e.preventDefault();
            } else if (!document.getElementById('registeredSmsPasswordCard')) {
                NCDSAlert({ message: '메시지 인증번호 설정 후 대체발송을 사용할 수 있습니다.', iconType: 'error' });
                e.preventDefault();
            }
        });

        // 자동 알림 발송 사용함 일때 대체발송 처리 방법 표시
        settingSection.querySelectorAll('input[name="myappPushConfig[autoSmsAlternativeSendFlag]"]').forEach((input) => {
            input.addEventListener('change', function (e) {
                e.preventDefault();
                const alternativeSmsFlagY = settingSection.querySelector('.myapp-push-alternative-sms-setting[data-myapp-push-auto-sms-alternative-send-flag="y"]');
                if (!alternativeSmsFlagY) return;

                if (input.value === 'y') {
                    alternativeSmsFlagY.classList.remove('display-none');
                    showLayoutMyappPushAutoSmsAlternativeSendType(settingSection, 'FAILED_MESSAGE');
                } else {
                    alternativeSmsFlagY.classList.add('display-none');
                }
            });
        });

        // 90 byte 초과 시 메시지 전송 방법 view 설정
        settingSection.querySelectorAll('input[name="myappPushConfig[autoSmsAlternativeSendType]"]').forEach((input) => {
            input.addEventListener('change', function (e) {
                e.preventDefault();
                const alternativeSendTypeSms = settingSection.querySelector('[data-myapp-push-auto-sms-alternative-send-type="FAILED_MESSAGE"]');
                if (!alternativeSendTypeSms) return;

                if (input.value === 'FAILED_MESSAGE') {
                    alternativeSendTypeSms.classList.remove('display-none');
                } else {
                    alternativeSendTypeSms.classList.add('display-none');
                }
            });
        });
    }

    function showLayoutMyappPushAutoSmsAlternativeSendType(parent, sendType) {
        const checkedValue = parent.querySelector('input[name="myappPushConfig[autoSmsAlternativeSendType]"]:checked')?.value;
        if (checkedValue === sendType) {
            parent.querySelector(`[data-myapp-push-auto-sms-alternative-send-type="${sendType}"]`).classList.remove('display-none');
        }
    }

    /**
     * 공통 로직
     */
    function commonLogic() {
        /**
         * 윈도우 팝업 함수 ( 원하는 사이즈, 스크롤 등 위해 분리 )
         */
        window.show_popup_message_config = ({url, width, height, onClose}) => {
            win = popup({
                url: url,
                target: '',
                width: width,
                height: height,
                scrollbars: 'yes',
                resizable: 'yes'
            });
            win?.focus();

            if (onClose && win) {
                const timer = setInterval(() => {
                    if (win.closed) {
                        clearInterval(timer);
                        onClose();
                    }
                }, 500);
            }

            return win;
        };

        /**
         * 저장하지 않은 변경사항 이탈 방지
         */
        window.unsavedGuard = new UnsavedChangesGuard('#frmMessageConfig', (proceed) => {
            NCDSConfirm({
                message: '페이지를 이동하시겠습니까?',
                subMessage: '저장하지 않은 내용이 있습니다.<br/>페이지를 이동하면 설정한 내용이 모두 사라집니다.',
                btnText: {
                    confirmLabel: '이동',
                    cancelLabel: '취소'
                },
                callback: (result) => {
                    if (result) proceed();
                }
            });
        });
    }

    function formInit() {
        if (typeof $ === 'undefined' || typeof $.validator === 'undefined') {
            console.warn('jQuery Validator가 로드되지 않았습니다.');
            return;
        }

        // 커스텀 validation 메서드 등록
        $.validator.addMethod('checkInsufficAlert', function (_, element) {
            // 알림 기준 선택 체크 (사용함일 때만)
            const alertFlag = $('input[name="messagePointConfig[UsePointLackNotify]"]:checked').val();

            if (alertFlag !== 'y') {
                return true; // 사용안함이면 검증 불필요
            }

            const notifyOnThreshold = $('input[name="messagePointConfig[notifyOnThreshold]"]').is(':checked');
            const notifyOnSendFail = $('input[name="messagePointConfig[notifyOnSendFail]"]').is(':checked');

            $('.insuffic-message-point-alert-support-text.no-padding').addClass('display-none');
            const checkboxInputs = $('input[name="messagePointConfig[notifyOnThreshold]"], input[name="messagePointConfig[notifyOnSendFail]').closest('.ncua-checkbox-input');
            checkboxInputs.removeClass('destructive');
            if (notifyOnThreshold || notifyOnSendFail) {
                return true;
            }

            // 둘 다 선택 안됨 - 에러 표시
            $('.insuffic-message-point-send-failed-alert-support-text.no-padding').removeClass('display-none');
            checkboxInputs.addClass('destructive');

            moveScroll(document.querySelector('.insuffic-message-point-alert-setting-row'));
            return false;
        }, '알림 기준을 선택해 주세요.');

        // 기본 설정 > 메시지 포인트 부족 알림 > 포인트 입력 필드 검증
        $.validator.addMethod('checkPointInput', function (value, element) {
            // 메시지 포인트 부족 알림이 "사용함"이고 "포인트 도달 시" 체크박스가 선택된 경우만 검증
            const alertFlag = $('input[name="messagePointConfig[UsePointLackNotify]"]:checked').val();
            if (alertFlag !== 'y') {
                return true; // 사용안함이면 검증 불필요
            }

            const notifyOnThreshold = $('#notifyOnThreshold').is(':checked');
            if (!notifyOnThreshold) {
                return true; // "포인트 도달 시" 체크박스가 선택되지 않았으면 검증 불필요
            }

            const trimmedValue = value ? value.trim() : '';
            const isInvalid = !trimmedValue || !/^\d+$/.test(trimmedValue) || parseInt(trimmedValue, 10) <= 0 || trimmedValue.length > 6;

            if (isInvalid) {
                moveScroll(document.querySelector('.insuffic-message-point-alert-setting-row'));
                return false;
            }

            $('.insuffic-message-point-alert-support-text').removeClass('destructive');
            return true;
        }, function(value, _) {
            const trimmedValue = value.trim();
            if (!trimmedValue) {
                return '알림을 받을 포인트를 입력해 주세요.';
            }
            if (!/^\d+$/.test(trimmedValue)) {
                return '숫자만 입력할 수 있습니다.';
            }
            if (trimmedValue.length > 6) {
                return '최대 6자리까지 입력할 수 있습니다.';
            }
            return '알림을 받을 포인트를 입력해 주세요.';
        });

        // Validator 설정 객체
        const validatorConfig = {
            rules: {
                'messagePointConfig[pointLackNotifyThreshold]': {
                    checkPointInput: true
                },
                'messagePointConfig[notifyOnThreshold]': {
                    checkInsufficAlert: true,
                }
            },
            messages: {
                'messagePointConfig[pointLackNotifyThreshold]': {
                    checkPointInput: '알림을 받을 포인트를 입력해 주세요.'
                }
            },
            dialog: false,
            invalidHandler: function() {
                // 기본 alert 방지
                return false;
            },
            submitHandler: function(form, event) {
                event.preventDefault();
                form.target = 'ifrmProcess';
                const formData = new FormData(form);

                // 메시지 포인트 부족 알림 체크박스 값 설정
                formData.set('messagePointConfig[notifyOnSendFail]', $('#notifyOnSendFail').is(':checked') ? 'true' : 'false');
                formData.set('messagePointConfig[notifyOnThreshold]', $('#notifyOnThreshold').is(':checked') ? 'true' : 'false');

                // 친구톡
                const kakaoFriendTalkUseFlag = formData.get('kakaoFriendTalkConfig[useFlag]');
                const smsAlternativeSendFlag = formData.get('kakaoFriendTalkConfig[smsAlternativeSendFlag]');

                // 알림톡
                const kakaoAlimTalkUseFlag = formData.get('kakaoAlimTalkConfig[useFlag]');
                const alimTalkManualSmsAlternativeSendFlag = formData.get('kakaoAlimTalkConfig[manualSmsAlternativeSendFlag]');
                const alimTalkAutoSmsAlternativeSendFlag = formData.get('kakaoAlimTalkConfig[autoSmsAlternativeSendFlag]');

                // 마이앱푸시
                const myappPushUseFlag = formData.get('myappPushConfig[useFlag]');
                const myappPushManualSmsAlternativeSendFlag = formData.get('myappPushConfig[manualSmsAlternativeSendFlag]');
                const myappPushAutoSmsAlternativeSendFlag = formData.get('myappPushConfig[autoSmsAlternativeSendFlag]');

                if (kakaoFriendTalkUseFlag === 'y' && !document.getElementById('registeredCloudKakaoTalkChannelCard')) {
                    NCDSAlert({
                        message: '카카오톡 채널을 등록해 주세요.',
                        subMessage: '카카오 알림톡을 사용하려면 카카오톡 채널이 필요합니다.',
                        iconType: 'error',
                        callback: function () {
                            moveScroll(document.querySelector('#layerCloudKakaoTalkChannel'));
                        }
                    });
                    return;
                }

                // 알림톡 발송업체 다중 선택 가능 시
                const kakaoAlimTalkSender = formData.get('kakaoAlimTalkConfig[sender]');
                if (isKakaoAlrimAvailable || isKakaoAlrimLunaInstalled) {
                    if (kakaoAlimTalkUseFlag === 'y') {
                        switch (kakaoAlimTalkSender) {
                            case 'kakaoAlrimCloud':
                                if (!document.getElementById('registeredCloudKakaoTalkChannelCard')) {
                                    NCDSAlert({
                                        message: '카카오톡 채널을 등록해 주세요.',
                                        subMessage: '카카오 알림톡을 사용하려면 카카오톡 채널이 필요합니다.',
                                        iconType: 'error',
                                        callback: function () {
                                            moveScroll(document.querySelector('#layerCloudKakaoTalkChannel'));
                                        }
                                    });
                                    return;
                                }
                                break;
                            case 'kakaoAlrim':
                                if (!document.getElementById('registeredBizmKakaoTalkChannelCard')) {
                                    NCDSAlert({
                                        message: '카카오톡 채널을 등록해 주세요.',
                                        subMessage: '카카오 알림톡을 사용하려면 카카오톡 채널이 필요합니다.',
                                        iconType: 'error',
                                        callback: function () {
                                            moveScroll(document.querySelector('#layerBizmKakaoTalkChannel'));
                                        }
                                    });
                                    return;
                                }
                                break;
                            case 'kakaoAlrimLuna':
                                if (!document.querySelector('button[data-click-target=logoutKakaoLuna]')) {
                                    NCDSAlert({
                                        message: '블룸에이아이 아이디를 입력해 주세요.',
                                        subMessage: '블룸에이아이를 통해 카카오 알림톡을 사용하려면<br>해당 서비스의 아이디가 필요합니다.',
                                        iconType: 'error',
                                        callback: function () {
                                            moveScroll(document.querySelector('#layerLunaKakaoTalkChannel'));
                                        }
                                    });
                                    return;
                                }
                                break;
                        }
                    }
                } else {
                    if ((kakaoAlimTalkUseFlag === 'y' || kakaoFriendTalkUseFlag === 'y') && !document.getElementById('registeredCloudKakaoTalkChannelCard')) {
                        NCDSAlert({
                            message: '카카오톡 채널을 등록해 주세요.',
                            subMessage: '카카오 친구톡, 알림톡을 사용하려면 카카오톡 채널이 필요합니다.',
                            iconType: 'error',
                            callback: function () {
                                moveScroll(document.querySelector('#layerCloudKakaoTalkChannel'));
                            }
                        });
                        return;
                    }
                }

                // 마이앱
                if (myappPushUseFlag === 'y') {
                    if (!isMyappInstalled && !isMyappReleased) {
                        NCDSAlert({
                            message: '앱푸시를 사용하려면 쇼핑몰 앱이 필요합니다.<br>앱스토어에서 마이앱을 신청해 먼저 쇼핑몰 앱을 만들어주세요.',
                            iconType: 'error'
                        });
                        return;
                    } else if (isMyappInstalled && !isMyappReleased) {
                        NCDSAlert({
                            message: '앱푸시를 사용하려면 쇼핑몰 앱이 필요합니다.<br>마이앱에서 앱 출시를 먼저 완료해 주세요.',
                            iconType: 'error'
                        });
                        return;
                    }
                }

                // Luna 발송업체는 대체발송 기능을 별도로 처리하므로 SMS 대체발송 검증에서 제외
                const needsSmsAlternative =
                    (kakaoFriendTalkUseFlag === 'y' && smsAlternativeSendFlag === 'y') ||
                    (kakaoAlimTalkUseFlag === 'y' && kakaoAlimTalkSender === 'kakaoAlrimCloud' && (alimTalkManualSmsAlternativeSendFlag === 'y' || alimTalkAutoSmsAlternativeSendFlag === 'y')) ||
                    (kakaoAlimTalkUseFlag === 'y' && kakaoAlimTalkSender === 'kakaoAlrim' && alimTalkAutoSmsAlternativeSendFlag === 'y') ||
                    (myappPushUseFlag === 'y' && (myappPushManualSmsAlternativeSendFlag === 'y' || myappPushAutoSmsAlternativeSendFlag === 'y'));

                if (needsSmsAlternative) {
                    if (!document.getElementById('registeredCallNumberCard')) {
                        NCDSAlert({message: '발신번호 설정 후 대체발송을 사용할 수 있습니다.', iconType: 'error' });
                        return;
                    }

                    if (document.getElementById('mismatchedSmsPasswordCard')) {
                        NCDSAlert({ message: '등록된 인증번호 정보가 일치하지 않아 대체발송을 사용할 수 없습니다.', subMessage: '어드민과 NHN 커머스(마이페이지)의 인증번호를 동일하게 설정한 뒤 다시 시도해 주세요.', iconType: 'error' });
                        return;
                    } else if (!document.getElementById('registeredSmsPasswordCard')) {
                        NCDSAlert({ message: '메시지 인증번호 설정 후 대체발송을 사용할 수 있습니다.', iconType: 'error' });
                        return;
                    }
                }

                $.ajax({
                    url: './message_config_ps.php',
                    type: 'POST',
                    data: formData,
                    processData: false,
                    contentType: false,
                    success: function(response) {
                        if (!response.success) {
                            NCDSAlert({ message: '메시지 설정 저장에 실패했습니다.', iconType: 'error' });
                            return;
                        }

                        window.NCDSToast({ message: '메시지 설정이 완료되었습니다.', color: 'success' });

                        // UnsavedChangesGuard 초기 상태 재설정
                        if (window.unsavedGuard) {
                            window.unsavedGuard.reset();
                        }
                    },
                    error: function(xhr, status, error) {
                        NCDSAlert({ message: '일시적인 오류가 발생하였습니다.<br>다시 시도해 주세요.', 'iconType': 'error' });
                    }
                });
            },
            showErrors: function(errorMap, errorList) {
                // NCDS Validator의 showErrors 호출
                if (typeof NCDSValidator !== 'undefined' && NCDSValidator.showErrors) {
                    NCDSValidator.showErrors(errorMap, errorList);
                }
                this.defaultShowErrors();
            },
            highlight: function(element) {
                if (typeof NCDSValidator !== 'undefined' && NCDSValidator.highlight) {
                    NCDSValidator.highlight(element);
                }
            },
            unhighlight: function(element) {
                if (typeof NCDSValidator !== 'undefined' && NCDSValidator.unhighlight) {
                    NCDSValidator.unhighlight(element);
                }
            }
        };

        // Validator 인스턴스 초기화
        const validatorInstance = $('#frmMessageConfig').validate(validatorConfig);

        // 포인트 입력 필드 변경 시 재검증
        $('#pointLackNotifyThreshold').on('blur', function() {
            validatorInstance.element(this);
        });

        // 메시지 포인트 부족 알림 스위치 변경 시 재검증
        $('input[name="messagePointConfig[UsePointLackNotify]"]').on('change', function() {
            if ($(this).val() === 'y') {
                // 사용함으로 변경 시 검증
                validatorInstance.element('#pointLackNotifyThreshold');
            } else {
                // 사용안함으로 변경 시 에러 제거
                validatorInstance.resetForm();
                $('.insuffic-message-point-alert-support-text').removeClass('destructive');
            }
        });

        const form = document.querySelector('#frmMessageConfig');
        if (!form) return;

        form.addEventListener('submit', function (e) {
            // jQuery Validator 검증 수행
            if (!validatorInstance.form()) {
                // Validation 실패 시 submit 중단
                e.preventDefault();
                return false;
            }
        });
    }

    function loadMessagePointLayer() {
        $.post('./message_config/layer_message_point.php', null, function (data) {
            $('#layerMessagePoint').html(data);
        });
    }

    function loadCallNumberLayer() {
        $.post('./message_config/layer_call_number.php', null, function (data) {
            $('#layerCallNumber').html(data);
        });
    }

    function loadSmsVerificationLayer() {
        $.post('./message_config/layer_sms_verification.php', null, function (data) {
            $('#layerSmsVerification').html(data);
        });
    }

    function loadCloudKakaoTalkChannelLayer() {
        $.post('./message_config/layer_cloud_kakao_talk_channel.php', null, function (data) {
            $('#layerCloudKakaoTalkChannel').html(data);
            if (document.getElementById('registeredCloudKakaoTalkChannelCard')) {
                document.querySelector('.js-cloud-kakao-talk-msg').innerHTML = "‘기본 설정’에 등록된 카카오톡 채널을 사용합니다.";
            } else {
                document.querySelector('.js-cloud-kakao-talk-msg').innerHTML = "‘기본 설정’에서 카카오톡 채널을 등록하세요.";
            }
        });
    }

    function loadBizmKakaoTalkChannelLayer() {
        $.post('./message_config/layer_bizm_kakao_talk_channel.php', null, function (data) {
            $('#layerBizmKakaoTalkChannel').html(data);

            // 레이어 로드 후 guard 초기화 (동적 input 추가로 인한 변경 감지 방지)
            if (window.unsavedGuard) {
                window.unsavedGuard.reset();
            }
        });
    }

    function loadLunaKakaoTalkChannelLayer() {
        $.post('./message_config/layer_luna_kakao_talk_channel.php', null, function (data) {
            $('#layerLunaKakaoTalkChannel').html(data);

            if (window.GodoCosGuide && window.cosData) {
                window.GodoCosGuide.apply(cosData);
            }

            // 레이어 로드 후 guard 초기화 (동적 input 추가로 인한 변경 감지 방지)
            if (window.unsavedGuard) {
                window.unsavedGuard.reset();
            }
        });
    }

    function initMessageConfig() {
        // 공통 로직 실행
        commonLogic();

        // 기본 설정 영역 실행
        defaultSetting();

        // SMS 설정 영역 실행
        smsSetting();

        // 카카오 친구톡 사용 설정 영역
        kakaoFriendTalkSetting();

        // 카카오 알림톡 사용 설정 영역
        kakaoAlimTalkSetting();

        // 앱푸시 설정 영역 실행
        mobileAppSetting();

        // 폼 validation 초기화
        formInit();
    }

    function moveScroll(element, addScrollY = 0)
    {
        const headerHeight = document.querySelector('.ncua-page-header')?.offsetHeight || 0;
        const elementPosition = element.getBoundingClientRect().top + window.scrollY + addScrollY;
        window.scrollTo({
            top: elementPosition - headerHeight,
            behavior: 'smooth'
        });
    }

    const DOM_READY_STATES = ['complete', 'interactive'];

    const onReady = () => {
        try {
            initMessageConfig();
        } catch (e) {
            NCDSAlert({ message: e.message, iconType: 'error' });
        }
    };

    if (DOM_READY_STATES.includes(document.readyState)) {
        onReady();
    } else {
        document.addEventListener('DOMContentLoaded', onReady);
    }
</script>

<!-- 툴팁 스크립트 -->
<script type="text/javascript">
    const code = '251210001';
    if (window.GodoCosGuide) {
        window.GodoCosGuide.init({
            apiUrl: <?= json_encode($cosGuideApiUrl) ?>,
            guideCode: code,
        }).then((data) => {
            window.cosData = data;
        });
    }
</script>
