<?php include __DIR__ . '/auto_send_config_recipient_checkboxes.php'; ?>

<?php foreach ($autoSendConfigsResponse as $recipient => $autoSendConfig): ?>
    <?php include __DIR__ . '/auto_send_config_recipient_section.php'; ?>
<?php endforeach; ?>