(function(window) {
    'use strict';

    /**
     * 파일 업로드 공통 스크립트
     * 침고(ncds 전용) : 해당 기본 로직은 고도몰 공통 스크립트 gdAjaxUpload 개선하여 만든 file input 공통 스크립트
     * file input 관련하여 필요한 공통 함수는 호출하여 사용 가능
     */


    /**
     * 저장된 파일명 input hidden 생성 함수
     * @param {number} index - 파일 인덱스
     * @param {string} saveFileNm - 저장된 파일명
     * @returns {HTMLInputElement} 저장된 파일명 input 요소
     */
    const makeSaveFileNmInput = (index, saveFileNm) => {
        const input = document.createElement('input');
        input.hidden = true;
        input.type = 'hidden';
        input.name = 'saveFileNm[' + index + ']';
        input.value = saveFileNm;
        return input;
    }

    /**
     * 업로드 파일명 input hidden 생성 함수
     * @param {number} index - 파일 인덱스
     * @param {string} uploadFileNm - 업로드 파일명
     * @returns {HTMLInputElement} 업로드 파일명 input 요소
     */
    const makeUploadFileNmInput = (index, uploadFileNm) => {
        const input = document.createElement('input');
        input.hidden = true;
        input.type = 'hidden';
        input.name = 'uploadFileNm[' + index + ']';
        input.value = uploadFileNm;
        return input;
    }


    /**
     * 파일 태그 생성 함수
     * @param {string} fileName - 파일명
     * @param {Object} options - 옵션 객체
     * @param {boolean} options.isExistingFile - 기존 파일 여부
     * @param {number} options.index - 기존 파일의 인덱스 (기존 파일일 경우)
     * @param {HTMLElement} options.container - 태그를 추가할 컨테이너 요소
     * @param {string} options.saveFileNm - 저장된 파일명 (새 파일일 경우)
     * @param {string} options.uploadFileNm - 업로드 파일명 (새 파일일 경우)
     * @param {Map} options.tagInstances - 태그 인스턴스 Map (참조로 전달)
     * @param {Array} options.currentFiles - 현재 파일 배열 (참조로 전달)
     */
    const createFileTag = ({fileName, options}) => {
        const { isExistingFile = false, index = null, container, saveFileNm, uploadFileNm, tagInstances, currentFiles } = options;
        

        // 태그 생성
        const fileTag = new ncua.Tag({
            text: fileName,
            size: 'sm',
            close: true,
            onButtonClick: () => { // 태그 삭제
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
                

                // 파일 배열에서 제거 (배열을 직접 수정)
                if (currentFiles && Array.isArray(currentFiles)) {
                    const fileIndex = currentFiles.findIndex(f => f.name === fileName);
                    if (fileIndex !== -1) {
                        currentFiles.splice(fileIndex, 1);
                    }
                }

                container.style.display = currentFiles?.length > 0 ? 'block' : 'none';
            }
        });
        
        // div로 감싸기
        const wrapperDiv = document.createElement('div');
        wrapperDiv.className = 'ncua-file-tags__content';
        wrapperDiv.appendChild(fileTag.element);
        
        if (isExistingFile) {
            const hiddenInput = document.createElement('input');
            hiddenInput.hidden = true;
            hiddenInput.type = 'checkbox';
            hiddenInput.name = 'delFile[' + index + ']';
            hiddenInput.value = 'y';
            wrapperDiv.appendChild(hiddenInput);
        } else {
            // 새 파일: 이미 서버에 업로드된 파일이므로 saveFileNm과 uploadFileNm을 hidden input으로 전송
            if (saveFileNm && uploadFileNm) {
                wrapperDiv.appendChild(makeSaveFileNmInput(index, saveFileNm));
                wrapperDiv.appendChild(makeUploadFileNmInput(index, uploadFileNm));
            }
        }
        
        // 태그 인스턴스에 wrapper 정보 저장
        fileTag.wrapper = wrapperDiv;
        tagInstances.set(fileName, fileTag);
        
        // 감싼 div를 container에 추가
        container.appendChild(wrapperDiv);
        container.style.display = container.children.length > 0 ? 'block' : 'none';

    }


    /**
     * 파일 업로드 검증 함수
     * @param {File[]} currentFiles - 현재 업로드된 파일 배열
     * @param {File[]} newFiles - 새로 추가할 파일 배열
     * @param {number} maxCount - 최대 파일 개수
     * @param {number} maxSize - 최대 파일 크기 (MB)
     * @returns {{valid: boolean, message: string}} 검증 결과
     */
    const validateFiles = ({ currentFiles = [], newFiles = [], maxCount = 0, maxSize = 0 }) => {
        // 중복 파일 검증
        const currentFileNames = currentFiles?.map(f => f.name.toLowerCase());
        for (let i = 0; i < newFiles?.length; i++) {
            const newFileName = newFiles[i]?.name?.toLowerCase();
            
            if (currentFileNames.includes(newFileName)) {
                return {
                    valid: false,
                    message: `동일한 파일명 "${newFiles[i].name}"이 이미 업로드되어 있습니다.`
                };
            }
        }

        // 파일 개수 검증
        const totalCount = currentFiles?.length + newFiles?.length;
        if (maxCount <= 0) return { valid: true };
        if (totalCount > maxCount) {
            return {
                valid: false,
                message: `파일은 최대 ${maxCount}개까지 업로드 가능합니다. (현재: ${currentFiles.length}개, 추가 시도: ${totalCount}개)`
            };
        }

        // 파일 크기 검증
        if (maxSize <= 0) return { valid: true };
        const maxSizeBytes = maxSize * 1024 * 1024;
        for (let i = 0; i < newFiles?.length; i++) {
            const file = newFiles[i];
            if (file.size > maxSizeBytes) {
                return {
                    valid: false,
                    message: `파일 "${file.name}"의 크기가 최대 허용 크기(${maxSize}MB)를 초과합니다. (${(file.size / 1024 / 1024).toFixed(2)}MB)`
                };
            }
        }


        return {
            valid: true,
        };
    };

    
    /**
     * Ajax 업로드를 위한 폼 설정 함수
     * @param {string} formId - 폼 요소의 ID
     */
    const setupAjaxUploadForm = (formId) => {
        const formElement = document.getElementById(formId);
        if (formElement) {
            formElement.addEventListener("submit", function () {
                window.onbeforeunload = null;
            });

            if (!formElement.querySelector('[name=uploadType][value=ajax]')) {
                const hiddenInput = document.createElement('input');
                hiddenInput.type = 'hidden';
                hiddenInput.name = 'uploadType';
                hiddenInput.value = 'ajax';
                formElement.appendChild(hiddenInput);
            }
        }
    }

    /**
     * Ajax를 통한 파일 업로드 및 태그 생성 함수
     * @param {string} ajaxUrl - 업로드 요청 URL
     * @param {FormData} formData - 업로드할 파일 데이터
     * @param {Array} uploadFiles - 업로드된 파일명 배열 (참조)
     * @param {File} file - 업로드할 파일 객체
     * @param {Object} fileTagOptions - 파일 태그 옵션
     * @param {HTMLElement} fileTagOptions.fileTagContainer - 파일 태그를 추가할 컨테이너
     * @param {Map} fileTagOptions.tagInstances - 태그 인스턴스 Map (참조)
     * @param {Array} fileTagOptions.currentFiles - 현재 파일 배열 (참조)
     * @param {boolean} fileTagOptions.needFilePreview - 파일 미리보기 여부
     */
 
    const uploadFileViaAjax = async ({ ajaxUrl, formData, uploadFiles, file, fileTagOptions }) => {
        const { fileTagContainer, tagInstances, currentFiles, needFilePreview } = fileTagOptions;
        
        try {
            const response = await fetch(ajaxUrl, {
                method: 'POST',
                body: formData,
                cache: 'no-cache'
            });

            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }

            const returnData = await response.json();

            if (returnData.result == 'ok') {
                currentFiles.push(file);
                let fileTagsCount = fileTagContainer.children.length;
               
                createFileTag({ fileName: file.name, options: {
                    isExistingFile: false,
                    container: fileTagContainer,
                    index: fileTagsCount++,
                    saveFileNm: returnData.saveFileNm,
                    uploadFileNm: returnData.uploadFileNm,
                    tagInstances: tagInstances,
                    currentFiles: currentFiles,
                }});

                if (needFilePreview) {
                    // 파일 미리보기 이미지 생성
                    const fileReader = new FileReader();
                    fileReader.onload = function(e) {
                        const tagInstance = tagInstances.get(file.name);
                        if (tagInstance && tagInstance.wrapper) {
                            const fileImage = document.createElement('img');
                            fileImage.className = 'ncua-file-input__file-image';
                            fileImage.src = e.target.result;
                            tagInstance.wrapper.appendChild(fileImage);
                        }
                    };
                    fileReader.readAsDataURL(file);
                }
                
                uploadFiles.push(returnData.saveFileNm);
            }
            else {
                NCDSAlert({ message: returnData.errorMsg || `파일 "${file.name}" 업로드에 실패했습니다.` });
            }
        } catch (error) {
            NCDSAlert({ message: `파일 "${file.name}" 업로드 중 오류가 발생했습니다.` });
            console.error('File upload error:', error);
        }
    }

    /**
     * 기존 파일로부터 파일 태그 생성 함수
     * @param {Object} options - 옵션 객체
     * @param {Array} options.uploadedFileNames - 업로드된 파일명 배열
     * @param {Array} options.uploadedFiles - 업로드된 파일 정보 배열
     * @param {Object} options.fileTagOptions - 파일 태그 옵션
     * @param {HTMLElement} options.fileTagOptions.fileTagContainer - 파일 태그를 추가할 컨테이너
     * @param {Map} options.fileTagOptions.tagInstances - 태그 인스턴스 Map (참조)
     * @param {Array} options.fileTagOptions.currentFiles - 현재 파일 배열 (참조)
     * @param {boolean} options.fileTagOptions.needFilePreview - 파일 미리보기 여부
     */
    const createFileTagFromExistingFile = ({ uploadedFileNames, uploadedFiles, fileTagOptions }) => {
        const { fileTagContainer, tagInstances, currentFiles, needFilePreview } = fileTagOptions;

        uploadedFileNames.forEach((fileName, i) => {
            if (!fileName) return;

            // 기존 파일명을 currentFiles에 추가
            currentFiles.push({ name: fileName });

            createFileTag({ fileName, options: {
                isExistingFile: true,
                index: i,
                container: fileTagContainer,
                tagInstances: tagInstances,
                currentFiles: currentFiles,
            }});

            if (needFilePreview) {
                // createFileTag가 완료된 후 태그 인스턴스를 가져옴
                const tagInstance = tagInstances.get(fileName);
                if (tagInstance && tagInstance.wrapper && uploadedFiles[i] && uploadedFiles[i].thumSrc) {
                    const fileImage = document.createElement('img');
                    fileImage.className = 'ncua-file-input__file-image';
                    fileImage.src = uploadedFiles[i].thumSrc;
                    tagInstance.wrapper.appendChild(fileImage);
                }
            }
        });
    }



    // 전역 객체에 노출
    window.FileInput = {
        validateFiles: validateFiles,
        createFileTag: createFileTag,
        setupAjaxUploadForm: setupAjaxUploadForm,
        uploadFileViaAjax: uploadFileViaAjax,
        createFileTagFromExistingFile: createFileTagFromExistingFile,
    };


})(window);
