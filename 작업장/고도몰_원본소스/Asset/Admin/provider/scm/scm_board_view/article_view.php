<section class="ncua-card">
    <header class="ncua-card__header">
        <h4 class="ncua-card__title">게시글 보기</h4>
    </header>
    <section class="ncua-card__body">
        <div class="ncua-table ncua-table--vertical">
            <table>
                <colgroup>
                    <col/>
                    <col/>
                </colgroup>
                <tr>
                    <th><div>대상</div></th>
                    <td><div><?= $data['scmTarget'] ?></div></td>
                </tr>
                <tr>
                    <th><div>제목</div></th>
                    <td><div><?= $data['subject'] ?></div></td>
                </tr>
                <tr>
                    <th><div>카테고리</div></th>
                    <td><div><?= $data['categoryText'] ?></div></td>
                </tr>
                <tr>
                    <th><div>작성자</div></th>
                    <td><div><?= $data['writer'] ?></div></td>
                </tr>
                <tr>
                    <th><div>파일첨부</div></th>
                    <td><div><?= $data['uploadFileList'] ?></div></td>
                </tr>
                <tr>
                    <th><div>내용</div></th>
                    <td><div class="article-content"><?= $data['contents'] ?></div></td>
                </tr>
                <tr>
                    <th><div>작성일</div></th>
                    <td>
                        <div><?= $data['regDt'] ?></div>
                    </td>
                </tr>
            </table>
        </div>
        <div class="article-pagination">
            <?php if ($nextData = $data['relationList']['nextData']) { ?>
                <div class="article-pagination__item">
                    <span class="article-pagination__item-label">다음글</span>
                    <a class="article-pagination__item-title" href="scm_board_view.php?sno=<?= $nextData['sno'] ?>">
                        <?= $nextData['subject'] ?>
                    </a>
                    <span class="article-pagination__item-writer"><?= $nextData['managerId'] ?>(<?= $nextData['managerNm'] ?>)</span>
                    <span class="article-pagination__item-date"><?= $nextData['regDt'] ?></span>
                </div>
            <?php } ?>

            <?php if ($groupData = $data['relationList']['groupData']) {
                foreach ($groupData as $val) {
                    $startBoldTag = $closeBoldTag = '';
                    $info = '-';
                    if ($req['sno'] == $val['sno']) {
                        $startBoldTag = '<b>';
                        $closeBoldTag = '</b>';
                        $info = '현재글';
                    }
                    ?>
                    <div class="article-pagination__item <?= $req['sno'] == $val['sno'] ? 'current-article' : '' ?>">
                        <span class="article-pagination__item-label"><?= $info ?></span>
                        <a class="article-pagination__item-title" href="scm_board_view.php?sno=<?= $val['sno'] ?>">
                            <?= $val['replyIcon'] . $startBoldTag . $val['subject'] . $closeBoldTag ?>
                        </a>
                        <span class="article-pagination__item-writer"><?= $val['managerId'] ?>(<?= $val['managerNm'] ?>)</span>
                        <span class="article-pagination__item-date"><?= $val['regDt'] ?></span>
                    </div>
                    <?php
                }
            } ?>
            <?php if ($prevData = $data['relationList']['prevData']) { ?>
                <div class="article-pagination__item">
                    <span class="article-pagination__item-label">이전글</span>
                    <a class="article-pagination__item-title" href="scm_board_view.php?sno=<?= $prevData['sno'] ?>">
                        <?= $prevData['subject'] ?>
                    </a>
                    <span class="article-pagination__item-writer"><?= $prevData['managerId'] ?>(<?= $prevData['managerNm'] ?>)</span>
                    <span class="article-pagination__item-date"><?= $prevData['regDt'] ?></span>
                </div>
            <?php } ?>
        </div>
    </section>
</section>
<div class="button-wrap">
    <?php if ($data['isNotice'] == 'n') { ?>
        <a href="scm_board_register.php?mode=reply&sno=<?= $req['sno'] ?>" class="ncua-btn ncua-btn--sm ncua-btn--secondary-gray">답변</a>
    <?php } ?>
    <?php if ($data['auth'] == 'y') { ?>
        <a href="scm_board_register.php?mode=modify&sno=<?= $req['sno'] ?>" class="ncua-btn ncua-btn--sm ncua-btn--secondary-gray">수정</a>
        <button onclick="articleDelete('<?=$req['sno']?>')" class="ncua-btn ncua-btn--sm ncua-btn--destructive">삭제</button>
    <?php } ?>
</div>
