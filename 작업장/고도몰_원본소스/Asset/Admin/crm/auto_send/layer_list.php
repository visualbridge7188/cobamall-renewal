<script src="<?=gd_set_browser_cache(PATH_ADMIN_GD_SHARE . 'ncds/script/common/switch.js')?>"></script>

<div class="ncua-search-result__summary">
    <p class="ncua-search-result__summary-count">
        검색 <strong><?= $pageCounts['search']; ?></strong>개 /
        전체 <strong><?= $pageCounts['total']; ?></strong>개
    </p>
</div>
<!-- // summary -->
<div class="ncua-search-result__content">
    <!-- 검색 결과 액션 -->
    <div class="ncua-search-result__actions">
        <div class="ncua-search-result__actions-select">
            <span class="ncua-select ncua-select--xs">
                <span class="ncua-select__content">
                    <select class="ncua-select__tag" id="autoSendListSort" name="sort">
                        <option value="category">카테고리순</option>
                        <option value="sendStatus">발송 설정순</option>
                    </select>
                </span>
            </span>
        </div>
    </div>
    <!-- // 검색 결과 액션 -->
    <!-- table -->
    <div class="ncua-table ncua-table--horizontal">
        <table>
            <colgroup>
                <col width="200px">
                <col width="100px">
                <col width="auto" class="col-auto-width">
                <col width="164px">
                <col width="190px">
                <col width="88ox">
                <col width="88ox">
            </colgroup>
            <thead>
            <tr>
                <th><div>발송여부</div></th>
                <th><div>카테고리</div></th>
                <th><div>발송항목</div></th>
                <th><div>발송 가능 대상</div></th>
                <th><div>발송 가능 수단</div></th>
                <th><div>설정</div></th>
                <th><div>미리보기</div></th>
            </tr>
            </thead>
            <tbody>
            <?php foreach ($autoSends as $code => $autoSend): ?>
                <tr data-category="<?= htmlspecialchars($autoSend['category']) ?>" data-send-status="<?= $autoSend['shouldAutoSend'] ?>">
                    <td>
                        <div>
                            <div class="ncua-switch ncua-switch--xs">
                                <label class="ncua-switch__option ncua-switch__option--left <?= $autoSend['shouldAutoSend'] === 'y' ? 'ncua-switch__option--active' : 'ncua-switch__option--inactive' ?>">
                                    <input class="auto-send-radio ncua-switch__radio ncua-switch__radio--left" type="radio" value="y" data-code="<?= $code ?>" name="sendStatus[<?= $code ?>]" <?= $autoSend['shouldAutoSend'] === 'y' ? 'checked' : '' ?> />
                                    <span class="ncua-switch__label">발송함</span>
                                </label>
                                <label class="ncua-switch__option ncua-switch__option--right <?= $autoSend['shouldAutoSend'] === 'n' ? 'ncua-switch__option--active' : 'ncua-switch__option--inactive' ?>">
                                    <input class="auto-send-radio ncua-switch__radio ncua-switch__radio--right" type="radio" value="n" data-code="<?= $code ?>" name="sendStatus[<?= $code ?>]" <?= $autoSend['shouldAutoSend'] === 'n' ? 'checked' : '' ?> />
                                    <span class="ncua-switch__label">발송안함</span>
                                </label>
                            </div>
                        </div>
                    </td>
                    <td><div><?= $autoSend['category'] ?></div></td>
                    <td>
                        <div class="ncua-left-align auto-list-item-content">
                            <div class="auto-list-item-title">
                                <?php if (($autoSend['recommend'] ?? 'n') === 'y'): ?>
                                    <span class="ncua-badge ncua-badge--pill-outline ncua-badge--pink ncua-badge--xs">
                                        <span class="ncua-badge__label">추천</span>
                                    </span>
                                <?php endif; ?>
                                <?= $autoSend['title'] ?>
                                <?php if (in_array($code, $newIconCodes ?? [])): ?>
                                    <img src="<?= PATH_ADMIN_GD_SHARE ?>ncds/image/ico_new.svg" alt="new">
                                <?php endif; ?>
                            </div>
                            <div class="auto-list-item-description">
                                <?= $autoSend['description'] ?>
                                <?php if (!empty($autoSend['activeDisplayOptions'])): ?>
                                    <br>• <?= implode(' • ', $autoSend['activeDisplayOptions']) ?>
                                <?php endif; ?>
                            </div>
                        </div>
                    </td>
                    <td><div><?= $autoSend['recipients'] ?></div></td>
                    <td><div><?= $autoSend['channels'] ?></div></td>
                    <td>
                        <div>
                            <button type="button" class="ncua-btn ncua-btn--xxs ncua-btn--secondary-gray" onclick="auto_send_config('<?= $code ?>');">
                                <span class="ncua-btn__label">설정</span>
                            </button>
                        </div>
                    </td>
                    <td>
                        <div>
                            <button type="button" class="ncua-btn ncua-btn--xxs ncua-btn--secondary-gray" onclick="preview_auto_send('<?= $code ?>');">
                                <span class="ncua-btn__label">미리보기</span>
                            </button>
                        </div>
                    </td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </div>
    <!-- // table -->
</div>