<div class="table-title div-business gd-help-manual">
    사업자정보
</div>
<div class="input_wrap form-inline div-business">
    <table class="table table-cols">
        <colgroup>
            <col class="width-sm"/>
            <col class="<?=(!empty($widthClass)) ? $widthClass : 'width-3xl'?>"/>
            <col class="width-sm"/>
            <col class="<?=(!empty($widthClass)) ? $widthClass : ''?>"/>
        </colgroup>
        <tr>
            <th>상호</th>
            <td>
                <span title="회사명를 입력해주세요!">
                    <input type="text" name="company" class="form-control" value="<?= $data['company']; ?>" maxlength="50" data-pattern="gdMemberNmGlobal"/>
                </span>
                <span id="companyMsg" class="input_error_msg"></span>
            </td>
            <th>사업자번호</th>
            <td>
                <span title="사업자번호를 입력해주세요!">
                    <input type="text" name="busiNo[]" size="3" maxlength="3" class="form-control js-number"
                           value="<?= $data['busiNo'][0]; ?>"/>
                    -
                    <input type="text" name="busiNo[]" size="2" maxlength="2" class="form-control js-number"
                           value="<?= $data['busiNo'][1]; ?>"/>
                    -
                    <input type="text" name="busiNo[]" size="5" maxlength="5" class="form-control js-number"
                           value="<?= $data['busiNo'][2]; ?>"/>
                    <input type="hidden" id="busiNo" name="fullBusiNo" class="error" value="<?= gd_implode('', $data['busiNo']); ?>" data-overlap-businofl="<?= $joinField['busiNo']['overlapBusiNoFl']; ?>" data-charlen="<?= $joinField['busiNo']['charlen']; ?>" data-oldbusino="<?= gd_implode('', $data['busiNo']); ?>"/>
                    <?php if ($joinField['busiNo']['overlapBusiNoFl'] == 'y') { ?>
                    <button type="button" id="overlap_busiNo" class="btn btn-gray btn-sm">중복확인</button>
                    <?php } ?>
                    <button type="button" id="find_busiNo" class="btn btn-gray btn-sm">사업자번호 조회</button>
                </span>
            </td>
        </tr>
        <tr>
            <th>대표자명</th>
            <td class="input_area" colspan="3">
                <span title="대표자를 입력해주세요!">
                    <input type="text" name="ceo" class="form-control" value="<?= $data['ceo']; ?>" data-pattern="gdEngKor" maxlength="20"/>
                </span>
            </td>

        </tr>
        <tr>
            <th>업태</th>
            <td>
                <span title="업태를 입력해주세요!">
                    <input type="text" name="service" class="form-control" value="<?= $data['service']; ?>" data-pattern="gdEngKor" maxlength="30"/>
                </span>
            </td>
            <th>종목</th>
            <td>
                <span title="종목을 입력해주세요!">
                    <input type="text" name="item" class="form-control" value="<?= $data['item']; ?>" data-pattern="gdEngKor" maxlength="30"/>
                </span>
            </td>
        </tr>
        <tr>
            <th>사업장 주소</th>
            <td class="input_area" colspan="3">
                <div class="form-inline mgb5">
                    <span title="우편번호를 입력해주세요!">
                        <input type="text" size="7" maxlength="5" name="comZonecode" class="form-control"
                               value="<?= $data['comZonecode'] ?>"/>
                        <input type="hidden" name="comZipcode" value="<?= $data['comZipcode'] ?>"/>
                        <span id="comZipcodeText" class="number <?php if (strlen($data['comZipcode']) != 7) {
                            echo 'display-none';
                        } ?>">(<?php echo $data['comZipcode']; ?>)
                        </span>
                    </span>
                    <input type="button"
                           onclick="postcode_search('comZonecode', 'comAddress', 'comZipcode');"
                           value="우편번호찾기" class="btn btn-sm btn-gray"/>
                </div>
                <div>
                    <span title="주소를 입력해주세요!">
                        <input type="text" name="comAddress" class="form-control width-2xl"
                               value="<?= $data['comAddress']; ?>"/>
                    </span>
                    <span title="상세주소를 입력해주세요!">
                        <input type="text" name="comAddressSub"
                               class="form-control width-2xl"
                               value="<?= $data['comAddressSub']; ?>"/>
                    </span>
                </div>
            </td>
        </tr>
        <tr>
            <th>사업자등록증</th>
            <td class="input_area" colspan="3">
                <?php if (!empty($certification)): ?>
                    <a href="#" class="js-certification-download" data-url="../member/company_certification.php?mode=download&sno=<?= $certification['sno'] ?>">
                        <?= $certification['imageFileNm'] ?>
                    </a>
                    <br>
                <?php endif; ?>
                <div class="form-inline">
                    <input type="file" id="companyCertification" name="companyCertification" class="form-control js-cert-file" accept="image/*, application/pdf" data-max-size="<?= $certificationMaxFileSize ?>"/>
                    <?php if ($mode === 'register'): // 등록 시에만 첨부 취소 버튼 노출 (수정 시 미노출) ?>
                    <button type="button" class="btn btn-white btn-sm js-cert-file-clear" style="display:none;">- 삭제</button>
                    <?php endif; ?>
                </div>
                <div class="notice-info mgt10">
                    파일 업로드 최대 사이즈는 <?= $certificationMaxFileSize ?>MB 입니다.<br>
                </div>
                <?php if (!empty($certification)): ?>
                    <span>
                        <input type="checkbox" name="certificationDeleteSno" class="js-certification-checkbox" value="<?= $certification['sno'] ?>" />
                        업로드된 파일 삭제 .. <?= $certification['imageFileNm'] ?>
                        <br>
                    </span>
                <?php endif; ?>
            </td>
        </tr>
        <?php
            for ($i = 0; $i < $certificationAdditionalFileCount ; $i++) {
                $additional = $additionalCertification["comAddiCert$i"] ?? null;
                if($additional && $additional['use'] === 'y') { ?>
                    <tr>
                        <th><?= $additional['name'] ?></th>
                        <td class="input_area" colspan="3">
                            <?php if (!empty($additional['file'])): ?>
                                <a href="#" class="js-certification-download" data-url="../member/company_certification.php?mode=download&sno=<?= $additional['file']['sno'] ?>">
                                    <?= $additional['file']['imageFileNm'] ?>
                                </a>
                                <br>
                            <?php endif; ?>
                            <div class="form-inline">
                                <input type="file" id="comAddiCert[<?= $i ?>][file]" name="comAddiCert[<?= $i ?>][file]" class="form-control js-cert-file" accept="image/*, application/pdf" data-max-size="<?= $additional['certificationFileSize'] ?>"/>
                                <?php if ($mode === 'register'): // 등록 시에만 첨부 취소 버튼 노출 (수정 시 미노출) ?>
                                <button type="button" class="btn btn-white btn-sm js-cert-file-clear" style="display:none;">- 삭제</button>
                                <?php endif; ?>
                            </div>
                            <div class="notice-info mgt10">
                                파일 업로드 최대 사이즈는 <?= $additional['certificationFileSize'] ?>MB 입니다.<br>
                            </div>
                            <?php if (!empty($additional['file'])): ?>
                                <span>
                                    <input type="checkbox" name="comAddiCert[<?= $i ?>][DeleteSno]" class="js-certification-checkbox" value="<?= $additional['file']['sno'] ?>" />
                                    업로드된 파일 삭제 .. <?= $additional['file']['imageFileNm'] ?>
                                    <br>
                                </span>
                            <?php endif; ?>
                        </td>
                    </tr>
        <?php
                }
        } ?>
    </table>
</div>
<iframe id="downloadFrame" style="display:none;"></iframe>
<script>
    $(document).on('click', '.js-certification-download', function(e) {
        e.preventDefault();
        var url = $(this).data('url');
        $('#downloadFrame').attr('src', url);
    });

    $(document).on('change', '.js-certification-checkbox', function(e) {
        var checkbox = this;
        if (checkbox.checked) {
            // 추가서류(comAddiCert[N][DeleteSno])와 사업자등록증(certificationDeleteSno) 문구 구분
            var message = checkbox.name.indexOf('comAddiCert') === 0
                ? '등록된 첨부파일을 삭제하시겠습니까? 회원정보 최종 저장 시 삭제되며, 복구가 불가능 합니다.'
                : '등록된 사업자등록증을 삭제하시겠습니까? 회원정보 최종 저장 시 삭제되며, 복구가 불가합니다.';
            dialog_confirm(message, function(result) {
                if (!result) {
                    checkbox.checked = false;
                }
            }, '경고');
        }
    });

    // 선택 파일 비우기: bootstrap-filestyle 위젯이 적용된 경우 표시 텍스트박스까지 함께 비운다
    function clearCertFile($input) {
        if ($input.data('filestyle')) {
            $input.filestyle('clear');
        } else {
            $input.val('');
        }
    }

    // 찾아보기로 파일 선택 시 확장자·용량 검사 후 '- 삭제' 버튼 노출, 클릭 시 선택 취소(첨부 제거)
    $(document).on('change', '.js-cert-file', function () {
        const file = this.files[0];

        if (file) {
            const allowedExt = ['pdf', 'jpg', 'jpeg', 'png', 'gif', 'bmp', 'webp', 'svg'];
            const ext = file.name.split('.').pop().toLowerCase();
            if (!allowedExt.includes(ext)) {
                clearCertFile($(this));
                dialog_alert('등록이 불가한 파일 형식입니다. 이미지(jpg, jpeg, png, gif, bmp, webp, svg) 및 pdf 파일만 업로드 가능합니다.');
            } else {
                const fileMaxSize = parseFloat($(this).data('max-size')) || 2; // MB
                const fileSizeMB = file.size / (1024 * 1024);
                if (fileSizeMB > fileMaxSize) {
                    clearCertFile($(this));
                    dialog_alert('파일은 최대 ' + fileMaxSize.toString() + 'MB 이하로만 등록 가능합니다.');
                }
            }
        }

        $(this).closest('.form-inline').find('.js-cert-file-clear').toggle(this.files.length > 0);
    });

    $(document).on('click', '.js-cert-file-clear', function () {
        clearCertFile($(this).closest('.form-inline').find('.js-cert-file'));
        $(this).hide();
    });
</script>
