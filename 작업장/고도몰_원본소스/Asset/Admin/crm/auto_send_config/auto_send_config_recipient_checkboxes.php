<section class="ncua-card ncua-card--no-border">
    <header class="ncua-card__header">
        <h4 class="ncua-card__title" data-tooltip-seq="005" data-tooltip-icon-type="fill">
            수신 대상 설정
        </h4>
    </header>

    <section class="ncua-card__body">
        <div class="ncua-border-group-box">
            <?php foreach ($recipients as $recipient):
                $recipientKey   = strtolower($recipient->name);
                $isForceChecked = in_array($recipient, $forceCheckRecipients ?? [], true);
                $isChecked      = $isForceChecked || in_array($recipientKey, $checkedRecipients);

                $checkedAttr  = $isChecked ? 'checked' : '';
                $disabledAttr = $isForceChecked ? 'disabled' : '';
                $hiddenValue  = $isForceChecked ? 'y' : 'n';
                ?>
                <label class="ncua-checkbox-field ncua-checkbox-field--xs has-text">
                    <span class="ncua-checkbox-input ncua-checkbox-input--xs ncua-checkbox-field__input">
                        <input type="hidden"
                               name="recipients[<?= $recipientKey ?>][isSelected]"
                               value="<?= $hiddenValue ?>" />

                        <input type="checkbox"
                               name="recipients[<?= $recipientKey ?>][isSelected]"
                               value="y"
                               <?= $checkedAttr ?>
                            <?= $disabledAttr ?>
                               data-recipient="<?= $recipientKey ?>" />
                    </span>

                    <span class="ncua-checkbox-field__text">
                        <?= $recipient->getTitle() ?>
                    </span>
                </label>
            <?php endforeach; ?>
        </div>
    </section>
</section>