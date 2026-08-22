<form class="skin-upload-form" id="frmSkinUpload" name="frmSkinUpload" action="design_skin_list_ps.php" method="post" enctype="multipart/form-data">
    <input type="hidden" name="mode" value="uploadSkin" />
    <input type="file" name="uploadSkin" class="no-filestyle display-none"/>
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
                        <th class="ncua-required"><div>스킨유형</div></th>
                        <td>
                            <div class="ncua-flex ncua-align-items-start skin-upload-form--skin-type-radio-group">
                                <label class="ncua-radio-field ncua-radio-field--xs has-text">
                                    <span class="ncua-radio-input ncua-radio-input--xs ncua-radio-field__input">
                                        <input type="radio" name="skinType" value="front" checked="checked"/>
                                    </span>
                                    <span>
                                        <span class="ncua-radio-field__text">PC</span>
                                    </span>
                                </label>

                                <label class="ncua-radio-field ncua-radio-field--xs has-text">
                                    <span class="ncua-radio-input ncua-radio-input--xs ncua-radio-field__input">
                                        <input type="radio" name="skinType" value="mobile" />
                                    </span>
                                    <span>
                                        <span class="ncua-radio-field__text">모바일</span>
                                    </span>
                                </label>
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <th class="ncua-required"><div>스킨코드</div></th>
                        <td>
                            <div class="ncua-flex ncua-flex-column ncua-align-items-start">
                                <div class="ncua-input ncua-input--xs ncua-input-full-width">
                                    <div class="ncua-input__content">
                                        <div class="ncua-input__field ncua-input__field--xs">
                                            <input type="text" name="uploadSkinCode" maxlength="20" placeholder="게시글의 제목을 입력해주세요" class="ncua-input__field--xs" />
                                        </div>
                                    </div>
                                    <span class="ncua-hint-text ncua-input__hint-text">스킨코드는 영문, 숫자, _만 입력하세요.</span>
                                </div>
                            </div>
                        </td>
                    </tr>

                    <tr>
                        <th><div>스킨명</div></th>
                        <td>
                            <div class="ncua-flex ncua-flex-column ncua-align-items-start">
                                <div class="ncua-input ncua-input--xs ncua-input-full-width">
                                    <div class="ncua-input__content">
                                        <div class="ncua-input__field ncua-input__field--xs">
                                            <input type="text" name="uploadSkinName" placeholder="스킨명을 입력하세요." class="ncua-input__field--xs" />
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </td>
                    </tr>

                    <tr>
                        <th class="ncua-required"><div>업로드</div></th>
                        <td class="js-upload-cell">
                            <div id="uploadSkinFile" class="upload-skin-file-container"></div>
                            <div class="upload-skin-tag-container">
                                <p id="tag-container"></p>
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <th><div>썸네일 이미지</div></th>
                        <td>
                            <div id="uploadSkinThumbnail"></div>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>

    <div class="board-template-write-footer modal-dialog__footer">
        <button type="button" class="ncua-btn ncua-btn--sm ncua-btn--secondary-gray js-layer-close">취소</button>
        <input type="submit" value="업로드" class="ncua-btn ncua-btn--sm ncua-btn--primary">
    </div>
</form>

<script type="text/javascript">
// validator 플러그인 직접 선언
$.validator.addMethod( "alphanumeric", function( value, element ) {
    return this.optional( element ) || /^\w+$/i.test( value );
}, "Letters, numbers, and underscores only please" );

function fileNameCheck(inputFileName){

    var agent = navigator.userAgent.toLowerCase();
    os = {
        Windows : /win/.test(agent),
        Mac : /mac/.test(agent),
        Linux : /linux/.test(agent),
        Unix : /x11/.test(agent)
    };
    if(!os.Windows){
        return true;
    }

    var fileValue = $('input[name='+inputFileName+']').val().split("\\");
    var fileName = fileValue[fileValue.length-1];
    var fileNameArry = fileName.split('.');
    var ext = fileNameArry.pop();
    var realName = fileNameArry.join('.');
    if(!/^\w+$/i.test( realName )){
        NCDSAlert({
            message: '업로드 파일명은 영문과 숫자만 입력해주세요.',
            iconType: 'error'
        });
        return false;
    }else{
        return true;
    }
}

$(document).ready(function(){
    // TODO: validation 체크 부탁드립니다.
    $("#frmSkinUpload").validate({
        invalidHandler: function(event, validator) {
            if (validator.errorList.length > 0) {
                NCDSAlert({
                    message: validator.errorList[0].message,
                    iconType: 'error'
                });
            }
        },
        submitHandler: function (form) {
            if(!fileNameCheck('uploadSkin')){
                return false;
            }
            form.target = 'ifrmProcess';
            form.submit();
        },
        dialog: false,
        rules: {
            uploadSkinCode: {
                required: true,
                alphanumeric: true
            },
            uploadSkin: "required"
        },
        messages: {
            uploadSkinCode: {
                required: '스킨코드를 입력해 주세요.',
                alphanumeric: '스킨코드는 영문, 숫자, _만 입력해주세요.'
            },
            uploadSkin: {
                required: '압축 파일(ZIP 파일)을 선택해 주세요.'
            }
        }
    });

    const uploadSkinFileInput = new ncua.FileInput({
        container: 'uploadSkinFile',
        buttonLabel: '파일 찾기',
        accept: '',
        hintItems: [
            '압축파일의 종류는 ZIP 파일만 가능합니다.',
            '압축파일의 용량은 <?php echo ini_get('upload_max_filesize');?>미만이여야 정상적으로 업로드가 가능합니다.'
        ],
        maxFileCount: 1,
        onChange: (files) => {
            const uploadCell = document.querySelector('.js-upload-cell');

            if (!uploadCell) return;

            const uploadSkinContainer = uploadCell?.querySelector('#uploadSkinFile');
            if (!uploadSkinContainer) return;

            const fileTags = uploadCell?.querySelector('#tag-container');
            const fileInput = document.querySelector('input[name="uploadSkin"]');

            if (!fileTags || !fileInput) return;

            if (!files || files.length === 0) {
                fileInput.value = '';
                uploadSkinFileInput.setDisabled(false);
                return;
            }

            const file = files[0];

            if(!['application/zip', 'application/x-zip-compressed', 'application/x-zip'].includes(file.type)){
                NCDSAlert({
                    message: '압축 파일(ZIP 파일)을 선택해 주세요.',
                    iconType: 'error'
                });
                return false;
            }

            const tag = new ncua.Tag({
                text: file.name,
                size: 'sm',
                close: true,
                onButtonClick: () => {
                    tag.destroy();
                    tag.getElement().remove();
                    fileInput.value = '';
         
                    uploadSkinFileInput.setDisabled(false);
                }
            });

            fileTags.innerHTML = '';
            fileTags.appendChild(tag.getElement());

            try {
                const dataTransfer = new DataTransfer();
                dataTransfer.items.add(file);
                fileInput.files = dataTransfer.files;
                uploadSkinFileInput.setDisabled(true);
            } catch (error) {
                console.error('Error setting file input:', error);
                uploadSkinFileInput.setDisabled(false);
            }
        }
    });

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
});

</script>
