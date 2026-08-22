<?php
/**
 * This is commercial software, only users who have purchased a valid license
 * and accept to the terms of the License Agreement can install and use this
 * program.
 *
 * Do not edit or add to this file if you wish to upgrade Godomall to newer
 * versions in the future.
 *
 * @copyright ⓒ 2023, NHN COMMERCE Corp.
 */

namespace Bundle\Component\Excel;

use App;
use Session;
use Component\Database\DBTableField;
use Component\Member\MemberDAO;
use Component\Sms\Code;
use Component\Sms\SmsAutoCode;
class ExcelCouponConvert extends \Component\Excel\ExcelDataConvert
{
    /**
     * 페이퍼쿠폰 코드 엑셀 업로드
     * 엑셀업로드 결과를 엑셀파일로 다운해주는 것이었으나 기획요청에 의해 간략 결과 보여줌
     * 엑셀내용을 변수에 담아 반환 해준 후 table을 만들어 excel 추출 (table2excel.js 사용)
     *
     * @author su
     */
    public function setCouponOfflineExcelCodeUp($arrData)
    {
        $request = App::getInstance('request');
        $excel = $request->files()->get('excel');

        $result['false'] = 0;
        $result['true'] = 0;
        $result['total'] = 0;
        $excelContent = '';

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
        $this->excelReader->setReadDataOnly(true);
        $this->sheet = $this->excelReader->setReadEmptyCells(false)->load($excel['tmp_name'])->getActiveSheet();
        $arrSheet = $this->sheet->toArray();
        unset($arrSheet[0]);

        $excelContent .= '<table border="1">' . chr(10);
        $excelContent .= '<tr>' . chr(10);
        $excelContent .= '<td>' . __('번호') . '</td><td>' . __('인증 번호') . '</td><td>' . __('상태') . '</td>' . chr(10);
        $excelContent .= '</tr>' . chr(10);

        $idx = 1;
        $couponAdmin = App::load('\\Component\\Coupon\\CouponAdmin');
        foreach($arrSheet as $key => $val){
            $couponOfflineCodeChk = true;
            $couponOfflineMsg = '';
            $couponOfflineCode = trim($val[0]);
            // $couponOfflineCode 가 있는지를 체크
            if (empty($val)) {
                $couponOfflineCodeChk = false;
                $couponOfflineMsg = __('인증번호 확인 필요');
            } else if (strlen($val[0]) > 12) {
                $couponOfflineCodeChk = false;
                $couponOfflineMsg = __('인증번호는 12자 이하');
            } else if (strlen($val[0]) < 8) {
                $couponOfflineCodeChk = false;
                $couponOfflineMsg = __('인증번호는 8자 이상');
            } else if ($couponAdmin->checkOfflineCode($couponOfflineCode)) {
                $couponOfflineCodeChk = false;
                $couponOfflineMsg = __('인증번호 존재');
            }
            $excelContent .= '<tr>' . chr(10);
            $excelContent .= '<td>' . ($idx) . '</td>' . chr(10);
            $excelContent .= '<td>' . $val[0] . '</td>' . chr(10);
            $idx++;
            if ($couponOfflineCodeChk === false) {
                $excelContent .= '<td>' . $couponOfflineMsg . '</td>' . chr(10);
                $result['false']++;
            } else {
                // 인증번호 저장
                $arrBind['param'] = "couponOfflineCode, couponOfflineCodeUser, couponNo, couponOfflineCodeSaveType, couponOfflineInsertAdminId,managerNo";
                $this->db->bind_param_push($arrBind['bind'], 's', $couponOfflineCode);
                $this->db->bind_param_push($arrBind['bind'], 's', $couponOfflineCode);
                $this->db->bind_param_push($arrBind['bind'], 'i', $arrData['couponNo']);
                $this->db->bind_param_push($arrBind['bind'], 's', 'n');
                $this->db->bind_param_push($arrBind['bind'], 's', $arrData['couponOfflineInsertAdminId']);
                $this->db->bind_param_push($arrBind['bind'], 'i', $arrData['managerNo']);
                $this->db->set_insert_db(DB_COUPON_OFFLINE_CODE, $arrBind['param'], $arrBind['bind'], 'y');
                unset($arrBind);
                $excelContent .= '<td>' . __('생성 됨') . '</td>' . chr(10);
                $result['true']++;
            }
            $excelContent .= '</tr>' . chr(10);
        }
        $excelContent .= '</table>' . chr(10);
        $result['total'] = $result['false'] + $result['true'];
        $result['content'] = $excelContent;

        return $result;
    }

    /**
     * 쿠폰 수동 발급 엑셀 업로드
     *
     * @author su
     */
    public function setExcelMemberCouponUp($arrData, $sSmsFlag = 'n', $passwordCheckFl = true)
    {
        $request = \App::getInstance('request');
        $excel = $request->files()->get('excel');
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
        $this->excelReader->setReadDataOnly(true);
        $this->sheet = $this->excelReader->setReadEmptyCells(false)->load($excel['tmp_name'])->getActiveSheet();
        $arrSheet = $this->sheet->toArray();
        unset($arrSheet[0]);

        echo $this->excelHeader;
        echo '<table border="1">' . chr(10);
        echo '<tr>' . chr(10);
        echo '<td>' . __('번호') . '</td><td>' . __('회원 번호') . '</td><td>' . __('아이디') . '</td><td>' . __('상태') . '</td>' . chr(10);
        echo '</tr>' . chr(10);

        $couponAdmin = App::load(\Component\Coupon\CouponAdmin::class);

        // sms발송 설정
        if ($sSmsFlag == 'y') {
            $logger = App::getInstance('logger');
            $smsAuto = App::load(\Component\Sms\SmsAuto::class);
            $couponInfo = $couponAdmin->getCouponInfo($arrData['couponNo'], '*');
        }

        // 엑셀 데이터를 추출 후 가공
        $idx = 1;
        $savedMemNoList = [];
        foreach ($arrSheet as $key => $val) {
            $memberId = $val[0];
            $member = ['mallSno' => DEFAULT_MALL_NUMBER,];
            // memNo 가 있는지를 체크
            if (empty($val[0]) === true) {
                $memNo = false;
            } else {
                $member = MemberDAO::getInstance()->selectMemberByOne($memberId, 'memId');
                $memNo = $member['memNo'];
            }

            echo '<tr>' . chr(10);
            echo '<td>' . ($idx) . '</td>' . chr(10);
            echo '<td>' . $memNo . '</td>' . chr(10);
            echo '<td>' . $memberId . '</td>' . chr(10);
            $idx++;

            if ($memNo === false || $memNo < 1) {
                echo '<td>' . __('회원정보 없음') . '</td>' . chr(10);
            } elseif ($member['mallSno'] == DEFAULT_MALL_NUMBER) {
                unset($arrData['memNo']);
                $arrData['memNo'] = $memNo;
                // 저장
                $arrBind = $this->db->get_binding(DBTableField::tableMemberCoupon(), $arrData, 'insert', array_keys($arrData), ['memberCouponNo']);
                $this->db->set_insert_db(DB_MEMBER_COUPON, $arrBind['param'], $arrBind['bind'], 'y');
                $savedMemNoList[] = $memNo;

                // sms발송
                if ($sSmsFlag == 'y') {
                    $member = ['smsFl' => 'n'];
                    if ($arrData['memNo'] >= 1) {
                        $member = MemberDAO::getInstance()->selectMemberByOne($arrData['memNo']);
                    } else {
                        $logger->info('Send coupon auto sms. not found member number.');
                    }
                    if ($couponInfo) {
                        if ($member['smsFl'] == 'y') {
                            $smsAuto->setPasswordCheckFl($passwordCheckFl);
                            $smsAuto->setSmsAutoCodeType(Code::COUPON_MANUAL);
                            $smsAuto->setSmsType(SmsAutoCode::PROMOTION);
                            $smsAuto->setReceiver($member);
                            $smsAuto->setReplaceArguments(
                                [
                                    'name'       => $member['memNm'],
                                    'memNo'      => $member['memNo'],
                                    'CouponName' => $couponInfo['couponNm'],
                                    'rc_memid'   => $member['memId'],
                                    'rc_mallNm'  => \Globals::get('gMall.mallNm'),
                                ]
                            );
                            $smsAuto->autoSend();
                        } else {
                            $logger->info(sprintf('Disallow sms receiving. memNo[%s], smsFl [%s]', $member['memNo'], $member['smsFl']));
                        }
                    }
                }

                $couponAdmin->setCouponMemberSaveCount($arrData['couponNo']);

                echo '<td>' . __('발급됨') . '</td>' . chr(10);
            } else {
                echo '<td>' . __('기준몰 회원만 쿠폰 발급이 가능합니다.') . '</td>' . chr(10);
            }
            echo '</tr>' . chr(10);
        }
        echo '</table>' . chr(10);

        // 엑셀 하단
        echo $this->excelFooter;

        \Logger::info("[지급] 쿠폰 수동발급 엑셀업로드 (memNo)", $savedMemNoList);
        return true;
    }
}
