<?php
/**
 * This is commercial software, only users who have purchased a valid license
 * and accept to the terms of the License Agreement can install and use this
 * program.
 *
 * Do not edit or add to this file if you wish to upgrade Godomall to newer
 * versions in the future.
 *
 * @copyright ⓒ 2024, NHN COMMERCE Corp.
 */

namespace Bundle\Component\Excel;

use App;
use Bundle\Component\Excel\Enum\ExcelSampleType;
use Bundle\Component\Member\Manager;
use Framework\Utility\ImageUtils;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Helper\Html;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class ExcelSample
{
    private Spreadsheet $spreadsheet;

    private Worksheet $worksheet;

    private ExcelSampleType $excelSampleType;

    public function __construct(ExcelSampleType $excelSampleType)
    {
        $this->spreadsheet = new Spreadsheet();
        $this->spreadsheet->getProperties()
            ->setTitle('Excel Down')
            ->setCreator('NHN Commerce')
            ->setLastModifiedBy('NHN Commerce');
        $this->worksheet = $this->spreadsheet->getActiveSheet();
        $this->excelSampleType = $excelSampleType;
    }

    public function outputSample(): void
    {
        try {
            // 엑셀 샘플 파일 생성
            switch ($this->excelSampleType) {
                case ExcelSampleType::MEMBER:
                    $this->spreadsheet = $this->createMemberUploadSample();
                    break;
                case ExcelSampleType::GOODS:
                    $this->spreadsheet = $this->createGoodsUploadSample();
                    break;
                case ExcelSampleType::DEPOSIT:
                    $this->spreadsheet = $this->createDepositUploadSample();
                    break;
                case ExcelSampleType::MILEAGE:
                    $this->spreadsheet = $this->createMileageUploadSample();
                    break;
                case ExcelSampleType::SMS:
                    $this->spreadsheet = $this->createSmsUploadSample();
                    break;
                case ExcelSampleType::COUPON:
                    $this->spreadsheet = $this->createCouponUploadSample();
                    break;
                case ExcelSampleType::PAPER_COUPON:
                    $this->spreadsheet = $this->createPaperCouponUploadSample();
                    break;
                case ExcelSampleType::INVOICE:
                    $this->spreadsheet = $this->readInvoiceUploadSample();
                    break;
                case ExcelSampleType::EXTERNAL_ORDER:
                    $this->spreadsheet = $this->readExternalOrderUploadSample();
                    break;
                case ExcelSampleType::DELIVERY_AREA:
                    $this->spreadsheet = $this->readDeliveryAreaUploadSample();
                    break;
            }

            // 엑셀 샘플 파일 output to browser
            $filename = $this->excelSampleType->getFilename();
            $this->output($filename);

        } catch (\Exception $e) {
            \Logger::error(sprintf('Failed to output [%s] excel sample (%s) (%s) (%s)', $this->excelSampleType->value, __METHOD__, $e->getCode(), $e->getMessage()));
        }
    }

    protected function output(string $fileName): void
    {
        header('Content-Type: application/vnd.ms-excel');
        header(sprintf('Content-Disposition: attachment;filename="%s.xlsx"', $fileName));
        header('Cache-Control: max-age=0');

        $writer = new Xlsx($this->spreadsheet);
        $writer->save('php://output');
        exit();
    }

    /**
     * 회원 업로드 샘플 엑셀 파일 생성
     *
     * @return Spreadsheet
     */
    private function createMemberUploadSample(): Spreadsheet
    {
        // 기본 설정
        $excelMember = new ExcelMember();
        $excelMemberField = $excelMember->formatMember();
        $headerField = [
            'text',
            'excelKey',
            'comment',
        ];

        // 전체 항목 선택
        $setData = [];
        foreach ($excelMemberField as $key => $val) {
            $setData['fieldCheck'][$key] = $val['dbKey'];
        }

        // 엑셀 헤더 작성
        $rowIndex = $this->writeHeaderData($headerField, $excelMemberField, $setData['fieldCheck']);
        $headerLastRow = $rowIndex - 1;

        // 샘플 데이터 작성
        $this->writeSampleData($excelMemberField, $setData['fieldCheck'], $rowIndex);

        // set cell style
        $this->setHeaderCellStyle($headerLastRow);
        $this->setAllCellStyle();

        return $this->spreadsheet;
    }

    /**
     * 상품 업로드 샘플 엑셀 파일 생성
     *
     * @return Spreadsheet
     */
    private function createGoodsUploadSample(): Spreadsheet
    {
        // 기본 설정
        $excelDataConvert = new ExcelDataConvert();
        $excelGoodsField = $excelDataConvert->excelGoods();
        $headerField = [
            'text',
            'excelKey',
            'comment',
        ];

        // 제외 항목
        $excludeArrField = [
            'orderGoodsCnt', // 주문상품 수
            'hitCnt', // 조회수
            'orderRate', // 구매율
            'cartCnt', // 장바구니 수
            'wishCnt', // 관심상품 수
            'reviewCnt', // 후기 수
        ];

        // 전체 항목 선택
        $setData = [];
        foreach ($excelGoodsField as $key => $val) {
            // 제외 항목 제거
            if (gd_in_array($val['dbKey'], $excludeArrField)) {
                unset($excelGoodsField[$key]);
                continue;
            }
            $setData['fieldCheck'][$key] = $val['dbKey'];
        }

        // 엑셀 헤더 작성
        $rowIndex = $this->writeHeaderData($headerField, $excelGoodsField, $setData['fieldCheck'], true);
        $headerLastRow = $rowIndex - 1;

        // 샘플 데이터 작성
        $this->writeSampleData($excelGoodsField, $setData['fieldCheck'], $rowIndex);

        // set cell style
        $this->setHeaderCellStyle($headerLastRow);
        $this->setAllCellStyle();

        return $this->spreadsheet;
    }

    /**
     * 예치금 지급 대상 회원 등록 샘플 엑셀 파일 생성
     *
     * @return Spreadsheet
     */
    private function createDepositUploadSample(): Spreadsheet
    {
        $excelDeposit = new ExcelDeposit();
        $excelDepositField = $excelDeposit->formatDeposit();
        $headerField = [
            'text',
            'excelKey',
            'comment',
        ];

        $setData = [];
        foreach ($excelDepositField as $key => $val) {
            $setData['fieldCheck'][$key] = $val['dbKey'];
        }

        // 엑셀 헤더 작성
        $rowIndex = $this->writeHeaderData($headerField, $excelDepositField, $setData['fieldCheck']);
        $headerLastRow = $rowIndex - 1;

        // 샘플 데이터 작성
        $this->writeSampleData($excelDepositField, $setData['fieldCheck'], $rowIndex);

        // set cell style
        $this->setHeaderCellStyle($headerLastRow);
        $this->setAllCellStyle();

        return $this->spreadsheet;
    }

    /**
     * 마일리지 지급 대상 회원 등록 샘플 엑셀 파일 생성
     *
     * @return Spreadsheet
     */
    private function createMileageUploadSample(): Spreadsheet
    {
        $excelMileage = new ExcelMileage();
        $excelMileageField = $excelMileage->formatMileage();
        $headerField = [
            'text',
            'excelKey',
            'comment',
        ];

        $setData = [];
        foreach ($excelMileageField as $key => $val) {
            $setData['fieldCheck'][$key] = $val['dbKey'];
        }

        // 엑셀 헤더 작성
        $rowIndex = $this->writeHeaderData($headerField, $excelMileageField, $setData['fieldCheck']);
        $headerLastRow = $rowIndex - 1;

        // 샘플 데이터 작성
        $this->writeSampleData($excelMileageField, $setData['fieldCheck'], $rowIndex);

        // set cell style
        $this->setHeaderCellStyle($headerLastRow);
        $this->setAllCellStyle();

        return $this->spreadsheet;
    }

    /**
     * SMS 발송 대상 회원 등록 샘플 엑셀 파일 생성
     *
     * @return Spreadsheet
     */
    private function createSmsUploadSample(): Spreadsheet
    {
        $excelSms = \App::load('Component\\Excel\\ExcelSms');
        $excelSmsField = $excelSms->formatSms();
        $headerField = [
            'text',
            'excelKey',
            'desc',
        ];

        $setData = [];
        foreach ($excelSmsField as $key => $val) {
            $setData['fieldCheck'][$key] = $val['dbKey'];
        }

        // 엑셀 헤더 작성
        $rowIndex = $this->writeHeaderData($headerField, $excelSmsField, $setData['fieldCheck']);
        $headerLastRow = $rowIndex - 1;

        // 샘플 데이터
        $sampleDatas = [];
        $maxRows = 1;
        for ($i = 0; $i < $maxRows; $i++) {
            $sampleRow = [];
            foreach ($excelSmsField as $field) {
                if ($maxRows === 1) {
                    $sampleRow[0][$field['dbKey']] = $field['sample'];
                } else {
                    $sampleRow[0][$field['dbKey']] = $field['dbKey'] == 'name' ? "{$field['sample']}{$i}" : sprintf("010-%s-%s", rand(1000, 9999), rand(1000, 9999));
                }
            }
            $sampleDatas[] = $sampleRow;
        }
        unset($excelSmsField, $headerField);

        // write data
        foreach ($sampleDatas as $sampleData) {
            $rowData = [];
            foreach ($setData['fieldCheck'] as $fVal) {
                $rowData[] = $sampleData[0][$fVal];
            }
            $this->worksheet->fromArray([$rowData], null, "A{$rowIndex}");
            $rowIndex++;
        }

        // set cell style
        $this->setHeaderCellStyle($headerLastRow);
        $this->setAllCellStyle();

        return $this->spreadsheet;
    }

    /**
     * 쿠폰 발급 대상 회원 등록 샘플 엑셀 파일
     *
     * @return Spreadsheet
     */
    private function createCouponUploadSample(): Spreadsheet
    {
        // 엑셀 헤더 작성
        $headerField = ['발급할 회원아이디'];
        $rowIndex = 1;
        $this->worksheet->fromArray($headerField, null, 'A' . $rowIndex++);
        $headerLastRow = $rowIndex - 1;

        // 샘플 데이터
        $sampleDatas = ['AAAAA', 'BBBBB', 'CCCCC', 'DDDDD', 'EEEEE'];

        // write data
        foreach ($sampleDatas as $sampleData) {
            $this->worksheet->fromArray([$sampleData], null, "A{$rowIndex}");
            $rowIndex++;
        }

        // set cell style
        $this->setHeaderCellStyle($headerLastRow);
        $this->setAllCellStyle();

        return $this->spreadsheet;
    }

    /**
     * 페이퍼쿠폰 인증번호 등록 샘플 엑셀 파일 생성
     *
     * @return Spreadsheet
     */
    private function createPaperCouponUploadSample(): Spreadsheet
    {
        // 엑셀 헤더 작성
        $headerField = ['등록할 인증번호'];
        $rowIndex = 1;
        $this->worksheet->fromArray($headerField, null, 'A' . $rowIndex++);
        $headerLastRow = $rowIndex - 1;

        // 샘플 데이터
        $sampleDatas = ['AAAAAAAAAAAA', 'BBBBBBBBBBBB', 'CCCCCCCCCCCC', 'DDDDDDDDDDDD', 'EEEEEEEEEEEE'];

        // write data
        foreach ($sampleDatas as $sampleData) {
            $this->worksheet->fromArray([$sampleData], null, "A{$rowIndex}");
            $rowIndex++;
        }

        // set cell style
        $this->setHeaderCellStyle($headerLastRow);
        $this->setAllCellStyle();

        return $this->spreadsheet;
    }

    /**
     * 송장일괄등록 엑셀 샘플 파일 읽기
     *
     * @return Spreadsheet
     */
    private function readInvoiceUploadSample(): Spreadsheet
    {
        if (Manager::isProvider()) {
            $filename = 'order_scmOrderInvoiceSample.xlsx';
        } else {
            $filename = 'order_orderInvoiceSample.xlsx';
        }

        return IOFactory::load(App::getBasePath() . "/data/excel/$filename");
    }

    /**
     * 외부 채널 주문 등록 엑셀 샘플 파일 읽기
     *
     * @return Spreadsheet
     */
    private function readExternalOrderUploadSample(): Spreadsheet
    {
        $filename = 'order_externalOrderSample.xlsx';
        return IOFactory::load(App::getBasePath() . "/data/excel/$filename");
    }

    /**
     * 지역별 배송비 등록 엑셀 샘플 파일 읽기
     *
     * @return Spreadsheet
     */
    private function readDeliveryAreaUploadSample(): Spreadsheet
    {
        $filename = 'policy_sampleAreaDelivery.xlsx';
        return IOFactory::load(App::getBasePath() . "/data/excel/$filename");
    }

    /**
     * 엑셀 헤더 구간 작성
     *
     * @param array $headerField
     * @param array $excelField
     * @param array $fieldCheck
     * @param bool $hasImage
     *
     * @return int header 아래 행의 row index
     */
    private function writeHeaderData(array $headerField, array $excelField, array $fieldCheck, bool $hasImage = false): int
    {
        $rowIndex = 1;
        for ($i = 0, $iMax = gd_count($headerField); $i < $iMax; $i++) {
            $rowData = [];
            foreach ($excelField as $field) {
                if (gd_in_array($field['dbKey'], $fieldCheck)) {
                    $rowData[] = (new Html())->toRichTextObject($field[$headerField[$i]] ?? '');

                    // 이미지 설정
                    if ($hasImage && $i == 0 && $field['dbKey'] == 'imageName') {
                        $tmp = gd_policy('goods.image');
                        ImageUtils::sortImageConf($tmp); // 이미지 순서 변경
                        $imageKey = gd_array_keys($tmp);
                        unset($tmp);
                    }
                }
            }
            // Add row data to the sheet
            $this->worksheet->fromArray([$rowData], null, "A{$rowIndex}");
            $rowIndex++;
        }
        return $rowIndex;
    }

    /**
     * 엑셀 샘플 데이터 작성
     *
     * @return void
     */
    private function writeSampleData(array $excelField, array $fieldCheck, int $rowIndex): void
    {
        $sampleDatas = [];
        foreach ($excelField as $field) {
            $sampleDatas[0][$field['dbKey']] = $field['sample'];
        }

        // 샘플 데이터 작성
        foreach ($sampleDatas as $sampleData) {
            $rowData = [];
            foreach ($fieldCheck as $field) {
                $rowData[] = (new Html())->toRichTextObject($sampleData[$field] ?? '');
            }
            $this->worksheet->fromArray([$rowData], null, "A{$rowIndex}");
            $rowIndex++;
        }
    }

    private function setHeaderCellStyle(int $headerLastRow): void
    {
        $lastColumn = $this->worksheet->getHighestColumn();

        for ($row = 1; $row <= $headerLastRow; $row++) {
            $headerCellStyle = $this->getHeaderCellStyle();
            $this->worksheet->getStyle("A{$row}:{$lastColumn}{$row}")->applyFromArray($headerCellStyle);
        }
    }

    private function getHeaderCellStyle(): array
    {
        return [
            'font' => [
                'name' => '맑은 고딕 (본문)',
                'size' => 10,
                'bold' => true,
            ],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['rgb' => 'F6F6F6'],
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
            ],
        ];
    }

    private function setAllCellStyle(): void
    {
        // 시트 내의 모든 셀에 대해 수직 중앙 정렬 적용
        $this->worksheet->getStyle($this->worksheet->calculateWorksheetDimension())->getAlignment()->setVertical(Alignment::VERTICAL_CENTER);

        // 시트 내의 모든 셀에 대해 border 적용
        $allCellStyle = $this->getAllCellStyle();
        $cellCoordinate = "A1:{$this->worksheet->getHighestColumn()}{$this->worksheet->getHighestRow()}";
        $this->worksheet->getStyle($cellCoordinate)->applyFromArray($allCellStyle);

        // 자동 너비 조정
        foreach ($this->worksheet->getColumnIterator() as $column) {
            $this->worksheet->getColumnDimension($column->getColumnIndex())->setAutoSize(true);
        }
    }

    private function getAllCellStyle(): array
    {
        return [
            'font' => [
                'name' => '맑은 고딕 (본문)',
                'size' => 10,
            ],
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color' => ['rgb' => '000000'],
                ],
            ],
            'alignment' => [
                'wrapText' => true, // 줄 바꿈 허용
            ],
        ];
    }
}
