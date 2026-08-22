<?php
/**
 * This is commercial software, only users who have purchased a valid license
 * and accept to the terms of the License Agreement can install and use this
 * program.
 *
 * Do not edit or add to this file if you wish to upgrade Godomall to newer
 * versions in the future.
 *
 * @copyright ⓒ 2022, NHN COMMERCE Corp.
 */

namespace Bundle\Component\Excel;

use Component\Member\Member;
use Component\Member\MemberAdmin;
use Component\Member\MemberValidation;
use Component\Member\MemberVO;
use Framework\Utility\StringUtils;
use Exception;

/**
 * 회원 엑셀 업로드
 * @package Bundle\Component\Excel
 * @author  yjwee
 */
class ExcelMemberConvert extends \Component\Excel\ExcelDataConvert
{
    /** @var \Bundle\Component\Member\MemberAdmin */
    private $memberAdminService;
    private $hasMemberNoField = false;
    private $isTransaction = true;
    private $memberHandleMode = 'insert';
    private $memberHandleResult = '실패';
    /** @var \PhpOffice\PhpSpreadsheet\Worksheet $sheet */
    protected $sheet;
    protected $fields = [];
    protected $dbNames = [];

    /**
     * @inheritDoc
     */
    public function __construct(MemberAdmin $memberAdminService = null)
    {
        parent::__construct();
        $this->memberAdminService = $memberAdminService;
        if ($memberAdminService === null) {
            $this->memberAdminService = new MemberAdmin();
        }
        $this->memberHandleResult = __('실패');
    }

    /**
     * 회원 업로드 함수
     *
     * @return bool
     */
    public function upload()
    {
        if ($this->hasError()) {
            $this->createBodyByError();
            $this->printExcel();

            return false;
        }
        if (!$this->read()) {
            $this->createBodyByReadError();
            $this->printExcel();

            return false;
        }
        if (!$this->hasData()) {
            $this->createDataError();
            $this->printExcel();

            return false;
        }
        $this->excelBody = [];
        $this->createTableHeader();
        $excelMember = new ExcelMember();
        $fields = $excelMember->formatMember();
        $this->resetExcelCode($fields);
        $this->setTableKey();
        $this->processExcel();

        return true;
    }

    /**
     * 업로드 결과 헤더 설정 함수
     */
    public function createTableHeader()
    {
        $this->excelBody[] = '<table border="1">' . chr(10);
        $this->excelBody[] = '<tr>' . chr(10);
        $this->excelBody[] = '<td>' . __('번호') . '</td><td>' . __('회원 번호') . '</td><td>' . __('아이디') . '</td><td>' . __('등록/수정') . '</td>' . chr(10);
        $this->excelBody[] = '</tr>' . chr(10);
    }

    /**
     * 엑셀의 항목과 회원 테이블의 컬럼 매칭 및 회원번호 필드가 존재하는지 체크하는 함수
     */
    public function setTableKey()
    {
        $cells = $this->sheet->getRowIterator(2)->current()->getCellIterator();
        $cells->rewind();
        $idx = 1;
        while ($cells->valid()) {
            $cell = $cells->current();
            $value = $cell->getValue();
            $this->dbKeys[$idx] = $this->fields[$value];
            $idx++;
            $cells->next();
        }
    }

    /**
     * processExcel
     * 엑셀 데이터 DB 저장
     */
    public function processExcel()
    {
        $rows = $this->sheet->getRowIterator(4);
        $db = \App::getInstance('DB');
        while ($rows->valid()) {
            $row = $rows->current();
            $db->begin_tran();
            try {
                $this->processCells($row);
            } catch (\Throwable $e) {
                $db->rollback();
                throw $e;
            }
            $db->commit();
            $rows->next();
        }
        echo '</table>' . chr(10);
        $this->printExcel();
        unset($this->excelBody, $this->excelHeader, $this->excelFooter);

        return true;
    }

    /**
     * processCells
     * cell 체크 및 엑셀 결과 세팅
     *
     * @param \PhpOffice\PhpSpreadsheet\Worksheet\Row $row
     *
     * @return bool
     * @throws \Throwable
     */
    protected function processCells($row)
    {
        $cells = $row->getCellIterator();

        $isMemberNoEach = true;
        $memberData = $beforeMember = [];
        $failMsg = null;
        $cells->rewind();
        $idx = 1;

        echo '<tr>' . chr(10);
        echo '<td>' . ($row->getRowIndex() - 3) . '</td>' . chr(10);
        while ($cells->valid() && $failMsg === null) {
            $cell = $cells->current();
            $value = trim($cell->getValue());
            $dbKey = $this->dbKeys[$idx];
            $dbName = $this->dbNames[$dbKey];
            $cells->next();
            $idx++;
            switch ($dbName) {
                case 'member':
                    $memberData[$dbKey] = StringUtils::strIsSet($value);
                    $this->processMember($memberData, $dbKey);
                    break;
            }
        }
        $existMember = false;
        if (!empty($memberData['memNo'])) {
            $beforeMember = $this->db->getData(DB_MEMBER, $memberData['memNo'], 'memNo');
            if (empty(gd_isset($memberData['memPw'], ''))) {
                unset($beforeMember['memPw']);
            }
            $existMember = $beforeMember['memNo'] == $memberData['memNo'];
        }
        if ((empty($memberData['memNo']) || $existMember === false)) {
            $isMemberNoEach = false;
        } else if ($existMember) {
            $this->memberHandleMode = 'update';
            if (!empty($memberData['memId']) && $beforeMember['memId'] != $memberData['memId']) {
                $this->addBodyByErrors($row->getRowIndex(), $memberData, [__('기존 회원의 아이디는 수정할 수 없습니다')]);
            }
            \Session::set(Member::SESSION_MODIFY_MEMBER_INFO, $beforeMember);
            $memberData = gd_array_merge($beforeMember, $memberData);
        }

        if (!$isMemberNoEach) {
            $requireErrors = $this->getRequireErrors($memberData);
            if (gd_count($requireErrors) > 0) {
                $this->addBodyByErrors($row->getRowIndex(), $memberData, $requireErrors);
            }
        }

        $overlapErrors = $this->getOverlapErrors($memberData);
        if (gd_count($overlapErrors) > 0) {
            $this->addBodyByErrors($row->getRowIndex(), $memberData, $overlapErrors);
        }

        $memberValidation = new MemberValidation();
        if ($memberValidation->isUnableId($memberData['memId'])) {
            $this->addBodyByErrors($row->getRowIndex(), $memberData, [__('가입불가 회원 아이디')]);
        }

        try {
            if (!$isMemberNoEach || $this->hasMemberNoField) {
                $memberVO = new MemberVO($memberData);
                $memberNo = $this->memberAdminService->register($memberVO);
                $memberData['memNo'] = $memberNo;
                $this->memberAdminService->applyExcelCoupon('excel', 0, $memberData['groupSno'], $memberData);
                $this->memberHandleResult = __('등록');
                $this->addBodyByRegister($row->getRowIndex(), $memberData);
            } else {
                $this->memberAdminService->modifyMemberData($memberData, 'excel');
                $this->memberHandleMode = 'update';
                $this->memberHandleResult = __('수정');
                $this->addBodyByModify($row->getRowIndex(), $memberData);
            }
        } catch (Exception $e) {
            $this->addBodyByErrors($row->getRowIndex(), $memberData, [$e->getMessage()]);
        }

        return true;
    }

    /**
     * processMember
     * 데이터 저장 전 cell 값 재 가공
     *
     * @param $memberData
     * @param $dbKey
     */
    protected function processMember(&$memberData, $dbKey)
    {
        switch($dbKey){
            case 'memPwEnc':
                if(empty($memberData['memPw']) && !empty($memberData[$dbKey])){
                    $this->memberAdminService->setIsExcelUpload(true);
                    $memberData['memPw'] = $memberData['memPwEnc'];
                }
                break;
        }
    }

    /**
     * 회원 정보 저장 시 중복데이터 체크 후 오류 메시지 추가 함수
     *
     * @param array $member
     *
     * @return array
     */
    public function getOverlapErrors(array $member)
    {
        $overlapMembers = $this->getMemberByOverlap($member);
        $errors = [];
        if (gd_count($overlapMembers) > 0) {
            foreach ($overlapMembers as $overlapMember) {
                foreach ($overlapMember as $index => $item) {
                    if (!empty($item) && $item == $member[$index]) {
                        $errors[] = $this->fieldTexts[$index] . __(' 중복');
                    }
                }
            }
        }

        return $errors;
    }

    /**
     * 업로드 결과에 오류 내용을 추가하는 함수
     *
     * @param       $i
     * @param array $memberData
     * @param array $errors
     */
    public function addBodyByErrors($i, array $memberData, array $errors)
    {
        $this->excelBody[] = '<tr>' . chr(10);
        $this->excelBody[] = '<td>' . ($i - 3) . '</td>' . chr(10);
        $this->excelBody[] = '<td>' . $memberData['memNo'] . '</td>' . chr(10);
        $this->excelBody[] = '<td>' . $memberData['memId'] . '</td>' . chr(10);
        $this->excelBody[] = '<td>' . $this->memberHandleMode . ' (' . $this->memberHandleResult . ') ' . gd_implode(chr(10), $errors) . '</td>' . chr(10);
        $this->excelBody[] = '</tr>' . chr(10);
    }

    /**
     * 업로드 시 필수 데이터 체크 후 오류 메시지 반환 함수
     *
     * @param array $member
     *
     * @return array
     */
    public function getRequireErrors(array $member)
    {
        $errors = [];
        if (empty(gd_isset($member['memId'], ''))) {
            $errors[] = $this->fieldTexts['memId'] . __(' 값은 필수입니다.');
        }
        if (empty(gd_isset($member['memPw'], '')) && empty(gd_isset($member['memPwEnc'], ''))) {
            $errors[] = $this->fieldTexts['memPw'] . __(' 값은 필수입니다.');
        }
        if (empty(gd_isset($member['memNm'], ''))) {
            $errors[] = $this->fieldTexts['memNm'] . __(' 값은 필수입니다.');
        }

        return $errors;
    }

    /**
     * 업로드 결과에 회원등록 메시지 추가 함수
     *
     * @param       $i
     * @param array $memberData
     */
    public function addBodyByRegister($i, array $memberData)
    {
        $this->excelBody[] = '<tr>' . chr(10);
        $this->excelBody[] = '<td>' . ($i - 3) . '</td>' . chr(10);
        $this->excelBody[] = '<td>' . $memberData['memNo'] . '</td>' . chr(10);
        $this->excelBody[] = '<td>' . $memberData['memId'] . '</td>' . chr(10);
        $this->excelBody[] = '<td>' . $this->memberHandleMode . ' (' . $this->memberHandleResult . ')</td>' . chr(10);
        $this->excelBody[] = '</tr>' . chr(10);
    }

    /**
     * 업로드 결과에 회원수정 메시지 추가 함수
     *
     * @param       $i
     * @param array $memberData
     */
    public function addBodyByModify($i, array $memberData)
    {
        $this->excelBody[] = '<tr>' . chr(10);
        $this->excelBody[] = '<td>' . ($i - 3) . '</td>' . chr(10);
        $this->excelBody[] = '<td>' . $memberData['memNo'] . '</td>' . chr(10);
        $this->excelBody[] = '<td>' . $memberData['memId'] . '</td>' . chr(10);
        $this->excelBody[] = '<td>' . $this->memberHandleMode . ' (' . $this->memberHandleResult . ')</td>' . chr(10);
        $this->excelBody[] = '</tr>' . chr(10);
    }

    /**
     * @param boolean $isTransaction
     */
    public function setIsTransaction($isTransaction)
    {
        $this->isTransaction = $isTransaction;
    }

    /**
     * 업로드 정보 중복 데이터를 테이블을 통해 체크하는 함수
     *
     * @param array $member
     *
     * @return array|object
     */
    public function getMemberByOverlap(array $member)
    {
        $binders = $wheres = [];
        $this->db->query_reset();
        $this->db->strField = 'memId, nickNm, email';
        $wheres[] = 'memId=?';
        $this->db->bind_param_push($binders, 's', $member['memId']);
        if (empty(gd_isset($member['nickNm'], ''))) {
            $wheres[] = 'nickNm=?';
            $this->db->bind_param_push($binders, 's', $member['nickNm']);
        }
        if (empty(gd_isset($member['email'], ''))) {
            $wheres[] = 'email=?';
            $this->db->bind_param_push($binders, 's', $member['email']);
        }
        $this->db->strWhere = gd_implode(' OR ', $wheres);
        if ($member['memNo'] > 0) {
            $this->db->strWhere = '(' . $this->db->strWhere . ') AND memNo!=?';
            $this->db->bind_param_push($binders, 's', $member['memNo']);
        }

        $query = $this->db->query_complete();
        $strSQL = 'SELECT ' . array_shift($query) . ' FROM ' . DB_MEMBER . gd_implode(' ', $query);

        $data = $this->db->query_fetch($strSQL, $binders);

        unset($strSQL);

        return $data;
    }
}
