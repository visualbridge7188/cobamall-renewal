<section class="ncua-card" style="margin-top: 12px;">
    <header class="ncua-card__header">
        <h4 class="ncua-card__title">자동 알림 리스트</h4>
    </header>
    <section id="auto-list-search" class="ncua-card__body">
        <div class="ncua-table ncua-table--vertical">
            <table>
                <tbody>
                <tr>
                    <th><div>발송 가능 대상</div></th>
                    <td>
                        <div class="ncua-flex-gap">
                            <label class="ncua-radio-field ncua-radio-field--xs has-text">
                                    <span class="ncua-radio-input ncua-radio-input--xs ncua-radio-field__input">
                                        <input type="radio" name="recipient" value="all" <?= empty($request['recipient']) || $request['recipient'] === 'all' ? 'checked' : '' ?>>
                                    </span>
                                <span class="ncua-radio-field__text">전체</span>
                            </label>
                            <?php foreach ($recipients as $recipient): ?>
                                <label class="ncua-radio-field ncua-radio-field--xs has-text">
                                    <span class="ncua-radio-input ncua-radio-input--xs ncua-radio-field__input">
                                        <input type="radio" name="recipient" value="<?= $recipient->name ?>" <?= ($request['recipient'] ?? '') === $recipient->name ? 'checked' : '' ?>>
                                    </span>
                                    <span class="ncua-radio-field__text"><?= $recipient->getTitle() ?></span>
                                </label>
                            <?php endforeach; ?>
                        </div>
                    </td>
                </tr>
                <!-- todo 카테고리 다 정의되면 enum loop 로 생성 -->
                <tr>
                    <th><div>카테고리</div></th>
                    <td>
                        <div class="ncua-flex-gap">
                            <label class="ncua-radio-field ncua-radio-field--xs has-text">
                                    <span class="ncua-radio-input ncua-radio-input--xs ncua-radio-field__input">
                                        <input type="radio" name="category" value="all" <?= empty($request['category']) || $request['category'] === 'all' ? 'checked' : '' ?>>
                                    </span>
                                <span class="ncua-radio-field__text">전체</span>
                            </label>
                            <label class="ncua-radio-field ncua-radio-field--xs has-text">
                                    <span class="ncua-radio-input ncua-radio-input--xs ncua-radio-field__input">
                                        <input type="radio" name="category" value="order" <?= ($request['category'] ?? '') === 'order' ? 'checked' : '' ?>>
                                    </span>
                                <span class="ncua-radio-field__text">주문/배송</span>
                            </label>
                            <!-- todo 나중에 value 확인 필요 -->
                            <label class="ncua-radio-field ncua-radio-field--xs has-text">
                                    <span class="ncua-radio-input ncua-radio-input--xs ncua-radio-field__input">
                                        <input type="radio" name="category" value="regular" <?= ($request['category'] ?? '') === 'regular' ? 'checked' : '' ?>>
                                    </span>
                                <span class="ncua-radio-field__text">정기결제(배송)</span>
                            </label>
                            <label class="ncua-radio-field ncua-radio-field--xs has-text">
                                    <span class="ncua-radio-input ncua-radio-input--xs ncua-radio-field__input">
                                        <input type="radio" name="category" value="present" <?= ($request['category'] ?? '') === 'present' ? 'checked' : '' ?>>
                                    </span>
                                <span class="ncua-radio-field__text">선물하기</span>
                            </label>
                            <!-- todo END-->
                            <label class="ncua-radio-field ncua-radio-field--xs has-text">
                                    <span class="ncua-radio-input ncua-radio-input--xs ncua-radio-field__input">
                                        <input type="radio" name="category" value="member" <?= ($request['category'] ?? '') === 'member' ? 'checked' : '' ?>>
                                    </span>
                                <span class="ncua-radio-field__text">회원</span>
                            </label>
                            </label>
                            <label class="ncua-radio-field ncua-radio-field--xs has-text">
                                    <span class="ncua-radio-input ncua-radio-input--xs ncua-radio-field__input">
                                        <input type="radio" name="category" value="promotion" <?= ($request['category'] ?? '') === 'promotion' ? 'checked' : '' ?>>
                                    </span>
                                <span class="ncua-radio-field__text">쿠폰/프로모션</span>
                            </label>
                            </label>
                            <label class="ncua-radio-field ncua-radio-field--xs has-text">
                                    <span class="ncua-radio-input ncua-radio-input--xs ncua-radio-field__input">
                                        <input type="radio" name="category" value="board" <?= ($request['category'] ?? '') === 'board' ? 'checked' : '' ?>>
                                    </span>
                                <span class="ncua-radio-field__text">게시판</span>
                            </label>
                        </div>
                    </td>
                </tr>
                <tr>
                    <th><div>발송 상태</div></th>
                    <td>
                        <div class="ncua-flex-gap">
                            <label class="ncua-radio-field ncua-radio-field--xs has-text">
                                    <span class="ncua-radio-input ncua-radio-input--xs ncua-radio-field__input">
                                        <input type="radio" name="shouldAutoSend" value="all" <?= empty($request['shouldAutoSend']) || $request['shouldAutoSend'] === 'all' ? 'checked' : '' ?>>
                                    </span>
                                <span class="ncua-radio-field__text">전체</span>
                            </label>
                            <label class="ncua-radio-field ncua-radio-field--xs has-text">
                                    <span class="ncua-radio-input ncua-radio-input--xs ncua-radio-field__input">
                                        <input type="radio" name="shouldAutoSend" value="y" <?= ($request['shouldAutoSend'] ?? '') === 'y' ? 'checked' : '' ?>>
                                    </span>
                                <span class="ncua-radio-field__text">발송함</span>
                            </label>
                            <label class="ncua-radio-field ncua-radio-field--xs has-text">
                                    <span class="ncua-radio-input ncua-radio-input--xs ncua-radio-field__input">
                                        <input type="radio" name="shouldAutoSend" value="n" <?= ($request['shouldAutoSend'] ?? '') === 'n' ? 'checked' : '' ?>>
                                    </span>
                                <span class="ncua-radio-field__text">발송안함</span>
                            </label>
                        </div>
                    </td>
                </tr>

                </tbody>
            </table>
        </div>
        <div class="ncua-btn-group ncua-align-right">
            <!-- 초기화는 검색 요소 초기화 + 검색까지 되어야 합니다. -->
            <button type="reset" id="list-search-reset" class="ncua-btn ncua-btn--xs has-underline ncua-btn--text search-reset"">초기화</button>
            <button type="button" id="list-search" class="ncua-btn ncua-btn--xs ncua-btn--secondary"">검색</button>
        </div>

        <!-- 검색 결과 영역 -->
        <div class="ncua-search-result" id="ncuaSearchResult">
            <?php include $autoList; ?>
        </div>
    </section>
</section>
