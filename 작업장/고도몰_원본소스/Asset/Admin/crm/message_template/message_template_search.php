<form name="frmSearch" id="frmSearch" action="" method="post" class="frmSearch js-form-enter-submit">
    <input type="hidden" name="sendMethod" value=<?= $sendMethod ?>>
    <div class="ncua-table ncua-table--vertical">
        <table>
            <colgroup>
                <col>
                <col>
                <col width="240px">
                <col>
            </colgroup>
            <tbody>
            <?php if ($sendMethod === 'kakao') : ?>
                <tr>
                    <th>
                        <div data-tooltip-seq="001">제공사</div>
                    </th>
                    <td colspan="3">
                        <div class="ncua-flex-gap">
                            <label class="ncua-radio-field ncua-radio-field--xs has-text">
                                        <span class="ncua-radio-input ncua-radio-input--xs ncua-radio-field__input">
                                            <input type="radio" name="provider"
                                                   value="cloud" <?= ($search->getProvider() === 'cloud') ? 'checked' : '' ?> onchange="redirectWithParams('provider', this.value)">
                                        </span>
                                <span class="ncua-radio-field__text">고도몰</span>
                            </label>
                            <?php if ($isNewMall === false) : ?>
                                <label class="ncua-radio-field ncua-radio-field--xs has-text">
                                            <span class="ncua-radio-input ncua-radio-input--xs ncua-radio-field__input">
                                                <input type="radio" name="provider"
                                                       value="bizm" <?= ($search->getProvider() === 'bizm') ? 'checked' : '' ?> onchange="redirectWithParams('provider', this.value)">
                                            </span>
                                    <span class="ncua-radio-field__text">비즈엠</span>
                                </label>
                            <?php endif; ?>
                        </div>
                    </td>
                </tr>
            <?php endif; ?>
            <tr>
                <th>
                    <div data-tooltip-seq="002">등록일</div>
                </th>
                <td colspan="3">
                    <div>
                        <div id="datepicker-container"></div>
                        <!--                            <div id="datepicker-container">-->
                        <?php //if(!empty($regdt)) $regdt->getDates() ?><!--</div>-->
                    </div>
                </td>
            </tr>
            <?php
            $categories = [
                ['value' => 'all', 'name' => '전체'],
            ];
            if ($sendMethod === 'sms') {
                $categories = array_merge($categories, $searchCategory->getSmsCategory());
            } elseif ($sendMethod === 'myapp') {
                $categories = array_merge($categories, $searchCategory->getMyappCategory());
            } elseif ($search->getProvider() === 'cloud') {
                $categories = array_merge($categories, $searchCategory->getCloudCategory());
            } elseif ($search->getProvider() === 'bizm') {
                $categories = array_merge($categories, $searchCategory->getBizmCategory());
            }
            ?>
            <tr>
                <th>
                    <div data-tooltip-seq="003"><?= $sendMethod === 'kakao' ? '구분' : '카테고리' ?></div>
                </th>
                <td colspan="3">
                    <div class="ncua-flex-gap js-trigger-type">
                        <?php foreach ($categories as $cat): ?>
                            <?php
                            $checked = in_array($cat['value'], $search->getCategory()) ? 'checked' : '';
                            ?>
                            <label class="ncua-checkbox-field ncua-checkbox-field--xs has-text">
                                    <span class="ncua-checkbox-input ncua-checkbox-input--xs ncua-checkbox-field__input">
                                        <input type="checkbox" name="category[]"
                                               value="<?= $cat['value'] ?>" <?= $checked ?>>
                                    </span>
                                <span class="ncua-checkbox-field__text"><?= $cat['name'] ?></span>
                            </label>
                        <?php endforeach; ?>
                    </div>
                </td>
            </tr>
            <?php if ($sendMethod === 'kakao') : ?>
                <?php
                $statusOptions = [
                    ['value' => 'all', 'label' => '전체'],
                ];
                if ($search->getProvider() === 'cloud') {
                    $statusOptions = array_merge($statusOptions, $searchStatus->getCloudStatus());
                } elseif ($search->getProvider() === 'bizm') {
                    $statusOptions = array_merge($statusOptions, $searchStatus->getBizmStatus());
                }
                ?>
                <tr>
                    <th>
                        <div data-tooltip-seq="004">검수 상태</div>
                    </th>
                    <td>
                        <div class="ncua-flex-gap js-trigger-status">
                            <?php foreach ($statusOptions as $option): ?>
                                <?php if (!isset($option['provider']) || $search->getProvider() === $option['provider']): ?>
                                    <label class="ncua-checkbox-field ncua-checkbox-field--xs has-text">
                                            <span class="ncua-checkbox-input ncua-checkbox-input--xs ncua-checkbox-field__input">
                                                <input type="checkbox" name="status[]"
                                                       value="<?= $option['value'] ?>" <?= (in_array($option['value'], $search->getStatus())) ? 'checked' : '' ?>>
                                            </span>
                                        <span class="ncua-checkbox-field__text"><?= $option['label'] ?></span>
                                    </label>
                                <?php endif; ?>
                            <?php endforeach; ?>
                        </div>
                    </td>
                    <?php
                    $autoTypeOptions = [
                        ['value' => 'all', 'label' => '전체'],
                        ['value' => 'y', 'label' => '사용함'],
                        ['value' => 'n', 'label' => '사용안함'],
                    ];
                    ?>
                    <th>
                        <div data-tooltip-seq="005">자동알림 사용</div>
                    </th>
                    <td>
                        <div class="ncua-flex-gap">
                            <?php foreach ($autoTypeOptions as $option): ?>
                                <label class="ncua-radio-field ncua-radio-field--xs has-text">
                                        <span class="ncua-radio-input ncua-radio-input--xs ncua-radio-field__input">
                                            <input type="radio" name="autoType"
                                                   value="<?= $option['value'] ?>" <?= ($search->getAutoType() === $option['value']) ? 'checked' : '' ?>>
                                        </span>
                                    <span class="ncua-radio-field__text"><?= $option['label'] ?></span>
                                </label>
                            <?php endforeach; ?>
                        </div>
                    </td>
                </tr>
            <?php endif; ?>
            <tr>
                <th>
                    <div data-tooltip-seq="006">검색어</div>
                </th>
                <td colspan="3">
                    <div class="ncua-flex-gap ncua-gap-4">
                        <div class="ncua-select ncua-select--xs">
                            <div class="ncua-select__content ">
                                <select id="searchField" class="ncua-select__tag" name="searchKey">
                                    <option value="all">통합검색</option>
                                    <option value="sno" <?= ($search->getSearchKey() === 'sno') ? 'selected' : "" ?>>템플릿
                                        코드
                                    </option>
                                    <option value="subject" <?= ($search->getSearchKey() === 'subject') ? 'selected' : "" ?>>
                                        템플릿 명
                                    </option>
                                </select>
                            </div>
                        </div>
                        <div class="ncua-select ncua-select--xs search-kind">
                            <div class="ncua-select__content">
                                <select class=" ncua-select__tag" name="searchDetailKey">
                                    <option value="all">검색어 전체일치</option>
                                    <option value="part"<?php if ($search->getSearchDetailKey() === 'part') echo 'selected' ?>>
                                        검색어 부분포함
                                    </option>
                                </select>
                            </div>
                        </div>
                        <div class="ncua-input ncua-input--xs ncua-input-width-320">
                            <div class="ncua-input__content">
                                <div class="ncua-input__field ncua-input__field--xs">
                                    <input name="searchWord" type="text"
                                           value="<?= $search->getSearchWord() !== null ? htmlspecialchars($search->getSearchWord(), ENT_QUOTES, 'UTF-8') : '' ?>"
                                           placeholder="검색어를 입력해 주세요.">
                                </div>
                            </div>
                        </div>
                    </div>
                </td>
            </tr>
            </tbody>
        </table>
    </div>
    <div class="ncua-flex ncua-justify-between ncua-align-items-start search-footer-container">
        <?php if ($sendMethod === 'kakao') : ?>
            <p class="ncua-notice-info">블룸에이아이의 템플릿은 <a class="ncua-link" href="https://bizmsg.blumn.ai/template"
                                                        target="_blank">블룸에이아이 파트너스 센터</a>에서 확인 가능합니다.</p>
        <?php else : ?>
            <p></p>
        <?php endif ?>
        <div class="ncua-btn-group">
            <button type="button" class="ncua-btn ncua-btn--xs has-underline ncua-btn--text" onclick="resetSearch()">
                초기화
            </button>
            <button type="submit" class="ncua-btn ncua-btn--xs ncua-btn--secondary">검색</button>
        </div>
    </div>
</form>
