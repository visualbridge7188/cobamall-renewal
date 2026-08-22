<link type="text/css" href="<?=gd_set_browser_cache(PATH_ADMIN_GD_SHARE . 'ncds/css/ncds-editor/'. NCDS_EDITOR_VERSION .'/ncds-editor.css')?>" rel="stylesheet"/>
<link type="text/css" href="<?=gd_set_browser_cache(PATH_ADMIN_GD_SHARE . 'ncds/css/board/scm-board-register.css')?>" rel="stylesheet"/>
<script defer type="text/javascript" src="<?=gd_set_browser_cache(PATH_ADMIN_GD_SHARE . 'ncds/script/ncds-editor/'. NCDS_EDITOR_VERSION .'/ncds-editor.js')?>"></script>

<article class="ncua-content scm-board-register">
    <form id="frmWrite" action="scm_board_ps.php" method="post" enctype="multipart/form-data">
        <header class="ncua-page-header page-header js-affix">
            <h3><button type="button" class="ncua-btn ncua-btn--md only-icon ncua-btn--secondary-gray ncua-history-back js-btn-back">뒤로가기</button><?= end($naviMenu->location); ?></h3>
            <div class="ncua-page-header__actions">
                <button type="button" class="ncua-btn ncua-btn--md ncua-btn--secondary-gray" onclick="goList('./scm_board_list.php');">목록</button>
                <button type="submit" class="ncua-btn ncua-btn--md ncua-btn--primary"><?= $data['mode'] == 'modify' ? '수정' : '등록' ?></button>
            </div>
        </header>
        <?php include $articleRegister ?>
    </form>
</article>
<script type="text/javascript">
    /**
     * 파일 업로드 검증 함수
     * @param {File[]} currentFiles - 현재 업로드된 파일 배열
     * @param {File[]} newFiles - 새로 추가할 파일 배열
     * @param {number} maxCount - 최대 파일 개수
     * @param {number} maxSize - 최대 파일 크기 (MB)
     * @returns {{valid: boolean, message: string}} 검증 결과
     */
    const validateFiles = (currentFiles, newFiles, maxCount, maxSize) => {
        // 파일 개수 검증
        const totalCount = currentFiles.length + newFiles.length;
        if (totalCount > maxCount) {
            return {
                valid: false,
                message: `파일은 최대 ${maxCount}개까지 업로드 가능합니다.`
            };
        }

        // 파일 크기 검증
        const maxSizeBytes = maxSize * 1024 * 1024;
        for (let i = 0; i < newFiles.length; i++) {
            const file = newFiles[i];
            if (file.size > maxSizeBytes) {
                return {
                    valid: false,
                    message: `파일 "${file.name}"의 크기가 최대 허용 크기(${maxSize}MB)를 초과합니다.`
                };
            }
        }

        // 중복 파일 검증
        const currentFileNames = currentFiles.map(f => f.name.toLowerCase());
        for (let i = 0; i < newFiles.length; i++) {
            const newFileName = newFiles[i].name.toLowerCase();
            if (currentFileNames.includes(newFileName)) {
                return {
                    valid: false,
                    message: `동일한 파일명 "${newFiles[i].name}"이 이미 업로드되어 있습니다.`
                };
            }
        }
        return {
            valid: true,
        };
    }
</script>
<script type="text/javascript">
    const MAX_UPLOAD_COUNT = <?=$maxUploadCount?>;
    const MAX_UPLOAD_SIZE = <?=$maxUploadSize?>;
    
    const tagInstances = new Map();
    let currentFiles = [];

    /**
     * 파일 태그 생성 함수
     * @param {string} fileName - 파일명
     * @param {File|Object} file - File 객체 또는 파일 정보 객체
     * @param {Object} options - 옵션 객체
     * @param {boolean} options.isExistingFile - 기존 파일 여부
     * @param {number} options.index - 기존 파일의 인덱스 (기존 파일일 경우)
     * @param {HTMLElement} options.container - 태그를 추가할 컨테이너 요소
     */
    function createFileTag(fileName, file, options) {
        const { isExistingFile = false, index = null, container } = options;
        
        // 태그 생성
        const fileTag = new ncua.Tag({
            text: fileName,
            size: 'sm',
            close: true,
            onButtonClick: () => {
                const tagInstance = tagInstances.get(fileName);
                if (tagInstance && tagInstance.wrapper) {
                    if (isExistingFile) {
                        // 기존 파일: checkbox를 checked 상태로 변경
                        const hiddenInput = tagInstance.wrapper.querySelector('input[type="checkbox"]');
                        if (hiddenInput) {
                            hiddenInput.checked = true;
                        }
                        // DOM에서 태그 요소만 제거
                        tagInstance.element.remove();
                        tagInstance.wrapper.style.display = 'none';
                    } else {
                        // 새 파일: 전체 wrapper 제거
                        tagInstance.wrapper.remove();
                    }
                }
                // Map에서 태그 인스턴스 삭제
                tagInstances.delete(fileName);
                // 파일 배열에서 제거
                currentFiles = currentFiles.filter(f => f.name !== fileName);
            }
        });
        
        // hidden input 생성
        const hiddenInput = document.createElement('input');
        hiddenInput.hidden = true;
        
        if (isExistingFile) {
            // 기존 파일: checkbox 사용
            hiddenInput.type = 'checkbox';
            hiddenInput.name = 'delFile[' + index + ']';
            hiddenInput.value = 'y';
        } else {
            // 새 파일: file input 사용
            hiddenInput.type = 'file';
            hiddenInput.tabIndex = -1;
            hiddenInput.setAttribute('aria-hidden', 'true');
            hiddenInput.className = 'no-filestyle';
            hiddenInput.accept = '';
            hiddenInput.name = 'uploadFiles[]';
            
            // FileList를 DataTransfer로 변환하여 파일 설정
            const dataTransfer = new DataTransfer();
            dataTransfer.items.add(file);
            hiddenInput.files = dataTransfer.files;
        }
        
        // div로 감싸기
        const wrapperDiv = document.createElement('div');
        wrapperDiv.className = 'ncua-file-tags__content';
        wrapperDiv.appendChild(fileTag.element);
        wrapperDiv.appendChild(hiddenInput);
        
        // 태그 인스턴스에 wrapper 정보 저장
        fileTag.wrapper = wrapperDiv;
        tagInstances.set(fileName, fileTag);
        
        // 감싼 div를 container에 추가
        container.appendChild(wrapperDiv);
    }

    const fileInput = new ncua.FileInput({
        container: 'fileInputContainer',
        buttonLabel : '파일 찾기',
        accept : '.gif,.png,.jpeg,.jpg,.pdf,.dmg',
        maxFileCount : MAX_UPLOAD_COUNT,
        maxFileSize : MAX_UPLOAD_SIZE * 1024 * 1024,
        hintItems: ['파일은 1개씩 첨부 가능하며 최대 첨부 가능한 파일은 '+MAX_UPLOAD_COUNT+'개입니다.', ' 파일 업로드 최대 사이즈는 '+MAX_UPLOAD_SIZE+'MB 입니다.'],
        fileInputName: 'uploadFiles[]',
        onChange: (newFiles) => {
            // 파일 검증
            const validation = validateFiles(currentFiles, newFiles, MAX_UPLOAD_COUNT, MAX_UPLOAD_SIZE);
            if (!validation.valid) {
                NCDSAlert({message: validation.message, iconType: 'error'});
                return;
            }
            
            currentFiles = [...currentFiles, ...newFiles];
            const fileTagContainer = document.getElementById('fileTagContainer');
            
            newFiles.forEach(file => {
                createFileTag(file.name, file, {
                    isExistingFile: false,
                    container: fileTagContainer
                });
            });
        }
    });


    const uploadFileNm = <?=json_encode(gd_isset($data['uploadFileNm']) && is_array($data['uploadFileNm']) ? $data['uploadFileNm'] : [], JSON_UNESCAPED_UNICODE)?>;

    if (uploadFileNm && uploadFileNm.length > 0) {
        const fileTagContainer = document.getElementById('fileTagContainer');
        
        uploadFileNm.forEach((fileName, i) => {
            if (!fileName) return;

            // 기존 파일명을 currentFiles에 추가 (검증 함수에서 사용하기 위해 name 속성을 가진 객체로 생성)
            currentFiles = [...currentFiles, { name: fileName }];
            
            createFileTag(fileName, { name: fileName }, {
                isExistingFile: true,
                index: i,
                container: fileTagContainer
            });
        });
    }
</script>


<script type="text/javascript">
    $(document).ready(function () {
        $("#frmWrite").validate({
            ignore: [],
            submitHandler: function (form) {
                if ($('input[name="scmFl"]:checked').val() == 'y') {
                    if ($('input[name="scmNo[]"]').length === 0) {
                        NCDSAlert({message: '대상을 선택해주세요.', iconType: 'error'});
                        return false;
                    }
                } 

                form.target = 'ifrmProcess';
                form.submit();
            },
            // onclick: false, // <-- add this option
            rules: {
                scmFl: 'required',
                subject: 'required',
                contents: 'required',
            },
            messages: {
                scmFl: {
                    required: '대상을 체크해주세요.',
                },
                subject: {
                    required: '제목을 입력해주세요.'
                },
                contents: {
                    required: '내용을 입력해주세요'
                },
            },
            invalidHandler: function(form, validator) {
                    if (validator.errorList.length > 0) {
                        NCDSAlert({message: validator.errorList[0].message, iconType: 'error'});
                }
            }
        });

        $('body').on('click', '.addUploadBtn', function () {
            if ($('input[name="uploadFiles[]"]').length >= 5) {
                NCDSAlert({message: "파일은 최대 "+MAX_UPLOAD_COUNT+"개까지 업로드를 지원합니다", iconType: 'error'});
                return;
            }
            const addUploadBox = _.template(
                $("script.template").html()
            );
            $(this).closest('ul').append(addUploadBox);
            init_file_style();
        });

        $('body').on('click', '.minusUploadBtn', function () {
            $(this).closest('li').remove();
        });

    });

    /**
     * 카테고리 연결하기 Ajax layer
     */
    function layer_register(typeStr, mode, isDisabled) {

        const addParam = {
            "mode": mode,
        };

        if (typeStr == 'scm') {
            $('input:radio[name=scmFl]:input[value=y]').prop("checked", true);
        }

        if (!_.isUndefined(isDisabled) && isDisabled == true) {
            addParam.disabled = 'disabled';
        }

        layer_add_info(typeStr,addParam);
    }
    document.querySelector('.js-btn-back').addEventListener('click', function() {
        history.back();
    });
</script>
<script type="text/template" class="template">
    <li class="form-inline mgb5">
        <input type="file" name="uploadFiles[]">
        <a class="btn btn-white btn-icon-minus minusUploadBtn btn-sm">삭제</a>
    </li>
</script>
