<ul>
    <li class="ncua-caution-text">치환코드는 지정된 페이지에서만 사용하실 수 있습니다.</li>
    <li class="ncua-notice-info">예) 주문 관련 치환코드는 주문리스트에서 SMS 발송 시에만 사용하실 수 있습니다.</li>
    <li class="ncua-notice-info">중복 선택은 불가합니다.</li>
</ul>
<div class="ncua-table ncua-table--vertical">
    <table>
        <tbody>
        <tr>
            <th><div>검색</div></th>
            <td>
                <div class="ncua-gap-4">
                    <div class="ncua-select ncua-select--xs">
                        <div class="ncua-select__content">
                            <select class="ncua-select__tag" id="searchCategory">
                                <option value="">전체</option>
                                <?php foreach ($categories as $category): ?>
                                    <option value="<?= $category['value'] ?>"><?= $category['name'] ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>
                    <div class="ncua-input ncua-input--xs">
                        <div class="ncua-input__content-wrap">
                            <div class="ncua-input__content">
                                <div class="ncua-input__field ncua-input__field--xs">
                                    <input type="text" id="searchKeyword" placeholder="제목 또는 내용을 입력해 주세요." />
                                </div>
                            </div>
                        </div>
                    </div>
                    <button type="button" class="ncua-btn ncua-btn--xs ncua-btn--secondary" id="btnSearch">
                        <span class="ncua-btn__label">검색</span>
                    </button>
                </div>
            </td>
        </tr>
        </tbody>
    </table>
</div>

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
                        <td colspan="3"><div class="ncua-align-center no-data">검색 결과가 없습니다.</div></td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($templates as $template): ?>
                        <tr>
                            <td>
                                <div class="ncua-align-center">
                                    <label class="ncua-radio-field ncua-radio-field--xs">
                            <span class="ncua-radio-input ncua-radio-input--xs ncua-radio-field__input">
                                <input type="radio" name="template" value="<?= $template['sno'] ?>" data-contents="<?= htmlspecialchars($template['contents'], ENT_QUOTES) ?>" data-subject="<?= htmlspecialchars($template['title'] ?? '', ENT_QUOTES) ?>" data-url="<?= htmlspecialchars($template['url'] ?? '', ENT_QUOTES) ?>"<?php if (!empty($template['myappImage'])): ?> data-myapp-image="<?= htmlspecialchars($template['myappImage'], ENT_QUOTES) ?>"<?php endif; ?>/>
                            </span>
                                    </label>
                                </div>
                            </td>
                            <td><div><?= $template['subject'] ?></div></td>
                            <td><div><?php if (!empty($template['title'])): ?><?= htmlspecialchars($template['title']) ?><br><?php endif; ?><?= nl2br($template['contents']) ?></div></td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
                </tbody>
            </table>
        </div>
        <div class="ncua-table-bottom"></div>
    </div>
</div>
<div id="templatePagination" class="ncua-pagination ncua-pagination--center">
    <?= $page->getPage('#'); ?>
</div>