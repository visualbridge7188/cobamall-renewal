<link type="text/css" href="<?=gd_set_browser_cache(PATH_ADMIN_GD_SHARE . 'ncds/css/ncds-editor/'. NCDS_EDITOR_VERSION .'/ncds-editor.css')?>" rel="stylesheet"/>
<link type="text/css" href="<?=gd_set_browser_cache(PATH_ADMIN_GD_SHARE . 'ncds/css/board/article-write-form.css')?>" rel="stylesheet"/>
<script defer type="text/javascript" src="<?=gd_set_browser_cache(PATH_ADMIN_GD_SHARE . 'ncds/script/ncds-editor/'. NCDS_EDITOR_VERSION .'/ncds-editor.js')?>"></script>

<article class="ncua-content article-write-form">
    <form name="frmWrite" id="frmWrite" action="article_ps.php" method="post" enctype="multipart/form-data">
        <input type="hidden" name="queryString" value=""/>
        <input type="hidden" name="isAdmin" value="true"/>
        <div class="ncua-page-header page-header js-affix">
            <h3 class="<?php if (!gd_is_provider()) { ?>ncua-help-manual<?php } ?>"><button type="button" class="ncua-btn ncua-btn--md only-icon ncua-btn--secondary-gray ncua-history-back js-btn-back">뒤로가기</button><?php echo end($naviMenu->location); ?></h3>
            <div class="ncua-page-header__actions">
                <?php if($req['popupMode'] !='yes') { // CRM 팝업모드가 아닐 경우 ?>
                <button type="button" class="ncua-btn ncua-btn--md ncua-btn--secondary-gray" onclick="goList('<?= $adminList; ?>');">목록</button>
                <?php } ?>
                <button type="submit" class="ncua-btn ncua-btn--md ncua-btn--primary"><?= $mode ?></button>
            </div>
        </div>
        <?php
        if ($req['mode'] == 'reply') {
            include "_article_detail.php";
        }
        ?>
        <?php include $articleWrite ?>
    </form>
</article>



<script type="text/javascript">
    var bdId = '<?=$bdWrite['cfg']['bdId']?>';

    function setAddGoods (frmData) {
        let selectGoodsContentHtml = '';
        frmData.info.forEach((val) => {
            selectGoodsContentHtml = `
                <div class="ncua-border-product-image-layout">
                    <div class="ncua-border-product-image">
                        <input type="hidden" name="goodsNo[]" value="${val.goodsNo}">
                        <a href="<?= URI_HOME ?>goods/goods_view.php?goodsNo=${val.goodsNo}" target="_blank">
                            <img src="${val.goodsImgageSrc}" width="100">
                        </a>
                    </div>
                    <div class="ncua-border-product-detail">
                        <div class="ncua-border-product-detail-item">
                            <div class="ncua-product-label">상품명</div>
                            <div class="ncua-product-value">
                                <button type="button" class="ncua-product-link" onclick="goods_register_popup('${val.goodsNo}' <?php if(gd_is_provider()) { echo ",'1'"; } ?>);">
                                    ${val.goodsName}
                                </button>
                            </div>
                        </div>
                        <div class="ncua-border-product-detail-item">
                            <div class="ncua-product-label">판매가</div>
                            <div class="ncua-product-value">${val.goodsPrice}</div>
                        </div>
                    </div>
                </div>
            `;
        });
        const selectGoodsElement = document.querySelector("#selectGoods");
        selectGoodsElement.innerHTML = selectGoodsContentHtml;
        selectGoodsElement.style.display = 'block';

        // 상품이 추가되면 삭제 버튼 보이기
        document.querySelectorAll(".js-select-remove").forEach(btn => btn.style.display = 'inline-block');
    }

    function setAddOrder (frmData) {
        let selectOrderContentHtml = '';
        frmData.info.forEach((val) => {
            let goodsLinkTag = '';
            let goodsLinkTagClose = '';
            if (val.goodsType == 'goods') {
                goodsLinkTag = `<a href="<?=URI_HOME?>goods/goods_view.php?goodsNo=${val.goodsNo}" target="_blank">`;
                goodsLinkTagClose = '</a>';
            }

            const addGoodsPopupFunction = val.goodsType == 'addGoods' ?
                `addgoods_register_popup('${val.goodsNo}' <?php if(gd_is_provider()) { echo ",'1'"; } ?>)` :
                `goods_register_popup('${val.goodsNo}' <?php if(gd_is_provider()) { echo ",'1'"; } ?>)`;

            selectOrderContentHtml = `
                <div class="ncua-border-product-image-layout">
                    <div class="ncua-border-product-image">
                        <input type="hidden" name="orderGoodsNo[]" value="${val.orderGoodsNo}">
                        ${goodsLinkTag}<img src="${val.goodsImgageSrc}" width="100" height="100">${goodsLinkTagClose}
                    </div>
                    <div class="ncua-border-product-detail">
                        <div class="ncua-border-product-detail-item">
                            <div class="ncua-product-label">주문 정보</div>
                            <div class="ncua-product-value">
                                <a class="ncua-product-link" href="<?= URI_ADMIN ?><?= gd_is_provider() ? 'provider/' : '' ?>order/order_view.php?orderNo=${val.orderNo}" target="_blank">${val.orderNo}</a> | ${val.regDt}<br>
                            </div>
                        </div>
                        <div class="ncua-border-product-detail-item">
                            <div class="ncua-product-label">주문 상품</div>
                            <div class="ncua-product-value">
                                <a class="ncua-product-link" href="javascript:void(0)" onclick="${addGoodsPopupFunction};">
                                    ${val.goodsName}
                                </a>
                                <br>${val.optionName}
                            </div>
                        </div>
                        <div class="ncua-border-product-detail-item">
                            <div class="ncua-product-label">결제 정보</div>
                            <div class="ncua-product-value">
                                [${val.orderStatus}] ${val.goodsPrice}
                            </div>
                        </div>
                    </div>
                </div>
            `;
        });
        const selectOrderElement = document.querySelector("#selectOrder");
        selectOrderElement.innerHTML = selectOrderContentHtml;
        selectOrderElement.style.display = 'block';

        // 주문이 추가되면 삭제 버튼 보이기
        document.querySelectorAll(".js-select-remove").forEach(btn => btn.style.display = 'inline-block');
    }

    $(document).ready(function () {
        var bdId = '<?=$req['bdId']?>';
        var bdSecretFl = '<?=$bdWrite['cfg']['bdSecretFl']?>';
        var mode = '<?=$mode?>';
        var flag = true;
        const editor = document.querySelector(`[data-godo-editor="article-write-editor"]`);

        $('#frmWrite').find('[name=queryString]').val(getUrlVars());
        $('select[name=bdId]').bind('change', function () {
            var flag = true;
            $('#board-table').find('input[type=text]').each(function (index, item) {
                if ($(item).val() != '') {
                    flag = false;
                    return false;
                }
            })

            if(flag){
                flag = editor.ncdsEditor.getHTML().length < 1;
            }

            if (flag === false) {
                NCDSConfirm({message: '게시판 변경 시 입력된 정보가 초기화됩니다. <br> 변경하시겠습니까?'}).then(function (result) {
                    if (result) {
                        location.href = 'article_write.php?bdId=' + $('select[name=bdId]').val();
                    } else {
                        $('select[name=bdId]').val(bdId);
                    }
                }).catch(function (error) {
                    NCDSAlert({message: error.message});
                });
            }
            else {
                location.href = 'article_write.php?bdId=' + $('select[name=bdId]').val();
            }
        })

        document.addEventListener('click', function(e) {
            if (e.target.classList.contains('js-select-remove')) {
                if (e.target.closest('.ncua-product-select-layout')) {
                    // 상품 선택 영역인지 주문 선택 영역인지 확인
                    const selectGoodsElement = document.querySelector("#selectGoods");
                    const selectOrderElement = document.querySelector("#selectOrder");

                    if (selectGoodsElement && selectGoodsElement.innerHTML.trim() !== '') {
                        selectGoodsElement.innerHTML = '';
                        selectGoodsElement.style.display = 'none';
                    }

                    if (selectOrderElement && selectOrderElement.innerHTML.trim() !== '') {
                        selectOrderElement.innerHTML = '';
                        selectOrderElement.style.display = 'none';
                    }

                    e.target.style.display = 'none';
                }
            }
        })

        $('body').on('click', '.addUploadBtn', function () {
            var uploadBoxCount = $('#uploadBox').find('input[name="upfiles[]"]').length;
            if (uploadBoxCount >= 10) {
                NCDSAlert({message: "업로드는 최대 10개만 지원합니다"});
                return;
            }

            var addUploadBox = _.template(
                $("script.template").html()
            );
            $(this).closest('ul').append(addUploadBox);
            init_file_style();
        });

        $('body').on('click', '.minusUploadBtn', function () {
            index = $(this).prevAll('input:file').attr('index'); //$('.file-upload button.uploadremove').index(target)+1;
            $("input[name='uploadFileNm[" + index + "]']").remove();
            $("input[name='saveFileNm[" + index + "]']").remove();
            $(this).closest('li').remove();
        })

        $('.js-add-goods').bind('click', function () {
            window.open('/share/ncds/popup_goods.php?checkType=radio&displayMode=noneSort', 'popup_goods_select', 'width=1200, height=850, scrollbars=no');
        })

        $('.js-add-order').bind('click', function () {
            window.open('/share/ncds/popup_order.php?checkType=radio', 'popup_order_select', 'width=1200, height=850, scrollbars=no');
        })

        $('input[name=isMove]').bind('change', function () {
            $('select[name=moveBdId]').attr('disabled', true);
            if ($(this).is(':checked')) {
                $('select[name=moveBdId]').attr('disabled', false);
            }
        });

        $("input[name=isMove]").trigger('change');

        $.validator.addMethod("checkEventDate", function (value) {
            var startDateTime = $('input[name="eventStart"]').val();
            var endDateTime = value;

            var startDate = new Date(startDateTime.replace(' ', 'T'));
            var endDate = new Date(endDateTime.replace(' ', 'T'));

            return endDate > startDate ? true : false;
        }, "종료일이 시작일보다 빠를수 없습니다.");

        $("#frmWrite").validate({
            ignore: [],
            submitHandler: function (form) {
                if($(form).find('[name=uploadType]').val() == 'ajax') {
                    $('input:file').prop('disabled', true);
                }
                $('input[name=\'isSecret\']').prop('value', $('#w_isSecret').is(':checked') ? 'y' : 'n');
                form.target = 'ifrmProcess';
                form.submit();
            },
            invalidHandler: function(event, validator) {
                if (validator.errorList.length > 0) {
                    NCDSAlert({
                        message: validator.errorList[0].message,
                        iconType: 'error'
                    });
                }
            },
            // onclick: false, // <-- add this option
            rules: {
                bdId: 'required',
                subject: 'required',
                contents: {
                    required: function (textarea) {
                        return textarea.value.length === 0;
                    }
                },
                eventStart: 'required',
                eventEnd: {
                    required: true,
                    checkEventDate: true,
                },
            },
            messages: {
                bdId: {
                    required: '게시판 아이디를 선택해주세요.'
                },
                subject: {
                    required: '제목을 입력해주세요.'
                },
                contents: {
                    required: '내용을 입력해주세요'
                },
                eventStart: {
                    required: '이벤트 기간을 입력해주세요'
                },
                eventEnd: {
                    required: '이벤트 기간을 입력해주세요'
                }
            }
        });

        $('#bdTemplateSno').change(async function(){
            if($(this).val() == '' || $(this).val() == 0){
                return false;
            }

            if (editor.ncdsEditor.getHTML().length > 0) {
                if (!await NCDSConfirm({message: '본문이 삭제되고 게시글양식 내용이 삽입됩니다. 진행하시겠습니까?'})) {
                    return false;
                }
            }

            $.ajax({
                method: "POST",
                url: "./template_ps.php",
                data: {mode : 'getData',sno : $(this).val()},
                dataType: 'json'
            }).success(function (result) {
                editor.ncdsEditor.setHTML(result['contents']);
            }).error(function (e) {
                NCDSAlert({ message: e.responseText });
            });
        })

        $('.js-template-register').bind('click', function () {
            $.ajax({
                url: 'template_write.php?templateType=admin',
                success: function (data) {
                    var layerForm = data;
                    layer_popup($(layerForm), '게시글 양식 등록', 'wide')
                },
                error: function (e) {
                    NCDSAlert({ message: e.responseText });
                }
            });
        })


        $('#w_isNotice').on('click', function(){
            if(mode != '답변'){
                if(bdSecretFl == 3) {
                    if ($(this).is(':checked')) {
                        $('#w_isSecret').prop('disabled', false);
                        $('#w_isSecret').prop('checked', false);
                        $('input[name=\'isSecret\']').prop('value', 'n');
                    } else {
                        $('#w_isSecret').prop('checked', true);
                        $('input[name=\'isSecret\']').prop('value', 'y');
                        $('#w_isSecret').prop('disabled', true);
                    }
                }
            }
        })

        $('#w_isSecret').on('change', function(){
            if ($(this).is(':checked')) {
                $(this).prop('value', 'y');
                $('input[name=\'isSecret\']').prop('value', 'y');
            } else {
                $(this).prop('value', 'n');
                $('input[name=\'isSecret\']').prop('value', 'n');
            }
        })
    });
</script>
<script type="text/template" class="template">
    <li class="form-inline mgb5">
        <input type="file" name="upfiles[]">
        <a class="btn btn-white btn-icon-minus minusUploadBtn btn-sm">삭제</a>
    </li>
</script>

<script type="text/javascript">
    <?php if ($bdWrite['cfg']['bdEventFl'] == 'y') { ?>
    const datePicker = new ncua.DatePicker(document.querySelector('#datepicker-container'), {
        size: 'xs',
        datePickerOptions: [
            {
            element: 'start-date',
            attrName: 'eventStart',
            options: {
                mode: 'single',
                static: true,
                dateFormat: 'Y-m-d H:i',
                clickOpens: true,
                allowInvalidPreload: true,
                allowInput: true,
                enableTime: true,
                locale: 'ko',
                },
            },
            {
            element: 'end-date',
            attrName: 'eventEnd',
            options: {
                mode: 'single',
                static: true,
                dateFormat: 'Y-m-d H:i',
                clickOpens: true,
                allowInvalidPreload: true,
                allowInput: true,
                enableTime: true,
                locale: 'ko',
                },
            },
        ],
    });
    datePicker.setDate(["<?= $bdWrite['data']['eventStart']; ?>", "<?= $bdWrite['data']['eventEnd']; ?>"]);
    <?php } ?>
</script>
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
                message: `파일은 최대 ${maxCount}개까지 업로드 가능합니다. (현재: ${currentFiles.length}개, 추가 시도: ${totalCount}개)`
            };
        }

        // 파일 크기 검증
        const maxSizeBytes = maxSize * 1024 * 1024;
        for (let i = 0; i < newFiles.length; i++) {
            const file = newFiles[i];
            if (file.size > maxSizeBytes) {
                return {
                    valid: false,
                    message: `파일 "${file.name}"의 크기가 최대 허용 크기(${maxSize}MB)를 초과합니다. (${(file.size / 1024 / 1024).toFixed(2)}MB)`
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
    const MAX_UPLOAD_COUNT = 10;
    const MAX_UPLOAD_SIZE = Number('<?=$bdWrite['cfg']['bdStrMaxSize']?>'.replace('M', ''));

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
        const { isExistingFile = false, index = null, container, saveFileNm, uploadFileNm } = options;
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
        const hiddenUpfilesInput = document.createElement('input');
        hiddenUpfilesInput.hidden = true;

        if (isExistingFile) {
            // 기존 파일: checkbox 사용
            hiddenUpfilesInput.type = 'checkbox';
            hiddenUpfilesInput.name = 'delFile[' + index + ']';
            hiddenUpfilesInput.value = 'y';
        } else {
            // 새 파일: file input 사용
            hiddenUpfilesInput.type = 'file';
            hiddenUpfilesInput.tabIndex = -1;
            hiddenUpfilesInput.setAttribute('aria-hidden', 'true');
            hiddenUpfilesInput.className = 'no-filestyle';
            hiddenUpfilesInput.accept = '';
            hiddenUpfilesInput.name = 'upfiles[]';
            // FileList를 DataTransfer로 변환하여 파일 설정
            const dataTransfer = new DataTransfer();
            dataTransfer.items.add(file);
            hiddenUpfilesInput.files = dataTransfer.files;
        }

        // div로 감싸기
        const wrapperDiv = document.createElement('div');
        wrapperDiv.className = 'ncua-file-tags__content';
        wrapperDiv.appendChild(fileTag.element);
        wrapperDiv.appendChild(hiddenUpfilesInput);
        if(!isExistingFile) {
            wrapperDiv.appendChild(makeSaveFileNmInput(index, saveFileNm));
            wrapperDiv.appendChild(makeUploadFileNmInput(index, uploadFileNm));
        }

        // 태그 인스턴스에 wrapper 정보 저장
        fileTag.wrapper = wrapperDiv;
        tagInstances.set(fileName, fileTag);

        // 감싼 div를 container에 추가
        container.appendChild(wrapperDiv);
    }



    let fileInputLength = document.querySelector('#fileTagContainer').children.length;
    let uploadImages = [];
    const makeSaveFileNmInput = (index, saveFileNm) => {
        const input = document.createElement('input');
        input.hidden = true;
        input.type = 'hidden';
        input.name = 'saveFileNm[' + index + ']';
        input.value = saveFileNm;
        return input;
    }
    const makeUploadFileNmInput = (index, uploadFileNm) => {
        const input = document.createElement('input');
        input.hidden = true;
        input.type = 'hidden';
        input.name = 'uploadFileNm[' + index + ']';
        input.value = uploadFileNm;
        return input;
    }

    const fileInput = new ncua.FileInput({
        container: 'fileInputContainer',
        buttonLabel : '파일 찾기',
        hintItems: [
            '파일은 최대 10개까지 다중업로드가 지원됩니다.',
            <?php if ($bdWrite['cfg']['bdStrMaxSize'] != '') { ?>
            '파일 업로드 최대 사이즈는 <?= $bdWrite['cfg']['bdStrMaxSize'] ?>B 입니다.'
            <?php } ?>
        ],
        fileInputName: 'upfiles[]',
        onChange: (newFiles) => {
            // 파일 검증
            const validation = validateFiles(currentFiles, newFiles, MAX_UPLOAD_COUNT, MAX_UPLOAD_SIZE);
            if (!validation.valid) {
                NCDSAlert({ message: validation.message, iconType: 'error' });
                return;
            }




            window.onbeforeunload = function () {
                if (uploadImages.length == 0) {
                    return false;
                }
                $.ajax({
                    method: "POST",
                    url: "./article_ps.php",
                    async: false,
                    data: {mode: 'deleteGarbageImage', bdId: $('[name=bdId]').val(), deleteImage: uploadImages.join('^|^')},
                    cache: false,
                }).success(function (data) {
                }).error(function (e) {
                });
            }

            $("#frmWrite").on("submit", function () {
                window.onbeforeunload = null;
            });

            if ($("#frmWrite").find('[name=uploadType][value=ajax]').length < 1) {
                $("#frmWrite").append('<input type="hidden"  name="uploadType" value="ajax" >');
            }

            const formData = new FormData();
            formData.append('bdId', $('[name=bdId]').val());
            formData.append('mode', 'ajaxUpload');
            formData.append('uploadFile', newFiles[0]);

            $.ajax({
                url: './article_ps.php',
                type: 'POST',
                data: formData,
                async: false,
                cache: false,
                contentType: false,
                processData: false,
                dataType: 'json',
                success: function (returnData) {
                    returnData['index'] = fileInputLength;
                    if (returnData.result == 'ok') {
                        currentFiles = [...currentFiles, ...newFiles];
                        const fileTagContainer = document.getElementById('fileTagContainer');

                        newFiles.forEach(file => {
                            createFileTag(file.name, file, {
                                isExistingFile: false,
                                container: fileTagContainer,
                                index: fileInputLength++,
                                saveFileNm: returnData.saveFileNm,
                                uploadFileNm: returnData.uploadFileNm,
                            });
                        });
                        uploadImages.push(returnData.saveFileNm);
                    }
                    else if (returnData.result == 'cancel') {
                        NCDSAlert({ message: returnData.errorMsg || '업로드에 실패했습니다.' });
                    }
                    else {
                        NCDSAlert({ message: returnData.errorMsg });
                    }

                }
            });
        }
    });

    document.querySelector('.js-btn-back').addEventListener('click', function() {
        history.back();
    });

    const uploadFileNm = <?=json_encode(gd_isset($bdWrite['data']['uploadFileNm']) && is_array($bdWrite['data']['uploadFileNm']) ? $bdWrite['data']['uploadFileNm'] : [], JSON_UNESCAPED_UNICODE)?>;


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

        fileInputLength = document.querySelector('#fileTagContainer').children.length;
    }
</script>
