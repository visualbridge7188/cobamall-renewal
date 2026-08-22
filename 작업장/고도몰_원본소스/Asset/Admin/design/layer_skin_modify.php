<script type="text/javascript">
    <!--
    $(document).ready(function(){
        $("#frmSkin").validate({
            submitHandler: function (form) {
                form.target = 'ifrmProcess';
                form.submit();
            },
            dialog: false
        });
    });
    //-->
</script>
<!-- TODO: form action 연결, 데이터 처리 -->
<form class="layer_skin_modify_form" id="frmSkin" name="frmSkin" action="design_skin_list_ps.php" method="post" enctype="multipart/form-data">
    <input type="hidden" name="mode" value="modifySkin" />
    <input type="hidden" name="skinName" value="<?php echo $skinInfo['skin_name']; ?>" />
    <input type="hidden" name="skinCode" value="<?php echo $skinInfo['skin_code']; ?>" />
    <input type="hidden" name="skinType" value="<?php echo $skinType;?>" />
    <input type="file" name="thumbnails" class="no-filestyle display-none" />

    <div class="modal-dialog__content">
        <div class="ncua-table ncua-table--vertical">
            <table>
                <colgroup>
                    <col width="144px"/>
                    <col/>
                </colgroup>
                <tbody>
                    <tr>
                        <th><div>스킨코드</div></th>
                        <td colspan="3">
                            <div><?php echo $skinInfo['skin_code']; ?></div>
                        </td>
                    </tr>

                    <tr>
                        <th><div>스킨명</div></th>
                        <td colspan="3">
                            <div class="ncua-flex ncua-flex-column ncua-align-items-start">
                                <div class="ncua-input ncua-input--xs ncua-input-full-width">
                                    <div class="ncua-input__content">
                                        <div class="ncua-input__field ncua-input__field--xs">
                                            <input type="text" name="skinName" value="<?php echo $skinInfo['skin_name']; ?>" placeholder="스킨명을 입력하세요" maxlength="20" />
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </td>
                    </tr>

                    <tr>
                        <th><div>썸네일 이미지</div></th>
                        <td colspan="3">
                            <div class="ncua-flex ncua-flex-column align-item-start">
                                <div id="uploadSkinThumbnail"></div>
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <th><div>적용가능 솔루션</div></th>
                        <td>
                            <div><?php echo $skinInfo['apply_solution']; ?></div>
                        </td>

                        <th><div>스킨 언어</div></th>
                        <td>
                            <div><?php echo $skinInfo['skin_language']; ?></div>
                        </td>
                    </tr>

                    <tr>
                        <th><div>배포일자</div></th>
                        <td>
                            <div><?php echo $skinInfo['skin_date']; ?></div>
                        </td>

                        <th><div>스킨버전</div></th>
                        <td>
                            <div><?php echo $skinInfo['skin_version']; ?></div>
                        </td>
                    </tr>

                    <tr>
                        <th><div>라이센스</div></th>
                        <td colspan="3">
                            <div><?php echo $skinInfo['skin_license']; ?></div>
                        </td>
                    </tr>
                    <tr>
                        <th><div>제작자</div></th>
                        <td>
                            <div><?php echo $skinInfo['author']; ?></div>
                        </td>
                        <th><div>제작사</div></th>
                        <td>
                            <div><?php echo $skinInfo['company']; ?></div>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>

    <div class="board-template-write-footer modal-dialog__footer">
        <button type="button" class="ncua-btn ncua-btn--sm ncua-btn--secondary-gray js-layer-close">취소</button>
        <input type="submit" value="변경" class="ncua-btn ncua-btn--sm ncua-btn--primary">
    </div>
</form>

<script type="text/javascript">
(function() {
    'use strict';
    const uploadSkinThumbnailInput = new ncua.ImageFileInput({
        container: 'uploadSkinThumbnail',
        buttonLabel: '파일 찾기',
        accept: 'image/*',
        maxFileCount: 1,
        hintItems: [
            'jpg, jpeg, png, gif만 등록 가능하며, 기본 이미지는 150x150 px 입니다.',
            '이미지명은 되도록이면 영문으로 올려주세요.'
        ],
        onFileSelect: (files) => {
            const fileInput = document.querySelector('input[name="thumbnails"]');
            const dataTransfer = new DataTransfer();
            
            if (files && files.length > 0) {
                dataTransfer.items.add(files[0]);
                fileInput.files = dataTransfer.files;
            }
        }
    });
})();
</script>

