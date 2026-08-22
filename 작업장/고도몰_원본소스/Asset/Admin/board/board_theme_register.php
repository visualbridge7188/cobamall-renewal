<link type="text/css" href="<?=gd_set_browser_cache(PATH_ADMIN_GD_SHARE . 'ncds/css/board/board-theme-register.css')?>" rel="stylesheet"/>
<article class="ncua-content board-theme-register">

    <form id="frmTheme" name="frmTheme" action="board_theme_ps.php" method="post" enctype="multipart/form-data">
        <input type="hidden" name="mode" value="<?= $data['mode']; ?>"/>
        <input type="hidden" name="sno" value="<?= $data['sno']; ?>"/>

        <header class="page-header ncua-page-header js-affix">
            <h3 class="ncua-help-manual"><button type="button" class="ncua-btn ncua-btn--md only-icon ncua-btn--secondary-gray ncua-history-back js-btn-back">뒤로가기</button>게시판 스킨 <?= ($data['mode'] == 'theme_register') ? '등록' : '수정'; ?></h3>
            <span class="ncua-page-header__actions">
                <button type="submit" class="ncua-btn ncua-btn--md ncua-btn--primary js-theme-submit"><?= ($data['mode'] == 'theme_register') ? '저장' : '수정'; ?></button>
            </span>
        </header>

        <?php include $boardThemeRegisterForm ?>

        
    </form>
</article>
<script type="text/javascript">
    <!--
    const bdKind = '<?=gd_isset($data['bdKind']); ?>';
    const themeId = '<?=gd_isset($data['themeId']); ?>';
    const bdMobileFl = '<?=gd_isset($data['bdMobileFl']); ?>';
    const isModify = '<?=$data['mode']?>' === 'theme_modify';

    function deleteIcon(themeId, iconType, device) {
        ifrmProcess.location.href = 'board_theme_ps.php?mode=deleteIcon&themeId=' + themeId + '&iconType=' + iconType + '&device=' + device;
    }


    const renderFileUpload = (prefix) => {
        let originImgPath = '';

        const contentType = <?= json_encode($iconTypeList) ?>;
        const contentTypeValues = Object.values(contentType);

        const fileUploadWrapper = document.querySelector(`#${prefix}${contentTypeValues[0]}`);

        if (fileUploadWrapper && fileUploadWrapper.hasChildNodes()) {
            return;
        }

        const resetPreview = (wrapper) => {
            const tagWrap = wrapper.querySelector('.ncua-file-tags');
            const inputFile = wrapper.querySelector('[type="file"]');
            const previewImg = wrapper.querySelector('img');
            const deleteBtn = wrapper.querySelector('.js-delete-preview-icon');

            if (isModify) {
                const modifyDelBtn = wrapper.querySelector('.js-ncua-icon-modify-delete');
                modifyDelBtn?.removeAttribute('hidden');
            }

            if (tagWrap) {
                tagWrap.remove();
            }

            deleteBtn.remove();
            previewImg.src = originImgPath;

            if (inputFile) {
                inputFile.value = '';
            }
        };


        const setFileDataToInput = (fileDatas, type, uploadWrapper) => {
            let fileInput = document.querySelector(`[name="${prefix}[${type}]"]`);
            const dataTransfer = new DataTransfer();

            if (!fileInput) {
                fileInput = document.createElement('input');
                fileInput.type = 'file';
                fileInput.name = `${prefix}[${type}]`;
                fileInput.style.display = 'none';
                uploadWrapper.appendChild(fileInput);
            }

            fileDatas.forEach(file => {
                dataTransfer.items.add(file);
            });

            fileInput.files = dataTransfer.files;
        };

        const setPreviewImg = (file, wrapper) => {
            const previewColumn = wrapper.querySelector('.board-theme-icon-preview');
            const img = previewColumn.querySelector('img');
            const objectURL = URL.createObjectURL(file);
            
            const existingDelBtn = previewColumn.querySelector('.js-delete-preview-icon');
            if (existingDelBtn) {
                existingDelBtn.remove();
            }

            const deleteBtn = document.createElement('button');

            deleteBtn.className = 'ncua-btn ncua-btn--xxs ncua-btn--secondary-gray js-delete-preview-icon';
            deleteBtn.type = 'button';
            deleteBtn.textContent = '삭제';

            deleteBtn.addEventListener('click', () => {
                resetPreview(wrapper);
            });

            const handleLoad = () => {
                if (isModify) {
                    const modifyDelBtn = previewColumn.querySelector('.js-ncua-icon-modify-delete');

                    if (modifyDelBtn) {
                        modifyDelBtn.setAttribute('hidden', true);
                    }
                }

                previewColumn.appendChild(deleteBtn);
                img.removeEventListener('load', handleLoad);
            };
            
            img.addEventListener('load', handleLoad, {once: true});

            if (!originImgPath) {
                originImgPath = img.src;
            }

            img.src = objectURL;
        };

        const setTag = (newFiles, type, uploadWrapper) => {
            const oldFileTagWrap = uploadWrapper.querySelector('.ncua-file-tags');

            if (oldFileTagWrap) {
                oldFileTagWrap.remove();
            }

            const createThumbnail = (tagEl) => {
                if (!tagEl) {
                    return;
                }

                const previewThumb = document.createElement('span');
                const previewThumbContent = document.createElement('span');

                previewThumb.className = 'ncua-file-tags';
                previewThumbContent.className = 'ncua-file-tags__content';
                previewThumbContent.appendChild(tagEl);
                previewThumb.appendChild(previewThumbContent);

                return previewThumb;
            };

            newFiles.forEach(file => {
                const uploadWrapper = document.querySelector(`#${prefix}${type}`).closest('.board-theme-register-icon-upload-wrapper');
                const wrapperTr = uploadWrapper.closest('tr');
                const previewColumn = wrapperTr.querySelector('.board-theme-icon-preview');

                const tag = new ncua.Tag({
                    text: file.name,
                    size: 'sm',
                    close: true,
                    onButtonClick: () => {
                        tag.destroy();
                        tag.getElement().remove();

                        resetPreview(wrapperTr);
                    },
                });

                if (file.type.includes('image')) {
                    setPreviewImg(file, wrapperTr);
                }

                uploadWrapper.appendChild(createThumbnail(tag?.getElement()));
            });
        }

        contentTypeValues.forEach(type => {
            const uploadWrapper = document.querySelector(`#${prefix}${type}`).closest('.board-theme-register-icon-upload-wrapper');
            const fileInput = new ncua.FileInput({
                container: `${prefix}${type}`,
                buttonLabel : `파일 찾기`,
                onChange: (newFiles) => {
                    if (newFiles.length === 0) {
                        return;
                    }

                    setFileDataToInput(newFiles, type, uploadWrapper);
                    setTag(newFiles, type, uploadWrapper);
                },
            });
        });
    }

    $(document).ready(function () {
        $('input[name=bdMobileFl]').bind('click', function () {
            let renderFileUploadPrefix = '';

            $('.js-pc-show').hide();
            $('.js-mobile-show').hide();

            if ($(this).val() == 'y') {
                $('.js-mobile-show').show();
                $('.js-mobile-hide').hide();
                renderFileUploadPrefix = 'boardIconMobile';
            }
            else {
                $('.js-pc-show').show();
                $('.js-mobile-hide').show();
                renderFileUploadPrefix = 'boardIcon';
            }
            renderFileUpload(renderFileUploadPrefix);
        });

        if (bdMobileFl == 'y') {
            $('.js-mobile-show').show();
            renderFileUpload('boardIconMobile');
        }
        else if (bdMobileFl == 'n') {
            $('.js-pc-show').show();
            renderFileUpload('boardIcon');
        }

        $('input[name=bdMobileFl]:checked').trigger('click');

        skin_modify_link = function (type) {
            if (bdMobileFl == 'y') {
                window.open('/mobile/design_page_edit.php?designPageId=board/skin/' + themeId + '/' + type + '.html');
            }
            else {
                window.open('/design/design_page_edit.php?designPageId=board/skin/' + themeId + '/' + type + '.html');
            }
        }
        const validId = function (id) {
            const regExp = /^[A-za-z][A-za-z0-9]{1,29}$/g;
            return regExp.test(id);
        }

        $.validator.addMethod("regx", function (value, element, fl) {
            return validId(value);
        }, "영문으로 시작해야하며 특수문자와 한글은 사용하실 수 없습니다.(2~30자)");

        $('#frmTheme').validate({
            debug: true,
            onclick: false,
            onfocusout: false,
            onkeyup: false,
            ignore: [],
            submitHandler: function (form) {
                form.target = 'ifrmProcess';
                form.submit();
            },
            invalidHandler: function(event, validator) {
                NCDSAlert({ message: validator.errorList[0].message, iconType: 'error' });
            },
            rules: {
                liveSkin: 'required',
                themeId: {
                    required: true,
                    minlength: 2,
                    maxlength: 30,
                    regx: true,
                    equalTo: '#chkThemeId',
                },
                themeNm: 'required',
                bdWidth: {
                    required: true,
                    number: true,
                },
                bdListLineSpacing: {
                    required: true,
                    number: true,
                }
            },
            messages: {
                themeId: {
                    required: '스킨코드를 입력해주세요.',
                    equalTo: '스킨코드 중복확인을 해주세요.',
                    minlength: '2~30자리까지 입력가능합니다.',
                    maxlength: '2~30자리까지 입력가능합니다.',
                },
                liveSkin: '적용 디자인 스킨을 선택해주세요.',
                themeNm: '스킨명을 입력해주세요.',
            }
        });

        $('[class^=input_int]').number_only('d');
        $(".display-none").find("*").prop("disabled", true);

        $('#overlap_themeId').bind('click', function () {
            const themeId = $('#themeId').val();

            if (!validId(themeId)) {
                NCDSAlert({message:  '영문으로 시작해야하며 특수문자와 한글은 사용하실 수 없습니다.(2~30자)', iconType: 'error'});
                return false;
            }

            let liveSkin = '';
            <?php if($gGlobal['isUse']){?>
            liveSkin = $('input[name=liveSkin]:checked').val();
            <?php }?>

            $.post('board_theme_ps.php', {'themeId': themeId, 'isMobile': $('input[name=bdMobileFl]:checked').val(), 'mode': 'overlapThemeId', 'liveSkin': liveSkin},
                function (data) {
                    if (data['result'] == 'ok') {
                        NCDSToast({message: data.msg, color: 'success'});
                        $('#themeId_msg').hide();
                        $('#chkThemeId').val(themeId);
                    } else {
                        NCDSAlert({message: data.msg, iconType: 'error'});
                    }
                })
        })
    });

    document.querySelector('.js-btn-back').addEventListener('click', function() {
        history.back();
    });

    const code = '251113006';
    if (window.GodoCosGuide) {
        window.GodoCosGuide.init({
            apiUrl: <?= json_encode($cosGuideApiUrl) ?>,
            guideCode: code,
        });
    }
    //-->
</script>


