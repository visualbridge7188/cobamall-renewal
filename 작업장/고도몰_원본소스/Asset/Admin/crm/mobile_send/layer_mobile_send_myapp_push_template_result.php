<div class="ncua-search-result">
    <div class="ncua-search-result__content">
        <div class="ncua-table ncua-table--horizontal ncua-table--border-bottom-radius-none">
            <table>
                <colgroup>
                    <col width="56px">
                    <col width="250px">
                    <col width="auto">
                </colgroup>
                <thead>
                <tr>
                    <th><div class="ncua-align-center">선택</div></th>
                    <th><div>제목</div></th>
                    <th><div>내용</div></th>
                </tr>
                </thead>
                <tbody id="templateTableBody">
                    <?php if (empty($templates)): ?>
                        <tr>
                            <td colspan="3"><div class="ncua-align-center">등록된 템플릿이 없습니다.</div></td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($templates as $template): ?>
                        <tr>
                            <td>
                                <div class="ncua-align-center">
                                    <label class="ncua-radio-field ncua-radio-field--xs">
                                        <span class="ncua-radio-input ncua-radio-input--xs ncua-radio-field__input">
                                            <input type="radio" name="template" value="<?= $template['pushSno'] ?>"
                                                data-push-subject="<?= htmlspecialchars($template['pushSubject'], ENT_QUOTES) ?>"
                                                data-push-content="<?= htmlspecialchars($template['pushContent'], ENT_QUOTES) ?>"
                                                data-push-image="<?= htmlspecialchars($template['pushImage'], ENT_QUOTES) ?>"
                                                data-push-url="<?= htmlspecialchars($template['pushUrl'], ENT_QUOTES) ?>"
                                                data-push-withdraw="<?= htmlspecialchars($template['pushWithdraw'], ENT_QUOTES) ?>"
                                            />
                                        </span>
                                    </label>
                                </div>
                            </td>
                            <td><div><?= nl2br(htmlspecialchars(str_replace(['\r\n', '\n'], "\n", $template['templateName'] ?? ''))) ?></div></td>
                            <?php
                                $pushText = '';
                                if (!empty($template['pushSubject'])) {
                                    $pushText = $template['pushSubject'] . "\n";
                                }
                                $pushText .= $template['pushContent'] ?? '';
                                $pushText = str_replace(['\r\n', '\n'], "\n", $pushText);
                            ?>
                            <td><div><?= nl2br(htmlspecialchars($pushText)) ?></div></td>
                        </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
            </table>
        </div>
        <div class="ncua-table-bottom"></div>
    </div>
</div>
<div class="ncua-pagination"><?= $page->getPage('searchTemplatesByPage(\'PAGELINK\')'); ?></div>

<script type="text/javascript">
    const requestData = JSON.parse('<?= json_encode($requestData, JSON_UNESCAPED_UNICODE); ?>');

    async function searchTemplatesByPage(pageLink = null) {
        try {
            let page = 1;
            if (pageLink) {
                const pageLinkParams = new URLSearchParams(pageLink);
                page = pageLinkParams.get('page');
            }

            const formData = new FormData();
            Object.entries(requestData).forEach(([key, value]) => {
                if (key === 'page') return;
                if (Array.isArray(value)) {
                    value.forEach(v => formData.append(`${key}[]`, v));
                } else {
                    formData.append(key, value);
                }
            });
            formData.append('page', page);

            await searchTemplates(formData);
        } catch (e) {
            logger.error(e);
        }
    }
</script>

