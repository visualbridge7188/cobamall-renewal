<section class="ncua-card">
    <header class="ncua-card__header">
        <h4 class="ncua-card__title">상단 하단 꾸미기</h4>
    </header>
    <section class="ncua-card__body board-template-list">
        <div class="ncua-table ncua-table--vertical">
            <table>
            <tr>
                <th><div>상단디자인<br>(Header)</div></th>
                <td>
                    <div class="editor-container">
                        <textarea name="bdHeader" data-max-width="100%" data-godo-editor="basic-editor" id="editor" label="상단디자인" imageUploadCallback="handleGodoImageUpload(images, editor)">
                            <?=gd_isset($data['bdHeader']); ?>
                        </textarea>
                    </div>
                </td>
            </tr>
            <tr>
                <th><div>하단디자인<br>(Footer)</div></th>
                <td>
                    <div class="editor-container">
                        <textarea name="bdFooter" data-max-width="100%" data-godo-editor="basic-editor" id="editor2" label="하단디자인" imageUploadCallback="handleGodoImageUpload(images, editor)">
                            <?=gd_isset($data['bdFooter']); ?>
                        </textarea>
                    </div>
                </td>
            </tr>
            </table>
        </div>
    </section>
</section>

