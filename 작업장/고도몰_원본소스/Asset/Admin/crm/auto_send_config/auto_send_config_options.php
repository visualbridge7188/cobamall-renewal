<section class="ncua-card">
    <header class="ncua-card__header">
        <h4 class="ncua-card__title">발송 설정</h4>
    </header>

    <section class="ncua-card__body">
        <div class="ncua-table ncua-table--vertical">
            <table>
                <tbody>
                <tr>
                    <th><div>발송 항목</div></th>
                    <td>
                        <div class="ncua-flex-column">
                            <div><?= $displayOptions['code']['title'] ?></div>
                            <p class="send-config-description"><?= $displayOptions['code']['description'] ?></p>
                        </div>
                    </td>
                </tr>

                <?php if ($displayOptions['targetOrderScope']):?>
                    <tr>
                        <th><div data-tooltip-seq="001">발송 대상 주문건</div></th>
                        <td>
                            <div class="ncua-gap-8">
                                <div>최근 (</div>
                                <?php foreach ($displayOptions['targetOrderScope'] as $index => $orderScope): ?>
                                    <label class="ncua-radio-field ncua-radio-field--xs has-text">
                                        <span class="ncua-radio-input ncua-radio-input--xs ncua-radio-field__input">
                                            <input type="radio" name="options[targetOrderScope]" value="<?= $orderScope ?>" <?= $options['targetOrderScope'] === $orderScope ? 'checked' : '' ?> />
                                        </span>
                                        <span class="ncua-radio-field__text"><?= $orderScope ?>일</span>
                                    </label>
                                <?php endforeach; ?>
                                <div>) 이내</div>
                            </div>
                        </td>
                    </tr>
                <?php endif; ?>
                <?php if ($displayOptions['sendUnit']):?>
                    <tr>
                        <th><div data-tooltip-seq="002">발송 단위</div></th>
                        <td>
                            <div class="ncua-flex-gap">
                                <?php foreach ($displayOptions['sendUnit'] as $sendUnitKey => $sendUnit): ?>
                                    <label class="ncua-radio-field ncua-radio-field--xs has-text">
                                    <span class="ncua-radio-input ncua-radio-input--xs ncua-radio-field__input">
                                        <input type="radio" name="options[sendUnit]" value="<?= $sendUnitKey ?>" <?= $options['sendUnit'] === $sendUnitKey ? 'checked' : '' ?> />
                                    </span>
                                        <span class="ncua-radio-field__text"><?= $sendUnit ?></span>
                                    </label>
                                <?php endforeach; ?>
                            </div>
                        </td>
                    </tr>
                <?php endif; ?>
                <?php if ($displayOptions['isResendEnabled']):?>
                    <tr>
                        <th><div>재발송</div></th>
                        <td>
                            <div class="ncua-flex-column ncua-align-center">
                                <div class="ncua-flex ncua-flex-gap">
                                    <?php foreach ($displayOptions['isResendEnabled']['flag'] as $resendEnabledFlagKey => $resendEnabledFlag): ?>
                                        <label class="ncua-radio-field ncua-radio-field--xs has-text">
                                            <span class="ncua-radio-input ncua-radio-input--xs ncua-radio-field__input">
                                                <input type="radio" name="options[isResendEnabled]" value="<?= $resendEnabledFlagKey ?>" <?= $options['isResendEnabled'] === $resendEnabledFlagKey ? 'checked' : '' ?> />
                                            </span>
                                            <span class="ncua-radio-field__text"><?= $resendEnabledFlag ?></span>
                                        </label>
                                    <?php endforeach; ?>
                                </div>
                                <div class="ncua-flex ncua-flex-column ncua-gap-8 resend-enabled-options">
                                    <div class="ncua-flex ncua-flex-gap">
                                        <?php foreach ($displayOptions['isResendEnabled']['days'] as $day): ?>
                                            <label class="ncua-radio-field ncua-radio-field--xs has-text">
                                                <span class="ncua-radio-input ncua-radio-input--xs ncua-radio-field__input">
                                                    <input type="radio" name="options[resendAfterDays]" value="<?= $day ?>" <?= $options['resendAfterDays'] == $day ? 'checked' : '' ?> />
                                                </span>
                                                <span class="ncua-radio-field__text"><?= $day ?>일 후</span>
                                            </label>
                                        <?php endforeach; ?>
                                    </div>
                                    <div class="ncua-flex ncua-flex-gap">
                                        <?php foreach ($displayOptions['isResendEnabled']['hours'] as $hour): ?>
                                            <label class="ncua-radio-field ncua-radio-field--xs has-text">
                                                <span class="ncua-radio-input ncua-radio-input--xs ncua-radio-field__input">
                                                    <input type="radio" name="options[resendTime]" value="<?= $hour ?>" <?= $options['resendTime'] == $hour ? 'checked' : '' ?> />
                                                </span>
                                                <span class="ncua-radio-field__text"><?= $hour ?>시</span>
                                            </label>
                                        <?php endforeach; ?>
                                    </div>
                                </div>
                            </div>
                        </td>
                    </tr>
                <?php endif; ?>

                <?php if ($displayOptions['isNightSendEnabled']):?>
                    <tr>
                        <th><div data-tooltip-seq="003">야간 발송</div></th>
                        <td>
                            <div class="ncua-flex-gap">
                                <?php foreach ($displayOptions['isNightSendEnabled'] as $nightSendEnabledKey => $nightSendEnabled): ?>
                                    <label class="ncua-radio-field ncua-radio-field--xs has-text">
                                    <span class="ncua-radio-input ncua-radio-input--xs ncua-radio-field__input">
                                        <input type="radio" name="options[isNightSendEnabled]" value="<?= $nightSendEnabledKey ?>" <?= $nightSendEnabledKey == $options['isNightSendEnabled'] ? 'checked' : '' ?> />
                                    </span>
                                        <span class="ncua-radio-field__text"><?= $nightSendEnabled ?></span>
                                    </label>
                                <?php endforeach; ?>
                            </div>
                        </td>
                    </tr>
                <?php endif; ?>
                <?php if ($displayOptions['sendTime']):?>
                <tr>
                    <th><div>발송시각</div></th>
                    <td>
                        <div class="ncua-flex-gap">
                            <?php foreach ($displayOptions['sendTime'] as $sendTime): ?>
                                <label class="ncua-radio-field ncua-radio-field--xs has-text">
                                    <span class="ncua-radio-input ncua-radio-input--xs ncua-radio-field__input">
                                        <input type="radio" name="options[sendTime]" value="<?= $sendTime ?>" <?= $options['sendTime'] == $sendTime ? 'checked' : '' ?> />
                                    </span>
                                    <span class="ncua-radio-field__text"><?= $sendTime ?>시</span>
                                </label>
                            <?php endforeach; ?>
                        </div>
                    </td>
                </tr>
                <?php endif; ?>
                <?php if ($displayOptions['sendTriggerType']):?>
                    <tr>
                        <th><div data-tooltip-seq="003">발송 시점</div></th>
                        <td>
                            <div class="ncua-gap-8">
                                <div>쿠폰 만료 (</div>

                                <?php foreach ($displayOptions['sendTriggerType'] as $sendTriggerType): ?>
                                    <label class="ncua-radio-field ncua-radio-field--xs has-text">
                                        <span class="ncua-radio-input ncua-radio-input--xs ncua-radio-field__input">
                                            <input type="radio" name="options[sendTriggerType]" value="<?= $sendTriggerType ?>" <?= $sendTriggerType == $options['sendTriggerType'] ? 'checked' : '' ?> />
                                        </span>
                                        <span class="ncua-radio-field__text"><?= $sendTriggerType ?>일</span>
                                    </label>
                                <?php endforeach; ?>
                                <div>) 전 발송</div>
                            </div>
                        </td>
                    </tr>
                <?php endif; ?>
                <?php if ($displayOptions['includeApprovalPendingMember']):?>
                    <tr>
                        <th><div data-tooltip-seq="004">승인대기 회원 포함</div></th>
                        <td>
                            <div class="ncua-flex-gap">
                                <?php foreach ($displayOptions['includeApprovalPendingMember'] as $includeApprovalPendingMemberKey => $includeApprovalPendingMember): ?>
                                    <label class="ncua-radio-field ncua-radio-field--xs has-text">
                                        <span class="ncua-radio-input ncua-radio-input--xs ncua-radio-field__input">
                                            <input type="radio" name="options[includeApprovalPendingMember]" value="<?= $includeApprovalPendingMemberKey ?>" <?= $includeApprovalPendingMemberKey == $options['includeApprovalPendingMember'] ? 'checked' : '' ?> />
                                        </span>
                                        <span class="ncua-radio-field__text"><?= $includeApprovalPendingMember ?></span>
                                    </label>
                                <?php endforeach; ?>
                            </div>
                        </td>
                    </tr>
                <?php endif; ?>
                </tbody>
            </table>
        </div>
    </section>
</section>
