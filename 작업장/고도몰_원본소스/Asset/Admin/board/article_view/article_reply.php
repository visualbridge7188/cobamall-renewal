<?php if ($bdView['cfg']['bdReplyStatusFl'] == 'y') { ?>
    <section class="ncua-card">
        <header class="ncua-card__header">
            <h4 class="ncua-card__title">게시글답변</h4>
        </header>
        <section class="ncua-card__body">
            <div class="ncua-table ncua-table--vertical">
                <table> 
                    <col class="width-md"/>
                    <col/>
                    <tr>
                        <th><div>답변 작성자</div></th>
                        <td>
                            <div><?= $bdView['data']['answerWriter'] ?></div>
                        </td>
                    </tr>

                    <tr>
                        <th><div>답변 상태</div></th>
                        <td>
                            <div><?= $bdView['data']['replyStatusText'] ?></div>
                        </td>
                    </tr>

                    <tr>
                        <th><div>답변 제목</div></th>
                        <td>
                            <div><?= gd_isset($bdView['data']['answerSubject'], '-') ?></div>
                        </td>
                    </tr>
                    <tr>
                        <th><div>답변 내용</div></th>
                        <td>
                            <div class="article-reply-contents"><?= gd_isset($bdView['data']['workedAnswerContents'], '-'); ?></div>
                        </td>
                    </tr>
                </table>
            </div>
        </section>
    </section>
<?php } ?>
