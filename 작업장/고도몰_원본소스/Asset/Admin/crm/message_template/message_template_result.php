 <form name="frmList" id="frmList" action="" method="post" target="">
    <input type="hidden" name="mode" value="">
    <div class="ncua-search-result">
        <div class="ncua-search-result__summary">
            <p class="ncua-search-result__summary-count">검색 <strong><?= $page->getTotal() ?></strong>개 / 전체 <strong><?= $page->getAmount() ?></strong>개</p>
        </div>

        <div class="ncua-search-result__content">
            <div class="ncua-search-result__actions">
                <div class="ncua-search-result__actions-button">
                    <div class="ncua-search-result__actions-text">
                        <button type="button" class="ncua-btn ncua-btn--xs ncua-btn--destructive ncua-select-delete js-btn-delete">선택 삭제</button>
                    </div>
                </div>
                <div class="ncua-search-result__actions-select">
                    <span class="ncua-select ncua-select--xs">
                        <span class="ncua-select__content">
                            <select class=" ncua-select__tag" id="sort" name="sort" onchange="redirectWithParams('sort', this.value)">
                                <option value="regDt desc" <?= ($search->getSort() == "regDt desc") ? 'selected="selected"' : '' ?>>최근 등록순</option>
                                <option value="regDt asc"  <?= ($search->getSort() == "regDt asc") ? 'selected="selected"' : '' ?>>과거 등록순</option>
                                <option value="subject asc" <?= ($search->getSort() == "subject asc" ) ? 'selected="selected"' : '' ?>>이름 오름차순</option>
                                <option value="subject desc" <?= ($search->getSort() == "subject desc") ? 'selected="selected"' : '' ?>>이름 내림차순</option>
                            </select>
                        </span>
                    </span>
                    <span class="ncua-select ncua-select--xs">
                        <span class="ncua-select__content">
                            <select class=" ncua-select__tag js-page-number" id="pageSize" name="pageSize" onchange="changePageSize(this.value)">
                                <option value="10" <?= ($search->getPageSize() == 10) ? 'selected="selected"' : '' ?>>10개 보기</option>
                                <option value="20" <?= ($search->getPageSize() == 20) ? 'selected="selected"' : '' ?>>20개 보기</option>
                                <option value="30" <?= ($search->getPageSize() == 30) ? 'selected="selected"' : '' ?>>30개 보기</option>
                                <option value="40" <?= ($search->getPageSize() == 40) ? 'selected="selected"' : '' ?>>40개 보기</option>
                                <option value="50" <?= ($search->getPageSize() == 50) ? 'selected="selected"' : '' ?>>50개 보기</option>
                            </select>
                        </span>
                    </span>
                </div>
            </div>
            
            <div class="ncua-table ncua-table--horizontal ncua-border-radius-none" data-checkbox-sync-initialized="true">
                <table>
                    <colgroup>
                        <col width="56px">
                        <col>
                        <col width="120px">
                        <col>
                        <col>
                        <?php if ($sendMethod === 'kakao') :?>
                            <col>
                            <col>
                            <col>
                        <?php endif; ?>
                        <col width="180px">
                    </colgroup>
                    <thead>
                        <tr>
                            <th rowspan="2">
                                <div class="ncua-align-center">
                                    <label class="ncua-checkbox-field ncua-checkbox-field--xs">
                                        <span class="ncua-checkbox-input ncua-checkbox-input--xs ncua-checkbox-field__input">
                                            <input type="checkbox" class="ncua-checkall" data-checkbox-name="templateCodeList[]">
                                        </span>
                                    </label>
                                </div>
                            </th>
                            <th rowspan="2"><div data-tooltip-seq="007">템플릿코드</div></th>
                            <th rowspan="2"><div data-tooltip-seq="008"><?= $sendMethod === 'kakao' ? '구분' : '카테고리' ?></div></th>
                            <th rowspan="2"><div data-tooltip-seq="009">템플릿명</div></th>
                            <?php if ($sendMethod === 'kakao') :?>
                                <th colspan="2"><div data-tooltip-seq="010">알림톡 검수</div></th>
                            <?php endif ?>
                            <th rowspan="2"><div data-tooltip-seq="011">등록일</div></th>
                            <?php if ($sendMethod === 'kakao') :?>
                                <th rowspan="2"><div data-tooltip-seq="012">사용 중인 자동 알림</div></th>
                            <?php endif ?>
                            <th rowspan="2"><div data-tooltip-seq="013">관리</div></th>
                        </tr>
                        <?php if ($sendMethod === 'kakao') :?>
                            <tr>
                                <th><div data-tooltip-seq="014">검수 상태</div></th>
                                <th><div data-tooltip-seq="015">검수 답변</div></th>
                            </tr>
                        <?php endif ?>
                    </thead>
                    <tbody>
                        <?php if (
                            ($sendMethod === 'kakao' && (
                                    ($search->getProvider() === 'bizm' && $config->getBizmFl() === 'n') ||
                                    ($search->getProvider() === 'cloud' && $config->getCloudFl() === 'n') ||
                                    ($config->getKakaoUseFl() === 'n')
                                )) ||
                            ($sendMethod === 'myapp' && $config->getMyappUseFl() === 'n')
                        ) :?>
                            <tr>
                                <td colspan="<?= ($sendMethod === 'kakao') ? '9' : '6' ?>" height="50" class="no-data">
                                    <div class="message-config-info">이 발송수단은 현재 '사용 안함'으로 설정되어 있습니다.<br>템플릿 등록 및 메시지를 발송하려면 <a href="./message_config.php" class="ncua-link">메시지 설정</a>에서 사용을 활성화해 주세요.</div>
                                </td>
                            </tr>
                        <?php elseif (empty($results)):?>
                            <tr>
                                <td colspan="<?= ($sendMethod === 'kakao') ? '9' : '6' ?>" height="50" class="no-data"><div>검색된 정보가 없습니다.</div></td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($results as $result) : ?>
                                <tr>
                                    <td><div>
                                    <label class="ncua-checkbox-field ncua-checkbox-field--xs has-text">
                                        <span class="ncua-checkbox-input ncua-checkbox-input--xs ncua-checkbox-field__input">
                                            <input type="checkbox" name="templateCodeList[]" value="<?= $result->getTemplateCode() ?>" <?= ( $result->getBasicFl() === 'y') ? 'disabled' : '' ?> />
                                        </span>
                                    </label>
                                    </div></td>
                                    <td><div><?= $result->getTemplateCode() ?></div></td>
                                    <td><div><?= $result->getTemplateCategory() ?></div></td>
                                    <td>
                                        <div>
                                            <button type="button" class="ncua-left-align message-list-content-btn" onclick="check_template_contents('<?= $result->getTemplateCode() ?>', '<?= $sendMethod ?>', '<?= $search->getProvider() ?>')"><?= $result->getTemplateName() ?></button>
                                        </div>
                                    </td>
                                    <?php if ($sendMethod === 'kakao') :?>
                                        <td><div><?= $result->getTemplateStatus() ?></div></td>
                                        <?php if ($result->getTemplateStatus() === '반려') :?>
                                            <td><div><button type="button" class="ncua-btn ncua-btn--xxs ncua-btn--secondary-gray" onclick="check_kakao_template_comment('<?= $search->getProvider() ?>','<?= $result->getTemplateCode() ?>')">답변 확인</button></div></td>
                                        <?php else: ?>
                                            <td><div>-</div></td>
                                        <?php endif; ?>
                                    <?php endif; ?>
                                    <td><div><?= date('Y. m. d', strtotime($result->getRegDt())) . ' ' . ((date('A', strtotime($result->getRegDt())) === 'AM' ? '오전' : '오후') . ' ' . date('h:i:s', strtotime($result->getRegDt()))) ?></div></td>
                                    <?php if ($sendMethod === 'kakao') :?>
                                        <?php if ($result->getAutoFl() === 'y') :?>
                                            <td>
                                                <div class="ncua-custom-tooltip">
                                                    <div data-tooltip-type="auto-notification-count">
                                                        <div class="ncua-left-align message-list-content-btn" ><?= $result->getAutoName() ?></div>
                                                        <div class="ncua-custom-tooltip__content">
                                                            <?php foreach ($result->getAutoList() as $auto):  ?>
                                                                <p><?= $auto ?></p>
                                                            <?php endforeach; ?>
                                                        </div>
                                                    </div>
                                                </div>
                                            </td>
                                        <?php else : ?>
                                            <td><div><?= $result->getAutoName() ?></div></td>
                                        <?php endif; ?>
                                    <?php endif; ?>
                                    <td><div class="ncua-flex-gap ncua-gap-4">
                                        <button type="button" class="ncua-btn ncua-btn--xxs ncua-btn--secondary-gray js-btn-copy" value='<?= $result->getTemplateCode() ?>'>복제</button>
                                        <?php
                                            $canModify = $sendMethod !== 'kakao' || in_array($result->getTemplateStatus(), ['등록', '반려']);
                                        ?>
                                        <?php if ($canModify): ?>
                                            <button type="button" class="ncua-btn ncua-btn--xxs ncua-btn--secondary-gray js-btn-modify" value='<?= $result->getTemplateCode() ?>'>수정</button>
                                        <?php endif; ?>
                                    </div></td>
                                </tr>
                            <?php endforeach ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
            <div class="ncua-table-bottom"></div>
        </div>
        <div class="ncua-pagination"><?= $page->getPage('loadByPage(\'PAGELINK\')') ?></div>
    </div>
</form>
