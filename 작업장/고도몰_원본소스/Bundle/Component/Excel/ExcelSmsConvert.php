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

use Framework\Security\Otp;
use Framework\Utility\DateTimeUtils;
use Framework\Utility\StringUtils;
use Respect\Validation\Rules\Length;
use Respect\Validation\Rules\Min;
use Respect\Validation\Rules\NotEmpty;
use Respect\Validation\Rules\StringType;
use Respect\Validation\Validator;

/**
 * Class ExcelSmsConvert
 * @package Bundle\Component\Excel
 * @author  yjwee <yeongjong.wee@godo.co.kr>
 */
class ExcelSmsConvert extends \Component\Excel\ExcelDataConvert
{
    /** @var string $uploadKey 엑셀 업로드시 생성되는 고유 키 */
    protected $uploadKey;
    /** @var string $fileName 업로드한 엑셀 파일명 */
    protected $fileName;
    /** @var integer $managerSno 업로드 시 로그인되어있는 관리자번호 */
    protected $managerSno;
    /** @var \PhpOffice\PhpSpreadsheet\Worksheet $sheet */
    protected $sheet;
    /** @var integer $totalRow 시트의 numRows 값 */
    private $totalRow;
    /** @var integer $totalColumn 시트의 numCols 값 */
    private $totalColumn;
    private $row = 4;
    private $column = 1;
    private $rows = [];
    /** @var integer $HeadRows 시트의 헤드 Row 개수 */
    private $HeadRows = 3;
    /** @var integer $bulkSmsNums Multiple Insert 개수 */
    private $bulkSmsNums = 100;
    /** @var array $bulkSmsExcel Multiple Insert 데이터 */
    private $bulkSmsExcel = [];
    /** @var integer $bulkSmsRow Multiple Insert 데이터 개수 */
    private $bulkSmsRow = 0;
    /** @var integer $bulkSmsTotalRow Multiple Insert 데이터 전체 개수 */
    private $bulkSmsTotalRow = 0;
    /** @var array $tmpCellPhones 중복 유효성 검증용 데이터 */
    private $tmpCellPhones = [];

    /**
     * 엑셀 업로드
     *
     * @return bool
     */
    public function upload()
    {
        if ($this->hasError()) {
            $this->createBodyByError();
            $this->makeFile();

            return false;
        }
        if (!$this->read()) {
            $this->createBodyByReadError();
            $this->makeFile();

            return false;
        }
        if (!$this->hasData()) {
            $this->createDataError();
            $this->makeFile();

            return false;
        }
        $this->setFileName();
        $this->setUploadKey();
        $this->setManagerSno();
        $this->excelBody = [];
        $this->createTableHeader();
        $excelSms = \App::load('Component\\Excel\\ExcelSms');
        $excelField = $excelSms->formatSms();
        $this->resetExcelCode($excelField);
        $this->setTableKey();
        $this->getRows();
        $this->process();
        $this->makeFile();

        return true;
    }

    /**
     * setManagerSno
     *
     */
    protected function setManagerSno()
    {
        $session = \App::getInstance('session');
        $this->managerSno = $session->get(\Component\Member\Manager::SESSION_MANAGER_LOGIN)['sno'];
    }

    /**
     * readFileName
     *
     */
    protected function setFileName()
    {
        $request = \App::getInstance('request');
        $this->fileName = $request->files()->get('excel')['name'];
    }

    /**
     * 엑셀 업로드 키 생성
     *
     */
    protected function setUploadKey()
    {
        $this->uploadKey = DateTimeUtils::dateFormat('ymdHis', 'now') . Otp::getOtp(4, Otp::OTP_TYPE_STRING);
    }

    /**
     * 엑셀 결과 헤더 추가
     *
     */
    protected function createTableHeader()
    {
        $this->excelBody[] = '<table border="1">' . chr(10);
        $this->excelBody[] = '<tr>' . chr(10);
        $this->excelBody[] = '<td class="title">' . __('번호') . '</td>' . chr(10);
        $this->excelBody[] = '<td class="title">' . __('휴대폰 번호') . '</td>' . chr(10);
        $this->excelBody[] = '<td class="title">' . __('업로드 결과') . '</td>' . chr(10);
        $this->excelBody[] = '</tr>' . chr(10);
    }

    /**
     * setTableKey
     *
     */
    protected function setTableKey()
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

    /**
     * getRows
     *
     */
    protected function getRows()
    {
        $rows = $this->sheet->getRowIterator(4);
        $this->totalRow = $this->sheet->getHighestRow();
        $this->totalColumn = count($this->tableKeys);
        while ($rows->valid()) {
            $row = $rows->current();
            $this->column = 1;
            $this->rows[] = $this->getCells($row);
            $this->row++;
            $rows->next();
        }
    }

    /**
     * process
     *
     * @throws \Exception
     */
    protected function process()
    {
        $logger = \App::getInstance('logger');
        if (!$this->validateUploadKey()) {
            throw new \Exception(__('엑셀 업로드 키가 없습니다.'));
        }
        if (!$this->validateFileExtension()) {
            throw new \Exception(__('잘못된 파일 확장자입니다.'));
        }
        if (!$this->validateManagerSno()) {
            throw new \Exception(__('현재 접속 중인 관리자 정보를 찾을 수 없습니다.'));
        }
        $logger->info("sms excel total rows[$this->totalRow]");
        foreach ($this->rows as $index => $row) {
            $row['originalCellPhone'] = $row['cellPhone'];
            $row['cellPhone'] = StringUtils::numberToCellPhone($row['originalCellPhone']);
            if ($this->hasName($row) && !$this->validateName($row['name'])) {
                $this->addBodyByErrors($index, $row, __('이름 없음'));
                continue;
            }
            if ($row['cellPhone'] === false) {
                $this->addBodyByErrors($index, $row, __('유효하지 않은 번호'));
                continue;
            }
            if ($this->validateDuplicationCellPhone($row['cellPhone'])) {
                $this->addBodyByErrors($index, $row, __('중복'));
                continue;
            }
            $this->save($row, 'y');
            $this->addBody($index, $row);
        }
        $this->excelBody[] = '</table>' . chr(10);

        // 초기화
        $this->tmpCellPhones = [];
        $this->bulkSmsExcel = [];
    }

    /**
     * saveRow
     *
     * @param array       $cells
     * @param null|string $validateFl
     * @param null|string $validateDesc
     *
     * @return int
     */
    protected function save(array $cells, $validateFl = null, $validateDesc = null)
    {
        $smsExcel = [
            'sno'    => '',
            'uploadKey'    => $this->uploadKey,
            'fileName'     => $this->fileName,
            'cellPhone'    => $cells['cellPhone'],
            'name'    => '',
            'validateFl'   => $validateFl,
            'validateDesc' => $validateDesc,
            'managerSno'   => $this->managerSno,
        ];

        if ($this->hasName($cells) && $this->validateName($cells['name'])) {
            $smsExcel['name'] = $cells['name'];
        }

        $this->bulkSmsExcel[] = $smsExcel;
        $this->bulkSmsRow++;
        $this->bulkSmsTotalRow++;
        if ($this->bulkSmsRow >= $this->bulkSmsNums || ($this->bulkSmsTotalRow + $this->HeadRows) >= $this->totalRow) {
            $dao = \App::load('Component\\Sms\\SmsExcelLogDAO');
            $sno = $dao->inserts($this->bulkSmsExcel);
            $this->bulkSmsExcel = [];
            $this->bulkSmsRow = 0;
        }
        unset($smsExcel);

        return $sno;
    }

    /**
     * hasName
     *
     * @param array $cells
     *
     * @return bool
     */
    protected function hasName(array $cells)
    {
        return gd_array_key_exists('name', $cells);
    }

    /**
     * validateName
     *
     * @param $name
     *
     * @return bool
     */
    protected function validateName($name)
    {
        $v = Validator::allOf();
        $v->addRule(new NotEmpty());
        $v->addRule(new Length(1));
        $v->addRule(new StringType());
        $validation = $v->validate($name);

        return $validation;
    }

    /**
     * validateDuplicationCellPhone
     *
     * @param $cellPhone
     *
     * @return bool
     */
    protected function validateDuplicationCellPhone($cellPhone)
    {
        $validation = gd_in_array($cellPhone, $this->tmpCellPhones);
        if ($validation === false) {
            $this->tmpCellPhones[] = $cellPhone;
        }

        return $validation;
    }

    /**
     * getCells
     *
     * @return array
     */
    protected function getCells($row)
    {
        $cells = $row->getCellIterator();
        $cells->rewind();
        $result = [];
        while ($cells->valid()) {
            $value = trim($cells->current()->getValue());
            $result[$this->tableKeys[$this->column]] = $value;
            $cells->next();
            $this->column++;

        }

        return $result;
    }

    /**
     * addBodyByErrors
     *
     * @param       $index
     * @param array $cells
     * @param array $errors
     */
    protected function addBodyByErrors($index, array $cells, $errors)
    {
        $this->save($cells, 'n', $errors);
        $this->excelBody[] = '<tr>' . chr(10);
        $this->excelBody[] = '<td>' . ($index + 1) . '</td>' . chr(10);
        $this->excelBody[] = '<td style="mso-number-format:\@">' . $cells['originalCellPhone'] . '</td>' . chr(10);
        $this->excelBody[] = '<td>실패(' . $errors . ')</td>' . chr(10);
        $this->excelBody[] = '</tr>' . chr(10);
    }

    /**
     * addBody
     *
     * @param       $index
     * @param array $cells
     */
    protected function addBody($index, array $cells)
    {
        $this->excelBody[] = '<tr>' . chr(10);
        $this->excelBody[] = '<td class="xl24">' . ($index + 1) . '</td>' . chr(10);
        $this->excelBody[] = '<td class="xl24" style="mso-number-format:\@">' . $cells['originalCellPhone'] . '</td>' . chr(10);
        $this->excelBody[] = '<td class="xl24">정상</td>' . chr(10);
        $this->excelBody[] = '</tr>' . chr(10);
    }

    /**
     * validateManagerSno
     *
     * @return bool
     */
    protected function validateManagerSno()
    {
        $validation = Validator::intVal()->addRule(new Min(1))->validate($this->managerSno);

        return $validation;
    }

    /**
     * validateUploadKey
     *
     * @return mixed
     */
    protected function validateUploadKey()
    {
        $validation = Validator::allOf()->addRule(new StringType())->addRule(new Length(16, 16))->validate($this->uploadKey);

        return $validation;
    }

    /**
     * validateFileExtension
     *
     * @return mixed
     */
    protected function validateFileExtension()
    {
        $expData = explode('.', $this->fileName);
        $ext = ['xls', 'xlsx'];

        return in_array(array_pop($expData), $ext);
    }

    /**
     * @return string
     */
    public function getUploadKey()
    {
        return $this->uploadKey;
    }
}
