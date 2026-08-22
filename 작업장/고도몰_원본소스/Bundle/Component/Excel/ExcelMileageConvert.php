<?php
/**
 * This is commercial software, only users who have purchased a valid license
 * and accept to the terms of the License Agreement can install and use this
 * program.
 *
 * Do not edit or add to this file if you wish to upgrade Godomall5 to newer
 * versions in the future.
 *
 * @copyright ⓒ 2016, NHN godo: Corp.
 * @link      http://www.godo.co.kr
 */

namespace Bundle\Component\Excel;

use Component\Member\History;
use Component\Member\Manager;
use Component\Member\MemberDAO;
use Component\Mileage\Mileage;
use Component\Mileage\MileageDomain;
use Component\Validator\Validator;
use Framework\Utility\UrlUtils;
use Globals;
use Request;
use Session;

/**
 * Class ExcelMileageConvert
 * @package Bundle\Component\Excel
 * @author  yjwee <yeongjong.wee@godo.co.kr>
 */
class ExcelMileageConvert extends \Component\Excel\ExcelDataConvert
{
    /** @var  \Bundle\Component\Member\History $historyService */
    private $historyService;
    /** @var  \Bundle\Component\Member\MemberDAO $memberDAO */
    private $memberDAO;
    /** @var  \Bundle\Component\Mileage\Mileage $mileageService */
    private $mileageService;
    private $excelMileage;
    /** @var \PhpOffice\PhpSpreadsheet\Worksheet $sheet */
    protected $sheet;
    protected $fields = [];
    private $totalRow;
    private $totalColumn;
    private $row = 4;
    private $column = 1;
    private $mileages = [];
    private $mailReceivers = [];
    private $smsReceivers = [];
    private $mileageCode;
    private $isSendGuideSms = false;
    private $isSendGuideMail = false;

    /**
     * @inheritDoc
     */
    public function __construct(MemberDAO $memberDAO = null, History $history = null)
    {
        parent::__construct();
        $this->excelMileage = new ExcelMileage();
        $this->mileageService = new Mileage();

        if ($memberDAO == null) {
            $memberDAO = new MemberDAO();
        }
        $this->memberDAO = $memberDAO;
        if ($history == null) {
            $history = new History();
        }
        $this->historyService = $history;
        $this->mileageCode = gd_code(Mileage::REASON_CODE_GROUP);
    }

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
        $this->_createTableHeader();
        $excelMember = new ExcelMileage();
        $fields = $excelMember->formatMileage();
        $this->resetExcelCode($fields);
        $this->_setTableKey();
        $this->_processExcel();
        $this->_processMileage();
        $this->printExcel();

        return true;
    }

    private function _createTableHeader()
    {
        $this->excelBody[] = '<table border="1">' . chr(10);
        $this->excelBody[] = '<tr>' . chr(10);
        $this->excelBody[] = '<td>' . __('번호') . '</td><td>' . __('아이디') . '</td><td>' . __('지급/차감 마일리지') . '</td>' . chr(10);
        $this->excelBody[] = '</tr>' . chr(10);
    }

    private function _setTableKey()
    {
        $cells = $this->sheet->getRowIterator(2)->current()->getCellIterator();
        $cells->rewind();
        $idx = 1;
        while ($cells->valid()) {
            $value = $cells->current()->getValue();
            $this->tableKeys[$idx] = $this->fields[$value];
            $idx++;
            $cells->next();
        }
    }

    private function _processExcel()
    {
        $rows = $this->sheet->getRowIterator(4);
        $this->totalRow = $this->sheet->getHighestRow();
        $this->totalColumn = count($this->tableKeys);
        while ($rows->valid()) {
            $row = $rows->current();
            $this->column = 1;
            $this->mileages[] = $this->_rowToMileage($row);
            $this->row++;
            $rows->next();
        }
    }

    private function _processMileage()
    {
        foreach ($this->mileages as $index => $mileage) {
            $member = $this->memberDAO->selectMileageByMemberId($mileage['memId']);
            if (gd_isset($member['memNo'], '') == '') {
                $this->_addBodyError($index, $mileage, __('ID를 찾을 수 없습니다. 휴면회원은 지급/차감 대상이 아닙니다.'));
                continue;
            }
            if (Validator::signDouble($mileage['mileage'], null, null, true) == false) {
                $this->_addBodyError($index, $mileage, __('마일리지는 필수 입니다.'));
                continue;
            }
            $maxlength = $mileage['mileage'] < 0 ? 9 : 8;
            if (Validator::maxlen($maxlength, $mileage['mileage'], true) == false) {
                $this->_addBodyError($index, $mileage, __('지급/차감 금액이 최대 입력 자리수(8자리)를 초과하여 처리되지 않았습니다.'));
                continue;
            }

            $domain = $this->_saveMileageWithUseHistory($member, $mileage);
            $this->_updateMemberMileageWithHistory($domain);
            $this->_addGuideMailReceiver($member, $domain->toArray());
            $this->_addGuideSmsReceiver($member, $domain->toArray());
            $this->_addBody($index, $mileage);
        }
    }

    /**
     * sms 수신자 추가
     *
     * @param array $member
     * @param array $domain
     */
    private function _addGuideSmsReceiver(array $member, array $domain)
    {
        // 2017-02-09 yjwee 마일리지 지급/차감의 경우 회원의 광고정보수신동의 여부와 무관하다.
        $isGuideSms = ($member['cellPhone'] != '') && $this->isSendGuideSms;
        $groupInfo = \Component\Member\Group\Util::getGroupName('sno=' . $member['groupSno']);
        if ($isGuideSms) {
            $aBasicInfo = gd_policy('basic.info');
            $this->smsReceivers[] = [
                'memNo'      => $member['memNo'],
                'cellPhone'  => $member['cellPhone'],
                'name'       => $member['memNm'],
                'rc_mileage' => $domain['mileage'],
                'mileageSno' => $domain['sno'],
                'memNm'       => $member['memNm'],
                'memId'       => $member['memId'],
                'mileage'     => $member['mileage'],
                'deposit'     => $member['deposit'],
                'groupNm'     => $groupInfo[$member['groupSno']],
                'rc_mallNm' => Globals::get('gMall.mallNm'),
                'shopUrl' => $aBasicInfo['mallDomain']
            ];
        }
    }

    private function _addGuideMailReceiver(array $member, array $domain)
    {
        $isGuideMail = ($member['email'] != '') && ($member['maillingFl'] == 'y') && $this->isSendGuideMail;
        if ($isGuideMail) {
            $this->mailReceivers[] = [
                'memNo'            => $member['memNo'],
                'email'            => $member['email'],
                'memId'            => $member['memId'],
                'mileage'          => $domain['mileage'],
                'totalMileage'     => $domain['afterMileage'],
                'deleteScheduleDt' => $domain['deleteScheduleDt'],
                'mileageSno'       => $domain['sno'],
            ];
        }
    }

    /**
     * 마일리지 지급 및 마일리지 사용내역 저장 함수
     *
     * @param array $member
     * @param array $mileage
     *
     * @return \Bundle\Component\Mileage\MileageDomain
     */
    private function _saveMileageWithUseHistory(array $member, array $mileage)
    {
        gd_isset($mileage['handleMode'], 'm');
        gd_isset($mileage['reasonCd'], Mileage::REASON_CODE_GROUP . Mileage::REASON_CODE_ETC);
        if (empty($mileage['handleCd'])) {
            $mileage['handleCd'] = null;
        }

        $mileage['memNo'] = $member['memNo'];
        $mileage['beforeMileage'] = $member['mileage'];
        $mileage['afterMileage'] = $member['mileage'] + $mileage['mileage'];
        if ($mileage['contents'] == '') {
            $mileage['contents'] = $this->mileageCode[$mileage['reasonCd']];
        }

        $domain = $this->mileageService->saveMileage(new MileageDomain($mileage));
        $this->mileageService->saveMileageUseHistory($domain);

        return $domain;
    }

    private function _updateMemberMileageWithHistory(MileageDomain $domain)
    {
        $member = [
            'memNo'   => $domain->getMemNo(),
            'mileage' => $domain->getAfterMileage(),
        ];
        $this->memberDAO->updateMember($member, ['mileage'], []);

        $manager = Session::get(Manager::SESSION_MANAGER_LOGIN);
        $this->historyService->setAfter(['memNo' => $domain->getMemNo()]);
        $this->historyService->setProcessor($manager['managerId']);
        $this->historyService->setManagerNo($manager['sno']);
        $this->historyService->setProcessorIp(Request::getRemoteAddress());
        $this->historyService->insertHistory(
            'mileage', [
                $domain->getBeforeMileage(),
                $domain->getAfterMileage(),
            ]
        );
    }

    /**
     * @param $row
     * @return array
     */
    private function _rowToMileage($row)
    {
        $cells = $row->getCellIterator();
        $cells->rewind();
        $mileage = [];
        while ($cells->valid()) {
            $value = trim($cells->current()->getValue());
            $mileage[$this->tableKeys[$this->column]] = $value;
            $cells->next();
            $this->column++;

        }

        return $mileage;
    }

    private function _addBody($i, array $mileage)
    {
        $this->excelBody[] = '<tr>' . chr(10);
        $this->excelBody[] = '<td>' . ($i + 1) . '</td>' . chr(10);
        $this->excelBody[] = '<td>' . $mileage['memId'] . '</td>' . chr(10);
        $this->excelBody[] = '<td>' . $mileage['mileage'] . '</td>' . chr(10);
        $this->excelBody[] = '</tr>' . chr(10);
    }

    private function _addBodyError($i, array $mileage, $message)
    {
        $this->excelBody[] = '<tr>' . chr(10);
        $this->excelBody[] = '<td>' . ($i + 1) . '</td>' . chr(10);
        $this->excelBody[] = '<td>' . $mileage['memId'] . '</td>' . chr(10);
        $this->excelBody[] = '<td>' . $message . '</td>' . chr(10);
        $this->excelBody[] = '</tr>' . chr(10);
    }

    public function getSmsReceivers()
    {
        return $this->smsReceivers;
    }

    public function getMailReceivers()
    {
        return $this->mailReceivers;
    }

    public function setSendGuideSms($isSendGuideSms)
    {
        $this->isSendGuideSms = $isSendGuideSms;
    }

    public function setSendGuideMail($isSendGuideMail)
    {
        $this->isSendGuideMail = $isSendGuideMail;
    }
}
