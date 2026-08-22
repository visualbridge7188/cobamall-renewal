
<link type="text/css" href="<?=gd_set_browser_cache(PATH_ADMIN_GD_SHARE . 'ncds/css/board/plus-review-register.css')?>" rel="stylesheet"/>
<script defer src="<?=gd_set_browser_cache(PATH_ADMIN_GD_SHARE . 'ncds/script/common/switch.js')?>"></script>
<script defer src="<?=gd_set_browser_cache(PATH_ADMIN_GD_SHARE . 'ncds/script/common/file-input.js')?>"></script>

<article class="ncua-content">
    <div class="plus_review_register">
        <form name="frmWrite" id="frmWrite" action="plus_review_ps.php" method="post" enctype="multipart/form-data">
            <input type="hidden" name="mode" value="<?= $req['mode'] ?>">
            <input type="hidden" name="sno" value="<?= $req['sno'] ?>">
            <input type="hidden" name="popupMode" value="<?= $req['popupMode'] ?>">
            <input type="hidden" name="queryString" value="<?= $queryString ?>">

            <header class="page-header js-affix ncua-page-header">
                <h3>
                    <button type="button" class="ncua-btn ncua-btn--md only-icon ncua-btn--secondary-gray ncua-history-back js-btn-back">뒤로가기</button>
                    <?php echo end($naviMenu->location); ?>
                </h3>
                <div class="ncua-page-header__actions">
                    <?php if($req['popupMode'] != 'yes') { // CRM 팝업모드가 아닐 경우 ?>
                    <button type="button" class="ncua-btn ncua-btn--md ncua-btn--secondary-gray js-btn-list">목록</button>
                    <?php } ?>
                    <button class="ncua-btn ncua-btn--md ncua-btn--primary">수정</button>
                </div>
            </header>

            <!-- 플러스리뷰  게시글 수정 -->
            <?php include $plusReviewRegister ?>
        </form>
    </div>
</article>
<script>
    $(document).ready(function () {
        $("#frmWrite").validate({
            dialog: false,
            ignore: [], // file input hidden
            invalidHandler: function(event, validator) {
                if (validator.errorList.length > 0) {
                    NCDSAlert({
                        message: validator.errorList[0].message,
                        iconType: 'error'
                    });
                }
            },
            submitHandler: function (form) {
                form.target = 'ifrmProcess';
                form.submit();
            },
            rules: {
                contents: {
                    required: true,
                },
            },
            messages: {
                contents: {
                    required: '내용을 입력해주세요'
                },

            }
        });

        // 뒤로가기 버튼
        document.querySelector('.js-btn-back')?.addEventListener('click', () => {
            history.back();
        });

        // 상단 > [목록] 버튼
        $('.js-btn-list').bind('click', function () {
            location.href = "plus_review_list.php?" + getUrlVars();
        })


    function getUrlVars(paramKey) {
        if (typeof paramKey == 'undefined') {
            paramKey = '';
        }
        var vars = [], hash;
        var hashes = window.location.href.slice(window.location.href.indexOf('?') + 1).split('&');
        if (window.location.href.indexOf('?') < 0) {
            return '';
        }
        for (var i = 0; i < hashes.length; i++) {
            hash = hashes[i].split('=');
            key = hash[0];
            val = hash[1];
            if (paramKey != '') {
                if (key == paramKey) {
                    return val;
                }
            }

            if (key == 'sno' || key == 'mode') {
                continue;
            }

            vars.push(hashes[i]);
        }

        if (paramKey != '') {
            return '';
        }

        return vars.join('&');
    }
})
</script>


<!-- FILE INPUT -->
 <script>
    // FileInput이 로드될 때까지 대기
    const initFileInput = () => {
        if (typeof FileInput === 'undefined') {
            console.error('FileInput이 로드되지 않았습니다. file-input.js 파일이 제대로 로드되었는지 확인하세요.');
            return;
        }
        
        const tagInstances = new Map();
        const MAX_UPLOAD_COUNT = <?=$config['uploadMaxCount']?>;
        const fileTagContainer = document.getElementById('fileTagContainer');

        let currentFiles = [];
        let uploadFiles = [];

        const fileTagContainerTagCount = fileTagContainer.children.length;
        fileTagContainer.style.display = fileTagContainerTagCount > 0 ? 'block' : 'none';

        // [파일 찾기]
        new ncua.FileInput({
            container: 'fileInputContainer',
            buttonLabel : '파일 찾기',
            multiple: true,
            maxFileCount : MAX_UPLOAD_COUNT,
            hintItems: [
                `파일은 최대 ${MAX_UPLOAD_COUNT}개까지 다중업로드가 지원됩니다.`,
            ],
            accept:'image/*',
            fileInputName: 'upfiles[]',
            checkboxName: 'delFile[]',
            onChange: (newFiles) => {
                // 파일 유효성 검사
                const validation = FileInput.validateFiles({ currentFiles, newFiles, maxCount: MAX_UPLOAD_COUNT });
                if (!validation.valid) {
                    NCDSAlert({ message: validation.message });
                    return;
                }

                window.onbeforeunload = function () {
                    if (uploadFiles.length == 0) {
                        return false;
                    }
                }

                FileInput.setupAjaxUploadForm('frmWrite');

                // 각 파일을 개별적으로 업로드
                (async () => {
                    for (const file of newFiles) {
                        const formData = new FormData();
                        formData.append('goodsNo', <?=$data['goodsNo']?>);
                        formData.append('mode', 'ajaxUpload');
                        formData.append('uploadFile', file);

                        await FileInput.uploadFileViaAjax({
                            ajaxUrl: './plus_review_ps.php',
                            formData: formData,
                            uploadFiles: uploadFiles,
                            file: file,
                            fileTagOptions: {
                                fileTagContainer: fileTagContainer,
                                tagInstances: tagInstances,
                                currentFiles: currentFiles,
                                needFilePreview: true,
                            },
                        });
                    }
                })();
            }
        });


        // 기존 파일 태그 생성
        <?php
        $uploadFileNm = [];
        $uploadedFiles = [];
        if (gd_isset($data['uploadedFile']) && is_array($data['uploadedFile'])) {
            foreach ($data['uploadedFile'] as $file) {
                if (gd_isset($file['uploadFileNm'])) {
                    $uploadFileNm[] = $file['uploadFileNm'];
                    $uploadedFiles[] = [
                        'uploadFileNm' => $file['uploadFileNm'],
                        'saveFileNm' => gd_isset($file['saveFileNm'], ''),
                        'thumSrc' => gd_isset($file['thumSrc'], ''),
                        'src' => gd_isset($file['src'], '')
                    ];
                }
            }
        }
        ?>
        
        const uploadedFileNames = <?=json_encode($uploadFileNm, JSON_UNESCAPED_UNICODE)?>;
        const uploadedFiles = <?=json_encode($uploadedFiles, JSON_UNESCAPED_UNICODE)?>;
      
        if (uploadedFileNames && uploadedFileNames.length > 0) {
            const fileTagOptions = {
                fileTagContainer: fileTagContainer,
                tagInstances: tagInstances,
                currentFiles: currentFiles,
                needFilePreview: true,
            };

            FileInput.createFileTagFromExistingFile({ uploadedFileNames, uploadedFiles, fileTagOptions });    
        }
    }
    
    // DOM이 로드된 후 실행 (defer 속성이 있어도 안전하게 처리)
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initFileInput);
    } else {
        // DOM이 이미 로드된 경우 즉시 실행
        initFileInput();
    }
</script>
