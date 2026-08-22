<section class="ncua-card">
    <header class="ncua-card__header">
        <h4 class="ncua-card__title">템플릿 내용</h4>
    </header>
    <section class="ncua-card__body">
        <!-- 본문 입력: 마이앱(앱푸시) -->
        <div class="message-content-myapp" data-component="message-content-myapp">
            <div class="ncua-card__body-title-wrap ncua-required">
                <p class="ncua-card__body-title--xs">본문 입력</p>
            </div>
            <div class="ncua-input ncua-input--xs ncua-input--full-width-with-count" data-show-hint-text="true">
                <div class="ncua-input__content-wrap">
                    <div class="ncua-input__content">
                        <div class="ncua-input__field ncua-input__field--xs">
                            <input class="template-content-myapp-title" data-charcount-key="templateContentMyappTitle" name="templateContentMyappTitle" type="text" value="<?= htmlspecialchars($response->getMyapp()?->getPushSubject() ?? '') ?>" maxlength="40" placeholder="맛집 BEST 3" />
                        </div>
                    </div>
                    <div class="ncua-input__field-text-count" data-charcount-text="templateContentMyappTitle">
                        <output class="ncua-input__field-text-count-current"><?= mb_strlen($response->getMyapp()?->getPushSubject() ?? '') ?></output><span>/40</span>
                    </div>
                </div>
            </div>
            <div id="myapp-message-input-container" class="message-input-container"></div>
        </div>

        <div id="myapp-variable-selector-container" class="variable-selector-container" data-component="variable-selector"></div>

        <!-- 세부 입력 -->
        <div class="detailed-input-content" data-component="detailed-input-content">
            <div class="ncua-card__body-title-wrap ncua-card__body-title-wrap--inline">
                <div class="ncua-flex ncua-gap-8 align-items-center">
                    <p class="ncua-card__body-title--xs">세부 입력</p>
                </div>
            </div>
            <div class="ncua-table ncua-table--vertical">
                <table>
                    <tbody>
                        <tr>
                            <th><div>수신 동의 철회 방법</div></th>
                            <td>
                                <div class="myapp-withdrawal-wrap">
                                    <div class="ncua-input ncua-input--xs ncua-input--full-width-with-count">
                                        <div class="ncua-input__content-wrap">
                                            <div class="ncua-input__content">
                                                <div class="ncua-input__field ncua-input__field--xs">
                                                    <input class="withdrawal-button-name" data-charcount-key="templateWithdrawalMethod" maxlength="40" name="templateWithdrawalMethod" type="text" value="<?= htmlspecialchars($response->getMyapp()?->getPushWithdraw() ?? '') ?>" placeholder="수신거부: 설정 > 알림 OFF" />
                                                </div>
                                            </div>
                                            <div class="ncua-input__field-text-count" data-charcount-text="templateWithdrawalMethod">
                                                <output class="ncua-input__field-text-count-current"><?= mb_strlen($response->getMyapp()?->getPushWithdraw() ?? '') ?></output><span>/40</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <th><div>이미지</div></th>
                            <td>
                                <div>
                                    <input name="templateWithdrawalImage" id="templateWithdrawalImageInput" tabindex="-1" aria-hidden="true" type="file" />
                                    <div id="withdrawal-image-file-container"></div>
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <th><div>URL</div></th>
                            <td>
                                <div class="ncua-flex-column url-input-wrap">
                                    <div class="ncua-flex ncua-gap-4 ncua-align-items-center">
                                        <span class="myapp-base-url"><?= $myappUrl ?></span>
                                        <div class="ncua-input ncua-input--xs" data-show-hint-text="true">
                                            <div class="ncua-input__content">
                                                <div class="ncua-input__field ncua-input__field--xs">
                                                    <input id="templateUrlInput" data-charcount-key="templateUrl" maxlength="100" name="templateUrl" type="text" value="<?= htmlspecialchars($response->getMyapp()?->getPushUrl() ?? '') ?>" placeholder="/event/12345" />
                                                </div>
                                            </div>
                                        </div>
                                        <button type="button" id="validateUrlBtn" class="ncua-btn ncua-btn--xs ncua-btn--secondary-gray">검증</button>
                                    </div>
                                    <ul>
                                        <li class="ncua-notice-info">입력되지 않은 푸시는 메인페이지로 이동됩니다.</li>
                                    </ul>
                                </div>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </section>
</section>
