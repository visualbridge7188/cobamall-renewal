<section class="ncua-card">
    <header class="ncua-card__header">
        <h3 class="ncua-card__title">기본 설정</h3>
    </header>
    <section class="ncua-card__body">
        <div class="ncua-table ncua-table--vertical">
            <table>
                <tbody>
                    <tr>
                        <th><div>발송수단</div></th>
                        <td>
                            <div class="ncua-flex ncua-flex-gap ncua-custom-tooltip <?= in_array($request->getMode(), ['copy', 'modify']) ? 'is-edit-mode' : '' ?>">
                                <label class="ncua-radio-field ncua-radio-field--xs has-text" data-tooltip-type="disabled-send-method">
                                    <span class="ncua-radio-input ncua-radio-input--xs ncua-radio-field__input">
                                        <input name="sendMethod" type="radio" value="sms" <?= ($request->getSendMethod() === 'sms') ? 'checked="checked"' : '' ?> <?= in_array($request->getMode(), ['copy', 'modify']) ? 'disabled' : '' ?> />
                                    </span>
                                    <span class="ncua-radio-field__text">SMS/LMS</span>
                                </label>
                                <label class="ncua-radio-field ncua-radio-field--xs has-text" data-tooltip-type="disabled-send-method">
                                    <span class="ncua-radio-input ncua-radio-input--xs ncua-radio-field__input">
                                        <input name="sendMethod" type="radio" value="kakao" <?= ($request->getSendMethod() === 'kakao') ? 'checked="checked"' : '' ?> <?= (in_array($request->getMode(), ['copy', 'modify']) || $config->getKakaoUseFl() === 'n' || ($config->getCloudFl() === 'n' && $config->getBizmFl() === 'n')) ? 'disabled' : '' ?> />
                                    </span>
                                    <span class="ncua-radio-field__text">카카오 알림톡</span>
                                </label>
                                <label class="ncua-radio-field ncua-radio-field--xs has-text" data-tooltip-type="disabled-send-method">
                                    <span class="ncua-radio-input ncua-radio-input--xs ncua-radio-field__input">
                                        <input name="sendMethod" type="radio" value="myapp" <?= ($request->getSendMethod() === 'myapp') ? 'checked="checked"' : '' ?> <?= (in_array($request->getMode(), ['copy', 'modify']) || $config->getMyappUseFl() === 'n') ? 'disabled' : '' ?> />
                                    </span>
                                    <span class="ncua-radio-field__text">마이앱 (앱푸시)</span>
                                </label>
                            </div>
                        </td>
                    </tr>
                    <!-- 제공사 (kakao만) -->
                    <?php $isProviderDisabled = in_array($request->getMode(), ['copy', 'modify']); ?>
                    <?php if ($isNewMall === false) : ?>
                    <tr data-show-when='{"sendMethod": ["kakao"]}' style="<?= $request->getSendMethod() !== 'kakao' ? 'display:none' : '' ?>">
                        <th><div>제공사</div></th>
                        <td>
                            <div class="ncua-flex ncua-flex-gap">
                                <label class="ncua-radio-field ncua-radio-field--xs has-text">
                                    <span class="ncua-radio-input ncua-radio-input--xs ncua-radio-field__input">
                                        <input name="provider" type="radio" value="cloud" <?= ($request->getProvider() === 'cloud') ? 'checked="checked"' : '' ?> <?= ($isProviderDisabled || $config->getCloudFl() === 'n') ? 'disabled="disabled"' : '' ?> />
                                    </span>
                                    <span class="ncua-radio-field__text">고도몰</span>
                                </label>
                                    <label class="ncua-radio-field ncua-radio-field--xs has-text">
                                        <span class="ncua-radio-input ncua-radio-input--xs ncua-radio-field__input">
                                            <input name="provider" type="radio" value="bizm" <?= ($request->getProvider() === 'bizm') ? 'checked="checked"' : '' ?> <?= ($isProviderDisabled || $config->getBizmFl() === 'n') ? 'disabled="disabled"' : '' ?> />
                                        </span>
                                        <span class="ncua-radio-field__text">비즈엠</span>
                                    </label>
                            </div>
                        </td>
                    </tr>
                    <?php else: ?>
                    <input type="hidden" name="provider" value="<?= $config->getCloudFl() === 'y' ? 'cloud' : 'bizm' ?>" />
                    <?php endif; ?>
                    <!-- 템플릿 카테고리 (sms) -->
                    <?php $selectedSmsCategory = $response->getSms()?->getSmsCategory(); ?>
                    <tr data-show-when='{"sendMethod": ["sms"]}' style="<?= $request->getSendMethod() !== 'sms' ? 'display:none' : '' ?>">
                        <th class="ncua-required"><div>템플릿 카테고리</div></th>
                        <td>
                            <div class="ncua-flex ncua-flex-gap">
                                <?php foreach ($category->getSmsCategory() as $index => $cat): ?>
                                <label class="ncua-radio-field ncua-radio-field--xs has-text">
                                    <span class="ncua-radio-input ncua-radio-input--xs ncua-radio-field__input">
                                        <input name="templateCategory" type="radio" value="<?= $cat['value'] ?>" <?= (($selectedSmsCategory ? $selectedSmsCategory === $cat['value'] : $index === 0)) ? 'checked="checked"' : '' ?> />
                                    </span>
                                    <span class="ncua-radio-field__text"><?= $cat['name'] ?></span>
                                </label>
                                <?php endforeach; ?>
                            </div>
                        </td>
                    </tr>
                    <!-- 템플릿 카테고리 (myapp) -->
                    <?php $selectedMyappCategory = $response->getMyapp()?->getPushType(); ?>
                    <tr data-show-when='{"sendMethod": ["myapp"]}' style="<?= $request->getSendMethod() !== 'myapp' ? 'display:none' : '' ?>">
                        <th class="ncua-required"><div>템플릿 카테고리</div></th>
                        <td>
                            <div class="ncua-flex ncua-flex-gap">
                                <?php foreach ($category->getMyappCategory() as $index => $cat): ?>
                                <label class="ncua-radio-field ncua-radio-field--xs has-text">
                                    <span class="ncua-radio-input ncua-radio-input--xs ncua-radio-field__input">
                                        <input name="templateCategory" type="radio" value="<?= $cat['value'] ?>" <?= (($selectedMyappCategory ? $selectedMyappCategory === $cat['value'] : $index === 0)) ? 'checked="checked"' : '' ?> />
                                    </span>
                                    <span class="ncua-radio-field__text"><?= $cat['name'] ?></span>
                                </label>
                                <?php endforeach; ?>
                            </div>
                        </td>
                    </tr>
                    <!-- 구분 (cloud) -->
                    <?php $selectedCloudType = $response->getCloud()?->getSmsType(); ?>
                    <tr data-show-when='{"sendMethod": ["kakao"], "provider": ["cloud"]}' style="<?= ($request->getSendMethod() !== 'kakao' || $request->getProvider() !== 'cloud') ? 'display:none' : '' ?>">
                        <th class="ncua-required"><div>구분</div></th>
                        <td>
                            <div class="ncua-flex ncua-flex-gap">
                                <?php foreach ($category->getCloudCategory() as $index => $type): ?>
                                <label class="ncua-radio-field ncua-radio-field--xs has-text">
                                    <span class="ncua-radio-input ncua-radio-input--xs ncua-radio-field__input">
                                        <input name="templateBasicType" type="radio" value="<?= $type['value'] ?>" <?= (($selectedCloudType ? $selectedCloudType === $type['value'] : $index === 0)) ? 'checked="checked"' : '' ?> />
                                    </span>
                                    <span class="ncua-radio-field__text"><?= $type['name'] ?></span>
                                </label>
                                <?php endforeach; ?>
                            </div>
                        </td>
                    </tr>
                    <!-- 구분 (bizm) -->
                    <?php $selectedBizmType = $response->getBizm()?->getSmsType(); ?>
                    <tr data-show-when='{"sendMethod": ["kakao"], "provider": ["bizm"]}' style="<?= ($request->getSendMethod() !== 'kakao' || $request->getProvider() !== 'bizm') ? 'display:none' : '' ?>">
                        <th class="ncua-required"><div>구분</div></th>
                        <td>
                            <div class="ncua-flex ncua-flex-gap">
                                <?php foreach ($category->getBizmCategory() as $index => $type): ?>
                                <label class="ncua-radio-field ncua-radio-field--xs has-text">
                                    <span class="ncua-radio-input ncua-radio-input--xs ncua-radio-field__input">
                                        <input name="templateBasicType" type="radio" value="<?= $type['value'] ?>" <?= (($selectedBizmType ? $selectedBizmType === $type['value'] : $index === 0)) ? 'checked="checked"' : '' ?> />
                                    </span>
                                    <span class="ncua-radio-field__text"><?= $type['name'] ?></span>
                                </label>
                                <?php endforeach; ?>
                            </div>
                        </td>
                    </tr>
                    <!-- 카카오 템플릿 카테고리 (kakao만) -->
                    <?php
                    $kakaoTemplateCategories = $category->getKakaoTemplateCategory();
                    // categoryCode에서 대분류 키 찾기
                    $selectedCategoryCode = $response->getCloud()?->getCategoryCode() ?? $response->getBizm()?->getCategoryCode();
                    $selectedCategory1 = '';
                    $selectedCategory1SubCategories = [];
                    if ($selectedCategoryCode) {
                        foreach ($kakaoTemplateCategories as $cat) {
                            foreach ($cat['subCategories'] as $sub) {
                                if ($sub['value'] === $selectedCategoryCode) {
                                    $selectedCategory1 = $cat['value'];
                                    $selectedCategory1SubCategories = $cat['subCategories'];
                                    break 2;
                                }
                            }
                        }
                    }
                    ?>
                    <tr data-show-when='{"sendMethod": ["kakao"]}' style="<?= $request->getSendMethod() !== 'kakao' ? 'display:none' : '' ?>">
                        <th class="ncua-required"><div data-tooltip-seq="001">템플릿 카테고리</div></th>
                        <td>
                            <div class="ncua-flex-column ncua-gap-8" data-show-hint-text="true">
                                <div class="ncua-flex ncua-select-group ncua-flex-gap ncua-gap-4">
                                    <span class="ncua-select ncua-select--xs" data-hint-target="templateCategory1">
                                        <span class="ncua-select__content">
                                            <select id="templateCategory1" name="templateCategory1" class="ncua-select__tag js-kakao-category1">
                                                <option value="">대분류</option>
                                                <?php foreach ($kakaoTemplateCategories as $cat): ?>
                                                <option value="<?= $cat['value'] ?>" <?= $selectedCategory1 === $cat['value'] ? 'selected' : '' ?>><?= $cat['name'] ?></option>
                                                <?php endforeach; ?>
                                            </select>
                                        </span>
                                    </span>
                                    <span class="ncua-select ncua-select--xs">
                                        <span class="ncua-select__content">
                                            <select id="templateCategory2" name="templateCategory2" class="ncua-select__tag js-kakao-category2">
                                                <option value="">중분류</option>
                                                <?php if ($selectedCategory1 && !empty($selectedCategory1SubCategories)): ?>
                                                    <?php foreach ($selectedCategory1SubCategories as $sub): ?>
                                                    <option value="<?= $sub['value'] ?>" <?= $selectedCategoryCode === $sub['value'] ? 'selected' : '' ?>><?= $sub['label'] ?></option>
                                                    <?php endforeach; ?>
                                                <?php endif; ?>
                                            </select>
                                        </span>
                                    </span>
                                </div>
                                <span class="ncua-hint-text ncua-input__hint-text destructive display-none" data-hint-for="templateCategory1">카테고리를 선택해주세요.</span>
                            </div>
                            <script>
                            (function() {
                                const categoryData = <?= json_encode(array_column($kakaoTemplateCategories, null, 'value')) ?>;
                                const category1 = document.querySelector('.js-kakao-category1');
                                const category2 = document.querySelector('.js-kakao-category2');

                                category1.addEventListener('change', function() {
                                    const selectedValue = this.value;

                                    if (selectedValue && categoryData[selectedValue]) {
                                        category2.innerHTML = '';
                                        const subCategories = categoryData[selectedValue].subCategories;
                                        subCategories.forEach(function(sub, index) {
                                            const option = document.createElement('option');
                                            option.value = sub.value;
                                            option.textContent = sub.label;
                                            if (index === 0) option.selected = true;
                                            category2.appendChild(option);
                                        });
                                    } else {
                                        category2.innerHTML = '<option value="">중분류</option>';
                                    }
                                });
                            })();
                            </script>
                        </td>
                    </tr>
                    <?php
                    // 발송수단에 따라 템플릿 제목 값 결정
                    $templateTitleValue = match($request->getSendMethod()) {
                        'sms' => $response->getSms()?->getSubject(),
                        'kakao' => $response->getCloud()?->getTemplateName() ?? $response->getBizm()?->getTemplateName(),
                        'myapp' => $response->getMyapp()?->getTemplateName(),
                        default => '',
                    } ?? '';
                    ?>
                    <tr>
                        <th class="ncua-required"><div data-tooltip-seq="002">템플릿 제목</div></th>
                        <td>
                            <div>
                                <div class="ncua-input ncua-input--xs" data-show-hint-text="true">
                                    <div class="ncua-input__content-wrap">
                                        <div class="ncua-input__content">
                                            <div class="ncua-input__field ncua-input__field--xs ncua-input-width-520">
                                                <?php
                                                    if ($request->getSendMethod() === 'kakao' && $request->getProvider() === 'cloud') {
                                                        $templateTitleMaxLengthAttr = '';
                                                    } elseif ($request->getSendMethod() === 'kakao' && $request->getProvider() === 'bizm') {
                                                        $templateTitleMaxLengthAttr = 'maxlength="30"';
                                                    } else {
                                                        $templateTitleMaxLengthAttr = 'maxlength="10"';
                                                    }
                                                ?>
                                                <input name="templateTitle" value="<?= htmlspecialchars($templateTitleValue) ?>" class="ncua-template__title js-template-title" type="text" placeholder="제목으로 강조할 내용을 작성해 주세요." <?= $templateTitleMaxLengthAttr ?> data-charcount-key="templateTitle" />
                                            </div>
                                        </div>
                                        <?php
                                            if ($request->getSendMethod() === 'kakao' && $request->getProvider() === 'bizm') {
                                                $templateTitleMaxLength = 30;
                                            } elseif ($request->getSendMethod() === 'kakao' && $request->getProvider() === 'cloud') {
                                                $templateTitleMaxLength = null;
                                            } else {
                                                $templateTitleMaxLength = 10;
                                            }
                                        ?>
                                        <div class="ncua-input__field-text-count" data-charcount-text="templateTitle" data-hide-when='{"sendMethod": ["kakao"], "provider": ["cloud"]}' style="<?= $request->getSendMethod() === 'kakao' && $request->getProvider() === 'cloud' ? 'display:none' : '' ?>">
                                            <output class="ncua-input__field-text-count-current"><?= mb_strlen($templateTitleValue) ?></output><span class="js-template-title-max-length"><?= $templateTitleMaxLength ? '/' . $templateTitleMaxLength : '' ?></span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </td>
                    </tr>
                    <!-- 보안 템플릿 설정 (kakao만) -->
                    <?php $securityFlagValue = $response->getCloud()?->getSecurityFlag() ?? $response->getBizm()?->getSecurityFlag() ?? false; ?>
                    <tr data-show-when='{"sendMethod": ["kakao"]}' style="<?= $request->getSendMethod() !== 'kakao' ? 'display:none' : '' ?>">
                        <th><div data-tooltip-seq="003">보안 템플릿 설정</div></th>
                        <td>
                            <div>
                                <div class="ncua-switch ncua-switch--xs" >
                                    <label class="ncua-switch__option ncua-switch__option--left <?= $securityFlagValue ? 'ncua-switch__option--active' : 'ncua-switch__option--inactive' ?>">
                                        <input class="ncua-switch__radio ncua-switch__radio--left" type="radio" value="y" name="securityTemplate" <?= $securityFlagValue ? 'checked="checked"' : '' ?>>
                                        <span class="ncua-switch__label">사용함</span>
                                    </label>
                                    <label class="ncua-switch__option ncua-switch__option--right <?= !$securityFlagValue ? 'ncua-switch__option--active' : 'ncua-switch__option--inactive' ?>">
                                        <input class="ncua-switch__radio ncua-switch__radio--right" type="radio" value="n" name="securityTemplate" <?= !$securityFlagValue ? 'checked="checked"' : '' ?>>
                                        <span class="ncua-switch__label">사용안함</span>
                                    </label>
                                </div>
                            </div>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </section>
</section>
